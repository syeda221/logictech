{{-- ══════════════════════════════════════════════════════════════
     CREATE PRODUCT / COMPONENT MODAL (RAW MATERIAL & FINISHED GOODS)
══════════════════════════════════════════════════════════════ --}}
<style>
    /* Modal Specific Styling */
    #createProductModal {
        z-index: 1050;
    }
    #createProductModal .modal-content {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
        overflow: hidden;
    }
    #createProductModal .modal-header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 24px;
    }
    #createProductModal .modal-body {
        background: #f8fafc;
        padding: 20px 24px;
        max-height: calc(88vh - 120px);
        overflow-y: auto;
    }
    #createProductModal .modal-footer {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 14px 24px;
    }

    /* Section Cards inside Modal */
    .cpm-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        margin-bottom: 16px;
        overflow: hidden;
    }
    .cpm-card-header {
        padding: 12px 18px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cpm-card-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cpm-card-body {
        padding: 18px;
    }

    /* Labels & Controls */
    .cpm-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 4px;
        letter-spacing: 0.02em;
        display: block;
    }
    .cpm-input, .cpm-select {
        display: block;
        width: 100%;
        padding: 8px 12px;
        font-size: 0.88rem;
        font-weight: 500;
        color: #0f172a;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        transition: all 0.15s ease-in-out;
    }
    .cpm-input:focus, .cpm-select:focus {
        border-color: #4f46e5;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    /* Classification Toggle */
    .cpm-type-toggle {
        display: flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        gap: 4px;
    }
    .cpm-type-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.88rem;
        color: #475569;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all 0.18s ease;
        user-select: none;
        background: transparent;
    }
    .cpm-type-btn:hover {
        background: rgba(255,255,255,0.7);
    }
    .cpm-type-btn.active-raw {
        background: #f59e0b !important;
        border-color: #f59e0b !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
    }
    .cpm-type-btn.active-finish {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
    }

    /* Image Uploader */
    .cpm-img-uploader {
        width: 100%;
        height: 120px;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.2s;
    }
    .cpm-img-uploader:hover {
        border-color: #4f46e5;
        background: #eef2ff;
    }
    .cpm-img-uploader img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Variants Table */
    #cpmVariantsTable {
        font-size: 0.8rem;
        border-color: #e2e8f0;
    }
    #cpmVariantsTable th {
        background: #f8fafc !important;
        color: #475569;
        font-weight: 700;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 8px 6px !important;
        white-space: nowrap;
        vertical-align: middle;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    #cpmVariantsTable td {
        padding: 4px 4px !important;
        vertical-align: middle;
        border-color: #f1f5f9;
    }
    #cpmVariantsTable .cpm-tbl-input, #cpmVariantsTable .cpm-tbl-select {
        height: 32px;
        padding: 3px 6px;
        font-size: 0.8rem;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        width: 100%;
        background-color: #ffffff;
    }
    #cpmVariantsTable .cpm-tbl-input:focus, #cpmVariantsTable .cpm-tbl-select:focus {
        border-color: #4f46e5;
        outline: 0;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
    }

    /* Stacked Modals z-index */
    #cpmCategoryModal, #cpmSubcategoryModal, #cpmBrandModal { z-index: 1070; }
</style>

