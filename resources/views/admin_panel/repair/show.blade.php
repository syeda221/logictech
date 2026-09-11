@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Repair Job Details & Audit Console
           ========================================================================== */
        :root {
            --erp-bg-card: #ffffff;
            --erp-border: #e2e8f0;
            --erp-border-subtle: #f1f5f9;
            --erp-text-main: #0f172a;
            --erp-text-muted: #64748b;
            --erp-primary: #2563eb;
            --erp-primary-hover: #1d4ed8;
            --erp-success: #059669;
            --erp-warning: #d97706;
            --erp-danger: #dc2626;
        }

        .erp-page-header {
            margin-bottom: 1.25rem;
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

        .card-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-bottom: 1.25rem;
            overflow: hidden;
        }
        .card-panel-header {
            padding: 0.85rem 1.15rem;
            background: #ffffff;
            border-bottom: 1.5px solid #dbeafe;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-panel-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }
        .card-panel-body {
            padding: 1.15rem;
        }

        /* Metric Tile */
        .metric-tile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .metric-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .metric-value {
            font-size: 1.20rem;
            font-weight: 800;
            font-family: monospace;
            color: #0f172a;
        }

        /* Audit Timeline */
        .audit-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 8px 10px;
            border-bottom: 1.5px solid #e2e8f0;
        }
        .audit-table td {
            font-size: 0.78rem;
            padding: 8px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Account Balance Badge */
        .account-balance-badge-wrap {
            display: inline-block;
            margin-top: 4px;
        }
        .account-balance-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Page Header --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-primary px-2.5 py-1 font-monospace" style="font-size: 0.82rem;">
                                {{ $repair->repair_no }}
                            </span>
                            {!! $repair->status_badge !!}
                            {!! $repair->priority_badge !!}
                            <h4 class="erp-title mb-0">
                                {{ $repair->item_name }}
                            </h4>
                        </div>
                        <p class="erp-subtitle">
                            Customer: <strong>{{ $repair->customer_display_name }}</strong> ({{ $repair->customer_display_phone }}) &bull; Received on {{ $repair->received_date ? $repair->received_date->format('d-M-Y') : 'N/A' }}
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('repair.print.thermal', $repair->id) }}" target="_blank" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1" style="font-weight: 600; border-radius: 7px; padding: 6px 12px;">
                            <i class="fas fa-receipt text-secondary"></i> 80mm Slip
                        </a>
                        <a href="{{ route('repair.print.a4', $repair->id) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="font-weight: 600; border-radius: 7px; padding: 6px 12px;">
                            <i class="fas fa-print"></i> Print A4 Job Sheet
                        </a>

                        @if ($repair->status !== 'delivered')
                            <button type="button" class="btn btn-sm btn-warning text-white fw-bold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#statusModal" style="border-radius: 7px; padding: 6px 12px;">
                                <i class="fas fa-tasks"></i> Update Status
                            </button>
                            <button type="button" class="btn btn-sm btn-success fw-bold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#deliverModal" style="border-radius: 7px; padding: 6px 14px;">
                                <i class="fas fa-check-double"></i> Deliver &amp; Settle Bill
                            </button>
                        @endif

                        <a href="{{ route('repair.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 7px; padding: 6px 12px;">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                {{-- Flash Notifications --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                        <i class="fas fa-check-circle fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Top Financial Metric Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="metric-tile">
                            <div class="metric-label"><i class="fas fa-calculator text-primary me-1"></i> Estimated Quote</div>
                            <div class="metric-value text-primary">Rs. {{ number_format($repair->estimated_cost, 2) }}</div>
                            <small class="text-muted" style="font-size: 0.68rem;">Initial quotation provided</small>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="metric-tile">
                            <div class="metric-label"><i class="fas fa-hand-holding-usd text-success me-1"></i> Advance Collected</div>
                            <div class="metric-value text-success">Rs. {{ number_format($repair->advance_paid, 2) }}</div>
                            <small class="text-muted" style="font-size: 0.68rem;">
                                @if($repair->advanceAccount)
                                    Deposited into: <strong>{{ $repair->advanceAccount->title }}</strong>
                                @else
                                    No advance received
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="metric-tile">
                            <div class="metric-label"><i class="fas fa-file-invoice-dollar text-dark me-1"></i> Total Bill / Charges</div>
                            <div class="metric-value text-dark">
                                Rs. {{ number_format($repair->total_charges > 0 ? $repair->total_charges : $repair->estimated_cost, 2) }}
                            </div>
                            <small class="text-muted" style="font-size: 0.68rem;">
                                Parts: Rs. {{ number_format($repair->parts_charges, 2) }} | Service: Rs. {{ number_format($repair->service_charges, 2) }}
                            </small>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="metric-tile" style="{{ $repair->due_amount > 0 ? 'border-color: #fca5a5; background: #fffafb;' : 'border-color: #86efac; background: #f0fdf4;' }}">
                            <div class="metric-label" style="color: {{ $repair->due_amount > 0 ? '#b91c1c' : '#15803d' }};">
                                <i class="fas fa-wallet me-1"></i> Net Balance Due
                            </div>
                            <div class="metric-value" style="color: {{ $repair->due_amount > 0 ? '#dc2626' : '#16a34a' }};">
                                Rs. {{ number_format($repair->due_amount, 2) }}
                            </div>
                            <small class="text-muted" style="font-size: 0.68rem;">
                                {{ $repair->due_amount > 0 ? 'Pending collection from customer' : 'Fully Settled / Paid' }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    {{-- LEFT COLUMN: Details & Diagnosis (col-lg-7) --}}
                    <div class="col-lg-7">

                        {{-- Device & Problem Statement Card --}}
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <h6 class="card-panel-title">
                                    <i class="fas fa-microchip text-primary"></i> Device &amp; Defect Profile
                                </h6>
                                <span class="badge bg-light text-dark border px-2 font-monospace" style="font-size: 0.70rem;">
                                    SN: {{ $repair->serial_no ?: 'No Serial' }}
                                </span>
                            </div>
                            <div class="card-panel-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.82rem;">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted fw-bold" style="width: 140px;">Device / Product:</td>
                                                <td class="fw-bold text-dark">{{ $repair->item_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-bold">Brand &amp; Model:</td>
                                                <td>{{ $repair->brand_model ?: '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-bold">Physical Condition:</td>
                                                <td>
                                                    <span class="badge bg-light text-secondary border">
                                                        {{ $repair->physical_condition ?: 'Standard used condition' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-bold">Accessories With Item:</td>
                                                <td>
                                                    @if($repair->accessories_received)
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                            {{ $repair->accessories_received }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">None (Unit only)</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-bold">Promised Delivery:</td>
                                                <td class="fw-semibold text-primary">
                                                    {{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d-M-Y') : 'Not specified' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3 p-2.5 rounded" style="background-color: #fff5f5; border: 1px solid #fed7d7;">
                                    <div class="fw-bold text-danger mb-1" style="font-size: 0.75rem; text-transform: uppercase;">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Customer Reported Fault:
                                    </div>
                                    <div style="font-size: 0.84rem; color: #1f2937;">
                                        {{ $repair->problem_description }}
                                    </div>
                                </div>

                                @if($repair->technician_notes)
                                <div class="mt-2.5 p-2.5 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="fw-bold text-secondary mb-1" style="font-size: 0.75rem; text-transform: uppercase;">
                                        <i class="fas fa-wrench me-1"></i> Technician Inspection Notes:
                                    </div>
                                    <div style="font-size: 0.82rem; color: #334155;">
                                        {{ $repair->technician_notes }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Customer & Logistics Card --}}
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <h6 class="card-panel-title">
                                    <i class="fas fa-user-circle text-primary"></i> Customer Contact &amp; Custody
                                </h6>
                            </div>
                            <div class="card-panel-body">
                                <div class="row g-2" style="font-size: 0.82rem;">
                                    <div class="col-sm-6">
                                        <div class="text-muted small fw-bold">Customer Name:</div>
                                        <div class="fw-bold text-dark">{{ $repair->customer_display_name }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-muted small fw-bold">Phone Number:</div>
                                        <div class="fw-bold text-dark">
                                            <a href="tel:{{ $repair->customer_display_phone }}" class="text-decoration-none">
                                                <i class="fas fa-phone-alt text-success me-1"></i> {{ $repair->customer_display_phone }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="text-muted small fw-bold">Address / Location:</div>
                                        <div>{{ $repair->customer_display_address ?: 'No address specified' }}</div>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <div class="text-muted small fw-bold">Received By:</div>
                                        <div>{{ $repair->receiver->name ?? 'Admin Staff' }}</div>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <div class="text-muted small fw-bold">Delivered By:</div>
                                        <div>{{ $repair->deliverer->name ?? ($repair->status === 'delivered' ? 'Staff' : 'Not delivered yet') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT COLUMN: Accounting, Settlements & Full Audit Trail (col-lg-5) --}}
                    <div class="col-lg-5">

                        {{-- Accounting & Payment Records Card --}}
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <h6 class="card-panel-title">
                                    <i class="fas fa-money-check-alt text-success"></i> Payment &amp; Ledger Accounts
                                </h6>
                            </div>
                            <div class="card-panel-body">
                                <div class="d-flex flex-column gap-2" style="font-size: 0.80rem;">
                                    {{-- Advance Entry --}}
                                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border">
                                        <div>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">Advance Payment</span>
                                            <div class="fw-bold text-dark">
                                                {{ $repair->advanceAccount ? $repair->advanceAccount->title : 'No Account' }}
                                            </div>
                                            <small class="text-muted">{{ $repair->received_date ? $repair->received_date->format('d-M-Y') : '' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="font-monospace fw-bold text-success" style="font-size: 0.95rem;">
                                                Rs. {{ number_format($repair->advance_paid, 2) }}
                                            </div>
                                            <span class="badge bg-white text-muted border px-1.5" style="font-size: 0.65rem;">Auto-Voucher</span>
                                        </div>
                                    </div>

                                    {{-- Final Payment Entry --}}
                                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border">
                                        <div>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">Final Settlement</span>
                                            <div class="fw-bold text-dark">
                                                {{ $repair->finalAccount ? $repair->finalAccount->title : ($repair->status === 'delivered' ? 'Cash Settlement' : 'Pending Delivery') }}
                                            </div>
                                            <small class="text-muted">{{ $repair->delivered_at ? $repair->delivered_at->format('d-M-Y') : 'Not yet delivered' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="font-monospace fw-bold text-primary" style="font-size: 0.95rem;">
                                                Rs. {{ number_format($repair->final_paid, 2) }}
                                            </div>
                                            @if($repair->final_paid > 0)
                                                <span class="badge bg-white text-muted border px-1.5" style="font-size: 0.65rem;">Auto-Voucher</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- AUDIT TRAIL LOGS CARD --}}
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <h6 class="card-panel-title">
                                    <i class="fas fa-history text-secondary"></i> Complete Audit Trail &amp; History
                                </h6>
                                <span class="badge rounded-pill bg-light text-muted border font-monospace" style="font-size: 0.70rem;">
                                    {{ $repair->logs->count() }} Actions
                                </span>
                            </div>
                            <div class="card-panel-body p-0">
                                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                    <table class="table audit-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Time &amp; User</th>
                                                <th>Action</th>
                                                <th>Ledger / Account</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($repair->logs as $log)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark" style="font-size: 0.74rem;">{{ $log->user->name ?? 'System' }}</div>
                                                        <div class="text-muted" style="font-size: 0.68rem;">{{ $log->created_at->format('d-M h:i A') }}</div>
                                                    </td>
                                                    <td>
                                                        @if($log->action === 'created')
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;">Received</span>
                                                        @elseif($log->action === 'status_updated')
                                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;">Status Change</span>
                                                        @elseif($log->action === 'delivered')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">Delivered</span>
                                                        @else
                                                            <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">{{ $log->action }}</span>
                                                        @endif

                                                        @if($log->amount > 0)
                                                            <div class="font-monospace fw-bold text-success mt-0.5" style="font-size: 0.70rem;">
                                                                +Rs. {{ number_format($log->amount, 2) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($log->account)
                                                            <div class="fw-semibold text-dark" style="font-size: 0.72rem;">{{ $log->account->title }}</div>
                                                            <small class="text-muted" style="font-size: 0.66rem;">{{ $log->account->account_code }}</small>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td style="max-width: 140px; font-size: 0.72rem; color: #475569;">
                                                        {{ $log->notes }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">No audit records found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL 1: UPDATE REPAIR STATUS --}}
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('repair.status.update', $repair->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning text-white py-2.5 px-3">
                        <h6 class="modal-title fw-bold" id="statusModalLabel">
                            <i class="fas fa-tasks me-1"></i> Update Workshop Status: {{ $repair->repair_no }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label class="form-label required">Current Status</label>
                            <select name="status" class="form-select" required>
                                <option value="received" {{ $repair->status === 'received' ? 'selected' : '' }}>Product Received</option>
                                <option value="diagnosing" {{ $repair->status === 'diagnosing' ? 'selected' : '' }}>Under Diagnosis</option>
                                <option value="in_progress" {{ $repair->status === 'in_progress' ? 'selected' : '' }}>In Progress / Repairing</option>
                                <option value="waiting_parts" {{ $repair->status === 'waiting_parts' ? 'selected' : '' }}>Waiting for Spare Parts</option>
                                <option value="completed" {{ $repair->status === 'completed' ? 'selected' : '' }}>Repaired &amp; Ready for Pickup</option>
                                <option value="cancelled" {{ $repair->status === 'cancelled' ? 'selected' : '' }}>Cancelled / Return Without Repair</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Technician Diagnosis Notes</label>
                            <textarea name="technician_notes" rows="2" class="form-control" placeholder="Update diagnostic findings, replaced parts, testing results...">{{ $repair->technician_notes }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Audit Log Remarks</label>
                            <input type="text" name="log_note" class="form-control" placeholder="Reason for change / customer update...">
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 bg-light">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-warning text-white fw-bold px-3">Save Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: DELIVER & COLLECT FINAL PAYMENT --}}
    <div class="modal fade" id="deliverModal" tabindex="-1" aria-labelledby="deliverModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('repair.deliver', $repair->id) }}" method="POST" id="modalDeliverForm">
                    @csrf
                    <div class="modal-header bg-success text-white py-2.5 px-3">
                        <h6 class="modal-title fw-bold" id="deliverModalLabel">
                            <i class="fas fa-check-circle me-1"></i> Deliver Device &amp; Collect Settlement
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="alert alert-light border py-2 px-3 mb-3 d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                            <div>
                                <span class="text-muted">Device:</span> <strong>{{ $repair->item_name }}</strong><br>
                                <span class="text-muted">Customer:</span> <strong>{{ $repair->customer_display_name }}</strong>
                            </div>
                            <div class="text-end">
                                <span class="text-muted">Advance Paid:</span><br>
                                <span class="font-monospace fw-bold text-success" id="modalAdvDisp">Rs. {{ number_format($repair->advance_paid, 2) }}</span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label required">Service Charges (Rs.)</label>
                                <input type="number" step="0.01" min="0" name="service_charges" id="modalServiceCharges"
                                    class="form-control text-end font-monospace fw-bold" value="{{ $repair->service_charges > 0 ? $repair->service_charges : $repair->estimated_cost }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Spare Parts Cost (Rs.)</label>
                                <input type="number" step="0.01" min="0" name="parts_charges" id="modalPartsCharges"
                                    class="form-control text-end font-monospace fw-bold" value="{{ $repair->parts_charges }}">
                            </div>
                        </div>

                        <div class="p-2.5 rounded bg-light border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.80rem;">
                                <span class="text-muted fw-bold">Total Final Bill:</span>
                                <span class="font-monospace fw-bold text-dark" id="modalTotalBill">Rs. 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1 text-success" style="font-size: 0.80rem;">
                                <span>Less: Advance Paid:</span>
                                <span class="font-monospace fw-bold">-Rs. {{ number_format($repair->advance_paid, 2) }}</span>
                            </div>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between align-items-center fw-bold text-danger" style="font-size: 0.88rem;">
                                <span>Net Remaining Due:</span>
                                <span class="font-monospace" id="modalNetDue">Rs. 0.00</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0 required">Final Payment Collected Now (Rs.)</label>
                                <a href="javascript:void(0)" id="modalFillDueBtn" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.70rem;">Auto-fill Net Due</a>
                            </div>
                            <input type="number" step="0.01" min="0" name="final_paid" id="modalFinalPaid"
                                class="form-control text-end font-monospace fw-bold text-success" value="0.00">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Deposit In Account (Cash / Bank)</label>
                            <select name="final_account_id" id="modalFinalAccount" class="form-select">
                                <option value="" selected>-- Select Account --</option>
                                @foreach ($accounts as $acc)
                                    <option value="{{ $acc->id }}" data-balance="{{ $acc->current_balance ?? 0 }}">
                                        {{ $acc->title }} ({{ $acc->account_code }})
                                    </option>
                                @endforeach
                            </select>

                            <div class="account-balance-badge-wrap" id="modalAccBalWrap" style="display: none;">
                                <span class="account-balance-badge" id="modalAccBalBadge">
                                    <i class="fas fa-wallet"></i> Current Bal: Rs. <span id="modalAccBalVal">0.00</span> <span id="modalAccBalType">DR</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 bg-light">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success fw-bold px-3">Confirm Delivery &amp; Settle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const advPaid = {{ (float) $repair->advance_paid }};
            const serviceInput = document.getElementById('modalServiceCharges');
            const partsInput = document.getElementById('modalPartsCharges');
            const totalBillDisp = document.getElementById('modalTotalBill');
            const netDueDisp = document.getElementById('modalNetDue');
            const finalPaidInput = document.getElementById('modalFinalPaid');
            const fillDueBtn = document.getElementById('modalFillDueBtn');
            const finalAccSelect = document.getElementById('modalFinalAccount');

            function calcDelivery() {
                const s = parseFloat(serviceInput.value) || 0;
                const p = parseFloat(partsInput.value) || 0;
                const total = s + p;
                const due = Math.max(0, total - advPaid);

                totalBillDisp.innerText = 'Rs. ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                netDueDisp.innerText = 'Rs. ' + due.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                return due;
            }

            if (serviceInput && partsInput) {
                serviceInput.addEventListener('input', calcDelivery);
                partsInput.addEventListener('input', calcDelivery);
                const initDue = calcDelivery();
                finalPaidInput.value = initDue.toFixed(2);
            }

            if (fillDueBtn) {
                fillDueBtn.addEventListener('click', function() {
                    const due = calcDelivery();
                    finalPaidInput.value = due.toFixed(2);
                });
            }

            // Live Account Balance Badge for Deliver Modal
            const modalAccBalWrap = document.getElementById('modalAccBalWrap');
            const modalAccBalVal = document.getElementById('modalAccBalVal');
            const modalAccBalType = document.getElementById('modalAccBalType');

            function updateDeliverAccBadge() {
                const opt = finalAccSelect.options[finalAccSelect.selectedIndex];
                if (!opt || !opt.value) {
                    modalAccBalWrap.style.display = 'none';
                    return;
                }
                const bal = parseFloat(opt.getAttribute('data-balance') || 0);
                modalAccBalVal.innerText = Math.abs(bal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                modalAccBalType.innerText = bal >= 0 ? 'DR' : 'CR';
                modalAccBalWrap.style.display = 'inline-block';
            }

            if (finalAccSelect) {
                finalAccSelect.addEventListener('change', updateDeliverAccBadge);
            }
        });
    </script>
@endsection
