@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Sales Management
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

        /* Page Layout */
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

        /* Filter Panel Single-Row System */
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
        .erp-item-customer { flex: 1.3; min-width: 125px; }
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

        /* Pill Style Action Buttons */
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
            .erp-item-actions .d-flex {
                width: 100%;
            }
            .erp-item-actions .btn {
                flex: 1;
            }
        }

        /* Nested Submenu & Radio Items */
        .dropdown-submenu {
            position: relative;
        }
        .dropdown-submenu .dropdown-submenu-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .dropdown-submenu .dropdown-submenu-menu {
            top: 0;
            right: 100%; /* Opens to the left to prevent screen overflow */
            margin-top: -6px;
            margin-right: -2px; /* Overlap slightly to prevent any cursor gap */
            display: none;
            position: absolute;
            min-width: 180px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 6px 0;
            z-index: 1060;
        }
        /* Invisible hover bridge to eliminate cursor dead-zone between Action menu and Submenu */
        .dropdown-submenu .dropdown-submenu-menu::after {
            content: '';
            position: absolute;
            top: 0;
            right: -20px;
            bottom: 0;
            width: 25px;
            background: transparent;
        }
        .dropdown-submenu.show > .dropdown-submenu-menu,
        .dropdown-submenu.is-hovered > .dropdown-submenu-menu {
            display: block !important;
        }
        .state-radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            cursor: pointer;
            font-size: 12px;
            color: #334155;
            transition: background-color 0.15s ease;
            margin-bottom: 0;
            user-select: none;
            width: 100%;
        }
        .state-radio-item:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .state-radio-item input[type="radio"] {
            cursor: pointer;
            margin: 0;
            accent-color: #2563eb;
            width: 15px;
            height: 15px;
        }

        /* Main Data Card & Table Container */
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
            padding-bottom: 120px;
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
            padding: 0.5rem 0.35rem !important;
            border: 1px solid #bfdbfe !important;
            border-bottom: 2px solid #60a5fa !important;
            white-space: nowrap;
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

        /* Sale Status Badges */
        .erp-badge.badge-posted {
            background-color: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }
        .erp-badge.badge-booked {
            background-color: #eff6ff;
            color: #1e40af;
            border-color: #bfdbfe;
        }
        .erp-badge.badge-draft {
            background-color: #f8fafc;
            color: #475569;
            border-color: #cbd5e1;
        }
        .erp-badge.badge-returned {
            background-color: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }
        .erp-badge.badge-exchange {
            background-color: #f5f3ff;
            color: #6d28d9;
            border-color: #ddd6fe;
        }
        .erp-badge.badge-partial-return {
            background-color: #fff1f2;
            color: #be123c;
            border-color: #fecdd3;
            font-size: 0.68rem;
            padding: 0.15rem 0.45rem;
        }

        /* Order State Badges */
        .erp-badge.state-ready {
            background-color: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }
        .erp-badge.state-delivered {
            background-color: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }
        .erp-badge.state-cancelled {
            background-color: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }
        .erp-badge.state-pending {
            background-color: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
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
        .erp-avatar-walkin {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* Bill Tag */
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

        /* Table Action Buttons */
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

        .btn-erp-state-action {
            background-color: #ffffff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: 600;
            border-radius: 6px;
            height: 26px;
            padding: 0 0.5rem;
            font-size: 0.70rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            transition: all 0.15s ease;
        }
        .btn-erp-state-action:hover,
        .btn-erp-state-action:focus,
        .btn-erp-state-action[aria-expanded="true"] {
            background-color: #eff6ff;
            border-color: #3b82f6;
            color: #1e40af;
        }

        /* Responsive Breakpoints (< 768px) */
        @media (max-width: 768px) {
            .sales-hdr-actions {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                width: 100%;
            }
            .sales-hdr-actions .btn {
                width: 100%;
                justify-content: center;
                height: 38px;
                font-size: 0.8rem;
            }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none !important;
                text-align: left !important;
                margin-bottom: 10px;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }
        @media (min-width: 769px) {
            .sales-hdr-actions {
                display: flex;
                gap: 8px;
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
                            <i class="fas fa-file-invoice-dollar text-primary"></i> Sales Management
                        </h4>
                        <p class="erp-subtitle">Executive sales ledger, bookings overview, dispatch status and invoice records</p>
                    </div>
                    <div class="sales-hdr-actions">
                        <a class="btn-erp-outline" href="{{ route('repair.index') }}" title="Repairing & Service Management">
                            <i class="fas fa-tools text-primary"></i> Repairing
                        </a>
                        <a class="btn-erp-outline-danger" href="{{ route('sale.return.index') }}">
                            <i class="fas fa-undo-alt"></i> Returns
                        </a>
                        @can('sales.create')
                            <a class="btn-erp-primary" href="{{ route('sale.add') }}">
                                <i class="fas fa-plus-circle"></i> Order
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- KPI Metric Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Total Invoices</div>
                                <div class="erp-kpi-value" id="statTotalCount">{{ number_format($stats['total_count'] ?? 0) }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Total Net Revenue</div>
                                <div class="erp-kpi-value text-success" id="statTotalNet">Rs. {{ number_format($stats['total_net'] ?? 0, 2) }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Discounts Given</div>
                                <div class="erp-kpi-value text-warning" id="statTotalDiscount" style="color: #d97706 !important;">Rs. {{ number_format($stats['total_discount'] ?? 0, 2) }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #fffbeb; color: #d97706;">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Booking / Confirmed</div>
                                <div class="erp-kpi-value" id="statStatusCounts" style="color: #0284c7 !important;">
                                    {{ $stats['booked_count'] ?? 0 }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['confirmed_booking_count'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #f0f9ff; color: #0284c7;">
                                <i class="fas fa-bookmark"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card erp-main-card">
                    <div class="card-body p-3 p-md-4">
                        @if (session('success'))
                            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ session('error') }}</span>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Mobile Filter Panel Toggle Button --}}
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 d-md-none mb-3 fw-bold d-flex align-items-center justify-content-center gap-2" id="toggleFilterPanel">
                            <i class="fas fa-filter"></i> Search & Filters Toggle
                        </button>

                        {{-- Modern ERP Filter Panel (Single-Row Toolbar with Right Pill Buttons) --}}
                        <div class="erp-filter-card" id="filterPanelContainer">
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
                                    <input type="text" class="form-control erp-filter-input datepicker-custom bg-white" name="from_date" id="filter_from_date" placeholder="dd/mm/yy">
                                </div>

                                <div class="erp-filter-item erp-item-date">
                                    <label class="erp-filter-label"><i class="far fa-calendar-check text-primary"></i> To</label>
                                    <input type="text" class="form-control erp-filter-input datepicker-custom bg-white" name="to_date" id="filter_to_date" placeholder="dd/mm/yy">
                                </div>

                                <div class="erp-filter-item erp-item-bill">
                                    <label class="erp-filter-label"><i class="fas fa-hashtag text-primary"></i> Bill#</label>
                                    <input type="text" class="form-control erp-filter-input" name="bill_no" id="filter_bill_no" placeholder="Bill ID...">
                                </div>

                                <div class="erp-filter-item erp-item-status">
                                    <label class="erp-filter-label"><i class="fas fa-tasks text-primary"></i> Status</label>
                                    <select class="form-select erp-filter-input" name="order_status" id="filter_order_status">
                                        <option value="all">All Status</option>
                                        <option value="pending">🔴 Pending</option>
                                        <option value="ready">🔵 Ready</option>
                                        <option value="delivered">🟢 Delivered</option>
                                        <option value="cancelled">🟡 Cancelled</option>
                                    </select>
                                </div>

                                <div class="erp-filter-item erp-item-customer">
                                    <label class="erp-filter-label"><i class="fas fa-user text-primary"></i> Customer</label>
                                    <select class="form-select erp-filter-input" name="customer_id" id="filter_customer_id">
                                        <option value="">All Customers</option>
                                        @foreach ($customers as $c)
                                            <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
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

                        {{-- Table Container --}}
                        <div class="erp-table-responsive table-responsive">
                            <table id="sales-table" class="table erp-table datanew" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="ps-2 text-center" style="width: 50px;">Bill#</th>
                                        <th style="min-width: 110px;">Customer</th>
                                        <th style="min-width: 100px;">Unique Serial No</th>
                                        <th style="min-width: 100px;">Products</th>
                                        <th class="text-center" style="width: 38px;">Qty</th>
                                        <th class="text-end" style="width: 80px;">Gross</th>
                                        <th class="text-end" style="width: 80px;">Add. Disc</th>
                                        <th class="text-end" style="width: 85px;">Net Total</th>
                                        <th class="text-end" style="width: 85px;">Balance</th>
                                        <th class="text-center" style="width: 70px;">Date</th>
                                        <th class="text-center" style="width: 72px;">Status</th>
                                        <th class="text-center" style="width: 72px;">State</th>
                                        <th class="pe-2 text-center" style="width: 70px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="salesTableBody">
                                    @include('admin_panel.sale.partials.sales_table_body')
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Delivery Product Specifications Modal --}}
    @include('admin_panel.sale.partials.delivery_specs_modal')
@endsection

@section('js')
    <script src="{{ asset('assets/vendors/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script>
        // Global function for instant order state change
        window.changeSaleOrderStatus = function(e, saleId, newStatus, el, saleStatus) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            if (!saleId || !newStatus) {
                console.error('Missing saleId or newStatus', saleId, newStatus);
                return;
            }

            // Save previous radio value before changing
            var prevStatus = $('input[name="order_state_' + saleId + '"]:checked').val()
                          || $('input[name="m_order_state_' + saleId + '"]:checked').val()
                          || 'pending';

            // If Delivered status is selected, check booking status first
            if (newStatus === 'delivered') {
                // Block if sale is still booked or draft - must be confirmed first
                if (saleStatus === 'booked' || saleStatus === 'draft') {
                    // Revert radio back to previous state
                    $('input[name="order_state_' + saleId + '"][value="' + prevStatus + '"], input[name="m_order_state_' + saleId + '"][value="' + prevStatus + '"]').prop('checked', true);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirm Order First!',
                        text: 'This sale has not been confirmed yet. Please confirm the order before marking it as delivered.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b',
                    });
                    return;
                }
            }

            // Set radio checked state in UI
            $('input[name="order_state_' + saleId + '"][value="' + newStatus + '"], input[name="m_order_state_' + saleId + '"][value="' + newStatus + '"]').prop('checked', true);

            // Close active dropdowns
            $('.dropdown-menu.show').removeClass('show');
            $('.dropdown-submenu.show').removeClass('show');
            $('.dropdown.show').removeClass('show');
            $('[data-toggle="dropdown"]').attr('aria-expanded', 'false');

            // If Delivered status is selected, open Product Specifications & Delivery Modal!
            if (newStatus === 'delivered') {
                window.openDeliverySpecsModal(saleId);
                return;
            }

            let directUrl = '{{ url("sales") }}/' + saleId + '/order-status';
            let fallbackUrl = '{{ url("sale") }}/' + saleId + '/order-status';
            let pathLoc = window.location.pathname.replace(/\/(sale|sales|bookings)(\/.*)?$/i, '');
            let relativeUrl = (pathLoc ? pathLoc : '') + '/sales/' + saleId + '/order-status';

            function sendUpdate(urlToTry, nextFallback) {
                $.ajax({
                    url: urlToTry,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_status: newStatus
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response && response.success) {
                            // Update badge HTML in both desktop table and mobile card
                            $('.sale-state-cell[data-sale-id="' + saleId + '"]').html(response.badge_html);
                            if (newStatus !== 'delivered') {
                                $('.sale-serial-cell[data-sale-id="' + saleId + '"]').html('<span class="text-muted font-monospace" style="font-size: 0.75rem;">-</span>');
                            }

                            // Update active indicator in dropdowns for this sale
                            $('[data-id="' + saleId + '"].btn-change-order-status').removeClass('active fw-bold');
                            $('[data-id="' + saleId + '"][data-status="' + newStatus + '"].btn-change-order-status').addClass('active fw-bold');

                            // Synchronize radio button selection across desktop & mobile
                            $('input[name="order_state_' + saleId + '"][value="' + newStatus + '"], input[name="m_order_state_' + saleId + '"][value="' + newStatus + '"]').prop('checked', true);

                            if (typeof Swal !== 'undefined') {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message || 'State updated successfully!'
                                });
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', response.message || 'Failed to update state.', 'error');
                            } else {
                                alert(response.message || 'Failed to update state.');
                            }
                        }
                    },
                    error: function(xhr) {
                        if (nextFallback) {
                            nextFallback();
                        } else {
                            let msg = 'Failed to update state.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', msg, 'error');
                            } else {
                                alert(msg);
                            }
                        }
                    }
                });
            }

            // Try directUrl -> fallbackUrl -> relativeUrl
            sendUpdate(directUrl, function() {
                sendUpdate(fallbackUrl, function() {
                    sendUpdate(relativeUrl, null);
                });
            });
        };

        // Global function to open Delivery Specs Modal
        window.openDeliverySpecsModal = function(saleId) {
            if (!saleId) return;

            // Ensure modal is attached to body so it is always on top without z-index/overflow clipping
            if ($('#deliverySpecsModal').parent()[0] !== document.body) {
                $('#deliverySpecsModal').appendTo('body');
            }

            $('#modalDeliverySaleId').val(saleId);
            $('#modalDeliveryLoader').removeClass('d-none');
            $('#modalDeliveryContent').addClass('d-none');
            $('#modalDeliveryItemsContainer').empty();
            $('#btnSubmitDeliverySpecs').prop('disabled', true);

            // Open modal using Bootstrap / jQuery
            try {
                if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                    $('#deliverySpecsModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                    $('#deliverySpecsModal').modal('show');
                } else if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                    const modalEl = document.getElementById('deliverySpecsModal');
                    const modalInst = window.bootstrap.Modal.getInstance(modalEl) || new window.bootstrap.Modal(modalEl);
                    modalInst.show();
                } else {
                    $('#deliverySpecsModal').modal('show');
                }
            } catch(e) {
                console.error('Modal show error', e);
                $('#deliverySpecsModal').modal('show');
            }

            let directUrl = '{{ url("sales") }}/' + saleId + '/delivery-details';
            let fallbackUrl = '{{ url("sale") }}/' + saleId + '/delivery-details';
            let pathLoc = window.location.pathname.replace(/\/(sale|sales|bookings)(\/.*)?$/i, '');
            let relativeUrl = (pathLoc ? pathLoc : '') + '/sales/' + saleId + '/delivery-details';

            function fetchSpecs(urlToTry, nextFallback) {
                $.ajax({
                    url: urlToTry,
                    type: 'GET',
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(res) {
                        if (res && res.success && res.sale) {
                            $('#modalDeliveryInvoiceNo').text('#' + res.sale.id + ' (' + res.sale.invoice_no + ')');
                            $('#modalDeliveryCustomer').text(res.sale.customer_name || 'Walk-in Customer');
                            $('#modalDeliveryDate').val(res.sale.delivery_date || new Date().toISOString().split('T')[0]);
                            $('#modalDeliverySource').val(res.sale.delivery_source || '');
                            $('#modalDeliveryRemarks').val(res.sale.delivery_remarks || '');
                            $('#modalDeliveryItemsCount').text(res.sale.items.length);

                            let html = '';
                            if (res.sale.items.length === 0) {
                                html = '<div class="alert alert-warning py-2 text-center small">No items found for this order.</div>';
                            } else {
                                res.sale.items.forEach(function(item, idx) {
                                    let techNameVal = item.technical_name || item.product_name || '';
                                    html += `
                                    <div class="card border rounded-3 p-3 bg-white shadow-sm mb-3" style="border-color: #cbd5e1 !important;">
                                        {{-- Item Header --}}
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                            <div class="fw-bold text-dark d-flex align-items-center flex-wrap gap-1" style="font-size: 0.86rem;">
                                                <span class="badge bg-primary text-white font-monospace px-2 py-1">Item #${idx + 1}</span>
                                                <span class="text-dark fw-bold ms-1">${item.product_name}</span>
                                                ${item.brand ? '<span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">' + item.brand + '</span>' : ''}
                                                ${item.item_code ? '<span class="badge bg-light text-muted border font-monospace" style="font-size: 0.72rem;">SKU: ' + item.item_code + '</span>' : ''}
                                            </div>
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold font-monospace" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.76rem;">
                                                Quantity: ${parseFloat(item.qty) || item.qty}
                                            </span>
                                        </div>

                                        {{-- Side-by-Side Dual Card Grid --}}
                                        <div class="row g-3">
                                            {{-- LEFT CARD: Image 1 - Company Technical Document Card --}}
                                            <div class="col-lg-6">
                                                <div class="h-100 p-3 rounded-3 border" style="background-color: #f8fafc; border-color: #93c5fd !important; border-top: 3px solid #2563eb !important;">
                                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom" style="border-color: #e2e8f0 !important;">
                                                        <span class="fw-bold text-primary small d-flex align-items-center gap-1.5">
                                                            <i class="fas fa-file-contract"></i>
                                                            <span>Company Technical Document</span>
                                                        </span>
                                                        <span class="badge bg-primary text-white" style="font-size: 0.65rem; letter-spacing: 0.3px;">INTERNAL SPEC SHEET</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.73rem;">
                                                            Equipment / Technical Title <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control form-control-sm fw-bold bg-white text-dark" name="items[${item.id}][technical_name]" value="${techNameVal}" placeholder='e.g. Induction Heater 60 kw for Forging' required>
                                                        <div class="text-muted" style="font-size: 0.68rem;">Company technical sheet par title print hoga</div>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.73rem;">
                                                            Technical Specifications &amp; Parameters
                                                        </label>
                                                        <textarea class="form-control form-control-sm bg-white" rows="2" name="items[${item.id}][technical_specs]" placeholder="e.g. Power: 60KW, Input: 380V 3-Phase, Frequency: 30-100kHz, Water Cooled, Custom Coil">${item.technical_specs || ''}</textarea>
                                                    </div>

                                                    <div class="mb-0">
                                                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.73rem;">
                                                            Technical Details, QC &amp; Engineering Remarks
                                                        </label>
                                                        <input type="text" class="form-control form-control-sm bg-white" name="items[${item.id}][technical_remarks]" value="${item.technical_remarks || ''}" placeholder="e.g. Tested on full load, tuned coil, ready for dispatch">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- RIGHT CARD: Image 2 - Customer Sale Invoice Card --}}
                                            <div class="col-lg-6">
                                                <div class="h-100 p-3 rounded-3 border" style="background-color: #fcfdfd; border-color: #86efac !important; border-top: 3px solid #10b981 !important;">
                                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom" style="border-color: #e2e8f0 !important;">
                                                        <span class="fw-bold text-success small d-flex align-items-center gap-1.5">
                                                            <i class="fas fa-file-invoice"></i>
                                                            <span>Sale Invoice Specifications</span>
                                                        </span>
                                                        <span class="badge bg-success text-white" style="font-size: 0.65rem; letter-spacing: 0.3px;">CUSTOMER PRINT</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.73rem;">Model / Specifications</label>
                                                        <input type="text" class="form-control form-control-sm fw-semibold bg-white" name="items[${item.id}][model]" value="${item.model || ''}" placeholder="e.g. LTZ-60KW, 3-Phase 380V">
                                                        <div class="text-muted" style="font-size: 0.68rem;">Customer invoice ke Model field me aayega</div>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.73rem;">Serial Number / Unique Code</label>
                                                        <input type="text" class="form-control form-control-sm font-monospace fw-bold text-primary bg-white" name="items[${item.id}][serial_no]" value="${item.serial_no || ''}" placeholder="e.g. SN-2026-00891">
                                                    </div>

                                                    <div class="mb-0">
                                                        <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.73rem;">Configuration / Notes / Specs</label>
                                                        <input type="text" class="form-control form-control-sm bg-white" name="items[${item.id}][specs]" value="${item.specs || ''}" placeholder="e.g. T9 sport, 50Hz Water Cooled">
                                                        <div class="text-muted" style="font-size: 0.68rem;">Customer invoice par specifications me aayega</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                });
                            }

                            $('#modalDeliveryItemsContainer').html(html);
                            $('#modalDeliveryLoader').addClass('d-none');
                            $('#modalDeliveryContent').removeClass('d-none');
                            $('#btnSubmitDeliverySpecs').prop('disabled', false);
                        } else {
                            if (nextFallback) nextFallback();
                            else $('#modalDeliveryLoader').html('<div class="text-danger py-3"><i class="fas fa-exclamation-triangle me-1"></i> Failed to retrieve item specifications.</div>');
                        }
                    },
                    error: function() {
                        if (nextFallback) nextFallback();
                        else $('#modalDeliveryLoader').html('<div class="text-danger py-3"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load item details. Please check network.</div>');
                    }
                });
            }

            fetchSpecs(directUrl, function() {
                fetchSpecs(fallbackUrl, function() {
                    fetchSpecs(relativeUrl, null);
                });
            });
        };

        $(document).ready(function() {
            let submenuHoverTimer = null;

            // Auto-detect viewport space: if near bottom, open upwards (dropup)
            $(document).on('show.bs.dropdown', '.dropdown', function () {
                let $btn = $(this).find('[data-toggle="dropdown"], [data-bs-toggle="dropdown"]');
                if ($btn.length) {
                    let offset = $btn.offset();
                    let spaceBelow = $(window).height() - (offset.top - $(window).scrollTop()) - $btn.outerHeight();
                    if (spaceBelow < 280) {
                        $(this).addClass('dropup');
                    } else {
                        $(this).removeClass('dropup');
                    }
                }
            });

            // Hover into submenu or toggle button: show instantly and clear hide timer
            $(document).on('mouseenter', '.dropdown-submenu', function() {
                clearTimeout(submenuHoverTimer);
                let $submenu = $(this);
                $('.dropdown-submenu').not($submenu).removeClass('show is-hovered');
                $submenu.addClass('show is-hovered');
            });

            // Hover out: 1-second (1000ms) grace period delay before closing to prevent accidental collapse
            $(document).on('mouseleave', '.dropdown-submenu', function() {
                let $submenu = $(this);
                submenuHoverTimer = setTimeout(function() {
                    $submenu.removeClass('show is-hovered');
                }, 1000);
            });

            // Click toggle support
            $(document).on('click', '.dropdown-submenu-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearTimeout(submenuHoverTimer);
                let $submenu = $(this).closest('.dropdown-submenu');
                $('.dropdown-submenu').not($submenu).removeClass('show is-hovered');
                $submenu.toggleClass('show');
            });

            // Prevent dropdown from closing prematurely when clicking within submenu header/options
            $(document).on('click', '.dropdown-submenu-menu', function(e) {
                e.stopPropagation();
            });

            // Clean up when parent action dropdown is closed
            $(document).on('hidden.bs.dropdown', '.dropdown', function() {
                clearTimeout(submenuHoverTimer);
                $(this).find('.dropdown-submenu').removeClass('show is-hovered');
            });

            // Submit Delivery Specifications Form (delegated - modal is dynamically appended to body)
            $(document).on('submit', '#formDeliverySpecs', function(e) {
                e.preventDefault();
                const saleId = $('#modalDeliverySaleId').val();
                if (!saleId) return;

                const $btn = $('#btnSubmitDeliverySpecs');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving Specs...');

                let directUrl = '{{ url("sales") }}/' + saleId + '/delivery-details';
                let fallbackUrl = '{{ url("sale") }}/' + saleId + '/delivery-details';
                let pathLoc = window.location.pathname.replace(/\/(sale|sales|bookings)(\/.*)?$/i, '');
                let relativeUrl = (pathLoc ? pathLoc : '') + '/sales/' + saleId + '/delivery-details';

                function sendDelivery(urlToTry, nextFallback) {
                    $.ajax({
                        url: urlToTry,
                        type: 'POST',
                        data: $('#formDeliverySpecs').serialize(),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            $btn.prop('disabled', false).html(origHtml);
                            if (response && response.success) {
                                // Close modal first - BS4 jQuery approach + cleanup
                                $('#deliverySpecsModal').modal('hide');
                                setTimeout(function() {
                                    $('#deliverySpecsModal').removeClass('show').css('display', 'none');
                                    $('.modal-backdrop').remove();
                                    $('body').removeClass('modal-open').css('padding-right', '');
                                }, 300);

                                let docUrl = response.technical_doc_url || ('{{ url("sales") }}/' + saleId + '/technical-doc');

                                // Show SweetAlert with direct link to Technical Document
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Specifications Saved!',
                                        html: `<p class="mb-3 text-muted" style="font-size: 0.95rem;">${response.message || 'Specifications saved &amp; Technical Document created!'}</p>
                                               <a href="${docUrl}" target="_blank" class="btn btn-primary btn-sm px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="border-radius: 6px;">
                                                   <i class="fas fa-file-contract"></i> View Technical Document
                                               </a>`,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Done / Reload Page',
                                        confirmButtonColor: '#10b981',
                                    }).then(function() {
                                        window.location.reload();
                                    });
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                if (nextFallback) {
                                    nextFallback();
                                } else {
                                    if (typeof Swal !== 'undefined') Swal.fire('Error', response.message || 'Failed to save specifications.', 'error');
                                    else alert(response.message || 'Failed to save specifications.');
                                }
                            }
                        },
                        error: function(xhr) {
                            if (nextFallback) {
                                nextFallback();
                            } else {
                                $btn.prop('disabled', false).html(origHtml);
                                let msg = 'Failed to save specifications.';
                                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                                if (typeof Swal !== 'undefined') Swal.fire('Error', msg, 'error');
                                else alert(msg);
                            }
                        }
                    });
                }

                sendDelivery(directUrl, function() {
                    sendDelivery(fallbackUrl, function() {
                        sendDelivery(relativeUrl, null);
                    });
                });
            });

            // Function to initialize DataTable
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('.datanew')) {
                    $('.datanew').DataTable().destroy();
                }
                $('.datanew').DataTable({
                    "pageLength": 10,
                    "order": [],
                    "autoWidth": false,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search sales..."
                    },
                    "dom": "<'row mb-3 align-items-center'<'col-12 col-md-6 mb-2 mb-md-0'l><'col-12 col-md-6'f>>" +
                        "<'row'<'col-12'tr>>" +
                        "<'row mt-3 align-items-center'<'col-12 col-md-5 mb-2 mb-md-0'i><'col-12 col-md-7'p>>",
                });
            }

            // Initial call
            initDataTable();

            // Mobile Filter Panel Toggle
            $('#toggleFilterPanel').on('click', function() {
                $('#filterPanelContainer').slideToggle(200);
            });

            // Quick Filter Logic
            $(document).on('change', '#quick_filter', function() {
                let val = $(this).val();
                let today = new Date();
                let start = new Date();
                let end = new Date();

                if (val === 'daily') {
                    // Start and end are both today
                } else if (val === 'weekly') {
                    let day = today.getDay();
                    let diff = today.getDate() - day + (day === 0 ? -6 : 1);
                    start.setDate(diff);
                } else if (val === 'monthly') {
                    start.setDate(1);
                } else if (val === 'yearly') {
                    start.setMonth(0, 1);
                } else if (val === 'custom') {
                    return;
                }

                let pickerFrom = document.getElementById('filter_from_date') ? document.getElementById('filter_from_date')._flatpickr : null;
                let pickerTo = document.getElementById('filter_to_date') ? document.getElementById('filter_to_date')._flatpickr : null;
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
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Searching...');

                let formData = $(this).serialize();
                let urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('status')) {
                    formData += '&status=' + urlParams.get('status');
                }

                $.ajax({
                    url: window.location.pathname,
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        $btn.prop('disabled', false).html(origHtml);
                        
                        if ($.fn.DataTable.isDataTable('.datanew')) {
                            $('.datanew').DataTable().destroy();
                        }
                        
                        $('#salesTableBody').html(response.html);
                        
                        // Update Stat Cards dynamically if present
                        if (response.stats) {
                            $('#statTotalCount').text(Number(response.stats.total_count || 0).toLocaleString());
                            $('#statTotalNet').text('Rs. ' + Number(response.stats.total_net || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#statTotalDiscount').text('Rs. ' + Number(response.stats.total_discount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#statStatusCounts').html((response.stats.booked_count || 0) + ' <span class="fs-6 fw-normal text-muted">/ ' + (response.stats.confirmed_booking_count || 0) + '</span>');
                        }

                        initDataTable();
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).html(origHtml);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Failed to retrieve filtered list.', 'error');
                        }
                    }
                });
            });

            $(document).on('click', '.btn-change-order-status', function(e) {
                const $btn = $(this);
                const saleId = $btn.attr('data-id') || $btn.data('id');
                const newStatus = $btn.attr('data-status') || $btn.data('status');
                window.changeSaleOrderStatus(e, saleId, newStatus, this);
            });

            // Reset form
            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                $('#filterForm').trigger('submit');
            });

            // Confirm Booking Action
            $(document).on('click', '.confirm-booking-btn', function(e) {
                e.preventDefault();
                let form = $(this).closest("form");

                Swal.fire({
                    title: "Confirm Order?",
                    text: "Are you sure you want to convert this order to a posted sale? This will update stocks and post ledgers.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Confirm it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
