<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Inwardgatepass;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Stock;
use App\Models\Vendor;
use App\Models\VendorLedger;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /** Keep stocks table in sync for a (branch,warehouse,product) */
    /** Keep warehouse_stocks table in sync for a (warehouse,product) */
    private function upsertStocks(int $productId, float $qtyPiecesDelta, int $branchId, int $warehouseId): void
    {
        // We ignore $branchId as WarehouseStock is warehouse-specific
        $stock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        // Get Product Master Data for Box Calculation
        $product = Product::find($productId);
        $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;

        if ($stock) {
            // ✅ Add new pieces directly to existing balance to prevent precision/sync loss
            $stock->total_pieces += $qtyPiecesDelta;
            
            // Recalculate Boxes for display only
            $stock->quantity = $stock->total_pieces / $ppb;
            
            $stock->save();
        } else {
            \App\Models\WarehouseStock::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'total_pieces' => $qtyPiecesDelta,
                'quantity' => $qtyPiecesDelta / $ppb, // Initial Box Qty
                'price' => 0, // Should be fetched from Product or Purchase Item? 
            ]);
        }
    }

    /**
     * AJAX: Return PO items JSON for GRN creation
     */
    public function getItemsJson($id)
    {
        $purchase = Purchase::with(['items.product', 'vendor', 'warehouse'])->findOrFail($id);

        $items = $purchase->items->map(function ($item) use ($purchase) {
            $pendingQty = max(0, $item->qty - ($item->received_qty ?? 0));
            return [
                'id'               => $item->id,
                'product_id'       => $item->product_id,
                'product_name'     => $item->product->name ?? 'N/A',
                'product_sku'      => $item->product->sku ?? '',
                'warehouse_id'     => $purchase->warehouse_id,
                'warehouse_name'   => $purchase->warehouse->name ?? 'N/A',
                'ordered_qty'      => $item->qty,
                'received_qty'     => $item->received_qty ?? 0,
                'pending_qty'      => $pendingQty,
                'unit_price'       => $item->price,
                'unit'             => $item->unit ?? '',
            ];
        });

        return response()->json([
            'purchase' => [
                'id'          => $purchase->id,
                'po_number'   => $purchase->invoice_no,
                'vendor_name' => $purchase->vendor->name ?? 'N/A',
                'purchase_type'=> $purchase->purchase_type ?? 'local',
                'currency'    => $purchase->currency ?? 'PKR',
                'po_status'   => $purchase->po_status,
                'warehouse_id'=> $purchase->warehouse_id,
            ],
            'items' => $items,
        ]);
    }


    public function index(Request $request)
    {
        $query = Purchase::with(['branch', 'warehouse', 'vendor', 'items', 'returns']);

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status_purchase', $request->status);
        }

        // Apply Date Filters
        if ($request->filled('from_date')) {
            $query->whereDate('purchase_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('purchase_date', '<=', $request->to_date);
        }

        // Apply Mobile Number Filter
        if ($request->filled('mobile_no')) {
            $query->whereHas('vendor', function ($q) use ($request) {
                $q->where('mobile', 'like', "%{$request->mobile_no}%");
            });
        }

        // Apply Bill No (Invoice No / ID) Filter
        if ($request->filled('bill_no')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_no', 'like', "%{$request->bill_no}%")
                  ->orWhere('id', 'like', "%{$request->bill_no}%");
            });
        }

        // Apply M.Bill # (Note) Filter
        if ($request->filled('reference')) {
            $query->where('note', 'like', "%{$request->reference}%");
        }

        // Apply Vendor Filter
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Order By ID / Invoice / Date descending
        $orderBy = $request->input('order_by', 'id');
        if ($orderBy === 'invoice_no') {
            $query->orderBy('invoice_no', 'desc');
        } elseif ($orderBy === 'purchase_date') {
            $query->orderBy('purchase_date', 'desc')->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $Purchase = $query->get();
        
        // Calculate updated amounts after returns
        $Purchase->each(function ($purchase) {
            // Calculate total returned amount for this purchase
            $totalReturned = $purchase->returns->sum('net_amount');
            
            // Store calculated values as attributes
            $purchase->total_returned = $totalReturned;
            $purchase->updated_net_amount = max(0, $purchase->net_amount - $totalReturned);
            $purchase->updated_due_amount = max(0, $purchase->due_amount - $totalReturned);
            
            // Check if fully returned
            $purchase->is_fully_returned = $purchase->net_amount > 0 && $totalReturned >= $purchase->net_amount;
            $purchase->has_partial_return = $totalReturned > 0 && $totalReturned < $purchase->net_amount;
        });

        // If AJAX request, return only table rows partial
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin_panel.purchase.partials.purchase_table_body', compact('Purchase'))->render()
            ]);
        }

        // Load all vendors for filter dropdown
        $vendors = Vendor::orderBy('name')->get();

        return view('admin_panel.purchase.index', compact('Purchase', 'vendors'));
    }

    public function addBill($gatepassId)
    {
        // Fetch the gatepass along with its related items and products
        $gatepass = InwardGatepass::with('items.product')->findOrFail($gatepassId);

        // Pass the gatepass data to the view
        return view('admin_panel.inward.add_bill', compact('gatepass'));
    }

    public function add_purchase()
    {
        // $userId = Auth::id();
        $Purchase = Purchase::get();
        $Vendor = Vendor::get();
        $Warehouse = Warehouse::get();
        // Filter accounts to only show Cash (1) and Bank (2) heads to prevent logic errors
        $accounts = \App\Models\Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();

        $lastInvoice = Purchase::latest('id')->value('invoice_no');
        $nextInvoice = $lastInvoice
            ? 'PUR-'.str_pad(((int) preg_replace('/[^0-9]/', '', $lastInvoice)) + 1, 3, '0', STR_PAD_LEFT)
            : 'PUR-001';

        // Return new V2 view
        return view('admin_panel.purchase.add_purchase_v2', compact('Vendor', 'Warehouse', 'Purchase', 'accounts', 'nextInvoice'));
    }
    public function quickCreate()
    {
        $Vendor = Vendor::get();
        $Warehouse = Warehouse::get();
        $accounts = \App\Models\Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();
        
        $categories = \App\Models\Category::get();

        $balanceService = app(\App\Services\BalanceService::class);
        $apId = $balanceService->getAccountsPayableId();
        $vendorBalances = \App\Models\JournalEntry::where('party_type', \App\Models\Vendor::class)
            ->where('account_id', $apId)
            ->selectRaw('party_id, COALESCE(SUM(credit) - SUM(debit), 0) as balance')
            ->groupBy('party_id')
            ->pluck('balance', 'party_id')
            ->toArray();

        return view('admin_panel.purchase.quick_create', compact('Vendor', 'Warehouse', 'accounts', 'categories', 'vendorBalances'));
    }

    public function quickSearchProducts(Request $request)
    {
        $term = trim($request->get('q', ''));
        if (empty($term)) {
            return response()->json([]);
        }

        $products = Product::with(['category_relation', 'sub_category_relation'])
            ->where('is_active', true)
            ->whereIn('item_type', ['raw_material', 'both'])
            ->where(function ($q) use ($term) {
                $q->where('item_name', 'like', "%{$term}%")
                  ->orWhere('item_code', 'like', "%{$term}%")
                  ->orWhere('barcode_path', 'like', "%{$term}%");
            })
            ->limit(20)
            ->get();

        $results = $products->map(function ($p) use ($term) {
            $variants = [];
            if ($p->color) {
                try {
                    $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                    if (is_array($parsed)) {
                        $variants = $parsed;
                    }
                } catch (\Exception $e) {}
            }

            $isExactMatch = strtolower(trim($p->item_name)) === strtolower($term) || strtolower(trim($p->item_code)) === strtolower($term);

            return [
                'id' => $p->id,
                'item_code' => $p->item_code,
                'item_name' => $p->item_name,
                'category_id' => $p->category_id,
                'category_name' => $p->category_relation->name ?? '',
                'sub_category_id' => $p->sub_category_id,
                'sub_category_name' => $p->sub_category_relation->name ?? '',
                'purchase_price' => (float) ($p->purchase_price_per_piece ?? 0),
                'sale_price' => (float) ($p->sale_price_per_piece ?? 0),
                'variants' => $variants,
                'is_exact' => $isExactMatch,
            ];
        });

        return response()->json($results);
    }

    public function quickStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'nullable',
                'new_vendor_name' => 'nullable|string',
                'opening_balance' => 'nullable|numeric',
                'product_id' => 'nullable|exists:products,id',
                'category_id' => 'required',
                'sub_category_id' => 'nullable',
                'base_product_name' => 'required|string',
                'payment_account_id' => 'nullable|exists:accounts,id',
                'payment_amount' => 'nullable|numeric|min:0',
                
                'variant_size' => 'required|array|min:1',
                'variant_color' => 'required|array|min:1',
                'qty' => 'required|array|min:1',
                'qty.*' => 'required|numeric|min:0',
                'purchase_price' => 'required|array',
                'purchase_price.*' => 'required|numeric|min:0',
                'sale_price' => 'required|array',
                'sale_price.*' => 'required|numeric|min:0',
            ]);

            $hasPositiveQty = false;
            foreach ($validated['qty'] as $q) {
                if ((float)$q > 0) {
                    $hasPositiveQty = true;
                    break;
                }
            }

            if (!$hasPositiveQty) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter a quantity greater than 0 for at least one variant.',
                        'errors' => ['qty' => ['At least one variant must have quantity greater than 0.']]
                    ], 422);
                }
                return back()->withErrors(['qty' => 'At least one variant must have quantity greater than 0.'])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors(), 'message' => 'Validation Error'], 422);
            }
            throw $e;
        }

        $purchase = DB::transaction(function () use ($validated, $request) {
            // 1. Resolve Vendor
            $vendorId = $validated['vendor_id'] ?? null;
            if (!$vendorId && !empty($validated['new_vendor_name'])) {
                $vendor = Vendor::create([
                    'name' => $validated['new_vendor_name'],
                    'status' => 1
                ]);
                $vendorId = $vendor->id;

                if (!empty($validated['opening_balance']) && $validated['opening_balance'] != 0) {
                    \App\Models\VendorLedger::create([
                        'vendor_id' => $vendorId,
                        'admin_or_user_id' => auth()->id() ?? 1,
                        'previous_balance' => 0,
                        'opening_balance' => 0,
                        'closing_balance' => (float)$validated['opening_balance']
                    ]);
                }
            }

            // 2. Create Header
            $lastInvoice = Purchase::latest('id')->value('invoice_no');
            $nextInvoice = $lastInvoice
                ? 'PUR-'.str_pad(((int) preg_replace('/[^0-9]/', '', $lastInvoice)) + 1, 3, '0', STR_PAD_LEFT)
                : 'PUR-001';

            $purchase = Purchase::create([
                'branch_id' => 1,
                'warehouse_id' => 1, // Default warehouse
                'vendor_id' => $vendorId,
                'purchase_date' => now(),
                'invoice_no' => $nextInvoice,
                'note' => 'Quick Purchase: ' . $validated['base_product_name'],
                'subtotal' => 0,
                'discount' => 0,
                'extra_cost' => 0,
                'net_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'status_purchase' => 'approved',
            ]);

            $subtotal = 0;
            $baseName = $validated['base_product_name'];
            $sizes = $request->variant_size;
            $colors = $request->variant_color;
            $qtys = $validated['qty'];
            $pPrices = $validated['purchase_price'];
            $sPrices = $validated['sale_price'];

            // 3. Build Catalog Variants (All defined variants in table to persist to product catalog)
            $catalogVariants = [];
            foreach ($sizes as $i => $sizeStr) {
                $sizeVal = trim($sizeStr ?? '');
                $colorVal = trim($colors[$i] ?? '');
                if ($sizeVal === '' && $colorVal === '') continue;

                $catalogVariants[] = [
                    'name' => $baseName, // Ensure name is set for POS display
                    'size' => $sizeVal,
                    'color' => $colorVal,
                    'sale_price' => (float) ($sPrices[$i] ?? 0),
                    'wholesale_price' => (float) ($sPrices[$i] ?? 0),
                    'purch_price' => (float) ($pPrices[$i] ?? 0),
                    'weight_per_piece' => 0,
                    'stock' => 0
                ];
            }

            // 3. Resolve Product (Existing vs New)
            if (!empty($validated['product_id'])) {
                $product = Product::findOrFail($validated['product_id']);

                // Merge variants into existing product
                $existingVariants = [];
                if ($product->color) {
                    $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                    if (is_array($parsed)) {
                        $existingVariants = $parsed;
                    }
                }

                foreach ($catalogVariants as $inVar) {
                    $matched = false;
                    foreach ($existingVariants as &$exVar) {
                        $exSize = $exVar['size'] ?? '';
                        $exColor = $exVar['color'] ?? '';
                        if (strcasecmp(trim($exSize), trim($inVar['size'])) === 0 && strcasecmp(trim($exColor), trim($inVar['color'])) === 0) {
                            $exVar['sale_price'] = $inVar['sale_price'];
                            $exVar['purch_price'] = $inVar['purch_price'];
                            $matched = true;
                            break;
                        }
                    }
                    unset($exVar);

                    if (!$matched) {
                        $existingVariants[] = $inVar;
                    }
                }

                $product->color = json_encode(array_values($existingVariants));
                if (!empty($validated['category_id'])) {
                    $product->category_id = $validated['category_id'];
                }
                if (!empty($validated['sub_category_id'])) {
                    $product->sub_category_id = $validated['sub_category_id'];
                }
                $product->save();
            } else {
                // Create ONE master Product
                $lastProduct = Product::orderBy('id', 'desc')->first();
                $nextCode = $lastProduct ? ('ITEM-'.str_pad($lastProduct->id + 1, 4, '0', STR_PAD_LEFT)) : 'ITEM-0001';

                $product = Product::create([
                    'item_name' => $baseName,
                    'category_id' => $validated['category_id'] ?? null,
                    'sub_category_id' => $validated['sub_category_id'] ?? null,
                    'color' => json_encode($catalogVariants), // Store all defined variants in catalog!
                    'item_code' => $nextCode,
                    'purchase_price_per_piece' => (float) ($pPrices[0] ?? 0),
                    'sale_price_per_piece' => (float) ($sPrices[0] ?? 0),
                    'size_mode' => 'pieces',
                    'total_m2' => 0,
                    'is_active' => 1
                ]);
            }

            // 4. Create Purchase Items linking to the single master Product
            foreach ($sizes as $i => $sizeStr) {
                $qty = (float) $qtys[$i];
                $pPrice = (float) $pPrices[$i];
                if ($qty <= 0) continue;

                $colorStr = $colors[$i];
                $lineTotal = $qty * $pPrice;

                // Create Purchase Item
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id, // All point to the same master product
                    'price' => $pPrice,
                    'qty' => $qty,
                    'line_total' => $lineTotal,
                    // Store variant info in color field for the PurchaseItem as JSON
                    'color' => json_encode(['size' => $sizeStr, 'color' => $colorStr]), 
                    'size_mode' => 'pieces',
                    'pieces_per_box' => 1,
                ]);

                $subtotal += $lineTotal;
            }

            $purchase->update([
                'subtotal' => $subtotal,
                'net_amount' => $subtotal,
                'due_amount' => $subtotal,
            ]);

            // 4. Approve (Stock + Ledgers)
            $purchase->load('items');
            $this->approvePurchase($purchase);

            // 5. Payment
            if (!empty($validated['payment_account_id']) && $validated['payment_amount'] > 0) {
                try {
                    $transactionService = app(\App\Services\TransactionService::class);
                    $transactionService->createPaymentForPurchase(
                        $purchase,
                        [$validated['payment_account_id']],
                        [$validated['payment_amount']]
                    );
                } catch (\Exception $e) {}
            }

            return $purchase;
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Quick Purchase saved successfully!',
                'redirect_url' => route('Purchase.home'),
            ]);
        }

        return redirect()->route('Purchase.home')->with('success', 'Quick Purchase saved successfully!');
    }

    private function approvePurchase(Purchase $purchase)
    {
        // 1. Stock Movements & Warehouse Stock
        // We need to re-iterate items because we need product_id and qty
        // But the previous logic used $validated arrays which might process duplicates or specific logic.
        // However, since the PurchaseItems are already saved in DB for 'draft' or 'new',
        // we should rely on the SAVED items for approval to ensure consistency.

        $branchId = $purchase->branch_id;
        $warehouseId = $purchase->warehouse_id;

        // Check for Gatepass link (if linked, no stock movement needed usually, logic from store method)
        $hasGatepass = \App\Models\InwardGatepass::where('purchase_id', $purchase->id)->exists();

        if (! $hasGatepass) {
            $movRows = [];
            foreach ($purchase->items as $item) {
                // Determine conversion factor and unit for weight products
                $convFactor = 1;
                $unit = strtolower($item->unit ?? '');
                if (!empty($item->color)) {
                    $itemColor = $item->color;
                    $b64Decoded = base64_decode($itemColor, true);
                    $json = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                    if (!is_array($json)) {
                        $json = json_decode($itemColor, true);
                    }
                    if (is_array($json)) {
                        if (isset($json['conv_factor']) && (float)$json['conv_factor'] > 0) {
                            $convFactor = (float) $json['conv_factor'];
                        }
                        if (isset($json['unit'])) {
                            $unit = strtolower($json['unit']);
                        }
                    }
                }

                if ($unit === 'gm' || $unit === 'g' || $unit === 'gram' || $unit === 'grams') {
                    $baseQty = ((float) $item->qty) / 1000.0;
                } else {
                    $baseQty = ((float) $item->qty) * $convFactor;
                }

                // movements (+)
                $movRows[] = [
                    'product_id' => $item->product_id,
                    'type' => 'in',
                    'qty' => $baseQty,
                    'ref_type' => 'PURCHASE',
                    'ref_id' => $purchase->id,
                    'note' => 'Purchase Confirmed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // stocks
                $this->upsertStocks($item->product_id, +$baseQty, $branchId, $warehouseId);
            }

            if (! empty($movRows)) {
                DB::table('stock_movements')->insert($movRows);
            }
        }

        // ✅ 1b. Sync Product Purchase Price — update purchase_price_per_piece for each item
        foreach ($purchase->items as $item) {
            if ($item->product_id && $item->price > 0) {
                Product::where('id', $item->product_id)->update([
                    'purchase_price_per_piece' => $item->price,
                    'purchase_price_per_box'   => $item->price * max(1, (float) ($item->pieces_per_box ?? 1)),
                ]);
            }
        }

        // 2. Vendor Ledger
        $netAmount = $purchase->net_amount;
        $prevClosing = \App\Models\VendorLedger::where('vendor_id', $purchase->vendor_id)
            ->value('closing_balance') ?? 0;

        \App\Models\VendorLedger::updateOrCreate(
            ['vendor_id' => $purchase->vendor_id],
            [
                'vendor_id' => $purchase->vendor_id,
                'admin_or_user_id' => auth()->id(),
                'previous_balance' => $prevClosing,
                'opening_balance' => $prevClosing,
                'closing_balance' => $prevClosing + $netAmount,
            ]
        );

        // 3. Accounting
        try {
            $transactionService = app(\App\Services\TransactionService::class);

            // A. Create Purchase Voucher
            $transactionService->createPurchaseVoucher($purchase);

            // B. Record Payment (This part is tricky if payments were passed solely in Request)
            // If payments were saved in a temp table or if we re-collect them, good.
            // BUT: distinct feature request "Confirm Purchase" usually implies later approval.
            // If payments were part of the initial 'store' request, they are lost if we didn't save them.
            // The user said "confirm purchase... don't create vouchers... just save...".
            // So if 'Draft', we did NOT run accounting. The payment inputs were ignored?
            // If we want to support payments on Confirm, we would need to store them or ask again.
            // For now, we will Assume no immediate payments on 'Draft -> Confirm' via separate button,
            // UNLESS we are in the immediate 'store' flow where Request data is available.

            // To handle both cases (immediate approve vs later approve), we can pass optional payment data.
            // But strict signature: approvePurchase(Purchase $purchase, array $paymentData = [])

        } catch (\Exception $e) {
            \Log::error('Purchase Accounting Error: '.$e->getMessage());
        }
    }

    public function confirm($id)
    {
        DB::transaction(function () use ($id) {
            $purchase = Purchase::with('items')->findOrFail($id);
            $oldNetAmount = $purchase->net_amount;

            if ($purchase->status_purchase !== 'draft') {
                return; // already approved or invalid state
            }

            // Run approval logic
            $this->approvePurchase($purchase);

            // Update status
            $purchase->update(['status_purchase' => 'approved']);
        });

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Purchase confirmed successfully.',
                'invoice_url' => route('purchase.invoice', $id),
                'redirect_url' => route('Purchase.home'),
            ]);
        }

        return redirect()->back()->with('success', 'Purchase confirmed successfully.');
    }

    public function bulkAdditionalDiscount(Request $request)
    {
        $request->validate([
            'purchase_ids' => 'required|array',
            'purchase_ids.*' => 'exists:purchases,id',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $purchaseIds = $request->purchase_ids;
        $percentage = (float)$request->discount_percentage;

        try {
            DB::transaction(function () use ($purchaseIds, $percentage) {
                foreach ($purchaseIds as $id) {
                    $purchase = Purchase::findOrFail($id);
                    
                    // Original net amount is the current net_amount + current additional_discount
                    $originalNet = (float)$purchase->net_amount + (float)$purchase->additional_discount;
                    
                    // Calculate absolute discount based on user-supplied percentage
                    $newAdditionalDiscount = round($originalNet * ($percentage / 100), 2);
                    
                    $oldAdditionalDiscount = (float)$purchase->additional_discount;
                    $diff = $newAdditionalDiscount - $oldAdditionalDiscount;

                    if ($diff != 0) {
                        // Update purchase record
                        $purchase->additional_discount = $newAdditionalDiscount;
                        $purchase->net_amount = max(0, $purchase->net_amount - $diff);
                        $purchase->due_amount = max(0, $purchase->due_amount - $diff);
                        $purchase->save();

                        // If approved, update ledger & journal entries
                        if ($purchase->status_purchase === 'approved') {
                            // 1. Adjust legacy VendorLedger closing balance
                            $vendorLedger = \App\Models\VendorLedger::where('vendor_id', $purchase->vendor_id)->first();
                            if ($vendorLedger) {
                                $vendorLedger->closing_balance -= $diff;
                                $vendorLedger->save();
                            }

                            // 2. Adjust Journal Vouchers
                            $voucher = \App\Models\VoucherMaster::where('remarks', "Purchase Voucher #{$purchase->invoice_no}")->first();
                            if ($voucher) {
                                $voucher->total_amount = max(0, $voucher->total_amount - $diff);
                                $voucher->save();

                                $balanceService = app(\App\Services\BalanceService::class);
                                $expenseAccountId = $balanceService->getPurchaseExpenseId();
                                $apAccountId = $balanceService->getAccountsPayableId();

                                // Update debit for expense
                                \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                                    ->where('source_id', $voucher->id)
                                    ->where('account_id', $expenseAccountId)
                                    ->update(['debit' => $purchase->net_amount]);

                                // Update credit for accounts payable
                                \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                                    ->where('source_id', $voucher->id)
                                    ->where('account_id', $apAccountId)
                                    ->update(['credit' => $purchase->net_amount]);
                            }
                        }
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Additional discount percentage applied successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $gatepassId = null)
    {
        // (A) Gatepass fetch if provided
        $gatepass = null;
        if ($gatepassId) {
            $gatepass = \App\Models\InwardGatepass::with('purchase')->findOrFail($gatepassId);
            if ($gatepass->purchase) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Gatepass already has a bill.'], 422);
                }

                return back()->with('error', 'This gatepass already has an associated bill.');
            }
        }

        // (B) Validation
        try {
            $validated = $request->validate([
                'invoice_no' => 'nullable|string',
                'vendor_id' => 'required|exists:vendors,id',
                'purchase_date' => 'nullable|date',
                'branch_id' => 'nullable',
                'warehouse_id' => 'nullable',
                'note' => 'nullable|string',
                'discount' => 'nullable|numeric|min:0',
                'extra_cost' => 'nullable|numeric|min:0',
                'product_id' => 'array',
                'product_id.*' => 'nullable|exists:products,id',
                'qty' => 'array',
                'qty.*' => 'nullable|required_with:product_id.*|numeric|min:0.01',
                'price' => 'array',
                'price.*' => 'nullable|required_with:product_id.*|numeric|min:0',
                'price_per_carton' => 'array',
                'price_per_carton.*' => 'nullable|numeric|min:0',
                'unit' => 'array',
                'unit.*' => 'nullable|required_with:product_id.*|string',
                'item_discount' => 'nullable|array',
                'item_discount.*' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'active' => false, 'errors' => $e->errors(), 'message' => 'Validation Error'], 422);
            }
            throw $e;
        }

        // Wrap in transaction... to allow returning $purchase outside
        $purchase = DB::transaction(function () use ($validated, $request, $gatepass) {

            // invoice number
            $lastInvoice = Purchase::latest('id')->value('invoice_no');
            $nextInvoice = $lastInvoice
                ? 'PUR-'.str_pad(((int) preg_replace('/[^0-9]/', '', $lastInvoice)) + 1, 3, '0', STR_PAD_LEFT)
                : 'PUR-001';

            // Resilient Branch resolution: check requested, then user branch, then first existing branch in DB
            $branchId = (int) ($validated['branch_id'] ?? 0);
            if ($branchId <= 0 || ! \App\Models\Branch::where('id', $branchId)->exists()) {
                $userBranch = auth()->user()->branch_id ?? null;
                if ($userBranch && \App\Models\Branch::where('id', $userBranch)->exists()) {
                    $branchId = (int) $userBranch;
                } else {
                    $firstBranch = \App\Models\Branch::first();
                    $branchId = $firstBranch ? (int) $firstBranch->id : 1;
                }
            }

            // Resilient Warehouse resolution
            $warehouseId = (int) ($validated['warehouse_id'] ?? 0);
            if ($warehouseId <= 0 || ! \App\Models\Warehouse::where('id', $warehouseId)->exists()) {
                $firstWarehouse = \App\Models\Warehouse::first();
                $warehouseId = $firstWarehouse ? (int) $firstWarehouse->id : 1;
            }

            // Status Logic
            $status = ($request->action === 'save_only') ? 'draft' : 'approved';

            // create header
            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'vendor_id' => $validated['vendor_id'] ?? null,
                'purchase_date' => $validated['purchase_date'] ?? now(),
                'invoice_no' => $validated['invoice_no'] ?? $nextInvoice,
                'note' => $validated['note'] ?? null,
                'subtotal' => 0,
                'discount' => 0,
                'extra_cost' => 0,
                'net_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'status_purchase' => $status,
            ]);

            $subtotal = 0;
            $pids = $validated['product_id'] ?? [];
            $qtys = $validated['qty'] ?? [];
            $prices = $validated['price'] ?? [];
            $cartonPrices = $validated['price_per_carton'] ?? [];
            $units = $validated['unit'] ?? [];
            $itemDiscs = $validated['item_discount'] ?? [];

            // Snapshot fields
            $sizeModes = $request->size_mode ?? [];
            $ppbs = $request->pieces_per_box ?? [];
            $ppm2 = $request->pieces_per_m2 ?? [];
            $boxesQtys = $request->boxes_qty ?? [];
            $looseQtys = $request->loose_qty ?? [];
            $lengths = $request->length ?? [];
            $widths = $request->width ?? [];

            foreach ($pids as $i => $pid) {
                $pid = (int) ($pid ?? 0);
                $qty = (float) ($qtys[$i] ?? 0);
                $price = (float) ($prices[$i] ?? 0);
                if (! $pid || $qty <= 0 || $price < 0) {
                    continue;
                }

                $discPercent = (float) ($itemDiscs[$i] ?? 0);
                $unit = $units[$i] ?? null;

                // Calculate Line Total matching Frontend Logic
                $curSizeMode = $sizeModes[$i] ?? null;
                $curPPM2 = (float) ($ppm2[$i] ?? 0); // This is actually m2_per_piece if by_size

                if ($curSizeMode === 'by_size') {
                    // Frontend: pieces_per_m2 * totalPieces * price
                    // where pieces_per_m2 is effectively m2 per piece, and price is price per m2
                    $grossTotal = $curPPM2 * $qty * $price;
                } else {
                    // Standard: pieces * price_per_piece
                    $grossTotal = $qty * $price;
                }

                // Calculate absolute discount from percentage
                $discAmount = $grossTotal * ($discPercent / 100);
                $lineTotal = $grossTotal - $discAmount;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $pid,
                    'unit' => $unit,
                    'price' => $price,
                    'item_discount' => $discAmount, // Store calculated amount
                    'qty' => $qty,
                    'line_total' => $lineTotal, // Fixed logic
                    'color' => $request->color[$i] ?? null, // Save variant details

                    // Snapshots
                    'size_mode' => $sizeModes[$i] ?? null,
                    'pieces_per_box' => $ppbs[$i] ?? 1,
                    'pieces_per_m2' => $ppm2[$i] ?? 0,
                    'boxes_qty' => $boxesQtys[$i] ?? 0,
                    'loose_qty' => $looseQtys[$i] ?? 0,
                    'length' => $lengths[$i] ?? null,
                    'width' => $widths[$i] ?? null,
                ]);

                $subtotal += $lineTotal;
            }

            // totals
            $discount = (float) ($request->discount ?? 0);
            $extraCost = (float) ($request->extra_cost ?? 0);
            $netAmount = ($subtotal - $discount) + $extraCost;

            $purchase->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'additional_discount' => $discount,
                'extra_cost' => $extraCost,
                'net_amount' => $netAmount,
                'due_amount' => $netAmount,
            ]);

            // If NOT draft, run full approval
            if ($status === 'approved') {
                $purchase->load('items'); // Load items for approval logic logic

                $this->approvePurchase($purchase); // Basic Stock + Ledger + Voucher

                // B. Record Payment (Only available in immediate Request)
                try {
                    $transactionService = app(\App\Services\TransactionService::class);
                    $paymentAccountIds = $request->input('payment_account_id', []);
                    $paymentAmounts = $request->input('payment_amount', []);

                    if (! empty(array_filter($paymentAccountIds))) {
                        $transactionService->createPaymentForPurchase(
                            $purchase,
                            $paymentAccountIds,
                            $paymentAmounts
                        );
                    }
                } catch (\Exception $e) { /* Logged already */
                }
            }

            // link gatepass -> purchase (and keep status)
            if ($gatepass) {
                $gatepass->purchase_id = $purchase->id;
                $gatepass->status = 'linked';
                $gatepass->save();
            }

            return $purchase;
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Purchase saved successfully.',
                'invoice_url' => route('purchase.invoice', $purchase->id),
                'redirect_url' => route('Purchase.home'),
            ]);
        }

        return redirect()->route('Purchase.home')->with('success', 'Purchase saved successfully.');
    }

    // public function store(Request $request)
    // {
    //     // ✅ Validation
    //     $validated = $request->validate([
    //         'invoice_no'     => 'nullable|string',
    //         'vendor_id'      => 'nullable|exists:vendors,id',
    //         'purchase_date'  => 'nullable|date',
    //         'warehouse_id'   => 'nullable|exists:warehouses,id',
    //         'note'           => 'nullable|string',
    //         'discount'       => 'nullable|numeric|min:0',
    //         'extra_cost'     => 'nullable|numeric|min:0',

    //         // Purchase Items
    //         'product_id'       => 'nullable|array',
    //         'product_id.*'     => 'nullable|exists:products,id',
    //         'qty'              => 'nullable|array',
    //         'qty.*'            => 'nullable|numeric|min:1',
    //         'price'            => 'nullable|array',
    //         'price.*'          => 'nullable|numeric|min:0',
    //         'unit'             => 'nullable|array',
    //         'unit.*'           => 'nullable|string',
    //         'item_discount'    => 'nullable|array',
    //         'item_discount.*'  => 'nullable|numeric|min:0',
    //     ]);

    //     DB::transaction(function () use ($validated, $request) {

    //         // 🧾 Generate Next Invoice No
    //         $lastInvoice = Purchase::latest()->value('invoice_no');
    //         $nextInvoice = $lastInvoice
    //             ? 'INV-' . str_pad(((int) filter_var($lastInvoice, FILTER_SANITIZE_NUMBER_INT)) + 1, 5, '0', STR_PAD_LEFT)
    //             : 'INV-00001';

    //         // ✍️ Create Purchase with temporary values
    //         $purchase = Purchase::create([
    //             'branch_id'     => auth()->user()->id,
    //             'warehouse_id'  => $validated['warehouse_id'],
    //             'vendor_id'     => $validated['vendor_id'] ?? null,
    //             'purchase_date' => $validated['purchase_date'] ?? now(),
    //             'invoice_no'    => $validated['invoice_no'] ?? $nextInvoice,
    //             'note'          => $validated['note'] ?? null,
    //             'subtotal'      => 0,
    //             'discount'      => 0,
    //             'extra_cost'    => 0,
    //             'net_amount'    => 0,
    //             'paid_amount'   => 0,
    //             'due_amount'    => 0,
    //         ]);

    //         $subtotal = 0;

    //         // 🧾 Purchase Items
    //         $productIds = $validated['product_id'] ?? [];
    //         foreach ($productIds as $index => $productId) {
    //             $qty   = $validated['qty'][$index] ?? null;
    //             $price = $validated['price'][$index] ?? null;

    //             if (empty($productId) || empty($qty) || empty($price)) {
    //                 continue;
    //             }

    //             $disc = $validated['item_discount'][$index] ?? 0; // ✅ Correct name
    //             $unit = $validated['unit'][$index] ?? null;

    //             $lineTotal = ($price * $qty) - $disc;

    //             PurchaseItem::create([
    //                 'purchase_id'   => $purchase->id,
    //                 'product_id'    => $productId,
    //                 'unit'          => $unit,
    //                 'price'         => $price,
    //                 'item_discount' => $disc,
    //                 'qty'           => $qty,
    //                 'line_total'    => $lineTotal,
    //             ]);

    //             $subtotal += $lineTotal;

    //             // 📦 Update Stock
    //             $stock = Stock::where('branch_id', auth()->user()->id)
    //                 ->where('warehouse_id', $validated['warehouse_id'])
    //                 ->where('product_id', $productId)
    //                 ->first();

    //             if ($stock) {
    //                 $stock->qty += $qty;
    //                 $stock->save();
    //             } else {
    //                 Stock::create([
    //                     'branch_id'     => auth()->user()->id,
    //                     'warehouse_id'  => $validated['warehouse_id'],
    //                     'product_id'    => $productId,
    //                     'qty'           => $qty,
    //                 ]);
    //             }
    //         }

    //         // 💵 Final Calculations (use values from request safely)
    //         $discount   = $request->discount ?? 0;
    //         $extraCost  = $request->extra_cost ?? 0;
    //         $netAmount  = ($subtotal - $discount) + $extraCost;

    //         $purchase->update([
    //             'subtotal'    => $subtotal,
    //             'discount'    => $discount,
    //             'extra_cost'  => $extraCost,
    //             'net_amount'  => $netAmount,
    //             'due_amount'  => $netAmount,
    //         ]);

    //         // 📘 Vendor Ledger Update
    //         $previousBalance = VendorLedger::where('vendor_id', $validated['vendor_id'])
    //             ->value('closing_balance') ?? 0;

    //         $newClosingBalance = $previousBalance + $netAmount;

    //         VendorLedger::updateOrCreate(
    //             ['vendor_id' => $validated['vendor_id']],
    //             [
    //                 'vendor_id'         => $validated['vendor_id'],
    //                 'admin_or_user_id'  => auth()->id(),
    //                 'previous_balance'  => $subtotal,
    //                 'closing_balance'   => $newClosingBalance,
    //             ]
    //         );
    //     });

    //     return back()->with('success', 'Purchase saved successfully!');
    // }

    // public function store(Request $request)
    // {

    //         $validated = $request->validate([
    //             'invoice_no'     => 'nullable|string',
    //             'vendor_id'      => 'nullable|exists:vendors,id',
    //             // 'branch_id'      => 'required|exists:branches,id',
    //             'purchase_date'  => 'nullable|date',
    //             'warehouse_id'   => 'nullable|exists:warehouses,id',
    //             'note'           => 'nullable|string',
    //     'discount'       => 'nullable|numeric|min:0',
    //     'extra_cost'     => 'nullable|numeric|min:0',

    //             // Purchase Items
    //             'product_id'     => 'nullable|array',
    //             'product_id.*'   => 'nullable|exists:products,id',
    //             'qty'            => 'nullable|array',
    //             'qty.*'          => 'nullable|numeric|min:1',
    //             'price'          => 'nullable|array',
    //             'price.*'        => 'nullable|numeric|min:0',
    //             'unit'           => 'nullable|array',
    //             'unit.*'         => 'nullable|string',
    //             'item_discount'  => 'nullable|array',
    //             'item_discount.*'=> 'nullable|numeric|min:0',
    //         ]);
    // DB::transaction(function () use ($validated) {

    //     $lastInvoice = Purchase::latest()->value('invoice_no');

    //     $nextInvoice = $lastInvoice
    //         ? 'INV-' . str_pad(((int) filter_var($lastInvoice, FILTER_SANITIZE_NUMBER_INT)) + 1, 5, '0', STR_PAD_LEFT)
    //         : 'INV-00001';

    //     // 1️⃣ Create purchase
    //     $purchase = Purchase::create([
    //         'branch_id'     => Auth()->user()->id,
    //         'warehouse_id'  => $validated['warehouse_id'],
    //         'vendor_id'     => $validated['vendor_id'] ?? null,
    //         'purchase_date' => $validated['purchase_date'] ?? now(),
    //         'invoice_no'    => $validated['invoice_no'] ?? $nextInvoice,
    //         'note'          => $validated['note'] ?? null,
    //         'subtotal'      => $validated['subtotal'] ?? 0,
    //         'discount'      => $validated['discount'] ?? 0,
    //         'extra_cost'    => $validated['extra_cost'] ?? 0,
    //         'net_amount'    => $validated['net_amount'] ?? 0,
    //         'paid_amount'   => 0,
    //         'due_amount'    => 0,

    //     ]);

    //     $subtotal = 0;

    //     // 2️⃣ Loop & filter rows
    //     $productIds = $validated['product_id'] ?? [];
    //     foreach ($productIds as $index => $productId) {
    //         $qty   = $validated['qty'][$index] ?? null;
    //         $price = $validated['price'][$index] ?? null;

    //         // Skip row if any essential field is empty
    //         if (empty($productId) || empty($qty) || empty($price)) {
    //             continue;
    //         }

    //         $disc = $validated['item_disc'][$index] ?? 0;
    //         $unit = $validated['unit'][$index] ?? null;

    //         $lineTotal = ($price * $qty) - $disc;

    //         // Save item
    //         PurchaseItem::create([
    //             'purchase_id'   => $purchase->id,
    //             'product_id'    => $productId,
    //             'unit'          => $unit,
    //             'price'         => $price,
    //             'item_discount' => $disc,
    //             'qty'           => $qty,
    //             'line_total'    => $lineTotal,
    //         ]);

    //         $subtotal += $lineTotal;

    //         // 3️⃣ Update stock
    //         $stock = Stock::where('branch_id', Auth()->user()->id)
    //             ->where('warehouse_id', $validated['warehouse_id'])
    //             ->where('product_id', $productId)
    //             ->first();

    //         if ($stock) {
    //             $stock->qty += $qty;
    //             $stock->save();
    //         } else {
    //             Stock::create([
    //                 'branch_id'     => Auth()->user()->id,
    //                 'warehouse_id'  => $validated['warehouse_id'],
    //                 'product_id'    => $productId,
    //                 'qty'           => $qty,
    //             ]);
    //         }
    //     }

    //     // 4️⃣ Update totals
    //     $purchase->update([
    //         'subtotal'    => $subtotal,
    //         'net_amount'  => $subtotal,
    //         'due_amount'  => $subtotal,
    //     ]);

    //     // 5️⃣ Vendor ledger
    //     $previousBalance = VendorLedger::where('vendor_id', $validated['vendor_id'])
    //         ->value('closing_balance') ?? 0;

    //     $newClosingBalance = $previousBalance + $subtotal;

    //     VendorLedger::updateOrCreate(
    //         ['vendor_id' => $validated['vendor_id']],
    //         [
    //             'vendor_id' => $validated['vendor_id'],
    //             'admin_or_user_id' => Auth::id(),
    //             'previous_balance' => $subtotal,
    //             'closing_balance' => $newClosingBalance,
    //         ]
    //     );

    // });

    // // DB::transaction(function () use ($validated) {

    // // $lastInvoice = Purchase::latest()->value('invoice_no');

    // // // Agar last invoice mila to +1 karo, warna start karo INV-00001
    // // $nextInvoice = $lastInvoice
    // //     ? 'INV-' . str_pad(((int) filter_var($lastInvoice, FILTER_SANITIZE_NUMBER_INT)) + 1, 5, '0', STR_PAD_LEFT)
    // //     : 'INV-00001';

    // //     // 1️⃣ Save main Purchase
    // //     $purchase = Purchase::create([

    // //         'branch_id'     => Auth()->user()->id,
    // //         'warehouse_id'  => $validated['warehouse_id'],
    // //         'vendor_id'     => $validated['vendor_id'] ?? null,
    // //         'purchase_date' => $validated['purchase_date'] ?? now(),
    // //         'invoice_no'    => $validated['invoice_no'] ?? $nextInvoice,
    // //         'note'          => $validated['note'] ?? null,
    // //         'subtotal'      => 0,
    // //         'discount'      => 0,
    // //         'extra_cost'    => 0,
    // //         'net_amount'    => 0,
    // //         'paid_amount'   => 0,
    // //         'due_amount'    => 0,
    // //     ]);

    // //     $subtotal = 0;

    // //     // 2️⃣ Loop purchase items
    // //     foreach ($validated['product_id'] as $index => $productId) {
    // //         $qty     = $validated['qty'][$index];
    // //         $price   = $validated['price'][$index];
    // //         $disc    = $validated['item_discount'][$index] ?? 0;
    // //         $lineTotal = ($price * $qty) - $disc;

    // //         // Save purchase item
    // //         PurchaseItem::create([
    // //             'purchase_id'   => $purchase->id,
    // //             'product_id'    => $productId,
    // //             'unit'          => $validated['unit'][$index] ?? null,
    // //             'price'         => $price,
    // //             'item_discount' => $disc,
    // //             'qty'           => $qty,
    // //             'line_total'    => $lineTotal,
    // //         ]);

    // //         $subtotal += $lineTotal;

    // //         // 3️⃣ Update stock
    // //         $stock = Stock::where('branch_id',  Auth()->user()->id,)
    // //             ->where('warehouse_id', $validated['warehouse_id'])
    // //             ->where('product_id', $productId)
    // //             ->first();

    // //         if ($stock) {
    // //             $stock->qty += $qty;
    // //             $stock->save();
    // //         } else {
    // //             Stock::create([
    // //                 'branch_id'     => Auth()->user()->id,
    // //                 'warehouse_id'  => $validated['warehouse_id'],
    // //                 'product_id'    => $productId,
    // //                 'qty'           => $qty,
    // //             ]);
    // //         }
    // //     }

    // //     // 4️⃣ Update totals in purchase
    // //     $purchase->update([
    // //         'subtotal'    => $subtotal,
    // //         'net_amount'  => $subtotal,
    // //         'due_amount'  => $subtotal,
    // //     ]);

    // //     $previousBalance = VendorLedger::where('vendor_id', $validated['vendor_id'])
    // //         ->value('closing_balance') ?? 0; // If no previous balance, start from 0
    // //     // Calculate new balances

    // //     $newPreviousBalance = $subtotal;

    // //     $newClosingBalance = $previousBalance + $subtotal;
    // //     $userId = Auth::id();

    // //     // Update or create distributor ledger
    // //     VendorLedger::updateOrCreate(
    // //         ['vendor_id' => $validated['vendor_id']],
    // //         [
    // //             'vendor_id' => $validated['vendor_id'],
    // //             'admin_or_user_id' => $userId,
    // //             'previous_balance' => $newPreviousBalance,
    // //             'closing_balance' => $newClosingBalance,
    // //         ]
    // //     );

    // });

    //     return redirect()->back()->with('success', 'Purchase saved successfully!');
    // }

    /**
     * Rollback the accounting, stock movements, warehouse stocks, and vendor ledger of an approved purchase.
     * Mirrors SaleController::rollbackPostedSale to ensure 100% data integrity on edit and delete.
     */
    private function rollbackPostedPurchase(Purchase $purchase): void
    {
        // 1. Rollback stock impact (if not linked to gatepass)
        $hasGatepass = \App\Models\InwardGatepass::where('purchase_id', $purchase->id)->exists();
        if (! $hasGatepass) {
            $branchId = (int) ($purchase->branch_id ?? 1);
            $warehouseId = (int) $purchase->warehouse_id;

            foreach ($purchase->items as $item) {
                $convFactor = 1;
                $unit = strtolower($item->unit ?? '');
                if (!empty($item->color)) {
                    $itemColor = $item->color;
                    $b64Decoded = base64_decode($itemColor, true);
                    $json = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                    if (!is_array($json)) {
                        $json = is_string($itemColor) ? json_decode($itemColor, true) : $itemColor;
                    }
                    if (is_array($json)) {
                        if (isset($json['conv_factor']) && (float)$json['conv_factor'] > 0) {
                            $convFactor = (float) $json['conv_factor'];
                        }
                        if (isset($json['unit'])) {
                            $unit = strtolower($json['unit']);
                        }
                    }
                }

                if ($unit === 'gm' || $unit === 'g' || $unit === 'gram' || $unit === 'grams') {
                    $baseQty = ((float) $item->qty) / 1000.0;
                } else {
                    $baseQty = ((float) $item->qty) * $convFactor;
                }

                // Reverse stock
                $this->upsertStocks((int)$item->product_id, -$baseQty, $branchId, $warehouseId);
            }

            // Delete stock movements for this purchase
            DB::table('stock_movements')
                ->whereIn('ref_type', ['PURCHASE', 'PURCHASE_EDIT', 'PURCHASE_DELETE'])
                ->where('ref_id', $purchase->id)
                ->delete();
        }

        // 2. Reverse & delete Vouchers & Journal Entries
        $journalService = app(\App\Services\JournalEntryService::class);
        $vouchers = \App\Models\VoucherMaster::where('remarks', 'like', "%#{$purchase->invoice_no}%")->get();
        foreach ($vouchers as $voucher) {
            $journalService->reverseEntriesForSource($voucher);
            \App\Models\VoucherDetail::where('voucher_master_id', $voucher->id)->delete();
            $voucher->delete();
        }

        // 3. Rollback Legacy Vendor Ledger Impact
        if ($purchase->vendor_id) {
            $netImpact = (float)$purchase->net_amount - (float)$purchase->paid_amount;
            $vendorLedger = \App\Models\VendorLedger::where('vendor_id', $purchase->vendor_id)->first();
            if ($vendorLedger && $netImpact != 0) {
                $vendorLedger->closing_balance -= $netImpact;
                $vendorLedger->save();
            }
        }
    }

    public function edit($id)
    {
        $purchase = Purchase::with(['items.product', 'vendor', 'warehouse'])->findOrFail($id);

        $Vendor = Vendor::all();
        $Warehouse = Warehouse::all();
        // Filter accounts to only show Cash (1) and Bank (2) heads
        $accounts = \App\Models\Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();

        // Get payment voucher lines for this purchase if any
        $paymentVoucher = \App\Models\VoucherMaster::with('details')
            ->where('voucher_type', \App\Models\VoucherMaster::TYPE_PAYMENT)
            ->where('remarks', 'like', "%#{$purchase->invoice_no}%")
            ->first();

        $paymentLines = collect();
        if ($paymentVoucher) {
            $paymentLines = $paymentVoucher->details->where('credit', '>', 0);
        }

        // Calculate vendor previous balance before this invoice
        $balanceService = app(\App\Services\BalanceService::class);
        $currentVendorBal = $purchase->vendor_id ? $balanceService->getVendorBalance($purchase->vendor_id) : 0;

        if ($purchase->status_purchase === 'approved') {
            $prevVendorBalance = $currentVendorBal - ((float)$purchase->net_amount - (float)$purchase->paid_amount);
        } else {
            $prevVendorBalance = $currentVendorBal;
        }

        return view('admin_panel.purchase.edit', compact('purchase', 'Vendor', 'Warehouse', 'accounts', 'paymentLines', 'prevVendorBalance'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'invoice_no' => 'nullable|string',
                'vendor_id' => 'required|exists:vendors,id',
                'purchase_date' => 'nullable|date',
                'branch_id' => 'nullable',
                'warehouse_id' => 'nullable',
                'note' => 'nullable|string',
                'discount' => 'nullable|numeric|min:0',
                'extra_cost' => 'nullable|numeric|min:0',

                'product_id' => 'required|array|min:1',
                'product_id.*' => 'required|exists:products,id',
                'qty' => 'required|array|min:1',
                'qty.*' => 'required|numeric|min:0.01',
                'price' => 'required|array|min:1',
                'price.*' => 'required|numeric|min:0',
                'unit' => 'array',
                'unit.*' => 'nullable|string',
                'item_discount' => 'nullable|array',
                'item_discount.*' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors(), 'message' => 'Validation Error'], 422);
            }
            throw $e;
        }

        try {
            $purchase = DB::transaction(function () use ($validated, $request, $id) {
                $purchase = Purchase::with(['items.product', 'vendor'])->findOrFail($id);

                // Determine target status
                $wasApproved = ($purchase->status_purchase === 'approved');
                $targetStatus = $wasApproved ? 'approved' : (($request->action === 'save_only') ? 'draft' : 'approved');

                // 1. If previously approved, rollback previous stock, accounting & ledger impact first
                if ($wasApproved) {
                    $this->rollbackPostedPurchase($purchase);
                }

                // Resilient Branch resolution
                $branchId = (int) ($validated['branch_id'] ?? $purchase->branch_id ?? 0);
                if ($branchId <= 0 || ! \App\Models\Branch::where('id', $branchId)->exists()) {
                    $userBranch = auth()->user()->branch_id ?? null;
                    if ($userBranch && \App\Models\Branch::where('id', $userBranch)->exists()) {
                        $branchId = (int) $userBranch;
                    } else {
                        $firstBranch = \App\Models\Branch::first();
                        $branchId = $firstBranch ? (int) $firstBranch->id : 1;
                    }
                }

                // Resilient Warehouse resolution
                $warehouseId = (int) ($validated['warehouse_id'] ?? $purchase->warehouse_id ?? 0);
                if ($warehouseId <= 0 || ! \App\Models\Warehouse::where('id', $warehouseId)->exists()) {
                    $firstWarehouse = \App\Models\Warehouse::first();
                    $warehouseId = $firstWarehouse ? (int) $firstWarehouse->id : 1;
                }

                // 2. Header Update
                $purchase->update([
                    'vendor_id' => $validated['vendor_id'],
                    'branch_id' => $branchId,
                    'warehouse_id' => $warehouseId,
                    'purchase_date' => $validated['purchase_date'] ?? $purchase->purchase_date,
                    'invoice_no' => $validated['invoice_no'] ?? $purchase->invoice_no,
                    'note' => $validated['note'] ?? null,
                    'purchase_type' => $request->purchase_type ?? $purchase->purchase_type ?? 'local',
                    'currency' => $request->currency ?? $purchase->currency ?? 'PKR',
                    'exchange_rate' => $request->exchange_rate ?? $purchase->exchange_rate ?? 1.0,
                    'proforma_invoice_no' => $request->proforma_invoice_no ?? $purchase->proforma_invoice_no,
                    'payment_method' => $request->payment_method ?? $purchase->payment_method,
                    'delivery_terms' => $request->delivery_terms ?? $purchase->delivery_terms,
                    'expected_delivery_date' => $request->expected_delivery_date ?? $purchase->expected_delivery_date,
                    'status_purchase' => $targetStatus,
                ]);

                // 3. Delete old items and re-create updated items
                $purchase->items()->delete();

                $subtotal = 0;
                $pids = $validated['product_id'] ?? [];
                $qtys = $validated['qty'] ?? [];
                $prices = $validated['price'] ?? [];
                $units = $validated['unit'] ?? [];
                $itemDiscs = $validated['item_discount'] ?? [];

                // Snapshot fields
                $sizeModes = $request->size_mode ?? [];
                $ppbs = $request->pieces_per_box ?? [];
                $ppm2 = $request->pieces_per_m2 ?? [];
                $boxesQtys = $request->boxes_qty ?? [];
                $looseQtys = $request->loose_qty ?? [];
                $lengths = $request->length ?? [];
                $widths = $request->width ?? [];
                $colors = $request->color ?? [];

                foreach ($pids as $i => $pid) {
                    $pid = (int) $pid;
                    $qty = (float) ($qtys[$i] ?? 0);
                    $price = (float) ($prices[$i] ?? 0);

                    if (!$pid || $qty <= 0) {
                        continue;
                    }

                    $discPercent = (float) ($itemDiscs[$i] ?? 0);
                    $unit = $units[$i] ?? null;

                    $curSizeMode = $sizeModes[$i] ?? null;
                    $curPPM2 = (float) ($ppm2[$i] ?? 0);

                    if ($curSizeMode === 'by_size') {
                        $grossTotal = $curPPM2 * $qty * $price;
                    } elseif (strtolower($unit ?? '') === 'gm' || strtolower($unit ?? '') === 'g') {
                        $grossTotal = ($qty / 1000.0) * $price;
                    } else {
                        $grossTotal = $qty * $price;
                    }

                    $discAmount = $grossTotal * ($discPercent / 100);
                    $lineTotal = max(0, $grossTotal - $discAmount);

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $pid,
                        'unit' => $unit,
                        'price' => $price,
                        'item_discount' => $discAmount,
                        'qty' => $qty,
                        'line_total' => $lineTotal,
                        'color' => $colors[$i] ?? null,
                        'size_mode' => $curSizeMode,
                        'pieces_per_box' => $ppbs[$i] ?? 1,
                        'pieces_per_m2' => $curPPM2,
                        'boxes_qty' => $boxesQtys[$i] ?? 0,
                        'loose_qty' => $looseQtys[$i] ?? 0,
                        'length' => $lengths[$i] ?? null,
                        'width' => $widths[$i] ?? null,
                    ]);

                    $subtotal += $lineTotal;
                }

                // 4. Totals Calculation
                $discount = (float) ($request->discount ?? 0);
                $extraCost = (float) ($request->extra_cost ?? 0);
                $netAmount = max(0, ($subtotal - $discount) + $extraCost);

                $purchase->update([
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'additional_discount' => $discount,
                    'extra_cost' => $extraCost,
                    'net_amount' => $netAmount,
                    'paid_amount' => 0,
                    'due_amount' => $netAmount,
                ]);

                // 5. If target status is approved, run full approval & payment processing
                if ($targetStatus === 'approved') {
                    $purchase->load(['items.product', 'vendor']);

                    // Stock Movements + Product Prices + Vendor Ledger + Purchase Voucher
                    $this->approvePurchase($purchase);

                    // Process Payment Voucher if payments provided
                    try {
                        $transactionService = app(\App\Services\TransactionService::class);
                        $paymentAccountIds = $request->input('payment_account_id', []);
                        $paymentAmounts = $request->input('payment_amount', []);

                        if (!empty(array_filter($paymentAccountIds))) {
                            $transactionService->createPaymentForPurchase(
                                $purchase,
                                $paymentAccountIds,
                                $paymentAmounts
                            );
                        }
                    } catch (\Exception $e) {
                        \Log::error('Purchase Payment Error on Update: ' . $e->getMessage());
                    }
                }

                return $purchase;
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase updated successfully.',
                    'invoice_url' => route('purchase.invoice', $purchase->id),
                    'redirect_url' => route('Purchase.home'),
                ]);
            }

            return redirect()->route('Purchase.home')->with('success', 'Purchase updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Purchase Update Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating purchase: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Error updating purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $purchase = Purchase::with('items')->findOrFail($id);

            // If approved, rollback all stock, movements, accounting vouchers, and vendor ledger first
            if ($purchase->status_purchase === 'approved') {
                $this->rollbackPostedPurchase($purchase);
            }

            $purchase->items()->delete();
            $purchase->delete();
        });

        return redirect()->back()->with('success', 'Purchase deleted successfully.');
    }

    public function Invoice($id)
    {
        $purchase = Purchase::with(['items.product', 'vendor', 'warehouse'])->findOrFail($id);
        
        $balanceService = app(\App\Services\BalanceService::class);
        $vendor_balance = $balanceService->getVendorBalance($purchase->vendor_id);

        $previousBalance = 0;
        if ($purchase->vendor_id) {
            $voucher = \App\Models\VoucherMaster::where('remarks', "Purchase Voucher #{$purchase->invoice_no}")
                ->orderByRaw("ABS(TIMESTAMPDIFF(SECOND, created_at, '{$purchase->created_at}'))")
                ->first();
            $journalEntry = null;
            if ($voucher) {
                $journalEntry = \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                    ->where('source_id', $voucher->id)
                    ->where('party_type', \App\Models\Vendor::class)
                    ->where('party_id', $purchase->vendor_id)
                    ->first();
            }

            if ($journalEntry) {
                $previousBalance = \App\Models\JournalEntry::where('party_type', \App\Models\Vendor::class)
                    ->where('party_id', $purchase->vendor_id)
                    ->where('id', '<', $journalEntry->id)
                    ->sum(\Illuminate\Support\Facades\DB::raw('credit - debit'));
            } else {
                $previousBalance = $balanceService->getVendorBalanceBeforeDate($purchase->vendor_id, $purchase->created_at->format('Y-m-d H:i:s'));
            }
        }
        $currentBalance = $previousBalance + $purchase->net_amount - $purchase->paid_amount;

        return view('admin_panel.purchase.Invoice', compact('purchase', 'vendor_balance', 'previousBalance', 'currentBalance'));
    }

    public function receipt($id)
    {
        $purchase = Purchase::with(['items.product', 'vendor'])->findOrFail($id);

        $balanceService = app(\App\Services\BalanceService::class);
        $previousBalance = 0;
        if ($purchase->vendor_id) {
            $voucher = \App\Models\VoucherMaster::where('remarks', "Purchase Voucher #{$purchase->invoice_no}")
                ->orderByRaw("ABS(TIMESTAMPDIFF(SECOND, created_at, '{$purchase->created_at}'))")
                ->first();
            $journalEntry = null;
            if ($voucher) {
                $journalEntry = \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                    ->where('source_id', $voucher->id)
                    ->where('party_type', \App\Models\Vendor::class)
                    ->where('party_id', $purchase->vendor_id)
                    ->first();
            }

            if ($journalEntry) {
                $previousBalance = \App\Models\JournalEntry::where('party_type', \App\Models\Vendor::class)
                    ->where('party_id', $purchase->vendor_id)
                    ->where('id', '<', $journalEntry->id)
                    ->sum(\Illuminate\Support\Facades\DB::raw('credit - debit'));
            } else {
                $previousBalance = $balanceService->getVendorBalanceBeforeDate($purchase->vendor_id, $purchase->created_at->format('Y-m-d H:i:s'));
            }
        }
        $currentBalance = $previousBalance + $purchase->net_amount - $purchase->paid_amount;

        return view('admin_panel.purchase.receipt', compact('purchase', 'previousBalance', 'currentBalance'));
    }

    // purchase_reutun

    public function showReturnForm($id)
    {
        $purchase = Purchase::with(['vendor', 'warehouse', 'items.product'])->findOrFail($id);
        $accounts = \App\Models\Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();
        // Identify max returnable qty: Purchase Qty - Already Returned Qty
        // 1. Get all previous returns for this purchase
        $pastReturns = \App\Models\PurchaseReturn::where('purchase_id', $id)
            ->with('items')
            ->get();
        
        $returnedQtyMap = [];
        foreach ($pastReturns as $pr) {
            foreach ($pr->items as $prItem) {
                $key = $prItem->product_id . '_' . ($prItem->color ?? '');
                if (!isset($returnedQtyMap[$key])) {
                    $returnedQtyMap[$key] = 0;
                }
                $returnedQtyMap[$key] += $prItem->qty;
            }
        }
        
        $purchaseItems = [];
        $hasReturnableItems = false;
        
        foreach ($purchase->items as $item) {
            $key = $item->product_id . '_' . ($item->color ?? '');
            $alreadyReturned = $returnedQtyMap[$key] ?? 0;
            $remaining = max(0, $item->qty - $alreadyReturned);
            
            if ($remaining > 0) {
                 $hasReturnableItems = true;
            }
            
            $itemName = optional($item->product)->item_name ?? 'Unknown Product';
            $colorStr = '';
            if (!empty($item->color)) {
                $decoded = base64_decode($item->color, true);
                if ($decoded !== false && is_string($decoded) && str_starts_with(trim($decoded), '{')) {
                    $parsed = json_decode($decoded, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                        $parts = [];
                        if (!empty($parsed['size']) && $parsed['size'] !== '-') $parts[] = $parsed['size'];
                        if (!empty($parsed['color']) && $parsed['color'] !== '-') $parts[] = $parsed['color'];
                        $colorStr = implode(' | ', $parts);
                    } else {
                        $colorStr = $item->color;
                    }
                } else {
                    $colorStr = $item->color;
                }
            }
            if ($colorStr) {
                $itemName .= ' (' . $colorStr . ')';
            }
            
            $purchaseItems[] = [
                'product_id' => $item->product_id,
                'item_name' => $itemName,
                'brand' => optional(optional($item->product)->brand)->name ?? '',
                'item_code' => optional($item->product)->item_code ?? '',
                // Fix: Prioritize Product Master PPB to ensure correct Frontend Box calculation
                'pieces_per_box' => (optional($item->product)->pieces_per_box > 0) ? $item->product->pieces_per_box : ($item->pieces_per_box ?? 1),
                'size_mode' => $item->size_mode ?? optional($item->product)->size_mode ?? 'by_pieces',
                'pieces_per_m2' => $item->pieces_per_m2 ?? optional($item->product)->pieces_per_m2 ?? 0,
                'price' => $item->price,
                
                // Qty Logic for Partial Return
                'original_qty' => $item->qty,
                'returned_qty' => $alreadyReturned,
                'qty' => $remaining, // Current Limit
                
                'unit' => $item->unit ?? 'pc',
                'discount' => $item->item_discount,
                'color' => $item->color,
            ];
        }
        
        // Block access if fully returned
        if (!$hasReturnableItems && $purchase->status_purchase == 'Returned') {
             // Or if total remaining is 0. 
             // Logic: If status is 'Returned', maybe it was fully returned? 
             // But partial return also sets status to 'Returned' in current logic (we might want to change that to 'Partial' later).
             // For now, relies on calculated remaining quantity.
             return redirect()->route('purchase.return.index')->with('error', 'This purchase has clearly been fully returned already.');
        }

        return view('admin_panel.purchase.purchase_return.create', compact('purchase', 'accounts', 'purchaseItems'));
    }

    // store return
    public function storeReturn(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => 'nullable|exists:purchases,id',
            'vendor_id' => 'required|exists:vendors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'return_reason' => 'nullable|string|max:255',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array', // Pieces (Total Pieces)
            'qty.*' => 'required|numeric|min:0', // Allow 0 if partial
            'price' => 'required|array',
            'payment_account_id' => 'nullable|array',
            'payment_amount' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // 1. Generate Return Invoice #
            $lastReturn = PurchaseReturn::latest()->first();
            $nextInvoice = 'PRTN-'.str_pad(optional($lastReturn)->id + 1 ?? 1, 5, '0', STR_PAD_LEFT);

            // 2. Create Purchase Return Record
            $purchase = $request->purchase_id ? Purchase::find($request->purchase_id) : null;
            $remarks = $request->return_reason;
            if ($purchase) {
                $remarks .= ' (Ref Invoice: '.$purchase->invoice_no.')';
            }

            $return = PurchaseReturn::create([
                'purchase_id' => $purchase ? $purchase->id : null, 
                'vendor_id' => $validated['vendor_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'return_invoice' => $nextInvoice,
                'return_date' => $validated['return_date'],
                'return_reason' => $validated['return_reason'],
                'remarks' => $remarks,
                'bill_amount' => 0, // calculated below
                'item_discount' => 0,
                'extra_discount' => $request->extra_discount ?? 0,
                'net_amount' => 0,
                'paid' => 0,
                'balance' => 0,
            ]);

            $subtotal = 0;
            $totalItemDiscount = 0;
            $movements = [];
            $now = now();

            // 3. Process Items & Stock
            foreach ($validated['product_id'] as $index => $productId) {
                $qty = (float) ($validated['qty'][$index] ?? 0); // Pieces
                $price = (float) ($validated['price'][$index] ?? 0);

                if ($qty <= 0) {
                    continue;
                }

                // Find original item to get snapshots
                $origItem = \App\Models\PurchaseItem::where('purchase_id', $purchase->id ?? 0)
                    ->where('product_id', $productId)
                    ->first();

                // Fallback to Product defaults if no original item
                $product = Product::find($productId);
                $ppb = $origItem ? ($origItem->pieces_per_box ?? 1) : ($product->pieces_per_box ?? 1);
                $sizeMode = $origItem ? ($origItem->size_mode ?? 'by_pieces') : ($product->size_mode ?? 'by_pieces');
                
                // Fallback to m2_of_box if pieces_per_m2 is 0 or missing in old data
                $ppm2 = $origItem && $origItem->pieces_per_m2 > 0 ? $origItem->pieces_per_m2 : ($product->m2_of_box ?? 0);

                // Calculate Line Total Logic (Same as Purchase Store)
                if ($sizeMode === 'by_size') {
                    // price is per m2. Gross = TotalPieces * m2_per_piece * price_per_m2
                    $lineTotal = round($ppm2 * $qty * $price, 2);
                } elseif ($sizeMode === 'by_cartons' || $sizeMode === 'by_carton') {
                    $lineTotal = $qty * $price;
                } else {
                    $lineTotal = $qty * $price;
                }

                $itemDisc = 0;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'product_id' => $productId,
                    'qty' => $qty, // Pieces
                    'price' => $price,
                    'item_discount' => $itemDisc,
                    'unit' => 'pc', // Default
                    'line_total' => $lineTotal,
                    'color' => $request->color[$index] ?? null,
                ]);

                // Check for weight product conversion factor
                $stockQty = $qty;
                $colorField = $request->color[$index] ?? null;
                if (!empty($colorField)) {
                    try {
                        $b64Decoded = base64_decode($colorField, true);
                        $variantData = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                        if (!is_array($variantData)) {
                            $variantData = is_string($colorField) ? json_decode($colorField, true) : $colorField;
                        }
                        if (is_array($variantData) && isset($variantData['conv_factor'])) {
                            $factor = (float)$variantData['conv_factor'];
                            if ($factor > 0) {
                                $stockQty = $stockQty * $factor;
                            }
                        }
                    } catch (\Exception $e) {}
                }

                // Update Stock (DECREMENT)
                $stock = WarehouseStock::where('warehouse_id', $validated['warehouse_id'])
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $product = Product::find($productId); // Ensure fresher product data
                    $ppbVal = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
                    
                    // Use total_pieces as primary source of truth to avoid losing loose pieces, fallback only if zero
                    $currentTotalPieces = $stock->total_pieces;
                    if ($currentTotalPieces == 0 && $stock->quantity > 0) {
                        $currentTotalPieces = $stock->quantity * $ppbVal;
                    }
                    
                    // Subtract Return Qty (Pieces)
                    $newTotalPieces = max(0, $currentTotalPieces - $stockQty);
                    
                    $stock->total_pieces = $newTotalPieces;
                    $stock->quantity = $newTotalPieces / $ppbVal; // Convert back to Boxes
                    $stock->save();
                } else {
                     // Should technically not happen for return, but handle gracefully
                     $product = Product::find($productId);
                     $ppbVal = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
                     
                     WarehouseStock::create([
                        'warehouse_id' => $validated['warehouse_id'],
                        'product_id' => $productId,
                        'total_pieces' => -$stockQty, // Negative stock?
                        'quantity' => -$stockQty / $ppbVal,
                        'price' => 0
                     ]);
                }

                // Prepare Stock Movement
                $movements[] = [
                    'product_id' => $productId,
                    'type' => 'out', // Return OUT to vendor
                    'qty' => $stockQty,
                    'ref_type' => 'PURCHASE_RETURN',
                    'ref_id' => $return->id,
                    'note' => "Purchase Return",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $subtotal += $lineTotal;
                $totalItemDiscount += $itemDisc;
            }

            // Bulk Insert Movements
            if (! empty($movements)) {
                DB::table('stock_movements')->insert($movements);
            }

            $netAmount = ($subtotal - $totalItemDiscount) - ($request->extra_discount ?? 0);

            // 4. Handle Refund Payment
            $totalPaid = 0;
            if (! empty($request->payment_account_id)) {
                $voucherService = app(\App\Services\VoucherService::class);
                $apId = app(\App\Services\BalanceService::class)->getAccountsPayableId();

                foreach ($request->payment_account_id as $idx => $accId) {
                    $amt = (float) ($request->payment_amount[$idx] ?? 0);
                    if ($accId && $amt > 0) {
                        $totalPaid += $amt;
                        
                        // Create Receipt Voucher via Service (Cash In)
                        $voucherData = [
                            'voucher_type' => \App\Models\VoucherMaster::TYPE_RECEIPT,
                            'date' => $validated['return_date'],
                            'status' => \App\Models\VoucherMaster::STATUS_POSTED,
                            'party_type' => \App\Models\Vendor::class,
                            'party_id' => $validated['vendor_id'],
                            'remarks' => "Refund for Return #{$nextInvoice}",
                        ];

                        $lines = [
                            [
                                'account_id' => $accId, 
                                'debit' => $amt,
                                'credit' => 0,
                                'narration' => 'Cash Refund Received'
                            ],
                            [
                                'account_id' => $apId,
                                'debit' => 0,
                                'credit' => $amt,
                                'narration' => 'Refund from Vendor'
                            ]
                        ];
                        
                        $voucherService->createVoucher($voucherData, $lines, auth()->id());
                    }
                }
            }

            $return->update([
                'bill_amount' => $subtotal,
                'item_discount' => $totalItemDiscount,
                'net_amount' => $netAmount,
                'paid' => $totalPaid,
                'balance' => $netAmount - $totalPaid,
            ]);

            // Update Purchase Status (only if full return)
            if ($purchase) {
                $totalBought = $purchase->items->sum('qty');
                $totalReturned = \App\Models\PurchaseReturnItem::join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                    ->where('purchase_returns.purchase_id', $purchase->id)
                    ->sum('purchase_return_items.qty');
                
                if ($totalReturned >= $totalBought) {
                    $purchase->update(['status_purchase' => 'Returned']);
                }
            }

            // 5. Update Vendor Ledger & Accounting
            // A. Create General Ledger Voucher for Return (Debit Vendor, Credit Purchase Return)
            $transactionService = app(\App\Services\TransactionService::class);
            if (method_exists($transactionService, 'createPurchaseReturnVoucher')) {
                 $transactionService->createPurchaseReturnVoucher($return);
            }

            // B. Update Legacy Vendor Ledger
            // Logic: Return reduces Payable (Debit Vendor).
            // Refund increases Payable back (Credit Vendor) - effectively clearing the Debit.
            
            $balanceChange = -($netAmount - $totalPaid); // Reduces payable

            // Using VendorLedger table manual update for legacy views
            $ledger = \App\Models\VendorLedger::where('vendor_id', $validated['vendor_id'])->latest()->first();
            $currentClosing = $ledger ? $ledger->closing_balance : 0; // Current closing
            
            // Note: VendorLedger table structure does not support transaction history (no date/desc columns),
            // so we treat it as a Balance Snapshot.
            \App\Models\VendorLedger::updateOrCreate(
                ['vendor_id' => $validated['vendor_id']],
                [
                    'admin_or_user_id' => auth()->id(),
                    'opening_balance' => $ledger ? $ledger->opening_balance : 0, 
                    'previous_balance' => $currentClosing,
                    'closing_balance' => $currentClosing + $balanceChange,
                ]
            );

            DB::commit();

            return redirect()->route('purchase.return.index')->with('success', 'Purchase return processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing return: '.$e->getMessage());
        }
    }

    public function purchaseReturnIndex()
    {
        $returns = \App\Models\PurchaseReturn::with(['vendor', 'warehouse', 'purchase'])->latest()->get();
        
        // Calculate updated financial details for each return
        $returns->each(function ($return) {
            if ($return->purchase) {
                $purchase = $return->purchase;
                
                // Original Purchase Amounts
                $return->original_net_amount = $purchase->net_amount;
                $return->original_paid_amount = $purchase->paid_amount;
                $return->original_due_amount = $purchase->due_amount;
                
                // Calculate total returns for this purchase
                $totalReturned = \App\Models\PurchaseReturn::where('purchase_id', $purchase->id)
                    ->sum('net_amount');
                
                // New amounts after return(s)
                $return->new_net_amount = max(0, $purchase->net_amount - $totalReturned);
                $return->new_due_amount = max(0, $purchase->due_amount - $return->net_amount);
                $return->total_returned = $totalReturned;
            }
        });

        return view('admin_panel.purchase.purchase_return.index', compact('returns'));
    }

    public function viewReturn($id)
    {
        $return = \App\Models\PurchaseReturn::with(['vendor', 'warehouse', 'items.product'])->findOrFail($id);
        return view('admin_panel.purchase.purchase_return.show', compact('return'));
    }

    /**
     * Store a simple Credit Purchase without product line items
     * Directly creates approved purchase, posts to double-entry journal, and updates vendor ledger
     */
    public function storeCreditPurchase(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'm_bill' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Generate Next Invoice
                $lastInvoice = Purchase::latest('id')->value('invoice_no');
                $nextInvoice = $lastInvoice
                    ? 'PUR-'.str_pad(((int) preg_replace('/[^0-9]/', '', $lastInvoice)) + 1, 3, '0', STR_PAD_LEFT)
                    : 'PUR-001';

                $warehouseId = \App\Models\Warehouse::first()->id ?? 1;
                $branchId = auth()->user()->branch_id ?? \App\Models\Branch::first()->id ?? 1;

                // Format note: M-Bill with description if provided
                $fullNote = trim($request->m_bill . ($request->description ? ' - ' . $request->description : ''));

                // 2. Create Purchase Record
                $purchase = Purchase::create([
                    'branch_id' => $branchId,
                    'warehouse_id' => $warehouseId,
                    'vendor_id' => $request->vendor_id,
                    'purchase_type' => 'local',
                    'currency' => 'PKR',
                    'exchange_rate' => 1.0,
                    'po_status' => 'complete',
                    'proforma_invoice_no' => $request->m_bill,
                    'payment_method' => 'credit',
                    'purchase_date' => $request->purchase_date,
                    'invoice_no' => $nextInvoice,
                    'note' => $fullNote,
                    'subtotal' => $request->amount,
                    'discount' => 0,
                    'additional_discount' => 0,
                    'extra_cost' => 0,
                    'net_amount' => $request->amount,
                    'paid_amount' => 0,
                    'due_amount' => $request->amount,
                    'status_purchase' => 'approved',
                    'created_by' => auth()->id(),
                ]);

                // 3. Update Legacy VendorLedger
                $netAmount = (float) $purchase->net_amount;
                $prevClosing = \App\Models\VendorLedger::where('vendor_id', $purchase->vendor_id)
                    ->value('closing_balance') ?? 0;

                \App\Models\VendorLedger::updateOrCreate(
                    ['vendor_id' => $purchase->vendor_id],
                    [
                        'vendor_id' => $purchase->vendor_id,
                        'admin_or_user_id' => auth()->id(),
                        'previous_balance' => $prevClosing,
                        'opening_balance' => $prevClosing,
                        'closing_balance' => $prevClosing + $netAmount,
                    ]
                );

                // 4. Double-Entry Accounting via TransactionService
                try {
                    $transactionService = app(\App\Services\TransactionService::class);
                    $transactionService->createPurchaseVoucher($purchase);
                } catch (\Exception $e) {
                    \Log::error('Accounting voucher creation error for credit purchase: ' . $e->getMessage());
                }

                $msg = "Credit Purchase #{$purchase->invoice_no} created successfully and posted to Vendor Ledger!";

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status'   => 'success',
                        'success'  => $msg,
                        'message'  => $msg,
                        'reload'   => true,
                        'purchase' => $purchase,
                    ]);
                }

                return redirect()->route('Purchase.home')->with('success', $msg);
            });
        } catch (\Exception $e) {
            \Log::error('Credit Purchase Creation Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'error'   => 'Failed to save credit purchase: ' . $e->getMessage(),
                    'message' => 'Failed to save credit purchase: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', 'Failed to save credit purchase: ' . $e->getMessage());
        }
    }
}
