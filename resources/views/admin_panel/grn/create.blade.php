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
    .form-control, .form-select {
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
        padding: 8px 16px !important;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="page-title">
                <h4>Create GRN</h4>
                <h6>Receive goods against Purchase Order</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('grn.index') }}" class="btn btn-light border">
                    <i class="fa fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('grn.store') }}" method="POST" id="grnForm">
            @csrf
            
            <div class="card premium-card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Select Purchase Order <span class="text-danger">*</span></label>
                            <select name="purchase_order_id" id="poSelect" class="form-select" required>
                                <option value="">Select PO...</option>
                                @foreach($pendingPOs ?? [] as $po)
                                <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->vendor->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Received Date <span class="text-danger">*</span></label>
                            <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Delivery Challan No</label>
                            <input type="text" name="delivery_challan_no" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">QC Notes / Remarks</label>
                            <textarea name="qc_notes" class="form-control" rows="2" style="height: auto;"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card premium-card mb-4" id="poDetailsCard" style="display: none;">
                <div class="card-body bg-light rounded">
                    <h5 class="mb-3">PO Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Vendor:</strong> <span id="lblVendor"></span>
                        </div>
                        <div class="col-md-4">
                            <strong>PO Date:</strong> <span id="lblPoDate"></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Currency:</strong> <span id="lblCurrency"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card premium-card">
                <div class="card-body">
                    <h5 class="mb-3">Receive Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="grnItemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th>Ordered Qty</th>
                                    <th>Received Qty <span class="text-danger">*</span></th>
                                    <th>Accepted Qty <span class="text-danger">*</span></th>
                                    <th>Rejected Qty</th>
                                    <th>Rejection Reason</th>
                                    <th>Batch No</th>
                                    <th>Unit Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Select a PO to load items</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-premium-primary" id="btnSubmit" disabled>
                    <i class="fa fa-save me-2"></i>Save GRN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const poSelect = document.getElementById('poSelect');
        const poDetailsCard = document.getElementById('poDetailsCard');
        const tbody = document.querySelector('#grnItemsTable tbody');
        const btnSubmit = document.getElementById('btnSubmit');
        
        poSelect.addEventListener('change', function() {
            const poId = this.value;
            if(!poId) {
                poDetailsCard.style.display = 'none';
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">Select a PO to load items</td></tr>';
                btnSubmit.disabled = true;
                return;
            }

            // Mock AJAX call - In reality this would be fetch(`/api/po/${poId}/items`)
            // For now, we simulate data loading based on requirements
            
            fetch(`/admin/purchase/${poId}/details`) // Ensure you have an endpoint that returns PO JSON
                .then(response => response.json())
                .then(data => {
                    document.getElementById('lblVendor').textContent = data.vendor_name || 'N/A';
                    document.getElementById('lblPoDate').textContent = data.po_date || 'N/A';
                    document.getElementById('lblCurrency').textContent = data.currency || 'PKR';
                    
                    poDetailsCard.style.display = 'block';
                    btnSubmit.disabled = false;
                    
                    tbody.innerHTML = '';
                    
                    if(data.items && data.items.length > 0) {
                        data.items.forEach((item, index) => {
                            let tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>
                                    ${item.product_name}
                                    <input type="hidden" name="items[${index}][po_item_id]" value="${item.id}">
                                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                </td>
                                <td>
                                    <select name="items[${index}][warehouse_id]" class="form-select form-select-sm" required>
                                        <!-- Need to inject warehouses here or load them via JS -->
                                        <option value="${item.warehouse_id || ''}">${item.warehouse_name || 'Default'}</option>
                                    </select>
                                </td>
                                <td>${item.ordered_qty}</td>
                                <td>
                                    <input type="number" name="items[${index}][received_qty]" class="form-control form-control-sm rcv-qty" step="0.01" min="0" max="${item.pending_qty}" value="${item.pending_qty}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[${index}][accepted_qty]" class="form-control form-control-sm acc-qty" step="0.01" min="0" value="${item.pending_qty}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[${index}][rejected_qty]" class="form-control form-control-sm rej-qty" step="0.01" min="0" value="0" readonly>
                                </td>
                                <td>
                                    <input type="text" name="items[${index}][rejection_reason]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="text" name="items[${index}][batch_no]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="number" name="items[${index}][unit_price]" class="form-control form-control-sm" step="0.01" value="${item.unit_price}" readonly>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                        
                        // Add event listeners for dynamic calculation
                        document.querySelectorAll('.rcv-qty, .acc-qty').forEach(input => {
                            input.addEventListener('input', function() {
                                let tr = this.closest('tr');
                                let rcv = parseFloat(tr.querySelector('.rcv-qty').value) || 0;
                                let acc = parseFloat(tr.querySelector('.acc-qty').value) || 0;
                                
                                if(acc > rcv) {
                                    acc = rcv;
                                    tr.querySelector('.acc-qty').value = acc;
                                }
                                
                                tr.querySelector('.rej-qty').value = rcv - acc;
                            });
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No pending items found for this PO</td></tr>';
                        btnSubmit.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching PO details:', error);
                    // Add dummy data for demonstration if fetch fails (e.g. endpoint doesn't exist yet)
                    tbody.innerHTML = '<tr><td colspan="9" class="text-danger text-center py-4">Error loading items. Ensure API endpoint exists.</td></tr>';
                });
        });
    });
</script>
@endsection
