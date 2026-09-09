@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ==========================================================================
       Enterprise ERP Design System - Raw Material Usage & Issue Management
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
    }
    .btn-erp-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
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
    }
    .btn-erp-outline-danger:hover {
        background-color: #fef2f2;
        border-color: #f87171;
        color: #b91c1c !important;
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
    .erp-item-quick { flex: 1.1; min-width: 105px; }
    .erp-item-date { flex: 1; min-width: 95px; }
    .erp-item-bill { flex: 0.9; min-width: 85px; }
    .erp-item-status { flex: 1.1; min-width: 105px; }
    .erp-item-customer { flex: 1.4; min-width: 130px; }
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
        padding: 0.5rem 0.35rem !important;
        border: 1px solid #bfdbfe !important;
        border-bottom: 2px solid #60a5fa !important;
        white-space: nowrap;
        vertical-align: middle;
    }
    .erp-table tbody td {
        padding: 0.45rem 0.35rem !important;
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

    /* Bill Tag (Exact Sales UI) */
    .erp-bill-tag {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        font-size: 0.75rem;
        color: #2563eb;
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        display: inline-block;
    }

    /* Avatar & Customer Meta (Exact Sales UI) */
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

    /* Table Action Dropdown Button (Exact Sales UI) */
    .btn-erp-table-action {
        background-color: #ffffff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        font-weight: 600;
        border-radius: 5px;
        height: 26px;
        padding: 0 0.5rem;
        font-size: 0.70rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .btn-erp-table-action:hover,
    .btn-erp-table-action:focus,
    .btn-erp-table-action[aria-expanded="true"] {
        background-color: #eff6ff;
        border-color: #3b82f6;
        color: #1e40af;
    }

    /* ERP Status Badges (Exact Sales UI) */
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
                        <i class="fas fa-boxes-packing text-primary"></i> Raw Material Usage
                    </h4>
                    <p class="erp-subtitle">Executive ledger of raw material consumption, warehouse stock deductions, and production issues</p>
                </div>
                <div class="d-flex gap-2">
                    @can('material.usage.report.view')
                        <a class="btn-erp-outline-danger" href="{{ route('report.material_usage') }}">
                            <i class="fas fa-chart-pie"></i> Report
                        </a>
                    @endcan
                    @can('material.usage.create')
                        <a class="btn-erp-primary" href="{{ route('material_usage.create') }}">
                            <i class="fas fa-plus-circle"></i> Issue Material
                        </a>
                    @endcan
                </div>
            </div>

            {{-- KPI Metric Cards (Exact 4 Cards layout) --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Invoices</div>
                            <div class="erp-kpi-value">{{ number_format($kpiMetrics['total_usages']) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Net Value</div>
                            <div class="erp-kpi-value text-success">Rs. {{ number_format($kpiMetrics['total_cost'], 2) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Quantity</div>
                            <div class="erp-kpi-value text-warning" style="color: #d97706 !important;">{{ number_format($kpiMetrics['total_qty'], 2) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #fffbeb; color: #d97706;">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Raw Items Issued</div>
                            <div class="erp-kpi-value" style="color: #0284c7 !important;">{{ number_format($kpiMetrics['total_items']) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #f0f9ff; color: #0284c7;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Data Card --}}
            <div class="card erp-main-card">
                <div class="card-body p-3 p-md-4">

                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ session('error') }}</span>
                            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Modern Single-Row Filter System (Exact Sales UI) --}}
                    <div class="erp-filter-card" id="filterPanelContainer">
                        <form method="GET" action="{{ route('material_usage.index') }}" class="erp-filter-single-row" autocomplete="off">
                            <div class="erp-filter-item erp-item-quick">
                                <label class="erp-filter-label"><i class="far fa-clock text-primary"></i> Period</label>
                                <select id="quick_filter" name="period" class="form-select erp-filter-input">
                                    <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom</option>
                                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Today</option>
                                    <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>This Week</option>
                                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>This Month</option>
                                    <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>This Year</option>
                                </select>
                            </div>

                            <div class="erp-filter-item erp-item-date">
                                <label class="erp-filter-label"><i class="far fa-calendar-alt text-primary"></i> From</label>
                                <input type="text" class="form-control erp-filter-input datepicker-custom bg-white" name="start_date" id="filter_from_date" value="{{ request('start_date') }}" placeholder="dd/mm/yy">
                            </div>

                            <div class="erp-filter-item erp-item-date">
                                <label class="erp-filter-label"><i class="far fa-calendar-check text-primary"></i> To</label>
                                <input type="text" class="form-control erp-filter-input datepicker-custom bg-white" name="end_date" id="filter_to_date" value="{{ request('end_date') }}" placeholder="dd/mm/yy">
                            </div>

                            <div class="erp-filter-item erp-item-bill">
                                <label class="erp-filter-label"><i class="fas fa-hashtag text-primary"></i> Bill#</label>
                                <input type="text" class="form-control erp-filter-input" name="search_voucher" id="filter_bill_no" value="{{ request('search_voucher') }}" placeholder="Bill ID...">
                            </div>

                            <div class="erp-filter-item erp-item-customer">
                                <label class="erp-filter-label"><i class="fas fa-cubes text-primary"></i> Material</label>
                                <select class="form-select erp-filter-input" name="product_id" id="filter_product_id">
                                    <option value="">All Materials</option>
                                    @foreach($rawMaterialsList ?? [] as $rmItem)
                                        <option value="{{ $rmItem->id }}" {{ request('product_id') == $rmItem->id ? 'selected' : '' }}>{{ $rmItem->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="erp-filter-item erp-item-actions ms-auto">
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('material_usage.index') }}" class="btn btn-erp-pill-outline" id="btnReset" title="Reset Filters">
                                        <i class="fas fa-undo"></i> <span>Reset</span>
                                    </a>
                                    <button type="submit" class="btn btn-erp-pill-primary" id="btnSearch" title="Apply Filter">
                                        <i class="fas fa-filter"></i> <span>Filter</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Table Container (Exact Sales UI) --}}
                    <div class="erp-table-responsive table-responsive">
                        <table class="table erp-table datanew" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="ps-2 text-center" style="width: 65px;">BILL#</th>
                                    <th style="min-width: 140px;">ISSUED BY</th>
                                    <th style="width: 130px;">M.BILL / REF</th>
                                    <th style="min-width: 180px;">PRODUCTS</th>
                                    <th class="text-center" style="width: 50px;">QTY</th>
                                    <th class="text-end" style="width: 90px;">AVG RATE</th>
                                    <th class="text-end" style="width: 100px;">NET TOTAL</th>
                                    <th class="text-center" style="width: 80px;">DATE</th>
                                    <th class="text-center" style="width: 80px;">STATUS</th>
                                    <th class="pe-2 text-center" style="width: 80px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="materialUsageTableBody">
                                @forelse($usages as $u)
                                    @php
                                        $materialNames = $u->items->map(function($it) {
                                            return ($it->product->item_name ?? 'Item') . ' (' . number_format($it->qty_used, 1) . ' ' . $it->unit_name . ')';
                                        })->implode(', ');

                                        $issuedBy = $u->user->name ?? 'Admin';
                                        $initial = strtoupper(substr($issuedBy, 0, 1));
                                        $avgRate = $u->total_qty > 0 ? ($u->total_cost / $u->total_qty) : 0;
                                    @endphp
                                    <tr class="border-bottom-0">
                                        {{-- Bill Tag --}}
                                        <td class="ps-2 text-center" data-sort="{{ $u->id }}">
                                            <span class="erp-bill-tag">#{{ $u->usage_no }}</span>
                                        </td>

                                        {{-- User / Issued By with Avatar --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-1.5" style="max-width: 140px;">
                                                <div class="erp-avatar erp-avatar-registered">
                                                    {{ $initial }}
                                                </div>
                                                <div class="d-flex flex-column text-truncate">
                                                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.78rem;" title="{{ $issuedBy }}">{{ $issuedBy }}</span>
                                                    <span class="text-muted text-truncate" style="font-size: 0.65rem; font-weight: 500;">Warehouse</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Purpose / Ref --}}
                                        <td>
                                            <span class="font-monospace text-muted" style="font-size: 0.75rem;">
                                                {{ $u->purpose ?: ($u->remarks ?: '-') }}
                                            </span>
                                        </td>

                                        {{-- Product Names --}}
                                        <td title="{{ $materialNames }}" class="text-muted small" style="max-width: 180px;">
                                            <div class="text-truncate fw-semibold text-dark" style="max-width: 180px; font-size: 0.78rem;">
                                                {{ $materialNames }}
                                            </div>
                                        </td>

                                        {{-- Qty --}}
                                        <td class="text-center font-monospace fw-semibold text-dark" style="font-size: 0.78rem;">
                                            {{ number_format($u->total_qty, 2) }}
                                        </td>

                                        {{-- Avg Rate / Gross --}}
                                        <td class="text-end fw-bold text-dark font-monospace" style="font-size: 0.78rem;">
                                            Rs. {{ number_format($avgRate, 2) }}
                                        </td>

                                        {{-- Net Total --}}
                                        <td class="text-end fw-bold font-monospace" style="color: #047857; font-size: 0.80rem;">
                                            Rs. {{ number_format($u->total_cost, 2) }}
                                        </td>

                                        {{-- Date --}}
                                        <td class="text-nowrap small text-muted font-monospace text-center" style="font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($u->date)->format('d/m/Y') }}
                                        </td>

                                        {{-- Status Badge --}}
                                        <td class="text-nowrap text-center">
                                            <span class="erp-badge badge-posted">
                                                <i class="fas fa-check-circle me-1"></i>Issued
                                            </span>
                                        </td>

                                        {{-- Actions Dropdown (Exact Sales UI) --}}
                                        <td class="pe-2 text-center text-nowrap">
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-erp-table-action dropdown-toggle shadow-none" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v small me-1"></i> Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3 py-2" style="min-width: 175px;">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 btn-view-modal" href="javascript:void(0)" data-id="{{ $u->id }}">
                                                            <i class="fas fa-eye text-primary fa-fw"></i> Quick View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('material_usage.show', $u->id) }}" target="_blank">
                                                            <i class="fas fa-file-invoice text-info fa-fw"></i> View Slip
                                                        </a>
                                                    </li>
                                                    @can('material.usage.delete')
                                                        <li class="border-top my-1"></li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger btn-delete-voucher" href="javascript:void(0)" data-url="{{ route('material_usage.destroy', $u->id) }}" data-no="{{ $u->usage_no }}">
                                                                <i class="fas fa-trash-alt text-danger fa-fw"></i> Cancel &amp; Revert
                                                            </a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                                            <h6 class="fw-bold">No material issue vouchers found</h6>
                                            <p class="small text-muted mb-3">Click below to record your first raw material usage.</p>
                                            <a href="{{ route('material_usage.create') }}" class="btn btn-erp-primary">
                                                <i class="fas fa-plus-circle mr-1"></i> Issue Material Now
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($usages->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="small text-muted">
                                Showing {{ $usages->firstItem() }} to {{ $usages->lastItem() }} of {{ $usages->total() }} vouchers
                            </span>
                            <div>
                                {{ $usages->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal for Quick Inspection -->
<div class="modal fade" id="usageDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" id="modalContentContainer" style="border-radius: 12px; overflow: hidden;">
            <div class="p-5 text-center text-muted">
                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                <p>Loading voucher details...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/vendors/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Quick Period Filter Auto-Change
        $('#quick_filter').on('change', function() {
            var val = $(this).val();
            if (val !== 'custom') {
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $(this).closest('form').submit();
            }
        });

        // Quick View Modal
        $(document).on('click', '.btn-view-modal', function(e) {
            e.preventDefault();
            var usageId = $(this).data('id');
            $('#modalContentContainer').html('<div class="p-5 text-center text-muted"><i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i><p>Loading voucher details...</p></div>');
            $('#usageDetailModal').modal('show');

            $.ajax({
                url: "{{ url('/material-usage') }}/" + usageId,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#modalContentContainer').html(res.html);
                    } else {
                        $('#modalContentContainer').html('<div class="p-4 text-center text-danger">Failed to load voucher details.</div>');
                    }
                },
                error: function() {
                    $('#modalContentContainer').html('<div class="p-4 text-center text-danger">An error occurred while loading the voucher.</div>');
                }
            });
        });

        // SweetAlert2 Confirmation for Voucher Cancellation & Stock Restoration
        $(document).on('click', '.btn-delete-voucher', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var voucherNo = $(this).data('no');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Cancel Voucher #' + voucherNo + '?',
                    text: 'This will delete the voucher and restore deducted raw material stock back to the warehouse!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Cancel & Restore Stock',
                    cancelButtonText: 'Dismiss'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = $('<form>', {
                            'method': 'POST',
                            'action': url
                        });
                        form.append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}' }));
                        form.append($('<input>', { 'type': 'hidden', 'name': '_method', 'value': 'DELETE' }));
                        $('body').append(form);
                        form.submit();
                    }
                });
            } else {
                if (confirm('Are you sure you want to cancel voucher #' + voucherNo + '? Deducted stock will be restored.')) {
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': url
                    });
                    form.append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}' }));
                    form.append($('<input>', { 'type': 'hidden', 'name': '_method', 'value': 'DELETE' }));
                    $('body').append(form);
                    form.submit();
                }
            }
        });
    });
</script>
@endsection