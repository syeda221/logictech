<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\AccountHead;
use Carbon\Carbon;

class CheckbookController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get all Cash and Bank Accounts
        $cashAndBankHeads = AccountHead::whereIn('name', ['Cash', 'Bank'])->pluck('id');
        $validAccountIds = Account::whereIn('head_id', $cashAndBankHeads)->where('status', 1)->pluck('id');

        // Day/Shift Closing Calculations
        $activeShift = \App\Models\DayClosing::where('status', 'open')->first();
        if ($activeShift) {
            $inflow = JournalEntry::whereIn('account_id', $validAccountIds)
                ->where('created_at', '>=', $activeShift->opened_at)
                ->sum('debit') ?? 0;
            
            $outflow = JournalEntry::whereIn('account_id', $validAccountIds)
                ->where('created_at', '>=', $activeShift->opened_at)
                ->sum('credit') ?? 0;

            // Manual drawer logs
            $manualIn = \App\Models\DrawerTransaction::where('day_closing_id', $activeShift->id)
                ->where('type', 'in')
                ->sum('amount') ?? 0;
            
            // Include returns received during this shift
            $returnsIn = \App\Models\DrawerTransaction::where('returned_in_closing_id', $activeShift->id)
                ->sum('amount') ?? 0;

            $manualOut = \App\Models\DrawerTransaction::where('day_closing_id', $activeShift->id)
                ->where('type', 'out')
                ->sum('amount') ?? 0;
            
            $activeShift->inflow_amount = $inflow;
            $activeShift->outflow_amount = $outflow;
            $activeShift->manual_in = $manualIn + $returnsIn;
            $activeShift->manual_out = $manualOut;
            $activeShift->expected_balance = $activeShift->opening_balance + $inflow - $outflow + ($manualIn + $returnsIn) - $manualOut;

            // Fetch drawer logs for active shift (either logged in it or returned in it)
            $drawerLogs = \App\Models\DrawerTransaction::where('day_closing_id', $activeShift->id)
                ->orWhere('returned_in_closing_id', $activeShift->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $drawerLogs = collect();
        }

        // Pending returns to show on the opening screen
        $pendingReturns = \App\Models\DrawerTransaction::where('status', 'pending')
            ->where('type', 'out')
            ->where('category', 'temporary_market')
            ->get();

        $closings = \App\Models\DayClosing::where('status', 'closed')->orderBy('closed_at', 'desc')->get();

        return view('admin_panel.checkbook.index', compact(
            'activeShift',
            'closings',
            'drawerLogs',
            'pendingReturns'
        ));
    }

    public function transactions(Request $request)
    {
        // Default to today if no filter is active
        if (!$request->filled('from_date') && !$request->filled('to_date') && !$request->filled('period') && !$request->filled('day_closing_id')) {
            $request->merge(['period' => 'day']);
        }

        // 1. Get all Cash and Bank Accounts
        $cashAndBankHeads = AccountHead::whereIn('name', ['Cash', 'Bank'])->pluck('id');
        $validAccountIds = Account::whereIn('head_id', $cashAndBankHeads)->where('status', 1)->pluck('id');
        $accounts = Account::whereIn('id', $validAccountIds)->get();

        // 2. Fetch all Day Closings (shifts) to populate filter dropdown
        $closings = \App\Models\DayClosing::orderBy('opened_at', 'desc')->get();

        // 3. Build Query for Journal Entries
        $query = JournalEntry::with(['account', 'source'])->whereIn('account_id', $validAccountIds);

        // Day/Shift Closing Filter
        if ($request->filled('day_closing_id')) {
            $shift = \App\Models\DayClosing::find($request->day_closing_id);
            if ($shift) {
                $query->whereBetween('created_at', [$shift->opened_at, $shift->closed_at ?? Carbon::now()]);
            }
        } else {
            // Date Filtering
            if ($request->filled('from_date')) {
                $query->whereDate('entry_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('entry_date', '<=', $request->to_date);
            }
            
            // Period filtering override
            if ($request->filled('period') && $request->period !== 'custom') {
                $now = Carbon::now();
                switch ($request->period) {
                    case 'day':
                        $query->whereDate('entry_date', $now->toDateString());
                        break;
                    case 'week':
                        $query->whereBetween('entry_date', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
                        break;
                    case 'month':
                        $query->whereMonth('entry_date', $now->month)->whereYear('entry_date', $now->year);
                        break;
                    case 'year':
                        $query->whereYear('entry_date', $now->year);
                        break;
                }
            }
        }

        // Account Filter
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // Transaction Type Filter (In / Out)
        if ($request->filled('type')) {
            if ($request->type === 'in') {
                $query->where('debit', '>', 0);
            } elseif ($request->type === 'out') {
                $query->where('credit', '>', 0);
            }
        }

        // Order by date and ID
        $query->orderBy('entry_date', 'asc')->orderBy('id', 'asc');

        // Execute Query
        $transactions = $query->get();

        // Calculate Summaries and Running Balances
        $totalIn = 0;
        $totalOut = 0;
        $runningBalance = 0;

        foreach ($transactions as $transaction) {
            $totalIn += $transaction->debit;
            $totalOut += $transaction->credit;
            
            // Debit increases Cash/Bank (Asset), Credit decreases
            $runningBalance += ($transaction->debit - $transaction->credit);
            $transaction->running_balance = $runningBalance;
        }

        $netBalance = $totalIn - $totalOut;

        // Cash in Hand (Just accounts under "Cash" head) filtered by the selected date/period
        $cashHeadIds = AccountHead::where('name', 'Cash')->pluck('id');
        $cashAccountIds = Account::whereIn('head_id', $cashHeadIds)->where('status', 1)->pluck('id');
        
        $cashInHandQuery = JournalEntry::whereIn('account_id', $cashAccountIds);
        
        if ($request->filled('day_closing_id')) {
            $shift = \App\Models\DayClosing::find($request->day_closing_id);
            if ($shift) {
                $cashInHandQuery->whereBetween('created_at', [$shift->opened_at, $shift->closed_at ?? Carbon::now()]);
            }
        } else {
            if ($request->filled('from_date')) {
                $cashInHandQuery->whereDate('entry_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $cashInHandQuery->whereDate('entry_date', '<=', $request->to_date);
            }
            if ($request->filled('period') && $request->period !== 'custom') {
                $now = Carbon::now();
                switch ($request->period) {
                    case 'day':
                        $cashInHandQuery->whereDate('entry_date', $now->toDateString());
                        break;
                    case 'week':
                        $cashInHandQuery->whereBetween('entry_date', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
                        break;
                    case 'month':
                        $cashInHandQuery->whereMonth('entry_date', $now->month)->whereYear('entry_date', $now->year);
                        break;
                    case 'year':
                        $cashInHandQuery->whereYear('entry_date', $now->year);
                        break;
                }
            }
        }
        $cashInHand = $cashInHandQuery->selectRaw('SUM(debit) - SUM(credit) as balance')
                        ->value('balance') ?? 0;

        // If it's an AJAX request, return the partial view
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin_panel.checkbook.partials.table_body', compact('transactions'))->render(),
                'summary' => [
                    'totalIn' => number_format($totalIn, 2),
                    'totalOut' => number_format($totalOut, 2),
                    'netBalance' => number_format($netBalance, 2),
                    'cashInHand' => number_format($cashInHand, 2),
                ]
            ]);
        }

        return view('admin_panel.checkbook.transactions', compact(
            'transactions', 
            'accounts', 
            'totalIn', 
            'totalOut', 
            'netBalance', 
            'cashInHand',
            'closings'
        ));
    }

    public function openDay(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'return_ids' => 'nullable|array',
            'return_ids.*' => 'integer|exists:drawer_transactions,id',
        ]);

        $exists = \App\Models\DayClosing::where('status', 'open')->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'A day/shift is already open.');
        }

        // Calculate total returns to add to opening balance
        $addedOpening = 0;
        if ($request->filled('return_ids')) {
            $addedOpening = \App\Models\DrawerTransaction::whereIn('id', $request->return_ids)
                ->where('status', 'pending')
                ->sum('amount') ?? 0;
        }

        $openingBalance = $request->opening_balance + $addedOpening;

        $activeShift = \App\Models\DayClosing::create([
            'opening_balance' => $openingBalance,
            'opened_at' => Carbon::now(),
            'status' => 'open',
        ]);

        // Mark checked returns as returned in this new shift
        if ($request->filled('return_ids')) {
            \App\Models\DrawerTransaction::whereIn('id', $request->return_ids)
                ->update([
                    'status' => 'returned',
                    'returned_in_closing_id' => $activeShift->id,
                ]);
        }

        return redirect()->back()->with('success', 'Day/Shift opened successfully.' . ($addedOpening > 0 ? ' Added ' . number_format($addedOpening, 2) . ' from pending returns.' : ''));
    }

    public function closeDay(Request $request)
    {
        $request->validate([
            'actual_balance' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $activeShift = \App\Models\DayClosing::where('status', 'open')->first();
        if (!$activeShift) {
            return redirect()->back()->with('error', 'No active day/shift found to close.');
        }

        // Get Cash/Bank accounts
        $cashAndBankHeads = AccountHead::whereIn('name', ['Cash', 'Bank'])->pluck('id');
        $validAccountIds = Account::whereIn('head_id', $cashAndBankHeads)->where('status', 1)->pluck('id');

        $inflow = JournalEntry::whereIn('account_id', $validAccountIds)
            ->where('created_at', '>=', $activeShift->opened_at)
            ->sum('debit') ?? 0;
        
        $outflow = JournalEntry::whereIn('account_id', $validAccountIds)
            ->where('created_at', '>=', $activeShift->opened_at)
            ->sum('credit') ?? 0;

        // Manual drawer logs
        $manualIn = \App\Models\DrawerTransaction::where('day_closing_id', $activeShift->id)
            ->where('type', 'in')
            ->sum('amount') ?? 0;
        
        $returnsIn = \App\Models\DrawerTransaction::where('returned_in_closing_id', $activeShift->id)
            ->sum('amount') ?? 0;

        $manualOut = \App\Models\DrawerTransaction::where('day_closing_id', $activeShift->id)
            ->where('type', 'out')
            ->sum('amount') ?? 0;

        // Expected final balance
        $expected = $activeShift->opening_balance + $inflow - $outflow + ($manualIn + $returnsIn) - $manualOut;
        $actual = $request->actual_balance;
        $difference = $actual - $expected;

        $activeShift->update([
            'inflow_amount' => $inflow + $manualIn + $returnsIn, // Sum total inflow
            'outflow_amount' => $outflow + $manualOut,           // Sum total outflow
            'expected_balance' => $expected,
            'actual_balance' => $actual,
            'difference' => $difference,
            'closed_at' => Carbon::now(),
            'status' => 'closed',
            'remarks' => $request->remarks
        ]);

        return redirect()->back()->with('success', 'Day/Shift closed successfully and history saved.');
    }

    public function storeDrawerTransaction(Request $request)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'category' => 'required|in:expense,temporary_market,owner_withdrawal,other',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $activeShift = \App\Models\DayClosing::where('status', 'open')->first();
        if (!$activeShift) {
            return redirect()->back()->with('error', 'No active day/shift open.');
        }

        \App\Models\DrawerTransaction::create([
            'day_closing_id' => $activeShift->id,
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => ($request->category === 'temporary_market' && $request->type === 'out') ? 'pending' : 'settled'
        ]);

        return redirect()->back()->with('success', 'Drawer transaction logged successfully.');
    }

    public function returnDrawerTransaction(Request $request, $id)
    {
        $activeShift = \App\Models\DayClosing::where('status', 'open')->first();
        if (!$activeShift) {
            return redirect()->back()->with('error', 'No active day/shift to return cash to.');
        }

        $tx = \App\Models\DrawerTransaction::findOrFail($id);
        if ($tx->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaction is already returned or settled.');
        }

        $tx->update([
            'status' => 'returned',
            'returned_in_closing_id' => $activeShift->id
        ]);

        return redirect()->back()->with('success', 'Market withdrawal returned to current active shift successfully.');
    }
}
