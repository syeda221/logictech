<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AccountHead;
use App\Models\Account;

class AccountsAndHeadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Create or get Bank head
            $bankHead = AccountHead::firstOrCreate(
                ['name' => 'Bank'],
                ['opening_balance' => 0]
            );

            // Create or get Cash head
            $cashHead = AccountHead::firstOrCreate(
                ['name' => 'Cash'],
                ['opening_balance' => 0]
            );

            // Create a common bank account (UBL) under Bank head
            Account::firstOrCreate(
                ['title' => 'UBL Bank', 'head_id' => $bankHead->id],
                ['account_code' => 'UBL', 'opening_balance' => 0, 'type' => 'Debit', 'status' => 1]
            );

            // Create Cash in Hand under Cash head
            Account::firstOrCreate(
                ['title' => 'Cash in Hand', 'head_id' => $cashHead->id],
                ['account_code' => 'CASH', 'opening_balance' => 0, 'type' => 'Debit', 'status' => 1]
            );
        });
    }
}
