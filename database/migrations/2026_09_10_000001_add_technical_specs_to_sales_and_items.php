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
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'technical_name')) {
                $table->string('technical_name', 255)->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('sale_items', 'technical_specs')) {
                $table->text('technical_specs')->nullable()->after('color');
            }
            if (!Schema::hasColumn('sale_items', 'technical_remarks')) {
                $table->text('technical_remarks')->nullable()->after('technical_specs');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'delivery_source')) {
                $table->string('delivery_source', 100)->nullable()->after('delivery_date');
            }
            if (!Schema::hasColumn('sales', 'delivery_remarks')) {
                $table->text('delivery_remarks')->nullable()->after('delivery_source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (Schema::hasColumn('sale_items', 'technical_name')) {
                $table->dropColumn('technical_name');
            }
            if (Schema::hasColumn('sale_items', 'technical_specs')) {
                $table->dropColumn('technical_specs');
            }
            if (Schema::hasColumn('sale_items', 'technical_remarks')) {
                $table->dropColumn('technical_remarks');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'delivery_source')) {
                $table->dropColumn('delivery_source');
            }
            if (Schema::hasColumn('sales', 'delivery_remarks')) {
                $table->dropColumn('delivery_remarks');
            }
        });
    }
};
