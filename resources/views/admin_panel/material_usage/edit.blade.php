@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />

<style>
    /* ==========================================================================
       Enterprise ERP Design System - Raw Material Issue & Consumption Edit Form
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
        margin-bottom: 1.25rem;
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
        cursor: pointer;
    }
    .btn-erp-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
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

    /* Master Information Card */
    .erp-info-card {
        background-color: #ffffff;
        border: 1.5px solid #dbeafe;
        border-radius: 12px;
        padding: 0.85rem 1.15rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.05);
    }
    .erp-filter-label {
        font-size: 0.68rem;
        font-weight: 700;
        color: #1e40af;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.3rem;
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

    /* Main ERP Card & Table */
    .erp-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
    }
    .erp-table {
        margin-bottom: 0;
        vertical-align: middle;
        font-size: 0.815rem;
    }
    .erp-table thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.70rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.6rem 0.5rem;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }
    .erp-table tbody td {
        padding: 0.45rem 0.4rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .erp-table tbody tr:hover td {
        background-color: #f8fbff;
    }

    /* Form Controls inside Table */
    .form-control-row {
        height: 32px !important;
        font-size: 0.80rem !important;
        border-radius: 6px !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: 0.2rem 0.4rem !important;
        transition: all 0.15s ease;
    }
    .form-control-row:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
        outline: none !important;
    }
    .input-readonly {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
        color: #475569 !important;
        cursor: not-allowed;
    }

    /* Badges */
    .erp-bill-tag {
        font-family: var(--bs-font-monospace, monospace);
        font-weight: 700;
        font-size: 0.85rem;
        color: #2563eb;
        background-color: #eff6ff;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        border: 1px solid #dbeafe;
        display: inline-block;
    }
    .badge-stock {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        display: inline-block;
        white-space: nowrap;
    }
    .badge-stock-has {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .badge-stock-zero {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* Pill Buttons */
    .btn-erp-pill-primary {
        background: #2563eb !important;
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
        box-shadow: 0 1px 3px rgba(37, 99, 235, 0.25) !important;
        transition: all 0.15s ease !important;
        cursor: pointer;
    }
    .btn-erp-pill-primary:hover {
        background: #1d4ed8 !important;
        transform: translateY(-1px);
        color: #ffffff !important;
    }
    .btn-erp-pill-outline {
        background: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #475569 !important;
        font-weight: 600 !important;
        font-size: 0.78rem !important;
        border-radius: 50px !important;
        height: 36px !important;
        padding: 0 16px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        transition: all 0.15s ease !important;
        text-decoration: none !important;
    }
    .btn-erp-pill-outline:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }
    .btn-erp-pill-success {
        background: #059669 !important;
        border: 1px solid #047857 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 0.78rem !important;
        border-radius: 50px !important;
        height: 36px !important;
        padding: 0 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3) !important;
        transition: all 0.15s ease !important;
        cursor: pointer;
    }
    .btn-erp-pill-success:hover {
        background: #047857 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.4) !important;
        color: #ffffff !important;
    }

    /* Bottom Summary Strip */
    .bottom-summary-strip {
        background: #ffffff;
        border: 1.5px solid #dbeafe;
        border-radius: 12px;
        padding: 0.85rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
    }

    /* Select2 Tweaks */
    .select2-container .select2-selection--single {
        height: 32px !important;
        border: 1.5px solid #bfdbfe !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        font-size: 0.80rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">

            {{-- Top Page Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                <div>
                    <h4 class="erp-title">
                        <i class="fas fa-edit text-warning"></i> Edit Raw Material Usage
                    </h4>
                    <p class="erp-subtitle">Modify issued raw material quantities, correct double entries, or remove duplicate rows</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn-erp-outline-danger" href="{{ route('material_usage.index') }}">
                        <i class="fas fa-arrow-left"></i> Usage List
                    </a>
                    <a class="btn btn-outline-info" href="{{ route('material_usage.show', $usage->id) }}" target="_blank" style="height: 34px; font-size: 0.815rem; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fas fa-file-invoice"></i> View Slip
                    </a>
                    <button type="button" class="btn-erp-primary" onclick="$('#materialUsageForm').submit();">
                        <i class="fas fa-check-circle"></i> Update Voucher
                    </button>
                </div>
            </div>

            {{-- 4 KPI Metric Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Voucher Number</div>
                            <div class="erp-kpi-value">
                                <span class="erp-bill-tag">#{{ $usage->usage_no }}</span>
                            </div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Est. Stock Remaining</div>
                            <div class="erp-kpi-value text-success" id="kpiNetTotal">0.00</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Qty to Issue</div>
                            <div class="erp-kpi-value text-warning" style="color: #d97706 !important;" id="kpiTotalQty">{{ number_format($usage->total_qty, 2) }}</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #fffbeb; color: #d97706;">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Raw Items Selected</div>
                            <div class="erp-kpi-value" style="color: #0284c7 !important;" id="kpiItemsCount">{{ $usage->items->count() }} Items</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #f0f9ff; color: #0284c7;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <form action="{{ route('material_usage.update', $usage->id) }}" method="POST" id="materialUsageForm" autocomplete="off">
                @csrf

                <!-- Hidden inputs -->
                <input type="hidden" name="warehouse_id" id="warehouseIdInput" value="{{ $usage->warehouse_id ?? $defaultWarehouse->id ?? 1 }}">
                <input type="hidden" id="editingUsageId" value="{{ $usage->id }}">

                <!-- Master Information Single-Row Card -->
                <div class="erp-info-card">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label class="erp-filter-label">
                                <i class="far fa-calendar-alt text-primary"></i> Issue Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" class="form-control erp-filter-input" value="{{ old('date', \Carbon\Carbon::parse($usage->date)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="erp-filter-label">
                                <i class="fas fa-tag text-primary"></i> Purpose / Usage Type
                            </label>
                            <input type="text" name="purpose" class="form-control erp-filter-input" placeholder="e.g. Production Consumption, Cutting, Assembling" value="{{ old('purpose', $usage->purpose) }}">
                        </div>
                        <div class="col-12 col-md-5">
                            <label class="erp-filter-label">
                                <i class="fas fa-comment-dots text-primary"></i> Remarks / Instructions (Optional)
                            </label>
                            <input type="text" name="remarks" class="form-control erp-filter-input" placeholder="Additional notes or job reference..." value="{{ old('remarks', $usage->remarks) }}">
                        </div>
                    </div>
                </div>

                <!-- Items Table Panel -->
                <div class="card erp-main-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <i class="fas fa-list-check text-primary"></i> Raw Materials in this Issue Voucher (<span id="itemsRowCount" class="text-primary fw-bold">{{ $usage->items->count() }}</span>)
                        </h6>
                        <div>
                            <button type="button" class="btn btn-erp-pill-primary" id="btnAddRow">
                                <i class="fas fa-plus"></i> Add Row
                            </button>
                        </div>
                    </div>

                    <div class="erp-table-responsive table-responsive">
                        <table class="table erp-table mb-0" id="usageItemsTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th style="min-width: 250px;" class="text-start ps-3">RAW MATERIAL / COMPONENT <span class="text-danger">*</span></th>
                                    <th style="width: 100px;" class="text-center">CODE</th>
                                    <th style="width: 130px;" class="text-center">OPENING STOCK</th>
                                    <th style="width: 110px;" class="text-center">QTY TO ISSUE <span class="text-danger">*</span></th>
                                    <th style="width: 70px;" class="text-center">UNIT</th>
                                    <th style="width: 130px;" class="text-center">REMAINING STOCK</th>
                                    <th style="min-width: 160px;" class="text-start ps-2">NOTES / STAGE</th>
                                    <th style="width: 45px;" class="text-center">×</th>
                                </tr>
                            </thead>
                            <tbody id="usageTableBody">
                                @forelse($usage->items as $index => $item)
                                    @php
                                        // Product effective stock = current stock + already issued in this usage voucher
                                        $currentProductStock = (float)($item->product->warehouseStocks->sum('total_pieces') ?? 0);
                                        $alreadyIssuedHere = (float)($usageItemQtyMap[$item->product_id] ?? 0);
                                        $effectiveStock = $currentProductStock + $alreadyIssuedHere;
                                        $unit = $item->unit_name ?: ($item->product->unit->name ?? $item->product->unit->unit_name ?? 'Pcs');
                                        $remStock = max(0, $effectiveStock - $item->qty_used);
                                    @endphp
                                    <tr class="item-row" data-row-id="{{ $loop->iteration }}">
                                        <td class="text-center font-weight-bold text-muted row-index" style="font-size: 0.75rem;">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="ps-2">
                                            <select name="product_id[]" class="form-control form-control-row product-select" required style="width: 100%;">
                                                <option value="">-- Choose Raw Material --</option>
                                                @foreach($rawMaterials as $rm)
                                                    @php
                                                        $stk = (float)($rm->warehouse_stocks_sum_total_pieces ?? 0);
                                                        $issuedHere = (float)($usageItemQtyMap[$rm->id] ?? 0);
                                                        $effStk = $stk + $issuedHere;
                                                        $u = $rm->unit->name ?? $rm->unit->unit_name ?? 'Pcs';
                                                    @endphp
                                                    <option value="{{ $rm->id }}" 
                                                            data-code="{{ $rm->item_code ?? '-' }}" 
                                                            data-stock="{{ $effStk }}" 
                                                            data-unit="{{ $u }}"
                                                            {{ $rm->id == $item->product_id ? 'selected' : '' }}>
                                                        {{ $rm->item_name }} &nbsp;—&nbsp; [ Stock: {{ number_format($effStk, 2) }} {{ $u }} ]
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-row input-readonly text-center row-code" readonly tabindex="-1" value="{{ $item->product->item_code ?? '-' }}" placeholder="-">
                                        </td>
                                        <td class="text-center">
                                            @if($effectiveStock > 0)
                                                <span class="badge-stock badge-stock-has row-stock-badge">
                                                    <i class="fas fa-check-circle mr-1 text-success"></i> {{ number_format($effectiveStock, 2) }} {{ $unit }}
                                                </span>
                                            @else
                                                <span class="badge-stock badge-stock-zero row-stock-badge">
                                                    <i class="fas fa-times-circle mr-1 text-danger"></i> 0.00 {{ $unit }}
                                                </span>
                                            @endif
                                            <input type="hidden" class="row-stock-val" value="{{ $effectiveStock }}">
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0.01" name="qty_used[]" class="form-control form-control-row text-center font-weight-bold row-qty" placeholder="1.00" value="{{ (float)$item->qty_used }}" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-row input-readonly text-center row-unit" readonly tabindex="-1" value="{{ $unit }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-row input-readonly text-center font-weight-bold row-rem-stock" style="color: #059669 !important;" readonly tabindex="-1" value="{{ number_format($remStock, 2) }}">
                                        </td>
                                        <td>
                                            <input type="text" name="notes[]" class="form-control form-control-row row-notes" value="{{ $item->notes }}" placeholder="Notes (optional)...">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-row" style="height: 28px; line-height: 24px; border-radius: 6px;" title="Remove duplicate/wrong row">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="item-row" data-row-id="1">
                                        <td class="text-center font-weight-bold text-muted row-index" style="font-size: 0.75rem;">1</td>
                                        <td class="ps-2">
                                            <select name="product_id[]" class="form-control form-control-row product-select" required style="width: 100%;">
                                                <option value="">-- Choose Raw Material --</option>
                                                @foreach($rawMaterials as $rm)
                                                    @php
                                                        $stk = (float)($rm->warehouse_stocks_sum_total_pieces ?? 0);
                                                        $u = $rm->unit->name ?? $rm->unit->unit_name ?? 'Pcs';
                                                    @endphp
                                                    <option value="{{ $rm->id }}" data-code="{{ $rm->item_code ?? '-' }}" data-stock="{{ $stk }}" data-unit="{{ $u }}">
                                                        {{ $rm->item_name }} &nbsp;—&nbsp; [ Stock: {{ number_format($stk, 2) }} {{ $u }} ]
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-row input-readonly text-center row-code" readonly tabindex="-1" placeholder="-">
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-stock badge-stock-zero row-stock-badge">
                                                <i class="fas fa-minus-circle mr-1"></i> --
                                            </span>
                                            <input type="hidden" class="row-stock-val" value="0">
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0.01" name="qty_used[]" class="form-control form-control-row text-center font-weight-bold row-qty" placeholder="1.00" value="1.00" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-row input-readonly text-center row-unit" readonly tabindex="-1" value="Pcs">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-row input-readonly text-center font-weight-bold row-rem-stock" style="color: #059669 !important;" readonly tabindex="-1" value="0.00">
                                        </td>
                                        <td>
                                            <input type="text" name="notes[]" class="form-control form-control-row row-notes" placeholder="Notes (optional)...">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-row" style="height: 28px; line-height: 24px; border-radius: 6px;" title="Remove row">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end font-weight-bold pe-3">Grand Total:</td>
                                    <td class="text-center font-weight-bold text-dark font-monospace" id="footerTotalQty" style="font-size: 0.85rem;">0.00</td>
                                    <td></td>
                                    <td class="text-center font-weight-bold font-monospace text-success" id="footerTotalRemStock" style="font-size: 0.85rem;">0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Bottom Summary & Action Strip -->
                <div class="bottom-summary-strip">
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted font-weight-bold small text-uppercase">Summary:</span>
                        <span class="badge bg-light border px-2 py-1.5 text-secondary" style="font-size: 0.8rem; border-radius: 6px;">
                            Items: <strong class="text-dark" id="summaryItemCount">{{ $usage->items->count() }}</strong>
                        </span>
                        <span class="badge bg-light border px-2 py-1.5 text-secondary" style="font-size: 0.8rem; border-radius: 6px;">
                            Total Qty: <strong class="text-warning" style="color: #d97706 !important;" id="summaryQty">{{ number_format($usage->total_qty, 2) }}</strong>
                        </span>
                        <span class="badge bg-light border px-2 py-1.5 text-secondary" style="font-size: 0.8rem; border-radius: 6px;">
                            Est. Stock Remaining: <strong class="text-success" style="color: #059669 !important;" id="summaryCost">0.00</strong>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('material_usage.index') }}" class="btn-erp-pill-outline">
                            Cancel
                        </a>
                        <button type="submit" class="btn-erp-pill-success" id="btnSubmit">
                            <i class="fas fa-check-circle"></i> Update Material Usage &amp; Adjust Stock
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    const rawMaterialsList = @json($rawMaterialsJson ?? []);
    const currentUsageId = $('#editingUsageId').val();
    let rowCounter = $('#usageTableBody tr.item-row').length || 1;

    // Build raw material option HTML for dynamically added rows
    function buildOptionsHtml(selectedId = null) {
        let html = '<option value="">-- Choose Raw Material --</option>';
        rawMaterialsList.forEach(item => {
            let isSelected = (selectedId && selectedId == item.id) ? 'selected' : '';
            html += `<option value="${item.id}" 
                             data-code="${item.code}" 
                             data-stock="${item.stock}" 
                             data-unit="${item.unit}" 
                             data-cost="${item.cost}" 
                             ${isSelected}>
                        ${item.name} &nbsp;—&nbsp; [ Stock: ${item.stock.toFixed(2)} ${item.unit} ]
                    </option>`;
        });
        return html;
    }

    // Helper to safely initialize Select2
    function initSelect2(elem) {
        if ($.fn.select2) {
            elem.select2({
                placeholder: '-- Choose Raw Material --',
                allowClear: true,
                width: '100%'
            });
        }
    }

    // Initialize Select2 on existing row(s)
    $('#usageTableBody tr.item-row').each(function() {
        initSelect2($(this).find('.product-select'));
    });

    // Add a new row to the table
    window.addUsageRow = function() {
        rowCounter++;
        let tbody = $('#usageTableBody');

        let rowHtml = `
            <tr class="item-row" data-row-id="${rowCounter}">
                <td class="text-center font-weight-bold text-muted row-index" style="font-size: 0.75rem;">
                    ${rowCounter}
                </td>
                <td class="ps-2">
                    <select name="product_id[]" class="form-control form-control-row product-select" required style="width: 100%;">
                        ${buildOptionsHtml()}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-row input-readonly text-center row-code" readonly tabindex="-1" placeholder="-">
                </td>
                <td class="text-center">
                    <span class="badge-stock badge-stock-zero row-stock-badge">
                        <i class="fas fa-minus-circle mr-1"></i> --
                    </span>
                    <input type="hidden" class="row-stock-val" value="0">
                </td>
                <td>
                    <input type="number" step="any" min="0.01" name="qty_used[]" class="form-control form-control-row text-center font-weight-bold row-qty" placeholder="1.00" value="1.00" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-row input-readonly text-center row-unit" readonly tabindex="-1" value="Pcs">
                </td>
                <td>
                    <input type="text" class="form-control form-control-row input-readonly text-center font-weight-bold row-rem-stock" style="color: #059669 !important;" readonly tabindex="-1" value="0.00">
                </td>
                <td>
                    <input type="text" name="notes[]" class="form-control form-control-row row-notes" placeholder="Notes (optional)...">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-row" style="height: 28px; line-height: 24px; border-radius: 6px;" title="Remove row">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;

        tbody.append(rowHtml);
        let newRow = tbody.find('tr.item-row').last();
        initSelect2(newRow.find('.product-select'));
        updateRowIndices();
        recalculateTotals();
    };

    // Add Row Click Event
    $(document).on('click', '#btnAddRow', function(e) {
        e.preventDefault();
        window.addUsageRow();
    });

    // Remove Row Click (removes item / double issue row)
    $(document).on('click', '.btn-remove-row', function() {
        if ($('#usageTableBody tr.item-row').length <= 1) {
            alert('At least one raw material row must remain in the issue voucher.');
            return;
        }
        $(this).closest('tr').remove();
        updateRowIndices();
        recalculateTotals();
    });

    // Product Selection Event on Table Row
    $(document).on('change', '.product-select', function() {
        let row = $(this).closest('tr');
        let selectedOption = $(this).find(':selected');
        let productId = $(this).val();

        if (!productId) {
            row.find('.row-code').val('-');
            row.find('.row-stock-badge').removeClass('badge-stock-has').addClass('badge-stock-zero').html('<i class="fas fa-minus-circle mr-1"></i> --');
            row.find('.row-stock-val').val(0);
            row.find('.row-unit').val('Pcs');
            row.find('.row-rem-stock').val('0.00');
            row.find('.row-qty').removeAttr('max');
            recalculateTotals();
            return;
        }

        let code = selectedOption.attr('data-code') || selectedOption.data('code') || '-';
        let stock = parseFloat(selectedOption.attr('data-stock') !== undefined ? selectedOption.attr('data-stock') : (selectedOption.data('stock') || 0));
        let unit = selectedOption.attr('data-unit') || selectedOption.data('unit') || 'Pcs';

        row.find('.row-code').val(code);
        row.find('.row-stock-val').val(stock);
        row.find('.row-unit').val(unit);
        row.find('.row-qty').attr('max', stock);

        // Update Stock Badge in Row
        let badge = row.find('.row-stock-badge');
        if (stock > 0) {
            badge.removeClass('badge-stock-zero').addClass('badge-stock-has')
                 .html('<i class="fas fa-check-circle mr-1 text-success"></i> ' + stock.toFixed(2) + ' ' + unit);
        } else {
            badge.removeClass('badge-stock-has').addClass('badge-stock-zero')
                 .html('<i class="fas fa-times-circle mr-1 text-danger"></i> 0.00 ' + unit);
        }

        calculateRowTotal(row);

        // Background AJAX live stock check with usage_id
        $.ajax({
            url: "{{ route('material_usage.get_stock') }}",
            type: 'GET',
            data: { 
                product_id: productId,
                usage_id: currentUsageId
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    let liveStock = parseFloat(res.available_stock || 0);
                    let liveUnit = res.unit_name || unit;
                    row.find('.row-stock-val').val(liveStock);
                    row.find('.row-qty').attr('max', liveStock);
                    let b = row.find('.row-stock-badge');
                    if (liveStock > 0) {
                        b.removeClass('badge-stock-zero').addClass('badge-stock-has')
                         .html('<i class="fas fa-check-circle mr-1 text-success"></i> ' + liveStock.toFixed(2) + ' ' + liveUnit);
                    } else {
                        b.removeClass('badge-stock-has').addClass('badge-stock-zero')
                         .html('<i class="fas fa-times-circle mr-1 text-danger"></i> 0.00 ' + liveUnit);
                    }
                    calculateRowTotal(row);
                }
            }
        });
    });

    // Qty change event
    $(document).on('input change', '.row-qty', function() {
        let row = $(this).closest('tr');
        calculateRowTotal(row);
    });

    // Calculate Row Total & Validate Stock
    function calculateRowTotal(row) {
        let qty = parseFloat(row.find('.row-qty').val()) || 0;
        let stock = parseFloat(row.find('.row-stock-val').val()) || 0;
        let qtyInput = row.find('.row-qty');

        if (stock > 0 && qty > stock) {
            qtyInput.css({ 'border-color': '#dc2626', 'background-color': '#fef2f2', 'color': '#dc2626' });
            qtyInput.attr('title', `Requested quantity (${qty}) exceeds available stock (${stock})`);
        } else {
            qtyInput.css({ 'border-color': '#cbd5e1', 'background-color': '#ffffff', 'color': '#0f172a' });
            qtyInput.removeAttr('title');
        }

        let remStock = Math.max(0, stock - qty);
        row.find('.row-rem-stock').val(remStock.toFixed(2));
        recalculateTotals();
    }

    // Recalculate Table Footer, KPI Cards & Summary Bar
    function recalculateTotals() {
        let sumQty = 0;
        let sumRemStock = 0;

        $('#usageTableBody tr.item-row').each(function() {
            let q = parseFloat($(this).find('.row-qty').val()) || 0;
            let r = parseFloat($(this).find('.row-rem-stock').val()) || 0;
            sumQty += q;
            sumRemStock += r;
        });

        let formattedQty = sumQty.toFixed(2);
        let formattedRemStock = sumRemStock.toFixed(2);

        // Footer
        $('#footerTotalQty').text(formattedQty);
        $('#footerTotalRemStock').text(formattedRemStock);

        // Summary Bar
        $('#summaryQty').text(formattedQty);
        $('#summaryCost').text(formattedRemStock);

        // Top KPI Cards
        $('#kpiTotalQty').text(formattedQty);
        $('#kpiNetTotal').text(formattedRemStock);
    }

    // Re-index rows
    function updateRowIndices() {
        let count = 0;
        $('#usageTableBody tr.item-row').each(function() {
            count++;
            $(this).find('.row-index').text(count);
        });
        $('#itemsRowCount').text(count);
        $('#summaryItemCount').text(count);
        $('#kpiItemsCount').text(count + (count === 1 ? ' Item' : ' Items'));
    }

    // Form Submission Validation
    $('#materialUsageForm').on('submit', function(e) {
        let valid = true;
        let rowsCount = 0;

        $('#usageTableBody tr.item-row').each(function() {
            rowsCount++;
            let pid = $(this).find('.product-select').val();
            let qty = parseFloat($(this).find('.row-qty').val()) || 0;
            let stock = parseFloat($(this).find('.row-stock-val').val()) || 0;

            if (!pid) {
                alert(`Row #${rowsCount}: Please select a raw material.`);
                valid = false;
                return false;
            }
            if (qty <= 0) {
                alert(`Row #${rowsCount}: Quantity must be greater than zero.`);
                valid = false;
                return false;
            }
            if (stock > 0 && qty > stock) {
                alert(`Row #${rowsCount}: Quantity (${qty}) exceeds available stock (${stock}). Please reduce the quantity.`);
                valid = false;
                return false;
            }
        });

        if (!valid) {
            e.preventDefault();
            return false;
        }

        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating & Adjusting Stock...');
    });

    // Run initial calculation on page load
    $('#usageTableBody tr.item-row').each(function() {
        calculateRowTotal($(this));
    });
    updateRowIndices();
    recalculateTotals();
});
</script>
@endsection
