@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ==========================================================================
       Enterprise ERP Design System - Receipts Vouchers
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

    /* Top Action Buttons */
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

    /* KPI Metric Cards */
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

    /* Single-Row Filter System */
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
        gap: 10px;
        width: 100%;
        flex-wrap: nowrap;
    }
    .erp-filter-item {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }
    .erp-item-search { flex: 1.5; min-width: 160px; }
    .erp-item-type { flex: 1.1; min-width: 120px; }
    .erp-item-status { flex: 1; min-width: 110px; }
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
        cursor: pointer;
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
        cursor: pointer;
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
        overflow: visible !important;
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
        padding: 0.6rem 0.5rem !important;
        border: 1px solid #bfdbfe !important;
        border-bottom: 2px solid #60a5fa !important;
        white-space: nowrap;
        vertical-align: middle;
    }
    .erp-table tbody td {
        padding: 0.55rem 0.5rem !important;
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

    /* Voucher / Tag Badge */
    .erp-bill-tag {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        font-size: 0.75rem;
        color: #2563eb;
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 0.2rem 0.5rem;
        border-radius: 5px;
        display: inline-block;
        white-space: nowrap;
    }

    /* Avatar & Party Meta */
    .erp-avatar {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
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
        padding: 0.22rem 0.55rem;
        border-radius: 50px;
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
    .erp-badge.badge-draft {
        background-color: #fffbeb;
        color: #b45309;
        border-color: #fde68a;
    }
    .erp-badge.badge-danger {
        background-color: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .badge-type-pill {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        padding: 0.18rem 0.5rem;
        border-radius: 50px;
        font-size: 0.68rem;
        font-weight: 600;
    }

    .badge-party-type {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 0.1rem 0.38rem;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 600;
    }

    /* Print Action Button */
    .btn-erp-print {
        background-color: #ffffff;
        border: 1.5px solid #fecaca;
        color: #dc2626 !important;
        font-weight: 600;
        font-size: 0.72rem;
        border-radius: 6px;
        padding: 0.3rem 0.65rem;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        text-decoration: none;
    }
    .btn-erp-print:hover {
        background-color: #fef2f2;
        border-color: #f87171;
        color: #b91c1c !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.15);
    }

    /* DataTables Custom Polish */
    .dataTables_wrapper .dataTables_length select {
        height: 32px !important;
        border: 1.5px solid #bfdbfe !important;
        border-radius: 6px !important;
        font-size: 0.78rem !important;
        padding: 0 0.5rem !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        height: 32px !important;
        border: 1.5px solid #bfdbfe !important;
        border-radius: 6px !important;
        font-size: 0.78rem !important;
        padding: 0.25rem 0.5rem !important;
        outline: none !important;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #ffffff !important;
        border: 1px solid #1d4ed8 !important;
        border-radius: 6px !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
        border-radius: 6px !important;
    }

    @media (max-width: 991px) {
        .erp-filter-single-row {
            flex-wrap: wrap !important;
        }
        .erp-filter-item {
            min-width: 100% !important;
        }
        .erp-item-actions {
            margin-left: 0 !important;
            width: 100%;
        }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">

            {{-- Page Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 erp-page-header">
                <div>
                    <h4 class="erp-title mb-0">
                        <i class="fas fa-receipt text-primary"></i> Receipts Vouchers
                    </h4>
                    <p class="erp-subtitle">View, filter and manage all customer and party receipt vouchers</p>
                </div>
                <div>
                    @can('receipts.voucher.create')
                        <a class="btn-erp-primary" href="{{ route('recepit_vochers') }}">
                            <i class="fas fa-plus"></i> Add Receipts Voucher
                        </a>
                    @endcan
                </div>
            </div>

            {{-- KPI Metric Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3 mb-3 mb-md-0">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Receipts</div>
                            <div class="erp-kpi-value">{{ number_format(count($receipts)) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3 mb-md-0">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Amount Received</div>
                            <div class="erp-kpi-value text-primary" style="color: #2563eb !important;">
                                Rs. {{ number_format($receipts->sum('total_amount'), 2) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3 mb-md-0">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Posted Vouchers</div>
                            <div class="erp-kpi-value text-success" style="color: #059669 !important;">
                                {{ number_format($receipts->where('status', 'posted')->count()) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3 mb-md-0">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Parties Count</div>
                            <div class="erp-kpi-value" style="color: #0284c7 !important;">
                                {{ number_format($receipts->pluck('party_name')->unique()->filter(function($p) { return $p != '-' && !empty($p); })->count()) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #f0f9ff; color: #0284c7;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Data Card --}}
            <div class="card erp-main-card">
                <div class="card-body p-3 p-md-4">

                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-3">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                            <button type="button" class="close ml-auto ms-auto" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-3">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ session('error') }}</span>
                            <button type="button" class="close ml-auto ms-auto" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Single-Row Filter System with ERP Pills --}}
                    <div class="erp-filter-card">
                        <div class="erp-filter-single-row">
                            <div class="erp-filter-item erp-item-search">
                                <label class="erp-filter-label"><i class="fas fa-search text-primary"></i> Search Voucher</label>
                                <input type="text" id="voucherSearchInput" class="form-control erp-filter-input" placeholder="Voucher #, Party, Remarks...">
                            </div>

                            <div class="erp-filter-item erp-item-type">
                                <label class="erp-filter-label"><i class="fas fa-user-tag text-primary"></i> Party Type</label>
                                <select id="voucherTypeFilter" class="form-control form-select erp-filter-input">
                                    <option value="">All Types</option>
                                    <option value="Customer">Customer</option>
                                    <option value="Vendor">Vendor</option>
                                    <option value="Account">Account</option>
                                </select>
                            </div>

                            <div class="erp-filter-item erp-item-status">
                                <label class="erp-filter-label"><i class="fas fa-toggle-on text-primary"></i> Status</label>
                                <select id="voucherStatusFilter" class="form-control form-select erp-filter-input">
                                    <option value="">All Status</option>
                                    <option value="Posted">Posted</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>

                            <div class="erp-filter-item erp-item-actions ms-auto">
                                <div class="d-flex align-items-center gap-1" style="gap: 6px;">
                                    <button type="button" id="btnResetVoucherFilters" class="btn btn-erp-pill-outline" title="Reset Filters">
                                        <i class="fas fa-undo"></i> <span>Reset</span>
                                    </button>
                                    <button type="button" id="btnApplyVoucherFilters" class="btn btn-erp-pill-primary" title="Apply Filter">
                                        <i class="fas fa-filter"></i> <span>Filter</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Data Table --}}
                    <div class="erp-table-responsive">
                        <table id="receiptsVouchersTable" class="table erp-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th style="min-width: 130px;">VOUCHER NO</th>
                                    <th style="min-width: 100px;">DATE</th>
                                    <th class="text-center" style="width: 90px;">TYPE</th>
                                    <th style="min-width: 180px;">PARTY / ACCOUNT</th>
                                    <th style="min-width: 220px;">REMARKS</th>
                                    <th class="text-end" style="min-width: 120px;">AMOUNT (RS)</th>
                                    <th class="text-center" style="width: 90px;">STATUS</th>
                                    <th class="text-center" style="width: 80px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receipts as $item)
                                    @php
                                        $party = $item->party_name ?: '-';
                                        $initial = strtoupper(substr(trim($party), 0, 1)) ?: 'P';
                                        $typeLabel = $item->type_label ?: 'Party';
                                        $isPosted = strtolower($item->status) === 'posted';
                                        $isDraft = strtolower($item->status) === 'draft';
                                    @endphp
                                    <tr>
                                        {{-- Row Number --}}
                                        <td class="text-center font-weight-bold text-muted" style="font-size: 0.75rem;">
                                            {{ $loop->iteration }}
                                        </td>

                                        {{-- Voucher Number Badge --}}
                                        <td>
                                            <span class="erp-bill-tag">
                                                <i class="fas fa-hashtag text-primary me-1" style="font-size: 0.65rem;"></i>{{ $item->voucher_no }}
                                            </span>
                                        </td>

                                        {{-- Date --}}
                                        <td>
                                            <span class="text-dark font-weight-semibold" style="font-size: 0.78rem;">
                                                <i class="far fa-calendar-alt text-muted mr-1"></i>{{ $item->date ? $item->date->format('d/m/Y') : '-' }}
                                            </span>
                                        </td>

                                        {{-- Voucher Type Pill --}}
                                        <td class="text-center">
                                            <span class="badge-type-pill">
                                                {{ ucfirst($item->payment_from ?? 'Receipt') }}
                                            </span>
                                        </td>

                                        {{-- Party / Account with Initials Avatar --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="erp-avatar erp-avatar-registered">
                                                    {{ $initial }}
                                                </div>
                                                <div class="d-flex flex-column text-truncate">
                                                    <span class="font-weight-bold text-dark text-truncate" style="font-size: 0.80rem;" title="{{ $party }}">
                                                        {{ $party }}
                                                    </span>
                                                    <div>
                                                        <span class="badge-party-type">{{ $typeLabel }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Remarks --}}
                                        <td>
                                            <span class="text-muted text-break" style="font-size: 0.76rem;" title="{{ $item->remarks }}">
                                                {{ Str::limit($item->remarks ?: 'No remarks provided', 55) }}
                                            </span>
                                        </td>

                                        {{-- Amount --}}
                                        <td class="text-end">
                                            <span class="font-weight-bold text-dark" style="font-size: 0.84rem; font-family: ui-monospace, monospace;">
                                                {{ number_format($item->total_amount, 2) }}
                                            </span>
                                        </td>

                                        {{-- Status Badge --}}
                                        <td class="text-center">
                                            @if ($isPosted)
                                                <span class="erp-badge badge-posted">
                                                    <i class="fas fa-check-circle"></i> Posted
                                                </span>
                                            @elseif ($isDraft)
                                                <span class="erp-badge badge-draft">
                                                    <i class="fas fa-clock"></i> Draft
                                                </span>
                                            @else
                                                <span class="erp-badge badge-danger">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Action --}}
                                        <td class="text-center">
                                            <a href="{{ route('print', $item->id) }}" target="_blank"
                                                class="btn-erp-print" title="Print Receipt Voucher">
                                                <i class="fas fa-print"></i> Print
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fas fa-receipt fa-3x mb-3 text-light"></i>
                                            <h6 class="font-weight-bold">No receipt vouchers found</h6>
                                            <p class="small text-muted mb-3">Record your first receipt voucher to track payments.</p>
                                            @can('receipts.voucher.create')
                                                <a href="{{ route('recepit_vochers') }}" class="btn-erp-primary">
                                                    <i class="fas fa-plus"></i> Add Receipts Voucher
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Initialize DataTable with ERP polish
        let dataTable = null;
        if ($.fn.DataTable) {
            dataTable = $('#receiptsVouchersTable').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Quick search table...",
                    lengthMenu: "Show _MENU_ vouchers",
                    info: "Showing _START_ to _END_ of _TOTAL_ vouchers",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>'
                    }
                }
            });
        }

        // Real-time Single-Row Filter System
        function applyCustomFilters() {
            const searchTerm = $('#voucherSearchInput').val().toLowerCase().trim();
            const selectedType = $('#voucherTypeFilter').val().toLowerCase().trim();
            const selectedStatus = $('#voucherStatusFilter').val().toLowerCase().trim();

            if (dataTable) {
                // If DataTables is active, use DataTables search and custom column filters
                dataTable.search(searchTerm);
                
                // Column 4 is Party/Account (contains Customer, Vendor, Account badge)
                dataTable.column(4).search(selectedType);
                
                // Column 7 is Status (Posted, Draft)
                dataTable.column(7).search(selectedStatus);

                dataTable.draw();
            } else {
                // Fallback direct DOM filter
                $('#receiptsVouchersTable tbody tr').each(function() {
                    const rowText = $(this).text().toLowerCase();
                    const rowType = $(this).find('td:nth-child(5)').text().toLowerCase();
                    const rowStatus = $(this).find('td:nth-child(8)').text().toLowerCase();

                    const matchesSearch = !searchTerm || rowText.indexOf(searchTerm) > -1;
                    const matchesType = !selectedType || rowType.indexOf(selectedType) > -1;
                    const matchesStatus = !selectedStatus || rowStatus.indexOf(selectedStatus) > -1;

                    if (matchesSearch && matchesType && matchesStatus) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        }

        // Event listeners for filter triggers
        $('#btnApplyVoucherFilters').on('click', applyCustomFilters);
        $('#voucherSearchInput').on('keyup', function(e) {
            applyCustomFilters();
        });
        $('#voucherTypeFilter, #voucherStatusFilter').on('change', function() {
            applyCustomFilters();
        });

        // Reset Filters Button
        $('#btnResetVoucherFilters').on('click', function() {
            $('#voucherSearchInput').val('');
            $('#voucherTypeFilter').val('');
            $('#voucherStatusFilter').val('');

            if (dataTable) {
                dataTable.search('').columns().search('').draw();
            } else {
                $('#receiptsVouchersTable tbody tr').show();
            }
        });
    });
</script>
@endsection
