@extends('admin_panel.layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">ERP Settings</h3>
                    </div>
                    <div class="card-body">
                        <!-- Advanced Settings & Navigation -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-muted mb-3 font-weight-bold text-uppercase"
                                style="font-size: 0.8rem; letter-spacing: 1px;">Advanced Actions</h6>
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('settings.return-policy') }}"
                                    class="btn btn-outline-primary mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-undo-alt mr-2"></i> Return Policy
                                </a>
                                <a href="{{ route('settings.return-approvers') }}"
                                    class="btn btn-outline-info mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-user-shield mr-2"></i> Return Approvers
                                </a>
                                <a href="{{ route('settings.db_backup') }}"
                                    class="btn btn-success mr-2 mb-2 shadow-sm font-weight-bold"
                                    id="btnTopDbBackup"
                                    title="Export and download complete database SQL file">
                                    <i class="fas fa-database mr-2"></i> Download Database Backup
                                </a>
                                <a href="#" class="btn btn-outline-dark mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-exchange-alt mr-2"></i> Switch Account
                                </a>
                            </div>
                        </div>

                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="company-tab" data-toggle="tab" href="#company"
                                    role="tab">
                                    <i class="fas fa-building"></i> Company
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sales-tab" data-toggle="tab" href="#sales" role="tab">
                                    <i class="fas fa-shopping-cart"></i> Sales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="inventory-tab" data-toggle="tab" href="#inventory" role="tab">
                                    <i class="fas fa-boxes"></i> Inventory
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="accounting-tab" data-toggle="tab" href="#accounting" role="tab">
                                    <i class="fas fa-calculator"></i> Accounting
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="backup-tab" data-toggle="tab" href="#backup" role="tab">
                                    <i class="fas fa-database"></i> Database Backup
                                </a>
                            </li>
                        </ul>

                        @php
                            $canEditSettings = !auth()->check() || auth()->user()->hasRole('Super Admin') || auth()->user()->can('settings.edit') || auth()->user()->can('settings.create');
                        @endphp
                        <form id="settingsForm" class="mt-4" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content" id="settingsTabContent">
                                <!-- Company Tab -->
                                <div class="tab-pane fade show active" id="company" role="tabpanel">
                                    <!-- Company Profile & Invoice Information Section -->
                                    <div class="card mb-4 border shadow-sm" style="background-color: #ffffff;">
                                        <div class="card-header bg-white font-weight-bold d-flex align-items-center" style="border-bottom: 2px solid #e2e8f0;">
                                            <i class="fas fa-building text-primary mr-2"></i> Company Information (Printed on Invoices & Receipts)
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">Company Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="settings[company_name]" class="form-control" value="{{ \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING') }}" required {{ !$canEditSettings ? 'disabled' : '' }}>
                                                    <small class="form-text text-muted">Official business / company name displayed on invoice headers</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">Primary Phone / Mobile</label>
                                                    <input type="text" name="settings[company_phone]" class="form-control" value="{{ \App\Models\Setting::get('company_phone', '92 336 1500082') }}" placeholder="e.g. 92 336 1500082" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                    <small class="form-text text-muted">Primary contact phone number printed on invoices</small>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> Company Address (Invoice Header) <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="settings[company_address]" class="form-control" rows="2" placeholder="Enter full official address (e.g. Street / Area / Industrial Zone, City, Pakistan)..." style="font-size: 14px; font-weight: 500;" required {{ !$canEditSettings ? 'disabled' : '' }}>{{ \App\Models\Setting::get('company_address', '') }}</textarea>
                                                    <small class="form-text text-muted">This address will be printed at the top of all Sale Invoices, Repair Job Sheets, and Purchase Orders.</small>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="font-weight-bold text-dark">Company Email</label>
                                                    <input type="email" name="settings[company_email]" class="form-control" value="{{ \App\Models\Setting::get('company_email', 'info@logictech.com.pk') }}" placeholder="info@logictech.com.pk" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                    <small class="form-text text-muted">Official contact email on invoices</small>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="font-weight-bold text-dark">NTN Number</label>
                                                    <input type="text" name="settings[company_ntn]" class="form-control" value="{{ \App\Models\Setting::get('company_ntn', '5561761-4') }}" placeholder="5561761-4" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                    <small class="form-text text-muted">National Tax Number on invoices</small>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="font-weight-bold text-dark">STRN Number</label>
                                                    <input type="text" name="settings[company_strn]" class="form-control" value="{{ \App\Models\Setting::get('company_strn', '') }}" placeholder="Optional STRN" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                    <small class="form-text text-muted">Sales Tax Registration Number</small>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="font-weight-bold text-dark">Currency Symbol</label>
                                                    <input type="text" name="settings[currency_symbol]" class="form-control" value="{{ \App\Models\Setting::get('currency_symbol', 'PKR') }}" placeholder="PKR" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                    <small class="form-text text-muted">Currency used in system (e.g. PKR)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Company Logo Section -->
                                    @php
                                        $companyLogo = \App\Models\Setting::getLogoUrl();
                                        $hasLogo = !empty($companyLogo);
                                        $companyStamp = \App\Models\Setting::getStampUrl();
                                        $hasStamp = !empty($companyStamp);
                                        $companySignature = \App\Models\Setting::getSignatureUrl();
                                        $hasSignature = !empty($companySignature);
                                    @endphp
                                    <div class="card mb-4 border shadow-sm" style="background-color: #f8fafc;">
                                        <div class="card-header bg-white font-weight-bold d-flex align-items-center">
                                            <i class="fas fa-image text-primary mr-2"></i> Company Logo (Sale Receipt & Invoice)
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted small mb-3">
                                                Upload your official company logo. When uploaded, this logo will appear at the top of thermal sale receipts and invoices.
                                            </p>
                                            <div class="row align-items-center">
                                                <div class="col-md-4 text-center mb-3 mb-md-0">
                                                    <div id="logoPreviewContainer" class="p-3 bg-white border rounded shadow-sm d-inline-flex align-items-center justify-content-center" style="min-width: 170px; min-height: 110px; width: 100%; max-width: 220px; height: 110px;">
                                                        <img id="logoPreview" 
                                                             src="{{ $hasLogo ? $companyLogo : '' }}" 
                                                             alt="Company Logo Preview" 
                                                             style="max-width: 100%; max-height: 90px; object-fit: contain; {{ !$hasLogo ? 'display: none;' : '' }}" />
                                                        <div id="noLogoText" class="text-muted text-center" style="{{ $hasLogo ? 'display: none;' : '' }}">
                                                            <i class="fas fa-image fa-2x d-block mb-1 text-secondary"></i>
                                                            <small>No Logo Uploaded</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="custom-file mb-2">
                                                        <input type="file" name="company_logo" id="companyLogoInput" class="custom-file-input" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                        <label class="custom-file-label text-truncate" for="companyLogoInput" id="companyLogoLabel">Choose logo image file...</label>
                                                    </div>
                                                    <input type="hidden" name="remove_company_logo" id="removeCompanyLogoInput" value="0">
                                                    
                                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemoveLogo" style="{{ $hasLogo ? '' : 'display: none;' }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                            <i class="fas fa-trash-alt mr-1"></i> Remove Logo
                                                        </button>
                                                        <small class="text-muted ml-md-2">Formats: PNG, JPG, WebP, SVG (Max: 4MB). Transparent PNG recommended.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Company E-Stamp & E-Signature Section -->
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border shadow-sm" style="background-color: #f8fafc;">
                                                <div class="card-header bg-white font-weight-bold d-flex align-items-center">
                                                    <i class="fas fa-stamp text-danger mr-2"></i> Official Company E-Stamp
                                                </div>
                                                <div class="card-body">
                                                    <p class="text-muted small mb-3">
                                                        Upload digital rubber stamp image (PNG with transparent background recommended) for auto-stamping invoices.
                                                    </p>
                                                    <div class="text-center mb-3">
                                                        <div id="stampPreviewContainer" class="p-3 bg-white border rounded shadow-sm d-inline-flex align-items-center justify-content-center" style="min-width: 150px; min-height: 100px; width: 100%; max-width: 200px; height: 100px;">
                                                            <img id="stampPreview" 
                                                                 src="{{ $hasStamp ? $companyStamp : '' }}" 
                                                                 alt="Company Stamp Preview" 
                                                                 style="max-width: 100%; max-height: 80px; object-fit: contain; {{ !$hasStamp ? 'display: none;' : '' }}" />
                                                            <div id="noStampText" class="text-muted text-center" style="{{ $hasStamp ? 'display: none;' : '' }}">
                                                                <i class="fas fa-stamp fa-2x d-block mb-1 text-secondary"></i>
                                                                <small>No Stamp Uploaded</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="custom-file mb-2">
                                                        <input type="file" name="company_stamp" id="companyStampInput" class="custom-file-input" accept="image/png, image/jpeg, image/jpg, image/webp" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                        <label class="custom-file-label text-truncate" for="companyStampInput" id="companyStampLabel">Choose stamp image...</label>
                                                    </div>
                                                    <input type="hidden" name="remove_company_stamp" id="removeCompanyStampInput" value="0">
                                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemoveStamp" style="{{ $hasStamp ? '' : 'display: none;' }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                            <i class="fas fa-trash-alt mr-1"></i> Remove Stamp
                                                        </button>
                                                        <small class="text-muted">Transparent PNG</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border shadow-sm" style="background-color: #f8fafc;">
                                                <div class="card-header bg-white font-weight-bold d-flex align-items-center">
                                                    <i class="fas fa-signature text-success mr-2"></i> Authorized E-Signature
                                                </div>
                                                <div class="card-body">
                                                    <p class="text-muted small mb-3">
                                                        Upload authorized signature image (PNG with transparent background recommended) for auto-signing invoices.
                                                    </p>
                                                    <div class="text-center mb-3">
                                                        <div id="sigPreviewContainer" class="p-3 bg-white border rounded shadow-sm d-inline-flex align-items-center justify-content-center" style="min-width: 150px; min-height: 100px; width: 100%; max-width: 200px; height: 100px;">
                                                            <img id="sigPreview" 
                                                                 src="{{ $hasSignature ? $companySignature : '' }}" 
                                                                 alt="E-Signature Preview" 
                                                                 style="max-width: 100%; max-height: 80px; object-fit: contain; {{ !$hasSignature ? 'display: none;' : '' }}" />
                                                            <div id="noSigText" class="text-muted text-center" style="{{ $hasSignature ? 'display: none;' : '' }}">
                                                                <i class="fas fa-signature fa-2x d-block mb-1 text-secondary"></i>
                                                                <small>No Signature Uploaded</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="custom-file mb-2">
                                                        <input type="file" name="company_signature" id="companySignatureInput" class="custom-file-input" accept="image/png, image/jpeg, image/jpg, image/webp" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                        <label class="custom-file-label text-truncate" for="companySignatureInput" id="companySignatureLabel">Choose signature image...</label>
                                                    </div>
                                                    <input type="hidden" name="remove_company_signature" id="removeCompanySignatureInput" value="0">
                                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemoveSignature" style="{{ $hasSignature ? '' : 'display: none;' }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                            <i class="fas fa-trash-alt mr-1"></i> Remove Signature
                                                        </button>
                                                        <small class="text-muted">Transparent PNG</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (isset($settings['company']))
                                        @foreach ($settings['company'] as $setting)
                                            @if (in_array($setting['key'], ['company_logo', 'company_stamp', 'company_signature', 'company_name', 'company_address', 'company_phone', 'company_email', 'company_ntn', 'company_strn', 'currency_symbol']))
                                                @continue
                                            @endif
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                @if ($setting['type'] === 'text')
                                                    <textarea name="settings[{{ $setting['key'] }}]" class="form-control" rows="3" {{ !$canEditSettings ? 'disabled' : '' }}>{{ $setting['value'] }}</textarea>
                                                @else
                                                    <input type="text" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @endif
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Sales Tab -->
                                <div class="tab-pane fade" id="sales" role="tabpanel">
                                    @if (isset($settings['sales']))
                                        @foreach ($settings['sales'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                @if ($setting['type'] === 'text')
                                                    <textarea name="settings[{{ $setting['key'] }}]" class="form-control" rows="3" {{ !$canEditSettings ? 'disabled' : '' }}>{{ $setting['value'] }}</textarea>
                                                @elseif($setting['type'] === 'integer')
                                                    <input type="number" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @else
                                                    <input type="text" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @endif
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Inventory Tab -->
                                <div class="tab-pane fade" id="inventory" role="tabpanel">
                                    @if (isset($settings['inventory']))
                                        @foreach ($settings['inventory'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                <input type="number" name="settings[{{ $setting['key'] }}]"
                                                    class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Accounting Tab -->
                                <div class="tab-pane fade" id="accounting" role="tabpanel">
                                    @if (isset($settings['accounting']))
                                        @foreach ($settings['accounting'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                <input type="text" name="settings[{{ $setting['key'] }}]"
                                                    class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                                <!-- Database Backup Tab -->
                                <div class="tab-pane fade" id="backup" role="tabpanel">
                                    <div class="card mb-4 border shadow-sm" style="background-color: #ffffff;">
                                        <div class="card-header bg-white font-weight-bold d-flex align-items-center" style="border-bottom: 2px solid #e2e8f0;">
                                            <i class="fas fa-database text-success mr-2"></i> Complete Database Export & Backup
                                        </div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-lg-8 mb-3 mb-lg-0">
                                                    <h5 class="font-weight-bold text-dark mb-2">Export Full MySQL Database (.sql)</h5>
                                                    <p class="text-muted mb-3" style="font-size: 0.92rem; line-height: 1.6;">
                                                        Aapke pure ERP software ka database backup generate aur download karein. Is backup file mein tamam tables, views, stock ledger, sales, accounts, customers, aur system settings shamil hain.
                                                    </p>
                                                    <div class="alert alert-info py-2 px-3 mb-3 d-inline-block shadow-none border" style="font-size: 0.85rem;">
                                                        <i class="fas fa-info-circle mr-1"></i> Format: <strong>.sql</strong> — Ready to restore directly via <strong>phpMyAdmin</strong> ya <strong>MySQL Workbench</strong> at any time.
                                                    </div>
                                                    <div class="mt-2">
                                                        <a href="{{ route('settings.db_backup') }}" class="btn btn-success btn-lg px-4 shadow font-weight-bold" id="btnPaneDbBackup">
                                                            <i class="fas fa-download mr-2"></i> Download Database Backup (.sql)
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="p-3 rounded border bg-light shadow-sm">
                                                        <h6 class="font-weight-bold text-dark mb-2 pb-1 border-bottom"><i class="fas fa-server text-primary mr-1"></i> Database Status</h6>
                                                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size: 0.85rem;">
                                                            <span class="text-muted">Connection:</span>
                                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Connected</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size: 0.85rem;">
                                                            <span class="text-muted">Database Name:</span>
                                                            <span class="font-weight-bold text-dark font-monospace">{{ config('database.connections.' . config('database.default', 'mysql') . '.database') }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size: 0.85rem;">
                                                            <span class="text-muted">Backup Type:</span>
                                                            <span class="font-weight-bold text-success">Full SQL Dump</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between py-1" style="font-size: 0.85rem;">
                                                            <span class="text-muted">Storage:</span>
                                                            <span class="text-muted">Direct Browser Stream</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <div class="mt-4">
                                @if($canEditSettings)
                                    <button type="submit" class="btn btn-primary" id="btnSaveSettings">
                                        <i class="fas fa-save mr-1"></i> Save Settings
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary" disabled style="opacity: 0.6; cursor: not-allowed;">
                                        <i class="fas fa-lock mr-1"></i> Save Settings (Read Only)
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Live preview logo on file select
                $('#companyLogoInput').on('change', function(e) {
                    var file = e.target.files[0];
                    if (file) {
                        $('#companyLogoLabel').text(file.name);
                        $('#removeCompanyLogoInput').val('0');
                        var reader = new FileReader();
                        reader.onload = function(evt) {
                            $('#logoPreview').attr('src', evt.target.result).show();
                            $('#noLogoText').hide();
                            $('#btnRemoveLogo').show();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Remove logo action
                $('#btnRemoveLogo').on('click', function() {
                    $('#companyLogoInput').val('');
                    $('#companyLogoLabel').text('Choose logo image file...');
                    $('#removeCompanyLogoInput').val('1');
                    $('#logoPreview').attr('src', '').hide();
                    $('#noLogoText').show();
                    $(this).hide();
                });

                // Stamp preview & remove
                $('#companyStampInput').on('change', function(e) {
                    var file = e.target.files[0];
                    if (file) {
                        $('#companyStampLabel').text(file.name);
                        $('#removeCompanyStampInput').val('0');
                        var reader = new FileReader();
                        reader.onload = function(evt) {
                            $('#stampPreview').attr('src', evt.target.result).show();
                            $('#noStampText').hide();
                            $('#btnRemoveStamp').show();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $('#btnRemoveStamp').on('click', function() {
                    $('#companyStampInput').val('');
                    $('#companyStampLabel').text('Choose stamp image...');
                    $('#removeCompanyStampInput').val('1');
                    $('#stampPreview').attr('src', '').hide();
                    $('#noStampText').show();
                    $(this).hide();
                });

                // Signature preview & remove
                $('#companySignatureInput').on('change', function(e) {
                    var file = e.target.files[0];
                    if (file) {
                        $('#companySignatureLabel').text(file.name);
                        $('#removeCompanySignatureInput').val('0');
                        var reader = new FileReader();
                        reader.onload = function(evt) {
                            $('#sigPreview').attr('src', evt.target.result).show();
                            $('#noSigText').hide();
                            $('#btnRemoveSignature').show();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $('#btnRemoveSignature').on('click', function() {
                    $('#companySignatureInput').val('');
                    $('#companySignatureLabel').text('Choose signature image...');
                    $('#removeCompanySignatureInput').val('1');
                    $('#sigPreview').attr('src', '').hide();
                    $('#noSigText').show();
                    $(this).hide();
                });

                $('#settingsForm').on('submit', function(e) {
                    e.preventDefault();

                    var submitBtn = $('#btnSaveSettings');
                    var origHtml = submitBtn.html();
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

                    var formData = new FormData(this);

                    $.ajax({
                        url: '{{ route('settings.update') }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            submitBtn.prop('disabled', false).html(origHtml);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            if (response.logo_url) {
                                $('#logoPreview').attr('src', response.logo_url).show();
                                $('#noLogoText').hide();
                                $('#btnRemoveLogo').show();
                                $('#removeCompanyLogoInput').val('0');
                                $('#companyLogoInput').val('');
                                $('#companyLogoLabel').text('Choose logo image file...');
                            } else if ($('#removeCompanyLogoInput').val() === '1') {
                                $('#logoPreview').attr('src', '').hide();
                                $('#noLogoText').show();
                                $('#btnRemoveLogo').hide();
                            }

                            if (response.stamp_url) {
                                $('#stampPreview').attr('src', response.stamp_url).show();
                                $('#noStampText').hide();
                                $('#btnRemoveStamp').show();
                                $('#removeCompanyStampInput').val('0');
                                $('#companyStampInput').val('');
                                $('#companyStampLabel').text('Choose stamp image...');
                            } else if ($('#removeCompanyStampInput').val() === '1') {
                                $('#stampPreview').attr('src', '').hide();
                                $('#noStampText').show();
                                $('#btnRemoveStamp').hide();
                            }

                            if (response.signature_url) {
                                $('#sigPreview').attr('src', response.signature_url).show();
                                $('#noSigText').hide();
                                $('#btnRemoveSignature').show();
                                $('#removeCompanySignatureInput').val('0');
                                $('#companySignatureInput').val('');
                                $('#companySignatureLabel').text('Choose signature image...');
                            } else if ($('#removeCompanySignatureInput').val() === '1') {
                                $('#sigPreview').attr('src', '').hide();
                                $('#noSigText').show();
                                $('#btnRemoveSignature').hide();
                            }
                        },
                        error: function(xhr) {
                            submitBtn.prop('disabled', false).html(origHtml);
                            var errorMsg = 'Failed to update settings';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errs = Object.values(xhr.responseJSON.errors).flat();
                                if (errs.length > 0) errorMsg = errs.join('\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMsg,
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
