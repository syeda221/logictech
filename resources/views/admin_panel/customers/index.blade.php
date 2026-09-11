@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ==========================================================================
       Enterprise ERP Design System - Customer Management
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
    .erp-item-id { flex: 0.9; min-width: 85px; }
    .erp-item-name { flex: 1.5; min-width: 140px; }
    .erp-item-status { flex: 1.1; min-width: 105px; }
    .erp-item-source { flex: 1.1; min-width: 105px; }
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
        overflow: visible !important;
    }
    .erp-table-responsive {
        border-radius: 8px;
        width: 100%;
        overflow-x: auto;
        min-height: 380px;
        padding-bottom: 140px;
    }
    .erp-table-responsive .dropdown-menu {
        z-index: 1060 !important;
        right: 0 !important;
        left: auto !important;
        top: 100% !important;
        bottom: auto !important;
        margin-top: 2px !important;
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

    /* Bill / Customer Tag */
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
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.70rem;
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
    .erp-badge.badge-inactive {
        background-color: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    /* Source Badges */
    .badge-src-website {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .badge-src-both {
        background-color: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-src-manual {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
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
                        <i class="fas fa-users text-primary"></i> Customer Management
                    </h4>
                    <p class="erp-subtitle">Directory of active clients, credit limits, account statuses, and ledger statements</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a class="btn-erp-outline-danger" href="{{ route('customers.inactive') }}">
                        <i class="fas fa-user-slash"></i> Inactive Clients
                    </a>
                    @can('customers.view')
                        <a class="btn-erp-outline" href="{{ route('customers.ledger') }}">
                            <i class="fas fa-book"></i> Ledger
                        </a>
                        <a class="btn-erp-outline" href="{{ route('customer.payments') }}">
                            <i class="fas fa-money-bill-wave"></i> Payments
                        </a>
                    @endcan
                    @can('customers.create')
                        <a class="btn-erp-primary" href="{{ route('customers.create') }}">
                            <i class="fas fa-plus-circle"></i> Add Customer
                        </a>
                    @endcan
                </div>
            </div>

            {{-- KPI Metric Cards (Exact Sales UI) --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Clients</div>
                            <div class="erp-kpi-value">{{ number_format($kpiMetrics['total_customers'] ?? count($customers)) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Active Accounts</div>
                            <div class="erp-kpi-value text-success">
                                {{ number_format($kpiMetrics['active_customers'] ?? $customers->where('status', 'active')->count()) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Inactive Accounts</div>
                            <div class="erp-kpi-value text-warning" style="color: #d97706 !important;">
                                {{ number_format($kpiMetrics['inactive_customers'] ?? $customers->where('status', 'inactive')->count()) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #fffbeb; color: #d97706;">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Credit Limit</div>
                            <div class="erp-kpi-value" style="color: #0284c7 !important;">
                                Rs. {{ number_format($kpiMetrics['total_credit_limit'] ?? 0, 0) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #f0f9ff; color: #0284c7;">
                            <i class="fas fa-credit-card"></i>
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
                            <button type="button" class="close ml-auto ms-auto" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ session('error') }}</span>
                            <button type="button" class="close ml-auto ms-auto" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Single-Row Filter System (Exact Sales UI) --}}
                    <div class="erp-filter-card">
                        <form method="GET" action="{{ route('customers.index') }}" class="erp-filter-single-row" autocomplete="off">
                            <div class="erp-filter-item erp-item-id">
                                <label class="erp-filter-label"><i class="fas fa-hashtag text-primary"></i> Customer ID</label>
                                <input type="text" class="form-control erp-filter-input" name="search_id" value="{{ request('search_id') }}" placeholder="CUST-ID...">
                            </div>

                            <div class="erp-filter-item erp-item-name">
                                <label class="erp-filter-label"><i class="fas fa-user text-primary"></i> Name / Phone</label>
                                <input type="text" class="form-control erp-filter-input" name="search_name" value="{{ request('search_name') }}" placeholder="Search name or mobile...">
                            </div>

                            <div class="erp-filter-item erp-item-status">
                                <label class="erp-filter-label"><i class="fas fa-toggle-on text-primary"></i> Status</label>
                                <select name="status" class="form-select erp-filter-input">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="erp-filter-item erp-item-source">
                                <label class="erp-filter-label"><i class="fas fa-globe text-primary"></i> Source</label>
                                <select name="source" class="form-select erp-filter-input">
                                    <option value="">All Sources</option>
                                    <option value="Manual" {{ request('source') == 'Manual' ? 'selected' : '' }}>Manual</option>
                                    <option value="Website" {{ request('source') == 'Website' ? 'selected' : '' }}>Website</option>
                                    <option value="Both" {{ request('source') == 'Both' ? 'selected' : '' }}>Both</option>
                                </select>
                            </div>

                            <div class="erp-filter-item erp-item-actions ms-auto">
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('customers.index') }}" class="btn btn-erp-pill-outline" title="Reset Filters">
                                        <i class="fas fa-undo"></i> <span>Reset</span>
                                    </a>
                                    <button type="submit" class="btn btn-erp-pill-primary" title="Apply Filter">
                                        <i class="fas fa-filter"></i> <span>Filter</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Data Table Container (Exact Sales UI) --}}
                    <div class="erp-table-responsive table-responsive">
                        <table class="table erp-table datanew" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="ps-2 text-center" style="width: 95px;">CUSTOMER ID</th>
                                    <th style="min-width: 170px;">CLIENT NAME</th>
                                    <th style="width: 130px;">MOBILE / CONTACT</th>
                                    <th class="text-end" style="width: 130px;">CREDIT LIMIT</th>
                                    <th class="text-center" style="width: 80px;">SOURCE</th>
                                    <th class="text-center" style="width: 85px;">STATUS</th>
                                    <th class="pe-2 text-center" style="width: 85px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $customer)
                                    @php
                                        $name = $customer->customer_name ?? 'Customer';
                                        $initial = strtoupper(substr($name, 0, 1));
                                        $source = $customer->source ?? 'Manual';
                                    @endphp
                                    <tr>
                                        {{-- Customer ID Badge --}}
                                        <td class="ps-2 text-center">
                                            <span class="erp-bill-tag">#{{ $customer->customer_id }}</span>
                                        </td>

                                        {{-- Client Name & Avatar --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="erp-avatar erp-avatar-registered">
                                                    {{ $initial }}
                                                </div>
                                                <div class="d-flex flex-column text-truncate">
                                                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.80rem;" title="{{ $name }}">
                                                        {{ $name }}
                                                    </span>
                                                    <span class="text-muted text-truncate" style="font-size: 0.68rem; font-weight: 500;">
                                                        {{ $customer->customer_type ?: ($customer->zone ?: 'Standard') }}
                                                        @if($customer->ntn)
                                                            &bull; <span class="text-primary fw-semibold">NTN: {{ $customer->ntn }}</span>
                                                        @endif
                                                        @if($customer->strn)
                                                            &bull; <span class="text-info fw-semibold">STRN: {{ $customer->strn }}</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Mobile Phone --}}
                                        <td>
                                            <span class="font-monospace fw-semibold text-dark" style="font-size: 0.78rem;">
                                                <i class="fas fa-phone-alt text-muted small me-1"></i>{{ $customer->mobile ?: '-' }}
                                            </span>
                                        </td>

                                        {{-- Credit Limit --}}
                                        <td class="text-end font-monospace">
                                            @if($customer->balance_range == 0)
                                                <span class="badge bg-light text-primary border" style="font-size: 0.72rem;">Unlimited</span>
                                            @else
                                                <span class="fw-bold text-dark" style="font-size: 0.78rem;">
                                                    Rs. {{ number_format($customer->balance_range, 0) }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Source Badge --}}
                                        <td class="text-center">
                                            @if($source === 'Website')
                                                <span class="erp-badge badge-src-website">
                                                    <i class="fas fa-globe small me-1"></i>Website
                                                </span>
                                            @elseif($source === 'Both')
                                                <span class="erp-badge badge-src-both">
                                                    <i class="fas fa-sync-alt small me-1"></i>Both
                                                </span>
                                            @else
                                                <span class="erp-badge badge-src-manual">
                                                    <i class="fas fa-keyboard small me-1"></i>Manual
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Status Badge --}}
                                        <td class="text-center">
                                            @if($customer->status === 'active')
                                                <span class="erp-badge badge-posted">
                                                    <i class="fas fa-check-circle me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="erp-badge badge-inactive">
                                                    <i class="fas fa-times-circle me-1"></i>Inactive
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Action Dropdown (Exact Sales UI) --}}
                                        <td class="pe-2 text-center text-nowrap">
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-erp-table-action dropdown-toggle shadow-none" type="button" data-toggle="dropdown" data-bs-toggle="dropdown" data-display="static" data-bs-display="static" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v small me-1"></i> Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right dropdown-menu-end border-0 shadow-lg rounded-3 py-2" style="min-width: 175px;">
                                                    @can('customers.edit')
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('customers.edit', $customer->id) }}">
                                                                <i class="fas fa-edit text-primary fa-fw"></i> Edit Profile
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('customers.view')
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('customers.ledger', ['customer_id' => $customer->id]) }}">
                                                                <i class="fas fa-file-invoice text-info fa-fw"></i> View Ledger
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('customer.payments') }}?customer_id={{ $customer->id }}">
                                                                <i class="fas fa-money-check-alt text-success fa-fw"></i> Payments
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('customers.edit')
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('customers.toggleStatus', $customer->id) }}">
                                                                <i class="fas fa-toggle-on {{ $customer->status === 'active' ? 'text-warning' : 'text-success' }} fa-fw"></i>
                                                                {{ $customer->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('customers.delete')
                                                        <li class="border-top my-1"></li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger btn-delete-customer" href="javascript:void(0)" data-url="{{ route('customers.destroy', $customer->id) }}" data-name="{{ $name }}">
                                                                <i class="fas fa-trash-alt text-danger fa-fw"></i> Delete Client
                                                            </a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                                            <h6 class="fw-bold">No customers found</h6>
                                            <p class="small text-muted mb-3">Add your first customer to begin recording sales and balances.</p>
                                            @can('customers.create')
                                                <a href="{{ route('customers.create') }}" class="btn btn-erp-primary">
                                                    <i class="fas fa-plus-circle mr-1"></i> Add New Customer
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
<script src="{{ asset('assets/vendors/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // SweetAlert2 Confirmation for Customer Deletion
        $(document).on('click', '.btn-delete-customer', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var customerName = $(this).data('name');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Customer?',
                    text: 'Are you sure you want to delete "' + customerName + '"? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            } else {
                if (confirm('Are you sure you want to delete "' + customerName + '"?')) {
                    window.location.href = url;
                }
            }
        });
    });
</script>
@endsection

