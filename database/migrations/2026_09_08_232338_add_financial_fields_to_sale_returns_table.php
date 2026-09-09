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
        Schema::table('sale_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_returns', 'due_adjusted')) {
                $table->decimal('due_adjusted', 15, 2)->default(0)->after('net_amount');
            }
            if (!Schema::hasColumn('sale_returns', 'refundable_amount')) {
                $table->decimal('refundable_amount', 15, 2)->default(0)->after('due_adjusted');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropColumn(['due_adjusted', 'refundable_amount']);
        });
    }
};
