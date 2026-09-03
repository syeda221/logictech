<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    /**
     * Runs on the local server instance to push data to the cloud.
     */
    public function syncToCloud(Request $request)
    {
        $cloudSyncUrl = env('CLOUD_SYNC_URL', 'https://haroontraders.binsultansweet.com');
        $syncToken = env('SYNC_TOKEN', 'trendhub_sync_default_token_2026');

        // 1. Fetch unsynced customers
        $customers = Customer::where('is_synced', 0)->get()->map(function($c) {
            return $c->toArray();
        })->toArray();

        // 2. Fetch unsynced sales with items
        $sales = Sale::where('is_synced', 0)->get()->map(function($s) {
            $sData = $s->toArray();
            
            // Map customer local ID to UUID
            $cust = Customer::find($s->customer_id);
            $sData['customer_uuid'] = $cust ? $cust->uuid : null;

            // Load items and map product local ID to SKU
            $sData['items'] = SaleItem::where('sale_id', $s->id)->get()->map(function($item) {
                $iData = $item->toArray();
                $prod = Product::find($item->product_id);
                $iData['product_code'] = $prod ? $prod->item_code : null;
                return $iData;
            })->toArray();

            return $sData;
        })->toArray();

        if (empty($customers) && empty($sales)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Your local database is already fully synchronized with the cloud!'
            ]);
        }

        try {
            // Send payload to cloud via HTTP Client (withoutVerifying to bypass local SSL errors)
            $response = Http::timeout(60)
                ->withoutVerifying()
                ->withHeaders(['X-Sync-Token' => $syncToken])
                ->post($cloudSyncUrl . '/api/receive-sync', [
                    'customers' => $customers,
                    'sales' => $sales
                ]);

            if ($response->successful()) {
                $resData = $response->json();
                if (isset($resData['status']) && $resData['status'] === 'success') {
                    // Update local records to synced
                    if (!empty($customers)) {
                        Customer::whereIn('uuid', collect($customers)->pluck('uuid'))->update(['is_synced' => 1]);
                    }
                    if (!empty($sales)) {
                        Sale::whereIn('uuid', collect($sales)->pluck('uuid'))->update(['is_synced' => 1]);
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Cloud synchronization completed successfully! ' . count($sales) . ' sales and ' . count($customers) . ' customers synced.'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => $resData['message'] ?? 'Sync failed on cloud server.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'HTTP request failed: ' . $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Connection to cloud failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Runs on the cloud server instance to receive pushed data.
     */
    public function receiveSync(Request $request)
    {
        $token = $request->header('X-Sync-Token') ?? $request->input('sync_token');
        if ($token !== env('SYNC_TOKEN', 'trendhub_sync_default_token_2026')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized sync request.'], 401);
        }

        $payload = $request->all();
        $customers = $payload['customers'] ?? [];
        $sales = $payload['sales'] ?? [];

        DB::beginTransaction();
        try {
            // Process customers
            foreach ($customers as $cData) {
                $uuid = $cData['uuid'];
                unset($cData['id']);
                
                $customer = Customer::where('uuid', $uuid)->first();
                if (!$customer) {
                    $customer = new Customer($cData);
                    $customer->uuid = $uuid;
                    $customer->is_synced = 1;
                    $customer->save();
                } else {
                    $customer->update($cData);
                }
            }

            // Process sales
            $saleController = app(\App\Http\Controllers\SaleController::class);
            $balanceService = app(\App\Services\BalanceService::class);
            $transactionService = app(\App\Services\TransactionService::class);

            foreach ($sales as $sData) {
                $uuid = $sData['uuid'];
                $items = $sData['items'] ?? [];
                
                unset($sData['id']);
                unset($sData['items']);

                $customerUuid = $sData['customer_uuid'] ?? null;
                if ($customerUuid) {
                    $cloudCustomer = Customer::where('uuid', $customerUuid)->first();
                    if ($cloudCustomer) {
                        $sData['customer_id'] = $cloudCustomer->id;
                    }
                }
                unset($sData['customer_uuid']);

                $sale = Sale::where('uuid', $uuid)->first();
                if (!$sale) {
                    $sale = new Sale($sData);
                    $sale->uuid = $uuid;
                    $sale->is_synced = 1;
                    $sale->save();

                    // Create sale items
                    foreach ($items as $iData) {
                        unset($iData['id']);
                        unset($iData['sale_id']);
                        
                        $productCode = $iData['product_code'] ?? null;
                        if ($productCode) {
                            $cloudProduct = Product::where('item_code', $productCode)->first();
                            if ($cloudProduct) {
                                $iData['product_id'] = $cloudProduct->id;
                            }
                        }
                        unset($iData['product_code']);

                        $saleItem = new SaleItem($iData);
                        $saleItem->sale_id = $sale->id;
                        $saleItem->save();
                    }

                    // Process postings on the cloud
                    if ($sale->sale_status === 'posted') {
                        // 1. Deduct stock from warehouse
                        $saleController->handleStockImpact($sale, 'out');
                        
                        // 2. Legacy Ledger posting
                        $saleController->updateLedger($sale);

                        // 3. Professional Ledger posting (Debit AR, Credit Sales)
                        $custForVoucher = $sale->customer_relation ?? Customer::find($sale->customer_id);
                        if ($custForVoucher) {
                            $date = $sale->created_at->format('Y-m-d');
                            $balanceService->createSaleVoucher(
                                $custForVoucher,
                                $sale->total_net,
                                $sale->invoice_no,
                                $date
                            );
                        }

                        // 4. Auto Receipt (cash payment posting)
                        $transactionService->createReceiptFromSale($sale);
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Sync payload processed successfully.']);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Sync error on cloud: ' . $ex->getMessage());
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }
    }
}
