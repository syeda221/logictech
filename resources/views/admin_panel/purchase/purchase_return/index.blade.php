@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Purchase Returns Management
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

        /* Page Layout */
        .erp-page-header {
            margin-bottom: 1.5rem;
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
        .btn-erp-outline {
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #334155 !important;
            font-weight: 600;
            font-size: 0.815rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
        }
        .btn-erp-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a !important;
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

        /* Main Data Card & Table Container */
        .erp-main-card {
            background: #ffffff;
            border: 1.5px solid #dbeafe;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
            overflow: hidden;
        }
        .erp-table-responsive {
            border-radius: 8px;
            width: 100%;
            overflow-x: auto;
        }
        .erp-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 0;
            border: 1.5px solid #bfdbfe !important;
            border-radius: 8px;
        }
        .erp-table thead th {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            font-weight: 700 !important;
            font-size: 0.70rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            padding: 0.65rem 0.5rem !important;
            border: 1px solid #bfdbfe !important;
            border-bottom: 2px solid #60a5fa !important;
            white-space: nowrap;
            vertical-align: middle !important;
        }
        .erp-table tbody td {
            padding: 0.55rem 0.5rem !important;
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

        /* Bill Tag */
        .erp-bill-tag {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            font-size: 0.76rem;
            color: #2563eb;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 0.2rem 0.45rem;
            border-radius: 5px;
            display: inline-block;
        }

        /* Avatar */
        .erp-avatar {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.70rem;
            font-weight: 700;
            flex-shrink: 0;
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }

        /* Status Badges */
        .erp-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.22rem 0.55rem;
            border-radius: 50px;
            font-size: 0.69rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            line-height: 1.15;
            white-space: nowrap;
        }
        .erp-badge.badge-returned {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .erp-badge.badge-partial-return {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .erp-badge.badge-draft {
            background-color: #f8fafc;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Action Buttons */
        .btn-erp-table-action {
            background-color: #ffffff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: 600;
            border-radius: 6px;
            height: 28px;
            padding: 0 0.65rem;
            font-size: 0.73rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            transition: all 0.15s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .btn-erp-table-action:hover {
            background-color: #eff6ff;
            border-color: #3b82f6;
            color: #1e40af;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.15);
        }

        /* DataTables Modern ERP Controls */
        .dataTables_wrapper {
            font-size: 0.80rem;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 0.85rem;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 6px !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.78rem !important;
            color: #0f172a !important;
            outline: none !important;
        }
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 0.85rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #bfdbfe !important;
            border-radius: 6px !important;
            padding: 0.3rem 0.6rem 0.3rem 2rem !important;
            font-size: 0.78rem !important;
            color: #0f172a !important;
            outline: none !important;
            background-color: #ffffff !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233b82f6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3ccircle cx='11' cy='11' r='8'%3e%3c/circle%3e%3cline x1='21' y1='21' x2='16.65' y2='16.65'%3e%3c/line%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: 8px center !important;
            background-size: 13px 13px !important;
            transition: all 0.15s ease-in-out !important;
            min-width: 220px;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 0.76rem !important;
            color: #64748b !important;
            font-weight: 600 !important;
            padding-top: 0.75rem !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0.75rem !important;
            padding-top: 0.25rem !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            padding: 4px 10px !important;
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
            opacity: 0.45;
            cursor: not-allowed;
            border-color: #e2e8f0 !important;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Page Header --}}
                <div class="erp-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h1 class="erp-title">
                            <i class="fas fa-undo-alt text-primary"></i> Purchase Returns
                        </h1>
                        <p class="erp-subtitle">Manage vendor debit notes, purchase return vouchers, debt settlements &amp; refunds</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a class="btn-erp-outline" href="{{ route('Purchase.home') }}">
                            <i class="fas fa-arrow-left"></i> Back to Purchases
                        </a>
                    </div>
                </div>

                {{-- Metric KPI Cards --}}
                @php
                    $totalReturnsCount = $returns->count();
                    $totalReturnValue = (float) $returns->sum('net_amount');
                    $totalRefundPaid = (float) $returns->sum('paid');
                    $totalDueSettled = max(0, $totalReturnValue - $totalRefundPaid);
                @endphp

                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Total Returns</div>
                                <div class="erp-kpi-value text-primary font-monospace">{{ $totalReturnsCount }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #2563eb;">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Return Goods Value</div>
                                <div class="erp-kpi-value text-danger font-monospace">Rs {{ number_format($totalReturnValue, 2) }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #fef2f2; color: #dc2626;">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Bill Due Settled</div>
                                <div class="erp-kpi-value font-monospace text-dark">Rs {{ number_format($totalDueSettled, 2) }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #eff6ff; color: #3b82f6;">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="erp-kpi-card">
                            <div>
                                <div class="erp-kpi-label">Cash Refund Received</div>
                                <div class="erp-kpi-value font-monospace" style="color: #059669;">Rs {{ number_format($totalRefundPaid, 2) }}</div>
                            </div>
                            <div class="erp-kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Data Card --}}
                <div class="erp-main-card">
                    <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center" style="border-color: #dbeafe !important;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark" style="font-size: 0.90rem;">
                                <i class="fas fa-list-alt text-primary me-1"></i> Purchase Return Records
                            </span>
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 font-monospace" style="font-size: 0.70rem;">
                                {{ $totalReturnsCount }} Records
                            </span>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="table-responsive erp-table-responsive">
                            <table id="return-table" class="erp-table table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 45px;">ID</th>
                                        <th style="min-width: 120px;">Invoice #</th>
                                        <th style="min-width: 130px;">Vendor</th>
                                        <th style="min-width: 110px;">Warehouse</th>
                                        <th class="text-center" style="min-width: 90px;">Return Date</th>
                                        <th class="text-end text-danger" style="min-width: 110px;">Return Amount</th>
                                        <th class="text-end" style="min-width: 110px;">Orig Purchase</th>
                                        <th class="text-end" style="min-width: 110px;">Total Returned</th>
                                        <th class="text-end text-success" style="min-width: 110px;">New Net Amount</th>
                                        <th class="text-end text-warning" style="min-width: 100px;">New Due</th>
                                        <th class="text-center" style="min-width: 100px;">Status</th>
                                        <th class="text-center" style="width: 75px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($returns as $return)
                                        @php
                                            $isPartialReturn =
                                                $return->purchase &&
                                                $return->total_returned < $return->original_net_amount;
                                            $isFullReturn =
                                                $return->purchase &&
                                                $return->total_returned >= $return->original_net_amount;
                                            $initial = strtoupper(substr($return->vendor->name ?? 'V', 0, 1));
                                        @endphp
                                        <tr>
                                            {{-- ID --}}
                                            <td class="text-center font-monospace text-muted fw-bold" data-order="{{ $return->id }}">
                                                #{{ $return->id }}
                                            </td>

                                            {{-- Invoice # --}}
                                            <td>
                                                <span class="erp-bill-tag">{{ $return->return_invoice }}</span>
                                                @if ($return->purchase)
                                                    <div class="text-muted small mt-1" style="font-size: 0.68rem;">
                                                        <i class="fas fa-file-invoice text-primary me-1"></i>Orig: <strong class="text-dark">{{ $return->purchase->invoice_no }}</strong>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Vendor --}}
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="erp-avatar">
                                                        {{ $initial }}
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold text-dark">{{ $return->vendor->name ?? 'N/A' }}</span>
                                                        @if(optional($return->vendor)->mobile)
                                                            <div class="text-muted small" style="font-size: 0.68rem;">{{ $return->vendor->mobile }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Warehouse --}}
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-warehouse text-muted me-1"></i>{{ $return->warehouse->warehouse_name ?? 'Main Store' }}
                                                </span>
                                            </td>

                                            {{-- Return Date --}}
                                            <td class="text-center font-monospace text-muted" data-order="{{ \Carbon\Carbon::parse($return->return_date)->timestamp }}">
                                                {{ \Carbon\Carbon::parse($return->return_date)->format('d/m/Y') }}
                                            </td>

                                            {{-- Return Amount --}}
                                            <td class="text-end font-monospace text-danger fw-bold" data-order="{{ (float)$return->net_amount }}">
                                                -{{ number_format($return->net_amount, 2) }}
                                                @if($return->paid > 0)
                                                    <div class="text-success small" style="font-size: 0.65rem;" title="Cash Refund Received">
                                                        <i class="fas fa-money-bill-wave me-1"></i>Refund: {{ number_format($return->paid, 2) }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Original Purchase Amount --}}
                                            <td class="text-end font-monospace text-dark fw-semibold" data-order="{{ (float)($return->original_net_amount ?? 0) }}">
                                                @if ($return->purchase)
                                                    {{ number_format($return->original_net_amount, 2) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Total Returned --}}
                                            <td class="text-end font-monospace text-danger fw-bold" data-order="{{ (float)($return->total_returned ?? $return->net_amount) }}">
                                                @if ($return->purchase)
                                                    {{ number_format($return->total_returned, 2) }}
                                                @else
                                                    {{ number_format($return->net_amount, 2) }}
                                                @endif
                                            </td>

                                            {{-- New Net Amount --}}
                                            <td class="text-end font-monospace text-success fw-bold" data-order="{{ (float)($return->new_net_amount ?? 0) }}">
                                                @if ($return->purchase)
                                                    {{ number_format($return->new_net_amount, 2) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- New Due Amount --}}
                                            <td class="text-end font-monospace text-warning fw-bold" data-order="{{ (float)($return->new_due_amount ?? 0) }}">
                                                @if ($return->purchase)
                                                    {{ number_format($return->new_due_amount, 2) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Status Badge --}}
                                            <td class="text-center">
                                                @if ($isFullReturn)
                                                    <span class="erp-badge badge-returned">
                                                        <i class="fas fa-undo"></i> Full Return
                                                    </span>
                                                @elseif($isPartialReturn)
                                                    <span class="erp-badge badge-partial-return">
                                                        <i class="fas fa-chart-pie"></i> Partial Return
                                                    </span>
                                                @else
                                                    <span class="erp-badge badge-draft">
                                                        <i class="fas fa-circle" style="font-size: 5px;"></i> Standalone
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center">
                                                <a href="{{ route('purchase.return.view', $return->id) }}" class="btn-erp-table-action" title="View Return Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Scripts --}}
                <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
                <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

                <script>
                    $(document).ready(function() {
                        $('#return-table').DataTable({
                            pageLength: 10,
                            lengthMenu: [5, 10, 25, 50, 100],
                            order: [
                                [0, 'desc']
                            ],
                            language: {
                                search: "",
                                searchPlaceholder: "Search return, vendor, bill...",
                                lengthMenu: "Show _MENU_ entries",
                                info: "Showing _START_ to _END_ of _TOTAL_ returns",
                                paginate: {
                                    previous: '<i class="fas fa-chevron-left"></i>',
                                    next: '<i class="fas fa-chevron-right"></i>'
                                }
                            }
                        });
                    });
                </script>

            </div>
        </div>
    </div>
@endsection