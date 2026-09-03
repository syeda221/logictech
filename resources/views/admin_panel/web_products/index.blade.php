@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
{{-- Toastr for ajax notifications --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    .page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
        background-color: #f8fafc;
        border-radius: 16px;
    }
    .page-header h4 {
        color: #0f172a;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    .filter-card {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        transition: box-shadow 0.2s;
    }
    .filter-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }
    .table-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }
    .form-select, .form-control {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 0.95rem;
        color: #1e293b;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-select:focus, .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        outline: 0;
    }
    .btn-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
        padding: 10px 24px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.2s;
    }
    .btn-primary:hover, .btn-primary:focus {
        background-color: #4338ca;
        border-color: #4338ca;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
    }
    .btn-light {
        background-color: #ffffff;
        border-color: #cbd5e1;
        color: #475569;
        padding: 10px 24px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.2s;
    }
    .btn-light:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }
    .product-img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table th {
        background-color: #f8fafc !important;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 14px 16px;
    }
    .table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }
    /* Soft Badge Styles */
    .badge-soft-success {
        background-color: #d1fae5;
        color: #065f46;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-soft-secondary {
        background-color: #f1f5f9;
        color: #475569;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-soft-primary {
        background-color: #dbeafe;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-soft-info {
        background-color: #e0f2fe;
        color: #0369a1;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    /* Switch Style */
    .form-switch .form-check-input {
        width: 2.75em;
        height: 1.4em;
        background-color: #e2e8f0;
        border-color: #cbd5e1;
        cursor: pointer;
        transition: background-position .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    .form-switch .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    /* Modal Customisation */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    .modal-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
        background-color: #f8fafc;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 20px 24px;
        background-color: #f8fafc;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }
    /* File Upload Styling inside Modal */
    .image-preview-thumbnail {
        height: 64px;
        width: 64px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 2px;
        background: #ffffff;
    }
    .preview-box {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
        padding: 10px;
        border-radius: 10px;
        border: 1px dashed #cbd5e1;
    }
</style>

<div class="page-container mt-3">
    <div class="d-flex align-items-center justify-content-between mb-4 page-header">
        <div>
            <h4 class="fw-bold mb-0">Web Products Management</h4>
            <small class="text-muted">Quickly toggle visibility and promotional settings for the website.</small>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <form action="{{ route('web_products.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filter by Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Website Visibility</label>
                    <select name="visibility" class="form-select">
                        <option value="">All Products</option>
                        <option value="1" {{ request('visibility') == '1' ? 'selected' : '' }}>Visible on Website</option>
                        <option value="0" {{ request('visibility') == '0' ? 'selected' : '' }}>Hidden from Website</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i> Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('web_products.index') }}" class="btn btn-light w-100 border"><i class="fas fa-sync me-2"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="productsTable">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 80px;">Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th class="text-center">Web Status</th>
                            <th class="text-center">Homepage</th>
                            <th>Promo Tag</th>
                            <th>Web Price</th>
                            <th class="text-end pe-4" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td class="ps-4">
                                @if($product->image)
                                    <img src="{{ asset('uploads/products/'.$product->image) }}" class="product-img" alt="{{ $product->item_name }}">
                                @else
                                    <div class="product-img bg-light d-flex align-items-center justify-content-center text-muted">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="fw-bold text-indigo text-decoration-none open-web-settings" data-id="{{ $product->id }}" style="color: #4f46e5;">{{ $product->item_name }}</a>
                                <div class="small text-muted mt-1">SKU: {{ $product->item_code }}</div>
                            </td>
                            <td class="fw-medium text-slate-600">{{ $product->category_relation->name ?? 'N/A' }}</td>
                            
                            {{-- Web Visible --}}
                            <td class="text-center">
                                @if($product->is_web_visible)
                                    <span class="badge-soft-success"><i class="fas fa-check-circle"></i> Visible</span>
                                @else
                                    <span class="badge-soft-secondary"><i class="fas fa-eye-slash"></i> Hidden</span>
                                @endif
                            </td>

                            {{-- Show on Home --}}
                            <td class="text-center">
                                @if($product->show_on_homepage)
                                    <span class="badge-soft-primary"><i class="fas fa-home"></i> Yes</span>
                                @else
                                    <span class="badge-soft-secondary">No</span>
                                @endif
                            </td>

                            {{-- Promo Tag --}}
                            <td>
                                @if($product->promo_tag)
                                    <span class="badge-soft-info"><i class="fas fa-tag"></i> {{ $product->promo_tag }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- Web Sale Price --}}
                            <td class="fw-bold text-slate-800">
                                @if($product->web_sale_price)
                                    Rs. {{ number_format($product->web_sale_price, 2) }}
                                @else
                                    <span class="text-muted small fw-normal">Default (Rs. {{ number_format($product->sale_price_per_piece ?? 0, 2) }})</span>
                                @endif
                            </td>
                            
                            {{-- Action --}}
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border open-web-settings" data-id="{{ $product->id }}">
                                    <i class="fas fa-cog me-1"></i> Settings
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-top d-flex justify-content-end">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@php
    $canEditProducts = auth()->user()->hasPermissionTo('web_products.edit');
