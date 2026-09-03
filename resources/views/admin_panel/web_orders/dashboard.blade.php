@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">

<style>
    .page-container {
        max-width: 1350px;
        margin: 0 auto;
        padding: 24px;
        background-color: #f8fafc;
        border-radius: 16px;
    }
    .dashboard-header h4 {
        color: #0f172a;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        padding: 20px;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .card-title-sub {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .card-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 4px;
    }
    .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .content-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }
    .content-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .content-card-body {
        padding: 24px;
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
    .account-badge-active {
        background-color: #d1fae5;
        color: #065f46;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
    }
    .account-badge-missing {
        background-color: #fee2e2;
        color: #991b1b;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
    }
</style>

<div class="page-container mt-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 dashboard-header flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <h4 class="fw-bold mb-0"><i class="fa-solid fa-chart-line text-indigo me-2" style="color:#4f46e5;"></i> Web Sales Dashboard</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('web_orders.index') }}" class="btn btn-outline-secondary px-3 shadow-sm d-flex align-items-center gap-2" style="border-radius:8px;">
                <i class="fas fa-list"></i> View Web Orders
            </a>
        </div>
    </div>

    <!-- Today's Stats Row -->
    <div class="mb-4">
        <h6 class="fw-semibold text-muted mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">Today's Activity</h6>
        <div class="row g-3">
            <!-- Today's Revenue -->
            <div class="col-12 col-md-6">
                <div class="stat-card d-flex align-items-center justify-content-between" style="border-left: 4px solid #6366f1;">
                    <div>
                        <span class="card-title-sub" style="color: #6366f1;">Today's Revenue</span>
                        <div class="card-value">Rs {{ number_format($stats['today_sales'], 2) }}</div>
                    </div>
                    <div class="icon-wrapper" style="background-color: #e0e7ff; color: #6366f1;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Orders -->
            <div class="col-12 col-md-6">
                <div class="stat-card d-flex align-items-center justify-content-between" style="border-left: 4px solid #ef4444;">
                    <div>
                        <span class="card-title-sub" style="color: #ef4444;">Today's Orders</span>
                        <div class="card-value">{{ $stats['today_orders_count'] }}</div>
                    </div>
                    <div class="icon-wrapper" style="background-color: #fee2e2; color: #ef4444;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lifetime / Overall Stats Row -->
    <div class="mb-4">
        <h6 class="fw-semibold text-muted mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">Overall Summary</h6>
        <div class="row g-3">
            <!-- Card 1: Total Web Sales -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title-sub">Total Revenue</span>
                        <div class="card-value">Rs {{ number_format($stats['total_sales'], 2) }}</div>
                    </div>
                    <div class="icon-wrapper" style="background-color: #f1f5f9; color: #475569;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Easypaisa Sales -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title-sub">Easypaisa Recv</span>
                        <div class="card-value">Rs {{ number_format($stats['total_easypaisa_confirmed'], 2) }}</div>
                    </div>
                    <div class="icon-wrapper" style="background-color: #d1fae5; color: #059669;">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Delivered COD Sales -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title-sub">COD Received</span>
                        <div class="card-value">Rs {{ number_format($stats['total_cod_delivered'], 2) }}</div>
                    </div>
                    <div class="icon-wrapper" style="background-color: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                </div>
            </div>

            <!-- Card 4: Total Orders count -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title-sub">Total Orders</span>
                        <div class="card-value">{{ $stats['total_orders'] }}</div>
                    </div>
                    <div class="icon-wrapper" style="background-color: #fef3c7; color: #d97706;">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Status Row -->
    <div class="row g-4 mb-4">
        <!-- Easypaisa Account Card -->
        <div class="col-12 col-md-6">
            <div class="content-card h-100">
                <div class="content-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-slate-800"><i class="fas fa-money-check text-success me-2"></i> Easypaisa Account Details</h5>
                    @if($easypaisaAccount)
                        <span class="account-badge-active">Linked</span>
                    @else
                        <span class="account-badge-missing">Not Found</span>
                    @endif
                </div>
                <div class="content-card-body">
                    @if($easypaisaAccount)
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Account Title:</span>
                            <strong class="text-slate-800">{{ $easypaisaAccount->title }}</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Account Code:</span>
                            <span class="badge bg-light text-dark fw-bold">{{ $easypaisaAccount->account_code ?: 'N/A' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Account Type:</span>
                            <span class="text-capitalize fw-semibold text-slate-700">{{ $easypaisaAccount->type }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <h4 class="fw-bold mb-0 text-slate-800" style="font-size: 1.3rem;">Current Balance:</h4>
                            <h3 class="fw-bold mb-0 text-success" style="font-size: 1.6rem;">Rs {{ number_format($easypaisaBalance, 2) }}</h3>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                            <p class="text-muted mb-0">Easypaisa account was not found in Chart of Accounts.</p>
                            <p class="text-muted small mt-1">Please create an account named "Easypaisa" inside Accounts Head menu to view balance details here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cash in Hand Account Card -->
        <div class="col-12 col-md-6">
            <div class="content-card h-100">
                <div class="content-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-slate-800"><i class="fas fa-coins text-info me-2"></i> Cash Account Details</h5>
                    @if($cashInHandAccount)
                        <span class="account-badge-active">Linked</span>
                    @else
                        <span class="account-badge-missing">Not Found</span>
                    @endif
                </div>
                <div class="content-card-body">
                    @if($cashInHandAccount)
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Account Title:</span>
                            <strong class="text-slate-800">{{ $cashInHandAccount->title }}</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Account Code:</span>
                            <span class="badge bg-light text-dark fw-bold">{{ $cashInHandAccount->account_code ?: 'N/A' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Account Type:</span>
                            <span class="text-capitalize fw-semibold text-slate-700">{{ $cashInHandAccount->type }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <h4 class="fw-bold mb-0 text-slate-800" style="font-size: 1.3rem;">Current Balance:</h4>
                            <h3 class="fw-bold mb-0 text-info" style="font-size: 1.6rem;">Rs {{ number_format($cashInHandBalance, 2) }}</h3>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                            <p class="text-muted mb-0">Cash Account was not found in Chart of Accounts.</p>
                            <p class="text-muted small mt-1">Please create a Cash/Cash in Hand Account in your financial accounts configuration to show details here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Table & Status Summary) -->
    <div class="row g-4">
        <!-- Web Sales Analytics Ledger (Recent Web Transactions) -->
        <div class="col-12 col-lg-8">
            <div class="content-card">
                <div class="content-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-slate-800"><i class="fas fa-list-ol text-indigo me-2"></i> Web Sales Analytics Ledger</h5>
                    <span class="badge bg-primary text-white" style="font-size: 11px;">Real-time</span>
                </div>
                <div class="content-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Date / Time</th>
                                    <th>Order Info</th>
                                    <th>Method / Type</th>
                                    <th>Short Description</th>
                                    <th class="text-end pe-4">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $t)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $t->date }}</td>
                                    <td>
                                        <a href="{{ route('web_orders.show', $t->order_id) }}" class="text-decoration-none fw-bold text-indigo" style="color:#4f46e5;">
                                            #{{ $t->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $t->status_badge }} px-2 py-1" style="font-size: 10px;">
                                            {{ $t->type }}
                                        </span>
                                    </td>
                                    <td class="text-slate-600 small">{{ $t->description }}</td>
                                    <td class="text-end pe-4 fw-bold text-slate-800">Rs {{ number_format($t->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-history fs-3 mb-2 d-block"></i>
                                        No sales transactions found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Order Distribution Status & Recent orders -->
        <div class="col-12 col-lg-4">
            <!-- Order Status counts card -->
            <div class="content-card mb-4">
                <div class="content-card-header">
                    <h5 class="mb-0 fw-bold text-slate-800"><i class="fas fa-chart-pie me-2 text-warning"></i> Order Status Summary</h5>
                </div>
                <div class="content-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted"><i class="fas fa-clock text-warning me-1"></i> Pending Orders</span>
                        <strong class="text-slate-800">{{ $stats['pending_count'] }}</strong>
                    </div>
                    <div class="progress mb-3" style="height: 6px; border-radius: 3px;">
                        @php $pendingPct = $stats['total_orders'] > 0 ? ($stats['pending_count'] / $stats['total_orders']) * 100 : 0; @endphp
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pendingPct }}%"></div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted"><i class="fas fa-cogs text-primary me-1"></i> Processing Orders</span>
                        <strong class="text-slate-800">{{ $stats['processing_count'] }}</strong>
                    </div>
                    <div class="progress mb-3" style="height: 6px; border-radius: 3px;">
                        @php $processingPct = $stats['total_orders'] > 0 ? ($stats['processing_count'] / $stats['total_orders']) * 100 : 0; @endphp
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $processingPct }}%"></div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted"><i class="fas fa-truck text-info me-1"></i> Dispatched Orders</span>
                        <strong class="text-slate-800">{{ $stats['shipped_count'] }}</strong>
                    </div>
                    <div class="progress mb-3" style="height: 6px; border-radius: 3px;">
                        @php $shippedPct = $stats['total_orders'] > 0 ? ($stats['shipped_count'] / $stats['total_orders']) * 100 : 0; @endphp
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $shippedPct }}%"></div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted"><i class="fas fa-check-double text-success me-1"></i> Delivered Orders</span>
                        <strong class="text-slate-800">{{ $stats['delivered_count'] }}</strong>
                    </div>
                    <div class="progress mb-3" style="height: 6px; border-radius: 3px;">
                        @php $deliveredPct = $stats['total_orders'] > 0 ? ($stats['delivered_count'] / $stats['total_orders']) * 100 : 0; @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $deliveredPct }}%"></div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted"><i class="fas fa-ban text-danger me-1"></i> Cancelled Orders</span>
                        <strong class="text-slate-800">{{ $stats['cancelled_count'] }}</strong>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 3px;">
                        @php $cancelledPct = $stats['total_orders'] > 0 ? ($stats['cancelled_count'] / $stats['total_orders']) * 100 : 0; @endphp
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $cancelledPct }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Recent Web Orders list -->
            <div class="content-card">
                <div class="content-card-header">
                    <h5 class="mb-0 fw-bold text-slate-800"><i class="fas fa-shopping-cart text-slate-500 me-2"></i> Recent Web Orders</h5>
                </div>
                <div class="content-card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentOrders as $order)
                        <a href="{{ route('web_orders.show', $order->id) }}" class="list-group-item list-group-item-action p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-slate-800 d-block">#{{ $order->order_number }}</span>
                                <span class="small text-muted">{{ $order->shipping_name }}</span>
                            </div>
                            <div class="text-end">
                                <span class="d-block fw-bold text-indigo" style="color:#4f46e5;">Rs {{ number_format($order->total, 2) }}</span>
                                <span class="badge bg-light text-slate-600 border px-2 mt-1" style="font-size: 9px;">
                                    {{ $order->order_status === 'shipped' ? 'Dispatched' : $order->order_status }}
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-4 text-muted">
                            No web orders found.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
