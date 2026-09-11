@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Repairing & Service Management
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

        .btn-erp-outline {
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #334155 !important;
            font-weight: 600;
            font-size: 0.815rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
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

        /* Main Data Card & Table Container */
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
            padding: 0.65rem 0.5rem !important;
            border: 1px solid #bfdbfe !important;
            border-bottom: 2px solid #60a5fa !important;
            white-space: nowrap;
            vertical-align: middle !important;
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

        /* Bill Tag */
        .erp-bill-tag {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            font-size: 0.76rem;
            color: #2563eb;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 0.2rem 0.45rem;
            border-radius: 5px;
            display: inline-block;
        }

        /* Avatar */
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
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }

        /* Status Badges */
        .erp-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.22rem 0.55rem;
            border-radius: 50px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            line-height: 1.15;
            white-space: nowrap;
        }
        .badge-received {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .badge-diagnosing {
            background-color: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }
        .badge-in-progress {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .badge-waiting-parts {
            background-color: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }
        .badge-completed {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .badge-delivered {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-cancelled {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Action Buttons */
        .btn-erp-table-action {
            background-color: #ffffff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: 600;
            border-radius: 6px;
            height: 26px;
            padding: 0 0.55rem;
            font-size: 0.70rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            transition: all 0.15s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .btn-erp-table-action:hover {
            background-color: #eff6ff;
            border-color: #3b82f6;
            color: #1e40af;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.15);
        }

        /* DataTables Modern ERP Controls */
        .dataTables_wrapper {
            font-size: 0.80rem;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 0.85rem;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 6px !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.78rem !important;
            color: #0f172a !important;
            outline: none !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 6px !important;
            padding: 0.3rem 0.6rem 0.3rem 2rem !important;
            font-size: 0.78rem !important;
            color: #0f172a !important;
            outline: none !important;
            background-color: #ffffff !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233b82f6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3ccircle cx='11' cy='11' r='8'%3e%3c/circle%3e%3cline x1='21' y1='21' x2='16.65' y2='16.65'%3e%3c/line%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: 8px center !important;
            background-size: 13px 13px !important;
            transition: all 0.15s ease-in-out !important;
            min-width: 220px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 0.76rem !important;
            font-weight: 600 !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #334155 !important;
            margin: 0 2px !important;
            cursor: pointer;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Page Header --}}
                <div class="erp-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h1 class="erp-title">
                            <i class="fas fa-tools text-primary"></i> Repairing &amp; Service Management
                        </h1>
                        <p class="erp-subtitle">Manage customer repair intake, diagnostic job sheets, technician status &amp; account payments</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a class="btn-erp-outline" href="{{ route('sale.index') }}">
                            <i class="fas fa-receipt"></i> Sales Ledger
                        </a>
                        <a class="btn-erp-primary" href="{{ route('repair.create') }}">
                            <i class="fas fa-plus-circle"></i> + Receive Product / New Job
                        </a>
                    </div>
                </div>

                {{-- Metric KPI Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Total Repair Jobs</div>
                                <div class="erp-kpi-value text-primary font-monospace">{{ $totalCount }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Active in Workshop</div>
                                <div class="erp-kpi-value text-warning font-monospace">{{ $activeCount }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #fffbeb; color: #d97706;">
                                <i class="fas fa-wrench"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Ready for Pickup</div>
                                <div class="erp-kpi-value text-success font-monospace">{{ $completedCount }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Revenue Collected</div>
                                <div class="erp-kpi-value text-dark font-monospace">Rs {{ number_format($revenueCollected, 2) }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #3b82f6;">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Filter Tabs --}}
                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                    <a href="{{ route('repair.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                       class="btn btn-sm {{ !request('status') || request('status') === 'all' ? 'btn-primary fw-bold' : 'btn-white border text-secondary' }}"
                       style="border-radius: 20px; padding: 4px 14px; font-size: 0.75rem;">
                        <i class="fas fa-list me-1"></i> All Jobs ({{ $totalCount }})
                    </a>
                    <a href="{{ route('repair.index', array_merge(request()->except('status'), ['status' => 'active'])) }}"
                       class="btn btn-sm {{ request('status') === 'active' ? 'btn-warning text-white fw-bold' : 'btn-white border text-secondary' }}"
                       style="border-radius: 20px; padding: 4px 14px; font-size: 0.75rem;">
                        <i class="fas fa-spinner fa-spin me-1"></i> Active in Workshop ({{ $activeCount }})
                    </a>
                    <a href="{{ route('repair.index', array_merge(request()->except('status'), ['status' => 'completed'])) }}"
                       class="btn btn-sm {{ request('status') === 'completed' ? 'btn-success text-white fw-bold' : 'btn-white border text-secondary' }}"
                       style="border-radius: 20px; padding: 4px 14px; font-size: 0.75rem;">
                        <i class="fas fa-check me-1"></i> Ready / Completed ({{ $completedCount }})
                    </a>
                    <a href="{{ route('repair.index', array_merge(request()->except('status'), ['status' => 'delivered'])) }}"
                       class="btn btn-sm {{ request('status') === 'delivered' ? 'btn-secondary text-white fw-bold' : 'btn-white border text-secondary' }}"
                       style="border-radius: 20px; padding: 4px 14px; font-size: 0.75rem;">
                        <i class="fas fa-box me-1"></i> Delivered ({{ $deliveredCount }})
                    </a>
                </div>

                {{-- Main Data Card --}}
                <div class="erp-main-card">
                    <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center" style="border-color: #dbeafe !important;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark" style="font-size: 0.90rem;">
                                <i class="fas fa-table text-primary me-1"></i> Repair Jobs Register
                            </span>
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 font-monospace" style="font-size: 0.70rem;">
                                {{ $repairs->count() }} Records
                            </span>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="table-responsive erp-table-responsive">
                            <table id="repair-table" class="erp-table table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 45px;">ID</th>
                                        <th style="min-width: 110px;">Ticket #</th>
                                        <th style="min-width: 140px;">Customer</th>
                                        <th style="min-width: 150px;">Product / Device</th>
                                        <th style="min-width: 160px;">Reported Defect</th>
                                        <th class="text-center" style="min-width: 90px;">Received</th>
                                        <th class="text-center" style="min-width: 90px;">Promised</th>
                                        <th class="text-end" style="min-width: 95px;">Est. Cost</th>
                                        <th class="text-end text-success" style="min-width: 100px;">Advance</th>
                                        <th class="text-end text-danger" style="min-width: 95px;">Due</th>
                                        <th class="text-center" style="min-width: 110px;">Status</th>
                                        <th class="text-center" style="width: 110px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($repairs as $repair)
                                        @php
                                            $initial = strtoupper(substr($repair->customer_display_name, 0, 1));
                                            $isWalkin = empty($repair->customer_id);
                                        @endphp
                                        <tr>
                                            {{-- ID --}}
                                            <td class="text-center font-monospace text-muted fw-bold" data-order="{{ $repair->id }}">
                                                #{{ $repair->id }}
                                            </td>

                                            {{-- Ticket # --}}
                                            <td>
                                                <a href="{{ route('repair.show', $repair->id) }}" class="erp-bill-tag text-decoration-none">
                                                    {{ $repair->repair_no }}
                                                </a>
                                                @if($repair->priority === 'urgent')
                                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger ms-1" style="font-size: 0.62rem;">Urgent</span>
                                                @endif
                                            </td>

                                            {{-- Customer --}}
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="erp-avatar">
                                                        {{ $initial }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark" style="font-size: 0.78rem;">{{ $repair->customer_display_name }}</div>
                                                        <div class="text-muted small" style="font-size: 0.68rem;">
                                                            <i class="fas fa-phone-alt me-1 text-secondary" style="font-size: 0.60rem;"></i>{{ $repair->customer_display_phone }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Product / Device --}}
                                            <td>
                                                <strong class="text-dark" style="font-size: 0.78rem;">{{ $repair->item_name }}</strong>
                                                @if($repair->brand_model || $repair->serial_no)
                                                    <div class="text-muted small" style="font-size: 0.68rem;">
                                                        {{ $repair->brand_model }} {{ $repair->serial_no ? '• S/N: '.$repair->serial_no : '' }}
                                                    </div>
                                                @endif
                                                @if($repair->accessories_received)
                                                    <div class="text-secondary small" style="font-size: 0.65rem;" title="Accessories: {{ $repair->accessories_received }}">
                                                        <i class="fas fa-plug text-muted me-1"></i>{{ Str::limit($repair->accessories_received, 20) }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Reported Defect --}}
                                            <td>
                                                <div class="text-dark" style="font-size: 0.74rem;" title="{{ $repair->problem_description }}">
                                                    {{ Str::limit($repair->problem_description, 45) }}
                                                </div>
                                            </td>

                                            {{-- Received Date --}}
                                            <td class="text-center font-monospace text-muted" data-order="{{ $repair->received_date->timestamp }}">
                                                {{ $repair->received_date->format('d/m/Y') }}
                                            </td>

                                            {{-- Promised Date --}}
                                            <td class="text-center font-monospace" data-order="{{ optional($repair->expected_delivery_date)->timestamp ?? 0 }}">
                                                @if($repair->expected_delivery_date)
                                                    <span class="{{ $repair->expected_delivery_date->isPast() && $repair->status !== 'delivered' ? 'text-danger fw-bold' : 'text-muted' }}">
                                                        {{ $repair->expected_delivery_date->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Est. Cost --}}
                                            <td class="text-end font-monospace text-dark fw-semibold">
                                                {{ number_format($repair->estimated_cost, 2) }}
                                            </td>

                                            {{-- Advance Paid --}}
                                            <td class="text-end font-monospace text-success fw-bold">
                                                {{ number_format($repair->advance_paid, 2) }}
                                                @if($repair->advanceAccount)
                                                    <div class="text-muted small" style="font-size: 0.62rem;" title="Account: {{ $repair->advanceAccount->title }}">
                                                        <i class="fas fa-wallet text-secondary me-1"></i>{{ Str::limit($repair->advanceAccount->title, 10) }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Due --}}
                                            <td class="text-end font-monospace text-danger fw-bold">
                                                {{ number_format($repair->due_amount, 2) }}
                                            </td>

                                            {{-- Status Badge --}}
                                            <td class="text-center">
                                                <span class="erp-badge {{ $repair->status_badge_class }}">
                                                    @if($repair->status === 'received')
                                                        <i class="fas fa-inbox"></i>
                                                    @elseif($repair->status === 'in_progress')
                                                        <i class="fas fa-wrench"></i>
                                                    @elseif($repair->status === 'completed')
                                                        <i class="fas fa-check-circle"></i>
                                                    @elseif($repair->status === 'delivered')
                                                        <i class="fas fa-handshake"></i>
                                                    @else
                                                        <i class="fas fa-circle" style="font-size: 5px;"></i>
                                                    @endif
                                                    {{ $repair->status_label }}
                                                </span>
                                            </td>

                                            {{-- Actions --}}
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    {{-- View Details & Audit --}}
                                                    <a href="{{ route('repair.show', $repair->id) }}" class="btn-erp-table-action" title="View Job Card & Audit Trail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    {{-- Print 80mm Slip --}}
                                                    <a href="{{ route('repair.print.thermal', $repair->id) }}" target="_blank" class="btn-erp-table-action text-dark" title="Print 80mm Thermal Slip">
                                                        <i class="fas fa-receipt"></i>
                                                    </a>
                                                    {{-- Print A4 Job Sheet --}}
                                                    <a href="{{ route('repair.print.a4', $repair->id) }}" target="_blank" class="btn-erp-table-action text-primary" title="Print A4 Job Sheet">
                                                        <i class="fas fa-print"></i>
                                                    </a>

                                                    {{-- Status Modal Trigger --}}
                                                    @if($repair->status !== 'delivered' && $repair->status !== 'cancelled')
                                                        <button type="button" class="btn-erp-table-action text-warning border-warning-subtle" 
                                                                data-bs-toggle="modal" data-bs-target="#statusModal{{ $repair->id }}" title="Update Status">
                                                            <i class="fas fa-tasks"></i>
                                                        </button>

                                                        <button type="button" class="btn-erp-table-action text-success border-success-subtle" 
                                                                data-bs-toggle="modal" data-bs-target="#deliverModal{{ $repair->id }}" title="Deliver to Customer & Final Payment">
                                                            <i class="fas fa-truck-loading"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                {{-- Modal: Update Status --}}
                                                @if($repair->status !== 'delivered')
                                                <div class="modal fade" id="statusModal{{ $repair->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start border-0 shadow">
                                                            <form action="{{ route('repair.status.update', $repair->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header bg-light py-2">
                                                                    <h6 class="modal-title font-weight-bold text-dark" style="font-size: 0.85rem;">
                                                                        <i class="fas fa-tasks text-primary me-1"></i> Update Repair Status #{{ $repair->repair_no }}
                                                                    </h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body p-3">
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold text-muted">Current Item</label>
                                                                        <div class="p-2 bg-light rounded border small">
                                                                            <strong>{{ $repair->item_name }}</strong> ({{ $repair->customer_display_name }})
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Select New Status <span class="text-danger">*</span></label>
                                                                        <select name="status" class="form-select form-select-sm" required>
                                                                            <option value="received" {{ $repair->status === 'received' ? 'selected' : '' }}>Received (Intake)</option>
                                                                            <option value="diagnosing" {{ $repair->status === 'diagnosing' ? 'selected' : '' }}>Diagnosing / Inspection</option>
                                                                            <option value="in_progress" {{ $repair->status === 'in_progress' ? 'selected' : '' }}>In Progress (Under Repair)</option>
                                                                            <option value="waiting_parts" {{ $repair->status === 'waiting_parts' ? 'selected' : '' }}>Waiting for Parts</option>
                                                                            <option value="completed" {{ $repair->status === 'completed' ? 'selected' : '' }}>Completed (Ready for Pickup)</option>
                                                                            <option value="cancelled" {{ $repair->status === 'cancelled' ? 'selected' : '' }}>Cancelled / Cannot Repair</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Technician Diagnostic Notes</label>
                                                                        <textarea name="technician_notes" class="form-control form-control-sm" rows="2" placeholder="e.g. Capacitor replaced, tested under 220V load...">{{ $repair->technician_notes }}</textarea>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Audit Remark / Note</label>
                                                                        <input type="text" name="log_note" class="form-control form-control-sm" placeholder="e.g. Work started by technician...">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer py-2 bg-light">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-sm btn-primary">Save Status</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Modal: Deliver & Collect Payment --}}
                                                <div class="modal fade" id="deliverModal{{ $repair->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start border-0 shadow">
                                                            <form action="{{ route('repair.deliver', $repair->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header bg-success text-white py-2">
                                                                    <h6 class="modal-title font-weight-bold" style="font-size: 0.85rem;">
                                                                        <i class="fas fa-handshake me-1"></i> Deliver Product &amp; Collect Payment
                                                                    </h6>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body p-3">
                                                                    <div class="alert alert-light border py-2 px-3 mb-2 small">
                                                                        <div><strong>Ticket:</strong> {{ $repair->repair_no }} — {{ $repair->item_name }}</div>
                                                                        <div><strong>Customer:</strong> {{ $repair->customer_display_name }} ({{ $repair->customer_display_phone }})</div>
                                                                        <div><strong>Advance Already Paid:</strong> Rs. {{ number_format($repair->advance_paid, 2) }}</div>
                                                                    </div>

                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-6">
                                                                            <label class="form-label small fw-bold">Labor / Service Charges <span class="text-danger">*</span></label>
                                                                            <input type="number" step="0.01" min="0" name="service_charges" class="form-control form-control-sm font-monospace fw-bold" 
                                                                                   value="{{ $repair->estimated_cost > 0 ? $repair->estimated_cost : 0 }}" required>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <label class="form-label small fw-bold">Spare Parts Cost</label>
                                                                            <input type="number" step="0.01" min="0" name="parts_charges" class="form-control form-control-sm font-monospace" value="0">
                                                                        </div>
                                                                    </div>

                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-6">
                                                                            <label class="form-label small fw-bold text-success">Final Amount Paid Now</label>
                                                                            <input type="number" step="0.01" min="0" name="final_paid" class="form-control form-control-sm font-monospace fw-bold text-success" 
                                                                                   value="{{ max(0, $repair->estimated_cost - $repair->advance_paid) }}">
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <label class="form-label small fw-bold">Deposit Account</label>
                                                                            <select name="final_account_id" class="form-select form-select-sm">
                                                                                <option value="">Select Account (Cash/Bank)</option>
                                                                                @foreach($accounts as $acc)
                                                                                    <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->account_code }})</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Delivery Remarks</label>
                                                                        <input type="text" name="delivery_notes" class="form-control form-control-sm" placeholder="e.g. Tested in front of customer, all working fine.">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer py-2 bg-light">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-sm btn-success fw-bold">Complete Delivery &amp; Save</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- DataTables Dependencies --}}
                <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
                <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

                <script>
                    $(document).ready(function() {
                        $('#repair-table').DataTable({
                            pageLength: 10,
                            lengthMenu: [5, 10, 25, 50, 100],
                            order: [
                                [0, 'desc']
                            ],
                            language: {
                                search: "",
                                searchPlaceholder: "Search ticket, customer, device...",
                                lengthMenu: "Show _MENU_ entries",
                                info: "Showing _START_ to _END_ of _TOTAL_ repair jobs",
                                paginate: {
                                    previous: '<i class="fas fa-chevron-left"></i>',
                                    next: '<i class="fas fa-chevron-right"></i>'
                                }
                            }
                        });
                    });
                </script>

            </div>
        </div>
    </div>
@endsection
