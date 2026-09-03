@extends('admin_panel.layout.app')

@section('content')
<style>
    .premium-card {
        border: 2px solid #cbd5e1 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
        background-color: #ffffff;
        margin-top: 10px;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
    }
    .badge-qc-pending { background-color: #fff7ed; color: #c2410c; border: 1px solid #fdba74; }
    .badge-qc-passed { background-color: #f0fdf4; color: #15803d; border: 1px solid #86efac; }
    .badge-qc-failed { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; }
    .badge-qc-partial { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #93c5fd; }
    
    .table-total-row {
        background-color: #f8fafc;
        font-weight: bold;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="page-title">
                <h4>GRN Details</h4>
                <h6>GRN Number: {{ $grn->grn_number ?? 'GRN-000' }}</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('grn.index') }}" class="btn btn-light border">
                    <i class="fa fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <div class="card premium-card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1">Date</small>
                            <strong>{{ isset($grn->received_date) ? date('d-M-Y', strtotime($grn->received_date)) : 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1">Purchase Order</small>
                            <strong>
                                @if(isset($grn->purchaseOrder))
                                    <a href="{{ route('purchase.show', $grn->purchase_order_id) }}">{{ $grn->purchaseOrder->po_number }}</a>
                                @else
                                    N/A
                                @endif
                            </strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1">Vendor</small>
                            <strong>{{ $grn->vendor->name ?? 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1">QC Status / Status</small>
                            <div class="mt-1">
                                @if(($grn->qc_status ?? '') == 'pending') <span class="badge-status badge-qc-pending">QC Pending</span>
                                @elseif(($grn->qc_status ?? '') == 'passed') <span class="badge-status badge-qc-passed">QC Passed</span>
                                @elseif(($grn->qc_status ?? '') == 'failed') <span class="badge-status badge-qc-failed">QC Failed</span>
                                @elseif(($grn->qc_status ?? '') == 'partial') <span class="badge-status badge-qc-partial">QC Partial</span>
                                @endif
                                
                                <span class="badge bg-secondary ms-1">{{ ucfirst($grn->status ?? 'Draft') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3">Received Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Product</th>
                                <th>Ordered</th>
                                <th>Received</th>
                                <th>Accepted</th>
                                <th>Rejected</th>
                                <th>Batch</th>
                                <th>Unit Price</th>
                                <th>Line Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalAmount = 0; @endphp
                            @forelse($grn->items ?? [] as $item)
                            @php 
                                $lineTotal = ($item->accepted_qty * $item->unit_price);
                                $totalAmount += $lineTotal;
                            @endphp
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->ordered_qty, 2) }}</td>
                                <td>{{ number_format($item->received_qty, 2) }}</td>
                                <td><span class="text-success fw-bold">{{ number_format($item->accepted_qty, 2) }}</span></td>
                                <td>
                                    @if($item->rejected_qty > 0)
                                        <span class="text-danger fw-bold">{{ number_format($item->rejected_qty, 2) }}</span>
                                        <br><small class="text-muted">{{ $item->rejection_reason }}</small>
                                    @else
                                        0.00
                                    @endif
                                </td>
                                <td>{{ $item->batch_no ?? '-' }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($lineTotal, 2) }}</td>
                                <td>
                                    @if($item->accepted_qty > 0)
                                    <button class="btn btn-sm btn-outline-danger" onclick="openQCRejectModal({{ $item->id }})">
                                        Reject
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-3">No items found.</td>
                            </tr>
                            @endforelse
                            <tr class="table-total-row">
                                <td colspan="7" class="text-end">Total Accepted Value:</td>
                                <td colspan="2">{{ number_format($totalAmount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QC Reject Modal -->
<div class="modal fade" id="qcRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('grn.reject_item') }}" method="POST">
            @csrf
            <input type="hidden" name="grn_item_id" id="qc_grn_item_id">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">QC Reject Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reject Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="rejected_qty" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Debit Note Modal (Auto-opens if session variable is set) -->
<div class="modal fade" id="debitNoteModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('grn.create-debit-note') }}" method="POST">
            @csrf
            <input type="hidden" name="grn_id" value="{{ $grn->id ?? '' }}">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Create Debit Note?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Rejected material found. Do you want to create a Debit Note for the supplier?</p>
                    <div class="mb-3">
                        <label class="form-label">Select Rejected Item</label>
                        <select name="rejected_item_id" class="form-select">
                            @foreach($grn->items ?? [] as $item)
                                @if($item->rejected_qty > 0)
                                    <option value="{{ $item->id }}">{{ $item->product->name ?? 'Product' }} ({{ $item->rejected_qty }} rejected)</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('purchase-return.create') }}" class="btn btn-outline-secondary">Create Manually</a>
                    <button type="submit" class="btn btn-primary">Create Automatically</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openQCRejectModal(itemId) {
        document.getElementById('qc_grn_item_id').value = itemId;
        var myModal = new bootstrap.Modal(document.getElementById('qcRejectModal'));
        myModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('show_debit_note_modal'))
            var dnModal = new bootstrap.Modal(document.getElementById('debitNoteModal'));
            dnModal.show();
        @endif
    });
</script>
@endsection
