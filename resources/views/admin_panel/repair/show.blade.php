@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Repair Job Card & Invoice Format View
           ========================================================================== */
        .erp-doc-card {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .erp-doc-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .erp-doc-title-badge {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .erp-doc-body {
            padding: 1.5rem;
        }

        /* Invoice Structured Table */
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.25rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        .doc-table th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.65rem 0.85rem;
            border-bottom: 2px solid #cbd5e1;
            border-right: 1px solid #e2e8f0;
        }
        .doc-table td {
            padding: 0.65rem 0.85rem;
            font-size: 0.82rem;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .doc-table td:last-child, .doc-table th:last-child {
            border-right: none;
        }

        /* Invoice Key-Value Grid Box */
        .doc-info-box {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            height: 100%;
        }

        .doc-info-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #2563eb;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.35rem;
        }

        .doc-kv-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.81rem;
            margin-bottom: 6px;
        }
        .doc-kv-row:last-child {
            margin-bottom: 0;
        }
        .doc-kv-label {
            color: #64748b;
            font-weight: 600;
        }
        .doc-kv-value {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }

        /* Invoice Total Block */
        .doc-totals-card {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 1rem 1.25rem;
        }
        .doc-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.83rem;
            padding: 4px 0;
        }

        /* Action Pill Buttons */
        .btn-erp-action {
            font-weight: 600;
            border-radius: 50px !important;
            padding: 6px 16px !important;
            font-size: 0.78rem !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .erp-doc-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .erp-doc-body {
                padding: 1rem;
            }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Header Actions Bar --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-primary"></i> Repair Job Card &amp; Invoice
                        </h4>
                        <p class="text-muted small mb-0 mt-0.5">Ticket #{{ $repair->repair_no }} &bull; Customer: {{ $repair->customer_display_name }}</p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('repair.print.thermal', $repair->id) }}" target="_blank" class="btn btn-outline-dark btn-erp-action bg-white">
                            <i class="fas fa-receipt text-secondary"></i> 80mm Thermal Slip
                        </a>
                        <a href="{{ route('repair.print.jobsheet', $repair->id) }}" target="_blank" class="btn btn-info text-white btn-erp-action fw-bold">
                            <i class="fas fa-clipboard-list"></i> Print A4 Job Sheet
                        </a>
                        <a href="{{ route('repair.print.a4', $repair->id) }}" target="_blank" class="btn btn-primary btn-erp-action fw-bold">
                            <i class="fas fa-file-invoice"></i> Print A4 Invoice
                        </a>

                        @if ($repair->status !== 'delivered')
                            <button type="button" class="btn btn-warning text-white btn-erp-action" data-toggle="modal" data-target="#statusModal" data-bs-toggle="modal" data-bs-target="#statusModal">
                                <i class="fas fa-tasks"></i> Update Status
                            </button>
                            <button type="button" class="btn btn-success btn-erp-action" data-toggle="modal" data-target="#deliverModal" data-bs-toggle="modal" data-bs-target="#deliverModal">
                                <i class="fas fa-check-double"></i> Deliver &amp; Settle Invoice
                            </button>
                        @endif

                        <a href="{{ route('repair.index') }}" class="btn btn-outline-secondary btn-erp-action bg-white">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                {{-- Flash Notifications --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-3" role="alert">
                        <i class="fas fa-check-circle fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-3" role="alert">
                        <i class="fas fa-exclamation-triangle fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- MAIN INVOICE / JOB SHEET DOCUMENT CARD --}}
                <div class="erp-doc-card">
                    {{-- Document Header --}}
                    <div class="erp-doc-header">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary px-3 py-1.5 font-monospace fs-6" style="border-radius: 6px;">
                                    {{ $repair->repair_no }}
                                </span>
                                {!! $repair->status_badge !!}
                                {!! $repair->priority_badge !!}
                            </div>
                            <h2 class="erp-doc-title-badge mt-2 mb-0">
                                {{ $repair->item_name }}
                            </h2>
                        </div>
                        <div class="text-md-end">
                            <div class="text-uppercase fw-bold text-primary-subtle" style="font-size: 0.72rem; letter-spacing: 1px;">Repair Ticket &amp; Job Voucher</div>
                            <div class="font-monospace fw-bold fs-5 text-white">{{ $repair->repair_no }}</div>
                            <div class="text-light small mt-0.5"><i class="far fa-calendar-alt me-1"></i> Date: {{ $repair->received_date ? $repair->received_date->format('d-M-Y') : 'N/A' }}</div>
                        </div>
                    </div>

                    {{-- Document Body --}}
                    <div class="erp-doc-body">

                        {{-- 1. Customer & Schedule Information Grid --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="doc-info-box">
                                    <div class="doc-info-title">
                                        <i class="fas fa-user-circle"></i> Customer Details
                                    </div>
                                    <div class="doc-kv-row">
                                        <span class="doc-kv-label">Customer Name:</span>
                                        <span class="doc-kv-value text-dark fs-6">{{ $repair->customer_display_name }}</span>
                                    </div>
                                    <div class="doc-kv-row">
                                        <span class="doc-kv-label">Mobile / WhatsApp:</span>
                                        <span class="doc-kv-value text-success font-monospace">
                                            <i class="fas fa-phone-alt me-1"></i> {{ $repair->customer_display_phone }}
                                        </span>
                                    </div>
                                    <div class="doc-kv-row">
                                        <span class="doc-kv-label">City / Address:</span>
                                        <span class="doc-kv-value">{{ $repair->customer_display_address ?: 'No address specified' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="doc-info-box">
                                    <div class="doc-info-title">
                                        <i class="fas fa-info-circle"></i> Order &amp; Custody Details
                                    </div>
                                    <div class="doc-kv-row">
                                        <span class="doc-kv-label">Received Date:</span>
                                        <span class="doc-kv-value">{{ $repair->received_date ? $repair->received_date->format('d-M-Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="doc-kv-row">
                                        <span class="doc-kv-label">Promised Delivery:</span>
                                        <span class="doc-kv-value text-primary fw-bold">{{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d-M-Y') : 'Not specified' }}</span>
                                    </div>
                                    <div class="doc-kv-row">
                                        <span class="doc-kv-label">Received By:</span>
                                        <span class="doc-kv-value">{{ $repair->receiver->name ?? 'Admin Staff' }}</span>
                                    </div>
                                    <div class="doc-kv-row">
                                        <span class="doc-kv-label">Delivered By:</span>
                                        <span class="doc-kv-value">{{ $repair->deliverer->name ?? ($repair->status === 'delivered' ? 'Staff' : 'Not delivered yet') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Device Specifications Itemized Table --}}
                        @php
                            $itemsData = json_decode($repair->problem_description, true);
                            if (!is_array($itemsData)) {
                                $itemsData = [
                                    [
                                        'sn' => 1,
                                        'item_name' => $repair->item_name,
                                        'brand_model' => $repair->brand_model,
                                        'serial_no' => $repair->serial_no,
                                        'problem_description' => $repair->problem_description,
                                        'estimated_cost' => $repair->estimated_cost,
                                    ]
                                ];
                            }
                        @endphp
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
                                <i class="fas fa-microchip text-primary"></i> Itemized Repair Specifications &amp; Quotation
                            </h6>
                            <div class="table-responsive">
                                <table class="doc-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; text-align: center;">S/N</th>
                                            <th style="width: 25%;">Item Description</th>
                                            <th style="width: 18%;">Brand / Model</th>
                                            <th style="width: 17%;">Serial No</th>
                                            <th style="width: 22%;">Reported Fault</th>
                                            <th style="width: 13%; text-align: right;">Amount (Rs.)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($itemsData as $idx => $it)
                                            <tr>
                                                <td class="text-center font-monospace fw-bold text-muted">{{ $it['sn'] ?? ($idx + 1) }}</td>
                                                <td class="fw-bold text-dark" style="font-size: 0.84rem;">
                                                    <i class="fas fa-tools text-primary me-1"></i> {{ $it['item_name'] ?? $repair->item_name }}
                                                </td>
                                                <td>{{ $it['brand_model'] ?? '—' }}</td>
                                                <td class="font-monospace fw-bold">{{ $it['serial_no'] ?? 'No Serial' }}</td>
                                                <td>{{ $it['problem_description'] ?? '—' }}</td>
                                                <td class="text-end font-monospace fw-bold text-dark">
                                                    Rs. {{ number_format((float)($it['estimated_cost'] ?? 0), 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="background: #f8fafc;">
                                            <td colspan="5" class="text-end fw-bold">Total Estimated Quote:</td>
                                            <td class="text-end font-monospace fw-bold text-primary fs-6">
                                                Rs. {{ number_format((float)$repair->estimated_cost, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @if($repair->physical_condition)
                                <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center gap-2 mb-2" style="font-size: 0.82rem;">
                                    <span class="fw-bold text-secondary"><i class="fas fa-eye text-primary me-1"></i> Physical Condition:</span>
                                    <span class="text-dark">{{ $repair->physical_condition }}</span>
                                </div>
                            @endif

                            @if($repair->accessories_received)
                                <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center gap-2 mb-3" style="font-size: 0.82rem;">
                                    <span class="fw-bold text-secondary"><i class="fas fa-box text-primary me-1"></i> Accessories Received With Items:</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold">
                                        {{ $repair->accessories_received }}
                                    </span>
                                </div>
                            @endif

                            @if($repair->technician_notes)
                                <div class="p-3 rounded-3 mb-3" style="background-color: #f8fafc; border: 1.5px solid #cbd5e1;">
                                    <div class="fw-bold text-secondary mb-1" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                        <i class="fas fa-wrench me-1"></i> Workshop Diagnosis Notes:
                                    </div>
                                    <div style="font-size: 0.84rem; color: #334155;">
                                        {{ $repair->technician_notes }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- 3. Invoice Financial Settlement & Totals Block --}}
                        <div class="row g-3 align-items-start mb-4">
                            <div class="col-md-7">
                                <div class="doc-info-box">
                                    <div class="doc-info-title">
                                        <i class="fas fa-wallet"></i> Payment &amp; Ledger Vouchers
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-white border">
                                            <div>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle mb-0.5">Advance Payment</span>
                                                <div class="fw-bold text-dark" style="font-size: 0.80rem;">
                                                    {{ $repair->advanceAccount ? $repair->advanceAccount->title : 'No Account' }}
                                                </div>
                                            </div>
                                            <div class="text-end font-monospace fw-bold text-success" style="font-size: 0.92rem;">
                                                Rs. {{ number_format($repair->advance_paid, 2) }}
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-white border">
                                            <div>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-0.5">Final Settlement</span>
                                                <div class="fw-bold text-dark" style="font-size: 0.80rem;">
                                                    {{ $repair->finalAccount ? $repair->finalAccount->title : ($repair->status === 'delivered' ? 'Cash Settlement' : 'Pending Delivery') }}
                                                </div>
                                            </div>
                                            <div class="text-end font-monospace fw-bold text-primary" style="font-size: 0.92rem;">
                                                Rs. {{ number_format($repair->final_paid, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="doc-totals-card">
                                    <div class="doc-total-row">
                                        <span class="text-muted">Estimated Quote:</span>
                                        <span class="font-monospace fw-bold">Rs. {{ number_format($repair->estimated_cost, 2) }}</span>
                                    </div>
                                    <div class="doc-total-row">
                                        <span class="text-muted">Service Charges:</span>
                                        <span class="font-monospace fw-bold">Rs. {{ number_format($repair->service_charges, 2) }}</span>
                                    </div>
                                    <div class="doc-total-row">
                                        <span class="text-muted">Spare Parts Cost:</span>
                                        <span class="font-monospace fw-bold">Rs. {{ number_format($repair->parts_charges, 2) }}</span>
                                    </div>
                                    <div class="doc-total-row border-top pt-1 mt-1">
                                        <span class="fw-bold text-dark">Total Bill Amount:</span>
                                        <span class="font-monospace fw-bold text-dark fs-6">
                                            Rs. {{ number_format($repair->total_charges > 0 ? $repair->total_charges : $repair->estimated_cost, 2) }}
                                        </span>
                                    </div>
                                    <div class="doc-total-row text-success">
                                        <span>Less: Advance Paid:</span>
                                        <span class="font-monospace fw-bold">-Rs. {{ number_format($repair->advance_paid, 2) }}</span>
                                    </div>
                                    <hr class="my-1.5">
                                    <div class="doc-total-row fw-bold" style="font-size: 0.95rem;">
                                        <span class="{{ $repair->due_amount > 0 ? 'text-danger' : 'text-success' }}">Net Balance Due:</span>
                                        <span class="font-monospace fs-5 {{ $repair->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                            Rs. {{ number_format($repair->due_amount, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- AUDIT TRAIL LOGS CARD --}}
                <div class="erp-doc-card">
                    <div class="card-panel-header bg-light">
                        <h6 class="card-panel-title">
                            <i class="fas fa-history text-secondary"></i> Complete Audit History &amp; Log
                        </h6>
                        <span class="badge rounded-pill bg-white text-muted border font-monospace" style="font-size: 0.72rem;">
                            {{ $repair->logs->count() }} Actions Recorded
                        </span>
                    </div>
                    <div class="card-panel-body p-0">
                        <div class="table-responsive" id="auditLogsWrapper" style="max-height: 220px; overflow: hidden; transition: max-height 0.35s ease;">
                            <table class="table doc-table mb-0" style="border: none;">
                                <thead>
                                    <tr>
                                        <th>Time &amp; User</th>
                                        <th>Action</th>
                                        <th>Ledger / Account</th>
                                        <th>Notes &amp; Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($repair->logs as $log)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark" style="font-size: 0.76rem;">{{ $log->user->name ?? 'System' }}</div>
                                                <div class="text-muted" style="font-size: 0.68rem;">{{ $log->created_at->format('d-M-Y h:i A') }}</div>
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
                                                    <div class="fw-semibold text-dark" style="font-size: 0.74rem;">{{ $log->account->title }}</div>
                                                    <small class="text-muted" style="font-size: 0.66rem;">{{ $log->account->account_code }}</small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td style="font-size: 0.75rem; color: #475569;">
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
                        @if($repair->logs->count() > 2)
                            <div class="text-center py-2 px-3 bg-light border-top">
                                <button type="button" class="btn btn-link btn-sm text-decoration-none fw-bold p-0 text-primary d-inline-flex align-items-center gap-1" id="btnToggleAuditLogs" style="font-size: 0.76rem;">
                                    <i class="fas fa-chevron-down" id="iconAuditToggle"></i> <span id="textAuditToggle">View More Actions ({{ $repair->logs->count() }})</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL 1: UPDATE REPAIR STATUS --}}
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <form action="{{ route('repair.status.update', $repair->id) }}" method="POST">
                    @csrf
                    <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        <div>
                            <h6 class="modal-title font-weight-bold mb-0 text-white" id="statusModalLabel" style="font-size: 0.95rem;">
                                <i class="fas fa-tasks text-warning me-2"></i> Update Repair Status
                            </h6>
                            <small class="text-white-50" style="font-size: 0.72rem;">
                                Ticket #<span class="font-monospace text-white fw-bold">{{ $repair->repair_no }}</span>
                            </small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" style="background-color: #f8fafc;">
                        
                        <div class="alert alert-primary border-primary-subtle py-2 px-3 mb-3 rounded-3 small" style="background-color: #eff6ff; font-size: 0.75rem;">
                            <div class="fw-bold text-primary mb-0.5"><i class="fas fa-laptop me-1"></i> {{ $repair->item_name }}</div>
                            <div class="text-secondary"><strong>Customer:</strong> {{ $repair->customer_display_name }} ({{ $repair->customer_display_phone }})</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Select New Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select form-select-sm border-primary-subtle" style="border-radius: 8px; font-weight: 600;" required>
                                <option value="received" {{ $repair->status === 'received' ? 'selected' : '' }}>Received (Intake)</option>
                                <option value="in_progress" {{ $repair->status === 'in_progress' ? 'selected' : '' }}>In Progress / Repairing</option>
                                <option value="completed" {{ $repair->status === 'completed' ? 'selected' : '' }}>Repaired &amp; Ready</option>
                                <option value="cancelled" {{ $repair->status === 'cancelled' ? 'selected' : '' }}>Cancelled / Returned</option>
                            </select>
                        </div>

                        <div class="mb-1">
                            <label class="form-label small fw-bold text-dark mb-1">
                                <i class="fas fa-stethoscope text-primary me-1"></i>Technician Diagnostic Notes
                            </label>
                            <textarea name="technician_notes" class="form-control form-control-sm border-primary-subtle" rows="3" placeholder="e.g. Main power supply capacitor replaced, circuit re-soldered, load tested under 220V..." style="border-radius: 8px; font-size: 0.78rem;">{{ $repair->technician_notes }}</textarea>
                        </div>

                    </div>
                    <div class="modal-footer py-2.5 px-4 bg-light border-top d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 6px; font-size: 0.78rem;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm" style="border-radius: 6px; font-size: 0.78rem;">
                            <i class="fas fa-save me-1"></i> Save Status Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: DELIVER PRODUCT --}}
    <div class="modal fade" id="deliverModal" tabindex="-1" aria-labelledby="deliverModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <form action="{{ route('repair.deliver', $repair->id) }}" method="POST" id="modalDeliverForm">
                    @csrf
                    <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #047857 0%, #059669 100%);">
                        <div>
                            <h6 class="modal-title font-weight-bold mb-0 text-white" id="deliverModalLabel" style="font-size: 0.95rem;">
                                <i class="fas fa-truck me-2"></i> Deliver Product
                            </h6>
                            <small class="text-white-50" style="font-size: 0.72rem;">
                                Ticket #<span class="font-monospace text-white fw-bold">{{ $repair->repair_no }}</span>
                            </small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" style="background-color: #f8fafc;">
                        
                        <div class="alert alert-success border-success-subtle py-2 px-3 mb-3 rounded-3 small" style="background-color: #ecfdf5; font-size: 0.75rem;">
                            <div class="fw-bold text-success mb-0.5"><i class="fas fa-check-circle me-1"></i> {{ $repair->item_name }}</div>
                            <div class="text-secondary"><strong>Customer:</strong> {{ $repair->customer_display_name }} ({{ $repair->customer_display_phone }})</div>
                            @if(($repair->advance_paid ?? 0) > 0)
                            <div class="text-dark font-monospace fw-bold mt-1">
                                Advance Paid: <span class="text-success" id="modalAdvDisp">Rs. {{ number_format($repair->advance_paid, 2) }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Delivery Date <span class="text-danger">*</span></label>
                            <input type="date" name="delivery_date" class="form-control form-control-sm font-monospace fw-bold" value="{{ date('Y-m-d') }}" style="border-radius: 8px; height: 36px;" required>
                        </div>

                        <div class="mb-1">
                            <label class="form-label small fw-bold text-dark mb-1">Delivery Remarks</label>
                            <input type="text" name="delivery_notes" class="form-control form-control-sm" placeholder="e.g. Tested in front of customer, handed over safely" style="border-radius: 8px; font-size: 0.78rem;">
                        </div>
                    </div>
                    <div class="modal-footer py-2.5 px-4 bg-light border-top d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 6px; font-size: 0.78rem;">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success px-4 fw-bold shadow-sm" style="border-radius: 6px; font-size: 0.78rem;">
                            <i class="fas fa-check-circle me-1"></i> Complete Delivery &amp; Save
                        </button>
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

            // Toggle View More Audit Logs
            const btnToggleAuditLogs = document.getElementById('btnToggleAuditLogs');
            const auditLogsWrapper = document.getElementById('auditLogsWrapper');
            const iconAuditToggle = document.getElementById('iconAuditToggle');
            const textAuditToggle = document.getElementById('textAuditToggle');

            if (btnToggleAuditLogs && auditLogsWrapper) {
                let isExpanded = false;
                btnToggleAuditLogs.addEventListener('click', function() {
                    isExpanded = !isExpanded;
                    if (isExpanded) {
                        auditLogsWrapper.style.maxHeight = '1200px';
                        iconAuditToggle.className = 'fas fa-chevron-up';
                        textAuditToggle.innerText = 'Show Less';
                    } else {
                        auditLogsWrapper.style.maxHeight = '220px';
                        iconAuditToggle.className = 'fas fa-chevron-down';
                        textAuditToggle.innerText = 'View More Actions ({{ $repair->logs->count() }})';
                    }
                });
            }
        });
    </script>
@endsection
