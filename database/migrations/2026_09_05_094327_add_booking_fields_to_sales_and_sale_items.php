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
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'estimated_delivery_date')) {
                $table->date('estimated_delivery_date')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('sales', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('estimated_delivery_date');
            }
            // Allow flexible status strings
            $table->string('sale_status', 50)->default('pending')->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'model')) {
                $table->string('model', 100)->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('sale_items', 'serial_no')) {
                $table->string('serial_no', 100)->nullable()->after('model');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'estimated_delivery_date')) {
                $table->dropColumn('estimated_delivery_date');
            }
            if (Schema::hasColumn('sales', 'delivery_date')) {
                $table->dropColumn('delivery_date');
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            if (Schema::hasColumn('sale_items', 'model')) {
                $table->dropColumn('model');
            }
            if (Schema::hasColumn('sale_items', 'serial_no')) {
                $table->dropColumn('serial_no');
            }
        });
    }
};
