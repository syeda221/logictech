<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('import_shipment_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->decimal('freight_charges', 12, 2)->default(0);
            $table->decimal('insurance_amount', 12, 2)->default(0);
            $table->decimal('port_charges', 12, 2)->default(0);
            $table->decimal('customs_duty', 12, 2)->default(0);
            $table->decimal('regulatory_duty', 12, 2)->default(0);
            $table->decimal('sales_tax_import', 12, 2)->default(0);
            $table->decimal('clearing_agent_charges', 12, 2)->default(0);
            $table->decimal('inland_freight', 12, 2)->default(0);
            $table->decimal('misc_costs', 12, 2)->default(0);
            $table->decimal('total_extra_cost', 12, 2)->default(0); // auto-calculated sum
            $table->enum('allocation_method', ['qty', 'weight', 'value'])->default('qty');
            $table->decimal('landed_cost_per_unit', 12, 4)->default(0); // final calculated value
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('import_shipment_costs');
    }
};
