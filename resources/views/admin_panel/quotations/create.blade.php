@extends('admin_panel.layout.app')

@section('content')
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

        /* Design System */
        :root {
            --erp-primary: #2563eb;
            --erp-border-blue: #bfdbfe;
            --erp-border-subtle: #dbeafe;
        }
        .main-container {
            max-width: 100%;
            margin: 0 auto;
        }
        .erp-page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1.5px solid #dbeafe;
            border-radius: 12px;
            padding: 10px 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
        }
        .erp-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .erp-subtitle {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }
        .erp-icon-badge {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border: 1px solid #bfdbfe;
        }
        .card-panel {
            background: #f8fafc;
            border: 1.5px solid #dbeafe;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.03);
        }
        .form-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #3730a3;
            margin-bottom: 4px;
        }
        .erp-pill-group {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid #bfdbfe;
            border-radius: 50px;
            overflow: hidden;
            background: #ffffff;
        }
        .erp-pill-group .btn-prefix {
            border: none;
            background: #eff6ff;
            color: #1e40af;
            border-right: 1px solid #bfdbfe;
            border-radius: 0;
        }
        .erp-pill-group .input-inv {
            border: none;
            background: transparent;
            outline: none;
        }
        .erp-pill-group .btn-refresh {
            border: none;
            background: #eff6ff;
            color: #2563eb;
            border-left: 1px solid #bfdbfe;
            border-radius: 0;
            cursor: pointer;
        }
        .btn-erp-pill-primary {
            background-color: #2563eb !important;
            border: 1px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            border-radius: 50px !important;
            height: 32px !important;
            padding: 0 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            cursor: pointer;
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
            gap: 5px !important;
            cursor: pointer;
        }
        .btn-erp-pill-outline:hover, .btn-erp-pill-outline.active {
            background-color: #eff6ff !important;
            border-color: #2563eb !important;
            color: #1d4ed8 !important;
        }

        /* Walk-in Button Highlight (matching Image 2) */
        .btn-walkin-toggle {
            background-color: #fef3c7 !important;
            border: 1.5px solid #f59e0b !important;
            color: #92400e !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            border-radius: 50px !important;
            height: 34px !important;
            padding: 0 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-walkin-toggle:hover, .btn-walkin-toggle.active {
            background-color: #fde68a !important;
            color: #78350f !important;
            border-color: #d97706 !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important;
        }

        /* Table & Grid System */
        .table-responsive {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 8px !important;
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
        .sales-table tbody .form-control,
        .sales-table tbody .form-select {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            height: 28px !important;
            padding: 1px 4px !important;
            width: 100% !important;
            background-color: transparent !important;
            text-align: center;
            font-size: 0.76rem !important;
            color: #1e293b !important;
        }
        .sales-table tbody .form-control:focus,
        .sales-table tbody .form-select:focus {
            outline: none !important;
            background-color: #eff6ff !important;
            box-shadow: inset 0 0 0 1.5px #2563eb !important;
        }
        .sales-table tbody .specs-subrow .form-control {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 6px !important;
            background-color: #ffffff !important;
            text-align: left !important;
            padding: 4px 10px !important;
            height: 32px !important;
            font-size: 0.78rem !important;
            color: #1e293b !important;
        }
        .sales-table tbody .specs-subrow .form-control:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .btn-toggle-specs {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 6px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-toggle-specs:hover {
            background: #2563eb;
            color: #fff;
        }

        /* Order Summary */
        .summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 0.80rem;
        }
        .summary-val-net {
            font-weight: 800;
            color: #2563eb;
            font-size: 1.15rem;
            font-family: monospace;
        }

        .btn-save-complete {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 8px 20px !important;
            font-size: 0.88rem !important;
            border: none !important;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3) !important;
            cursor: pointer;
            transition: all 0.2s ease !important;
        }
        .btn-save-complete:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(16, 185, 129, 0.45) !important;
            color: #ffffff !important;
        }

        /* Bottom Summary Strip */
        .bottom-summary-strip {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            border: 1px solid #334155 !important;
            border-radius: 12px !important;
            padding: 10px 18px !important;
            margin-top: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25) !important;
            color: #f8fafc !important;
        }
        .summary-strip-label {
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            color: #94a3b8 !important;
        }

        /* Customer Wrapper */
        #customerInputWrapper {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            width: 100% !important;
        }
        #customerInputWrapper .select2-container {
            width: 100% !important;
            flex-grow: 1 !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single {
            height: 34px !important;
            min-height: 34px !important;
            display: flex !important;
            align-items: center !important;
            border: 1.5px solid #bfdbfe !important;
            border-radius: 50px !important;
            background-color: #ffffff !important;
            padding: 0 !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            padding-left: 14px !important;
            padding-right: 24px !important;
            font-size: 0.78rem !important;
            font-weight: 500 !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px !important;
            right: 8px !important;
        }

        /* Select2 Product Dropdown in Sales Table */
        .sales-table tbody .select2-container {
            width: 100% !important;
        }
        .sales-table tbody .select2-container--default .select2-selection--single {
            height: 28px !important;
            min-height: 28px !important;
            padding: 0 4px !important;
            border: 1.5px solid #bfdbfe !important;
            border-radius: 6px !important;
            background-color: #ffffff !important;
            display: flex;
            align-items: center;
        }
        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding-left: 4px !important;
            padding-right: 20px !important;
            font-size: 0.78rem !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            text-align: left !important;
        }
        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            right: 4px !important;
        }
        .select2-dropdown {
            font-size: 0.78rem !important;
            border: 1.5px solid #93c5fd !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.22) !important;
        }

        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #2563eb;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.4); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
    </style>

    <div class="container-fluid py-2 px-2">
        <div class="main-container bg-white mx-auto">

            <div id="alertBox" class="alert d-none mb-2" role="alert" style="padding:6px 12px; font-size:0.78rem;"></div>

            <form id="quotationForm" autocomplete="off">
                @csrf

                {{-- TOP HEADER BAR (Matches Image 2) --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                    <div class="d-flex align-items-center gap-2.5">
                        <a href="{{ route('quotations.index') }}" class="btn btn-erp-pill-outline" title="Back to Quotations">
                            <i class="fas fa-arrow-left"></i> <span>Back</span>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <div class="erp-icon-badge">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <div>
                                <h4 class="erp-title mb-0">
                                    <span id="pageMainHeading">New Quotation</span>
                                </h4>
                                <p class="erp-subtitle">Executive quote generation, proposal pricing, and multi-format dispatch quotation</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Invoice Title -> QUOTATION Editable Field (Image 2) -->
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted fw-bold text-uppercase" style="font-size:0.72rem; letter-spacing:0.03em; white-space:nowrap;">Inv Title:</span>
                            <input type="text" 
                                   name="quote_title" 
                                   id="inputQuoteTitle" 
                                   list="quoteTitleSuggestions" 
                                   class="form-control form-control-sm fw-bold" 
                                   value="QUOTATION" 
                                   placeholder="QUOTATION" 
                                   style="height: 32px; font-size: 0.80rem; width: 145px; border-radius: 6px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #1e3a8a;">
                            <datalist id="quoteTitleSuggestions">
                                <option value="QUOTATION">
                                <option value="PRICE QUOTATION">
                                <option value="PROFORMA INVOICE">
                                <option value="ESTIMATE">
                                <option value="COMMERCIAL PROPOSAL">
                            </datalist>
                        </div>

                        <!-- Quote No. Pill Widget (Image 2: "Invoice Number ki jaga Quote Number") -->
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted fw-bold text-uppercase" style="font-size:0.72rem; letter-spacing:0.03em;">Invoice:</span>
                            <div class="erp-pill-group" style="height: 32px; box-shadow: 0 1px 3px rgba(37, 99, 235, 0.08);">
                                <button class="btn dropdown-toggle btn-prefix py-0 px-2 fw-bold" 
                                        type="button" 
                                        id="btnQuotePrefix" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false"
                                        style="font-size:0.75rem; height:100%;">
                                    <span id="activePrefixLabel">{{ $activePrefix ?? 'QUO' }}</span>
                                </button>
                                <ul class="dropdown-menu shadow-lg p-1 border-0" id="dropdownQuoteSeriesList" aria-labelledby="btnQuotePrefix" style="min-width: 135px; font-size: 0.78rem; z-index: 1050;">
                                    <li><a class="dropdown-item fw-bold text-primary active bg-light" href="#" data-prefix="QUO"><i class="fas fa-check text-primary me-1"></i> QUO (4d)</a></li>
                                    <li><a class="dropdown-item fw-bold" href="#" data-prefix="QUOTE">QUOTE (4d)</a></li>
                                </ul>

                                <input type="text" class="input-inv fw-bold font-monospace" name="quote_no" id="inputQuoteNo" value="{{ $nextQuoteNumber }}" readonly style="font-size:0.80rem; width: 95px; text-align:center;">

                                <button class="btn-refresh py-0 px-2" 
                                        type="button" 
                                        id="btnRefreshQuoteNo" 
                                        title="Regenerate Quote Number"
                                        style="font-size:0.75rem;">
                                    <i class="fas fa-sync-alt" id="iconRefreshQuote"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-erp-pill-outline" title="Fullscreen" onclick="document.documentElement.requestFullscreen()"><i class="fas fa-expand"></i></button>
                    </div>
                </div>

                <!-- TOP INFORMATION PANEL (PILL TOOLBAR LAYOUT - Matches Image 2) -->
                <div class="card-panel mb-2">
                    <div class="d-flex flex-wrap align-items-end gap-3 w-100">
                        <!-- Customer Selection + Walk In Customer Button (Image 2) -->
                        <div style="flex: 2.5 1 340px; min-width: 280px;">
                            <label class="form-label mb-1"><i class="fas fa-user text-primary me-1"></i>Customer</label>
                            <div id="customerInputWrapper">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <input type="text" class="form-control fw-bold d-none" name="walkin_name" id="walkinNameInput" placeholder="Enter Walk-in Customer Name..." style="height: 34px; border-radius: 50px; border: 1.5px solid #f59e0b; padding-left: 14px; font-size: 0.78rem;">
                                    <select class="form-select select2-customer-ajax" id="customerSelect" name="customer" style="width:100%">
                                        <option value="">Search by Name or Code...</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}">
                                                {{ $c->customer_id ? $c->customer_id . ' — ' : '' }}{{ $c->customer_name }}{{ $c->mobile ? ' (' . $c->mobile . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- "walk in customer" button (Highlighted in Image 2!) --}}
                                <button type="button" class="btn btn-walkin-toggle flex-shrink-0" id="btnToggleWalkin" title="Click to enter walk in customer name directly">
                                    <i class="fas fa-walking"></i> <span>walk in customer</span>
                                </button>

@php
    $productOptionsHtml = '<option value=""></option>';
    if (isset($allProducts) && count($allProducts) > 0) {
        foreach($allProducts as $p) {
            $skuTxt = $p->item_code ? ' (SKU: ' . e($p->item_code) . ')' : '';
            $priceTxt = ($p->sale_price_per_piece ?? 0) > 0 ? ' — Rs. ' . number_format($p->sale_price_per_piece, 0) : '';
            $unitTxt = optional($p->unit)->name ?? 'Pcs';
            $brandTxt = optional($p->brand)->name ?? '';
            $modelTxt = $p->model ?? '';
            $priceVal = (float)($p->sale_price_per_piece ?? 0);
            $productOptionsHtml .= '<option value="' . e($p->item_name) . '" data-id="' . $p->id . '" data-name="' . e($p->item_name) . '" data-price="' . $priceVal . '" data-unit="' . e($unitTxt) . '" data-sku="' . e($p->item_code ?? '') . '" data-model="' . e($modelTxt) . '" data-brand="' . e($brandTxt) . '">' . e($p->item_name) . $skuTxt . $priceTxt . '</option>';
        }
    }
@endphp

                                <button type="button" class="btn btn-erp-pill-primary flex-shrink-0" id="btnHeaderAddCustomer" data-bs-toggle="modal" data-bs-target="#addCustomerModal" data-toggle="modal" data-target="#addCustomerModal" title="Add New Customer">
                                    <i class="fas fa-plus-circle"></i> <span>Add Customer</span>
                                </button>
                                <input type="hidden" id="walkinToggle" name="is_walkin" value="0">
                            </div>
                        </div>

                        <!-- Quotation Date (Image 2: Order Date -> Quotation Date) -->
                        <div style="width: 145px; min-width: 140px; flex-shrink: 0;">
                            <label class="form-label"><i class="fas fa-calendar-alt text-primary me-1"></i>Order Date</label>
                            <div class="erp-pill-date-wrapper">
                                <input type="text" name="quotation_date" class="form-control datepicker-custom text-center fw-bold bg-white" id="displayDateInput" value="{{ date('Y-m-d') }}">
                                <i class="fas fa-calendar-alt text-primary date-icon"></i>
                            </div>
                        </div>

                        <!-- Estimated Delivery Date (Image 2: EST. DELIV - 25 days gap) -->
                        <div style="width: 145px; min-width: 140px; flex-shrink: 0;">
                            <label class="form-label"><i class="fas fa-clock text-warning me-1"></i>Est. Deliv</label>
                            <div class="erp-pill-date-wrapper">
                                <input type="text" name="estimated_delivery_date" class="form-control datepicker-custom text-center fw-bold bg-white" id="estDeliveryDateInput" value="{{ date('Y-m-d', strtotime('+25 days')) }}" placeholder="YYYY-MM-DD">
                                <i class="fas fa-calendar-day text-warning date-icon"></i>
                            </div>
                        </div>

                        <!-- Subject Field (Image 2: SUBJECT e.g. Induction Heater Order / Subject) -->
                        <div style="flex: 1.5 1 200px; min-width: 180px;">
                            <label class="form-label"><i class="fas fa-tag text-info me-1"></i>Subject</label>
                            <input type="text" name="reference" class="form-control" id="inputSubject" placeholder="e.g. Induction Heater Order / Subject" style="height: 34px; border-radius: 50px; border: 1.5px solid #bfdbfe; padding: 0 14px; font-size: 0.78rem; font-weight: 500;">
                        </div>
                    </div>
                </div>

                {{-- MAIN WORKSPACE (Items Grid Left, Quote Summary Right) --}}
                <div class="row g-2 mb-2">
                    <!-- LEFT PANEL: Items Table -->
                    <div class="col-lg-8 col-xl-9">
                        <div class="card-panel p-2.5 bg-white h-100 d-flex flex-column" style="border-radius:10px;">
                            {{-- Action Bar above Table --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-dark" style="font-size:0.82rem;"><i class="fas fa-cubes text-primary me-1"></i>Order Items (<span id="itemsCountBadge">1</span>)</span>
                                    <button type="button" class="btn btn-erp-pill-outline py-0 px-2 fw-bold" data-bs-toggle="offcanvas" data-bs-target="#quickProductsOffcanvas" style="height:26px; font-size:0.72rem;">
                                        <i class="fas fa-th text-primary me-1"></i>Quick Products Panel
                                    </button>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <button type="button" class="btn btn-erp-pill-outline py-0 px-2 fw-bold" id="btnQuickCreateProduct" data-bs-toggle="modal" data-bs-target="#quickAddProductModal" data-toggle="modal" data-target="#quickAddProductModal" style="height:26px; font-size:0.72rem;">
                                        <i class="fas fa-bolt text-warning me-1"></i>Create Product
                                    </button>
                                    <button type="button" class="btn btn-erp-pill-primary py-0 px-2.5 fw-bold" id="btnAddRow" style="height:26px; font-size:0.72rem;">
                                        <i class="fas fa-plus me-1"></i>Add Row
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive flex-grow-1" style="min-height: 250px;">
                                <table class="sales-table" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px;">#</th>
                                            <th style="min-width: 240px; text-align: left; padding-left: 10px !important;">Product / Specifications</th>
                                            <th style="width: 65px;">Qty</th>
                                            <th style="width: 60px;">Unit</th>
                                            <th style="width: 95px;">Price</th>
                                            <th style="width: 80px;">Discount</th>
                                            <th style="width: 65px;">GST %</th>
                                            <th style="width: 95px;">Amount</th>
                                            <th style="width: 32px;"><i class="fas fa-trash text-danger"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <!-- Row 1 Default -->
                                        <tr class="item-main-row" data-row="0">
                                            <td class="text-center fw-bold row-index-cell" style="font-size: 0.75rem;">1</td>
                                            <td style="padding: 2px 4px !important;">
                                                <div class="d-flex align-items-center gap-1">
                                                    <div style="flex-grow: 1; min-width: 0;">
                                                        <select class="form-select product-select" name="item_name[]" style="width: 100%;">
                                                            {!! $productOptionsHtml !!}
                                                        </select>
                                                        <input type="hidden" name="product_id[]" class="product-id-input" value="">
                                                    </div>
                                                    <button type="button" class="btn-toggle-specs flex-shrink-0" title="Toggle Specifications & Details">
                                                        <i class="fas fa-sliders-h"></i> Specs / Details <i class="fas fa-chevron-down ms-0.5"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" step="any" min="0" class="form-control qty-input font-monospace fw-bold" name="qty[]" value="1">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control unit-input text-center text-muted" name="unit[]" value="Pcs" placeholder="Pcs">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="number" step="0.01" min="0" class="form-control price-input text-end font-monospace fw-bold" name="price[]" value="0.00">
                                                    <span class="text-muted small px-1">R</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="number" step="0.01" min="0" class="form-control discount-input text-end font-monospace" name="discount[]" value="0">
                                                    <input type="hidden" name="discount_type[]" class="discount-type-val" value="percent">
                                                    <span class="badge bg-light text-secondary border px-1 py-0.5 ms-1 disc-type-badge" style="cursor: pointer;" title="Click to toggle % / Fixed">%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="number" step="0.01" min="0" class="form-control tax-input text-end font-monospace" name="item_tax[]" value="0">
                                                    <span class="text-muted small px-1">%</span>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold font-monospace pe-2 line-total-cell" style="font-size: 0.78rem;">
                                                0.00
                                                <input type="hidden" name="total[]" class="line-total-input" value="0.00">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm text-danger p-0 btn-remove-row" title="Delete Row" style="font-size: 1rem; line-height: 1;">&times;</button>
                                            </td>
                                        </tr>
                                        <!-- Specs Sub-row 1 Default -->
                                        <tr class="specs-subrow d-none" data-row="0">
                                            <td colspan="9" style="background: #f8fbff; padding: 8px 12px !important; border-top: none !important;">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-tag me-1"></i>Equipment Title (Technical Name)</label>
                                                        <input type="text" class="form-control" name="technical_name[]" placeholder="e.g. Induction Melting Furnace 40kw ABC">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-cogs me-1"></i>Technical Specifications (Multiline bullets)</label>
                                                        <textarea class="form-control" name="technical_specs[]" rows="2" placeholder="• Melting Capacity: 20kg Copper&#10;• Tilting System: Manual&#10;• Duty Cycle: 24/7 100%"></textarea>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-bolt me-1"></i>Input Power / Brand</label>
                                                        <input type="text" class="form-control" name="brand[]" placeholder="e.g. 3-PHASE 440V AC 50Hz">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-tachometer-alt me-1"></i>Duty Cycle / Rating</label>
                                                        <input type="text" class="form-control" name="color[]" placeholder="e.g. 24/7 100%">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-fan me-1"></i>Cooling Type / Model</label>
                                                        <input type="text" class="form-control" name="model[]" placeholder="e.g. LTZMODEL Water Cooled">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background: #f8fafc;">
                                            <td colspan="7" class="text-end fw-bold text-muted small pe-2">Subtotal Amount:</td>
                                            <td class="text-end fw-bold font-monospace pe-2" id="tFootTotal">0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Terms & Conditions Card (Image 1 Content by default) -->
                            <div class="card-panel p-2.5 bg-white mt-2" style="border-radius:10px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0 fw-bold text-dark" style="font-size: 0.78rem;">
                                        <i class="fas fa-file-contract text-primary me-1"></i>Terms &amp; Conditions (Manual as per Client Order / Payments)
                                    </label>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-bold" id="btnResetTerms" style="font-size: 0.72rem;" title="Reset to default quotation terms">
                                        <i class="fas fa-undo me-1"></i>Default
                                    </button>
                                </div>
                                <textarea class="form-control" name="terms_and_conditions" id="termsAndConditions" rows="5" style="font-size: 0.78rem; resize: vertical; line-height: 1.45;" placeholder="Enter Terms & Conditions manually as per client proposal...">{{ $defaultTerms }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT PANEL: Summary ONLY (Advance Payment & Accounts crossed out as in Image 2) -->
                    <div class="col-lg-4 col-xl-3">
                        <div class="d-flex flex-column h-100 gap-2">
                            <!-- Executive Summary Card (Matches Image 2 Right Box) -->
                            <div class="card-panel p-3 bg-white" style="border-radius:10px;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom" style="border-color: #dbeafe !important;">
                                    <span class="fw-bold text-dark" style="font-size:0.85rem;"><i class="fas fa-calculator text-primary me-1"></i>Order Summary</span>
                                    <span class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size:0.7rem;">
                                        <span class="pulse-dot"></span> Live
                                    </span>
                                </div>
                                
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Invoice Total</span>
                                    <span class="fw-bold text-dark font-monospace" id="tGross">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Discount</span>
                                    <span class="fw-bold text-danger font-monospace" id="tLineDisc">0.00</span>
                                </div>
                                <div class="summary-row" id="summaryGstRow">
                                    <span class="text-muted fw-semibold">GST / Tax (<span id="summaryGstRate">0</span>%)</span>
                                    <span class="fw-bold font-monospace text-success" id="summaryGstAmount">0.00</span>
                                </div>

                                <div class="summary-row my-2 p-2 rounded" style="background-color: #eff6ff; border: 1.5px solid #bfdbfe;">
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">Net Total</span>
                                    <span class="summary-val-net font-monospace text-primary fs-5" id="tSub">0.00</span>
                                </div>

                                <div class="p-2 rounded mt-2 text-center" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; font-size: 0.72rem; color: #64748b;">
                                    <i class="fas fa-info-circle text-primary me-1"></i> Quotation does not impact customer ledger or require advance account selection.
                                </div>

                                {{-- "Save Quote" Button (Image 2: "'Book order' ki jaga 'Save Quote' ka button hoga") --}}
                                <button type="button" class="btn btn-save-complete w-100 mt-3 py-2.5" id="btnSaveQuote">
                                    <i class="fas fa-bookmark me-2"></i>Save Quote
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM SUMMARY STRIP (Matches Image 2 Footer Strip) -->
                <div class="bottom-summary-strip">
                    <div class="d-flex align-items-center gap-2">
                        <span class="summary-strip-label">Invoice Total:</span>
                        <span class="fs-6 fw-bold text-white font-monospace" id="bottomInvoiceTotal">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="summary-strip-label">Discount:</span>
                        <span class="fs-6 fw-bold font-monospace text-warning" id="bottomTotalDiscount">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2 px-2.5 py-1 rounded-3" style="background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.35);">
                        <span class="summary-strip-label" style="color: #93c5fd !important;">Net Total:</span>
                        <span class="fs-5 fw-bold text-info font-monospace" id="bottomNetTotal">0.00</span>
                    </div>

                    {{-- Image 2: "Save Quote" Button in bottom footer bar --}}
                    <button type="button" class="btn btn-save-complete px-4 py-2" id="btnSaveQuoteBottom">
                        <i class="fas fa-bookmark me-1.5"></i>Save Quote
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Products Offcanvas Drawer -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="quickProductsOffcanvas" style="width: 360px;">
        <div class="offcanvas-header bg-light py-2 border-bottom">
            <h6 class="offcanvas-title fw-bold text-dark mb-0"><i class="fas fa-th text-primary me-2"></i>Quick Products Panel</h6>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-2">
            <div class="input-group input-group-sm mb-2">
                <input type="text" class="form-control" id="sidebarProductSearch" placeholder="Search product by name, barcode or SKU...">
                <button class="btn btn-primary px-2" type="button"><i class="fas fa-search"></i></button>
            </div>
            <div class="overflow-auto pe-1" id="sidebarProductContainer" style="max-height: calc(100vh - 120px);">
                @if(isset($recentProducts) && count($recentProducts) > 0)
                    @foreach($recentProducts as $prod)
                        <div class="pos-product-card d-flex align-items-center justify-content-between p-2 mb-2 rounded border bg-white" style="border-color: #dbeafe !important;">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark small">{{ $prod->item_name }}</div>
                                <div class="text-muted" style="font-size: 0.68rem;">Stock: {{ $prod->total_pieces ?? 0 }} Pcs</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold font-monospace small">Rs. {{ number_format($prod->sale_price_per_piece ?? $prod->retail_price ?? 0, 2) }}</span>
                                <button type="button" class="btn btn-sm btn-primary add-product-direct-btn py-0 px-2" 
                                        data-id="{{ $prod->id }}"
                                        data-name="{{ $prod->item_name }}" 
                                        data-price="{{ $prod->sale_price_per_piece ?? $prod->retail_price ?? 0 }}"
                                        data-unit="{{ optional($prod->unit)->name ?? 'Pcs' }}"
                                        data-model="{{ $prod->model ?? '' }}"
                                        data-brand="{{ optional($prod->brand)->name ?? '' }}"
                                        title="Add to Grid">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
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
                                <input type="text" class="form-control" name="mobile" placeholder="Mobile / WhatsApp">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">NTN / CNIC</label>
                                <input type="text" class="form-control" name="ntn" placeholder="NTN or CNIC">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="opening_balance" value="0" placeholder="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Address</label>
                                <input type="text" class="form-control" name="address" placeholder="Full Address">
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="btnSaveNewCustomer">Save Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Add Product Modal --}}
    @include('admin_panel.partials.quick_add_product_modal')
@endsection

@section('js')
    <script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Flatpickr initialization
            if (typeof flatpickr !== 'undefined') {
                flatpickr('.datepicker-custom', {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true
                });
            }

            // Select2 for Customers
            if (typeof $.fn.select2 !== 'undefined') {
                $('#customerSelect').select2({
                    placeholder: 'Search by Name or Code...',
                    allowClear: true
                });
            }

            // Image 2 Walk-in Customer Button Toggle
            $('#btnToggleWalkin').on('click', function(e) {
                e.preventDefault();
                var currentWalkin = $('#walkinToggle').val() === '1';
                if (!currentWalkin) {
                    // Switch to Walkin mode
                    $('#walkinToggle').val('1');
                    $(this).addClass('active').html('<i class="fas fa-check"></i> <span>Walk-in Active</span>');
                    $('#customerSelect').next('.select2-container').addClass('d-none');
                    $('#walkinNameInput').removeClass('d-none').focus();
                } else {
                    // Switch back to Registered customer mode
                    $('#walkinToggle').val('0');
                    $(this).removeClass('active').html('<i class="fas fa-walking"></i> <span>walk in customer</span>');
                    $('#walkinNameInput').addClass('d-none').val('');
                    $('#customerSelect').next('.select2-container').removeClass('d-none');
                }
            });

            // Toggle Specs Row
            $(document).on('click', '.btn-toggle-specs', function(e) {
                e.preventDefault();
                var tr = $(this).closest('tr');
                var nextTr = tr.next('.specs-subrow');
                nextTr.toggleClass('d-none');
                var icon = $(this).find('.fa-chevron-down, .fa-chevron-up');
                if (nextTr.hasClass('d-none')) {
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                } else {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }
            });

            // Discount type toggle (% or Fixed)
            $(document).on('click', '.disc-type-badge', function() {
                var container = $(this).closest('td');
                var typeInput = container.find('.discount-type-val');
                if (typeInput.val() === 'percent') {
                    typeInput.val('fixed');
                    $(this).text('PKR').removeClass('bg-light text-secondary').addClass('bg-warning text-dark');
                } else {
                    typeInput.val('percent');
                    $(this).text('%').removeClass('bg-warning text-dark').addClass('bg-light text-secondary');
                }
                recalcTotals();
            });

            // Recalculate Totals
            function recalcTotals() {
                var totalGross = 0;
                var totalDisc = 0;
                var totalGst = 0;
                var totalNet = 0;
                var rowCount = 0;

                $('#itemsTableBody .item-main-row').each(function(idx) {
                    rowCount++;
                    $(this).find('.row-index-cell').text(rowCount);

                    var qty = parseFloat($(this).find('.qty-input').val()) || 0;
                    var price = parseFloat($(this).find('.price-input').val()) || 0;
                    var discVal = parseFloat($(this).find('.discount-input').val()) || 0;
                    var discType = $(this).find('.discount-type-val').val() || 'percent';
                    var taxRate = parseFloat($(this).find('.tax-input').val()) || 0;

                    var rowGross = qty * price;
                    var rowDisc = 0;
                    if (discVal > 0) {
                        if (discType === 'fixed') {
                            rowDisc = Math.min(rowGross, discVal);
                        } else {
                            rowDisc = (rowGross * discVal) / 100;
                        }
                    }

                    var afterDisc = Math.max(0, rowGross - rowDisc);
                    var rowTax = 0;
                    if (taxRate > 0) {
                        rowTax = (afterDisc * taxRate) / 100;
                    }

                    var rowNet = afterDisc + rowTax;

                    $(this).find('.line-total-cell').html(rowNet.toFixed(2) + '<input type="hidden" name="total[]" class="line-total-input" value="' + rowNet.toFixed(2) + '">');

                    totalGross += rowGross;
                    totalDisc += rowDisc;
                    totalGst += rowTax;
                    totalNet += rowNet;
                });

                $('#itemsCountBadge').text(rowCount);
                $('#tFootTotal').text(totalGross.toFixed(2));
                $('#tGross').text(totalGross.toFixed(2));
                $('#bottomInvoiceTotal').text(totalGross.toFixed(2));
                $('#tLineDisc').text(totalDisc.toFixed(2));
                $('#bottomTotalDiscount').text(totalDisc.toFixed(2));
                $('#summaryGstAmount').text(totalGst.toFixed(2));
                $('#tSub').text(totalNet.toFixed(2));
                $('#bottomNetTotal').text(totalNet.toFixed(2));
            }

            // Input changes in items
            $(document).on('input', '.qty-input, .price-input, .discount-input, .tax-input', function() {
                recalcTotals();
            });

            // Initialize Select2 on product dropdowns
            function initProductSelect2($el) {
                if (typeof $.fn.select2 === 'undefined') return;
                $el.select2({
                    placeholder: 'Search Product (Name / SKU / Barcode)',
                    allowClear: true,
                    width: '100%',
                    tags: true,
                    dropdownAutoWidth: true,
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') return null;
                        return {
                            id: term,
                            text: term,
                            name: term,
                            isNew: true
                        };
                    }
                });
            }

            initProductSelect2($('.product-select'));

            // Product Select2 change event
            $(document).on('select2:select', '.product-select', function(e) {
                var data = e.params.data;
                var $row = $(this).closest('tr.item-main-row');
                var $specsRow = $row.next('tr.specs-subrow');

                var $opt = $(this).find('option:selected');
                var pId = $opt.data('id');
                var pPrice = $opt.data('price');
                var pUnit = $opt.data('unit');
                var pModel = $opt.data('model');
                var pBrand = $opt.data('brand');
                var pName = $opt.data('name') || data.text;

                if (pId) {
                    $row.find('.product-id-input').val(pId);
                    if (pPrice !== undefined && pPrice !== null && pPrice > 0) {
                        $row.find('.price-input').val(parseFloat(pPrice).toFixed(2));
                    }
                    if (pUnit) {
                        $row.find('.unit-input').val(pUnit);
                    }
                    if (pModel) {
                        $specsRow.find('input[name="model[]"]').val(pModel);
                    }
                    if (pBrand) {
                        $specsRow.find('input[name="brand[]"]').val(pBrand);
                    }
                    if (!$specsRow.find('input[name="technical_name[]"]').val()) {
                        $specsRow.find('input[name="technical_name[]"]').val(pName);
                    }
                } else {
                    $row.find('.product-id-input').val('');
                    if (!$specsRow.find('input[name="technical_name[]"]').val()) {
                        $specsRow.find('input[name="technical_name[]"]').val(data.text);
                    }
                }
                recalcTotals();
            });

            $(document).on('select2:clear', '.product-select', function(e) {
                var $row = $(this).closest('tr.item-main-row');
                $row.find('.product-id-input').val('');
                $row.find('.price-input').val('0.00');
                recalcTotals();
            });

            // Product Options Template for new rows
            var productOptionsTemplate = `{!! $productOptionsHtml !!}`;

            // Add Row
            $('#btnAddRow').on('click', function(e) {
                e.preventDefault();
                var count = $('#itemsTableBody .item-main-row').length + 1;
                var html = `
                    <tr class="item-main-row" data-row="${count}">
                        <td class="text-center fw-bold row-index-cell" style="font-size: 0.75rem;">${count}</td>
                        <td style="padding: 2px 4px !important;">
                            <div class="d-flex align-items-center gap-1">
                                <div style="flex-grow: 1; min-width: 0;">
                                    <select class="form-select product-select" name="item_name[]" style="width: 100%;">
                                        ${productOptionsTemplate}
                                    </select>
                                    <input type="hidden" name="product_id[]" class="product-id-input" value="">
                                </div>
                                <button type="button" class="btn-toggle-specs flex-shrink-0" title="Toggle Specifications & Details">
                                    <i class="fas fa-sliders-h"></i> Specs / Details <i class="fas fa-chevron-down ms-0.5"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <input type="number" step="any" min="0" class="form-control qty-input font-monospace fw-bold" name="qty[]" value="1">
                        </td>
                        <td>
                            <input type="text" class="form-control unit-input text-center text-muted" name="unit[]" value="Pcs" placeholder="Pcs">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <input type="number" step="0.01" min="0" class="form-control price-input text-end font-monospace fw-bold" name="price[]" value="0.00">
                                <span class="text-muted small px-1">R</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <input type="number" step="0.01" min="0" class="form-control discount-input text-end font-monospace" name="discount[]" value="0">
                                <input type="hidden" name="discount_type[]" class="discount-type-val" value="percent">
                                <span class="badge bg-light text-secondary border px-1 py-0.5 ms-1 disc-type-badge" style="cursor: pointer;" title="Click to toggle % / Fixed">%</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <input type="number" step="0.01" min="0" class="form-control tax-input text-end font-monospace" name="item_tax[]" value="0">
                                <span class="text-muted small px-1">%</span>
                            </div>
                        </td>
                        <td class="text-end fw-bold font-monospace pe-2 line-total-cell" style="font-size: 0.78rem;">
                            0.00
                            <input type="hidden" name="total[]" class="line-total-input" value="0.00">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm text-danger p-0 btn-remove-row" title="Delete Row" style="font-size: 1rem; line-height: 1;">&times;</button>
                        </td>
                    </tr>
                    <tr class="specs-subrow d-none" data-row="${count}">
                        <td colspan="9" style="background: #f8fbff; padding: 8px 12px !important; border-top: none !important;">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-tag me-1"></i>Equipment Title (Technical Name)</label>
                                    <input type="text" class="form-control" name="technical_name[]" placeholder="e.g. Induction Melting Furnace 40kw ABC">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-cogs me-1"></i>Technical Specifications (Multiline bullets)</label>
                                    <textarea class="form-control" name="technical_specs[]" rows="2" placeholder="• Melting Capacity: 20kg Copper&#10;• Tilting System: Manual&#10;• Duty Cycle: 24/7 100%"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-bolt me-1"></i>Input Power / Brand</label>
                                    <input type="text" class="form-control" name="brand[]" placeholder="e.g. 3-PHASE 440V AC 50Hz">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-tachometer-alt me-1"></i>Duty Cycle / Rating</label>
                                    <input type="text" class="form-control" name="color[]" placeholder="e.g. 24/7 100%">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-size: 0.68rem; color: #1e40af;"><i class="fas fa-fan me-1"></i>Cooling Type / Model</label>
                                    <input type="text" class="form-control" name="model[]" placeholder="e.g. LTZMODEL Water Cooled">
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                var $newRow = $(html);
                $('#itemsTableBody').append($newRow);
                initProductSelect2($newRow.find('.product-select'));
                recalcTotals();
            });

            // Remove Row
            $(document).on('click', '.btn-remove-row', function(e) {
                e.preventDefault();
                var tr = $(this).closest('tr');
                var nextTr = tr.next('.specs-subrow');
                if ($('#itemsTableBody .item-main-row').length > 1) {
                    tr.remove();
                    nextTr.remove();
                    recalcTotals();
                } else {
                    alert('At least one item is required in the quotation.');
                }
            });

            // Quick Product click from offcanvas
            $(document).on('click', '.add-product-direct-btn', function(e) {
                e.preventDefault();
                var pId = $(this).data('id');
                var pName = $(this).data('name');
                var pPrice = parseFloat($(this).data('price')) || 0;
                var pUnit = $(this).data('unit') || 'Pcs';
                var pModel = $(this).data('model') || '';
                var pBrand = $(this).data('brand') || '';

                // Find first empty row or create new
                var targetRow = null;
                $('#itemsTableBody .item-main-row').each(function() {
                    var selVal = $(this).find('.product-select').val();
                    if (!selVal) {
                        targetRow = $(this);
                        return false;
                    }
                });

                if (!targetRow) {
                    $('#btnAddRow').click();
                    targetRow = $('#itemsTableBody .item-main-row').last();
                }

                var $sel = targetRow.find('.product-select');
                var $specsRow = targetRow.next('.specs-subrow');

                var $existingOpt = $sel.find('option[data-id="' + pId + '"]');
                if ($existingOpt.length > 0) {
                    $sel.val($existingOpt.val()).trigger('change');
                } else {
                    var newOpt = new Option(pName, pName, true, true);
                    $(newOpt).attr('data-id', pId).attr('data-price', pPrice).attr('data-unit', pUnit).attr('data-model', pModel).attr('data-brand', pBrand);
                    $sel.append(newOpt).trigger('change');
                }

                targetRow.find('.product-id-input').val(pId);
                if (pPrice > 0) {
                    targetRow.find('.price-input').val(pPrice.toFixed(2));
                }
                if (pUnit) {
                    targetRow.find('.unit-input').val(pUnit);
                }
                if (pModel) {
                    $specsRow.find('input[name="model[]"]').val(pModel);
                }
                if (pBrand) {
                    $specsRow.find('input[name="brand[]"]').val(pBrand);
                }
                if (!$specsRow.find('input[name="technical_name[]"]').val()) {
                    $specsRow.find('input[name="technical_name[]"]').val(pName);
                }

                recalcTotals();
            });

            // Auto calculate Est Delivery Date as +25 days when Order Date is changed
            $('#displayDateInput').on('change', function() {
                var orderDateStr = $(this).val();
                if (orderDateStr) {
                    var parts = orderDateStr.split('-');
                    if (parts.length === 3) {
                        var d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                        d.setDate(d.getDate() + 25);
                        var yyyy = d.getFullYear();
                        var mm = String(d.getMonth() + 1).padStart(2, '0');
                        var dd = String(d.getDate()).padStart(2, '0');
                        var newEst = yyyy + '-' + mm + '-' + dd;
                        var estPicker = document.querySelector("#estDeliveryDateInput") ? document.querySelector("#estDeliveryDateInput")._flatpickr : null;
                        if (estPicker) {
                            estPicker.setDate(newEst, true);
                        } else {
                            $('#estDeliveryDateInput').val(newEst);
                        }
                    }
                }
            });

            // Modal Trigger Buttons
            $('#btnHeaderAddCustomer').on('click', function(e) {
                e.preventDefault();
                $('#addCustomerModal').modal('show');
            });

            $('#btnQuickCreateProduct').on('click', function(e) {
                e.preventDefault();
                $('#quickAddProductModal').modal('show');
            });

            // Universal modal close / cleanup
            $(document).on('hidden.bs.modal', '.modal', function () {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
            });

            $(document).on('click', '[data-bs-dismiss="modal"], [data-dismiss="modal"]', function(e) {
                var $m = $(this).closest('.modal');
                if ($m.length) {
                    $m.modal('hide');
                    setTimeout(function() {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
                    }, 200);
                }
            });

            // Reset Terms Button (Image 1 content)
            var defaultQuotationTerms = `{!! addslashes($defaultTerms) !!}`;
            $('#btnResetTerms').on('click', function(e) {
                e.preventDefault();
                $('#termsAndConditions').val(defaultQuotationTerms);
            });

            // Save Quotation Handler
            function submitQuotation() {
                var formData = $('#quotationForm').serialize();
                var btn = $('#btnSaveQuote, #btnSaveQuoteBottom');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: "{{ route('quotations.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-bookmark me-1.5"></i> Save Quote');
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Quotation Saved!',
                                text: res.message,
                                showCancelButton: true,
                                confirmButtonText: '<i class="fas fa-print me-1"></i> Print / View Quote',
                                cancelButtonText: '<i class="fas fa-list me-1"></i> Quotations List',
                                confirmButtonColor: '#2563eb',
                                cancelButtonColor: '#64748b',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = res.redirect_url;
                                } else {
                                    window.location.href = res.index_url;
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-bookmark me-1.5"></i> Save Quote');
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error saving quotation.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: msg,
                            confirmButtonColor: '#dc2626'
                        });
                    }
                });
            }

            $('#btnSaveQuote, #btnSaveQuoteBottom').on('click', function(e) {
                e.preventDefault();
                submitQuotation();
            });

            // Ajax Add Customer Form
            $('#ajaxAddCustomerForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var btn = $('#btnSaveNewCustomer');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: "{{ route('customers.store') }}",
                    type: "POST",
                    data: form.serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).text('Save Customer');
                        if (res.success && res.customer) {
                            $('#addCustomerModal').modal('hide');
                            form[0].reset();

                            if ($('#walkinToggle').val() === '1') {
                                $('#btnToggleWalkin').click();
                            }

                            var label = (res.customer.customer_id ? res.customer.customer_id + ' — ' : '') + res.customer.customer_name + (res.customer.mobile ? ' (' + res.customer.mobile + ')' : '');
                            var newOpt = new Option(label, res.customer.id, true, true);
                            $('#customerSelect').append(newOpt).val(res.customer.id).trigger('change');

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Customer Added!',
                                    text: res.message || 'Customer saved successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            alert(res.message || 'Could not save customer.');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Save Customer');
                        var msg = 'Could not save customer.';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', msg, 'error');
                        } else {
                            alert(msg);
                        }
                    }
                });
            });
        });
    </script>
@endsection
