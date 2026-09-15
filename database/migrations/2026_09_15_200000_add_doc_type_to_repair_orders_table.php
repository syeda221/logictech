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
        if (!Schema::hasColumn('repair_orders', 'doc_type')) {
            Schema::table('repair_orders', function (Blueprint $table) {
                $table->string('doc_type', 20)->default('invoice')->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('repair_orders', 'doc_type')) {
            Schema::table('repair_orders', function (Blueprint $table) {
                $table->dropColumn('doc_type');
            });
        }
    }
};
