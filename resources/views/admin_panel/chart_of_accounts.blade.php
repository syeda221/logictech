@extends('admin_panel.layout.app')

@section('content')
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Chart of Accounts
           ========================================================================== */
        :root {
            --erp-primary: #2563eb;
            --erp-primary-hover: #1d4ed8;
            --erp-primary-light: #eff6ff;
            --erp-border-blue: #bfdbfe;
            --erp-border-subtle: #dbeafe;
            --erp-text-dark: #0f172a;
            --erp-text-muted: #64748b;
            --erp-text-navy: #1e40af;
            --erp-card-bg: #ffffff;
            --erp-row-hover: #f0f7ff;
        }

        body {
            background-color: #f8fafc !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            color: #1e293b !important;
        }

        .main-container {
            border: 1.5px solid #dbeafe !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04) !important;
            background-color: #ffffff !important;
            padding: 1rem !important;
            max-width: 100%;
        }

        /* Page Header */
        .erp-page-header {
            margin-bottom: 1rem;
        }
        .erp-title {
            font-weight: 700;
            font-size: 1.15rem;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .erp-subtitle {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0;
            font-weight: 400;
        }

        /* Pill Buttons */
        .btn-erp-pill-primary {
            background-color: #2563eb !important;
            border: 1.5px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 0.76rem !important;
            border-radius: 50px !important;
            height: 34px !important;
            padding: 0 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.25) !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
            text-decoration: none !important;
        }
        .btn-erp-pill-primary:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(37, 99, 235, 0.35) !important;
            color: #ffffff !important;
        }

        .btn-erp-pill-outline {
            background-color: #ffffff !important;
            border: 1.5px solid #bfdbfe !important;
            color: #1e40af !important;
            font-weight: 600 !important;
            font-size: 0.76rem !important;
            border-radius: 50px !important;
            height: 34px !important;
            padding: 0 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
            text-decoration: none !important;
        }
        .btn-erp-pill-outline:hover {
            background-color: #eff6ff !important;
            border-color: #3b82f6 !important;
            color: #1d4ed8 !important;
            transform: translateY(-1px);
        }

        /* KPI Cards */
        .erp-kpi-card {
            background: #ffffff;
            border: 1.5px solid #dbeafe;
            border-radius: 10px;
            padding: 0.75rem 0.95rem;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            height: 100%;
        }
        .erp-kpi-card:hover {
            border-color: #93c5fd;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.08);
            transform: translateY(-1px);
        }
        .erp-kpi-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 0.2rem;
            display: flex;
            align-items: center;
        }
        .erp-kpi-value {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }
        .erp-kpi-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* Main Table Container */
        .erp-main-card {
            background: #ffffff;
            border: 1.5px solid #dbeafe;
            border-radius: 10px;
            padding: 0.85rem;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.03);
        }

        /* ERP Table */
        .erp-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
            border: 1px solid #bfdbfe !important;
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
            vertical-align: middle !important;
            white-space: nowrap;
        }
        .erp-table tbody td {
            padding: 0.45rem 0.4rem !important;
            font-size: 0.78rem !important;
            color: #1e293b !important;
            border: 1px solid #dbeafe !important;
            vertical-align: middle !important;
            background-color: #ffffff;
            transition: background-color 0.15s ease;
        }
        .erp-table tbody tr:hover td {
            background-color: #f0f7ff !important;
        }

        /* Row Action Pills */
        .btn-action-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 8px;
            height: 27px;
            border-radius: 6px;
            transition: all 0.15s ease;
            text-decoration: none !important;
            white-space: nowrap;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .btn-pill-ledger {
            background-color: #eff6ff;
            color: #1e40af !important;
            border-color: #bfdbfe;
        }
        .btn-pill-ledger:hover {
            background-color: #2563eb;
            color: #ffffff !important;
            border-color: #2563eb;
            box-shadow: 0 2px 5px rgba(37, 99, 235, 0.25);
            transform: translateY(-1px);
        }
        .btn-pill-edit {
            background-color: #f0fdf4;
            color: #15803d !important;
            border-color: #bbf7d0;
        }
        .btn-pill-edit:hover {
            background-color: #16a34a;
            color: #ffffff !important;
            border-color: #16a34a;
            box-shadow: 0 2px 5px rgba(22, 163, 74, 0.25);
            transform: translateY(-1px);
        }
        .btn-pill-history {
            background-color: #f8fafc;
            color: #475569 !important;
            border-color: #cbd5e1;
        }
        .btn-pill-history:hover {
            background-color: #475569;
            color: #ffffff !important;
            border-color: #475569;
            box-shadow: 0 2px 5px rgba(71, 85, 105, 0.25);
            transform: translateY(-1px);
        }
        .btn-pill-status {
            width: 27px;
            height: 27px;
            padding: 0;
            border-radius: 6px;
        }
        .btn-pill-status.status-active {
            background-color: #fef2f2;
            color: #dc2626 !important;
            border-color: #fecaca;
        }
        .btn-pill-status.status-active:hover {
            background-color: #dc2626;
            color: #ffffff !important;
        }
        .btn-pill-status.status-inactive {
            background-color: #ecfdf5;
            color: #059669 !important;
            border-color: #a7f3d0;
        }
        .btn-pill-status.status-inactive:hover {
            background-color: #059669;
            color: #ffffff !important;
        }

        /* DataTables Controls Modernization */
        .dataTables_wrapper {
            padding: 0.25rem 0;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 0.85rem;
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 600;
        }
        .dataTables_wrapper .dataTables_length label {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 0.78rem !important;
            color: #475569 !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 6px !important;
            padding: 2px 24px 2px 8px !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            color: #1e40af !important;
            outline: none !important;
            height: 32px !important;
            background-color: #ffffff !important;
            cursor: pointer;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 0.85rem;
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 600;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 0.78rem !important;
            color: #475569 !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
            position: relative !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 50px !important;
            padding: 4px 14px 4px 30px !important;
            font-size: 0.78rem !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            outline: none !important;
            height: 32px !important;
            min-width: 210px !important;
            background-color: #ffffff !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233b82f6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3ccircle cx='11' cy='11' r='8'%3e%3c/circle%3e%3cline x1='21' y1='21' x2='16.65' y2='16.65'%3e%3c/line%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: 10px center !important;
            background-size: 13px 13px !important;
            transition: all 0.15s ease-in-out !important;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0.85rem !important;
            padding-top: 0.5rem !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 50px !important;
            padding: 3px 12px !important;
            font-size: 0.76rem !important;
            font-weight: 600 !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #334155 !important;
            margin: 0 2px !important;
            transition: all 0.15s ease !important;
            cursor: pointer;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25) !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1e40af !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: #e2e8f0 !important;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 0.76rem !important;
            color: #64748b !important;
            padding-top: 0.85rem !important;
            font-weight: 600 !important;
        }

        /* Suppress Sorting arrows on non-sortable columns */
        table.dataTable thead th.no-sort:before,
        table.dataTable thead th.no-sort:after,
        table.dataTable thead td.no-sort:before,
        table.dataTable thead td.no-sort:after {
            display: none !important;
            content: "" !important;
        }
        table.dataTable thead th.no-sort {
            cursor: default !important;
            padding-right: 0.4rem !important;
        }

        /* Modal ERP Overrides */
        .modal-content {
            border: 1.5px solid #dbeafe !important;
            border-radius: 12px !important;
            overflow: hidden !important;
        }
        .modal-header {
            background-color: #eff6ff !important;
            border-bottom: 1.5px solid #bfdbfe !important;
            padding: 0.85rem 1.25rem !important;
        }
        .modal-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            letter-spacing: -0.01em;
        }
        .modal-body .form-control,
        .modal-body .form-select {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 8px !important;
            font-size: 0.78rem !important;
            padding: 0.4rem 0.75rem !important;
            font-weight: 500 !important;
            color: #0f172a !important;
        }
        .modal-body select.form-select,
        .modal-body select.form-control {
            appearance: none !important;
            -webkit-appearance: none !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%232563eb' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 12px 10px !important;
            padding-right: 32px !important;
            cursor: pointer;
        }
        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .modal-body label {
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            color: #1e40af !important;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.25rem;
        }
        .btn-save-complete {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 6px 18px !important;
            font-size: 0.82rem !important;
            border: none !important;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25) !important;
            transition: all 0.15s ease !important;
        }
        .btn-save-complete:hover {
            background: #059669 !important;
            transform: translateY(-1px);
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35) !important;
        }
    </style>

    @php
        $totalAccounts = $accounts->count();
        $activeAccounts = $accounts->where('status', 1)->count();
        $totalCash = 0;
        $totalBank = 0;
        foreach ($accounts as $acc) {
            $headName = strtolower(optional($acc->head)->name ?? '');
            $code = strtolower($acc->account_code ?? '');
            $title = strtolower($acc->title ?? '');
            if (str_contains($headName, 'cash') || str_contains($code, 'cash') || str_contains($title, 'cash')) {
                $totalCash += (float) $acc->current_balance;
            } elseif (str_contains($headName, 'bank') || str_contains($code, 'bank') || str_contains($title, 'bank')) {
                $totalBank += (float) $acc->current_balance;
            }
        }
    @endphp

    <div class="container-fluid py-2 px-2">
        <div class="main-container bg-white mx-auto">

            {{-- TOP HEADER BAR --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 erp-page-header">
                <div>
                    <h4 class="erp-title">
                        <i class="fas fa-sitemap text-primary"></i> <span>Chart Of Accounts</span>
                    </h4>
                    <p class="erp-subtitle">Manage financial ledger accounts, categories, opening balances &amp; audit history</p>
                </div>
                @can('chart.of.accounts.create')
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-erp-pill-primary" data-toggle="modal" data-target="#addAccountModal">
                            <i class="fas fa-plus"></i> <span>Add New Account</span>
                        </button>
                        <button type="button" class="btn btn-erp-pill-outline" data-toggle="modal" data-target="#addHeadModal">
                            <i class="fas fa-folder-plus text-primary"></i> <span>Add Category</span>
                        </button>
                        <button type="button" class="btn btn-erp-pill-outline" data-toggle="modal" data-target="#manageHeadsModal">
                            <i class="fas fa-layer-group text-secondary"></i> <span>All Categories ({{ $heads->count() }})</span>
                        </button>
                    </div>
                @endcan
            </div>

            {{-- KPI METRICS STRIP --}}
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label"><i class="fas fa-wallet me-1 text-primary"></i> Total Accounts</div>
                            <div class="erp-kpi-value">{{ $totalAccounts }} <small class="text-muted fw-normal" style="font-size:0.72rem;">({{ $activeAccounts }} Active)</small></div>
                        </div>
                        <div class="erp-kpi-icon" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;">
                            <i class="fas fa-university"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label"><i class="fas fa-money-bill-wave me-1 text-success"></i> Cash in Hand</div>
                            <div class="erp-kpi-value text-success font-monospace">Rs {{ number_format($totalCash, 2) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label"><i class="fas fa-credit-card me-1 text-info"></i> Bank Accounts</div>
                            <div class="erp-kpi-value text-primary font-monospace">Rs {{ number_format($totalBank, 2) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background:#eff6ff; color:#0284c7; border:1px solid #bae6fd;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label"><i class="fas fa-layer-group me-1" style="color:#7c3aed;"></i> Categories</div>
                            <div class="erp-kpi-value text-dark">{{ $heads->count() }} <small class="text-muted fw-normal" style="font-size:0.72rem;">Heads</small></div>
                        </div>
                        <div class="erp-kpi-icon" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;">
                            <i class="fas fa-sitemap"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-2" role="alert" style="padding:8px 14px; font-size:0.8rem; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0 !important;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-2" role="alert" style="padding:8px 14px; font-size:0.8rem; background:#fef2f2; color:#991b1b; border:1px solid #fecaca !important;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

                {{-- ══════════════════════════════════════════════════════════════
                     DESKTOP TABLE VIEW (Visible on tablet / desktop screens)
                ══════════════════════════════════════════════════════════════ --}}
            {{-- DESKTOP DATA TABLE VIEW --}}
            <div class="erp-main-card d-none d-md-block">
                <div class="table-responsive">
                    <table class="table erp-table align-middle datanew" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center no-sort" style="width: 4%;">#</th>
                                <th style="width: 10%;">Code</th>
                                <th style="width: 14%;">Head / Group</th>
                                <th style="width: 22%;">Account Title</th>
                                <th class="text-center" style="width: 8%;">Type</th>
                                <th style="width: 16%;">Opening / Current</th>
                                <th class="text-center" style="width: 8%;">Status</th>
                                <th class="text-center no-sort" style="width: 18%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $acc)
                                <tr>
                                    {{-- # --}}
                                    <td class="text-center fw-bold text-muted font-monospace" style="font-size:0.75rem;">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- Code --}}
                                    <td>
                                        <span class="badge" style="background:#f8fafc; color:#1e293b; border:1px solid #e2e8f0; font-family:monospace; font-size:0.75rem; border-radius:6px; padding:4px 8px;">
                                            {{ $acc->account_code ?? 'N/A' }}
                                        </span>
                                    </td>

                                    {{-- Head / Group --}}
                                    <td>
                                        <span class="badge" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; border-radius:50px; font-size:0.70rem; padding:4px 10px;">
                                            <i class="far fa-folder me-1"></i>{{ $acc->head->name ?? '-' }}
                                        </span>
                                        @if ($acc->head && $acc->head->parent_id)
                                            <small class="text-muted d-block ms-1" style="font-size: 0.68rem;">({{ $acc->head->parent->name ?? '' }})</small>
                                        @endif
                                    </td>

                                    {{-- Account Title --}}
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size:0.82rem;">{{ $acc->title }}</div>
                                    </td>

                                    {{-- Type --}}
                                    <td class="text-center">
                                        @if ($acc->type == 'Debit')
                                            <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:50px; font-size:0.70rem; padding:3px 9px;">
                                                Debit
                                            </span>
                                        @else
                                            <span class="badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; border-radius:50px; font-size:0.70rem; padding:3px 9px;">
                                                Credit
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Opening / Current Balance --}}
                                    <td>
                                        <div class="fw-bold font-monospace" style="font-size:0.82rem; color: {{ $acc->current_balance < 0 ? '#dc2626' : '#15803d' }};">
                                            Rs {{ number_format(abs($acc->current_balance), 2) }}
                                            <span class="badge" style="background: {{ $acc->current_balance >= 0 ? '#f0fdf4' : '#fef2f2' }}; color: {{ $acc->current_balance >= 0 ? '#15803d' : '#dc2626' }}; border: 1px solid {{ $acc->current_balance >= 0 ? '#bbf7d0' : '#fecaca' }}; font-size:0.65rem; padding: 2px 5px; border-radius: 4px;">
                                                {{ $acc->current_balance >= 0 ? 'Dr' : 'Cr' }}
                                            </span>
                                        </div>
                                        <small class="text-muted d-block font-monospace" style="font-size:0.68rem;">
                                            Opening: Rs {{ number_format($acc->opening_balance, 2) }}
                                        </small>
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @if ($acc->status)
                                            <span class="badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; border-radius:50px; font-size:0.70rem; padding:4px 10px;">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge" style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca; border-radius:50px; font-size:0.70rem; padding:4px 10px;">
                                                <i class="fas fa-ban me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Action --}}
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
                                            {{-- Ledger --}}
                                            <a href="{{ route('accounts.ledger', $acc->id) }}" class="btn-action-pill btn-pill-ledger" title="View Account Ledger">
                                                <i class="fas fa-book me-1"></i> Ledger
                                            </a>

                                            {{-- Edit --}}
                                            <button type="button" class="btn-action-pill btn-pill-edit" data-toggle="modal" data-bs-toggle="modal" data-target="#editAccountModal{{ $acc->id }}" data-bs-target="#editAccountModal{{ $acc->id }}" title="Edit Account / Ledger Balance">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>

                                            {{-- History --}}
                                            <button type="button" class="btn-action-pill btn-pill-history" data-toggle="modal" data-bs-toggle="modal" data-target="#historyAccountModal{{ $acc->id }}" data-bs-target="#historyAccountModal{{ $acc->id }}" title="Audit History">
                                                <i class="fas fa-history me-1"></i> ({{ $acc->histories->count() }})
                                            </button>

                                            {{-- Toggle Status --}}
                                            <form action="{{ route('accounts.toggleStatus', $acc->id) }}" method="POST" style="display:inline-block; margin: 0;">
                                                @csrf
                                                <button type="button" onclick="this.closest('form').submit()" class="btn-action-pill btn-pill-status {{ $acc->status ? 'status-active' : 'status-inactive' }}" title="{{ $acc->status ? 'Deactivate Account' : 'Activate Account' }}">
                                                    <i class="fas {{ $acc->status ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE CARDS VIEW --}}
            <div class="d-block d-md-none">
                @foreach ($accounts as $acc)
                    <div class="card border mb-2 shadow-sm rounded-3" style="border: 1.5px solid #dbeafe !important; background: #ffffff;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge" style="background:#f8fafc; color:#1e293b; border:1px solid #e2e8f0; font-family:monospace; font-size:0.75rem; border-radius:6px; padding:3px 8px;">
                                    {{ $acc->account_code ?? 'N/A' }}
                                </span>
                                @if ($acc->status)
                                    <span class="badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; border-radius:50px; font-size:0.68rem; padding:3px 8px;">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge" style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca; border-radius:50px; font-size:0.68rem; padding:3px 8px;">
                                        <i class="fas fa-ban me-1"></i>Inactive
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-1 mb-1">
                                <span class="badge" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; border-radius:50px; font-size:0.68rem; padding:2px 8px;">
                                    {{ $acc->head->name ?? '-' }}
                                </span>
                                <span class="badge" style="background-color: {{ $acc->type == 'Debit' ? '#eff6ff' : '#fef3c7' }}; color: {{ $acc->type == 'Debit' ? '#1d4ed8' : '#b45309' }}; border: 1px solid {{ $acc->type == 'Debit' ? '#bfdbfe' : '#fde68a' }}; border-radius:50px; font-size:0.68rem; padding:2px 8px;">
                                    {{ $acc->type }}
                                </span>
                            </div>

                            <div class="fw-bold text-dark fs-6 mb-2">{{ $acc->title }}</div>

                            <div class="p-2 mb-2 rounded-2" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.75rem;">
                                    <span class="text-muted">Opening:</span>
                                    <span class="font-monospace fw-semibold text-dark">Rs {{ number_format($acc->opening_balance, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:0.80rem;">
                                    <span class="text-muted">Current:</span>
                                    <span class="fw-bold font-monospace" style="color: {{ $acc->current_balance < 0 ? '#dc2626' : '#15803d' }};">
                                        Rs {{ number_format(abs($acc->current_balance), 2) }}
                                        <small>{{ $acc->current_balance >= 0 ? 'Dr' : 'Cr' }}</small>
                                    </span>
                                </div>
                            </div>

                            <div class="row g-1">
                                <div class="col-6">
                                    <a href="{{ route('accounts.ledger', $acc->id) }}" class="btn-action-pill btn-pill-ledger w-100 py-1">
                                        <i class="fas fa-book me-1"></i> Ledger
                                    </a>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn-action-pill btn-pill-edit w-100 py-1" data-toggle="modal" data-target="#editAccountModal{{ $acc->id }}">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn-action-pill btn-pill-history w-100 py-1" data-toggle="modal" data-target="#historyAccountModal{{ $acc->id }}">
                                        <i class="fas fa-history me-1"></i> History ({{ $acc->histories->count() }})
                                    </button>
                                </div>
                                <div class="col-6">
                                    <form action="{{ route('accounts.toggleStatus', $acc->id) }}" method="POST" class="w-100 m-0">
                                        @csrf
                                        <button type="button" onclick="this.closest('form').submit()" class="btn-action-pill btn-pill-status {{ $acc->status ? 'status-active' : 'status-inactive' }} w-100 py-1">
                                            <i class="fas {{ $acc->status ? 'fa-ban' : 'fa-check' }} me-1"></i> {{ $acc->status ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

                {{-- ══════════════════════════════════════════════════════════════
                     ALL MODALS RENDERED OUTSIDE (FULL MOBILE & DESKTOP COMPATIBILITY)
                ══════════════════════════════════════════════════════════════ --}}
                @foreach ($accounts as $acc)

                    {{-- EDIT ACCOUNT & LEDGER BALANCE MODAL --}}
                    <div class="modal fade" id="editAccountModal{{ $acc->id }}" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel{{ $acc->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" action="{{ route('accounts.update', $acc->id) }}" method="POST">
                                @csrf
                                <div class="modal-header d-flex align-items-center justify-content-between px-4 py-3" style="background:#eff6ff; border-bottom: 1.5px solid #bfdbfe;">
                                    <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="editAccountModalLabel{{ $acc->id }}" style="font-size:0.92rem;">
                                        <i class="fas fa-edit text-primary"></i> <span>Edit Account &amp; Ledger: <span class="text-primary">{{ $acc->title }}</span></span>
                                    </h6>
                                    <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4 text-start">
                                    <div class="alert mb-3 p-2 rounded-3" style="background:#eff6ff; border:1px solid #bfdbfe; font-size:0.75rem; color:#1e40af;">
                                        <i class="fas fa-info-circle me-1"></i> Aap <strong>Current Ledger Balance</strong> ko direct edit / update karke naya amount daal sakte hain.
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Select Head (Category)</label>
                                        <select class="form-control form-select" name="head_id" required style="height: 38px;">
                                            @foreach ($heads as $head)
                                                <option value="{{ $head->id }}" {{ $acc->head_id == $head->id ? 'selected' : '' }}>{{ $head->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Account Title</label>
                                        <input type="text" name="title" class="form-control" value="{{ $acc->title }}" required style="height: 38px;">
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-md-4 col-12 mb-2 mb-md-0">
                                            <div class="form-group mb-0">
                                                <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Type</label>
                                                <select class="form-control form-select" name="type" style="height: 38px;">
                                                    <option value="Debit" {{ $acc->type == 'Debit' ? 'selected' : '' }}>Debit</option>
                                                    <option value="Credit" {{ $acc->type == 'Credit' ? 'selected' : '' }}>Credit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Opening Balance</label>
                                                <input type="number" step="0.01" name="opening_balance" class="form-control font-monospace fw-bold" value="{{ $acc->opening_balance }}" required style="height: 38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #15803d; text-transform: uppercase; letter-spacing: 0.03em;">Current Balance</label>
                                                <input type="number" step="0.01" name="current_balance" class="form-control fw-bold font-monospace text-success border-success" value="{{ $acc->current_balance }}" required style="height: 38px;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-3 mb-3">
                                        <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Reason / Note for Ledger Edit (Optional)</label>
                                        <input type="text" name="note" class="form-control" placeholder="e.g., 'Manual payment addition of Rs 5,000'" style="height: 38px;">
                                    </div>

                                    <div class="form-group mb-0">
                                        <div class="form-check custom-control custom-checkbox">
                                            <input type="checkbox" class="form-check-input custom-control-input" id="statusCheck{{ $acc->id }}" name="status" {{ $acc->status ? 'checked' : '' }}>
                                            <label class="form-check-label custom-control-label small text-secondary fw-semibold" for="statusCheck{{ $acc->id }}">Active Account</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer px-4 py-3" style="background:#f8fafc; border-top: 1px solid #e2e8f0;">
                                    <button type="button" class="btn btn-erp-pill-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:34px; font-size:0.78rem;">Cancel</button>
                                    <button type="submit" class="btn btn-save-complete" style="height:34px; font-size:0.80rem; padding: 4px 20px !important;">
                                        <i class="fas fa-check-circle me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- ACCOUNT AUDIT HISTORY MODAL --}}
                    <div class="modal fade" id="historyAccountModal{{ $acc->id }}" tabindex="-1" role="dialog" aria-labelledby="historyAccountModalLabel{{ $acc->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                <div class="modal-header d-flex align-items-center justify-content-between px-4 py-3" style="background:#eff6ff; border-bottom: 1.5px solid #bfdbfe;">
                                    <div>
                                        <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="historyAccountModalLabel{{ $acc->id }}" style="font-size:0.92rem;">
                                            <i class="fas fa-history text-primary"></i> <span>Audit History: <span class="text-primary">{{ $acc->title }}</span></span>
                                        </h6>
                                        <small class="text-muted" style="font-size:0.72rem;">Record of all balance edits and modifications</small>
                                    </div>
                                    <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body p-0 custom-scroll" style="max-height: 60vh; overflow-y: auto;">
                                    @if($acc->histories->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table erp-table table-hover align-middle mb-0 text-nowrap" style="font-size:.82rem;">
                                                <thead>
                                                    <tr>
                                                        <th class="ps-4">Date &amp; Time</th>
                                                        <th>User</th>
                                                        <th class="text-end">Old Balance</th>
                                                        <th class="text-end">New Balance</th>
                                                        <th class="ps-4">Remarks / Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($acc->histories as $h)
                                                        <tr>
                                                            <td class="ps-4 font-monospace small text-muted">{{ $h->created_at->format('d-M-Y h:i:s A') }}</td>
                                                            <td>
                                                                <span class="badge" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; border-radius:50px; font-size:0.70rem; padding:3px 8px;">
                                                                    {{ $h->user_name ?? ($h->user->name ?? 'User') }}
                                                                </span>
                                                            </td>
                                                            <td class="text-end text-muted font-monospace">Rs {{ number_format($h->old_balance, 2) }}</td>
                                                            <td class="text-end fw-bold text-primary font-monospace">Rs {{ number_format($h->new_balance, 2) }}</td>
                                                            <td class="ps-4 text-muted small">{{ $h->note ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5 text-muted">
                                            <i class="fas fa-history fa-3x mb-3" style="color:#cbd5e1;"></i>
                                            <p class="fw-bold mb-1 text-dark">No edit history recorded yet</p>
                                            <small class="text-muted">History entries will automatically appear here whenever opening balance or account details are updated.</small>
                                        </div>
                                    @endif
                                </div>

                                <div class="modal-footer px-4 py-3" style="background:#f8fafc; border-top: 1px solid #e2e8f0;">
                                    <button type="button" class="btn btn-erp-pill-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:34px; font-size:0.78rem;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach

                <!-- Add New Account Modal -->
                <div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" action="{{ route('accounts.store') }}" method="POST">
                            @csrf
                            <div class="modal-header d-flex align-items-center justify-content-between px-4 py-3" style="background:#eff6ff; border-bottom: 1.5px solid #bfdbfe;">
                                <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="addAccountModalLabel" style="font-size:0.92rem;">
                                    <i class="fas fa-plus-circle text-primary"></i> <span>Add New Account</span>
                                </h6>
                                <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <div class="alert mb-3 p-2 rounded-3" style="background:#eff6ff; border:1px solid #bfdbfe; font-size:0.75rem; color:#1e40af;">
                                    <i class="fas fa-info-circle me-1"></i> Enter account details to add a new ledger to the Chart of Accounts.
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Select Head (Category) <span class="text-danger">*</span></label>
                                    <select class="form-control form-select" name="head_id" required style="height: 38px;">
                                        <option value="">Select Head / Category</option>
                                        @foreach ($heads as $head)
                                            <option value="{{ $head->id }}">{{ $head->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Account Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g., UBL Current, Cash Drawer 1" required style="height: 38px;">
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Account Type</label>
                                            <select class="form-control form-select" name="type" style="height: 38px;">
                                                <option value="Debit">Debit</option>
                                                <option value="Credit">Credit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Opening Balance</label>
                                            <input type="number" step="0.01" name="opening_balance" class="form-control font-monospace fw-bold" value="0" style="height: 38px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <div class="form-check custom-control custom-checkbox">
                                        <input type="checkbox" class="form-check-input custom-control-input" id="statusCheck" name="status" checked>
                                        <label class="form-check-label custom-control-label small text-secondary fw-semibold" for="statusCheck">Active Account</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer px-4 py-3" style="background:#f8fafc; border-top: 1px solid #e2e8f0;">
                                <button type="button" class="btn btn-erp-pill-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:34px; font-size:0.78rem;">Close</button>
                                <button type="submit" class="btn btn-save-complete" style="height:34px; font-size:0.80rem; padding: 4px 20px !important;">
                                    <i class="fas fa-check-circle me-1"></i> Save Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ADD CATEGORY / HEAD MODAL --}}
                <div class="modal fade" id="addHeadModal" tabindex="-1" role="dialog" aria-labelledby="addHeadLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" action="{{ route('account-heads.store') }}" method="POST">
                            @csrf
                            <div class="modal-header d-flex align-items-center justify-content-between px-4 py-3" style="background:#eff6ff; border-bottom: 1.5px solid #bfdbfe;">
                                <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="addHeadLabel" style="font-size:0.92rem;">
                                    <i class="fas fa-folder-plus text-primary"></i> <span>Add New Category / Head</span>
                                </h6>
                                <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <div class="alert mb-3 p-2 rounded-3" style="background:#eff6ff; border:1px solid #bfdbfe; font-size:0.75rem; color:#1e40af;">
                                    <i class="fas fa-info-circle me-1"></i> <strong>Note:</strong> Categories / Heads are top-level parent groups (e.g. <strong>Cash</strong>, <strong>Bank</strong>). Individual accounts should be created via <strong>Add New Account</strong> under a head.
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Category / Head Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g., Cash, Bank, Mobile Wallets" required style="height: 38px;">
                                </div>
                            </div>
                            <div class="modal-footer px-4 py-3" style="background:#f8fafc; border-top: 1px solid #e2e8f0;">
                                <button type="button" class="btn btn-erp-pill-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:34px; font-size:0.78rem;">Close</button>
                                <button type="submit" class="btn btn-save-complete" style="height:34px; font-size:0.80rem; padding: 4px 20px !important;">
                                    <i class="fas fa-check-circle me-1"></i> Save Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MANAGE CATEGORIES / HEADS MODAL --}}
                <div class="modal fade" id="manageHeadsModal" tabindex="-1" role="dialog" aria-labelledby="manageHeadsLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header d-flex align-items-center justify-content-between px-4 py-3" style="background:#eff6ff; border-bottom: 1.5px solid #bfdbfe;">
                                <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="manageHeadsLabel" style="font-size:0.92rem;">
                                    <i class="fas fa-layer-group text-primary"></i> <span>Account Categories / Heads Management</span>
                                </h6>
                                <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-3">
                                <div class="table-responsive">
                                    <table class="table erp-table align-middle mb-0" style="font-size:0.80rem;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 8%;">#</th>
                                                <th style="width: 50%;">Category / Head Name</th>
                                                <th style="width: 22%;">Accounts Linked</th>
                                                <th style="width: 20%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($heads as $h)
                                                <tr>
                                                    <td class="text-center fw-bold text-muted font-monospace">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="fw-bold text-dark">{{ $h->name }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; border-radius:50px; font-size:0.70rem; padding:3px 8px;">
                                                            {{ $h->accounts_count }} {{ Str::plural('Account', $h->accounts_count) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                                            {{-- Edit Head Button --}}
                                                            <button type="button" class="btn-action-pill btn-pill-edit" data-toggle="modal" data-bs-toggle="modal" data-target="#editHeadModal{{ $h->id }}" data-bs-target="#editHeadModal{{ $h->id }}" data-dismiss="modal" data-bs-dismiss="modal" title="Edit Head Name">
                                                                <i class="fas fa-edit me-1"></i> Edit
                                                            </button>
                                                            
                                                            {{-- Delete Head Form (only if 0 accounts) --}}
                                                            @if ($h->accounts_count == 0)
                                                                <form action="{{ route('account-heads.delete', $h->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Category/Head?');" style="display: inline-block; margin:0;">
                                                                    @csrf
                                                                    <button type="submit" class="btn-action-pill btn-pill-status status-active" title="Delete Unused Head">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="text-muted small" title="Cannot delete: Accounts are linked to this head" style="font-size: 0.72rem;">In Use</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">No Categories / Heads found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer px-4 py-3" style="background:#f8fafc; border-top: 1px solid #e2e8f0;">
                                <button type="button" class="btn btn-erp-pill-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:34px; font-size:0.78rem;">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- EDIT HEAD MODALS --}}
                @foreach ($heads as $h)
                    <div class="modal fade" id="editHeadModal{{ $h->id }}" tabindex="-1" role="dialog" aria-labelledby="editHeadLabel{{ $h->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" action="{{ route('account-heads.update', $h->id) }}" method="POST">
                                @csrf
                                <div class="modal-header d-flex align-items-center justify-content-between px-4 py-3" style="background:#eff6ff; border-bottom: 1.5px solid #bfdbfe;">
                                    <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="editHeadLabel{{ $h->id }}" style="font-size:0.92rem;">
                                        <i class="fas fa-edit text-primary"></i> <span>Edit Category / Head</span>
                                    </h6>
                                    <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4 text-start">
                                    <div class="form-group mb-0">
                                        <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.03em;">Category / Head Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $h->name }}" required style="height: 38px;">
                                    </div>
                                </div>
                                <div class="modal-footer px-4 py-3" style="background:#f8fafc; border-top: 1px solid #e2e8f0;">
                                    <button type="button" class="btn btn-erp-pill-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:34px; font-size:0.78rem;">Cancel</button>
                                    <button type="submit" class="btn btn-save-complete" style="height:34px; font-size:0.80rem; padding: 4px 20px !important;">
                                        <i class="fas fa-check-circle me-1"></i> Update Category
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().destroy();
            }
            $('.datanew').DataTable({
                "pageLength": 10,
                "aaSorting": [],
                "columnDefs": [
                    { "orderable": false, "targets": [0, 7] }
                ],
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search accounts...",
                    "lengthMenu": "Show _MENU_ accounts",
                    "info": "Showing _START_ to _END_ of _TOTAL_ accounts",
                    "paginate": {
                        "first": '<i class="fas fa-angle-double-left"></i>',
                        "previous": '<i class="fas fa-chevron-left"></i>',
                        "next": '<i class="fas fa-chevron-right"></i>',
                        "last": '<i class="fas fa-angle-double-right"></i>'
                    }
                }
            });
        });
    </script>
@endsection
