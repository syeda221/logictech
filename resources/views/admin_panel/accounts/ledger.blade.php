@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ==========================================================================
       Enterprise ERP Design System - General Ledger
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
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.25);
    }

    .btn-erp-outline {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155 !important;
        font-weight: 600;
        font-size: 0.815rem;
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
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
        box-shadow: 0 1px 2px rgba(220, 38, 38, 0.05);
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-erp-outline-danger:hover {
        background-color: #fef2f2;
        border-color: #ef4444;
        color: #b91c1c !important;
    }

    /* Metric Summary KPI Cards (Exact Sales UI) */
    .erp-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .erp-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .erp-kpi-icon-wrap {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .erp-kpi-icon-blue {
        background-color: #eff6ff;
        color: #2563eb;
    }
    .erp-kpi-icon-green {
        background-color: #ecfdf5;
        color: #059669;
    }
    .erp-kpi-icon-amber {
        background-color: #fffbeb;
        color: #d97706;
    }
    .erp-kpi-icon-sky {
        background-color: #f0f9ff;
        color: #0284c7;
    }
    .erp-kpi-content {
        flex: 1;
        min-width: 0;
    }
    .erp-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 0.2rem;
    }
    .erp-kpi-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        letter-spacing: -0.02em;
        margin-bottom: 0.15rem;
    }
    .erp-kpi-sub {
        font-size: 0.75rem;
        color: #94a3b8;
        margin: 0;
    }

    /* Single-Row Filter System */
    .erp-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }
    .erp-filter-single-row {
        display: flex;
        flex-wrap: nowrap;
        align-items: flex-end;
        gap: 0.75rem;
    }
    .erp-filter-item {
        flex: 1 1 0px;
        min-width: 0;
    }
    .erp-filter-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    .erp-filter-label {
        display: block;
        font-size: 0.70rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
        margin-bottom: 0.35rem;
    }
    .erp-filter-input {
        width: 100%;
        height: 38px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.4rem 0.75rem;
        font-size: 0.815rem;
        color: #0f172a;
        background-color: #ffffff;
        transition: all 0.15s ease;
    }
    .erp-filter-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        outline: none;
    }

    .btn-erp-pill-primary {
        background-color: #2563eb;
        border: 1px solid #1d4ed8;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.815rem;
        border-radius: 50px;
        height: 38px;
        padding: 0 1.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.2);
        transition: all 0.15s ease;
        white-space: nowrap;
        cursor: pointer;
    }
    .btn-erp-pill-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 3px 6px -1px rgba(37, 99, 235, 0.3);
    }

    .btn-erp-pill-outline {
        background-color: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #475569 !important;
        font-weight: 600;
        font-size: 0.815rem;
        border-radius: 50px;
        height: 38px;
        padding: 0 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: all 0.15s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-erp-pill-outline:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    /* Main Table Card */
    .erp-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .erp-table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    /* Enterprise Table Styling */
    .erp-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        font-size: 0.815rem;
    }
    .erp-table thead th {
        background-color: #eff6ff !important;
        color: #1e40af !important;
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 0.9rem;
        border-top: none;
        border-bottom: 2px solid #60a5fa !important;
        border-right: 1px solid #bfdbfe;
        white-space: nowrap;
        vertical-align: middle;
    }
    .erp-table thead th:last-child {
        border-right: none;
    }
    .erp-table tbody td {
        padding: 0.75rem 0.9rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        color: #1e293b;
    }
    .erp-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .erp-table tfoot td {
        background-color: #f8fafc;
        border-top: 2px solid #bfdbfe;
        font-weight: 700;
        padding: 0.85rem 0.9rem;
    }

    .erp-badge {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .badge-dr {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }
    .badge-cr {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    @media print {
        .header, .sidebar, .erp-filter-card, .erp-page-header .btn-erp-outline, .erp-page-header .btn-erp-primary, .btn-erp-outline-danger {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .erp-main-card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid mt-3">

            {{-- Page Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                <div>
                    <h4 class="erp-title">
                        <i class="fas fa-book text-primary"></i> General Ledger
                        <span class="badge bg-light text-primary border ms-2 font-monospace" style="font-size: 0.8rem; font-weight: 600;">
                            {{ $account->account_code }}
                        </span>
                    </h4>
                    <p class="erp-subtitle">
                        Account: <strong class="text-dark">{{ $account->title }}</strong> | Head: <span class="text-muted">{{ $account->head->name ?? 'N/A' }}</span> | Type: <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $account->type }}</span>
                    </p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn-erp-outline-danger" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Statement
                    </button>
                    @can('chart.of.accounts.view')
                        <a class="btn-erp-primary" href="{{ route('view_all') }}">
                            <i class="fas fa-arrow-left"></i> Chart of Accounts
                        </a>
                    @endcan
                </div>
            </div>

            @php
                $openBal = $account->opening_balance ?? 0;
                $totDebit = $entries->sum('debit');
                $totCredit = $entries->sum('credit');
                $closeBal = $account->current_balance;
            @endphp

            {{-- 4 Metric Summary Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="erp-kpi-card">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-blue">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label">Opening Balance</div>
                            <div class="erp-kpi-val">Rs. {{ number_format(abs($openBal), 2) }}</div>
                            <p class="erp-kpi-sub">Initial book balance</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="erp-kpi-card">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-green">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label">Total Debit</div>
                            <div class="erp-kpi-val" style="color: #059669;">Rs. {{ number_format($totDebit, 2) }}</div>
                            <p class="erp-kpi-sub">Period debits total</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="erp-kpi-card">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-amber">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label">Total Credit</div>
                            <div class="erp-kpi-val" style="color: #d97706;">Rs. {{ number_format($totCredit, 2) }}</div>
                            <p class="erp-kpi-sub">Period credits total</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="erp-kpi-card">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-sky">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label">Closing Balance</div>
                            <div class="erp-kpi-val" style="color: #0284c7;">
                                Rs. {{ number_format(abs($closeBal), 2) }}
                                <span class="erp-badge {{ $closeBal >= 0 ? 'badge-dr' : 'badge-cr' }}" style="font-size: 0.65rem;">
                                    {{ $closeBal >= 0 ? 'Dr' : 'Cr' }}
                                </span>
                            </div>
                            <p class="erp-kpi-sub">Current book value</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Single-Row Filter System --}}
            <div class="erp-filter-card">
                <form method="GET" action="{{ route('accounts.ledger', $account->id) }}" class="erp-filter-single-row">
                    <div class="erp-filter-item">
                        <label class="erp-filter-label"><i class="fas fa-calendar-alt text-primary"></i> From Date</label>
                        <input type="text" name="from_date" value="{{ request('from_date') }}" class="erp-filter-input datepicker-custom" placeholder="dd/mm/yy">
                    </div>
                    <div class="erp-filter-item">
                        <label class="erp-filter-label"><i class="fas fa-calendar-alt text-primary"></i> To Date</label>
                        <input type="text" name="to_date" value="{{ request('to_date') }}" class="erp-filter-input datepicker-custom" placeholder="dd/mm/yy">
                    </div>
                    <div class="erp-filter-actions">
                        <button type="submit" class="btn-erp-pill-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('accounts.ledger', $account->id) }}" class="btn-erp-pill-outline">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <a href="{{ route('view_all') }}" class="btn-erp-pill-outline">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </form>
            </div>

            {{-- Main Data Table Card --}}
            <div class="erp-main-card">
                <div class="erp-table-responsive">
                    <table class="erp-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 105px;">DATE</th>
                                <th class="text-center" style="width: 120px;">VOUCHER NO</th>
                                <th>DESCRIPTION</th>
                                <th style="width: 180px;">PARTY</th>
                                <th class="text-end" style="width: 130px;">DEBIT (DR)</th>
                                <th class="text-end" style="width: 130px;">CREDIT (CR)</th>
                                <th class="text-end pe-3" style="width: 150px;">BALANCE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $runningBalance = $account->opening_balance ?? 0;
                            @endphp

                            {{-- Opening Balance Row --}}
                            <tr style="background-color: #f8fafc;">
                                <td class="text-center font-monospace small text-muted">-</td>
                                <td class="text-center font-monospace small text-muted">-</td>
                                <td colspan="2" class="fw-bold text-dark" style="font-size: 0.80rem;">
                                    <i class="fas fa-clock text-primary me-1"></i> Opening Balance B/F
                                </td>
                                <td class="text-end font-monospace text-muted">-</td>
                                <td class="text-end font-monospace text-muted">-</td>
                                <td class="text-end pe-3 font-monospace fw-bold text-dark" style="font-size: 0.82rem;">
                                    Rs. {{ number_format(abs($runningBalance), 2) }}
                                    <span class="erp-badge {{ $runningBalance >= 0 ? 'badge-dr' : 'badge-cr' }} ms-1">
                                        {{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}
                                    </span>
                                </td>
                            </tr>

                            @forelse ($entries as $entry)
                                @php
                                    $debit = $entry->debit ?? 0;
                                    $credit = $entry->credit ?? 0;
                                    $runningBalance = $runningBalance + $debit - $credit;

                                    $voucherNo = '-';
                                    if ($entry->source && $entry->source->voucher_no) {
                                        $voucherNo = $entry->source->voucher_no;
                                    } elseif ($entry->source && $entry->source->invoice_no) {
                                        $voucherNo = $entry->source->invoice_no;
                                    }

                                    // Resolve party name and type
                                    $partyName = '';
                                    $partyType = '';
                                    $partyBadgeClass = 'bg-secondary';
                                    if ($entry->party) {
                                        if ($entry->party_type === 'App\\Models\\Customer') {
                                            $partyName = $entry->party->customer_name ?? 'Unknown';
                                            $partyType = 'Customer';
                                            $partyBadgeClass = 'bg-primary';
                                        } elseif ($entry->party_type === 'App\\Models\\Vendor') {
                                            $partyName = $entry->party->name ?? 'Unknown';
                                            $partyType = 'Vendor';
                                            $partyBadgeClass = 'bg-warning text-dark';
                                        } else {
                                            $partyName = $entry->party->title ?? $entry->party->name ?? 'Account';
                                            $partyType = 'Account';
                                            $partyBadgeClass = 'bg-info text-dark';
                                        }
                                    }
                                @endphp
                                <tr>
                                    {{-- Date --}}
                                    <td class="text-center font-monospace small text-muted">
                                        {{ $entry->entry_date->format('d/m/Y') }}
                                    </td>

                                    {{-- Voucher No --}}
                                    <td class="text-center">
                                        <span class="badge bg-light text-primary border font-monospace" style="font-size: 0.72rem; padding: 3px 6px;">
                                            {{ $voucherNo }}
                                        </span>
                                    </td>

                                    {{-- Description --}}
                                    <td>
                                        <span class="text-dark fw-semibold" style="font-size: 0.78rem;">
                                            {{ $entry->description }}
                                        </span>
                                    </td>

                                    {{-- Party --}}
                                    <td>
                                        @if ($partyName)
                                            <div class="d-flex align-items-center gap-1.5">
                                                <span class="badge {{ $partyBadgeClass }}" style="font-size: 0.65rem;">{{ $partyType }}</span>
                                                <span class="fw-bold text-dark text-truncate" style="font-size: 0.78rem;" title="{{ $partyName }}">
                                                    {{ $partyName }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
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
                                    <td class="text-end pe-3 font-monospace">
                                        <span class="fw-bold text-dark" style="font-size: 0.82rem;">
                                            Rs. {{ number_format(abs($runningBalance), 2) }}
                                        </span>
                                        <span class="erp-badge {{ $runningBalance >= 0 ? 'badge-dr' : 'badge-cr' }} ms-1">
                                            {{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-book fa-3x mb-3 text-light"></i>
                                        <h6 class="fw-bold">No ledger journal entries found</h6>
                                        <p class="small text-muted mb-0">Select another date range to inspect historical transactions.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold pe-3">Period Totals:</td>
                                <td class="text-end font-monospace fw-bold" style="color: #059669; font-size: 0.85rem;">
                                    {{ number_format($totDebit, 2) }}
                                </td>
                                <td class="text-end font-monospace fw-bold" style="color: #dc2626; font-size: 0.85rem;">
                                    {{ number_format($totCredit, 2) }}
                                </td>
                                <td class="text-end pe-3 font-monospace fw-bold text-primary" style="font-size: 0.85rem;">
                                    Rs. {{ number_format(abs($closeBal), 2) }} {{ $closeBal >= 0 ? 'Dr' : 'Cr' }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

