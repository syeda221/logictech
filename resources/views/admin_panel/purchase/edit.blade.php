@extends('admin_panel.layout.app')

@section('content')
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">

    <style>
        /* 💎 PREMIUM MODERN ERP THEME FOR PURCHASE EDIT 💎 */
        body {
            background-color: #f8fafc;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Containers & Cards */
        .main-container {
            border: 2px solid #475569 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05) !important;
            background-color: #ffffff !important;
            padding: 18px !important;
            font-size: .85rem;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        .card-panel {
            background-color: #f8fafc !important;
            border: 2px solid #cbd5e1 !important;
            border-radius: 10px !important;
            padding: 20px !important;
            height: 100%;
            transition: all 0.2s;
        }

        .card-panel:hover {
            border-color: #94a3b8 !important;
        }

        .summary-card {
            background-color: #f1f5f9 !important;
            border: 2px solid #cbd5e1 !important;
            border-radius: 10px !important;
            padding: 20px !important;
        }

        /* Bold Section Titles */
        .section-title {
            font-weight: 800 !important;
            text-transform: uppercase;
            font-size: 0.8rem !important;
            letter-spacing: 1px !important;
            color: #1e293b !important;
            margin-bottom: 16px !important;
            border-left: 4px solid #2563eb !important;
            padding-left: 10px !important;
        }

        /* Clean inputs with bold borders */
        .form-control,
        .form-select,
        .select2-container--default .select2-selection--single {
            border: 2px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            transition: all 0.2s ease-in-out !important;
            height: auto !important;
            font-size: 0.85rem !important;
        }

        .form-control:focus,
        .form-select:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15) !important;
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

        /* Buttons */
        .btn-action-primary {
            background-color: #2563eb !important;
            border: 2px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 8px 20px !important;
            transition: all 0.2s;
            font-size: 0.85rem !important;
        }

        .btn-action-primary:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px);
            color: #ffffff !important;
        }

        .btn-action-secondary {
            background-color: #ffffff !important;
            border: 2px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 8px 20px !important;
            transition: all 0.2s;
            font-size: 0.85rem !important;
        }

        .btn-action-secondary:hover {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }

        /* Transaction Grid / Table */
        .table-responsive {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            overflow-x: hidden !important;
            overflow-y: visible !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03) !important;
            min-height: 150px;
            background-color: #ffffff;
        }

        .sales-table {
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
            width: 100% !important;
            table-layout: auto !important;
        }

        .sales-table thead th {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 11px !important;
            letter-spacing: 0.5px;
            padding: 10px 8px !important;
            border: 1px solid #cbd5e1 !important;
            border-bottom: 2px solid #94a3b8 !important;
            vertical-align: middle !important;
            text-align: center;
        }

        .sales-table tbody td {
            border: 1px solid #cbd5e1 !important;
            padding: 4px 6px !important;
            background-color: #ffffff;
            vertical-align: middle !important;
        }

        .col-product { width: 36%; }
        .col-unit { width: 10%; text-align: center; }
        .col-qty { width: 12%; text-align: center; }
        .col-price { width: 13%; text-align: right; }
        .col-disc { width: 8%; text-align: right; }
        .col-disc-amt { width: 9%; text-align: right; }
        .col-amount { width: 12%; text-align: right; }
        .col-action { width: 4%; text-align: center; }
    </style>

    <div class="container-fluid py-2 px-1">
        <div class="main-container bg-white border shadow-sm mx-auto p-3 rounded-3">

            <div id="alertBox" class="alert d-none mb-3" role="alert"></div>

            <form id="purchaseForm" action="{{ route('purchase.update', $purchase->id) }}" method="POST" autocomplete="off">
                @csrf
                @method('PUT')
                <input type="hidden" id="action" name="action" value="{{ $purchase->status_purchase === 'approved' ? 'approved' : 'save_only' }}">

                {{-- TOP HEADER & INVOICE / VENDOR CARD --}}
                <div class="card-panel shadow-sm mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('Purchase.home') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <h4 class="header-text text-dark fw-bold mb-0 ms-2">
                                <i class="fas fa-edit text-primary me-2"></i>LogicTech — Edit Purchase Order (#{{ $purchase->invoice_no }})
                            </h4>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if ($purchase->status_purchase === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fs-6 fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1"></i>Status: Approved
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 fs-6 fw-semibold">
                                    <i class="bi bi-clock-history me-1"></i>Status: Draft
                                </span>
                            @endif
                            <span class="badge bg-light text-secondary border px-3 py-2 fs-6 fw-semibold" id="entryDate">
                                Date: {{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') : date('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">System No.</label>
                            <input type="text" class="form-control input-readonly" name="invoice_no" value="{{ $purchase->invoice_no }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">Vendor Inv# / Challan</label>
                            <input type="text" class="form-control" name="purchase_order_no" value="{{ $purchase->purchase_order_no }}" placeholder="Manual Ref">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">Procurement Type</label>
                            <select class="form-select" name="purchase_type" id="purchaseTypeSelect">
                                <option value="local" {{ ($purchase->purchase_type ?? 'local') === 'local' ? 'selected' : '' }}>🇵🇰 Local Purchase</option>
                                <option value="import" {{ ($purchase->purchase_type ?? '') === 'import' ? 'selected' : '' }}>🌐 Import Procurement</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1 text-muted small">Select Vendor / Supplier</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="flex-grow-1">
                                    <select class="form-select select2" id="vendorSelect" name="vendor_id">
                                        <option value="" disabled>Select Vendor</option>
                                        @foreach ($Vendor as $v)
                                            <option value="{{ $v->id }}" data-phone="{{ $v->phone }}" data-address="{{ $v->address }}" {{ $v->id == $purchase->vendor_id ? 'selected' : '' }}>{{ $v->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addVendorModal" style="padding: 0.38rem 0.75rem;" title="Add New Vendor">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-bold mb-1 text-muted small">Date</label>
                            <input type="text" name="purchase_date" class="form-control datepicker-custom" value="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : date('Y-m-d') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">Remarks / Notes</label>
                            <input type="text" class="form-control" name="note" id="remarks" value="{{ $purchase->note }}" placeholder="Optional notes...">
                        </div>
                    </div>

                    <!-- IMPORT PROCUREMENT FIELDS (Shown only when Import selected) -->
                    <div id="importProcurementFields" class="mt-3 p-3 border rounded-3 bg-light {{ ($purchase->purchase_type ?? '') === 'import' ? '' : 'd-none' }}">
                        <div class="fw-bold text-primary small mb-2"><i class="fas fa-ship me-1"></i> Import Shipment &amp; Foreign Currency Details</div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label fw-bold mb-1 text-muted small">Currency</label>
                                <select name="currency" class="form-select form-select-sm">
                                    @php $curr = $purchase->currency ?? 'PKR'; @endphp
                                    <option value="PKR" {{ $curr === 'PKR' ? 'selected' : '' }}>PKR (Rs)</option>
                                    <option value="USD" {{ $curr === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ $curr === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="CNY" {{ $curr === 'CNY' ? 'selected' : '' }}>CNY (¥)</option>
                                    <option value="AED" {{ $curr === 'AED' ? 'selected' : '' }}>AED</option>
                                    <option value="GBP" {{ $curr === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold mb-1 text-muted small">Exchange Rate</label>
                                <input type="number" step="0.000001" name="exchange_rate" class="form-control form-control-sm" value="{{ $purchase->exchange_rate ?? '1.000000' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold mb-1 text-muted small">Proforma Inv #</label>
                                <input type="text" name="proforma_invoice_no" class="form-control form-control-sm" value="{{ $purchase->proforma_invoice_no }}" placeholder="PI-...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold mb-1 text-muted small">Payment Method</label>
                                @php $pm = $purchase->payment_method ?? 'bank_transfer'; @endphp
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="bank_transfer" {{ $pm === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer / TT</option>
                                    <option value="lc" {{ $pm === 'lc' ? 'selected' : '' }}>Letter of Credit (LC)</option>
                                    <option value="cash" {{ $pm === 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold mb-1 text-muted small">Delivery Terms</label>
                                @php $dt = $purchase->delivery_terms ?? 'FOB'; @endphp
                                <select name="delivery_terms" class="form-select form-select-sm">
                                    <option value="FOB" {{ $dt === 'FOB' ? 'selected' : '' }}>FOB</option>
                                    <option value="CIF" {{ $dt === 'CIF' ? 'selected' : '' }}>CIF</option>
                                    <option value="CFR" {{ $dt === 'CFR' ? 'selected' : '' }}>CFR</option>
                                    <option value="EXW" {{ $dt === 'EXW' ? 'selected' : '' }}>EXW</option>
                                    <option value="DDP" {{ $dt === 'DDP' ? 'selected' : '' }}>DDP</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold mb-1 text-muted small">Expected Arrival</label>
                                <input type="date" name="expected_delivery_date" class="form-control form-control-sm" value="{{ $purchase->expected_delivery_date ? \Carbon\Carbon::parse($purchase->expected_delivery_date)->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- TOP VENDOR DETAILS & HISTORY STRIP -->
                    <div id="vendorInfoCard" class="mt-3 p-2 border rounded-3 bg-light {{ $purchase->vendor ? '' : 'd-none' }}">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 px-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-telephone-fill text-primary"></i>
                                <span class="fw-bold text-muted small">Mobile:</span>
                                <span class="fw-semibold text-dark small" id="vi_mobile">{{ $purchase->vendor->phone ?? '—' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-primary"></i>
                                <span class="fw-bold text-muted small">Address:</span>
                                <span class="fw-semibold text-dark small" id="vi_address">{{ $purchase->vendor->address ?? '—' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger-subtle text-danger border border-danger fs-6 px-3 py-1">
                                    Previous Balance: Rs. <span id="vi_prev_bal">{{ number_format($prevVendorBalance ?? 0, 2, '.', '') }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Default Warehouse & Hidden Branch --}}
                    @php
                        $defaultWarehouseId = $purchase->warehouse_id ?? optional($Warehouse->first())->id ?? optional(\App\Models\Warehouse::first())->id ?? 1;
                        $defaultBranchId = $purchase->branch_id ?? auth()->user()->branch_id ?? optional(\App\Models\Branch::first())->id ?? 1;
                    @endphp
                    <input type="hidden" name="warehouse_id" id="warehouseSelect" value="{{ $defaultWarehouseId }}">
                    <input type="hidden" name="branch_id" value="{{ $defaultBranchId }}">
                </div>

                {{-- PURCHASE ITEMS (FULL WIDTH) --}}
                <div class="card-panel shadow-sm p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0">Purchase Items</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success px-3 shadow-sm" data-toggle="modal" data-target="#quickAddProductModal">
                                <i class="bi bi-plus-circle me-1"></i>Quick Add Product
                            </button>
                            <button type="button" class="btn btn-sm btn-primary px-3 shadow-sm" id="btnAdd">
                                <i class="bi bi-plus-lg"></i> Add Row
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive border rounded-3 bg-white">
                        <table class="table table-bordered sales-table mb-0" id="purchaseTable">
                            <thead>
                                <tr>
                                    <th class="col-product">Product</th>
                                    <th class="col-unit">Unit</th>
                                    <th class="col-qty">Qty</th>
                                    <th class="col-price">Purchase Price</th>
                                    <th class="col-disc">Disc %</th>
                                    <th class="col-disc-amt">Disc Amt</th>
                                    <th class="col-amount">Amount</th>
                                    <th class="col-action">Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseTableBody">
                                @forelse ($purchase->items as $item)
                                    @php
                                        $prod = $item->product;
                                        $curUnit = $item->unit ?? ($prod->unit->name ?? 'Pcs');
                                        $gross = (float)$item->line_total + (float)$item->item_discount;
                                        $discPercent = ($gross > 0) ? round(((float)$item->item_discount / $gross) * 100, 2) : 0;
                                        $sizeMode = $item->size_mode ?? ($prod->size_mode ?? '');
                                        $ppm2 = (float)($item->pieces_per_m2 ?? ($prod->pieces_per_m2 ?? 0));
                                        $ppb = (float)($item->pieces_per_box ?? ($prod->pieces_per_box ?? 1));
                                    @endphp
                                    <tr data-sizemode="{{ $sizeMode }}" data-pieces_per_m2="{{ $ppm2 }}" data-p_price_piece="{{ $item->price }}">
                                        <td>
                                            <select class="form-select product-select2" name="product_id[]">
                                                @if ($prod)
                                                    <option value="{{ $item->product_id }}" selected>
                                                        {{ $prod->item_name }} (SKU: {{ $prod->item_code ?? 'N/A' }})
                                                    </option>
                                                @endif
                                            </select>
                                            <!-- Hidden fields for product data snapshot -->
                                            <input type="hidden" name="size_mode[]" class="hidden-size-mode" value="{{ $sizeMode }}">
                                            <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="{{ $ppb }}">
                                            <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="{{ $ppm2 }}">
                                            <input type="hidden" name="price_per_carton[]" class="hidden-price-per-carton" value="{{ $item->price * $ppb }}">
                                            <input type="hidden" name="length[]" class="hidden-length" value="{{ $item->length }}">
                                            <input type="hidden" name="width[]" class="hidden-width" value="{{ $item->width }}">
                                            <input type="hidden" name="color[]" class="hidden-variant-data" value="{{ $item->color }}">
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm {{ strtolower($curUnit) === 'gm' ? 'btn-outline-info' : 'btn-outline-primary' }} fw-bold unit-toggle-btn py-0 px-2" data-unit="{{ $curUnit }}" style="font-size:0.72rem;">{{ $curUnit }}</button>
                                            <input type="hidden" name="unit[]" class="unit-input-val" value="{{ $curUnit }}">
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0.01" name="qty[]" class="form-control text-center main-qty-input" value="{{ $item->qty }}" placeholder="Qty">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="price[]" class="form-control text-end price" value="{{ number_format($item->price, 2, '.', '') }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="item_discount[]" class="form-control text-end item-disc-percent" value="{{ $discPercent }}">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-end input-readonly item-disc-amt" value="{{ number_format($item->item_discount, 2, '.', '') }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-end input-readonly row-total" value="{{ number_format($item->line_total, 2, '.', '') }}" readonly>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-x-lg"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- Empty state: dynamic row added via JS -->
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold text-muted">Total Amount:</td>
                                    <td class="text-end fw-bold fs-6 text-dark"><span id="totalAmount">{{ number_format($purchase->subtotal, 2, '.', '') }}</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Totals + Summary --}}
                <div class="row g-3 mt-1">
                    <div class="col-lg-7">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Payment / Receipt Voucher</div>
                            <div id="paymentWrapper" class="border rounded p-3 bg-light mb-3">
                                @if (isset($paymentLines) && $paymentLines->count() > 0)
                                    @foreach ($paymentLines as $index => $pline)
                                        <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                            <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                                                <option value="" disabled>Select Account</option>
                                                @foreach ($accounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ $acc->id == $pline->account_id ? 'selected' : '' }}>{{ $acc->title }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" step="0.01" class="form-control text-end payment-amount" name="payment_amount[]" value="{{ number_format($pline->credit, 2, '.', '') }}" placeholder="Amount" style="width:140px">
                                            @if ($index == 0)
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment">
                                                    <i class="bi bi-plus"></i> Add
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                @elseif ($purchase->paid_amount > 0)
                                    <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                        <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                                            <option value="" disabled>Select Account</option>
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $acc->title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" class="form-control text-end payment-amount" name="payment_amount[]" value="{{ number_format($purchase->paid_amount, 2, '.', '') }}" placeholder="Amount" style="width:140px">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    </div>
                                @else
                                    <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                        <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                                            <option value="" selected disabled>Select Account</option>
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <span class="me-2 fw-bold text-muted">Total Paid:</span>
                                <span class="fw-bold fs-6 text-success" id="totalPaid">{{ number_format($purchase->paid_amount, 2, '.', '') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="bg-white shadow-sm rounded-3 p-3 h-100 border">
                            <div class="section-title mb-3">Summary</div>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-muted fw-medium">Total Qty</div>
                                    <div class="col-5 text-end"><span id="tQty" class="fw-bold">0</span></div>
                                </div>
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-muted fw-medium">Sub-Total</div>
                                    <div class="col-5 text-end fw-bold"><span id="tSub">{{ number_format($purchase->subtotal, 2, '.', '') }}</span></div>
                                </div>
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-muted fw-medium">Bill Discount</div>
                                    <div class="col-5 text-end d-flex gap-1">
                                        <input type="number" class="form-control text-end form-control-sm"
                                            id="billDiscountPct" placeholder="%" style="width: 70px;" step="0.01">
                                        <input type="number" class="form-control text-end form-control-sm"
                                            id="billDiscount" value="{{ number_format($purchase->discount ?? 0, 2, '.', '') }}" step="0.01">
                                        <input type="hidden" name="discount" id="discountInput" value="{{ number_format($purchase->additional_discount ?? $purchase->discount ?? 0, 2, '.', '') }}">
                                    </div>
                                </div>
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-muted fw-medium">Extra Cost</div>
                                    <div class="col-5 text-end">
                                        <input type="number" class="form-control text-end form-control-sm"
                                            name="extra_cost" id="extraCost" value="{{ number_format($purchase->extra_cost ?? 0, 2, '.', '') }}" step="0.01">
                                    </div>
                                </div>
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-danger fw-medium">Previous Balance</div>
                                    <div class="col-5 text-end text-danger fw-bold"><span id="tPrev">{{ number_format($prevVendorBalance ?? 0, 2, '.', '') }}</span></div>
                                </div>
                                <hr class="my-2 border-secondary">
                                <div class="row py-2">
                                    <div class="col-6 fw-bold fs-5 text-primary">Current Bill</div>
                                    <div class="col-6 text-end fw-bold fs-5 text-primary"><span id="tPayable">{{ number_format($purchase->net_amount, 2, '.', '') }}</span></div>
                                </div>
                                <div class="row py-2 bg-warning-subtle rounded-2">
                                    <div class="col-6 fw-bold fs-5 text-dark">Total Payable</div>
                                    <div class="col-6 text-end fw-bold fs-5 text-dark"><span id="tTotalPayable">0.00</span></div>
                                </div>
                                <input type="hidden" name="net_amount" id="netAmountInput" value="{{ number_format($purchase->net_amount, 2, '.', '') }}">
                                <input type="hidden" name="subtotal" id="subtotalInput" value="{{ number_format($purchase->subtotal, 2, '.', '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-3 justify-content-end p-3 mt-3 border-top bg-light rounded-bottom">
                    <a href="{{ route('Purchase.home') }}" class="btn btn-action-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                    <button type="button" class="btn btn-action-secondary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>

                    @if ($purchase->status_purchase === 'draft')
                        <button type="button" class="btn btn-action-primary bg-info border-info text-white" id="btnSaveOnly">
                            <i class="bi bi-save me-1"></i> Save Draft
                        </button>
                        <button type="button" class="btn btn-action-primary bg-success border-success text-white" id="btnConfirm">
                            <i class="bi bi-check-circle me-1"></i> Confirm Purchase
                        </button>
                    @else
                        <button type="button" class="btn btn-action-primary bg-success border-success text-white" id="btnConfirm">
                            <i class="bi bi-check2-square me-1"></i> Update Purchase
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Add Vendor Modal -->
    <div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0 pb-2">
                    <h5 class="modal-title fw-bold" id="addVendorModalLabel">Add New Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickAddVendorForm">
                    @csrf
                    <div class="modal-body pt-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Vendor Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="Enter vendor name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Phone Number</label>
                                <input type="text" class="form-control" name="phone" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="opening_balance" value="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-muted">Address</label>
                            <textarea class="form-control" name="address" rows="2" placeholder="Optional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnQuickSaveVendor">Save Vendor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>

    {{-- Quick Add Product Modal --}}
    @include('admin_panel.partials.quick_add_product_modal')

    <script>
        $(document).ready(function() {
            // Init Select2
            $('.select2').select2({
                width: '100%'
            });

            // Purchase Type (Local vs Import) Toggle
            $('#purchaseTypeSelect').on('change', function() {
                if ($(this).val() === 'import') {
                    $('#importProcurementFields').removeClass('d-none');
                } else {
                    $('#importProcurementFields').addClass('d-none');
                }
            });

            // Vendor Select Logic
            $('#vendorSelect').on('change', function() {
                const vendorId = $(this).val();
                if (!vendorId) {
                    $('#vendorInfoCard').addClass('d-none');
                    return;
                }

                // Fetch Vendor Info & Ledger
                $.get(`/vendor/${vendorId}/ledger-json`, function(data) {
                    $('#vi_mobile').text(data.vendor.phone || '—');
                    $('#vi_address').text(data.vendor.address || '—');
                    $('#vi_prev_bal').text(parseFloat(data.current_balance).toFixed(2));
                    $('#vendorInfoCard').removeClass('d-none');

                    $('#tPrev').text(parseFloat(data.current_balance).toFixed(2));
                    recalcAll();
                });
            });

            // Initialize existing table rows
            $('#purchaseTableBody tr').each(function() {
                initProductSelect2($(this).find('.product-select2'));
            });

            // If table has no rows, add a blank one
            if ($('#purchaseTableBody tr').length === 0) {
                addBlankRow();
            }

            // Add Row Button
            $('#btnAdd').click(function() {
                addBlankRow();
            });

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                if ($('#purchaseTableBody tr').length > 1) {
                    $(this).closest('tr').remove();
                    recalcAll();
                } else {
                    Swal.fire('Warning', 'At least one product item is required.', 'warning');
                }
            });

            // Inputs -> Calc
            $('#purchaseTableBody').on('input', '.main-qty-input, .price, .item-disc-percent', function() {
                recalcRow($(this).closest('tr'));
                recalcAll();
            });

            // Summary Inputs
            $('#billDiscount, #billDiscountPct, #extraCost').on('input', function() {
                recalcAll();
            });

            function normalizeDiscountInput() {
                let totalInlineDiscount = 0;
                $('#purchaseTableBody tr').each(function() {
                    const rowDiscAmt = parseFloat($(this).find('.item-disc-amt').val()) || 0;
                    totalInlineDiscount += rowDiscAmt;
                });

                let billDiscVal = parseFloat($('#billDiscount').val());
                if (isNaN(billDiscVal) || billDiscVal < totalInlineDiscount) {
                    $('#billDiscount').val(totalInlineDiscount.toFixed(2));
                }
                recalcAll();
            }

            $('#billDiscount, #billDiscountPct').on('blur', function() {
                normalizeDiscountInput();
            });

            $('#purchaseForm').on('submit', function() {
                normalizeDiscountInput();
            });

            // Payment Row Add
            $('#btnAddPayment').click(function() {
                const html = `
                    <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                        <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                            <option value="" selected disabled>Select Account</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                            @endforeach
                        </select>
                        <input type="number" step="0.01" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>`;
                $('#paymentWrapper').append(html);
            });

            $(document).on('click', '.remove-payment', function() {
                $(this).closest('.payment-row').remove();
                calcTotalPaid();
            });

            $(document).on('input', '.payment-amount', function() {
                calcTotalPaid();
            });

            function calcTotalPaid() {
                let total = 0;
                $('.payment-amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#totalPaid').text(total.toFixed(2));
                recalcAll();
            }

            // --- SUBMIT LOGIC (AJAX with SweetAlert2) ---

            // 1. Save (Draft)
            $('#btnSaveOnly').click(function(e) {
                e.preventDefault();
                normalizeDiscountInput();
                let $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

                $('#action').val('save_only');

                $.ajax({
                    url: "{{ route('purchase.update', $purchase->id) }}",
                    method: "POST",
                    data: $('#purchaseForm').serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Purchase draft updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('Purchase.home') }}";
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Draft');
                        let msg = 'Something went wrong.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            msg += '\n' + errors;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // 2. Confirm / Update Purchase
            $('#btnConfirm').click(function(e) {
                e.preventDefault();
                normalizeDiscountInput();

                const isAlreadyApproved = "{{ $purchase->status_purchase }}" === 'approved';
                const confirmTitle = isAlreadyApproved ? 'Update Purchase?' : 'Confirm Purchase?';
                const confirmText = isAlreadyApproved 
                    ? 'Updating will re-synchronize stock, vendor balances, and financial vouchers.' 
                    : 'This will approve the purchase and post stock and accounting vouchers.';
                const confirmBtnText = isAlreadyApproved ? 'Yes, Update it!' : 'Yes, Confirm it!';

                Swal.fire({
                    title: confirmTitle,
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmBtnText
                }).then((result) => {
                    if (result.isConfirmed) {
                        let $btn = $('#btnConfirm');
                        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

                        $('#action').val('approved');

                        $.ajax({
                            url: "{{ route('purchase.update', $purchase->id) }}",
                            method: "POST",
                            data: $('#purchaseForm').serialize(),
                            success: function(response) {
                                if (response.invoice_url) {
                                    window.open(response.invoice_url, '_blank');
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Purchase updated and synchronized successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = response.redirect_url || "{{ route('Purchase.home') }}";
                                });
                            },
                            error: function(xhr) {
                                $btn.prop('disabled', false).html('<i class="bi bi-check2-square me-1"></i> Update Purchase');
                                let msg = 'Something went wrong.';
                                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    let errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                                    msg += '\n' + errors;
                                }
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // Quick Add Vendor AJAX
            $('#quickAddVendorForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnQuickSaveVendor');
                let originalText = $btn.text();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

                $.ajax({
                    url: "{{ route('vendors.store.ajax') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $btn.prop('disabled', false).text(originalText);
                        let vendorId = null;
                        let vendorName = $('#quickAddVendorForm input[name="name"]').val();

                        if (response.vendor && response.vendor.id) {
                            vendorId = response.vendor.id;
                            vendorName = response.vendor.name;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Vendor Added',
                            text: 'The vendor has been created successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#addVendorModal').modal('hide');
                            $('#quickAddVendorForm')[0].reset();

                            if (vendorId) {
                                let newOption = new Option(vendorName, vendorId, false, true);
                                $('#vendorSelect').append(newOption).trigger('change');
                            } else {
                                window.location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).text(originalText);
                        let msg = 'Error adding vendor.';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            function addBlankRow() {
                const html = `
                <tr>
                    <td>
                        <select class="form-select product-select2" name="product_id[]"></select>
                        <input type="hidden" name="size_mode[]" class="hidden-size-mode" value="">
                        <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="1">
                        <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="0">
                        <input type="hidden" name="price_per_carton[]" class="hidden-price-per-carton" value="0">
                        <input type="hidden" name="length[]" class="hidden-length" value="">
                        <input type="hidden" name="width[]" class="hidden-width" value="">
                        <input type="hidden" name="color[]" class="hidden-variant-data" value="">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold unit-toggle-btn py-0 px-2" data-unit="Pcs" style="font-size:0.72rem;">Pcs</button>
                        <input type="hidden" name="unit[]" class="unit-input-val" value="Pcs">
                    </td>
                    <td>
                        <input type="number" step="any" min="0.01" name="qty[]" class="form-control text-center main-qty-input" value="1" placeholder="Qty">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="price[]" class="form-control text-end price" value="0">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="item_discount[]" class="form-control text-end item-disc-percent" value="0">
                    </td>
                    <td>
                        <input type="number" class="form-control text-end input-readonly item-disc-amt" value="0.00" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control text-end input-readonly row-total" value="0.00" readonly>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-x-lg"></i></button>
                    </td>
                </tr>
                `;
                const $row = $(html);
                $('#purchaseTableBody').append($row);
                initProductSelect2($row.find('.product-select2'));
            }

            function initProductSelect2($el) {
                $el.select2({
                    placeholder: 'Search Product (Name / SKU / Barcode)',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '{{ route('products.ajax.search') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results || [],
                                pagination: {
                                    more: (data.pagination && data.pagination.more) ? true : false
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    templateResult: formatProduct,
                    templateSelection: formatSelection
                });

                $el.on('select2:select', function(e) {
                    const data = e.params.data;
                    const $row = $(this).closest('tr');

                    let unitName = data.unit_name || 'Pcs';
                    if (data.size_mode === 'by_kg' || data.size_mode === 'by_gm') {
                        unitName = 'Kg';
                        $row.find('.unit-toggle-btn').removeClass('btn-outline-info').addClass('btn-outline-primary');
                    }
                    $row.find('.unit-toggle-btn').text(unitName).attr('data-unit', unitName);
                    $row.find('.unit-input-val').val(unitName);

                    $row.find('.hidden-size-mode').val(data.size_mode || '');
                    $row.find('.hidden-pieces-per-box').val(data.pieces_per_box || 1);
                    $row.find('.hidden-pieces-per-m2').val(data.pieces_per_m2 || 0);
                    $row.find('.hidden-price-per-carton').val(
                        Number(data.purchase_price_per_box || 0) ||
                        Number(data.purchase_price_per_piece || 0) * Number(data.pieces_per_box || 1) ||
                        0
                    );
                    $row.find('.hidden-length').val(data.length || '');
                    $row.find('.hidden-width').val(data.width || '');
                    $row.find('.hidden-variant-data').val(data.variant_data || '');

                    $row.data('sizemode', data.size_mode);
                    $row.data('pieces_per_m2', Number(data.pieces_per_m2) || 0);
                    $row.data('p_price_piece', Number(data.purchase_price_per_piece) || 0);

                    $row.find('.item-disc-percent').val(data.purchase_discount_percent || 0);

                    const sizeMode = data.size_mode || 'std';
                    const pM2 = parseFloat(data.purchase_price_per_m2) || 0;
                    const pPiece = parseFloat(data.purchase_price_per_piece) || parseFloat(data.trade_price) || 0;
                    let finalPrice = (sizeMode === 'by_size') ? pM2 : pPiece;

                    $row.find('.price').val(finalPrice);
                    $row.find('.main-qty-input').focus().select();

                    recalcRow($row);
                    recalcAll();
                });
            }

            function formatProduct(repo) {
                if (repo.loading) return repo.text;
                let stock = repo.stock !== undefined ? repo.stock : 0;
                let sku = repo.sku || 'N/A';
                let unit = repo.unit_name || 'Pcs';
                let stockVal = parseFloat(repo.stock_pieces !== undefined ? repo.stock_pieces : repo.stock) || 0;
                let badgeClass = stockVal > 0 ? 'bg-success' : 'bg-danger';

                return $(`
                <div class="clearfix">
                    <div class="float-start">
                        <div class="fw-bold">${repo.name || repo.text}</div>
                        <small class="text-muted">SKU: ${sku} | Unit: ${unit}</small>
                    </div>
                    <div class="float-end">
                        <span class="badge ${badgeClass} rounded-pill">Stock: ${stock}</span>
                    </div>
                </div>
                `);
            }

            function formatSelection(repo) {
                return repo.name || repo.text;
            }

            $(document).on('click', '.unit-toggle-btn', function() {
                const $btn = $(this);
                const $row = $btn.closest('tr');
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();

                if (sizeMode === 'by_kg' || sizeMode === 'by_gm') {
                    let currentUnit = $btn.attr('data-unit') || 'Kg';
                    if (currentUnit.toLowerCase() === 'kg') {
                        currentUnit = 'Gm';
                        $btn.text('Gm').removeClass('btn-outline-primary').addClass('btn-outline-info');
                    } else {
                        currentUnit = 'Kg';
                        $btn.text('Kg').removeClass('btn-outline-info').addClass('btn-outline-primary');
                    }
                    $btn.attr('data-unit', currentUnit);
                    $row.find('.unit-input-val').val(currentUnit);
                    recalcRow($row);
                    recalcAll();
                }
            });

            function recalcRow($row) {
                const qty = parseFloat($row.find('.main-qty-input').val()) || 0;
                const price = parseFloat($row.find('.price').val()) || 0;
                const discPct = parseFloat($row.find('.item-disc-percent').val()) || 0;
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();
                const unitVal = ($row.find('.unit-input-val').val() || '').toLowerCase();
                const pieces_per_m2 = parseFloat($row.data('pieces_per_m2')) || 0;

                let gross = 0;
                if (sizeMode === 'by_size') {
                    gross = (pieces_per_m2 || 1) * qty * price;
                } else if (unitVal === 'gm' || unitVal === 'g') {
                    gross = (qty / 1000.0) * price;
                } else {
                    gross = qty * price;
                }

                const discAmt = gross * (discPct / 100);
                const lineTotal = Math.max(0, gross - discAmt);

                $row.find('.item-disc-amt').val(discAmt.toFixed(2));
                $row.find('.row-total').val(lineTotal.toFixed(2));
            }

            function recalcAll() {
                let totalQty = 0;
                let subtotal = 0;
                let totalInlineDiscount = 0;

                $('#purchaseTableBody tr').each(function() {
                    const qty = parseFloat($(this).find('.main-qty-input').val()) || 0;
                    const total = parseFloat($(this).find('.row-total').val()) || 0;
                    const rowDiscAmt = parseFloat($(this).find('.item-disc-amt').val()) || 0;

                    totalQty += qty;
                    subtotal += total;
                    totalInlineDiscount += rowDiscAmt;
                });

                const grossSubtotal = subtotal + totalInlineDiscount;

                $('#tQty').text(totalQty.toFixed(2));
                $('#tSub').text(subtotal.toFixed(2));
                $('#subtotalInput').val(subtotal.toFixed(2));

                let additionalDiscount = parseFloat($('#discountInput').val()) || 0;
                let billDiscVal = parseFloat($('#billDiscount').val());

                if ($(document.activeElement).is('#billDiscount') || $(document.activeElement).is('#billDiscountPct')) {
                    if ($(document.activeElement).is('#billDiscountPct')) {
                        const pct = parseFloat($('#billDiscountPct').val()) || 0;
                        billDiscVal = grossSubtotal * (pct / 100);
                        $('#billDiscount').val(billDiscVal.toFixed(2));
                    }
                    if (!isNaN(billDiscVal)) {
                        additionalDiscount = Math.max(0, billDiscVal - totalInlineDiscount);
                    } else {
                        additionalDiscount = 0;
                    }
                } else {
                    billDiscVal = totalInlineDiscount + additionalDiscount;
                    $('#billDiscount').val(billDiscVal.toFixed(2));
                }

                const pct = grossSubtotal > 0 ? (billDiscVal / grossSubtotal) * 100 : 0;
                $('#billDiscountPct').val(pct.toFixed(2));
                $('#discountInput').val(additionalDiscount.toFixed(2));

                const extraCost = parseFloat($('#extraCost').val()) || 0;
                const net = subtotal - additionalDiscount + extraCost;

                $('#tPayable').text(net.toFixed(2));
                $('#netAmountInput').val(net.toFixed(2));
                $('#totalAmount').text(subtotal.toFixed(2));

                const prevBal = parseFloat($('#tPrev').text()) || 0;
                const totalPaid = parseFloat($('#totalPaid').text()) || 0;
                const totalPayable = (prevBal + net) - totalPaid;

                $('#tTotalPayable').text(totalPayable.toFixed(2));
            }

            // Initial calculation on page load
            calcTotalPaid();
            recalcAll();
        });
    </script>
@endsection
