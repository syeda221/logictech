<?php

use Illuminate\Database\Migrations\Migration;
use App\Http\Controllers\Hr\PayrollController;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (class_exists(PayrollController::class)) {
            PayrollController::cleanupDuplicatePayrollTransactions();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed for duplicate cleanup
    }
};
