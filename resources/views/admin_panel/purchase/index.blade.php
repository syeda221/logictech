@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ==========================================================================
       Enterprise ERP Design System - Procurement & Purchase Orders
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

    /* KPI Metric Cards (Exact Enterprise ERP UI) */
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

    /* Status Filter Tabs / Pills */
    .erp-status-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .erp-status-pill {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.35rem 0.9rem;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .erp-status-pill.active {
        background-color: #2563eb;
        color: #ffffff !important;
        border: 1px solid #1d4ed8;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25);
    }
    .erp-status-pill:not(.active) {
        background-color: #ffffff;
        color: #475569 !important;
        border: 1px solid #cbd5e1;
    }
    .erp-status-pill:not(.active):hover {
        background-color: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a !important;
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
    .erp-item-quick { flex: 0.85; min-width: 100px; }
    .erp-item-date { flex: 0.95; min-width: 110px; }
    .erp-item-bill { flex: 0.85; min-width: 95px; }
    .erp-item-mbill { flex: 0.85; min-width: 95px; }
    .erp-item-vendor { flex: 1.5; min-width: 140px; }
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

    /* Main ERP Card & Table Container */
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
        min-height: 420px;
        padding-bottom: 140px;
    }
    .erp-table-responsive .dropdown-menu {
        z-index: 1060 !important;
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
        padding: 0.55rem 0.5rem !important;
        border: 1px solid #bfdbfe !important;
        border-bottom: 2px solid #60a5fa !important;
        white-space: nowrap;
        vertical-align: middle;
    }
    .erp-table tbody td {
        padding: 0.5rem 0.5rem !important;
        font-size: 0.78rem !important;
        color: #334155 !important;
        border: 1px solid #dbeafe !important;
        vertical-align: middle !important;
        background-color: #ffffff;
    }
    .erp-table tbody tr:hover td {
        background-color: #f8fbff !important;
    }

    /* Dropdown Action Menu in Table */
    .btn-premium-action,
    .btn-erp-table-action {
        background-color: #ffffff !important;
        border: 1.5px solid #bfdbfe !important;
        color: #1e40af !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        height: 30px !important;
        padding: 0 10px !important;
        font-size: 0.72rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.08) !important;
        transition: all 0.15s ease !important;
        white-space: nowrap;
    }
    .btn-premium-action:hover,
    .btn-premium-action:focus,
    .btn-premium-action[aria-expanded="true"],
    .btn-erp-table-action:hover,
    .btn-erp-table-action:focus,
    .btn-erp-table-action[aria-expanded="true"] {
        background-color: #eff6ff !important;
        border-color: #3b82f6 !important;
        color: #1d4ed8 !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 6px -1px rgba(37, 99, 235, 0.18) !important;
    }
    .dropdown-menu {
        border: 1.5px solid #dbeafe !important;
        border-radius: 10px !important;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        padding: 6px 0 !important;
        z-index: 1060 !important;
    }
    .dropdown-item {
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        color: #334155 !important;
        padding: 6px 14px !important;
        transition: all 0.15s ease !important;
    }
    .dropdown-item:hover {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
    }
    .dropdown-divider {
        border-top: 1px solid #e2e8f0 !important;
        margin: 4px 0 !important;
    }

    /* Avatar Circle */
    .avatar-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        background-color: #eff6ff !important;
        color: #2563eb !important;
        border: 1px solid #bfdbfe;
        flex-shrink: 0;
    }

    /* Bulk Discount Bar Styling */
    .bulk-discount-card {
        border: 1.5px solid #93c5fd !important;
        border-radius: 12px !important;
        background: linear-gradient(180deg, #ffffff 0%, #eff6ff 100%) !important;
        box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.1) !important;
    }

    .btn-circle-custom {
        width: 30px !important;
        height: 30px !important;
        border-radius: 50% !important;
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
        background-color: #ffffff !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s;
    }
    .btn-circle-custom:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .erp-filter-single-row {
            flex-wrap: wrap !important;
        }
        .erp-filter-item {
            flex: 1 1 calc(50% - 8px) !important;
            min-width: 130px !important;
        }
        .erp-item-actions {
            flex: 1 1 100% !important;
            justify-content: flex-end;
            margin-top: 6px;
        }
    }
    @media (max-width: 768px) {
        .purch-hdr-actions {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            width: 100%;
        }
        .purch-hdr-actions .btn-erp-outline,
        .purch-hdr-actions .btn-erp-primary,
        .purch-hdr-actions .btn-erp-outline-danger {
            width: 100%;
            justify-content: center;
        }
    }

    /* Modal Select2 Styling Fix */
    .select2-container--open {
        z-index: 99999999 !important;
    }
    .select2-dropdown {
        z-index: 99999999 !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    /* Prevent dropdown clipping in table rows */
    .erp-table td .dropdown {
        position: relative;
    }
    .erp-table td .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        left: auto;
        margin-top: 4px;
        min-width: 175px;
        z-index: 9999 !important;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">

            {{-- Page Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                <div>
                    <h4 class="erp-title">
                        <i class="fas fa-shopping-cart text-primary"></i> Procurement &amp; Purchase Orders
                    </h4>
                    <p class="erp-subtitle">Manage procurement, supplier bills, order receipts &amp; accounts payable</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2 purch-hdr-actions">
                    @can('purchases.create')
                        <a class="btn-erp-primary" href="{{ route('add_purchase') }}">
                            <i class="fas fa-boxes"></i> Stock Purchase
                        </a>
                        <button type="button" class="btn-erp-primary btn-open-credit-purchase" id="btnCreditPurchaseModal" data-toggle="modal" data-target="#creditPurchaseModal" data-bs-toggle="modal" data-bs-target="#creditPurchaseModal" style="cursor: pointer;">
                            <i class="fas fa-credit-card"></i> Credit Purchase
                        </button>
                    @endcan
                </div>
            </div>

            {{-- 4 Metric Summary KPI Cards (Exact Enterprise ERP Style) --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Orders</div>
                            <div class="erp-kpi-value">{{ number_format($Purchase->count()) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Purchases</div>
                            <div class="erp-kpi-value text-success">
                                Rs. {{ number_format($Purchase->sum(function($p) { return $p->total_returned > 0 ? $p->updated_net_amount : $p->net_amount; }), 2) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Paid Amount</div>
                            <div class="erp-kpi-value" style="color: #0284c7 !important;">
                                Rs. {{ number_format($Purchase->sum('paid_amount'), 2) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #f0f9ff; color: #0284c7;">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Outstanding Due</div>
                            <div class="erp-kpi-value text-danger">
                                Rs. {{ number_format($Purchase->sum(function($p) { return $p->total_returned > 0 ? $p->updated_due_amount : $p->due_amount; }), 2) }}
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #fef2f2; color: #dc2626;">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Data Card --}}
            <div class="card erp-main-card">
                <div class="card-body p-3 p-md-4">
                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-3">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-3">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ session('error') }}</span>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Quick Status Filter Pills --}}
                    <div class="erp-status-pills">
                        <a href="{{ route('Purchase.home', ['status' => 'all']) }}"
                            class="erp-status-pill {{ request('status') == 'all' || !request('status') ? 'active' : '' }}">
                            <i class="fas fa-list-ul"></i> All Orders
                        </a>
                        <a href="{{ route('Purchase.home', ['status' => 'Returned']) }}"
                            class="erp-status-pill {{ request('status') == 'Returned' ? 'active' : '' }}">
                            <i class="fas fa-undo text-danger"></i> Returned
                        </a>
                    </div>

                    {{-- Single-Row Modern Filter Card (Exact Sales UI) --}}
                    <div class="erp-filter-card">
                        <form id="filterForm" class="erp-filter-single-row" autocomplete="off">
                            <div class="erp-filter-item erp-item-quick">
                                <label class="erp-filter-label"><i class="far fa-clock text-primary"></i> Period</label>
                                <select id="quick_filter" class="form-select erp-filter-input">
                                    <option value="custom">Custom</option>
                                    <option value="daily">Today</option>
                                    <option value="weekly">This Week</option>
                                    <option value="monthly">This Month</option>
                                    <option value="yearly">This Year</option>
                                </select>
                            </div>

                            <div class="erp-filter-item erp-item-date">
                                <label class="erp-filter-label"><i class="far fa-calendar-alt text-primary"></i> From</label>
                                <input type="text" class="form-control erp-filter-input datepicker-custom bg-white" name="from_date" id="filter_from_date" placeholder="dd/mm/yyyy">
                            </div>

                            <div class="erp-filter-item erp-item-date">
                                <label class="erp-filter-label"><i class="far fa-calendar-check text-primary"></i> To</label>
                                <input type="text" class="form-control erp-filter-input datepicker-custom bg-white" name="to_date" id="filter_to_date" placeholder="dd/mm/yyyy">
                            </div>

                            <div class="erp-filter-item erp-item-bill">
                                <label class="erp-filter-label"><i class="fas fa-hashtag text-primary"></i> Bill#</label>
                                <input type="text" class="form-control erp-filter-input" name="bill_no" id="filter_bill_no" placeholder="PUR-...">
                            </div>

                            <div class="erp-filter-item erp-item-mbill">
                                <label class="erp-filter-label"><i class="fas fa-file-invoice text-primary"></i> M.Bill#</label>
                                <input type="text" class="form-control erp-filter-input" name="reference" id="filter_reference" placeholder="M.Bill...">
                            </div>

                            <div class="erp-filter-item erp-item-vendor">
                                <label class="erp-filter-label"><i class="fas fa-user-tie text-primary"></i> Vendor</label>
                                <select class="form-select erp-filter-input" name="vendor_id" id="filter_vendor_id">
                                    <option value="">All Vendors</option>
                                    @foreach ($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="erp-filter-item erp-item-actions ms-auto">
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-erp-pill-outline" id="btnReset" title="Reset Filters">
                                        <i class="fas fa-undo"></i> <span>Reset</span>
                                    </button>
                                    <button type="submit" class="btn btn-erp-pill-primary" id="btnSearch" title="Apply Filter">
                                        <i class="fas fa-filter"></i> <span>Filter</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Data Table Container --}}
                    <div class="erp-table-responsive table-responsive">
                        <table id="purchase-table" class="table erp-table datanew align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 38px; vertical-align: middle;">
                                        <input type="checkbox" id="selectAllPurchases" style="cursor: pointer; width: 16px; height: 16px; margin: 0 auto !important;">
                                    </th>
                                    <th>Bill#</th>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th>M.Bill</th>
                                    <th>Status</th>
                                    <th>Vendor</th>
                                    <th>Location</th>
                                    <th class="text-end">Inline Disc</th>
                                    <th class="text-end">Add. Disc</th>
                                    <th class="text-end">Net Amount</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Due</th>
                                    <th class="text-center" style="width: 90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseTableBody">
                                @include('admin_panel.purchase.partials.purchase_table_body')
                            </tbody>
                        </table>
                    </div>

                    {{-- Bulk Discount Card --}}
                    <div id="bulk-discount-bar" class="d-none card mt-3 bulk-discount-card">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2" style="color: #2563eb; font-size: 14px; font-weight: 700;">
                                    <i class="fas fa-tag me-2"></i>
                                    <span>Apply additional discount to <span id="selected-purchases-count-text">0</span> selected rows</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label mb-1 text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">DISCOUNT PERCENTAGE (%)</label>
                                    <div class="input-group" style="max-width: 360px;">
                                        <input type="number" id="bulk-discount-input" min="0" max="100" step="0.1" class="form-control erp-filter-input" placeholder="Enter discount % (e.g. 5)" style="border-radius: 6px 0 0 6px !important; height: 36px;">
                                        <span class="input-group-text" style="background-color: #eff6ff; border: 1.5px solid #bfdbfe; border-left: none; border-radius: 0 6px 6px 0 !important; font-weight: bold; color: #1e40af; height: 36px;">%</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" id="btn-save-bulk-discount" class="btn btn-erp-pill-primary px-3">
                                            <i class="fas fa-check"></i> Save Changes
                                        </button>
                                        <button type="button" id="btn-cancel-bulk-discount" class="btn btn-erp-pill-outline px-3">
                                            Cancel
                                        </button>
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        <button type="button" id="btn-minimize-bulk-bar" class="btn-circle-custom me-2" title="Dismiss">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <div style="font-size: 12px; color: #64748b; font-weight: 600;">
                                            <span id="selected-ratio-text">0 of 0</span> purchases selected
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- Credit Purchase Modal (No Products - Direct Vendor Ledger Booking) --}}
<div class="modal fade" id="creditPurchaseModal" tabindex="-1" role="dialog" aria-labelledby="creditPurchaseModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-bottom: 2.5px solid #60a5fa; padding: 14px 20px;">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-white" id="creditPurchaseModalLabel" style="font-size: 16px;">
                        <i class="fas fa-credit-card me-2"></i> New Credit Purchase
                    </h5>
                    <small class="text-white-50" style="font-size: 11.5px;">Direct vendor credit booking (Without product inventory line items)</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; opacity: 0.9; outline: none; border: none; background: transparent; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="creditPurchaseForm" class="myform" method="POST" action="{{ route('purchase.credit_store') }}">
                @csrf
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="row g-3">
                        {{-- Vendor Selection --}}
                        <div class="col-md-7 col-12 mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">
                                <i class="fas fa-truck text-primary me-1"></i> Vendor Name <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="flex-grow-1">
                                    <select name="vendor_id" id="cp_vendor_id" class="form-control form-select form-select-sm select2" style="width: 100%; height: 38px;" required>
                                        <option value="">-- Select Vendor --</option>
                                        @foreach ($vendors as $v)
                                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary shadow-sm" id="btnOpenAddVendorFromCP" style="height: 38px; padding: 0 12px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;" title="Quick Add New Vendor">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Purchase Date --}}
                        <div class="col-md-5 col-12 mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">
                                <i class="far fa-calendar-alt text-primary me-1"></i> Purchase Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="purchase_date" id="cp_purchase_date" class="form-control form-control-sm" style="height: 38px;" value="{{ date('Y-m-d') }}" required>
                        </div>

                        {{-- M.Bill # --}}
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">
                                <i class="fas fa-file-invoice text-primary me-1"></i> M-Bill # <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="m_bill" id="cp_m_bill" class="form-control form-control-sm" style="height: 38px;" placeholder="e.g. MB-10492" required>
                        </div>

                        {{-- Purchase Amount --}}
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">
                                <i class="fas fa-coins text-primary me-1"></i> Purchase Amount (Rs) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm" style="height: 38px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white fw-bold text-muted" style="border-color: #cbd5e1;">Rs.</span>
                                </div>
                                <input type="number" step="0.01" min="0.01" name="amount" id="cp_amount" class="form-control fw-bold text-dark fs-6" placeholder="0.00" style="border-color: #cbd5e1;" required>
                            </div>
                        </div>

                        {{-- Description / Remarks --}}
                        <div class="col-12 mb-2">
                            <label class="form-label fw-bold small text-dark mb-1">
                                <i class="fas fa-comment-alt text-primary me-1"></i> Description / Narration
                            </label>
                            <textarea name="description" id="cp_description" class="form-control" rows="2" placeholder="e.g. Credit purchase against raw materials / invoice details..."></textarea>
                        </div>
                    </div>

                    {{-- Enterprise Alert Note --}}
                    <div class="alert alert-light border py-2 px-3 mt-3 mb-0 d-flex align-items-center" style="font-size: 11.5px; border-radius: 8px; background-color: #eff6ff; border-color: #bfdbfe !important;">
                        <i class="fas fa-shield-alt fs-5 me-2 text-primary"></i>
                        <div class="text-secondary">
                            This entry will automatically record an approved purchase and <strong>post credit to the Vendor Ledger</strong> &amp; Accounts Payable balance with full double-entry integrity.
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-light border px-3 fw-medium" data-dismiss="modal" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" id="btnSaveCreditPurchase" class="btn btn-sm btn-primary px-4 fw-bold save-btn shadow-sm" style="background-color: #2563eb; border-color: #1d4ed8; height: 38px; border-radius: 6px;">
                        <i class="fas fa-check-circle me-1"></i> Save &amp; Post to Ledger
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom-0 pb-2">
                <h5 class="modal-title fw-bold" id="addVendorModalLabel"><i class="fas fa-user-plus text-primary me-2"></i>Add New Vendor</h5>
                <button type="button" class="btn-close close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddVendorForm">
                @csrf
                <div class="modal-body pt-2">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Vendor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="Enter vendor name">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Phone Number</label>
                            <input type="text" class="form-control" name="phone" placeholder="Optional">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Opening Balance</label>
                            <input type="number" step="0.01" class="form-control" name="opening_balance" value="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted">Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnQuickSaveVendor">Save Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Function to initialize DataTable
        function initDataTable() {
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().destroy();
            }
            $('.datanew').DataTable({
                "pageLength": 10,
                "order": [[1, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": [0, 13] }
                ],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search purchases..."
                },
                "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });
        }

        // Initial call
        initDataTable();

        // Quick Filter Logic
        $(document).on('change', '#quick_filter', function() {
            let val = $(this).val();
            let today = new Date();
            let start = new Date();
            let end = new Date();

            if (val === 'daily') {
                // Start and end are both today
            } else if (val === 'weekly') {
                let day = today.getDay(); // 0 is Sunday, 1 is Monday
                let diff = today.getDate() - day + (day === 0 ? -6 : 1);
                start.setDate(diff);
            } else if (val === 'monthly') {
                start.setDate(1);
            } else if (val === 'yearly') {
                start.setMonth(0, 1);
            } else if (val === 'custom') {
                return; // Don't change dates for custom
            }

            let pickerFrom = document.getElementById('filter_from_date')._flatpickr;
            let pickerTo = document.getElementById('filter_to_date')._flatpickr;
            if(pickerFrom) pickerFrom.setDate(start);
            else $("#filter_from_date").val(start.toISOString().split('T')[0]);
            
            if(pickerTo) pickerTo.setDate(end);
            else $("#filter_to_date").val(end.toISOString().split('T')[0]);
            
            $('#filterForm').trigger('submit');
        });

        // Submit filter form via AJAX
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btnSearch');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Filtering...');

            $.ajax({
                url: '{{ route("Purchase.home") }}',
                method: 'GET',
                data: $(this).serialize(),
                success: function(response) {
                    $btn.prop('disabled', false).html(origHtml);
                    
                    if ($.fn.DataTable.isDataTable('.datanew')) {
                        $('.datanew').DataTable().destroy();
                    }
                    
                    $('#purchaseTableBody').html(response.html);
                    
                    initDataTable();
                },
                error: function(err) {
                    $btn.prop('disabled', false).html(origHtml);
                    Swal.fire('Error', 'Failed to retrieve filtered list.', 'error');
                }
            });
        });

        // Reset form
        $('#btnReset').on('click', function() {
            $('#filterForm')[0].reset();
            let pickerFrom = document.getElementById('filter_from_date')._flatpickr;
            let pickerTo = document.getElementById('filter_to_date')._flatpickr;
            if(pickerFrom) pickerFrom.clear();
            if(pickerTo) pickerTo.clear();
            $('#filterForm').trigger('submit');
        });

        // Confirm Purchase Action
        $(document).on('click', '.confirm-purchase-btn', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');

            Swal.fire({
                title: "Confirm Purchase?",
                text: "This will finalize the purchase, update stocks, and post ledgers.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#2563eb",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Yes, Confirm it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: "GET",
                        success: function(response) {
                            if (response.invoice_url) {
                                window.open(response.invoice_url, '_blank');
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Confirmed!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            let msg = 'Something went wrong.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        });

        // Delete Confirmation
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            let form = $(this).closest("form");

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Helper to get DataTables nodes if available
        function getPurchaseNodes() {
            if ($.fn.DataTable.isDataTable('#purchase-table')) {
                return $('#purchase-table').DataTable().rows().nodes();
            }
            return document;
        }

        // Bulk select checkboxes logic
        function updateBulkDiscountBar() {
            let nodes = getPurchaseNodes();
            let selectedRows = $('.select-purchase-row:checked', nodes);
            let count = selectedRows.length;
            let total = $('.select-purchase-row', nodes).length;
            if (count > 0) {
                $('#selected-purchases-count-text').text(count);
                $('#selected-ratio-text').text(count + ' of ' + total);
                $('#bulk-discount-bar').removeClass('d-none');
            } else {
                $('#bulk-discount-bar').addClass('d-none');
            }
        }

        $(document).on('change', '.select-purchase-row', function() {
            updateBulkDiscountBar();
            let nodes = getPurchaseNodes();
            let allChecked = $('.select-purchase-row', nodes).length > 0 && $('.select-purchase-row', nodes).length === $('.select-purchase-row:checked', nodes).length;
            $('#selectAllPurchases').prop('checked', allChecked);
        });

        $(document).on('change', '#selectAllPurchases', function() {
            let isChecked = $(this).is(':checked');
            let nodes = getPurchaseNodes();
            $('.select-purchase-row', nodes).prop('checked', isChecked);
            updateBulkDiscountBar();
        });

        // Cancel button functionality
        $(document).on('click', '#btn-cancel-bulk-discount', function() {
            let nodes = getPurchaseNodes();
            $('.select-purchase-row', nodes).prop('checked', false);
            $('#selectAllPurchases').prop('checked', false);
            updateBulkDiscountBar();
        });

        // Dismiss button functionality
        $(document).on('click', '#btn-minimize-bulk-bar', function() {
            let nodes = getPurchaseNodes();
            $('.select-purchase-row', nodes).prop('checked', false);
            $('#selectAllPurchases').prop('checked', false);
            updateBulkDiscountBar();
        });

        // Recheck on AJAX table redraw
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings && settings.url && (settings.url.indexOf('Purchase') !== -1 || settings.url.indexOf('purchase') !== -1)) {
                $('#selectAllPurchases').prop('checked', false);
                updateBulkDiscountBar();
            }
        });

        // Save bulk additional discount
        $(document).on('click', '#btn-save-bulk-discount', function() {
            let nodes = getPurchaseNodes();
            let selectedIds = $('.select-purchase-row:checked', nodes).map(function() {
                return $(this).val();
            }).get();
            let discountValue = $('#bulk-discount-input').val();

            if (selectedIds.length === 0) {
                Swal.fire('Error', 'Please select at least one purchase.', 'error');
                return;
            }
            if (discountValue === '' || discountValue < 0 || discountValue > 100) {
                Swal.fire('Error', 'Please enter a valid discount percentage (0 to 100).', 'error');
                return;
            }

            let btn = $(this);
            let origHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: '{{ route("purchases.bulk-additional-discount") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    purchase_ids: selectedIds,
                    discount_percentage: discountValue
                },
                success: function(response) {
                    btn.prop('disabled', false).html(origHtml);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to save discount.', 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    let msg = 'Failed to save discount.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        // Dynamic Vendor Loader Helper
        function refreshVendorDropdowns(selectedVendorId = null) {
            $.ajax({
                url: "{{ route('vendors.ajax-list') }}",
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res && res.vendors) {
                        // 1. Update Modal Vendor Select
                        let $cpSelect = $('#cp_vendor_id');
                        let currentCpVal = selectedVendorId || $cpSelect.val();
                        $cpSelect.empty().append('<option value="">-- Select Vendor --</option>');
                        
                        // 2. Update Filter Vendor Select
                        let $filterSelect = $('#filter_vendor_id');
                        let currentFilterVal = $filterSelect.val();
                        $filterSelect.empty().append('<option value="">All Vendors</option>');

                        res.vendors.forEach(function(v) {
                            $cpSelect.append(new Option(v.name, v.id, false, false));
                            $filterSelect.append(new Option(v.name, v.id, false, false));
                        });

                        if (currentCpVal) {
                            $cpSelect.val(currentCpVal);
                        }
                        if (currentFilterVal) {
                            $filterSelect.val(currentFilterVal);
                        }

                        if ($.fn.select2) {
                            $cpSelect.trigger('change.select2');
                            $filterSelect.trigger('change.select2');
                        }
                    }
                }
            });
        }

        // Show Credit Purchase Modal
        $(document).on('click', '#btnCreditPurchaseModal, .btn-open-credit-purchase', function(e) {
            e.preventDefault();
            $('#creditPurchaseModal').modal('show');
        });

        // Initialize Select2 & refresh vendors when Credit Purchase modal opens
        $('#creditPurchaseModal').on('shown.bs.modal', function () {
            // Re-fetch latest vendors in case new ones were added
            refreshVendorDropdowns();

            if ($.fn.select2) {
                $('#cp_vendor_id').select2({
                    dropdownParent: $('#creditPurchaseModal'),
                    placeholder: "-- Select Vendor --",
                    allowClear: true,
                    width: '100%'
                });
            }
            $('#cp_m_bill').trigger('focus');
        });

        // Open Quick Add Vendor Modal from inside Credit Purchase Modal
        $(document).on('click', '#btnOpenAddVendorFromCP', function(e) {
            e.preventDefault();
            $('#addVendorModal').modal('show');
        });

        // Handle Quick Add Vendor Form Submission
        $('#quickAddVendorForm').on('submit', function(e) {
            e.preventDefault();
            let $btn = $('#btnQuickSaveVendor');
            let originalText = $btn.text();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

            $.ajax({
                url: "{{ route('vendors.store.ajax') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $btn.prop('disabled', false).text(originalText);
                    
                    let vendorId = null;
                    let vendorName = $('#quickAddVendorForm input[name="name"]').val();
                    
                    if (response.vendor && response.vendor.id) {
                        vendorId = response.vendor.id;
                        vendorName = response.vendor.name;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Vendor Added',
                        text: 'Vendor created successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#addVendorModal').modal('hide');
                        $('#quickAddVendorForm')[0].reset();
                        
                        // Immediately refresh both dropdowns and select the newly created vendor
                        refreshVendorDropdowns(vendorId);
                        if (vendorId) {
                            setTimeout(function() {
                                $('#cp_vendor_id').val(vendorId).trigger('change');
                            }, 300);
                        }
                    });
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).text(originalText);
                    let msg = 'Error adding vendor.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: msg
                    });
                }
            });
        });

        // Credit Purchase Form Submit via standard myAjax
        $(document).on('submit', '#creditPurchaseForm', function(e) {
            e.preventDefault();

            let vendorId = $('#cp_vendor_id').val();
            let mBill = $.trim($('#cp_m_bill').val());
            let amount = parseFloat($('#cp_amount').val());
            let date = $('#cp_purchase_date').val();

            if (!vendorId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Vendor Required',
                    text: 'Please select a vendor.',
                    confirmButtonColor: '#2563eb'
                });
                return false;
            }
            if (!mBill) {
                Swal.fire({
                    icon: 'warning',
                    title: 'M-Bill Required',
                    text: 'Please enter M-Bill #.',
                    confirmButtonColor: '#2563eb'
                });
                return false;
            }
            if (isNaN(amount) || amount <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Amount',
                    text: 'Please enter a valid purchase amount greater than zero.',
                    confirmButtonColor: '#2563eb'
                });
                return false;
            }
            if (!date) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Date Required',
                    text: 'Please select a purchase date.',
                    confirmButtonColor: '#2563eb'
                });
                return false;
            }

            let form = this;
            let formdata = new FormData(form);
            let url = $(form).attr('action');
            let method = $(form).attr('method') || 'POST';

            let $submitBtn = $(form).find(':submit');
            $submitBtn.prop('disabled', true);

            myAjax(url, formdata, method);
        });
    });
</script>
@endsection
