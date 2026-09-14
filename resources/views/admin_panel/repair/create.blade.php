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
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 erp-page-header mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-1.5 font-monospace shadow-sm" style="font-size: 0.80rem; border-radius: 6px; letter-spacing: 0.04em;">
                                {{ $nextRepairNo }}
                            </span>
                            <h4 class="erp-title mb-0">
                                <i class="fas fa-tools text-primary"></i> Receive Product for Repair
                            </h4>
                        </div>
                        <p class="erp-subtitle" style="margin-top: 0.35rem;">
                            Create customer repair job card, record device specifications, physical condition, accessories and issue intake receipt
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('repair.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-weight: 600; border-radius: 8px; padding: 7px 16px; font-size: 0.80rem;">
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
                                                        {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                                        {{ $c->customer_name }} ({{ $c->mobile ?? 'No phone' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="custBalanceBadgeWrap" class="mt-1" style="display: none;">
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
                                                value="{{ old('customer_name') }}" placeholder="e.g. Tariq Textiles / Ahmed Khan" required>
                                        </div>

                                        {{-- 3. Mobile / WhatsApp --}}
                                        <div class="col-md-3">
                                            <label class="form-label required">Mobile / WhatsApp #</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted" style="font-size: 0.75rem;"><i class="fas fa-phone"></i></span>
                                                <input type="text" name="customer_phone" id="customerPhone" class="form-control"
                                                    value="{{ old('customer_phone') }}" placeholder="0300-1234567" required>
                                            </div>
                                        </div>

                                        {{-- 4. City / Address --}}
                                        <div class="col-md-3">
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

                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label required">Reported Fault, Physical Condition &amp; Accessories Received</label>
                                            <textarea name="problem_description" id="problemDescription" rows="3" class="form-control"
                                                placeholder="Enter reported fault / problem description, physical condition, and accessories received..." required>{{ old('problem_description') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- RIGHT COLUMN: Schedule, Priority & Actions (col-lg-4) --}}
                        <div class="col-lg-4">

                            {{-- 3. Dates & Priority Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-calendar-alt text-primary"></i> Schedule &amp; Priority
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

                            {{-- 4. Advance Payment Card --}}
                            <div class="card-panel">
                                <div class="card-panel-header">
                                    <h6 class="card-panel-title">
                                        <i class="fas fa-wallet text-success"></i> Advance Payment Collected
                                    </h6>
                                </div>
                                <div class="card-panel-body">
                                    <div class="mb-3">
                                        <label class="form-label">Advance Amount (Rs.)</label>
                                        <input type="number" step="0.01" min="0" name="advance_paid" id="advancePaidInput" class="form-control font-monospace fw-bold text-success"
                                            value="{{ old('advance_paid', '0.00') }}" placeholder="0.00">
                                    </div>

                                    <div>
                                        <label class="form-label">Deposit Account</label>
                                        <select name="advance_account_id" class="form-select">
                                            <option value="">-- Choose Cash / Bank Account --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}" {{ old('advance_account_id') == $acc->id ? 'selected' : '' }}>
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

        });
    </script>
@endsection

