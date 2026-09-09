{{-- ===== QUICK ADD PRODUCT / COMPONENT MODAL ===== --}}
<div class="modal fade" id="quickAddProductModal" tabindex="-1" aria-labelledby="quickAddProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 pb-2">
                <h5 class="modal-title fw-bold" id="quickAddProductModalLabel">
                    <i class="fas fa-microchip text-primary me-2"></i>Quick Add Material / Component
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddProductForm">
                @csrf
                <input type="hidden" name="item_type" value="raw_material">
                <div class="modal-body pt-2">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Item / Component Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" required placeholder="e.g. Induction Heating Coil 25kW, IGBT Module, Air Chiller 5TR">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="qap_category" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Sub Category</label>
                            <select class="form-select" name="sub_category_id" id="qap_subcategory">
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Manufacturer / Brand <span class="text-danger">*</span></label>
                            <select class="form-select" name="brand_id" id="qap_brand" required>
                                <option value="">Select Brand</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Model / Part No. / Spec</label>
                            <input type="text" class="form-control" name="model" placeholder="e.g. LT-IND-2026, 3-Phase 380V">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Unit of Measure <span class="text-danger">*</span></label>
                            <select class="form-select" name="size_mode" id="qap_size_mode" required>
                                <option value="by_pieces" selected>By Pieces / Units / Sets</option>
                                <option value="by_cartons">By Boxes / Bulk Packaging</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="qap_ppb_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">Pieces Per Box</label>
                            <input type="number" class="form-control" name="pieces_per_box" id="qap_ppb" value="1" min="1" placeholder="e.g. 12">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Low Stock Alert (Units)</label>
                            <input type="number" class="form-control" name="alert_carton_quantity" min="0" placeholder="e.g. 5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Purchase Cost (Rs. / Unit)</label>
                            <input type="number" step="0.01" class="form-control" name="purchase_price_per_piece" value="0" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Sale Price (Rs. / Unit)</label>
                            <input type="number" step="0.01" class="form-control" name="sale_price_per_box" value="0" placeholder="0.00">
                        </div>
                        <div class="col-md-6" id="qap_boxes_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">In-Stock Packaging (Boxes)</label>
                            <input type="number" class="form-control border-primary text-primary fw-bold" name="boxes_quantity" id="qap_boxes_quantity" value="0" placeholder="0">
                        </div>
                        <div class="col-md-6" id="qap_loose_wrap" style="display: none;">
                            <label class="form-label fw-bold small text-muted">Loose Units (Extra)</label>
                            <input type="number" class="form-control border-warning" name="loose_pieces" id="qap_loose_pieces" value="0" placeholder="0">
                        </div>
                        <div class="col-md-12" id="qap_pieces_wrap">
                            <label class="form-label fw-bold small text-muted">Initial Stock Quantity (Units / Pieces)</label>
                            <input type="number" class="form-control border-primary text-primary fw-bold" name="piece_quantity" id="qap_piece_quantity" value="0" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnQuickSaveProduct">
                        <i class="fas fa-save me-1"></i>Save to Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle fields based on size mode
    $('#qap_size_mode').on('change', function() {
        if ($(this).val() === 'by_pieces') {
            $('#qap_ppb_wrap').hide();
            $('#qap_boxes_wrap').hide();
            $('#qap_loose_wrap').hide();
            $('#qap_pieces_wrap').show();
            $('#qap_ppb').val(1);
        } else {
            $('#qap_ppb_wrap').show();
            $('#qap_boxes_wrap').show();
            $('#qap_loose_wrap').show();
            $('#qap_pieces_wrap').hide();
        }
    });

    // Load categories, brands, and subcategories immediately
    var $catSelect = $('#qap_category');
    var $brandSelect = $('#qap_brand');
    var $subCatSelect = $('#qap_subcategory');

    // Load categories if empty
    if ($catSelect.find('option').length <= 1) {
        $.get("{{ url('/get-categories') }}", function(data) {
            (data || []).forEach(function(cat) {
                $catSelect.append('<option value="'+ cat.id +'">'+ cat.name +'</option>');
            });
        }).fail(function() {
            console.error('Failed to load categories');
        });
    }

    // Load brands if empty
    if ($brandSelect.find('option').length <= 1) {
        $.get("{{ url('/get-brands') }}", function(data) {
            (data || []).forEach(function(brand) {
                $brandSelect.append('<option value="'+ brand.id +'">'+ brand.name +'</option>');
            });
        }).fail(function() {
            console.error('Failed to load brands');
        });
    }

    // Load all subcategories initially if empty
    if ($subCatSelect.find('option').length <= 1) {
        $.get("{{ url('/get-all-subcategories') }}", function(data) {
            (data || []).forEach(function(sub) {
                $subCatSelect.append('<option value="'+ sub.id +'">'+ sub.name +'</option>');
            });
        }).fail(function() {
            console.error('Failed to load subcategories');
        });
    }

    // Load subcategories when category changes
    $('#qap_category').on('change', function() {
        var categoryId = $(this).val();
        var $subCatSelect = $('#qap_subcategory');
        $subCatSelect.html('<option value="">Select Sub Category</option>');
        
        if (categoryId) {
            $.get("{{ url('/get-subcategories') }}/" + categoryId, function(data) {
                (data || []).forEach(function(sub) {
                    $subCatSelect.append('<option value="'+ sub.id +'">'+ sub.name +'</option>');
                });
            });
        }
    });

    // Submit Quick Add Product
    $('#quickAddProductForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnQuickSaveProduct');
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

        $.ajax({
            url: "{{ route('store-product') }}",
            method: "POST",
            data: $(this).serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(response) {
                $btn.prop('disabled', false).html(originalHtml);
                $('#quickAddProductForm')[0].reset();
                $('#quickAddProductModal').modal('hide');

                // ✅ Auto-fill into sales or purchase table with the new product
                if (response.product && response.product.id) {
                    var prod = response.product;
                    var prodName = prod.item_name || prod.text || prod.name;
                    var prodId = prod.id;

                    // Sales Screen
                    if ($('#salesTableBody').length) {
                        let $targetRow = $('#salesTableBody tr:last');
                        const lastProdVal = $targetRow.find('.product').val();

                        if (!$targetRow.length || lastProdVal) {
                            if (typeof addNewRow === 'function') {
                                addNewRow();
                            } else {
                                $('#btnAdd').trigger('click');
                            }
                            $targetRow = $('#salesTableBody tr:last');
                        }

                        const $select = $targetRow.find('.product');
                        if ($select.find(`option[value="${prodId}"]`).length === 0) {
                            $select.append(new Option(prodName, prodId, true, true));
                        } else {
                            $select.val(prodId).trigger('change.select2');
                        }
                        $select.trigger('change');
                    } 
                    // Purchase Screen
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
</script>
