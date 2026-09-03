<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grn_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->date('received_date');
            $table->string('delivery_challan_no')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->unsignedBigInteger('store_incharge_id')->nullable();
            $table->enum('qc_status', ['pending', 'passed', 'failed', 'partial'])->default('pending');
            $table->text('qc_notes')->nullable();
            $table->enum('status', ['draft', 'confirmed'])->default('draft');
            $table->boolean('stock_updated')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('received_by')->references('id')->on('users');
            $table->foreign('store_incharge_id')->references('id')->on('users');
        });
    }

    public function down(): void {
        Schema::dropIfExists('grn_receipts');
    }
};
