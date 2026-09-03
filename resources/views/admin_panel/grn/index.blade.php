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
    .filter-panel {
        background-color: #f8fafc !important;
        border: 2px dashed #94a3b8 !important;
        border-radius: 10px !important;
        padding: 18px !important;
    }
    .filter-panel label {
        font-size: 12px;
        font-weight: 700 !important;
        color: #475569 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-panel .form-control, .filter-panel .form-select {
        border: 2px solid #cbd5e1 !important;
        border-radius: 6px !important;
        font-weight: 500 !important;
        height: 38px !important;
    }
    .btn-premium-primary {
        background-color: #2563eb !important;
        border: 2px solid #1d4ed8 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        height: 38px !important;
        padding: 0 16px !important;
    }
    .btn-xs {
        padding: 4px 8px !important;
        font-size: 11px !important;
        border-radius: 4px !important;
        font-weight: 700 !important;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin: 2px !important;
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
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="page-title">
                <h4>Goods Receipt Notes (GRN)</h4>
                <h6>Manage received inventory</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('grn.create') }}" class="btn btn-premium-primary">
                    <i class="fa fa-plus me-2"></i>Create New GRN
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card premium-card">
                    <div class="card-body">
                        <h6 class="text-muted">Total GRNs</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card premium-card">
                    <div class="card-body">
                        <h6 class="text-muted">QC Pending</h6>
                        <h3 class="mb-0 text-warning">{{ $stats['qc_pending'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card premium-card">
                    <div class="card-body">
                        <h6 class="text-muted">QC Passed</h6>
                        <h3 class="mb-0 text-success">{{ $stats['qc_passed'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card premium-card">
                    <div class="card-body">
                        <h6 class="text-muted">Has Rejected Items</h6>
                        <h3 class="mb-0 text-danger">{{ $stats['has_rejected'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card premium-card">
            <div class="card-body">
                <form action="{{ route('grn.index') }}" method="GET" class="filter-panel mb-4">
                    <div class="row align-items-end">
                        <div class="col-md-2 mb-3 mb-md-0">
                            <label>QC Status</label>
                            <select name="qc_status" class="form-select">
                                <option value="">All</option>
                                <option value="pending" {{ request('qc_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="passed" {{ request('qc_status') == 'passed' ? 'selected' : '' }}>Passed</option>
                                <option value="failed" {{ request('qc_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="partial" {{ request('qc_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 mb-md-0">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 mb-md-0">
                            <label>From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2 mb-3 mb-md-0">
                            <label>To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" placeholder="GRN#, PO#..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-premium-primary w-100"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>GRN#</th>
                                <th>Date</th>
                                <th>PO#</th>
                                <th>Vendor</th>
                                <th>Delivery Challan#</th>
                                <th>QC Status</th>
                                <th>Status</th>
                                <th>Received By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grns ?? [] as $grn)
                            <tr>
                                <td><strong>{{ $grn->grn_number }}</strong></td>
                                <td>{{ date('d-M-Y', strtotime($grn->received_date)) }}</td>
                                <td>{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</td>
                                <td>{{ $grn->vendor->name ?? 'N/A' }}</td>
                                <td>{{ $grn->delivery_challan_no }}</td>
                                <td>
                                    @if($grn->qc_status == 'pending') <span class="badge-status badge-qc-pending">Pending</span>
                                    @elseif($grn->qc_status == 'passed') <span class="badge-status badge-qc-passed">Passed</span>
                                    @elseif($grn->qc_status == 'failed') <span class="badge-status badge-qc-failed">Failed</span>
                                    @elseif($grn->qc_status == 'partial') <span class="badge-status badge-qc-partial">Partial</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($grn->status) }}</td>
                                <td>{{ $grn->receiver->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('grn.show', $grn->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">No GRNs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($grns) && method_exists($grns, 'links'))
                <div class="mt-4">
                    {{ $grns->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
