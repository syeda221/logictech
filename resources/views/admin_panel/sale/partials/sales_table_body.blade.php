@foreach ($sales as $sale)
    @php
        $pNames = 'N/A';
        if ($sale->items && $sale->items->count() > 0) {
            $pNames = $sale->items
                ->map(fn($item) => optional($item->product)->item_name ?? '?')
                ->implode(', ');
        } elseif ($sale->product) {
            $pNames = $sale->product;
        }

        // 1. Sale Status (Accounting/Workflow)
        $saleStatusBadge = '<span class="erp-badge badge-draft"><i class="fas fa-file-alt me-1"></i>Draft</span>';
        $isExchange = \Illuminate\Support\Str::startsWith($sale->reference, 'Exchange for');
        
        if ($sale->sale_status === 'posted') {
            if ($sale->is_booking) {
                $saleStatusBadge = '<span class="erp-badge badge-posted"><i class="fas fa-check-circle me-1"></i>Confirmed Order</span>';
            } elseif ($isExchange) {
                $saleStatusBadge = '<span class="erp-badge badge-exchange"><i class="fas fa-exchange-alt me-1"></i>Exchange</span>';
            } else {
                $saleStatusBadge = '<span class="erp-badge badge-posted"><i class="fas fa-check-circle me-1"></i>Posted</span>';
            }
        } elseif ($sale->sale_status === 'booked') {
            $saleStatusBadge = '<span class="erp-badge badge-booked"><i class="fas fa-bookmark me-1"></i>Order</span>';
        } elseif ($sale->sale_status === 'returned' || $sale->sale_status == 1) {
            $saleStatusBadge = '<span class="erp-badge badge-returned"><i class="fas fa-undo me-1"></i>Returned</span>';
        } elseif ($sale->sale_status === 'draft') {
            $saleStatusBadge = '<span class="erp-badge badge-draft"><i class="fas fa-file-alt me-1"></i>Draft</span>';
        } else {
            $saleStatusBadge = '<span class="erp-badge badge-posted"><i class="fas fa-check-circle me-1"></i>Posted</span>';
        }

        if ($sale->returns && $sale->returns->count() > 0) {
            $saleStatusBadge .= '<div class="mt-1"><span class="erp-badge badge-partial-return"><i class="fas fa-undo-alt me-1"></i>Partial Return</span></div>';
        }

        // 2. Product / Order Condition Status (pending, ready, delivered, cancelled)
        $ordStatus = strtolower($sale->order_status ?? 'pending');
        if ($ordStatus === 'ready') {
            $orderStatusBadge = '<span class="erp-badge state-ready"><i class="fas fa-box me-1"></i>Ready</span>';
        } elseif ($ordStatus === 'delivered') {
            $orderStatusBadge = '<span class="erp-badge state-delivered"><i class="fas fa-truck me-1"></i>Delivered</span>';
        } elseif ($ordStatus === 'cancelled') {
            $orderStatusBadge = '<span class="erp-badge state-cancelled"><i class="fas fa-ban me-1"></i>Cancelled</span>';
        } else {
            $orderStatusBadge = '<span class="erp-badge state-pending"><i class="fas fa-hourglass-half me-1"></i>Pending</span>';
        }

        // Serial numbers are shown ONLY after delivered
        $serialNumbers = collect();
        if ($ordStatus === 'delivered' && $sale->items && $sale->items->count() > 0) {
            $serialNumbers = $sale->items->pluck('serial_no')->filter(function($val) {
                return !is_null($val) && trim($val) !== '';
            })->values();
        }

        $inline_val = $sale->items ? $sale->items->sum('discount_amount') : 0;
        $bill_amount = $sale->total_bill_amount > 0 ? $sale->total_bill_amount : (float) $sale->per_total;
        $gross_subtotal = $bill_amount + $inline_val;
        $inline_pct = $gross_subtotal > 0 ? ($inline_val / $gross_subtotal) * 100 : 0;

        $collected = $sale->cash - $sale->change;
        $refunded = 0;
        if (isset($isExchange) && $isExchange && $collected <= 0) {
            $refundPayment = \App\Models\CustomerPayment::where('note', 'Refund Paid for POS Exchange #'.$sale->invoice_no)->first();
            if ($refundPayment) {
                $refunded = $refundPayment->amount;
            }
        }

        // Balance & Status Determination
        $isOrderCancelled = ($ordStatus === 'cancelled');
        $isUnconfirmedOrder = ($sale->sale_status === 'booked' || $sale->sale_status === 'draft');
        $isFullyReturned = ($sale->sale_status === 'returned' || $sale->sale_status == 1);
        $hasReturns = ($sale->returns && $sale->returns->count() > 0);

        $balanceDisplayVal = '-';
        $balanceIsDue = false;
        $balanceBadgeHtml = '';

        if ($isOrderCancelled) {
            $balanceDisplayVal = '-';
            $balanceBadgeHtml = '<div class="mt-0.5"><span class="erp-badge state-cancelled" style="font-size: 0.65rem; padding: 1px 6px;"><i class="fas fa-ban me-1"></i>Cancelled</span></div>';
        } elseif ($isUnconfirmedOrder) {
            $balanceDisplayVal = '-';
            $balanceBadgeHtml = '<div class="mt-0.5"><span class="erp-badge badge-booked" style="font-size: 0.65rem; padding: 1px 6px;"><i class="fas fa-bookmark me-1"></i>Unconfirmed</span></div>';
        } elseif ($isFullyReturned || ($hasReturns && $sale->returns->sum('net_amount') >= $sale->total_net)) {
            $totalRefundable = (float) $sale->returns->sum('refundable_amount');
            $totalRefundPaid = (float) $sale->returns->sum('paid');
            $refundRemainingDue = max(0, $totalRefundable - $totalRefundPaid);

            if ($refundRemainingDue > 0) {
                $balanceIsDue = true;
                $balanceDisplayVal = 'Rs. ' . number_format($refundRemainingDue, 2);
                $balanceBadgeHtml = '<div class="mt-0.5"><span class="erp-badge" style="font-size: 0.65rem; padding: 1px 6px; color: #dc2626; background-color: #fef2f2; border: 1px solid #fecaca;"><i class="fas fa-clock me-1"></i>Due</span></div>';
            } else {
                $balanceDisplayVal = 'Rs. 0.00';
                $balanceBadgeHtml = '<div class="mt-0.5"><span class="erp-badge badge-returned" style="font-size: 0.65rem; padding: 1px 6px;"><i class="fas fa-undo me-1"></i>Returned</span></div>';
            }
        } else {
            $salePaid = (float)($sale->cash ?? 0) + (float)($sale->card ?? 0);
            $saleNet = (float)($sale->total_net ?? 0);
            $retDueAdj = $hasReturns ? (float)$sale->returns->sum('due_adjusted') : 0;
            
            if (isset($isExchange) && $isExchange) {
                $remainingBalance = 0;
            } else {
                $remainingBalance = max(0, round($saleNet - $salePaid - $retDueAdj, 2));
            }

            if ($remainingBalance > 0) {
                $balanceIsDue = true;
                $balanceDisplayVal = 'Rs. ' . number_format($remainingBalance, 2);
                $balanceBadgeHtml = '<div class="mt-0.5"><span class="erp-badge" style="font-size: 0.65rem; padding: 1px 6px; color: #dc2626; background-color: #fef2f2; border: 1px solid #fecaca;"><i class="fas fa-clock me-1"></i>Due</span></div>';
            } else {
                $balanceDisplayVal = 'Rs. 0.00';
                $balanceBadgeHtml = '<div class="mt-0.5"><span class="erp-badge badge-posted" style="font-size: 0.65rem; padding: 1px 6px;"><i class="fas fa-check-circle me-1"></i>Paid</span></div>';
            }
        }
        // Customer Name determination
        $custDisplayName = optional($sale->customer_relation)->customer_name;
        $isWalkin = false;
        if (empty($custDisplayName)) {
            if (!empty($sale->walkin_name)) {
                $custDisplayName = $sale->walkin_name;
                $isWalkin = true;
            } else {
                $custDisplayName = 'Walk-in Customer';
                $isWalkin = true;
            }
        }
        $custInitial = strtoupper(substr($custDisplayName, 0, 1));
    @endphp

    {{-- Desktop Table Row (≥ 768px) --}}
    <tr class="border-bottom-0 d-none d-md-table-row">
        <td class="ps-2 text-center" data-sort="{{ $sale->id }}"><span class="erp-bill-tag">#{{ $sale->id }}</span></td>
        <td>
            <div class="d-flex align-items-center gap-1.5" style="max-width: 140px;">
                <div class="erp-avatar {{ $isWalkin ? 'erp-avatar-walkin' : 'erp-avatar-registered' }}">
                    {{ $custInitial }}
                </div>
                <div class="d-flex flex-column text-truncate">
                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.78rem;" title="{{ $custDisplayName }}">{{ $custDisplayName }}</span>
                    @if ($isWalkin && !empty($sale->walkin_name))
                        <span class="text-muted text-truncate" style="font-size: 0.65rem; font-weight: 500;">Walk-in</span>
                    @endif
                </div>
            </div>
        </td>
        <td class="sale-serial-cell" data-sale-id="{{ $sale->id }}">
            @if ($ordStatus === 'delivered' && $serialNumbers->isNotEmpty())
                @if ($serialNumbers->count() === 1)
                    <span class="font-monospace fw-bold text-dark" style="font-size: 0.75rem;">{{ $serialNumbers->first() }}</span>
                @else
                    <div class="d-flex flex-column gap-0.5">
                        @foreach ($serialNumbers as $sn)
                            <span class="font-monospace fw-bold text-dark text-truncate" style="font-size: 0.72rem;" title="{{ $sn }}">{{ $sn }}</span>
                        @endforeach
                    </div>
                @endif
            @else
                <span class="font-monospace text-muted" style="font-size: 0.75rem;">-</span>
            @endif
        </td>
        <td title="{{ $pNames }}" class="text-muted small" style="max-width: 120px;">
            <div class="text-truncate" style="max-width: 120px;">
                {{ \Illuminate\Support\Str::limit($pNames, 22) }}
            </div>
        </td>
        <td class="text-center font-monospace fw-semibold text-dark" style="font-size: 0.78rem;">
            {{ $sale->total_items > 0 ? $sale->total_items : $sale->qty }}
        </td>
        <td class="text-end fw-bold text-dark font-monospace" style="font-size: 0.78rem;">
            Rs. {{ number_format($gross_subtotal, 2) }}
        </td>
        <td class="text-end text-dark font-monospace">
            @if ($sale->total_extradiscount > 0)
                @php
                    $add_val = $sale->total_extradiscount;
                    $add_pct = $bill_amount > 0 ? ($add_val / $bill_amount) * 100 : 0;
                @endphp
                <span class="badge rounded-pill border px-1.5 py-0.5" style="background-color: #fffbeb; color: #b45309; border-color: #fde68a !important; font-size: 10px; font-weight: 700; display: inline-flex; align-items: center; gap: 2px;">
                    <i class="fas fa-tag" style="font-size: 8px;"></i> Rs. {{ number_format($add_val, 2) }}
                </span>
                <div class="text-muted small mt-0.5" style="font-size: 9px;">({{ number_format($add_pct, 1) }}%)</div>
            @else
                <span class="text-muted" style="font-size: 0.75rem;">Rs. 0.00</span>
            @endif
        </td>
        <td class="text-end fw-bold font-monospace" style="color: #047857; font-size: 0.80rem;">
            @if (isset($isExchange) && $isExchange)
                @if ($collected > 0)
                    Rs. {{ number_format($collected, 2) }}
                @elseif ($refunded > 0)
                    <span class="text-danger">-Rs. {{ number_format($refunded, 2) }}</span>
                @else
                    Rs. 0.00
                @endif
                <div class="mt-0.5"><span class="erp-badge badge-exchange" style="font-size: 9px; padding: 1px 4px;"><i class="fas fa-exchange-alt me-1"></i>Exchange</span></div>
            @else
                Rs. {{ number_format($sale->total_net, 2) }}
            @endif
        </td>
        <td class="text-end font-monospace">
            @if ($balanceIsDue)
                <span class="fw-bold" style="color: #dc2626; font-size: 0.80rem;">{{ $balanceDisplayVal }}</span>
            @elseif ($balanceDisplayVal === '-')
                <span class="text-muted fw-bold" style="font-size: 0.80rem;">-</span>
            @else
                <span class="fw-bold text-success" style="font-size: 0.80rem;">{{ $balanceDisplayVal }}</span>
            @endif
            {!! $balanceBadgeHtml !!}
        </td>
        <td class="text-nowrap small text-muted font-monospace" style="font-size: 0.75rem;">
            {{ $sale->created_at->format('d/m/Y') }}
        </td>
        <td class="text-nowrap text-center">{!! $saleStatusBadge !!}</td>
        <td class="text-nowrap text-center sale-state-cell" data-sale-id="{{ $sale->id }}">{!! $orderStatusBadge !!}</td>
        <td class="pe-2 text-center text-nowrap">
            <div class="dropdown d-inline-block">
                <button class="btn btn-erp-table-action dropdown-toggle shadow-none" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v small me-1"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3 py-2" style="min-width: 175px;">
                    @can('sales.edit')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.edit', $sale->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit
                            </a>
                        </li>
                    @endcan

                    @can('sales.view')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', $sale->id) }}" target="_blank">
                                <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                            </a>
                        </li>
                        @if($ordStatus === 'delivered')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.technical_doc', $sale->id) }}" target="_blank">
                                <i class="fas fa-file-contract text-warning fa-fw"></i> Technical Document
                            </a>
                        </li>
                        @endif
                    @endcan

                    @can('sales.edit')
                        {{-- Nested State Menu with Radio Buttons --}}
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-submenu-toggle d-flex align-items-center justify-content-between py-2" href="javascript:void(0)">
                                <span><i class="fas fa-tasks text-info fa-fw me-1"></i> State</span>
                                <i class="fas fa-chevron-left small text-muted"></i>
                            </a>
                            <div class="dropdown-submenu-menu shadow-lg">
                                <div class="px-3 py-1 border-bottom text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Select State</div>

                                <label class="state-radio-item" onclick="changeSaleOrderStatus(event, {{ $sale->id }}, 'pending', this, '{{ $sale->sale_status }}')">
                                    <input type="radio" name="order_state_{{ $sale->id }}" value="pending" {{ $ordStatus === 'pending' ? 'checked' : '' }}>
                                    <span class="badge rounded-circle p-1" style="background-color: #ef4444; width: 8px; height: 8px; display: inline-block;"></span>
                                    <span class="fw-medium">🔴 Pending</span>
                                </label>

                                <label class="state-radio-item" onclick="changeSaleOrderStatus(event, {{ $sale->id }}, 'ready', this, '{{ $sale->sale_status }}')">
                                    <input type="radio" name="order_state_{{ $sale->id }}" value="ready" {{ $ordStatus === 'ready' ? 'checked' : '' }}>
                                    <span class="badge rounded-circle p-1" style="background-color: #2563eb; width: 8px; height: 8px; display: inline-block;"></span>
                                    <span class="fw-medium">🔵 Ready</span>
                                </label>

                                <label class="state-radio-item" onclick="changeSaleOrderStatus(event, {{ $sale->id }}, 'delivered', this, '{{ $sale->sale_status }}')">
                                    <input type="radio" name="order_state_{{ $sale->id }}" value="delivered" {{ $ordStatus === 'delivered' ? 'checked' : '' }}>
                                    <span class="badge rounded-circle p-1" style="background-color: #10b981; width: 8px; height: 8px; display: inline-block;"></span>
                                    <span class="fw-medium">🟢 Delivered</span>
                                </label>

                                <label class="state-radio-item" onclick="changeSaleOrderStatus(event, {{ $sale->id }}, 'cancelled', this, '{{ $sale->sale_status }}')">
                                    <input type="radio" name="order_state_{{ $sale->id }}" value="cancelled" {{ $ordStatus === 'cancelled' ? 'checked' : '' }}>
                                    <span class="badge rounded-circle p-1" style="background-color: #f59e0b; width: 8px; height: 8px; display: inline-block;"></span>
                                    <span class="fw-medium">🟡 Cancelled</span>
                                </label>
                            </div>
                        </li>
                    @endcan

                    @if ($sale->sale_status === 'draft' || $sale->sale_status === 'booked')
                        @can('sales.create')
                            <li>
                                <form action="{{ route('sales.confirm', $sale->id) }}" method="POST" class="confirm-booking-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-success d-flex align-items-center gap-2 py-2">
                                        <i class="fas fa-check-circle fa-fw"></i> Confirm Order
                                    </button>
                                </form>
                            </li>
                        @endcan
                    @endif

                    @if ($sale->sale_status === 'posted')
                        @can('sales.create')
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="{{ route('sale.return.show', $sale->id) }}">
                                    <i class="fas fa-undo fa-fw"></i> Return Sale
                                </a>
                            </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </td>
    </tr>

    {{-- Mobile Table Card Row (< 768px) --}}
    @php
        $cardBorderColor = '#059669'; // Emerald for posted
        if ($sale->sale_status === 'draft') $cardBorderColor = '#64748b';
        elseif ($sale->sale_status === 'booked') $cardBorderColor = '#2563eb';
        elseif ($sale->sale_status === 'returned' || $sale->sale_status == 1) $cardBorderColor = '#dc2626';
    @endphp
    <tr class="d-table-row d-md-none border-0">
        <td colspan="13" class="p-0 border-0 bg-transparent">
            <div class="sale-mcard p-3 bg-white rounded-3 border mb-3 shadow-sm" style="border-left: 4px solid {{ $cardBorderColor }} !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="erp-bill-tag">#{{ $sale->reference ?? $sale->invoice_no ?? $sale->id }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">{!! $saleStatusBadge !!} <span class="sale-state-cell" data-sale-id="{{ $sale->id }}">{!! $orderStatusBadge !!}</span></div>
                </div>

                <div class="d-flex align-items-center gap-2 my-2">
                    <div class="erp-avatar {{ $isWalkin ? 'erp-avatar-walkin' : 'erp-avatar-registered' }}">
                        {{ $custInitial }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">
                            {{ $custDisplayName }}
                            @if ($isWalkin && !empty($sale->walkin_name))
                                <span class="badge bg-light text-muted border ms-1" style="font-size: 9px; font-weight: 600;">Walk-in</span>
                            @endif
                        </div>
                        <div class="text-muted small" style="font-size: 11px;">
                            <i class="far fa-calendar-alt me-1"></i> {{ $sale->created_at->format('d/m/Y') }}
                            <span class="ms-2"><i class="fas fa-box me-1"></i> {{ $sale->total_items > 0 ? $sale->total_items : $sale->qty }} Items</span>
                        </div>
                        @if ($ordStatus === 'delivered' && $serialNumbers->isNotEmpty())
                            <div class="mt-1">
                                <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.68rem;">
                                    <i class="fas fa-barcode text-primary me-1"></i>SN: {{ $serialNumbers->implode(', ') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row g-2 bg-light rounded-2 p-2 my-2 text-center" style="font-size: 0.8rem;">
                    <div class="col-4">
                        <div class="text-muted small">Subtotal</div>
                        <div class="fw-bold text-dark font-monospace" style="font-size: 0.75rem;">Rs. {{ number_format($gross_subtotal, 2) }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Net Total</div>
                        <div class="fw-bold text-success font-monospace" style="font-size: 0.75rem;">Rs. {{ number_format($sale->total_net, 2) }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Balance</div>
                        @if ($balanceIsDue)
                            <div class="fw-bold font-monospace" style="color: #dc2626; font-size: 0.75rem;">{{ $balanceDisplayVal }}</div>
                        @elseif ($balanceDisplayVal === '-')
                            <div class="text-muted fw-bold font-monospace" style="font-size: 0.75rem;">-</div>
                        @else
                            <div class="fw-bold text-success font-monospace" style="font-size: 0.75rem;">{{ $balanceDisplayVal }}</div>
                        @endif
                        {!! $balanceBadgeHtml !!}
                    </div>
                </div>

                {{-- Mobile Action Buttons Grid --}}
                <div class="d-grid gap-2 mt-2" style="display: grid; grid-template-columns: 1fr 1fr;">
                    @can('sales.edit')
                        <div class="dropdown" style="grid-column: span 2;">
                            <button class="btn btn-sm btn-erp-outline w-100 dropdown-toggle fw-bold d-flex align-items-center justify-content-center gap-1" type="button" data-toggle="dropdown" aria-expanded="false" style="border-radius: 8px;">
                                <i class="fas fa-tasks me-1 text-info"></i> Change State
                            </button>
                            <div class="dropdown-menu border-0 shadow-lg rounded-3 w-100 p-2">
                                <div class="px-2 py-1 border-bottom text-uppercase fw-bold text-muted mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Select State</div>
                                <label class="state-radio-item" onclick="changeSaleOrderStatus(event, {{ $sale->id }}, 'pending', this, '{{ $sale->sale_status }}')">
                                    <input type="radio" name="m_order_state_{{ $sale->id }}" value="pending" {{ $ordStatus === 'pending' ? 'checked' : '' }}>
                                    <span class="badge rounded-circle p-1" style="background-color: #ef4444; width: 8px; height: 8px; display: inline-block;"></span>
                                    <span class="fw-medium">🔴 Pending</span>
                                </label>
                                <label class="state-radio-item" onclick="changeSaleOrderStatus(event, {{ $sale->id }}, 'ready', this, '{{ $sale->sale_status }}')">
                                    <input type="radio" name="m_order_state_{{ $sale->id }}" value="ready" {{ $ordStatus === 'ready' ? 'checked' : '' }}>
                                    <span class="badge rounded-circle p-1" style="background-color: #2563eb; width: 8px; height: 8px; display: inline-block;"></span>
                                    <span class="fw-medium">🔵 Ready</span>
                                </label>
                                <label class="state-radio-item" onclick="changeSaleOrderStatus(event, {{ $sale->id }}, 'delivered', this, '{{ $sale->sale_status }}')">
                                    <input type="radio" name="m_order_state_{{ $sale->id }}" value="delivered" {{ $ordStatus === 'delivered' ? 'checked' : '' }}>
                                    <span class="badge rounded-circle p-1" style="background-color: #10b981; width: 8px; height: 8px; display: inline-block;"></span>
                                    <span class="fw-medium">🟢 Delivered</span>
                                </label>
                                <label class="state-radio-item" onclick="changeSaleOrderStatus(event, {{ $sale->id }}, 'cancelled', this, '{{ $sale->sale_status }}')">
                                    <input type="radio" name="m_order_state_{{ $sale->id }}" value="cancelled" {{ $ordStatus === 'cancelled' ? 'checked' : '' }}>
                                    <span class="badge rounded-circle p-1" style="background-color: #f59e0b; width: 8px; height: 8px; display: inline-block;"></span>
                                    <span class="fw-medium">🟡 Cancelled</span>
                                </label>
                            </div>
                        </div>
                    @endcan

                    @can('sales.edit')
                        <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-sm btn-erp-outline fw-bold justify-content-center" style="border-radius: 8px;">
                            <i class="fas fa-edit me-1 text-primary"></i> Edit
                        </a>
                    @endcan

                    @if ($sale->sale_status === 'draft' || $sale->sale_status === 'booked')
                        @can('sales.create')
                            <form action="{{ route('sales.confirm', $sale->id) }}" method="POST" class="confirm-booking-form m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success w-100 fw-bold justify-content-center" style="border-radius: 8px;">
                                    <i class="fas fa-check-circle me-1"></i> Confirm
                                </button>
                            </form>
                        @endcan
                    @endif

                    @can('sales.view')
                        <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="btn btn-sm btn-erp-outline fw-bold justify-content-center" style="border-radius: 8px;">
                            <i class="fas fa-file-invoice me-1 text-info"></i> Invoice
                        </a>
                        @if($ordStatus === 'delivered')
                        <a href="{{ route('sales.technical_doc', $sale->id) }}" target="_blank" class="btn btn-sm btn-erp-outline fw-bold justify-content-center" style="border-radius: 8px;">
                            <i class="fas fa-file-contract me-1 text-warning"></i> Tech Doc
                        </a>
                        @endif
                    @endcan

                    @if ($sale->sale_status === 'posted')
                        @can('sales.create')
                            <a href="{{ route('sale.return.show', $sale->id) }}" class="btn btn-sm btn-erp-outline-danger fw-bold justify-content-center" style="border-radius: 8px;">
                                <i class="fas fa-undo me-1"></i> Return
                            </a>
                        @endcan
                    @endif
                </div>
            </div>
        </td>
    </tr>
@endforeach


