<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\JournalEntry;
use App\Models\VoucherDetail;
use App\Models\VoucherMaster;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    /**
     * Get customer balance from journal entries
     * Positive = Customer owes money (Dr)
     * Negative = Customer has advance/credit (Cr)
     */
    public function getCustomerBalance($customer): float
    {
        if (!($customer instanceof Customer)) {
            $customer = Customer::find($customer);
        }

        if (! $customer) {
            return 0;
        }

        // Auto-sync opening balance if present in customer table but journal/ledger entry is missing
        $this->ensureCustomerOpeningBalanceSynced($customer);

        // Sum of all journal entries for this customer (includes Opening Balance journal entry)
        $journalBalance = JournalEntry::where('party_type', Customer::class)
            ->where('party_id', $customer->id)
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->value('balance') ?? 0;

        return (float) $journalBalance;
    }

    /**
     * Get customer balance before a specific date
     */
    public function getCustomerBalanceBeforeDate(int $customerId, string $date): float
    {
        $journalBalance = JournalEntry::where('party_type', Customer::class)
            ->where('party_id', $customerId)
            ->where('entry_date', '<', $date)
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->value('balance') ?? 0;

        return (float) $journalBalance;
    }

    /**
     * Get customer ledger entries for a date range
     */
    public function getCustomerLedger(int $customerId, string $startDate, string $endDate): array
    {
        $customer = Customer::find($customerId);
        if (! $customer) {
            return [
                'customer' => null,
                'opening_balance' => 0,
                'transactions' => [],
            ];
        }

        // Auto-sync opening balance if present in customer table but journal/ledger entry is missing
        $this->ensureCustomerOpeningBalanceSynced($customer);

        // Get opening balance (balance before start date)
        $openingBalance = $this->getCustomerBalanceBeforeDate($customerId, $startDate);

        // Get journal entries in range
        $entries = JournalEntry::where('party_type', Customer::class)
            ->where('party_id', $customerId)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->orderBy('id', 'asc')
            ->get();

        // Calculate running balance
        $runningBalance = $openingBalance;
        $transactions = $entries->map(function ($entry) use (&$runningBalance) {
            $runningBalance += ($entry->debit - $entry->credit);

            return [
                'id' => $entry->id,
                'date' => $entry->entry_date,
                'description' => $entry->description,
                'debit' => $entry->debit,
                'credit' => $entry->credit,
                'balance' => $runningBalance,
                'source_type' => $entry->source_type,
                'source_id' => $entry->source_id,
            ];
        });

        return [
            'customer' => $customer,
            'opening_balance' => $openingBalance,
            'closing_balance' => $runningBalance,
            'transactions' => $transactions,
        ];
    }

    /**
     * Get vendor balance directly from purchases & payments
     * Positive = We owe vendor (Payable)
     * Logic: Opening + Purchases - Payments - Purchase Returns
     */
    public function getVendorBalance(int $vendorId): float
    {
        $apId = $this->getAccountsPayableId();
        
        $balance = JournalEntry::where('party_type', \App\Models\Vendor::class)
            ->where('party_id', $vendorId)
            ->where('account_id', $apId)
            ->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as balance')
            ->value('balance') ?? 0;

        return (float) $balance;
    }

    /**
     * Get vendor balance before a specific date
     */
    public function getVendorBalanceBeforeDate(int $vendorId, string $date): float
    {
        $apId = $this->getAccountsPayableId();

        $balance = JournalEntry::where('party_type', \App\Models\Vendor::class)
            ->where('party_id', $vendorId)
            ->where('account_id', $apId)
            ->where('entry_date', '<', $date)
            ->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as balance')
            ->value('balance') ?? 0;

        return (float) $balance;
    }

    /**
     * Get Financial Summary for Dashboard
     */
    public function getFinancialSummary(string $startDate, string $endDate): array
    {
        // 1. Sales Revenue (Credit entries in Sales Account)
        // Assuming Sales Account ID is 4 (Standard) or fetch by code
        $salesHeadId = 4; // Income
        $sales = JournalEntry::whereHas('account', function ($q) use ($salesHeadId) {
            $q->where('head_id', $salesHeadId);
        })
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->sum('credit');

        // 2. Purchase Expense (Debit entries in Expense Account)
        $expenseHeadId = 3; // Expense
        $purchases = JournalEntry::whereHas('account', function ($q) use ($expenseHeadId) {
            $q->where('head_id', $expenseHeadId);
        })
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->sum('debit');

        // 3. Total Receivables (Money people owe us)
        $receivables = \App\Models\CustomerLedger::sum('closing_balance'); // Use legacy for now or calculate from journals

        // 4. Total Payables (Money we owe vendors)
        // Calculate from Journal Entries since we just implemented it
        $payables = JournalEntry::where('party_type', \App\Models\Vendor::class)
            ->selectRaw('SUM(credit) - SUM(debit) as balance')
            ->value('balance') ?? 0;

        return [
            'sales' => $sales,
            'purchases' => $purchases,
            'receivables' => $receivables,
            'payables' => $payables,
            'net_cash_flow' => $sales - $purchases, // Rough estimate
        ];
    }

    /**
     * Get vendor ledger entries for a date range
     * Builds from purchases + payment journal entries directly
     */
    public function getVendorLedger(int $vendorId, string $startDate, string $endDate): array
    {
        $vendor = \App\Models\Vendor::find($vendorId);
        if (! $vendor) {
            return ['vendor' => null, 'opening_balance' => 0, 'transactions' => []];
        }

        $openingBalance = $this->getVendorBalanceBeforeDate($vendorId, $startDate);

        // Purchases in range (Increase payable - they go in Debit column = money we owe)
        $purchases = DB::table('purchases')
            ->where('vendor_id', $vendorId)
            ->whereIn('status_purchase', ['approved', 'Returned'])
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->select('id', 'invoice_no', 'net_amount', 'purchase_date', 'note')
            ->get()
            ->map(fn($p) => [
                'source_type' => 'Purchase',
                'source_id'   => $p->id,
                'date'        => $p->purchase_date,
                'description' => 'Purchase Invoice #' . $p->invoice_no . ($p->note ? ' (' . $p->note . ')' : ''),
                'debit'       => 0,
                'credit'      => (float) $p->net_amount, // Cr = we owe vendor more
                'sort_date'   => $p->purchase_date,
            ]);

        // Purchase Returns in range (Reduce payable)
        $returns = DB::table('purchase_returns')
            ->where('vendor_id', $vendorId)
            ->whereBetween('return_date', [$startDate, $endDate])
            ->select('id', 'return_invoice', 'net_amount', 'return_date')
            ->get()
            ->map(fn($r) => [
                'source_type' => 'PurchaseReturn',
                'source_id'   => $r->id,
                'date'        => $r->return_date,
                'description' => 'Purchase Return #' . $r->return_invoice,
                'debit'       => (float) $r->net_amount, // Dr = reduces what we owe
                'credit'      => 0,
                'sort_date'   => $r->return_date,
            ]);

        // Payments in range: AP debit journal entries against this vendor (excluding duplicate purchase/return voucher entries)
        $apId = $this->getAccountsPayableId();
        $payments = JournalEntry::where('party_type', \App\Models\Vendor::class)
            ->where('party_id', $vendorId)
            ->where('account_id', $apId)
            ->whereNot('description', 'like', 'Payable to Vendor%')
            ->whereNot('description', 'like', 'Debit Note for Return%')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get()
            ->map(fn($e) => [
                'source_type' => $e->source_type,
                'source_id'   => $e->source_id,
                'date'        => $e->entry_date,
                'description' => $e->description,
                'debit'       => (float) $e->debit,   // Dr = reduces what we owe
                'credit'      => (float) $e->credit,  // Cr = increases what we owe (e.g. refunds/adjustments)
                'sort_date'   => $e->entry_date,
            ]);

        // Merge & sort
        $all = collect([])
            ->merge($purchases)
            ->merge($returns)
            ->merge($payments)
            ->sortBy('sort_date')
            ->values();

        $runningBalance = $openingBalance;
        $transactions = $all->map(function ($row) use (&$runningBalance) {
            // Credit = payable increases | Debit = payable decreases
            $runningBalance += $row['credit'] - $row['debit'];
            return array_merge($row, ['balance' => $runningBalance]);
        });

        return [
            'vendor'          => $vendor,
            'opening_balance' => $openingBalance,
            'closing_balance' => $runningBalance,
            'transactions'    => $transactions,
        ];
    }

    /**
     * Create a Receipt Voucher using VoucherMaster + JournalEntry
     */
    public function createReceiptVoucher(
        Customer $customer,
        float $amount,
        int $cashAccountId,
        string $date,
        ?string $description = null,
        $source = null
    ): VoucherMaster {
        return DB::transaction(function () use ($customer, $amount, $cashAccountId, $date, $description) {

            // 1. Generate voucher number
            $voucherNo = $this->generateVoucherNo('receipt');

            // 2. Create VoucherMaster
            $voucher = VoucherMaster::create([
                'voucher_type' => VoucherMaster::TYPE_RECEIPT,
                'voucher_no' => $voucherNo,
                'date' => $date,
                'party_type' => Customer::class,
                'party_id' => $customer->id,
                'total_amount' => $amount,
                'remarks' => $description ?? "Receipt from {$customer->customer_name}",
                'status' => VoucherMaster::STATUS_POSTED,
                'created_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // 3. Create VoucherDetails (Dr Cash, Cr Receivable)
            $receivableAccountId = $this->getAccountsReceivableId();

            // Debit Cash/Bank
            VoucherDetail::create([
                'voucher_master_id' => $voucher->id,
                'account_id' => $cashAccountId,
                'debit' => $amount,
                'credit' => 0,
                'narration' => 'Cash/Bank received',
            ]);

            // Credit Accounts Receivable
            VoucherDetail::create([
                'voucher_master_id' => $voucher->id,
                'account_id' => $receivableAccountId,
                'debit' => 0,
                'credit' => $amount,
                'narration' => 'Customer payment received',
            ]);

            // 4. Create Journal Entries
            $journalService = app(JournalEntryService::class);

            // Dr Cash
            $journalService->recordEntry(
                $voucher,
                $cashAccountId,
                $amount,
                0,
                $description ?? "Receipt #{$voucherNo}",
                $date
            );

            // Cr Receivable (with Customer party)
            $journalService->recordEntry(
                $voucher,
                $receivableAccountId,
                0,
                $amount,
                $description ?? "Receipt #{$voucherNo}",
                $date,
                $customer
            );

            return $voucher;
        });
    }

    /**
     * Create a Sale Invoice Voucher
     */
    public function createSaleVoucher(
        Customer $customer,
        float $amount,
        string $invoiceNo,
        string $date
    ): VoucherMaster {
        return DB::transaction(function () use ($customer, $amount, $invoiceNo, $date) {

            $voucherNo = $this->generateVoucherNo('journal');

            $voucher = VoucherMaster::create([
                'voucher_type' => VoucherMaster::TYPE_JOURNAL,
                'voucher_no' => $voucherNo,
                'date' => $date,
                'party_type' => Customer::class,
                'party_id' => $customer->id,
                'total_amount' => $amount,
                'remarks' => "Sale Invoice #{$invoiceNo}",
                'status' => VoucherMaster::STATUS_POSTED,
                'created_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            $receivableAccountId = $this->getAccountsReceivableId();
            $salesAccountId = $this->getSalesRevenueId();

            // Dr Receivable
            VoucherDetail::create([
                'voucher_master_id' => $voucher->id,
                'account_id' => $receivableAccountId,
                'debit' => $amount,
                'credit' => 0,
                'narration' => "Sale Invoice #{$invoiceNo}",
            ]);

            // Cr Sales Revenue
            VoucherDetail::create([
                'voucher_master_id' => $voucher->id,
                'account_id' => $salesAccountId,
                'debit' => 0,
                'credit' => $amount,
                'narration' => "Sale Invoice #{$invoiceNo}",
            ]);

            // Journal Entries
            $journalService = app(JournalEntryService::class);

            // Dr Receivable with customer party
            $journalService->recordEntry(
                $voucher,
                $receivableAccountId,
                $amount,
                0,
                "Sale Invoice #{$invoiceNo}",
                $date,
                $customer
            );

            // Cr Sales
            $journalService->recordEntry(
                $voucher,
                $salesAccountId,
                0,
                $amount,
                "Sale Invoice #{$invoiceNo}",
                $date
            );

            return $voucher;
        });
    }

    /**
     * Generate unique voucher number
     */
    private function generateVoucherNo(string $type): string
    {
        $prefix = match ($type) {
            'receipt' => 'RV',
            'payment' => 'PV',
            'expense' => 'EV',
            'journal' => 'JV',
            default => 'V',
        };

        $year = date('Y');
        $lastVoucher = VoucherMaster::where('voucher_type', $type)
            ->where('voucher_no', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastVoucher) {
            $lastNum = (int) substr($lastVoucher->voucher_no, -4);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return "{$prefix}-{$year}-".str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get Accounts Receivable account ID
     */
    public function getAccountsReceivableId(): int
    {
        $account = Account::where('title', 'like', '%Receivable%')
            ->orWhere('account_code', 'AR')
            ->first();

        if (! $account) {
            $account = Account::create([
                'title' => 'Accounts Receivable',
                'account_code' => 'AR',
                'type' => 'Debit',
                'head_id' => null,
                'opening_balance' => 0,
                'status' => 1,
            ]);
        }

        return $account->id;
    }

    /**
     * Get Sales Revenue account ID
     */
    public function getSalesRevenueId(): int
    {
        $account = Account::where('title', 'like', '%Sales%')
            ->orWhere('account_code', 'SALES')
            ->first();

        if (! $account) {
            $account = Account::create([
                'title' => 'Sales Revenue',
                'account_code' => 'SALES',
                'type' => 'Credit', // Income is Credit nature
                'head_id' => null,
                'opening_balance' => 0,
                'status' => 1,
            ]);
        }

        return $account->id;
    }

    /**
     * Get Cash account ID
     */
    public function getCashAccountId(): int
    {
        $account = Account::where('title', 'like', '%Cash%')
            ->orWhere('account_code', 'CASH')
            ->first();

        if (! $account) {
            $account = Account::create([
                'title' => 'Cash Account',
                'account_code' => 'CASH',
                'type' => 'Debit', // Asset is Debit nature
                'head_id' => null,
                'opening_balance' => 0,
                'status' => 1,
            ]);
        }

        return $account->id;
    }

    /**
     * Get Accounts Payable account ID (Liability)
     * Auto-creates if missing.
     */
    public function getAccountsPayableId(): int
    {
        $account = Account::where('title', 'like', '%Payable%')
            ->orWhere('account_code', 'AP')
            ->first();

        if (! $account) {
            \Log::info("BalanceService: 'Accounts Payable' missing, creating it.");
            // Ideally should find a Liability Head, but for now create without head or default
            $account = Account::create([
                'title' => 'Accounts Payable',
                'account_code' => 'AP',
                'type' => 'Credit', // Liability is Cr nature
                'head_id' => null, // Or look for Liability head
                'opening_balance' => 0,
                'status' => 1,
                'is_active' => 1,
            ]);
        }

        return $account->id;
    }

    /**
     * Get Purchase Expense account ID (Expense)
     * Auto-creates if missing.
     */
    public function getPurchaseExpenseId(): int
    {
        $account = Account::where('title', 'like', '%Purchase%')
            ->orWhere('title', 'like', '%Cost of Goods%')
            ->orWhere('account_code', 'PURCHASE')
            ->orWhere('account_code', 'COGS')
            ->first();

        if (! $account) {
            \Log::info("BalanceService: 'Purchase Expense' missing, creating it.");
            $account = Account::create([
                'title' => 'Purchase Expense',
                'account_code' => 'PURCHASE',
                'type' => 'Debit', // Expense is Dr nature
                'head_id' => null, // Or look for Expense head
                'opening_balance' => 0,
                'status' => 1,
                'is_active' => 1,
            ]);
        }

        return $account->id;
    }

    /**
     * Synchronize customer opening balance in CustomerLedger and JournalEntry
     */
    public function syncCustomerOpeningBalance(Customer $customer, float $oldOpening = 0, float $newOpening = 0): void
    {
        // 1. Sync Journal Entry
        try {
            $arId = $this->getAccountsReceivableId();
            $journalService = app(JournalEntryService::class);

            $obJournal = JournalEntry::where(function ($q) use ($customer) {
                $q->where('source_type', Customer::class)
                    ->where('source_id', $customer->id);
            })->orWhere(function ($q) use ($customer) {
                $q->where('party_type', Customer::class)
                    ->where('party_id', $customer->id)
                    ->where('description', 'Opening Balance');
            })->first();

            $entryDate = $customer->created_at ? $customer->created_at->format('Y-m-d') : now()->format('Y-m-d');

            if ($obJournal) {
                if ($newOpening > 0) {
                    $oldDebit = (float) $obJournal->debit;
                    $journalDiff = $newOpening - $oldDebit;

                    $obJournal->debit = $newOpening;
                    $obJournal->party_type = Customer::class;
                    $obJournal->party_id = $customer->id;
                    $obJournal->source_type = Customer::class;
                    $obJournal->source_id = $customer->id;
                    $obJournal->account_id = $arId;
                    $obJournal->save();

                    // Adjust Account current_balance
                    $account = Account::find($arId);
                    if ($account && $journalDiff != 0) {
                        $account->current_balance = ($account->current_balance ?? 0) + $journalDiff;
                        $account->save();
                    }
                } else {
                    // Deleted or zeroed opening balance
                    $account = Account::find($obJournal->account_id);
                    if ($account) {
                        $account->current_balance = ($account->current_balance ?? 0) - (float) $obJournal->debit;
                        $account->save();
                    }
                    $obJournal->delete();
                }
            } else {
                if ($newOpening > 0) {
                    $journalService->recordEntry(
                        $customer,
                        $arId,
                        $newOpening,
                        0,
                        "Opening Balance",
                        $entryDate,
                        $customer
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error("syncCustomerOpeningBalance (Journal) Error: " . $e->getMessage());
        }

        // 2. Sync CustomerLedger
        try {
            $openingLedger = CustomerLedger::where('customer_id', $customer->id)
                ->where(function ($q) {
                    $q->where('opening_balance', '>', 0)
                        ->orWhere('description', 'Opening Balance');
                })
                ->orderBy('id', 'asc')
                ->first();

            if ($openingLedger) {
                $oldLedgerOpening = (float) $openingLedger->opening_balance;
                $ledgerDiff = $newOpening - $oldLedgerOpening;

                if ($newOpening > 0) {
                    $openingLedger->opening_balance = $newOpening;
                    $openingLedger->closing_balance = (float)$openingLedger->previous_balance + $newOpening;
                    $openingLedger->save();
                } else {
                    $openingLedger->delete();
                }

                // If ledgerDiff != 0, update all subsequent ledger entries for this customer
                if ($ledgerDiff != 0) {
                    CustomerLedger::where('customer_id', $customer->id)
                        ->where('id', '>', $openingLedger->id)
                        ->increment('previous_balance', $ledgerDiff);

                    CustomerLedger::where('customer_id', $customer->id)
                        ->where('id', '>', $openingLedger->id)
                        ->increment('closing_balance', $ledgerDiff);
                }
            } else {
                if ($newOpening > 0) {
                    $newLedger = CustomerLedger::create([
                        'customer_id' => $customer->id,
                        'admin_or_user_id' => auth()->id() ?? 1,
                        'previous_balance' => 0,
                        'opening_balance' => $newOpening,
                        'closing_balance' => $newOpening,
                        'description' => 'Opening Balance',
                        'created_at' => $customer->created_at ?? now(),
                        'updated_at' => now(),
                    ]);

                    // If other ledger entries existed, shift their previous and closing balances
                    CustomerLedger::where('customer_id', $customer->id)
                        ->where('id', '!=', $newLedger->id)
                        ->increment('previous_balance', $newOpening);

                    CustomerLedger::where('customer_id', $customer->id)
                        ->where('id', '!=', $newLedger->id)
                        ->increment('closing_balance', $newOpening);
                }
            }
        } catch (\Exception $e) {
            \Log::error("syncCustomerOpeningBalance (Ledger) Error: " . $e->getMessage());
        }
    }

    /**
     * Ensure customer's opening balance is synchronized if present in customer master
     */
    public function ensureCustomerOpeningBalanceSynced(Customer $customer): void
    {
        $opening = (float) ($customer->opening_balance ?? 0);
        if ($opening <= 0) {
            return;
        }

        $obJournal = JournalEntry::where(function ($q) use ($customer) {
            $q->where('source_type', Customer::class)
                ->where('source_id', $customer->id);
        })->orWhere(function ($q) use ($customer) {
            $q->where('party_type', Customer::class)
                ->where('party_id', $customer->id)
                ->where('description', 'Opening Balance');
        })->first();

        $openingLedger = CustomerLedger::where('customer_id', $customer->id)
            ->where(function ($q) {
                $q->where('opening_balance', '>', 0)
                    ->orWhere('description', 'Opening Balance');
            })->first();

        $needsSync = false;
        if (! $obJournal || abs((float)$obJournal->debit - $opening) > 0.001) {
            $needsSync = true;
        }
        if (! $openingLedger || abs((float)$openingLedger->opening_balance - $opening) > 0.001) {
            $needsSync = true;
        }

        if ($needsSync) {
            $oldJournalDebit = $obJournal ? (float) $obJournal->debit : 0;
            $this->syncCustomerOpeningBalance($customer, $oldJournalDebit, $opening);
        }
    }

    /**
     * Format balance with Dr/Cr indicator
     */
    public static function formatBalance(float $balance): string
    {
        $formatted = number_format(abs($balance), 2);
        $suffix = $balance >= 0 ? 'Dr' : 'Cr';

        return "{$formatted} {$suffix}";
    }
}
