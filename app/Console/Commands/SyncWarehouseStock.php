<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncWarehouseStock extends Command
{
    protected $signature = 'stock:sync-warehouse-stock {--dry-run : Only check and report discrepancies without updating}';

    protected $description = 'Synchronize and repair warehouse stocks and total_pieces for all products';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $this->info($isDryRun ? '--- DRY RUN: Checking Stock Discrepancies ---' : '--- Synchronizing Warehouse Stocks ---');

        $defaultWarehouse = Warehouse::first();
        $defaultWhId = $defaultWarehouse ? $defaultWarehouse->id : 1;

        $products = Product::with('warehouseStocks')->get();
        $fixedCount = 0;
        $missingStockCount = 0;
        $zeroPiecesCount = 0;

        foreach ($products as $product) {
            $ppb = ($product->pieces_per_box > 0) ? (float)$product->pieces_per_box : 1.0;
            $variants = [];
            if ($product->color) {
                try {
                    $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                    if (is_array($parsed) && count($parsed) > 0 && isset($parsed[0]['name'])) {
                        $variants = $parsed;
                    }
                } catch (\Exception $e) {}
            }

            $variantStockSum = count($variants) > 0 ? (float)array_sum(array_column($variants, 'stock')) : 0;

            // 1. Check if product has any warehouse_stocks entry
            if ($product->warehouseStocks->isEmpty()) {
                $missingStockCount++;
                $this->warn("Product #{$product->id} ({$product->item_name}): Missing warehouse_stocks record. Initial variant sum: {$variantStockSum}");

                if (!$isDryRun) {
                    WarehouseStock::create([
                        'warehouse_id' => $defaultWhId,
                        'product_id'   => $product->id,
                        'total_pieces' => $variantStockSum,
                        'quantity'     => $ppb > 0 ? round($variantStockSum / $ppb, 2) : $variantStockSum,
                        'price'        => 0,
                        'remarks'      => 'Auto-created during Stock Sync',
                    ]);
                    $fixedCount++;
                }
                continue;
            }

            // 2. Check existing warehouse stocks for total_pieces = 0 but quantity > 0
            foreach ($product->warehouseStocks as $ws) {
                $changed = false;
                $oldPieces = (float)$ws->total_pieces;
                $oldQty = (float)$ws->quantity;

                // Case A: total_pieces is 0 or negative, but quantity (boxes) > 0
                if ($oldPieces <= 0 && $oldQty > 0) {
                    $newPieces = round($oldQty * $ppb, 2);
                    $this->line("Product #{$product->id} ({$product->item_name}) in WH #{$ws->warehouse_id}: Recovered total_pieces from boxes: {$oldPieces} -> {$newPieces}");
                    $zeroPiecesCount++;
                    if (!$isDryRun) {
                        $ws->total_pieces = $newPieces;
                        $ws->save();
                        $changed = true;
                    }
                }

                // Case B: product has variant initial stock, but total_pieces is 0 across all warehouses
                if ($variantStockSum > 0 && (float)$product->warehouseStocks->sum('total_pieces') <= 0 && $ws->warehouse_id == $defaultWhId) {
                    $this->line("Product #{$product->id} ({$product->item_name}): Syncing variant initial sum {$variantStockSum} to WH #{$ws->warehouse_id}");
                    if (!$isDryRun) {
                        $ws->total_pieces = $variantStockSum;
                        $ws->quantity = $ppb > 0 ? round($variantStockSum / $ppb, 2) : $variantStockSum;
                        $ws->save();
                        $changed = true;
                    }
                }

                if ($changed) {
                    $fixedCount++;
                }
            }
        }

        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Total Products Scanned: {$products->count()}");
        $this->info("Products with Missing Stock Records: {$missingStockCount}");
        $this->info("Stocks with Zero Pieces but Positive Quantity: {$zeroPiecesCount}");

        if ($isDryRun) {
            $this->warn("Dry run complete. No records were modified. Run without --dry-run to apply fixes.");
        } else {
            $this->info("Successfully synchronized/fixed {$fixedCount} stock record(s).");
        }

        return 0;
    }
}
