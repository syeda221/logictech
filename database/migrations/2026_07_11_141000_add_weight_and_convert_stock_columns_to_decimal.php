<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add weight_per_piece and wholesale_price to products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'weight_per_piece')) {
                $table->decimal('weight_per_piece', 12, 4)->nullable()->default(0)->after('width');
            }
            if (!Schema::hasColumn('products', 'wholesale_price')) {
                $table->decimal('wholesale_price', 12, 2)->nullable()->default(0)->after('sale_price_per_piece');
            }
        });

        // 2. Change total_pieces in warehouse_stocks to decimal(12,4)
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->decimal('total_pieces', 12, 4)->default(0)->change();
        });

        // 3. Change total_pieces and qty in sale_items to decimal(12,4)
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('total_pieces', 12, 4)->default(0)->change();
            $table->decimal('qty', 12, 4)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'weight_per_piece')) {
                $table->dropColumn('weight_per_piece');
            }
            if (Schema::hasColumn('products', 'wholesale_price')) {
                $table->dropColumn('wholesale_price');
            }
        });

        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->integer('total_pieces')->default(0)->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->integer('total_pieces')->default(0)->change();
            $table->integer('qty')->default(0)->change();
        });
    }
};
