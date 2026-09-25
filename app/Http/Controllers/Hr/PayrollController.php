<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Hr\Attendance;
use App\Models\Hr\Employee;
use App\Models\Hr\Payroll;
use App\Services\PayrollCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollCalculationService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Display Payroll Summary Sheet (Excel-style)
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Get selected month (default to current Y-m, e.g., '2026-08')
        $month = $request->get('month', date('Y-m'));
        $prevMonth = Carbon::parse($month.'-01')->subMonth()->format('Y-m');

        // Fetch Active Financial Accounts for Payment Account Dropdown
        $accounts = \App\Models\Account::where('status', 1)->orderBy('title', 'asc')->get();

        // Get all active employees sorted by ID
        $employees = Employee::with(['designation', 'department', 'salaryStructure'])
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();

        // Get existing payroll records for this month
        $existingPayrolls = Payroll::where('month', $month)
            ->where('payroll_type', 'monthly')
            ->get()
            ->keyBy('employee_id');

        // Get most recent saved monthly payroll before selected $month for each employee
        $prevPayrolls = Payroll::where('month', '<', $month)
            ->where('payroll_type', 'monthly')
            ->orderBy('month', 'desc')
            ->get()
            ->unique('employee_id')
            ->keyBy('employee_id');

        $payrollItems = [];
        $totals = [
            'basic_salary' => 0,
            'salary_count' => 0,
            'overtime_hours' => 0,
            'overtime_days' => 0,
            'overtime_pay' => 0,
            'total_pay' => 0,
            'prev_balance' => 0,
            'advances' => 0,
            'other_allowance' => 0,
            'net_payable' => 0,
            'payment_this_month' => 0,
            'closing_balance' => 0,
        ];

        foreach ($employees as $index => $emp) {
            $p = $existingPayrolls->get($emp->id);
            $prevP = $prevPayrolls->get($emp->id);

            // Auto-fetch Basic Salary from Payroll or Employee Profile / Salary Structure
            $basicSalary = floatval(($p && floatval($p->basic_salary) > 0) ? $p->basic_salary : $emp->basic_salary);
            
            // Total outstanding advance loans (fallback if no previous saved payroll)
            $totalActiveLoans = floatval(\App\Models\Hr\Loan::where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->get()
                ->sum(function($l) { return $l->amount - $l->paid_amount; }));

            // Previous Balance: comes from previous saved payroll's closing_balance, or -$totalActiveLoans
            if ($prevP !== null) {
                $prevBalance = floatval($prevP->closing_balance ?? 0);
            } else {
                $prevBalance = -$totalActiveLoans;
            }

            if ($p) {
                $pDays = floatval($p->p_days ?? 30);
                $salaryCount = floatval($p->salary_count ?? (($basicSalary / 30) * $pDays));
                $otHours = floatval($p->overtime_hours ?? 0);
                $otDays = floatval($p->overtime_days ?? ($otHours / 9.0));
                $otPay = floatval($p->overtime_pay ?? round(($basicSalary / 30) * 1.5 * $otDays));
                $totalPay = floatval($p->total_pay ?? ($salaryCount + $otPay));
                
                if ($p->previous_advance_balance !== null && floatval($p->previous_advance_balance) != 0) {
                    $prevBalance = floatval($p->previous_advance_balance);
                }

                $advances = floatval($p->advances ?? $p->deductions ?? 0);
                $otherAllowance = floatval($p->other_allowance ?? $p->manual_allowances ?? 0);
                
                // Net Payable = Total Pay + Previous Balance - Advances + Other Allowance
                $netPayable = floatval($p->net_salary ?? ($totalPay + $prevBalance - $advances + $otherAllowance));
                $paymentThisMonth = floatval($p->payment_this_month ?? $netPayable);
                
                // Closing Balance = Net Payable - Payment This Month
                $closingBalance = floatval($p->closing_balance ?? ($netPayable - $paymentThisMonth));

                $paymentDate = $p->payment_date ? $p->payment_date->format('Y-m-d') : date('Y-m-04');
                $accountId = $p->account_id;
                $status = $p->status;
                $payrollId = $p->id;
            } else {
                // Default initial draft values with auto-fetched calculations
                $pDays = 30.0;
                $salaryCount = round(($basicSalary / 30) * $pDays);
                $otHours = 0.0;
                $otDays = 0.0;
                $otPay = 0.0;
                $totalPay = $salaryCount + $otPay;
                $advances = 0.0;
                $otherAllowance = 0.0;

                // Net Payable = Total Pay + Previous Balance - Advances + Other Allowance
                $netPayable = $totalPay + $prevBalance - $advances + $otherAllowance;
                $paymentThisMonth = $netPayable;
                
                // Closing Balance = Net Payable - Payment This Month
                $closingBalance = $netPayable - $paymentThisMonth;

                $paymentDate = date('Y-m-04');
                $accountId = $accounts->first()?->id;
                $status = 'draft';
                $payrollId = null;
            }

            $payrollItems[] = [
                'sn' => $index + 1,
                'employee_id' => $emp->id,
                'employee_name' => $emp->full_name,
                'designation' => $emp->designation->name ?? '',
                'basic_salary' => $basicSalary,
                'p_days' => $pDays,
                'salary_count' => $salaryCount,
                'overtime_hours' => $otHours,
                'overtime_days' => $otDays,
                'overtime_pay' => $otPay,
                'total_pay' => $totalPay,
                'prev_balance' => $prevBalance,
                'advances' => $advances,
                'active_loans' => $totalActiveLoans,
                'other_allowance' => $otherAllowance,
                'net_payable' => $netPayable,
                'payment_this_month' => $paymentThisMonth,
                'closing_balance' => $closingBalance,
                'payment_date' => $paymentDate,
                'account_id' => $accountId,
                'status' => $status,
                'payroll_id' => $payrollId,
            ];

            // Accumulate Totals
            $totals['basic_salary'] += $basicSalary;
            $totals['salary_count'] += $salaryCount;
            $totals['overtime_hours'] += $otHours;
            $totals['overtime_days'] += $otDays;
            $totals['overtime_pay'] += $otPay;
            $totals['total_pay'] += $totalPay;
            $totals['prev_balance'] += $prevBalance;
            $totals['advances'] += $advances;
            $totals['other_allowance'] += $otherAllowance;
            $totals['net_payable'] += $netPayable;
            $totals['payment_this_month'] += $paymentThisMonth;
            $totals['closing_balance'] += $closingBalance;
        }

        $payrolls = Payroll::with(['employee.designation', 'employee.department'])
            ->where('month', $month)
            ->latest()
            ->paginate(50);

        return view('hr.payroll.index', compact(
            'payrollItems',
            'totals',
            'month',
            'employees',
            'accounts',
            'payrolls'
        ))->with('activeTab', 'all');
    }

    /**
     * Bulk save monthly payroll summary sheet
     */
    public function saveSheet(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.create') && ! auth()->user()->can('hr.payroll.edit')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'rows' => 'required|array',
            'rows.*.employee_id' => 'required|exists:hr_employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $month = $request->month;

            foreach ($request->rows as $row) {
                $employeeId = $row['employee_id'];
                $basicSalary = floatval($row['basic_salary'] ?? 0);
                $pDays = floatval($row['p_days'] ?? 30);
                $salaryCount = floatval($row['salary_count'] ?? (($basicSalary / 30) * $pDays));
                $otHours = floatval($row['overtime_hours'] ?? 0);
                $otDays = floatval($row['overtime_days'] ?? ($otHours / 9.0));
                $otPay = floatval($row['overtime_pay'] ?? round(($basicSalary / 30) * 1.5 * $otDays));
                $totalPay = floatval($row['total_pay'] ?? ($salaryCount + $otPay));
                $prevBalance = floatval($row['previous_balance'] ?? $row['prev_balance'] ?? 0);
                $advances = floatval($row['advances'] ?? 0);
                $otherAllowance = floatval($row['other_allowance'] ?? 0);
                $netPayable = floatval($row['net_payable'] ?? ($totalPay + $prevBalance - $advances + $otherAllowance));
                $paymentThisMonth = floatval($row['payment_this_month'] ?? $netPayable);
                $closingBalance = floatval($row['closing_balance'] ?? ($netPayable - $paymentThisMonth));
                $paymentDate = !empty($row['payment_date']) ? $row['payment_date'] : date('Y-m-d');
                $accountId = !empty($row['account_id']) ? $row['account_id'] : ($request->account_id ?? null);

                // Update employee basic salary if changed
                $employee = Employee::find($employeeId);
                if ($employee && $basicSalary > 0 && $employee->basic_salary != $basicSalary) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('hr_employees', 'basic_salary')) {
                        $employee->update(['basic_salary' => $basicSalary]);
                    }
                    $structure = $employee->salaryStructure;
                    if ($structure) {
                        $structure->update(['base_salary' => $basicSalary]);
                    } else {
                        SalaryStructure::create([
                            'employee_id' => $employee->id,
                            'base_salary' => $basicSalary,
                            'salary_type' => 'monthly',
                        ]);
                    }
                }

                // Create or Update Payroll Record
                $payroll = Payroll::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'month' => $month,
                        'payroll_type' => 'monthly',
                    ],
                    [
                        'basic_salary' => $basicSalary,
                        'p_days' => $pDays,
                        'salary_count' => $salaryCount,
                        'overtime_hours' => $otHours,
                        'overtime_days' => $otDays,
                        'overtime_pay' => $otPay,
                        'total_pay' => $totalPay,
                        'previous_advance_balance' => $prevBalance,
                        'advances' => $advances,
                        'other_allowance' => $otherAllowance,
                        'gross_salary' => $totalPay,
                        'deductions' => $advances,
                        'allowances' => $otherAllowance,
                        'manual_allowances' => $otherAllowance,
                        'manual_deductions' => $advances,
                        'net_salary' => $netPayable,
                        'payment_this_month' => $paymentThisMonth,
                        'closing_balance' => $closingBalance,
                        'payment_date' => $paymentDate,
                        'account_id' => $accountId,
                        'status' => 'paid',
                    ]
                );

                // Deduct from Loan/Advance if advances deducted in payroll
                if ($advances > 0) {
                    $loans = \App\Models\Hr\Loan::where('employee_id', $employeeId)
                        ->where('status', 'approved')
                        ->whereRaw('paid_amount < amount')
                        ->get();

                    $remDeduction = $advances;
                    foreach ($loans as $loan) {
                        if ($remDeduction <= 0) break;
                        $unpaid = $loan->amount - $loan->paid_amount;
                        $payAmount = min($remDeduction, $unpaid);
                        $loan->increment('paid_amount', $payAmount);
                        $remDeduction -= $payAmount;
                    }
                }

                // Record Financial Account History if Payment Account is specified
                if ($accountId && $paymentThisMonth > 0) {
                    $account = \App\Models\Account::find($accountId);
                    if ($account) {
                        $oldBalance = floatval($account->current_balance);
                        $newBalance = $oldBalance - $paymentThisMonth;
                        $account->update(['current_balance' => $newBalance]);

                        // Create Account History Entry for Payroll Expense
                        \App\Models\AccountHistory::create([
                            'account_id' => $account->id,
                            'old_balance' => $oldBalance,
                            'new_balance' => $newBalance,
                            'user_id' => auth()->id(),
                            'user_name' => auth()->user()->name ?? 'System',
                            'note' => "Salary Paid: Rs. " . number_format($paymentThisMonth, 2) . " to {$employee->full_name} for {$month} (Ref Payroll #{$payroll->id})",
                        ]);

                        // Post General Ledger Payment Voucher if VoucherService exists
                        try {
                            if (class_exists(\App\Services\VoucherService::class)) {
                                $voucherService = app(\App\Services\VoucherService::class);
                                $salariesAccount = \App\Models\Account::where('title', 'like', '%Salary%')
                                    ->orWhere('title', 'like', '%Payroll%')
                                    ->first() ?? $account;

                                $voucherData = [
                                    'voucher_type' => \App\Models\VoucherMaster::TYPE_PAYMENT ?? 'payment',
                                    'date' => $paymentDate,
                                    'status' => \App\Models\VoucherMaster::STATUS_POSTED ?? 'posted',
                                    'remarks' => "Salary Paid to {$employee->full_name} for {$month} (Payroll #{$payroll->id})",
                                ];

                                $lines = [
                                    [
                                        'account_id' => $salariesAccount->id,
                                        'debit' => $paymentThisMonth,
                                        'credit' => 0,
                                        'narration' => "Salary for {$employee->full_name} ({$month})",
                                    ],
                                    [
                                        'account_id' => $account->id,
                                        'debit' => 0,
                                        'credit' => $paymentThisMonth,
                                        'narration' => "Paid from {$account->title}",
                                    ],
                                ];

                                $voucherService->createVoucher($voucherData, $lines, auth()->id());
                            }
                        } catch (\Exception $ex) {
                            \Log::warning("Voucher creation for salary payment failed: " . $ex->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => 'Payroll Sheet saved and payments recorded successfully.',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }

    /**
     * Give Advance Salary / Loan to Employee
     */
    public function giveAdvance(Request $request)
    {
        if (auth()->check() && ! auth()->user()->hasRole('Super Admin') && ! auth()->user()->can('hr.payroll.create') && ! auth()->user()->can('hr.payroll.edit') && ! auth()->user()->can('hr.payroll.generate')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:hr_employees,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'reason' => 'nullable|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($request->employee_id);
            $amount = floatval($request->amount);

            // Create Loan / Advance Record
            $loan = \App\Models\Hr\Loan::create([
                'employee_id' => $employee->id,
                'amount' => $amount,
                'installment_amount' => $amount,
                'status' => 'approved',
                'reason' => $request->reason ?? 'Advance Salary Paid',
                'paid_amount' => 0,
            ]);

            // If Financial Account selected, record payout from account
            if ($request->account_id) {
                $account = \App\Models\Account::find($request->account_id);
                if ($account) {
                    $oldBalance = floatval($account->current_balance);
                    $newBalance = $oldBalance - $amount;
                    $account->update(['current_balance' => $newBalance]);

                    // Log Account History Audit Entry
                    \App\Models\AccountHistory::create([
                        'account_id' => $account->id,
                        'old_balance' => $oldBalance,
                        'new_balance' => $newBalance,
                        'user_id' => auth()->id(),
                        'user_name' => auth()->user()->name ?? 'System',
                        'note' => "Advance Salary Paid: Rs. " . number_format($amount, 2) . " to {$employee->full_name}" . ($request->reason ? " (Reason: {$request->reason})" : ""),
                    ]);

                    // Post General Ledger Payment Voucher if VoucherService exists
                    try {
                        if (class_exists(\App\Services\VoucherService::class)) {
                            $voucherService = app(\App\Services\VoucherService::class);
                            $salariesAccount = \App\Models\Account::where('title', 'like', '%Salary%')
                                ->orWhere('title', 'like', '%Advance%')
                                ->orWhere('title', 'like', '%Payroll%')
                                ->first() ?? $account;

                            $voucherData = [
                                'voucher_type' => \App\Models\VoucherMaster::TYPE_PAYMENT ?? 'payment',
                                'date' => $request->date,
                                'status' => \App\Models\VoucherMaster::STATUS_POSTED ?? 'posted',
                                'remarks' => "Advance Salary Paid to {$employee->full_name}" . ($request->reason ? " - {$request->reason}" : ""),
                            ];

                            $lines = [
                                [
                                    'account_id' => $salariesAccount->id,
                                    'debit' => $amount,
                                    'credit' => 0,
                                    'narration' => "Advance Salary to {$employee->full_name}",
                                ],
                                [
                                    'account_id' => $account->id,
                                    'debit' => 0,
                                    'credit' => $amount,
                                    'narration' => "Paid from {$account->title}",
                                ],
                            ];

                            $voucherService->createVoucher($voucherData, $lines, auth()->id());
                        }
                    } catch (\Exception $ex) {
                        \Log::warning("Voucher creation for advance salary failed: " . $ex->getMessage());
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => "Rs. ".number_format($amount)." Advance Salary issued to {$employee->full_name} successfully.",
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }

    /**
     * View Payment History of all paid payrolls
     */
    public function history(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Payroll::with(['employee.designation', 'employee.department', 'account'])
            ->where('status', 'paid');

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        $payrolls = $query->latest('payment_date')->paginate(30);
        $employees = Employee::all();
        $accounts = \App\Models\Account::all();

        return view('hr.payroll.history', compact('payrolls', 'employees', 'accounts'));
    }

    /**
     * View Printable Payslip for individual employee
     */
    public function payslip($id)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        $payroll = Payroll::with(['employee.designation', 'employee.department', 'account'])->findOrFail($id);

        return view('hr.payroll.payslip', compact('payroll'));
    }

    /**
     * Print printable payroll summary sheet matching Excel design
     */
    public function printSummary(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        $month = $request->get('month', date('Y-m'));

        $employees = Employee::with(['designation', 'department', 'salaryStructure'])
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();

        $existingPayrolls = Payroll::where('month', $month)
            ->where('payroll_type', 'monthly')
            ->get()
            ->keyBy('employee_id');

        $prevPayrolls = Payroll::where('month', '<', $month)
            ->where('payroll_type', 'monthly')
            ->orderBy('month', 'desc')
            ->get()
            ->unique('employee_id')
            ->keyBy('employee_id');

        $payrollItems = [];
        $totals = [
            'basic_salary' => 0,
            'salary_count' => 0,
            'overtime_hours' => 0,
            'overtime_days' => 0,
            'overtime_pay' => 0,
            'total_pay' => 0,
            'prev_balance' => 0,
            'advances' => 0,
            'other_allowance' => 0,
            'net_payable' => 0,
            'payment_this_month' => 0,
            'closing_balance' => 0,
        ];

        foreach ($employees as $index => $emp) {
            $p = $existingPayrolls->get($emp->id);
            $prevP = $prevPayrolls->get($emp->id);
            $basicSalary = floatval(($p && floatval($p->basic_salary) > 0) ? $p->basic_salary : $emp->basic_salary);

            $totalActiveLoans = floatval(\App\Models\Hr\Loan::where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->get()
                ->sum(function($l) { return $l->amount - $l->paid_amount; }));

            if ($prevP !== null) {
                $prevBalance = floatval($prevP->closing_balance ?? 0);
            } else {
                $prevBalance = -$totalActiveLoans;
            }

            if ($p) {
                $pDays = floatval($p->p_days ?? 30);
                $salaryCount = floatval($p->salary_count ?? (($basicSalary / 30) * $pDays));
                $otHours = floatval($p->overtime_hours ?? 0);
                $otDays = floatval($p->overtime_days ?? ($otHours / 9.0));
                $otPay = floatval($p->overtime_pay ?? round(($basicSalary / 30) * 1.5 * $otDays));
                $totalPay = floatval($p->total_pay ?? ($salaryCount + $otPay));

                if ($p->previous_advance_balance !== null && floatval($p->previous_advance_balance) != 0) {
                    $prevBalance = floatval($p->previous_advance_balance);
                }

                $advances = floatval($p->advances ?? $p->deductions ?? 0);
                $otherAllowance = floatval($p->other_allowance ?? $p->manual_allowances ?? 0);
                $netPayable = floatval($p->net_salary ?? ($totalPay + $prevBalance - $advances + $otherAllowance));
                $paymentThisMonth = floatval($p->payment_this_month ?? $netPayable);
                $closingBalance = floatval($p->closing_balance ?? ($netPayable - $paymentThisMonth));
                $paymentDate = $p->payment_date ? $p->payment_date->format('d-M-y') : date('d-M-y');
            } else {
                $pDays = 30.0;
                $salaryCount = round(($basicSalary / 30) * $pDays);
                $otHours = 0.0;
                $otDays = 0.0;
                $otPay = 0.0;
                $totalPay = $salaryCount + $otPay;
                $advances = 0.0;
                $otherAllowance = 0.0;
                $netPayable = $totalPay + $prevBalance - $advances + $otherAllowance;
                $paymentThisMonth = $netPayable;
                $closingBalance = $netPayable - $paymentThisMonth;
                $paymentDate = date('d-M-y');
            }

            $payrollItems[] = [
                'sn' => $index + 1,
                'employee_name' => $emp->full_name,
                'basic_salary' => $basicSalary,
                'p_days' => $pDays,
                'salary_count' => $salaryCount,
                'overtime_hours' => $otHours,
                'overtime_days' => $otDays,
                'overtime_pay' => $otPay,
                'total_pay' => $totalPay,
                'prev_balance' => $prevBalance,
                'advances' => $advances,
                'other_allowance' => $otherAllowance,
                'net_payable' => $netPayable,
                'payment_this_month' => $paymentThisMonth,
                'closing_balance' => $closingBalance,
                'payment_date' => $paymentDate,
            ];

            $totals['basic_salary'] += $basicSalary;
            $totals['salary_count'] += $salaryCount;
            $totals['overtime_hours'] += $otHours;
            $totals['overtime_days'] += $otDays;
            $totals['overtime_pay'] += $otPay;
            $totals['total_pay'] += $totalPay;
            $totals['prev_balance'] += $prevBalance;
            $totals['advances'] += $advances;
            $totals['other_allowance'] += $otherAllowance;
            $totals['net_payable'] += $netPayable;
            $totals['payment_this_month'] += $paymentThisMonth;
            $totals['closing_balance'] += $closingBalance;
        }

        return view('hr.payroll.print', compact('payrollItems', 'totals', 'month'));
    }

    /**
     * Display Employee Ledger Statement & Transaction Report
     */
    public function employeeLedger(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        if ($employees->isEmpty()) {
            $employees = Employee::orderBy('first_name')->get();
        }

        $employeeId = $request->get('employee_id');
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-12-31'));

        $selectedEmployee = $employeeId ? Employee::with(['designation', 'department'])->find($employeeId) : null;

        $ledgerEntries = collect([]);
        $summary = [
            'opening_balance' => 0,
            'total_advances_deducted' => 0,
            'total_salary_earned' => 0,
            'total_other_allowances' => 0,
            'total_net_paid' => 0,
            'closing_balance' => 0,
        ];

        if ($selectedEmployee) {
            $payrolls = Payroll::where('employee_id', $selectedEmployee->id)->where('status', 'paid')->get();

            foreach ($payrolls as $p) {
                $dateStr = $p->payment_date ? $p->payment_date->format('Y-m-d') : ($p->updated_at ? $p->updated_at->format('Y-m-d') : date('Y-m-d'));
                $monthName = \Carbon\Carbon::parse($p->month.'-01')->format('M Y');
                $advDeducted = floatval($p->advances ?: $p->deductions ?: 0);
                $otherAllowance = floatval($p->other_allowance ?: $p->manual_allowances ?: 0);
                $salaryEarned = floatval($p->total_pay ?: $p->gross_salary ?: $p->basic_salary);
                $netPaid = floatval($p->payment_this_month ?: $p->net_salary);

                $ledgerEntries->push([
                    'date' => $dateStr,
                    'raw_date' => $p->payment_date ? $p->payment_date->timestamp : ($p->updated_at ? $p->updated_at->timestamp : 0),
                    'type' => "Salary Paid ({$monthName})",
                    'description' => "Monthly Salary for {$monthName}",
                    'advance_given' => 0,
                    'advance_deducted' => $advDeducted,
                    'other_allowance' => $otherAllowance,
                    'salary_earned' => $salaryEarned,
                    'net_paid' => $netPaid,
                    'ref' => "PAY-#{$p->id}",
                ]);
            }

            $ledgerEntries = $ledgerEntries->sortBy('raw_date')->values();
            $filteredEntries = collect([]);
            $runningBalance = 0;

            foreach ($ledgerEntries as $entry) {
                $eDate = $entry['date'];
                $balanceChange = ($entry['net_paid'] + $entry['advance_deducted']) - ($entry['salary_earned'] + $entry['other_allowance']);

                if ($eDate < $startDate) {
                    $summary['opening_balance'] += $balanceChange;
                    $runningBalance += $balanceChange;
                } elseif ($eDate >= $startDate && $eDate <= $endDate) {
                    $runningBalance += $balanceChange;
                    $entry['running_balance'] = $runningBalance;
                    $filteredEntries->push($entry);

                    $summary['total_advances_deducted'] += $entry['advance_deducted'];
                    $summary['total_salary_earned'] += $entry['salary_earned'];
                    $summary['total_other_allowances'] += $entry['other_allowance'];
                    $summary['total_net_paid'] += $entry['net_paid'];
                }
            }

            $summary['closing_balance'] = $runningBalance;
            $ledgerEntries = $filteredEntries;
        }

        return view('hr.payroll.employee_ledger', compact(
            'employees',
            'employeeId',
            'selectedEmployee',
            'startDate',
            'endDate',
            'ledgerEntries',
            'summary'
        ));
    }

    /**
     * Print Printable Employee Ledger Statement
     */
    public function printEmployeeLedger(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        $employeeId = $request->get('employee_id');
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-12-31'));

        $selectedEmployee = $employeeId ? Employee::with(['designation', 'department'])->find($employeeId) : null;

        $ledgerEntries = collect([]);
        $summary = [
            'opening_balance' => 0,
            'total_advances_deducted' => 0,
            'total_salary_earned' => 0,
            'total_other_allowances' => 0,
            'total_net_paid' => 0,
            'closing_balance' => 0,
        ];

        if ($selectedEmployee) {
            $payrolls = Payroll::where('employee_id', $selectedEmployee->id)->where('status', 'paid')->get();

            foreach ($payrolls as $p) {
                $dateStr = $p->payment_date ? $p->payment_date->format('Y-m-d') : ($p->updated_at ? $p->updated_at->format('Y-m-d') : date('Y-m-d'));
                $monthName = \Carbon\Carbon::parse($p->month.'-01')->format('M Y');
                $advDeducted = floatval($p->advances ?: $p->deductions ?: 0);
                $otherAllowance = floatval($p->other_allowance ?: $p->manual_allowances ?: 0);
                $salaryEarned = floatval($p->total_pay ?: $p->gross_salary ?: $p->basic_salary);
                $netPaid = floatval($p->payment_this_month ?: $p->net_salary);

                $ledgerEntries->push([
                    'date' => $dateStr,
                    'raw_date' => $p->payment_date ? $p->payment_date->timestamp : ($p->updated_at ? $p->updated_at->timestamp : 0),
                    'type' => "Salary Paid ({$monthName})",
                    'description' => "Monthly Salary for {$monthName}",
                    'advance_given' => 0,
                    'advance_deducted' => $advDeducted,
                    'other_allowance' => $otherAllowance,
                    'salary_earned' => $salaryEarned,
                    'net_paid' => $netPaid,
                    'ref' => "PAY-#{$p->id}",
                ]);
            }

            $ledgerEntries = $ledgerEntries->sortBy('raw_date')->values();
            $filteredEntries = collect([]);
            $runningBalance = 0;

            foreach ($ledgerEntries as $entry) {
                $eDate = $entry['date'];
                $balanceChange = ($entry['net_paid'] + $entry['advance_deducted']) - ($entry['salary_earned'] + $entry['other_allowance']);

                if ($eDate < $startDate) {
                    $summary['opening_balance'] += $balanceChange;
                    $runningBalance += $balanceChange;
                } elseif ($eDate >= $startDate && $eDate <= $endDate) {
                    $runningBalance += $balanceChange;
                    $entry['running_balance'] = $runningBalance;
                    $filteredEntries->push($entry);

                    $summary['total_advances_deducted'] += $entry['advance_deducted'];
                    $summary['total_salary_earned'] += $entry['salary_earned'];
                    $summary['total_other_allowances'] += $entry['other_allowance'];
                    $summary['total_net_paid'] += $entry['net_paid'];
                }
            }

            $summary['closing_balance'] = $runningBalance;
            $ledgerEntries = $filteredEntries;
        }

        return view('hr.payroll.print_employee_ledger', compact(
            'selectedEmployee',
            'startDate',
            'endDate',
            'ledgerEntries',
            'summary'
        ));
    }

    /**
     * Show monthly payrolls only
     */
    public function monthly(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Payroll::with(['employee.designation', 'employee.department'])
            ->monthly();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        $payrolls = $query->latest()->paginate(12);
        $employees = Employee::whereHas('salaryStructure', function ($q) {
            $q->whereIn('salary_type', ['salary', 'both']);
        })->get();

        return view('hr.payroll.index', compact('payrolls', 'employees'))->with('activeTab', 'monthly');
    }

    /**
     * Show daily payrolls only
     */
    public function daily(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Payroll::with(['employee.designation', 'employee.department'])
            ->daily();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        $payrolls = $query->latest()->paginate(12);
        $employees = Employee::whereHas('salaryStructure', function ($q) {
            $q->where('use_daily_wages', true);
        })->get();

        return view('hr.payroll.index', compact('payrolls', 'employees'))->with('activeTab', 'daily');
    }

    /**
     * Get detailed payroll breakdown
     */
    public function details($id)
    {
        if (! auth()->user()->can('hr.payroll.view')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $payroll = Payroll::with(['employee.designation', 'details', 'reviewer'])->findOrFail($id);

        // Format payroll period based on type
        $payrollPeriod = $this->formatPayrollPeriod($payroll);

        // Get allowance details
        $allowanceDetails = $payroll->details()->where('type', 'allowance')->get()->map(function ($detail) {
            return [
                'name' => $detail->name,
                'amount' => $detail->amount,
                'description' => $detail->description,
                'calculation_type' => $detail->description ? 'fixed' : 'fixed', // Can enhance this later
            ];
        });

        // Get deduction details (non-attendance)
        $deductionDetails = $payroll->details()->where('type', 'deduction')->get()->map(function ($detail) {
            return [
                'name' => $detail->name,
                'amount' => $detail->amount,
                'description' => $detail->description,
            ];
        });

        // Get attendance breakdown for the payroll period
        $attendanceBreakdown = $this->getAttendanceBreakdown($payroll);

        return response()->json([
            'payroll' => $payroll,
            'payroll_period' => $payrollPeriod,
            'breakdown' => [
                'earnings' => [
                    'basic_salary' => $payroll->basic_salary,
                    'allowances' => $payroll->allowances,
                    'manual_allowances' => $payroll->manual_allowances,
                    'total' => $payroll->gross_salary,
                ],
                'deductions' => [
                    'fixed_deductions' => $payroll->deductions,
                    'attendance_deductions' => $payroll->attendance_deductions,
                    'carried_forward' => $payroll->carried_forward_deduction,
                    'carried_forward_to_next' => $payroll->carried_forward_to_next,
                    'manual_deductions' => $payroll->manual_deductions,
                    'total' => $payroll->total_deductions,
                ],
                'net_payable' => $payroll->net_salary,
            ],
            'allowance_details' => $allowanceDetails,
            'deduction_details' => $deductionDetails,
            'attendance_breakdown' => $attendanceBreakdown,
        ]);
    }

    /**
     * Format payroll period based on payroll type
     */
    private function formatPayrollPeriod($payroll): array
    {
        if ($payroll->payroll_type === 'daily') {
            // For daily: Display Date, Month, and Year (e.g., "15 March 2026")
            $date = \Carbon\Carbon::parse($payroll->month);
            return [
                'type' => 'daily',
                'formatted' => $date->format('d/m/Y'),
                'day' => $date->format('d'),
                'month' => $date->format('F'),
                'year' => $date->format('Y'),
            ];
        } else {
            // For monthly: Display Month and Year only (e.g., "March 2026")
            $date = \Carbon\Carbon::parse($payroll->month . '-01');
            return [
                'type' => 'monthly',
                'formatted' => $date->format('F Y'),
                'month' => $date->format('F'),
                'year' => $date->format('Y'),
            ];
        }
    }

    /**
     * Get attendance breakdown for payroll period
     */
    private function getAttendanceBreakdown($payroll): array
    {
        $employee = $payroll->employee;
        
        // Get salary structure for deduction policy
        $structure = $this->payrollService->getEffectiveSalaryStructure($employee);
        $policy = $structure ? ($structure->attendance_deduction_policy ?? []) : [];
        $perDayDeduction = $structure ? ($structure->leave_salary_per_day ?? 0) : 0;
        
        if ($payroll->payroll_type === 'monthly') {
            // For monthly payroll, get attendance stats for the entire month
            $startDate = \Carbon\Carbon::parse($payroll->month . '-01')->startOfMonth();
            $endDate = \Carbon\Carbon::parse($payroll->month . '-01')->endOfMonth();
            
            $totalWorkingDays = $this->getWorkingDaysInRange($startDate, $endDate);
            
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('date', 'asc')
                ->get();
            
            // Check if attendance data is complete
            $hasData = $attendances->count() > 0;
            
            $daysPresent = $attendances->filter(fn($att) => strtolower($att->status) === 'present')->count();
            $daysAbsent = $attendances->filter(fn($att) => strtolower($att->status) === 'absent')->count();
            $lateCheckIns = $attendances->where('is_late', true)->count();
            $earlyCheckOuts = $attendances->where('is_early_leave', true)->count(); // Fixed: is_early_leave
            
            // Calculate deduction breakdown
            $lateMinutesTotal = $attendances->sum('late_minutes');
            $earlyMinutesTotal = $attendances->sum('early_leave_minutes'); // Fixed: early_leave_minutes
            
            // Calculate actual deduction amounts
            $absenceDeduction = $daysAbsent * $perDayDeduction;
            
            $lateDeduction = 0;
            $latePenalty = $policy['late_penalty_per_instance'] ?? 0;
            if ($latePenalty > 0) {
                $lateDeduction = $lateCheckIns * $latePenalty;
            }
            
            $earlyDeduction = 0;
            $earlyPenalty = $policy['early_penalty_per_instance'] ?? 0;
            if ($earlyPenalty > 0) {
                $earlyDeduction = $earlyCheckOuts * $earlyPenalty;
            }
            
            // Build detailed records for each issue type
            $absentDays = $attendances->filter(fn($att) => strtolower($att->status) === 'absent')
                ->map(function ($att) use ($perDayDeduction) {
                    return [
                        'date' => \Carbon\Carbon::parse($att->date)->format('d/m/Y'),
                        'day' => \Carbon\Carbon::parse($att->date)->format('l'),
                        'deduction' => $perDayDeduction,
                    ];
                })->values()->toArray();
            
            $lateDays = $attendances->where('is_late', true)->map(function ($att) use ($latePenalty) {
                return [
                    'date' => \Carbon\Carbon::parse($att->date)->format('d/m/Y'),
                    'day' => \Carbon\Carbon::parse($att->date)->format('l'),
                    'check_in' => $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('h:i A') : 'N/A', // Fixed: clock_in
                    'late_minutes' => $att->late_minutes ?? 0,
                    'deduction' => $latePenalty,
                ];
            })->values()->toArray();
            
            $earlyDays = $attendances->where('is_early_leave', true)->map(function ($att) use ($earlyPenalty) { // Fixed: is_early_leave
                return [
                    'date' => \Carbon\Carbon::parse($att->date)->format('d/m/Y'),
                    'day' => \Carbon\Carbon::parse($att->date)->format('l'),
                    'check_out' => $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('h:i A') : 'N/A', // Fixed: clock_out
                    'early_minutes' => $att->early_leave_minutes ?? 0, // Fixed: early_leave_minutes
                    'deduction' => $earlyPenalty,
                ];
            })->values()->toArray();
            
            return [
                'has_data' => $hasData,
                'data_message' => $hasData ? null : 'Attendance data incomplete for this period',
                'has_attendance_deductions' => $payroll->attendance_deductions > 0,
                'total_working_days' => $totalWorkingDays,
                'days_present' => $daysPresent,
                'days_absent' => $daysAbsent,
                'late_check_ins' => $lateCheckIns,
                'early_check_outs' => $earlyCheckOuts,
                'late_minutes_total' => $lateMinutesTotal,
                'early_minutes_total' => $earlyMinutesTotal,
                'total_deduction' => $payroll->attendance_deductions,
                'deduction_details' => [
                    'absence_deduction' => $absenceDeduction,
                    'late_deduction' => $lateDeduction,
                    'early_deduction' => $earlyDeduction,
                    'per_day_rate' => $perDayDeduction,
                    'late_penalty_rate' => $latePenalty,
                    'early_penalty_rate' => $earlyPenalty,
                ],
                // Detailed day-by-day records
                'absent_records' => $absentDays,
                'late_records' => $lateDays,
                'early_records' => $earlyDays,
            ];
        } else {
            // For daily payroll
            $date = \Carbon\Carbon::parse($payroll->month);
            
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $date->format('Y-m-d'))
                ->first();
            
            if ($attendance) {
                // Get specific deduction amounts from saved details
                $lateDeductionAmount = $payroll->details
                    ->filter(fn($d) => str_contains(strtolower($d->name), 'late check-in'))
                    ->sum('amount');
                    
                $earlyDeductionAmount = $payroll->details
                    ->filter(fn($d) => str_contains(strtolower($d->name), 'early leave') || str_contains(strtolower($d->name), 'early check-out'))
                    ->sum('amount');

                return [
                    'has_data' => true,
                    'has_attendance_deductions' => $payroll->attendance_deductions > 0,
                    'date' => $date->format('Y-m-d'),
                    'formatted_date' => $date->format('d/m/Y'),
                    'day' => $date->format('l'),
                    'status' => $attendance->status,
                    'check_in' => $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') : 'N/A',
                    'check_out' => $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') : 'N/A',
                    'is_late' => $attendance->is_late,
                    'is_early_out' => $attendance->is_early_leave,
                    'late_minutes' => $attendance->late_minutes ?? 0,
                    'early_checkout_minutes' => $attendance->early_leave_minutes ?? 0,
                    'total_deduction' => $payroll->attendance_deductions,
                    'late_deduction_amount' => $lateDeductionAmount,
                    'early_deduction_amount' => $earlyDeductionAmount,
                ];
            }
            
            return [
                'has_data' => false,
                'data_message' => 'Attendance data incomplete for this period',
                'has_attendance_deductions' => false,
                'date' => $date->format('Y-m-d'),
                'status' => 'No attendance record',
            ];
        }
    }



    /**
     * Calculate working days in a date range (excluding weekends)
     */
    private function getWorkingDaysInRange($startDate, $endDate): int
    {
        $workingDays = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            // Exclude Saturdays (6) and Sundays (0)
            if (!in_array($current->dayOfWeek, [0, 6])) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        return $workingDays;
    }

    /**
     * Generate payroll (manual or single employee)
     */
    public function generate(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.create')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:hr_employees,id',
            'month' => 'required',
            'payroll_type' => 'required|in:monthly,daily',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $employee = Employee::with('salaryStructure')->findOrFail($request->employee_id);

            // Check if payroll already exists
            $exists = Payroll::where('employee_id', $employee->id)
                ->where('month', $request->month)
                ->where('payroll_type', $request->payroll_type)
                ->exists();

            if ($exists) {
                return response()->json([
                    'errors' => ['month' => ['Payroll already generated for this period.']],
                ], 422);
            }

            if ($request->payroll_type === 'monthly') {
                $payrollData = $this->payrollService->calculateMonthlyPayroll($employee, $request->month);
            } else { // daily
                // For manual daily payroll generation, we need a date
                $validator = Validator::make($request->all(), [
                    'date' => 'required|date',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $request->date)
                    ->first();

                if (! $attendance || ! $attendance->clock_out) {
                    return response()->json([
                        'errors' => ['date' => ['No completed attendance record found for this date.']],
                    ], 422);
                }

                $payrollData = $this->payrollService->calculateDailyPayroll($employee, $attendance);
            }

            // Create payroll
            $payroll = Payroll::create(array_merge(
                ['employee_id' => $employee->id],
                Arr::except($payrollData, ['allowance_details', 'deduction_details', 'new_pending_deductions'])
            ));

            // Save detailed breakdown
            $this->payrollService->savePayrollDetails(
                $payroll,
                $payrollData['allowance_details'] ?? [],
                $payrollData['deduction_details'] ?? []
            );

            // Update pending deductions for daily payroll
            if ($request->payroll_type === 'daily') {
                $this->payrollService->updatePendingDeductions(
                    $employee,
                    $payrollData['new_pending_deductions'] ?? 0
                );
            }

            DB::commit();

            return response()->json([
                'success' => 'Payroll generated successfully.',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }

    /**
     * Generate monthly payrolls for all salaried employees
     */
    public function generateMonthly(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.create')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $employees = Employee::with('salaryStructure')
                ->whereHas('salaryStructure', function ($q) {
                    $q->whereIn('salary_type', ['salary', 'both']);
                })
                ->where('status', 'active')
                ->get();

            $generated = 0;
            $skipped = 0;
            $errors = [];

            foreach ($employees as $employee) {
                // Skip if already exists
                $exists = Payroll::where('employee_id', $employee->id)
                    ->where('month', $request->month)
                    ->where('payroll_type', 'monthly')
                    ->exists();

                if ($exists) {
                    $skipped++;

                    continue;
                }

                try {
                    $payrollData = $this->payrollService->calculateMonthlyPayroll($employee, $request->month);

                    $payroll = Payroll::create(array_merge(
                        ['employee_id' => $employee->id],
                        Arr::except($payrollData, ['allowance_details', 'deduction_details'])
                    ));

                    $this->payrollService->savePayrollDetails(
                        $payroll,
                        $payrollData['allowance_details'] ?? [],
                        $payrollData['deduction_details'] ?? []
                    );

                    $generated++;
                } catch (\Exception $e) {
                    $errors[] = $employee->full_name.': '.$e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => "Monthly payroll generated for {$generated} employees. {$skipped} skipped (already exists).",
                'errors' => $errors,
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }

    /**
     * Generate daily payrolls for all daily wage employees for a specific date
     */
    public function generateDaily(Request $request)
    {
        if (! auth()->user()->can('hr.payroll.create')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Fetch employees configured for daily wages
            $employees = Employee::with('salaryStructure')
                ->whereHas('salaryStructure', function ($q) {
                    $q->where('use_daily_wages', true);
                })
                ->where('status', 'active')
                ->get();

            $generated = 0;
            $skipped = 0;
            $errors = [];

            foreach ($employees as $employee) {
                // Skip if already exists for this date
                $monthStr = Carbon::parse($request->date)->format('Y-m');
                // Check exact date overlap for daily payroll
                $exists = Payroll::where('employee_id', $employee->id)
                    ->where('payroll_type', 'daily')
                    ->whereDate('created_at', $request->date) // Usually we might check a date column, currently daily stores date in 'month' or created_at? 
                    // Let's check how calculateDailyPayroll stores it. It stores 'month' => Y-m-d.
                    ->where('month', $request->date) 
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Get attendance for the date
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $request->date)
                    ->first();

                if (! $attendance || ! $attendance->clock_out) {
                    $errors[] = $employee->full_name . ': No completed attendance found.';
                    continue;
                }

                try {
                    $payrollData = $this->payrollService->calculateDailyPayroll($employee, $attendance);

                    $payroll = Payroll::create(array_merge(
                        ['employee_id' => $employee->id],
                        Arr::except($payrollData, ['allowance_details', 'deduction_details', 'new_pending_deductions'])
                    ));

                    $this->payrollService->savePayrollDetails(
                        $payroll,
                        $payrollData['allowance_details'] ?? [],
                        $payrollData['deduction_details'] ?? []
                    );

                    $this->payrollService->updatePendingDeductions(
                        $employee,
                        $payrollData['new_pending_deductions'] ?? 0
                    );

                    $generated++;
                } catch (\Exception $e) {
                    $errors[] = $employee->full_name . ': ' . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => "Daily payroll generated for {$generated} employees. {$skipped} skipped. " . (count($errors) > 0 ? count($errors) . " errors." : ""),
                'errors' => $errors,
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }

    /**
     * Update payroll (add manual allowances/deductions, edit notes)
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('hr.payroll.edit')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $payroll = Payroll::findOrFail($id);

        if (! $payroll->canEdit()) {
            return response()->json([
                'error' => 'Cannot edit paid payroll.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'manual_allowances' => 'nullable|numeric|min:0',
            'manual_deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Update manual adjustments
            $payroll->update([
                'manual_allowances' => $request->manual_allowances ?? 0,
                'manual_deductions' => $request->manual_deductions ?? 0,
                'notes' => $request->notes,
            ]);

            // Recalculate net salary
            $totalDeductions = $payroll->deductions +
                              $payroll->attendance_deductions +
                              $payroll->manual_deductions +
                              $payroll->carried_forward_deduction;

            $grossSalary = $payroll->basic_salary +
                          $payroll->allowances +
                          $payroll->manual_allowances;

            $payroll->update([
                'gross_salary' => $grossSalary,
                'net_salary' => $grossSalary - $totalDeductions,
            ]);

            DB::commit();

            return response()->json([
                'success' => 'Payroll updated successfully.',
                'payroll' => $payroll->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }

    /**
     * Mark payroll as reviewed
     */
    public function markReviewed($id)
    {
        if (! auth()->user()->can('hr.payroll.edit')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $payroll = Payroll::findOrFail($id);

        if (! $payroll->canMarkReviewed()) {
            return response()->json([
                'error' => 'Payroll is not in generated status.',
            ], 403);
        }

        $payroll->update([
            'status' => 'reviewed',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => 'Payroll marked as reviewed successfully.',
        ]);
    }

    /**
     * Mark payroll as paid
     */
    public function markPaid($id)
    {
        if (! auth()->user()->can('hr.payroll.edit')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $payroll = Payroll::findOrFail($id);

        if (! $payroll->canMarkPaid()) {
            return response()->json([
                'error' => 'Payroll cannot be marked as paid.',
            ], 403);
        }

        $payroll->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        return response()->json([
            'success' => 'Payroll marked as paid successfully.',
        ]);
    }

    /**
     * Delete payroll
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('hr.payroll.delete')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $payroll = Payroll::findOrFail($id);

        // Only allow deletion if not paid
        if ($payroll->status === 'paid') {
            return response()->json([
                'error' => 'Cannot delete paid payroll.',
            ], 403);
        }

        $payroll->delete();

        return response()->json([
            'success' => 'Payroll deleted successfully.',
        ]);
    }

    /**
     * Auto-generate daily payroll when employee checks out
     * This should be called from attendance checkout process
     */
    public function autoGenerateDaily(Employee $employee, Attendance $attendance)
    {
        // Check if employee uses daily wages
        if (! $employee->salaryStructure || ! $employee->salaryStructure->use_daily_wages) {
            return;
        }

        // Check if payroll already exists for this date
        $month = Carbon::parse($attendance->date)->format('Y-m');
        $exists = Payroll::where('employee_id', $employee->id)
            ->where('month', $month)
            ->where('payroll_type', 'daily')
            ->whereDate('created_at', $attendance->date)
            ->exists();

        if ($exists) {
            return;
        }

        try {
            DB::beginTransaction();

            $payrollData = $this->payrollService->calculateDailyPayroll($employee, $attendance);

            $payroll = Payroll::create(array_merge(
                ['employee_id' => $employee->id],
                Arr::except($payrollData, ['allowance_details', 'deduction_details', 'new_pending_deductions'])
            ));

            $this->payrollService->savePayrollDetails(
                $payroll,
                $payrollData['allowance_details'] ?? [],
                $payrollData['deduction_details'] ?? []
            );

            $this->payrollService->updatePendingDeductions(
                $employee,
                $payrollData['new_pending_deductions'] ?? 0
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Auto-generate daily payroll failed: '.$e->getMessage());
        }
    }
}
