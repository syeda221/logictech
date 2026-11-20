@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Quotations Management
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
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
        .erp-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .erp-subtitle {
            font-size: 0.825rem;
            color: #64748b;
            margin-top: 0.2rem;
            margin-bottom: 0;
            font-weight: 500;
        }

        /* Top Action Buttons */
        .btn-erp-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
            border: none;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.825rem;
            border-radius: 10px;
            padding: 0.6rem 1.15rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-erp-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }

        .btn-erp-outline {
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #334155 !important;
            font-weight: 700;
            font-size: 0.825rem;
            border-radius: 10px;
            padding: 0.55rem 1rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .btn-erp-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a !important;
            transform: translateY(-1px);
        }

        .btn-erp-outline-danger {
            background-color: #ffffff;
            border: 1.5px solid #fca5a5;
            color: #dc2626 !important;
            font-weight: 700;
            font-size: 0.825rem;
            border-radius: 10px;
            padding: 0.55rem 1rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 1px 2px rgba(220, 38, 38, 0.05);
        }
        .btn-erp-outline-danger:hover {
            background-color: #fef2f2;
            border-color: #f87171;
            color: #b91c1c !important;
            transform: translateY(-1px);
        }

        /* KPI Metric Cards */
        .erp-kpi-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.2rem 1.35rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .erp-kpi-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            transform: translateY(-3px);
        }
        .erp-kpi-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 0.35rem;
        }
        .erp-kpi-value {
            font-size: 1.45rem;
            font-weight: 800;
            line-height: 1.2;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .erp-kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        /* Filter Panel Single-Row System */
        .erp-filter-card {
            background-color: #ffffff;
            border: 1.5px solid #e0e7ff;
            border-radius: 14px;
            padding: 0.9rem 1.15rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.04);
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
        .erp-item-quick { flex: 1.1; min-width: 105px; }
        .erp-item-date { flex: 1; min-width: 95px; }
        .erp-item-search { flex: 1.4; min-width: 140px; }
        .erp-item-status { flex: 1.1; min-width: 110px; }
        .erp-item-customer { flex: 1.6; min-width: 150px; }
        .erp-item-actions {
            flex: 0 0 auto;
            margin-left: auto;
        }
        .erp-filter-label {
            font-size: 0.70rem;
            font-weight: 800;
            color: #3730a3;
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
            outline: none !important;
        }

        /* Pill Buttons */
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
            transition: all 0.15s ease !important;
        }
        .btn-erp-pill-primary:hover {
            background-color: #1d4ed8 !important;
            color: #ffffff !important;
        }
        .btn-erp-pill-outline {
            background-color: #ffffff !important;
            border: 1.5px solid #bfdbfe !important;
            color: #1e40af !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            border-radius: 50px !important;
            height: 34px !important;
            padding: 0 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            transition: all 0.15s ease !important;
        }
        .btn-erp-pill-outline:hover {
            background-color: #eff6ff !important;
            border-color: #3b82f6 !important;
            color: #1d4ed8 !important;
        }

        /* Table Container */
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

        /* Badges */
        .erp-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.45rem;
            border-radius: 5px;
            font-size: 0.68rem;
            font-weight: 600;
            line-height: 1.15;
            white-space: nowrap;
            border: 1px solid transparent;
        }
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
        .erp-badge.state-cancelled {
            background-color: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }

        /* Avatar & Tags */
        .erp-avatar {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.70rem;
        }
        .erp-avatar-registered {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }
        .erp-avatar-walkin {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }
        .erp-bill-tag {
            font-family: monospace;
            font-weight: 700;
            color: #2563eb;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            transition: all 0.15s ease;
        }
        .erp-bill-tag:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #1d4ed8;
        }
        .btn-erp-table-action {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-weight: 600;
            border-radius: 6px;
            height: 26px;
            padding: 0 0.5rem;
            font-size: 0.70rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.15s ease;
        }
        .btn-erp-table-action:hover,
        .btn-erp-table-action:focus,
        .btn-erp-table-action[aria-expanded="true"] {
            background-color: #eff6ff;
            border-color: #3b82f6;
            color: #1e40af;
        }

        @media (max-width: 768px) {
            .sales-hdr-actions {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                width: 100%;
            }
            .erp-filter-single-row {
                flex-wrap: wrap;
            }
            .erp-filter-item {
                flex: 1 1 100%;
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

                {{-- Page Header (Matches Image 3) --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                    <div>
                        <h4 class="erp-title">
                            <i class="fas fa-file-contract text-primary"></i> Quotations Management
                        </h4>
                        <p class="erp-subtitle">Executive quotation ledger, proposals overview, status and quotation records</p>
                    </div>
                    <div class="sales-hdr-actions">
                        <a class="btn-erp-primary" href="{{ route('quotations.create') }}">
                            <i class="fas fa-plus"></i> New Quote
                        </a>
                        <a class="btn-erp-outline" href="{{ route('repair.index') }}" title="Repairing & Service Management">
                            <i class="fas fa-tools text-primary"></i> Repairing
                        </a>
                        <a class="btn-erp-outline-danger" href="{{ route('sale.return.index') }}">
                            <i class="fas fa-undo-alt"></i> Returns
                        </a>
                        <a class="btn-erp-outline" href="{{ route('sale.index') }}" title="Go to Sales Management">
                            <i class="fas fa-shopping-cart text-info"></i> Order
                        </a>
                    </div>
                </div>

                {{-- KPI Metric Cards (Matches Image 3) --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Total Quotations</div>
                                <div class="erp-kpi-value" id="statTotalCount">{{ number_format($stats['total_count'] ?? 0) }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                                <i class="fas fa-file-signature"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Total Quote Value</div>
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
                                <div class="erp-kpi-label">Converted / Total</div>
                                <div class="erp-kpi-value" id="statStatusCounts" style="color: #0284c7 !important;">
                                    {{ $stats['converted_count'] ?? 0 }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['total_count'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #f0f9ff; color: #0284c7;">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Card --}}
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

                        {{-- Modern ERP Filter Panel (Single-Row Toolbar matching Image 3) --}}
                        <div class="erp-filter-card" id="filterPanelContainer">
                            <form id="filterForm" action="{{ route('quotations.index') }}" method="GET" class="erp-filter-single-row" autocomplete="off">
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
                                    <input type="text" class="form-control erp-filter-input datepicker-custom bg-white" name="from_date" id="filter_from_date" value="{{ request('from_date') }}" placeholder="dd/mm/yy">
                                </div>

                                <div class="erp-filter-item erp-item-date">
                                    <label class="erp-filter-label"><i class="far fa-calendar-check text-primary"></i> To</label>
                                    <input type="text" class="form-control erp-filter-input datepicker-custom bg-white" name="to_date" id="filter_to_date" value="{{ request('to_date') }}" placeholder="dd/mm/yy">
                                </div>

                                <div class="erp-filter-item erp-item-search">
                                    <label class="erp-filter-label"><i class="fas fa-search text-primary"></i> Serial / Search</label>
                                    <input type="text" class="form-control erp-filter-input" name="serial_no" id="filter_serial_no" value="{{ request('serial_no') }}" placeholder="Quote # / Product / Client...">
                                </div>

                                <div class="erp-filter-item erp-item-status">
                                    <label class="erp-filter-label"><i class="fas fa-tasks text-primary"></i> Status</label>
                                    <select class="form-select erp-filter-input" name="status" id="filter_status">
                                        <option value="all">All Status</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>🔴 Pending</option>
                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>🟢 Approved</option>
                                        <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>🔵 Converted</option>
                                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>🟡 Cancelled</option>
                                    </select>
                                </div>

                                <div class="erp-filter-item erp-item-customer">
                                    <label class="erp-filter-label"><i class="fas fa-user text-primary"></i> Customer</label>
                                    <select class="form-select erp-filter-input select2-customer" name="customer_id" id="filter_customer_id" style="width: 100%;">
                                        <option value="">All Customers</option>
                                        @if(isset($customers))
                                            @foreach ($customers as $c)
                                                <option value="{{ $c->id }}" {{ (string)$c->id === (string)request('customer_id') ? 'selected' : '' }}>
                                                    {{ $c->customer_id ? $c->customer_id . ' — ' : '' }}{{ $c->customer_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="erp-filter-item erp-item-actions ms-auto">
                                    <div class="d-flex align-items-center gap-1">
                                        <a href="{{ route('quotations.index') }}" class="btn btn-erp-pill-outline" id="btnReset" title="Reset Filters">
                                            <i class="fas fa-undo"></i> <span>Reset</span>
                                        </a>
                                        <button type="submit" class="btn btn-erp-pill-primary" id="btnSearch" title="Apply Filter">
                                            <i class="fas fa-filter"></i> <span>Filter</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Table Container (Matches Image 3 Column Structure) --}}
                        <div class="erp-table-responsive table-responsive">
                            <table id="quotations-table" class="table erp-table datanew" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="min-width: 140px;">Customer</th>
                                        <th style="min-width: 95px;">Quote No.</th>
                                        <th style="min-width: 160px;">Products</th>
                                        <th class="text-center" style="width: 38px;">Qty</th>
                                        <th class="text-end" style="width: 85px;">Gross</th>
                                        <th class="text-end" style="width: 85px;">Add. Disc</th>
                                        <th class="text-end" style="width: 80px;">GST</th>
                                        <th class="text-end" style="width: 90px;">Net Total</th>
                                        <th class="text-center" style="width: 75px;">Date</th>
                                        <th class="text-center" style="width: 80px;">Confirmation</th>
                                        <th class="pe-2 text-center" style="width: 70px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="quotationsTableBody">
                                    @include('admin_panel.quotations.partials.quotations_table_body')
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
    <script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Flatpickr
            if (typeof flatpickr !== 'undefined') {
                flatpickr('.datepicker-custom', {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true
                });
            }

            // Quick Filter Period Logic
            $('#quick_filter').on('change', function() {
                var val = $(this).val();
                if (val === 'custom') return;

                var today = new Date();
                var fromDate = new Date();
                var toDate = new Date();

                if (val === 'daily') {
                    // today
                } else if (val === 'weekly') {
                    fromDate.setDate(today.getDate() - 7);
                } else if (val === 'monthly') {
                    fromDate.setMonth(today.getMonth() - 1);
                } else if (val === 'yearly') {
                    fromDate.setFullYear(today.getFullYear() - 1);
                }

                function formatYMD(d) {
                    var month = '' + (d.getMonth() + 1),
                        day = '' + d.getDate(),
                        year = d.getFullYear();
                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;
                    return [year, month, day].join('-');
                }

                $('#filter_from_date').val(formatYMD(fromDate));
                $('#filter_to_date').val(formatYMD(toDate));
                $('#filterForm').submit();
            });

            // AJAX Filter Submit
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var data = $(this).serialize();

                $('#quotationsTableBody').css('opacity', '0.5');

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: data,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(res) {
                        $('#quotationsTableBody').html(res.html).css('opacity', '1');
                        if (res.stats) {
                            $('#statTotalCount').text(Number(res.stats.total_count || 0).toLocaleString());
                            $('#statTotalNet').text('Rs. ' + Number(res.stats.total_net || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                            $('#statTotalDiscount').text('Rs. ' + Number(res.stats.total_discount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                            $('#statStatusCounts').html((res.stats.converted_count || 0) + ' <span class="fs-6 fw-normal text-muted">/ ' + (res.stats.total_count || 0) + '</span>');
                        }
                    },
                    error: function(xhr) {
                        $('#quotationsTableBody').css('opacity', '1');
                        console.error('Filter error', xhr);
                    }
                });
            });
        });
    </script>
@endsection
