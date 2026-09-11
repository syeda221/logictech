{{-- ===== QUICK ADD PRODUCT / COMPONENT MODAL ===== --}}
@php
    $qapCategories = \App\Models\Category::orderBy('name')->get(['id', 'name']);
    $qapBrands = \App\Models\Brand::orderBy('name')->get(['id', 'name']);
    $qapSubcategories = \App\Models\Subcategory::orderBy('name')->get(['id', 'name', 'category_id']);
@endphp
<div class="modal fade" id="quickAddProductModal" tabindex="-1" aria-labelledby="quickAddProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 pb-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold mb-0" id="quickAddProductModalLabel">
                        <i class="fas fa-microchip text-success me-2" id="qap_header_icon"></i>
                        <span id="qap_header_title">Quick Add Finished Goods</span>
                    </h5>
                    <span class="badge" id="qap_header_badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-size:11px;">
                        <i class="fas fa-bolt me-1"></i>Made to Order
                    </span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddProductForm">
                @csrf
                <div class="modal-body pt-2">
                    <div class="row g-3">
                        {{-- ── Item Classification: Finished Goods vs Raw Material ── --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted mb-1 d-flex justify-content-between">
                                <span><i class="fas fa-layer-group text-primary me-1"></i>Item Classification / Type <span class="text-danger">*</span></span>
                                <span class="text-muted fw-normal" style="font-size:0.75rem;">Finished Goods (Sale) vs Raw Material (Stock)</span>
                            </label>
                            <div class="btn-group w-100 p-1 bg-light rounded-3 border" role="group" aria-label="Item Classification">
                                <input type="radio" class="btn-check" name="item_type" id="qap_type_fg" value="finish_goods" checked autocomplete="off">
                                <label class="btn btn-sm btn-outline-success rounded-2 py-2 fw-bold text-dark d-flex align-items-center justify-content-center gap-2 w-100" for="qap_type_fg" id="qap_label_fg">
                                    <i class="fas fa-microchip text-success"></i>
                                    <span>Finished Goods (Sale / MTO)</span>
                                </label>

                                <input type="radio" class="btn-check d-none" name="item_type" id="qap_type_rm" value="raw_material" autocomplete="off">
                                <label class="btn btn-sm btn-outline-warning rounded-2 py-2 fw-bold text-dark d-flex align-items-center justify-content-center gap-2 d-none" for="qap_type_rm" id="qap_label_rm" style="display: none !important;">
                                    <i class="fas fa-boxes-stacked text-warning"></i>
                                    <span>Raw Material (Purchase / Stock)</span>
                                </label>
                            </div>
                        </div>

                        {{-- ── Notice for Finished Goods (Made to Order) ── --}}
                        <div class="col-12" id="qap_fg_notice">
                            <div class="p-2 rounded-2 border d-flex align-items-center gap-2 small" style="background: #f0fdf4; border-color: #bbf7d0 !important; color: #166534;">
                                <i class="fas fa-info-circle text-success fs-6 flex-shrink-0"></i>
                                <div>
                                    <strong>Finished Goods (Make-to-Order):</strong> Initial stock &amp; purchase cost are not required (Stock = 0). Only item details and Sale Price are needed.
                                </div>
                            </div>
                        </div>

                        {{-- ── Product Name ── --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted" id="qap_name_label">Finished Goods / Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold" name="product_name" id="qap_product_name" required placeholder="e.g. High Frequency Induction Heater 25kW, Industrial Air Chiller 5TR">
                        </div>

                        {{-- ── Category & Subcategory ── --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="qap_category" required>
                                <option value="">Select Category</option>
                                @foreach($qapCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Sub Category</label>
                            <select class="form-select" name="sub_category_id" id="qap_subcategory">
                                <option value="">Select Sub Category</option>
                                @foreach($qapSubcategories as $sub)
                                    <option value="{{ $sub->id }}" data-category-id="{{ $sub->category_id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ── Brand & Model ── --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Manufacturer / Brand <span class="text-danger">*</span></label>
                            <select class="form-select" name="brand_id" id="qap_brand" required>
                                <option value="">Select Brand</option>
                                @foreach($qapBrands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Model / Part No. / Spec</label>
                            <input type="text" class="form-control" name="model" id="qap_model" placeholder="e.g. LT-IND-2026, 3-Phase 380V">
                        </div>

                        {{-- ── Unit of Measure ── --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Unit of Measure <span class="text-danger">*</span></label>
                            <select class="form-select fw-semibold" name="size_mode" id="qap_size_mode" required>
                                <option value="by_pieces" selected>By Pieces / Units / Sets</option>
                                <option value="by_cartons">By Boxes / Bulk Packaging</option>
                                <option value="by_meter">By Meter (Mtr)</option>
                                <option value="by_feet">By Feet (Ft)</option>
                                <option value="by_kg">By Kilogram (Kg)</option>
                            </select>
                        </div>

                        {{-- ── Sale Price (Primary for Sale / Finished Goods) ── --}}
                        <div class="col-md-6" id="qap_sale_wrap">
                            <label class="form-label fw-bold small text-muted">Sale Price (Rs. / Unit) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control border-success text-success fw-bold" name="sale_price_per_box" id="qap_sale_price" value="0" required placeholder="0.00">
                        </div>

                        {{-- ── Purchase Cost (Only for Raw Material) ── --}}
                        <div class="col-md-6" id="qap_purch_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">Purchase Cost (Rs. / Unit)</label>
                            <input type="number" step="0.01" class="form-control" name="purchase_price_per_piece" id="qap_purch_price" value="0" placeholder="0.00">
                        </div>

                        {{-- ── Low Stock Alert (Only for Raw Material) ── --}}
                        <div class="col-md-6" id="qap_alert_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">Low Stock Alert (Units)</label>
                            <input type="number" class="form-control" name="alert_carton_quantity" id="qap_alert_qty" min="0" value="0" placeholder="e.g. 5">
                        </div>

                        {{-- ── Initial Stock Quantities (Only for Raw Material) ── --}}
                        <div class="col-md-6" id="qap_ppb_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">Pieces Per Box</label>
                            <input type="number" class="form-control" name="pieces_per_box" id="qap_ppb" value="1" min="1" placeholder="e.g. 12">
                        </div>
                        <div class="col-md-6" id="qap_boxes_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">In-Stock Packaging (Boxes)</label>
                            <input type="number" class="form-control border-primary text-primary fw-bold" name="boxes_quantity" id="qap_boxes_quantity" value="0" placeholder="0">
                        </div>
                        <div class="col-md-6" id="qap_loose_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">Loose Units (Extra)</label>
                            <input type="number" class="form-control border-warning" name="loose_pieces" id="qap_loose_pieces" value="0" placeholder="0">
                        </div>
                        <div class="col-md-12" id="qap_pieces_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">Initial Stock Quantity (Units / Pieces)</label>
                            <input type="number" class="form-control border-primary text-primary fw-bold" name="piece_quantity" id="qap_piece_quantity" value="0" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold" id="btnQuickSaveProduct">
                        <i class="fas fa-save me-1"></i>Save Finished Goods
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    function initQuickAddProduct() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof jQuery !== 'undefined') {
                    initQuickAddProduct();
                } else {
                    window.addEventListener('load', initQuickAddProduct);
                }
            });
            return;
        }

        $(function() {
            // Function to ensure dropdowns are populated
            function refreshQapDropdowns() {
                var $catSelect = $('#qap_category');
                var $brandSelect = $('#qap_brand');
                var $subCatSelect = $('#qap_subcategory');

                // Load categories if empty
                if ($catSelect.find('option').length <= 1) {
                    $.get("{{ url('/get-categories') }}", function(data) {
                        (data || []).forEach(function(cat) {
                            if ($catSelect.find('option[value="'+ cat.id +'"]').length === 0) {
                                $catSelect.append('<option value="'+ cat.id +'">'+ cat.name +'</option>');
                            }
                        });
                    }).fail(function() {
                        console.error('Failed to load categories');
                    });
                }

                // Load brands if empty
                if ($brandSelect.find('option').length <= 1) {
                    $.get("{{ url('/get-brands') }}", function(data) {
                        (data || []).forEach(function(brand) {
                            if ($brandSelect.find('option[value="'+ brand.id +'"]').length === 0) {
                                $brandSelect.append('<option value="'+ brand.id +'">'+ brand.name +'</option>');
                            }
                        });
                    }).fail(function() {
                        console.error('Failed to load brands');
                    });
                }

                // Load all subcategories if empty
                if ($subCatSelect.find('option').length <= 1) {
                    $.get("{{ url('/get-all-subcategories') }}", function(data) {
                        (data || []).forEach(function(sub) {
                            if ($subCatSelect.find('option[value="'+ sub.id +'"]').length === 0) {
                                $subCatSelect.append('<option value="'+ sub.id +'" data-category-id="'+ (sub.category_id || '') +'">'+ sub.name +'</option>');
                            }
                        });
                    }).fail(function() {
                        console.error('Failed to load subcategories');
                    });
                }
            }

            // ── Dynamic Item Type Switcher (Finished Goods vs Raw Material) ──
            function updateQapItemType(type) {
                if (type === 'finish_goods') {
                    $('#qap_type_fg').prop('checked', true);
                    $('#qap_label_rm').addClass('d-none').attr('style', 'display: none !important;');
                    $('#qap_label_fg').removeClass('d-none').addClass('w-100').show();
                    $('#qap_header_icon').attr('class', 'fas fa-microchip text-success me-2');
                    $('#qap_header_title').text('Quick Add Finished Goods');
                    $('#qap_header_badge').html('<i class="fas fa-bolt me-1"></i>Made to Order')
                        .css({'background': '#ecfdf5', 'color': '#059669', 'border-color': '#a7f3d0'});
                    $('#qap_name_label').html('Finished Goods / Product Name <span class="text-danger">*</span>');
                    $('#qap_product_name').attr('placeholder', 'e.g. High Frequency Induction Heater 25kW, Industrial Air Chiller 5TR');
                    $('#qap_fg_notice').show();

                    // Hide purchase, alert, and stock fields for Finished Goods (Made to Order)
                    $('#qap_purch_wrap').hide();
                    $('#qap_purch_price').val(0);
                    $('#qap_alert_wrap').hide();
                    $('#qap_alert_qty').val(0);
                    $('#qap_ppb_wrap, #qap_boxes_wrap, #qap_loose_wrap, #qap_pieces_wrap').hide();
                    $('#qap_piece_quantity, #qap_boxes_quantity, #qap_loose_pieces').val(0);

                    // Update button
                    $('#btnQuickSaveProduct')
                        .removeClass('btn-primary btn-warning')
                        .addClass('btn-success')
                        .html('<i class="fas fa-save me-1"></i>Save Finished Goods');
                } else {
                    $('#qap_type_rm').prop('checked', true);
                    $('#qap_header_icon').attr('class', 'fas fa-boxes-stacked text-warning me-2');
                    $('#qap_header_title').text('Quick Add Raw Material / Component');
                    $('#qap_header_badge').html('<i class="fas fa-boxes-stacked me-1"></i>Raw Material')
                        .css({'background': '#fef3c7', 'color': '#b45309', 'border-color': '#fde68a'});
                    $('#qap_name_label').html('Raw Material / Component Name <span class="text-danger">*</span>');
                    $('#qap_product_name').attr('placeholder', 'e.g. IGBT Module 1200V, Copper Pipe 1/2", Stainless Steel Sheet, Relay 24V');
                    $('#qap_fg_notice').hide();

                    // Show purchase, alert, and stock fields for Raw Material
                    $('#qap_purch_wrap').show();
                    $('#qap_alert_wrap').show();

                    // Show stock fields based on size mode
                    handleQapSizeMode();

                    // Update button
                    $('#btnQuickSaveProduct')
                        .removeClass('btn-success')
                        .addClass('btn-primary')
                        .html('<i class="fas fa-save me-1"></i>Save Raw Material');
                }
            }

            // ── Stock Fields visibility based on Size Mode (Raw Material only) ──
            function handleQapSizeMode() {
                var isFg = $('input[name="item_type"]:checked').val() === 'finish_goods';
                if (isFg) {
                    $('#qap_ppb_wrap, #qap_boxes_wrap, #qap_loose_wrap, #qap_pieces_wrap').hide();
                    return;
                }

                var mode = $('#qap_size_mode').val();
                if (mode === 'by_cartons') {
                    $('#qap_ppb_wrap').show();
                    $('#qap_boxes_wrap').show();
                    $('#qap_loose_wrap').show();
                    $('#qap_pieces_wrap').hide();
                } else {
                    $('#qap_ppb_wrap').hide();
                    $('#qap_boxes_wrap').hide();
                    $('#qap_loose_wrap').hide();
                    $('#qap_pieces_wrap').show();
                    $('#qap_ppb').val(1);
                }
            }

            $('#qap_size_mode').on('change', handleQapSizeMode);
            $('input[name="item_type"]').on('change', function() {
                updateQapItemType($(this).val());
            });

            // Trigger dropdown refresh immediately and context detection on modal show
            refreshQapDropdowns();

            $(document).on('show.bs.modal shown.bs.modal', '#quickAddProductModal', function() {
                refreshQapDropdowns();

                // Detect screen context
                var isSaleScreen = $('#salesTableBody').length > 0;
                var isPurchaseScreen = $('#purchaseTableBody').length > 0;

                if (isSaleScreen) {
                    updateQapItemType('finish_goods');
                } else if (isPurchaseScreen) {
                    updateQapItemType('raw_material');
                } else {
                    var currentChecked = $('input[name="item_type"]:checked').val() || 'finish_goods';
                    updateQapItemType(currentChecked);
                }
            });

            // Load subcategories when category changes
            $('#qap_category').on('change', function() {
                var categoryId = $(this).val();
                var $subCatSelect = $('#qap_subcategory');
                $subCatSelect.val('');

                if (!categoryId) {
                    $subCatSelect.find('option').show();
                } else {
                    var hasMatchingOption = false;
                    $subCatSelect.find('option').each(function() {
                        var catId = $(this).attr('data-category-id');
                        if (!$(this).val()) {
                            $(this).show();
                        } else if (catId == categoryId) {
                            $(this).show();
                            hasMatchingOption = true;
                        } else {
                            $(this).hide();
                        }
                    });

                    // Fetch dynamically via AJAX if no matching options preloaded
                    if (!hasMatchingOption) {
                        $.get("{{ url('/get-subcategories') }}/" + categoryId, function(data) {
                            (data || []).forEach(function(sub) {
                                if ($subCatSelect.find('option[value="'+ sub.id +'"]').length === 0) {
                                    $subCatSelect.append('<option value="'+ sub.id +'" data-category-id="'+ categoryId +'">'+ sub.name +'</option>');
                                }
                            });
                        });
                    }
                }
            });

            // Submit Quick Add Product
            $('#quickAddProductForm').on('submit', function(e) {
                e.preventDefault();
                var $btn = $('#btnQuickSaveProduct');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

                // Ensure stock is 0 for finished goods before serializing
                if ($('input[name="item_type"]:checked').val() === 'finish_goods') {
                    $('#qap_piece_quantity').val(0);
                    $('#qap_boxes_quantity').val(0);
                    $('#qap_loose_pieces').val(0);
                    $('#qap_purch_price').val(0);
                }

                $.ajax({
                    url: "{{ route('store-product') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        $('#quickAddProductForm')[0].reset();
                        
                        var modalEl = document.getElementById('quickAddProductModal');
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var inst = bootstrap.Modal.getInstance(modalEl);
                            if (inst) inst.hide();
                            else $(modalEl).modal('hide');
                        } else {
                            $('#quickAddProductModal').modal('hide');
                        }

                        // Auto-fill into sales or purchase table with the new product
                        if (response.product && response.product.id) {
                            var prod = response.product;
                            var prodName = prod.item_name || prod.text || prod.name;
                            var prodId = prod.id;

                            // ── Sales Screen Auto-fill ──
                            if ($('#salesTableBody').length) {
                                let $targetRow = $('#salesTableBody tr:not(.specs-subrow):last');
                                const lastProdVal = $targetRow.find('.product').val();

                                if (!$targetRow.length || lastProdVal) {
                                    if (typeof addNewRow === 'function') {
                                        addNewRow();
                                    } else {
                                        $('#btnAdd').trigger('click');
                                    }
                                    $targetRow = $('#salesTableBody tr:not(.specs-subrow):last');
                                }

                                const $select = $targetRow.find('.product');
                                const optText = prodName + (prod.item_code ? ' (SKU: ' + prod.item_code + ')' : '');

                                const selectData = {
                                    id: prod.id,
                                    text: optText,
                                    name: prod.item_name || prodName,
                                    item_name: prod.item_name || prodName,
                                    sku: prod.item_code || '',
                                    model: prod.model || $('#qap_model').val() || prod.brand || '',
                                    brand: prod.brand || '',
                                    size_mode: prod.size_mode || 'by_pieces',
                                    pieces_per_box: prod.pieces_per_box || 1,
                                    retail_price: prod.sale_price || prod.retail_price || 0,
                                    trade_price: prod.purchase_price_per_piece || 0,
                                    wholesale_price: prod.wholesale_price || 0,
                                    unit_name: prod.unit_name || 'Pcs',
                                    stock: prod.stock || 'Made to Order',
                                    variant_data: ''
                                };

                                const newOpt = new Option(optText, prod.id, true, true);
                                $(newOpt).data('data', selectData);
                                $select.append(newOpt);

                                // Explicitly fill model input
                                if (selectData.model) {
                                    $targetRow.find('.model-input').val(selectData.model);
                                }

                                // Trigger select2 selection
                                $select.trigger({
                                    type: 'select2:select',
                                    params: { data: selectData }
                                });
                                $select.trigger('change');

                                // Fetch price & details if helper exists
                                if (typeof fetchProductPrice === 'function') {
                                    fetchProductPrice($targetRow, prod.id);
                                }
                            } 
                            // ── Purchase Screen Auto-fill ──
                            else if ($('#purchaseTableBody').length) {
                                var $rows = $('#purchaseTableBody tr');
                                var $targetRow = $rows.last();

                                var lastRowProductId = $targetRow.find('.product-select2').val();
                                if (lastRowProductId) {
                                    $('#btnAdd').trigger('click');
                                    $targetRow = $('#purchaseTableBody tr').last();
                                }

                                var $select = $targetRow.find('.product-select2');
                                var newOpt = new Option(prod.text || prod.item_name, prod.id, true, true);
                                $(newOpt).data('data', {
                                    id: prod.id,
                                    text: prod.text || prod.item_name,
                                    name: prod.item_name,
                                    item_name: prod.item_name,
                                    sku: prod.item_code,
                                    size_mode: prod.size_mode || 'by_pieces',
                                    pieces_per_box: prod.pieces_per_box || 1,
                                    unit_name: prod.unit_name || 'Pcs',
                                    purchase_price_per_piece: prod.purchase_price_per_piece || 0,
                                    trade_price: prod.trade_price || 0,
                                    stock: 0,
                                    stock_pieces: 0,
                                    variant_data: ''
                                });
                                $select.append(newOpt).trigger('change');

                                $targetRow.find('.hidden-size-mode').val(prod.size_mode || 'by_pieces');
                                $targetRow.find('.hidden-pieces-per-box').val(prod.pieces_per_box || 1);
                                $targetRow.find('.price').val(prod.purchase_price_per_piece || 0);
                                $targetRow.find('.unit-toggle-btn').text('Pcs').attr('data-unit', 'Pcs');
                                $targetRow.find('.unit-input-val').val('Pcs');

                                if (typeof recalcRow === 'function') recalcRow($targetRow);
                                if (typeof recalcAll === 'function') recalcAll();
                                $targetRow.find('.main-qty-input').focus().select();
                            }
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Product Added!',
                                text: response.message || 'Product created successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert('Product Added successfully!');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        var msg = 'Error adding product.';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', msg, 'error');
                        } else {
                            alert('Error: ' + msg);
                        }
                    }
                });
            });
        });
    }

    initQuickAddProduct();
})();
</script>
