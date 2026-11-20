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
        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quote_no', 100)->unique()->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('walkin_name', 255)->nullable();
                $table->string('reference', 255)->nullable(); // Subject / Project reference
                $table->string('quote_title', 100)->default('QUOTATION');
                $table->date('quotation_date');
                $table->date('estimated_delivery_date')->nullable();
                $table->decimal('total_bill_amount', 15, 2)->default(0); // Gross Total
                $table->decimal('total_discount', 15, 2)->default(0);
                $table->decimal('tax_percent', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_net', 15, 2)->default(0); // Net Payable
                $table->integer('total_items')->default(0);
                $table->text('terms_and_conditions')->nullable();
                $table->string('status', 50)->default('pending'); // pending, approved, converted, expired, cancelled
                $table->unsignedBigInteger('converted_sale_id')->nullable()->index();
                $table->timestamp('converted_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                $table->foreign('converted_sale_id')->references('id')->on('sales')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('quotation_items')) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('quotation_id')->index();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('product_name', 255);
                $table->string('technical_name', 255)->nullable();
                $table->text('technical_specs')->nullable();
                $table->text('technical_remarks')->nullable();
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('sub_category_id')->nullable();
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->string('size_mode', 50)->default('std');
                $table->decimal('qty', 12, 2)->default(1);
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('discount_percent', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percent', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->string('color', 100)->nullable();
                $table->string('model', 100)->nullable();
                $table->integer('total_pieces')->default(1);
                $table->integer('loose_pieces')->default(0);
                $table->decimal('price_per_piece', 15, 2)->default(0);
                $table->boolean('is_manual')->default(0);
                $table->timestamps();

                $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
