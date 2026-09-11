<?php

namespace App\Http\Controllers;

use App\Models\MaterialUsage;
use App\Models\MaterialUsageItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialUsageController extends Controller
{
    /**
     * Check authorization
     */
    private function checkPermission()
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized. Please login.');
        }

        if ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }

        if (
            $user->can('material.usage.view') ||
            $user->can('material.usage.create') ||
            $user->can('material.usage.edit') ||
            $user->can('material.usage.delete') ||
            $user->can('material.usage.report.view') ||
            $user->can('warehouse.view') ||
            $user->can('warehouse.stock.view') ||
            $user->can('stock.adjust.create') ||
            $user->can('stock.adjust.view')
        ) {
            return true;
        }

        abort(403, 'Unauthorized. You do not have permission to access Material Usage.');
    }

    /**
     * Get the default primary warehouse (Single Warehouse System)
     */
    private function getDefaultWarehouse()
    {
        return Warehouse::orderBy('id')->first() ?? (object)['id' => 1, 'warehouse_name' => 'Main Store'];
    }

    /**
     * List all material usages (Executive All Orders / Sales Index Design)
     */
    public function index(Request $request)
    {
        $this->checkPermission();

        $query = MaterialUsage::with(['warehouse', 'user', 'items.product.unit']);

        // Quick Period Filter
        if ($request->filled('period') && $request->period !== 'custom') {
            switch ($request->period) {
                case 'daily':
                    $query->whereDate('date', today());
                    break;
                case 'weekly':
                    $query->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
                    break;
                case 'monthly':
                    $query->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);
                    break;
                case 'yearly':
                    $query->whereBetween('date', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()]);
                    break;
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        // Search Voucher
        if ($request->filled('search_voucher')) {
            $vNo = trim(str_replace('#', '', $request->search_voucher));
            $query->where('usage_no', 'like', "%{$vNo}%");
        }

        // Product filter
        if ($request->filled('product_id')) {
            $query->whereHas('items', function ($iq) use ($request) {
                $iq->where('product_id', $request->product_id);
            });
        }

        // General search text
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('usage_no', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function ($pq) use ($search) {
                      $pq->where('item_name', 'like', "%{$search}%")
                         ->orWhere('item_code', 'like', "%{$search}%");
                  });
            });
        }

        // Summary KPI Metrics
        $metricQuery = clone $query;
        $kpiMetrics = [
            'total_usages' => $metricQuery->count(),
            'total_items'  => $metricQuery->sum('total_items'),
            'total_qty'    => $metricQuery->sum('total_qty'),
            'total_cost'   => $metricQuery->sum('total_cost'),
        ];

        $usages = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(25)->withQueryString();
        $rawMaterialsList = Product::where('item_type', 'raw_material')->orderBy('item_name')->get();

        return view('admin_panel.material_usage.index', compact(
            'usages',
            'kpiMetrics',
            'rawMaterialsList'
        ));
    }

    /**
     * Show form to issue / record material usage (Automatic single warehouse)
     */
    public function create()
    {
        $this->checkPermission();
        $defaultWarehouse = $this->getDefaultWarehouse();

        // ONLY RAW MATERIALS - Finished goods strictly excluded!
        $rawMaterials = Product::where('item_type', 'raw_material')
            ->with(['unit', 'category_relation', 'warehouseStocks'])
            ->withSum('warehouseStocks', 'total_pieces')
            ->orderBy('item_name')
            ->get();

        $rawMaterialsJson = $rawMaterials->map(function($rm) {
            return [
                'id'    => $rm->id,
                'name'  => $rm->item_name,
                'code'  => $rm->item_code ?? '-',
                'stock' => (float)($rm->warehouse_stocks_sum_total_pieces ?? 0),
                'unit'  => $rm->unit->name ?? $rm->unit->unit_name ?? 'Pcs',
                'cost'  => (float)($rm->purchase_price_per_piece ?? 0),
            ];
        })->values();

        $nextUsageNo = MaterialUsage::generateNextUsageNo();

        return view('admin_panel.material_usage.create', compact(
            'defaultWarehouse',
            'rawMaterials',
            'rawMaterialsJson',
            'nextUsageNo'
        ));
    }

    /**
     * AJAX endpoint to get available stock of a raw material (Auto default warehouse)
     */
    public function getRawMaterialStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $defaultWarehouse = $this->getDefaultWarehouse();
        $warehouseId = $request->warehouse_id ?: $defaultWarehouse->id;

        $product = Product::with('unit')->findOrFail($request->product_id);

        if ($product->item_type !== 'raw_material') {
            return response()->json([
                'success' => false,
                'message' => 'Selected item is not a raw material.',
            ], 422);
        }

        $warehouseStock = WarehouseStock::where('product_id', $product->id)
            ->where('warehouse_id', $warehouseId)
            ->first();

        // Fallback: If no stock record exists in this warehouse, check any warehouse
        if (!$warehouseStock) {
            $warehouseStock = WarehouseStock::where('product_id', $product->id)->first();
        }

        $availableStock = $warehouseStock ? (float) ($warehouseStock->total_pieces ?? $warehouseStock->quantity ?? 0) : 0.0;
        $unitCost = (float) ($product->purchase_price_per_piece ?? $product->purchase_price_per_m2 ?? 0.0);
        $unitName = $product->unit ? ($product->unit->unit_name ?? $product->unit->name ?? 'Pcs') : 'Pcs';

        return response()->json([
            'success'         => true,
            'product_id'      => $product->id,
            'product_name'    => $product->item_name,
            'item_code'       => $product->item_code,
            'available_stock' => $availableStock,
            'unit_name'       => $unitName,
            'unit_cost'       => $unitCost,
        ]);
    }

    /**
     * Store material usage and deduct stock
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        $defaultWarehouse = $this->getDefaultWarehouse();
        $warehouseId = $request->warehouse_id ?: $defaultWarehouse->id;

        $request->validate([
            'date'               => 'required|date',
            'purpose'            => 'nullable|string|max:255',
            'remarks'            => 'nullable|string',
        ]);

        $submittedItems = $request->items;
        if (!$submittedItems && $request->has('product_id')) {
            $submittedItems = [];
            foreach ($request->product_id as $idx => $pid) {
                if ($pid) {
                    $submittedItems[] = [
                        'product_id' => $pid,
                        'qty_used'   => $request->qty_used[$idx] ?? 0,
                        'notes'      => $request->notes[$idx] ?? null,
                    ];
                }
            }
        }

        if (empty($submittedItems) || !is_array($submittedItems)) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one raw material to issue.');
        }

        DB::beginTransaction();
        try {
            $totalItems = 0;
            $totalQty   = 0.0;
            $totalCost  = 0.0;
            $itemsData  = [];

            foreach ($submittedItems as $row) {
                $productId = $row['product_id'] ?? null;
                $qtyUsed   = (float) ($row['qty_used'] ?? 0);

                if ($qtyUsed <= 0) {
                    continue;
                }

                $product = Product::with('unit')->findOrFail($productId);

                if ($product->item_type !== 'raw_material') {
                    throw new \Exception("Product '{$product->item_name}' is not a raw material.");
                }

                // Lock warehouse stock row for update (auto single warehouse)
                $warehouseStock = WarehouseStock::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->lockForUpdate()
                    ->first();

                if (!$warehouseStock) {
                    $warehouseStock = WarehouseStock::where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();
                }

                $availableStock = $warehouseStock ? (float) ($warehouseStock->total_pieces ?? $warehouseStock->quantity ?? 0) : 0.0;

                if ($qtyUsed > $availableStock) {
                    throw new \Exception("Insufficient stock for '{$product->item_name}'. Available: {$availableStock}, Requested: {$qtyUsed}.");
                }

                $unitCost = (float) ($product->purchase_price_per_piece ?? 0.0);
                $lineCost = $qtyUsed * $unitCost;
                $unitName = $product->unit ? ($product->unit->unit_name ?? $product->unit->name ?? 'Pcs') : 'Pcs';

                $itemsData[] = [
                    'product'                => $product,
                    'warehouse_stock'        => $warehouseStock,
                    'available_stock_at_time'=> $availableStock,
                    'qty_used'               => $qtyUsed,
                    'unit_name'              => $unitName,
                    'unit_cost'              => $unitCost,
                    'total_cost'             => $lineCost,
                    'notes'                  => $row['notes'] ?? null,
                ];

                $totalItems++;
                $totalQty  += $qtyUsed;
                $totalCost += $lineCost;
            }

            if (count($itemsData) === 0) {
                throw new \Exception("Please select at least one valid raw material with a quantity greater than zero.");
            }

            $usageNo = MaterialUsage::generateNextUsageNo();

            // Create Master Material Usage Voucher
            $materialUsage = MaterialUsage::create([
                'usage_no'            => $usageNo,
                'date'                => $request->date,
                'warehouse_id'        => $warehouseId,
                'user_id'             => Auth::id(),
                'production_order_no' => null,
                'purpose'             => $request->purpose ?: 'Production Consumption',
                'remarks'             => $request->remarks,
                'total_items'         => $totalItems,
                'total_qty'           => $totalQty,
                'total_cost'          => $totalCost,
            ]);

            // Save line items, deduct warehouse stock, and log stock movements
            foreach ($itemsData as $item) {
                MaterialUsageItem::create([
                    'material_usage_id'      => $materialUsage->id,
                    'product_id'             => $item['product']->id,
                    'unit_name'              => $item['unit_name'],
                    'available_stock_at_time'=> $item['available_stock_at_time'],
                    'qty_used'               => $item['qty_used'],
                    'unit_cost'              => $item['unit_cost'],
                    'total_cost'             => $item['total_cost'],
                    'notes'                  => $item['notes'],
                ]);

                // Deduct warehouse stock
                $ws = $item['warehouse_stock'];
                $newPieces = max(0, ((float) $ws->total_pieces) - $item['qty_used']);
                $ws->total_pieces = $newPieces;
                $ws->quantity     = $newPieces;
                $ws->remarks      = "Issued in Material Usage {$usageNo}";
                $ws->save();

                // Log in stock_movements
                DB::table('stock_movements')->insert([
                    'product_id'   => $item['product']->id,
                    'type'         => 'out',
                    'qty'          => $item['qty_used'],
                    'ref_type'     => 'MATERIAL_USAGE',
                    'ref_id'       => $materialUsage->id,
                    'note'         => "Usage #{$usageNo} | Purpose: " . ($materialUsage->purpose ?: 'Production Issue'),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('material_usage.index')->with('success', "Material Usage Voucher {$usageNo} created successfully. Stock deducted.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show / Print Requisition Voucher
     */
    public function show($id)
    {
        $this->checkPermission();

        $usage = MaterialUsage::with(['warehouse', 'user', 'items.product.unit', 'items.product.category_relation'])
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'html'    => view('admin_panel.material_usage.partials.modal_content', compact('usage'))->render(),
            ]);
        }

        return view('admin_panel.material_usage.show', compact('usage'));
    }

    /**
     * Cancel / Delete a Material Usage Voucher and revert deducted stock
     */
    public function destroy($id)
    {
        $this->checkPermission();

        $usage = MaterialUsage::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Restore warehouse stock
            foreach ($usage->items as $item) {
                $ws = WarehouseStock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $usage->warehouse_id)
                    ->first();

                if (!$ws) {
                    $ws = WarehouseStock::where('product_id', $item->product_id)->first();
                }

                if ($ws) {
                    $ws->total_pieces = ((float) $ws->total_pieces) + (float) $item->qty_used;
                    $ws->quantity     = $ws->total_pieces;
                    $ws->remarks      = "Reverted from deleted Material Usage {$usage->usage_no}";
                    $ws->save();
                } else {
                    WarehouseStock::create([
                        'warehouse_id' => $usage->warehouse_id,
                        'product_id'   => $item->product_id,
                        'quantity'     => $item->qty_used,
                        'total_pieces' => $item->qty_used,
                        'remarks'      => "Recreated from deleted Material Usage {$usage->usage_no}",
                    ]);
                }

                // Log reversal in stock_movements
                DB::table('stock_movements')->insert([
                    'product_id'   => $item->product_id,
                    'type'         => 'in',
                    'qty'          => $item->qty_used,
                    'ref_type'     => 'MATERIAL_USAGE_REVERSAL',
                    'ref_id'       => $usage->id,
                    'note'         => "Reversal of Material Usage #{$usage->usage_no}",
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            $usageNo = $usage->usage_no;
            $usage->delete();

            DB::commit();

            return redirect()->route('material_usage.index')->with('success', "Material Usage {$usageNo} cancelled and stock successfully restored.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', "Failed to delete voucher: " . $e->getMessage());
        }
    }

    /**
     * Material Usage Consumption Report
     */
    public function report(Request $request)
    {
        $this->checkPermission();

        $query = MaterialUsageItem::with(['usage.warehouse', 'usage.user', 'product.unit', 'product.category_relation']);

        // Product filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('usage', function ($q) use ($request) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            });
        } elseif ($request->filled('start_date')) {
            $query->whereHas('usage', function ($q) use ($request) {
                $q->where('date', '>=', $request->start_date);
            });
        } elseif ($request->filled('end_date')) {
            $query->whereHas('usage', function ($q) use ($request) {
                $q->where('date', '<=', $request->end_date);
            });
        }

        // Summary Aggregates
        $summaryQuery = clone $query;
        $totalQtyUsed  = $summaryQuery->sum('qty_used');
        $totalCostUsed = $summaryQuery->sum('total_cost');
        $totalTransactions = $summaryQuery->count();

        // Item-wise Grouped Summary
        $itemSummary = DB::table('material_usage_items')
            ->join('material_usages', 'material_usage_items.material_usage_id', '=', 'material_usages.id')
            ->join('products', 'material_usage_items.product_id', '=', 'products.id')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->when($request->filled('product_id'), function ($q) use ($request) {
                return $q->where('material_usage_items.product_id', $request->product_id);
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                return $q->where('material_usages.date', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                return $q->where('material_usages.date', '<=', $request->end_date);
            })
            ->select(
                'products.id as product_id',
                'products.item_name',
                'products.item_code',
                'units.name as unit_name',
                DB::raw('SUM(material_usage_items.qty_used) as sum_qty'),
                DB::raw('SUM(material_usage_items.total_cost) as sum_cost'),
                DB::raw('COUNT(material_usage_items.id) as issue_count')
            )
            ->groupBy('products.id', 'products.item_name', 'products.item_code', 'units.name')
            ->orderByDesc('sum_qty')
            ->get();

        $items = $query->orderByDesc('id')->paginate(50)->withQueryString();

        $rawMaterials = Product::where('item_type', 'raw_material')->orderBy('item_name')->get();

        return view('admin_panel.reporting.material_usage_report', compact(
            'items',
            'itemSummary',
            'rawMaterials',
            'totalQtyUsed',
            'totalCostUsed',
            'totalTransactions'
        ));
    }
}