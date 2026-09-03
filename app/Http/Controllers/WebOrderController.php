<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EcommerceOrder;

class WebOrderController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasAnyPermission(['web_orders.view', 'web_orders.read'])) {
            abort(403, 'Unauthorized action. You do not have permission to view Web Orders.');
        }

        $query = EcommerceOrder::with('customer');

        // Search Filter (Order ID, Customer Name, Phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Order Status Filter
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Payment Status Filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Payment Method Filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date From Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Date To Filter
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('admin_panel.web_orders.index', compact('orders'));
    }

    public function dashboard()
    {
        if (!auth()->user()->hasAnyPermission(['web_orders.view', 'web_orders.read'])) {
            abort(403, 'Unauthorized action. You do not have permission to view Web Dashboard.');
        }

        // 1. Fetch Accounts
        $easypaisaAccount = \App\Models\Account::where('title', 'like', '%easypaisa%')->first();
        $cashInHandAccount = \App\Models\Account::where('title', 'like', '%cash in hand%')
            ->orWhere('title', 'like', '%cash account%')
            ->first();

        // Helper function to calculate balance (inline inside php block, or in controller)
        $getAccountBalance = function($account) {
            if (!$account) return 0;
            $sum = \App\Models\JournalEntry::where('account_id', $account->id)
                ->selectRaw('SUM(debit) as debits, SUM(credit) as credits')
                ->first();
            $debits = $sum->debits ?? 0;
            $credits = $sum->credits ?? 0;
            if (strtolower($account->type) === 'credit') {
                return ($account->initial_balance ?? 0) + ($credits - $debits);
            } else {
                return ($account->initial_balance ?? 0) + ($debits - $credits);
            }
        };

        $easypaisaBalance = $getAccountBalance($easypaisaAccount);
        $cashInHandBalance = $getAccountBalance($cashInHandAccount);

        // 2. Fetch Web Orders for analytics
        $allOrders = EcommerceOrder::orderBy('created_at', 'desc')->get();

        $today = now()->format('Y-m-d');

        $stats = [
            'total_orders' => $allOrders->count(),
            'pending_count' => $allOrders->where('order_status', 'pending')->count(),
            'processing_count' => $allOrders->where('order_status', 'processing')->count(),
            'shipped_count' => $allOrders->where('order_status', 'shipped')->count(),
            'delivered_count' => $allOrders->where('order_status', 'delivered')->count(),
            'cancelled_count' => $allOrders->where('order_status', 'cancelled')->count(),
            'total_easypaisa_confirmed' => 0,
            'total_cod_delivered' => 0,
            'total_sales' => 0,
            'today_sales' => 0,
            'today_orders_count' => 0,
        ];

        // 3. Build Web Transactions Ledger dynamically
        $transactions = [];
        foreach ($allOrders as $order) {
            $orderNum = $order->order_number;
            $total = (float)$order->total;
            $paid = (float)$order->paid_amount;
            $paymentMethod = $order->payment_method;
            $paymentStatus = $order->payment_status;
            $orderStatus = $order->order_status;
            
            $createdAt = $order->created_at ? $order->created_at->format('Y-m-d') : '';
            $updatedAt = $order->updated_at ? $order->updated_at->format('Y-m-d') : '';
            $date = $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s');

            if ($createdAt === $today) {
                $stats['today_orders_count']++;
            }

            // --- Easypaisa Entry ---
            // If payment method is Easypaisa and it is confirmed/paid (meaning payment is received)
            if (in_array(strtolower($paymentMethod), ['easypaisa', 'online']) && in_array(strtolower($paymentStatus), ['paid', 'confirmed', 'verified']) && $paid > 0) {
                $stats['total_easypaisa_confirmed'] += $paid;
                
                // If payment confirmation occurred today
                if ($updatedAt === $today) {
                    $stats['today_sales'] += $paid;
                }

                $transactions[] = (object)[
                    'date' => $date,
                    'order_id' => $order->id,
                    'order_number' => $orderNum,
                    'method' => 'Easypaisa',
                    'amount' => $paid,
                    'type' => 'Easypaisa Confirmed',
                    'status_badge' => 'bg-success',
                    'description' => "Easypaisa payment confirmed for Order #{$orderNum}"
                ];
            }

            // --- Cash / COD Entry ---
            // If order is delivered, show COD entry (amount = remaining COD amount, i.e., total - paid)
            $remainingCod = $total - $paid;
            if ($orderStatus === 'delivered' && $remainingCod > 0) {
                $stats['total_cod_delivered'] += $remainingCod;
                
                // If delivery occurred today
                if ($updatedAt === $today) {
                    $stats['today_sales'] += $remainingCod;
                }

                $transactions[] = (object)[
                    'date' => $date,
                    'order_id' => $order->id,
                    'order_number' => $orderNum,
                    'method' => 'Cash on Delivery (COD)',
                    'amount' => $remainingCod,
                    'type' => 'COD Delivered',
                    'status_badge' => 'bg-info text-dark',
                    'description' => "COD remaining amount received upon delivery of Order #{$orderNum}"
                ];
            }
        }

        $stats['total_sales'] = $stats['total_easypaisa_confirmed'] + $stats['total_cod_delivered'];

        // Sort unified transactions list by Date (newest first)
        usort($transactions, function($a, $b) {
            return strcmp($b->date, $a->date);
        });

        // Limit recent transactions to 10 for dashboard view
        $recentTransactions = array_slice($transactions, 0, 10);
        $recentOrders = $allOrders->take(5);

        return view('admin_panel.web_orders.dashboard', compact(
            'easypaisaAccount', 'easypaisaBalance',
            'cashInHandAccount', 'cashInHandBalance',
            'stats', 'recentTransactions', 'recentOrders'
        ));
    }

    public function show($id)
    {
        if (!auth()->user()->hasAnyPermission(['web_orders.view', 'web_orders.read'])) {
            abort(403, 'Unauthorized action. You do not have permission to view Web Order details.');
        }

        $order = EcommerceOrder::with(['customer', 'items.product'])->findOrFail($id);
        return view('admin_panel.web_orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('web_orders.edit')) {
            abort(403, 'Unauthorized action. You do not have permission to edit Web Orders.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'courier_name' => 'required_if:status,shipped|nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:255',
        ]);

        $order = EcommerceOrder::with('items.product')->findOrFail($id);
        $oldStatus = $order->order_status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return redirect()->back()->with('info', 'No status change detected.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // Confirm/Process Order -> Deduct Stock
            if (in_array($newStatus, ['processing', 'shipped', 'delivered']) && !$order->is_stock_deducted) {
                foreach ($order->items as $item) {
                    if (!$item->product_id) continue;
                    
                    // Default warehouse ID = 1
                    $warehouseStock = \App\Models\WarehouseStock::where('product_id', $item->product_id)
                        ->where('warehouse_id', 1)
                        ->first();

                    if (!$warehouseStock) {
                        // Create record if not exists but with 0 stock
                        $warehouseStock = \App\Models\WarehouseStock::create([
                            'product_id' => $item->product_id,
                            'warehouse_id' => 1,
                            'quantity' => 0,
                            'total_pieces' => 0
                        ]);
                    }

                    if ($warehouseStock->total_pieces < $item->quantity) {
                        throw new \Exception("Insufficient stock for product '{$item->product_name}'. Available: {$warehouseStock->total_pieces}, Required: {$item->quantity}");
                    }

                    // Deduct stock
                    $warehouseStock->total_pieces -= $item->quantity;
                    
                    $ppb = $item->product->pieces_per_box > 0 ? $item->product->pieces_per_box : 1;
                    $warehouseStock->quantity = round($warehouseStock->total_pieces / $ppb, 2);
                    $warehouseStock->save();

                    // Insert movement
                    \Illuminate\Support\Facades\DB::table('stock_movements')->insert([
                        'product_id' => $item->product_id,
                        'type' => 'out',
                        'qty' => -$item->quantity,
                        'ref_type' => 'web_order',
                        'ref_id' => $order->id,
                        'note' => 'Web Order Confirmed #' . $order->order_number,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                $order->is_stock_deducted = true;
            }
            
            // Cancel/Revert Order -> Restore Stock
            if (in_array($newStatus, ['cancelled', 'pending']) && $order->is_stock_deducted) {
                foreach ($order->items as $item) {
                    if (!$item->product_id) continue;

                    $warehouseStock = \App\Models\WarehouseStock::where('product_id', $item->product_id)
                        ->where('warehouse_id', 1)
                        ->first();

                    if (!$warehouseStock) {
                        $warehouseStock = \App\Models\WarehouseStock::create([
                            'product_id' => $item->product_id,
                            'warehouse_id' => 1,
                            'quantity' => 0,
                            'total_pieces' => 0
                        ]);
                    }

                    // Restore stock
                    $warehouseStock->total_pieces += $item->quantity;

                    $ppb = $item->product->pieces_per_box > 0 ? $item->product->pieces_per_box : 1;
                    $warehouseStock->quantity = round($warehouseStock->total_pieces / $ppb, 2);
                    $warehouseStock->save();

                    // Insert movement
                    \Illuminate\Support\Facades\DB::table('stock_movements')->insert([
                        'product_id' => $item->product_id,
                        'type' => 'in',
                        'qty' => $item->quantity,
                        'ref_type' => 'web_order_cancel',
                        'ref_id' => $order->id,
                        'note' => 'Web Order Reverted/Cancelled #' . $order->order_number,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $order->is_stock_deducted = false;
            }

            $order->order_status = $newStatus;
            
            // Save courier info if Dispatched
            if ($newStatus === 'shipped') {
                $order->courier_name = $request->courier_name;
                $order->tracking_number = $request->tracking_number;
                $order->tracking_url = $request->tracking_url;
            }
            
            $order->save();

            \Illuminate\Support\Facades\DB::commit();

            // Trigger WhatsApp Notification
            try {
                $whatsAppService = app(\App\Services\WhatsAppService::class);
                $whatsAppService->sendStatusNotification($order, $newStatus);
            } catch (\Exception $e) {
                \Log::error("Failed to send WhatsApp notification: " . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Order status updated and stock adjusted successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function verifyPayment(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('web_orders.edit')) {
            abort(403, 'Unauthorized action. You do not have permission to verify Web Order payments.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $order = EcommerceOrder::with('items.product')->findOrFail($id);

        if ($request->action === 'approve') {
            if ($order->payment_status === 'paid') {
                return redirect()->back()->with('info', 'Payment is already marked as Paid.');
            }

            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                if (!$order->is_stock_deducted) {
                    foreach ($order->items as $item) {
                        if (!$item->product_id) continue;
                        
                        $warehouseStock = \App\Models\WarehouseStock::where('product_id', $item->product_id)
                            ->where('warehouse_id', 1)
                            ->first();

                        if (!$warehouseStock) {
                            $warehouseStock = \App\Models\WarehouseStock::create([
                                'product_id' => $item->product_id,
                                'warehouse_id' => 1,
                                'quantity' => 0,
                                'total_pieces' => 0
                            ]);
                        }

                        if ($warehouseStock->total_pieces < $item->quantity) {
                            throw new \Exception("Insufficient stock for product '{$item->product_name}'. Available: {$warehouseStock->total_pieces}, Required: {$item->quantity}");
                        }

                        $warehouseStock->total_pieces -= $item->quantity;
                        $ppb = $item->product->pieces_per_box > 0 ? $item->product->pieces_per_box : 1;
                        $warehouseStock->quantity = round($warehouseStock->total_pieces / $ppb, 2);
                        $warehouseStock->save();

                        \Illuminate\Support\Facades\DB::table('stock_movements')->insert([
                            'product_id' => $item->product_id,
                            'type' => 'out',
                            'qty' => -$item->quantity,
                            'ref_type' => 'web_order',
                            'ref_id' => $order->id,
                            'note' => 'Web Order Confirmed #' . $order->order_number,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    $order->is_stock_deducted = true;
                }

                // Set manual paid amount or default to order total
                $paidAmount = $request->filled('paid_amount') ? (float)$request->paid_amount : $order->total;
                $order->paid_amount = $paidAmount;

                $order->payment_status = 'paid';
                $order->order_status = 'processing';
                $order->save();

                \Illuminate\Support\Facades\DB::commit();

                // Trigger WhatsApp Notification
                try {
                    $whatsAppService = app(\App\Services\WhatsAppService::class);
                    $whatsAppService->sendStatusNotification($order, 'processing');
                } catch (\Exception $e) {
                    \Log::error("Failed to send WhatsApp notification: " . $e->getMessage());
                }

                return redirect()->back()->with('success', 'Payment approved successfully! Order is now in Processing status.');

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                return redirect()->back()->with('error', 'Error approving payment: ' . $e->getMessage());
            }
        } else {
            $order->payment_status = 'failed';
            $order->save();
            return redirect()->back()->with('success', 'Payment rejected.');
        }
    }
}