<div class="modal fade" id="createProductModal" tabindex="-1" role="dialog" aria-labelledby="createProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            
            {{-- Modal Header --}}
            <div class="modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-sm btn-light border rounded-circle p-0" data-dismiss="modal" data-bs-dismiss="modal" style="width: 32px; height: 32px; display: grid; place-items: center;" title="Close">
                        <i class="fas fa-arrow-left text-muted"></i>
                    </button>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="createProductModalLabel">
                            <i class="fas fa-microchip text-primary" id="cpmHeaderIcon"></i>
                            <span id="cpmHeaderTitle">Create Product / Component</span>
                        </h5>
                        <small class="text-muted" style="font-size:0.78rem;" id="cpmHeaderSub">LogicTech Industrial ERP — Add heating devices, chillers, power electronics, or raw materials</small>
                    </div>
                </div>
                <button type="button" class="close btn-close text-muted" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; background: none; border: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Modal Body with Form --}}
            <form id="cpmProductForm" action="{{ route('store-product') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    
                    {{-- ── CARD 1: Identity & Classification ── --}}
                    <div class="cpm-card">
                        <div class="cpm-card-header">
                            <h6 class="cpm-card-title">
                                <i class="fas fa-microchip text-primary"></i> Product &amp; Component Identity
                            </h6>
                        </div>
                        <div class="cpm-card-body">
                            <div class="row g-3">
                                
                                {{-- Left 9 Cols: Classification, Name, Category, Subcategory, Brand, Unit --}}
                                <div class="col-lg-9 col-md-8">
                                    <div class="row g-3">
                                        
                                        {{-- Item Classification Toggle --}}
                                        <div class="col-12">
                                            <label class="cpm-label d-flex align-items-center justify-content-between">
                                                <span><i class="fas fa-layer-group text-primary me-1"></i> Item Classification / Material Type <span class="text-danger">*</span></span>
                                                <span class="text-muted fw-normal" style="font-size: 0.72rem; text-transform: none;">Choose whether this item is Raw Material or Finished Goods</span>
                                            </label>
                                            
                                            <input type="hidden" name="item_type" id="cpm_item_type" value="raw_material">
                                            
                                            <div class="cpm-type-toggle">
                                                <div class="cpm-type-btn active-raw" id="cpmTypeRawBtn" onclick="cpmSetItemType('raw_material')">
                                                    <i class="fas fa-boxes-stacked"></i>
                                                    <span>Raw Material</span>
                                                </div>
                                                <div class="cpm-type-btn" id="cpmTypeFinishBtn" onclick="cpmSetItemType('finish_goods')">
                                                    <i class="fas fa-microchip"></i>
                                                    <span>Finished Goods</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Product / Component Name --}}
                                        <div class="col-12">
                                            <label class="cpm-label">Product / Component Name <span class="text-danger">*</span></label>
                                            <input type="text" class="cpm-input fw-bold" name="product_name" id="cpm_product_name" required placeholder="e.g. High Frequency Induction Heater 25kW, Industrial Air Chiller 5TR, IGBT Module 1200V">
                                        </div>

                                        {{-- 4 Columns Dropdowns --}}
                                        <div class="col-md-3 col-sm-6">
                                            <label class="cpm-label">Category <span class="text-danger">*</span></label>
                                            <div class="d-flex gap-1">
                                                <select class="cpm-select form-select" id="cpm_category_id" name="category_id" required>
                                                    <option value="">Select...</option>
                                                    @foreach ($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-light border px-2 shadow-sm" data-toggle="modal" data-target="#cpmCategoryModal" title="Add Category">+</button>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <label class="cpm-label">Sub Category</label>
                                            <div class="d-flex gap-1">
                                                <select class="cpm-select form-select" id="cpm_sub_category_id" name="sub_category_id">
                                                    <option value="">Select...</option>
                                                </select>
                                                <button type="button" class="btn btn-light border px-2 shadow-sm" data-toggle="modal" data-target="#cpmSubcategoryModal" title="Add Subcategory">+</button>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <label class="cpm-label">Brand</label>
                                            <div class="d-flex gap-1">
                                                <select class="cpm-select form-select" id="cpm_brand_id" name="brand_id" required>
                                                    <option value="">Select...</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-light border px-2 shadow-sm" data-toggle="modal" data-target="#cpmBrandModal" title="Add Brand">+</button>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <label class="cpm-label">Unit</label>
                                            <select class="cpm-select form-select fw-bold" name="size_mode" id="cpm_unit_dropdown">
                                                <option value="by_pieces" selected>Pcs</option>
                                                <option value="by_cartons">Carton</option>
                                                <option value="by_meter">Meter</option>
                                                <option value="by_feet">Ft (Feet)</option>
                                                <option value="by_kg">Kg</option>
                                                <option value="by_gm">Gm</option>
                                                <option value="by_ton">Ton</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                {{-- Right 3 Cols: Product Image Upload --}}
                                <div class="col-lg-3 col-md-4">
                                    <label class="cpm-label">Product Image</label>
                                    <input type="file" id="cpmImageInput" name="image" class="d-none" accept="image/*">
                                    <div class="cpm-img-uploader" id="cpmUploadArea" onclick="document.getElementById('cpmImageInput').click()">
                                        <button type="button" id="cpmClearImageBtn" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 d-none rounded-circle" style="width:20px;height:20px;padding:0;z-index:10; font-size:12px; line-height:1;">&times;</button>
                                        <img id="cpmImagePreview" class="d-none" alt="Preview">
                                        <div id="cpmUploadPlaceholder" class="text-center p-2">
                                            <i class="fas fa-camera fs-3 text-primary mb-1"></i>
                                            <div class="fw-bold text-muted" style="font-size: 11px;">Upload</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ── CARD 2: Product Variants & Units ── --}}
                    <div class="cpm-card">
                        <div class="cpm-card-header">
                            <h6 class="cpm-card-title text-primary">
                                <i class="fas fa-cubes me-1"></i> Product Variants &amp; Units
                            </h6>
                            <button type="button" class="btn btn-sm btn-primary" id="cpmAddVariantRowBtn" style="background:#4f46e5; border:none; font-weight:600; font-size:0.78rem;">
                                <i class="fas fa-plus me-1"></i> Add Variant Row
                            </button>
                        </div>
                        <div class="cpm-card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0" id="cpmVariantsTable">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 150px;">Variant / Part Name</th>
                                            <th style="width: 85px;">Rating / Spec</th>
                                            <th style="width: 85px;">Model / Type</th>
                                            <th style="width: 75px;">Unit</th>
                                            <th style="width: 90px;" class="text-center">Initial Stock</th>
                                            <th style="width: 95px;" class="text-center cpm-conv-col">Pcs / Carton</th>
                                            <th style="width: 90px;" class="cpm-price-col">Sale Price</th>
                                            <th style="width: 90px;" class="cpm-wholesale-col">Wholesale</th>
                                            <th style="width: 90px;" class="cpm-purch-col">Purch Price</th>
                                            <th style="width: 60px;">Alert</th>
                                            <th style="width: 105px;">Barcode</th>
                                            <th style="width: 45px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cpmVariantsBody">
                                        <!-- Rows injected by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Hidden compatibility inputs --}}
                    <div style="display:none !important;">
                        <input type="number" name="height" id="cpm_height" value="0">
                        <input type="number" name="width" id="cpm_width" value="0">
                        <input type="number" name="price_per_m2" id="cpm_price_per_m2" value="0">
                        <input type="number" name="purchase_price_per_m2" id="cpm_purchase_price_per_m2" value="0">
                        <input type="number" name="piece_quantity" id="cpm_piece_quantity" value="0">
                        <input type="number" name="pieces_per_box" id="cpm_pieces_per_box" value="1">
                        <input type="number" name="boxes_quantity" id="cpm_boxes_quantity" value="0">
                        <input type="number" name="loose_pieces" id="cpm_loose_pieces" value="0">
                        <input type="number" name="sale_price_per_box" id="cpm_sale_price_per_box" value="0">
                        <input type="number" name="wholesale_price" id="cpm_wholesale_price" value="0">
                        <input type="number" name="weight_per_piece" id="cpm_weight_per_piece" value="0">
                        <input type="number" name="purchase_price_per_piece" id="cpm_purchase_price_per_piece" value="0">
                        <input type="number" name="alert_carton_quantity" id="cpm_alert_carton_quantity" value="0">
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.88rem;">
                        Cancel
                    </button>
                    <button type="submit" id="cpmSubmitBtn" class="btn btn-primary px-5 py-2 fw-bold" style="background: #4f46e5; border: none; border-radius: 8px; font-size: 0.88rem; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                        <i class="fas fa-check-circle me-1"></i> SAVE PRODUCT
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ── Quick Add Child Modals ── --}}
{{-- 1. Category Modal --}}
<div id="cpmCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form id="cpmQuickCategoryForm" action="{{ route('store.category') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">New Category</h6>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="page" value="product_page">
                    <div class="mb-3">
                        <label class="cpm-label">Category Name</label>
                        <input type="text" name="name" class="cpm-input" required placeholder="e.g. Heating Devices, Chillers, Raw Materials">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Subcategory Modal --}}
