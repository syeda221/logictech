<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHead;
use App\Models\ExpenseCategory;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\ExpenseVoucher;
use App\Models\Narration;
use App\Models\PaymentVoucher;
use App\Models\ReceiptsVoucher;
use App\Models\Vendor;
use App\Models\VendorLedger;
use App\Models\VoucherMaster;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index($type)
    {

        // Sirf selected type ka data laa lo
        $vouchers = Voucher::where('voucher_type', $type)->latest()->get();
        $narration = Narration::where('expense_head', $type)->get();

        return view('admin_panel.accounts.expenses', [
            'vouchers' => $vouchers,
            'type' => $type,
            'narration' => $narration,
        ]);
    }

    public function store(Request $request)
    {
        // Validate that arrays are present and match in length
        $request->validate([
            'date' => 'required',
            'type' => 'required',
            'person' => 'required',
            'narration' => 'required',
            'amount' => 'required',
        ]);

        // Loop through each row and create a voucher
        foreach ($request->date as $index => $date) {
            Voucher::create([
                'voucher_type' => $request->sub_head,
                'sales_officer' => auth()->user()->name,
                'date' => $date,
                'type' => $request->type[$index],
                'person' => $request->person[$index],
                'sub_head' => $request->sub_head[$index] ?? null,
                'narration' => $request->narration[$index],
                'amount' => $request->amount[$index],
                'status' => 'draft',
            ]);
        }

        return back()->with('success', 'Vouchers saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Voucher $voucher)
    {
        //
    }

    public function receipt($id)
    {
        $voucher = Voucher::findOrFail($id);

        $customerName = $voucher->person; // Default
        $customerAddress = '-';
        $closingBalance = 0;

        // yahan accounts bhi show karwayn all heads
        // bank cash
        if ($voucher->type === 'Main Customer' && $voucher->mainCustomer) {
            $customerName = $voucher->mainCustomer->customer_name;
            $customerAddress = $voucher->mainCustomer->address;
            $closingBalance = $voucher->mainCustomer->closing_balance;
        } elseif ($voucher->type === 'Sub Customer' && $voucher->subCustomer) {
            $customerName = $voucher->subCustomer->customer_name;
            $customerAddress = $voucher->subCustomer->address;
            $closingBalance = $voucher->subCustomer->closing_balance;
        }

        return view('admin_panel.accounts.receipt', compact('voucher', 'customerName', 'customerAddress', 'closingBalance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voucher $voucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voucher $voucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        //
    }

    public function all_recepit_vochers()
    {
        // V2: Fetch Receipt Vouchers only
        $receipts = \App\Models\VoucherMaster::where('voucher_type', \App\Models\VoucherMaster::TYPE_RECEIPT)
            ->with('party') // Eager load the polymorphic party
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($receipts as $voucher) {
            $typeLabel = '-';
            $partyName = '-';

            if ($voucher->party) {
                // Determine Label from Class Name
                $class = get_class($voucher->party);
                if (str_contains($class, 'Customer')) {
                    $typeLabel = 'Customer';
                    $partyName = $voucher->party->customer_name ?? $voucher->party->name ?? '-';
                } elseif (str_contains($class, 'Vendor')) {
                    $typeLabel = 'Vendor';
                    $partyName = $voucher->party->name ?? '-';
                } elseif (str_contains($class, 'Account')) {
                    $typeLabel = 'Account';
                    $partyName = $voucher->party->title ?? '-';
                } else {
                    $typeLabel = class_basename($class);
                    $partyName = $voucher->party->name ?? '-';
                }
            }

            // Attach for View
            $voucher->type_label = $typeLabel;
            $voucher->party_name = $partyName;

            // Map old fields to new fields for View compatibility (or update View)
            // View uses: rvid, receipt_date, entry_date
            $voucher->rvid = $voucher->voucher_no;
            $voucher->receipt_date = $voucher->date->format('Y-m-d');
            $voucher->entry_date = $voucher->created_at->format('Y-m-d');

            // Fix: Map total_amount to amount for View compatibility
            if (! isset($voucher->amount)) {
                $voucher->amount = $voucher->total_amount;
            }
        }

        return view('admin_panel.vochers.all_recepit_vochers', compact('receipts'));
    }

    public function print($id)
    {
        \Log::info('Print Voucher Requested. ID: '.$id);

        // 1. Try V2 VoucherMaster First
        $voucherV2 = \App\Models\VoucherMaster::find($id);

        if ($voucherV2) {
            \Log::info('Found V2 Voucher: '.$voucherV2->voucher_no);

            // Lazy load relationships to avoid eager loading weirdness
            $voucherV2->load(['details.account', 'party']);

            // -- Adapter for V2 to V1 View --
            $voucher = (object) [
                'rvid' => $voucherV2->voucher_no,
                'receipt_date' => $voucherV2->date->format('Y-m-d'),
                'total_amount' => $voucherV2->amount,
                'remarks' => $voucherV2->remarks,
                'type' => 'unknown', // Default
            ];

            if (! isset($voucher->total_amount)) {
                $voucher->total_amount = $voucherV2->total_amount;
            }

            $rows = [];
            foreach ($voucherV2->details as $detail) {
                // Receipt Voucher: Only show DEBIT rows (Cash/Bank received into)
                // Skip Credit rows (Accounts Receivable / AR) — they are the accounting offset, not useful for print
                if ($voucherV2->voucher_type === 'receipt' && $detail->credit > 0) {
                    continue; // Skip Cr side (AR)
                }

                $accTitle  = $detail->account->title ?? '-';
                $accCode   = $detail->account->account_code ?? '-';
                $headName  = $detail->account->accountHead->name ?? '-';

                // Clean label: "Hand Cash" not "Hand Cash (Receipt) [Code: ACC-0004]"
                $rows[] = [
                    'narration'    => $detail->narration,
                    'reference'    => '-',
                    'account_head' => $headName,
                    'account_name' => $accTitle,
                    'account_code' => $accCode,
                    'amount'       => $detail->debit > 0 ? $detail->debit : $detail->credit,
                ];
            }

            // Party Logic
            $party = $voucherV2->party;
            $previousBalance = 0;

            if ($party) {
                if ($party instanceof \App\Models\Customer) {
                    $voucher->type = ($party->customer_type == 'Walking Customer') ? 'walkin' : 'customer';

                    // Ensure fields expected by view exist
                    $party->name = $party->customer_name; // Fallback
                    $party->address = $party->address ?? '-';
                    $party->mobile = $party->mobile ?? '-';

                    $previousBalance = \App\Models\CustomerLedger::where('customer_id', $party->id)
                        ->where('created_at', '<', $voucherV2->created_at)
                        ->orderBy('id', 'desc')
                        ->value('closing_balance') ?? ($party->opening_balance ?? 0);

                } elseif ($party instanceof \App\Models\Vendor) {
                    $voucher->type = 'vendor';
                    $party->address = $party->address ?? '-';
                    $party->phone = $party->phone ?? '-'; // View uses phone

                    $previousBalance = \App\Models\VendorLedger::where('vendor_id', $party->id)
                        ->where('created_at', '<', $voucherV2->created_at)
                        ->orderBy('id', 'desc')
                        ->value('closing_balance') ?? ($party->opening_balance ?? 0);

                } elseif ($party instanceof \App\Models\Account) {
                    $voucher->type = '1'; // Numeric triggers Account Block
                    $party->name = $party->title;
                    $party->phone = $party->account_code;
                    $party->head_name = $party->accountHead->name ?? 'Account';

                    $previousBalance = $party->opening_balance;
                }
            } else {
                $previousBalance = 0;
            }

            return view('admin_panel.vochers.print', compact('voucher', 'rows', 'party', 'previousBalance'));
        }

        // 2. Fallback to V1 Legacy (Original Code)
        $voucher = ReceiptsVoucher::findOrFail($id);

        // Decode JSON arrays
        $narrations = json_decode($voucher->narration_id, true) ?? [];
        $references = json_decode($voucher->reference_no, true) ?? [];
        $accountHeads = json_decode($voucher->row_account_head, true) ?? [];
        $accounts = json_decode($voucher->row_account_id, true) ?? [];
        $amounts = json_decode($voucher->amount, true) ?? [];

        // Rows build
        $rows = [];
        foreach ($narrations as $index => $narrId) {
            $narration = DB::table('narrations')->where('id', $narrId)->value('narration');
            $ref = $references[$index] ?? null;
            $accountHead = DB::table('account_heads')->where('id', $accountHeads[$index] ?? null)->value('name');
            $account = DB::table('accounts')->where('id', $accounts[$index] ?? null)->first();
            $amount = (float) ($amounts[$index] ?? 0);

            $rows[] = [
                'narration' => $narration,
                'reference' => $ref,
                'account_head' => $accountHead,
                'account_name' => $account->title ?? null,
                'account_code' => $account->account_code ?? null,
                'amount' => $amount,
            ];
        }

        // 🧩 Party setup — dynamic based on type
        $party = null;
        $previousBalance = 0;

        // ✅ If type is numeric → means from Account Head
        if (is_numeric($voucher->type)) {
            $accountHead = DB::table('account_heads')->where('id', $voucher->type)->first();
            $account = DB::table('accounts')->where('id', $voucher->party_id)->first();

            if ($account) {
                $party = (object) [
                    'name' => $account->title ?? '—',
                    'address' => '—',
                    'phone' => $account->account_code ?? '—',
                    'head_name' => $accountHead->name ?? '—',
                ];
            }

            // ✅ If vendor
        } elseif ($voucher->type === 'vendor') {
            $party = DB::table('vendors')->where('id', $voucher->party_id)->first();
            $previousBalance = DB::table('vendor_ledgers')
                ->where('vendor_id', $voucher->party_id)
                ->orderByDesc('id')
                ->value('closing_balance') ?? 0;

            // ✅ If customer
        } elseif ($voucher->type === 'customer') {
            $party = DB::table('customers')->where('id', $voucher->party_id)->first();
            $previousBalance = DB::table('customer_ledgers')
                ->where('customer_id', $voucher->party_id)
                ->orderByDesc('id')
                ->value('closing_balance') ?? 0;

            // ✅ If walkin
        } elseif ($voucher->type === 'walkin') {
            $party = DB::table('customers')
                ->where('id', $voucher->party_id)
                ->where('customer_type', 'Walking Customer')
                ->first();
        }

        return view('admin_panel.vochers.print', compact('voucher', 'rows', 'party', 'previousBalance'));
    }

    public function getAccountsByHead($headId)
    {
        try {
            $query = DB::table('accounts');

            if (is_numeric($headId)) {
                $query->where('head_id', $headId);
            } else {
                // Find head ID by head name (e.g. 'cash', 'bank', etc.)
                $head = DB::table('account_heads')
                    ->where('name', 'LIKE', $headId)
                    ->orWhere('name', 'LIKE', '%' . $headId . '%')
                    ->first();

                if ($head) {
                    $query->where('head_id', $head->id);
                } else {
                    $query->where('head_id', $headId);
                }
            }

            // Exclude system control accounts from voucher selection
            $excludedCodes = ['AR', 'AP', 'SALES', 'PURCHASE'];
            $accounts = $query->where(function ($q) {
                    $q->where('status', 1)->orWhereNull('status');
                })
                ->whereNotIn('account_code', $excludedCodes)
                ->orderBy('title', 'asc')
                ->get();

            return response()->json($accounts);
        } catch (\Throwable $e) {
            \Log::error('getAccountsByHead Error: ' . $e->getMessage());

            return response()->json([], 200);
        }
    }

    public function getOpeningBalance($type, $id)
    {
        try {
            if ($type === 'customer' || $type === 'walkin') {
                $customer = Customer::find($id);

                return response()->json([
                    'opening_balance' => $customer->opening_balance ?? 0,
                ]);
            }

            // Account case: check accounts table first, then account_heads fallback
            $account = DB::table('accounts')->where('id', $id)->first();
            if (! $account) {
                $account = DB::table('account_heads')->where('id', $id)->first();
            }

            return response()->json([
                'opening_balance' => $account->opening_balance ?? 0,
            ]);
        } catch (\Throwable $e) {
            \Log::error('getOpeningBalance Error: ' . $e->getMessage());

            return response()->json(['opening_balance' => 0], 200);
        }
    }

    /**
     * AJAX: Get all outstanding (Due) invoices for a customer
     * Used in Receipt Voucher form for invoice-linking
     */
    public function getCustomerDueInvoices($id)
    {
        $due = [];

        // 1. Sales Invoices
        $sales = \App\Models\Sale::where('customer_id', $id)
            ->where('sale_status', 'posted')
            ->orderBy('created_at', 'asc') // oldest first (FIFO)
            ->get(['id', 'invoice_no', 'total_net', 'cash', 'created_at']);

        foreach ($sales as $sale) {
            $paid    = (float) $sale->cash;
            $net     = (float) $sale->total_net;
            $remaining = round($net - $paid, 2);
            if ($remaining > 0) {
                $due[] = [
                    'id'          => $sale->id,
                    'type'        => 'sale',
                    'type_label'  => 'Order',
                    'invoice_no'  => $sale->invoice_no,
                    'total_net'   => $net,
                    'paid'        => $paid,
                    'remaining'   => $remaining,
                    'date'        => $sale->created_at ? $sale->created_at->format('d M Y') : '-',
                    'raw_date'    => $sale->created_at ? $sale->created_at->toDateTimeString() : '1970-01-01',
                ];
            }
        }

        // 2. Repair Orders Invoices
        if (\Illuminate\Support\Facades\Schema::hasTable('repair_orders')) {
            $repairs = \App\Models\RepairOrder::where('customer_id', $id)
                ->where('status', '!=', 'cancelled')
                ->orderBy('received_date', 'asc')
                ->get();

            foreach ($repairs as $repair) {
                $totalCharges = (float)$repair->total_charges > 0 ? (float)$repair->total_charges : (float)$repair->estimated_cost;
                $paid = (float)$repair->advance_paid + (float)$repair->final_paid;
                $remaining = round(max(0, $totalCharges - $paid), 2);

                if ($remaining > 0) {
                    $itemDesc = $repair->item_name ? " ({$repair->item_name})" : '';
                    $date = $repair->received_date ? $repair->received_date->format('d M Y') : ($repair->created_at ? $repair->created_at->format('d M Y') : '-');
                    $rawDate = $repair->received_date ? $repair->received_date->toDateTimeString() : ($repair->created_at ? $repair->created_at->toDateTimeString() : '1970-01-01');

                    $due[] = [
                        'id'          => $repair->id,
                        'type'        => 'repair',
                        'type_label'  => 'Repair',
                        'invoice_no'  => $repair->repair_no . $itemDesc,
                        'total_net'   => $totalCharges,
                        'paid'        => $paid,
                        'remaining'   => $remaining,
                        'date'        => $date,
                        'raw_date'    => $rawDate,
                    ];
                }
            }
        }

        usort($due, function($a, $b) {
            return strcmp($a['raw_date'] ?? '', $b['raw_date'] ?? '');
        });

        return response()->json($due);
    }

    public function recepit_vochers()
    {
        $narrations = \App\Models\Narration::where('expense_head', 'Receipts Voucher')
            ->pluck('narration', 'id');
        $AccountHeads = AccountHead::whereRaw('LOWER(TRIM(name)) IN ("cash", "bank")')->get();

        // Last RVID nikalna
        $lastVoucher = \App\Models\ReceiptsVoucher::latest('id')->first();

        // Next ID generate karna
        $nextId = $lastVoucher ? $lastVoucher->id + 1 : 1;
        $nextRvid = 'RVID-'.str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('admin_panel.vochers.reciepts_vouchers', compact('narrations', 'AccountHeads', 'nextRvid'));
    }


    public function store_rec_vochers(Request $request)
    {
        DB::beginTransaction();
        try {
            $rvid = $request->rvid ?: \App\Models\ReceiptsVoucher::generateRVID(auth()->id());
            $narrationIds = [];

            foreach ($request->narration_id as $index => $narrId) {
                $manualText = $request->narration_text[$index] ?? null;
                $manualType = $request->narration_type_text[$index] ?? 'Manual';

                if (empty($narrId) && ! empty($manualText)) {
                    $expenseHead = 'Receipts Voucher';
                    $new = \App\Models\Narration::create([
                        'expense_head' => $expenseHead,
                        'narration'    => $manualText,
                    ]);
                    $narrationIds[] = (string) $new->id;
                } else {
                    $narrationIds[] = (string) $narrId;
                }
            }

            $voucherData = [
                'rvid'             => $rvid,
                'receipt_date'     => $request->receipt_date,
                'entry_date'       => $request->entry_date,
                'type'             => $request->vendor_type,
                'party_id'         => $request->vendor_id,
                'tel'              => $request->tel,
                'remarks'          => $request->remarks,
                'narration_id'     => json_encode($narrationIds),
                'reference_no'     => json_encode($request->reference_no),
                'row_account_head' => json_encode($request->row_account_head),
                'row_account_id'   => json_encode($request->row_account_id),
                'discount_value'   => json_encode($request->discount_value),
                'rate'             => json_encode($request->rate),
                'amount'           => json_encode($request->amount),
                'total_amount'     => $request->total_amount,
                'processed'        => true,
            ];

            $rec = ReceiptsVoucher::create($voucherData);

            // ─────────────────────────────────────────────────────────────
            // INVOICE SETTLEMENT LOGIC (ERP-style)
            // ─────────────────────────────────────────────────────────────
            $vType      = strtolower($request->vendor_type);
            $customerId = (int) $request->vendor_id;
            $totalAmt   = (float) $request->total_amount;
            $settledSaleIds = [];

            if (($vType === 'customer' || $vType === 'walkin') && $customerId > 0 && $totalAmt > 0) {

                $linkedSaleIds     = array_filter((array)($request->input('linked_sale_ids', [])));
                $linkedSaleTypes   = (array)($request->input('linked_sale_types', []));
                $linkedSaleAmounts = (array)($request->input('linked_sale_amounts', []));

                if (!empty($linkedSaleIds)) {
                    // ─── MODE A: User ne manually invoice select ki ───
                    $remaining = $totalAmt;
                    foreach ($linkedSaleIds as $idx => $itemId) {
                        if ($remaining <= 0) break;
                        $itemType = $linkedSaleTypes[$idx] ?? 'sale';

                        if ($itemType === 'repair') {
                            $repair = \App\Models\RepairOrder::find($itemId);
                            if (!$repair || $repair->customer_id != $customerId || $repair->status === 'cancelled') continue;

                            $totalCost = (float)$repair->total_charges > 0 ? (float)$repair->total_charges : (float)$repair->estimated_cost;
                            $paid = (float)$repair->advance_paid + (float)$repair->final_paid;
                            $invoiceDue = round(max(0, $totalCost - $paid), 2);
                            if ($invoiceDue <= 0) continue;

                            $applyAmt = isset($linkedSaleAmounts[$idx]) && (float)$linkedSaleAmounts[$idx] > 0
                                ? min((float)$linkedSaleAmounts[$idx], $invoiceDue, $remaining)
                                : min($invoiceDue, $remaining);

                            $repair->final_paid = (float)$repair->final_paid + $applyAmt;
                            $repair->due_amount = max(0, $totalCost - ($repair->advance_paid + $repair->final_paid));
                            $repair->save();

                            if (class_exists(\App\Models\RepairOrderLog::class)) {
                                \App\Models\RepairOrderLog::create([
                                    'repair_order_id' => $repair->id,
                                    'user_id'         => auth()->id(),
                                    'action'          => 'payment_received',
                                    'amount'          => $applyAmt,
                                    'notes'           => "Payment of Rs. " . number_format($applyAmt, 2) . " received via Receipt Voucher {$rvid}",
                                ]);
                            }

                            $remaining -= $applyAmt;
                            $settledSaleIds[] = [
                                'type'           => 'repair',
                                'sale_id'        => $repair->id,
                                'invoice_no'     => $repair->repair_no,
                                'amount_applied' => $applyAmt
                            ];
                        } else {
                            $sale = \App\Models\Sale::find($itemId);
                            if (!$sale || $sale->customer_id != $customerId || $sale->sale_status !== 'posted') continue;

                            $invoiceDue = round((float)$sale->total_net - (float)$sale->cash, 2);
                            if ($invoiceDue <= 0) continue;

                            $applyAmt = isset($linkedSaleAmounts[$idx]) && (float)$linkedSaleAmounts[$idx] > 0
                                ? min((float)$linkedSaleAmounts[$idx], $invoiceDue, $remaining)
                                : min($invoiceDue, $remaining);

                            $sale->cash += $applyAmt;
                            $sale->change = $sale->cash - $sale->total_net;
                            $sale->save();

                            $remaining -= $applyAmt;
                            $settledSaleIds[] = [
                                'type'           => 'sale',
                                'sale_id'        => $sale->id,
                                'invoice_no'     => $sale->invoice_no,
                                'amount_applied' => $applyAmt
                            ];
                        }
                    }

                } else {
                    // ─── MODE B: Koi invoice select nahi → FIFO auto-allocation across sales & repairs ───
                    $allDue = [];

                    $sales = \App\Models\Sale::where('customer_id', $customerId)
                        ->where('sale_status', 'posted')
                        ->get(['id', 'invoice_no', 'total_net', 'cash', 'created_at']);
                    foreach ($sales as $s) {
                        $due = round((float)$s->total_net - (float)$s->cash, 2);
                        if ($due > 0) {
                            $allDue[] = [
                                'type'  => 'sale',
                                'model' => $s,
                                'due'   => $due,
                                'date'  => $s->created_at ? $s->created_at->toDateTimeString() : '1970-01-01',
                            ];
                        }
                    }

                    if (\Illuminate\Support\Facades\Schema::hasTable('repair_orders')) {
                        $repairs = \App\Models\RepairOrder::where('customer_id', $customerId)
                            ->where('status', '!=', 'cancelled')
                            ->get();
                        foreach ($repairs as $r) {
                            $tot = (float)$r->total_charges > 0 ? (float)$r->total_charges : (float)$r->estimated_cost;
                            $paid = (float)$r->advance_paid + (float)$r->final_paid;
                            $due = round(max(0, $tot - $paid), 2);
                            if ($due > 0) {
                                $allDue[] = [
                                    'type'  => 'repair',
                                    'model' => $r,
                                    'due'   => $due,
                                    'date'  => $r->received_date ? $r->received_date->toDateTimeString() : ($r->created_at ? $r->created_at->toDateTimeString() : '1970-01-01'),
                                ];
                            }
                        }
                    }

                    usort($allDue, function($a, $b) {
                        return strcmp($a['date'], $b['date']);
                    });

                    $remaining = $totalAmt;
                    foreach ($allDue as $item) {
                        if ($remaining <= 0) break;
                        $applyAmt = min($item['due'], $remaining);

                        if ($item['type'] === 'repair') {
                            $repair = $item['model'];
                            $repair->final_paid = (float)$repair->final_paid + $applyAmt;
                            $tot = (float)$repair->total_charges > 0 ? (float)$repair->total_charges : (float)$repair->estimated_cost;
                            $repair->due_amount = max(0, $tot - ($repair->advance_paid + $repair->final_paid));
                            $repair->save();

                            if (class_exists(\App\Models\RepairOrderLog::class)) {
                                \App\Models\RepairOrderLog::create([
                                    'repair_order_id' => $repair->id,
                                    'user_id'         => auth()->id(),
                                    'action'          => 'payment_received',
                                    'amount'          => $applyAmt,
                                    'notes'           => "Payment of Rs. " . number_format($applyAmt, 2) . " received via Receipt Voucher {$rvid} (Auto-FIFO)",
                                ]);
                            }

                            $settledSaleIds[] = [
                                'type'           => 'repair',
                                'sale_id'        => $repair->id,
                                'invoice_no'     => $repair->repair_no,
                                'amount_applied' => $applyAmt
                            ];
                        } else {
                            $sale = $item['model'];
                            $sale->cash += $applyAmt;
                            $sale->change = $sale->cash - $sale->total_net;
                            $sale->save();

                            $settledSaleIds[] = [
                                'type'           => 'sale',
                                'sale_id'        => $sale->id,
                                'invoice_no'     => $sale->invoice_no,
                                'amount_applied' => $applyAmt
                            ];
                        }
                        $remaining -= $applyAmt;
                    }
                }

                $rec->update(['settled_invoices' => json_encode($settledSaleIds)]);
                \Log::info('Invoice Settlement Done. Voucher: '.$rvid.', Settled: '.json_encode($settledSaleIds));
            }
            // ─────────────────────────────────────────────────────────────

            // ✅ V2 VOUCHER INTEGRATION
            try {
                \Log::info('V2 Integration Start. Type: '.$request->vendor_type.', ID: '.$request->vendor_id);

                $partyType       = null;
                $creditAccountId = null;
                $balanceService  = app(\App\Services\BalanceService::class);

                if ($vType == 'customer' || $vType == 'walkin') {
                    $partyType       = \App\Models\Customer::class;
                    $creditAccountId = $balanceService->getAccountsReceivableId();
                } elseif ($vType == 'vendor') {
                    $partyType       = \App\Models\Vendor::class;
                    $creditAccountId = $balanceService->getAccountsPayableId();
                } else {
                    $partyType       = \App\Models\Account::class;
                    $creditAccountId = $request->vendor_id;
                }

                if ($creditAccountId) {
                    $v2Lines = [];
                    // DEBIT SIDE (Cash/Bank)
                    if ($request->row_account_id && $request->amount) {
                        foreach ($request->row_account_id as $idx => $accId) {
                            $amt = (float) ($request->amount[$idx] ?? 0);
                            if ($amt > 0) {
                                $v2Lines[] = [
                                    'account_id' => $accId,
                                    'debit'      => $amt,
                                    'credit'     => 0,
                                    'narration'  => $request->narration_text[$idx] ?? 'Receipt',
                                ];
                            }
                        }
                    }

                    // CREDIT SIDE (AR)
                    if ($totalAmt > 0) {
                        $v2Lines[] = [
                            'account_id' => $creditAccountId,
                            'debit'      => 0,
                            'credit'     => $totalAmt,
                            'narration'  => 'Receipt from '.$request->vendor_type,
                        ];
                    }

                    if (! empty($v2Lines)) {
                        // Build remarks with settled invoice info
                        $remarksNote = $request->remarks . " (Ref: $rvid)";
                        if (!empty($settledSaleIds)) {
                            $invoiceNos = implode(', ', array_column($settledSaleIds, 'invoice_no'));
                            $remarksNote .= " | Invoices: $invoiceNos";
                        }

                        app(\App\Services\VoucherService::class)->createVoucher([
                            'voucher_type' => 'receipt',
                            'date'         => $request->receipt_date,
                            'status'       => 'posted',
                            'party_type'   => $partyType,
                            'party_id'     => $request->vendor_id,
                            'remarks'      => $remarksNote,
                        ], $v2Lines, auth()->id());

                        \Log::info('V2 Voucher Created Successfully.');

                        // ✅ Update CustomerLedger
                        if (($vType == 'customer' || $vType == 'walkin') && $totalAmt > 0) {
                            $latestLedger = CustomerLedger::where('customer_id', $request->vendor_id)->latest()->first();
                            $prevBal = $latestLedger ? $latestLedger->closing_balance : (
                                \App\Models\Customer::find($request->vendor_id)->opening_balance ?? 0
                            );
                            $ledgerDesc = 'Receipt Voucher '.$rvid;
                            if (!empty($settledSaleIds)) {
                                $invoiceNos = implode(', ', array_column($settledSaleIds, 'invoice_no'));
                                $ledgerDesc .= ' | ' . $invoiceNos;
                            }
                            CustomerLedger::create([
                                'customer_id'      => $request->vendor_id,
                                'admin_or_user_id' => auth()->id(),
                                'previous_balance' => $prevBal,
                                'opening_balance'  => 0,
                                'closing_balance'  => $prevBal - $totalAmt,
                                'description'      => $ledgerDesc,
                            ]);
                        }

                    } else {
                        \Log::warning('V2 Lines Empty. Total Amt: '.$totalAmt);
                    }
                } else {
                    \Log::warning("Credit Account ID missing for type: $vType");
                }
            } catch (\Exception $e) {
                \Log::error('V2 Sync Error: '.$e->getMessage());
            }

            DB::commit();

            return back()->with('success', 'Receipt Voucher saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show form for editing a Receipt Voucher
     */
    public function edit_rec_vochers($id)
    {
        // Smart finder: By VoucherMaster ID (since all_recepit_vochers passes $item->id), ReceiptsVoucher ID, or RVID
        $receiptVoucher = null;
        $vm = null;

        if (is_numeric($id)) {
            $vm = VoucherMaster::find($id);
            if (!$vm) {
                $receiptVoucher = ReceiptsVoucher::find($id);
            }
        } else {
            $receiptVoucher = ReceiptsVoucher::where('rvid', $id)->first();
            if (!$receiptVoucher) {
                $vm = VoucherMaster::where('voucher_no', $id)->first();
            }
        }

        if ($vm && !$receiptVoucher) {
            if (preg_match('/Ref:\s*(RVID-[\w\-]+)/', $vm->remarks, $m)) {
                $receiptVoucher = ReceiptsVoucher::where('rvid', $m[1])->first();
            }
            if (!$receiptVoucher) {
                $receiptVoucher = ReceiptsVoucher::where('rvid', $vm->voucher_no)->first();
            }
        }

        // If still no ReceiptsVoucher, but VoucherMaster exists, auto-mirror / create ReceiptsVoucher so it can be edited smoothly
        if (!$receiptVoucher && $vm) {
            $partyType = 'customer';
            if ($vm->party_type && (str_contains($vm->party_type, 'Vendor') || $vm->party_type === 'vendor')) {
                $partyType = 'vendor';
            } elseif ($vm->party_type && (str_contains($vm->party_type, 'Account') || is_numeric($vm->party_type))) {
                $partyType = (string)$vm->party_id;
            }

            $rowHeads = [];
            $rowAccs  = [];
            $amounts  = [];

            foreach ($vm->details as $d) {
                if ((float)$d->debit > 0) {
                    $acc = \App\Models\Account::find($d->account_id);
                    $rowHeads[] = $acc ? (string)$acc->head_id : '1';
                    $rowAccs[]  = (string)$d->account_id;
                    $amounts[]  = (string)$d->debit;
                }
            }

            if (empty($rowAccs)) {
                $defaultAcc = \App\Models\Account::where('head_id', 1)->first() ?? \App\Models\Account::first();
                $rowHeads[] = $defaultAcc ? (string)$defaultAcc->head_id : '1';
                $rowAccs[]  = $defaultAcc ? (string)$defaultAcc->id : '1';
                $amounts[]  = (string)$vm->total_amount;
            }

            // Parse settled invoices from remarks (e.g. "Advance Receipt for Sale Invoice #INV-0002" or "Invoices: INV-0002")
            $settledInvoices = [];
            if (preg_match('/(?:Sale Invoice|Invoice|Invoices)[:\s#]*([A-Za-z0-9\-_]+)/i', $vm->remarks, $invMatch)) {
                $invNo = trim($invMatch[1]);
                if (str_starts_with($invNo, 'REP')) {
                    $rep = \App\Models\RepairOrder::where('repair_no', $invNo)->first();
                    if ($rep) {
                        $settledInvoices[] = [
                            'type'           => 'repair',
                            'sale_id'        => $rep->id,
                            'invoice_no'     => $rep->repair_no,
                            'amount_applied' => (float)$vm->total_amount,
                        ];
                    }
                } else {
                    $sale = \App\Models\Sale::where('invoice_no', $invNo)->first();
                    if ($sale) {
                        $settledInvoices[] = [
                            'type'           => 'sale',
                            'sale_id'        => $sale->id,
                            'invoice_no'     => $sale->invoice_no,
                            'amount_applied' => (float)$vm->total_amount,
                        ];
                    }
                }
            }

            $receiptVoucher = ReceiptsVoucher::create([
                'rvid'             => $vm->voucher_no,
                'receipt_date'     => $vm->date ? $vm->date->format('Y-m-d') : now()->toDateString(),
                'entry_date'       => $vm->created_at ? $vm->created_at->format('Y-m-d') : now()->toDateString(),
                'type'             => $partyType,
                'party_id'         => $vm->party_id,
                'remarks'          => $vm->remarks,
                'row_account_head' => json_encode($rowHeads),
                'row_account_id'   => json_encode($rowAccs),
                'amount'           => json_encode($amounts),
                'total_amount'     => $vm->total_amount,
                'settled_invoices' => json_encode($settledInvoices),
                'processed'        => true,
            ]);
        }

        if (!$receiptVoucher) {
            $receiptVoucher = ReceiptsVoucher::where('rvid', $id)->firstOrFail();
        }

        $narrations = \App\Models\Narration::where('expense_head', 'Receipts Voucher')
            ->pluck('narration', 'id');
        $AccountHeads = AccountHead::whereRaw('LOWER(TRIM(name)) IN ("cash", "bank")')->get();

        // Decode rows
        $rowHeads = json_decode($receiptVoucher->row_account_head, true) ?: [];
        $rowAccs  = json_decode($receiptVoucher->row_account_id, true) ?: [];
        $amounts  = json_decode($receiptVoucher->amount, true) ?: [];
        $narrIds  = json_decode($receiptVoucher->narration_id, true) ?: [];
        $refNos   = json_decode($receiptVoucher->reference_no, true) ?: [];

        $rows = [];
        $max = max(count($rowHeads), count($rowAccs), count($amounts), 1);
        for ($i = 0; $i < $max; $i++) {
            $headId = $rowHeads[$i] ?? null;
            $accId  = $rowAccs[$i] ?? null;
            $amt    = $amounts[$i] ?? 0;

            $accounts = [];
            if ($headId) {
                $accounts = DB::table('accounts')->where('head_id', $headId)->where(function($q) {
                    $q->where('status', 1)->orWhereNull('status');
                })->whereNotIn('account_code', ['AR', 'AP', 'SALES', 'PURCHASE'])->orderBy('title', 'asc')->get();
            }

            $rows[] = [
                'head_id'      => $headId,
                'account_id'   => $accId,
                'accounts'     => $accounts,
                'amount'       => $amt,
                'narration_id' => $narrIds[$i] ?? '',
                'reference_no' => $refNos[$i] ?? '',
            ];
        }

        // Party details
        $partyType = $receiptVoucher->type;
        $partyId   = $receiptVoucher->party_id;
        $partyBalance = 0;
        $partyList = [];

        if (in_array($partyType, ['vendor', 'customer', 'walkin'])) {
            if ($partyType === 'customer' || $partyType === 'walkin') {
                $parties = Customer::all();
                foreach ($parties as $c) {
                    $bal = \App\Models\CustomerLedger::where('customer_id', $c->id)->latest()->value('closing_balance') ?? ($c->opening_balance ?? 0);
                    $partyList[] = [
                        'id'              => $c->id,
                        'text'            => $c->customer_name . " ({$bal})",
                        'mobile'          => $c->mobile ?? '',
                        'closing_balance' => $bal,
                    ];
                    if ($c->id == $partyId) {
                        $partyBalance = $bal;
                    }
                }
            } elseif ($partyType === 'vendor') {
                $vendors = Vendor::all();
                foreach ($vendors as $v) {
                    $bal = \App\Models\VendorLedger::where('vendor_id', $v->id)->latest()->value('closing_balance') ?? ($v->opening_balance ?? 0);
                    $partyList[] = [
                        'id'              => $v->id,
                        'text'            => $v->name . " ({$bal})",
                        'mobile'          => $v->phone ?? '',
                        'closing_balance' => $bal,
                    ];
                    if ($v->id == $partyId) {
                        $partyBalance = $bal;
                    }
                }
            }
        } else {
            // Account Head
            $accs = DB::table('accounts')->where('head_id', $partyType)->get();
            foreach ($accs as $a) {
                $partyList[] = [
                    'id'              => $a->id,
                    'text'            => $a->title,
                    'mobile'          => $a->account_code ?? '',
                    'closing_balance' => $a->current_balance ?? 0,
                ];
                if ($a->id == $partyId) {
                    $partyBalance = $a->current_balance ?? 0;
                }
            }
        }

                // Outstanding / Settled Invoices for Customer / Walkin
        $invoices = [];
        $appliedMap = [];

        $prevSettled = json_decode($receiptVoucher->settled_invoices, true) ?: [];
        if (empty($prevSettled)) {
            $vm = VoucherMaster::where('voucher_no', $receiptVoucher->rvid)
                ->orWhere('remarks', 'LIKE', "%Ref: {$receiptVoucher->rvid}%")
                ->first();
            $vmRemarks = $vm ? $vm->remarks : $receiptVoucher->remarks;
            if ($vmRemarks && preg_match('/Invoices:\s*([^\)]+)/', $vmRemarks, $m)) {
                $invNos = array_map('trim', explode(',', $m[1]));
                foreach ($invNos as $invNo) {
                    if (str_starts_with($invNo, 'REP')) {
                        $r = \App\Models\RepairOrder::where('repair_no', $invNo)->first();
                        if ($r) {
                            $prevSettled[] = [
                                'type'           => 'repair',
                                'sale_id'        => $r->id,
                                'invoice_no'     => $r->repair_no,
                                'amount_applied' => min((float)$r->final_paid, (float)$receiptVoucher->total_amount)
                            ];
                        }
                    } else {
                        $s = \App\Models\Sale::where('invoice_no', $invNo)->first();
                        if ($s) {
                            $prevSettled[] = [
                                'type'           => 'sale',
                                'sale_id'        => $s->id,
                                'invoice_no'     => $s->invoice_no,
                                'amount_applied' => min((float)$s->cash, (float)$receiptVoucher->total_amount)
                            ];
                        }
                    }
                }
            } elseif ($vmRemarks && preg_match('/(?:Sale Invoice|Invoice)[:\s#]*([A-Za-z0-9\-_]+)/i', $vmRemarks, $m)) {
                $invNo = trim($m[1]);
                if (str_starts_with($invNo, 'REP')) {
                    $r = \App\Models\RepairOrder::where('repair_no', $invNo)->first();
                    if ($r) {
                        $prevSettled[] = [
                            'type'           => 'repair',
                            'sale_id'        => $r->id,
                            'invoice_no'     => $r->repair_no,
                            'amount_applied' => (float)$receiptVoucher->total_amount,
                        ];
                    }
                } else {
                    $s = \App\Models\Sale::where('invoice_no', $invNo)->first();
                    if ($s) {
                        $prevSettled[] = [
                            'type'           => 'sale',
                            'sale_id'        => $s->id,
                            'invoice_no'     => $s->invoice_no,
                            'amount_applied' => (float)$receiptVoucher->total_amount,
                        ];
                    }
                }
            }
        }

        foreach ($prevSettled as $ps) {
            $type = $ps['type'] ?? (str_starts_with($ps['invoice_no'] ?? '', 'REP') ? 'repair' : 'sale');
            $appliedMap[$type . '_' . $ps['sale_id']] = (float)($ps['amount_applied'] ?? 0);
        }

        if ($partyType === 'customer' || $partyType === 'walkin') {
            // 1. Sales Invoices
            $sales = \App\Models\Sale::where('customer_id', $partyId)
                ->where('sale_status', 'posted')
                ->orderBy('created_at', 'asc')
                ->get(['id', 'invoice_no', 'total_net', 'cash', 'created_at']);

            foreach ($sales as $sale) {
                $totalNet = (float) $sale->total_net;
                $currentCash = (float) $sale->cash;
                
                $thisVoucherApplied = $appliedMap['sale_' . $sale->id] ?? 0;
                $preVoucherPaid = max(0, $currentCash - $thisVoucherApplied);
                $effectiveRemaining = round($totalNet - $preVoucherPaid, 2);

                if ($effectiveRemaining > 0 || $thisVoucherApplied > 0) {
                    $invoices[] = [
                        'id'             => $sale->id,
                        'type'           => 'sale',
                        'type_label'     => 'Order',
                        'invoice_no'     => $sale->invoice_no,
                        'date'           => $sale->created_at ? $sale->created_at->format('d M Y') : '-',
                        'raw_date'       => $sale->created_at ? $sale->created_at->toDateTimeString() : '1970-01-01',
                        'total_net'      => $totalNet,
                        'paid'           => $preVoucherPaid,
                        'remaining'      => $effectiveRemaining,
                        'is_selected'    => ($thisVoucherApplied > 0),
                        'applied_amount' => $thisVoucherApplied,
                    ];
                }
            }

            // 2. Repair Orders Invoices
            if (\Illuminate\Support\Facades\Schema::hasTable('repair_orders')) {
                $repairs = \App\Models\RepairOrder::where('customer_id', $partyId)
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('received_date', 'asc')
                    ->get();

                foreach ($repairs as $repair) {
                    $totalCharges = (float)$repair->total_charges > 0 ? (float)$repair->total_charges : (float)$repair->estimated_cost;
                    $currentPaid = (float)$repair->advance_paid + (float)$repair->final_paid;

                    $thisVoucherApplied = $appliedMap['repair_' . $repair->id] ?? 0;
                    $preVoucherPaid = max(0, $currentPaid - $thisVoucherApplied);
                    $effectiveRemaining = round(max(0, $totalCharges - $preVoucherPaid), 2);

                    if ($effectiveRemaining > 0 || $thisVoucherApplied > 0) {
                        $itemDesc = $repair->item_name ? " ({$repair->item_name})" : '';
                        $date = $repair->received_date ? $repair->received_date->format('d M Y') : ($repair->created_at ? $repair->created_at->format('d M Y') : '-');
                        $rawDate = $repair->received_date ? $repair->received_date->toDateTimeString() : ($repair->created_at ? $repair->created_at->toDateTimeString() : '1970-01-01');

                        $invoices[] = [
                            'id'             => $repair->id,
                            'type'           => 'repair',
                            'type_label'     => 'Repair',
                            'invoice_no'     => $repair->repair_no . $itemDesc,
                            'date'           => $date,
                            'raw_date'       => $rawDate,
                            'total_net'      => $totalCharges,
                            'paid'           => $preVoucherPaid,
                            'remaining'      => $effectiveRemaining,
                            'is_selected'    => ($thisVoucherApplied > 0),
                            'applied_amount' => $thisVoucherApplied,
                        ];
                    }
                }
            }

            usort($invoices, function($a, $b) {
                return strcmp($a['raw_date'] ?? '', $b['raw_date'] ?? '');
            });
        }

        $nextRvid = $receiptVoucher->rvid;

        return view('admin_panel.vochers.reciepts_vouchers', compact(
            'receiptVoucher',
            'rows',
            'narrations',
            'AccountHeads',
            'nextRvid',
            'partyType',
            'partyId',
            'partyList',
            'partyBalance',
            'invoices'
        ));
    }

    /**
     * Update an existing Receipt Voucher
     */
    public function update_rec_vochers(Request $request, $id)
    {
        $receiptVoucher = ReceiptsVoucher::find($id);
        if (!$receiptVoucher) {
            $vm = VoucherMaster::find($id);
            if ($vm && preg_match('/Ref:\s*(RVID-[\w\-]+)/', $vm->remarks, $m)) {
                $receiptVoucher = ReceiptsVoucher::where('rvid', $m[1])->first();
            }
            if (!$receiptVoucher && $vm) {
                $receiptVoucher = ReceiptsVoucher::where('rvid', $vm->voucher_no)->first();
            }
        }
        if (!$receiptVoucher) {
            $receiptVoucher = ReceiptsVoucher::where('rvid', $id)->firstOrFail();
        }

        DB::beginTransaction();
        try {
            $rvid = $receiptVoucher->rvid;

            // 1. Rollback old V2 integration / journal entries
            $oldVoucherMaster = VoucherMaster::where('remarks', 'LIKE', "%Ref: {$rvid}%")
                ->orWhere('voucher_no', $rvid)
                ->first();
            $oldVoucherNo = $oldVoucherMaster ? $oldVoucherMaster->voucher_no : null;
            if ($oldVoucherMaster) {
                app(\App\Services\JournalEntryService::class)->reverseEntriesForSource($oldVoucherMaster);
                $oldVoucherMaster->details()->delete();
                $oldVoucherMaster->delete();
            }

            // 2. Rollback old legacy ledger impact
            $oldType     = strtolower($receiptVoucher->type);
            $oldPartyId  = (int)$receiptVoucher->party_id;
            $oldTotalAmt = (float)$receiptVoucher->total_amount;

            if (($oldType === 'customer' || $oldType === 'walkin') && $oldTotalAmt > 0) {
                $latestLedger = CustomerLedger::where('customer_id', $oldPartyId)->latest()->first();
                $bal = $latestLedger ? $latestLedger->closing_balance : 0;
                CustomerLedger::create([
                    'customer_id'      => $oldPartyId,
                    'admin_or_user_id' => auth()->id(),
                    'previous_balance' => $bal,
                    'opening_balance'  => 0,
                    'closing_balance'  => $bal + $oldTotalAmt,
                    'description'      => "Adjustment: Receipt Voucher Edit #{$rvid} (Rollback)",
                ]);
            } elseif ($oldType === 'vendor' && $oldTotalAmt > 0) {
                $latestLedger = VendorLedger::where('vendor_id', $oldPartyId)->latest()->first();
                $bal = $latestLedger ? $latestLedger->closing_balance : 0;
                VendorLedger::create([
                    'vendor_id'        => $oldPartyId,
                    'admin_or_user_id' => auth()->id(),
                    'previous_balance' => $bal,
                    'opening_balance'  => 0,
                    'closing_balance'  => $bal - $oldTotalAmt,
                    'description'      => "Adjustment: Receipt Voucher Edit #{$rvid} (Rollback)",
                ]);
            } elseif (is_numeric($oldType)) {
                $acc = Account::find($oldPartyId);
                if ($acc) {
                    $acc->current_balance = $acc->current_balance - $oldTotalAmt;
                    $acc->save();
                }
            }

                        // Rollback previous invoice settlement (both sale and repair)
            $prevSettled = json_decode($receiptVoucher->settled_invoices, true) ?: [];
            if (empty($prevSettled) && $oldVoucherMaster) {
                if (preg_match('/Invoices:\s*([^\)]+)/', $oldVoucherMaster->remarks, $m)) {
                    $invNos = array_map('trim', explode(',', $m[1]));
                    foreach ($invNos as $invNo) {
                        if (str_starts_with($invNo, 'REP')) {
                            $r = \App\Models\RepairOrder::where('repair_no', $invNo)->first();
                            if ($r) {
                                $prevSettled[] = [
                                    'type'           => 'repair',
                                    'sale_id'        => $r->id,
                                    'invoice_no'     => $r->repair_no,
                                    'amount_applied' => min((float)$r->final_paid, $oldTotalAmt)
                                ];
                            }
                        } else {
                            $s = \App\Models\Sale::where('invoice_no', $invNo)->first();
                            if ($s) {
                                $prevSettled[] = [
                                    'type'           => 'sale',
                                    'sale_id'        => $s->id,
                                    'invoice_no'     => $s->invoice_no,
                                    'amount_applied' => min((float)$s->cash, $oldTotalAmt)
                                ];
                            }
                        }
                    }
                }
            }

            if (!empty($prevSettled)) {
                foreach ($prevSettled as $ps) {
                    $type = $ps['type'] ?? (str_starts_with($ps['invoice_no'] ?? '', 'REP') ? 'repair' : 'sale');
                    $applied = (float)($ps['amount_applied'] ?? 0);

                    if ($type === 'repair') {
                        $repair = \App\Models\RepairOrder::find($ps['sale_id'] ?? null);
                        if ($repair) {
                            $repair->final_paid = max(0, (float)$repair->final_paid - $applied);
                            $tot = (float)$repair->total_charges > 0 ? (float)$repair->total_charges : (float)$repair->estimated_cost;
                            $repair->due_amount = max(0, $tot - ($repair->advance_paid + $repair->final_paid));
                            $repair->save();
                        }
                    } else {
                        $sale = \App\Models\Sale::find($ps['sale_id'] ?? null);
                        if ($sale) {
                            $sale->cash = max(0, (float)$sale->cash - $applied);
                            $sale->change = (float)$sale->cash - (float)$sale->total_net;
                            $sale->save();
                        }
                    }
                }
            }

            // 3. Process Narrations
            $narrationIds = [];
            if ($request->narration_id) {
                foreach ($request->narration_id as $index => $narrId) {
                    $manualText = $request->narration_text[$index] ?? null;
                    if (empty($narrId) && !empty($manualText)) {
                        $new = \App\Models\Narration::create([
                            'expense_head' => 'Receipts Voucher',
                            'narration'    => $manualText,
                        ]);
                        $narrationIds[] = (string) $new->id;
                    } else {
                        $narrationIds[] = (string) $narrId;
                    }
                }
            }

            // 4. Update ReceiptsVoucher record
            $receiptVoucher->update([
                'receipt_date'     => $request->receipt_date,
                'entry_date'       => $request->entry_date,
                'type'             => $request->vendor_type,
                'party_id'         => $request->vendor_id,
                'tel'              => $request->tel,
                'remarks'          => $request->remarks,
                'narration_id'     => json_encode($narrationIds),
                'reference_no'     => json_encode($request->reference_no),
                'row_account_head' => json_encode($request->row_account_head),
                'row_account_id'   => json_encode($request->row_account_id),
                'discount_value'   => json_encode($request->discount_value ?? []),
                'rate'             => json_encode($request->rate ?? []),
                'amount'           => json_encode($request->amount),
                'total_amount'     => $request->total_amount,
                'processed'        => true,
            ]);

            // 5. Invoice settlement logic (both sales and repairs)
            if (($vType === 'customer' || $vType === 'walkin') && $customerId > 0 && $totalAmt > 0) {
                $linkedSaleIds     = array_filter((array)($request->input('linked_sale_ids', [])));
                $linkedSaleTypes   = (array)($request->input('linked_sale_types', []));
                $linkedSaleAmounts = (array)($request->input('linked_sale_amounts', []));

                if (!empty($linkedSaleIds)) {
                    $remaining = $totalAmt;
                    foreach ($linkedSaleIds as $idx => $itemId) {
                        if ($remaining <= 0) break;
                        $itemType = $linkedSaleTypes[$idx] ?? 'sale';

                        if ($itemType === 'repair') {
                            $repair = \App\Models\RepairOrder::find($itemId);
                            if (!$repair || $repair->customer_id != $customerId || $repair->status === 'cancelled') continue;

                            $totalCost = (float)$repair->total_charges > 0 ? (float)$repair->total_charges : (float)$repair->estimated_cost;
                            $paid = (float)$repair->advance_paid + (float)$repair->final_paid;
                            $invoiceDue = round(max(0, $totalCost - $paid), 2);
                            if ($invoiceDue <= 0) continue;

                            $applyAmt = isset($linkedSaleAmounts[$idx]) && (float)$linkedSaleAmounts[$idx] > 0
                                ? min((float)$linkedSaleAmounts[$idx], $invoiceDue, $remaining)
                                : min($invoiceDue, $remaining);

                            $repair->final_paid = (float)$repair->final_paid + $applyAmt;
                            $repair->due_amount = max(0, $totalCost - ($repair->advance_paid + $repair->final_paid));
                            $repair->save();

                            if (class_exists(\App\Models\RepairOrderLog::class)) {
                                \App\Models\RepairOrderLog::create([
                                    'repair_order_id' => $repair->id,
                                    'user_id'         => auth()->id(),
                                    'action'          => 'payment_received',
                                    'amount'          => $applyAmt,
                                    'notes'           => "Payment of Rs. " . number_format($applyAmt, 2) . " received via Receipt Voucher {$rvid}",
                                ]);
                            }

                            $remaining -= $applyAmt;
                            $settledSaleIds[] = [
                                'type'           => 'repair',
                                'sale_id'        => $repair->id,
                                'invoice_no'     => $repair->repair_no,
                                'amount_applied' => $applyAmt
                            ];
                        } else {
                            $sale = \App\Models\Sale::find($itemId);
                            if (!$sale || $sale->customer_id != $customerId || $sale->sale_status !== 'posted') continue;

                            $invoiceDue = round((float)$sale->total_net - (float)$sale->cash, 2);
                            if ($invoiceDue <= 0) continue;

                            $applyAmt = isset($linkedSaleAmounts[$idx]) && (float)$linkedSaleAmounts[$idx] > 0
                                ? min((float)$linkedSaleAmounts[$idx], $invoiceDue, $remaining)
                                : min($invoiceDue, $remaining);

                            $sale->cash += $applyAmt;
                            $sale->change = $sale->cash - $sale->total_net;
                            $sale->save();

                            $remaining -= $applyAmt;
                            $settledSaleIds[] = [
                                'type'           => 'sale',
                                'sale_id'        => $sale->id,
                                'invoice_no'     => $sale->invoice_no,
                                'amount_applied' => $applyAmt
                            ];
                        }
                    }
                } else {
                    $allDue = [];

                    $sales = \App\Models\Sale::where('customer_id', $customerId)
                        ->where('sale_status', 'posted')
                        ->get(['id', 'invoice_no', 'total_net', 'cash', 'created_at']);
                    foreach ($sales as $s) {
                        $due = round((float)$s->total_net - (float)$s->cash, 2);
                        if ($due > 0) {
                            $allDue[] = [
                                'type'  => 'sale',
                                'model' => $s,
                                'due'   => $due,
                                'date'  => $s->created_at ? $s->created_at->toDateTimeString() : '1970-01-01',
                            ];
                        }
                    }

                    if (\Illuminate\Support\Facades\Schema::hasTable('repair_orders')) {
                        $repairs = \App\Models\RepairOrder::where('customer_id', $customerId)
                            ->where('status', '!=', 'cancelled')
                            ->get();
                        foreach ($repairs as $r) {
                            $tot = (float)$r->total_charges > 0 ? (float)$r->total_charges : (float)$r->estimated_cost;
                            $paid = (float)$r->advance_paid + (float)$r->final_paid;
                            $due = round(max(0, $tot - $paid), 2);
                            if ($due > 0) {
                                $allDue[] = [
                                    'type'  => 'repair',
                                    'model' => $r,
                                    'due'   => $due,
                                    'date'  => $r->received_date ? $r->received_date->toDateTimeString() : ($r->created_at ? $r->created_at->toDateTimeString() : '1970-01-01'),
                                ];
                            }
                        }
                    }

                    usort($allDue, function($a, $b) {
                        return strcmp($a['date'], $b['date']);
                    });

                    $remaining = $totalAmt;
                    foreach ($allDue as $item) {
                        if ($remaining <= 0) break;
                        $applyAmt = min($item['due'], $remaining);

                        if ($item['type'] === 'repair') {
                            $repair = $item['model'];
                            $repair->final_paid = (float)$repair->final_paid + $applyAmt;
                            $tot = (float)$repair->total_charges > 0 ? (float)$repair->total_charges : (float)$repair->estimated_cost;
                            $repair->due_amount = max(0, $tot - ($repair->advance_paid + $repair->final_paid));
                            $repair->save();

                            if (class_exists(\App\Models\RepairOrderLog::class)) {
                                \App\Models\RepairOrderLog::create([
                                    'repair_order_id' => $repair->id,
                                    'user_id'         => auth()->id(),
                                    'action'          => 'payment_received',
                                    'amount'          => $applyAmt,
                                    'notes'           => "Payment of Rs. " . number_format($applyAmt, 2) . " received via Receipt Voucher {$rvid} (Auto-FIFO)",
                                ]);
                            }

                            $settledSaleIds[] = [
                                'type'           => 'repair',
                                'sale_id'        => $repair->id,
                                'invoice_no'     => $repair->repair_no,
                                'amount_applied' => $applyAmt
                            ];
                        } else {
                            $sale = $item['model'];
                            $sale->cash += $applyAmt;
                            $sale->change = $sale->cash - $sale->total_net;
                            $sale->save();

                            $settledSaleIds[] = [
                                'type'           => 'sale',
                                'sale_id'        => $sale->id,
                                'invoice_no'     => $sale->invoice_no,
                                'amount_applied' => $applyAmt
                            ];
                        }
                        $remaining -= $applyAmt;
                    }
                }
            }

            // 6. V2 Integration
            $partyType       = null;
            $creditAccountId = null;
            $balanceService  = app(\App\Services\BalanceService::class);

            if ($vType == 'customer' || $vType == 'walkin') {
                $partyType       = \App\Models\Customer::class;
                $creditAccountId = $balanceService->getAccountsReceivableId();
            } elseif ($vType == 'vendor') {
                $partyType       = \App\Models\Vendor::class;
                $creditAccountId = $balanceService->getAccountsPayableId();
            } else {
                $partyType       = \App\Models\Account::class;
                $creditAccountId = $request->vendor_id;
            }

            if ($creditAccountId) {
                $v2Lines = [];
                // DEBIT SIDE (Cash/Bank)
                if ($request->row_account_id && $request->amount) {
                    foreach ($request->row_account_id as $idx => $accId) {
                        $amt = (float) ($request->amount[$idx] ?? 0);
                        if ($amt > 0) {
                            $v2Lines[] = [
                                'account_id' => $accId,
                                'debit'      => $amt,
                                'credit'     => 0,
                                'narration'  => $request->narration_text[$idx] ?? 'Receipt',
                            ];
                        }
                    }
                }

                // CREDIT SIDE (AR/Party)
                if ($totalAmt > 0) {
                    $v2Lines[] = [
                        'account_id' => $creditAccountId,
                        'debit'      => 0,
                        'credit'     => $totalAmt,
                        'party_type' => $partyType,
                        'party_id'   => $request->vendor_id,
                        'narration'  => 'Receipt from '.$request->vendor_type,
                    ];
                }

                if (!empty($v2Lines)) {
                    $remarksNote = $request->remarks . " (Ref: $rvid)";
                    if (!empty($settledSaleIds)) {
                        $invoiceNos = implode(', ', array_column($settledSaleIds, 'invoice_no'));
                        $remarksNote .= " | Invoices: $invoiceNos";
                    }

                    app(\App\Services\VoucherService::class)->createVoucher([
                        'voucher_type' => 'receipt',
                        'voucher_no'   => $oldVoucherNo,
                        'date'         => $request->receipt_date,
                        'status'       => 'posted',
                        'party_type'   => $partyType,
                        'party_id'     => $request->vendor_id,
                        'remarks'      => $remarksNote,
                    ], $v2Lines, auth()->id());

                    // Legacy customer ledger update
                    if (($vType == 'customer' || $vType == 'walkin') && $totalAmt > 0) {
                        $latestLedger = CustomerLedger::where('customer_id', $request->vendor_id)->latest()->first();
                        $prevBal = $latestLedger ? $latestLedger->closing_balance : (
                            \App\Models\Customer::find($request->vendor_id)->opening_balance ?? 0
                        );
                        $ledgerDesc = 'Receipt Voucher ' . $rvid;
                        if (!empty($settledSaleIds)) {
                            $invoiceNos = implode(', ', array_column($settledSaleIds, 'invoice_no'));
                            $ledgerDesc .= ' | ' . $invoiceNos;
                        }
                        CustomerLedger::create([
                            'customer_id'      => $request->vendor_id,
                            'admin_or_user_id' => auth()->id(),
                            'previous_balance' => $prevBal,
                            'opening_balance'  => 0,
                            'closing_balance'  => $prevBal - $totalAmt,
                            'description'      => $ledgerDesc,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('all_recepit_vochers')->with('success', "Receipt Voucher #{$rvid} updated successfully and ledgers synchronized!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update Receipt Voucher Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update voucher: ' . $e->getMessage())->withInput();
        }
    }

    public function Payment_vochers()
    {
        $narrations = \App\Models\Narration::where('expense_head', 'Payment voucher')
            ->pluck('narration', 'id');
        $AccountHeads = AccountHead::whereRaw('LOWER(TRIM(name)) IN ("cash", "bank")')->get();
        // echo"<pre>";
        // print_r($AccountHeads);
        // echo"</pre>";
        // dd();

        // Last RVID nikalna
        $lastVoucher = \App\Models\PaymentVoucher::latest('id')->first();

        // Next ID generate karna
        $nextId = $lastVoucher ? $lastVoucher->id + 1 : 1;
        $nextPVID = 'PVID-'.str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('admin_panel.vochers.payment_vochers.payment_vouchers', compact('narrations', 'AccountHeads', 'nextPVID'));
    }

    public function store_Pay_vochers(Request $request)
    {
        DB::beginTransaction();
        try {
            $pvid = PaymentVoucher::generateInvoiceNo();
            $narrationIds = [];

            // Narration handling (assuming multiple narrations from table)
            if ($request->narration_id) {
                foreach ($request->narration_id as $index => $narrId) {
                    $manualText = $request->narration_text[$index] ?? null;
                    if (empty($narrId) && ! empty($manualText)) {
                        $new = \App\Models\Narration::create([
                            'expense_head' => 'Payment voucher',
                            'narration' => $manualText,
                        ]);
                        $narrationIds[] = (string) $new->id;
                    } else {
                        $narrationIds[] = (string) $narrId;
                    }
                }
            }

            // In this new design:
            // Header = Source (Account) -> row_account_id (Single)
            // Table = Destination (Party) -> vendor_type[], vendor_id[] (Multiple)

            $voucherData = [
                'pvid' => $pvid,
                'receipt_date' => $request->receipt_date,
                'entry_date' => $request->entry_date,

                // Store Header Source as single values
                'row_account_head' => $request->header_account_head,
                'row_account_id' => $request->header_account_id,
                'remarks' => $request->remarks,

                // Store Table Destinations as JSON
                'type' => json_encode($request->vendor_type),
                'party_id' => json_encode($request->vendor_id),
                'narration_id' => json_encode($narrationIds),
                'reference_no' => json_encode($request->reference_no),
                'discount_value' => json_encode($request->discount_value),
                'rate' => json_encode($request->rate),
                'amount' => json_encode($request->amount),
                'total_amount' => $request->total_amount,
            ];

            $payment = PaymentVoucher::create($voucherData);

            $totalAmount = (float) $request->total_amount;

            // ✅ V2 VOUCHER INTEGRATION (Primary Logic for printing and GL)
            try {
                \Log::info('V2 Payment Integration Start. Header Account: '.$request->header_account_id);

                $balanceService = app(\App\Services\BalanceService::class);
                $firstRow = null;
                if ($request->vendor_id && count($request->vendor_id) > 0) {
                    $firstRow = [
                        'type' => $request->vendor_type[0] ?? null,
                        'id'   => $request->vendor_id[0] ?? null,
                    ];
                }

                $partyType = null;
                if ($firstRow) {
                    $fType = strtolower($firstRow['type']);
                    if ($fType == 'customer' || $fType == 'walkin') {
                        $partyType = \App\Models\Customer::class;
                    } elseif ($fType == 'vendor') {
                        $partyType = \App\Models\Vendor::class;
                    } else {
                        $partyType = \App\Models\Account::class;
                    }
                }

                $v2Lines = [];
                // 1. DEBIT SIDE (AP / AR / Accounts) - Table Destinations
                if ($request->vendor_id && $request->amount) {
                    foreach ($request->vendor_id as $idx => $partyId) {
                        $type = $request->vendor_type[$idx] ?? null;
                        $amt = (float) ($request->amount[$idx] ?? 0);
                        if ($amt <= 0) {
                            continue;
                        }

                        $controlAccountId = null;
                        if ($type == 'vendor') {
                            $controlAccountId = $balanceService->getAccountsPayableId();
                        } elseif ($type == 'customer' || $type == 'walkin') {
                            $controlAccountId = $balanceService->getAccountsReceivableId();
                        } else {
                            $controlAccountId = $partyId;
                        }

                        $linePartyType = null;
                        $linePartyId = null;

                        if ($type == 'vendor') {
                            $linePartyType = \App\Models\Vendor::class;
                            $linePartyId = $partyId;
                        } elseif ($type == 'customer' || $type == 'walkin') {
                            $linePartyType = \App\Models\Customer::class;
                            $linePartyId = $partyId;
                        } else {
                            $linePartyType = \App\Models\Account::class;
                            $linePartyId = $partyId;
                        }

                        if ($controlAccountId) {
                            $v2Lines[] = [
                                'account_id' => $controlAccountId,
                                'debit' => $amt,
                                'credit' => 0,
                                'party_type' => $linePartyType,
                                'party_id' => $linePartyId,
                                'narration' => $request->narration_text[$idx] ?? "Payment to " . $type,
                            ];
                        }
                    }
                }

                // 2. CREDIT SIDE (Cash/Bank) - Header Source
                if ($totalAmount > 0 && $request->header_account_id) {
                    $v2Lines[] = [
                        'account_id' => $request->header_account_id,
                        'debit' => 0,
                        'credit' => $totalAmount,
                        'narration' => $request->remarks ?: "Payment from Cash/Bank",
                    ];
                }

                if (! empty($v2Lines)) {
                    app(\App\Services\VoucherService::class)->createVoucher([
                        'voucher_type' => 'payment',
                        'date' => $request->receipt_date,
                        'status' => 'posted',
                        'party_type' => $partyType,
                        'party_id' => $firstRow ? $firstRow['id'] : null,
                        'remarks' => $request->remarks." (Ref: $pvid)",
                    ], $v2Lines, auth()->id());

                    \Log::info('V2 Payment Voucher Created Successfully.');
                }
            } catch (\Exception $e) {
                \Log::error('V2 Payment Sync Error: '.$e->getMessage());
            }

            /**
             * STEP 2: Legacy Table Destinations (Parties) - Update polymorphic ledger balances (Closing balances)
             */
            if ($request->vendor_id && $request->amount) {
                foreach ($request->vendor_id as $index => $partyId) {
                    $type = $request->vendor_type[$index] ?? null;
                    $rowAmount = isset($request->amount[$index]) ? (float) $request->amount[$index] : 0;

                    if ($rowAmount <= 0) {
                        continue;
                    }

                    if ($type === 'vendor') {
                        $ledger = VendorLedger::where('vendor_id', $partyId)->latest()->first();
                        $bal = $ledger ? $ledger->closing_balance : 0;
                        VendorLedger::create([
                            'vendor_id'         => $partyId,
                            'admin_or_user_id'  => auth()->id(),
                            'opening_balance'   => 0,
                            'previous_balance'  => $bal,
                            'closing_balance'   => $bal - $rowAmount, // ✅ MINUS: payment reduces vendor balance
                        ]);

                    } elseif ($type === 'customer' || $type === 'walkin') {
                        $ledger = CustomerLedger::where('customer_id', $partyId)->latest()->first();
                        $bal = $ledger ? $ledger->closing_balance : 0;
                        CustomerLedger::create([
                            'customer_id'      => $partyId,
                            'admin_or_user_id' => auth()->id(),
                            'previous_balance' => $bal,
                            'opening_balance'  => 0,
                            'closing_balance'  => $bal + $rowAmount, // ✅ PLUS: paying customer increases customer balance
                        ]);
                    } elseif ($type) {
                        // Account ID in table
                        $acc = Account::find($partyId);
                        if ($acc) {
                            $acc->current_balance = $acc->current_balance + $rowAmount;
                            $acc->save();
                        }
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'Payment Voucher saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function all_Payment_vochers()
    {
        // V2: Fetch Payment Vouchers only
        $receipts = \App\Models\VoucherMaster::where('voucher_type', \App\Models\VoucherMaster::TYPE_PAYMENT)
            ->with('party') // Eager load the polymorphic party
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($receipts as $voucher) {
            $typeLabel = '-';
            $partyName = '-';

            if ($voucher->party) {
                // Determine Label from Class Name
                $class = get_class($voucher->party);
                if (str_contains($class, 'Customer')) {
                    $typeLabel = 'Customer';
                    $partyName = $voucher->party->customer_name ?? $voucher->party->name ?? '-';
                } elseif (str_contains($class, 'Vendor')) {
                    $typeLabel = 'Vendor';
                    $partyName = $voucher->party->name ?? '-';
                } elseif (str_contains($class, 'Account')) {
                    $typeLabel = 'Account';
                    $partyName = $voucher->party->title ?? '-';
                } else {
                    $typeLabel = class_basename($class);
                    $partyName = $voucher->party->name ?? '-';
                }
            }

            // Attach for View
            $voucher->type_label = $typeLabel;
            $voucher->party_name = $partyName;

            // Map fields for View compatibility
            $voucher->pvid = $voucher->voucher_no;
            $voucher->receipt_date = $voucher->date->format('Y-m-d');
            $voucher->entry_date = $voucher->created_at->format('Y-m-d');

            // Fix for view expecting 'amount' field
            if (! isset($voucher->amount)) {
                $voucher->amount = $voucher->total_amount;
            }
        }

        return view('admin_panel.vochers.payment_vochers.all_payment_vochers', compact('receipts'));
    }


    /**
     * Show form for editing a Payment Voucher
     */
    public function edit_Pay_vochers($id)
    {
        // Smart finder: By VoucherMaster ID (since all_payment_vochers passes $item->id), PaymentVoucher ID, or PVID
        $paymentVoucher = null;
        $vm = null;

        if (is_numeric($id)) {
            $vm = VoucherMaster::find($id);
            if (!$vm) {
                $paymentVoucher = PaymentVoucher::find($id);
            }
        } else {
            $paymentVoucher = PaymentVoucher::where('pvid', $id)->first();
            if (!$paymentVoucher) {
                $vm = VoucherMaster::where('voucher_no', $id)->first();
            }
        }

        if ($vm && !$paymentVoucher) {
            if (preg_match('/Ref:\s*(PVID-[\w\-]+)/', $vm->remarks, $m)) {
                $paymentVoucher = PaymentVoucher::where('pvid', $m[1])->first();
            }
            if (!$paymentVoucher) {
                $paymentVoucher = PaymentVoucher::where('pvid', $vm->voucher_no)->first();
            }
        }

        // If still no PaymentVoucher, but VoucherMaster exists, auto-mirror / create PaymentVoucher
        if (!$paymentVoucher && $vm) {
            $types = [];
            $partyIds = [];
            $amounts = [];
            $narrations = [];
            $refNos = [];

            $headerAcc = null;
            foreach ($vm->details as $d) {
                if ((float)$d->credit > 0 && !$headerAcc) {
                    $headerAcc = \App\Models\Account::find($d->account_id);
                }
                if ((float)$d->debit > 0) {
                    $pType = 'vendor';
                    $pId = $d->party_id;
                    if ($d->party_type && (str_contains($d->party_type, 'Customer') || $d->party_type === 'customer')) {
                        $pType = 'customer';
                    } elseif ($d->party_type && (str_contains($d->party_type, 'Account') || is_numeric($d->party_type))) {
                        $pType = (string)$d->account_id;
                        $pId = $d->account_id;
                    }
                    if (!$pId && $vm->party_id) {
                        $pId = $vm->party_id;
                        if ($vm->party_type && (str_contains($vm->party_type, 'Customer') || $vm->party_type === 'customer')) {
                            $pType = 'customer';
                        }
                    }
                    $types[] = $pType;
                    $partyIds[] = (string)$pId;
                    $amounts[] = (string)$d->debit;
                    $narrations[] = $d->narration ?? '';
                    $refNos[] = '';
                }
            }

            if (empty($partyIds)) {
                $types[] = 'vendor';
                $partyIds[] = $vm->party_id ? (string)$vm->party_id : '1';
                $amounts[] = (string)$vm->total_amount;
                $narrations[] = '';
                $refNos[] = '';
            }

            $defaultHead = $headerAcc ? $headerAcc->head_id : 1;
            $defaultAccId = $headerAcc ? $headerAcc->id : 1;

            $paymentVoucher = PaymentVoucher::create([
                'pvid'             => $vm->voucher_no,
                'receipt_date'     => $vm->date ? $vm->date->format('Y-m-d') : now()->toDateString(),
                'entry_date'       => $vm->created_at ? $vm->created_at->format('Y-m-d') : now()->toDateString(),
                'row_account_head' => $defaultHead,
                'row_account_id'   => $defaultAccId,
                'remarks'          => $vm->remarks,
                'type'             => json_encode($types),
                'party_id'         => json_encode($partyIds),
                'amount'           => json_encode($amounts),
                'narration_id'     => json_encode($narrations),
                'reference_no'     => json_encode($refNos),
                'total_amount'     => $vm->total_amount,
            ]);
        }

        if (!$paymentVoucher) {
            $paymentVoucher = PaymentVoucher::where('pvid', $id)->firstOrFail();
        }

        $narrations = \App\Models\Narration::where('expense_head', 'Payment voucher')
            ->pluck('narration', 'id');
        $AccountHeads = AccountHead::whereRaw('LOWER(TRIM(name)) IN ("cash", "bank")')->get();

        // Decode JSON rows
        $types = json_decode($paymentVoucher->type, true);
        if (!is_array($types)) $types = [$paymentVoucher->type];

        $partyIds = json_decode($paymentVoucher->party_id, true);
        if (!is_array($partyIds)) $partyIds = [$paymentVoucher->party_id];

        $amounts = json_decode($paymentVoucher->amount, true);
        if (!is_array($amounts)) $amounts = [$paymentVoucher->amount];

        $narrIds = json_decode($paymentVoucher->narration_id, true);
        if (!is_array($narrIds)) $narrIds = [];

        $refNos = json_decode($paymentVoucher->reference_no, true);
        if (!is_array($refNos)) $refNos = [];

        $rows = [];
        $balanceService = app(\App\Services\BalanceService::class);

        foreach ($partyIds as $idx => $pId) {
            $type = $types[$idx] ?? 'vendor';
            $amount = $amounts[$idx] ?? 0;
            $partyLabel = 'Select Party';

            if ($type === 'vendor') {
                $v = \App\Models\Vendor::find($pId);
                if ($v) {
                    $bal = $balanceService->getVendorBalance($v->id);
                    $drCr = $bal >= 0 ? 'Cr' : 'Dr';
                    $partyLabel = $v->name . ' (Bal: ' . number_format(abs($bal), 2) . ' ' . $drCr . ')';
                }
            } elseif ($type === 'customer' || $type === 'walkin') {
                $c = \App\Models\Customer::find($pId);
                if ($c) {
                    $bal = $balanceService->getCustomerBalance($c->id);
                    $drCr = $bal >= 0 ? 'Dr' : 'Cr';
                    $partyLabel = $c->customer_name . ' (Bal: ' . number_format(abs($bal), 2) . ' ' . $drCr . ')';
                }
            } else {
                $acc = \App\Models\Account::find($pId);
                if ($acc) {
                    $partyLabel = $acc->title . ' (Bal: ' . number_format(abs($acc->current_balance), 2) . ')';
                }
            }

            $rows[] = [
                'type' => $type,
                'party_id' => $pId,
                'party_label' => $partyLabel,
                'amount' => $amount,
                'narration_id' => $narrIds[$idx] ?? '',
                'reference_no' => $refNos[$idx] ?? '',
            ];
        }

        $sourceAccount = \App\Models\Account::find($paymentVoucher->row_account_id);
        $sourceAccounts = $paymentVoucher->row_account_head 
            ? \App\Models\Account::where('head_id', $paymentVoucher->row_account_head)->get()
            : collect();

        return view('admin_panel.vochers.payment_vochers.payment_vouchers', compact(
            'paymentVoucher',
            'rows',
            'narrations',
            'AccountHeads',
            'sourceAccount',
            'sourceAccounts'
        ));
    }

    /**
     * Update an existing Payment Voucher
     */
    public function update_Pay_vochers(Request $request, $id)
    {
        $payment = PaymentVoucher::find($id);
        if (!$payment) {
            $vm = VoucherMaster::find($id);
            if ($vm && preg_match('/Ref:\s*(PVID-[\w\-]+)/', $vm->remarks, $m)) {
                $payment = PaymentVoucher::where('pvid', $m[1])->first();
            }
        }
        if (!$payment) {
            $payment = PaymentVoucher::where('pvid', $id)->firstOrFail();
        }

        DB::beginTransaction();
        try {
            $pvid = $payment->pvid;

            // 1. Rollback old V2 integration / journal entries
            $oldVoucherMaster = VoucherMaster::where('remarks', 'LIKE', "%Ref: {$pvid}%")
                ->orWhere('voucher_no', $pvid)
                ->first();
            $oldVoucherNo = $oldVoucherMaster ? $oldVoucherMaster->voucher_no : null;
            if ($oldVoucherMaster) {
                app(\App\Services\JournalEntryService::class)->reverseEntriesForSource($oldVoucherMaster);
                $oldVoucherMaster->details()->delete();
                $oldVoucherMaster->delete();
            }

            // 2. Rollback old legacy ledger impacts
            $oldTypes = json_decode($payment->type, true) ?: [$payment->type];
            $oldPartyIds = json_decode($payment->party_id, true) ?: [$payment->party_id];
            $oldAmounts = json_decode($payment->amount, true) ?: [$payment->amount];

            foreach ($oldPartyIds as $idx => $oldPartyId) {
                $oldType = $oldTypes[$idx] ?? null;
                $oldAmt = (float)($oldAmounts[$idx] ?? 0);
                if ($oldAmt <= 0) continue;

                if ($oldType === 'vendor') {
                    $ledger = VendorLedger::where('vendor_id', $oldPartyId)->latest()->first();
                    $bal = $ledger ? $ledger->closing_balance : 0;
                    VendorLedger::create([
                        'vendor_id'         => $oldPartyId,
                        'admin_or_user_id'  => auth()->id(),
                        'opening_balance'   => 0,
                        'previous_balance'  => $bal,
                        'closing_balance'   => $bal + $oldAmt,
                    ]);
                } elseif ($oldType === 'customer' || $oldType === 'walkin') {
                    $ledger = CustomerLedger::where('customer_id', $oldPartyId)->latest()->first();
                    $bal = $ledger ? $ledger->closing_balance : 0;
                    CustomerLedger::create([
                        'customer_id'      => $oldPartyId,
                        'admin_or_user_id' => auth()->id(),
                        'previous_balance' => $bal,
                        'opening_balance'  => 0,
                        'closing_balance'  => $bal - $oldAmt,
                    ]);
                } elseif ($oldType) {
                    $acc = Account::find($oldPartyId);
                    if ($acc) {
                        $acc->current_balance = $acc->current_balance - $oldAmt;
                        $acc->save();
                    }
                }
            }

            // 3. Update PaymentVoucher record
            $narrationIds = [];
            if ($request->narration_id) {
                foreach ($request->narration_id as $index => $narrId) {
                    $manualText = $request->narration_text[$index] ?? null;
                    if (empty($narrId) && !empty($manualText)) {
                        $new = \App\Models\Narration::create([
                            'expense_head' => 'Payment voucher',
                            'narration' => $manualText,
                        ]);
                        $narrationIds[] = (string) $new->id;
                    } else {
                        $narrationIds[] = (string) $narrId;
                    }
                }
            }

            $payment->update([
                'receipt_date' => $request->receipt_date,
                'entry_date' => $request->entry_date,
                'row_account_head' => $request->header_account_head,
                'row_account_id' => $request->header_account_id,
                'remarks' => $request->remarks,
                'type' => json_encode($request->vendor_type),
                'party_id' => json_encode($request->vendor_id),
                'narration_id' => json_encode($narrationIds),
                'reference_no' => json_encode($request->reference_no),
                'discount_value' => json_encode($request->discount_value),
                'rate' => json_encode($request->rate),
                'amount' => json_encode($request->amount),
                'total_amount' => $request->total_amount,
            ]);

            $totalAmount = (float) $request->total_amount;

            // 4. Create new V2 Voucher & Journal Entries with line-level party tracking
            $balanceService = app(\App\Services\BalanceService::class);
            $v2Lines = [];

            if ($request->vendor_id && $request->amount) {
                foreach ($request->vendor_id as $idx => $partyId) {
                    $type = $request->vendor_type[$idx] ?? null;
                    $amt = (float) ($request->amount[$idx] ?? 0);
                    if ($amt <= 0) continue;

                    $controlAccountId = null;
                    $linePartyType = null;
                    $linePartyId = null;

                    if ($type == 'vendor') {
                        $controlAccountId = $balanceService->getAccountsPayableId();
                        $linePartyType = \App\Models\Vendor::class;
                        $linePartyId = $partyId;
                    } elseif ($type == 'customer' || $type == 'walkin') {
                        $controlAccountId = $balanceService->getAccountsReceivableId();
                        $linePartyType = \App\Models\Customer::class;
                        $linePartyId = $partyId;
                    } else {
                        $controlAccountId = $partyId;
                        $linePartyType = \App\Models\Account::class;
                        $linePartyId = $partyId;
                    }

                    if ($controlAccountId) {
                        $v2Lines[] = [
                            'account_id' => $controlAccountId,
                            'debit' => $amt,
                            'credit' => 0,
                            'party_type' => $linePartyType,
                            'party_id' => $linePartyId,
                            'narration' => $request->narration_text[$idx] ?? "Payment to " . $type,
                        ];
                    }
                }
            }

            if ($totalAmount > 0 && $request->header_account_id) {
                $v2Lines[] = [
                    'account_id' => $request->header_account_id,
                    'debit' => 0,
                    'credit' => $totalAmount,
                    'party_type' => null,
                    'party_id' => null,
                    'narration' => $request->remarks ?: "Payment from Cash/Bank",
                ];
            }

            if (!empty($v2Lines)) {
                $firstRowType = $v2Lines[0]['party_type'] ?? null;
                $firstRowId = $v2Lines[0]['party_id'] ?? null;

                app(\App\Services\VoucherService::class)->createVoucher([
                    'voucher_type' => 'payment',
                    'voucher_no' => $oldVoucherNo,
                    'date' => $request->receipt_date,
                    'status' => 'posted',
                    'party_type' => $firstRowType,
                    'party_id' => $firstRowId,
                    'remarks' => $request->remarks . " (Ref: $pvid)",
                ], $v2Lines, auth()->id());
            }

            // 5. Update legacy ledger impacts for each individual party
            if ($request->vendor_id && $request->amount) {
                foreach ($request->vendor_id as $index => $partyId) {
                    $type = $request->vendor_type[$index] ?? null;
                    $rowAmount = isset($request->amount[$index]) ? (float) $request->amount[$index] : 0;
                    if ($rowAmount <= 0) continue;

                    if ($type === 'vendor') {
                        $ledger = VendorLedger::where('vendor_id', $partyId)->latest()->first();
                        $bal = $ledger ? $ledger->closing_balance : 0;
                        VendorLedger::create([
                            'vendor_id'         => $partyId,
                            'admin_or_user_id'  => auth()->id(),
                            'opening_balance'   => 0,
                            'previous_balance'  => $bal,
                            'closing_balance'   => $bal - $rowAmount,
                        ]);
                    } elseif ($type === 'customer' || $type === 'walkin') {
                        $ledger = CustomerLedger::where('customer_id', $partyId)->latest()->first();
                        $bal = $ledger ? $ledger->closing_balance : 0;
                        CustomerLedger::create([
                            'customer_id'      => $partyId,
                            'admin_or_user_id' => auth()->id(),
                            'previous_balance' => $bal,
                            'opening_balance'  => 0,
                            'closing_balance'  => $bal + $rowAmount,
                        ]);
                    } elseif ($type) {
                        $acc = Account::find($partyId);
                        if ($acc) {
                            $acc->current_balance = $acc->current_balance + $rowAmount;
                            $acc->save();
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('all_Payment_vochers')->with('success', "Payment Voucher #{$pvid} updated successfully and ledgers synchronized!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update Payment Voucher Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update voucher: ' . $e->getMessage())->withInput();
        }
    }

    public function Paymentprint($id)
    {
        // 1. Try V2 VoucherMaster First
        $voucherV2 = \App\Models\VoucherMaster::find($id);

        if ($voucherV2) {
            // Lazy load relationships
            $voucherV2->load(['details.account', 'party']);

            // -- Adapter for V2 to V1 View --
            $voucher = (object) [
                'pvid' => $voucherV2->voucher_no,
                'receipt_date' => $voucherV2->date->format('Y-m-d'),
                'total_amount' => $voucherV2->amount,
                'remarks' => $voucherV2->remarks,
                'type' => 'unknown', // Default
            ];

            if (! isset($voucher->total_amount)) {
                $voucher->total_amount = $voucherV2->total_amount;
            }

            $rows = [];
            foreach ($voucherV2->details as $detail) {
                // Payment Voucher: Only show CREDIT rows (which account money left from - Cash/Bank)
                // Skip Debit rows (AP/Payable offset) — not useful for print
                if ($voucherV2->voucher_type === 'payment' && $detail->debit > 0) {
                    continue; // Skip Dr side (AP)
                }

                $headName = $detail->account->accountHead->name ?? '-';
                $accName  = $detail->account->title ?? '-';
                $accCode  = $detail->account->account_code ?? '-';

                $rows[] = [
                    'narration'    => $detail->narration,
                    'reference'    => '-',
                    'account_head' => $headName,
                    'account_name' => $accName,
                    'account_code' => $accCode,
                    'amount'       => $detail->credit > 0 ? $detail->credit : $detail->debit,
                ];
            }

            // Party Logic
            $party = $voucherV2->party;
            $previousBalance = 0;

            if ($party) {
                if ($party instanceof \App\Models\Customer) {
                    $voucher->type = ($party->customer_type == 'Walking Customer') ? 'walkin' : 'customer';

                    $party->name = $party->customer_name;
                    $party->address = $party->address ?? '-';
                    $party->mobile = $party->mobile ?? '-'; // View uses mobile/phone? View uses mobile for customer

                    $previousBalance = \App\Models\CustomerLedger::where('customer_id', $party->id)
                        ->where('created_at', '<', $voucherV2->created_at)
                        ->orderBy('id', 'desc')
                        ->value('closing_balance') ?? ($party->opening_balance ?? 0);

                } elseif ($party instanceof \App\Models\Vendor) {
                    $voucher->type = 'vendor';
                    $party->address = $party->address ?? '-';
                    $party->phone = $party->phone ?? '-'; // View uses phone

                    $previousBalance = \App\Models\VendorLedger::where('vendor_id', $party->id)
                        ->where('created_at', '<', $voucherV2->created_at)
                        ->orderBy('id', 'desc')
                        ->value('closing_balance') ?? ($party->opening_balance ?? 0);

                } elseif ($party instanceof \App\Models\Account) {
                    $voucher->type = '1'; // Numeric triggers Account Block
                    $party->name = $party->title;
                    $party->phone = $party->account_code; // View uses phone
                    $party->head_name = $party->accountHead->name ?? 'Account';

                    $previousBalance = $party->opening_balance;
                }
            }

            return view('admin_panel.vochers.payment_vochers.print', compact('voucher', 'rows', 'party', 'previousBalance'));
        }

        $voucher = \App\Models\PaymentVoucher::findOrFail($id);

        // Decode JSON arrays
        $narrations = json_decode($voucher->narration_id, true) ?? [];
        $references = json_decode($voucher->reference_no, true) ?? [];
        $accountHeads = json_decode($voucher->row_account_head, true) ?? [];
        $accounts = json_decode($voucher->row_account_id, true) ?? [];
        $amounts = json_decode($voucher->amount, true) ?? [];

        // 🧾 Build detailed rows
        $rows = [];
        foreach ($narrations as $index => $narrId) {
            $narration = DB::table('narrations')->where('id', $narrId)->value('narration');
            $ref = $references[$index] ?? null;
            $accountHead = DB::table('account_heads')->where('id', $accountHeads[$index] ?? null)->value('name');
            $account = DB::table('accounts')->where('id', $accounts[$index] ?? null)->first();
            $amount = (float) ($amounts[$index] ?? 0);

            $rows[] = [
                'narration' => $narration,
                'reference' => $ref,
                'account_head' => $accountHead,
                'account_name' => $account->title ?? null,
                'account_code' => $account->account_code ?? null,
                'amount' => $amount,
            ];
        }

        // 🧩 Party setup — dynamic based on type
        $party = null;
        $previousBalance = 0;

        // ✅ Account Head type (numeric)
        if (is_numeric($voucher->type)) {
            $accountHead = DB::table('account_heads')->where('id', $voucher->type)->first();
            $account = DB::table('accounts')->where('id', $voucher->party_id)->first();

            if ($account) {
                $party = (object) [
                    'name' => $account->title ?? '—',
                    'address' => '—',
                    'phone' => $account->account_code ?? '—',
                    'head_name' => $accountHead->name ?? '—',
                ];
            }

            $previousBalance = $account->opening_balance ?? 0;

            // ✅ Vendor
        } elseif ($voucher->type === 'vendor') {
            $party = DB::table('vendors')->where('id', $voucher->party_id)->first();
            $previousBalance = DB::table('vendor_ledgers')
                ->where('vendor_id', $voucher->party_id)
                ->orderByDesc('id')
                ->value('closing_balance') ?? 0;

            // ✅ Customer
        } elseif ($voucher->type === 'customer') {
            $party = DB::table('customers')->where('id', $voucher->party_id)->first();
            $previousBalance = DB::table('customer_ledgers')
                ->where('customer_id', $voucher->party_id)
                ->orderByDesc('id')
                ->value('closing_balance') ?? 0;

            // ✅ Walking customer
        } elseif ($voucher->type === 'walkin') {
            $party = DB::table('customers')
                ->where('id', $voucher->party_id)
                ->where('customer_type', 'Walking Customer')
                ->first();
        }

        return view('admin_panel.vochers.payment_vochers.print', compact('voucher', 'rows', 'party', 'previousBalance'));
    }

    public function partyList(Request $request)
    {
        $type = $request->type;
        $data = [];

        try {
            $balanceService = app(\App\Services\BalanceService::class);

            if ($type == 'vendor') {
                $vendors = \Illuminate\Support\Facades\DB::table('vendors')->select('id', 'name as text', 'phone as mobile', 'address', 'opening_balance')->get();
                foreach ($vendors as $vendor) {
                    $vendor->closing_balance = $balanceService->getVendorBalance($vendor->id);
                    $bal = number_format(abs($vendor->closing_balance), 0);
                    $lbl = $vendor->closing_balance >= 0 ? 'Cr' : 'Dr';
                    $vendor->text = $vendor->text . " (Bal: {$bal} {$lbl})";
                    $data[] = $vendor;
                }
            } elseif ($type == 'customer') {
                $customers = \App\Models\Customer::where('customer_type', '!=', 'Walking Customer')
                    ->get(['id', 'customer_name', 'mobile', 'address', 'status', 'opening_balance']);

                foreach ($customers as $customer) {
                    $customer->closing_balance = $balanceService->getCustomerBalance($customer->id);
                    $bal = number_format(abs($customer->closing_balance), 0);
                    $lbl = $customer->closing_balance >= 0 ? 'Dr' : 'Cr';
                    
                    $customer->text = $customer->customer_name . " (Bal: {$bal} {$lbl})";
                    $customer->remarks = $customer->status;
                    $data[] = $customer;
                }
            } elseif ($type == 'walkin') {
                $customers = \App\Models\Customer::where('customer_type', 'Walking Customer')
                    ->get(['id', 'customer_name', 'mobile', 'address', 'status', 'opening_balance']);

                foreach ($customers as $customer) {
                    $customer->closing_balance = $balanceService->getCustomerBalance($customer->id);
                    $bal = number_format(abs($customer->closing_balance), 0);
                    $lbl = $customer->closing_balance >= 0 ? 'Dr' : 'Cr';

                    $customer->text = $customer->customer_name . " (Bal: {$bal} {$lbl})";
                    $customer->remarks = $customer->status;
                    $data[] = $customer;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Party List Error: '.$e->getMessage());

            return response()->json([]); // Return empty on error to avoid breaking JS
        }

        return response()->json($data);
    }

    public function expense_vochers()
    {
        $narrations = \App\Models\Narration::where('expense_head', 'Expense voucher')
            ->pluck('narration', 'id');
        $expenseCategories = \App\Models\ExpenseCategory::orderBy('name')->get();
        $AccountHeads = AccountHead::whereRaw('LOWER(TRIM(name)) IN ("cash", "bank")')->get();

        // Last RVID nikalna
        $lastVoucher = \App\Models\ExpenseVoucher::latest('id')->first();

        // Next ID generate karna
        $nextId = $lastVoucher ? $lastVoucher->id + 1 : 1;
        $nextRvid = 'EVID-'.str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('admin_panel.vochers.expense_vochers.expense_vouchers', compact('narrations', 'expenseCategories', 'AccountHeads', 'nextRvid'));
    }

    public function store_expense_vochers(Request $request)
    {
        DB::beginTransaction();
        try {
            $evid = ExpenseVoucher::generateInvoiceNo();
            $narrationIds = [];

            foreach ($request->narration_id as $index => $narrId) {
                $manualText = $request->narration_text[$index] ?? null;
                $manualType = $request->narration_type_text[$index] ?? 'Manual';

                if (empty($narrId) && ! empty($manualText)) {
                    // Auto expense_head set based on voucher type
                    $expenseHead = 'Expense voucher';
                    if (stripos($manualType, 'Receipt') !== false || $request->voucher_type == 'receipt') {
                        $expenseHead = 'Expense voucher';
                    }

                    $new = \App\Models\Narration::create([
                        'expense_head' => $expenseHead,
                        'narration' => $manualText,
                    ]);

                    $narrationIds[] = (string) $new->id; // store as string → ["7"]
                } else {
                    $narrationIds[] = (string) $narrId; // force string format
                }
            }
            $voucherData = [
                'evid' => $evid,
                'entry_date' => $request->entry_date,
                'type' => $request->vendor_type,
                'party_id' => $request->vendor_id,
                'tel' => $request->tel,
                'remarks' => $request->remarks,
                'reference_no' => $request->ref_no_header,
                'narration_id' => json_encode($narrationIds),
                'row_account_head' => json_encode($request->row_account_head ?? array_fill(0, count($request->row_account_id ?? []), "0")),
                'row_account_id' => json_encode($request->row_account_id),
                'amount' => json_encode($request->amount),
                'total_amount' => $request->total_amount,
            ];

            $expense = ExpenseVoucher::create($voucherData);

            $amount = (float) $request->total_amount;

            $journalService = app(\App\Services\JournalEntryService::class);
            $balanceService = app(\App\Services\BalanceService::class);

            /**
             * STEP 1: Expense Accounts (row side) → PLUS (Debit)
             */
            // Find or create "Expense" Account Head
            $expenseHead = AccountHead::firstOrCreate(
                ['name' => 'Expense'],
                ['opening_balance' => 0]
            );

            // Find or create "General Expense" Account
            $generalExpenseAccount = Account::firstOrCreate(
                ['account_code' => 'GEN-EXP'],
                [
                    'head_id' => $expenseHead->id,
                    'title' => 'General Expense',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'type' => 'Debit',
                    'status' => 1
                ]
            );

            if ($request->row_account_id && $request->amount) {
                foreach ($request->row_account_id as $index => $accId) {
                    $rowAmount = isset($request->amount[$index]) ? (float) $request->amount[$index] : 0;

                    if ($rowAmount > 0) {
                        $categoryName = DB::table('expense_categories')->where('id', $accId)->value('name') ?? 'General Expense';
                        
                        $journalService->recordEntry(
                            $expense,
                            $generalExpenseAccount->id,
                            $rowAmount, // Debit Expense
                            0, // Credit
                            "Expense Voucher #$evid ($categoryName)",
                            $request->entry_date ?? date('Y-m-d')
                        );
                    }
                }
            }

            /**
             * STEP 2: Party side → MINUS
             */
            if ($request->vendor_type === 'vendor') {
                $ledger = VendorLedger::where('vendor_id', $request->vendor_id)->latest()->first();
                if ($ledger) {
                    $ledger->previous_balance = $ledger->closing_balance;
                    $ledger->closing_balance = $ledger->closing_balance - $amount; // MINUS
                    $ledger->save();
                } else {
                    VendorLedger::create([
                        'vendor_id' => $request->vendor_id,
                        'admin_or_user_id' => auth()->id(),
                        'opening_balance' => 0,
                        'previous_balance' => 0,
                        'closing_balance' => -$amount,
                    ]);
                }

                // Credit Vendor Liability Side
                $journalService->recordEntry(
                    $expense,
                    $balanceService->getAccountsPayableId(),
                    0, // Debit
                    $amount, // Credit vendor liability
                    "Expense Voucher #$evid",
                    $request->entry_date ?? date('Y-m-d'),
                    \App\Models\Vendor::find($request->vendor_id)
                );
            } elseif ($request->vendor_type === 'customer') {
                $ledger = CustomerLedger::where('customer_id', $request->vendor_id)->latest()->first();
                if ($ledger) {
                    $ledger->previous_balance = $ledger->closing_balance;
                    $ledger->closing_balance = $ledger->closing_balance - $amount; // MINUS
                    $ledger->save();
                } else {
                    CustomerLedger::create([
                        'customer_id' => $request->vendor_id,
                        'admin_or_user_id' => auth()->id(),
                        'previous_balance' => 0,
                        'opening_balance' => 0,
                        'closing_balance' => -$amount,
                    ]);
                }

                // Credit Customer Receivable Side
                $journalService->recordEntry(
                    $expense,
                    $balanceService->getAccountsReceivableId(),
                    0, // Debit
                    $amount, // Credit Accounts Receivable
                    "Expense Voucher #$evid",
                    $request->entry_date ?? date('Y-m-d'),
                    \App\Models\Customer::find($request->vendor_id)
                );
            } else {
                // yahan vendor_type numeric (1,2,3) hai → matlab Account ID
                $account = Account::find($request->vendor_id);
                if ($account) {
                    // Credit Cash/Bank Side
                    $journalService->recordEntry(
                        $expense,
                        $account->id,
                        0, // Debit
                        $amount, // Credit Cash/Bank
                        "Expense Voucher #$evid",
                        $request->entry_date ?? date('Y-m-d')
                    );
                }
            }

            DB::commit();

            return back()->with('success', 'Expense Voucher saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function all_expense_vochers()
    {
        $receipts = \App\Models\ExpenseVoucher::orderBy('id', 'DESC')->get();

        foreach ($receipts as $voucher) {
            $partyName = '-';
            $typeLabel = '-';

            // 🧩 If type is numeric → Account Head / Account
            if (is_numeric($voucher->type)) {
                $accountHead = DB::table('account_heads')->where('id', $voucher->type)->first();
                $account = DB::table('accounts')->where('id', $voucher->party_id)->first();

                $typeLabel = $accountHead->name ?? 'Account';
                $partyName = $account->title ?? '-';
            } elseif ($voucher->type === 'vendor') {
                $vendor = DB::table('vendors')->where('id', $voucher->party_id)->first();
                $typeLabel = 'Vendor';
                $partyName = $vendor->name ?? '-';
            } elseif ($voucher->type === 'customer') {
                $customer = DB::table('customers')->where('id', $voucher->party_id)->first();
                $typeLabel = 'Customer';
                $partyName = $customer->customer_name ?? '-';
            } elseif ($voucher->type === 'walkin') {
                $walkin = DB::table('customers')
                    ->where('id', $voucher->party_id)
                    ->where('customer_type', 'Walking Customer')
                    ->first();
                $typeLabel = 'Walk-in';
                $partyName = $walkin->customer_name ?? '-';
            }

            // 🔗 Attach extra fields for Blade
            $voucher->type_label = $typeLabel;
            $voucher->party_name = $partyName;
        }

        return view('admin_panel.vochers.expense_vochers.all_expense_vochers', compact('receipts'));
    }

    public function expenseprint($id)
    {
        $voucher = \App\Models\ExpenseVoucher::findOrFail($id);

        // Decode JSON arrays safely
        $narrations = json_decode($voucher->narration_id, true) ?? [];
        $references = json_decode($voucher->reference_no, true) ?? [];
        $accountHeads = json_decode($voucher->row_account_head, true) ?? [];
        $accounts = json_decode($voucher->row_account_id, true) ?? [];
        $amounts = json_decode($voucher->amount, true) ?? [];

        // 🧾 Prepare detailed rows
        $rows = [];
        foreach ($narrations as $index => $narrId) {
            $narration = DB::table('narrations')->where('id', $narrId)->value('narration');
            $ref = $references[$index] ?? null;

            // Check if it is a new custom category or a legacy account
            $accId = $accounts[$index] ?? null;
            $expenseCategory = DB::table('expense_categories')->where('id', $accId)->first();
            if ($expenseCategory) {
                $accountHead = 'Expense Category';
                $accountName = $expenseCategory->name;
                $accountCode = $expenseCategory->code;
            } else {
                $accountHead = DB::table('account_heads')->where('id', $accountHeads[$index] ?? null)->value('name');
                $account = DB::table('accounts')->where('id', $accId)->first();
                $accountName = $account->title ?? null;
                $accountCode = $account->account_code ?? null;
            }

            $amount = (float) ($amounts[$index] ?? 0);

            $rows[] = [
                'narration' => $narration,
                'reference' => $ref,
                'account_head' => $accountHead,
                'account_name' => $accountName,
                'account_code' => $accountCode,
                'amount' => $amount,
            ];
        }

        // 🧩 Party Setup Based on Type
        $party = null;
        $previousBalance = 0;

        if (is_numeric($voucher->type)) {
            // ✅ Account Head type (numeric)
            $accountHead = DB::table('account_heads')->where('id', $voucher->type)->first();
            $account = DB::table('accounts')->where('id', $voucher->party_id)->first();

            if ($account) {
                $party = (object) [
                    'name' => $account->title ?? '—',
                    'address' => '—',
                    'phone' => $account->account_code ?? '—',
                    'head_name' => $accountHead->name ?? '—',
                ];
            }

            $previousBalance = $account->opening_balance ?? 0;
        } elseif ($voucher->type === 'vendor') {
            // ✅ Vendor Type
            $party = DB::table('vendors')->where('id', $voucher->party_id)->first();
            $previousBalance = DB::table('vendor_ledgers')
                ->where('vendor_id', $voucher->party_id)
                ->orderByDesc('id')
                ->value('closing_balance') ?? 0;
        } elseif ($voucher->type === 'customer') {
            // ✅ Customer Type
            $party = DB::table('customers')->where('id', $voucher->party_id)->first();
            $previousBalance = DB::table('customer_ledgers')
                ->where('customer_id', $voucher->party_id)
                ->orderByDesc('id')
                ->value('closing_balance') ?? 0;
        } elseif ($voucher->type === 'walkin') {
            // ✅ Walking Customer
            $party = DB::table('customers')
                ->where('id', $voucher->party_id)
                ->where('customer_type', 'Walking Customer')
                ->first();
        }

        return view('admin_panel.vochers.expense_vochers.print', compact('voucher', 'rows', 'party', 'previousBalance'));
    }

    public function fetchReceiptVouchers(Request $request)
    {

        // Fetch all accounts for the dropdown
        $accounts = \Illuminate\Support\Facades\DB::table('accounts')
            ->select('id', 'title', 'head_id')
            ->orderBy('title')
            ->get()
            ->map(function ($account) {
                // Get account head name
                $headName = \Illuminate\Support\Facades\DB::table('account_heads')
                    ->where('id', $account->head_id)
                    ->value('name');

                return [
                    'id' => $account->id,
                    'title' => $account->title,
                    'head_name' => $headName,
                    'display_name' => ($headName ? $headName.' - ' : '').$account->title,
                ];
            });

        return response()->json([
            'accounts' => $accounts,
        ]);
    }
}
