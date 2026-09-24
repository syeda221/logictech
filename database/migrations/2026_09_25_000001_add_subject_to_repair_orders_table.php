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
        if (!Schema::hasColumn('repair_orders', 'subject')) {
            Schema::table('repair_orders', function (Blueprint $table) {
                $table->text('subject')->nullable()->after('customer_address');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('repair_orders', 'subject')) {
            Schema::table('repair_orders', function (Blueprint $table) {
                $table->dropColumn('subject');
            });
        }
    }
};
