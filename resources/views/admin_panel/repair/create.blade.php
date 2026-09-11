@extends('admin_panel.layout.app')

@section('content')
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Repairing Intake & Job Card Creation
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

        .form-label {
            font-size: 0.76rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.3rem;
            display: block;
        }
        .form-label.required::after {
            content: " *";
            color: #dc2626;
        }
        .form-control, .form-select {
            font-size: 0.82rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.45rem 0.65rem;
            color: #0f172a;
            transition: all 0.15s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
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
        .account-balance-badge.bal-positive {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }
        .account-balance-badge.bal-negative {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }
        .account-balance-badge.bal-zero {
            background: #f8fafc;
            color: #64748b;
            border-color: #cbd5e1;
        }

        /* Priority Options */
        .priority-group {
            display: flex;
            gap: 8px;
        }
        .priority-card {
            flex: 1;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }
        .priority-card:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }
        .priority-card.active-normal {
            border-color: #3b82f6;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 700;
        }
        .priority-card.active-high {
            border-color: #f59e0b;
            background: #fffbeb;
            color: #b45309;
            font-weight: 700;
        }
        .priority-card.active-urgent {
            border-color: #ef4444;
            background: #fef2f2;
            color: #b91c1c;
            font-weight: 700;
        }

        /* Accessory Pills Checkboxes */
        .accessory-pill-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .accessory-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            font-size: 0.74rem;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }
        .accessory-pill input[type="checkbox"] {
            margin: 0;
            accent-color: #2563eb;
            cursor: pointer;
        }
        .accessory-pill:has(input:checked) {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }

        /* Summary Settlement Strip */
        .summary-tile {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 10px;
        }
        .summary-tile-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.80rem;
            margin-bottom: 6px;
        }
        .summary-tile-row:last-child {
            margin-bottom: 0;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Page Header --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary px-2.5 py-1 font-monospace" style="font-size: 0.75rem;">
                                {{ $nextRepairNo }}
                            </span>
                            <h4 class="erp-title mb-0">
                                <i class="fas fa-tools text-primary"></i> Receive Product for Repair
                            </h4>
                        </div>
                        <p class="erp-subtitle">Create customer repair job card, record faults, accessories, advance payment and issue intake receipt</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('repair.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" style="font-weight: 600; border-radius: 7px; padding: 6px 14px;">
                            <i class="fas fa-arrow-left"></i> Back to Repair List
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
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Please fix the following errors:</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.82rem;">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('repair.store') }}" method="POST" id="repairIntakeForm">
                    @csrf

                    <div class="row g-3">
                        {{-- LEFT COLUMN: Customer + Device + Fault Details (col-lg-8) --}}
                        <div class="col-lg-8">

                            {{-- 1. Customer Details Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-user-circle text-primary"></i> Customer Information
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check" name="cust_type" id="custTypeRegistered" value="registered" checked autocomplete="off">
                                            <label class="btn btn-outline-primary btn-sm py-1 px-2" for="custTypeRegistered" style="font-size: 0.72rem; font-weight: 600;">Registered Customer</label>

                                            <input type="radio" class="btn-check" name="cust_type" id="custTypeWalkin" value="walkin" autocomplete="off">
                                            <label class="btn btn-outline-primary btn-sm py-1 px-2" for="custTypeWalkin" style="font-size: 0.72rem; font-weight: 600;">Walk-in / New</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-panel-body">
                                    {{-- Registered Customer Select --}}
                                    <div class="mb-3" id="registeredCustGroup">
                                        <label class="form-label">Select Registered Customer</label>
                                        <select name="customer_id" id="customerSelect" class="form-select w-100">
                                            <option value="">-- Choose Existing Customer (Search by Name / Mobile) --</option>
                                            @foreach ($customers as $c)
                                                <option value="{{ $c->id }}"
                                                    data-name="{{ $c->customer_name }}"
                                                    data-phone="{{ $c->mobile ?? $c->mobile_2 }}"
                                                    data-address="{{ $c->address }}"
                                                    data-balance="{{ $c->previous_balance ?? 0 }}"
                                                    {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->customer_name }} ({{ $c->mobile ?? 'No phone' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="custBalanceBadgeWrap" class="mt-1" style="display: none;">
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="fas fa-balance-scale text-primary me-1"></i> Existing Ledger Balance:
                                                <span id="custBalanceVal" class="font-monospace fw-bold">0.00</span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <label class="form-label required">Customer / Company Name</label>
                                            <input type="text" name="customer_name" id="customerName" class="form-control"
                                                value="{{ old('customer_name') }}" placeholder="e.g. Tariq Textiles / Ahmed Khan" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label required">Mobile / WhatsApp #</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted" style="font-size: 0.75rem;"><i class="fas fa-phone"></i></span>
                                                <input type="text" name="customer_phone" id="customerPhone" class="form-control"
                                                    value="{{ old('customer_phone') }}" placeholder="0300-1234567" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">City / Address</label>
                                            <input type="text" name="customer_address" id="customerAddress" class="form-control"
                                                value="{{ old('customer_address') }}" placeholder="e.g. Unit 4, SITE Hyderabad">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Device & Product Identification Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-microchip text-primary"></i> Product / Device Specifications
                                    </h6>
                                    @if(count($products) > 0)
                                    <div class="d-flex align-items-center gap-1">
                                        <small class="text-muted" style="font-size: 0.72rem;">Quick Fill:</small>
                                        <select id="quickProductSelect" class="form-select form-select-sm" style="max-width: 220px; font-size: 0.72rem; padding: 2px 8px;">
                                            <option value="">-- Finished Good Catalog --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-name="{{ $p->item_name }}">{{ $p->item_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                                <div class="card-panel-body">
                                    <input type="hidden" name="product_id" id="productId" value="{{ old('product_id') }}">

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label required">Device / Item Name</label>
                                            <input type="text" name="item_name" id="itemName" class="form-control"
                                                value="{{ old('item_name') }}" placeholder="e.g. Industrial Heating Device 5KW / Chiller Unit" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Brand / Model</label>
                                            <input type="text" name="brand_model" id="brandModel" class="form-control"
                                                value="{{ old('brand_model') }}" placeholder="e.g. LogicTech PW-500">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Serial / Machine #</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted" style="font-size: 0.75rem;"><i class="fas fa-barcode"></i></span>
                                                <input type="text" name="serial_no" id="serialNo" class="form-control font-monospace"
                                                    value="{{ old('serial_no') }}" placeholder="SN-84920">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Physical Condition --}}
                                    <div class="mb-3">
                                        <label class="form-label">Physical Condition & Visual Inspection</label>
                                        <input type="text" name="physical_condition" id="physicalCondition" class="form-control"
                                            value="{{ old('physical_condition') }}" placeholder="e.g. Outer casing scratched, power cord clipped, front knob missing, warranty seal broken">
                                        <div class="d-flex gap-1 mt-1 flex-wrap">
                                            <span class="badge bg-light text-muted border quick-condition-tag" style="cursor: pointer; font-size: 0.68rem;" data-tag="Intact & Clean">Intact & Clean</span>
                                            <span class="badge bg-light text-muted border quick-condition-tag" style="cursor: pointer; font-size: 0.68rem;" data-tag="Scratches & Minor Dents">Scratches & Dents</span>
                                            <span class="badge bg-light text-muted border quick-condition-tag" style="cursor: pointer; font-size: 0.68rem;" data-tag="Burn Marks on Terminals">Burn Marks</span>
                                            <span class="badge bg-light text-muted border quick-condition-tag" style="cursor: pointer; font-size: 0.68rem;" data-tag="Missing Screws / Cover">Missing Screws</span>
                                            <span class="badge bg-light text-muted border quick-condition-tag" style="cursor: pointer; font-size: 0.68rem;" data-tag="Water / Chemical Splash Marks">Water Marks</span>
                                        </div>
                                    </div>

                                    {{-- Accessories Received --}}
                                    <div>
                                        <label class="form-label">Accessories Received With Item</label>
                                        <div class="accessory-pill-wrap mb-2">
                                            <label class="accessory-pill">
                                                <input type="checkbox" name="accessories_received[]" value="Power Cable"> Power Cable
                                            </label>
                                            <label class="accessory-pill">
                                                <input type="checkbox" name="accessories_received[]" value="Power Supply / Adapter"> Power Adapter
                                            </label>
                                            <label class="accessory-pill">
                                                <input type="checkbox" name="accessories_received[]" value="Digital Remote / Controller"> Remote / Panel
                                            </label>
                                            <label class="accessory-pill">
                                                <input type="checkbox" name="accessories_received[]" value="Temperature Sensor Probe"> Sensor Probe
                                            </label>
                                            <label class="accessory-pill">
                                                <input type="checkbox" name="accessories_received[]" value="Mounting Brackets"> Mounting Brackets
                                            </label>
                                            <label class="accessory-pill">
                                                <input type="checkbox" name="accessories_received[]" value="Original Box / Packaging"> Original Box
                                            </label>
                                        </div>
                                        <input type="text" name="accessories_received[]" class="form-control"
                                            placeholder="Other accessories (e.g. Connecting pipes, manual, extra fuses)">
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Fault & Problem Reported Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-exclamation-circle text-danger"></i> Fault & Diagnostic Notes
                                    </h6>
                                </div>
                                <div class="card-panel-body">
                                    <div class="mb-3">
                                        <label class="form-label required">Problem / Fault Reported by Customer</label>
                                        <textarea name="problem_description" id="problemDescription" rows="3" class="form-control"
                                            placeholder="Describe defect clearly, e.g. Coil not heating up, tripping breaker on 220V, display shows error E-02 after 5 mins..." required>{{ old('problem_description') }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label">Technician Initial Remarks / Internal Notes (Optional)</label>
                                        <textarea name="technician_notes" id="technicianNotes" rows="2" class="form-control"
                                            placeholder="Internal inspection notes (visible to workshop staff only)">{{ old('technician_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- RIGHT COLUMN: Schedule, Advance, Accounts & Actions (col-lg-4) --}}
                        <div class="col-lg-4">

                            {{-- 4. Dates & Priority Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-calendar-alt text-primary"></i> Schedule & Priority
                                    </h6>
                                </div>
                                <div class="card-panel-body">
                                    <div class="mb-3">
                                        <label class="form-label required">Received Date</label>
                                        <input type="date" name="received_date" class="form-control"
                                            value="{{ old('received_date', date('Y-m-d')) }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Promised / Expected Delivery Date</label>
                                        <input type="date" name="expected_delivery_date" class="form-control"
                                            value="{{ old('expected_delivery_date', date('Y-m-d', strtotime('+3 days'))) }}">
                                    </div>

                                    <div>
                                        <label class="form-label required">Priority Level</label>
                                        <input type="hidden" name="priority" id="priorityInput" value="{{ old('priority', 'normal') }}">
                                        <div class="priority-group">
                                            <div class="priority-card active-normal" data-val="normal">
                                                <i class="fas fa-clock d-block mb-1"></i>
                                                <small>Normal</small>
                                            </div>
                                            <div class="priority-card" data-val="high">
                                                <i class="fas fa-bolt d-block mb-1"></i>
                                                <small>High</small>
                                            </div>
                                            <div class="priority-card" data-val="urgent">
                                                <i class="fas fa-fire d-block mb-1"></i>
                                                <small>Urgent</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 5. Cost Estimation & Advance Payment Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-wallet text-success"></i> Estimation & Advance Payment
                                    </h6>
                                </div>
                                <div class="card-panel-body">
                                    {{-- Estimated Cost --}}
                                    <div class="mb-3">
                                        <label class="form-label">Estimated Repair Cost (Rs.)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted fw-bold" style="font-size: 0.75rem;">PKR</span>
                                            <input type="number" step="0.01" min="0" name="estimated_cost" id="estimatedCost"
                                                class="form-control text-end fw-bold font-monospace" value="{{ old('estimated_cost', '0') }}">
                                        </div>
                                        <small class="text-muted" style="font-size: 0.68rem;">Tentative quote provided to customer</small>
                                    </div>

                                    {{-- Advance Payment --}}
                                    <div class="mb-3">
                                        <label class="form-label">Advance Payment Collected (Rs.)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-subtle text-success fw-bold" style="font-size: 0.75rem;">PKR</span>
                                            <input type="number" step="0.01" min="0" name="advance_paid" id="advancePaid"
                                                class="form-control text-end fw-bold font-monospace text-success" value="{{ old('advance_paid', '0') }}">
                                        </div>
                                    </div>

                                    {{-- Advance Account Selection with LIVE BALANCE BADGE --}}
                                    <div class="mb-3" id="advanceAccountGroup">
                                        <label class="form-label">Deposit In Account (Cash / Bank)</label>
                                        <select name="advance_account_id" id="advanceAccountSelect" class="form-select">
                                            <option value="" selected>-- Select Account --</option>
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}" data-balance="{{ $acc->current_balance ?? 0 }}"
                                                    {{ old('advance_account_id') == $acc->id ? 'selected' : '' }}>
                                                    {{ $acc->title }} ({{ $acc->account_code }})
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- Dynamic Account Balance Badge --}}
                                        <div class="account-balance-badge-wrap" id="accBalWrap" style="display: none;">
                                            <span class="account-balance-badge" id="accBalBadge">
                                                <i class="fas fa-wallet"></i> Current Bal: Rs. <span id="accBalVal">0.00</span> <span id="accBalType">DR</span>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Live Financial Settlement Breakdown --}}
                                    <div class="summary-tile">
                                        <div class="summary-tile-row text-muted">
                                            <span>Estimated Total:</span>
                                            <span class="font-monospace fw-semibold" id="dispEstTotal">Rs. 0.00</span>
                                        </div>
                                        <div class="summary-tile-row text-success">
                                            <span>Advance Received:</span>
                                            <span class="font-monospace fw-bold" id="dispAdvance">Rs. 0.00</span>
                                        </div>
                                        <hr class="my-1" style="border-color: #cbd5e1;">
                                        <div class="summary-tile-row text-danger fw-bold" style="font-size: 0.86rem;">
                                            <span>Estimated Due at Delivery:</span>
                                            <span class="font-monospace" id="dispDue">Rs. 0.00</span>
                                        </div>
                                    </div>

                                    <div class="alert alert-info py-2 px-2.5 mb-0" style="font-size: 0.70rem;">
                                        <i class="fas fa-info-circle me-1"></i> If advance is collected, a double-entry Receipt Voucher will be created and posted into your ledger automatically.
                                    </div>
                                </div>
                            </div>

                            {{-- 6. Form Submission Buttons --}}
                            <div class="card-panel p-3">
                                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" style="border-radius: 8px; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);">
                                    <i class="fas fa-save"></i> Receive Device &amp; Generate Job Card
                                </button>
                                <a href="{{ route('repair.index') }}" class="btn btn-outline-secondary w-100 mt-2 py-1.5" style="border-radius: 8px; font-size: 0.80rem;">
                                    Cancel
                                </a>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Initialize Select2 on Customer dropdown
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#customerSelect').select2({
                    placeholder: "-- Search Customer by Name or Mobile --",
                    allowClear: true,
                    width: '100%'
                });
            }

            // Customer Type Toggle (Registered vs Walkin)
            const custTypeRegistered = document.getElementById('custTypeRegistered');
            const custTypeWalkin = document.getElementById('custTypeWalkin');
            const registeredCustGroup = document.getElementById('registeredCustGroup');
            const customerName = document.getElementById('customerName');
            const customerPhone = document.getElementById('customerPhone');
            const customerAddress = document.getElementById('customerAddress');
            const custBalanceBadgeWrap = document.getElementById('custBalanceBadgeWrap');
            const custBalanceVal = document.getElementById('custBalanceVal');

            function handleCustTypeChange() {
                if (custTypeWalkin.checked) {
                    registeredCustGroup.style.display = 'none';
                    if (typeof $ !== 'undefined' && $('#customerSelect').length) {
                        $('#customerSelect').val('').trigger('change');
                    }
                    customerName.value = '';
                    customerPhone.value = '';
                    customerAddress.value = '';
                    custBalanceBadgeWrap.style.display = 'none';
                } else {
                    registeredCustGroup.style.display = 'block';
                }
            }
            custTypeRegistered.addEventListener('change', handleCustTypeChange);
            custTypeWalkin.addEventListener('change', handleCustTypeChange);

            // Handle Customer Select Change
            $('#customerSelect').on('change', function() {
                const selected = $(this).find(':selected');
                const name = selected.data('name');
                const phone = selected.data('phone');
                const address = selected.data('address');
                const balance = parseFloat(selected.data('balance') || 0);

                if (name) {
                    customerName.value = name;
                    customerPhone.value = phone || '';
                    customerAddress.value = address || '';

                    custBalanceBadgeWrap.style.display = 'block';
                    custBalanceVal.innerText = 'Rs. ' + balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    if (balance > 0) {
                        custBalanceVal.className = 'font-monospace fw-bold text-danger';
                    } else if (balance < 0) {
                        custBalanceVal.className = 'font-monospace fw-bold text-success';
                    } else {
                        custBalanceVal.className = 'font-monospace fw-bold text-muted';
                    }
                } else {
                    custBalanceBadgeWrap.style.display = 'none';
                }
            });

            // Quick Finished Goods Select
            const quickProductSelect = document.getElementById('quickProductSelect');
            if (quickProductSelect) {
                quickProductSelect.addEventListener('change', function() {
                    const sel = this.options[this.selectedIndex];
                    const name = sel.getAttribute('data-name');
                    const id = sel.value;
                    if (name) {
                        document.getElementById('itemName').value = name;
                        document.getElementById('productId').value = id;
                    }
                });
            }

            // Quick Condition Tags
            document.querySelectorAll('.quick-condition-tag').forEach(tag => {
                tag.addEventListener('click', function() {
                    const tagText = this.getAttribute('data-tag');
                    const input = document.getElementById('physicalCondition');
                    if (input.value.trim() === '') {
                        input.value = tagText;
                    } else if (!input.value.includes(tagText)) {
                        input.value += ', ' + tagText;
                    }
                });
            });

            // Priority Cards
            const priorityInput = document.getElementById('priorityInput');
            const priorityCards = document.querySelectorAll('.priority-card');
            priorityCards.forEach(card => {
                card.addEventListener('click', function() {
                    const val = this.getAttribute('data-val');
                    priorityInput.value = val;
                    priorityCards.forEach(c => {
                        c.classList.remove('active-normal', 'active-high', 'active-urgent');
                    });
                    this.classList.add('active-' + val);
                });
            });

            // Live Account Balance Badge Logic
            const advanceAccountSelect = document.getElementById('advanceAccountSelect');
            const accBalWrap = document.getElementById('accBalWrap');
            const accBalBadge = document.getElementById('accBalBadge');
            const accBalVal = document.getElementById('accBalVal');
            const accBalType = document.getElementById('accBalType');

            function updateAccountBalanceBadge() {
                const selected = advanceAccountSelect.options[advanceAccountSelect.selectedIndex];
                if (!selected || !selected.value) {
                    accBalWrap.style.display = 'none';
                    return;
                }

                const bal = parseFloat(selected.getAttribute('data-balance') || 0);
                accBalVal.innerText = Math.abs(bal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                accBalType.innerText = bal >= 0 ? 'DR' : 'CR';

                accBalBadge.className = 'account-balance-badge';
                if (bal > 0) {
                    accBalBadge.classList.add('bal-positive');
                } else if (bal < 0) {
                    accBalBadge.classList.add('bal-negative');
                } else {
                    accBalBadge.classList.add('bal-zero');
                }
                accBalWrap.style.display = 'inline-block';
            }
            advanceAccountSelect.addEventListener('change', updateAccountBalanceBadge);
            updateAccountBalanceBadge(); // Run once on load if pre-selected

            // Cost & Advance Calculation
            const estimatedCostInput = document.getElementById('estimatedCost');
            const advancePaidInput = document.getElementById('advancePaid');
            const dispEstTotal = document.getElementById('dispEstTotal');
            const dispAdvance = document.getElementById('dispAdvance');
            const dispDue = document.getElementById('dispDue');

            function calculateSettlement() {
                const est = parseFloat(estimatedCostInput.value) || 0;
                const adv = parseFloat(advancePaidInput.value) || 0;
                const due = Math.max(0, est - adv);

                dispEstTotal.innerText = 'Rs. ' + est.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                dispAdvance.innerText = 'Rs. ' + adv.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                dispDue.innerText = 'Rs. ' + due.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                // Enforce account selection if advance > 0
                if (adv > 0) {
                    advanceAccountSelect.setAttribute('required', 'required');
                } else {
                    advanceAccountSelect.removeAttribute('required');
                }
            }

            estimatedCostInput.addEventListener('input', calculateSettlement);
            advancePaidInput.addEventListener('input', calculateSettlement);
            calculateSettlement();

            // Form Validation before submit
            document.getElementById('repairIntakeForm').addEventListener('submit', function(e) {
                const adv = parseFloat(advancePaidInput.value) || 0;
                if (adv > 0 && !advanceAccountSelect.value) {
                    e.preventDefault();
                    alert('Advance payment received hai! Meherbani farma kar deposit account select karein.');
                    advanceAccountSelect.focus();
                }
            });

        });
    </script>
@endsection
