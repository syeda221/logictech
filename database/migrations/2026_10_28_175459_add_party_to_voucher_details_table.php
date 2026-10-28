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
        Schema::table('voucher_details', function (Blueprint $table) {
            if (!Schema::hasColumn('voucher_details', 'party_type')) {
                $table->string('party_type')->nullable()->after('narration');
            }
            if (!Schema::hasColumn('voucher_details', 'party_id')) {
                $table->unsignedBigInteger('party_id')->nullable()->after('party_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_details', function (Blueprint $table) {
            if (Schema::hasColumn('voucher_details', 'party_id')) {
                $table->dropColumn('party_id');
            }
            if (Schema::hasColumn('voucher_details', 'party_type')) {
                $table->dropColumn('party_type');
            }
        });
    }
};
