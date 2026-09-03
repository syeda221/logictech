<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('vendors', function (Blueprint $table) {
            $table->enum('type', ['local', 'international'])->default('local')->after('name');
            $table->string('currency', 10)->default('PKR')->after('type');
            $table->string('country')->nullable()->after('currency');
            $table->string('contact_person')->nullable()->after('country');
            $table->string('mobile')->nullable()->after('phone');
            $table->string('ntn_number')->nullable()->after('mobile');
            $table->string('strn_number')->nullable()->after('ntn_number');
            $table->decimal('credit_limit', 15, 2)->default(0)->after('strn_number');
            $table->integer('lead_time_days')->default(7)->after('credit_limit');
            $table->string('payment_terms')->nullable()->after('lead_time_days'); // Net30, Advance, LC, TT
            $table->text('bank_details')->nullable()->after('payment_terms');
            $table->boolean('is_active')->default(true)->after('bank_details');
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['type','currency','country','contact_person','mobile','ntn_number','strn_number','credit_limit','lead_time_days','payment_terms','bank_details','is_active']);
            $table->dropSoftDeletes();
        });
    }
};
