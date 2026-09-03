<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('purchases', function (Blueprint $table) {
            // Purchase type
            $table->enum('purchase_type', ['local', 'import'])->default('local')->after('vendor_id');
            // Link to requisition
            $table->foreignId('pr_id')->nullable()->after('purchase_type')->constrained('purchase_requisitions')->nullOnDelete();
            // Currency fields
            $table->string('currency', 10)->default('PKR')->after('pr_id');
            $table->decimal('exchange_rate', 12, 6)->default(1)->after('currency');
            // PO Status tracking
            $table->enum('po_status', ['pending', 'approved', 'in_transit', 'partially_received', 'received', 'partially_complete', 'complete', 'cancelled'])->default('pending')->after('exchange_rate');
            // Import-specific fields
            $table->string('proforma_invoice_no')->nullable()->after('po_status');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'lc', 'tt_advance', 'credit'])->nullable()->after('proforma_invoice_no');
            $table->date('expected_delivery_date')->nullable()->after('payment_method');
            $table->enum('delivery_terms', ['FOB', 'CIF', 'CFR', 'EXW', 'DDP', 'DAP'])->nullable()->after('expected_delivery_date');
            $table->string('bl_number')->nullable()->after('delivery_terms'); // Bill of Lading
            $table->string('port_of_loading')->nullable()->after('bl_number');
            $table->string('port_of_discharge')->nullable()->after('port_of_loading');
            $table->date('actual_arrival_date')->nullable()->after('port_of_discharge');
            $table->date('clearance_date')->nullable()->after('actual_arrival_date');
            // Created by
            $table->unsignedBigInteger('created_by')->nullable()->after('clearance_date');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['pr_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['purchase_type','pr_id','currency','exchange_rate','po_status','proforma_invoice_no','payment_method','expected_delivery_date','delivery_terms','bl_number','port_of_loading','port_of_discharge','actual_arrival_date','clearance_date','created_by']);
        });
    }
};
