@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ==========================================================================
       Enterprise ERP Design System - Customer Ledger & Statement
       ========================================================================== */
    :root {
        --erp-bg-card: #ffffff;
        --erp-border: #e2e8f0;
        --erp-border-subtle: #f1f5f9;
        --erp-text-main: #0f172a;
        --erp-text-muted: #64748b;
        --erp-text-light: #94a3b8;
        --erp-primary: #2563eb;
        --erp-primary-hover: #1d4ed8;
        --erp-success: #059669;
        --erp-warning: #d97706;
        --erp-danger: #dc2626;
    }

    /* Page Header */
    .erp-page-header {
        margin-bottom: 1.5rem;
    }
    .erp-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .erp-subtitle {
        font-size: 0.815rem;
        color: #64748b;
        margin-top: 0.15rem;
        margin-bottom: 0;
    }

    /* Top Action Buttons (Exact Sales UI) */
    .btn-erp-primary {
        background-color: #2563eb;
        border: 1px solid #1d4ed8;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.815rem;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.2);
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
    }
    .btn-erp-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
    }

    .btn-erp-outline {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155 !important;
        font-weight: 600;
        font-size: 0.815rem;
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
    }
    .btn-erp-outline:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    .btn-erp-outline-danger {
        background-color: #ffffff;
        border: 1px solid #fca5a5;
        color: #dc2626 !important;
        font-weight: 600;
        font-size: 0.815rem;
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
    }
    .btn-erp-outline-danger:hover {
        background-color: #fef2f2;
        border-color: #f87171;
        color: #b91c1c !important;
    }

    /* KPI Metric Cards (Exact Sales UI) */
    .erp-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .erp-kpi-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 6px 16px -2px rgba(0, 0, 0, 0.07);
        transform: translateY(-2px);
    }
    .erp-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 0.35rem;
    }
    .erp-kpi-value {
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.2;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .erp-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    /* Single-Row Filter System (Exact Sales UI) */
    .erp-filter-card {
        background-color: #ffffff;
        border: 1.5px solid #dbeafe;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.05);
    }
    .erp-filter-single-row {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        width: 100%;
        flex-wrap: nowrap;
    }
    .erp-filter-item {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }
    .erp-item-customer { flex: 1.6; min-width: 150px; }
    .erp-item-date { flex: 1.1; min-width: 100px; }
    .erp-item-actions {
        flex: 0 0 auto;
        margin-left: auto;
    }
    .erp-filter-label {
        font-size: 0.68rem;
        font-weight: 700;
        color: #1e40af;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.3rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .erp-filter-input {
        height: 34px !important;
        border: 1.5px solid #bfdbfe !important;
        border-radius: 6px !important;
        font-size: 0.78rem !important;
        font-weight: 500 !important;
        color: #0f172a !important;
        background-color: #ffffff !important;
        padding: 0.25rem 0.5rem !important;
        transition: all 0.15s ease-in-out !important;
        width: 100% !important;
    }
    .erp-filter-input:hover {
        border-color: #60a5fa !important;
        background-color: #f8fbff !important;
    }
    .erp-filter-input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        background-color: #ffffff !important;
        outline: none !important;
    }

    /* Pill Style Buttons for Filter */
    .btn-erp-pill-primary {
        background-color: #2563eb !important;
        border: 1px solid #1d4ed8 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        border-radius: 50px !important;
        height: 34px !important;
        padding: 0 14px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.25) !important;
        transition: all 0.15s ease !important;
        white-space: nowrap !important;
    }
    .btn-erp-pill-primary:hover {
        background-color: #1d4ed8 !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 6px -1px rgba(37, 99, 235, 0.35) !important;
    }

    .btn-erp-pill-outline {
        background-color: #ffffff !important;
        border: 1.5px solid #bfdbfe !important;
        color: #1e40af !important;
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        border-radius: 50px !important;
        height: 34px !important;
        padding: 0 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        transition: all 0.15s ease !important;
        white-space: nowrap !important;
        text-decoration: none;
    }
    .btn-erp-pill-outline:hover {
        background-color: #eff6ff !important;
        border-color: #3b82f6 !important;
        color: #1d4ed8 !important;
    }

    /* Main ERP Card & Table */
    .erp-main-card {
        background: #ffffff;
        border: 1.5px solid #dbeafe;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
        overflow: hidden;
    }
    .erp-table-responsive {
        border-radius: 8px;
        width: 100%;
        overflow-x: auto;
    }
    .erp-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-bottom: 0;
        border: 1.5px solid #bfdbfe !important;
        border-radius: 8px;
    }
    .erp-table thead th {
        background-color: #eff6ff !important;
        color: #1e40af !important;
        font-weight: 700 !important;
        font-size: 0.70rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        padding: 0.5rem 0.4rem !important;
        border: 1px solid #bfdbfe !important;
        border-bottom: 2px solid #60a5fa !important;
        white-space: nowrap;
        vertical-align: middle;
    }
    .erp-table tbody td {
        padding: 0.45rem 0.4rem !important;
        font-size: 0.78rem !important;
        color: #334155 !important;
        border: 1px solid #dbeafe !important;
        vertical-align: middle !important;
        background-color: #ffffff;
        transition: background-color 0.15s ease;
    }
    .erp-table tbody tr:hover td {
        background-color: #f0f7ff !important;
    }
    .erp-table tfoot td {
        background-color: #eff6ff !important;
        border-top: 2px solid #60a5fa !important;
        border: 1px solid #bfdbfe !important;
        padding: 0.55rem 0.5rem !important;
        font-weight: 700 !important;
        color: #1e40af !important;
        font-size: 0.82rem !important;
    }

    /* Bill / Tag */
    .erp-bill-tag {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        font-size: 0.75rem;
        color: #2563eb;
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        display: inline-block;
    }

    /* Avatar & Customer Meta */
    .erp-avatar {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.68rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .erp-avatar-registered {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
    }

    /* ERP Status Badges */
    .erp-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.45rem;
        border-radius: 5px;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        line-height: 1.15;
        white-space: nowrap;
        border: 1px solid transparent;
    }
    .erp-badge.badge-posted {
        background-color: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
    }
    .erp-badge.badge-danger {
        background-color: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    /* Select2 Height */
    .select2-container .select2-selection--single {
        height: 34px !important;
        border: 1.5px solid #bfdbfe !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        font-size: 0.78rem !important;
        font-weight: 500 !important;
        color: #0f172a !important;
    }

    @media print {
        .erp-page-header .d-flex,
        .erp-filter-card,
        .sidebar-area,
        .header-area,
        .rt_nav_header,
        .btn-erp-pill-primary,
        .btn-erp-pill-outline {
            display: none !important;
        }
        .erp-main-card {
            border: none !important;
            box-shadow: none !important;
        }
    }

    @media (max-width: 991px) {
        .erp-filter-single-row {
            flex-wrap: wrap !important;
        }
        .erp-filter-item {
            flex: 1 1 calc(50% - 8px) !important;
            min-width: 140px !important;
        }
        .erp-item-actions {
            flex: 1 1 100% !important;
            justify-content: flex-end;
            margin-top: 6px;
        }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">

            {{-- Page Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                <div>
                    <h4 class="erp-title">
                        <i class="fas fa-file-invoice text-primary"></i> Customer Ledger (Statement)
                    </h4>
                    <p class="erp-subtitle">Comprehensive account statement, sales debit notes, payment credits, and running balance</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn-erp-outline-danger" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Statement
                    </button>
                    @can('customers.view')
                        <a class="btn-erp-outline" href="{{ route('customer.payments') }}">
                            <i class="fas fa-money-bill-wave"></i> Payments
                        </a>
                    @endcan
                    @can('chart.of.accounts.view')
                        <a class="btn-erp-outline" href="{{ route('view_all') }}">
                            <i class="fas fa-book"></i> Back to Accounts
                        </a>
                    @endcan
                    <a class="btn-erp-primary" href="{{ route('customers.index') }}">
                        <i class="fas fa-arrow-left"></i> Customer Directory
                    </a>
                </div>
            </div>

            {{-- Modern Single-Row Filter System (Exact Sales UI) --}}
            <div class="erp-filter-card">
                <form method="GET" action="{{ route('customers.ledger') }}" class="erp-filter-single-row" autocomplete="off">
                    <div class="erp-filter-item erp-item-customer">
                        <label class="erp-filter-label"><i class="fas fa-user text-primary"></i> Select Customer</label>
                        <select name="customer_id" class="form-select erp-filter-input select2">
                            <option value="">-- All Customers --</option>
                            @foreach ($customers as $cust)
                                <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->customer_name }} {{ $cust->customer_id ? '(' . $cust->customer_id . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="erp-filter-item erp-item-date">
                        <label class="erp-filter-label"><i class="far fa-calendar-alt text-primary"></i> From Date</label>
                        <input type="text" name="from_date" value="{{ request('from_date') }}" class="form-control erp-filter-input datepicker-custom bg-white" placeholder="dd/mm/yy">
                    </div>

                    <div class="erp-filter-item erp-item-date">
                        <label class="erp-filter-label"><i class="far fa-calendar-check text-primary"></i> To Date</label>
                        <input type="text" name="to_date" value="{{ request('to_date') }}" class="form-control erp-filter-input datepicker-custom bg-white" placeholder="dd/mm/yy">
                    </div>

                    <div class="erp-filter-item erp-item-actions ms-auto">
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ route('customers.ledger') }}" class="btn btn-erp-pill-outline" title="Reset Filters">
                                <i class="fas fa-undo"></i> <span>Reset</span>
                            </a>
                            <button type="submit" class="btn btn-erp-pill-primary" title="Apply Filter">
                                <i class="fas fa-filter"></i> <span>Filter</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            @php
                $totalDebit = $CustomerLedgers->sum('debit');
                $totalCredit = $CustomerLedgers->sum('credit');
                $cb = $closing_balance ?? 0;
            @endphp

            {{-- 4 KPI Metric Cards (Exact Sales UI Layout) --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Opening Balance</div>
                            <div class="erp-kpi-value text-dark">
                                Rs. {{ number_format(abs($opening_balance ?? 0), 2) }}
                                <small class="text-muted" style="font-size: 0.75rem;">{{ ($opening_balance ?? 0) >= 0 ? 'Dr' : 'Cr' }}</small>
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                            <i class="fas fa-calendar-minus"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Debit (Sales)</div>
                            <div class="erp-kpi-value text-success">
                                Rs. {{ number_format($totalDebit, 2) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Credit (Paid)</div>
                            <div class="erp-kpi-value text-warning" style="color: #d97706 !important;">
                                Rs. {{ number_format($totalCredit, 2) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #fffbeb; color: #d97706;">
                            <i class="fas fa-money-bill-transfer"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Closing Balance</div>
                            <div class="erp-kpi-value {{ $cb > 0 ? 'text-danger' : ($cb < 0 ? 'text-success' : 'text-primary') }}">
                                Rs. {{ number_format(abs($cb), 2) }}
                                <small style="font-size: 0.75rem;">{{ $cb >= 0 ? 'Dr' : 'Cr' }}</small>
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #f0f9ff; color: #0284c7;">
                            <i class="fas fa-scale-balanced"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Data Card --}}
            <div class="card erp-main-card">
                <div class="card-body p-3 p-md-4">

                    {{-- Data Table Container (Exact Sales UI) --}}
                    <div class="erp-table-responsive table-responsive">
                        <table class="table erp-table datanew" id="ledger-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="ps-2 text-center" style="width: 45px;">#</th>
                                    <th class="text-center" style="width: 90px;">DATE</th>
                                    <th style="min-width: 150px;">CUSTOMER</th>
                                    <th style="min-width: 250px;">DESCRIPTION / PARTICULARS</th>
                                    <th class="text-end" style="width: 120px;">DEBIT (DR)</th>
                                    <th class="text-end" style="width: 120px;">CREDIT (CR)</th>
                                    <th class="pe-3 text-end" style="width: 140px;">RUNNING BALANCE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($CustomerLedgers as $key => $ledger)
                                    @php
                                        $debit = $ledger->debit ?? 0;
                                        $credit = $ledger->credit ?? 0;
                                        $balance = $ledger->closing_balance;
                                        $suffix = $balance >= 0 ? 'Dr' : 'Cr';
                                        $custName = $ledger->customer->customer_name ?? 'N/A';
                                        $initial = strtoupper(substr($custName, 0, 1));
                                    @endphp
                                    <tr>
                                        {{-- Row Number --}}
                                        <td class="ps-2 text-center font-monospace text-muted" style="font-size: 0.75rem;">
                                            {{ $loop->iteration }}
                                        </td>

                                        {{-- Date --}}
                                        <td class="text-center font-monospace small text-muted">
                                            {{ $ledger->created_at->format('d/m/Y') }}
                                        </td>

                                        {{-- Customer with Avatar --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-1.5" style="max-width: 150px;">
                                                <div class="erp-avatar erp-avatar-registered">
                                                    {{ $initial }}
                                                </div>
                                                <span class="fw-bold text-dark text-truncate" style="font-size: 0.78rem;" title="{{ $custName }}">
                                                    {{ $custName }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Description / Particulars --}}
                                        <td>
                                            <span class="text-dark fw-semibold" style="font-size: 0.78rem;">
                                                {{ $ledger->description }}
                                            </span>
                                        </td>

                                        {{-- Debit (Dr) --}}
                                        <td class="text-end font-monospace fw-bold" style="color: #059669; font-size: 0.80rem;">
                                            {{ $debit > 0 ? number_format($debit, 2) : '-' }}
                                        </td>

                                        {{-- Credit (Cr) --}}
                                        <td class="text-end font-monospace fw-bold" style="color: #dc2626; font-size: 0.80rem;">
                                            {{ $credit > 0 ? number_format($credit, 2) : '-' }}
                                        </td>

                                        {{-- Running Balance --}}
                                        <td class="pe-3 text-end font-monospace">
                                            <span class="fw-bold {{ $balance > 0 ? 'text-dark' : ($balance < 0 ? 'text-success' : 'text-muted') }}" style="font-size: 0.82rem;">
                                                Rs. {{ number_format(abs($balance), 2) }}
                                            </span>
                                            <span class="erp-badge {{ $balance > 0 ? 'badge-danger' : 'badge-posted' }} ms-1" style="font-size: 0.65rem; padding: 1px 4px;">
                                                {{ $suffix }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-receipt fa-3x mb-3 text-light"></i>
                                            <h6 class="fw-bold">No transactions found in this period</h6>
                                            <p class="small text-muted mb-0">Select another date range or choose a different customer to inspect their ledger statement.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end font-weight-bold pe-3">Period Total:</td>
                                    <td class="text-end font-weight-bold font-monospace" style="color: #059669; font-size: 0.85rem;">
                                        {{ number_format($totalDebit, 2) }}
                                    </td>
                                    <td class="text-end font-weight-bold font-monospace" style="color: #dc2626; font-size: 0.85rem;">
                                        {{ number_format($totalCredit, 2) }}
                                    </td>
                                    <td class="pe-3 text-end font-weight-bold font-monospace" style="color: #1e40af; font-size: 0.85rem;">
                                        Rs. {{ number_format(abs($cb), 2) }} {{ $cb >= 0 ? 'Dr' : 'Cr' }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                width: '100%'
            });
        }
    });
</script>
@endpush

