@extends('admin_panel.layout.app')

@section('content')
    @include('hr.partials.hr-styles')

    <style>
        .payroll-sheet-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .excel-banner-header {
            background-color: #1f497d;
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 700;
            padding: 12px 20px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .excel-banner-sub {
            background-color: #8db4e2;
            color: #000000;
            font-size: 1rem;
            font-weight: 700;
            padding: 8px 20px;
            text-align: center;
            border-bottom: 2px solid #1f497d;
        }

        .table-payroll-excel {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .table-payroll-excel th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #cbd5e1;
            padding: 10px 8px;
            white-space: nowrap;
        }

        .table-payroll-excel td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
            text-align: center;
            background: #ffffff;
        }

        /* Excel Column Specific Colors */
        .col-total-pay {
            background-color: #8db4e2 !important;
            font-weight: 700;
            color: #000000 !important;
        }

        .col-net-payable {
            background-color: #b8cce4 !important;
            font-weight: 700;
            color: #000000 !important;
        }

        .table-payroll-excel input.form-control-sheet {
            width: 100%;
            border: 1px solid transparent;
            background: transparent;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .table-payroll-excel input.form-control-sheet:hover,
        .table-payroll-excel input.form-control-sheet:focus {
            background: #ffffff;
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .total-summary-row td {
            background-color: #f1f5f9 !important;
            font-weight: 800 !important;
            font-size: 0.9rem;
            border-top: 2px solid #1f497d !important;
        }

        /* Overtime Section */
        .ot-section {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin-top: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .ot-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            margin-top: 10px;
        }

        .ot-table th, .ot-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            text-align: center;
        }

        .ot-table-header {
            background-color: #8db4e2;
            color: #000000;
            font-weight: 700;
            text-align: center;
        }

        .btn-sheet-action {
            padding: 8px 16px;
            font-weight: 600;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-3">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h1 class="h3 font-weight-bold text-dark mb-1">
                            <i class="fa fa-table text-primary me-2"></i> Payroll Management Sheet
                        </h1>
                        <p class="text-muted small mb-0">Manage employee salaries, advances, auto-closing balances, and payments</p>
                    </div>

                    <!-- Top Bar Controls -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">

                        <!-- Payment Account Dropdown -->
                        <div class="d-flex align-items-center gap-2 bg-light p-1 px-2 rounded border">
                            <i class="fa fa-wallet text-secondary"></i>
                            <span class="small fw-bold text-secondary">Account:</span>
                            <select id="globalAccountId" class="form-select form-select-sm border-0 bg-transparent fw-bold text-dark" style="min-width: 180px;">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->title }} (Bal: Rs {{ number_format($acc->current_balance ?? 0, 0) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Selector Form -->
                        <form method="GET" action="{{ route('hr.payroll.index') }}" class="d-flex align-items-center gap-1">
                            <input type="month" id="month" name="month" class="form-control form-control-sm fw-bold"
                                value="{{ $month }}" onchange="this.form.submit()">
                        </form>

                        <!-- Issue Advance Salary Button -->
                        <button type="button" class="btn btn-warning btn-sheet-action text-dark" data-toggle="modal" data-target="#modalGiveAdvance" data-bs-toggle="modal" data-bs-target="#modalGiveAdvance">
                            <i class="fa fa-hand-holding-usd"></i> Give Advance
                        </button>

                        <!-- View Payment History Button -->
                        <a href="{{ route('hr.payroll.history') }}" class="btn btn-info btn-sheet-action text-white">
                            <i class="fa fa-history"></i> Payment History
                        </a>

                        <!-- Print Sheet Button -->
                        <a href="{{ route('hr.payroll.print-summary', ['month' => $month]) }}" target="_blank"
                            class="btn btn-outline-primary btn-sheet-action">
                            <i class="fa fa-print"></i> Print Sheet
                        </a>

                        <!-- Save Sheet Button -->
                        @can('hr.payroll.create')
                            <button type="button" id="btnSaveSheet" class="btn btn-success btn-sheet-action">
                                <i class="fa fa-save"></i> Save Payroll Sheet
                            </button>
                        @endcan
                    </div>
                </div>

                <!-- Main Excel Sheet Card -->
                <div class="payroll-sheet-card">
                    <div class="excel-banner-header">
                        Payroll Summary
                    </div>
                    <div class="excel-banner-sub">
                        {{ \Carbon\Carbon::parse($month.'-01')->format('M-y') }}
                    </div>

                    <div class="table-responsive">
                        <table class="table-payroll-excel" id="payrollTable">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">SN</th>
                                    <th style="min-width: 150px;" class="text-start ps-3">Name</th>
                                    <th style="width: 70px;">P-Days</th>
                                    <th style="min-width: 100px;">Basic Salary</th>
                                    <th style="min-width: 110px;">Salary Count this month</th>
                                    <th style="width: 80px;">Overtime Days</th>
                                    <th style="min-width: 100px;">Overtime Pay</th>
                                    <th style="min-width: 110px;" class="col-total-pay">Total Pay</th>
                                    <th style="min-width: 110px;">Advances this month</th>
                                    <th style="min-width: 110px;">Other Allowance /Eidi</th>
                                    <th style="min-width: 120px;" class="col-net-payable">Net Payable this month</th>
                                    <th style="min-width: 110px;">Payment this month</th>
                                    <th style="min-width: 120px;">Closing Balance/previous advances</th>
                                    <th style="min-width: 110px;">Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrollItems as $item)
                                    <tr data-emp-id="{{ $item['employee_id'] }}" data-prev-closing="{{ $item['prev_closing'] }}" data-active-loans="{{ $item['active_loans'] }}">
                                        <td>{{ $item['sn'] }}</td>
                                        <td class="text-start ps-3 fw-bold text-uppercase">
                                            {{ $item['employee_name'] }}
                                            <input type="hidden" class="inp-employee-id" value="{{ $item['employee_id'] }}">
                                            @if($item['active_loans'] > 0)
                                                <small class="d-block text-danger fw-bold" style="font-size: 0.72rem; text-transform: none;">
                                                    <i class="fa fa-hand-holding-usd text-warning"></i> Adv Given: Rs {{ number_format($item['active_loans'], 0) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" min="0" max="31" class="form-control-sheet inp-p-days"
                                                value="{{ $item['p_days'] }}">
                                        </td>
                                        <td>
                                            <input type="number" step="1" class="form-control-sheet inp-basic-salary"
                                                value="{{ $item['basic_salary'] }}">
                                        </td>
                                        <td class="td-salary-count fw-bold">
                                            {{ number_format($item['salary_count'], 0) }}
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control-sheet inp-ot-days"
                                                value="{{ $item['overtime_days'] > 0 ? $item['overtime_days'] : '' }}" placeholder="-">
                                        </td>
                                        <td>
                                            <input type="number" step="1" class="form-control-sheet inp-ot-pay"
                                                value="{{ $item['overtime_pay'] > 0 ? $item['overtime_pay'] : '' }}" placeholder="-">
                                        </td>
                                        <td class="col-total-pay td-total-pay">
                                            {{ number_format($item['total_pay'], 0) }}
                                        </td>
                                        <td>
                                            <input type="number" step="1" class="form-control-sheet inp-advances text-danger"
                                                value="{{ $item['advances'] != 0 ? $item['advances'] : '' }}" placeholder="-0">
                                        </td>
                                        <td>
                                            <input type="number" step="1" class="form-control-sheet inp-other-allowance"
                                                value="{{ $item['other_allowance'] > 0 ? $item['other_allowance'] : '' }}" placeholder="-">
                                        </td>
                                        <td class="col-net-payable td-net-payable">
                                            {{ number_format($item['net_payable'], 0) }}
                                        </td>
                                        <td>
                                            <input type="number" step="1" class="form-control-sheet inp-payment-month fw-bold"
                                                value="{{ $item['payment_this_month'] }}">
                                        </td>
                                        <td>
                                            <input type="number" step="1" class="form-control-sheet inp-closing-balance"
                                                value="{{ $item['closing_balance'] != 0 ? $item['closing_balance'] : '' }}" placeholder="-">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control-sheet inp-payment-date"
                                                value="{{ $item['payment_date'] }}">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="py-4 text-muted italic">No active employees found for payroll.</td>
                                    </tr>
                                @endforelse

                                <!-- Total Row -->
                                <tr class="total-summary-row">
                                    <td colspan="2" class="text-start ps-3">TOTAL</td>
                                    <td id="totPDays">-</td>
                                    <td id="totBasicSalary" class="text-end pe-2">{{ number_format($totals['basic_salary'], 0) }}</td>
                                    <td id="totSalaryCount" class="text-end pe-2">{{ number_format($totals['salary_count'], 0) }}</td>
                                    <td id="totOTDays">-</td>
                                    <td id="totOTPay" class="text-end pe-2">{{ number_format($totals['overtime_pay'], 0) }}</td>
                                    <td id="totTotalPay" class="col-total-pay text-end pe-2">{{ number_format($totals['total_pay'], 0) }}</td>
                                    <td id="totAdvances" class="text-end pe-2 text-danger">{{ number_format($totals['advances'], 0) }}</td>
                                    <td id="totOtherAllowance" class="text-end pe-2">{{ number_format($totals['other_allowance'], 0) }}</td>
                                    <td id="totNetPayable" class="col-net-payable text-end pe-2">{{ number_format($totals['net_payable'], 0) }}</td>
                                    <td id="totPaymentMonth" class="text-end pe-2">{{ number_format($totals['payment_this_month'], 0) }}</td>
                                    <td id="totClosingBalance" class="text-end pe-2">{{ number_format($totals['closing_balance'], 0) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- OVERTIME DETAILS SECTION -->
                <div class="ot-section">
                    <h5 class="fw-bold mb-3 text-dark">
                        <i class="fa fa-clock text-primary me-2"></i> OVERTIME DETAILS
                    </h5>
                    <div class="table-responsive">
                        <table class="ot-table" id="otTable">
                            <thead>
                                <tr>
                                    <th colspan="{{ count($payrollItems) + 1 }}" class="ot-table-header">OVERTIME DETAILS</th>
                                </tr>
                                <tr>
                                    <th class="text-start ps-3" style="width: 140px;">NAME</th>
                                    @foreach($payrollItems as $item)
                                        <th>{{ strtok($item['employee_name'], ' ') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start ps-3 fw-bold">HEAD</td>
                                    @foreach($payrollItems as $item)
                                        <td>OT</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start ps-3 fw-bold">OVERTIME HOURS</td>
                                    @foreach($payrollItems as $item)
                                        <td>
                                            <input type="number" step="0.1" class="form-control-sheet inp-ot-hours-sec"
                                                data-emp-id="{{ $item['employee_id'] }}"
                                                value="{{ $item['overtime_hours'] > 0 ? $item['overtime_hours'] : 0 }}">
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start ps-3 fw-bold">OVERTIME DAYS</td>
                                    @foreach($payrollItems as $item)
                                        <td class="td-ot-days-sec" data-emp-id="{{ $item['employee_id'] }}">
                                            {{ number_format($item['overtime_days'], 2) }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL: ISSUE ADVANCE SALARY -->
    <div class="modal fade" id="modalGiveAdvance" tabindex="-1" aria-labelledby="modalGiveAdvanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <!-- Header with Modern Gradient -->
                <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-white" id="modalGiveAdvanceLabel" style="font-size: 1.1rem;">
                        <span class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded-circle" style="width: 32px; height: 32px; font-size: 0.95rem;">
                            <i class="fa fa-hand-holding-usd"></i>
                        </span>
                        Issue Advance Salary
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formGiveAdvance">
                    <div class="modal-body p-4" style="background-color: #f8fafc;">
                        <!-- Employee Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fa fa-user text-primary me-1"></i> Select Employee <span class="text-danger">*</span>
                            </label>
                            <select name="employee_id" class="form-select form-select-lg shadow-sm border-slate-300" style="border-radius: 10px; font-size: 0.9rem;" required>
                                <option value="">-- Choose Employee --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->designation->name ?? 'Staff' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Advance Amount -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fa fa-money-bill-wave text-success me-1"></i> Advance Amount (PKR) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <span class="input-group-text bg-white border-slate-300 text-muted fw-bold" style="font-size: 0.9rem;">Rs.</span>
                                <input type="number" name="amount" class="form-control border-slate-300 fw-bold" placeholder="e.g. 10000" min="1" style="font-size: 0.95rem;" required>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fa fa-calendar-alt text-info me-1"></i> Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" class="form-control form-control-lg shadow-sm border-slate-300" value="{{ date('Y-m-d') }}" style="border-radius: 10px; font-size: 0.9rem;" required>
                        </div>

                        <!-- Payment Account Dropdown -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fa fa-university text-warning me-1"></i> Payment Account (Paid From / Source)
                            </label>
                            <select name="account_id" class="form-select form-select-lg shadow-sm border-slate-300 fw-semibold" style="border-radius: 10px; font-size: 0.9rem;">
                                <option value="">-- Select Cash / Bank Account --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        💳 {{ $acc->title }} — (Available Bal: Rs {{ number_format($acc->current_balance ?? 0, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                <i class="fa fa-info-circle me-1"></i> Amount will be deducted from this account &amp; logged in its ledger.
                            </small>
                        </div>

                        <!-- Reason / Notes -->
                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fa fa-comment-alt text-secondary me-1"></i> Reason / Notes
                            </label>
                            <textarea name="reason" class="form-control shadow-sm border-slate-300" rows="2" placeholder="e.g. Personal emergency advance" style="border-radius: 10px; font-size: 0.88rem;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-white px-4 py-3 border-top" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                        <button type="button" class="btn btn-light border fw-semibold px-4 py-2" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                        <button type="submit" class="btn btn-indigo px-4 py-2 fw-bold text-white shadow-sm" id="btnSubmitAdvance" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none; border-radius: 10px;">
                            <i class="fa fa-check-circle me-1"></i> Issue Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts for Live Sheet Calculation & Save -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Function to recalculate a specific row & totals
            function calculateRow(tr) {
                var basicSalary = parseFloat(tr.querySelector('.inp-basic-salary').value) || 0;
                var pDays = parseFloat(tr.querySelector('.inp-p-days').value) || 0;
                
                // Salary Count = (Basic Salary / 30) * P-Days
                var salaryCount = Math.round((basicSalary / 30) * pDays);
                tr.querySelector('.td-salary-count').textContent = salaryCount.toLocaleString('en-US');

                // Overtime Days & Pay (1.5x Multiplier)
                var otDays = parseFloat(tr.querySelector('.inp-ot-days').value) || 0;
                var otPayInput = tr.querySelector('.inp-ot-pay');
                var otPay = parseFloat(otPayInput.value);

                if (isNaN(otPay) && otDays > 0) {
                    otPay = Math.round((basicSalary / 30) * 1.5 * otDays);
                    otPayInput.value = otPay;
                } else if (isNaN(otPay)) {
                    otPay = 0;
                }

                // Total Pay = Salary Count + Overtime Pay
                var totalPay = salaryCount + otPay;
                tr.querySelector('.td-total-pay').textContent = totalPay.toLocaleString('en-US');

                // Advances & Other Allowances
                var advances = Math.abs(parseFloat(tr.querySelector('.inp-advances').value) || 0);
                var otherAllowance = parseFloat(tr.querySelector('.inp-other-allowance').value) || 0;

                // Net Payable = Total Pay - Advances + Other Allowance
                var netPayable = totalPay - advances + otherAllowance;
                tr.querySelector('.td-net-payable').textContent = netPayable.toLocaleString('en-US');

                // Sync payment this month default if unmodified
                var paymentInput = tr.querySelector('.inp-payment-month');
                if (!paymentInput.dataset.userEdited) {
                    paymentInput.value = netPayable;
                }
                var paymentThisMonth = parseFloat(paymentInput.value) || 0;

                // Auto-calculate Closing Balance based on Previous Closing / Active Loans + Advances + (Net Payable - Payment)
                var prevClosing = parseFloat(tr.dataset.prevClosing) || 0;
                var activeLoans = parseFloat(tr.dataset.activeLoans) || 0;
                var baseClosing = prevClosing !== 0 ? prevClosing : (activeLoans > 0 ? -activeLoans : 0);

                var closingInput = tr.querySelector('.inp-closing-balance');
                if (!closingInput.dataset.userEdited) {
                    var computedClosing = baseClosing + advances + (netPayable - paymentThisMonth);
                    closingInput.value = computedClosing !== 0 ? computedClosing : '';
                }

                calculateTotals();
            }

            // Function to calculate all column totals
            function calculateTotals() {
                var totBasic = 0, totSalaryCount = 0, totOTPay = 0, totTotalPay = 0;
                var totAdvances = 0, totOtherAllowance = 0, totNetPayable = 0;
                var totPaymentMonth = 0, totClosingBalance = 0;

                document.querySelectorAll('#payrollTable tbody tr[data-emp-id]').forEach(function(tr) {
                    var basic = parseFloat(tr.querySelector('.inp-basic-salary').value) || 0;
                    var pDays = parseFloat(tr.querySelector('.inp-p-days').value) || 0;
                    var salCount = Math.round((basic / 30) * pDays);
                    var otPay = parseFloat(tr.querySelector('.inp-ot-pay').value) || 0;
                    var totPay = salCount + otPay;
                    var adv = Math.abs(parseFloat(tr.querySelector('.inp-advances').value) || 0);
                    var allow = parseFloat(tr.querySelector('.inp-other-allowance').value) || 0;
                    var net = totPay - adv + allow;
                    var payMonth = parseFloat(tr.querySelector('.inp-payment-month').value) || 0;
                    var closing = parseFloat(tr.querySelector('.inp-closing-balance').value) || 0;

                    totBasic += basic;
                    totSalaryCount += salCount;
                    totOTPay += otPay;
                    totTotalPay += totPay;
                    totAdvances += adv;
                    totOtherAllowance += allow;
                    totNetPayable += net;
                    totPaymentMonth += payMonth;
                    totClosingBalance += closing;
                });

                document.getElementById('totBasicSalary').textContent = totBasic.toLocaleString('en-US');
                document.getElementById('totSalaryCount').textContent = totSalaryCount.toLocaleString('en-US');
                document.getElementById('totOTPay').textContent = totOTPay.toLocaleString('en-US');
                document.getElementById('totTotalPay').textContent = totTotalPay.toLocaleString('en-US');
                document.getElementById('totAdvances').textContent = totAdvances > 0 ? '-' + totAdvances.toLocaleString('en-US') : '0';
                document.getElementById('totOtherAllowance').textContent = totOtherAllowance.toLocaleString('en-US');
                document.getElementById('totNetPayable').textContent = totNetPayable.toLocaleString('en-US');
                document.getElementById('totPaymentMonth').textContent = totPaymentMonth.toLocaleString('en-US');
                document.getElementById('totClosingBalance').textContent = totClosingBalance.toLocaleString('en-US');
            }

            // Event Listeners on inputs
            document.querySelectorAll('#payrollTable input').forEach(function(input) {
                input.addEventListener('input', function() {
                    if (this.classList.contains('inp-payment-month') || this.classList.contains('inp-closing-balance')) {
                        this.dataset.userEdited = "true";
                    }
                    var tr = this.closest('tr');
                    if (tr && tr.dataset.empId) {
                        calculateRow(tr);
                    }
                });
            });

            // Overtime hours section listener
            document.querySelectorAll('.inp-ot-hours-sec').forEach(function(input) {
                input.addEventListener('input', function() {
                    var empId = this.dataset.empId;
                    var hours = parseFloat(this.value) || 0;
                    var days = (hours / 9.0).toFixed(2);

                    // Update OT Days in OT section
                    var tdOtDays = document.querySelector('.td-ot-days-sec[data-emp-id="' + empId + '"]');
                    if (tdOtDays) {
                        tdOtDays.textContent = days;
                    }

                    // Update main table row
                    var tr = document.querySelector('#payrollTable tr[data-emp-id="' + empId + '"]');
                    if (tr) {
                        var inpOtDays = tr.querySelector('.inp-ot-days');
                        inpOtDays.value = days > 0 ? days : '';
                        
                        var basicSalary = parseFloat(tr.querySelector('.inp-basic-salary').value) || 0;
                        var otPay = Math.round((basicSalary / 30) * 1.5 * days);
                        tr.querySelector('.inp-ot-pay').value = otPay > 0 ? otPay : '';

                        calculateRow(tr);
                    }
                });
            });

            // Handle Give Advance Form Submit
            var formGiveAdvance = document.getElementById('formGiveAdvance');
            if (formGiveAdvance) {
                formGiveAdvance.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = document.getElementById('btnSubmitAdvance');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

                    var formData = new FormData(this);

                    fetch("{{ route('hr.payroll.give-advance') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: formData
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(res => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa fa-check me-1"></i> Issue Advance';

                        var data = res.body;
                        if (res.status === 200 && data.success) {
                            alert(data.success);
                            window.location.reload();
                        } else if (data.errors) {
                            var msg = Object.values(data.errors).flat().join('\n');
                            alert('❌ Error: ' + msg);
                        } else if (data.error) {
                            alert('❌ Error: ' + data.error);
                        } else {
                            alert('❌ Error processing advance request.');
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa fa-check me-1"></i> Issue Advance';
                        alert('❌ Error processing advance request.');
                    });
                });
            }

            // Bulk Save Sheet Action
            var btnSaveSheet = document.getElementById('btnSaveSheet');
            if (btnSaveSheet) {
                btnSaveSheet.addEventListener('click', function() {
                    var rowsData = [];
                    var globalAccountId = document.getElementById('globalAccountId').value;

                    document.querySelectorAll('#payrollTable tbody tr[data-emp-id]').forEach(function(tr) {
                        var empId = tr.dataset.empId;
                        var basicSalary = parseFloat(tr.querySelector('.inp-basic-salary').value) || 0;
                        var pDays = parseFloat(tr.querySelector('.inp-p-days').value) || 0;
                        var salaryCount = Math.round((basicSalary / 30) * pDays);

                        var otHoursSec = document.querySelector('.inp-ot-hours-sec[data-emp-id="' + empId + '"]');
                        var otHours = otHoursSec ? (parseFloat(otHoursSec.value) || 0) : 0;

                        var otDays = parseFloat(tr.querySelector('.inp-ot-days').value) || (otHours / 9.0);
                        var otPay = parseFloat(tr.querySelector('.inp-ot-pay').value) || Math.round((basicSalary / 30) * 1.5 * otDays);

                        var totalPay = salaryCount + otPay;
                        var advances = Math.abs(parseFloat(tr.querySelector('.inp-advances').value) || 0);
                        var otherAllowance = parseFloat(tr.querySelector('.inp-other-allowance').value) || 0;
                        var netPayable = totalPay - advances + otherAllowance;
                        var paymentThisMonth = parseFloat(tr.querySelector('.inp-payment-month').value) || netPayable;
                        var closingBalance = parseFloat(tr.querySelector('.inp-closing-balance').value) || 0;
                        var paymentDate = tr.querySelector('.inp-payment-date').value || '';

                        rowsData.push({
                            employee_id: empId,
                            basic_salary: basicSalary,
                            p_days: pDays,
                            salary_count: salaryCount,
                            overtime_hours: otHours,
                            overtime_days: otDays,
                            overtime_pay: otPay,
                            total_pay: totalPay,
                            advances: advances,
                            other_allowance: otherAllowance,
                            net_payable: netPayable,
                            payment_this_month: paymentThisMonth,
                            closing_balance: closingBalance,
                            payment_date: paymentDate,
                            account_id: globalAccountId
                        });
                    });

                    btnSaveSheet.disabled = true;
                    btnSaveSheet.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

                    fetch("{{ route('hr.payroll.save-sheet') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            month: "{{ $month }}",
                            account_id: globalAccountId,
                            rows: rowsData
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        btnSaveSheet.disabled = false;
                        btnSaveSheet.innerHTML = '<i class="fa fa-save"></i> Save Payroll Sheet';

                        if (data.success) {
                            alert('✅ Payroll Sheet & Account Payments Saved Successfully!');
                            if (data.reload) {
                                window.location.reload();
                            }
                        } else if (data.errors) {
                            var msg = Object.values(data.errors).flat().join('\n');
                            alert('❌ Failed to save: ' + msg);
                        }
                    })
                    .catch(error => {
                        btnSaveSheet.disabled = false;
                        btnSaveSheet.innerHTML = '<i class="fa fa-save"></i> Save Payroll Sheet';
                        alert('❌ Error occurred while saving payroll sheet.');
                        console.error(error);
                    });
                });
            }

        });
    </script>
@endsection
