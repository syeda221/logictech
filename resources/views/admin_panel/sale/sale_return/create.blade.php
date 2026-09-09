@extends('admin_panel.layout.app')

@section('content')
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
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

        .form-control {
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

        .form-select,
        select.form-select {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 50px !important;
            padding: 0.25rem 32px 0.25rem 12px !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
            height: 34px !important;
            font-size: 0.78rem !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%232563eb' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 12px 10px !important;
            width: 100% !important;
            cursor: pointer !important;
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
        input[readonly].input-readonly,
        .form-control[readonly] {
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1e40af !important;
            font-weight: 700 !important;
            cursor: not-allowed !important;
        }

        /* Pill Buttons */
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
            text-decoration: none;
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
            padding: 0.5rem 0.4rem !important;
            border: 1px solid #bfdbfe !important;
            border-bottom: 2px solid #60a5fa !important;
            vertical-align: middle !important;
            text-align: center;
            white-space: nowrap;
        }

        .sales-table tbody td {
            border: 1px solid #dbeafe !important;
            padding: 4px 6px !important;
            background-color: #ffffff;
            vertical-align: middle !important;
            font-size: 0.78rem;
        }

        .sales-table tbody tr:hover td {
            background-color: #f0f7ff !important;
        }

        /* Table Inputs */
        .sales-table tbody .quantity-box {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 6px !important;
            height: 30px !important;
            padding: 2px 6px !important;
            text-align: center;
            font-weight: 700 !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
            font-size: 0.82rem !important;
            transition: all 0.15s ease;
        }

        .sales-table tbody .quantity-box:focus {
            border-color: #2563eb !important;
            background-color: #eff6ff !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            outline: none !important;
        }

        .sales-table tbody .quantity-box[readonly] {
            background-color: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
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
            font-size: 1.25rem;
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

        /* Select2 In Return */
        .select2-container--default .select2-selection--single {
            height: 34px !important;
            border: 1.5px solid #bfdbfe !important;
            border-radius: 50px !important;
            display: flex !important;
            align-items: center !important;
            background-color: #ffffff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            padding-left: 12px !important;
            font-size: 0.78rem !important;
            color: #1e293b !important;
            font-weight: 500 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px !important;
            right: 8px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .refund-row .select2-container {
            width: 100% !important;
        }
        .account-bal-badge {
            font-size: 0.70rem;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            letter-spacing: 0.01em;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
    </style>


    <div class="container-fluid py-2 px-2">
        <div class="main-container bg-white mx-auto">
            {{-- Alert Section --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-2" role="alert" style="padding:8px 14px; font-size:0.8rem;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-2" role="alert" style="padding:8px 14px; font-size:0.8rem;">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @php
                $isWalking = ($sale->customer_relation && $sale->customer_relation->customer_type === 'Walking Customer') || ($sale->customer_id == 1) || empty($sale->customer_id);
                $customerDisplayName = optional($sale->customer_relation ?? $sale->customer)->customer_name ?? ($sale->walkin_name ?: 'Walking Customer');
            @endphp

            <form action="{{ route('sale.return.store') }}" method="POST" id="saleReturnForm" autocomplete="off">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                <input type="hidden" name="warehouse_id" value="{{ $sale->warehouse_id ?? 1 }}">
                <input type="hidden" name="customer_id" value="{{ $sale->customer_id }}">
                <input type="hidden" id="isWalkingCustomer" value="{{ $isWalking ? '1' : '0' }}">
                <input type="hidden" name="total_amount_Words" id="amountInWords" value="">
                <input type="hidden" id="hasLinkedSale" value="{{ $sale ? '1' : '0' }}">
                <input type="hidden" id="saleTotalNet" value="{{ $saleTotalNet ?? 0 }}">
                <input type="hidden" id="customerPaid" value="{{ $customerPaid ?? 0 }}">
                <input type="hidden" id="invRemainingDue" name="remaining_invoice_due" value="{{ $remainingInvoiceDue ?? 0 }}">
                <input type="hidden" id="remainingInvoiceDue" value="{{ $remainingInvoiceDue ?? 0 }}">
                <input type="hidden" id="invPaidRefundable" name="remaining_paid_refundable" value="{{ $remainingPaidRefundable ?? 0 }}">
                <input type="hidden" id="remainingPaidRefundable" value="{{ $remainingPaidRefundable ?? 0 }}">

                {{-- TOP HEADER BAR --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 erp-page-header">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('sale.index') }}" class="btn btn-erp-pill-outline" title="Back to Sales">
                            <i class="fas fa-arrow-left"></i> <span>Back</span>
                        </a>
                        <div>
                            <h4 class="erp-title">
                                <i class="fas fa-undo-alt text-primary"></i> <span>Sale Return &mdash; Inv #{{ $sale->invoice_no }}</span>
                            </h4>
                            <p class="erp-subtitle">Process item returns, inventory restock, credit notes, and customer refunds</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge" style="background-color: #eff6ff; color: #1e40af; border: 1.5px solid #bfdbfe; font-size:0.75rem; padding: 6px 14px; border-radius: 50px;">
                            <i class="fas fa-file-invoice me-1"></i> Original Invoice: {{ $sale->invoice_no }}
                        </span>
                        <span class="badge" style="background-color: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0; font-size:0.75rem; padding: 6px 14px; border-radius: 50px;">
                            <i class="far fa-calendar-alt me-1"></i> Sale Date: {{ $sale->created_at->format('d/m/Y h:i A') }}
                        </span>
                    </div>
                </div>

                {{-- INVOICE FINANCIAL STATUS STRIP --}}
                @if($sale)
                <div class="card-panel mb-2 py-2 px-3" style="background: #f8fafc; border-left: 4px solid #2563eb; border-radius: 8px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-semibold">Sale Invoice Total:</span>
                            <strong class="font-monospace text-dark">Rs {{ number_format($saleTotalNet ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-semibold">Customer Advance/Paid:</span>
                            <strong class="font-monospace text-success">Rs {{ number_format($customerPaid ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-semibold">Unpaid Invoice Debt:</span>
                            <strong class="font-monospace text-danger">Rs {{ number_format($remainingInvoiceDue ?? 0, 2) }}</strong>
                        </div>
                        <div>
                            <span class="badge rounded-pill bg-light text-primary border" style="font-size: 0.72rem;">
                                <i class="fas fa-shield-alt me-1"></i> Return value first settles unpaid debt; cash refund is capped to advance paid.
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- TOP INFORMATION PANEL (PILL TOOLBAR LAYOUT) --}}
                <div class="card-panel mb-2">
                    <div class="d-flex flex-wrap align-items-end gap-2 w-100">
                        {{-- Customer Display --}}
                        <div style="flex: 2 1 240px; min-width: 200px;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0"><i class="fas fa-user text-primary me-1"></i>Customer</label>
                                @if($isWalking)
                                    <span class="badge bg-warning-subtle text-warning fw-bold border border-warning" style="font-size:0.65rem; border-radius:50px;">Walk-in</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary fw-bold border border-primary-subtle" style="font-size:0.65rem; border-radius:50px;">Registered</span>
                                @endif
                            </div>
                            <input type="text" class="form-control input-readonly fw-bold" value="{{ $customerDisplayName }}" readonly>
                        </div>

                        {{-- Reference / Invoice # --}}
                        <div style="width: 140px; min-width: 120px; flex-shrink: 0;">
                            <label class="form-label"><i class="fas fa-hashtag text-primary me-1"></i>Invoice #</label>
                            <input type="text" class="form-control input-readonly text-center font-monospace fw-bold" value="{{ $sale->invoice_no }}" readonly>
                        </div>

                        {{-- Warehouse --}}
                        <div style="width: 160px; min-width: 140px; flex-shrink: 0;">
                            <label class="form-label"><i class="fas fa-warehouse text-primary me-1"></i>Warehouse</label>
                            <input type="text" class="form-control input-readonly text-center" value="{{ $sale->warehouse->warehouse_name ?? 'Main Warehouse' }}" readonly>
                        </div>

                        {{-- Return Date --}}
                        <div style="width: 150px; min-width: 130px; flex-shrink: 0;">
                            <label class="form-label"><i class="fas fa-calendar-alt text-primary me-1"></i>Return Date <span class="text-danger">*</span></label>
                            <input type="date" name="return_date" class="form-control text-center fw-bold bg-white" value="{{ date('Y-m-d') }}" required>
                        </div>

                        {{-- Reason / Notes --}}
                        <div style="flex: 2 1 220px; min-width: 180px;">
                            <label class="form-label"><i class="fas fa-comment-dots text-primary me-1"></i>Return Reason / Notes</label>
                            <input type="text" name="return_reason" class="form-control" placeholder="e.g. Damaged goods, customer return...">
                        </div>
                    </div>
                </div>

                {{-- 2-COLUMN RESPONSIVE POS LAYOUT --}}
                <div class="row g-2 align-items-stretch">
                    {{-- LEFT MAIN AREA: Items Grid Table (col-lg-8 col-xl-9) --}}
                    <div class="col-lg-8 col-xl-9">
                        <div class="card-panel d-flex flex-column h-100 p-2 bg-white" style="border-radius:10px;">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <div class="section-title mb-0" style="font-size:0.78rem;">
                                    Sale Items to Return (<span id="itemsRowCount">{{ count($sale->items) }}</span>)
                                </div>
                                <button type="button" class="btn btn-erp-pill-primary py-1 px-3 fw-bold" id="btnReturnAll" style="height: 30px; font-size:0.75rem;">
                                    <i class="fas fa-check-double me-1"></i> Return All Items
                                </button>
                            </div>

                            <div class="table-responsive flex-grow-1">
                                <table class="table sales-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 25px;" class="text-center">#</th>
                                            <th style="min-width: 160px;" class="text-start ps-2">Product Description</th>
                                            <th style="width: 65px;">Size</th>
                                            <th style="width: 65px;">Color</th>
                                            <th style="width: 75px;">Item Code</th>
                                            <th style="width: 65px;">PC/Box</th>
                                            <th style="width: 80px;" class="text-end">Sold Price</th>
                                            <th style="width: 95px;" class="text-center">Purchased / Rem.</th>
                                            <th style="width: 110px;" class="text-center">Return Qty (Box.Pc)</th>
                                            <th style="width: 70px;" class="text-center">Return Pcs</th>
                                            <th style="width: 85px;" class="text-end text-danger">Line Disc.</th>
                                            <th style="width: 90px;" class="text-end">Amount</th>
                                            <th style="width: 30px;" class="text-center">×</th>
                                        </tr>
                                    </thead>
                                    <tbody id="returnItems">
                                        @foreach ($sale->items as $index => $item)
                                            @php
                                                $original = $item['original_qty'] ?? $item['qty'];
                                                $returned = $item['returned_qty'] ?? 0;
                                                $netRemaining = $item['max_returnable'] ?? ($original - $returned);
                                                $ppb = (int)($item['pieces_per_box'] ?? 1);
                                                if ($ppb <= 0) $ppb = 1;

                                                if ($ppb > 1) {
                                                    $remBoxes = floor($netRemaining / $ppb);
                                                    $remPcs = $netRemaining % $ppb;
                                                    $remDisplay = $remBoxes . ($remPcs > 0 ? '.'.$remPcs : '');
                                                } else {
                                                    $remDisplay = $netRemaining;
                                                }
                                                $unitDisc = ($original > 0) ? ((float)($item['discount'] ?? 0) / $original) : 0;
                                            @endphp
                                            <tr>
                                                <input type="hidden" name="product_id[]" value="{{ $item['product_id'] }}">
                                                <input type="hidden" name="color[]" value="{{ $item['color'] ?? '' }}">
                                                <input type="hidden" name="item_disc[]" class="item_disc" value="0" data-unit-disc="{{ $unitDisc }}">
                                                <input type="hidden" name="unit[]" value="{{ $item['unit'] ?? 'pc' }}">
                                                <input type="hidden" name="size_mode[]" class="size-mode" value="{{ $item['size_mode'] ?? 'by_pieces' }}">
                                                <input type="hidden" name="pieces_per_m2[]" class="pieces-per-m2" value="{{ $item['pieces_per_m2'] ?? 0 }}">
                                                <input type="hidden" class="pieces-per-box" value="{{ $ppb }}">
                                                <input type="hidden" name="price[]" class="price" value="{{ (float) $item['price'] }}">

                                                {{-- # --}}
                                                <td class="text-center fw-bold text-muted" style="font-size:0.75rem;">{{ $index + 1 }}</td>

                                                {{-- Product Details --}}
                                                <td class="text-start ps-2">
                                                    <div class="fw-bold text-dark" style="font-size:0.80rem;">{{ $item['item_name'] }}</div>
                                                    @if(!empty($item['brand']))
                                                        <small class="text-muted d-block" style="font-size: 0.68rem;"><i class="fas fa-tag me-1"></i>{{ $item['brand'] }}</small>
                                                    @endif
                                                </td>

                                                {{-- Size --}}
                                                <td class="text-center">
                                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.70rem;">{{ $item['size_val'] ?? '-' }}</span>
                                                </td>

                                                {{-- Color --}}
                                                <td class="text-center">
                                                    <span class="badge bg-light text-secondary border" style="font-size:0.70rem;">{{ $item['color_val'] ?? '-' }}</span>
                                                </td>

                                                {{-- Item Code --}}
                                                <td class="text-center font-monospace text-muted" style="font-size:0.72rem;">
                                                    {{ $item['item_code'] ?: '-' }}
                                                </td>

                                                {{-- PC per box --}}
                                                <td class="text-center font-monospace" style="font-size:0.75rem;">
                                                    {{ $ppb }}
                                                </td>

                                                {{-- Sold Price --}}
                                                <td class="text-end font-monospace">
                                                    <div class="fw-semibold text-dark">{{ number_format((float) $item['price'], 2) }}</div>
                                                    <small class="text-muted d-block" style="font-size: 0.62rem;">
                                                        @if (($item['size_mode'] ?? '') == 'by_size')
                                                            Per M²
                                                        @elseif(($item['size_mode'] ?? '') == 'by_cartons')
                                                            Per Box
                                                        @else
                                                            Per Pc
                                                        @endif
                                                    </small>
                                                </td>

                                                {{-- Purchased / Remaining Qty --}}
                                                <td class="text-center">
                                                    <span class="fw-bold text-dark" style="font-size:0.82rem;">{{ $remDisplay }}</span>
                                                    <small class="text-muted d-block" style="font-size: 0.65rem;">Rem: <strong>{{ $netRemaining }}</strong> pcs</small>
                                                    @if ($returned > 0)
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1" style="font-size: 0.60rem;">Ret: {{ $returned }}</span>
                                                    @endif
                                                </td>

                                                {{-- Return Qty (Box.Piece) Input --}}
                                                <td class="text-center" style="padding: 3px 6px !important;">
                                                    <input type="text" name="qty_box[]" class="form-control quantity-box w-100" value="0" placeholder="0.0" {{ $netRemaining <= 0 ? 'readonly' : '' }}>
                                                </td>

                                                {{-- Total Return Pieces (Calculated) --}}
                                                <td class="text-center">
                                                    <input type="number" name="qty[]" class="form-control text-center fw-bold quantity border-0 bg-transparent p-0" value="0" readonly min="0" max="{{ $netRemaining }}" data-max="{{ $netRemaining }}" data-original="{{ $original }}" data-returned="{{ $returned }}" style="color: #2563eb; font-size: 0.85rem;">
                                                </td>

                                                {{-- Line Discount --}}
                                                <td class="text-end font-monospace">
                                                    <span class="text-danger fw-bold line-disc-display" style="font-size: 0.82rem;">0.00</span>
                                                </td>

                                                {{-- Total Amount --}}
                                                <td class="text-end font-monospace">
                                                    <input type="text" name="total[]" class="form-control text-end fw-bold row-total border-0 bg-transparent p-0 text-dark" value="0.00" readonly style="font-size: 0.85rem;">
                                                </td>

                                                {{-- Remove Row --}}
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row rounded-circle p-1" title="Exclude from Return" style="width: 24px; height: 24px; line-height: 1;">
                                                        <i class="fas fa-times" style="font-size: 0.70rem;"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Return Status & Words Strip --}}
                            <div class="mt-2 pt-2 border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="fas fa-cubes text-primary"></i>
                                        <span class="text-muted small">Total Pieces Returned:</span>
                                        <strong id="totalPieces" class="text-primary fs-6 font-monospace">0</strong>
                                    </div>
                                    <div class="vr d-none d-md-block"></div>
                                    <div>
                                        <small class="text-muted me-1">Words:</small>
                                        <span id="amountInWordsDisplay" class="fw-semibold text-dark small fst-italic">Zero Rupees</span>
                                    </div>
                                </div>

                                {{-- Sleek Clean ERP Return Progress Indicator --}}
                                <div class="d-flex align-items-center gap-2" style="min-width: 220px;">
                                    <span id="returnTypeBadge" class="badge rounded-pill bg-light text-muted border" style="font-size: 0.70rem; padding: 4px 10px;">
                                        No Items Selected
                                    </span>
                                    <div class="progress flex-grow-1" style="height: 10px; background: #e2e8f0; border-radius: 50px; overflow: hidden; min-width: 100px;">
                                        <div id="returnProgressBar" class="progress-bar" role="progressbar" style="width: 0%; background: #2563eb; transition: width 0.3s ease;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span id="returnPercentage" class="fw-bold font-monospace text-primary" style="font-size: 0.75rem;">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT PANEL: Summary & Refund Payment (col-lg-4 col-xl-3) --}}
                    <div class="col-lg-4 col-xl-3">
                        <div class="d-flex flex-column h-100 gap-2">
                            {{-- Executive Return Summary Card --}}
                            <div class="card-panel p-3 bg-white" style="border-radius:10px;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom" style="border-color: #dbeafe !important;">
                                    <span class="fw-bold text-dark" style="font-size:0.85rem;"><i class="fas fa-calculator text-primary me-1"></i>Return Summary</span>
                                    <span class="badge rounded-pill px-2 py-1" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size:0.7rem;">Live</span>
                                </div>
                                
                                <div class="summary-row">
                                    <span class="text-muted fw-semibold">Gross Goods Total</span>
                                    <span class="fw-bold text-dark font-monospace" id="displayGrossBill">0.00</span>
                                </div>
                                <div class="summary-row" id="rowLineDiscount">
                                    <span class="text-danger fw-semibold">Less: Line Discounts</span>
                                    <span class="fw-bold text-danger font-monospace" id="displayTotalItemDiscount">-0.00</span>
                                </div>
                                <div class="summary-row border-top pt-1 mt-1">
                                    <span class="text-muted fw-semibold">Net Goods Subtotal</span>
                                    <span class="fw-bold text-dark font-monospace" id="displayBillAmount">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="text-danger fw-semibold">Less: Extra Deductions</span>
                                    <div class="input-group input-group-sm" style="width: 110px;">
                                        <input type="number" name="extra_discount" id="extraDiscount" class="form-control text-end fw-bold text-danger font-monospace" value="{{ number_format($saleExtraDiscount ?? 0, 2, '.', '') }}" min="0" step="0.01">
                                        <span class="input-group-text bg-light text-muted px-1" style="font-size:0.7rem;">Rs</span>
                                    </div>
                                </div>
                                <div class="summary-row pt-2 mt-1 border-top">
                                    <span class="fw-bold text-dark">Total Net Return</span>
                                    <span class="summary-val-net font-monospace text-primary fw-bold" id="displayNetAmount">0.00</span>
                                </div>

                                {{-- Accounting Allocation --}}
                                <div class="mt-2 pt-2 border-top">
                                    <div class="summary-row mb-1">
                                        <span class="text-muted small"><i class="fas fa-file-invoice text-secondary me-1"></i>Adjust Unpaid Bill:</span>
                                        <span class="fw-bold text-danger font-monospace small" id="displayDueAdjusted">0.00</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="text-dark fw-bold small"><i class="fas fa-hand-holding-usd text-success me-1"></i>Max Cash Refund:</span>
                                        <span class="fw-bold text-success font-monospace" style="font-size:0.92rem;" id="displayMaxCashRefund">0.00</span>
                                    </div>
                                </div>

                                <input type="hidden" name="total_gross" id="grossAmount" value="0.00">
                                <input type="hidden" name="total_item_disc" id="totalItemDisc" value="0.00">
                                <input type="hidden" name="total_subtotal" id="billAmount" value="0.00">
                                <input type="hidden" name="net_amount" id="netAmount" value="0.00">
                            </div>

                            {{-- Refund Payment Card --}}
                            <div class="card-panel p-3 bg-white flex-grow-1 d-flex flex-column" style="border-radius:10px;">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom" style="border-color: #dbeafe !important;">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fw-bold text-dark" style="font-size:0.82rem;"><i class="fas fa-wallet text-success me-1"></i>Refund Payment</span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" id="maxRefundBadge" style="font-size:0.68rem;">Max: Rs 0.00</span>
                                    </div>
                                    <button type="button" class="btn btn-erp-pill-outline py-0 px-2 fw-bold" id="btnAddRefundAccount" style="height: 26px; font-size:0.7rem;">
                                        <i class="fas fa-plus me-1"></i>Add Account
                                    </button>
                                </div>

                                <div id="noCashRefundNotice" class="alert alert-info border small p-2 mb-2" style="font-size:0.72rem; display:none;">
                                    <i class="fas fa-info-circle me-1"></i> <strong>Zero Cash Refund:</strong> Unpaid invoice balance will be adjusted directly. No cash payout is required.
                                </div>

                                @if($isWalking)
                                    <div class="alert alert-warning border border-warning-subtle small p-2 mb-2" style="font-size:0.74rem;">
                                        <i class="fas fa-exclamation-triangle text-warning me-1"></i> <strong>Walk-in Customer:</strong> 100% full cash/bank refund required.
                                    </div>
                                @else
                                    <div class="alert alert-light border small p-2 mb-2 text-muted" id="registeredCustomerNotice" style="font-size:0.72rem;">
                                        <i class="fas fa-info-circle text-primary me-1"></i> Optional payout up to Max Cash Refund. Unrefunded balance remains in customer's store credit.
                                    </div>
                                @endif

                                <div id="refundAccountsWrapper" class="mb-2">
                                    <div class="refund-row mb-2">
                                        <div class="d-flex gap-1 align-items-start">
                                            <div style="flex: 1 1 auto; min-width: 0;">
                                                <select name="payment_account_id[]" class="form-select form-select-sm payment-account fw-bold" style="font-size:0.75rem;">
                                                    <option value="" data-bal="0">Select Account (Cash/Bank)</option>
                                                    @foreach ($accounts as $acc)
                                                        <option value="{{ $acc->id }}" data-bal="{{ (float) $acc->current_balance }}" {{ (str_contains(strtolower($acc->title), 'cash') || str_contains(strtolower($acc->account_code), 'cash')) ? 'selected' : '' }}>
                                                            {{ $acc->title }} ({{ $acc->account_code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="account-bal-wrap mt-1" style="display: none;">
                                                    <span class="badge account-bal-badge">
                                                        <i class="fas fa-wallet me-1"></i> Balance: <strong class="account-bal-text font-monospace ms-1">Rs 0.00</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <input type="number" name="payment_amount[]" step="0.01" class="form-control form-control-sm text-end payment-amount fw-bold font-monospace" placeholder="0.00" style="width: 105px; height: 34px; font-size:0.75rem;">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-save-complete w-100 mt-auto py-2" id="btnSubmitReturn">
                                    <i class="fas fa-check-circle me-2"></i> Process Sale Return
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BOTTOM SUMMARY STRIP --}}
                <div class="bottom-summary-strip">
                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted fw-semibold" style="font-size:0.75rem;">Gross:</span>
                        <span class="fw-bold text-dark font-monospace" style="font-size:0.85rem;" id="bottomGross">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <span class="text-danger fw-semibold" style="font-size:0.75rem;">Line Disc:</span>
                        <span class="fw-bold text-danger font-monospace" style="font-size:0.85rem;" id="bottomLineDisc">-0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted fw-semibold" style="font-size:0.75rem;">Subtotal:</span>
                        <span class="fw-bold text-dark font-monospace" style="font-size:0.85rem;" id="bottomSubtotal">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <span class="text-danger fw-semibold" style="font-size:0.75rem;">Extra Disc:</span>
                        <span class="fw-bold text-danger font-monospace" style="font-size:0.85rem;" id="bottomDeductions">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted fw-semibold" style="font-size:0.75rem;">Net Return:</span>
                        <span class="fw-bold text-primary font-monospace" style="font-size:0.95rem;" id="bottomNetRefund">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted fw-semibold" style="font-size:0.75rem;">Due Settled:</span>
                        <span class="fw-bold text-danger font-monospace" style="font-size:0.85rem;" id="bottomDueAdjusted">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted fw-semibold" style="font-size:0.75rem;">Cash Refund:</span>
                        <span class="fw-bold font-monospace" style="font-size:0.85rem; color: #059669;" id="bottomRefundPaid">0.00</span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted fw-semibold" style="font-size:0.75rem;">Store Credit:</span>
                        <span class="fw-bold text-primary font-monospace" style="font-size:0.85rem;" id="bottomLedgerCredit">0.00</span>
                    </div>

                    <button type="submit" class="btn btn-save-complete ms-auto" id="btnSubmitReturnBottom">
                        <i class="fas fa-check-circle me-1"></i> Process Return
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // Initialize Select2 if loaded
            function initSelect2($el) {
                if ($.fn.select2) {
                    $el.select2({
                        width: '100%'
                    });
                }
            }
            initSelect2($('.payment-account'));

            function num(n) {
                return isNaN(parseFloat(n)) ? 0 : parseFloat(n);
            }

            function numberToWords(nVal) {
                const a = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
                    "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen",
                    "Eighteen", "Nineteen"
                ];
                const b = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
                let numStr = Math.round(nVal).toString();
                if (numStr.length > 9) return "Overflow";
                const n = ("000000000" + numStr).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{3})$/);
                if (!n) return "";
                let str = "";
                str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + " " + a[n[1][1]]) + " Crore " : "";
                str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + " " + a[n[2][1]]) + " Lakh " : "";
                str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + " " + a[n[3][1]]) + " Thousand " : "";
                str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + " " + a[n[4][1]]) + " " : "";
                return str.trim() ? str.trim() + " Rupees Only" : "Zero Rupees";
            }

            function formatMoney(val) {
                return num(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function recalcRow($row) {
                const qty = num($row.find('.quantity').val()); // Pieces (Total)
                const price = num($row.find('.price').val()); // Price
                const sizeMode = $row.find('.size-mode').val();
                const ppm2 = num($row.find('.pieces-per-m2').val());
                const unitDisc = num($row.find('.item_disc').attr('data-unit-disc'));

                let gross = 0;
                if (sizeMode === 'by_size') {
                    gross = qty * ppm2 * price;
                } else {
                    gross = qty * price;
                }

                const itemDisc = unitDisc * qty;
                $row.find('.item_disc').val(itemDisc.toFixed(2));

                const rowTotal = Math.max(0, gross - itemDisc);
                $row.find('.row-total').val(rowTotal.toFixed(2));
                $row.attr('data-gross', gross.toFixed(2));
                $row.attr('data-disc', itemDisc.toFixed(2));

                if (itemDisc > 0.005) {
                    $row.find('.line-disc-display').text('-' + formatMoney(itemDisc));
                    $row.find('.row-disc-text').removeClass('d-none').find('.disc-val').text(formatMoney(itemDisc));
                } else {
                    $row.find('.line-disc-display').text('0.00');
                    $row.find('.row-disc-text').addClass('d-none');
                }
            }

            function recalcSummary() {
                let grossAmount = 0;
                let totalLineDisc = 0;
                let billAmount = 0;
                let totalQty = 0;

                $('#returnItems tr').each(function() {
                    const qty = num($(this).find('.quantity').val());
                    const gross = num($(this).attr('data-gross'));
                    const disc = num($(this).find('.item_disc').val());
                    const rowTotal = num($(this).find('.row-total').val());

                    grossAmount += (gross > 0 ? gross : (rowTotal + disc));
                    totalLineDisc += disc;
                    billAmount += rowTotal;
                    totalQty += qty;
                });

                const extraDiscount = num($('#extraDiscount').val()); // Deduction
                const net = Math.max(0, billAmount - extraDiscount);

                // Financial calculations from invoice status
                const remainingInvoiceDue = num($('#invRemainingDue').val());
                const remainingPaidRefundable = num($('#invPaidRefundable').val());

                const dueAdjusted = Math.min(net, remainingInvoiceDue);
                const maxCashRefund = Math.min(Math.max(0, net - dueAdjusted), remainingPaidRefundable);

                // Hidden Inputs
                $('#grossAmount').val(grossAmount.toFixed(2));
                $('#totalItemDisc').val(totalLineDisc.toFixed(2));
                $('#billAmount').val(billAmount.toFixed(2));
                $('#netAmount').val(net.toFixed(2));

                // Right Panel Display
                $('#displayGrossBill').text(formatMoney(grossAmount));
                $('#displayTotalItemDiscount').text(totalLineDisc > 0 ? ('-' + formatMoney(totalLineDisc)) : '0.00');
                $('#displayBillAmount').text(formatMoney(billAmount));
                $('#displayNetAmount').text(formatMoney(net));
                $('#displayDueAdjusted').text(formatMoney(dueAdjusted));
                $('#displayMaxCashRefund').text(formatMoney(maxCashRefund));
                $('#maxRefundBadge').text('Max: Rs ' + formatMoney(maxCashRefund));

                if (maxCashRefund <= 0 && net > 0) {
                    $('#noCashRefundNotice').show();
                    $('#registeredCustomerNotice').hide();
                } else {
                    $('#noCashRefundNotice').hide();
                    $('#registeredCustomerNotice').show();
                }

                // Bottom Strip Display
                $('#bottomGross').text(formatMoney(grossAmount));
                $('#bottomLineDisc').text(totalLineDisc > 0 ? ('-' + formatMoney(totalLineDisc)) : '0.00');
                $('#bottomSubtotal').text(formatMoney(billAmount));
                $('#bottomDeductions').text(formatMoney(extraDiscount));
                $('#bottomNetRefund').text(formatMoney(net));
                $('#bottomDueAdjusted').text(formatMoney(dueAdjusted));

                // Amount in Words
                const words = net > 0 ? numberToWords(net) : 'Zero Rupees';
                $('#amountInWords').val(words);
                $('#amountInWordsDisplay').text(words);

                // Quantities and Counts
                $('#totalPieces').text(totalQty);
                $('#itemsRowCount').text($('#returnItems tr').length);

                // Payment input auto-fill if single payment row
                const isWalking = $('#isWalkingCustomer').val() === '1';
                if ($('.refund-row').length === 1) {
                    const $pInput = $('.payment-amount').first();
                    if (isWalking || maxCashRefund > 0) {
                        $pInput.val(maxCashRefund > 0 ? maxCashRefund.toFixed(2) : '0.00');
                    } else {
                        $pInput.val('0.00');
                    }
                }

                // Sum Paid & Store Credit
                let totalPaid = 0;
                $('.payment-amount').each(function() {
                    totalPaid += num($(this).val());
                });

                $('#bottomRefundPaid').text(formatMoney(totalPaid));

                let storeCredit = isWalking ? 0 : Math.max(0, maxCashRefund - totalPaid);
                $('#bottomLedgerCredit').text(formatMoney(storeCredit));

                // Update visual indicators
                updatePartialReturnIndicator();
            }

            // Box.Piece Input Logic
            $(document).on('input', '.quantity-box', function() {
                const $row = $(this).closest('tr');
                const val = $(this).val();
                const ppb = num($row.find('.pieces-per-box').val()) || 1;
                const maxReturnable = num($row.find('.quantity').attr('data-max'));

                let boxes = 0;
                let pieces = 0;

                if (val.includes('.')) {
                    const parts = val.split('.');
                    boxes = num(parts[0]);
                    const decimalPart = parts[1];
                    if (decimalPart) {
                        pieces = parseInt(decimalPart);
                    }
                } else {
                    boxes = num(val);
                }

                let totalPieces = 0;
                if (ppb > 0) {
                    totalPieces = (boxes * ppb) + pieces;
                } else {
                    totalPieces = boxes;
                }

                if (maxReturnable > 0 && totalPieces > maxReturnable) {
                    totalPieces = maxReturnable;
                    let boxDisp = (ppb > 1 && maxReturnable % ppb > 0) ? (Math.floor(maxReturnable / ppb) + '.' + (maxReturnable % ppb)) : (maxReturnable / (ppb > 0 ? ppb : 1));
                    $(this).val(boxDisp);
                }

                $row.find('.quantity').val(totalPieces);
                $row.find('.quantity').trigger('input');
            });

            // Auto-fill Max on Click/Focus if 0
            $(document).on('click focus', '.quantity-box', function() {
                const val = $(this).val();
                if (parseFloat(val) === 0 || val === '') {
                    const $row = $(this).closest('tr');
                    const maxQty = parseFloat($row.find('.quantity').attr('data-max')) || 0;
                    const ppb = parseFloat($row.find('.pieces-per-box').val()) || 1;

                    let boxDisplay = '';
                    if (ppb > 1) {
                        const boxes = Math.floor(maxQty / ppb);
                        const pieces = maxQty % ppb;
                        boxDisplay = pieces > 0 ? (boxes + '.' + pieces) : boxes;
                    } else {
                        boxDisplay = maxQty;
                    }

                    $(this).val(boxDisplay);
                    $(this).trigger('input');
                    $(this).select();
                }
            });

            // Return All Button Logic
            $('#btnReturnAll').click(function() {
                $('#returnItems tr').each(function() {
                    const $row = $(this);
                    const maxQty = parseFloat($row.find('.quantity').attr('data-max')) || 0;
                    const ppb = parseFloat($row.find('.pieces-per-box').val()) || 1;

                    $row.find('.quantity').val(maxQty);

                    let boxDisplay = '';
                    if (ppb > 1) {
                        const boxes = Math.floor(maxQty / ppb);
                        const pieces = maxQty % ppb;
                        boxDisplay = pieces > 0 ? (boxes + '.' + pieces) : boxes;
                    } else {
                        boxDisplay = maxQty;
                    }

                    $row.find('.quantity-box').val(boxDisplay);
                    recalcRow($row);
                });
                recalcSummary();
            });

            // Update Account Balance Badge
            function updateAccountBalanceBadge($select) {
                const $row = $select.closest('.refund-row');
                const $wrap = $row.find('.account-bal-wrap');
                const $badge = $row.find('.account-bal-badge');
                const $opt = $select.find('option:selected');
                const val = $select.val();

                if (!val) {
                    $wrap.hide();
                    return;
                }

                const bal = parseFloat($opt.attr('data-bal')) || 0;
                const formatted = 'Rs ' + num(bal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                if (bal > 0) {
                    $badge.css({
                        'background-color': '#f0fdf4',
                        'color': '#166534',
                        'border': '1px solid #bbf7d0'
                    });
                    $badge.html('<i class="fas fa-wallet me-1"></i> Balance: <strong class="font-monospace ms-1">' + formatted + '</strong>');
                } else if (bal < 0) {
                    $badge.css({
                        'background-color': '#fef2f2',
                        'color': '#991b1b',
                        'border': '1px solid #fecaca'
                    });
                    $badge.html('<i class="fas fa-exclamation-circle me-1"></i> Balance: <strong class="font-monospace ms-1">' + formatted + '</strong>');
                } else {
                    $badge.css({
                        'background-color': '#f8fafc',
                        'color': '#475569',
                        'border': '1px solid #e2e8f0'
                    });
                    $badge.html('<i class="fas fa-wallet me-1"></i> Balance: <strong class="font-monospace ms-1">Rs 0.00</strong>');
                }

                $wrap.show();
            }

            $(document).on('change', '.payment-account', function() {
                updateAccountBalanceBadge($(this));
            });

            // Dynamic Refund Accounts
            $('#btnAddRefundAccount').on('click', function() {
                const rowHtml = `
                    <div class="refund-row mb-2">
                        <div class="d-flex gap-1 align-items-start">
                            <div style="flex: 1 1 auto; min-width: 0;">
                                <select name="payment_account_id[]" class="form-select form-select-sm payment-account fw-bold" style="font-size:0.75rem;">
                                    <option value="" data-bal="0">Select Account (Cash/Bank)</option>
                                    @foreach ($accounts as $acc)
                                        <option value="{{ $acc->id }}" data-bal="{{ (float) $acc->current_balance }}">{{ $acc->title }} ({{ $acc->account_code }})</option>
                                    @endforeach
                                </select>
                                <div class="account-bal-wrap mt-1" style="display: none;">
                                    <span class="badge account-bal-badge">
                                        <i class="fas fa-wallet me-1"></i> Balance: <strong class="account-bal-text font-monospace ms-1">Rs 0.00</strong>
                                    </span>
                                </div>
                            </div>
                            <input type="number" name="payment_amount[]" step="0.01" class="form-control form-control-sm text-end payment-amount fw-bold font-monospace" placeholder="0.00" style="width: 105px; height: 34px; font-size:0.75rem;">
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 btnRemRefund p-0 d-flex align-items-center justify-content-center" style="width:28px; height:34px; flex-shrink:0;" title="Remove Account">
                                <i class="fas fa-times" style="font-size:0.75rem;"></i>
                            </button>
                        </div>
                    </div>
                `;
                const $newRow = $(rowHtml);
                $('#refundAccountsWrapper').append($newRow);
                initSelect2($newRow.find('.payment-account'));
            });

            $(document).on('click', '.btnRemRefund', function() {
                $(this).closest('.refund-row').remove();
                recalcSummary();
            });

            // Input Events
            $(document).on('input', '.quantity, .price, #extraDiscount', function() {
                const $row = $(this).closest('tr');

                if ($(this).hasClass('quantity')) {
                    const qtyPc = num($(this).val());
                    const maxReturnable = num($(this).attr('data-max'));

                    if (qtyPc > maxReturnable) {
                        $(this).val(maxReturnable);
                        $(this).addClass('border-danger');

                        if (!$(this).next('.text-danger').length) {
                            $(this).after('<small class="text-danger d-block">Max: ' + maxReturnable +
                                ' pieces (Purchased Qty)</small>');
                        }

                        setTimeout(() => {
                            $(this).removeClass('border-danger');
                            $(this).next('.text-danger').fadeOut(300, function() {
                                $(this).remove();
                            });
                        }, 2000);
                    }
                }

                if ($row.length) {
                    recalcRow($row);
                }
                recalcSummary();
            });

            $(document).on('input', '.payment-amount', function() {
                let totalPaid = 0;
                $('.payment-amount').each(function() {
                    totalPaid += num($(this).val());
                });

                const net = num($('#netAmount').val());
                const remainingInvoiceDue = num($('#invRemainingDue').val());
                const remainingPaidRefundable = num($('#invPaidRefundable').val());
                const dueAdjusted = Math.min(net, remainingInvoiceDue);
                const maxCashRefund = Math.min(Math.max(0, net - dueAdjusted), remainingPaidRefundable);
                const isWalking = $('#isWalkingCustomer').val() === '1';

                if (totalPaid > (maxCashRefund + 0.05)) {
                    $(this).val(maxCashRefund.toFixed(2));
                    totalPaid = maxCashRefund;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Refund Limit Exceeded',
                            text: 'Maximum eligible cash refund is Rs ' + formatMoney(maxCashRefund) + ' (advance/amount received from customer). The remaining Rs ' + formatMoney(dueAdjusted) + ' settles unpaid bill balance.',
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                }

                $('#bottomRefundPaid').text(formatMoney(totalPaid));
                let storeCredit = isWalking ? 0 : Math.max(0, maxCashRefund - totalPaid);
                $('#bottomLedgerCredit').text(formatMoney(storeCredit));
            });

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                recalcSummary();
            });

            // Form Submit Validation
            $('#saleReturnForm').on('submit', function(e) {
                let totalQty = 0;
                $('#returnItems tr').each(function() {
                    totalQty += num($(this).find('.quantity').val());
                });

                if (totalQty <= 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Items Returned',
                            text: 'Please enter return quantity for at least one item before submitting.',
                            confirmButtonColor: '#2563eb'
                        });
                    } else {
                        alert('Please enter return quantity for at least one item before submitting.');
                    }
                    return false;
                }

                const net = num($('#netAmount').val());
                const remainingInvoiceDue = num($('#invRemainingDue').val());
                const remainingPaidRefundable = num($('#invPaidRefundable').val());
                const dueAdjusted = Math.min(net, remainingInvoiceDue);
                const maxCashRefund = Math.min(Math.max(0, net - dueAdjusted), remainingPaidRefundable);
                const isWalking = $('#isWalkingCustomer').val() === '1';

                let totalPaid = 0;
                let hasSelectedAccount = false;
                $('.refund-row').each(function() {
                    const accId = $(this).find('.payment-account').val();
                    const amt = num($(this).find('.payment-amount').val());
                    if (accId && amt > 0) hasSelectedAccount = true;
                    totalPaid += amt;
                });

                if (totalPaid > (maxCashRefund + 0.05)) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Refund Limit Exceeded',
                            text: 'Cash refund cannot exceed Rs ' + formatMoney(maxCashRefund) + '. The remaining Rs ' + formatMoney(dueAdjusted) + ' adjusts the customer\'s unpaid bill.',
                            confirmButtonColor: '#2563eb'
                        });
                    } else {
                        alert('Cash refund cannot exceed Rs ' + formatMoney(maxCashRefund));
                    }
                    return false;
                }

                if (isWalking && maxCashRefund > 0) {
                    if (!hasSelectedAccount || totalPaid < (maxCashRefund - 0.05)) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Full Refund Required',
                                text: 'Walk-in customer requires 100% refund payout of Rs ' + formatMoney(maxCashRefund) + '. Please select a Cash/Bank account.',
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert('Walk-in customer requires 100% refund payment of Rs ' + formatMoney(maxCashRefund));
                        }
                        return false;
                    }
                }

                // Prevent multiple clicks
                $('#btnSubmitReturn, #btnSubmitReturnBottom').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
                return true;
            });

            // Update Partial Return Visual Indicator
            function updatePartialReturnIndicator() {
                let totalOriginalPieces = 0;
                let totalReturningPieces = 0;

                $('#returnItems tr').each(function() {
                    const $qtyInput = $(this).find('.quantity');
                    const soldQty = num($qtyInput.attr('data-max'));
                    const returningQty = num($qtyInput.val());

                    totalOriginalPieces += soldQty;
                    totalReturningPieces += returningQty;
                });

                const returnPercentage = totalOriginalPieces > 0 ? (totalReturningPieces / totalOriginalPieces * 100) : 0;

                $('#returnProgressBar').css('width', returnPercentage + '%');
                $('#returnProgressBar').attr('aria-valuenow', returnPercentage);
                $('#returnPercentage').text(returnPercentage.toFixed(1) + '%');

                if (totalReturningPieces === 0) {
                    $('#returnTypeBadge').html('<i class="fas fa-info-circle me-1"></i>No Items Selected');
                    $('#returnTypeBadge').removeClass().addClass('badge bg-light text-muted border');
                    $('#returnProgressBar').css('background', '#94a3b8');
                } else if (returnPercentage >= 100) {
                    $('#returnTypeBadge').html('<i class="fas fa-check-circle me-1"></i>Full Return');
                    $('#returnTypeBadge').removeClass().addClass('badge bg-success-subtle text-success border border-success-subtle');
                    $('#returnProgressBar').css('background', '#10b981');
                } else {
                    $('#returnTypeBadge').html('<i class="fas fa-chart-pie me-1"></i>Partial Return');
                    $('#returnTypeBadge').removeClass().addClass('badge bg-primary-subtle text-primary border border-primary-subtle');
                    $('#returnProgressBar').css('background', '#2563eb');
                }
            }

            // Initial Calculations: Auto-populate all return items so full return and financial breakdown is live immediately
            $('#btnReturnAll').trigger('click');

            // Initialize Account Balance Badges on page load
            $('.payment-account').each(function() {
                updateAccountBalanceBadge($(this));
            });

        });
    </script>
@endsection
