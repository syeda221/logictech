<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceSeries;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with(['customer_relation', 'items.product', 'convertedSale']);

        // Apply Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply Date Filters
        if ($request->filled('from_date')) {
            try {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $query->where('quotation_date', '>=', $fromDate);
            } catch (\Exception $e) {
                $query->whereDate('quotation_date', '>=', $request->from_date);
            }
        }
        if ($request->filled('to_date')) {
            try {
                $toDate = Carbon::parse($request->to_date)->endOfDay();
                $query->where('quotation_date', '<=', $toDate);
            } catch (\Exception $e) {
                $query->whereDate('quotation_date', '<=', $request->to_date);
            }
        }

        // Apply Search / Term Filter
        if ($request->filled('search') || $request->filled('serial_no')) {
            $term = trim($request->input('search', $request->input('serial_no')));
            $query->where(function ($q) use ($term) {
                $q->where('quote_no', 'like', "%{$term}%")
                  ->orWhere('id', 'like', "%{$term}%")
                  ->orWhere('reference', 'like', "%{$term}%")
                  ->orWhere('walkin_name', 'like', "%{$term}%")
                  ->orWhereHas('items', function ($sq) use ($term) {
                      $sq->where('product_name', 'like', "%{$term}%")
                         ->orWhere('technical_name', 'like', "%{$term}%")
                         ->orWhere('model', 'like', "%{$term}%");
                  })
                  ->orWhereHas('customer_relation', function ($sq) use ($term) {
                      $sq->where('customer_name', 'like', "%{$term}%")
                         ->orWhere('mobile', 'like', "%{$term}%")
                         ->orWhere('customer_id', 'like', "%{$term}%");
                  });
            });
        }

        // Apply Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Order By
        $query->orderBy('id', 'desc');

        $quotations = $query->get();

        $stats = [
            'total_count' => $quotations->count(),
            'total_net' => (float) $quotations->sum('total_net'),
            'total_discount' => (float) $quotations->sum('total_discount'),
            'converted_count' => $quotations->where('status', 'converted')->count(),
            'pending_count' => $quotations->where('status', 'pending')->count(),
            'approved_count' => $quotations->where('status', 'approved')->count(),
        ];

        // If AJAX request, return partial HTML & stats
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin_panel.quotations.partials.quotations_table_body', compact('quotations'))->render(),
                'stats' => $stats,
            ]);
        }

        // Load customers for filter dropdown
        $customers = Customer::orderBy('customer_name')->limit(50)->get();

        return view('admin_panel.quotations.index', compact('quotations', 'customers', 'stats'));
    }

    public function create()
    {
        $customers = Customer::all();
        $warehouses = Warehouse::all();

        // Quotation series prefixes
        $allSeries = InvoiceSeries::where('prefix', 'LIKE', '%QUO%')
            ->orWhere('prefix', 'QUOTE')
            ->orderBy('prefix', 'asc')
            ->get();

        if ($allSeries->isEmpty()) {
            $allSeries = collect([
                (object)[
                    'prefix' => 'QUO',
                    'next_number' => 1,
                    'padding' => 4,
                ]
            ]);
        }

        $activePrefix = 'QUO';
        $nextQuoteNumber = Quotation::generateQuoteNo($activePrefix);

        $recentProducts = Product::where('is_active', true)
            ->whereIn('item_type', ['finish_goods', 'both'])
            ->latest()
            ->take(12)
            ->get();

        $defaultTerms = Setting::get('quotation_terms') ?: Quotation::DEFAULT_TERMS;

        return view('admin_panel.quotations.create', compact(
            'customers',
            'warehouses',
            'allSeries',
            'activePrefix',
            'nextQuoteNumber',
            'recentProducts',
            'defaultTerms'
        ));
    }

    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                // Determine Customer / Walkin
                $isWalkin = $request->input('is_walkin') == '1' || $request->filled('walkin_name') || empty($request->input('customer'));
                $customerId = $isWalkin ? null : $request->input('customer');
                $walkinName = $isWalkin ? trim($request->input('walkin_name') ?: 'Walk-in Customer') : null;

                // Series & Quote Number
                $quoteNo = trim($request->input('quote_no', $request->input('Invoice_no', '')));
                if (empty($quoteNo)) {
                    $prefix = $request->input('prefix', 'QUO');
                    $quoteNo = Quotation::generateQuoteNo($prefix);
                }

                // Dates
                $quoteDate = $request->input('quotation_date', $request->input('sale_date', date('Y-m-d')));
                $estDelivDate = $request->input('estimated_delivery_date');

                // Terms
                $terms = $request->input('terms_and_conditions') ?: Quotation::DEFAULT_TERMS;

                // Create Quotation Record
                $quotation = new Quotation();
                $quotation->quote_no = $quoteNo;
                $quotation->customer_id = $customerId;
                $quotation->walkin_name = $walkinName;
                $quotation->reference = $request->input('reference', $request->input('subject'));
                $quotation->quote_title = $request->input('quote_title', $request->input('invoice_title', 'QUOTATION')) ?: 'QUOTATION';
                $quotation->quotation_date = $quoteDate;
                $quotation->estimated_delivery_date = $estDelivDate ?: null;
                $quotation->terms_and_conditions = $terms;
                $quotation->status = $request->input('status', 'pending');
                $quotation->created_by = Auth::id();

                $quotation->total_bill_amount = 0;
                $quotation->total_discount = 0;
                $quotation->tax_percent = 0;
                $quotation->tax_amount = 0;
                $quotation->total_net = 0;
                $quotation->total_items = 0;
                $quotation->save();

                // Process Line Items
                $itemNames = $request->input('item_name', []);
                $quantities = $request->input('qty', []);
                $prices = $request->input('price', []);
                $discounts = $request->input('discount', []);
                $discountTypes = $request->input('discount_type', []);
                $totals = $request->input('total', []);
                $itemTaxes = $request->input('item_tax', []);
                $techNames = $request->input('technical_name', []);
                $techSpecs = $request->input('technical_specs', []);
                $techRemarks = $request->input('technical_remarks', []);
                $colors = $request->input('color', []);
                $models = $request->input('model', []);
                $units = $request->input('unit', []);
                $subcategories = $request->input('subcategory', []);

                $grossSum = 0;
                $discountSum = 0;
                $taxSum = 0;
                $netSum = 0;
                $totalPiecesCount = 0;

                foreach ($itemNames as $i => $name) {
                    if (empty($name) && empty($quantities[$i])) {
                        continue;
                    }

                    $qtyVal = (float)($quantities[$i] ?? 1);
                    $priceVal = (float)($prices[$i] ?? 0);
                    $lineTotal = (float)($totals[$i] ?? ($qtyVal * $priceVal));
                    $discVal = (float)($discounts[$i] ?? 0);
                    $discType = $discountTypes[$i] ?? 'percent';

                    $discAmount = 0;
                    $discPct = 0;
                    if ($discVal > 0) {
                        if ($discType === 'fixed') {
                            $discAmount = $discVal;
                            $discPct = ($qtyVal * $priceVal) > 0 ? ($discAmount / ($qtyVal * $priceVal)) * 100 : 0;
                        } else {
                            $discPct = $discVal;
                            $discAmount = (($qtyVal * $priceVal) * $discPct) / 100;
                        }
                    }

                    $itemTaxVal = (float)($itemTaxes[$i] ?? 0);
                    $itemTaxAmt = 0;
                    if ($itemTaxVal > 0) {
                        $itemTaxAmt = (($lineTotal) * $itemTaxVal) / 100;
                    }

                    $grossSum += ($qtyVal * $priceVal);
                    $discountSum += $discAmount;
                    $taxSum += $itemTaxAmt;
                    $netSum += ($lineTotal + $itemTaxAmt);
                    $totalPiecesCount += (int) $qtyVal;

                    // Find product if exists
                    $product = Product::where('item_name', $name)->first();

                    $quotationItem = new QuotationItem();
                    $quotationItem->quotation_id = $quotation->id;
                    $quotationItem->product_id = $product ? $product->id : null;
                    $quotationItem->product_name = $name;
                    $quotationItem->technical_name = $techNames[$i] ?? null;
                    $quotationItem->technical_specs = $techSpecs[$i] ?? null;
                    $quotationItem->technical_remarks = $techRemarks[$i] ?? null;
                    $quotationItem->unit_id = $units[$i] ?? ($product ? $product->unit_id : null);
                    $quotationItem->sub_category_id = $subcategories[$i] ?? ($product ? $product->sub_category_id : null);
                    $quotationItem->brand_id = $product ? $product->brand_id : null;
                    $quotationItem->category_id = $product ? $product->category_id : null;
                    $quotationItem->qty = $qtyVal;
                    $quotationItem->price = $priceVal;
                    $quotationItem->discount_percent = $discPct;
                    $quotationItem->discount_amount = $discAmount;
                    $quotationItem->tax_percent = $itemTaxVal;
                    $quotationItem->tax_amount = $itemTaxAmt;
                    $quotationItem->total = $lineTotal;
                    $quotationItem->color = $colors[$i] ?? null;
                    $quotationItem->model = $models[$i] ?? null;
                    $quotationItem->total_pieces = (int) $qtyVal;
                    $quotationItem->save();
                }

                // Global Tax override if specified
                $globalTaxPct = (float)$request->input('tax_percent', 0);
                $globalTaxAmt = (float)$request->input('tax_amount', 0);
                if ($globalTaxAmt <= 0 && $globalTaxPct > 0) {
                    $globalTaxAmt = (($grossSum - $discountSum) * $globalTaxPct) / 100;
                }
                if ($globalTaxAmt > 0 && $taxSum <= 0) {
                    $taxSum = $globalTaxAmt;
                }

                $finalNet = max(0, ($grossSum - $discountSum) + $taxSum);

                $quotation->total_bill_amount = $grossSum;
                $quotation->total_discount = $discountSum;
                $quotation->tax_percent = $globalTaxPct;
                $quotation->tax_amount = $taxSum;
                $quotation->total_net = $finalNet;
                $quotation->total_items = $totalPiecesCount;
                $quotation->save();

                // Increment sequence series
                Quotation::incrementCounterForQuote($quotation->quote_no);

                return response()->json([
                    'success' => true,
                    'message' => "Quotation #{$quotation->quote_no} saved successfully!",
                    'quotation_id' => $quotation->id,
                    'quote_no' => $quotation->quote_no,
                    'redirect_url' => route('quotations.show', $quotation->id),
                    'index_url' => route('quotations.index'),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Quotation Store Error: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save quotation: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $quotation = Quotation::resolveByIdOrQuoteNo($id, ['customer_relation', 'items.product', 'convertedSale']);
        if (!$quotation) {
            abort(404, 'Quotation not found.');
        }

        $items = $this->_getQuotationItems($quotation);

        return view('admin_panel.quotations.print', compact('quotation', 'items'));
    }

    public function edit($id)
    {
        $quotation = Quotation::resolveByIdOrQuoteNo($id, ['customer_relation', 'items.product']);
        if (!$quotation) {
            abort(404, 'Quotation not found.');
        }

        $customers = Customer::all();
        $warehouses = Warehouse::all();

        $allSeries = InvoiceSeries::where('prefix', 'LIKE', '%QUO%')
            ->orWhere('prefix', 'QUOTE')
            ->orderBy('prefix', 'asc')
            ->get();

        $activePrefix = 'QUO';
        if (preg_match('/^([A-Z0-9]+)-/i', $quotation->quote_no, $m)) {
            $activePrefix = strtoupper($m[1]);
        }

        $nextQuoteNumber = $quotation->quote_no;

        $recentProducts = Product::where('is_active', true)
            ->whereIn('item_type', ['finish_goods', 'both'])
            ->latest()
            ->take(12)
            ->get();

        $defaultTerms = Quotation::DEFAULT_TERMS;

        return view('admin_panel.quotations.edit', compact(
            'quotation',
            'customers',
            'warehouses',
            'allSeries',
            'activePrefix',
            'nextQuoteNumber',
            'recentProducts',
            'defaultTerms'
        ));
    }

    public function update(Request $request, $id)
    {
        $quotation = Quotation::resolveByIdOrQuoteNo($id);
        if (!$quotation) {
            return response()->json(['success' => false, 'message' => 'Quotation not found.'], 404);
        }

        try {
            return DB::transaction(function () use ($request, $quotation) {
                $isWalkin = $request->input('is_walkin') == '1' || $request->filled('walkin_name') || empty($request->input('customer'));
                $customerId = $isWalkin ? null : $request->input('customer');
                $walkinName = $isWalkin ? trim($request->input('walkin_name') ?: 'Walk-in Customer') : null;

                $quoteDate = $request->input('quotation_date', $request->input('sale_date', date('Y-m-d')));
                $estDelivDate = $request->input('estimated_delivery_date');
                $terms = $request->input('terms_and_conditions') ?: Quotation::DEFAULT_TERMS;

                $quotation->customer_id = $customerId;
                $quotation->walkin_name = $walkinName;
                $quotation->reference = $request->input('reference', $request->input('subject'));
                $quotation->quote_title = $request->input('quote_title', $request->input('invoice_title', 'QUOTATION')) ?: 'QUOTATION';
                $quotation->quotation_date = $quoteDate;
                $quotation->estimated_delivery_date = $estDelivDate ?: null;
                $quotation->terms_and_conditions = $terms;
                if ($request->filled('status')) {
                    $quotation->status = $request->input('status');
                }

                // Delete old items
                $quotation->items()->delete();

                // Re-create items
                $itemNames = $request->input('item_name', []);
                $quantities = $request->input('qty', []);
                $prices = $request->input('price', []);
                $discounts = $request->input('discount', []);
                $discountTypes = $request->input('discount_type', []);
                $totals = $request->input('total', []);
                $itemTaxes = $request->input('item_tax', []);
                $techNames = $request->input('technical_name', []);
                $techSpecs = $request->input('technical_specs', []);
                $techRemarks = $request->input('technical_remarks', []);
                $colors = $request->input('color', []);
                $models = $request->input('model', []);
                $units = $request->input('unit', []);
                $subcategories = $request->input('subcategory', []);

                $grossSum = 0;
                $discountSum = 0;
                $taxSum = 0;
                $netSum = 0;
                $totalPiecesCount = 0;

                foreach ($itemNames as $i => $name) {
                    if (empty($name) && empty($quantities[$i])) {
                        continue;
                    }

                    $qtyVal = (float)($quantities[$i] ?? 1);
                    $priceVal = (float)($prices[$i] ?? 0);
                    $lineTotal = (float)($totals[$i] ?? ($qtyVal * $priceVal));
                    $discVal = (float)($discounts[$i] ?? 0);
                    $discType = $discountTypes[$i] ?? 'percent';

                    $discAmount = 0;
                    $discPct = 0;
                    if ($discVal > 0) {
                        if ($discType === 'fixed') {
                            $discAmount = $discVal;
                            $discPct = ($qtyVal * $priceVal) > 0 ? ($discAmount / ($qtyVal * $priceVal)) * 100 : 0;
                        } else {
                            $discPct = $discVal;
                            $discAmount = (($qtyVal * $priceVal) * $discPct) / 100;
                        }
                    }

                    $itemTaxVal = (float)($itemTaxes[$i] ?? 0);
                    $itemTaxAmt = 0;
                    if ($itemTaxVal > 0) {
                        $itemTaxAmt = (($lineTotal) * $itemTaxVal) / 100;
                    }

                    $grossSum += ($qtyVal * $priceVal);
                    $discountSum += $discAmount;
                    $taxSum += $itemTaxAmt;
                    $netSum += ($lineTotal + $itemTaxAmt);
                    $totalPiecesCount += (int) $qtyVal;

                    $product = Product::where('item_name', $name)->first();

                    $quotationItem = new QuotationItem();
                    $quotationItem->quotation_id = $quotation->id;
                    $quotationItem->product_id = $product ? $product->id : null;
                    $quotationItem->product_name = $name;
                    $quotationItem->technical_name = $techNames[$i] ?? null;
                    $quotationItem->technical_specs = $techSpecs[$i] ?? null;
                    $quotationItem->technical_remarks = $techRemarks[$i] ?? null;
                    $quotationItem->unit_id = $units[$i] ?? ($product ? $product->unit_id : null);
                    $quotationItem->sub_category_id = $subcategories[$i] ?? ($product ? $product->sub_category_id : null);
                    $quotationItem->brand_id = $product ? $product->brand_id : null;
                    $quotationItem->category_id = $product ? $product->category_id : null;
                    $quotationItem->qty = $qtyVal;
                    $quotationItem->price = $priceVal;
                    $quotationItem->discount_percent = $discPct;
                    $quotationItem->discount_amount = $discAmount;
                    $quotationItem->tax_percent = $itemTaxVal;
                    $quotationItem->tax_amount = $itemTaxAmt;
                    $quotationItem->total = $lineTotal;
                    $quotationItem->color = $colors[$i] ?? null;
                    $quotationItem->model = $models[$i] ?? null;
                    $quotationItem->total_pieces = (int) $qtyVal;
                    $quotationItem->save();
                }

                $globalTaxPct = (float)$request->input('tax_percent', 0);
                $globalTaxAmt = (float)$request->input('tax_amount', 0);
                if ($globalTaxAmt <= 0 && $globalTaxPct > 0) {
                    $globalTaxAmt = (($grossSum - $discountSum) * $globalTaxPct) / 100;
                }
                if ($globalTaxAmt > 0 && $taxSum <= 0) {
                    $taxSum = $globalTaxAmt;
                }

                $finalNet = max(0, ($grossSum - $discountSum) + $taxSum);

                $quotation->total_bill_amount = $grossSum;
                $quotation->total_discount = $discountSum;
                $quotation->tax_percent = $globalTaxPct;
                $quotation->tax_amount = $taxSum;
                $quotation->total_net = $finalNet;
                $quotation->total_items = $totalPiecesCount;
                $quotation->save();

                return response()->json([
                    'success' => true,
                    'message' => "Quotation #{$quotation->quote_no} updated successfully!",
                    'quotation_id' => $quotation->id,
                    'quote_no' => $quotation->quote_no,
                    'redirect_url' => route('quotations.show', $quotation->id),
                    'index_url' => route('quotations.index'),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Quotation Update Error: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update quotation: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $quotation = Quotation::resolveByIdOrQuoteNo($id);
        if (!$quotation) {
            return redirect()->back()->with('error', 'Quotation not found.');
        }

        $quotation->items()->delete();
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }

    /**
     * Convert Quotation to Order / Sale (Audio 2 requirement)
     */
    public function convertToOrder(Request $request, $id)
    {
        $quotation = Quotation::resolveByIdOrQuoteNo($id, ['customer_relation', 'items.product']);
        if (!$quotation) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Quotation not found.'], 404);
            }
            return redirect()->back()->with('error', 'Quotation not found.');
        }

        // If request is GET, redirect to Sale creation page prefilled with this quotation
        if ($request->isMethod('get')) {
            return redirect()->route('sale.add', ['from_quotation' => $quotation->id]);
        }

        // If request is POST, perform immediate conversion to a Booked Order / Sale
        try {
            return DB::transaction(function () use ($quotation) {
                $nextInvoiceNo = Sale::generateInvoiceNo();

                $sale = new Sale();
                $sale->invoice_no = $nextInvoiceNo;
                $sale->customer_id = $quotation->customer_id;
                $sale->walkin_name = $quotation->walkin_name;
                $sale->reference = $quotation->reference ?: "From Quote #{$quotation->quote_no}";
                $sale->invoice_title = 'Invoice';
                $sale->sale_status = 'booked';
                $sale->order_status = 'pending';
                $sale->is_booking = 1;
                $sale->estimated_delivery_date = $quotation->estimated_delivery_date;
                $sale->delivery_date = $quotation->estimated_delivery_date;
                $sale->total_bill_amount = $quotation->total_bill_amount;
                $sale->total_extradiscount = $quotation->total_discount;
                $sale->tax_percent = $quotation->tax_percent;
                $sale->tax_amount = $quotation->tax_amount;
                $sale->total_net = $quotation->total_net;
                $sale->total_items = $quotation->total_items;
                $sale->cash = 0;
                $sale->card = 0;
                $sale->change = 0;
                $sale->terms_and_conditions = $quotation->terms_and_conditions;
                $sale->save();

                // Copy items
                foreach ($quotation->items as $qItem) {
                    $saleItem = new SaleItem();
                    $saleItem->sale_id = $sale->id;
                    $saleItem->product_id = $qItem->product_id;
                    $saleItem->product_name = $qItem->product_name;
                    $saleItem->technical_name = $qItem->technical_name;
                    $saleItem->technical_specs = $qItem->technical_specs;
                    $saleItem->technical_remarks = $qItem->technical_remarks;
                    $saleItem->unit_id = $qItem->unit_id;
                    $saleItem->sub_category_id = $qItem->sub_category_id;
                    $saleItem->brand_id = $qItem->brand_id;
                    $saleItem->category_id = $qItem->category_id;
                    $saleItem->size_mode = $qItem->size_mode ?: 'std';
                    $saleItem->qty = $qItem->qty;
                    $saleItem->price = $qItem->price;
                    $saleItem->discount_percent = $qItem->discount_percent;
                    $saleItem->discount_amount = $qItem->discount_amount;
                    $saleItem->tax_percent = $qItem->tax_percent;
                    $saleItem->tax_amount = $qItem->tax_amount;
                    $saleItem->total = $qItem->total;
                    $saleItem->color = $qItem->color;
                    $saleItem->model = $qItem->model;
                    $saleItem->total_pieces = $qItem->total_pieces;
                    $saleItem->save();
                }

                // Increment invoice counter
                InvoiceSeries::incrementCounterForInvoice($sale->invoice_no);

                // Mark quotation as converted
                $quotation->status = 'converted';
                $quotation->converted_sale_id = $sale->id;
                $quotation->converted_at = now();
                $quotation->save();

                return redirect()->route('sale.index')->with(
                    'success',
                    "Quotation #{$quotation->quote_no} has been converted into Order #{$sale->invoice_no} successfully!"
                );
            });
        } catch (\Exception $e) {
            Log::error('Quotation Conversion Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to convert quotation: ' . $e->getMessage());
        }
    }

    /**
     * Helper to format quotation items for view / print
     */
    private function _getQuotationItems($quotation)
    {
        return $quotation->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'item_name' => $item->product_name,
                'technical_name' => $item->technical_name,
                'technical_specs' => $item->technical_specs,
                'technical_remarks' => $item->technical_remarks,
                'brand' => optional($item->brand)->name ?? '',
                'unit' => optional($item->unit)->name ?? 'Pcs',
                'size_mode' => $item->size_mode,
                'size_val' => $item->size_mode,
                'color_val' => $item->color,
                'model' => $item->model,
                'qty' => (float)$item->qty,
                'total_pieces' => (int)$item->total_pieces,
                'price' => (float)$item->price,
                'discount_percent' => (float)$item->discount_percent,
                'discount_amount' => (float)$item->discount_amount,
                'tax_percent' => (float)$item->tax_percent,
                'tax_amount' => (float)$item->tax_amount,
                'total' => (float)$item->total,
            ];
        })->toArray();
    }
}
