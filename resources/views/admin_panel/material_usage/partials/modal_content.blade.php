<div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; border-radius: 12px 12px 0 0; padding: 1.25rem 1.5rem;">
    <div>
        <h5 class="modal-title font-weight-bold mb-1" style="font-size: 1.2rem; letter-spacing: -0.01em;">
            <i class="fas fa-boxes-packing text-primary mr-2"></i>Material Issue Voucher #{{ $usage->usage_no }}
        </h5>
        <div class="text-muted small" style="color: #94a3b8 !important;">
            Date: {{ \Carbon\Carbon::parse($usage->date)->format('d M, Y') }} | Issued By: <strong class="text-white">{{ $usage->user->name ?? 'System' }}</strong>
        </div>
    </div>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.85;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body p-4" style="background-color: #f8fafc;">
    <!-- Meta Info -->
    @if($usage->purpose || $usage->remarks)
        <div class="p-3 bg-white rounded border shadow-sm mb-4">
            <div class="text-muted small font-weight-bold text-uppercase">Purpose / Instructions</div>
            <div class="font-weight-bold text-dark mt-1">{{ $usage->purpose ?: 'Production Consumption' }} {{ $usage->remarks ? '('.$usage->remarks.')' : '' }}</div>
        </div>
    @endif

    <!-- Items Table -->
    <div class="table-responsive bg-white rounded border shadow-sm mb-4">
        <table class="table table-sm table-hover mb-0">
            <thead class="bg-light text-uppercase small text-muted" style="font-size: 0.75rem;">
                <tr>
                    <th class="py-2 pl-3">#</th>
                    <th class="py-2">Raw Material</th>
                    <th class="py-2">Code</th>
                    <th class="py-2 text-right">Available Then</th>
                    <th class="py-2 text-right">Qty Used</th>
                    <th class="py-2 text-right">Unit Cost</th>
                    <th class="py-2 text-right pr-3">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usage->items as $index => $item)
                    <tr>
                        <td class="pl-3 align-middle text-muted">{{ $index + 1 }}</td>
                        <td class="align-middle">
                            <span class="font-weight-bold text-dark">{{ $item->product->item_name ?? 'N/A' }}</span>
                            @if($item->notes)
                                <div class="text-muted small italic">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td class="align-middle text-muted small">{{ $item->product->item_code ?? '-' }}</td>
                        <td class="align-middle text-right text-muted small">{{ number_format($item->available_stock_at_time, 2) }} {{ $item->unit_name }}</td>
                        <td class="align-middle text-right font-weight-bold text-danger">{{ number_format($item->qty_used, 2) }} {{ $item->unit_name }}</td>
                        <td class="align-middle text-right text-muted">Rs. {{ number_format($item->unit_cost, 2) }}</td>
                        <td class="align-middle text-right font-weight-bold text-dark pr-3">Rs. {{ number_format($item->total_cost, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light font-weight-bold">
                <tr>
                    <td colspan="4" class="text-right py-2">Total:</td>
                    <td class="text-right py-2 text-danger font-weight-bold">{{ number_format($usage->total_qty, 2) }}</td>
                    <td class="text-right py-2"></td>
                    <td class="text-right py-2 pr-3 text-dark">Rs. {{ number_format($usage->total_cost, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Actions inside modal -->
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('material_usage.show', $usage->id) }}" target="_blank" class="btn btn-outline-primary btn-sm font-weight-bold">
            <i class="fas fa-print mr-1"></i> Print Official Slip
        </a>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
    </div>
</div>