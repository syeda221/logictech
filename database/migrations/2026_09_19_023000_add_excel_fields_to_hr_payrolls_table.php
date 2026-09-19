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
            if (!Schema::hasColumn('hr_payrolls', 'p_days')) {
                $table->decimal('p_days', 5, 2)->default(30.00)->after('month');
            }
            if (!Schema::hasColumn('hr_payrolls', 'salary_count')) {
                $table->decimal('salary_count', 10, 2)->default(0.00)->after('basic_salary');
            }
            if (!Schema::hasColumn('hr_payrolls', 'overtime_hours')) {
                $table->decimal('overtime_hours', 8, 2)->default(0.00)->after('salary_count');
            }
            if (!Schema::hasColumn('hr_payrolls', 'overtime_days')) {
                $table->decimal('overtime_days', 5, 2)->default(0.00)->after('overtime_hours');
            }
            if (!Schema::hasColumn('hr_payrolls', 'overtime_pay')) {
                $table->decimal('overtime_pay', 10, 2)->default(0.00)->after('overtime_days');
            }
            if (!Schema::hasColumn('hr_payrolls', 'total_pay')) {
                $table->decimal('total_pay', 10, 2)->default(0.00)->after('overtime_pay');
            }
            if (!Schema::hasColumn('hr_payrolls', 'advances')) {
                $table->decimal('advances', 10, 2)->default(0.00)->after('total_pay');
            }
            if (!Schema::hasColumn('hr_payrolls', 'other_allowance')) {
                $table->decimal('other_allowance', 10, 2)->default(0.00)->after('advances');
            }
            if (!Schema::hasColumn('hr_payrolls', 'payment_this_month')) {
                $table->decimal('payment_this_month', 10, 2)->default(0.00)->after('net_salary');
            }
            if (!Schema::hasColumn('hr_payrolls', 'closing_balance')) {
                $table->decimal('closing_balance', 10, 2)->default(0.00)->after('payment_this_month');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'p_days',
                'salary_count',
                'overtime_hours',
                'overtime_days',
                'overtime_pay',
                'total_pay',
                'advances',
                'other_allowance',
                'payment_this_month',
                'closing_balance',
            ]);
        });
    }
};
