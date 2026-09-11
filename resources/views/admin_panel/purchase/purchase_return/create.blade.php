@extends('admin_panel.layout.app')

@section('content')
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">

    <style>
        /* ================= ENTERPRISE ERP DESIGN SYSTEM (COMPACT) ================= */
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
            padding: 0.85rem !important;
            max-width: 100%;
        }

        .erp-page-header {
            margin-bottom: 0.65rem;
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
            font-size: 0.72rem;
            color: #64748b;
            margin-bottom: 0;
            font-weight: 400;
        }

        /* Top Panel Card */
        .card-panel {
            background-color: #ffffff !important;
            border: 1.5px solid #dbeafe !important;
            border-radius: 10px !important;
            padding: 0.65rem 0.8rem !important;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.03) !important;
        }

        .section-title {
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.72rem !important;
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
            font-size: 0.66rem !important;
            font-weight: 700 !important;
            color: #1e40af !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            margin-bottom: 0.2rem !important;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-control, .form-select {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 6px !important;
            padding: 0.2rem 0.6rem !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
            height: 32px !important;
            font-size: 0.78rem !important;
        }

        .form-control:hover, .form-select:hover {
            border-color: #60a5fa !important;
            background-color: #f8fbff !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            background-color: #ffffff !important;
            outline: none !important;
        }

        .input-readonly, input[readonly].input-readonly, .form-control[readonly] {
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
            height: 30px !important;
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
            height: 30px !important;
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
            font-size: 0.68rem !important;
            letter-spacing: 0.04em;
            padding: 0.45rem 0.4rem !important;
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
            height: 28px !important;
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
            padding: 5px 0;
            border-bottom: 1px dashed #dbeafe;
            font-size: 0.78rem;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-val-net {
            font-weight: 800;
            color: #2563eb;
            font-size: 1.15rem;
            font-family: monospace;
        }

        .btn-save-complete {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 8px 18px !important;
            font-size: 0.85rem !important;
            border: none !important;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25) !important;
            transition: all 0.2s ease !important;
            cursor: pointer;
            width: 100%;
        }
        .btn-save-complete:hover {
            background: #1d4ed8 !important;
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(37, 99, 235, 0.35) !important;
            color: #ffffff !important;
        }

        /* Account Balance Badge */
        .account-balance-badge {
            font-size: 0.70rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .account-balance-badge.bal-positive {
            background-color: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid #a7f3d0 !important;
        }
        .account-balance-badge.bal-negative {
            background-color: #fef2f2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca !important;
        }
        .account-balance-badge.bal-zero {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
        }
    </style>

    <div class="container-fluid py-2 px-2">
        <div class="main-container bg-white mx-auto">

            {{-- Alert Section --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-2 py-2 px-3" role="alert" style="font-size:0.8rem;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.6rem;"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-2 py-2 px-3" role="alert" style="font-size:0.8rem;">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.6rem;"></button>
                </div>
            @endif

            <form action="{{ route('purchase.return.store') }}" method="POST" id="purchaseReturnForm" autocomplete="off">
                @csrf
                <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                <input type="hidden" name="warehouse_id" value="{{ $purchase->warehouse_id ?? 1 }}">
                <input type="hidden" name="vendor_id" value="{{ $purchase->vendor_id }}">
                <input type="hidden" name="total_amount_Words" id="amountInWords" value="">

                {{-- Hidden financial metrics for client-side math --}}
                <input type="hidden" id="purchaseTotalNet" value="{{ $purchaseTotalNet ?? $purchase->net_amount ?? 0 }}">
                <input type="hidden" id="vendorPaid" value="{{ $vendorPaid ?? $purchase->paid_amount ?? 0 }}">
                <input type="hidden" id="remainingInvoiceDue" value="{{ $remainingInvoiceDue ?? $purchase->due_amount ?? 0 }}">
                <input type="hidden" id="remainingPaidRefundable" value="{{ $remainingPaidRefundable ?? $purchase->paid_amount ?? 0 }}">

                {{-- ── TOP HEADER BAR ── --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 erp-page-header">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('Purchase.home') }}" class="btn btn-erp-pill-outline" title="Back to Purchase List">
                            <i class="fas fa-arrow-left"></i> <span>Back</span>
                        </a>
                        <div>
                            <h4 class="erp-title">
                                <i class="fas fa-undo-alt text-primary"></i> <span>Purchase Return &mdash; Inv #{{ $purchase->invoice_no }}</span>
                            </h4>
                            <p class="erp-subtitle">Process purchase returns, debit notes, vendor refunds, and warehouse restock</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge" style="background-color: #eff6ff; color: #1e40af; border: 1.5px solid #bfdbfe; font-size:0.75rem; padding: 5px 12px; border-radius: 50px;">
                            <i class="fas fa-file-invoice me-1"></i> Original Inv: {{ $purchase->invoice_no }}
                        </span>
                        <span class="badge" style="background-color: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0; font-size:0.75rem; padding: 5px 12px; border-radius: 50px;">
                            <i class="far fa-calendar-alt me-1"></i> Purchase Date: {{ $purchase->created_at->format('d/m/Y h:i A') }}
                        </span>
                    </div>
                </div>

                {{-- ── INVOICE FINANCIAL STATUS STRIP ── --}}
                @if($purchase)
                <div class="card-panel mb-2 py-2 px-3" style="background: #f8fafc; border-left: 4px solid #2563eb; border-radius: 8px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-semibold">Purchase Invoice Total:</span>
                            <strong class="font-monospace text-dark" style="font-size:0.85rem;">Rs {{ number_format($purchaseTotalNet ?? $purchase->net_amount ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-semibold">Paid / Advance Given:</span>
                            <strong class="font-monospace text-success" style="font-size:0.85rem;">Rs {{ number_format($vendorPaid ?? $purchase->paid_amount ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-semibold">Unpaid Invoice Balance (Due):</span>
                            <strong class="font-monospace text-danger" style="font-size:0.85rem;">Rs {{ number_format($remainingInvoiceDue ?? $purchase->due_amount ?? 0, 2) }}</strong>
                        </div>
                        <div>
                            <span class="badge rounded-pill bg-light text-primary border" style="font-size: 0.70rem; padding: 4px 10px;">
                                <i class="fas fa-shield-alt me-1"></i> Return value first settles unpaid debt; cash refund from vendor is capped to advance paid.
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ── TOP INFORMATION PANEL ── --}}
                <div class="card-panel mb-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-user-tie me-1"></i>Vendor</label>
                            <input type="text" class="form-control input-readonly" value="{{ optional($purchase->vendor)->name ?? 'Unknown Vendor' }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><i class="fas fa-hashtag me-1"></i>Reference / PO #</label>
                            <input type="text" name="reference" class="form-control input-readonly" value="{{ $purchase->invoice_no ?? '' }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><i class="far fa-calendar-alt me-1"></i>Return Date <span class="text-danger">*</span></label>
                            <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="far fa-comment-alt me-1"></i>Return Reason / Notes</label>
                            <input type="text" name="return_reason" class="form-control" placeholder="e.g. Damaged goods, excess, wrong item">
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-erp-pill-primary w-100" id="btnReturnAll">
                                <i class="fas fa-check-double"></i> Return All
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── TWO COLUMN MAIN LAYOUT ── --}}
                <div class="row g-2">

                    {{-- LEFT PANEL: ITEMS TABLE & RETURN STATUS STRIP (col-lg-8 col-xl-9) --}}
                    <div class="col-lg-8 col-xl-9">
                        <div class="table-responsive mb-2">
                            <table class="sales-table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 26%; text-align: left; padding-left: 10px;">Product</th>
                                        <th style="width: 10%;">Item Code</th>
                                        <th style="width: 8%;">Box Qty</th>
                                        <th style="width: 12%; text-align: right;">Price</th>
                                        <th style="width: 12%;">Remaining Qty</th>
                                        <th style="width: 12%;">Return Qty (Box.Pc)</th>
                                        <th style="width: 8%;">Return Pcs</th>
                                        <th style="width: 10%; text-align: right;">Total (Rs)</th>
                                        <th style="width: 2%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="returnItems">
                                    @foreach ($purchaseItems as $index => $item)
                                        @php
                                            $original = $item['original_qty'] ?? $item['qty'];
                                            $returned = $item['returned_qty'] ?? 0;
                                            $netRemaining = $item['max_returnable'] ?? ($original - $returned);
                                            $ppb = $item['pieces_per_box'] ?? 1;
                                            
                                            if ($ppb > 1) {
                                                $remBoxes = floor($netRemaining / $ppb);
                                                $remPcs = $netRemaining % $ppb;
                                                $remDisplay = $remBoxes . ($remPcs > 0 ? '.'.$remPcs : '');
                                            } else {
                                                $remDisplay = $netRemaining;
                                            }
                                        @endphp
                                        <tr>
                                            <input type="hidden" name="product_id[]" value="{{ $item['product_id'] }}">
                                            <input type="hidden" name="item_disc[]" class="item_disc" value="{{ $item['discount'] ?? 0 }}">
                                            <input type="hidden" name="unit[]" value="{{ $item['unit'] ?? 'pc' }}">
                                            <input type="hidden" name="size_mode[]" class="size-mode" value="{{ $item['size_mode'] ?? 'by_pieces' }}">
                                            <input type="hidden" name="pieces_per_m2[]" class="pieces-per-m2" value="{{ $item['pieces_per_m2'] ?? 0 }}">
                                            <input type="hidden" name="color[]" value="{{ $item['color'] }}">

                                            {{-- Product Name & Brand --}}
                                            <td style="padding-left: 10px;">
                                                <div class="fw-bold text-dark" style="font-size:0.80rem;">{{ $item['item_name'] }}</div>
                                                @if(!empty($item['brand']))
                                                    <small class="text-muted" style="font-size:0.68rem;">Brand: {{ $item['brand'] }}</small>
                                                @endif
                                            </td>

                                            {{-- Item Code --}}
                                            <td class="text-center">
                                                <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.72rem;">{{ $item['item_code'] ?: '-' }}</span>
                                            </td>

                                            {{-- PC per box --}}
                                            <td class="text-center">
                                                <input type="number" class="form-control text-center pieces-per-box input-readonly p-0 border-0"
                                                    value="{{ $item['pieces_per_box'] ?? 1 }}" readonly style="font-size:0.78rem; height:24px;">
                                            </td>

                                            {{-- Price --}}
                                            <td class="text-end">
                                                <input type="hidden" name="price[]" class="price" value="{{ (float) $item['price'] }}">
                                                <span class="fw-bold font-monospace" style="font-size:0.80rem;">{{ number_format((float) $item['price'], 2) }}</span>
                                                <small class="text-muted d-block" style="font-size: 0.64rem;">
                                                    @if (($item['size_mode'] ?? '') == 'by_size')
                                                        Per M²
                                                    @elseif(($item['size_mode'] ?? '') == 'by_cartons')
                                                        Per Box
                                                    @else
                                                        Per Pc
                                                    @endif
                                                </small>
                                            </td>

                                            {{-- Remaining Qty --}}
                                            <td class="text-center">
                                                <span class="fw-bold text-dark" style="font-size:0.80rem;">{{ $remDisplay }}</span>
                                                <small class="text-muted d-block" style="font-size: 0.64rem;">Rem: <strong>{{ $netRemaining }}</strong> pcs</small>
                                                @if ($returned > 0)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.60rem; padding: 1px 4px;">Ret: {{ $returned }}</span>
                                                @endif
                                            </td>

                                            {{-- Return Qty Box.Piece Input --}}
                                            <td class="text-center" style="padding: 2px 4px !important;">
                                                <input type="text" name="qty_box[]" class="form-control quantity-box w-100" value="0" placeholder="0.0" {{ $netRemaining <= 0 ? 'readonly' : '' }}>
                                                <small class="text-muted d-block" style="font-size: 0.62rem;">Box.Piece</small>
                                            </td>

                                            {{-- Total Return Pieces (Calculated) --}}
                                            <td class="text-center">
                                                <input type="number" name="qty[]" class="form-control text-center fw-bold quantity border-0 bg-transparent p-0"
                                                    value="0" readonly min="0" max="{{ $netRemaining }}" data-max="{{ $netRemaining }}"
                                                    data-original="{{ $original }}" data-returned="{{ $returned }}" style="color: #2563eb; font-size: 0.85rem;">
                                            </td>

                                            {{-- Total Amount --}}
                                            <td class="text-end font-monospace">
                                                <input type="text" name="total[]" class="form-control text-end fw-bold row-total border-0 bg-transparent p-0 text-dark"
                                                    value="0.00" readonly style="font-size: 0.85rem;">
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row rounded-circle p-0"
                                                    title="Remove Item" style="width: 22px; height: 22px; line-height: 20px;">
                                                    <i class="fas fa-times" style="font-size: 0.70rem;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Return Status & Words Strip (Sleek Compact Bar) --}}
                        <div class="card-panel py-2 px-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center gap-1">
                                    <i class="fas fa-cubes text-primary"></i>
                                    <span class="text-muted small">Total Pieces Returned:</span>
                                    <strong id="totalPieces" class="text-primary font-monospace" style="font-size:0.95rem;">0</strong>
                                </div>
                                <div class="vr d-none d-md-block"></div>
                                <div>
                                    <small class="text-muted me-1">Words:</small>
                                    <span id="amountInWordsDisplay" class="fw-semibold text-dark small fst-italic">Zero Rupees</span>
                                </div>
                            </div>

                            {{-- Sleek Clean ERP Return Progress Indicator --}}
                            <div class="d-flex align-items-center gap-2" style="min-width: 220px;">
                                <span id="returnTypeBadge" class="badge rounded-pill bg-light text-muted border" style="font-size: 0.68rem; padding: 4px 10px;">
                                    No Items Selected
                                </span>
                                <div class="progress flex-grow-1" style="height: 8px; background: #e2e8f0; border-radius: 50px; overflow: hidden; min-width: 90px;">
                                    <div id="returnProgressBar" class="progress-bar" role="progressbar" style="width: 0%; background: #2563eb; transition: width 0.3s ease;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span id="returnPercentage" class="fw-bold font-monospace text-primary" style="font-size: 0.75rem;">0%</span>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT PANEL: SUMMARY & REFUND PAYMENT (col-lg-4 col-xl-3) --}}
                    <div class="col-lg-4 col-xl-3">
                        <div class="d-flex flex-column h-100 gap-2">

                            {{-- Refund & Bill Settlement Summary Card --}}
                            <div class="card-panel p-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom" style="border-color: #dbeafe !important;">
                                    <span class="fw-bold text-dark" style="font-size:0.82rem;"><i class="fas fa-calculator text-primary me-1"></i>Summary & Settlement</span>
                                    <span class="badge rounded-pill px-2 py-0.5" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size:0.68rem;">Live</span>
                                </div>

                                <div class="summary-row">
                                    <span class="text-muted fw-semibold" style="font-size: 0.76rem;">Goods Return Subtotal</span>
                                    <span class="fw-bold text-dark font-monospace" id="displayBillAmount">0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="text-danger fw-semibold" style="font-size: 0.76rem;">Less: Extra Deductions</span>
                                    <div class="input-group input-group-sm" style="width: 100px;">
                                        <input type="number" name="extra_discount" id="extraDiscount" class="form-control text-end fw-bold text-danger font-monospace" value="0" min="0" step="0.01" style="height: 26px !important; font-size: 0.75rem !important;">
                                    </div>
                                </div>
                                <div class="summary-row pt-2 mt-1 border-top">
                                    <span class="fw-bold text-dark" style="font-size: 0.80rem;">Total Net Return</span>
                                    <span class="summary-val-net font-monospace text-primary fw-bold" id="displayNetAmount">0.00</span>
                                </div>

                                {{-- Accounting & Advance Allocation Breakdown --}}
                                @if($purchase)
                                <div class="mt-2 pt-2 border-top">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-balance-scale text-primary me-1"></i> Bill & Ledger Impact
                                        </span>
                                    </div>

                                    <div class="summary-row mb-1">
                                        <span class="text-secondary small" style="font-size: 0.73rem;">Original Bill Total:</span>
                                        <span class="fw-semibold text-dark font-monospace small" id="dispOriginalBill">Rs {{ number_format($purchaseTotalNet ?? 0, 2) }}</span>
                                    </div>

                                    <div class="summary-row mb-1">
                                        <span class="text-secondary small" style="font-size: 0.73rem;">
                                            <i class="fas fa-box-open text-info me-1"></i>Maal Jo Rakha (Retained):
                                        </span>
                                        <span class="fw-bold text-dark font-monospace small" id="dispGoodsRetained">Rs {{ number_format($purchaseTotalNet ?? 0, 2) }}</span>
                                    </div>

                                    <div class="summary-row mb-1">
                                        <span class="text-success small fw-semibold" style="font-size: 0.73rem;">
                                            <i class="fas fa-check-circle text-success me-1"></i>Advance Already Paid:
                                        </span>
                                        <span class="fw-bold text-success font-monospace small" id="dispAdvancePaid">- Rs {{ number_format($vendorPaid ?? 0, 2) }}</span>
                                    </div>

                                    <div class="my-1 border-top" style="border-color: #e2e8f0 !important;"></div>

                                    <div class="summary-row py-1 px-2 rounded mb-1" id="rowVendorLedgerDue" style="background-color: #fef2f2; border: 1px solid #fecaca;">
                                        <span class="text-danger fw-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-file-invoice-dollar me-1"></i>Bakaiya (Vendor Ledger):
                                        </span>
                                        <span class="fw-bold text-danger font-monospace" style="font-size: 0.86rem;" id="dispVendorLedgerDue">Rs 0.00</span>
                                    </div>

                                    <div class="summary-row py-1 px-2 rounded" id="rowCashRefund" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                                        <span class="text-success fw-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-hand-holding-usd me-1"></i>Cash Refund from Vendor:
                                        </span>
                                        <span class="fw-bold text-success font-monospace" style="font-size: 0.86rem;" id="displayMaxCashRefund">0.00</span>
                                    </div>
                                </div>
                                @else
                                <div class="mt-2 pt-2 border-top">
                                    <div class="summary-row">
                                        <span class="text-dark fw-bold small"><i class="fas fa-hand-holding-usd text-success me-1"></i>Max Cash Refund:</span>
                                        <span class="fw-bold text-success font-monospace" style="font-size:0.92rem;" id="displayMaxCashRefund">0.00</span>
                                    </div>
                                </div>
                                @endif

                                <input type="hidden" name="total_subtotal" id="billAmount" value="0.00">
                                <input type="hidden" name="net_amount" id="netAmount" value="0.00">
                            </div>

                            {{-- Refund Payment Card (Optional) --}}
                            <div class="card-panel p-3 bg-white flex-grow-1 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom" style="border-color: #dbeafe !important;">
                                    <span class="fw-bold text-dark" style="font-size:0.82rem;">
                                        <i class="fas fa-money-bill-wave text-success me-1"></i>Refund Received from Vendor <small class="text-muted fw-normal">(Opt)</small>
                                    </span>
                                </div>

                                <div class="alert alert-light border py-1 px-2 mb-2" id="refundHelpBox" style="font-size: 0.68rem; color: #475569; background: #f8fafc;">
                                    <span id="refundHelpText"><i class="fas fa-info-circle text-primary me-1"></i> Items return karne par settlement calculate hogi.</span>
                                </div>

                                <div class="payment-row mb-2">
                                    <div class="mb-2">
                                        <label class="form-label">Refund Account</label>
                                        <select name="payment_account_id[]" class="form-select payment-account rv-account" id="paymentAccountSelect">
                                            <option value="" selected disabled>Select Account (Cash/Bank)</option>
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}" data-balance="{{ $acc->current_balance ?? 0 }}">
                                                    {{ $acc->title }} ({{ $acc->account_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="account-balance-badge-wrap mt-1" id="accBalWrap" style="display: none;">
                                            <span class="account-balance-badge bal-zero" id="accBalBadge">
                                                <i class="bi bi-wallet2"></i> Bal: Rs. <span id="accBalVal">0.00</span> <span id="accBalType"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label mb-0">Refund Cash Amount</label>
                                            <a href="javascript:void(0)" id="btnFillMaxRefund" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.68rem;">Auto-fill Max</a>
                                        </div>
                                        <input type="number" name="payment_amount[]" id="refundAmount" step="0.01" class="form-control text-end payment-amount font-monospace fw-bold" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="mt-auto pt-2">
                                    <button type="submit" class="btn btn-save-complete" id="btnSubmitReturn">
                                        <i class="fas fa-check-circle me-1"></i> Process Purchase Return
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            function num(n) {
                return isNaN(parseFloat(n)) ? 0 : parseFloat(n);
            }

            function numberToWords(num) {
                const a = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
                    "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen",
                    "Eighteen", "Nineteen"
                ];
                const b = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
                if ((num = num.toString()).length > 9) return "Overflow";
                const n = ("000000000" + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{3})$/);
                if (!n) return "";
                let str = "";
                str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + " " + a[n[1][1]]) + " Crore " : "";
                str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + " " + a[n[2][1]]) + " Lakh " : "";
                str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + " " + a[n[3][1]]) + " Thousand " : "";
                str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + " " + a[n[4][1]]) + " " : "";
                return str.trim() + " Rupees Only";
            }

            function recalcRow($row) {
                const qty = num($row.find('.quantity').val());
                const price = num($row.find('.price').val());
                const sizeMode = $row.find('.size-mode').val();
                const ppm2 = num($row.find('.pieces-per-m2').val());

                let total = 0;
                if (sizeMode === 'by_size') {
                    total = qty * ppm2 * price;
                } else {
                    total = qty * price;
                }

                $row.find('.row-total').val(total.toFixed(2));
            }

            function recalcSummary() {
                let billAmount = 0;
                let totalQty = 0;

                $('#returnItems tr').each(function() {
                    const qty = num($(this).find('.quantity').val());
                    const rowTotal = num($(this).find('.row-total').val());

                    billAmount += rowTotal;
                    totalQty += qty;
                });

                const extraDiscount = num($('#extraDiscount').val());
                const netReturn = Math.max(0, billAmount - extraDiscount);

                // Financial metrics from invoice
                const purchaseTotalNet = num($('#purchaseTotalNet').val());
                const vendorPaid = num($('#vendorPaid').val());
                const remainingInvoiceDue = num($('#remainingInvoiceDue').val());
                const remainingPaidRefundable = num($('#remainingPaidRefundable').val());

                // Net Goods Retained (Maal Jo Rakha / Liya)
                const goodsRetained = Math.max(0, purchaseTotalNet - netReturn);

                // Settlement math:
                // If goodsRetained >= vendorPaid:
                // User has kept goods worth more than or equal to advance paid.
                // Vendor is still owed money: newVendorLedgerDue = goodsRetained - vendorPaid.
                // Cash refund = 0.00
                // If vendorPaid > goodsRetained:
                // User returned goods beyond the unpaid bill, so excess advance is refundable in cash.
                // newVendorLedgerDue = 0.00
                // maxCashRefund = min(vendorPaid - goodsRetained, remainingPaidRefundable)

                let newVendorLedgerDue = 0;
                let maxCashRefund = 0;

                if (purchaseTotalNet > 0) {
                    if (goodsRetained >= vendorPaid) {
                        newVendorLedgerDue = goodsRetained - vendorPaid;
                        maxCashRefund = 0;
                    } else {
                        newVendorLedgerDue = 0;
                        maxCashRefund = Math.min(vendorPaid - goodsRetained, remainingPaidRefundable);
                    }
                } else {
                    maxCashRefund = netReturn;
                }

                $('#billAmount').val(billAmount.toFixed(2));
                $('#displayBillAmount').text(billAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#netAmount').val(netReturn.toFixed(2));
                $('#displayNetAmount').text(netReturn.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                $('#dispOriginalBill').text('Rs ' + purchaseTotalNet.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#dispGoodsRetained').text('Rs ' + goodsRetained.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#dispAdvancePaid').text('- Rs ' + vendorPaid.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#dispVendorLedgerDue').text('Rs ' + newVendorLedgerDue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#displayMaxCashRefund').text(maxCashRefund.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                // Dynamic helper message in payment box
                if (maxCashRefund > 0) {
                    $('#refundHelpText').html('<i class="fas fa-hand-holding-usd text-success me-1"></i> Eligible Cash Refund: Up to <strong>Rs. ' + maxCashRefund.toLocaleString(undefined, {minimumFractionDigits: 2}) + '</strong> (Advance cash refund).');
                    $('#refundHelpBox').removeClass('alert-light alert-warning').addClass('alert-success');
                    $('#btnFillMaxRefund').show();
                    $('#refundAmount').prop('readonly', false);
                } else {
                    if (netReturn > 0) {
                        $('#refundHelpText').html('<i class="fas fa-balance-scale text-primary me-1"></i> Cash refund Rs 0.00 hai. Return amount (Rs. ' + netReturn.toLocaleString(undefined, {minimumFractionDigits: 2}) + ') se bill adjust hoga. <strong>Bakaiya (Vendor Ledger): Rs. ' + newVendorLedgerDue.toLocaleString(undefined, {minimumFractionDigits: 2}) + '</strong>.');
                    } else {
                        $('#refundHelpText').html('<i class="fas fa-info-circle text-primary me-1"></i> Items return karne par settlement calculate hogi.');
                    }
                    $('#refundHelpBox').removeClass('alert-success alert-warning').addClass('alert-light');
                    $('#btnFillMaxRefund').hide();
                    $('#refundAmount').val('');
                }

                if (netReturn > 0) {
                    const words = numberToWords(Math.round(netReturn));
                    $('#amountInWords').val(words);
                    $('#amountInWordsDisplay').text(words);
                } else {
                    $('#amountInWords').val('Zero Rupees');
                    $('#amountInWordsDisplay').text('Zero Rupees');
                }

                $('#totalPieces').text(totalQty);
                updatePartialReturnIndicator();
            }

            // Auto fill max cash refund
            $('#btnFillMaxRefund').click(function() {
                const maxCash = parseFloat($('#displayMaxCashRefund').text().replace(/[^0-9.]/g, '')) || 0;
                $('#refundAmount').val(maxCash > 0 ? maxCash.toFixed(2) : '');
            });

            // When account is selected, if refund amount is blank, auto fill max cash refund
            $('#paymentAccountSelect').on('change', function() {
                if ($(this).val()) {
                    const currentVal = num($('#refundAmount').val());
                    const maxCash = parseFloat($('#displayMaxCashRefund').text().replace(/[^0-9.]/g, '')) || 0;
                    if (currentVal === 0 && maxCash > 0) {
                        $('#refundAmount').val(maxCash.toFixed(2));
                    }
                }
            });

            // Validate cash refund doesn't exceed advance paid
            $('#refundAmount').on('input blur', function() {
                const entered = num($(this).val());
                const maxCash = parseFloat($('#displayMaxCashRefund').text().replace(/[^0-9.]/g, '')) || 0;
                if (entered > maxCash + 0.05) {
                    alert('Vendor se cash refund sirf Rs. ' + maxCash.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' banta hai. Baqiya amount vendor ledger mein bill balance adjust kar rahi hai.');
                    $(this).val(maxCash > 0 ? maxCash.toFixed(2) : '');
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
                        boxDisplay = boxes + (pieces > 0 ? '.' + pieces : '');
                    } else {
                        boxDisplay = maxQty;
                    }

                    $row.find('.quantity-box').val(boxDisplay);
                    recalcRow($row);
                });
                recalcSummary();
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
                        boxDisplay = boxes + (pieces > 0 ? '.' + pieces : '');
                    } else {
                        boxDisplay = maxQty;
                    }

                    $(this).val(boxDisplay);
                    $(this).trigger('input');
                    $(this).select();
                }
            });

            // Box.Piece Input Logic
            $(document).on('input', '.quantity-box', function() {
                const $row = $(this).closest('tr');
                const val = $(this).val();
                const ppb = num($row.find('.pieces-per-box').val());

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

                $row.find('.quantity').val(totalPieces);
                $row.find('.quantity').trigger('input');
            });

            // Events
            $(document).on('input', '.quantity, .price, #extraDiscount', function() {
                const $row = $(this).closest('tr');

                if ($(this).hasClass('quantity')) {
                    const qtyPc = num($(this).val());
                    const maxReturnable = num($(this).attr('data-max'));

                    if (qtyPc > maxReturnable) {
                        $(this).val(maxReturnable);
                        $(this).addClass('border-danger');

                        setTimeout(() => {
                            $(this).removeClass('border-danger');
                        }, 2000);
                    }
                }

                if ($row.length) {
                    recalcRow($row);
                }
                recalcSummary();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                if (confirm('Are you sure you want to exclude this item from the return?')) {
                    $(this).closest('tr').remove();
                    recalcSummary();
                }
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
                    $('#returnTypeBadge').removeClass().addClass('badge rounded-pill bg-light text-muted border');
                    $('#returnProgressBar').css('background', '#94a3b8');
                } else if (returnPercentage >= 100) {
                    $('#returnTypeBadge').html('<i class="fas fa-check-circle me-1"></i>Full Return');
                    $('#returnTypeBadge').removeClass().addClass('badge rounded-pill bg-success-subtle text-success border border-success');
                    $('#returnProgressBar').css('background', '#10b981');
                } else {
                    $('#returnTypeBadge').html('<i class="fas fa-chart-pie me-1"></i>Partial Return');
                    $('#returnTypeBadge').removeClass().addClass('badge rounded-pill bg-warning-subtle text-warning border border-warning');
                    $('#returnProgressBar').css('background', '#f59e0b');
                }
            }

            // Account Balance Badge Logic
            $('#paymentAccountSelect').on('change', function() {
                const optBal = $(this).find('option:selected').data('balance');
                const $wrap = $('#accBalWrap');
                const $badge = $('#accBalBadge');
                const $val = $('#accBalVal');
                const $type = $('#accBalType');

                if ($(this).val() && optBal !== undefined && optBal !== null && optBal !== '') {
                    const bal = parseFloat(optBal) || 0;
                    $val.text(Math.abs(bal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $badge.removeClass('bal-positive bal-negative bal-zero');
                    if (bal > 0) {
                        $badge.addClass('bal-positive');
                        $type.text('Dr');
                    } else if (bal < 0) {
                        $badge.addClass('bal-negative');
                        $type.text('Cr');
                    } else {
                        $badge.addClass('bal-zero');
                        $type.text('');
                    }
                    $wrap.show();
                } else {
                    $wrap.hide();
                }
            });

            // Initialize on load
            $('#returnItems tr').each(function() {
                recalcRow($(this));
            });
            recalcSummary();
            if ($('#paymentAccountSelect').val()) {
                $('#paymentAccountSelect').trigger('change');
            }
        });
    </script>
@endsection