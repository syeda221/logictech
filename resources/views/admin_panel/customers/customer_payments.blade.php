@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ==========================================================================
       Enterprise ERP Design System - Customer Payments & Recoveries
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

    /* Page Header */
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

    /* Top Action Buttons (Exact Sales UI) */
    .btn-erp-primary {
        background-color: #2563eb;
        border: 1px solid #1d4ed8;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.815rem;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.2);
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-erp-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.25);
    }

    .btn-erp-outline {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155 !important;
        font-weight: 600;
        font-size: 0.815rem;
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
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

    /* Metric Summary KPI Cards (Exact Sales UI) */
    .erp-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .erp-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .erp-kpi-icon-wrap {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .erp-kpi-icon-blue {
        background-color: #eff6ff;
        color: #2563eb;
    }
    .erp-kpi-icon-green {
        background-color: #ecfdf5;
        color: #059669;
    }
    .erp-kpi-icon-amber {
        background-color: #fffbeb;
        color: #d97706;
    }
    .erp-kpi-icon-purple {
        background-color: #faf5ff;
        color: #7e22ce;
    }
    .erp-kpi-content {
        flex: 1;
        min-width: 0;
    }
    .erp-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 0.2rem;
    }
    .erp-kpi-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        letter-spacing: -0.02em;
        margin-bottom: 0.15rem;
    }
    .erp-kpi-sub {
        font-size: 0.75rem;
        color: #94a3b8;
        margin: 0;
    }

    /* Main Table Card */
    .erp-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .erp-table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    /* Enterprise Table Styling */
    .erp-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        font-size: 0.815rem;
    }
    .erp-table thead th {
        background-color: #eff6ff !important;
        color: #1e40af !important;
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 0.9rem;
        border-top: none;
        border-bottom: 2px solid #60a5fa !important;
        border-right: 1px solid #bfdbfe;
        white-space: nowrap;
        vertical-align: middle;
    }
    .erp-table thead th:last-child {
        border-right: none;
    }
    .erp-table tbody td {
        padding: 0.75rem 0.9rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        color: #1e293b;
    }
    .erp-table tbody tr:hover td {
        background-color: #f8fafc;
    }

    /* Customer Avatar */
    .erp-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    .erp-avatar-registered {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }

    /* Method Badges */
    .erp-badge {
        font-size: 0.70rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .badge-cash {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .badge-bank {
        background-color: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }
    .badge-other {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    /* Modal Styling */
    .modal-erp-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }
    .modal-erp-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .modal-erp-body {
        padding: 1.25rem;
    }
    .modal-erp-label {
        font-size: 0.74rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
    }
    .modal-erp-control {
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.85rem;
        padding: 0.55rem 0.8rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .modal-erp-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        outline: none;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid mt-3">

            {{-- Alerts --}}
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i><strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                <div>
                    <h4 class="erp-title">
                        <i class="fas fa-money-bill-wave text-primary"></i> Customer Payments & Recoveries
                    </h4>
                    <p class="erp-subtitle">Manage customer receivables, payments history, cash adjustments & balance settlements</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn-erp-primary" data-bs-toggle="modal" data-bs-target="#paymentModal" onclick="clearPaymentForm()">
                        <i class="fas fa-plus"></i> Add Payment
                    </button>
                    @can('customer.ledger.view')
                        <a class="btn-erp-outline" href="{{ route('customers.ledger') }}">
                            <i class="fas fa-file-invoice text-primary"></i> Customer Ledger
                        </a>
                    @endcan
                    @can('chart.of.accounts.view')
                        <a class="btn-erp-outline" href="{{ route('view_all') }}">
                            <i class="fas fa-book text-secondary"></i> Accounts
                        </a>
                    @endcan
                    <a class="btn-erp-outline" href="{{ route('customers.index') }}">
                        <i class="fas fa-users text-secondary"></i> Customer Directory
                    </a>
                </div>
            </div>

            @php
                $totalCount = $payments->count();
                $totalAmount = $payments->sum('amount');
                $uniqueCustomers = $payments->pluck('customer_id')->unique()->count();
                $avgPayment = $totalCount > 0 ? $totalAmount / $totalCount : 0;
            @endphp

            {{-- 4 Metric Summary Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="erp-kpi-card">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-blue">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label">Total Transactions</div>
                            <div class="erp-kpi-val">{{ number_format($totalCount) }}</div>
                            <p class="erp-kpi-sub">Total payment records</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="erp-kpi-card">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-green">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label">Total Recoveries</div>
                            <div class="erp-kpi-val" style="color: #059669;">Rs. {{ number_format($totalAmount, 2) }}</div>
                            <p class="erp-kpi-sub">Total amount collected</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="erp-kpi-card">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-amber">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label">Active Customers</div>
                            <div class="erp-kpi-val">{{ number_format($uniqueCustomers) }}</div>
                            <p class="erp-kpi-sub">Customers with payments</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="erp-kpi-card">
                        <div class="erp-kpi-icon-wrap erp-kpi-icon-purple">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="erp-kpi-content">
                            <div class="erp-kpi-label">Average Recovery</div>
                            <div class="erp-kpi-val" style="color: #7e22ce;">Rs. {{ number_format($avgPayment, 2) }}</div>
                            <p class="erp-kpi-sub">Per transaction avg</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Data Table Card --}}
            <div class="erp-main-card">
                <div class="erp-table-responsive p-3">
                    <table class="erp-table datanew">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 45px;">#</th>
                                <th class="text-center" style="width: 100px;">DATE</th>
                                <th>CUSTOMER</th>
                                <th class="text-end" style="width: 140px;">AMOUNT (PKR)</th>
                                <th class="text-center" style="width: 120px;">METHOD</th>
                                <th>NOTES / PARTICULARS</th>
                                <th class="text-center" style="width: 100px;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $key => $p)
                                @php
                                    $custName = $p->customer->customer_name ?? 'N/A';
                                    $initial = strtoupper(substr($custName, 0, 1));
                                    $method = strtolower($p->payment_method ?? '');
                                    $methodBadge = 'badge-other';
                                    if (strpos($method, 'cash') !== false) {
                                        $methodBadge = 'badge-cash';
                                    } elseif (strpos($method, 'bank') !== false || strpos($method, 'cheque') !== false || strpos($method, 'online') !== false) {
                                        $methodBadge = 'badge-bank';
                                    }
                                @endphp
                                <tr>
                                    {{-- # --}}
                                    <td class="text-center font-monospace text-muted" style="font-size: 0.75rem;">
                                        {{ $key + 1 }}
                                    </td>

                                    {{-- Date --}}
                                    <td class="text-center font-monospace text-muted" style="font-size: 0.78rem;">
                                        {{ \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') }}
                                    </td>

                                    {{-- Customer --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="erp-avatar erp-avatar-registered">
                                                {{ $initial }}
                                            </div>
                                            <div>
                                                <a href="{{ route('customers.ledger') }}?customer_id={{ $p->customer_id }}" class="fw-bold text-dark text-decoration-none" style="font-size: 0.82rem;" title="View Ledger">
                                                    {{ $custName }}
                                                </a>
                                                @if($p->customer && $p->customer->customer_id)
                                                    <div class="text-muted" style="font-size: 0.70rem;">ID: {{ $p->customer->customer_id }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="text-end font-monospace fw-bold" style="color: #059669; font-size: 0.85rem;">
                                        Rs. {{ number_format($p->amount, 2) }}
                                    </td>

                                    {{-- Method --}}
                                    <td class="text-center">
                                        <span class="erp-badge {{ $methodBadge }}">
                                            <i class="fas fa-money-check-alt me-1"></i> {{ $p->payment_method ?: 'Cash' }}
                                        </span>
                                    </td>

                                    {{-- Notes --}}
                                    <td>
                                        <span class="text-muted" style="font-size: 0.78rem;">
                                            {{ $p->note ?: '-' }}
                                        </span>
                                    </td>

                                    {{-- Action --}}
                                    <td class="text-center">
                                        <form action="{{ route('customer.payments.destroy', $p->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this payment transaction? This will reverse the customer ledger balance.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="border-radius: 6px; font-size: 0.75rem;" title="Delete Payment">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Add Payment Modal (Enterprise ERP Theme) --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('customer.payments.store') }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-erp-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-erp-title">
                        <i class="fas fa-money-check-alt text-primary"></i> Add Customer Payment / Recovery
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-erp-body">
                    
                    {{-- Customer Selector --}}
                    <div class="mb-3">
                        <label class="modal-erp-label">Select Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select modal-erp-control select2" required onchange="fetchCustomerBalance(this.value)">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->customer_name }} {{ $c->customer_id ? '(' . $c->customer_id . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Outstanding Balance Indicator --}}
                    <div class="mb-3 p-2 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                        <div>
                            <span class="small text-muted fw-bold d-block text-uppercase" style="font-size: 0.68rem;">Current Outstanding Balance</span>
                            <span class="fw-bold text-dark font-monospace" id="customer_balance_display" style="font-size: 1rem;">Rs. 0.00</span>
                        </div>
                        <input type="hidden" id="customer_balance" readonly>
                        <span class="badge bg-primary" id="balance_badge">Select a Customer</span>
                    </div>

                    {{-- Adjustment Type --}}
                    <div class="mb-3">
                        <label class="modal-erp-label">Transaction / Adjustment Type <span class="text-danger">*</span></label>
                        <select name="adjustment_type" class="form-select modal-erp-control" required>
                            <option value="minus" selected>- Minus (Payment Received / Balance Decreased)</option>
                            <option value="plus">+ Plus (Debit Adjustment / Outstanding Increased)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="modal-erp-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control modal-erp-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="modal-erp-label">Amount (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" class="form-control modal-erp-control font-monospace fw-bold" placeholder="0.00" required>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-3">
                        <label class="modal-erp-label">Payment Method</label>
                        <input type="text" name="payment_method" class="form-control modal-erp-control" placeholder="e.g. Cash, Bank Alfalah, Online Transfer, Cheque #1234">
                    </div>

                    {{-- Note / Description --}}
                    <div class="mb-3">
                        <label class="modal-erp-label">Note / Particulars</label>
                        <textarea name="note" class="form-control modal-erp-control" rows="2" placeholder="Reference invoice, slip number, remarks..."></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light px-4 py-2.5 border-top">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;">
                        <i class="fas fa-check-circle me-1"></i> Save Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fetchCustomerBalance(customerId) {
        if (!customerId) {
            $('#customer_balance_display').text('Rs. 0.00');
            $('#customer_balance').val('0.00');
            $('#balance_badge').text('Select a Customer').removeClass('bg-success bg-danger').addClass('bg-primary');
            return;
        }

        $.ajax({
            url: '/customer/ledger/' + customerId,
            method: 'GET',
            success: function(response) {
                if (response.closing_balance !== undefined) {
                    var bal = parseFloat(response.closing_balance);
                    $('#customer_balance_display').text('Rs. ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#customer_balance').val(bal.toFixed(2));
                    if (bal > 0) {
                        $('#balance_badge').text('Receivable (Dr)').removeClass('bg-primary bg-success').addClass('bg-danger');
                    } else if (bal < 0) {
                        $('#balance_badge').text('Advance (Cr)').removeClass('bg-primary bg-danger').addClass('bg-success');
                    } else {
                        $('#balance_badge').text('Settled (0.00)').removeClass('bg-danger bg-success').addClass('bg-primary');
                    }
                } else {
                    $('#customer_balance_display').text('Rs. 0.00');
                    $('#customer_balance').val('0.00');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                $('#customer_balance_display').text('Error fetching balance');
            }
        });
    }

    function clearPaymentForm() {
        $('#paymentModal select[name="customer_id"]').val('').trigger('change');
        $('#paymentModal input[name="payment_date"]').val('{{ date("Y-m-d") }}');
        $('#paymentModal input[name="amount"]').val('');
        $('#paymentModal input[name="payment_method"]').val('');
        $('#paymentModal textarea[name="note"]').val('');
        $('#customer_balance_display').text('Rs. 0.00');
        $('#customer_balance').val('');
        $('#balance_badge').text('Select a Customer').removeClass('bg-success bg-danger').addClass('bg-primary');
    }

    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                dropdownParent: $('#paymentModal')
            });
        }
    });
</script>
@endpush