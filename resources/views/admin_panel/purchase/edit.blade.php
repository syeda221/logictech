@extends('admin_panel.layout.app')

@section('content')
  <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        /* ================= RESPONSIVE PURCHASE UI (Modernized) ================= */
        body {
            background-color: #f4f6f9;
            /* Light gray background for contrast */
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .sales-table {
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
            min-width: 1000px;
        }

        .sales-table thead th {
            background-color: #f8fafc !important; /* Light clean header */
            color: #0f172a !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 11px !important;
            letter-spacing: 0.5px;
            padding: 10px 8px !important;
            border: 1px solid #cbd5e1 !important;
            border-bottom: 2px solid #94a3b8 !important; /* Thick header separator border */
            vertical-align: middle !important;
            text-align: center;
        }

        .sales-table thead th.col-product {
            text-align: left !important;
            padding-left: 12px !important;
        }

        .sales-table tbody td {
            border: 1px solid #cbd5e1 !important; /* Flat interior cell borders */
            padding: 0 !important; /* Zero padding to let input fill cell completely */
            background-color: #ffffff;
            vertical-align: middle !important;
        }

        /* ⚡ FLAT BORDERLESS GRID INPUTS ⚡ */
        .sales-table tbody .form-control,
        .sales-table tbody .form-select {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            height: 38px !important; /* Uniform height */
            margin: 0 !important;
            padding: 6px 8px !important;
            width: 100% !important;
            background-color: transparent !important;
            text-align: center; /* Center-align text in grid inputs */
            color: #1e293b !important;
            font-weight: 500 !important;
            font-size: 0.82rem !important;
        }

        .sales-table tbody td.col-product .form-select {
            text-align: left !important;
            padding-left: 12px !important;
        }

        /* Calculations and Read-Only cells get a neat slate tone background */
        .sales-table tbody .input-readonly,
        .sales-table tbody input[readonly],
        .sales-table tbody select[disabled] {
            background-color: #f1f5f9 !important;
            cursor: not-allowed !important;
            color: #475569 !important;
            font-weight: 600 !important;
        }

        /* Subtle focus highlight inside cell */
        .sales-table tbody .form-control:focus,
        .sales-table tbody .form-select:focus {
            outline: none !important;
            background-color: #f8fafc !important;
            box-shadow: inset 0 0 0 2px #2563eb !important;
        }

        /* Select2 Specific flat borderless styling */
        .sales-table tbody .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background-color: transparent !important;
            display: flex;
            align-items: center;
        }

        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 12px !important;
            padding-right: 20px !important;
            font-size: 0.82rem !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            text-align: left !important;
        }

        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            right: 8px !important;
        }

        /* Select2 Focus state */
        .sales-table tbody .select2-container--default.select2-container--focus .select2-selection--single {
            background-color: #f8fafc !important;
            box-shadow: inset 0 0 0 2px #2563eb !important;
        }

        /* Elegant flat block layout for discount input + toggle */
        .sales-table tbody .discount-wrapper {
            display: flex !important;
            align-items: stretch !important;
            width: 100% !important;
            height: 38px !important;
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
            padding: 6px 8px !important;
        }

        .sales-table tbody .discount-wrapper .discount-toggle {
            border: none !important;
            border-radius: 0 !important;
            background-color: #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            width: 32px !important;
            min-width: 32px !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            cursor: pointer !important;
            transition: background-color 0.2s !important;
        }

        .sales-table tbody .discount-wrapper .discount-toggle:hover {
            background-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .sales-table tfoot td {
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-top: 2px solid #94a3b8 !important; /* Thick tfoot separator */
            padding: 8px 10px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        /* Row hover */
        .sales-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* Column widths */
        .col-product {
            width: 300px;
            min-width: 250px;
        }

        .col-warehouse {
            width: 140px;
        }

        .col-stock {
            width: 90px;
        }

        .col-qty {
            width: 100px;
        }

        .col-pieces {
            width: 100px;
        }

        .col-price {
            width: 120px;
        }

        .col-disc {
            width: 80px;
        }

        .col-disc-amt {
            width: 95px;
        }

        .col-price-p {
            width: 100px;
        }

        .col-amount {
            width: 120px;
            text-align: right;
        }

        .col-action {
            width: 50px;
            text-align: center;
        }

        .main-container {
            font-size: .85rem;
            max-width: 99%;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
        }

        .btn {
            font-size: .82rem;
            padding: .35rem .8rem;
            border-radius: 5px;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }

        /* Mobile Breakpoints (< 768px) */
        @media (max-width: 768px) {
            .header-text {
                font-size: 1.1rem !important;
            }
            .main-container {
                padding: 12px !important;
                border-radius: 8px !important;
            }
            .sales-table {
                min-width: 780px !important;
            }
            .discount-wrapper {
                min-width: 70px !important;
            }
            .btn-submit-update {
                width: 100% !important;
                height: 46px !important;
                font-size: 1rem !important;
            }
            .payment-row select, .payment-row input {
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        .section-title {
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
            border-left: 3px solid #0d6efd;
            padding-left: 8px;
        }

        /* Product Search Dropdown */
        .search-results {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            z-index: 1000;
            max-height: 250px;
            overflow-y: auto;
            width: 100%;
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
        }

        .search-result-item {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            transition: background 0.1s;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover,
        .search-result-item.active {
            background-color: #e7f1ff;
            color: #0b5ed7;
        }

        /* Layout Helpers */
        .card-panel {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            height: 100%;
        }

        .summary-card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }

        .select2-container .select2-selection--single {
            height: 36px !important;
            padding: 3px 12px;
            border-color: #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 5px !important;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="main-container bg-white border shadow-sm mx-auto p-2 rounded-3">

            <form id="purchaseForm" action="{{ route('purchase.update', $purchase->id) }}" method="POST" autocomplete="off">
                @csrf
                @method('PUT')

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <a href="{{ route('Purchase.home') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                    <h2 class="header-text text-secondary fw-bold mb-0">Edit Purchase #{{ $purchase->invoice_no }}</h2>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-secondary" id="entryDate">Date: {{ date('d/m/Y') }}</small>
                    </div>
                </div>

                <div class="row g-3 border-bottom pb-4 mb-3 mt-2">
                    {{-- LEFT: Invoice & Vendor --}}
                    <div class="col-lg-3 col-md-4">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Invoice & Vendor</div>

                            <div class="mb-2 d-flex align-items-center gap-2">
                                <label class="form-label fw-bold mb-0 text-muted small" style="min-width: 80px;">Invoice
                                    No</label>
                                <input type="text" class="form-control input-readonly" name="invoice_no"
                                    value="{{ $purchase->invoice_no }}" readonly>
                            </div>

                            <!-- VENDOR SELECT -->
                            <div class="mb-2">
                                <label class="form-label fw-bold mb-1 text-muted small">Select Vendor</label>
                                <select class="form-select select2" id="vendorSelect" name="vendor_id">
                                    <option value="" disabled>Select Vendor</option>
                                    @foreach ($Vendor as $v)
                                        <option value="{{ $v->id }}"
                                            {{ $v->id == $purchase->vendor_id ? 'selected' : '' }}>
                                            {{ $v->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold mb-1 text-muted small">Date</label>
                                <input type="date" name="purchase_date" class="form-control"
                                    value="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : date('Y-m-d') }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold text-muted small">M.Bill</label>
                                <textarea class="form-control" name="note" rows="2">{{ $purchase->note }}</textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold text-muted small">Warehouse</label>
                                <select name="warehouse_id" class="form-control select2">
                                    @foreach ($Warehouse as $w)
                                        <option value="{{ $w->id }}"
                                            {{ $w->id == $purchase->warehouse_id ? 'selected' : '' }}>
                                            {{ $w->warehouse_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Items --}}
                    <div class="col-lg-9 col-md-8">
                        <div class="card-panel shadow-sm p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="section-title mb-0">Purchase Items</div>
                                <button type="button" class="btn btn-sm btn-primary px-3 shadow-sm"
                                    onclick="addBlankRow()">
                                    <i class="bi bi-plus-lg"></i> Add Row
                                </button>
                            </div>

                            <div class="table-responsive border rounded-3 bg-white">
                                <table class="table table-bordered sales-table mb-0" id="purchaseTable">
                                    <thead>
                                        <tr>
                                            <th class="col-product">Product</th>
                                            <th class="col-qty">Cartons</th>
                                            <th class="col-qty">Loose Pcs</th>
                                            <th class="col-stock">Pack Size</th>
                                            <th class="col-pieces">Pieces</th>
                                            <th class="col-price">Price</th>
                                            <th class="col-disc">Disc %</th>
                                            <th class="col-disc-amt">Disc Amt</th>
                                            <th class="col-amount">Amount</th>
                                            <th class="col-action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchaseTableBody">
                                        @foreach ($purchase->items as $item)
                                            @php
                                                $sizeMode = $item->size_mode ?? 'by_pieces';
                                                $ppb = (float) ($item->pieces_per_box > 0 ? $item->pieces_per_box : 1);
                                                $boxes = (float) ($item->boxes_qty ?? 0);
                                                $loose = (float) ($item->loose_qty ?? 0);

                                                // Create 5.3 format for display
                                                $displayBoxes = $boxes > 0 ? $boxes : '0';
                                                if ($loose > 0) {
                                                    $displayBoxes .= '.' . $loose;
                                                } elseif ($boxes == 0) {
                                                    $displayBoxes = ''; // Empty if 0
                                                }

                                                // Unit Label
                                                $unitLabel = '';
                                                if ($sizeMode == 'by_size') {
                                                    $unitLabel = '(m²)';
                                                } elseif ($sizeMode == 'by_cartons') {
                                                    $unitLabel = '(carton)';
                                                } else {
                                                }

                                                $variantInfo = '';
                                                if (!empty($item->color)) {
                                                    $decodedColor = base64_decode($item->color, true);
                                                    $vData = ($decodedColor !== false) ? json_decode($decodedColor, true) : null;
                                                    if (!$vData) {
                                                        $vData = json_decode($item->color, true);
                                                    }
                                                    if (is_array($vData)) {
                                                        $vColorName = $vData['color'] ?? '';
                                                        $vSize = $vData['size'] ?? '';
                                                        $vParts = [];
                                                        if ($vColorName && $vColorName !== '-') {
                                                            $vParts[] = $vColorName;
                                                        }
                                                        if ($vSize && $vSize !== '-') {
                                                            $vParts[] = $vSize;
                                                        }
                                                        if (!empty($vParts)) {
                                                            $variantInfo = ' (' . implode(' | ', $vParts) . ')';
                                                        }
                                                    } else {
                                                        $variantInfo = ' (' . $item->color . ')';
                                                    }
                                                }
                                            @endphp
                                            <tr data-sizemode="{{ $sizeMode }}"
                                                data-pieces_per_m2="{{ $item->pieces_per_m2 }}">
                                                <td>
                                                    <select class="form-select product-select2" name="product_id[]">
                                                        <option value="{{ $item->product_id }}" selected>
                                                            {{ $item->product->item_name }}
                                                            ({{ $item->product->item_code }}){{ $variantInfo }}
                                                        </option>
                                                    </select>
                                                    {{-- Snapshots --}}
                                                    <input type="hidden" name="size_mode[]" class="hidden-size-mode"
                                                        value="{{ $sizeMode }}">
                                                    <input type="hidden" name="pieces_per_box[]"
                                                        class="hidden-pieces-per-box" value="{{ $ppb }}">
                                                    <input type="hidden" name="pieces_per_m2[]"
                                                        class="hidden-pieces-per-m2" value="{{ $item->pieces_per_m2 }}">
                                                    <input type="hidden" name="length[]" class="hidden-length"
                                                        value="{{ $item->length }}">
                                                    <input type="hidden" name="width[]" class="hidden-width"
                                                        value="{{ $item->width }}">
                                                    <input type="hidden" name="color[]" class="hidden-variant-data"
                                                        value="{{ $item->color ?? '' }}">
                                                    </td>
                                                <td>
                                                    <input type="number" class="form-control carton-qty" name="boxes_qty[]"
                                                        value="{{ $boxes }}" placeholder="Cartons" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control loose-qty" name="loose_qty[]"
                                                        value="{{ $loose }}" placeholder="Loose Pcs" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control input-readonly pack-size" name="pieces_per_box_display[]"
                                                        value="{{ $ppb }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="qty[]"
                                                        class="form-control input-readonly qty-pcs"
                                                        value="{{ (float) $item->qty }}" readonly>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" name="price[]" class="form-control price"
                                                            step="0.01" value="{{ (float) $item->price }}">
                                                    </div>
                                                    <small class="text-muted price-unit-label"
                                                        style="font-size:0.7rem;">{{ $unitLabel }}</small>
                                                </td>
                                                <td>
                                                    {{-- Calc Disc % from Amt --}}
                                                    @php
                                                        $gross = $item->line_total + $item->item_discount;
                                                        $dPct = $gross > 0 ? ($item->item_discount / $gross) * 100 : 0;
                                                    @endphp
                                                    <input type="number" name="item_discount[]" class="form-control item-disc-percent"
                                                        value="{{ round($dPct, 2) }}">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        class="form-control item-disc-amt"
                                                        value="{{ (float) $item->item_discount }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control input-readonly row-total"
                                                        value="{{ (float) $item->line_total }}" readonly>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger remove-row border-0"><i
                                                            class="bi bi-x-lg"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7" class="text-end fw-bold text-muted">Total Amount:</td>
                                            <td class="text-end fw-bold fs-6 text-dark"><span id="totalAmount">0.00</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUMMARY --}}
                <div class="row g-3 mt-1">
                    {{-- LEFT: Payment / Receipt Voucher --}}
                    <div class="col-lg-7">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Payment / Receipt Voucher</div>
                            <div id="paymentWrapper" class="border rounded p-3 bg-light mb-3">
                                <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                    <select class="form-select rv-account" name="payment_account_id[]"
                                        style="max-width: 300px; flex-grow: 1;">
                                        <option value="" selected disabled>Select Account</option>
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" class="form-control text-end payment-amount"
                                        name="payment_amount[]" placeholder="Amount" style="width:140px">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment">
                                        <i class="bi bi-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="me-2 fw-bold text-muted">Total Paid:</span>
                                <span class="fw-bold fs-6 text-success" id="totalPaid">0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Summary --}}
                    <div class="col-lg-5">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Summary</div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Total Qty (Pieces)</div>
                                <div class="col-5 text-end"><span id="tQty" class="fw-bold">0</span></div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Sub-Total</div>
                                <div class="col-5 text-end fw-bold"><span id="tSub">0.00</span></div>
                                <input type="hidden" name="subtotal" id="subtotalInput">
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Bill Discount</div>
                                <div class="col-5 text-end d-flex gap-1">
                                    @php
                                        $inlineVal = $purchase->items->sum('item_discount');
                                        $bSub = (float) $purchase->subtotal + $inlineVal;
                                        $bDisc = (float) $purchase->discount + $inlineVal;
                                        $bPct = $bSub > 0 ? ($bDisc / $bSub) * 100 : 0;
                                    @endphp
                                    <input type="number" class="form-control text-end form-control-sm"
                                        id="billDiscountPct" value="{{ round($bPct, 2) }}" placeholder="%" style="width: 70px;" step="0.01">
                                    <input type="number" class="form-control text-end form-control-sm"
                                        id="billDiscount" value="{{ (float) $bDisc }}" step="0.01">
                                    <input type="hidden" name="discount" id="discountInput" value="{{ (float) $purchase->discount }}">
                                </div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Extra Cost</div>
                                <div class="col-5 text-end">
                                    <input type="number" class="form-control text-end form-control-sm" name="extra_cost"
                                        id="extraCost" value="{{ (float) $purchase->extra_cost }}">
                                </div>
                            </div>
                            <hr class="my-2 border-secondary">
                            <div class="row py-2">
                                <div class="col-6 fw-bold fs-5 text-primary">Net Payable</div>
                                <div class="col-6 text-end fw-bold fs-5 text-primary"><span id="tPayable">0.00</span>
                                </div>
                                <input type="hidden" name="net_amount" id="netAmountInput">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-submit-update px-5 fw-bold shadow-sm">
                        <i class="bi bi-save me-2"></i> Update Purchase
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Init Global Select2
            $('.select2').select2({
                width: '100%'
            });

            // Initialize existing product selects
            $('.product-select2').each(function() {
                initProductSelect2($(this));
            });

            // Recalc existing rows
            recalcAll();

            // Add Row
            window.addBlankRow = function() {
                const html = `
                <tr>
                    <td>
                        <select class="form-select product-select2" name="product_id[]"></select>
                        <input type="hidden" name="size_mode[]" class="hidden-size-mode">
                        <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="1">
                        <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="0">
                        <input type="hidden" name="length[]" class="hidden-length">
                        <input type="hidden" name="width[]" class="hidden-width">
                        </td>
                    <td><input type="number" class="form-control carton-qty" name="boxes_qty[]" value="0" placeholder="Cartons" min="0"></td>
                    <td><input type="number" class="form-control loose-qty" name="loose_qty[]" value="0" placeholder="Loose Pcs" min="0"></td>
                    <td><input type="number" class="form-control input-readonly pack-size" name="pieces_per_box_display[]" value="1" readonly></td>
                    <td><input type="number" name="qty[]" class="form-control input-readonly qty-pcs" value="0" readonly></td>
                    <td><div class="input-group input-group-sm"><input type="number" name="price[]" class="form-control price" step="0.01" value="0"></div></td>
                    <td><input type="number" name="item_discount[]" class="form-control item-disc-percent" value="0"></td>
                    <td><input type="number" class="form-control item-disc-amt" value="0"></td>
                    <td><input type="number" class="form-control input-readonly row-total" value="0" readonly></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row border-0"><i class="bi bi-x-lg"></i></button></td>
                </tr>`;
                const $row = $(html);
                $('#purchaseTableBody').append($row);
                initProductSelect2($row.find('.product-select2'));
            };

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                recalcAll();
            });

            // Inputs -> Calc
            $('#purchaseTableBody').on('input', '.carton-qty, .loose-qty, .price, .item-disc-percent, .item-disc-amt', function() {
                recalcRow($(this).closest('tr'));
                recalcAll();
            });

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

            // --- Payment Section Logic ---
            $('#btnAddPayment').on('click', function() {
                const row = `
                <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                    <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                        <option value="" selected disabled>Select Account</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>`;
                $('#paymentWrapper').append(row);
            });

            $(document).on('click', '.remove-payment', function() {
                $(this).closest('.payment-row').remove();
                recalcPayments();
            });

            $(document).on('input', '.payment-amount', function() {
                recalcPayments();
            });

            function recalcPayments() {
                let total = 0;
                $('.payment-amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#totalPaid').text(total.toFixed(2));
            }

            function normalizeQtyInput($input, $row) {
                // Same logic as add_purchase_v2
                const val = $input.val();
                const ppb = parseFloat($row.find('.pack-size').val()) || 1;
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();

                if (sizeMode === 'by_pieces') {
                    if (val.includes('.')) {
                        $input.val(val.split('.')[0]);
                    }
                    return;
                }

                if (ppb > 1 && val.includes('.')) {
                    const parts = val.split('.');
                    const boxes = parseInt(parts[0]) || 0;
                    const loose = parts[1] ? parseInt(parts[1]) : 0;

                    if (loose >= ppb) {
                        const extraBoxes = Math.floor(loose / ppb);
                        const newLoose = loose % ppb;
                        const newBoxes = boxes + extraBoxes;
                        let newVal = newBoxes.toString();
                        if (newLoose > 0) newVal += '.' + newLoose;
                        $input.val(newVal);
                    }
                }
            }

            function recalcRow($row) {
                const ppb = parseFloat($row.find('.pack-size').val()) || 1;
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();
                const pieces_per_m2 = parseFloat($row.data('pieces_per_m2')) || parseFloat($row.find('.hidden-pieces-per-m2').val()) || 0;

                // Read separate Carton + Loose inputs
                const cartons = parseInt($row.find('.carton-qty').val()) || 0;
                let loose = parseInt($row.find('.loose-qty').val()) || 0;

                // Auto-convert excess loose into cartons
                if (loose >= ppb && ppb > 1) {
                    const extraCartons = Math.floor(loose / ppb);
                    loose = loose % ppb;
                    $row.find('.carton-qty').val(cartons + extraCartons);
                    $row.find('.loose-qty').val(loose);
                }

                const totalPieces = (cartons * ppb) + loose;

                // Update the readonly Pieces field (sent as qty[])
                $row.find('.qty-pcs').val(totalPieces);

                const price = parseFloat($row.find('.price').val()) || 0;

                // --- TOTAL CALCULATION ---
                let grossTotal = 0;

                if (sizeMode == 'by_size') {
                    // Price is per M2. Total M2 = totalPieces * pieces_per_m2 (m2/piece)
                    grossTotal = (totalPieces * pieces_per_m2) * price;
                } else {
                    // price is always treated as per-piece for purchase entry
                    grossTotal = totalPieces * price;
                }

                // Discount
                let discAmt = parseFloat($row.find('.item-disc-amt').val()) || 0;
                // If focus on %, calc amt
                if ($(document.activeElement).hasClass('item-disc-percent')) {
                    const pct = parseFloat($row.find('.item-disc-percent').val()) || 0;
                    discAmt = grossTotal > 0 ? grossTotal * (pct / 100) : 0;
                    $row.find('.item-disc-amt').val(discAmt.toFixed(2));
                } else {
                    // Else calc % from amt (default or if amt edited)
                    const pct = grossTotal > 0 ? (discAmt / grossTotal) * 100 : 0;
                    $row.find('.item-disc-percent').val(pct.toFixed(2));
                }

                const net = grossTotal - discAmt;
                $row.find('.row-total').val(net.toFixed(2));
            }

            function recalcAll() {
                let totalQty = 0;
                let subtotal = 0;
                let totalInlineDiscount = 0;

                $('#purchaseTableBody tr').each(function() {
                    const qty = parseFloat($(this).find('.qty-pcs').val()) || 0;
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
                $('#totalAmount').text(subtotal.toFixed(2));

                let additionalDiscount = parseFloat($('#discountInput').val()) || 0;
                let billDiscVal = parseFloat($('#billDiscount').val());

                if ($(document.activeElement).is('#billDiscount') || $(document.activeElement).is('#billDiscountPct')) {
                    // User is editing bill discount manually
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
                    // Inline discount or items changed: keep additional discount and update total discount
                    billDiscVal = totalInlineDiscount + additionalDiscount;
                    $('#billDiscount').val(billDiscVal.toFixed(2));
                }
                
                // Calc % from amount
                const pct = grossSubtotal > 0 ? (billDiscVal / grossSubtotal) * 100 : 0;
                $('#billDiscountPct').val(pct.toFixed(2));

                $('#discountInput').val(additionalDiscount.toFixed(2));

                const extraCost = parseFloat($('#extraCost').val()) || 0;

                const net = subtotal - additionalDiscount + extraCost;

                $('#tPayable').text(net.toFixed(2));
                $('#netAmountInput').val(net.toFixed(2));
            }

            function initProductSelect2($el) {
                $el.select2({
                    placeholder: 'Search Product...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '{{ route('products.ajax.search') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            // Map if needed or just return results
                            return {
                                results: data.results || data
                            };
                        },
                        cache: true
                    },
                    templateResult: formatProduct,
                    templateSelection: formatSelection
                });

                $el.on('select2:select', function(e) {
                    const data = e.params.data;
                    const $row = $(this).closest('tr');

                    // Populate Snapshots
                    $row.find('.hidden-size-mode').val(data.size_mode || '');
                    $row.find('.hidden-pieces-per-box').val(data.pieces_per_box || 1);
                    $row.find('.hidden-pieces-per-m2').val(data.pieces_per_m2 || 0);
                    $row.find('.hidden-length').val(data.length || '');
                    $row.find('.hidden-width').val(data.width || '');

                    $row.find('.pack-size').val(data.pieces_per_box || 1);

                    // Set default discount
                    $row.find('.item-disc-percent').val(data.purchase_discount_percent || 0);

                    // Set Price & Label
                    const sizeMode = data.size_mode || 'std';
                    const pM2 = parseFloat(data.purchase_price_per_m2) || 0;
                    const pPiece = parseFloat(data.purchase_price_per_piece) || 0;
                    const ppb = parseFloat(data.pieces_per_box) || 1;

                    let price = 0;
                    let unitLabel = '';

                    if (sizeMode === 'by_size') {
                        price = pM2;
                        unitLabel = '(m²)';
                    } else {
                        price = pPiece;
                        unitLabel = '(pieces)';
                    }

                    $row.find('.price').val(price);
                    // Add/Update label (remove old if any)
                    $row.find('.price-unit-label').remove();
                    $row.find('.price').after(
                        '<small class="text-muted price-unit-label" style="font-size:0.7rem;">' +
                        unitLabel + '</small>');

                    // Data Attributes
                    $row.data('sizemode', sizeMode);
                    $row.data('pieces_per_m2', Number(data.pieces_per_m2) || 0);

                    // Recalc
                    $row.find('.box-qty').focus();
                    recalcRow($row);
                    recalcAll();
                });
            }

            function formatProduct(repo) {
                if (repo.loading) return repo.text;
                let stock = repo.stock !== undefined ? repo.stock : 0;
                let sku = repo.sku || 'N/A';
                return $(`
                <div class="clearfix">
                    <div class="float-start">
                        <div class="fw-bold">${repo.name || repo.text}</div>
                        <small class="text-muted">SKU: ${sku}</small>
                    </div>
                    <div class="float-end">
                        <span class="badge bg-secondary rounded-pill">Stock: ${stock}</span>
                    </div>
                </div>`);
            }

            function formatSelection(repo) {
                return repo.name || repo.text;
            }
        });
    </script>
@endsection
