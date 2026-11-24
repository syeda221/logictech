<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('quotation_items') && !Schema::hasColumn('quotation_items', 'discount_type')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->string('discount_type', 20)->default('percent')->after('discount_amount')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('quotation_items') && Schema::hasColumn('quotation_items', 'discount_type')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->dropColumn('discount_type');
            });
        }
    }
};
