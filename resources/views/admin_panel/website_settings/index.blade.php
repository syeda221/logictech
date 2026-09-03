@extends('admin_panel.layout.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<style>
    /* Premium Font Stack overrides */
    .page-container-custom {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .page-container-custom input, 
    .page-container-custom select, 
    .page-container-custom textarea, 
    .page-container-custom button {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .page-container-custom {
        max-width: 1200px;
        margin: 20px auto;
        padding: 24px;
        background-color: #f8fafc;
        border-radius: 16px;
    }
    
    .page-header-custom {
        margin-bottom: 24px;
        padding-left: 4px;
    }
    
    .page-header-custom h4 {
        color: #0f172a;
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin: 0;
    }
    
    .section-card-custom {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    
    .section-card-custom:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    
    .card-header-pro-custom {
        padding: 18px 24px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 600;
        font-size: 0.95rem; /* ~15px */
        display: flex;
        align-items: center;
        gap: 10px;
        color: #1e293b !important;
    }
    
    .card-header-pro-custom i {
        font-size: 1rem;
        opacity: 0.9;
    }
    
    /* Subtle color coding for icons in section headers */
    .card-header-pro-custom.text-primary i { color: #4f46e5 !important; }
    .card-header-pro-custom.text-info i { color: #0ea5e9 !important; }
    .card-header-pro-custom.text-success i { color: #10b981 !important; }
    .card-header-pro-custom.text-warning i { color: #f59e0b !important; }
    .card-header-pro-custom.text-emerald-600 i { color: #059669 !important; }
    
    .card-body-pro-custom {
        padding: 24px;
    }
    
    .form-label-custom {
        font-size: 12.5px;
        font-weight: 500;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }
    
    .form-control-custom {
        height: 42px;
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.875rem; /* 14px */
        color: #1e293b;
        padding: 8px 16px;
        background-color: #ffffff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control-custom::placeholder {
        color: #94a3b8;
        font-size: 0.875rem;
    }
    
    .form-control-custom:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        outline: 0;
    }
    
    textarea.form-control-custom {
        height: auto;
        min-height: 100px;
        padding: 12px 16px;
    }
    
    /* Premium file input customization */
    input[type="file"].form-control-custom {
        padding: 6px 12px;
        display: flex;
        align-items: center;
        background-color: #f8fafc;
        cursor: pointer;
    }
    
    input[type="file"].form-control-custom::file-selector-button {
        background-color: #e2e8f0;
        color: #334155;
        border: 0;
        padding: 4px 12px;
        margin-right: 12px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.825rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    input[type="file"].form-control-custom:hover::file-selector-button {
        background-color: #cbd5e1;
    }
    
    .image-preview-container-custom {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
        width: fit-content;
    }
    
    .image-preview-container-custom img {
        border-radius: 6px;
        max-height: 48px;
        max-width: 120px;
        object-fit: contain;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    
    .image-preview-container-custom video {
        border-radius: 6px;
        max-height: 60px;
        border: 1px solid #e2e8f0;
        background: #000000;
    }
    
    .alert-success-custom {
        background-color: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
        border-radius: 8px;
        padding: 14px 20px;
        font-weight: 500;
        font-size: 0.875rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    .alert-success-custom i {
        color: #15803d;
    }
    
    /* Table Styling */
    .table-responsive-custom {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        background-color: #ffffff;
    }
    
    .table-custom {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-custom th {
        background-color: #f8fafc !important;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem; /* 12px */
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 14px 24px;
        vertical-align: middle;
    }
    
    .table-custom td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle !important;
        color: #334155;
        font-size: 0.875rem;
    }
    
    .table-custom tbody tr:last-child td {
        border-bottom: 0;
    }
    
    .table-custom-hover tbody tr:hover {
        background-color: #fafafa;
    }
    
    /* Toggle Switches Centered */
    .form-switch-custom {
        display: inline-flex;
        align-items: center;
        min-height: auto;
        padding-left: 2.5em;
        margin-bottom: 0;
    }
    
    .form-switch-custom .form-check-input {
        width: 2.5em;
        height: 1.25em;
        background-color: #e2e8f0;
        border-color: #cbd5e1;
        cursor: pointer;
        margin-top: 0;
    }
    
    .form-switch-custom .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    /* Form Buttons */
    .btn-primary-custom {
        background-color: #4f46e5;
        border-color: #4f46e5;
        height: 42px;
        padding: 0 24px;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 1px 2px 0 rgba(79, 70, 229, 0.05);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        color: #ffffff;
        cursor: pointer;
        text-decoration: none;
    }
    
    .btn-primary-custom:hover {
        background-color: #4338ca;
        border-color: #4338ca;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.15), 0 2px 4px -2px rgba(79, 70, 229, 0.15);
        color: #ffffff;
    }
    
    .btn-primary-custom:active {
        transform: translateY(0);
    }
    
    .btn-primary-custom:focus {
        background-color: #4338ca;
        border-color: #4338ca;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        color: #ffffff;
        outline: 0;
    }

    .btn-outline-primary-custom {
        color: #4f46e5;
        border-color: #cbd5e1;
        background-color: #ffffff;
        font-weight: 500;
        font-size: 0.875rem;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-check:checked + .btn-outline-primary-custom {
        color: #ffffff !important;
        background-color: #4f46e5 !important;
        border-color: #4f46e5 !important;
    }
    .btn-outline-primary-custom:hover {
        background-color: #f8fafc;
        border-color: #4f46e5;
    }
    
    /* Card Footer styling for buttons */
    .card-footer-pro-custom {
        padding: 16px 24px;
        background-color: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
    }

    /* Small form control styling for table */
    .form-control-sm-custom {
        height: 36px !important;
        padding: 6px 12px !important;
        font-size: 0.825rem !important;
        border-radius: 6px !important;
    }
    
    input[type="file"].form-control-sm-custom {
        padding: 4px 8px !important;
    }
    
    input[type="file"].form-control-sm-custom::file-selector-button {
        padding: 2px 8px !important;
        margin-right: 8px !important;
        font-size: 0.775rem !important;
        border-radius: 4px !important;
    }
    
    .text-slate-700 {
        color: #334155 !important;
    }
    
    /* Responsive Settings */
    @media (max-width: 768px) {
        .page-container-custom {
            padding: 16px;
            margin: 10px auto;
        }
        .card-body-pro-custom {
            padding: 16px;
        }
        .card-header-pro-custom {
            padding: 12px 16px;
        }
        .table-custom th, .table-custom td {
            padding: 12px 16px;
        }
    }
</style>

@php
    $canEdit = auth()->user()->hasAnyPermission(['website-settings.edit', 'website-settings.update']);
    $canCreate = auth()->user()->hasPermissionTo('website-settings.create');
    $canDelete = auth()->user()->hasPermissionTo('website-settings.delete');
    $canUpload = auth()->user()->hasPermissionTo('website-settings.upload_manage');
@endphp

<div class="page-container-custom mt-3 mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4 page-header-custom">
        <h4>Website Settings</h4>
    </div>

    @if(session('success'))
        <div class="alert-success-custom d-flex align-items-center mb-4 border" role="alert">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-4 border" role="alert" style="border-radius: 8px; padding: 14px 20px; font-weight: 500; font-size: 0.875rem; background-color: #fef2f2; border-color: #fca5a5; color: #991b1b;">
            <i class="fas fa-exclamation-triangle me-3 fs-4 text-danger"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <form action="{{ route('website_settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- General Details --}}
        <div class="section-card-custom">
            <div class="card-header-pro-custom text-primary"><i class="fas fa-info-circle"></i>General Information</div>
            <div class="card-body-pro-custom">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-custom">Site Name</label>
                        <input type="text" name="site_name" class="form-control-custom" value="{{ $settings['web_site_name'] ?? '' }}" placeholder="Enter Site Name" {{ ((empty($settings['web_site_name']) && !$canCreate) || (!empty($settings['web_site_name']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control-custom" value="{{ $settings['web_contact_email'] ?? '' }}" placeholder="Enter Contact Email" {{ ((empty($settings['web_contact_email']) && !$canCreate) || (!empty($settings['web_contact_email']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control-custom" value="{{ $settings['web_contact_phone'] ?? '' }}" placeholder="Enter Contact Phone" {{ ((empty($settings['web_contact_phone']) && !$canCreate) || (!empty($settings['web_contact_phone']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" class="form-control-custom" value="{{ $settings['web_whatsapp_number'] ?? '' }}" placeholder="Enter WhatsApp Number" {{ ((empty($settings['web_whatsapp_number']) && !$canCreate) || (!empty($settings['web_whatsapp_number']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        {{-- Social Links --}}
        <div class="section-card-custom">
            <div class="card-header-pro-custom text-info"><i class="fas fa-share-alt"></i>Social Links</div>
            <div class="card-body-pro-custom">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label-custom"><i class="fab fa-facebook text-primary me-2"></i> Facebook Link</label>
                        <input type="url" name="facebook_link" class="form-control-custom" value="{{ $settings['web_facebook_link'] ?? '' }}" placeholder="https://facebook.com/yourpage" {{ ((empty($settings['web_facebook_link']) && !$canCreate) || (!empty($settings['web_facebook_link']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom"><i class="fab fa-instagram text-danger me-2"></i> Instagram Link</label>
                        <input type="url" name="instagram_link" class="form-control-custom" value="{{ $settings['web_instagram_link'] ?? '' }}" placeholder="https://instagram.com/yourpage" {{ ((empty($settings['web_instagram_link']) && !$canCreate) || (!empty($settings['web_instagram_link']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom"><i class="fab fa-tiktok text-dark me-2"></i> TikTok Link</label>
                        <input type="url" name="tiktok_link" class="form-control-custom" value="{{ $settings['web_tiktok_link'] ?? '' }}" placeholder="https://tiktok.com/@yourpage" {{ ((empty($settings['web_tiktok_link']) && !$canCreate) || (!empty($settings['web_tiktok_link']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        {{-- Banners & Images --}}
        <div class="section-card-custom">
            <div class="card-header-pro-custom text-success"><i class="fas fa-images"></i>Banners & Images</div>
            <div class="card-body-pro-custom">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-custom">Site Logo</label>
                        @if(isset($settings['web_site_logo']))
                            <div class="image-preview-container-custom">
                                <img src="{{ asset($settings['web_site_logo']) }}" class="border" alt="Site Logo">
                                <span class="small text-muted">Current Logo</span>
                            </div>
                        @endif
                        <input type="file" name="site_logo" class="form-control-custom" accept="image/*" {{ !$canUpload ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">New Arrival Section Large Image</label>
                        @if(isset($settings['web_home_banner_image']))
                            <div class="image-preview-container-custom">
                                <img src="{{ asset($settings['web_home_banner_image']) }}" class="border" alt="New Arrival Section Large Image">
                                <span class="small text-muted">Current Large Image</span>
                            </div>
                        @endif
                        <input type="file" name="home_banner_image" class="form-control-custom" accept="image/*" {{ !$canUpload ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Hero Section Media Type</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="home_hero_media_type" id="media_type_video" value="video" {{ (!isset($settings['web_home_hero_media_type']) || $settings['web_home_hero_media_type'] === 'video') ? 'checked' : '' }} {{ (!$canEdit && !$canCreate) ? 'disabled' : '' }}>
                            <label class="btn btn-outline-primary-custom flex-fill text-center py-2" for="media_type_video">
                                <i class="fas fa-video me-2"></i> Video
                            </label>

                            <input type="radio" class="btn-check" name="home_hero_media_type" id="media_type_image" value="image" {{ (isset($settings['web_home_hero_media_type']) && $settings['web_home_hero_media_type'] === 'image') ? 'checked' : '' }} {{ (!$canEdit && !$canCreate) ? 'disabled' : '' }}>
                            <label class="btn btn-outline-primary-custom flex-fill text-center py-2" for="media_type_image">
                                <i class="fas fa-image me-2"></i> Image
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6" id="hero_video_wrapper">
                        <label class="form-label-custom">Home Hero Section Video</label>
                        @if(isset($settings['web_home_hero_video']))
                            <div class="image-preview-container-custom d-flex flex-column align-items-start gap-2">
                                <video src="{{ asset($settings['web_home_hero_video']) }}" class="border rounded" controls></video>
                                <span class="small text-muted text-break">Current Video: {{ basename($settings['web_home_hero_video']) }}</span>
                            </div>
                        @endif
                        <input type="file" name="home_hero_video" class="form-control-custom" accept="video/mp4,video/webm,video/ogg" {{ !$canUpload ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6" id="hero_image_wrapper" style="display: none;">
                        <label class="form-label-custom">Home Hero Section Image</label>
                        @if(isset($settings['web_home_hero_image']))
                            <div class="image-preview-container-custom">
                                <img src="{{ asset($settings['web_home_hero_image']) }}" class="border" alt="Hero Background Image">
                                <span class="small text-muted">Current Hero Image</span>
                            </div>
                        @endif
                        <input type="file" name="home_hero_image" class="form-control-custom" accept="image/*" {{ !$canUpload ? 'disabled' : '' }}>
                    </div>
                    {{--
                    <div class="col-md-12">
                        <label class="form-label-custom">Home Banner Text</label>
                        <textarea name="home_banner_text" class="form-control-custom" rows="2" placeholder="Write banner slogan or text here...">{{ $settings['web_home_banner_text'] ?? '' }}</textarea>
                    </div>
                    --}}
                </div>
            </div>
        </div>

        {{-- Easypaisa Payment Settings --}}
        <div class="section-card-custom">
            <div class="card-header-pro-custom text-emerald-600"><i class="fas fa-wallet"></i>Easypaisa Payment Details</div>
            <div class="card-body-pro-custom">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-custom">Easypaisa Account Title</label>
                        <input type="text" name="easypaisa_account_title" class="form-control-custom" value="{{ $settings['web_easypaisa_account_title'] ?? '' }}" placeholder="e.g. TrendHub Premium" {{ ((empty($settings['web_easypaisa_account_title']) && !$canCreate) || (!empty($settings['web_easypaisa_account_title']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Easypaisa Mobile Number</label>
                        <input type="text" name="easypaisa_mobile_number" class="form-control-custom" value="{{ $settings['web_easypaisa_mobile_number'] ?? '' }}" placeholder="e.g. 0300-1234567" {{ ((empty($settings['web_easypaisa_mobile_number']) && !$canCreate) || (!empty($settings['web_easypaisa_mobile_number']) && !$canEdit)) ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-custom">Easypaisa QR Code Image</label>
                        @if(isset($settings['web_easypaisa_qr_code']))
                            <div class="image-preview-container-custom">
                                <img src="{{ asset($settings['web_easypaisa_qr_code']) }}" class="border" alt="Easypaisa QR Code">
                                <span class="small text-muted font-monospace">Current QR Code: {{ basename($settings['web_easypaisa_qr_code']) }}</span>
                            </div>
                        @endif
                        <input type="file" name="easypaisa_qr_code" class="form-control-custom" accept="image/*" {{ !$canUpload ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        {{-- Store Locator Settings --}}
        <div class="section-card-custom">
            <div class="card-header-pro-custom text-primary"><i class="fas fa-map-marked-alt"></i>Store Locator Settings</div>
            <div class="card-body-pro-custom">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label-custom">Store Locator Banner Image</label>
                        @if(isset($settings['web_store_locator_banner_image']))
                            <div class="image-preview-container-custom">
                                <img src="{{ asset($settings['web_store_locator_banner_image']) }}" class="border" alt="Store Locator Banner">
                                <span class="small text-muted">Current Banner Image</span>
                            </div>
                        @endif
                        <input type="file" name="store_locator_banner_image" class="form-control-custom" accept="image/*" {{ !$canUpload ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-custom">Google Maps Embed Iframe Src URL</label>
                        <input type="text" name="store_locator_map_iframe" class="form-control-custom" value="{{ $settings['web_store_locator_map_iframe'] ?? '' }}" placeholder="https://www.google.com/maps/embed?pb=..." {{ ((empty($settings['web_store_locator_map_iframe']) && !$canCreate) || (!empty($settings['web_store_locator_map_iframe']) && !$canEdit)) ? 'disabled' : '' }}>
                        <small class="form-text text-muted">Paste only the <strong>src</strong> attribute value from the Google Maps share embed HTML code.</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-custom">Store Locations (Add/Edit List)</label>
                        <input type="hidden" name="store_locator_locations" id="store_locator_locations_input" value="{{ $settings['web_store_locator_locations'] ?? '[]' }}">
                        <div class="table-responsive-custom">
                            <table class="table mb-0" id="locations_table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size: 11px; padding: 8px 16px;">Store Name</th>
                                        <th style="font-size: 11px; padding: 8px 16px;">Address</th>
                                        <th style="font-size: 11px; padding: 8px 16px;">Phone</th>
                                        <th style="font-size: 11px; padding: 8px 16px; width: 80px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add_location_btn" {{ !$canEdit ? 'disabled' : '' }}>
                                <i class="fas fa-plus"></i> Add Store Location
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Policies & Content --}}
        <div class="section-card-custom">
            <div class="card-header-pro-custom text-warning"><i class="fas fa-file-contract"></i>Policies & Content</div>
            <div class="card-body-pro-custom">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label-custom">About Us</label>
                        <textarea name="about_us" class="form-control-custom" rows="4" placeholder="Brief history or description of your company..." {{ ((empty($settings['web_about_us']) && !$canCreate) || (!empty($settings['web_about_us']) && !$canEdit)) ? 'disabled' : '' }}>{{ $settings['web_about_us'] ?? '' }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-custom">Shipping Policy</label>
                        <textarea name="shipping_policy" class="form-control-custom" rows="4" placeholder="Delivery rules, charges, and timelines..." {{ ((empty($settings['web_shipping_policy']) && !$canCreate) || (!empty($settings['web_shipping_policy']) && !$canEdit)) ? 'disabled' : '' }}>{{ $settings['web_shipping_policy'] ?? '' }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-custom">Return & Refund Policy</label>
                        <textarea name="return_policy" class="form-control-custom" rows="4" placeholder="Return timelines and guidelines..." {{ ((empty($settings['web_return_policy']) && !$canCreate) || (!empty($settings['web_return_policy']) && !$canEdit)) ? 'disabled' : '' }}>{{ $settings['web_return_policy'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer-pro-custom">
                @if($canEdit || $canCreate || $canDelete || $canUpload)
                    <button type="submit" class="btn-primary-custom"><i class="fas fa-save"></i> Save Settings</button>
                @else
                    <button type="button" class="btn-primary-custom" disabled style="opacity: 0.6; cursor: not-allowed;"><i class="fas fa-lock"></i> Save Settings (Read Only)</button>
                @endif
            </div>
        </div>
    </form>

    {{-- Category Website Settings --}}
    <form action="{{ route('website_settings.categories.update') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <div class="section-card-custom">
            <div class="card-header-pro-custom text-primary"><i class="fas fa-tags"></i>Category Website Settings</div>
            <div class="card-body-pro-custom p-0">
                <div class="table-responsive-custom border-0 rounded-0">
                    <table class="table-custom table-custom-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Show on Website</th>
                                <th>Web Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td class="fw-semibold text-slate-700">{{ $category->name }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-custom">
                                        <input class="form-check-input" type="checkbox" name="categories[{{ $category->id }}][show_on_website]" value="1" {{ $category->show_on_website ? 'checked' : '' }} {{ !$canEdit ? 'disabled' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($category->web_image)
                                            <div class="image-preview-container-custom m-0 p-1">
                                                <img src="{{ asset($category->web_image) }}" class="border m-0" alt="{{ $category->name }}" style="max-height: 32px; max-width: 60px;">
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <input type="file" name="categories[{{ $category->id }}][web_image]" class="form-control-custom form-control-sm-custom" accept="image/*" {{ !$canUpload ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer-pro-custom">
                    @if($canEdit || $canUpload)
                        <button type="submit" class="btn-primary-custom"><i class="fas fa-save"></i> Save Category Settings</button>
                    @else
                        <button type="button" class="btn-primary-custom" disabled style="opacity: 0.6; cursor: not-allowed;"><i class="fas fa-lock"></i> Save Category Settings (Read Only)</button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const videoRadio = document.getElementById('media_type_video');
        const imageRadio = document.getElementById('media_type_image');
        const videoWrapper = document.getElementById('hero_video_wrapper');
        const imageWrapper = document.getElementById('hero_image_wrapper');

        function toggleMediaInputs() {
            if (videoRadio.checked) {
                videoWrapper.style.display = 'block';
                imageWrapper.style.display = 'none';
            } else {
                videoWrapper.style.display = 'none';
                imageWrapper.style.display = 'block';
            }
        }

        if (videoRadio && imageRadio && videoWrapper && imageWrapper) {
            videoRadio.addEventListener('change', toggleMediaInputs);
            imageRadio.addEventListener('change', toggleMediaInputs);
            toggleMediaInputs();
        }

        // Store Locator Locations Manager
        const locationsInput = document.getElementById('store_locator_locations_input');
        const locationsTableBody = document.querySelector('#locations_table tbody');
        const addLocationBtn = document.getElementById('add_location_btn');
        
        let locations = [];
        try {
            locations = JSON.parse(locationsInput.value || '[]');
        } catch(e) {
            locations = [];
        }

        function renderLocations() {
            locationsTableBody.innerHTML = '';
            if (locations.length === 0) {
                locationsTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3 small">No store locations added yet. Click 'Add Store Location' to get started.</td>
                    </tr>
                `;
                return;
            }

            locations.forEach((loc, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <input type="text" class="form-control form-control-sm loc-input" data-index="${index}" data-field="name" value="${loc.name || ''}" placeholder="Dolmen Mall, Clifton" required style="font-size: 13px;" ${ {{ !$canEdit ? 'true' : 'false' }} ? 'disabled' : '' }>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm loc-input" data-index="${index}" data-field="address" value="${loc.address || ''}" placeholder="Shop # F-21 & F-22..." required style="font-size: 13px;" ${ {{ !$canEdit ? 'true' : 'false' }} ? 'disabled' : '' }>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm loc-input" data-index="${index}" data-field="phone" value="${loc.phone || ''}" placeholder="021-3898237" style="font-size: 13px;" ${ {{ !$canEdit ? 'true' : 'false' }} ? 'disabled' : '' }>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-loc-btn" data-index="${index}" ${ {{ !$canEdit ? 'true' : 'false' }} ? 'disabled' : '' }>
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;
                locationsTableBody.appendChild(row);
            });

            // Attach listeners
            document.querySelectorAll('.loc-input').forEach(input => {
                input.addEventListener('input', function() {
                    const idx = parseInt(this.getAttribute('data-index'));
                    const field = this.getAttribute('data-field');
                    locations[idx][field] = this.value;
                    locationsInput.value = JSON.stringify(locations);
                });
            });

            document.querySelectorAll('.remove-loc-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = parseInt(this.getAttribute('data-index'));
                    locations.splice(idx, 1);
                    locationsInput.value = JSON.stringify(locations);
                    renderLocations();
                });
            });
        }

        if (addLocationBtn) {
            addLocationBtn.addEventListener('click', function() {
                locations.push({ name: '', address: '', phone: '' });
                locationsInput.value = JSON.stringify(locations);
                renderLocations();
            });
        }

        renderLocations();
    });
</script>
@endsection
