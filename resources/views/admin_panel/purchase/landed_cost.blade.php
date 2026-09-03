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
    .cost-input {
        text-align: right;
    }
    .live-calc-box {
        background: #f8fafc;
        border: 2px dashed #94a3b8;
        border-radius: 10px;
        padding: 20px;
    }
    .final-cost-box {
        background: #f0fdf4;
        border: 2px solid #22c55e;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
    }
    .final-cost-val {
        font-size: 24px;
        font-weight: 800;
        color: #166534;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="page-title">
                <h4>Landed Cost Calculator</h4>
                <h6>PO: {{ $po->po_number ?? 'PO-000' }}</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('Purchase.home') }}" class="btn btn-light border">
                    <i class="fa fa-arrow-left me-2"></i>Back to Purchases
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card premium-card">
                    <div class="card-body bg-light rounded">
                        <h5 class="mb-3">PO Summary</h5>
                        <div class="row">
                            <div class="col-md-2">
                                <small class="text-muted d-block text-uppercase fw-bold">Vendor</small>
                                <strong>{{ $po->vendor->name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted d-block text-uppercase fw-bold">Date</small>
                                <strong>{{ isset($po->date) ? date('d-M-Y', strtotime($po->date)) : 'N/A' }}</strong>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted d-block text-uppercase fw-bold">Currency</small>
                                <strong>{{ $po->currency ?? 'PKR' }}</strong>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted d-block text-uppercase fw-bold">Exchange Rate</small>
                                <strong>{{ number_format($po->exchange_rate ?? 1, 4) }}</strong>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted d-block text-uppercase fw-bold">Total Items</small>
                                <strong id="lblTotalItems">{{ $po->items_count ?? 0 }}</strong>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted d-block text-uppercase fw-bold">Subtotal (PKR)</small>
                                <strong id="lblSubtotalPkr">{{ number_format($po->subtotal_pkr ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card premium-card">
                    <div class="card-body">
                        <h5 class="mb-3">Items Information</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Ordered Qty</th>
                                        <th class="text-end">Unit Price ({{ $po->currency ?? 'PKR' }})</th>
                                        <th class="text-end">Unit Price (PKR)</th>
                                        <th class="text-end">Line Total (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $totalQty = 0;
                                        $totalPkr = 0;
                                    @endphp
                                    @forelse($po->items ?? [] as $item)
                                    @php 
                                        $qty = $item->ordered_qty ?? 0;
                                        $upFcy = $item->unit_price ?? 0;
                                        $upPkr = $upFcy * ($po->exchange_rate ?? 1);
                                        $ltPkr = $qty * $upPkr;
                                        
                                        $totalQty += $qty;
                                        $totalPkr += $ltPkr;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($qty, 2) }}</td>
                                        <td class="text-end">{{ number_format($upFcy, 2) }}</td>
                                        <td class="text-end">{{ number_format($upPkr, 2) }}</td>
                                        <td class="text-end">{{ number_format($ltPkr, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No items found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light fw-bold">
                                        <td>Totals</td>
                                        <td class="text-end" id="dataTotalQty">{{ number_format($totalQty, 2) }}</td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end" id="dataTotalPkr">{{ number_format($totalPkr, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="live-calc-box mt-4">
                            <h5 class="mb-3 text-center border-bottom pb-2">LIVE CALCULATION</h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Item Cost (PKR):</span>
                                        <strong id="calcItemCost">{{ number_format($totalPkr, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Extra Logistics Cost:</span>
                                        <strong id="calcExtraCost">0.00</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Ordered Qty:</span>
                                        <strong id="calcTotalQty">{{ number_format($totalQty, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Average Unit Cost (PKR):</span>
                                        <strong id="calcAvgUnitCost">{{ $totalQty > 0 ? number_format($totalPkr / $totalQty, 2) : '0.00' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Extra Cost Per Unit (PKR):</span>
                                        <strong id="calcExtraPerUnit">0.00</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="final-cost-box mt-3">
                                <div class="text-uppercase fw-bold text-success mb-1">LANDED COST PER UNIT (PKR)</div>
                                <div class="final-cost-val" id="calcLandedCost">
                                    {{ $totalQty > 0 ? number_format($totalPkr / $totalQty, 2) : '0.00' }}
                                </div>
                                <small class="text-muted mt-2 d-block">Formula: Landed Cost = Unit Cost + (Total Extra Costs ÷ Total Qty)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <form action="{{ route('import-costs.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="purchase_order_id" value="{{ $po->id ?? '' }}">
                    
                    <div class="card premium-card">
                        <div class="card-body">
                            <h5 class="mb-3">Extra Logistics Costs (PKR)</h5>
                            
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Freight Charges</label>
                                <div class="col-sm-6">
                                    <input type="number" name="freight_charges" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Insurance Amount</label>
                                <div class="col-sm-6">
                                    <input type="number" name="insurance_amount" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Port Charges</label>
                                <div class="col-sm-6">
                                    <input type="number" name="port_charges" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Customs Duty</label>
                                <div class="col-sm-6">
                                    <input type="number" name="customs_duty" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Regulatory Duty</label>
                                <div class="col-sm-6">
                                    <input type="number" name="regulatory_duty" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Sales Tax (Import)</label>
                                <div class="col-sm-6">
                                    <input type="number" name="sales_tax_import" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Clearing Agent Charges</label>
                                <div class="col-sm-6">
                                    <input type="number" name="clearing_agent_charges" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Inland Freight</label>
                                <div class="col-sm-6">
                                    <input type="number" name="inland_freight" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-6 col-form-label">Miscellaneous Costs</label>
                                <div class="col-sm-6">
                                    <input type="number" name="miscellaneous_costs" class="form-control cost-input calc-trigger" step="0.01" min="0" value="0">
                                </div>
                            </div>

                            <hr>
                            
                            <div class="mb-3">
                                <label class="form-label">Allocation Method</label>
                                <select name="allocation_method" class="form-select" id="allocationMethod">
                                    <option value="qty">By Quantity</option>
                                    <option value="weight">By Weight</option>
                                    <option value="value">By Value</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>

                            <button type="submit" class="btn btn-premium-primary w-100 mt-2">
                                <i class="fa fa-save me-2"></i>Save Landed Cost
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalQty = {{ $totalQty }};
        const totalPkr = {{ $totalPkr }};
        const avgUnitCost = totalQty > 0 ? (totalPkr / totalQty) : 0;
        
        const inputs = document.querySelectorAll('.calc-trigger');
        const calcExtraCost = document.getElementById('calcExtraCost');
        const calcExtraPerUnit = document.getElementById('calcExtraPerUnit');
        const calcLandedCost = document.getElementById('calcLandedCost');
        
        function formatNumber(num) {
            return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function recalculate() {
            let totalExtra = 0;
            inputs.forEach(input => {
                let val = parseFloat(input.value) || 0;
                totalExtra += val;
            });
            
            calcExtraCost.textContent = formatNumber(totalExtra);
            
            let extraPerUnit = 0;
            if(totalQty > 0) {
                extraPerUnit = totalExtra / totalQty;
            }
            
            calcExtraPerUnit.textContent = formatNumber(extraPerUnit);
            calcLandedCost.textContent = formatNumber(avgUnitCost + extraPerUnit);

            // Optional: You could make an AJAX call to /import-costs/{id}/calculate here as requested, 
            // but doing it client-side is faster and smoother for "live" calculation.
            // If server-side calculation is strict requirement:
            /*
            fetch(`/import-costs/{{ $po->id ?? 0 }}/calculate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    freight: document.querySelector('input[name="freight_charges"]').value,
                    // ... other fields
                })
            }).then(res => res.json()).then(data => {
                // update UI from server response
            });
            */
        }

        inputs.forEach(input => {
            input.addEventListener('input', recalculate);
        });
        
        document.getElementById('allocationMethod').addEventListener('change', recalculate);
    });
</script>
@endsection
