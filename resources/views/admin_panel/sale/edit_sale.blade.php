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
    <style>
        /* ================= ULTRA-COMPACT EXCEL-LIKE ERP UI ================= */
        body {
            background-color: #f8fafc !important;
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }

        .main-container {
            border: 1px solid #94a3b8 !important;
            border-radius: 4px !important;
            box-shadow: none !important;
            background-color: #ffffff !important;
            padding: 6px !important;
            font-size: .78rem;
            max-width: 100%;
        }

        .card-panel {
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 3px !important;
            padding: 6px !important;
            height: 100%;
        }

        .totals-card {
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 3px !important;
            padding: 6px !important;
        }

        /* Section Titles */
        .section-title {
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.72rem !important;
            letter-spacing: 0.5px !important;
            color: #1e293b !important;
            margin-bottom: 4px !important;
            border-left: 3px solid #2563eb !important;
            padding-left: 6px !important;
        }

        .form-control,
        .form-select,
        .select2-container--default .select2-selection--single {
            border: 1px solid #cbd5e1 !important;
            border-radius: 3px !important;
            padding: 2px 6px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
            height: 26px !important;
            font-size: 0.78rem !important;
        }

        .form-control:focus,
        .form-select:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1) !important;
            outline: none !important;
        }

        /* Read-only fields */
        .input-readonly {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            cursor: not-allowed !important;
        }

        /* Compact Buttons */
        .btn-action-primary {
            background-color: #2563eb !important;
            border: 1px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 3px !important;
            padding: 4px 12px !important;
            transition: all 0.15s;
            font-size: 0.78rem !important;
        }
        .btn-action-primary:hover {
            background-color: #1d4ed8 !important;
            color: #ffffff !important;
        }

        .btn-action-secondary {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            border-radius: 3px !important;
            padding: 4px 12px !important;
            transition: all 0.15s;
            font-size: 0.78rem !important;
        }
        .btn-action-secondary:hover {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }

        /* Transaction Grid / Table */
        .table-responsive {
            border: 1px solid #cbd5e1 !important;
            border-radius: 2px !important;
            overflow-x: auto !important;
            overflow-y: visible !important;
            box-shadow: none !important;
            min-height: 100px;
            background-color: #ffffff;
        }

        .minw-350 {
            min-width: 280px;
            width: 280px;
            flex-shrink: 0;
        }

        .sales-table {
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
            width: 100%;
            min-width: 900px;
        }

        .sales-table thead th {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 10px !important;
            letter-spacing: 0.3px;
            padding: 3px 4px !important;
            border: 1px solid #94a3b8 !important;
            border-bottom: 2px solid #64748b !important;
            vertical-align: middle !important;
            text-align: center;
            white-space: nowrap;
        }

        .sales-table thead th.col-product {
            text-align: left !important;
            padding-left: 4px !important;
        }

        .sales-table tbody td {
            border: 1px solid #cbd5e1 !important;
            padding: 0 !important;
            background-color: #ffffff;
            vertical-align: middle !important;
        }

        /* ⚡ FLAT BORDERLESS GRID INPUTS - COMPACT ⚡ */
        .sales-table tbody .form-control,
        .sales-table tbody .form-select {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            height: 26px !important;
            margin: 0 !important;
            padding: 1px 4px !important;
            width: 100% !important;
            background-color: transparent !important;
            text-align: center;
            color: #1e293b !important;
            font-weight: 500 !important;
            font-size: 0.76rem !important;
        }

        .sales-table tbody td.col-product .form-select {
            text-align: left !important;
            padding-left: 12px !important;
        }

        .sales-table tbody .input-readonly,
        .sales-table tbody input[readonly],
        .sales-table tbody select[disabled] {
            background-color: #f1f5f9 !important;
            cursor: not-allowed !important;
            color: #475569 !important;
            font-weight: 600 !important;
        }

        .sales-table tbody .form-control:focus,
        .sales-table tbody .form-select:focus {
            outline: none !important;
            background-color: #eff6ff !important;
            box-shadow: inset 0 0 0 1px #2563eb !important;
        }

        /* Select2 Specific flat borderless styling */
        .sales-table tbody .select2-container--default .select2-selection--single {
            height: 26px !important;
            padding: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background-color: transparent !important;
            display: flex;
            align-items: center;
        }

        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding-left: 4px !important;
            padding-right: 16px !important;
            font-size: 0.76rem !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            text-align: left !important;
        }

        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            right: 4px !important;
        }

        /* Select2 Focus state */
        .sales-table tbody .select2-container--default.select2-container--focus .select2-selection--single {
            background-color: #eff6ff !important;
            box-shadow: inset 0 0 0 1px #2563eb !important;
        }

        /* Elegant flat block layout for discount input + toggle */
        .sales-table tbody .discount-wrapper {
            display: flex !important;
            align-items: stretch !important;
            width: 100% !important;
            height: 26px !important;
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
            background-color: #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 0.7rem !important;
            width: 24px !important;
            min-width: 24px !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            cursor: pointer !important;
        }

        .sales-table tbody .discount-wrapper .discount-toggle:hover {
            background-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .sales-table tfoot td {
            background-color: #e2e8f0 !important;
            border: 1px solid #94a3b8 !important;
            border-top: 2px solid #64748b !important;
            padding: 2px 4px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            font-size: 0.78rem !important;
        }

        /* Row hover */
        .sales-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* Column Widths - Compact & Full Width */
        .col-product { width: auto; min-width: 160px; }
        .col-model { width: 95px; min-width: 95px; }
        .col-serial { width: 85px; min-width: 85px; }
        .col-stock { width: 55px; min-width: 55px; }
        .col-qty, .col-qty-wrapper { width: 85px; min-width: 85px; }
        .col-pieces { width: 50px; min-width: 50px; }
        .col-price-p { width: 85px; min-width: 85px; }
        .col-disc { width: 75px; min-width: 75px; }
        .col-disc-amt { width: 70px; min-width: 70px; }
        .col-amount { width: 90px; min-width: 90px; }
        .col-action { width: 30px; min-width: 30px; text-align: center; }

        /* Invalid cells & inputs */
        .invalid-cell {
            background-color: #fff5f5 !important;
            border: 1px solid #ef4444 !important;
        }
        .invalid-select,
        .invalid-input {
            border-color: #ef4444 !important;
            box-shadow: none !important;
        }
        .badge-soft {
            background: #eef2ff;
            color: #3730a3;
            font-weight: 700;
        }

        /* Walk-in Customer Select Alignment */
        #customerInputWrapper .select2-container--default .select2-selection--single {
            height: 26px !important;
            min-height: 26px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 3px !important;
            background-color: #ffffff !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            padding-left: 6px !important;
            font-size: 0.78rem !important;
            color: #1e293b !important;
            font-weight: 500 !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 24px !important;
            top: 0 !important;
            right: 2px !important;
        }
        #customerInputWrapper .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1) !important;
        }

        /* Right Summary Panel Flow */
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 0.8rem;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-val-net {
            font-size: 1.1rem !important;
            font-weight: 800 !important;
            color: #2563eb !important;
        }
        .summary-val-change {
            font-size: 1.1rem !important;
            font-weight: 800 !important;
            color: #dc2626 !important;
        }

        /* Bottom Summary Strip */
        .bottom-summary-strip {
            background-color: #1e293b;
            color: #ffffff;
            border-radius: 6px;
            padding: 8px 16px;
            margin-top: 8px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .bottom-summary-strip span {
            color: #f8fafc;
        }
        .btn-save-complete {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            padding: 8px 24px;
            border-radius: 6px;
            font-size: 0.9rem;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
            transition: all 0.2s;
        }
        .btn-save-complete:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(16, 185, 129, 0.4);
            color: #ffffff;
        }
    </style>

    <div class="container-fluid py-0 px-1">
        <div class="main-container bg-white border mx-auto">
            <div id="alertBox" class="alert d-none mb-1" role="alert" style="padding:4px 8px; font-size:0.78rem;"></div>
            <form id="saleForm">
                @csrf
                <input type="hidden" name="booking_id" id="booking_id" value="{{ $sale->id ?? '' }}">
                <input type="hidden" name="action" id="action" value="booking">

                {{-- HEADER - Compact --}}
                <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom" style="min-height:26px;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark header-text" style="font-size:0.85rem;"><i class="fas fa-edit text-primary me-1"></i>Edit Order / Booking #{{ $sale->invoice_no }}</span>
                        <span class="badge bg-secondary" id="entryDateTime" style="font-size:0.7rem;">Date: {{ $sale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 fw-bold" id="btnHeaderPosted"
                            disabled style="font-size:0.72rem; height:22px; line-height:20px;">Sale</button>
                    </div>
                </div>

                <!-- TOP HORIZONTAL INFORMATION PANEL (EXCEL ORDERS SHEET COLUMNS) -->
                <div class="p-1 border bg-light mb-1" style="border-radius:3px;">
                    <div class="row g-1 align-items-end w-100 m-0">
                        <!-- Order / Invoice No. -->
                        <div class="col-sm-2 col-md-1">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size:0.7rem;"><i class="fas fa-hashtag text-primary me-1"></i>Order No.</label>
                            <input type="text" class="form-control input-readonly text-center fw-bold" name="Invoice_no"
                                value="{{ $nextInvoiceNumber ?? ($sale->invoice_no ?? '') }}" readonly>
                        </div>
                        
                        <!-- Credit Days -->
                        <div class="col-sm-1 col-md-1">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size:0.7rem;">Cr. Days</label>
                            <input type="number" class="form-control text-center" name="credit_days" placeholder="0"
                                min="0" value="{{ $sale->credit_days ?? '' }}">
                        </div>
                        
                        <!-- Order Date -->
                        <div class="col-sm-2 col-md-1">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size:0.7rem;"><i class="fas fa-calendar-alt text-primary me-1"></i>Order Date</label>
                            <input type="text" name="sale_date" class="form-control datepicker-custom text-center fw-bold" id="displayDateInput" value="{{ isset($sale) ? $sale->created_at->format('Y-m-d') : date('Y-m-d') }}">
                        </div>

                        <!-- Estimated Delivery Date -->
                        <div class="col-sm-2 col-md-1">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size:0.7rem;"><i class="fas fa-clock text-warning me-1"></i>Est. Deliv</label>
                            <input type="text" name="estimated_delivery_date" class="form-control datepicker-custom text-center fw-bold" id="estimatedDeliveryDateInput" value="{{ isset($sale->estimated_delivery_date) ? \Carbon\Carbon::parse($sale->estimated_delivery_date)->format('Y-m-d') : '' }}" placeholder="YYYY-MM-DD">
                        </div>

                        <!-- Order Status Dropdown -->
                        <div class="col-sm-2 col-md-1">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size:0.7rem;"><i class="fas fa-flag text-info me-1"></i>Status</label>
                            <select class="form-select fw-bold" name="sale_status" id="saleStatusSelect" style="height: 26px !important; font-size: 0.78rem;">
                                <option value="pending" class="text-danger fw-bold" {{ ($sale->sale_status ?? 'pending') == 'pending' ? 'selected' : '' }}>🔴 Pending</option>
                                <option value="ready" class="text-primary fw-bold" {{ ($sale->sale_status ?? '') == 'ready' ? 'selected' : '' }}>🔵 Ready</option>
                                <option value="delivered" class="text-success fw-bold" {{ ($sale->sale_status ?? '') == 'delivered' ? 'selected' : '' }}>🟢 Delivered</option>
                                <option value="cancelled" class="text-warning fw-bold" {{ ($sale->sale_status ?? '') == 'cancelled' ? 'selected' : '' }}>🟡 Cancelled</option>
                            </select>
                        </div>

                        <!-- Actual Delivery Date -->
                        <div class="col-sm-2 col-md-1">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size:0.7rem;"><i class="fas fa-truck text-success me-1"></i>Deliv. Date</label>
                            <input type="text" name="delivery_date" class="form-control datepicker-custom text-center fw-bold" id="actualDeliveryDateInput" value="{{ isset($sale->delivery_date) ? \Carbon\Carbon::parse($sale->delivery_date)->format('Y-m-d') : '' }}" placeholder="-">
                        </div>

                        <!-- M.Bill / Remarks -->
                        <div class="col-sm-3 col-md-2">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size:0.7rem;"><i class="fas fa-comment-dots text-secondary me-1"></i>Remarks</label>
                            <input type="text" class="form-control" name="reference" id="remarks" value="{{ $sale->reference ?? '' }}" placeholder="e.g. Self Collected">
                        </div>

                        <!-- Customer & Walk-in Toggle -->
                        <div class="col-sm-4 col-md-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-secondary mb-0" style="font-size:0.7rem;"><i class="fas fa-user text-primary me-1"></i>Customer</label>
                                <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addCustomerModal" style="font-size: 0.65rem;">
                                    <i class="fas fa-user-plus me-1"></i>New
                                </button>
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

                        <!-- Walk-in switch & Quick Save -->
                        <div class="col-sm-2 col-md-1 d-flex flex-column align-items-end justify-content-end">
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <div class="form-check form-switch mb-0 d-flex align-items-center p-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" id="walkinToggle" name="is_walkin" value="1" {{ !isset($sale) || $sale->walkin_name ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label fw-bold ms-1" for="walkinToggle" style="color: #2563eb; font-size: 0.72rem; cursor: pointer;">Walk-in</label>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-success w-100 fw-bold py-1 shadow-sm" id="btnHeaderSaveSale" style="font-size: 0.75rem;"><i class="fas fa-check me-1"></i>Save</button>
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
                        <div class="card-panel d-flex flex-column h-100 p-2 bg-white" style="border-radius:6px;">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="section-title mb-0" style="font-size:0.8rem;">Order Items (<span id="itemsRowCount">{{ isset($sale->items) ? count($sale->items) : 0 }}</span>)</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-primary btn-sm py-1 px-3 rounded-pill fw-bold" id="btnAdd" style="font-size:0.75rem;">
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
                                                                    class="btn btn-outline-secondary discount-toggle"
                                                                    data-type="percent" tabindex="-1" title="Toggle % / PKR">%</button>
                                                        </div>
                                                        <input type="hidden" class="discount-amount" value="{{ $item->discount_amount ?? 0 }}">
                                                    </td>

                                                    <!-- AMOUNT -->
                                                    <td class="col-amount">
                                                        <input type="text" class="form-control sales-amount text-end input-readonly fw-bold text-dark" name="total[]" value="{{ $item->total ?? 0 }}" readonly tabindex="-1">
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
                                            <td colspan="11" class="p-1 border-0" style="background-color: #fef2f2 !important;">
                                                <div class="d-flex align-items-center justify-content-between px-2 py-1 rounded border border-danger text-danger fw-bold" style="background-color: #fee2e2; font-size: 0.78rem;">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                                                        <span id="gridAlertText">Warning: Discount limit exceeded!</span>
                                                    </div>
                                                    <button type="button" class="btn-close py-0" style="font-size: 0.65rem;" onclick="$('#gridAlertRow').addClass('d-none');"></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end fw-bold text-uppercase text-secondary" style="font-size:0.78rem;">Invoice Total:</td>
                                            <td class="text-end fw-bold text-success fs-6"><span id="totalAmount">0.00</span></td>
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
                            <div class="card-panel p-3 bg-white" style="border-radius:6px;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                    <span class="fw-bold text-dark" style="font-size:0.85rem;"><i class="fas fa-calculator text-primary me-1"></i>Summary</span>
                                    <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size:0.7rem;">Live</span>
                                </div>
                                
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Invoice Total</span>
                                    <span class="fw-bold text-dark" id="tGross">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Discount</span>
                                    <span class="fw-bold text-danger" id="tLineDisc">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="fw-bold text-dark">Net Total</span>
                                    <span class="summary-val-net" id="tSub">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Advance Payment</span>
                                    <span class="fw-bold text-success" id="receiptsTotalBadge">0.00</span>
                                </div>
                                <div class="summary-row pt-1">
                                    <span class="fw-bold text-dark">Balance Amount</span>
                                    <span class="summary-val-change" id="walkinChange">0.00</span>
                                </div>
                            </div>

                            <!-- Payment Methods Card -->
                            <div class="card-panel p-3 bg-white flex-grow-1 d-flex flex-column" style="border-radius:6px;">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                    <span class="fw-bold text-dark" style="font-size:0.82rem;"><i class="fas fa-wallet text-success me-1"></i>Advance / Payment</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold" id="btnAddRV" style="font-size:0.7rem;"><i class="fas fa-plus me-1"></i>Add Account</button>
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
                                        }
                                        $firstLine = $receiptLines->first();
                                        $otherLines = $receiptLines->skip(1);
                                    @endphp
                                    <div class="d-flex gap-1 align-items-center mb-2 rv-row">
                                        <select class="form-select form-select-sm rv-account bg-light fw-bold" name="receipt_account_id[]" style="font-size:0.75rem;">
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}" {{ $firstLine && $firstLine->account_id == $acc->id ? 'selected' : ($firstLine ? '' : (str_contains(strtolower($acc->title), 'cash') ? 'selected' : '')) }}>{{ $acc->title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-end rv-amount fw-bold" name="receipt_amount[]" value="{{ $firstLine ? number_format($firstLine->debit, 2, '.', '') : '' }}" placeholder="0.00" style="width: 110px; font-size:0.75rem;">
                                    </div>
                                    @foreach ($otherLines as $line)
                                        <div class="d-flex gap-1 align-items-center mb-2 rv-row">
                                            <select class="form-select form-select-sm rv-account bg-light fw-bold" name="receipt_account_id[]" style="font-size:0.75rem;">
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
                                    <i class="fas fa-save me-2"></i>Update & Complete (F9)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM SUMMARY STRIP -->
                <div class="bottom-summary-strip">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Invoice Total:</span>
                        <span class="fs-6 fw-bold text-dark" id="bottomInvoiceTotal">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Discount:</span>
                        <div class="input-group input-group-sm" style="width: 130px;">
                            <input type="number" class="form-control text-end fw-bold text-danger" id="walkinDiscountRs" value="{{ isset($sale) && $sale->is_walkin ? $sale->total_extradiscount : '0' }}" placeholder="0">
                            <span class="input-group-text bg-light text-muted">Rs</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Net Total:</span>
                        <span class="fs-5 fw-bold text-primary" id="walkinNetTotal">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Advance Paid:</span>
                        <span class="fs-6 fw-bold text-success" id="bottomPaymentsTotal">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold" style="font-size:0.8rem;">Balance:</span>
                        <span class="fs-6 fw-bold text-danger" id="bottomChangeVal">0.00</span>
                    </div>

                    <button type="button" class="btn btn-save-complete" id="btnSaveAndComplete2">
                        <i class="fas fa-save me-2"></i>Update & Complete (F9)
                    </button>
                </div>

                {{-- ACTION BUTTONS ROW --}}
                <div class="d-flex flex-wrap gap-2 justify-content-center py-2 px-3 mt-2 border-top bg-white rounded-3 shadow-sm">
                    <button type="button" class="btn btn-outline-primary btn-sm px-3 fw-bold" id="btnSave"><i class="fas fa-bookmark me-1"></i>Booking</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" id="btnPosted" disabled><i class="fas fa-shopping-cart me-1"></i>Sale</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold" id="btnPrint"><i class="fas fa-print me-1"></i>A4 Print</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold" id="btnEstimate"><i class="fas fa-file-invoice me-1"></i>Estimate</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold" id="btnPrint2"><i class="fas fa-receipt me-1"></i>Thermal Print</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold" id="btnDcThermal"><i class="fas fa-truck me-1"></i>DC</button>
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
