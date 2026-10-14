<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHead;
use App\Models\AccountHistory;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class AccountsHeadController extends Controller
{
    public function index()
    {
        $heads = AccountHead::withCount('accounts')->orderBy('name', 'asc')->get();

        // Exclude system control accounts (AR, AP, SALES, PURCHASE, EXP-SAL)
        $excludedCodes = ['AR', 'AP', 'SALES', 'PURCHASE', 'EXP-SAL'];
        $excludedTitles = ['Accounts Receivable', 'Accounts Payable', 'Sales Revenue', 'Purchase Expense', 'Salaries & Wages Expense'];

        $accounts = Account::with(['head', 'histories.user'])
            ->whereNotIn('account_code', $excludedCodes)
            ->whereNotIn('title', $excludedTitles)
            ->whereNotNull('head_id')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin_panel.chart_of_accounts', compact('heads', 'accounts'));
    }

    public function storeHead(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:account_heads,name',
        ]);

        AccountHead::create([
            'name' => trim($request->name),
        ]);

        return back()->with('success', 'Category / Head added successfully!');
    }

    public function updateHead(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:account_heads,name,' . $id,
        ]);

        $head = AccountHead::findOrFail($id);
        $head->update([
            'name' => trim($request->name),
        ]);

        return back()->with('success', 'Category / Head updated successfully!');
    }

    public function deleteHead($id)
    {
        $head = AccountHead::withCount('accounts')->findOrFail($id);

        if ($head->accounts_count > 0) {
            return back()->with('error', 'Cannot delete "' . $head->name . '" because it has ' . $head->accounts_count . ' associated account(s). Please move or delete those accounts first.');
        }

        $headName = $head->name;
        $head->delete();

        return back()->with('success', 'Category / Head "' . $headName . '" deleted successfully!');
    }

    public function storeAccount(Request $request)
    {
        // If head_id is missing or invalid, fallback to/create default Cash head
        if (!$request->filled('head_id') || !AccountHead::where('id', $request->head_id)->exists()) {
            $defaultHead = AccountHead::firstOrCreate(
                ['name' => 'Cash'],
                ['code' => '1001', 'type' => 'Asset', 'level' => 1]
            );
            $request->merge(['head_id' => $defaultHead->id]);
        }

        $request->validate([
            'head_id' => 'required|exists:account_heads,id',
            'title' => 'required|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'type' => 'nullable|string',
        ]);

        $initialBalance = (float) ($request->opening_balance ?? 0);
        $type = $request->type ?: 'Debit';

        $account = Account::create([
            'head_id' => $request->head_id,
            'title' => $request->title,
            'opening_balance' => $initialBalance,
            'current_balance' => $initialBalance,
            'type' => $type,
            'status' => 1,
        ]);

        $account->account_code = 'ACC-'.str_pad($account->id, 4, '0', STR_PAD_LEFT);
        $account->save();

        // Log initial balance history
        AccountHistory::create([
            'account_id' => $account->id,
            'old_balance' => 0,
            'new_balance' => $initialBalance,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'User',
            'note' => 'Account created with initial balance: Rs ' . number_format($initialBalance, 2),
        ]);

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->filled('_ajax')) {
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!',
                'account' => $account,
            ]);
        }

        return back()->with('success', 'Account added successfully!');
    }

    public function updateAccount(Request $request, $id)
    {
        $request->validate([
            'head_id' => 'required|exists:account_heads,id',
            'title' => 'required',
            'opening_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'type' => 'required',
        ]);

        $account = Account::findOrFail($id);

        $oldOpening = (float) $account->opening_balance;
        $newOpening = (float) $request->opening_balance;

        $oldCurrent = (float) $account->current_balance;
        $newCurrent = (float) $request->current_balance;

        $account->title = $request->title;
        $account->head_id = $request->head_id;
        $account->type = $request->type;
        $account->opening_balance = $newOpening;
        $account->current_balance = $newCurrent;
        $account->status = $request->has('status') ? 1 : 0;

        $account->save();

        // Create detailed audit log entry if opening balance, current balance changed or note provided
        if ($oldOpening != $newOpening || $oldCurrent != $newCurrent || $request->filled('note')) {
            $user = auth()->user();

            $noteParts = [];
            if ($oldCurrent != $newCurrent) {
                $diff = $newCurrent - $oldCurrent;
                $diffStr = ($diff >= 0 ? '+' : '') . number_format($diff, 2);
                $noteParts[] = "Current Balance: Rs " . number_format($oldCurrent, 2) . " -> Rs " . number_format($newCurrent, 2) . " (Diff: " . $diffStr . ")";
            }
            if ($oldOpening != $newOpening) {
                $noteParts[] = "Opening: Rs " . number_format($oldOpening, 2) . " -> Rs " . number_format($newOpening, 2);
            }
            if ($request->filled('note')) {
                $noteParts[] = "Reason: " . $request->note;
            }

            AccountHistory::create([
                'account_id' => $account->id,
                'old_balance' => $oldCurrent,
                'new_balance' => $newCurrent,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'User',
                'note' => implode(' | ', $noteParts),
            ]);
        }

        return back()->with('success', 'Account "' . $account->title . '" ledger balance updated successfully!');
    }

    public function showLedger($id, Request $request)
    {
        $account = Account::with('head')->findOrFail($id);

        $selectedYear = intval($request->get('year', date('Y')));
        if ($request->filled('from_date') && ! $request->has('year')) {
            try {
                $selectedYear = \Carbon\Carbon::parse($request->from_date)->year;
            } catch (\Exception $e) {}
        }

        // Available years for dropdown
        $availableYears = JournalEntry::where('account_id', $id)
            ->selectRaw('DISTINCT YEAR(entry_date) as yr')
            ->whereNotNull('entry_date')
            ->pluck('yr')
            ->map(fn($y) => intval($y))
            ->filter()
            ->toArray();

        $currentYear = intval(date('Y'));
        if (! in_array($currentYear, $availableYears)) {
            $availableYears[] = $currentYear;
        }
        if (! in_array($selectedYear, $availableYears)) {
            $availableYears[] = $selectedYear;
        }
        rsort($availableYears);

        // Fetch all entries for the selected year for the 12-Month Calendar Overview
        $yearEntries = JournalEntry::where('account_id', $id)
            ->whereYear('entry_date', $selectedYear)
            ->with(['party', 'source'])
            ->orderBy('entry_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $entriesByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $entriesByMonth[$m] = [];
        }

        foreach ($yearEntries as $entry) {
            $m = intval(\Carbon\Carbon::parse($entry->entry_date)->month);
            $partyInfo = $this->resolvePartyInfo($entry);

            $voucherNo = '-';
            if ($entry->source && isset($entry->source->voucher_no) && $entry->source->voucher_no) {
                $voucherNo = $entry->source->voucher_no;
            } elseif ($entry->source && isset($entry->source->invoice_no) && $entry->source->invoice_no) {
                $voucherNo = $entry->source->invoice_no;
            }

            $displayDesc = $entry->description ?? '—';
            if (($displayDesc === '—' || str_starts_with($displayDesc, 'Paid from')) && $entry->source && ! empty($entry->source->remarks)) {
                $displayDesc = $entry->source->remarks;
            }

            $entriesByMonth[$m][] = [
                'id' => $entry->id,
                'date' => \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y'),
                'raw_date' => \Carbon\Carbon::parse($entry->entry_date)->format('Y-m-d'),
                'voucher_no' => $voucherNo,
                'description' => $displayDesc,
                'party_name' => $partyInfo['name'],
                'party_type' => $partyInfo['type'],
                'party_initial' => $partyInfo['initial'],
                'debit' => floatval($entry->debit ?? 0),
                'credit' => floatval($entry->credit ?? 0),
            ];
        }

        $monthlyCalendar = [];
        for ($m = 1; $m <= 12; $m++) {
            $mList = $entriesByMonth[$m];
            $totDr = array_sum(array_column($mList, 'debit'));
            $totCr = array_sum(array_column($mList, 'credit'));
            $count = count($mList);

            // Paid amount logic:
            // For Cash/Bank liquid accounts, outgoing payments are Credit (Cr).
            // For Expense accounts, expenses incurred are Debit (Dr).
            // If credit > 0, we treat credit as paid; if credit is 0 and debit > 0, treat debit as paid.
            $paidAmount = $totCr > 0 ? $totCr : $totDr;

            $compactAmount = $this->formatCompactAmount($paidAmount);
            $startDate = sprintf('%04d-%02d-01', $selectedYear, $m);
            $endDate = \Carbon\Carbon::parse($startDate)->endOfMonth()->format('Y-m-d');

            $monthlyCalendar[$m] = [
                'month_num' => $m,
                'short_name' => \Carbon\Carbon::create($selectedYear, $m, 1)->format('M'),
                'full_name' => \Carbon\Carbon::create($selectedYear, $m, 1)->format('F Y'),
                'from_date' => $startDate,
                'to_date' => $endDate,
                'total_debit' => $totDr,
                'total_credit' => $totCr,
                'paid_amount' => $paidAmount,
                'compact_amount' => $compactAmount,
                'count' => $count,
                'entries' => $mList,
            ];
        }

        // Query for the main ledger table
        $query = JournalEntry::where('account_id', $id)
            ->with(['party', 'source'])
            ->orderBy('entry_date', 'asc')
            ->orderBy('id', 'asc');

        if ($request->has('from_date') && $request->has('to_date') && $request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('entry_date', [$request->from_date, $request->to_date]);
        }

        $entries = $query->get();

        return view('admin_panel.accounts.ledger', compact(
            'account',
            'entries',
            'monthlyCalendar',
            'availableYears',
            'selectedYear'
        ));
    }

    /**
     * Resolve party information for a journal entry (Customer, Vendor, Employee, etc.)
     */
    protected function resolvePartyInfo($entry)
    {
        $partyObj = $entry->party;
        $partyType = $entry->party_type;
        $partyName = '';

        if (! $partyObj && $entry->source) {
            if (isset($entry->source->party) && $entry->source->party) {
                $partyObj = $entry->source->party;
                $partyType = get_class($partyObj);
            } elseif (isset($entry->source->customer) && $entry->source->customer) {
                $partyObj = $entry->source->customer;
                $partyType = get_class($partyObj);
            } elseif (isset($entry->source->vendor) && $entry->source->vendor) {
                $partyObj = $entry->source->vendor;
                $partyType = get_class($partyObj);
            }
        }

        if (! $partyObj) {
            $textToSearch = ($entry->description ?? '') . ' ' . (isset($entry->source->remarks) ? $entry->source->remarks : '');
            if (preg_match('/(?:Invoice|INV|#INV)[-\s#]*([A-Za-z0-9\-]+)/i', $textToSearch, $m)) {
                $invNo = trim($m[1]);
                $candidates = [$invNo, 'INV-' . $invNo];
                $sale = \App\Models\Sale::whereIn('invoice_no', $candidates)->with('customer')->first();
                if ($sale) {
                    if ($sale->customer) {
                        $partyObj = $sale->customer;
                        $partyType = get_class($partyObj);
                    } else {
                        $partyName = 'Walk-in Customer';
                        $partyType = 'Customer';
                    }
                }
            }
        }

        if (! $partyObj && ! $partyName && $entry->source_type && $entry->source_id) {
            $sibling = JournalEntry::where('source_type', $entry->source_type)
                ->where('source_id', $entry->source_id)
                ->whereNotNull('party_id')
                ->with('party')
                ->first();
            if ($sibling && $sibling->party) {
                $partyObj = $sibling->party;
                $partyType = $sibling->party_type;
            }
        }

        if ($partyObj) {
            if ($partyObj instanceof \App\Models\Customer || str_contains((string)$partyType, 'Customer')) {
                $partyName = $partyObj->customer_name ?? $partyObj->name ?? 'Customer';
                $partyType = 'Customer';
            } elseif ($partyObj instanceof \App\Models\Vendor || str_contains((string)$partyType, 'Vendor')) {
                $partyName = $partyObj->name ?? 'Vendor';
                $partyType = 'Vendor';
            } elseif ($partyObj instanceof \App\Models\Account || str_contains((string)$partyType, 'Account')) {
                $partyName = $partyObj->title ?? $partyObj->name ?? 'Account';
                $partyType = 'Account';
            } elseif ($partyObj instanceof \App\Models\Hr\Employee || str_contains((string)$partyType, 'Employee')) {
                $partyName = $partyObj->full_name ?? $partyObj->name ?? 'Employee';
                $partyType = 'Employee';
            } else {
                $partyName = $partyObj->name ?? $partyObj->customer_name ?? $partyObj->title ?? class_basename($partyType);
                $partyType = class_basename($partyType);
            }
        }

        // Regex check if description or source remarks mention employee or person
        if (! $partyName) {
            $sourceRemarks = isset($entry->source->remarks) ? $entry->source->remarks : '';
            $checkText = ($entry->description ?? '') . ' ' . $sourceRemarks;

            if ($entry->source_type && $entry->source_id) {
                try {
                    $siblingDesc = JournalEntry::where('source_type', $entry->source_type)
                        ->where('source_id', $entry->source_id)
                        ->where('id', '!=', $entry->id)
                        ->pluck('description')
                        ->filter()
                        ->implode(' ');
                    $checkText .= ' ' . $siblingDesc;
                } catch (\Exception $ex) {}
            }

            if (preg_match('/(?:Salary\s+Paid\s+to|Salary\s+for|Salary\s+Paid:\s*Rs\.?\s*[\d,.]+\s+to|Paid\s+to|Advance\s+Salary\s+to|Advance\s+Salary\s+Paid\s+to)\s+([A-Za-z\s]+?)(?:\s*\(|\s+for|\s*$)/i', $checkText, $pm)) {
                $candidate = trim($pm[1]);
                if (strlen($candidate) > 2) {
                    $partyName = $candidate;
                    $partyType = 'Employee';
                }
            }
        }

        return [
            'name' => $partyName ?: '—',
            'type' => $partyType ?: 'General',
            'initial' => strtoupper(substr(trim($partyName ?: 'G'), 0, 1)),
        ];
    }

    /**
     * Format number compactly (e.g. 30000 -> 30k, 1500 -> 1.5k, 1200000 -> 1.2M)
     */
    protected function formatCompactAmount($amount)
    {
        $abs = abs($amount);
        if ($abs >= 1000000) {
            $val = round($amount / 1000000, 1);
            return ($val == intval($val) ? intval($val) : $val) . 'M';
        } elseif ($abs >= 1000) {
            $val = round($amount / 1000, 1);
            return ($val == intval($val) ? intval($val) : $val) . 'k';
        }
        return number_format($amount, 0);
    }

    public function toggleStatus($id)
    {
        $account = Account::findOrFail($id);
        $account->status = ! $account->status;
        $account->save();

        return back()->with('success', 'Account status updated successfully!');
    }
}
