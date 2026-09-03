<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('item_discount');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('tax_percentage');
            $table->decimal('foreign_unit_price', 12, 4)->default(0)->after('tax_amount'); // price in foreign currency
            $table->decimal('received_qty', 12, 3)->default(0)->after('qty'); // actual received qty
        });
    }

    public function down(): void {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['tax_percentage','tax_amount','foreign_unit_price','received_qty']);
        });
    }
};