@endphp

{{-- Web Settings Modal --}}
<div class="modal fade" id="webSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="webSettingsForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #0f172a;"><i class="fas fa-globe text-indigo me-2"></i> Website Settings: <span id="modalProductName" class="text-indigo" style="color: #4f46e5;"></span></h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="modal_is_web_visible" name="is_web_visible" value="1" {{ !$canEditProducts ? 'disabled' : '' }}>
                                <label class="form-check-label fw-bold text-slate-700 ms-2" for="modal_is_web_visible">Enable for Website</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="modal_show_on_homepage" name="show_on_homepage" value="1" {{ !$canEditProducts ? 'disabled' : '' }}>
                                <label class="form-check-label fw-bold text-slate-700 ms-2" for="modal_show_on_homepage">Show on Homepage</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="modal_auto_hide" name="auto_hide_out_of_stock" value="1" {{ !$canEditProducts ? 'disabled' : '' }}>
                                <label class="form-check-label fw-bold text-slate-700 ms-2" for="modal_auto_hide">Auto Hide Out of Stock</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Promotional Tag</label>
                            <select class="form-select" name="promo_tag" id="modal_promo_tag" {{ !$canEditProducts ? 'disabled' : '' }}>
                                <option value="">None</option>
                                <option value="Featured">Featured</option>
                                <option value="New Arrival">New Arrival</option>
                                <option value="Best Seller">Best Seller</option>
                                <option value="Trending">Trending</option>
                                <option value="Flash Sale">Flash Sale</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website Sale Price</label>
                            <input type="number" step="0.01" class="form-control" name="web_sale_price" id="modal_web_sale_price" placeholder="Leave empty to use default" {{ !$canEditProducts ? 'disabled' : '' }}>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Title (SEO)</label>
                            <input type="text" class="form-control" name="meta_title" id="modal_meta_title" placeholder="SEO Title" {{ !$canEditProducts ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description (SEO)</label>
                            <input type="text" class="form-control" name="meta_description" id="modal_meta_description" placeholder="SEO Description" {{ !$canEditProducts ? 'disabled' : '' }}>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-indigo fw-bold">Main Website Image (Primary)</label>
                            <div id="modalExistingMainImage" class="mb-3"></div>
                            <input type="file" class="form-control" name="web_main_image" accept="image/*" {{ !$canEditProducts ? 'disabled' : '' }}>
                            <small class="text-muted mt-2 d-block">This image will show as the primary thumbnail on the website.</small>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            <label class="form-label text-indigo fw-bold mb-1">Website Gallery Images (Max 4)</label>
                            <div id="modalExistingImages" class="d-flex flex-wrap gap-2 mb-3"></div>
                            
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="small text-muted mb-2 d-block">Image 1</label>
                                    <input type="file" class="form-control form-control-sm" name="web_images[]" accept="image/*" {{ !$canEditProducts ? 'disabled' : '' }}>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted mb-2 d-block">Image 2</label>
                                    <input type="file" class="form-control form-control-sm" name="web_images[]" accept="image/*" {{ !$canEditProducts ? 'disabled' : '' }}>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted mb-2 d-block">Image 3</label>
                                    <input type="file" class="form-control form-control-sm" name="web_images[]" accept="image/*" {{ !$canEditProducts ? 'disabled' : '' }}>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted mb-2 d-block">Image 4</label>
                                    <input type="file" class="form-control form-control-sm" name="web_images[]" accept="image/*" {{ !$canEditProducts ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <small class="text-muted mt-3 d-block">Upload new images in any of the slots above to replace the existing gallery.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Close</button>
                    @if($canEditProducts)
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    @else
                        <button type="button" class="btn btn-primary" disabled><i class="fas fa-lock me-1"></i> Save Settings (Read Only)</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        // CSRF Token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // Datatables setup
        $('#productsTable').DataTable({
            "paging": false,
            "info": false,
            "searching": true,
            "ordering": true,
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] }
            ]
        });

        // Toastr options
        toastr.options = {
            "positionClass": "toast-bottom-right",
            "timeOut": "2000",
        };

        // Open Web Settings Modal
        $('.open-web-settings').on('click', function(e) {
            e.preventDefault();
            let productId = $(this).data('id');
            let url = "{{ url('/web-products') }}/" + productId + "/settings";

            $.get(url, function(response) {
                if(response.status === 'success') {
                    let p = response.product;
                    
                    // Set Form Action
                    $('#webSettingsForm').attr('action', url);
                    
                    $('#modalProductName').text(p.item_name);
                    $('#modal_is_web_visible').prop('checked', p.is_web_visible == 1);
                    $('#modal_show_on_homepage').prop('checked', p.show_on_homepage == 1);
                    $('#modal_auto_hide').prop('checked', p.auto_hide_out_of_stock == 1);
                    $('#modal_promo_tag').val(p.promo_tag);
                    $('#modal_web_sale_price').val(p.web_sale_price);
                    $('#modal_meta_title').val(p.meta_title);
                    $('#modal_meta_description').val(p.meta_description);

                    // Existing Main Image
                    if (p.web_main_image) {
                        let mainImgUrl = "{{ asset('uploads/products') }}/" + p.web_main_image;
                        $('#modalExistingMainImage').html(`
                            <div class="preview-box">
                                <img src="${mainImgUrl}" class="image-preview-thumbnail">
                                <div class="small text-muted fw-semibold">Current Main Image</div>
                            </div>
                        `);
                    } else if (p.image) {
                        let defaultImgUrl = "{{ asset('uploads/products') }}/" + p.image;
                        $('#modalExistingMainImage').html(`
                            <div class="preview-box">
                                <img src="${defaultImgUrl}" class="image-preview-thumbnail">
                                <div>
                                    <div class="small text-muted fw-semibold">Default POS Image</div>
                                    <small class="text-muted d-block" style="font-size:11px;">Will be used as default</small>
                                </div>
                            </div>
                        `);
                    } else {
                        $('#modalExistingMainImage').html('');
                    }

                    // Existing Gallery Images
                    let imagesHtml = '';
                    if(p.web_images && p.web_images.length > 0) {
                        p.web_images.forEach(function(img) {
                            let imgUrl = "{{ asset('uploads/products') }}/" + img.image_path;
                            imagesHtml += `<img src="${imgUrl}" class="image-preview-thumbnail me-2 shadow-sm">`;
                        });
                    }
                    $('#modalExistingImages').html(imagesHtml);

                    // Show Modal (Bootstrap 4 fallback)
                    $('#webSettingsModal').modal('show');
                }
            });
        });
    });
</script>
@endsection
