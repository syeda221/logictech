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
        Schema::table('receipts_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('receipts_vouchers', 'settled_invoices')) {
                $table->json('settled_invoices')->nullable()->after('processed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts_vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('receipts_vouchers', 'settled_invoices')) {
                $table->dropColumn('settled_invoices');
            }
        });
    }
};
