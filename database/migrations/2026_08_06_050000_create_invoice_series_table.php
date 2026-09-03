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
        Schema::create('invoice_series', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 20)->unique();
            $table->unsignedBigInteger('next_number')->default(1);
            $table->integer('padding')->default(4);
            $table->boolean('is_default')->default(0);
            $table->timestamps();
        });

        // Seed initial default series
        DB::table('invoice_series')->insert([
            ['prefix' => 'INV', 'next_number' => 1, 'padding' => 4, 'is_default' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'INVSLE', 'next_number' => 1, 'padding' => 4, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'SQ', 'next_number' => 1, 'padding' => 6, 'is_default' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_series');
    }
};
