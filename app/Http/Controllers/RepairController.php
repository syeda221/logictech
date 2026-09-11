<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RepairOrder;
use App\Models\RepairOrderLog;
use App\Models\VoucherMaster;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepairController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Display a listing of repair jobs with KPI metrics and filters.
     */
    public function index(Request $request)
    {
        $query = RepairOrder::with(['customer', 'product', 'advanceAccount', 'finalAccount', 'receiver']);

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->whereIn('status', ['received', 'diagnosing', 'in_progress', 'waiting_parts']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by Search Query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('repair_no', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('serial_no', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by Date
        if ($request->filled('from_date')) {
            $query->whereDate('received_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('received_date', '<=', $request->to_date);
        }

        $repairs = $query->latest('id')->get();

        // 4 KPI Metrics
        $totalCount = RepairOrder::count();
        $activeCount = RepairOrder::whereIn('status', ['received', 'diagnosing', 'in_progress', 'waiting_parts'])->count();
        $completedCount = RepairOrder::where('status', 'completed')->count();
        $deliveredCount = RepairOrder::where('status', 'delivered')->count();
        $revenueCollected = (float) RepairOrder::sum('advance_paid') + (float) RepairOrder::sum('final_paid');
        $totalEstimated = (float) RepairOrder::sum('estimated_cost');

        $customers = Customer::orderBy('customer_name')->get();
        $accounts = Account::where('status', 1)->orderBy('title')->get();

        return view('admin_panel.repair.index', compact(
            'repairs',
            'customers',
            'accounts',
            'totalCount',
            'activeCount',
            'completedCount',
            'deliveredCount',
            'revenueCollected',
            'totalEstimated'
        ));
    }

    /**
     * Show the form for creating a new repair job.
     */
    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        
        // Filter accounts to Cash & Bank
        $accounts = Account::whereHas('head', function ($q) {
            $q->whereIn('name', ['Cash', 'Bank', 'cash', 'bank']);
        })->orWhereIn('id', [4, 5, 7, 8, 9])->where('status', 1)->orderBy('title')->get();

        // Finished Goods products for quick selection
        $products = Product::where('item_type', 'finish_goods')
            ->orderBy('item_name')
            ->get();

        // Generate next Repair Ticket #
        $lastId = RepairOrder::latest('id')->value('id') ?? 0;
        $nextRepairNo = 'REP-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        return view('admin_panel.repair.create', compact('customers', 'accounts', 'products', 'nextRepairNo'));
    }

    /**
     * Store a newly created repair job and generate customer receiving slip.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:150',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'nullable|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required|string|max:200',
            'brand_model' => 'nullable|string|max:100',
            'serial_no' => 'nullable|string|max:100',
            'accessories_received' => 'nullable',
            'problem_description' => 'required|string',
            'physical_condition' => 'nullable|string',
            'technician_notes' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'advance_paid' => 'nullable|numeric|min:0',
            'advance_account_id' => 'nullable|exists:accounts,id',
            'priority' => 'required|in:normal,urgent,high',
            'received_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
        ]);

        if (empty($validated['customer_id']) && empty($validated['customer_name'])) {
            return back()->withInput()->with('error', 'Please enter customer name or select an existing customer.');
        }

        $advancePaid = (float) ($validated['advance_paid'] ?? 0);
        if ($advancePaid > 0 && empty($validated['advance_account_id'])) {
            return back()->withInput()->with('error', 'Please select an account for the advance payment.');
        }

        try {
            DB::beginTransaction();

            // Next Repair No
            $lastId = RepairOrder::latest('id')->value('id') ?? 0;
            $repairNo = 'REP-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

            $accessories = $request->accessories_received;
            if (is_array($accessories)) {
                $accessories = implode(', ', array_filter($accessories));
            }

            $estimatedCost = (float) ($validated['estimated_cost'] ?? 0);
            $dueAmount = max(0, $estimatedCost - $advancePaid);

            // Create Repair Order
            $repair = RepairOrder::create([
                'repair_no' => $repairNo,
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'] ?? null,
                'product_id' => $validated['product_id'] ?? null,
                'item_name' => $validated['item_name'],
                'brand_model' => $validated['brand_model'] ?? null,
                'serial_no' => $validated['serial_no'] ?? null,
                'accessories_received' => $accessories,
                'problem_description' => $validated['problem_description'],
                'physical_condition' => $validated['physical_condition'] ?? null,
                'technician_notes' => $validated['technician_notes'] ?? null,
                'estimated_cost' => $estimatedCost,
                'service_charges' => 0,
                'parts_charges' => 0,
                'total_charges' => $estimatedCost,
                'advance_paid' => $advancePaid,
                'advance_account_id' => $advancePaid > 0 ? $validated['advance_account_id'] : null,
                'due_amount' => $dueAmount,
                'status' => 'received',
                'priority' => $validated['priority'],
                'received_date' => $validated['received_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'received_by' => Auth::id(),
            ]);

            // If advance payment was received, record Voucher
            if ($advancePaid > 0 && $repair->advance_account_id) {
                $this->recordPaymentVoucher(
                    $repair,
                    $repair->advance_account_id,
                    $advancePaid,
                    $repair->received_date->format('Y-m-d'),
                    "Advance for Repair #{$repair->repair_no} - {$repair->item_name}"
                );
            }

            // Create Audit Log
            RepairOrderLog::create([
                'repair_order_id' => $repair->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'to_status' => 'received',
                'amount' => $advancePaid,
                'account_id' => $repair->advance_account_id,
                'notes' => "Product received for repair. Advance collected: Rs. " . number_format($advancePaid, 2),
            ]);

            DB::commit();

            return redirect()->route('repair.show', $repair->id)
                ->with('success', "Repair Ticket #{$repair->repair_no} created successfully! Customer receipt is ready to print.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating repair order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified repair job / customer intake receipt.
     */
    public function show($id)
    {
        $repair = RepairOrder::with([
            'customer',
            'product',
            'advanceAccount',
            'finalAccount',
            'receiver',
            'deliverer',
            'logs.user',
            'logs.account'
        ])->findOrFail($id);

        $accounts = Account::whereHas('head', function ($q) {
            $q->whereIn('name', ['Cash', 'Bank', 'cash', 'bank']);
        })->orWhereIn('id', [4, 5, 7, 8, 9])->where('status', 1)->orderBy('title')->get();

        return view('admin_panel.repair.show', compact('repair', 'accounts'));
    }

    /**
     * Update repair status and diagnostic notes.
     */
    public function updateStatus(Request $request, $id)
    {
        $repair = RepairOrder::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:received,diagnosing,in_progress,waiting_parts,completed,cancelled',
            'technician_notes' => 'nullable|string',
            'log_note' => 'nullable|string',
        ]);

        $prevStatus = $repair->status;
        $repair->status = $validated['status'];

        if (!empty($validated['technician_notes'])) {
            $repair->technician_notes = $validated['technician_notes'];
        }

        $repair->save();

        // Audit Trail
        RepairOrderLog::create([
            'repair_order_id' => $repair->id,
            'user_id' => Auth::id(),
            'action' => 'status_updated',
            'from_status' => $prevStatus,
            'to_status' => $repair->status,
            'notes' => $validated['log_note'] ?? "Status changed from {$prevStatus} to {$repair->status}",
        ]);

        return back()->with('success', "Status updated to " . $repair->status_label);
    }

    /**
     * Deliver item to customer, finalize bill, and collect payment.
     */
    public function deliver(Request $request, $id)
    {
        $repair = RepairOrder::findOrFail($id);

        $validated = $request->validate([
            'service_charges' => 'required|numeric|min:0',
            'parts_charges' => 'nullable|numeric|min:0',
            'final_paid' => 'nullable|numeric|min:0',
            'final_account_id' => 'nullable|exists:accounts,id',
            'delivery_notes' => 'nullable|string',
        ]);

        $serviceCharges = (float) $validated['service_charges'];
        $partsCharges = (float) ($validated['parts_charges'] ?? 0);
        $totalCharges = $serviceCharges + $partsCharges;

        $finalPaid = (float) ($validated['final_paid'] ?? 0);
        if ($finalPaid > 0 && empty($validated['final_account_id'])) {
            return back()->with('error', 'Please select an account to deposit the final payment.');
        }

        $totalPaid = $repair->advance_paid + $finalPaid;
        $dueAmount = max(0, $totalCharges - $totalPaid);

        try {
            DB::beginTransaction();

            $prevStatus = $repair->status;

            $repair->update([
                'service_charges' => $serviceCharges,
                'parts_charges' => $partsCharges,
                'total_charges' => $totalCharges,
                'final_paid' => $finalPaid,
                'final_account_id' => $finalPaid > 0 ? $validated['final_account_id'] : null,
                'due_amount' => $dueAmount,
                'status' => 'delivered',
                'delivered_at' => now(),
                'delivered_by' => Auth::id(),
            ]);

            // Record final payment voucher if paid > 0
            if ($finalPaid > 0 && $repair->final_account_id) {
                $this->recordPaymentVoucher(
                    $repair,
                    $repair->final_account_id,
                    $finalPaid,
                    date('Y-m-d'),
                    "Final Settlement for Repair #{$repair->repair_no} - {$repair->item_name}"
                );
            }

            // Create Audit Log
            RepairOrderLog::create([
                'repair_order_id' => $repair->id,
                'user_id' => Auth::id(),
                'action' => 'delivered',
                'from_status' => $prevStatus,
                'to_status' => 'delivered',
                'amount' => $finalPaid,
                'account_id' => $repair->final_account_id,
                'notes' => "Item delivered to customer. Final Bill: Rs. " . number_format($totalCharges, 2) . " | Paid: Rs. " . number_format($finalPaid, 2) . ($dueAmount > 0 ? " | Due: Rs. " . number_format($dueAmount, 2) : " | Fully Paid"),
            ]);

            DB::commit();

            return back()->with('success', "Item delivered successfully! Final payment recorded.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing delivery: ' . $e->getMessage());
        }
    }

    /**
     * Delete / Cancel a repair order.
     */
    public function destroy($id)
    {
        $repair = RepairOrder::findOrFail($id);
        $repairNo = $repair->repair_no;
        $repair->delete();

        return redirect()->route('repair.index')->with('success', "Repair ticket #{$repairNo} has been deleted.");
    }

    /**
     * Print 80mm thermal receipt / job slip.
     */
    public function printThermal($id)
    {
        $repair = RepairOrder::with(['customer', 'product', 'advanceAccount', 'finalAccount', 'receiver', 'deliverer'])->findOrFail($id);
        return view('admin_panel.repair.print_thermal', compact('repair'));
    }

    /**
     * Print A4 Job Card & Customer Intake Receipt.
     */
    public function printA4($id)
    {
        $repair = RepairOrder::with(['customer', 'product', 'advanceAccount', 'finalAccount', 'receiver', 'deliverer'])->findOrFail($id);
        return view('admin_panel.repair.print_a4', compact('repair'));
    }

    /**
     * Helper to create double-entry receipt voucher
     */
    protected function recordPaymentVoucher(RepairOrder $repair, int $accountId, float $amount, string $date, string $narration)
    {
        if ($amount <= 0 || !$accountId) {
            return;
        }

        try {
            // Find AR or Sales/Revenue account for Credit side
            $creditAcc = Account::where('account_code', 'SALES')
                ->orWhere('title', 'like', '%Sales Revenue%')
                ->orWhere('account_code', 'AR')
                ->first();

            $creditAccId = $creditAcc ? $creditAcc->id : 6;

            $voucherData = [
                'voucher_type' => VoucherMaster::TYPE_RECEIPT,
                'date' => $date,
                'status' => VoucherMaster::STATUS_POSTED,
                'party_type' => $repair->customer_id ? Customer::class : null,
                'party_id' => $repair->customer_id ?? null,
                'remarks' => $narration,
            ];

            $lines = [
                [
                    'account_id' => $accountId,
                    'debit' => $amount,
                    'credit' => 0,
                    'narration' => "Cash In: {$narration}"
                ],
                [
                    'account_id' => $creditAccId,
                    'debit' => 0,
                    'credit' => $amount,
                    'narration' => "Repair Service Revenue"
                ]
            ];

            $this->voucherService->createVoucher($voucherData, $lines, Auth::id());

        } catch (\Exception $e) {
            \Log::warning("RepairController voucher creation failed: " . $e->getMessage());
        }
    }
}
