<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleReturnController extends Controller
{
    public function showReturnForm($id)
    {
        $sale = Sale::resolveByIdOrInvoice($id, ['customer_relation', 'items.product.brand']);
        if (!$sale) {
            abort(404, 'Sale not found');
        }

        // Only posted sales can be returned
        if ($sale->sale_status !== 'posted') {
            if ($sale->sale_status === 'returned') {
                return redirect()->route('sale.index')->with('error', 'Sale invoice #' . $sale->invoice_no . ' has already been fully returned.');
            }
            return redirect()->route('sale.index')->with('error', 'Only posted sales can be returned. Current sale status is ' . ucfirst($sale->sale_status) . '.');
        }

        $id = $sale->id;
        $accounts = Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();
        
        // Calculate already returned quantities
        $pastReturns = SaleReturn::where('sale_id', $id)
            ->with('items')
            ->get();
        
        $returnedQtyMap = [];
        foreach ($pastReturns as $sr) {
            foreach ($sr->items as $srItem) {
                if ($srItem->is_manual) {
                    $key = 'MANUAL_' . strtolower(trim($srItem->product_name));
                } else {
                    $key = $srItem->product_id . '_' . ($srItem->color ?? '');
                }
                if (!isset($returnedQtyMap[$key])) {
                    $returnedQtyMap[$key] = 0;
                }
                $returnedQtyMap[$key] += $srItem->qty;
            }
        }
        
        // Format sale items with complete product data
        $hasReturnableItems = false;
        $sale->items->each(function ($item) use ($returnedQtyMap, &$hasReturnableItems) {
            $product = $item->product;
            if ($item->is_manual) {
                $key = 'MANUAL_' . strtolower(trim($item->product_name));
            } else {
                $key = $item->product_id . '_' . ($item->color ?? '');
            }
            $alreadyReturned = $returnedQtyMap[$key] ?? 0;
            
            // Extract variant information from sale item color field
            $variant = [];
            if (!empty($item->color)) {
                $b64Decoded = base64_decode($item->color, true);
                if ($b64Decoded !== false) {
                    $json = json_decode($b64Decoded, true);
                    if (is_array($json)) {
                        $variant = $json;
                    }
                }
                if (empty($variant)) {
                    $json = json_decode($item->color, true);
                    if (is_array($json)) {
                        $variant = $json;
                    }
                }
            }

            $sizeStr = $variant['size'] ?? ($variant['size_val'] ?? '-');
            $colorStr = $variant['color'] ?? ($variant['color_val'] ?? '-');

            $item->size_val = $sizeStr;
            $item->color_val = $colorStr;

            // Add product details with safe fallbacks
            $item->item_name = $product ? ($product->product_name ?? $product->item_name ?? 'Unknown') : ($item->product_name ?? 'Item');
            $item->item_code = $product ? ($product->product_code ?? $product->item_code ?? '') : ($item->product_code ?? '');
            
            // Fix brand - get name from relationship
            if ($product && $product->brand && is_object($product->brand)) {
                $item->brand = $product->brand->name ?? '';
            } else {
                $item->brand = $product ? ($product->brand_name ?? '') : '';
            }
            
            // Ensure pieces_per_box is numeric and valid
            $item->pieces_per_box = (int) ($product->pieces_per_box ?? $product->packet_size ?? 1);
            if ($item->pieces_per_box <= 0) {
                $item->pieces_per_box = 1;
            }
            
            $item->size_mode = $product ? ($product->size_mode ?? 'by_pieces') : 'by_pieces';
            $item->pieces_per_m2 = $product ? ($product->m2_of_box ?? 0) : 0;
            $item->unit = $item->unit ?? 'pc';
            
            // Quantity calculations
            $item->qty = $item->total_pieces ?? $item->qty ?? 0;
            $item->original_qty = $item->qty;
            $item->returned_qty = $alreadyReturned;
            $item->max_returnable = max(0, $item->qty - $alreadyReturned);
            if ($item->max_returnable > 0) {
                $hasReturnableItems = true;
            }
            
            // Pricing: use actual sale price (price_per_piece from POS/Sale), not product master price
            $item->price = (!empty($item->price_per_piece) && $item->price_per_piece > 0) 
                ? $item->price_per_piece 
                : ($item->price ?? $item->per_price ?? 0);
            $item->discount = $item->discount_amount ?? $item->discount ?? 0;
        });

        if (!$hasReturnableItems) {
            return redirect()->route('sale.index')->with('error', 'All items in sale invoice #' . $sale->invoice_no . ' have already been fully returned.');
        }

        // Financial tracking of the Sale Invoice
        $saleTotalNet = (float) $sale->total_net;
        $customerPaid = (float) ($sale->cash + ($sale->card ?? 0));
        
        $pastReturnedTotal = (float) $pastReturns->sum('net_amount');
        $pastRefundPaid = (float) $pastReturns->sum('paid');
        $pastDueAdjusted = (float) $pastReturns->sum('due_adjusted');

        $originalInvoiceDue = max(0, $saleTotalNet - $customerPaid);
        if ($pastDueAdjusted == 0 && $pastReturns->count() > 0) {
            $pastDueAdjusted = min($pastReturnedTotal, $originalInvoiceDue);
        }
        $remainingInvoiceDue = max(0, $originalInvoiceDue - $pastDueAdjusted);
        $remainingPaidRefundable = max(0, $customerPaid - $pastRefundPaid);
        $saleExtraDiscount = (float) ($sale->total_extradiscount ?? 0);
        
        return view('admin_panel.sale.sale_return.create', compact(
            'sale', 
            'accounts', 
            'returnedQtyMap',
            'saleTotalNet',
            'customerPaid',
            'remainingInvoiceDue',
            'remainingPaidRefundable',
            'saleExtraDiscount'
        ));
    }

    /**
     * Process the sale return
     */
    public function processSaleReturn(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'nullable|exists:sales,id',
            'customer_id' => 'nullable',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'color' => 'nullable|array',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:0',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
            'item_disc' => 'nullable|array',
            'extra_discount' => 'nullable|numeric|min:0',
            'return_reason' => 'nullable|string',
            'payment_account_id' => 'nullable|array',
            'payment_amount' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $sale = !empty($validated['sale_id']) ? Sale::with(['items', 'customer_relation'])->find($validated['sale_id']) : null;
            if ($sale && $sale->sale_status !== 'posted') {
                throw new \Exception("Only posted sales can be returned. Current sale status is " . ucfirst($sale->sale_status));
            }

            // Generate Return Invoice Number
            $lastReturnId = SaleReturn::max('id') ?? 0;
            do {
                $lastReturnId++;
                $nextInvoice = 'SR-' . str_pad($lastReturnId, 4, '0', STR_PAD_LEFT);
            } while (SaleReturn::where('return_invoice', $nextInvoice)->exists());

            // Resolve Customer ID (fallback to sale customer or Walking Customer)
            $customerId = $validated['customer_id'] ?? null;
            if (!$customerId && $sale) {
                $customerId = $sale->customer_id;
            }
            if (!$customerId) {
                $walkingCustomer = Customer::where('customer_type', 'Walking Customer')->first();
                if ($walkingCustomer) {
                    $customerId = $walkingCustomer->id;
                } else {
                    $walkingCustomer = Customer::create([
                        'customer_id' => 'CUST-WALK',
                        'customer_name' => 'Walking Customer',
                        'customer_type' => 'Walking Customer',
                        'mobile' => '-',
                        'status' => 'active',
                        'opening_balance' => 0,
                    ]);
                    $customerId = $walkingCustomer->id;
                }
            }

            $customer = Customer::find($customerId);
            $isWalking = ($customer && $customer->customer_type === 'Walking Customer') || ($customerId == 1);

            // Validate that at least one item has return quantity > 0
            $hasItems = false;
            foreach ($request->product_id as $idx => $productId) {
                $qty = (float) $request->qty[$idx];
                if ($qty > 0) {
                    $hasItems = true;
                    break;
                }
            }
            if (!$hasItems) {
                throw new \Exception("Please enter a return quantity for at least one item.");
            }

            // Prepare returnable limits from past returns if linked to a sale
            $returnedQtyMap = [];
            $soldQtyMap = [];
            if ($sale) {
                $pastReturns = SaleReturn::where('sale_id', $sale->id)->with('items')->get();
                foreach ($pastReturns as $sr) {
                    foreach ($sr->items as $srItem) {
                        $key = $srItem->product_id . '_' . ($srItem->color ?? '');
                        $returnedQtyMap[$key] = ($returnedQtyMap[$key] ?? 0) + (float)$srItem->qty;
                    }
                }
                foreach ($sale->items as $sItem) {
                    $key = $sItem->product_id . '_' . ($sItem->color ?? '');
                    $soldQty = (float)($sItem->total_pieces ?? $sItem->qty ?? 0);
                    $soldQtyMap[$key] = ($soldQtyMap[$key] ?? 0) + $soldQty;
                }
            }

            // Create Sale Return Header
            $return = SaleReturn::create([
                'sale_id' => $validated['sale_id'] ?? null,
                'return_invoice' => $nextInvoice,
                'customer_id' => $customerId,
                'warehouse_id' => $validated['warehouse_id'],
                'return_date' => $validated['return_date'],
                'remarks' => $validated['return_reason'] ?? null,
                'status' => 'posted',
            ]);

            $now = Carbon::now();
            $movements = [];
            $subtotal = 0;
            $totalItemDiscount = 0;

            // Process Each Return Item
            foreach ($request->product_id as $idx => $productId) {
                $qty = (float) $request->qty[$idx]; // Total pieces
                if ($qty <= 0) continue;

                // Validate quantity does not exceed available returnable quantity
                if ($sale) {
                    $itemColor = $request->color[$idx] ?? '';
                    $key = $productId . '_' . $itemColor;
                    $sold = $soldQtyMap[$key] ?? 0;
                    $already = $returnedQtyMap[$key] ?? 0;
                    $maxAllowed = max(0, $sold - $already);
                    if ($qty > ($maxAllowed + 0.001)) {
                        $prod = Product::find($productId);
                        $prodName = $prod ? $prod->product_name : "Item #{$productId}";
                        throw new \Exception("Return quantity ({$qty}) exceeds remaining returnable quantity ({$maxAllowed}) for {$prodName}.");
                    }
                }

                $price = (float) $request->price[$idx];
                $itemDisc = (float) ($request->item_disc[$idx] ?? 0);

                // Get product for PPB and size_mode calculation
                $product = Product::find($productId);
                $ppb = ($product && $product->pieces_per_box > 0) ? (int)$product->pieces_per_box : 1;
                $sizeMode = $product ? ($product->size_mode ?? 'by_pieces') : 'by_pieces';
                $ppm2 = $product ? (float)($product->m2_of_box ?? 0) : 0;

                // Calculate Line Total Logic based on size mode
                if ($sizeMode === 'by_size') {
                    $grossLine = round($ppm2 * $qty * $price, 2);
                } elseif ($sizeMode === 'by_cartons' || $sizeMode === 'by_carton') {
                    $grossLine = round($qty * $price, 2);
                } else {
                    $grossLine = round($qty * $price, 2);
                }
                $lineTotal = max(0, round($grossLine - $itemDisc, 2));

                // Calculate boxes and loose pieces
                $boxes = floor($qty / $ppb);
                $loosePieces = (int)$qty % $ppb;

                // Create Return Item
                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'product_id' => $productId,
                    'color' => $request->color[$idx] ?? null,
                    'warehouse_id' => $validated['warehouse_id'],
                    'qty' => $qty,
                    'boxes' => $boxes + ($loosePieces / $ppb), // Decimal boxes
                    'loose_pieces' => $loosePieces,
                    'price' => $price,
                    'item_discount' => $itemDisc,
                    'unit' => 'pc',
                    'line_total' => $lineTotal,
                ]);

                // Calculate Stock Qty with Variant Conv Factor
                $stockQty = $qty;
                $rColor = $request->color[$idx] ?? null;
                if (!empty($rColor)) {
                    try {
                        $b64Decoded = base64_decode($rColor, true);
                        $variantData = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                        if (!is_array($variantData)) {
                            $variantData = is_string($rColor) ? json_decode($rColor, true) : $rColor;
                        }
                        if (is_array($variantData) && isset($variantData['conv_factor'])) {
                            $factor = (float)$variantData['conv_factor'];
                            if ($factor > 0) {
                                $stockQty = $qty * $factor;
                            }
                        }
                    } catch (\Exception $e) {}
                }

                // Update Stock (INCREMENT - goods coming back to warehouse)
                $stock = WarehouseStock::where('warehouse_id', $validated['warehouse_id'])
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $currentTotalPieces = (float)$stock->total_pieces;
                    if ($currentTotalPieces == 0 && $stock->quantity > 0) {
                        $currentTotalPieces = $stock->quantity * $ppb;
                    }
                    $newTotalPieces = $currentTotalPieces + $stockQty;
                    
                    $stock->total_pieces = $newTotalPieces;
                    $stock->quantity = $newTotalPieces / $ppb;
                    $stock->save();
                } else {
                    WarehouseStock::create([
                        'warehouse_id' => $validated['warehouse_id'],
                        'product_id' => $productId,
                        'total_pieces' => $stockQty,
                        'quantity' => $stockQty / $ppb,
                        'price' => 0
                    ]);
                }

                // Stock Movement (IN - goods returned to warehouse)
                $movements[] = [
                    'product_id' => $productId,
                    'type' => 'in',
                    'qty' => $stockQty,
                    'ref_type' => 'SALE_RETURN',
                    'ref_id' => $return->id,
                    'note' => "Return #{$nextInvoice}",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $subtotal += $lineTotal;
                $totalItemDiscount += $itemDisc;
            }

            // Bulk Insert Stock Movements
            if (!empty($movements)) {
                DB::table('stock_movements')->insert($movements);
            }

            $extraDiscount = (float)($request->extra_discount ?? 0);
            $netAmount = max(0, $subtotal - $extraDiscount);

            // Compute debt offset and refundable amount if linked to a sale
            $dueAdjusted = 0;
            $refundableAmount = $netAmount;

            if ($sale) {
                $saleTotalNet = (float)$sale->total_net;
                $customerPaid = (float)($sale->cash + ($sale->card ?? 0));
                $originalInvoiceDue = max(0, $saleTotalNet - $customerPaid);

                // Any past returns on this sale
                $pastReturns = SaleReturn::where('sale_id', $sale->id)
                    ->where('id', '!=', $return->id)
                    ->get();
                $pastReturnedTotal = (float) $pastReturns->sum('net_amount');
                $pastRefundPaid = (float) $pastReturns->sum('paid');
                $pastDueAdjusted = (float) $pastReturns->sum('due_adjusted');

                if ($pastDueAdjusted == 0 && $pastReturns->count() > 0) {
                    $pastDueAdjusted = min($pastReturnedTotal, $originalInvoiceDue);
                }

                $remainingInvoiceDue = max(0, $originalInvoiceDue - $pastDueAdjusted);
                $remainingPaidRefundable = max(0, $customerPaid - $pastRefundPaid);

                // Debt offset wipes out the customer's unpaid invoice balance first
                $dueAdjusted = min($netAmount, $remainingInvoiceDue);
                // Eligible Cash/Bank Refund cannot exceed the customer's actual paid advance
                $refundableAmount = min(max(0, $netAmount - $dueAdjusted), $remainingPaidRefundable);
            }

            // Handle Refund Payment (Payment Voucher)
            $totalPaid = 0;
            $submittedPaid = 0;
            if (!empty($request->payment_account_id) && !empty($request->payment_amount)) {
                foreach ($request->payment_amount as $pAmt) {
                    $submittedPaid += (float)$pAmt;
                }
            }

            if ($submittedPaid > ($refundableAmount + 0.05)) {
                throw new \Exception("Cash refund of Rs. " . number_format($submittedPaid, 2) . " exceeds the eligible cash refund amount of Rs. " . number_format($refundableAmount, 2) . ". The remaining Rs. " . number_format($dueAdjusted, 2) . " settles the customer's unpaid invoice balance.");
            }

            // Walking customer MUST receive full eligible cash refund immediately
            if ($isWalking && $refundableAmount > 0 && $submittedPaid < ($refundableAmount - 0.05)) {
                throw new \Exception("Walking Customer requires immediate cash refund of Rs. " . number_format($refundableAmount, 2) . ". Please select a refund payment account.");
            }

            if (!empty($request->payment_account_id)) {
                $voucherService = app(\App\Services\VoucherService::class);
                $arId = app(\App\Services\BalanceService::class)->getAccountsReceivableId();

                foreach ($request->payment_account_id as $idx => $accId) {
                    $amt = (float) ($request->payment_amount[$idx] ?? 0);
                    if ($accId && $amt > 0) {
                        $totalPaid += $amt;
                        
                        // Create Payment Voucher via Service
                        $voucherData = [
                            'voucher_type' => \App\Models\VoucherMaster::TYPE_PAYMENT,
                            'date' => $validated['return_date'],
                            'status' => \App\Models\VoucherMaster::STATUS_POSTED,
                            'party_type' => \App\Models\Customer::class,
                            'party_id' => $customerId,
                            'remarks' => "Refund for Return #{$nextInvoice}",
                        ];

                        $lines = [
                            [
                                'account_id' => $accId, 
                                'debit' => 0,
                                'credit' => $amt,
                                'narration' => 'Cash Refund Paid'
                            ],
                            [
                                'account_id' => $arId, 
                                'debit' => $amt,
                                'credit' => 0,
                                'narration' => 'Refund to Customer'
                            ]
                        ];

                        $voucherService->createVoucher($voucherData, $lines, auth()->id());
                    }
                }
            }

            // Calculate remaining balance of the refund
            // If refund is paid in full, balance is 0. If customer leaves refund on account/store credit, balance is refundableAmount - totalPaid.
            $remainingRefundBalance = max(0, $refundableAmount - $totalPaid);

            // Update Return Totals
            $return->update([
                'bill_amount' => $subtotal + $totalItemDiscount,
                'item_discount' => $totalItemDiscount,
                'extra_discount' => $extraDiscount,
                'net_amount' => $netAmount,
                'due_adjusted' => $dueAdjusted,
                'refundable_amount' => $refundableAmount,
                'paid' => $totalPaid,
                'balance' => $remainingRefundBalance,
            ]);

            // Update Sale Status (if fully returned)
            if ($sale) {
                $totalSold = (float)$sale->items->sum(function($item) {
                    return $item->total_pieces ?? $item->qty ?? 0;
                });
                $totalReturned = (float)SaleReturnItem::join('sale_returns', 'sale_returns.id', '=', 'sale_return_items.sale_return_id')
                    ->where('sale_returns.sale_id', $sale->id)
                    ->sum('sale_return_items.qty');
                
                if ($totalReturned >= ($totalSold - 0.001)) {
                    $sale->update(['sale_status' => 'returned']);
                }
            }

            // Create Journal Voucher (Credit Note)
            $transactionService = app(\App\Services\TransactionService::class);
            if (method_exists($transactionService, 'createSaleReturnVoucher') && $netAmount > 0) {
                $transactionService->createSaleReturnVoucher($return);
            }

            // Update Customer Ledger & Master Balance (for registered customers)
            if (!$isWalking && $customerId && $customer) {
                // 1. Return credit: Goods return reduces customer debt
                $latestLedger = \App\Models\CustomerLedger::where('customer_id', $customerId)->latest('id')->first();
                $prevBal = $latestLedger ? (float)$latestLedger->closing_balance : (float)($customer->previous_balance ?? 0);
                $afterReturnBal = $prevBal - $netAmount;

                \App\Models\CustomerLedger::create([
                    'customer_id' => $customerId,
                    'admin_or_user_id' => auth()->id() ?? 1,
                    'description' => "Sale Return #{$nextInvoice}" . ($sale ? " for Invoice #{$sale->invoice_no}" : ""),
                    'previous_balance' => $prevBal,
                    'closing_balance' => $afterReturnBal,
                    'opening_balance' => 0,
                ]);

                // 2. Refund debit: Cash refund paid back to customer offsets reduction
                if ($totalPaid > 0) {
                    $prevBalRefund = $afterReturnBal;
                    $afterRefundBal = $prevBalRefund + $totalPaid;

                    \App\Models\CustomerLedger::create([
                        'customer_id' => $customerId,
                        'admin_or_user_id' => auth()->id() ?? 1,
                        'description' => "Refund Paid for Return #{$nextInvoice}",
                        'previous_balance' => $prevBalRefund,
                        'closing_balance' => $afterRefundBal,
                        'opening_balance' => 0,
                    ]);
                    $customer->previous_balance = $afterRefundBal;
                } else {
                    $customer->previous_balance = $afterReturnBal;
                }
                $customer->save();
            }

            DB::commit();

            return redirect()->route('sale.return.index')->with('success', 'Sale return processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing return: ' . $e->getMessage());
        }
    }

    /**
     * Display all sale returns
     */
    public function saleReturnIndex()
    {
        $returns = SaleReturn::with(['customer', 'sale', 'warehouse'])->orderBy('id', 'desc')->get();
        
        // Calculate updated financial details
        $returns->each(function ($return) {
            if ($return->sale) {
                $sale = $return->sale;
                $saleNet = (float) $sale->total_net;
                $customerPaid = (float) ($sale->cash + ($sale->card ?? 0));
                $origDue = max(0, $saleNet - $customerPaid);

                // Ensure due_adjusted and refundable_amount are properly set for legacy returns
                if ((float)$return->due_adjusted == 0 && (float)$return->paid == 0 && (float)$return->net_amount > 0) {
                    $return->due_adjusted = min((float)$return->net_amount, $origDue);
                    $return->refundable_amount = min(max(0, (float)$return->net_amount - (float)$return->due_adjusted), $customerPaid);
                }

                $return->original_net_amount = $saleNet;
                $return->customer_advance = $customerPaid;
                $return->original_due = $origDue;

                $totalReturned = (float) SaleReturn::where('sale_id', $sale->id)->sum('net_amount');
                $totalDueAdjusted = (float) SaleReturn::where('sale_id', $sale->id)->sum('due_adjusted');
                if ($totalDueAdjusted == 0) {
                    $totalDueAdjusted = min($totalReturned, $origDue);
                }

                $return->new_net_amount = max(0, $saleNet - $totalReturned);
                $return->total_returned = $totalReturned;
                $return->new_due_amount = max(0, $origDue - $totalDueAdjusted);
            } else {
                $return->original_net_amount = (float) $return->net_amount;
                $return->customer_advance = 0;
                $return->original_due = 0;
                $return->new_net_amount = 0;
                $return->new_due_amount = 0;
                $return->total_returned = (float) $return->net_amount;
            }
        });

        return view('admin_panel.sale.sale_return.index', compact('returns'));
    }

    /**
     * View a specific sale return
     */
    public function viewReturn($id)
    {
        $return = SaleReturn::with(['customer', 'sale', 'items.product', 'warehouse'])->findOrFail($id);

        // Ensure accurate financial breakdown even for legacy records
        if ((float)$return->due_adjusted == 0 && $return->sale) {
            $saleNet = (float) $return->sale->total_net;
            $customerPaid = (float) ($return->sale->cash + ($return->sale->card ?? 0));
            $origDue = max(0, $saleNet - $customerPaid);
            $return->due_adjusted = min((float)$return->net_amount, $origDue);
            $return->refundable_amount = max(0, (float)$return->net_amount - (float)$return->due_adjusted);
            $return->balance = max(0, (float)$return->refundable_amount - (float)$return->paid);
        }

        return view('admin_panel.sale.sale_return.show', compact('return'));
    }
}
