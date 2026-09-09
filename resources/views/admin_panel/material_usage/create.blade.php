@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />

<style>
    /* ==========================================================================
       Enterprise ERP Design System - Raw Material Issue & Consumption Form
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

    /* KPI Metric Cards (Exact Sales UI) */
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
        padding: 0.25rem 0.65rem !important;
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
        border: 1.5px solid #dbeafe;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
        padding: 1.15rem;
        margin-bottom: 1.25rem;
    }
    .erp-table-responsive {
        border: 1.5px solid #bfdbfe !important;
        border-radius: 8px !important;
        width: 100%;
        overflow-x: auto;
        background-color: #ffffff;
    }
    .erp-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-bottom: 0;
    }
    .erp-table thead th {
        background-color: #eff6ff !important;
        color: #1e40af !important;
        font-weight: 700 !important;
        font-size: 0.70rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        padding: 0.55rem 0.4rem !important;
        border: 1px solid #bfdbfe !important;
        border-bottom: 2px solid #60a5fa !important;
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }
    .erp-table tbody td {
        padding: 0.4rem 0.35rem !important;
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
    .erp-table tfoot td {
        background-color: #eff6ff !important;
        border-top: 2px solid #60a5fa !important;
        border: 1px solid #bfdbfe !important;
        padding: 0.55rem 0.5rem !important;
        font-weight: 700 !important;
        color: #1e40af !important;
        font-size: 0.82rem !important;
    }

    /* Bill / Voucher Tag */
    .erp-bill-tag {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        font-size: 0.80rem;
        color: #2563eb;
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        display: inline-block;
    }

    /* Table Row Inputs */
    .form-control-row {
        height: 32px !important;
        border: 1.5px solid #bfdbfe !important;
        border-radius: 6px !important;
        font-size: 0.80rem !important;
        padding: 2px 6px !important;
        font-weight: 600 !important;
        width: 100%;
        color: #0f172a !important;
        background-color: #ffffff !important;
        transition: all 0.15s ease;
    }
    .form-control-row:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
        outline: none !important;
    }
    .input-readonly {
        background-color: #eff6ff !important;
        border-color: #bfdbfe !important;
        color: #1e40af !important;
        font-weight: 700 !important;
        cursor: not-allowed;
        font-family: ui-monospace, monospace;
    }

    /* Stock Badges */
    .badge-stock {
        font-size: 0.72rem;
        padding: 3px 6px;
        border-radius: 5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
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
        text-decoration: none;
        cursor: pointer;
    }
    .btn-erp-pill-outline:hover {
        background-color: #eff6ff !important;
        border-color: #3b82f6 !important;
        color: #1d4ed8 !important;
    }

    .btn-erp-pill-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
        border: 1px solid #047857 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 0.80rem !important;
        border-radius: 50px !important;
        height: 36px !important;
        padding: 0 20px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3) !important;
        transition: all 0.15s ease !important;
        cursor: pointer;
    }
    .btn-erp-pill-success:hover {
        background: #059669 !important;
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
                        <i class="fas fa-boxes-packing text-primary"></i> Issue Raw Material
                    </h4>
                    <p class="erp-subtitle">Record raw material consumption, warehouse stock deductions, and production issues</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn-erp-outline-danger" href="{{ route('material_usage.index') }}">
                        <i class="fas fa-arrow-left"></i> Usage List
                    </a>
                    <button type="button" class="btn-erp-primary" onclick="$('#materialUsageForm').submit();">
                        <i class="fas fa-check-circle"></i> Save Voucher
                    </button>
                </div>
            </div>

            {{-- 4 KPI Metric Cards (Exact Sales UI Layout) --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Voucher Number</div>
                            <div class="erp-kpi-value">
                                <span class="erp-bill-tag">#{{ $nextUsageNo }}</span>
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
                            <div class="erp-kpi-label">Total Net Value</div>
                            <div class="erp-kpi-value text-success" id="kpiNetTotal">Rs. 0.00</div>
                        </div>
                        <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="erp-kpi-card">
                        <div>
                            <div class="erp-kpi-label">Total Quantity</div>
                            <div class="erp-kpi-value text-warning" style="color: #d97706 !important;" id="kpiTotalQty">0.00</div>
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
                            <div class="erp-kpi-value" style="color: #0284c7 !important;" id="kpiItemsCount">1 Item</div>
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

            <form action="{{ route('material_usage.store') }}" method="POST" id="materialUsageForm" autocomplete="off">
                @csrf

                <!-- Auto Warehouse Hidden -->
                <input type="hidden" name="warehouse_id" id="warehouseIdInput" value="{{ $defaultWarehouse->id ?? 1 }}">

                <!-- Master Information Single-Row Card (Exact Sales UI Style) -->
                <div class="erp-info-card">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label class="erp-filter-label">
                                <i class="far fa-calendar-alt text-primary"></i> Issue Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" class="form-control erp-filter-input" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="erp-filter-label">
                                <i class="fas fa-tag text-primary"></i> Purpose / Usage Type
                            </label>
                            <input type="text" name="purpose" class="form-control erp-filter-input" placeholder="e.g. Production Consumption, Cutting, Assembling" value="{{ old('purpose', 'Production Consumption') }}">
                        </div>
                        <div class="col-12 col-md-5">
                            <label class="erp-filter-label">
                                <i class="fas fa-comment-dots text-primary"></i> Remarks / Instructions (Optional)
                            </label>
                            <input type="text" name="remarks" class="form-control erp-filter-input" placeholder="Additional notes or job reference..." value="{{ old('remarks') }}">
                        </div>
                    </div>
                </div>

                <!-- Items Table Panel (Exact Enterprise ERP Table) -->
                <div class="card erp-main-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <i class="fas fa-list-check text-primary"></i> Raw Materials to Issue (<span id="itemsRowCount" class="text-primary fw-bold">1</span>)
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
                                    <th style="min-width: 280px;" class="text-start ps-3">RAW MATERIAL / COMPONENT <span class="text-danger">*</span></th>
                                    <th style="width: 110px;" class="text-center">CODE</th>
                                    <th style="width: 140px;" class="text-center">AVAILABLE STOCK</th>
                                    <th style="width: 110px;" class="text-center">QTY TO ISSUE <span class="text-danger">*</span></th>
                                    <th style="width: 70px;" class="text-center">UNIT</th>
                                    <th style="width: 110px;" class="text-end pe-3">UNIT COST</th>
                                    <th style="width: 130px;" class="text-end pe-3">TOTAL VALUE</th>
                                    <th style="min-width: 160px;" class="text-start ps-2">NOTES / STAGE</th>
                                    <th style="width: 45px;" class="text-center">×</th>
                                </tr>
                            </thead>
                            <tbody id="usageTableBody">
                                <tr class="item-row" data-row-id="1">
                                    <td class="text-center font-weight-bold text-muted row-index" style="font-size: 0.75rem;">1</td>
                                    <td class="ps-2">
                                        <select name="product_id[]" class="form-control form-control-row product-select" required style="width: 100%;">
                                            <option value="">-- Choose Raw Material --</option>
                                            @foreach($rawMaterials as $rm)
                                                @php
                                                    $stk = (float)($rm->warehouse_stocks_sum_total_pieces ?? 0);
                                                    $u = $rm->unit->name ?? $rm->unit->unit_name ?? 'Pcs';
                                                    $c = (float)($rm->purchase_price_per_piece ?? 0);
                                                @endphp
                                                <option value="{{ $rm->id }}" 
                                                        data-code="{{ $rm->item_code ?? '-' }}" 
                                                        data-stock="{{ $stk }}" 
                                                        data-unit="{{ $u }}" 
                                                        data-cost="{{ $c }}">
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
                                        <input type="text" class="form-control form-control-row input-readonly text-end row-cost" readonly tabindex="-1" value="0.00">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-row input-readonly text-end font-weight-bold row-total" style="color: #047857 !important;" readonly tabindex="-1" value="0.00">
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
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end font-weight-bold pe-3">Grand Total:</td>
                                    <td class="text-center font-weight-bold text-dark font-monospace" id="footerTotalQty" style="font-size: 0.85rem;">0.00</td>
                                    <td></td>
                                    <td class="text-end font-weight-bold pe-3">Total Cost:</td>
                                    <td class="text-end font-weight-bold font-monospace pe-3" id="footerTotalCost" style="color: #047857; font-size: 0.88rem;">Rs. 0.00</td>
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
                            Items: <strong class="text-dark" id="summaryItemCount">1</strong>
                        </span>
                        <span class="badge bg-light border px-2 py-1.5 text-secondary" style="font-size: 0.8rem; border-radius: 6px;">
                            Total Qty: <strong class="text-warning" style="color: #d97706 !important;" id="summaryQty">0.00</strong>
                        </span>
                        <span class="badge bg-light border px-2 py-1.5 text-secondary" style="font-size: 0.8rem; border-radius: 6px;">
                            Net Cost: <strong class="text-success" style="color: #059669 !important;" id="summaryCost">Rs. 0.00</strong>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('material_usage.index') }}" class="btn-erp-pill-outline">
                            Cancel
                        </a>
                        <button type="submit" class="btn-erp-pill-success" id="btnSubmit">
                            <i class="fas fa-check-circle"></i> Record Material Usage &amp; Deduct Stock
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
                <td>
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
                    <input type="number" step="any" min="0.01" name="qty_used[]" class="form-control form-control-row text-right font-weight-bold row-qty" placeholder="1.00" value="1.00" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-row input-readonly text-center row-unit" readonly tabindex="-1" value="Pcs">
                </td>
                <td>
                    <input type="text" class="form-control form-control-row input-readonly text-right row-cost" readonly tabindex="-1" value="0.00">
                </td>
                <td>
                    <input type="text" class="form-control form-control-row input-readonly text-right font-weight-bold text-dark row-total" readonly tabindex="-1" value="0.00">
                </td>
                <td>
                    <input type="text" name="notes[]" class="form-control form-control-row row-notes" placeholder="Notes (optional)...">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-row" style="height: 28px; line-height: 24px; border-radius: 6px;">
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

    // Add Row Click Event (both ID and delegation)
    $(document).on('click', '#btnAddRow', function(e) {
        e.preventDefault();
        window.addUsageRow();
    });

    // Remove Row Click
    $(document).on('click', '.btn-remove-row', function() {
        if ($('#usageTableBody tr.item-row').length <= 1) {
            alert('At least one row must remain in the issue voucher.');
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
            row.find('.row-cost').val('0.00');
            row.find('.row-total').val('0.00');
            row.find('.row-qty').removeAttr('max');
            recalculateTotals();
            return;
        }

        let code = selectedOption.attr('data-code') || selectedOption.data('code') || '-';
        let stock = parseFloat(selectedOption.attr('data-stock') !== undefined ? selectedOption.attr('data-stock') : (selectedOption.data('stock') || 0));
        let unit = selectedOption.attr('data-unit') || selectedOption.data('unit') || 'Pcs';
        let cost = parseFloat(selectedOption.attr('data-cost') !== undefined ? selectedOption.attr('data-cost') : (selectedOption.data('cost') || 0));

        row.find('.row-code').val(code);
        row.find('.row-stock-val').val(stock);
        row.find('.row-unit').val(unit);
        row.find('.row-cost').val(cost.toFixed(2));
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

        // Background AJAX live stock check
        $.ajax({
            url: "{{ route('material_usage.get_stock') }}",
            type: 'GET',
            data: { product_id: productId },
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
        let cost = parseFloat(row.find('.row-cost').val()) || 0;
        let qtyInput = row.find('.row-qty');

        if (stock > 0 && qty > stock) {
            qtyInput.css({ 'border-color': '#dc2626', 'background-color': '#fef2f2', 'color': '#dc2626' });
            qtyInput.attr('title', `Requested quantity (${qty}) exceeds available stock (${stock})`);
        } else {
            qtyInput.css({ 'border-color': '#cbd5e1', 'background-color': '#ffffff', 'color': '#0f172a' });
            qtyInput.removeAttr('title');
        }

        let total = qty * cost;
        row.find('.row-total').val(total.toFixed(2));
        recalculateTotals();
    }

    // Recalculate Table Footer, KPI Cards & Summary Bar
    function recalculateTotals() {
        let sumQty = 0;
        let sumCost = 0;

        $('#usageTableBody tr.item-row').each(function() {
            let q = parseFloat($(this).find('.row-qty').val()) || 0;
            let t = parseFloat($(this).find('.row-total').val()) || 0;
            sumQty += q;
            sumCost += t;
        });

        let formattedCost = 'Rs. ' + sumCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        let formattedQty = sumQty.toFixed(2);

        // Footer
        $('#footerTotalQty').text(formattedQty);
        $('#footerTotalCost').text(formattedCost);

        // Summary Bar
        $('#summaryQty').text(formattedQty);
        $('#summaryCost').text(formattedCost);

        // Top KPI Cards
        $('#kpiTotalQty').text(formattedQty);
        $('#kpiNetTotal').text(formattedCost);
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

        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Recording & Deducting Stock...');
    });

    updateRowIndices();
    recalculateTotals();
});
</script>
@endsection