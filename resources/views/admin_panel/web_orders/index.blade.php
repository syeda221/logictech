@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">

<style>
    .page-container {
        max-width: 1350px;
        margin: 0 auto;
        padding: 24px;
        background-color: #f8fafc;
        border-radius: 16px;
    }
    .page-header h4 {
        color: #0f172a;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    .table-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .table th {
        background-color: #f8fafc !important;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 14px 16px;
    }
    .table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }
    .alert-success {
        background-color: #d1fae5;
        border-color: #a7f3d0;
        color: #065f46;
        border-radius: 12px;
        padding: 16px 20px;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(6, 95, 70, 0.05);
    }
    /* Soft Badges for Payments */
    .badge-pay-paid {
        background-color: #d1fae5;
        color: #065f46;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-pay-failed {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-pay-pending {
        background-color: #fef3c7;
        color: #92400e;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    /* Soft Badges for Order Statuses */
    .badge-status-pending {
        background-color: #fef3c7;
        color: #92400e;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-status-processing {
        background-color: #dbeafe;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-status-shipped {
        background-color: #f3e8ff;
        color: #6b21a8;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-status-delivered {
        background-color: #d1fae5;
        color: #065f46;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-light {
        background-color: #ffffff;
        border-color: #cbd5e1;
        color: #475569;
        padding: 8px 16px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-light:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 20px;
    }
</style>

<div class="page-container mt-3">
    <div class="d-flex align-items-center justify-content-between mb-4 page-header">
        <div>
            <h4 class="fw-bold mb-0">Web Orders</h4>
            <small class="text-muted">Track customer orders, payments, and fulfillment status.</small>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card filter-card mb-4">
        <form action="{{ route('web_orders.index') }}" method="GET" class="row g-3 align-items-end">
            <!-- Search field -->
            <div class="col-12 col-sm-6 col-md-3">
                <label for="search" class="form-label small fw-semibold text-slate-600">Search Order</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="ID, Name, or Phone..." value="{{ request('search') }}">
            </div>

            <!-- Order Status -->
            <div class="col-12 col-sm-6 col-md-2">
                <label for="order_status" class="form-label small fw-semibold text-slate-600">Order Status</label>
                <select name="order_status" id="order_status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Dispatched</option>
                    <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Payment Status -->
            <div class="col-12 col-sm-6 col-md-2">
                <label for="payment_status" class="form-label small fw-semibold text-slate-600">Payment Status</label>
                <select name="payment_status" id="payment_status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="confirmed" {{ request('payment_status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <!-- Payment Method -->
            <div class="col-12 col-sm-6 col-md-2">
                <label for="payment_method" class="form-label small fw-semibold text-slate-600">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-select">
                    <option value="">All Methods</option>
                    <option value="COD" {{ request('payment_method') == 'COD' ? 'selected' : '' }}>COD / Cash</option>
                    <option value="Easypaisa" {{ request('payment_method') == 'Easypaisa' ? 'selected' : '' }}>Easypaisa</option>
                </select>
            </div>

            <!-- Date From -->
            <div class="col-12 col-sm-6 col-md-1-5" style="flex: 1; min-width: 120px;">
                <label for="date_from" class="form-label small fw-semibold text-slate-600">From Date</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <!-- Date To -->
            <div class="col-12 col-sm-6 col-md-1-5" style="flex: 1; min-width: 120px;">
                <label for="date_to" class="form-label small fw-semibold text-slate-600">To Date</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <!-- Buttons -->
            <div class="col-12 col-md-auto d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary px-3 shadow-sm d-flex align-items-center gap-1" style="height: 38px; border-radius: 8px;"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('web_orders.index') }}" class="btn btn-light px-3 border shadow-sm d-flex align-items-center gap-1" style="height: 38px; border-radius: 8px; background-color: #f8fafc;"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4 border" role="alert">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="ordersTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="ps-4"><span class="fw-bold text-indigo" style="color: #4f46e5;">#{{ $order->order_number }}</span></td>
                            <td class="text-slate-600 fw-medium">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            <td class="fw-bold text-slate-800">{{ $order->customer->name ?? 'Guest' }}</td>
                            <td class="text-slate-600">{{ $order->customer->phone ?? 'N/A' }}</td>
                            <td class="fw-bold text-slate-800">Rs. {{ number_format($order->total, 2) }}</td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge-pay-paid"><i class="fas fa-check-circle"></i> Paid</span>
                                @elseif($order->payment_status == 'failed')
                                    <span class="badge-pay-failed"><i class="fas fa-times-circle"></i> Failed</span>
                                @else
                                    <span class="badge-pay-pending"><i class="fas fa-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = 'badge-status-pending';
                                    $statusIcon = 'fa-clock';
                                    if ($order->order_status == 'processing') {
                                        $statusClass = 'badge-status-processing';
                                        $statusIcon = 'fa-cogs';
                                    } elseif ($order->order_status == 'shipped') {
                                        $statusClass = 'badge-status-shipped';
                                        $statusIcon = 'fa-truck';
                                    } elseif ($order->order_status == 'delivered') {
                                        $statusClass = 'badge-status-delivered';
                                        $statusIcon = 'fa-box-open';
                                    } elseif ($order->order_status == 'cancelled') {
                                        $statusClass = 'badge-status-cancelled';
                                        $statusIcon = 'fa-ban';
                                    }
                                @endphp
                                <span class="{{ $statusClass }} text-capitalize">
                                    <i class="fas {{ $statusIcon }}"></i> {{ $order->order_status === 'shipped' ? 'Dispatched' : $order->order_status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('web_orders.show', $order->id) }}" class="btn btn-sm btn-light border">
                                    <i class="fas fa-eye me-1"></i> View Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-top d-flex justify-content-end">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            "paging": false,
            "info": false,
            "order": [[ 1, "desc" ]]
        });
    });
</script>
@endsection
