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
            if (!Schema::hasColumn('sales', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('sales', 'is_synced')) {
                $table->tinyInteger('is_synced')->default(0)->after('uuid');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('customers', 'is_synced')) {
                $table->tinyInteger('is_synced')->default(0)->after('uuid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'uuid')) {
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('sales', 'is_synced')) {
                $table->dropColumn('is_synced');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'uuid')) {
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('customers', 'is_synced')) {
                $table->dropColumn('is_synced');
            }
        });
    }
};
