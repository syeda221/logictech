<?php

namespace App\Services;

use App\Models\EcommerceOrder;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send WhatsApp notification based on order status change.
     */
    public function sendStatusNotification(EcommerceOrder $order, string $newStatus)
    {
        try {
            $brandName = Setting::where('key', 'web_site_name')->value('value') ?: 'TrendHub';
            $customerName = $order->customer->name ?? $order->shipping_name;
            $orderId = $order->order_number;
            $orderTotal = number_format($order->total, 2);
            $websiteUrl = url('/');
            $phone = $this->formatPhoneNumber($order->shipping_phone);

            // Fetch Support Phone dynamically
            $supportPhoneSetting = Setting::where('key', 'web_whatsapp_number')->value('value') 
                ?: Setting::where('key', 'web_contact_phone')->value('value');
                
            $supportUrlLine = '';
            if (!empty($supportPhoneSetting)) {
                $supportPhone = preg_replace('/[^0-9]/', '', $supportPhoneSetting);
                if (str_starts_with($supportPhone, '0')) {
                    $supportPhone = '92' . substr($supportPhone, 1);
                }
                if (strlen($supportPhone) === 10 && !str_starts_with($supportPhone, '92')) {
                    $supportPhone = '92' . $supportPhone;
                }
                $supportUrlLine = "\nIf you need any assistance, feel free to contact our customer support:\nhttps://wa.me/" . $supportPhone;
            }

            if (!$phone) {
                Log::warning("WhatsApp notification skipped: Invalid phone number for Order #{$orderId}");
                return false;
            }

            $message = '';

            switch ($newStatus) {
                case 'processing':
                    $message = "Order Confirmed!\n" .
                               "Dear {$customerName},\n" .
                               "Your order {$orderId} from {$brandName} has been successfully confirmed and verified.\n" .
                               "It will be delivered to you within 3-4 days.{$supportUrlLine}\n" .
                               "Thank you for shopping with us.";
                    break;

                case 'shipped':
                    $courierName = $order->courier_name ?: 'Courier Service';
                    $trackingInfo = '';
                    if ($order->tracking_url) {
                        $trackingInfo = "\nTracking URL: " . $order->tracking_url;
                    } elseif ($order->tracking_number) {
                        $trackingInfo = "\nTracking Number: " . $order->tracking_number;
                    }

                    $message = "Order Dispatched\n" .
                               "Dear {$customerName},\n" .
                               "Your order from {$brandName} has been shipped.\n" .
                               "Order ID: {$orderId}\n" .
                               "Amount: PKR {$orderTotal}\n\n" .
                               "Courier: {$courierName}" .
                               "{$trackingInfo}\n\n" .
                               "Thank you for shopping with us.\n" .
                               "Visit Website:\n" .
                               "{$websiteUrl}";
                    break;

                default:
                    return false;
            }

            return $this->sendTextMessage($phone, $message);

        } catch (\Exception $e) {
            Log::error("WhatsApp service notification error for Order #{$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send HTTP request to WhatsApp API.
     */
    protected function sendTextMessage(string $phone, string $message)
    {
        $apiUrl = env('WHATSAPP_API_URL');
        $apiKey = env('WHATSAPP_API_KEY');

        Log::info("Sending WhatsApp to {$phone}. Message:\n{$message}");

        if (empty($apiUrl)) {
            Log::info("WhatsApp API URL not set in .env. Message logged to laravel.log instead.");
            return true;
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ])->post($apiUrl, [
                'to' => $phone,
                'message' => $message
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("WhatsApp API returned error code {$response->status()}: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp API connection failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format Pakistan phone numbers to standard format (e.g. 923XXXXXXXXX).
     */
    protected function formatPhoneNumber(string $phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '0')) {
            $phone = '92' . substr($phone, 1);
        }
        
        if (strlen($phone) === 12 && str_starts_with($phone, '92')) {
            return $phone;
        }

        return $phone;
    }
}
