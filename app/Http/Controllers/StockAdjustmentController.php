<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    /**
     * Check m,dmd if user is authorized for stock adjustments
     */
    private function checkPermission()
    {
        $user = auth()->user();
        if ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }

        if ($user->can('stock.adjust.view') || $user->can('stock.adjust.create')) {
            return true;
        }

        abort(403, 'Unauthorized action. You do not have permission to access Stock Adjustments.');
    }

    /**
     * Get warehouses permitted for current user
     */
    private function getPermittedWarehouses()
    {
        $user = auth()->user();

        if ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->can('warehouse.view')) {
            return Warehouse::orderBy('warehouse_name')->get();
        }

        if (isset($user->warehouse_id) && $user->warehouse_id) {
            return Warehouse::where('id', $user->warehouse_id)->get();
        }

        $userWarehouses = Warehouse::where('creater_id', $user->id)->get();
        if ($userWarehouses->count() > 0) {
            return $userWarehouses;
        }

        return Warehouse::limit(1)->get();
    }

    /**
     * Audit Log Index Page
     */
    public function index(Request $request)
    {
        $this->checkPermission();

        $permittedWarehouses = $this->getPermittedWarehouses();
        $permittedWarehouseIds = $permittedWarehouses->pluck('id')->toArray();

        $query = StockAdjustment::with(['user', 'warehouse', 'product'])
            ->whereIn('warehouse_id', $permittedWarehouseIds);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('item_name', 'like', "%{$search}%")
                       ->orWhere('item_code', 'like', "%{$search}%");
                })->orWhere('variant_name', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $adjustments = $query->latest()->paginate(20)->appends($request->query());

        // Summary Stats
        $totalAdjustments = StockAdjustment::whereIn('warehouse_id', $permittedWarehouseIds)->count();
        $totalAdded       = StockAdjustment::whereIn('warehouse_id', $permittedWarehouseIds)->where('type', 'add')->sum('qty');
        $totalSubtracted  = StockAdjustment::whereIn('warehouse_id', $permittedWarehouseIds)->where('type', 'subtract')->sum('qty');
        $thisMonthCount   = StockAdjustment::whereIn('warehouse_id', $permittedWarehouseIds)
                                          ->whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->count();

        $products = Product::select('id', 'item_name', 'item_code', 'color', 'size_mode', 'pieces_per_box')->get();

        return view('admin_panel.stock_adjustment.index', [
            'adjustments'       => $adjustments,
            'totalAdjustments'  => $totalAdjustments,
            'totalAdded'        => $totalAdded,
            'totalSubtracted'   => $totalSubtracted,
            'thisMonthCount'    => $thisMonthCount,
            'warehouses'        => $permittedWarehouses,
            'products'          => $products,
        ]);
    }

    /**
     * Dedicated POS-Style Stock Adjustment Terminal Screen
     */
    public function create(Request $request)
    {
        $this->checkPermission();

        $warehouses = $this->getPermittedWarehouses();
        $categories = Category::all();
        $brands     = Brand::all();

        // Fetch products with variant details
        $products = Product::with(['category_relation', 'brand'])
            ->select('id', 'item_name', 'item_code', 'barcode_path', 'image', 'category_id', 'brand_id', 'color', 'size_mode', 'pieces_per_box')
            ->get();

        return view('admin_panel.stock_adjustment.create', compact('warehouses', 'categories', 'brands', 'products'));
    }

    public function getProductVariants(Request $request, $productId)
    {
        $this->checkPermission();

        $product = Product::findOrFail($productId);
        $warehouseId = $request->query('warehouse_id');
        if (!$warehouseId) {
            $warehouseId = $this->getPermittedWarehouses()->first()->id ?? 1;
        }

        $variants = $this->getCalculatedVariants($product);

        $stockPieces = 0;
        if (count($variants) > 0) {
            $stockPieces = array_sum(array_column($variants, 'current_stock'));
        } elseif ($warehouseId) {
            $whStock = WarehouseStock::where('warehouse_id', $warehouseId)->where('product_id', $productId)->first();
            $stockPieces = (float) ($whStock->total_pieces ?? 0);
        } else {
            $stockPieces = (float) WarehouseStock::where('product_id', $productId)->sum('total_pieces');
        }

        return response()->json([
            'success'        => true,
            'product_id'     => $product->id,
            'product_name'   => $product->item_name,
            'item_code'      => $product->item_code,
            'total_stock'    => $stockPieces,
            'has_variants'   => count($variants) > 0,
            'variants'       => $variants
        ]);
    }

    /**
     * Store Single Adjustment
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        if (!$request->filled('warehouse_id')) {
            $firstWh = $this->getPermittedWarehouses()->first();
            if ($firstWh) {
                $request->merge(['warehouse_id' => $firstWh->id]);
            }
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'type'         => 'required|in:add,subtract,set',
            'qty'          => 'required|numeric|min:0',
            'reason'       => 'required|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $this->processSingleAdjustment([
                    'warehouse_id' => $request->warehouse_id,
                    'product_id'   => $request->product_id,
                    'variant_key'  => $request->variant_key,
                    'variant_name' => $request->variant_name,
                    'type'         => $request->type,
                    'qty'          => (float) $request->qty,
                    'reason'       => $request->reason,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment processed successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store Multi-Item / Multi-Variant Batch Adjustment
     */
    public function storeBatch(Request $request)
    {
        $this->checkPermission();

        if (!$request->filled('warehouse_id')) {
            $firstWh = $this->getPermittedWarehouses()->first();
            if ($firstWh) {
                $request->merge(['warehouse_id' => $firstWh->id]);
            }
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.type'       => 'required|in:add,subtract,set',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'global_reason'      => 'required|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $warehouseId   = $request->warehouse_id;
                $globalReason  = $request->global_reason;

                foreach ($request->items as $item) {
                    $itemReason = !empty($item['reason']) ? $item['reason'] : $globalReason;

                    $this->processSingleAdjustment([
                        'warehouse_id' => $warehouseId,
                        'product_id'   => $item['product_id'],
                        'variant_key'  => $item['variant_key'] ?? null,
                        'variant_name' => $item['variant_name'] ?? null,
                        'type'         => $item['type'],
                        'qty'          => (float) $item['qty'],
                        'reason'       => $itemReason,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => count($request->items) . ' stock adjustment(s) saved successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process batch adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper to compute live variant stock balances matching POS logic
     */
    private function getCalculatedVariants($product)
    {
        $variants = [];
        if (!$product->color) {
            return $variants;
        }

        try {
            $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
            if (!is_array($parsed) || count($parsed) === 0 || (!isset($parsed[0]['name']) && !isset($parsed[0]['color']))) {
                return $variants;
            }

            // Fetch sales
            $salesList = DB::table('sale_items')
                ->where('product_id', $product->id)
                ->select('total_pieces', 'color')
                ->get();

            // Fetch confirmed web sales
            $webSalesList = DB::table('ecommerce_order_items as eoi')
                ->join('ecommerce_orders as eo', 'eo.id', '=', 'eoi.ecommerce_order_id')
                ->where('eoi.product_id', $product->id)
                ->where('eo.is_stock_deducted', 1)
                ->select('eoi.quantity as total_pieces', 'eoi.color', 'eoi.size')
                ->get();

            $salesListArray = $salesList->toArray();
            foreach ($webSalesList as $wItem) {
                $salesListArray[] = (object) [
                    'total_pieces' => $wItem->total_pieces,
                    'color' => json_encode([
                        'color' => $wItem->color ?: '-',
                        'size' => $wItem->size ?: '-'
                    ])
                ];
            }
            $salesList = collect($salesListArray);

            $returnsList = DB::table('sale_return_items as sri')
                ->join('sale_returns as sr', 'sr.id', '=', 'sri.sale_return_id')
                ->where('sri.product_id', $product->id)
                ->select('sri.qty', 'sri.color', 'sr.sale_id')
                ->get();

            $saleIds = $returnsList->pluck('sale_id')->unique()->toArray();
            $saleItemsMap = [];
            if (!empty($saleIds)) {
                $siList = DB::table('sale_items')
                    ->whereIn('sale_id', $saleIds)
                    ->where('product_id', $product->id)
                    ->select('sale_id', 'color')
                    ->get();
                foreach ($siList as $si) {
                    $saleItemsMap[$si->sale_id][] = $si->color;
                }
            }

            $purchasesList = DB::table('purchase_items as pi')
                ->join('purchases as pur', 'pur.id', '=', 'pi.purchase_id')
                ->where('pi.product_id', $product->id)
                ->whereIn('pur.status_purchase', ['approved', 'Returned', 'Partial'])
                ->select('pi.qty as total_pieces', 'pi.color')
                ->get();

            $purchaseReturnsList = DB::table('purchase_return_items as pri')
                ->where('pri.product_id', $product->id)
                ->select('pri.qty', 'pri.color')
                ->get();

            foreach ($parsed as $v) {
                $vName = $v['name'] ?? $product->item_name;
                $vSize = $v['size'] ?? '-';
                $vColor = $v['color'] ?? '-';
                $vInitial = (float)($v['stock'] ?? 0);

                $purchased = 0;
                foreach ($purchasesList as $pItem) {
                    if ($this->matchSaleItemToVariant($pItem, $v)) {
                        $purchased += (float) $pItem->total_pieces;
                    }
                }

                $pReturned = 0;
                foreach ($purchaseReturnsList as $prItem) {
                    if ($this->matchSaleItemToVariant($prItem, $v)) {
                        $pReturned += (float) $prItem->qty;
                    }
                }

                $sold = 0;
                foreach ($salesList as $sItem) {
                    if ($this->matchSaleItemToVariant($sItem, $v)) {
                        $sold += (float) $sItem->total_pieces;
                    }
                }

                $returnedQty = 0;
                foreach ($returnsList as $rItem) {
                    $rColor = $rItem->color;
                    if (empty($rColor)) {
                        $saleColors = $saleItemsMap[$rItem->sale_id] ?? [];
                        $rColor = !empty($saleColors) ? $saleColors[0] : '';
                    }
                    $rItemCopy = (object)[
                        'qty' => $rItem->qty,
                        'color' => $rColor
                    ];
                    if ($this->matchSaleItemToVariant($rItemCopy, $v)) {
                        $returnedQty += (float) $rItem->qty;
                    }
                }

                $vUncappedStock = $vInitial + $purchased - $sold + $returnedQty - $pReturned;
                $vCurrentStock = max(0, $vUncappedStock);

                $variants[] = [
                    'variant_key'   => ($vName . '|' . $vSize . '|' . $vColor),
                    'name'          => $vName,
                    'size'          => $vSize,
                    'color'         => $vColor,
                    'initial_stock' => $vInitial,
                    'current_stock' => $vCurrentStock,
                    'uncapped_stock'=> $vUncappedStock,
                    'display_label' => "{$vName} (Size: {$vSize}, Color: {$vColor})"
                ];
            }
        } catch (\Exception $e) {}

        return $variants;
    }

    /**
     * Match a sale/purchase item to a specific variant
     */
    private function matchSaleItemToVariant($saleItem, $variant)
    {
        $itemColor = $saleItem->color;
        if (empty($itemColor)) {
            return false;
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
            return strtolower(trim($itemColor)) === strtolower(trim($variant['color'] ?? ''));
        }

        $vColor = strtolower(trim($variant['color'] ?? '-'));
        $vSize = strtolower(trim($variant['size'] ?? '-'));

        $itemVColor = strtolower(trim($itemVariant['color'] ?? ($itemVariant['color_val'] ?? '-')));
        $itemVSize = strtolower(trim($itemVariant['size'] ?? ($itemVariant['size_val'] ?? '-')));

        if ($vColor === '') $vColor = '-';
        if ($vSize === '') $vSize = '-';
        if ($itemVColor === '') $itemVColor = '-';
        if ($itemVSize === '') $itemVSize = '-';

        return $vColor === $itemVColor && $vSize === $itemVSize;
    }

    /**
     * Private helper to execute a single stock adjustment
     */
    private function processSingleAdjustment(array $data)
    {
        $product = Product::where('id', $data['product_id'])->lockForUpdate()->first();

        $warehouseStock = WarehouseStock::firstOrCreate(
            [
                'warehouse_id' => $data['warehouse_id'],
                'product_id'   => $data['product_id'],
            ],
            [
                'quantity'     => 0,
                'total_pieces' => 0,
            ]
        );

        $variantKey  = $data['variant_key'] ?? null;
        $variantName = $data['variant_name'] ?? null;
        $inputQty    = (float) $data['qty'];
        $deltaQty    = 0;
        $oldStock    = 0;
        $newStock    = 0;

        if ($variantKey && $product->color) {
            $variants = $this->getCalculatedVariants($product);
            $targetVariant = null;
            foreach ($variants as $v) {
                if ($variantKey ? ($v['variant_key'] === $variantKey) : ($variantName && $v['name'] === $variantName)) {
                    $targetVariant = $v;
                    break;
                }
            }

            $oldStock = $targetVariant ? (float) $targetVariant['current_stock'] : 0;
            $oldUncapped = $targetVariant ? (float) $targetVariant['uncapped_stock'] : 0;

            if ($data['type'] === 'add') {
                $deltaQty = $inputQty;
                $newStock = $oldStock + $deltaQty;
            } elseif ($data['type'] === 'subtract') {
                $deltaQty = -1 * min($oldStock, $inputQty);
                $newStock = max(0, $oldStock + $deltaQty);
            } else { // 'set'
                $deltaQty = $inputQty - $oldStock;
                $newStock = max(0, $inputQty);
            }

            try {
                $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                if (is_array($parsed)) {
                    foreach ($parsed as &$v) {
                        $vKey = ($v['name'] ?? $product->item_name) . '|' . ($v['size'] ?? '-') . '|' . ($v['color'] ?? '-');
                        if ($variantKey ? ($vKey === $variantKey) : ($variantName && ($v['name'] ?? '') === $variantName)) {
                            $vOld = (float)($v['stock'] ?? 0);
                            $v['stock'] = $vOld + ($newStock - $oldUncapped);
                            break;
                        }
                    }
                    unset($v);
                    $product->color = json_encode($parsed);
                    $product->save();
                }
            } catch (\Exception $e) {}

            // Update WarehouseStock
            $warehouseStock->total_pieces = max(0, (float)$warehouseStock->total_pieces + $deltaQty);
            $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
            if ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size'])) {
                $warehouseStock->boxes_quantity = floor($warehouseStock->total_pieces / $ppb);
                $warehouseStock->quantity       = $warehouseStock->boxes_quantity;
            } else {
                $warehouseStock->quantity       = $warehouseStock->total_pieces;
            }
            $warehouseStock->remarks = 'Adjusted via Stock Adjustment module';
            $warehouseStock->save();

        } else {
            // Standard Product (No variants)
            $oldStock = (float) $warehouseStock->total_pieces;

            if ($data['type'] === 'add') {
                $deltaQty = $inputQty;
                $newStock = $oldStock + $deltaQty;
            } elseif ($data['type'] === 'subtract') {
                $deltaQty = -1 * min($oldStock, $inputQty);
                $newStock = max(0, $oldStock + $deltaQty);
            } else { // 'set'
                $deltaQty = $inputQty - $oldStock;
                $newStock = max(0, $inputQty);
            }

            $warehouseStock->total_pieces = max(0, $newStock);
            $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
            if ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size'])) {
                $warehouseStock->boxes_quantity = floor($warehouseStock->total_pieces / $ppb);
                $warehouseStock->quantity       = $warehouseStock->boxes_quantity;
            } else {
                $warehouseStock->quantity       = $warehouseStock->total_pieces;
            }
            $warehouseStock->remarks = 'Adjusted via Stock Adjustment module';
            $warehouseStock->save();
        }

        // Log Stock Movement
        DB::table('stock_movements')->insert([
            'product_id'   => $product->id,
            'type'         => 'adjustment',
            'qty'          => $deltaQty,
            'ref_type'     => 'STOCK_ADJUSTMENT',
            'ref_id'       => null,
            'note'         => "Warehouse #{$data['warehouse_id']} | " . ($variantName ? "Variant: {$variantName} | " : '') . "Reason: " . $data['reason'],
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Create StockAdjustment audit record
        StockAdjustment::create([
            'user_id'      => auth()->id(),
            'warehouse_id' => $data['warehouse_id'],
            'product_id'   => $product->id,
            'variant_key'  => $variantKey,
            'variant_name' => $variantName ?: null,
            'type'         => $data['type'],
            'qty'          => $inputQty,
            'old_stock'    => $oldStock,
            'new_stock'    => $newStock,
            'reason'       => $data['reason'],
        ]);
    }
}
