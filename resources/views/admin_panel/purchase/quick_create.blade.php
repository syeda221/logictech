@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Modern Clean ERP Styling */
    .premium-card {
        border: none !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
        background-color: #ffffff;
        margin-bottom: 20px;
    }
    .card-header-premium {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 12px 12px 0 0 !important;
        padding: 14px 20px;
        font-weight: 700;
        color: #1e293b;
    }
    .summary-card {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }
    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 5px;
    }
    .summary-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
    }
    .form-control, .form-select {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 12px;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .premium-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .premium-table thead th {
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 12px 15px;
        border-bottom: 2px solid #e2e8f0;
    }
    .premium-table tbody td {
        padding: 10px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }
    .btn-premium-primary {
        background-color: #2563eb;
        border: none;
        color: #ffffff;
        font-weight: 600;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.2s;
    }
    .btn-premium-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }
    /* Select2 overrides for modern look */
    .select2-container--bootstrap4 .select2-selection {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: calc(1.5em + .75rem + 2px) !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + .75rem) !important;
        padding-left: 12px !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem) !important;
    }

    /* Z-Index Stacking to prevent dropdown clipping */
    .card-section-1 {
        position: relative;
        z-index: 5;
        overflow: visible !important;
    }
    .card-section-2 {
        position: relative;
        z-index: 4;
    }
    .card-section-3 {
        position: relative;
        z-index: 3;
    }

    /* Clean Floating Product Autocomplete Dropdown */
    .product-autocomplete-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        z-index: 99999 !important;
        background: #ffffff;
        border: 1px solid #94a3b8;
        border-radius: 8px;
        box-shadow: 0 14px 35px rgba(0, 0, 0, 0.22) !important;
        display: none;
        overflow: hidden;
    }
    .product-suggest-item {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .product-suggest-item:hover {
        background-color: #e0f2fe;
    }
    .product-suggest-item:last-child {
        border-bottom: none;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0 text-dark"><i class="fas fa-bolt text-primary me-2"></i>LogicTech — Quick Purchase Entry</h4>
                    <p class="text-muted mb-0">Record heating/cooling equipment, electronic components, materials, specifications & payments rapidly.</p>
                </div>
                <div>
                    <a href="{{ route('Purchase.home') }}" class="btn btn-outline-secondary px-4 fw-medium shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                </div>
            </div>

            <form action="{{ route('purchase.quick_store') }}" method="POST" id="quickPurchaseForm">
                @csrf
                
                <!-- Hidden Product ID Field -->
                <input type="hidden" name="product_id" id="product_id" value="">

                <!-- 1. Purchase & Product Details -->
                <div class="card premium-card card-section-1">
                    <div class="card-header-premium d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle me-2 text-primary"></i> 1. Material / Component &amp; Vendor Details
                        </div>
                        <div id="productLinkedBadgeContainer" style="display: none;">
                            <span class="badge bg-success px-3 py-2" style="font-size: 0.82rem;">
                                <i class="fas fa-link me-1"></i> Linked: <strong id="badgeProductName">-</strong>
                                <a href="javascript:void(0)" class="text-white text-decoration-underline ms-2 fw-bold" id="unlinkProductBtn" title="Switch to creating as new product">
                                    <i class="fas fa-times ms-1"></i> Switch to New
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Vendor Row -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold text-dark mb-1">Vendor / Supplier <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="vendor_id" name="vendor_id" style="width: 100%;">
                                        <option value="">-- Select Existing Vendor --</option>
                                        @foreach($Vendor as $v)
                                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold text-dark mb-1">Or Create New Vendor</label>
                                    <input type="text" class="form-control" name="new_vendor_name" id="new_vendor_name" placeholder="Type supplier / vendor name...">
                                </div>
                            </div>
                            <div class="col-md-4" id="openingBalanceWrapper" style="display: none;">
                                <div class="form-group mb-0">
                                    <label class="fw-bold text-dark mb-1">Opening Balance (New Vendor)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted">Rs</span>
                                        <input type="number" step="0.01" class="form-control border-start-0" name="opening_balance" id="opening_balance" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Row -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold text-dark mb-1">Category <span class="text-danger">*</span></label>
                                    <select class="form-select select2" name="category_id" id="category_id" style="width: 100%;" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold text-dark mb-1">Sub Category</label>
                                    <select class="form-select select2" name="sub_category_id" id="sub_category_id" style="width: 100%;">
                                        <option value="">-- Select Subcategory --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0 position-relative">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="fw-bold text-dark mb-0">Product / Component Name <span class="text-danger">*</span></label>
                                        <span id="productLiveStatus"></span>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" class="form-control fw-bold" name="base_product_name" id="base_product_name" required placeholder='e.g. "Induction Heater 15kW", "IGBT Module 1200V"' autocomplete="off">
                                        <span class="input-group-text bg-white" id="productCheckSpinner" style="display: none;">
                                            <i class="fas fa-spinner fa-spin text-primary"></i>
                                        </span>
                                    </div>

                                    <!-- Clean Floating Suggestions Dropdown -->
                                    <div id="productSuggestionsBox" class="product-autocomplete-dropdown">
                                        <div class="d-flex justify-content-between align-items-center py-2 px-3 bg-light border-bottom">
                                            <span class="fw-bold small text-dark"><i class="fas fa-microchip text-primary me-1"></i> Existing Components in Catalog:</span>
                                            <button type="button" class="btn-close btn-sm" id="closeSuggestionsBtn" style="font-size: 8px;"></button>
                                        </div>
                                        <div id="productSuggestionsList" style="max-height: 220px; overflow-y: auto;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Variants Table -->
                <div class="card premium-card card-section-2">
                    <div class="card-header-premium d-flex justify-content-between align-items-center">
                        <div><i class="fas fa-sliders-h me-2 text-primary"></i> 2. Technical Specifications, Model &amp; Pricing</div>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="addRowBtn" style="border-radius: 6px;">
                            <i class="fas fa-plus me-1"></i> Add Specification Row
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table premium-table mb-0" id="variantsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 18%;">Rating / Spec / Size <span class="text-danger">*</span></th>
                                        <th style="width: 18%;">Model / Material / Type <span class="text-danger">*</span></th>
                                        <th style="width: 12%;">QTY <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Purchase Price <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Sale Price <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Line Total</th>
                                        <th style="width: 7%; text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="variant-row">
                                        <td><input type="text" class="form-control form-control-sm" name="variant_size[]" placeholder="e.g. 15kW, 380V, 5TR, 6mm" required></td>
                                        <td><input type="text" class="form-control form-control-sm" name="variant_color[]" placeholder="e.g. Copper, 3-Phase, Air-Cooled" required></td>
                                        <td><input type="number" class="form-control form-control-sm calc-qty text-center" name="qty[]" value="0" min="0" required></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm calc-pprice text-end" name="purchase_price[]" placeholder="0.00" required></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm text-end" name="sale_price[]" placeholder="0.00" required></td>
                                        <td><input type="text" class="form-control form-control-sm calc-line-total bg-light text-end fw-bold" readonly value="0.00"></td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-light duplicate-row-btn border text-primary" title="Duplicate"><i class="fas fa-copy"></i></button>
                                                <button type="button" class="btn btn-sm btn-light remove-row-btn border text-danger" title="Remove"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 3. Immediate Payment -->
                <div class="card premium-card card-section-3">
                    <div class="card-header-premium">
                        <i class="fas fa-wallet me-2 text-primary"></i> 3. Immediate Payment
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="form-group mb-md-0">
                                    <label class="fw-bold text-dark">Payment Account</label>
                                    <select class="form-select select2" name="payment_account_id" id="payment_account_id" style="width: 100%;">
                                        <option value="">-- No Immediate Payment --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') ? 'selected' : '' }}>{{ $acc->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-md-0">
                                    <label class="fw-bold text-dark">Amount Paid Now</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold text-muted border-end-0">Rs</span>
                                        <input type="number" step="0.01" class="form-control form-control-lg fw-bold text-success border-start-0" name="payment_amount" id="payment_amount" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-end mt-4 mt-md-0">
                                <button type="submit" class="btn btn-premium-primary btn-lg w-100" id="submitBtn">
                                    <i class="fas fa-check me-2"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Summary Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="summary-card" style="margin-bottom: 0;">
                            <div class="row text-center">
                                <div class="col-6 col-md border-end border-secondary">
                                    <div class="summary-label">Total Qty</div>
                                    <div class="summary-value text-white" id="displayTotalQty">0</div>
                                </div>
                                <div class="col-6 col-md border-end border-secondary">
                                    <div class="summary-label">Previous Balance</div>
                                    <div class="summary-value text-info" id="displayPrevBalance">0.00</div>
                                </div>
                                <div class="col-6 col-md border-end border-secondary">
                                    <div class="summary-label">Purchase Total</div>
                                    <div class="summary-value text-warning" id="displayPurchaseTotal">0.00</div>
                                </div>
                                <div class="col-6 col-md border-end border-secondary">
                                    <div class="summary-label">Paid Now</div>
                                    <div class="summary-value text-success" id="displayPaidAmount">0.00</div>
                                </div>
                                <div class="col-12 col-md">
                                    <div class="summary-label">Remaining Balance</div>
                                    <div class="summary-value text-danger" id="displayRemainingBalance">0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var vendorBalances = @json($vendorBalances ?? []);

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // CRITICAL FIX: Ensure the search box gets focus when opened.
    $(document).on('select2:open', function() {
        let searchField = document.querySelector('.select2-search__field');
        if (searchField) {
            searchField.focus();
        }
    });

    // Make Select2 open on focus
    $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
        var $select = $(this).closest(".select2-container").siblings('select:enabled');
        if ($select.length && $select.data('select2') && !$select.data('select2').isOpen()) {
            $select.select2('open');
        }
    });

    // Prevent double-open bug
    $('select.select2').on('select2:closing', function (e) {
        if ($(e.target).data("select2") && $(e.target).data("select2").$selection) {
            $(e.target).data("select2").$selection.one('focus focusin', function (e) {
                e.stopPropagation();
            });
        }
    });

    // ── Apply Existing Product ──────────────────────────────────
    window.applyExistingProduct = function(p) {
        $('#product_id').val(p.id);
        $('#base_product_name').val(p.item_name);

        // Update header badge
        var displayLabel = (p.item_code ? '[' + p.item_code + '] ' : '') + p.item_name;
        $('#badgeProductName').text(displayLabel);
        $('#productLinkedBadgeContainer').fadeIn(150);

        // Hide floating suggestions & clear indicator
        $('#productSuggestionsBox').hide();
        $('#productLiveStatus').html('<small class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Linked</small>');

        // Set Category & Subcategory
        if (p.category_id) {
            $('#category_id').val(p.category_id).trigger('change.select2');
            
            $.get('/get-subcategories/' + p.category_id, function(d) {
                $('#sub_category_id').empty().append('<option value="">-- Select Subcategory --</option>');
                $.each(d, function(_, v) {
                    var selected = (p.sub_category_id && v.id == p.sub_category_id) ? 'selected' : '';
                    $('#sub_category_id').append('<option value="' + v.id + '" ' + selected + '>' + v.name + '</option>');
                });
                if (p.sub_category_id) {
                    $('#sub_category_id').val(p.sub_category_id).trigger('change.select2');
                }
            });
        }

        // Populate Variants Table
        if (p.variants && p.variants.length > 0) {
            $('#variantsTable tbody').empty();
            $.each(p.variants, function(idx, v) {
                var size = v.size || '';
                var color = v.color || '';
                var pPrice = v.purch_price || p.purchase_price || 0;
                var sPrice = v.sale_price || p.sale_price || 0;

                var rowHtml = `
                    <tr class="variant-row">
                        <td><input type="text" class="form-control form-control-sm" name="variant_size[]" value="${size}" placeholder="e.g. 15kW, 380V, 5TR, 6mm" required></td>
                        <td><input type="text" class="form-control form-control-sm" name="variant_color[]" value="${color}" placeholder="e.g. Copper, 3-Phase, Air-Cooled" required></td>
                        <td><input type="number" class="form-control form-control-sm calc-qty text-center" name="qty[]" value="0" min="0" required></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm calc-pprice text-end" name="purchase_price[]" value="${pPrice}" placeholder="0.00" required></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm text-end" name="sale_price[]" value="${sPrice}" placeholder="0.00" required></td>
                        <td><input type="text" class="form-control form-control-sm calc-line-total bg-light text-end fw-bold" readonly value="0.00"></td>
                        <td class="text-center align-middle">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-light duplicate-row-btn border text-primary" title="Duplicate"><i class="fas fa-copy"></i></button>
                                <button type="button" class="btn btn-sm btn-light remove-row-btn border text-danger" title="Remove"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
                $('#variantsTable tbody').append(rowHtml);
            });
            updateSummary();
        } else if (p.purchase_price || p.sale_price) {
            var $firstRow = $('#variantsTable tbody tr:first');
            if ($firstRow.length) {
                if (p.purchase_price) $firstRow.find('input[name="purchase_price[]"]').val(p.purchase_price);
                if (p.sale_price) $firstRow.find('input[name="sale_price[]"]').val(p.sale_price);
                updateSummary();
            }
        }
    };

    // Unlink / Switch to New Product
    $('#unlinkProductBtn').on('click', function() {
        $('#product_id').val('');
        $('#productLinkedBadgeContainer').fadeOut(150);
        $('#productLiveStatus').html('<small class="text-secondary fw-bold"><i class="fas fa-plus me-1"></i> New Product</small>');
    });

    // Close suggestions box
    $('#closeSuggestionsBtn').on('click', function(e) {
        e.stopPropagation();
        $('#productSuggestionsBox').hide();
    });

    // Click outside to close suggestions
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#base_product_name, #productSuggestionsBox').length) {
            $('#productSuggestionsBox').hide();
        }
    });

    // ── Live Product Detection on Typing ─────────────────────────
    var productSearchTimer = null;
    var rawProductsData = {};

    $('#base_product_name').on('input', function() {
        var query = $(this).val().trim();
        var currentProductId = $('#product_id').val();

        if (currentProductId) {
            $('#product_id').val('');
            $('#productLinkedBadgeContainer').fadeOut(150);
        }

        clearTimeout(productSearchTimer);

        if (query.length < 2) {
            $('#productLiveStatus').empty();
            $('#productCheckSpinner').hide();
            $('#productSuggestionsBox').hide();
            return;
        }

        $('#productCheckSpinner').show();

        productSearchTimer = setTimeout(function() {
            $.ajax({
                url: '{{ route("purchase.quick_search_products") }}',
                method: 'GET',
                data: { q: query },
                success: function(products) {
                    $('#productCheckSpinner').hide();
                    rawProductsData = {};

                    if (products && products.length > 0) {
                        var hasExact = products.some(p => p.is_exact);
                        
                        if (hasExact) {
                            $('#productLiveStatus').html('<small class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Already Exists</small>');
                        } else {
                            $('#productLiveStatus').html('<small class="text-primary fw-bold"><i class="fas fa-search me-1"></i> ' + products.length + ' Found</small>');
                        }

                        // Build suggestions list
                        var listHtml = '';
                        $.each(products, function(i, item) {
                            rawProductsData[item.id] = item;
                            var varCount = item.variants ? item.variants.length : 0;
                            var varBadge = varCount > 0 ? `<span class="badge bg-light text-dark border ms-1">${varCount} variants</span>` : '';
                            var exactBadge = item.is_exact ? `<span class="badge bg-danger text-white ms-1">Exact Match</span>` : '';

                            listHtml += `
                                <div class="product-suggest-item d-flex justify-content-between align-items-center select-suggestion-btn" data-id="${item.id}">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                            [${item.item_code}] ${item.item_name} ${exactBadge}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.78rem;">
                                            Cat: ${item.category_name || '-'} | Price: Rs ${item.purchase_price || 0} ${varBadge}
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary py-1 px-2" style="font-size: 0.78rem; border-radius: 6px;">
                                        <i class="fas fa-check me-1"></i> Select
                                    </button>
                                </div>
                            `;
                        });

                        $('#productSuggestionsList').html(listHtml);
                        $('#productSuggestionsBox').show();
                    } else {
                        $('#productLiveStatus').html('<small class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> New Product</small>');
                        $('#productSuggestionsBox').hide();
                    }
                },
                error: function() {
                    $('#productCheckSpinner').hide();
                }
            });
        }, 300);
    });

    // Handle suggestion click
    $(document).on('click', '.select-suggestion-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pId = $(this).data('id');
        if (rawProductsData[pId]) {
            applyExistingProduct(rawProductsData[pId]);
        }
    });

    // ── Calculations & Summary ──────────────────────────────────
    function updateSummary() {
        var totalQty = 0;
        var purchaseTotal = 0;
        $('#variantsTable tbody tr').each(function() {
            var qty = parseFloat($(this).find('.calc-qty').val()) || 0;
            var price = parseFloat($(this).find('.calc-pprice').val()) || 0;
            var lineTotal = qty * price;
            $(this).find('.calc-line-total').val(lineTotal.toFixed(2));
            totalQty += qty;
            purchaseTotal += lineTotal;
        });

        $('#displayTotalQty').text(Number.isInteger(totalQty) ? totalQty : totalQty.toFixed(2));
        $('#displayPurchaseTotal').text(purchaseTotal.toFixed(2));

        var prevBalance = 0;
        var vendorId = $('#vendor_id').val();
        
        if (vendorId !== '') {
            prevBalance = parseFloat(vendorBalances[vendorId]) || 0;
        } else if ($('#new_vendor_name').val().trim() !== '') {
            prevBalance = parseFloat($('#opening_balance').val()) || 0;
        }

        $('#displayPrevBalance').text(prevBalance.toFixed(2));

        var paidNow = parseFloat($('#payment_amount').val()) || 0;
        $('#displayPaidAmount').text(paidNow.toFixed(2));

        var remaining = prevBalance + purchaseTotal - paidNow;
        $('#displayRemainingBalance').text(remaining.toFixed(2));
    }

    // Auto-fill payment amount when account is selected
    $('#payment_account_id').on('change', function() {
        if($(this).val() !== '') {
            var purchaseTotal = parseFloat($('#displayPurchaseTotal').text()) || 0;
            var currentPayment = parseFloat($('#payment_amount').val()) || 0;
            if (currentPayment === 0 && purchaseTotal > 0) {
                $('#payment_amount').val(purchaseTotal.toFixed(2));
                updateSummary();
            }
        } else {
            $('#payment_amount').val('');
            updateSummary();
        }
    });

    // Vendor Logic
    $('#new_vendor_name').on('input', function() {
        if($(this).val().trim() !== '') {
            $('#vendor_id').val('').trigger('change.select2');
            $('#openingBalanceWrapper').slideDown(200);
        } else {
            $('#openingBalanceWrapper').slideUp(200);
            $('#opening_balance').val('');
        }
        updateSummary();
    });

    $('#vendor_id').on('change', function() {
        if($(this).val() !== '') {
            $('#new_vendor_name').val('');
            $('#openingBalanceWrapper').slideUp(200);
            $('#opening_balance').val('');
        }
        updateSummary();
    });

    $(document).on('input change', '#opening_balance, #payment_amount, .calc-qty, .calc-pprice', function() {
        updateSummary();
    });

    // Add Row
    $('#addRowBtn').on('click', function() {
        var newRow = `
            <tr class="variant-row">
                <td><input type="text" class="form-control form-control-sm" name="variant_size[]" placeholder="e.g. 15kW, 380V, 5TR, 6mm" required></td>
                <td><input type="text" class="form-control form-control-sm" name="variant_color[]" placeholder="e.g. Copper, 3-Phase, Air-Cooled" required></td>
                <td><input type="number" class="form-control form-control-sm calc-qty text-center" name="qty[]" value="0" min="0" required></td>
                <td><input type="number" step="0.01" class="form-control form-control-sm calc-pprice text-end" name="purchase_price[]" placeholder="0.00" required></td>
                <td><input type="number" step="0.01" class="form-control form-control-sm text-end" name="sale_price[]" placeholder="0.00" required></td>
                <td><input type="text" class="form-control form-control-sm calc-line-total bg-light text-end fw-bold" readonly value="0.00"></td>
                <td class="text-center align-middle">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-light duplicate-row-btn border text-primary" title="Duplicate"><i class="fas fa-copy"></i></button>
                        <button type="button" class="btn btn-sm btn-light remove-row-btn border text-danger" title="Remove"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
        $('#variantsTable tbody').append(newRow);
        updateSummary();
    });

    // Duplicate Row
    $(document).on('click', '.duplicate-row-btn', function() {
        var $currentRow = $(this).closest('tr');
        var $newRow = $currentRow.clone();
        
        $newRow.find('input[name="variant_size[]"]').val('');
        $newRow.find('input[name="variant_color[]"]').val('');
        $newRow.find('.calc-qty').val(0).attr('min', 0);
        $newRow.find('input[name="purchase_price[]"]').val($currentRow.find('input[name="purchase_price[]"]').val());
        $newRow.find('input[name="sale_price[]"]').val($currentRow.find('input[name="sale_price[]"]').val());
        $newRow.find('.calc-line-total').val('0.00');
        
        $currentRow.after($newRow);
        updateSummary();
    });

    // Remove Row
    $(document).on('click', '.remove-row-btn', function() {
        if($('#variantsTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            updateSummary();
        } else {
            Swal.fire('Warning', 'You must have at least one variant row.', 'warning');
        }
    });

    // Submit handler
    $('#quickPurchaseForm').on('submit', function(e) {
        e.preventDefault();

        if($('#vendor_id').val() === '' && $('#new_vendor_name').val().trim() === '') {
            Swal.fire('Error', 'Please select a vendor or type a new vendor name.', 'error');
            return;
        }

        var totalQty = 0;
        $('.calc-qty').each(function() {
            totalQty += parseFloat($(this).val()) || 0;
        });

        if (totalQty <= 0) {
            Swal.fire('Warning', 'Please enter a quantity greater than 0 for at least one variant to record a purchase.', 'warning');
            return;
        }

        var $btn = $('#submitBtn');
        var originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message
                    }).then(() => {
                        window.location.href = res.redirect_url;
                    });
                }
            },
            error: function(err) {
                $btn.html(originalHtml).prop('disabled', false);
                var errors = err.responseJSON && err.responseJSON.errors ? err.responseJSON.errors : null;
                var msg = 'Something went wrong.';
                if(errors) {
                    msg = Object.values(errors).map(e => e[0]).join('<br>');
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Subcategory load on manual category change
    $('#category_id').on('change', function() {
        var cid = $(this).val();
        if (cid) {
            $.get('/get-subcategories/' + cid, function(d) {
                $('#sub_category_id').empty().append('<option value="">-- Select Subcategory --</option>');
                $.each(d, function(_, v) {
                    $('#sub_category_id').append('<option value="' + v.id + '">' + v.name + '</option>');
                });
            });
        } else {
            $('#sub_category_id').empty().append('<option value="">-- Select Subcategory --</option>');
        }
    });

    // Init Summary
    updateSummary();
});
</script>
@endpush
