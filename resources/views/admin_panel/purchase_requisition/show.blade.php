@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --pr-primary:    #4f46e5;
        --pr-primary-lt: #eef2ff;
        --pr-success:    #10b981;
        --pr-success-lt: #ecfdf5;
        --pr-warning:    #f59e0b;
        --pr-warning-lt: #fffbeb;
        --pr-danger:     #ef4444;
        --pr-danger-lt:  #fef2f2;
        --pr-border:     #e2e8f0;
        --pr-card-bg:    #ffffff;
        --pr-text:       #0f172a;
        --pr-muted:      #64748b;
        --pr-radius:     14px;
        --pr-shadow:     0 2px 8px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.02);
    }

    .pr-page-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        padding: 20px 0;
        min-height: calc(100vh - 80px);
        background: #f8fafc;
    }

    .pr-card {
        background: var(--pr-card-bg);
        border-radius: var(--pr-radius);
        border: 1px solid var(--pr-border);
        box-shadow: var(--pr-shadow);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .pr-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--pr-border);
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .pr-card-title {
        font-size: 0.96rem;
        font-weight: 700;
        color: var(--pr-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pr-card-body {
        padding: 20px 22px;
    }

    .info-tile {
        background: #f8fafc;
        border: 1px solid var(--pr-border);
        border-radius: 10px;
        padding: 12px 16px;
    }

    .info-tile-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--pr-muted);
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .info-tile-val {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--pr-text);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-pill.draft { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .status-pill.pending_approval { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .status-pill.approved { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .status-pill.rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; }
    .status-pill.closed { background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; }

    .items-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .items-table th {
        background-color: #f8fafc;
        color: var(--pr-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.4px;
        padding: 12px 14px;
        border-bottom: 1px solid var(--pr-border);
    }

    .items-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
</style>

<div class="pr-page-container">
    <div class="container-fluid px-3 px-md-4">
        
        {{-- Header Bar --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-file-alt text-primary"></i> Purchase Requisition: {{ $pr->pr_number ?? 'PR-000' }}
                </h4>
                <p class="text-muted small mb-0">Requisition details, approval actions &amp; linked purchase orders</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('purchase-requisitions.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>

                @if(($pr->status ?? '') === 'pending_approval')
                    <form action="{{ route('purchase-requisitions.approve', $pr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to approve this Requisition?');">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-3 py-2 fw-bold">
                            <i class="fas fa-check-circle me-1"></i> Approve PR
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-sm rounded-3 px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times-circle me-1"></i> Reject PR
                    </button>
                @endif

                @if(($pr->status ?? '') === 'approved')
                    <a href="{{ route('purchase-requisitions.convert-to-po', $pr->id) }}" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold shadow-sm">
                        <i class="fas fa-shopping-cart me-1"></i> Convert to Purchase Order (PO)
                    </a>
                @endif
            </div>
        </div>

        {{-- Meta & Summary Card --}}
        <div class="pr-card">
            <div class="pr-card-header">
                <h5 class="pr-card-title">
                    <i class="fas fa-info-circle text-primary"></i> Requisition Information
                </h5>
                <div>
                    @php $st = $pr->status ?? 'draft'; @endphp
                    @if($st === 'pending_approval')
                        <span class="status-pill pending_approval"><i class="fas fa-clock"></i> Pending Approval</span>
                    @elseif($st === 'approved')
                        <span class="status-pill approved"><i class="fas fa-check-circle"></i> Approved</span>
                    @elseif($st === 'rejected')
                        <span class="status-pill rejected"><i class="fas fa-times-circle"></i> Rejected</span>
                    @elseif($st === 'closed')
                        <span class="status-pill closed"><i class="fas fa-check-double"></i> Closed</span>
                    @else
                        <span class="status-pill draft"><i class="fas fa-pencil-alt"></i> Draft</span>
                    @endif
                </div>
            </div>
            <div class="pr-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="info-tile">
                            <div class="info-tile-label">Branch</div>
                            <div class="info-tile-val"><i class="fas fa-building text-primary me-1"></i>{{ $pr->branch->name ?? 'Main Branch' }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-tile">
                            <div class="info-tile-label">Target Warehouse</div>
                            <div class="info-tile-val"><i class="fas fa-warehouse text-primary me-1"></i>{{ $pr->warehouse->warehouse_name ?? ($pr->warehouse->name ?? 'Main Warehouse') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-tile">
                            <div class="info-tile-label">Created By</div>
                            <div class="info-tile-val"><i class="fas fa-user-circle text-primary me-1"></i>{{ $pr->creator->name ?? 'System' }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-tile">
                            <div class="info-tile-label">Required By Date</div>
                            <div class="info-tile-val"><i class="far fa-calendar-alt text-primary me-1"></i>{{ $pr->required_by_date ? date('d M, Y', strtotime($pr->required_by_date)) : 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                @if(!empty($pr->notes))
                <div class="mt-3 p-3 bg-light rounded-3 border">
                    <span class="fw-bold text-dark d-block mb-1"><i class="fas fa-comment-alt text-muted me-1"></i> Notes &amp; Justification:</span>
                    <p class="text-muted mb-0 small">{{ $pr->notes }}</p>
                </div>
                @endif

                @if(!empty($pr->rejection_reason))
                <div class="mt-3 p-3 bg-danger bg-opacity-10 border border-danger rounded-3 text-danger">
                    <span class="fw-bold d-block mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Rejection Reason:</span>
                    <p class="mb-0 small">{{ $pr->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Items Card --}}
        <div class="pr-card">
            <div class="pr-card-header">
                <h5 class="pr-card-title">
                    <i class="fas fa-boxes text-primary"></i> Requisition Items ({{ $pr->items->count() }})
                </h5>
            </div>
            <div class="pr-card-body p-0">
                <div class="table-responsive">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product / Material</th>
                                <th>UOM</th>
                                <th class="text-end">Required Qty</th>
                                <th class="text-end">Ordered Qty</th>
                                <th class="text-end">Pending Qty</th>
                                <th class="text-end">Est. Unit Price</th>
                                <th>Reason / Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pr->items ?? [] as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $item->product->item_name ?? ($item->product->name ?? 'N/A') }}
                                    </div>
                                    @if(isset($item->product->item_code))
                                        <small class="text-muted">Code: {{ $item->product->item_code }}</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $item->uom }}</span></td>
                                <td class="text-end fw-bold">{{ number_format($item->required_qty, 2) }}</td>
                                <td class="text-end text-success fw-bold">{{ number_format($item->ordered_qty, 2) }}</td>
                                <td class="text-end text-danger fw-bold">{{ number_format($item->required_qty - $item->ordered_qty, 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($item->estimated_price, 2) }}</td>
                                <td class="text-muted small">{{ $item->reason ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No items in this requisition.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Related POs Card --}}
        @if(isset($pr->purchaseOrders) && $pr->purchaseOrders->count() > 0)
        <div class="pr-card">
            <div class="pr-card-header">
                <h5 class="pr-card-title">
                    <i class="fas fa-file-invoice text-primary"></i> Related Purchase Orders (POs)
                </h5>
            </div>
            <div class="pr-card-body p-0">
                <div class="table-responsive">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Date</th>
                                <th>Vendor</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pr->purchaseOrders as $po)
                            <tr>
                                <td class="fw-bold text-primary">{{ $po->po_number }}</td>
                                <td>{{ date('d-M-Y', strtotime($po->date)) }}</td>
                                <td>{{ $po->vendor->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark border text-uppercase">{{ $po->status }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('purchase.show', $po->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                        <i class="fas fa-eye me-1"></i> View PO
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Reject Modal -->
@if(($pr->status ?? '') === 'pending_approval')
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('purchase-requisitions.reject', $pr->id) }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                        <i class="fas fa-times-circle"></i> Reject Purchase Requisition
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Explain why this requirement is rejected or what changes are required..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">Confirm Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
