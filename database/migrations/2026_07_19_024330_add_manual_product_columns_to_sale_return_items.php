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
        Schema::table('sale_return_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->boolean('is_manual')->default(0)->after('product_id');
            $table->string('product_name')->nullable()->after('is_manual');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('product_name');
            $table->decimal('purchase_price', 15, 2)->nullable()->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_return_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->dropColumn(['is_manual', 'product_name', 'vendor_id', 'purchase_price']);
        });
    }
};
