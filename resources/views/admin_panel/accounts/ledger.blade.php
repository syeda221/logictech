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

            {{-- Compact Page Header (Heading Choti Kar Di) --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2 pb-2 border-bottom">
                <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                    <h5 class="m-0 font-weight-bold text-dark d-flex align-items-center" style="font-size: 1.10rem; gap: 6px;">
                        <i class="fas fa-book-open text-primary"></i> General Ledger
                    </h5>
                    <span class="badge bg-light text-primary border font-monospace px-2 py-1" style="font-size: 0.74rem; font-weight: 700;">
                        {{ $account->account_code }}
                    </span>
                    <span class="font-weight-bold text-dark" style="font-size: 0.90rem;">
                        {{ $account->title }}
                    </span>
                    <span class="text-muted small">|</span>
                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.70rem;">
                        Head: {{ $account->head->name ?? 'N/A' }}
                    </span>
                    <span class="badge {{ $account->type == 'Debit' ? 'bg-primary-subtle text-primary border-primary-subtle' : 'bg-success-subtle text-success border-success-subtle' }} px-2 py-1" style="font-size: 0.68rem; font-weight: 700;">
                        {{ $account->type }}
                    </span>
                </div>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-3 d-inline-flex align-items-center" onclick="window.print()" style="height: 32px; font-size: 0.78rem; font-weight: 600; border-radius: 6px; gap: 5px;">
                        <i class="fas fa-print"></i> Print Statement
                    </button>
                    @can('chart.of.accounts.view')
                        <a class="btn btn-primary btn-sm py-1 px-3 d-inline-flex align-items-center" href="{{ route('view_all') }}" style="height: 32px; font-size: 0.78rem; font-weight: 600; border-radius: 6px; gap: 5px;">
                            <i class="fas fa-arrow-left"></i> Chart of Accounts
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Unified Top Control / Normal Filters Bar (Normal Filters Upar Laga Diye) --}}
            <div class="erp-calendar-section p-2 mb-3 bg-white" style="border-radius: 10px;">
                <form method="GET" action="{{ route('accounts.ledger', $account->id) }}" class="d-flex flex-wrap align-items-center justify-content-between m-0" style="gap: 8px;">
                    <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                        {{-- Year Filter Dropdown --}}
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <span class="small font-weight-bold text-secondary text-nowrap"><i class="fas fa-calendar-alt text-primary"></i> Year:</span>
                            <select name="year" class="form-control form-control-sm font-weight-bold border" style="width: 100px; height: 32px; font-size: 0.80rem; border-radius: 6px;" onchange="this.form.submit()">
                                @foreach($availableYears as $yr)
                                    <option value="{{ $yr }}" {{ $yr == $selectedYear ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <span class="text-muted small d-none d-sm-inline">|</span>

                        {{-- From Date --}}
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <span class="small font-weight-bold text-secondary text-nowrap">From:</span>
                            <input type="text" name="from_date" value="{{ request('from_date') }}" class="form-control form-control-sm datepicker-custom border" placeholder="dd/mm/yyyy" style="width: 110px; height: 32px; font-size: 0.80rem; border-radius: 6px;">
                        </div>

                        {{-- To Date --}}
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <span class="small font-weight-bold text-secondary text-nowrap">To:</span>
                            <input type="text" name="to_date" value="{{ request('to_date') }}" class="form-control form-control-sm datepicker-custom border" placeholder="dd/mm/yyyy" style="width: 110px; height: 32px; font-size: 0.80rem; border-radius: 6px;">
                        </div>

                        {{-- Filter & Clear Buttons --}}
                        <button type="submit" class="btn btn-primary btn-sm px-3 font-weight-bold d-inline-flex align-items-center" style="height: 32px; font-size: 0.78rem; border-radius: 6px; gap: 4px;">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if(request()->filled('from_date') || request()->filled('to_date') || (request()->filled('year') && request('year') != date('Y')))
                            <a href="{{ route('accounts.ledger', $account->id) }}" class="btn btn-outline-secondary btn-sm px-2 d-inline-flex align-items-center" style="height: 32px; font-size: 0.78rem; border-radius: 6px; gap: 4px;" title="Reset All Filters">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </div>

                    <div class="d-flex align-items-center text-muted small" style="gap: 6px;">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span>Click any month card to view date-wise details</span>
                    </div>
                </form>
            </div>

            @php
                $openBal = $account->opening_balance ?? 0;
                $totDebit = $entries->sum('debit');
                $totCredit = $entries->sum('credit');
                $closeBal = $account->current_balance;
            @endphp

            {{-- 4 Compact Metric Summary Cards (Gap & Padding Optimized) --}}
            <div class="row g-2 g-md-3 mb-3">
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card py-2 px-3">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-blue" style="width: 38px; height: 38px; font-size: 1.05rem;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label" style="font-size: 0.72rem;">Opening Balance</div>
                            <div class="erp-kpi-val" style="font-size: 1.05rem;">Rs. {{ number_format(abs($openBal), 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card py-2 px-3">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-green" style="width: 38px; height: 38px; font-size: 1.05rem;">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label" style="font-size: 0.72rem;">Total Debit</div>
                            <div class="erp-kpi-val text-success" style="font-size: 1.05rem;">Rs. {{ number_format($totDebit, 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card py-2 px-3">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-amber" style="width: 38px; height: 38px; font-size: 1.05rem;">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label" style="font-size: 0.72rem;">Total Credit</div>
                            <div class="erp-kpi-val text-warning" style="font-size: 1.05rem;">Rs. {{ number_format($totCredit, 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card py-2 px-3">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-sky" style="width: 38px; height: 38px; font-size: 1.05rem;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label" style="font-size: 0.72rem;">Closing Balance</div>
                            <div class="erp-kpi-val text-primary" style="font-size: 1.05rem;">
                                Rs. {{ number_format(abs($closeBal), 0) }}
                                <span class="erp-badge {{ $closeBal >= 0 ? 'badge-dr' : 'badge-cr' }}" style="font-size: 0.60rem; padding: 1px 5px;">
                                    {{ $closeBal >= 0 ? 'Dr' : 'Cr' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 12-Month Interactive Payment Calendar Cards (Attractive & Spacing Optimized) --}}
            <div class="erp-calendar-section p-3 mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.75rem; font-weight: 700; border-radius: 6px;">{{ $selectedYear }}</span>
                        <h6 class="m-0 font-weight-bold text-dark" style="font-size: 0.96rem;">
                            12-Month Payment & Activity Overview
                        </h6>
                    </div>
                    <div class="small text-muted font-weight-medium">
                        Total Year Outflow: <strong class="text-danger">Rs. {{ number_format($monthlyCalendar ? array_sum(array_column($monthlyCalendar, 'paid_amount')) : 0, 0) }}</strong>
                    </div>
                </div>

                {{-- 12 Month Boxes Grid --}}
                <div class="row g-2 g-md-3">
                    @foreach($monthlyCalendar as $mKey => $m)
                        @php
                            $hasTxn = $m['count'] > 0;
                            $isCurrentMonth = ($selectedYear == intval(date('Y')) && $m['month_num'] == intval(date('n')));
                        @endphp
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-2">
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

            {{-- Optional: Detailed Statements only when Custom Date Filter is Applied (Nichy Wala Section Hata Diya Gaya Hai) --}}
            @if(request()->filled('from_date') && request()->filled('to_date'))
                <div class="erp-main-card mb-3">
                    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <i class="fas fa-filter text-primary"></i>
                            <h6 class="m-0 font-weight-bold text-dark" style="font-size: 0.92rem;">
                                Filtered Statement: {{ request('from_date') }} to {{ request('to_date') }}
                            </h6>
                            <span class="badge badge-info">{{ $entries->count() }} records</span>
                        </div>
                        <a href="{{ route('accounts.ledger', $account->id) }}" class="btn btn-sm btn-outline-secondary" style="font-size: 0.76rem;">
                            <i class="fas fa-times me-1"></i> Clear Filter & Return to Calendar
                        </a>
                    </div>
                    <div class="erp-table-responsive">
                        <table class="erp-table">
                            <thead>
                                <tr>
                                    <th class="erp-col-date">DATE</th>
                                    <th class="erp-col-voucher">VOUCHER NO</th>
                                    <th class="erp-col-desc">DESCRIPTION</th>
                                    <th class="erp-col-party">PARTY / PERSON</th>
                                    <th class="erp-col-dr">DEBIT (DR)</th>
                                    <th class="erp-col-cr">CREDIT (CR)</th>
                                    <th class="erp-col-bal pr-3 pe-3">BALANCE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningBalance = $account->opening_balance ?? 0;
                                @endphp
                                @forelse ($entries as $entry)
                                    @php
                                        $debit = $entry->debit ?? 0;
                                        $credit = $entry->credit ?? 0;
                                        $runningBalance = $runningBalance + $debit - $credit;

                                        $voucherNo = '-';
                                        if ($entry->source && isset($entry->source->voucher_no) && $entry->source->voucher_no) {
                                            $voucherNo = $entry->source->voucher_no;
                                        } elseif ($entry->source && isset($entry->source->invoice_no) && $entry->source->invoice_no) {
                                            $voucherNo = $entry->source->invoice_no;
                                        }

                                        $dispDesc = $entry->description ?? '—';
                                        if (($dispDesc === '—' || str_starts_with($dispDesc, 'Paid from')) && $entry->source && !empty($entry->source->remarks)) {
                                            $dispDesc = $entry->source->remarks;
                                        }

                                        // Resolve party
                                        $partyName = '';
                                        $partyType = 'General';
                                        if ($entry->party) {
                                            $partyName = $entry->party->name ?? $entry->party->customer_name ?? $entry->party->full_name ?? $entry->party->title ?? '';
                                            $partyType = class_basename($entry->party_type ?? 'Party');
                                        } elseif ($entry->source && isset($entry->source->remarks)) {
                                            if (preg_match('/(?:Salary\s+Paid\s+to|Salary\s+for|Paid\s+to)\s+([A-Za-z\s]+?)(?:\s*\(|\s+for|\s*$)/i', $entry->source->remarks, $pm)) {
                                                $partyName = trim($pm[1]);
                                                $partyType = 'Employee';
                                            }
                                        }
                                        $initial = strtoupper(substr(trim($partyName ?: 'P'), 0, 1));
                                    @endphp
                                    <tr>
                                        <td class="erp-col-date">
                                            <span class="font-monospace text-dark font-weight-medium">{{ $entry->entry_date->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="erp-col-voucher">
                                            <span class="erp-bill-tag">{{ $voucherNo }}</span>
                                        </td>
                                        <td class="erp-col-desc">
                                            <span class="text-dark font-weight-medium">{{ $dispDesc }}</span>
                                        </td>
                                        <td class="erp-col-party">
                                            @if ($partyName)
                                                <div class="d-flex align-items-center" style="gap: 8px;">
                                                    <div class="erp-avatar erp-avatar-registered">{{ $initial }}</div>
                                                    <div class="d-flex flex-column text-truncate" style="line-height: 1.25;">
                                                        <span class="font-weight-bold text-dark text-truncate" title="{{ $partyName }}">{{ $partyName }}</span>
                                                        <div><span class="badge-party-type">{{ $partyType }}</span></div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted font-weight-medium">—</span>
                                            @endif
                                        </td>
                                        <td class="erp-col-dr">
                                            <span class="erp-amount-num font-weight-bold text-success">{{ $debit > 0 ? number_format($debit, 2) : '-' }}</span>
                                        </td>
                                        <td class="erp-col-cr">
                                            <span class="erp-amount-num font-weight-bold text-danger">{{ $credit > 0 ? number_format($credit, 2) : '-' }}</span>
                                        </td>
                                        <td class="erp-col-bal pr-3 pe-3">
                                            <span class="erp-amount-num font-weight-bold text-dark">Rs. {{ number_format(abs($runningBalance), 2) }}</span>
                                            <span class="erp-badge {{ $runningBalance >= 0 ? 'badge-dr' : 'badge-cr' }} ml-1 ms-1">{{ $runningBalance >= 0 ? 'DR' : 'CR' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <p class="m-0 font-italic">No transactions found for the selected date range.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

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

