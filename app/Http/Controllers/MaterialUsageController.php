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
use Illuminate\Support\Facades\Schema;

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
        $usageIds = $metricQuery->pluck('id');
        $totalOpeningStock = DB::table('material_usage_items')->whereIn('material_usage_id', $usageIds)->sum('available_stock_at_time');
        $totalQtyUsed = $metricQuery->sum('total_qty');
        $totalRemainingStock = max(0, $totalOpeningStock - $totalQtyUsed);

        $kpiMetrics = [
            'total_usages'          => $metricQuery->count(),
            'total_items'           => $metricQuery->sum('total_items'),
            'total_qty'             => $totalQtyUsed,
            'total_opening_stock'   => $totalOpeningStock,
            'total_remaining_stock' => $totalRemainingStock,
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

        // RAW MATERIALS & BOTH - Finished goods strictly excluded!
        $rawMaterials = Product::whereIn('item_type', ['raw_material', 'both'])
            ->with(['unit', 'category_relation', 'warehouseStocks'])
            ->orderBy('item_name')
            ->get();

        $realStocksMap = $this->calculateRealStocksMap($rawMaterials->pluck('id')->toArray());

        $rawMaterialsJson = $rawMaterials->map(function($rm) use ($realStocksMap) {
            return [
                'id'    => $rm->id,
                'name'  => $rm->item_name,
                'code'  => $rm->item_code ?? '-',
                'stock' => (float)($realStocksMap[$rm->id] ?? 0),
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

        if ($product->item_type !== 'raw_material' && $product->item_type !== 'both') {
            return response()->json([
                'success' => false,
                'message' => 'Selected item is not a raw material.',
            ], 422);
        }

        $realStocks = $this->calculateRealStocksMap([$product->id], $warehouseId);
        $availableStock = (float) ($realStocks[$product->id] ?? 0);

        // If editing an existing usage, add back whatever qty this usage voucher already consumed
        if ($request->filled('usage_id')) {
            $existingQty = MaterialUsageItem::where('material_usage_id', $request->usage_id)
                ->where('product_id', $product->id)
                ->sum('qty_used');
            $availableStock += (float) $existingQty;
        }

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
     * Show form to edit material usage voucher
     */
    public function edit($id)
    {
        $this->checkPermission();
        $defaultWarehouse = $this->getDefaultWarehouse();

        $usage = MaterialUsage::with(['warehouse', 'items.product.unit'])->findOrFail($id);

        $rawMaterials = Product::whereIn('item_type', ['raw_material', 'both'])
            ->with(['unit', 'category_relation', 'warehouseStocks'])
            ->withSum('warehouseStocks', 'total_pieces')
            ->orderBy('item_name')
            ->get();

        // Calculate quantity of each product already in this voucher so we can reflect true available stock
        $usageItemQtyMap = $usage->items->groupBy('product_id')->map(function ($rows) {
            return $rows->sum('qty_used');
        });

        $realStocksMap = $this->calculateRealStocksMap($rawMaterials->pluck('id')->toArray());

        $rawMaterialsJson = $rawMaterials->map(function ($rm) use ($usageItemQtyMap, $realStocksMap) {
            $currentStock = (float) ($realStocksMap[$rm->id] ?? 0);
            $alreadyIssuedInThisVoucher = (float) ($usageItemQtyMap[$rm->id] ?? 0);
            $effectiveStock = $currentStock + $alreadyIssuedInThisVoucher;

            return [
                'id'    => $rm->id,
                'name'  => $rm->item_name,
                'code'  => $rm->item_code ?? '-',
                'stock' => $effectiveStock,
                'unit'  => $rm->unit->name ?? $rm->unit->unit_name ?? 'Pcs',
                'cost'  => (float) ($rm->purchase_price_per_piece ?? 0),
            ];
        })->values();

        return view('admin_panel.material_usage.edit', compact(
            'usage',
            'defaultWarehouse',
            'rawMaterials',
            'rawMaterialsJson',
            'usageItemQtyMap'
        ));
    }

    /**
     * Update material usage voucher and adjust warehouse stock
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission();

        $usage = MaterialUsage::with('items')->findOrFail($id);

        $defaultWarehouse = $this->getDefaultWarehouse();
        $warehouseId = $request->warehouse_id ?: ($usage->warehouse_id ?: $defaultWarehouse->id);

        $request->validate([
            'date'    => 'required|date',
            'purpose' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $submittedItems = $request->items;
        if (! $submittedItems && $request->has('product_id')) {
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

        if (empty($submittedItems) || ! is_array($submittedItems)) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one raw material to issue.');
        }

        DB::beginTransaction();
        try {
            // 1. Restore previous stock deductions for old items of this voucher
            foreach ($usage->items as $oldItem) {
                $ws = WarehouseStock::where('product_id', $oldItem->product_id)
                    ->where('warehouse_id', $usage->warehouse_id)
                    ->lockForUpdate()
                    ->first();

                if (! $ws) {
                    $ws = WarehouseStock::where('product_id', $oldItem->product_id)
                        ->lockForUpdate()
                        ->first();
                }

                if ($ws) {
                    $ws->total_pieces = ((float) $ws->total_pieces) + (float) $oldItem->qty_used;
                    $ws->quantity     = $ws->total_pieces;
                    $ws->save();
                }
            }

            // Remove existing stock movements for this usage voucher
            DB::table('stock_movements')
                ->where('ref_type', 'MATERIAL_USAGE')
                ->where('ref_id', $usage->id)
                ->delete();

            // Delete old items
            $usage->items()->delete();

            // 2. Process and validate newly submitted items against the restored stock
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

                if ($product->item_type !== 'raw_material' && $product->item_type !== 'both') {
                    throw new \Exception("Product '{$product->item_name}' is not a raw material.");
                }

                // Lock warehouse stock for update
                $warehouseStock = WarehouseStock::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->lockForUpdate()
                    ->first();

                if (! $warehouseStock) {
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
                    'product'                 => $product,
                    'warehouse_stock'         => $warehouseStock,
                    'available_stock_at_time' => $availableStock,
                    'qty_used'                => $qtyUsed,
                    'unit_name'               => $unitName,
                    'unit_cost'               => $unitCost,
                    'total_cost'              => $lineCost,
                    'notes'                   => $row['notes'] ?? null,
                ];

                $totalItems++;
                $totalQty  += $qtyUsed;
                $totalCost += $lineCost;
            }

            if (count($itemsData) === 0) {
                throw new \Exception("Please select at least one valid raw material with a quantity greater than zero.");
            }

            // 3. Update Master Material Usage Voucher
            $usage->update([
                'date'        => $request->date,
                'warehouse_id'=> $warehouseId,
                'purpose'     => $request->purpose ?: 'Production Consumption',
                'remarks'     => $request->remarks,
                'total_items' => $totalItems,
                'total_qty'   => $totalQty,
                'total_cost'  => $totalCost,
            ]);

            // 4. Save new line items, deduct warehouse stock, and log stock movements
            foreach ($itemsData as $item) {
                MaterialUsageItem::create([
                    'material_usage_id'       => $usage->id,
                    'product_id'              => $item['product']->id,
                    'unit_name'               => $item['unit_name'],
                    'available_stock_at_time' => $item['available_stock_at_time'],
                    'qty_used'                => $item['qty_used'],
                    'unit_cost'               => $item['unit_cost'],
                    'total_cost'              => $item['total_cost'],
                    'notes'                   => $item['notes'],
                ]);

                // Deduct warehouse stock
                $ws = $item['warehouse_stock'];
                $newPieces = max(0, ((float) $ws->total_pieces) - $item['qty_used']);
                $ws->total_pieces = $newPieces;
                $ws->quantity     = $newPieces;
                $ws->remarks      = "Updated in Material Usage {$usage->usage_no}";
                $ws->save();

                // Log in stock_movements
                DB::table('stock_movements')->insert([
                    'product_id'   => $item['product']->id,
                    'type'         => 'out',
                    'qty'          => $item['qty_used'],
                    'ref_type'     => 'MATERIAL_USAGE',
                    'ref_id'       => $usage->id,
                    'note'         => "Usage #{$usage->usage_no} (Edited) | Purpose: " . ($usage->purpose ?: 'Production Issue'),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('material_usage.index')->with('success', "Material Usage Voucher {$usage->usage_no} updated successfully. Stock adjusted.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
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

        $allProductIds = $itemSummary->pluck('product_id')->toArray();
        $items = $query->orderByDesc('id')->paginate(50)->withQueryString();

        $detailProductIds = $items->pluck('product_id')->unique()->toArray();
        $uniqueProductIds = array_unique(array_merge($allProductIds, $detailProductIds));

        $stocksMap = $this->calculateRealStocksMap($uniqueProductIds);

        foreach ($itemSummary as $row) {
            $row->current_stock = (float) ($stocksMap[$row->product_id] ?? 0);
        }

        foreach ($items as $item) {
            $item->current_stock = (float) ($stocksMap[$item->product_id] ?? 0);
        }

        $totalAvailableStock = 0;
        foreach ($allProductIds as $pid) {
            $totalAvailableStock += (float) ($stocksMap[$pid] ?? 0);
        }

        $rawMaterials = Product::whereIn('item_type', ['raw_material', 'both'])->orderBy('item_name')->get();

        return view('admin_panel.reporting.material_usage_report', compact(
            'items',
            'itemSummary',
            'rawMaterials',
            'totalQtyUsed',
            'totalCostUsed',
            'totalTransactions',
            'totalAvailableStock'
        ));
    }

    /**
     * Calculate true current stock for products, taking into account variant stock calculations
     * and ensuring 100% consistency with Item Stock Report.
     */
    private function calculateRealStocksMap(array $productIds, $warehouseId = null): array
    {
        if (empty($productIds)) {
            return [];
        }

        $products = Product::with('warehouseStocks')
            ->whereIn('id', $productIds)
            ->get();

        $stocksMap = [];

        // Pre-fetch warehouse stocks map as baseline
        $wsQuery = DB::table('warehouse_stocks')->whereIn('product_id', $productIds);
        if ($warehouseId && $warehouseId !== 'all') {
            $wsQuery->where('warehouse_id', $warehouseId);
        }
        $wsMap = $wsQuery->groupBy('product_id')
            ->select('product_id', DB::raw('COALESCE(SUM(total_pieces), 0) as total_stock'))
            ->pluck('total_stock', 'product_id')
            ->toArray();

        foreach ($products as $product) {
            $parsedVariants = [];
            if (!empty($product->color)) {
                try {
                    $decoded = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                    if (is_array($decoded) && count($decoded) > 0 && isset($decoded[0]['name'])) {
                        $parsedVariants = $decoded;
                    }
                } catch (\Exception $e) {}
            }

            if (count($parsedVariants) > 0) {
                // Product has variants - calculate dynamic ledger balance identical to ReportingController
                $purchasesQuery = DB::table('purchase_items as pi')
                    ->join('purchases as pur', 'pur.id', '=', 'pi.purchase_id')
                    ->where('pi.product_id', $product->id)
                    ->whereIn('pur.status_purchase', ['approved', 'posted', 'Returned', 'Partial']);
                if ($warehouseId && $warehouseId !== 'all') {
                    $purchasesQuery->where('pur.warehouse_id', $warehouseId);
                }
                $purchasesList = $purchasesQuery->select('pi.qty as total_pieces', 'pi.color')->get();

                $pReturnsQuery = DB::table('purchase_return_items as pri')->where('pri.product_id', $product->id);
                $purchaseReturnsList = $pReturnsQuery->select('pri.qty', 'pri.color')->get();

                $salesQuery = DB::table('sale_items')->where('product_id', $product->id);
                if ($warehouseId && $warehouseId !== 'all') {
                    $salesQuery->where('warehouse_id', $warehouseId);
                }
                $salesList = $salesQuery->select('total_pieces', 'color')->get();

                $returnsQuery = DB::table('sale_return_items as sri')
                    ->join('sale_returns as sr', 'sr.id', '=', 'sri.sale_return_id')
                    ->where('sri.product_id', $product->id);
                if ($warehouseId && $warehouseId !== 'all') {
                    $returnsQuery->where('sri.warehouse_id', $warehouseId);
                }
                $returnsList = $returnsQuery->select('sri.qty', 'sri.color', 'sr.sale_id')->get();

                $adjQuery = DB::table('stock_movements')
                    ->where('product_id', $product->id)
                    ->where('type', 'adjustment')
                    ->whereNotIn('ref_type', ['INIT', 'INITIAL_STOCK', 'init']);
                if ($warehouseId && $warehouseId !== 'all') {
                    $adjQuery->where('note', 'like', "%Warehouse #{$warehouseId}%");
                }
                $adjList = $adjQuery->select('qty', 'note')->get();

                $usageQuery = DB::table('material_usage_items as mui')
                    ->join('material_usages as mu', 'mu.id', '=', 'mui.material_usage_id')
                    ->where('mui.product_id', $product->id);
                if ($warehouseId && $warehouseId !== 'all') {
                    $usageQuery->where('mu.warehouse_id', $warehouseId);
                }
                $usageList = $usageQuery->select('mui.qty_used', 'mui.notes')->get();

                $variantCount = count($parsedVariants);
                $productTotalBalance = 0;

                foreach ($parsedVariants as $v) {
                    $vRawStock = (string) ($v['stock'] ?? '0');
                    $initial = (float) $vRawStock;

                    $purchased = 0;
                    foreach ($purchasesList as $pItem) {
                        if ($this->matchVariant($pItem, $v, $variantCount)) {
                            $purchased += (float) $pItem->total_pieces;
                        }
                    }

                    $pReturned = 0;
                    foreach ($purchaseReturnsList as $prItem) {
                        if ($this->matchVariant($prItem, $v, $variantCount)) {
                            $pReturned += (float) $prItem->qty;
                        }
                    }

                    $sold = 0;
                    foreach ($salesList as $sItem) {
                        if ($this->matchVariant($sItem, $v, $variantCount)) {
                            $sold += (float) $sItem->total_pieces;
                        }
                    }

                    $returnedQty = 0;
                    foreach ($returnsList as $rItem) {
                        if ($this->matchVariant($rItem, $v, $variantCount)) {
                            $returnedQty += (float) $rItem->qty;
                        }
                    }

                    $adjustments = 0;
                    foreach ($adjList as $adjItem) {
                        if ($this->matchAdjVariant($adjItem, $v, $variantCount)) {
                            $adjustments += (float) $adjItem->qty;
                        }
                    }

                    $materialUsedQty = 0;
                    foreach ($usageList as $uItem) {
                        if ($this->matchVariant($uItem, $v, $variantCount)) {
                            $materialUsedQty += (float) $uItem->qty_used;
                        }
                    }

                    $balance = max(0, $initial + $purchased - $materialUsedQty - $sold + $returnedQty - $pReturned + $adjustments);
                    $productTotalBalance += $balance;
                }

                $stocksMap[$product->id] = (float) $productTotalBalance;

                // Sync warehouse_stocks table so it stays in sync
                $currentWs = (float) ($wsMap[$product->id] ?? 0);
                if (abs($currentWs - $productTotalBalance) > 0.0001) {
                    $firstWs = WarehouseStock::where('product_id', $product->id)->first();
                    if ($firstWs) {
                        $firstWs->total_pieces = $productTotalBalance;
                        $firstWs->quantity = $firstWs->total_pieces;
                        $firstWs->save();
                    }
                }
            } else {
                $stocksMap[$product->id] = (float) ($wsMap[$product->id] ?? 0);
            }
        }

        return $stocksMap;
    }

    private function matchVariant($item, $variant, $totalVariantsCount = 0): bool
    {
        if ($totalVariantsCount <= 1) {
            return true;
        }

        $vColor = strtolower(trim($variant['color'] ?? '-'));
        $vSize  = strtolower(trim($variant['size'] ?? '-'));
        if ($vColor === '' || $vColor === 'none' || $vColor === 'null') $vColor = '-';
        if ($vSize === '' || $vSize === 'none' || $vSize === 'null') $vSize = '-';

        if (($vColor === '-' || $vColor === '') && ($vSize === '-' || $vSize === '')) {
            return true;
        }

        $itemColor = $item->color ?? ($item->notes ?? ($item->note ?? ''));
        if (empty($itemColor)) {
            return $totalVariantsCount <= 1;
        }

        $itemVariant = [];
        $b64Decoded = base64_decode($itemColor, true);
        if ($b64Decoded !== false) {
            $json = json_decode($b64Decoded, true);
            if (is_array($json)) {
                $itemVariant = $json;
            }
        }
        if (empty($itemVariant)) {
            $json = json_decode($itemColor, true);
            if (is_array($json)) {
                $itemVariant = $json;
            }
        }

        if (empty($itemVariant)) {
            $str = strtolower(trim($itemColor));
            if ($str === $vColor || ($vColor !== '-' && str_contains($str, $vColor))) {
                return true;
            }
            return $totalVariantsCount <= 1;
        }

        $itemVColor = strtolower(trim($itemVariant['color'] ?? ($itemVariant['color_val'] ?? '-')));
        $itemVSize  = strtolower(trim($itemVariant['size'] ?? ($itemVariant['size_val'] ?? '-')));

        if ($itemVColor === '') $itemVColor = '-';
        if ($itemVSize === '')  $itemVSize  = '-';

        return ($vColor === '-' || $vColor === $itemVColor) && ($vSize === '-' || $vSize === $itemVSize);
    }

    private function matchAdjVariant($adjItem, $variant, $totalVariantsCount = 0): bool
    {
        if ($totalVariantsCount <= 1) {
            return true;
        }

        $note = strtolower($adjItem->note ?? '');
        if (empty($note)) {
            return $totalVariantsCount <= 1;
        }

        $vSize  = strtolower(trim($variant['size'] ?? '-'));
        $vColor = strtolower(trim($variant['color'] ?? '-'));

        $sizeMatch = true;
        if ($vSize !== '-' && !empty($vSize)) {
            $pattern = '/\b' . preg_quote($vSize, '/') . '\b/i';
            $sizeMatch = preg_match($pattern, $note) === 1;
        }

        $colorMatch = true;
        if ($vColor !== '-' && !empty($vColor)) {
            $pattern = '/\b' . preg_quote($vColor, '/') . '\b/i';
            $colorMatch = preg_match($pattern, $note) === 1;
        }

        return $sizeMatch && $colorMatch;
    }
}