@foreach ($Purchase as $purchase)
    {{-- Desktop Table Row (≥ 768px) --}}
    <tr class="border-bottom-0 d-none d-md-table-row">
        <td class="ps-3 text-center" style="width: 40px; vertical-align: middle;">
            <input type="checkbox" class="select-purchase-row" value="{{ $purchase->id }}" style="cursor: pointer; width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin: 0 auto !important;">
        </td>
        <td class="fw-bold text-muted" data-order="{{ $purchase->id }}">
            #{{ $purchase->id }}
            <div>
                @if(($purchase->purchase_type ?? 'local') === 'import')
                    <span class="badge bg-primary text-white" style="font-size: 9.5px;"><i class="fas fa-globe me-1"></i>Import</span>
                @else
                    <span class="badge bg-light text-secondary border" style="font-size: 9.5px;"><i class="fas fa-map-marker-alt me-1"></i>Local</span>
                @endif
            </div>
        </td>
        <td class="text-nowrap">
            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
        </td>
        <td class="font-monospace text-dark">{{ $purchase->invoice_no }}</td>
        <td class="font-monospace text-dark small">{{ $purchase->note ?? '-' }}</td>
        <td>
            {!! $purchase->po_status_badge !!}
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-info-subtle text-info me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 32px; height: 32px; font-size: 14px;">
                    {{ strtoupper(substr($purchase->vendor->name ?? 'V', 0, 1)) }}
                </div>
                <span class="fw-medium text-dark">{{ $purchase->vendor->name ?? 'N/A' }}</span>
            </div>
        </td>
        <td class="text-muted small">
            {{ $purchase->warehouse->warehouse_name ?? 'N/A' }}
        </td>

        <td class="text-end text-dark">
            @php
                $inline_val = $purchase->items->sum('item_discount');
                $gross_subtotal = $purchase->subtotal + $inline_val;
                $inline_pct = $gross_subtotal > 0 ? ($inline_val / $gross_subtotal) * 100 : 0;
            @endphp
            Rs. {{ number_format($inline_val, 2) }}
            @if ($inline_val > 0)
                <div class="text-muted small mt-1" style="font-size: 10px;">({{ number_format($inline_pct, 1) }}%)</div>
            @endif
        </td>

        <td class="text-end text-dark">
            @if ($purchase->additional_discount > 0)
                @php
                    $add_val = $purchase->additional_discount;
                    $original_net = $purchase->net_amount + $add_val;
                    $add_pct = $original_net > 0 ? ($add_val / $original_net) * 100 : 0;
                @endphp
                <span class="badge rounded-pill border px-2 py-1" style="background-color: #fff8e1; color: #b78103; border-color: #ffe082 !important; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-tag" style="font-size: 10px;"></i> Rs. {{ number_format($add_val, 2) }}
                </span>
                <div class="text-muted small mt-1" style="font-size: 10px;">({{ number_format($add_pct, 1) }}%)</div>
            @else
                <span class="text-muted">Rs. 0.00</span>
            @endif
        </td>

        <td class="text-end fw-bold text-dark">
            @if ($purchase->total_returned > 0)
                <div>
                    <small class="text-muted text-decoration-line-through">Rs. {{ number_format($purchase->net_amount, 2) }}</small>
                </div>
                <div class="text-success">
                    Rs. {{ number_format($purchase->updated_net_amount, 2) }}
                </div>
                <small class="text-danger">(-{{ number_format($purchase->total_returned, 2) }})</small>
            @else
                Rs. {{ number_format($purchase->net_amount, 2) }}
            @endif
        </td>
        <td class="text-end text-success">
            {{ number_format($purchase->paid_amount, 2) }}
        </td>
        <td class="text-end">
            @php
                $displayDue = $purchase->total_returned > 0 ? $purchase->updated_due_amount : $purchase->due_amount;
            @endphp
            @if ($displayDue > 0)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">{{ number_format($displayDue, 2) }}</span>
            @else
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Paid</span>
            @endif

            @if ($purchase->has_partial_return)
                <br><small class="badge bg-danger text-white mt-1"><i class="fas fa-undo-alt me-1"></i> Partial Return</small>
            @elseif($purchase->is_fully_returned)
                <br><small class="badge bg-danger mt-1">Fully Returned</small>
            @endif
        </td>

        <td class="pe-3 text-center">
            <div class="dropdown">
                <button class="btn btn-premium-action dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v small me-1"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">
                    @can('purchases.edit')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.edit', $purchase->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit (Simple)
                            </a>
                        </li>
                    @endcan

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-success confirm-purchase-btn" href="{{ route('purchase.confirm', $purchase->id) }}">
                                    <i class="fas fa-check-circle fa-fw"></i> Confirm Purchase
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase != 'draft')
                        @if(($purchase->purchase_type ?? 'local') === 'import')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-info" href="{{ route('import-costs.show', $purchase->id) }}">
                                    <i class="fas fa-ship text-info fa-fw"></i> Landed Cost &amp; Customs
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        @can('purchases.view')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.invoice', $purchase->id) }}">
                                    <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.receipt', $purchase->id) }}">
                                    <i class="fas fa-receipt text-secondary fa-fw"></i> View Receipt
                                </a>
                            </li>
                        @endcan
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.return.show', $purchase->id) }}">
                                    <i class="fas fa-undo text-warning fa-fw"></i> Return
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.delete')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 delete-btn text-danger">
                                        <i class="fas fa-trash-alt fa-fw"></i> Delete
                                    </button>
                                </form>
                            </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </td>
    </tr>

    {{-- Mobile Table Card Row (< 768px) --}}
    <tr class="d-table-row d-md-none border-0">
        <td colspan="14" class="p-0 border-0 bg-transparent">
            <div class="purch-mcard p-3 bg-white rounded-3 border mb-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <input type="checkbox" class="select-purchase-row" value="{{ $purchase->id }}" style="width: 16px; height: 16px;">
                        <span class="fw-bold text-dark fs-6">#{{ $purchase->invoice_no }}</span>
                        @if(($purchase->purchase_type ?? 'local') === 'import')
                            <span class="badge bg-primary text-white" style="font-size: 9px;"><i class="fas fa-globe me-1"></i>Import</span>
                        @else
                            <span class="badge bg-light text-secondary border" style="font-size: 9px;"><i class="fas fa-map-marker-alt me-1"></i>Local</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        {!! $purchase->po_status_badge !!}
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 my-2">
                    <div class="avatar-circle bg-info-subtle text-info fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-size: 13px;">
                        {{ strtoupper(substr($purchase->vendor->name ?? 'V', 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">{{ $purchase->vendor->name ?? 'N/A' }}</div>
                        <div class="text-muted small" style="font-size: 11px;">
                            <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                            <span class="ms-2"><i class="fas fa-building me-1"></i> {{ $purchase->warehouse->warehouse_name ?? 'Main' }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-2 bg-light rounded-2 p-2 my-2 text-center" style="font-size: 0.8rem;">
                    <div class="col-4">
                        <div class="text-muted small">Net Total</div>
                        <div class="fw-bold text-dark">Rs. {{ number_format($purchase->total_returned > 0 ? $purchase->updated_net_amount : $purchase->net_amount, 2) }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Paid</div>
                        <div class="fw-bold text-success">Rs. {{ number_format($purchase->paid_amount, 2) }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Due</div>
                        @php $displayDueMob = $purchase->total_returned > 0 ? $purchase->updated_due_amount : $purchase->due_amount; @endphp
                        <div class="fw-bold {{ $displayDueMob > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $displayDueMob > 0 ? 'Rs. '.number_format($displayDueMob, 2) : 'Paid' }}
                        </div>
                    </div>
                </div>

                {{-- Mobile Action Buttons Grid --}}
                <div class="d-grid gap-2 mt-2" style="display: grid; grid-template-columns: 1fr 1fr;">
                    @can('purchases.edit')
                        <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-edit me-1"></i> Edit (Simple)
                        </a>
                    @endcan

                    @if ($purchase->status_purchase != 'draft')
                        @can('purchases.view')
                            <a href="{{ route('purchase.invoice', $purchase->id) }}" class="btn btn-sm btn-outline-info fw-bold" style="border-radius: 8px;">
                                <i class="fas fa-file-invoice me-1"></i> Invoice
                            </a>
                            <a href="{{ route('purchase.receipt', $purchase->id) }}" class="btn btn-sm btn-outline-secondary fw-bold" style="border-radius: 8px;">
                                <i class="fas fa-receipt me-1"></i> Receipt
                            </a>
                        @endcan
                    @else
                        @can('purchases.create')
                            <a href="{{ route('purchase.confirm', $purchase->id) }}" class="btn btn-sm btn-success fw-bold confirm-purchase-btn" style="border-radius: 8px;">
                                <i class="fas fa-check-circle me-1"></i> Confirm
                            </a>
                        @endcan
                        @can('purchases.delete')
                            <form action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger fw-bold delete-btn w-100" style="border-radius: 8px;">
                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                </button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
        </td>
    </tr>
@endforeach
