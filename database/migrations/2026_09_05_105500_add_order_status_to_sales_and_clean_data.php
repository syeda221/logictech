<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'order_status')) {
                $table->string('order_status', 50)->default('pending')->after('sale_status');
            }
        });

        // Migrate any previous corrupted status values where order status was written to sale_status
        DB::table('sales')->whereIn('sale_status', ['pending', 'ready', 'delivered', 'cancelled'])->get()->each(function ($sale) {
            $orderStatus = $sale->sale_status;
            $newSaleStatus = ($sale->is_booking == 1) ? 'booked' : 'posted';
            
            DB::table('sales')->where('id', $sale->id)->update([
                'order_status' => $orderStatus,
                'sale_status' => $newSaleStatus,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'order_status')) {
                $table->dropColumn('order_status');
            }
        });
    }
};
