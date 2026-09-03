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
        if (!Schema::hasColumn('products', 'item_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('item_type', 50)->default('both')->after('brand_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'item_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('item_type');
            });
        }
    }
};
