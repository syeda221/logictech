@extends('admin_panel.layout.app')

@section('content')
<style>
    .mfg-page { font-family:'Inter',system-ui,sans-serif; color:#1e293b; }
    .card-premium { border:1.5px solid #e2e8f0; border-radius:14px; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    .card-header-mfg { background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%); color:#fff; border-radius:12px 12px 0 0; padding:18px 22px; }
    .form-label { font-size:.82rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.4px; }
    .pr-item-row { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:10px; }
</style>
<div class="container-fluid py-4 mfg-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchase-requisitions.index') }}">Purchase Requisitions</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchase-requisitions.show', $pr->id) }}">{{ $pr->pr_number }}</a></li>
            <li class="breadcrumb-item active">Convert to PO</li>
        </ol>
    </nav>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-file-import me-2 text-primary"></i>Convert PR to Purchase Order</h4>
            <p class="text-muted small mb-0">PR: <strong>{{ $pr->pr_number }}</strong> | Items: {{ $pr->items->count() }}</p>
        </div>
        <a href="{{ route('purchase-requisitions.show', $pr->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3">{{ session('error') }}</div>
    @endif
    <form action="{{ route('store.Purchase') }}" method="POST">
        @csrf
        {{-- Hidden metadata fields --}}
        <input type="hidden" name="pr_id" value="{{ $pr->id }}">
        <input type="hidden" name="purchase_type" id="purchaseType" value="local">
        <input type="hidden" name="action" value="save_only"> {{-- Draft: no stock update until GRN --}}
        <input type="hidden" name="subtotal" id="subtotalInput" value="0">
        <input type="hidden" name="net_amount" id="netAmountInput" value="0">
        <input type="hidden" name="due_amount" id="dueAmountInput" value="0">
        <input type="hidden" name="extra_cost" value="0">
        <input type="hidden" name="discount" id="discountHidden" value="0">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card-premium mb-3">
                    <div class="card-header-mfg"><h6 class="mb-0"><i class="fas fa-cog me-2"></i>PO Configuration</h6></div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label">Purchase Type</label>
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-outline-success active" id="btnLocal" onclick="setPurchaseType('local')"><i class="fas fa-map-marker-alt me-1"></i>Local</button>
                                <button type="button" class="btn btn-outline-primary" id="btnImport" onclick="setPurchaseType('import')"><i class="fas fa-globe me-1"></i>Import</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vendor *</label>
                            <select name="vendor_id" class="form-select" required>
                                <option value="">-- Select Vendor --</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}{{ $vendor->type==='international'?' 🌐':'' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Branch *</label>
                                <select name="branch_id" class="form-select" required>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $branch->id==$pr->branch_id?'selected':'' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Warehouse *</label>
                                <select name="warehouse_id" class="form-select" required>
                                    @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ $wh->id==$pr->warehouse_id?'selected':'' }}>{{ $wh->warehouse_name ?? $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">PO Date *</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Invoice No.</label>
                                <input type="text" name="invoice_no" class="form-control" placeholder="PO-2026-001">
                            </div>
                        </div>
                        <div id="importFields" style="display:none;">
                            <hr class="my-2"><p class="text-primary small fw-bold mb-2"><i class="fas fa-ship me-1"></i>Import Details</p>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label">Currency</label>
                                    <select name="currency" class="form-select form-select-sm">
                                        <option value="PKR">PKR</option><option value="USD">USD</option><option value="EUR">EUR</option><option value="CNY">CNY</option><option value="AED">AED</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Exchange Rate</label>
                                    <input type="number" name="exchange_rate" class="form-control form-control-sm" value="1" step="0.000001">
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select form-select-sm">
                                        <option value="">-- Select --</option><option value="lc">LC</option><option value="tt_advance">TT Advance</option><option value="bank_transfer">Bank Transfer</option><option value="cash">Cash</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Delivery Terms</label>
                                    <select name="delivery_terms" class="form-select form-select-sm">
                                        <option value="">-- Select --</option><option value="FOB">FOB</option><option value="CIF">CIF</option><option value="CFR">CFR</option><option value="EXW">EXW</option><option value="DDP">DDP</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label">Port of Loading</label>
                                    <input type="text" name="port_of_loading" class="form-control form-control-sm" placeholder="Shanghai">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Port of Discharge</label>
                                    <input type="text" name="port_of_discharge" class="form-control form-control-sm" placeholder="Karachi">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Expected Delivery Date</label>
                                <input type="date" name="expected_delivery_date" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="note" class="form-control" rows="2">{{ $pr->notes }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card-premium">
                    <div class="card-header-mfg"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Items from Requisition</h6></div>
                    <div class="p-4">
                        @foreach($pr->items as $index => $item)
                        <div class="pr-item-row">
                            {{-- ✅ FLAT ARRAYS — matches PurchaseController::store() validation --}}
                            <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                            <input type="hidden" name="size_mode[]" value="by_pieces">
                            <input type="hidden" name="pieces_per_box[]" value="1">
                            <input type="hidden" name="pieces_per_m2[]" value="0">
                            <input type="hidden" name="color[]" value="">
                            <div class="fw-bold mb-2">{{ $item->product->name ?? 'N/A' }} <span class="badge bg-warning text-dark ms-1 small">Req: {{ $item->required_qty }} {{ $item->uom }}</span></div>
                            <div class="row g-2">
                                <div class="col-3">
                                    <label class="form-label" style="font-size:.72rem;">Order Qty *</label>
                                    <input type="number" name="qty[]" class="form-control form-control-sm item-qty" value="{{ $item->required_qty }}" min="0.001" step="0.001" data-index="{{ $index }}" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" style="font-size:.72rem;">Price (PKR) *</label>
                                    <input type="number" name="price[]" class="form-control form-control-sm item-price" value="{{ $item->estimated_unit_price }}" min="0" step="0.01" data-index="{{ $index }}" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" style="font-size:.72rem;">Disc %</label>
                                    <input type="number" name="item_discount[]" class="form-control form-control-sm" value="0" min="0" step="0.01">
                                </div>
                                <div class="col-3">
                                    <label class="form-label" style="font-size:.72rem;">Line Total</label>
                                    <input type="text" id="line_total_{{ $index }}" class="form-control form-control-sm bg-light fw-bold" readonly>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" style="font-size:.72rem;">UOM</label>
                                    <input type="text" name="unit[]" class="form-control form-control-sm" value="{{ $item->uom }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="border-top pt-3 mt-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Subtotal:</span><span class="fw-bold" id="displaySubtotal">PKR 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Bill Discount:</span>
                                <div style="width:120px;"><input type="number" id="totalDiscount" class="form-control form-control-sm text-end" value="0" min="0" step="0.01"></div>
                            </div>
                            <div class="d-flex justify-content-between fw-bold fs-5 mt-2">
                                <span>Net Amount:</span><span id="displayNetAmount" class="text-success">PKR 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-3 small">
                    <i class="fas fa-info-circle me-1"></i> <strong>Note:</strong> PO will be created as <strong>Draft</strong>. Stock will be added only after GRN (Goods Received Note) is created.
                </div>
                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold py-3"><i class="fas fa-file-alt me-2"></i>Create Purchase Order (Draft)</button>
                </div>
            </div>
        </div>
    </form>

</div>
<script>
function setPurchaseType(t){
    document.getElementById('purchaseType').value=t;
    document.getElementById('importFields').style.display=t==='import'?'block':'none';
    document.getElementById('btnImport').classList.toggle('active',t==='import');
    document.getElementById('btnLocal').classList.toggle('active',t==='local');
}
function recalc(){
    let sub=0;
    document.querySelectorAll('.item-qty').forEach(function(q){
        const i=q.dataset.index,qty=parseFloat(q.value)||0,price=parseFloat(document.querySelector('.item-price[data-index="'+i+'"]').value)||0,lt=qty*price;
        sub+=lt;
        const ltEl=document.getElementById('line_total_'+i);
        if(ltEl) ltEl.value=lt.toFixed(2);
    });
    const disc=parseFloat(document.getElementById('totalDiscount').value)||0;
    const net=Math.max(0,sub-disc);
    document.getElementById('displaySubtotal').textContent='PKR '+sub.toFixed(2);
    document.getElementById('displayNetAmount').textContent='PKR '+net.toFixed(2);
    document.getElementById('subtotalInput').value=sub.toFixed(2);
    document.getElementById('netAmountInput').value=net.toFixed(2);
    document.getElementById('dueAmountInput').value=net.toFixed(2);
    document.getElementById('discountHidden').value=disc.toFixed(2);
}
document.querySelectorAll('.item-qty,.item-price').forEach(e=>e.addEventListener('input',recalc));
document.getElementById('totalDiscount').addEventListener('input',recalc);
recalc();
</script>
@endsection