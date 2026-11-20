@forelse ($quotations as $quote)
    @php
        $pNames = 'N/A';
        if ($quote->items && $quote->items->count() > 0) {
            $pNames = $quote->items
                ->map(fn($item) => $item->product_name ?: (optional($item->product)->item_name ?? 'Item'))
                ->implode(', ');
        }

        // Customer Name determination
        $custDisplayName = optional($quote->customer_relation)->customer_name;
        $isWalkin = false;
        if (empty($custDisplayName)) {
            if (!empty($quote->walkin_name)) {
                $custDisplayName = $quote->walkin_name;
                $isWalkin = true;
            } else {
                $custDisplayName = 'Walk-in Customer';
                $isWalkin = true;
            }
        }
        $custInitial = strtoupper(substr($custDisplayName, 0, 1));

        // Calculations
        $grossSubtotal = (float)$quote->total_bill_amount;
        $totalDiscount = (float)$quote->total_discount;
        $discountPct = $grossSubtotal > 0 ? ($totalDiscount / $grossSubtotal) * 100 : 0;
        $gstAmt = (float)$quote->tax_amount;
        $gstPct = (float)$quote->tax_percent;
        if ($gstPct <= 0 && $grossSubtotal > 0 && $gstAmt > 0) {
            $gstPct = ($gstAmt / $grossSubtotal) * 100;
        }

        // Status Badge
        $statusStr = strtolower($quote->status ?? 'pending');
        if ($statusStr === 'converted' || $quote->converted_sale_id) {
            $statusBadge = '<span class="erp-badge badge-posted"><i class="fas fa-check-circle me-1"></i>Converted to Order</span>';
        } elseif ($statusStr === 'approved') {
            $statusBadge = '<span class="erp-badge" style="background-color: #ecfdf5; color: #047857; border-color: #a7f3d0;"><i class="fas fa-thumbs-up me-1"></i>Approved</span>';
        } elseif ($statusStr === 'cancelled') {
            $statusBadge = '<span class="erp-badge state-cancelled"><i class="fas fa-ban me-1"></i>Cancelled</span>';
        } else {
            $statusBadge = '<span class="erp-badge badge-draft"><i class="fas fa-clock me-1"></i>Pending</span>';
        }
    @endphp

    {{-- Desktop Table Row --}}
    <tr class="border-bottom-0 d-none d-md-table-row">
        <td>
            <div class="d-flex align-items-center gap-1.5" style="min-width: 140px;">
                <div class="erp-avatar {{ $isWalkin ? 'erp-avatar-walkin' : 'erp-avatar-registered' }} flex-shrink-0">
                    {{ $custInitial }}
                </div>
                <div class="d-flex flex-column" style="white-space: normal; word-break: break-word;">
                    <span class="fw-bold text-dark" style="font-size: 0.78rem; line-height: 1.25;" title="{{ $custDisplayName }}">{{ $custDisplayName }}</span>
                    @if ($isWalkin && !empty($quote->walkin_name))
                        <span class="text-muted" style="font-size: 0.65rem; font-weight: 500;">Walk-in</span>
                    @endif
                </div>
            </div>
        </td>
        <td class="align-middle">
            <a href="{{ route('quotations.show', $quote->id) }}" target="_blank" class="erp-bill-tag text-decoration-none" title="View Quotation">
                #{{ $quote->quote_no }}
            </a>
            @if ($quote->converted_sale_id && $quote->convertedSale)
                <div class="mt-0.5">
                    <a href="{{ route('sales.invoice', $quote->converted_sale_id) }}" target="_blank" class="text-decoration-none small text-success font-monospace" style="font-size: 0.68rem;" title="View Converted Order">
                        <i class="fas fa-link"></i> #{{ $quote->convertedSale->invoice_no }}
                    </a>
                </div>
            @endif
        </td>
        <td title="{{ $pNames }}" class="text-dark small" style="min-width: 150px; white-space: normal; word-break: break-word;">
            <div class="fw-medium text-dark" style="font-size: 0.78rem; line-height: 1.3;">
                {{ $pNames }}
            </div>
            @if(!empty($quote->reference))
                <div class="text-muted small mt-0.5" style="font-size: 0.70rem; font-style: italic;">
                    Subj: {{ \Illuminate\Support\Str::limit($quote->reference, 40) }}
                </div>
            @endif
        </td>
        <td class="text-center font-monospace fw-semibold text-dark" style="font-size: 0.78rem;">
            {{ $quote->total_items > 0 ? $quote->total_items : $quote->items->sum('qty') }}
        </td>
        <td class="text-end fw-bold text-dark font-monospace" style="font-size: 0.78rem;">
            Rs. {{ number_format($grossSubtotal, 2) }}
        </td>
        <td class="text-end text-dark font-monospace">
            @if ($totalDiscount > 0)
                <span class="badge rounded-pill border px-1.5 py-0.5" style="background-color: #fffbeb; color: #b45309; border-color: #fde68a !important; font-size: 10px; font-weight: 700; display: inline-flex; align-items: center; gap: 2px;">
                    <i class="fas fa-tag" style="font-size: 8px;"></i> Rs. {{ number_format($totalDiscount, 2) }}
                </span>
                <div class="text-muted small mt-0.5" style="font-size: 9px;">({{ number_format($discountPct, 1) }}%)</div>
            @else
                <span class="text-muted" style="font-size: 0.75rem;">Rs. 0.00</span>
            @endif
        </td>
        <td class="text-end text-dark font-monospace">
            @if ($gstAmt > 0)
                <span class="badge rounded-pill border px-1.5 py-0.5" style="background-color: #f0f9ff; color: #0369a1; border-color: #bae6fd !important; font-size: 10px; font-weight: 700; display: inline-flex; align-items: center; gap: 2px;">
                    <i class="fas fa-percent" style="font-size: 8px;"></i> Rs. {{ number_format($gstAmt, 2) }}
                </span>
                @if ($gstPct > 0)
                    <div class="text-muted small mt-0.5" style="font-size: 9px;">({{ number_format($gstPct, 1) }}%)</div>
                @endif
            @else
                <span class="text-muted" style="font-size: 0.75rem;">Rs. 0.00</span>
            @endif
        </td>
        <td class="text-end fw-bold font-monospace" style="color: #047857; font-size: 0.80rem;">
            Rs. {{ number_format($quote->total_net, 2) }}
        </td>
        <td class="text-nowrap small text-muted font-monospace" style="font-size: 0.75rem;">
            {{ $quote->quotation_date ? \Carbon\Carbon::parse($quote->quotation_date)->format('d/m/Y') : $quote->created_at->format('d/m/Y') }}
        </td>
        <td class="text-nowrap text-center">
            {!! $statusBadge !!}
        </td>
        <td class="pe-2 text-center text-nowrap">
            <div class="dropdown d-inline-block">
                <button class="btn btn-erp-table-action dropdown-toggle shadow-none" type="button" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v small me-1"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-right dropdown-menu-end border-0 shadow-lg rounded-3 py-2" style="min-width: 185px; z-index: 1070;">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('quotations.show', $quote->id) }}" target="_blank">
                            <i class="fas fa-print text-info fa-fw"></i> View / Print Quote
                        </a>
                    </li>
                    @if ($statusStr !== 'converted')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('quotations.edit', $quote->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit Quote
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        {{-- Audio 2: Convert to Order / Sale --}}
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-success fw-bold" href="{{ route('quotations.convert_to_order', $quote->id) }}" title="Load in New Sale to Book Order">
                                <i class="fas fa-arrow-right-to-bracket fa-fw"></i> Convert to Order
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('quotations.convert_to_order', $quote->id) }}" method="POST" onsubmit="return confirm('Quickly convert Quotation #{{ $quote->quote_no }} into a Booked Order now?');">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-primary">
                                    <i class="fas fa-bolt fa-fw"></i> Quick Book Order
                                </button>
                            </form>
                        </li>
                    @else
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-success" href="{{ route('sales.invoice', $quote->converted_sale_id) }}" target="_blank">
                                <i class="fas fa-file-invoice fa-fw"></i> View Booked Order
                            </a>
                        </li>
                    @endif
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form action="{{ route('quotations.destroy', $quote->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                <i class="fas fa-trash fa-fw"></i> Delete
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </td>
    </tr>

    {{-- Mobile Table Card (< 768px) --}}
    <tr class="d-table-row d-md-none border-0">
        <td colspan="11" class="p-0 border-0 bg-transparent">
            <div class="card mb-3 shadow-sm border-0 rounded-3 overflow-hidden" style="border-left: 4px solid #2563eb !important; background: #ffffff;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="erp-avatar {{ $isWalkin ? 'erp-avatar-walkin' : 'erp-avatar-registered' }}">
                                {{ $custInitial }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $custDisplayName }}</h6>
                                <a href="{{ route('quotations.show', $quote->id) }}" target="_blank" class="erp-bill-tag text-decoration-none mt-1 d-inline-block">
                                    #{{ $quote->quote_no }}
                                </a>
                            </div>
                        </div>
                        <div>{!! $statusBadge !!}</div>
                    </div>

                    <div class="p-2 my-2 rounded-2" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                        <div class="small fw-bold text-dark mb-1">{{ $pNames }}</div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Total Amount:</span>
                            <span class="fw-bold text-success font-monospace fs-6">Rs. {{ number_format($quote->total_net, 2) }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small text-muted font-monospace">
                            <i class="far fa-calendar-alt me-1"></i>{{ $quote->quotation_date ? \Carbon\Carbon::parse($quote->quotation_date)->format('d/m/Y') : $quote->created_at->format('d/m/Y') }}
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('quotations.show', $quote->id) }}" target="_blank" class="btn btn-sm btn-erp-pill-outline px-2">
                                <i class="fas fa-print"></i> Print
                            </a>
                            @if ($statusStr !== 'converted')
                                <a href="{{ route('quotations.convert_to_order', $quote->id) }}" class="btn btn-sm btn-erp-pill-primary px-2">
                                    <i class="fas fa-arrow-right"></i> Order
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center py-5 text-muted">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <i class="fas fa-file-contract text-secondary fs-1 mb-2" style="opacity: 0.4;"></i>
                <h6 class="fw-bold mb-1">No Quotations Found</h6>
                <p class="small text-muted mb-3">Create your first proposal to get started.</p>
                <a href="{{ route('quotations.create') }}" class="btn btn-sm btn-erp-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create Quotation
                </a>
            </div>
        </td>
    </tr>
@endforelse
