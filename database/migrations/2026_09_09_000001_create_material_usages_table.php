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
        if (!Schema::hasTable('material_usages')) {
            Schema::create('material_usages', function (Blueprint $table) {
                $table->id();
                $table->string('usage_no', 50)->unique();
                $table->date('date');
                $table->unsignedBigInteger('warehouse_id');
                $table->unsignedBigInteger('user_id');
                $table->string('production_order_no', 100)->nullable();
                $table->string('purpose', 255)->nullable();
                $table->text('remarks')->nullable();
                $table->integer('total_items')->default(0);
                $table->decimal('total_qty', 15, 2)->default(0);
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->timestamps();

                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['date', 'warehouse_id']);
            });
        }

        if (!Schema::hasTable('material_usage_items')) {
            Schema::create('material_usage_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('material_usage_id');
                $table->unsignedBigInteger('product_id');
                $table->string('unit_name', 50)->nullable();
                $table->decimal('available_stock_at_time', 15, 2)->default(0);
                $table->decimal('qty_used', 15, 2);
                $table->decimal('unit_cost', 15, 2)->default(0);
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->foreign('material_usage_id')->references('id')->on('material_usages')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->index(['material_usage_id', 'product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_usage_items');
        Schema::dropIfExists('material_usages');
    }
};
