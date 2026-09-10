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
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'tax_percent')) {
                $table->decimal('tax_percent', 5, 2)->default(0)->after('total_extradiscount');
            }
            if (!Schema::hasColumn('sales', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percent');
            }
            if (!Schema::hasColumn('sales', 'terms_and_conditions')) {
                $table->text('terms_and_conditions')->nullable()->after('total_amount_Words');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'tax_percent')) {
                $table->dropColumn('tax_percent');
            }
            if (Schema::hasColumn('sales', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }
            if (Schema::hasColumn('sales', 'terms_and_conditions')) {
                $table->dropColumn('terms_and_conditions');
            }
        });
    }
};
