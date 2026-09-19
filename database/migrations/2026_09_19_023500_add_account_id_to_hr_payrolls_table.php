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
        Schema::table('hr_payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('hr_payrolls', 'account_id')) {
                $table->foreignId('account_id')->nullable()->after('payment_date')->constrained('accounts')->nullOnDelete();
            }
            if (!Schema::hasColumn('hr_payrolls', 'previous_advance_balance')) {
                $table->decimal('previous_advance_balance', 10, 2)->default(0.00)->after('advances');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_payrolls', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn(['account_id', 'previous_advance_balance']);
        });
    }
};
