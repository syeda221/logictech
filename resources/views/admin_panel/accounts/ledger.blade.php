@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap');

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

    .erp-ledger-wrap {
        font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
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
        border: 1.5px solid #dbeafe;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
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
        font-size: 0.90rem;
        font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .erp-table thead th {
        background-color: #eff6ff !important;
        color: #1e40af !important;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.75rem 0.85rem;
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
        padding: 0.70rem 0.85rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #f1f5f9;
        color: #1e293b;
        font-size: 0.90rem;
        line-height: 1.45;
        background-color: #ffffff;
    }
    .erp-table tbody tr:hover td {
        background-color: #f8fbff;
    }
    .erp-table tfoot td {
        background-color: #f8fafc;
        border-top: 2px solid #93c5fd;
        font-weight: 700;
        padding: 0.85rem 1rem;
        font-size: 0.95rem;
    }

    /* Column Specific Styles */
    .erp-col-date { width: 110px; text-align: center; }
    .erp-col-voucher { width: 140px; text-align: center; }
    .erp-col-desc { min-width: 290px; width: 330px; }
    .erp-col-party { min-width: 190px; }
    .erp-col-dr { width: 135px; text-align: right; }
    .erp-col-cr { width: 135px; text-align: right; }
    .erp-col-bal { width: 160px; text-align: right; }

    /* Voucher / Tag Badge */
    .erp-bill-tag {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 600;
        font-size: 0.82rem;
        color: #2563eb;
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        display: inline-block;
        white-space: nowrap;
    }

    /* Avatar & Party Meta (Matches Receipts & Sales ERP) */
    .erp-avatar {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.78rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .erp-avatar-registered {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
    }
    .badge-party-type {
        font-size: 0.70rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 1.5px 7px;
        border-radius: 4px;
        display: inline-block;
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    /* ERP Status Badges for DR/CR */
    .erp-badge {
        font-size: 0.74rem;
        font-weight: 700;
        padding: 2.5px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        letter-spacing: 0.02em;
    }
    .badge-dr {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .badge-cr {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .erp-amount-num {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        font-size: 0.92rem;
        letter-spacing: -0.01em;
    }

    /* 12-Month Calendar Overview Cards */
    .erp-calendar-section {
        background: #ffffff;
        border: 1.5px solid #dbeafe;
        border-radius: 14px;
        padding: 1.25rem 1.35rem;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.05);
        margin-bottom: 1.75rem;
    }
    .erp-calendar-header {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 0.85rem;
    }
    .erp-month-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.90rem 0.95rem;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 110px;
        user-select: none;
    }
    .erp-month-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px -2px rgba(37, 99, 235, 0.14);
        border-color: #3b82f6;
    }
    .erp-month-card.has-data {
        border-color: #bfdbfe;
        background: linear-gradient(180deg, #ffffff 0%, #f0f7ff 100%);
    }
    .erp-month-card.has-data:hover {
        border-color: #2563eb;
        background: #ffffff;
    }
    .erp-month-card.empty-month {
        opacity: 0.72;
        background: #fbfcfe;
    }
    .erp-month-card.empty-month:hover {
        opacity: 1;
        border-color: #cbd5e1;
    }
    .erp-month-card.is-current-month {
        border: 2px solid #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .erp-month-card.is-current-month::after {
        content: 'Current';
        position: absolute;
        top: -9px;
        right: 10px;
        background: #2563eb;
        color: #ffffff;
        font-size: 0.60rem;
        font-weight: 800;
        padding: 1px 6px;
        border-radius: 10px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .month-badge {
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 3px 8px;
        border-radius: 6px;
        background-color: #f1f5f9;
        color: #475569;
    }
    .month-badge.active {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .compact-amount-badge {
        font-size: 0.76rem;
        font-weight: 700;
        color: #059669;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        padding: 1px 7px;
        border-radius: 20px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    .month-amount-val {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0.35rem 0 0.15rem 0;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        letter-spacing: -0.01em;
    }
    .currency-symbol {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
    }
    .txn-count-pill {
        font-size: 0.72rem;
        font-weight: 600;
    }
    .click-icon {
        font-size: 0.70rem;
        transition: transform 0.15s ease;
    }
    .erp-month-card:hover .click-icon {
        transform: translateX(2px);
        color: #2563eb !important;
    }

    @media print {
        .header, .sidebar, .erp-filter-card, .erp-calendar-section, .erp-page-header .btn-erp-outline, .erp-page-header .btn-erp-primary, .btn-erp-outline-danger {
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

<div class="main-content erp-ledger-wrap">
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

            {{-- 12-Month Interactive Payment Calendar --}}
            <div class="erp-calendar-section">
                <div class="erp-calendar-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
                    <div>
                        <h5 class="m-0 font-weight-bold text-dark d-flex align-items-center" style="font-size: 1.05rem; gap: 8px;">
                            <i class="fas fa-calendar-alt text-primary"></i> 12-Month Payment & Activity Calendar
                            <span class="badge badge-primary px-2 py-1 ms-1" style="font-size: 0.72rem; font-weight: 700; border-radius: 6px;">Year {{ $selectedYear }}</span>
                        </h5>
                        <p class="text-muted small m-0 mt-1">
                            Click on any month card below to view date-wise breakdown of payments & vouchers (kb kb kis kis ko di hai).
                        </p>
                    </div>

                    {{-- Top Year Filter Dropdown --}}
                    <div class="d-flex align-items-center gap-2">
                        <label for="yearSelector" class="small font-weight-bold text-secondary mb-0 text-nowrap">Filter Year:</label>
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0 text-primary">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                            </div>
                            <select id="yearSelector" class="form-control form-control-sm border-left-0 font-weight-bold" onchange="changeCalendarYear(this.value)">
                                @foreach($availableYears as $yr)
                                    <option value="{{ $yr }}" {{ $yr == $selectedYear ? 'selected' : '' }}>Year {{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 12 Month Boxes Grid --}}
                <div class="row g-2 g-md-3">
                    @foreach($monthlyCalendar as $mKey => $m)
                        @php
                            $hasTxn = $m['count'] > 0;
                            $isCurrentMonth = ($selectedYear == intval(date('Y')) && $m['month_num'] == intval(date('n')));
                        @endphp
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                            <div class="erp-month-card {{ $hasTxn ? 'has-data' : 'empty-month' }} {{ $isCurrentMonth ? 'is-current-month' : '' }}"
                                 onclick="openMonthModal({{ $m['month_num'] }})"
                                 role="button"
                                 title="Click to view {{ $m['full_name'] }} detailed transactions">
                                
                                {{-- Month Name & Compact Amount Tag --}}
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="month-badge {{ $hasTxn ? 'active' : '' }}">
                                        {{ $m['short_name'] }}
                                    </span>
                                    @if($hasTxn)
                                        <span class="compact-amount-badge">
                                            {{ $m['compact_amount'] }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </div>

                                {{-- Main Pay Amount --}}
                                <div class="month-amount-val text-truncate" title="Total Outflow / Paid: Rs. {{ number_format($m['paid_amount'], 2) }}">
                                    @if($hasTxn)
                                        <span class="currency-symbol">Rs.</span> {{ number_format($m['paid_amount'], 0) }}
                                    @else
                                        <span class="text-muted" style="font-weight: 500; font-size: 0.85rem;">Rs. 0</span>
                                    @endif
                                </div>

                                {{-- Bottom Meta: Count & Arrow Icon --}}
                                <div class="d-flex align-items-center justify-content-between pt-1 border-top" style="border-color: #f1f5f9 !important;">
                                    <span class="txn-count-pill {{ $hasTxn ? 'text-primary font-weight-bold' : 'text-muted' }}">
                                        <i class="fas fa-receipt mr-1 me-1"></i> {{ $m['count'] }} {{ \Illuminate\Support\Str::plural('Txn', $m['count']) }}
                                    </span>
                                    <span class="click-icon text-muted">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                                <th class="erp-col-date">DATE</th>
                                <th class="erp-col-voucher">VOUCHER NO</th>
                                <th class="erp-col-desc">DESCRIPTION</th>
                                <th class="erp-col-party">PARTY</th>
                                <th class="erp-col-dr">DEBIT (DR)</th>
                                <th class="erp-col-cr">CREDIT (CR)</th>
                                <th class="erp-col-bal pr-3 pe-3">BALANCE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $runningBalance = $account->opening_balance ?? 0;
                            @endphp

                            {{-- Opening Balance Row --}}
                            <tr style="background-color: #f8fafc;">
                                <td class="erp-col-date font-monospace text-muted font-weight-bold" style="font-size: 0.86rem;">-</td>
                                <td class="erp-col-voucher font-monospace text-muted font-weight-bold" style="font-size: 0.86rem;">-</td>
                                <td colspan="2" class="font-weight-semibold text-dark" style="font-size: 0.90rem; color: #1e293b;">
                                    <i class="fas fa-history text-primary mr-1 me-1"></i> Opening Balance B/F
                                </td>
                                <td class="erp-col-dr font-monospace text-muted font-weight-bold" style="font-size: 0.86rem;">-</td>
                                <td class="erp-col-cr font-monospace text-muted font-weight-bold" style="font-size: 0.86rem;">-</td>
                                <td class="erp-col-bal pr-3 pe-3">
                                    <span class="erp-amount-num font-weight-bold text-dark" style="font-size: 0.94rem;">
                                        Rs. {{ number_format(abs($runningBalance), 2) }}
                                    </span>
                                    <span class="erp-badge {{ $runningBalance >= 0 ? 'badge-dr' : 'badge-cr' }} ml-1 ms-1">
                                        {{ $runningBalance >= 0 ? 'DR' : 'CR' }}
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
                                    $partyObj = $entry->party;
                                    $partyType = $entry->party_type;
                                    $partyName = '';

                                    // Fallback 1: Source party (VoucherMaster, Sale, Purchase, etc.)
                                    if (!$partyObj && $entry->source) {
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

                                    // Fallback 2: Check invoice in description or remarks
                                    if (!$partyObj) {
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

                                    // Fallback 3: Check sibling journal entry from the same source
                                    if (!$partyObj && !$partyName && $entry->source_type && $entry->source_id) {
                                        $sibling = \App\Models\JournalEntry::where('source_type', $entry->source_type)
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
                                        if ($partyObj instanceof \App\Models\Customer || str_contains($partyType, 'Customer')) {
                                            $partyName = $partyObj->customer_name ?? $partyObj->name ?? 'Customer';
                                            $partyType = 'Customer';
                                        } elseif ($partyObj instanceof \App\Models\Vendor || str_contains($partyType, 'Vendor')) {
                                            $partyName = $partyObj->name ?? 'Vendor';
                                            $partyType = 'Vendor';
                                        } elseif ($partyObj instanceof \App\Models\Account || str_contains($partyType, 'Account')) {
                                            $partyName = $partyObj->title ?? $partyObj->name ?? 'Account';
                                            $partyType = 'Account';
                                        } else {
                                            $partyName = $partyObj->name ?? $partyObj->customer_name ?? $partyObj->title ?? class_basename($partyType);
                                            $partyType = class_basename($partyType);
                                        }
                                    }

                                    $initial = strtoupper(substr(trim($partyName ?: 'P'), 0, 1));
                                @endphp
                                <tr>
                                    {{-- Date --}}
                                    <td class="erp-col-date">
                                        <span class="font-monospace text-dark font-weight-medium" style="font-size: 0.88rem;">
                                            {{ $entry->entry_date->format('d/m/Y') }}
                                        </span>
                                    </td>

                                    {{-- Voucher No --}}
                                    <td class="erp-col-voucher">
                                        <span class="erp-bill-tag">
                                            {{ $voucherNo }}
                                        </span>
                                    </td>

                                    {{-- Description --}}
                                    <td class="erp-col-desc">
                                        <span class="text-dark font-weight-medium" style="font-size: 0.88rem; line-height: 1.45; color: #1e293b; display: block;">
                                            {{ $entry->description }}
                                        </span>
                                    </td>

                                    {{-- Party --}}
                                    <td class="erp-col-party">
                                        @if ($partyName)
                                            <div class="d-flex align-items-center" style="gap: 8px;">
                                                <div class="erp-avatar erp-avatar-registered">
                                                    {{ $initial }}
                                                </div>
                                                <div class="d-flex flex-column text-truncate" style="line-height: 1.25;">
                                                    <span class="font-weight-bold text-dark text-truncate" style="font-size: 0.88rem;" title="{{ $partyName }}">
                                                        {{ $partyName }}
                                                    </span>
                                                    <div>
                                                        <span class="badge-party-type">{{ $partyType }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted font-weight-medium" style="font-size: 0.88rem;">—</span>
                                        @endif
                                    </td>

                                    {{-- Debit (Dr) --}}
                                    <td class="erp-col-dr">
                                        <span class="erp-amount-num font-weight-bold" style="color: #059669 !important;">
                                            {{ $debit > 0 ? number_format($debit, 2) : '-' }}
                                        </span>
                                    </td>

                                    {{-- Credit (Cr) --}}
                                    <td class="erp-col-cr">
                                        <span class="erp-amount-num font-weight-bold" style="color: #dc2626 !important;">
                                            {{ $credit > 0 ? number_format($credit, 2) : '-' }}
                                        </span>
                                    </td>

                                    {{-- Running Balance --}}
                                    <td class="erp-col-bal pr-3 pe-3">
                                        <span class="erp-amount-num font-weight-bold text-dark" style="font-size: 0.94rem;">
                                            Rs. {{ number_format(abs($runningBalance), 2) }}
                                        </span>
                                        <span class="erp-badge {{ $runningBalance >= 0 ? 'badge-dr' : 'badge-cr' }} ml-1 ms-1">
                                            {{ $runningBalance >= 0 ? 'DR' : 'CR' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-book fa-3x mb-3 text-light"></i>
                                        <h6 class="font-weight-bold">No ledger journal entries found</h6>
                                        <p class="small text-muted mb-0">Select another date range to inspect historical transactions.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right text-end font-weight-bold pr-3 pe-3 text-dark" style="font-size: 0.94rem;">Period Totals:</td>
                                <td class="erp-col-dr">
                                    <span class="erp-amount-num font-weight-bold" style="color: #059669 !important; font-size: 1.00rem;">
                                        {{ number_format($totDebit, 2) }}
                                    </span>
                                </td>
                                <td class="erp-col-cr">
                                    <span class="erp-amount-num font-weight-bold" style="color: #dc2626 !important; font-size: 1.00rem;">
                                        {{ number_format($totCredit, 2) }}
                                    </span>
                                </td>
                                <td class="erp-col-bal pr-3 pe-3">
                                    <span class="erp-amount-num font-weight-bold text-primary" style="font-size: 1.02rem; color: #2563eb !important;">
                                        Rs. {{ number_format(abs($closeBal), 2) }} {{ $closeBal >= 0 ? 'DR' : 'CR' }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Interactive Month Details Modal --}}
<div class="modal fade" id="monthDetailsModal" tabindex="-1" role="dialog" aria-labelledby="monthModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-dark m-0" id="monthModalTitle">
                            Month Transactions
                        </h5>
                        <p class="text-muted small m-0 mt-1" id="monthModalSubtitle">
                            Account: <strong>{{ $account->title }}</strong>
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center" style="gap: 10px;">
                    <span class="badge badge-success px-3 py-2 font-monospace" id="modalTotalPaidBadge" style="font-size: 0.85rem; font-weight: 700; border-radius: 8px;">
                        Paid: Rs. 0
                    </span>
                    <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; outline: none; border: none; background: transparent;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                    <div class="input-group input-group-sm" style="max-width: 300px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="modalSearchInput" class="form-control border-left-0" placeholder="Filter by party, description, voucher...">
                    </div>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <span class="small font-weight-bold text-secondary" id="modalEntryCount">0 transactions</span>
                        <a href="#" id="modalFilterMainLedgerBtn" class="btn btn-sm btn-outline-primary" style="font-size: 0.80rem; font-weight: 600; border-radius: 6px;">
                            <i class="fas fa-external-link-alt mr-1 me-1"></i> Open In Main Ledger
                        </a>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 480px;">
                    <table class="erp-table table-hover mb-0" id="modalTxnTable">
                        <thead>
                            <tr>
                                <th class="erp-col-date">DATE</th>
                                <th class="erp-col-voucher">VOUCHER NO</th>
                                <th class="erp-col-desc">DESCRIPTION</th>
                                <th class="erp-col-party">PARTY / PERSON (KIS KO DI HAI)</th>
                                <th class="erp-col-dr">DEBIT (DR)</th>
                                <th class="erp-col-cr">CREDIT (CR)</th>
                            </tr>
                        </thead>
                        <tbody id="modalTxnTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                        <tfoot id="modalTxnTableFoot">
                            <tr>
                                <td colspan="4" class="text-right text-end font-weight-bold pr-3 pe-3 text-dark">Total This Month:</td>
                                <td class="erp-col-dr font-weight-bold text-success font-monospace" id="modalFootDebit" style="font-size: 0.95rem;">Rs. 0</td>
                                <td class="erp-col-cr font-weight-bold text-danger font-monospace" id="modalFootCredit" style="font-size: 0.95rem;">Rs. 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between align-items-center">
                <span class="small text-muted font-italic">
                    <i class="fas fa-info-circle mr-1 me-1 text-primary"></i> Date-wise General Ledger transactions for {{ $account->title }}
                </span>
                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Monthly Data passed from Controller
    const monthlyCalendarData = @json($monthlyCalendar);
    let currentMonthEntries = [];

    function changeCalendarYear(year) {
        const url = new URL(window.location.href);
        url.searchParams.set('year', year);
        window.location.href = url.toString();
    }

    function formatNumber(num, decimals = 0) {
        return parseFloat(num || 0).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function openMonthModal(monthNum) {
        const m = monthlyCalendarData[monthNum];
        if (!m) return;

        currentMonthEntries = m.entries || [];

        // Set Title & Badges
        document.getElementById('monthModalTitle').innerHTML = '<i class="fas fa-calendar-alt text-primary mr-1 me-1"></i> ' + m.full_name + ' Details';
        document.getElementById('monthModalSubtitle').innerHTML = 'Account: <strong class="text-dark">{{ $account->title }}</strong> | Period: ' + m.from_date + ' to ' + m.to_date;
        document.getElementById('modalTotalPaidBadge').innerHTML = 'Paid / Outflow: Rs. ' + formatNumber(m.paid_amount, 2);
        document.getElementById('modalEntryCount').innerHTML = m.count + ' transaction' + (m.count === 1 ? '' : 's');

        // Update Filter Link
        const filterBtn = document.getElementById('modalFilterMainLedgerBtn');
        const baseUrl = "{{ route('accounts.ledger', $account->id) }}";
        filterBtn.href = baseUrl + '?from_date=' + m.from_date + '&to_date=' + m.to_date;

        // Render Table Rows
        renderModalTable(currentMonthEntries);

        // Reset search input
        const searchInput = document.getElementById('modalSearchInput');
        if (searchInput) searchInput.value = '';

        // Show Modal (Compatible with Bootstrap 4 & 5)
        const modalEl = document.getElementById('monthDetailsModal');
        if (window.bootstrap && bootstrap.Modal) {
            try {
                const bModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                bModal.show();
            } catch (e) {
                if (window.jQuery) $(modalEl).modal('show');
            }
        } else if (window.jQuery) {
            $(modalEl).modal('show');
        }
    }

    function renderModalTable(entries) {
        const tbody = document.getElementById('modalTxnTableBody');
        const footDebit = document.getElementById('modalFootDebit');
        const footCredit = document.getElementById('modalFootCredit');

        if (!entries || entries.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-calendar-times fa-2x mb-2 text-light"></i><p class="m-0 font-italic">No transactions recorded in this month.</p></td></tr>';
            footDebit.innerText = 'Rs. 0';
            footCredit.innerText = 'Rs. 0';
            return;
        }

        let totDr = 0;
        let totCr = 0;
        let html = '';

        entries.forEach(function(e) {
            totDr += parseFloat(e.debit || 0);
            totCr += parseFloat(e.credit || 0);

            const drHtml = e.debit > 0
                ? '<span class="erp-amount-num font-weight-bold" style="color: #059669;">' + formatNumber(e.debit, 2) + '</span>'
                : '<span class="text-muted font-monospace">-</span>';

            const crHtml = e.credit > 0
                ? '<span class="erp-amount-num font-weight-bold" style="color: #dc2626;">' + formatNumber(e.credit, 2) + '</span>'
                : '<span class="text-muted font-monospace">-</span>';

            const partyHtml = e.party_name && e.party_name !== '—'
                ? '<div class="d-flex align-items-center" style="gap: 8px;">' +
                    '<div class="erp-avatar erp-avatar-registered">' + (e.party_initial || 'P') + '</div>' +
                    '<div class="d-flex flex-column text-truncate" style="line-height: 1.25;">' +
                        '<span class="font-weight-bold text-dark text-truncate" title="' + e.party_name + '">' + e.party_name + '</span>' +
                        '<div><span class="badge-party-type">' + e.party_type + '</span></div>' +
                    '</div>' +
                  '</div>'
                : '<span class="text-muted font-weight-medium">—</span>';

            html += '<tr>' +
                '<td class="erp-col-date"><span class="font-monospace text-dark font-weight-medium" style="font-size: 0.88rem;">' + e.date + '</span></td>' +
                '<td class="erp-col-voucher"><span class="erp-bill-tag">' + (e.voucher_no || '-') + '</span></td>' +
                '<td class="erp-col-desc"><span class="text-dark font-weight-medium" style="font-size: 0.88rem; line-height: 1.45;">' + (e.description || '—') + '</span></td>' +
                '<td class="erp-col-party">' + partyHtml + '</td>' +
                '<td class="erp-col-dr">' + drHtml + '</td>' +
                '<td class="erp-col-cr">' + crHtml + '</td>' +
            '</tr>';
        });

        tbody.innerHTML = html;
        footDebit.innerText = 'Rs. ' + formatNumber(totDr, 2);
        footCredit.innerText = 'Rs. ' + formatNumber(totCr, 2);
    }

    // Modal Live Search Filter
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('modalSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                if (!q) {
                    renderModalTable(currentMonthEntries);
                    return;
                }
                const filtered = currentMonthEntries.filter(function(e) {
                    return (e.party_name && e.party_name.toLowerCase().includes(q)) ||
                           (e.party_type && e.party_type.toLowerCase().includes(q)) ||
                           (e.description && e.description.toLowerCase().includes(q)) ||
                           (e.voucher_no && e.voucher_no.toLowerCase().includes(q)) ||
                           (e.date && e.date.includes(q));
                });
                renderModalTable(filtered);
            });
        }
    });
</script>
@endsection

