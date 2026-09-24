<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing approved stock purchases where po_status is pending to complete
        DB::table('purchases')
            ->where('status_purchase', 'approved')
            ->where(function ($q) {
                $q->where('po_status', 'pending')
                  ->orWhereNull('po_status');
            })
            ->update(['po_status' => 'complete']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal required
    }
};
