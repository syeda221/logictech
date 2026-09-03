<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained('grn_receipts')->onDelete('cascade');
            $table->foreignId('purchase_item_id')->nullable()->constrained('purchase_items')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->decimal('ordered_qty', 12, 3)->default(0);
            $table->decimal('received_qty', 12, 3)->default(0);
            $table->decimal('accepted_qty', 12, 3)->default(0);
            $table->decimal('rejected_qty', 12, 3)->default(0);
            $table->text('rejection_reason')->nullable();
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->boolean('debit_note_created')->default(false);
            $table->unsignedBigInteger('debit_note_id')->nullable(); // link to purchase_returns
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('grn_items');
    }
};
