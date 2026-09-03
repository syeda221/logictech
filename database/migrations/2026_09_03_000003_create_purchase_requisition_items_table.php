<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pr_id')->constrained('purchase_requisitions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('required_qty', 12, 3)->default(0);
            $table->string('uom')->nullable(); // kg, pcs, mtr
            $table->decimal('estimated_unit_price', 12, 2)->default(0);
            $table->text('reason')->nullable();
            $table->decimal('ordered_qty', 12, 3)->default(0); // filled when PO created
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('purchase_requisition_items');
    }
};