<div id="cpmSubcategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form id="cpmQuickSubcategoryForm" action="{{ route('store.subcategory') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">New Subcategory</h6>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="page" value="product_page">
                    <div class="mb-3">
                        <label class="cpm-label">Parent Category</label>
                        <select name="category_id" id="cpm_sub_parent_cat" class="cpm-select form-select" required>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="cpm-label">Subcategory Name</label>
                        <input type="text" name="name" class="cpm-input" required placeholder="e.g. Induction Coils, IGBT Modules">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Create Subcategory</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. Brand Modal --}}
<div id="cpmBrandModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form id="cpmQuickBrandForm" action="{{ route('store.Brand') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">New Brand / Manufacturer</h6>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="page" value="product_page">
                    <div class="mb-3">
                        <label class="cpm-label">Brand / Manufacturer Name</label>
                        <input type="text" name="name" class="cpm-input" required placeholder="e.g. LogicTech, Danfoss, Infineon">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Create Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Global Helper for Item Type Toggle ──
window.cpmSetItemType = function(type) {
    $('#cpm_item_type').val(type);
    if (type === 'raw_material') {
        $('#cpmTypeRawBtn').addClass('active-raw');
        $('#cpmTypeFinishBtn').removeClass('active-finish');
        $('#cpmHeaderTitle').html('Create Raw Material / Component');
        $('#cpmHeaderIcon').attr('class', 'fas fa-boxes-stacked text-warning');
    } else {
        $('#cpmTypeRawBtn').removeClass('active-raw');
        $('#cpmTypeFinishBtn').addClass('active-finish');
        $('#cpmHeaderTitle').html('Create Finished Goods / Product');
        $('#cpmHeaderIcon').attr('class', 'fas fa-microchip text-success');
    }
};

// ── Global Function to Open Modal ──
window.openCreateProductModal = function(type) {
    window.cpmSetItemType(type || 'raw_material');
    $('#createProductModal').modal('show');
};

$(document).ready(function() {
    const cpmForm = document.getElementById('cpmProductForm');
    const cpmUnitDropdown = document.getElementById('cpm_unit_dropdown');
    const cpmVariantsBody = document.getElementById('cpmVariantsBody');
    const cpmNameInput = document.getElementById('cpm_product_name');

    // ── Generate Random Barcode ──
    function cpmGenerateBarcode() {
        return Math.floor(100000 + Math.random() * 900000).toString();
    }

    // ── Image Upload Handling ──
    $('#cpmImageInput').on('change', function() {
        if (this.files && this.files[0]) {
            const r = new FileReader();
            r.onload = (e) => {
                $('#cpmImagePreview').attr('src', e.target.result).removeClass('d-none');
                $('#cpmUploadPlaceholder').addClass('d-none');
                $('#cpmClearImageBtn').removeClass('d-none');
            };
            r.readAsDataURL(this.files[0]);
        }
    });

    $('#cpmClearImageBtn').on('click', function(e) {
        e.stopPropagation();
        $('#cpmImageInput').val('');
        $('#cpmImagePreview').attr('src', '').addClass('d-none');
        $('#cpmUploadPlaceholder').removeClass('d-none');
        $('#cpmClearImageBtn').addClass('d-none');
    });

    // ── Add Base Variant Row ──
    function cpmAddBaseRow() {
        if (!cpmVariantsBody) return;
        const prodName = cpmNameInput ? cpmNameInput.value : '';
        const unitVal  = cpmUnitDropdown ? cpmUnitDropdown.options[cpmUnitDropdown.selectedIndex].text : 'Pcs';
        const isCarton = cpmUnitDropdown && cpmUnitDropdown.value === 'by_cartons';
        const vid      = 'base_' + Date.now();

        const tr = document.createElement('tr');
        tr.dataset.vid = vid;
        tr.innerHTML = `
            <td class="p-1">
                <input type="text" class="cpm-tbl-input cpm-base-name-input fw-bold" name="variant_name[]" value="${prodName}" placeholder="Name" required>
                <input type="hidden" name="variant_is_base[]" value="1">
            </td>
            <td class="p-1"><input type="text" class="cpm-tbl-input" name="variant_size[]" placeholder="Spec / Rating"></td>
            <td class="p-1"><input type="text" class="cpm-tbl-input" name="variant_color[]" placeholder="Model / Type"></td>
            <td class="p-1">
                <select class="cpm-tbl-select form-select fw-bold text-primary px-1" name="variant_unit[]" style="font-size:11px;">
                    <option value="Pcs" ${(!isCarton && (unitVal.includes('Pcs')||unitVal.includes('Pieces')))?'selected':''}>Pcs</option>
                    <option value="Carton" ${isCarton||unitVal.includes('Carton')?'selected':''}>Carton</option>
                    <option value="Kg" ${unitVal.includes('Kg')?'selected':''}>Kg</option>
                    <option value="Gm" ${unitVal.includes('Gm')?'selected':''}>Gm</option>
                    <option value="Ft" ${unitVal.includes('Ft')?'selected':''}>Ft</option>
                    <option value="Meter" ${unitVal.includes('Meter')?'selected':''}>Mtr</option>
                    <option value="Box">Box</option>
                    <option value="Dozen">Dzn</option>
                </select>
            </td>
            <td class="p-1">
                <input type="number" class="cpm-tbl-input text-center fw-bold text-primary" name="variant_stock[]" step="any" value="0" placeholder="0">
            </td>
            <td class="p-1 text-center cpm-conv-col">
                <input type="number" class="cpm-tbl-input text-center" name="variant_conv_factor[]" step="any" value="${isCarton ? '0' : '1'}" ${isCarton ? '' : 'readonly'} placeholder="1">
            </td>
            <td class="p-1"><input type="number" class="cpm-tbl-input cpm-base-sale" name="variant_sale_price[]" step="any" placeholder="0.00" value="0" required></td>
            <td class="p-1"><input type="number" class="cpm-tbl-input" name="variant_wholesale_price[]" step="any" placeholder="0.00" value="0"></td>
            <td class="p-1"><input type="number" class="cpm-tbl-input cpm-base-purch" name="variant_purchase_price[]" step="any" placeholder="0.00" value="0" required></td>
            <td class="p-1"><input type="number" class="cpm-tbl-input" name="variant_alert_qty[]" value="0" placeholder="0"></td>
            <td class="p-1"><input type="text" class="cpm-tbl-input" name="variant_barcode[]" value="${cpmGenerateBarcode()}"></td>
            <td class="p-1 text-center">
                <span class="badge bg-primary px-2 py-1 text-white" style="font-size:10px;">Base</span>
            </td>
        `;
        cpmVariantsBody.appendChild(tr);
    }

    // ── Add New Variant Row ──
    function cpmAddVariantRow() {
        if (!cpmVariantsBody) return;
        const prodName = cpmNameInput ? cpmNameInput.value : '';
        const unitVal  = cpmUnitDropdown ? cpmUnitDropdown.options[cpmUnitDropdown.selectedIndex].text : 'Pcs';
        const isCarton = cpmUnitDropdown && cpmUnitDropdown.value === 'by_cartons';
        const vid      = 'var_' + Date.now();

        const tr = document.createElement('tr');
        tr.dataset.vid = vid;
        tr.innerHTML = `
            <td class="p-1">
                <input type="text" class="cpm-tbl-input fw-semibold" name="variant_name[]" value="${prodName}" placeholder="Variant Name" required>
                <input type="hidden" name="variant_is_base[]" value="0">
            </td>
            <td class="p-1"><input type="text" class="cpm-tbl-input" name="variant_size[]" placeholder="Spec / Rating"></td>
            <td class="p-1"><input type="text" class="cpm-tbl-input" name="variant_color[]" placeholder="Model / Type"></td>
            <td class="p-1">
                <select class="cpm-tbl-select form-select fw-bold text-primary px-1" name="variant_unit[]" style="font-size:11px;">
                    <option value="Pcs" ${(!isCarton && (unitVal.includes('Pcs')||unitVal.includes('Pieces')))?'selected':''}>Pcs</option>
                    <option value="Carton" ${isCarton||unitVal.includes('Carton')?'selected':''}>Carton</option>
                    <option value="Kg" ${unitVal.includes('Kg')?'selected':''}>Kg</option>
                    <option value="Gm" ${unitVal.includes('Gm')?'selected':''}>Gm</option>
                    <option value="Ft" ${unitVal.includes('Ft')?'selected':''}>Ft</option>
                    <option value="Meter" ${unitVal.includes('Meter')?'selected':''}>Mtr</option>
                    <option value="Box">Box</option>
                    <option value="Dozen">Dzn</option>
                </select>
            </td>
            <td class="p-1">
                <input type="number" class="cpm-tbl-input text-center fw-bold text-primary" name="variant_stock[]" step="any" value="0" placeholder="0">
            </td>
            <td class="p-1 text-center cpm-conv-col">
                <input type="number" class="cpm-tbl-input text-center" name="variant_conv_factor[]" step="any" value="${isCarton ? '0' : '1'}" ${isCarton ? '' : 'readonly'} placeholder="1">
            </td>
            <td class="p-1"><input type="number" class="cpm-tbl-input" name="variant_sale_price[]" step="any" placeholder="0.00" value="0" required></td>
            <td class="p-1"><input type="number" class="cpm-tbl-input" name="variant_wholesale_price[]" step="any" placeholder="0.00" value="0"></td>
            <td class="p-1"><input type="number" class="cpm-tbl-input" name="variant_purchase_price[]" step="any" placeholder="0.00" value="0" required></td>
            <td class="p-1"><input type="number" class="cpm-tbl-input" name="variant_alert_qty[]" value="0" placeholder="0"></td>
            <td class="p-1"><input type="text" class="cpm-tbl-input" name="variant_barcode[]" value="${cpmGenerateBarcode()}"></td>
            <td class="p-1 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;border-radius:6px;" onclick="$(this).closest('tr').remove()" title="Remove Variant">
                    <i class="fas fa-trash-alt" style="font-size:11px;"></i>
                </button>
            </td>
        `;
        cpmVariantsBody.appendChild(tr);
    }

    // Bind Add Variant Button
    $('#cpmAddVariantRowBtn').on('click', function() {
        cpmAddVariantRow();
    });

    // Sync Base Row with Product Name
    $('#cpm_product_name').on('input', function() {
        const baseNameInp = document.querySelector('.cpm-base-name-input');
        if (baseNameInp) {
            baseNameInp.value = this.value;
        }
    });

    // Dependent Subcategory Fetch
    $('#cpm_category_id').on('change', function() {
        const cid = $(this).val();
        $('#cpm_sub_parent_cat').val(cid);
        if (cid) {
            $.get('/get-subcategories/' + cid, function(d) {
                $('#cpm_sub_category_id').empty().append('<option value="">Select...</option>');
                $.each(d, function(_, v) {
                    $('#cpm_sub_category_id').append('<option value="' + v.id + '">' + v.name + '</option>');
                });
            });
        } else {
            $('#cpm_sub_category_id').empty().append('<option value="">Select...</option>');
        }
    });

    // Initialize Base Row on Modal Open
    $('#createProductModal').on('show.bs.modal', function() {
        if ($('#cpmVariantsBody tr').length === 0) {
            cpmAddBaseRow();
        }
    });

    // Reset Form on Modal Hidden
    $('#createProductModal').on('hidden.bs.modal', function() {
        if (cpmForm) cpmForm.reset();
        $('#cpmVariantsBody').empty();
        $('#cpmImagePreview').attr('src', '').addClass('d-none');
        $('#cpmUploadPlaceholder').removeClass('d-none');
        $('#cpmClearImageBtn').addClass('d-none');
        cpmAddBaseRow();
    });

    // ── Quick Add Handlers for Category, Subcategory, Brand ──
    function bindQuickModal(formId, modalId, targetSelect, parentUpdateSelect = null) {
        $('#' + formId).on('submit', function(e) {
            e.preventDefault();
            const f = $(this);
            const btn = f.find('button[type="submit"]');
            const orig = btn.text();
            btn.text('Creating...').prop('disabled', true);

            $.ajax({
                url: f.attr('action'),
                method: 'POST',
                data: f.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    if (res.success || res.status === 'success') {
                        const newId = res.id;
                        const newName = res.name;
                        $(targetSelect).append(new Option(newName, newId, true, true)).trigger('change');
                        if (parentUpdateSelect) {
                            $(parentUpdateSelect).append(new Option(newName, newId, false, false));
                        }
                        $('#' + modalId).modal('hide');
                        f[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Created successfully',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1800
                        });
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? (xhr.responseJSON.error || xhr.responseJSON.message || 'Error creating item') : 'Error occurred';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                },
                complete: function() {
                    btn.text(orig).prop('disabled', false);
                }
            });
        });
    }

    bindQuickModal('cpmQuickCategoryForm', 'cpmCategoryModal', '#cpm_category_id', '#cpm_sub_parent_cat');
    bindQuickModal('cpmQuickSubcategoryForm', 'cpmSubcategoryModal', '#cpm_sub_category_id');
    bindQuickModal('cpmQuickBrandForm', 'cpmBrandModal', '#cpm_brand_id');

    // Maintain scrollability on parent modal when child modal closes
    $('#cpmCategoryModal, #cpmSubcategoryModal, #cpmBrandModal').on('hidden.bs.modal', function() {
        if ($('#createProductModal').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });

    // ── AJAX Submit Main Form ──
    $('#cpmProductForm').on('submit', function(e) {
        e.preventDefault();

        // Sync variants data to compatibility fields
        const vStocks = document.querySelectorAll('#cpmVariantsTable input[name="variant_stock[]"]');
        const vSale = document.querySelectorAll('#cpmVariantsTable input[name="variant_sale_price[]"]');
        const vWholesale = document.querySelectorAll('#cpmVariantsTable input[name="variant_wholesale_price[]"]');
        const vPurch = document.querySelectorAll('#cpmVariantsTable input[name="variant_purchase_price[]"]');
        const vAlert = document.querySelectorAll('#cpmVariantsTable input[name="variant_alert_qty[]"]');

        let totalStock = 0;
        vStocks.forEach(el => totalStock += (parseFloat(el.value) || 0));

        let firstSale = vSale.length > 0 ? (parseFloat(vSale[0].value) || 0) : 0;
        let firstWholesale = vWholesale.length > 0 ? (parseFloat(vWholesale[0].value) || 0) : 0;
        let firstPurch = vPurch.length > 0 ? (parseFloat(vPurch[0].value) || 0) : 0;
        let firstAlert = vAlert.length > 0 ? (parseFloat(vAlert[0].value) || 0) : 0;

        const mode = cpmUnitDropdown ? cpmUnitDropdown.value : 'by_pieces';
        if (mode === 'by_cartons') {
            document.getElementById('cpm_boxes_quantity').value = totalStock;
            document.getElementById('cpm_pieces_per_box').value = 1;
            document.getElementById('cpm_loose_pieces').value = 0;
            document.getElementById('cpm_piece_quantity').value = 0;
        } else {
            document.getElementById('cpm_piece_quantity').value = totalStock;
            document.getElementById('cpm_boxes_quantity').value = 0;
        }

        document.getElementById('cpm_sale_price_per_box').value = firstSale;
        document.getElementById('cpm_wholesale_price').value = firstWholesale;
        document.getElementById('cpm_purchase_price_per_piece').value = firstPurch;
        document.getElementById('cpm_alert_carton_quantity').value = firstAlert;

        const submitBtn = $('#cpmSubmitBtn');
        const origContent = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...').prop('disabled', true);

        const formData = new FormData(cpmForm);

        fetch(cpmForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(r => r.json().then(data => ({ status: r.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 || body.status === 'success') {
                $('#createProductModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Saved Successfully!',
                    text: 'Product / component profile created.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                let msg = 'Validation error occurred';
                if (body.errors) {
                    msg = Object.values(body.errors).flat().join('<br>');
                } else if (body.message) {
                    msg = body.message;
                }
                Swal.fire({ icon: 'error', title: 'Error', html: msg });
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Server Error', text: 'An unexpected error occurred while saving.' });
        })
        .finally(() => {
            submitBtn.html(origContent).prop('disabled', false);
        });
    });

});
</script>
@endpush
