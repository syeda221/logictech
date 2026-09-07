@extends('admin_panel.layout.app')

@section('content')
    <!-- Loader Overlay -->
    <div id="pageLoader"
        class="{{ isset($sale) ? '' : 'd-none' }} position-fixed top-0 start-0 w-100 h-100 d-flex flex-column gap-3 justify-content-center align-items-center"
        style="background: rgba(255,255,255,0.9); z-index: 1055;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="fw-bold text-primary fs-5">Loading Sale Data...</div>
    </div>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/flatpickr.min.css') }}" rel="stylesheet" />
    <style>
        /* Compact Flatpickr Calendar Styling */
        .flatpickr-calendar {
            width: 246px !important;
            font-size: 11px !important;
            border: 1.5px solid #bfdbfe !important;
            border-radius: 10px !important;
            box-shadow: 0 8px 24px -4px rgba(37, 99, 235, 0.18) !important;
            font-family: inherit !important;
            background: #ffffff !important;
            padding: 0 !important;
        }
        .flatpickr-calendar .flatpickr-months {
            height: 28px !important;
            background: #2563eb !important;
            border-radius: 8px 8px 0 0 !important;
            align-items: center !important;
        }
        .flatpickr-calendar .flatpickr-month {
            height: 28px !important;
            color: #ffffff !important;
            fill: #ffffff !important;
        }
        .flatpickr-current-month {
            font-size: 11px !important;
            padding: 2px 0 0 0 !important;
            height: 28px !important;
        }
        .flatpickr-current-month .numInputWrapper {
            width: 46px !important;
        }
        .flatpickr-current-month input.cur-year {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            padding: 0 2px !important;
            background: #2563eb !important;
        }
        .flatpickr-months .flatpickr-prev-month, 
        .flatpickr-months .flatpickr-next-month {
            height: 28px !important;
            padding: 4px 8px !important;
            color: #ffffff !important;
            fill: #ffffff !important;
        }
        .flatpickr-months .flatpickr-prev-month svg, 
        .flatpickr-months .flatpickr-next-month svg {
            width: 10px !important;
            height: 10px !important;
            fill: #ffffff !important;
        }
        .flatpickr-weekdays {
            height: 20px !important;
            background: #eff6ff !important;
            border-bottom: 1px solid #dbeafe !important;
        }
        span.flatpickr-weekday {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            color: #1e40af !important;
            line-height: 20px !important;
        }
        .flatpickr-days {
            width: 240px !important;
            padding: 2px !important;
        }
        .dayContainer {
            width: 240px !important;
            min-width: 240px !important;
            max-width: 240px !important;
            padding: 1px !important;
        }
        .flatpickr-day {
            max-width: 31px !important;
            height: 26px !important;
            line-height: 26px !important;
            font-size: 10.5px !important;
            margin: 1px !important;
            border-radius: 5px !important;
            font-weight: 500 !important;
        }
        .flatpickr-day.selected, 
        .flatpickr-day.startRange, 
        .flatpickr-day.endRange {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        .flatpickr-day:hover {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1e40af !important;
        }
        .flatpickr-day.today {
            border-color: #2563eb !important;
            font-weight: 700 !important;
        }

        /* Pill Date Wrapper */
        .erp-pill-date-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .erp-pill-date-wrapper input {
            padding-right: 28px !important;
            cursor: pointer !important;
        }
        .erp-pill-date-wrapper .date-icon {
            position: absolute;
            right: 10px;
            pointer-events: none;
            font-size: 0.75rem;
            z-index: 5;
        }

        /* ================= ENTERPRISE ERP DESIGN SYSTEM ================= */
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

        .erp-page-header {
            margin-bottom: 0.85rem;
        }
        .erp-title {
            font-weight: 700;
            font-size: 1.1rem;
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

        /* Top Panel Card */
        .card-panel {
            background-color: #ffffff !important;
            border: 1.5px solid #dbeafe !important;
            border-radius: 10px !important;
            padding: 0.75rem 0.85rem !important;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.03) !important;
        }

        .section-title {
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.75rem !important;
            letter-spacing: 0.04em !important;
            color: #1e40af !important;
            margin-bottom: 0 !important;
            border-left: 3px solid #2563eb !important;
            padding-left: 8px !important;
            display: flex;
            align-items: center;
        }

        /* Form Labels & Controls */
        .form-label {
            font-size: 0.68rem !important;
            font-weight: 700 !important;
            color: #1e40af !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            margin-bottom: 0.25rem !important;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-control,
        .form-select {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 50px !important;
            padding: 0.25rem 0.75rem !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
            height: 34px !important;
            font-size: 0.78rem !important;
        }

        .form-control:hover,
        .form-select:hover {
            border-color: #60a5fa !important;
            background-color: #f8fbff !important;
        }

        .form-control:focus,
        .form-select:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            background-color: #ffffff !important;
            outline: none !important;
        }

        .input-readonly,
        input[readonly].input-readonly {
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1e40af !important;
            font-weight: 700 !important;
            cursor: not-allowed !important;
        }

        /* Pill Group for Invoice Prefix & Number */
        .erp-pill-group {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 50px !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
            background: #ffffff !important;
            height: 34px !important;
            transition: all 0.15s ease-in-out !important;
        }
        .erp-pill-group:hover {
            border-color: #60a5fa !important;
        }
        .erp-pill-group:focus-within {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .erp-pill-group .input-inv {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background-color: #eff6ff !important;
            font-family: monospace !important;
            font-weight: 700 !important;
            color: #1e40af !important;
            text-align: center !important;
            height: 100% !important;
            padding: 0 10px !important;
            font-size: 0.80rem !important;
            flex-grow: 1 !important;
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
            height: 32px !important;
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
            color: #ffffff !important;
        }

        .btn-erp-pill-outline {
            background-color: #ffffff !important;
            border: 1.5px solid #bfdbfe !important;
            color: #1e40af !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            border-radius: 50px !important;
            height: 32px !important;
            padding: 0 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
            cursor: pointer;
        }
        .btn-erp-pill-outline:hover {
            background-color: #eff6ff !important;
            border-color: #3b82f6 !important;
            color: #1d4ed8 !important;
        }

        /* Table & Grid System */
        .table-responsive {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 8px !important;
            overflow-x: auto !important;
            overflow-y: visible !important;
            box-shadow: none !important;
            background-color: #ffffff;
        }

        .sales-table {
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
            width: 100%;
        }

        .sales-table thead th {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.70rem !important;
            letter-spacing: 0.04em;
            padding: 0.45rem 0.35rem !important;
            border: 1px solid #bfdbfe !important;
            border-bottom: 2px solid #60a5fa !important;
            vertical-align: middle !important;
            text-align: center;
            white-space: nowrap;
        }

        .sales-table tbody td {
            border: 1px solid #dbeafe !important;
            padding: 0 !important;
            background-color: #ffffff;
            vertical-align: middle !important;
        }

        .sales-table tbody tr:hover td {
            background-color: #f0f7ff !important;
        }

        /* Table Grid Inputs */
        .sales-table tbody .form-control,
        .sales-table tbody .form-select {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            height: 28px !important;
            margin: 0 !important;
            padding: 1px 4px !important;
            width: 100% !important;
            background-color: transparent !important;
            text-align: center;
            color: #1e293b !important;
            font-weight: 500 !important;
            font-size: 0.76rem !important;
        }

        .sales-table tbody .form-control:focus,
        .sales-table tbody .form-select:focus {
            outline: none !important;
            background-color: #eff6ff !important;
            box-shadow: inset 0 0 0 1.5px #2563eb !important;
        }

        .sales-table tbody .input-readonly,
        .sales-table tbody input[readonly],
        .sales-table tbody select[disabled] {
            background-color: #f8fafc !important;
            cursor: not-allowed !important;
            color: #475569 !important;
            font-weight: 600 !important;
        }

        /* Select2 Specific flat borderless styling */
        .sales-table tbody .select2-container--default .select2-selection--single {
            height: 28px !important;
            padding: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background-color: transparent !important;
            display: flex;
            align-items: center;
        }

        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding-left: 6px !important;
            padding-right: 16px !important;
            font-size: 0.76rem !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            text-align: left !important;
        }

        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            right: 4px !important;
        }

        .sales-table tbody .select2-container--default.select2-container--focus .select2-selection--single {
            background-color: #eff6ff !important;
            box-shadow: inset 0 0 0 1.5px #2563eb !important;
        }

        /* Discount Input Wrapper */
        .sales-table tbody .discount-wrapper {
            display: flex !important;
            align-items: stretch !important;
            width: 100% !important;
            height: 28px !important;
            gap: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .sales-table tbody .discount-wrapper .discount-value {
            flex-grow: 1 !important;
            border: none !important;
            border-radius: 0 !important;
            height: 100% !important;
            text-align: center;
            background-color: transparent !important;
            padding: 1px 3px !important;
        }
        .sales-table tbody .discount-wrapper .discount-toggle {
            border: none !important;
            border-radius: 0 !important;
            background-color: #eff6ff !important;
            color: #1e40af !important;
            font-weight: 700 !important;
            font-size: 0.70rem !important;
            width: 24px !important;
            min-width: 24px !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            cursor: pointer !important;
            border-left: 1px solid #bfdbfe !important;
        }
        .sales-table tbody .discount-wrapper .discount-toggle:hover {
            background-color: #dbeafe !important;
            color: #1d4ed8 !important;
        }

        .sales-table tfoot td {
            background-color: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            border-top: 2px solid #60a5fa !important;
            padding: 4px 8px !important;
            font-weight: 700 !important;
            color: #1e40af !important;
            font-size: 0.80rem !important;
        }

        /* Summary & Side Panels */
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px dashed #dbeafe;
            font-size: 0.80rem;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-val-net {
            font-weight: 800;
            color: #2563eb;
            font-size: 1.1rem;
            font-family: monospace;
        }
        .summary-val-change {
            background: #fef2f2;
            color: #dc2626;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #fecaca;
            font-size: 0.95rem;
            font-family: monospace;
        }

        .bottom-summary-strip {
            background: #ffffff;
            border: 1.5px solid #dbeafe;
            border-radius: 10px;
            padding: 10px 16px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
        }

        .btn-save-complete {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 8px 20px !important;
            font-size: 0.88rem !important;
            border: none !important;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.25) !important;
            transition: all 0.2s ease !important;
            cursor: pointer;
        }
        .btn-save-complete:hover {
            background: #059669 !important;
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(16, 185, 129, 0.35) !important;
            color: #ffffff !important;
        }

        /* Customer input select2 alignment - Pill Style */
        #customerInputWrapper .select2-container--default .select2-selection--single {
            height: 34px !important;
            min-height: 34px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            border: 1.5px solid #bfdbfe !important;
            border-radius: 50px !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single:hover {
            border-color: #60a5fa !important;
            background-color: #f8fbff !important;
        }
        #customerInputWrapper .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            background-color: #ffffff !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            padding-left: 14px !important;
            padding-right: 24px !important;
            font-size: 0.78rem !important;
            color: #1e293b !important;
            font-weight: 500 !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px !important;
            top: 0 !important;
            right: 8px !important;
        }
    </style>

    <div class="container-fluid py-2 px-2">
        <div class="main-container bg-white mx-auto">
            <div id="alertBox" class="alert d-none mb-2" role="alert" style="padding:6px 12px; font-size:0.78rem;"></div>
            <form id="saleForm" autocomplete="off">
                @csrf
                <input type="hidden" name="booking_id" id="booking_id" value="{{ $sale->id ?? '' }}">
                <input type="hidden" name="action" id="action" value="booking">

                {{-- TOP HEADER BAR --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('sale.index') }}" class="btn btn-erp-pill-outline" title="Back to Sales">
                            <i class="fas fa-arrow-left"></i> <span>Back</span>
                        </a>
                        <div>
                            <h4 class="erp-title">
                                <i class="fas fa-edit text-primary"></i> <span id="pageMainHeading">Edit Order / Booking #{{ $sale->invoice_no }}</span>
                            </h4>
                            <p class="erp-subtitle">Executive order generation, instant booking ledger, and multi-format dispatch invoicing</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" id="entryDateTime" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size:0.75rem; padding: 6px 12px; border-radius: 50px;">Date: {{ $sale->created_at->format('d/m/Y H:i') }}</span>
                        <button type="button" class="btn btn-erp-pill-outline" id="btnHeaderPosted" disabled style="height: 32px; font-size: 0.75rem;">Sale</button>
                    </div>
                </div>

                <!-- TOP INFORMATION PANEL (PILL TOOLBAR LAYOUT) -->
                <div class="card-panel mb-2">
                    <div class="d-flex flex-wrap align-items-end gap-2 w-100">
                        <!-- Customer & Walk-in Toggle (WIDE & PROMINENT) -->
                        <div style="flex: 2 1 280px; min-width: 230px;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0"><i class="fas fa-user text-primary me-1"></i>Customer</label>
                                <div class="form-check form-switch mb-0 d-flex align-items-center p-0">
                                    <input class="form-check-input ms-0 me-1" type="checkbox" role="switch" id="walkinToggle" name="is_walkin" value="1" {{ !isset($sale) || $sale->walkin_name ? 'checked' : '' }} style="cursor: pointer; width: 28px; height: 14px;">
                                    <label class="form-check-label fw-bold" for="walkinToggle" style="color: #2563eb; font-size: 0.70rem; cursor: pointer;">Walk-in</label>
                                </div>
                            </div>
                            <div id="customerInputWrapper">
                                <input type="text" class="form-control fw-bold {{ (!isset($sale) || $sale->walkin_name) ? '' : 'd-none' }}" name="walkin_name" id="walkinNameInput" value="{{ $sale->walkin_name ?? 'Walk-in Customer' }}" placeholder="Enter Customer Name...">
                                <select class="form-select {{ (!isset($sale) || $sale->walkin_name) ? 'd-none' : '' }}" id="customerSelect" name="customer" style="width:100%">
                                    @if (isset($sale) && $sale->customer_relation)
                                        <option value="{{ $sale->customer_id }}" selected>
                                            {{ $sale->customer_relation->customer_id }} — {{ $sale->customer_relation->customer_name }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="Invoice_no" value="{{ $nextInvoiceNumber ?? ($sale->invoice_no ?? '') }}">
                        
                        <!-- Credit Days (Compact) -->
                        <div style="width: 80px; min-width: 75px; flex-shrink: 0;">
                            <label class="form-label">Cr. Days</label>
                            <input type="number" class="form-control text-center" name="credit_days" placeholder="0"
                                min="0" value="{{ $sale->credit_days ?? '' }}">
                        </div>
                        
                        <!-- Order Date (Auto-Fetched: Compact with Calendar Icon) -->
                        <div style="width: 140px; min-width: 135px; flex-shrink: 0;">
                            <label class="form-label"><i class="fas fa-calendar-alt text-primary me-1"></i>Order Date</label>
                            <div class="erp-pill-date-wrapper">
                                <input type="text" name="sale_date" class="form-control datepicker-custom text-center fw-bold bg-white" id="displayDateInput" value="{{ isset($sale) ? $sale->created_at->format('Y-m-d') : date('Y-m-d') }}">
                                <i class="fas fa-calendar-alt text-primary date-icon"></i>
                            </div>
                        </div>

                        <!-- Estimated Delivery Date (Auto-Fetched: Compact with Calendar Icon) -->
                        <div style="width: 140px; min-width: 135px; flex-shrink: 0;">
                            <label class="form-label"><i class="fas fa-clock text-warning me-1"></i>Est. Deliv</label>
                            <div class="erp-pill-date-wrapper">
                                <input type="text" name="estimated_delivery_date" class="form-control datepicker-custom text-center fw-bold bg-white" id="estimatedDeliveryDateInput" value="{{ isset($sale->estimated_delivery_date) ? \Carbon\Carbon::parse($sale->estimated_delivery_date)->format('Y-m-d') : '' }}" placeholder="YYYY-MM-DD">
                                <i class="fas fa-calendar-day text-warning date-icon"></i>
                            </div>
                        </div>

                        <!-- Hidden fields for status & delivery date -->
                        <input type="hidden" name="order_status" id="orderStatusSelect" value="{{ $sale->order_status ?? 'pending' }}">
                        <input type="hidden" name="delivery_date" id="actualDeliveryDateInput" value="{{ isset($sale->delivery_date) ? \Carbon\Carbon::parse($sale->delivery_date)->format('Y-m-d') : '' }}">

                        <!-- Remarks (User Choice: WIDE & PROMINENT) -->
                        <div style="flex: 1.5 1 180px; min-width: 150px;">
                            <label class="form-label"><i class="fas fa-comment-dots text-primary me-1"></i>Remarks</label>
                            <input type="text" class="form-control" name="reference" id="remarks" value="{{ $sale->reference ?? '' }}" placeholder="e.g. Self Collected">
                        </div>

                        <!-- Quick Save / Book Order Button (Hidden) -->
                        <div class="d-none">
                            <button type="button" class="btn btn-erp-pill-primary fw-bold" id="btnHeaderSaveSale" style="height: 34px; padding: 0 16px;">
                                <i class="fas fa-bookmark me-1"></i>Book Order
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Hidden fields for backend --}}
                <input type="hidden" id="address" name="address" value="{{ optional($sale->customer_relation)->address }}">
                <input type="hidden" id="tel" name="tel" value="{{ optional($sale->customer_relation)->mobile }}">
                <input type="hidden" id="previousBalance" value="{{ optional($sale->customer_relation)->previous_balance ?? 0 }}">
                <input type="hidden" id="rangeBalance" value="{{ optional($sale->customer_relation)->balance_range ?? 0 }}">

                <!-- 2-COLUMN RESPONSIVE POS LAYOUT -->
                <div class="row g-2 align-items-stretch">
                    <!-- LEFT MAIN AREA: Items Grid Table (col-lg-8 col-xl-9) -->
                    <div class="col-lg-8 col-xl-9">
                        <div class="card-panel d-flex flex-column h-100 p-2 bg-white" style="border-radius:10px;">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="section-title mb-0" style="font-size:0.78rem;">Order Items (<span id="itemsRowCount">{{ isset($sale->items) ? count($sale->items) : 0 }}</span>)</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-erp-pill-primary py-1 px-3 fw-bold" id="btnAdd" style="height: 30px; font-size:0.75rem;">
                                        <i class="fas fa-plus me-1"></i>Add Row
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive flex-grow-1" style="overflow-x: auto; overflow-y: visible;">
                                <table class="table table-bordered sales-table mb-0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width:25px;" class="text-center">#</th>
                                            <th class="col-product" style="min-width: 160px;">PRODUCT / SPECIFICATIONS</th>
                                            <th class="col-model" style="width: 95px;">MODEL</th>
                                            <th class="col-serial" style="width: 85px;">SERIAL NO</th>
                                            <th class="col-stock" style="width: 55px;">STOCK</th>
                                            <th class="col-qty" style="width: 85px;">QTY</th>
                                            <th class="col-pieces" style="width: 50px;">UNIT</th>
                                            <th class="col-price-p" style="width: 85px;">PRICE</th>
                                            <th class="col-disc" style="width: 75px;">DISCOUNT</th>
                                            <th class="col-amount" style="width: 90px;">AMOUNT</th>
                                            <th class="col-action" style="width: 30px;">×</th>
                                        </tr>
                                    </thead>
                                    <tbody id="salesTableBody">
                                        @if (isset($sale) && $sale->items && count($sale->items) > 0)
                                            @foreach ($sale->items as $item)
                                                @php
                                                    $prod = $item->product;
                                                    $sizeMode = $item->size_mode ?? ($prod->size_mode ?? 'std');
                                                    $ppb = 1;
                                                    if ($item->pieces_per_box > 0) {
                                                        $ppb = $item->pieces_per_box;
                                                    } elseif ($prod && $prod->pieces_per_box > 0) {
                                                        $ppb = $prod->pieces_per_box;
                                                    }

                                                    $selStockDisp = '';
                                                    if ($prod && $prod->warehouseStocks) {
                                                        $selWs = $prod->warehouseStocks->where('warehouse_id', $item->warehouse_id)->first();
                                                        if ($selWs) {
                                                            $selStockDisp = (float) $selWs->total_pieces;
                                                        }
                                                    }

                                                    $variantLabel = '';
                                                    $vSize = '-';
                                                    $vCol = '-';
                                                    if ($item->color) {
                                                        try {
                                                            $vData = json_decode(base64_decode($item->color), true);
                                                            if ($vData && isset($vData['name'])) {
                                                                $vSize = (isset($vData['size']) && $vData['size'] !== '-') ? $vData['size'] : '-';
                                                                $vCol  = (isset($vData['color']) && $vData['color'] !== '-') ? $vData['color'] : '-';
                                                                $sStr = $vSize !== '-' ? " {$vSize}" : '';
                                                                $cStr = $vCol !== '-' ? " ({$vCol})" : '';
                                                                $variantLabel = ' — ' . $vData['name'] . $sStr . $cStr;
                                                            }
                                                        } catch (\Exception $e) {}
                                                    }
                                                @endphp
                                                <tr data-size_mode="{{ $sizeMode }}" data-pieces_per_box="{{ $ppb }}">
                                                    <!-- # ROW INDEX -->
                                                    <td class="text-center fw-bold text-muted row-index" style="vertical-align:middle; font-size:0.75rem;">{{ $loop->iteration }}</td>

                                                    <!-- PRODUCT / SPECIFICATIONS -->
                                                    <td class="col-product">
                                                        <select class="form-select product" style="width:100%">
                                                            @if ($prod)
                                                                <option value="{{ $item->product_id }}" selected>
                                                                    {{ $prod->item_name }}{{ $variantLabel }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                        <input type="hidden" class="product-id-hidden" name="product_id[]" value="{{ $item->product_id }}">
                                                        <input type="hidden" class="variant-data-hidden" name="color[]" value="{{ $item->color ?? '' }}">
                                                        <input type="hidden" class="item-code-display" value="{{ $prod->item_code ?? '' }}">
                                                        <input type="hidden" class="size-h" value="{{ $prod->height ?? '-' }}">
                                                        <input type="hidden" class="size-w" value="{{ $prod->width ?? '-' }}">
                                                        <input type="hidden" class="size-mode-text" value="{{ $sizeMode }}">
                                                    </td>

                                                    <!-- MODEL -->
                                                    <td class="col-model">
                                                        <input type="text" class="form-control model-input text-center fw-semibold" name="model[]" value="{{ $item->model ?? ($prod->model ?? '') }}" placeholder="e.g. LTZ-35KW">
                                                    </td>

                                                    <!-- PRODUCT SERIAL NO -->
                                                    <td class="col-serial">
                                                        <input type="text" class="form-control serial-no-input text-center font-monospace" name="serial_no[]" value="{{ $item->serial_no ?? '' }}" placeholder="e.g. 10001">
                                                    </td>

                                                    <!-- STOCK -->
                                                    <td class="col-stock">
                                                        <input type="text" class="form-control stock text-center input-readonly" readonly value="{{ $selStockDisp }}" tabindex="-1">
                                                        <input type="hidden" class="warehouse" name="warehouse_id[]" value="{{ $item->warehouse_id ?? (auth()->user()->warehouse_id ?? 1) }}">
                                                        <input type="hidden" class="variant-stock-value">
                                                    </td>

                                                    <!-- QTY -->
                                                    <td style="width:85px;min-width:85px;" class="col-qty-wrapper">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <input type="number" step="any" class="form-control carton-qty text-center fw-bold" name="carton_qty[]" value="{{ $item->total_pieces > 0 ? $item->total_pieces : ($item->qty ?? 1) }}" min="0" style="flex: 1; min-width: 0; height: 26px; font-size: 0.85rem; padding: 1px 4px;">
                                                            <button type="button" class="btn btn-sm btn-outline-primary qty-unit-toggle px-1 py-0 d-none" 
                                                                    data-unit-mode="main" title="Toggle Unit" style="font-size: 0.65rem; height: 26px; min-width: 28px; font-weight: 700; border-radius: 4px; flex-shrink: 0;">
                                                                Kg
                                                            </button>
                                                        </div>
                                                        <input type="hidden" class="hidden-sub-unit-mode" name="sub_unit_mode[]" value="main">
                                                    </td>

                                                    <!-- Loose Pieces (hidden) -->
                                                    <td style="width:70px;min-width:70px;" class="d-none">
                                                        <input type="number" class="form-control loose-pcs-input text-end" name="loose_qty[]" value="0" min="0">
                                                    </td>

                                                    <!-- UNIT (Display - readonly) -->
                                                    <td class="col-pieces">
                                                        <input type="text" class="form-control unit-display text-center input-readonly" readonly tabindex="-1" placeholder="Pcs" value="Pcs">
                                                        <input type="hidden" class="total-pieces" name="total_pieces[]" value="{{ $item->total_pieces ?? 1 }}">
                                                        <input type="hidden" class="sales-qty" name="qty[]" value="{{ $item->total_pieces ?? 1 }}">
                                                        <input type="hidden" class="pack-qty" name="pack_qty[]" value="{{ $ppb }}">
                                                    </td>
                                                 
                                                    <!-- PRICE (EDITABLE) -->
                                                    <td class="col-price-p">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <input type="text" class="form-control visible-price text-end fw-bold" name="visible_price[]" value="{{ $item->price }}" placeholder="0" style="flex: 1; min-width: 0;">
                                                            <button type="button" class="btn btn-sm btn-outline-primary price-mode-row-toggle px-1 py-0" 
                                                                    data-mode="retail" title="Retail Mode" style="font-size: 0.65rem; height: 24px; min-width: 20px; font-weight: bold;">
                                                                R
                                                            </button>
                                                        </div>
                                                        <input type="hidden" class="price-per-piece" name="price_per_piece[]" value="{{ $item->price }}"><input type="hidden" class="retail-price" value="{{ $prod->retail_price ?? $item->price }}"><input type="hidden" class="wholesale-price" value="{{ $prod->wholesale_price ?? $item->price }}"><input type="hidden" class="weight-per-piece" value="{{ $prod->weight_per_piece ?? 0 }}">
                                                    </td>

                                                    <!-- DISCOUNT -->
                                                    <td class="col-disc">
                                                        <div class="discount-wrapper">
                                                            <input type="number"
                                                                   class="form-control discount-value text-end"
                                                                   name="item_disc[]"
                                                                   value="{{ $item->discount_percent ?? 0 }}"
                                                                   placeholder="0"
                                                                   min="0"
                                                                   max="100"
                                                                   title="Max 100% in % mode, or Total Amount in PKR mode">
                                                            <input type="hidden" class="discount-type-hidden" name="discount_type[]" value="percent">
                                                            <button type="button"
                                                                   class="btn discount-toggle"
                                                                   data-type="percent" tabindex="-1" title="Toggle % / PKR">%</button>
                                                        </div>
                                                        <input type="hidden" class="discount-amount" value="{{ $item->discount_amount ?? 0 }}">
                                                    </td>

                                                    <!-- AMOUNT -->
                                                    <td class="col-amount">
                                                        <input type="text" class="form-control sales-amount text-end input-readonly fw-bold text-dark font-monospace" name="total[]" value="{{ $item->total ?? 0 }}" readonly tabindex="-1">
                                                        <input type="hidden" class="gross-amount" value="{{ ($item->total ?? 0) + ($item->discount_amount ?? 0) }}">
                                                    </td>

                                                    <!-- ACTION -->
                                                    <td class="col-action text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger del-row" tabindex="-1">&times;</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr id="gridAlertRow" class="d-none">
                                            <td colspan="11" class="p-1 border-0" style="background-color: transparent !important;">
                                                <div id="gridAlertBox" class="alert alert-warning d-flex align-items-center justify-content-between px-2 py-1 mb-0 rounded border border-warning shadow-sm" style="font-size: 0.78rem; font-weight: 600; color: #856404; background-color: #fff3cd;">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="fas fa-exclamation-triangle text-warning me-1 alert-icon" style="font-size: 0.85rem;"></i>
                                                        <span id="gridAlertText">Discount cannot exceed 100%!</span>
                                                    </div>
                                                    <button type="button" class="btn-close py-0" style="font-size: 0.65rem;" onclick="$('#gridAlertRow').addClass('d-none');" aria-label="Close"></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end fw-bold text-uppercase" style="font-size:0.78rem; color: #1e40af;">Invoice Total:</td>
                                            <td class="text-end fw-bold fs-6" style="color: #059669;"><span id="totalAmount">0.00</span></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT PANEL: Summary & Payment Methods (col-lg-4 col-xl-3) -->
                    <div class="col-lg-4 col-xl-3">
                        <div class="d-flex flex-column h-100 gap-2">
                            <!-- Executive Summary Card (Excel Orders Sheet Flow) -->
                            <div class="card-panel p-3 bg-white" style="border-radius:10px;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom" style="border-color: #dbeafe !important;">
                                    <span class="fw-bold text-dark" style="font-size:0.85rem;"><i class="fas fa-calculator text-primary me-1"></i>Summary</span>
                                    <span class="badge rounded-pill px-2 py-1" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size:0.7rem;">Live</span>
                                </div>
                                
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Invoice Total</span>
                                    <span class="fw-bold text-dark font-monospace" id="tGross">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Discount</span>
                                    <span class="fw-bold text-danger font-monospace" id="tLineDisc">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="fw-bold text-dark">Net Total</span>
                                    <span class="summary-val-net font-monospace" id="tSub">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Advance Payment</span>
                                    <span class="fw-bold font-monospace" style="color: #059669;" id="receiptsTotalBadge">0.00</span>
                                </div>
                                <div class="summary-row pt-1">
                                    <span class="fw-bold text-dark">Balance Amount</span>
                                    <span class="summary-val-change font-monospace" id="walkinChange">0.00</span>
                                </div>
                            </div>

                            <!-- Payment Methods Card -->
                            <div class="card-panel p-3 bg-white flex-grow-1 d-flex flex-column" style="border-radius:10px;">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom" style="border-color: #dbeafe !important;">
                                    <span class="fw-bold text-dark" style="font-size:0.82rem;"><i class="fas fa-wallet text-success me-1"></i>Advance / Payment</span>
                                    <button type="button" class="btn btn-erp-pill-outline py-0 px-2 fw-bold" id="btnAddRV" style="height: 26px; font-size:0.7rem;"><i class="fas fa-plus me-1"></i>Add Account</button>
                                </div>

                                <div id="rvWrapper" class="mb-2">
                                    @php
                                        $receiptVoucher = \App\Models\VoucherMaster::with('details')
                                            ->where('voucher_type', \App\Models\VoucherMaster::TYPE_RECEIPT)
                                            ->where('remarks', 'like', "%#{$sale->invoice_no}%")
                                            ->first();
                                        $receiptLines = collect();
                                        if ($receiptVoucher) {
                                            $receiptLines = $receiptVoucher->details->where('debit', '>', 0);
                                        } elseif (!empty($sale->payment_details)) {
                                            $parsedDetails = json_decode($sale->payment_details, true);
                                            if (is_array($parsedDetails)) {
                                                $receiptLines = collect($parsedDetails)->map(function($pd) {
                                                    return (object)[
                                                        'account_id' => $pd['account_id'],
                                                        'debit' => $pd['amount']
                                                    ];
                                                });
                                            }
                                        } elseif (($sale->cash ?? 0) > 0) {
                                            $defaultCashAcc = $accounts->first(function($a) {
                                                return str_contains(strtolower($a->title), 'cash');
                                            });
                                            $receiptLines = collect([
                                                (object)[
                                                    'account_id' => $defaultCashAcc ? $defaultCashAcc->id : null,
                                                    'debit' => $sale->cash
                                                ]
                                            ]);
                                        }
                                        $firstLine = $receiptLines->first();
                                        $otherLines = $receiptLines->skip(1);
                                    @endphp
                                    <div class="d-flex gap-1 align-items-center mb-2 rv-row">
                                        <select class="form-select form-select-sm rv-account fw-bold" name="receipt_account_id[]" style="font-size:0.75rem;">
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}" {{ $firstLine && $firstLine->account_id == $acc->id ? 'selected' : ($firstLine ? '' : (str_contains(strtolower($acc->title), 'cash') ? 'selected' : '')) }}>{{ $acc->title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-end rv-amount fw-bold" name="receipt_amount[]" value="{{ $firstLine ? number_format($firstLine->debit, 2, '.', '') : '' }}" placeholder="0.00" style="width: 110px; font-size:0.75rem;">
                                    </div>
                                    @foreach ($otherLines as $line)
                                        <div class="d-flex gap-1 align-items-center mb-2 rv-row">
                                            <select class="form-select form-select-sm rv-account fw-bold" name="receipt_account_id[]" style="font-size:0.75rem;">
                                                @foreach ($accounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ $line->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->title }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" step="0.01" class="form-control form-control-sm text-end rv-amount fw-bold" name="receipt_amount[]" value="{{ number_format($line->debit, 2, '.', '') }}" placeholder="0.00" style="width: 110px; font-size:0.75rem;">
                                            <button type="button" class="btn btn-outline-danger btn-sm btnRemRV px-2">&times;</button>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="btn btn-save-complete w-100 mt-auto py-2" id="btnSaveAndComplete">
                                    <i class="fas fa-bookmark me-2"></i>Book Order (F9)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM SUMMARY STRIP -->
                <div class="bottom-summary-strip">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Invoice Total:</span>
                        <span class="fs-6 fw-bold text-dark font-monospace" id="bottomInvoiceTotal">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Discount:</span>
                        <span class="fs-6 fw-bold text-danger font-monospace" id="bottomTotalDiscount">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Extra Disc:</span>
                        <div class="input-group input-group-sm" style="width: 120px;">
                            <input type="number" class="form-control text-end fw-bold text-danger font-monospace" id="walkinDiscountRs" value="{{ isset($sale) && $sale->is_walkin ? $sale->total_extradiscount : '0' }}" placeholder="0">
                            <span class="input-group-text bg-light text-muted px-1">Rs</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Net Total:</span>
                        <span class="fs-5 fw-bold text-primary font-monospace" id="walkinNetTotal">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Advance Paid:</span>
                        <span class="fs-6 fw-bold font-monospace" style="color: #059669;" id="bottomPaymentsTotal">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Balance:</span>
                        <span class="fs-6 fw-bold text-danger font-monospace" id="bottomChangeVal">0.00</span>
                    </div>

                    <button type="button" class="btn btn-save-complete" id="btnSaveAndComplete2">
                        <i class="fas fa-bookmark me-2"></i>Book Order (F9)
                    </button>
                </div>

                {{-- ACTION BUTTONS ROW (HIDDEN) --}}
                <div class="d-none">
                    <button type="button" class="btn btn-erp-pill-outline" id="btnSave"><i class="fas fa-bookmark me-1"></i>Booking</button>
                    <button type="button" class="btn btn-erp-pill-primary" id="btnPosted" disabled><i class="fas fa-shopping-cart me-1"></i>Sale</button>
                    <button type="button" class="btn btn-erp-pill-outline" id="btnPrint"><i class="fas fa-print me-1"></i>A4 Print</button>
                    <button type="button" class="btn btn-erp-pill-outline" id="btnEstimate"><i class="fas fa-file-invoice me-1"></i>Estimate</button>
                    <button type="button" class="btn btn-erp-pill-outline" id="btnPrint2"><i class="fas fa-receipt me-1"></i>Thermal Print</button>
                    <button type="button" class="btn btn-erp-pill-outline" id="btnDcThermal"><i class="fas fa-truck me-1"></i>DC</button>
                </div>

                {{-- Hidden elements required for calculations and controllers --}}
                <div class="d-none">
                    <span id="tQty">0</span>
                    <input type="number" name="discountPercent" id="discountPercent" value="0">
                    <span id="tOrderDisc">0.00</span>
                    <span id="tCurrentBill">0.00</span>
                    <span id="tPrev">{{ number_format(optional($sale->customer_relation)->previous_balance ?? 0, 2) }}</span>
                    <span id="tPayable">0.00</span>
                    <div id="receiptsTotal">0.00</div>
                    <div id="walkinReceiptsContainer"></div>
                    <input type="hidden" name="subTotal1" id="subTotal1" value="0">
                    <input type="hidden" name="total_subtotal" id="subTotal2" value="0">
                    <input type="hidden" name="total_extra_cost" id="discountAmount" value="0">
                    <input type="hidden" name="total_net" id="totalBalance" value="0">
                    <input type="hidden" name="cash" value="0">
                    <input type="hidden" name="card" value="0">
                    <input type="hidden" name="change" id="backendChange" value="0">
                </div>
            </form>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">
                        <i class="fas fa-user-plus text-primary me-2"></i>New Customer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ajaxAddCustomerForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Customer Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="customer_type" required>
                                    <option value="Main Customer">Main Customer</option>
                                    <option value="Walking Customer">Walking Customer</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="customer_name" required placeholder="Customer Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mobile</label>
                                <input type="text" class="form-control" name="mobile" placeholder="0300-1234567">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="opening_balance" value="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Address</label>
                                <input type="text" class="form-control" name="address" placeholder="Address">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveAjaxCustomer">Save Customer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('admin_panel.sale.scripts.shared_logic')

    <script>
        $(document).ready(function() {
            // Datepicker Initialization with visual calendar popup
            if (typeof flatpickr !== 'undefined') {
                flatpickr('#displayDateInput', {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "Y-m-d",
                    allowInput: true,
                    altInputClass: "form-control erp-pill-input text-center fw-bold bg-white cursor-pointer",
                    defaultDate: "{{ isset($sale) ? $sale->created_at->format('Y-m-d') : date('Y-m-d') }}"
                });
                flatpickr('#estimatedDeliveryDateInput', {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "Y-m-d",
                    allowInput: true,
                    altInputClass: "form-control erp-pill-input text-center fw-bold bg-white cursor-pointer",
                    defaultDate: "{{ isset($sale->estimated_delivery_date) ? \Carbon\Carbon::parse($sale->estimated_delivery_date)->format('Y-m-d') : '' }}"
                });
            }

            // ============================================================
            // CUSTOMER SELECT2 AJAX SEARCH (Name or Code)
            // ============================================================
            function getPartyType() {
                return $('input[name="partyType"]:checked').val() || 'Main Customer';
            }

            $('#customerSelect').select2({
                placeholder: 'Search by Name or Code...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('salecustomers.index') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            type: getPartyType(),
                            search: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(c) {
                                return {
                                    id: c.id,
                                    text: (c.customer_id || '') + ' — ' + c.customer_name,
                                    customer: c
                                };
                            })
                        };
                    },
                    cache: false
                },
                templateResult: function(item) {
                    if (item.loading) return item.text;
                    if (!item.customer) return item.text;
                    const c = item.customer;
                    return $(`<div>
                        <strong>${c.customer_name}</strong>
                        <small class="text-muted ms-2">${c.customer_id || ''}</small>
                        ${c.mobile ? '<br><small class="text-muted">' + c.mobile + '</small>' : ''}
                    </div>`);
                },
                templateSelection: function(item) {
                    if (!item.customer) return item.text;
                    return item.customer.customer_id + ' — ' + item.customer.customer_name;
                }
            });

            // Set initial visibility state of Customer Select / Walk-in input
            $('#walkinToggle').trigger('change');

            // Party type change → reset customer
            $(document).on('change', 'input[name="partyType"]', function() {
                $('#customerSelect').val(null).trigger('change');
                clearCustomerInfo();
            });

            // Customer selected → load details
            $('#customerSelect').on('select2:select', function(e) {
                const id = e.params.data.id;
                if (!id) return;

                $.get("{{ url('sale/customers') }}/" + id + "?t=" + new Date().getTime(), function(d) {
                    $('#address').val(d.address || '');
                    $('#tel').val(d.mobile || '');
                    $('#remarks').val(d.status || '');
                    const prev = parseFloat(d.previous_balance || 0);
                    const range = parseFloat(d.balance_range || 0);
                    $('#previousBalance').val(prev.toFixed(2));
                    $('#rangeBalance').val(range.toFixed(2));

                    if (typeof updateGrandTotals === 'function') updateGrandTotals();
                }).fail(function() {
                    showAlert('error', 'Failed to load customer details');
                });
            });

            // Customer cleared
            $('#customerSelect').on('select2:clear', function() {
                clearCustomerInfo();
                if (typeof updateGrandTotals === 'function') updateGrandTotals();
            });

            function clearCustomerInfo() {
                $('#address, #tel, #remarks').val('');
                $('#previousBalance, #rangeBalance').val('0');
            }

            $('#clearCustomerData').on('click', function() {
                $('#customerSelect').val(null).trigger('change');
                clearCustomerInfo();
                if (typeof updateGrandTotals === 'function') updateGrandTotals();
            });

            // Edit Sale Specific Handlers
            $('#btnPrint').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/invoice', '_blank'));
            });
            $('#btnEstimate').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/invoice?type=estimate', '_blank'));
            });
            $('#btnPrint2').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/recepit', '_blank'));
            });
            $('#btnDcThermal').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/dc-thermal', '_blank'));
            });
        });
    </script>
    @if (isset($sale))
        <script>
            $(document).ready(function() {
                // --- PRE-FILL EDIT MODE (Server Side Rendered) ---
                console.log("Loading Edit Mode for Sale #{{ $sale->id }}");
                $('#booking_id').val("{{ $sale->id }}");
                $('#entryDateTime').text("Date: {{ $sale->created_at->format('d/m/Y H:i') }}");

                // Initialize Select2 on server-rendered rows
                $('.product').each(function() {
                    if (typeof initProductSelect2 === 'function') {
                        initProductSelect2($(this));
                    }
                });

                // Recalculate totals based on rendered values
                $('#salesTableBody tr').each(function() {
                    if (typeof computeRow === 'function') {
                        computeRow($(this));
                    }
                });

                // Recompute Receipts and then updateGrandTotals
                if (typeof window.recomputeReceipts === 'function') {
                    window.recomputeReceipts();
                } else {
                    updateGrandTotals();
                }

                if (typeof refreshPostedState === 'function') {
                    refreshPostedState();
                }

                setTimeout(() => {
                    $('#pageLoader').addClass('d-none');
                }, 300);
            });
        </script>
    @endif
@endsection
