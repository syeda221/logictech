<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add columns to products table
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_web_visible')->default(0)->after('is_active');
            $table->boolean('show_on_homepage')->default(0)->after('is_web_visible');
            $table->string('promo_tag')->nullable()->after('show_on_homepage'); // Featured, New Arrival, Best Seller, etc.
            $table->decimal('web_sale_price', 15, 2)->nullable()->after('sale_price_per_piece');
            $table->boolean('auto_hide_out_of_stock')->default(0)->after('web_sale_price');
            $table->string('meta_title')->nullable()->after('auto_hide_out_of_stock');
            $table->text('meta_description')->nullable()->after('meta_title');
        });

        // 2. Create product_web_images table
        Schema::create('product_web_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Create web_customers table (if separate from pos customers)
        Schema::create('web_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 4. Create coupons table
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percent']);
            $table->decimal('value', 10, 2);
            $table->decimal('min_spend', 10, 2)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('uses')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        // 5. Create ecommerce_orders table
        Schema::create('ecommerce_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('web_customer_id')->nullable()->constrained('web_customers')->onDelete('set null');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->decimal('delivery_charges', 10, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->string('payment_method'); // COD, Bank Transfer, JazzCash, etc.
            $table->string('payment_status')->default('pending');
            $table->string('order_status')->default('pending');
            $table->text('order_notes')->nullable();
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->timestamps();
        });

        // 6. Create ecommerce_order_items table
        Schema::create('ecommerce_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecommerce_order_id')->constrained('ecommerce_orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('product_name');
            $table->decimal('price', 15, 2);
            $table->integer('quantity');
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });

        // 7. Create wishlists table
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('web_customer_id')->constrained('web_customers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['web_customer_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('ecommerce_order_items');
        Schema::dropIfExists('ecommerce_orders');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('web_customers');
        Schema::dropIfExists('product_web_images');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_web_visible',
                'show_on_homepage',
                'promo_tag',
                'web_sale_price',
                'auto_hide_out_of_stock',
                'meta_title',
                'meta_description'
            ]);
        });
    }
};
