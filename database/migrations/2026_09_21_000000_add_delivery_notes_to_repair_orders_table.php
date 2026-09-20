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
        if (!Schema::hasColumn('repair_orders', 'delivery_notes')) {
            Schema::table('repair_orders', function (Blueprint $table) {
                $table->text('delivery_notes')->nullable()->after('delivered_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('repair_orders', 'delivery_notes')) {
            Schema::table('repair_orders', function (Blueprint $table) {
                $table->dropColumn('delivery_notes');
            });
        }
    }
};
