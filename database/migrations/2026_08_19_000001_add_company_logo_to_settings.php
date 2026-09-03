<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('settings')->where('key', 'company_logo')->exists();
        if (!$exists) {
            DB::table('settings')->insert([
                'key' => 'company_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'company',
                'label' => 'Company Logo',
                'description' => 'Logo displayed at the top of receipts and invoices',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'company_logo')->delete();
    }
};
