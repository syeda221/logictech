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
        Schema::create('repair_orders', function (Blueprint $table) {
            $table->id();
            $table->string('repair_no', 30)->unique();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->text('customer_address')->nullable();
            
            // Product details
            $table->unsignedBigInteger('product_id')->nullable()->index(); // Optional link to catalog finished goods
            $table->string('item_name'); // e.g. Industrial Heating Element, Chiller 5-Ton, Inverter
            $table->string('brand_model')->nullable();
            $table->string('serial_no')->nullable();
            $table->text('accessories_received')->nullable(); // e.g. Cable, Remote, Adapter, Box, Sensors
            $table->text('problem_description')->nullable(); // Customer reported fault
            $table->text('physical_condition')->nullable(); // Pre-existing damage, scratches, inspection
            $table->text('technician_notes')->nullable(); // Internal diagnosis & work done
            
            // Financials
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('service_charges', 15, 2)->default(0);
            $table->decimal('parts_charges', 15, 2)->default(0);
            $table->decimal('total_charges', 15, 2)->default(0);
            
            $table->decimal('advance_paid', 15, 2)->default(0);
            $table->unsignedBigInteger('advance_account_id')->nullable()->index(); // Cash/Bank account
            
            $table->decimal('final_paid', 15, 2)->default(0);
            $table->unsignedBigInteger('final_account_id')->nullable()->index(); // Cash/Bank account
            
            $table->decimal('due_amount', 15, 2)->default(0);
            
            // Lifecycle & Workflow
            $table->enum('status', [
                'received',       // Intake complete, slip printed
                'diagnosing',     // In diagnosis
                'in_progress',    // Work ongoing
                'waiting_parts',  // Waiting for spares
                'completed',      // Ready for delivery
                'delivered',      // Handed over to customer, paid
                'cancelled'       // Cannot repair / cancelled
            ])->default('received')->index();
            
            $table->enum('priority', ['normal', 'urgent', 'high'])->default('normal');
            
            // Dates & Users
            $table->date('received_date');
            $table->date('expected_delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedBigInteger('received_by')->nullable()->index();
            $table->unsignedBigInteger('delivered_by')->nullable()->index();
            
            $table->timestamps();
        });

        Schema::create('repair_order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('repair_order_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('action', 50); // created, status_updated, payment_received, delivered, note_added
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('repair_order_id')->references('id')->on('repair_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_order_logs');
        Schema::dropIfExists('repair_orders');
    }
};
