<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('web_customers', 'customer_id')) {
            Schema::table('web_customers', function (Blueprint $table) {
                $table->string('customer_id')->nullable();
            });
        }

        if (!Schema::hasColumn('customers', 'source')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('source')->default('Manual');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('web_customers', 'customer_id')) {
            Schema::table('web_customers', function (Blueprint $table) {
                $table->dropColumn('customer_id');
            });
        }

        if (Schema::hasColumn('customers', 'source')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('source');
            });
        }
    }
};
