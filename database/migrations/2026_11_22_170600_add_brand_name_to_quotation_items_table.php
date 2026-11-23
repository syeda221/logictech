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
        if (Schema::hasTable('quotation_items') && !Schema::hasColumn('quotation_items', 'brand_name')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->string('brand_name', 255)->nullable()->after('brand_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quotation_items') && Schema::hasColumn('quotation_items', 'brand_name')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->dropColumn('brand_name');
            });
        }
    }
};
