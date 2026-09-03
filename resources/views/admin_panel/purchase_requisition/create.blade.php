@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --pr-primary:    #4f46e5;
        --pr-primary-lt: #eef2ff;
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

    .form-label-pro {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }

    .form-control-pro, .form-select-pro {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        border: 1px solid var(--pr-border);
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--pr-text);
        background: #ffffff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .form-control-pro:focus, .form-select-pro:focus {
        border-color: var(--pr-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    .btn-pr-primary {
        background: var(--pr-primary) !important;
        border: 1px solid var(--pr-primary) !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 10px;
        padding: 9px 20px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s;
    }

    .btn-pr-primary:hover {
        background: #4338ca !important;
        transform: translateY(-1px);
        color: #ffffff !important;
    }

    .btn-pr-back {
        background: #ffffff;
        color: var(--pr-muted);
        border: 1px solid var(--pr-border);
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 0.84rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.15s;
    }

    .btn-pr-back:hover {
        background: #f1f5f9;
        color: var(--pr-text);
    }

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
        padding: 10px 14px;
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
                    <i class="fas fa-file-signature text-primary"></i> Create Purchase Requisition
                </h4>
                <p class="text-muted small mb-0">Submit internal material demands &amp; requirements for approval</p>
            </div>
            <div>
                <a href="{{ route('purchase-requisitions.index') }}" class="btn-pr-back">
                    <i class="fas fa-arrow-left"></i> Back to Requisitions
                </a>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm mb-3">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Please fix the following errors:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('purchase-requisitions.store') }}" method="POST">
            @csrf
            
            {{-- Card 1: General Information --}}
            <div class="pr-card">
                <div class="pr-card-header">
                    <h5 class="pr-card-title">
                        <i class="fas fa-info-circle text-primary"></i> General Information
                    </h5>
                </div>
                <div class="pr-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label-pro">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select-pro" required>
                                <option value="">Select Branch...</option>
                                @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-pro">Target Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select-pro" required>
                                <option value="">Select Warehouse...</option>
                                @foreach($warehouses ?? [] as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->warehouse_name ?? $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-pro">Required By Date <span class="text-danger">*</span></label>
                            <input type="date" name="required_by_date" class="form-control-pro" value="{{ old('required_by_date', date('Y-m-d', strtotime('+7 days'))) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label-pro">Notes / Purpose of Requirement</label>
                            <textarea name="notes" class="form-control-pro" rows="2" style="height: auto;" placeholder="State department justification, urgent maintenance requirement, or project details...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Requisition Items --}}
            <div class="pr-card">
                <div class="pr-card-header">
                    <h5 class="pr-card-title">
                        <i class="fas fa-cubes text-primary"></i> Requisition Items &amp; Quantities
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="addItemBtn">
                        <i class="fas fa-plus me-1"></i> Add Material / Item
                    </button>
                </div>
                <div class="pr-card-body p-0">
                    <div class="table-responsive">
                        <table class="items-table" id="prItemsTable">
                            <thead>
                                <tr>
                                    <th style="min-width: 260px;">Product / Material <span class="text-danger">*</span></th>
                                    <th style="width: 130px;">UOM <span class="text-danger">*</span></th>
                                    <th style="width: 140px;">Required Qty <span class="text-danger">*</span></th>
                                    <th style="width: 160px;">Est. Unit Price</th>
                                    <th>Reason / Spec Notes</th>
                                    <th style="width: 60px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows inserted dynamically via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Form Submit --}}
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('purchase-requisitions.index') }}" class="btn-pr-back">Cancel</a>
                <button type="submit" class="btn-pr-primary">
                    <i class="fas fa-paper-plane"></i> Submit Requisition for Approval
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.querySelector('#prItemsTable tbody');
        const addBtn = document.getElementById('addItemBtn');
        let rowIndex = 0;

        function addRow() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][product_id]" class="form-select-pro product-select" required>
                        <option value="">Select Product / Material...</option>
                        @foreach($products ?? [] as $product)
                        <option value="{{ $product->id }}"
                            data-uom="{{ $product->size_mode === 'by_cartons' ? 'Carton' : ($product->size_mode === 'by_meter' ? 'Meter' : ($product->size_mode === 'by_kg' ? 'Kg' : 'Pcs')) }}"
                            data-price="{{ $product->purchase_price_per_piece ?? 0 }}">
                            [{{ $product->item_code }}] {{ $product->item_name }}
                        </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="items[${rowIndex}][uom]" class="form-control-pro item-uom" required placeholder="e.g. Pcs, Mtr, Kg">
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][required_qty]" class="form-control-pro fw-bold text-end" step="0.01" min="0.01" required placeholder="0">
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][estimated_unit_price]" class="form-control-pro item-price text-end" step="0.01" min="0" placeholder="0.00">
                </td>
                <td>
                    <input type="text" name="items[${rowIndex}][reason]" class="form-control-pro" placeholder="Optional notes...">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle remove-btn" title="Remove Row" style="width:32px;height:32px;padding:0;">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            rowIndex++;
        }

        // Add first row by default
        addRow();

        addBtn.addEventListener('click', addRow);

        tbody.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const opt = e.target.selectedOptions[0];
                const row = e.target.closest('tr');
                if (opt && opt.dataset) {
                    if (opt.dataset.uom) {
                        const uomInput = row.querySelector('.item-uom');
                        if (uomInput) uomInput.value = opt.dataset.uom;
                    }
                    if (opt.dataset.price) {
                        const priceInput = row.querySelector('.item-price');
                        if (priceInput) priceInput.value = opt.dataset.price;
                    }
                }
            }
        });

        tbody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-btn')) {
                if (tbody.children.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('At least one item is required in the requisition.');
                }
            }
        });
    });
</script>
@endsection
