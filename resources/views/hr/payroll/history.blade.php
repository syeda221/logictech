@extends('admin_panel.layout.app')

@section('content')
    @include('hr.partials.hr-styles')

    <style>
        .history-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .filter-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 16px 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .table-history {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .table-history th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 14px;
            white-space: nowrap;
        }

        .table-history td {
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 14px;
            vertical-align: middle;
        }

        .table-history tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge-account {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid #bae6fd;
        }

        .badge-month {
            background-color: #f1f5f9;
            color: #334155;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid #cbd5e1;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-3">

                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h1 class="h3 font-weight-bold text-dark mb-1">
                            <i class="fa fa-history text-primary mr-2"></i> Payroll Payment History
                        </h1>
                        <p class="text-muted small mb-0">Record and track all paid employee salaries and payment accounts</p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('hr.payroll.index') }}" class="btn btn-outline-primary font-weight-bold">
                            <i class="fa fa-table mr-1"></i> Back to Payroll Sheet
                        </a>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="filter-card">
                    <form method="GET" action="{{ route('hr.payroll.history') }}" class="row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="form-label small font-weight-bold text-secondary mb-1">Select Month</label>
                            <input type="month" name="month" class="form-control form-control-sm font-weight-bold"
                                value="{{ request('month') }}" onchange="this.form.submit()">
                        </div>

                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="form-label small font-weight-bold text-secondary mb-1">Select Employee</label>
                            <select name="employee_id" class="form-control form-control-sm custom-select" onchange="this.form.submit()">
                                <option value="">-- All Employees --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="form-label small font-weight-bold text-secondary mb-1">Payment Account</label>
                            <select name="account_id" class="form-control form-control-sm custom-select" onchange="this.form.submit()">
                                <option value="">-- All Accounts --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                        {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-fill font-weight-bold">
                                <i class="fa fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('hr.payroll.history') }}" class="btn btn-sm btn-light border font-weight-bold" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Payment History Card & Table -->
                <div class="history-card">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                            <i class="fa fa-list mr-1 text-primary"></i> Payment Records ({{ $payrolls->total() }})
                        </span>
                        <span class="text-muted small">Showing {{ $payrolls->count() }} of {{ $payrolls->total() }} entries</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table-history">
                            <thead>
                                <tr>
                                    <th class="pl-4" style="width: 50px;">#</th>
                                    <th>Payment Date</th>
                                    <th>Employee</th>
                                    <th>Month</th>
                                    <th class="text-right">Basic Salary</th>
                                    <th class="text-right">Total Pay</th>
                                    <th class="text-right">Advances Deducted</th>
                                    <th class="text-right">Net Amount Paid</th>
                                    <th>Payment Account</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pr-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $index => $payroll)
                                    <tr>
                                        <td class="pl-4 text-muted font-weight-bold">{{ $payrolls->firstItem() + $index }}</td>
                                        <td class="font-weight-bold text-dark">
                                            {{ $payroll->payment_date ? $payroll->payment_date->format('d M, Y') : '-' }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle mr-3">
                                                    {{ strtoupper(substr($payroll->employee->first_name ?? 'E', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold text-dark mb-0">{{ $payroll->employee->full_name ?? 'Unknown' }}</div>
                                                    <div class="text-muted small">{{ $payroll->employee->designation->name ?? 'Staff' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-month">
                                                {{ \Carbon\Carbon::parse($payroll->month.'-01')->format('M Y') }}
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-semibold">Rs. {{ number_format($payroll->basic_salary, 0) }}</td>
                                        <td class="text-right font-weight-semibold">Rs. {{ number_format($payroll->total_pay ?: $payroll->gross_salary, 0) }}</td>
                                        <td class="text-right text-danger font-weight-semibold">
                                            - Rs. {{ number_format($payroll->advances ?: $payroll->deductions, 0) }}
                                        </td>
                                        <td class="text-right text-success font-weight-bold" style="font-size: 0.95rem;">
                                            Rs. {{ number_format($payroll->payment_this_month ?: $payroll->net_salary, 0) }}
                                        </td>
                                        <td>
                                            @if($payroll->account)
                                                <span class="badge-account">
                                                    <i class="fa fa-wallet mr-1"></i> {{ $payroll->account->name }}
                                                </span>
                                            @else
                                                <span class="text-muted small">Cash Account</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-success px-2 py-1 font-weight-bold"><i class="fa fa-check-circle mr-1"></i> Paid</span>
                                        </td>
                                        <td class="text-center pr-4">
                                            <a href="{{ route('hr.payroll.payslip', $payroll->id) }}" target="_blank"
                                                class="btn btn-xs btn-outline-primary font-weight-bold px-2 py-1" title="Print Payslip">
                                                <i class="fa fa-print mr-1"></i> Payslip
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5 text-muted">
                                            <i class="fa fa-receipt fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                            <span class="font-weight-bold">No paid payroll records found.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($payrolls->hasPages())
                        <div class="p-3 bg-white border-top">
                            {{ $payrolls->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
