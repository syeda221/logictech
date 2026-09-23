@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Employee Ledger Report Styling */
    .ledger-container {
        padding: 12px 16px;
        background: #f8fafc;
        min-height: calc(100vh - 75px);
    }
    
    .summary-pill-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
        margin-bottom: 16px;
    }
    
    .stat-pill {
        flex: 1 1 0px;
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .stat-pill .stat-label {
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .stat-pill .stat-val {
        font-size: .95rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .table-wrap {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .report-table {
        font-size: 0.85rem;
        margin-bottom: 0;
        width: 100%;
        border-collapse: collapse;
    }
    .report-table thead th {
        background-color: #eff6ff !important;
        color: #1e40af !important;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 10px 12px;
        border-bottom: 2px solid #60a5fa !important;
        white-space: nowrap;
    }
    .report-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .report-table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .badge-trans {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .badge-advance {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .badge-salary {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
</style>

<div class="ledger-container">
    <div class="container-fluid px-0">
        
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="h4 font-weight-bold text-dark mb-0">
                    <i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Employee Ledger Statement
                </h1>
                <p class="text-muted small mb-0">Track advances given, salary earned, deductions and running closing balance per employee</p>
            </div>

            <div class="d-flex gap-2">
                @if($selectedEmployee)
                    <a href="{{ route('hr.payroll.print-employee-ledger', ['employee_id' => $selectedEmployee->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                        target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold">
                        <i class="fas fa-print mr-1"></i> Print Statement
                    </a>
                @endif
                <a href="{{ route('hr.payroll.index') }}" class="btn btn-sm btn-outline-secondary font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Payroll Sheet
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-12 mb-3">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('hr.payroll.employee-ledger') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold text-secondary mb-1">Select Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-control form-control-sm custom-select font-weight-bold" onchange="this.form.submit()" required>
                            <option value="">-- Choose Employee --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }} ({{ $emp->designation->name ?? 'Staff' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small font-weight-bold text-secondary mb-1">From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold"
                            value="{{ $startDate }}" onchange="this.form.submit()">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small font-weight-bold text-secondary mb-1">To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold"
                            value="{{ $endDate }}" onchange="this.form.submit()">
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill font-weight-bold">
                            <i class="fas fa-filter mr-1"></i> View
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedEmployee)
            <!-- Summary Metric Pills -->
            <div class="summary-pill-bar">
                <div class="stat-pill border-info">
                    <div class="stat-label text-info">Opening Balance</div>
                    <div class="stat-val text-info">Rs. {{ number_format($summary['opening_balance'], 0) }}</div>
                </div>

                @if($summary['total_advances_given'] > 0)
                    <div class="stat-pill border-warning">
                        <div class="stat-label text-warning">Total Advances Given</div>
                        <div class="stat-val text-warning">Rs. {{ number_format($summary['total_advances_given'], 0) }}</div>
                    </div>
                @endif

                <div class="stat-pill border-danger">
                    <div class="stat-label text-danger">Advances Deducted</div>
                    <div class="stat-val text-danger">Rs. {{ number_format($summary['total_advances_deducted'], 0) }}</div>
                </div>

                <div class="stat-pill border-primary">
                    <div class="stat-label text-primary">Total Salary Earned</div>
                    <div class="stat-val text-primary">Rs. {{ number_format($summary['total_salary_earned'], 0) }}</div>
                </div>

                <div class="stat-pill border-success">
                    <div class="stat-label text-success">Total Net Paid</div>
                    <div class="stat-val text-success">Rs. {{ number_format($summary['total_net_paid'], 0) }}</div>
                </div>

                <div class="stat-pill border-dark bg-light">
                    <div class="stat-label text-dark">Closing Balance</div>
                    <div class="stat-val text-dark">Rs. {{ number_format($summary['closing_balance'], 0) }}</div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="pl-3" style="width: 100px;">Date</th>
                            <th style="width: 100px;">Ref #</th>
                            <th style="width: 180px;">Transaction Type</th>
                            <th>Description</th>
                            <th class="text-right text-warning">Advances Given (+)</th>
                            <th class="text-right text-primary">Salary Earned (+)</th>
                            <th class="text-right text-danger">Advances Deducted (-)</th>
                            <th class="text-right text-success">Net Amount Paid</th>
                            <th class="text-right pr-3" style="width: 150px;">Running Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Opening Balance Row -->
                        <tr class="bg-light font-weight-bold">
                            <td class="pl-3">{{ $startDate }}</td>
                            <td class="text-muted">-</td>
                            <td><span class="badge badge-secondary px-2 py-1">Opening</span></td>
                            <td>Opening Advance Balance Owed</td>
                            <td class="text-right">-</td>
                            <td class="text-right">-</td>
                            <td class="text-right">-</td>
                            <td class="text-right">-</td>
                            <td class="text-right pr-3 text-primary">Rs. {{ number_format($summary['opening_balance'], 0) }}</td>
                        </tr>

                        @forelse($ledgerEntries as $entry)
                            <tr>
                                <td class="pl-3 font-weight-bold text-dark">{{ $entry['date'] }}</td>
                                <td class="font-weight-bold text-secondary">{{ $entry['ref'] }}</td>
                                <td>
                                    <span class="badge-trans {{ str_contains($entry['type'], 'Advance') ? 'badge-advance' : 'badge-salary' }}">
                                        <i class="fas {{ str_contains($entry['type'], 'Advance') ? 'fa-hand-holding-usd' : 'fa-check-circle' }} mr-1"></i>
                                        {{ $entry['type'] }}
                                    </span>
                                </td>
                                <td>{{ $entry['description'] }}</td>

                                <!-- Advances Given (+) -->
                                <td class="text-right font-weight-bold {{ $entry['advance_given'] > 0 ? 'text-warning' : 'text-muted' }}">
                                    {{ $entry['advance_given'] > 0 ? 'Rs. ' . number_format($entry['advance_given'], 0) : '-' }}
                                </td>

                                <!-- Salary Earned (+) -->
                                <td class="text-right font-weight-semibold {{ $entry['salary_earned'] > 0 ? 'text-primary' : 'text-muted' }}">
                                    {{ $entry['salary_earned'] > 0 ? 'Rs. ' . number_format($entry['salary_earned'], 0) : '-' }}
                                </td>

                                <!-- Advances Deducted (-) -->
                                <td class="text-right font-weight-semibold {{ $entry['advance_deducted'] > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ $entry['advance_deducted'] > 0 ? '- Rs. ' . number_format($entry['advance_deducted'], 0) : '-' }}
                                </td>

                                <!-- Net Paid -->
                                <td class="text-right font-weight-bold {{ $entry['net_paid'] > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ $entry['net_paid'] > 0 ? 'Rs. ' . number_format($entry['net_paid'], 0) : '-' }}
                                </td>

                                <!-- Running Balance -->
                                <td class="text-right pr-3 font-weight-bold {{ $entry['running_balance'] != 0 ? 'text-dark' : 'text-muted' }}">
                                    Rs. {{ number_format($entry['running_balance'], 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted font-italic">
                                    No advance or salary transactions found for this period.
                                </td>
                            </tr>
                        @endforelse

                        <!-- Summary Total Row -->
                        <tr class="bg-light font-weight-bold" style="border-top: 2px solid #1e40af;">
                            <td colspan="4" class="pl-3 font-weight-bold">STATEMENT TOTALS</td>
                            <td class="text-right text-warning">Rs. {{ number_format($summary['total_advances_given'], 0) }}</td>
                            <td class="text-right text-primary">Rs. {{ number_format($summary['total_salary_earned'], 0) }}</td>
                            <td class="text-right text-danger">Rs. {{ number_format($summary['total_advances_deducted'], 0) }}</td>
                            <td class="text-right text-success">Rs. {{ number_format($summary['total_net_paid'], 0) }}</td>
                            <td class="text-right pr-3 font-weight-bold text-dark">Rs. {{ number_format($summary['closing_balance'], 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @else
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-user-circle fa-4x text-muted mb-3 opacity-50"></i>
                    <h5 class="font-weight-bold text-secondary">Please select an Employee to view their statement</h5>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
