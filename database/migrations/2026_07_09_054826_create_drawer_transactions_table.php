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
        Schema::create('drawer_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('day_closing_id')->constrained('day_closings')->onDelete('cascade');
            $table->string('type'); // in, out
            $table->string('category'); // expense, temporary_market, owner_withdrawal, other
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->string('status')->default('settled'); // pending, returned, settled
            $table->foreignId('returned_in_closing_id')->nullable()->constrained('day_closings')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drawer_transactions');
    }
};
