@extends('admin_panel.layout.app')

@section('content')
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        /* ==========================================================================
           Enterprise ERP Design System - Repairing Job Card Edit
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
            font-size: 0.835rem;
            color: #64748b;
            margin-top: 0.35rem;
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
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid px-3 px-md-4 py-3">

                {{-- Page Header --}}
                <div class="erp-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h4 class="erp-title">
                            <i class="fas fa-edit text-primary"></i> Edit Repair Job #<span class="font-monospace text-primary">{{ $repair->repair_no }}</span>
                        </h4>
                        <p class="erp-subtitle">Modify repair job intake specifications, reported defect, schedule, and customer details</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('repair.show', $repair->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 px-3" style="border-radius: 7px; height: 34px; font-size: 0.76rem;">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('repair.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3" style="border-radius: 7px; height: 34px; font-size: 0.76rem;">
                            <i class="fas fa-arrow-left"></i> Back to Jobs
                        </a>
                    </div>
                </div>

                {{-- Flash Notifications --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                        <i class="fas fa-check-circle fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Please correct the following errors:</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @php
                    $itemsData = $itemsData ?? [];
                    $reportedDefect = $reportedDefect ?? ($repair->reported_defect ?? '');
                    $firstItem = $itemsData[0] ?? [];
                    $itemNameVal = old('item_name', $firstItem['item_name'] ?? $repair->item_name);
                    $brandVal    = old('brand_model', $firstItem['brand_model'] ?? $repair->brand_model);
                    $serialVal   = old('serial_no', $firstItem['serial_no'] ?? $repair->serial_no);
                    $defectVal   = old('problem_description', ($reportedDefect !== '—' && $reportedDefect !== '[]') ? $reportedDefect : ($firstItem['problem_description'] ?? ''));
                    $costVal     = old('estimated_cost', $firstItem['estimated_cost'] ?? $repair->estimated_cost);
                @endphp

                <form action="{{ route('repair.update', $repair->id) }}" method="POST" id="editRepairForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- LEFT COLUMN: Customer + Device + Fault Details (col-lg-8) --}}
                        <div class="col-lg-8">

                            {{-- 1. Customer Details Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-user-circle text-primary"></i> Customer Information
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('customers.create') }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2.5 fw-bold d-inline-flex align-items-center gap-1" style="font-size: 0.74rem; border-radius: 6px;" title="Open Create Customer form in new tab">
                                            <i class="fas fa-user-plus"></i> + Add Customer
                                        </a>
                                    </div>
                                </div>
                                <div class="card-panel-body">
                                    <div class="row g-2">
                                        {{-- 1. Select Registered Customer --}}
                                        <div class="col-md-3">
                                            <label class="form-label">Select Registered Customer</label>
                                            <select name="customer_id" id="customerSelect" class="form-select w-100">
                                                <option value="">-- Choose Existing Customer --</option>
                                                @foreach ($customers as $c)
                                                    <option value="{{ $c->id }}"
                                                        data-name="{{ $c->customer_name }}"
                                                        data-phone="{{ $c->mobile ?? $c->mobile_2 }}"
                                                        data-address="{{ $c->address }}"
                                                        data-balance="{{ $c->previous_balance ?? 0 }}"
                                                        {{ old('customer_id', $repair->customer_id) == $c->id ? 'selected' : '' }}>
                                                        {{ $c->customer_name }} ({{ $c->mobile ?? 'No phone' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="custBalanceBadgeWrap" class="mt-1" style="{{ $repair->customer_id ? '' : 'display: none;' }}">
                                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.70rem;">
                                                    <i class="fas fa-balance-scale text-primary me-1"></i> Balance:
                                                    <span id="custBalanceVal" class="font-monospace fw-bold">0.00</span>
                                                </span>
                                            </div>
                                        </div>

                                        {{-- 2. Customer Name --}}
                                        <div class="col-md-3">
                                            <label class="form-label required">Customer / Company Name</label>
                                            <input type="text" name="customer_name" id="customerName" class="form-control"
                                                value="{{ old('customer_name', $repair->customer_display_name) }}" placeholder="e.g. Tariq Textiles / Ahmed Khan" required>
                                        </div>

                                        {{-- 3. Mobile / WhatsApp --}}
                                        <div class="col-md-3">
                                            <label class="form-label required">Mobile / WhatsApp #</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted" style="font-size: 0.75rem;"><i class="fas fa-phone"></i></span>
                                                <input type="text" name="customer_phone" id="customerPhone" class="form-control"
                                                    value="{{ old('customer_phone', $repair->customer_display_phone) }}" placeholder="0300-1234567" required>
                                            </div>
                                        </div>

                                        {{-- 4. City / Address --}}
                                        <div class="col-md-3">
                                            <label class="form-label">City / Address</label>
                                            <input type="text" name="customer_address" id="customerAddress" class="form-control"
                                                value="{{ old('customer_address', $repair->customer_display_address) }}" placeholder="e.g. Unit 4, SITE Hyderabad">
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
                                                <option value="{{ $p->id }}" data-name="{{ $p->item_name }}" {{ old('product_id', $repair->product_id) == $p->id ? 'selected' : '' }}>{{ $p->item_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                                <div class="card-panel-body">
                                    <input type="hidden" name="product_id" id="productId" value="{{ old('product_id', $repair->product_id) }}">

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label required">Device / Item Name</label>
                                            <input type="text" name="item_name" id="itemName" class="form-control"
                                                value="{{ $itemNameVal }}" placeholder="e.g. Industrial Heating Device 5KW / Chiller Unit" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Brand / Model</label>
                                            <input type="text" name="brand_model" id="brandModel" class="form-control"
                                                value="{{ $brandVal }}" placeholder="e.g. LogicTech PW-500">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Serial / Machine #</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted" style="font-size: 0.75rem;"><i class="fas fa-barcode"></i></span>
                                                <input type="text" name="serial_no" id="serialNo" class="form-control font-monospace"
                                                    value="{{ $serialVal }}" placeholder="SN-84920">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-12">
                                            <label class="form-label required">
                                                <i class="fas fa-wrench text-danger me-1"></i> Reported Fault / Problem Description
                                            </label>
                                            <textarea name="problem_description" id="problemDescription" rows="3" class="form-control"
                                                placeholder="Enter customer reported fault / defect description..." required>{{ $defectVal }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label">
                                                <i class="fas fa-plug text-primary me-1"></i> Accessories Received With Unit
                                            </label>
                                            <input type="text" name="accessories_received" id="accessoriesReceived" class="form-control"
                                                value="{{ old('accessories_received', $repair->accessories_received) }}" placeholder="e.g. Power Cable, Induction Coil, Foot Switch, etc.">
                                            <small class="text-muted" style="font-size: 0.68rem;">Visible on Job Sheet, A4 Invoice, and Register</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">
                                                <i class="fas fa-eye text-secondary me-1"></i> Physical Condition / Appearance
                                            </label>
                                            <input type="text" name="physical_condition" id="physicalCondition" class="form-control"
                                                value="{{ old('physical_condition', $repair->physical_condition) }}" placeholder="e.g. Intact casing, minor scratches on front panel">
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-2">
                                        <div class="col-12">
                                            <label class="form-label">
                                                <i class="fas fa-clipboard-check text-info me-1"></i> Workshop Diagnosis / Technician Notes
                                            </label>
                                            <textarea name="technician_notes" id="technicianNotes" rows="2" class="form-control"
                                                placeholder="Internal workshop inspection notes, testing details...">{{ old('technician_notes', $repair->technician_notes) }}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        {{-- RIGHT COLUMN: Schedule, Priority & Financials (col-lg-4) --}}
                        <div class="col-lg-4">

                            {{-- 3. Dates, Priority & Status Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-calendar-alt text-primary"></i> Schedule &amp; Priority
                                    </h6>
                                    <span class="badge {{ $repair->status_badge_class }}" style="font-size: 0.68rem;">
                                        {{ $repair->status_label }}
                                    </span>
                                </div>
                                <div class="card-panel-body">
                                    <div class="mb-3">
                                        <label class="form-label required">Received Date</label>
                                        <input type="date" name="received_date" class="form-control"
                                            value="{{ old('received_date', $repair->received_date ? $repair->received_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Promised / Expected Delivery Date</label>
                                        <input type="date" name="expected_delivery_date" class="form-control"
                                            value="{{ old('expected_delivery_date', $repair->expected_delivery_date ? $repair->expected_delivery_date->format('Y-m-d') : '') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Priority Level</label>
                                        @php $currentPriority = old('priority', $repair->priority ?? 'normal'); @endphp
                                        <input type="hidden" name="priority" id="priorityInput" value="{{ $currentPriority }}">
                                        <div class="priority-group">
                                            <div class="priority-card {{ $currentPriority === 'normal' ? 'active-normal' : '' }}" data-val="normal">
                                                <i class="fas fa-clock d-block mb-1"></i>
                                                <small>Normal</small>
                                            </div>
                                            <div class="priority-card {{ $currentPriority === 'high' ? 'active-high' : '' }}" data-val="high">
                                                <i class="fas fa-bolt d-block mb-1"></i>
                                                <small>High</small>
                                            </div>
                                            <div class="priority-card {{ $currentPriority === 'urgent' ? 'active-urgent' : '' }}" data-val="urgent">
                                                <i class="fas fa-fire d-block mb-1"></i>
                                                <small>Urgent</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label">Repair Status</label>
                                        <select name="status" class="form-select">
                                            <option value="received" {{ old('status', $repair->status) === 'received' ? 'selected' : '' }}>Received</option>
                                            <option value="diagnosing" {{ old('status', $repair->status) === 'diagnosing' ? 'selected' : '' }}>Diagnosing</option>
                                            <option value="in_progress" {{ old('status', $repair->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="waiting_parts" {{ old('status', $repair->status) === 'waiting_parts' ? 'selected' : '' }}>Waiting for Parts</option>
                                            <option value="completed" {{ old('status', $repair->status) === 'completed' ? 'selected' : '' }}>Ready / Completed</option>
                                            <option value="delivered" {{ old('status', $repair->status) === 'delivered' ? 'selected' : '' }}>Delivered &amp; Closed</option>
                                            <option value="cancelled" {{ old('status', $repair->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Estimated Cost & Advance Payment Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-wallet text-success"></i> Quotation &amp; Advance
                                    </h6>
                                </div>
                                <div class="card-panel-body">
                                    <div class="mb-3">
                                        <label class="form-label">Estimated Repair Cost (Rs.)</label>
                                        <input type="number" step="0.01" min="0" name="estimated_cost" id="estimatedCostInput" class="form-control font-monospace fw-bold text-primary"
                                            value="{{ $costVal }}" placeholder="0.00">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Advance Amount Paid (Rs.)</label>
                                        <input type="number" step="0.01" min="0" name="advance_paid" id="advancePaidInput" class="form-control font-monospace fw-bold text-success"
                                            value="{{ old('advance_paid', $repair->advance_paid ?? 0) }}" placeholder="0.00">
                                    </div>

                                    <div>
                                        <label class="form-label">Advance Account</label>
                                        <select name="advance_account_id" class="form-select">
                                            <option value="">-- Choose Cash / Bank Account --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}" {{ old('advance_account_id', $repair->advance_account_id) == $acc->id ? 'selected' : '' }}>
                                                    {{ $acc->title }} ({{ $acc->account_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- 5. Form Submission Buttons --}}
                            <div class="card-panel p-3">
                                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" style="border-radius: 8px; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);">
                                    <i class="fas fa-save"></i> Save Changes / Update Job
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

            const customerName = document.getElementById('customerName');
            const customerPhone = document.getElementById('customerPhone');
            const customerAddress = document.getElementById('customerAddress');
            const custBalanceBadgeWrap = document.getElementById('custBalanceBadgeWrap');
            const custBalanceVal = document.getElementById('custBalanceVal');

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
                }
            });

            // Handle Priority Card Clicks
            const priorityInput = document.getElementById('priorityInput');
            const priorityCards = document.querySelectorAll('.priority-card');

            priorityCards.forEach(card => {
                card.addEventListener('click', function() {
                    const val = this.dataset.val;
                    priorityInput.value = val;

                    priorityCards.forEach(c => {
                        c.classList.remove('active-normal', 'active-high', 'active-urgent');
                    });
                    this.classList.add('active-' + val);
                });
            });

            // Quick Product Fill
            const quickProduct = document.getElementById('quickProductSelect');
            if (quickProduct) {
                quickProduct.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    if (opt && opt.value) {
                        document.getElementById('productId').value = opt.value;
                        document.getElementById('itemName').value = opt.dataset.name || opt.text;
                    }
                });
            }

        });
    </script>
@endsection
