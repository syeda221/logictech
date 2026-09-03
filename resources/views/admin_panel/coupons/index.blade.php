@extends('admin_panel.layout.app')

@section('content')
<style>
    .page-container {
        max-width: 1300px;
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
    .btn-outline-danger {
        border-color: #fca5a5;
        color: #ef4444;
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 500;
        background: transparent;
    }
    .btn-outline-danger:hover {
        background-color: #fee2e2;
        border-color: #ef4444;
        color: #b91c1c;
    }
    .btn-outline-indigo {
        border-color: #c7d2fe;
        color: #4f46e5;
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 500;
        background: transparent;
    }
    .btn-outline-indigo:hover {
        background-color: #e0e7ff;
        border-color: #4f46e5;
        color: #3730a3;
    }
    .alert-success {
        background-color: #d1fae5;
        border-color: #a7f3d0;
        color: #065f46;
        border-radius: 12px;
        padding: 16px 20px;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(6, 95, 70, 0.05);
    }
    .alert-danger {
        background-color: #fee2e2;
        border-color: #fca5a5;
        color: #991b1b;
        border-radius: 12px;
        padding: 16px 20px;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(153, 27, 27, 0.05);
    }
    .table-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
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
    .badge-soft-success {
        background-color: #d1fae5;
        color: #065f46;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
    }
    .badge-soft-danger {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
    }
    .badge-soft-indigo {
        background-color: #e0e7ff;
        color: #3730a3;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.785rem;
        text-transform: uppercase;
    }

    /* PREMIUM MODAL REDESIGN WITH MULTI-COLUMN LAYOUT */
    #addCouponModal .modal-dialog, 
    [id^="editCouponModal"] .modal-dialog {
        max-width: 680px !important;
        width: 680px !important;
    }
    
    @media (max-width: 768px) {
        #addCouponModal .modal-dialog, 
        [id^="editCouponModal"] .modal-dialog {
            max-width: 92% !important;
            width: auto !important;
            margin: 1.75rem auto !important;
        }
    }
    
    /* Fade + Scale Animation */
    .modal.fade .modal-dialog {
        transform: scale(0.95) translate3d(0, -20px, 0);
        opacity: 0;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease-out;
    }
    .modal.show .modal-dialog {
        transform: scale(1) translate3d(0, 0, 0);
        opacity: 1;
    }

    .modal-content {
        background-color: #ffffff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15);
        overflow: hidden;
    }
    .modal-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 32px;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }
    .modal-header .close {
        padding: 0;
        margin: 0;
        background: transparent;
        border: 0;
        font-size: 1.5rem;
        color: #94a3b8;
        cursor: pointer;
        line-height: 1;
        transition: color 0.2s;
    }
    .modal-header .close:hover {
        color: #0f172a;
    }
    .modal-body {
        padding: 24px 32px;
        background-color: #ffffff;
    }
    .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 20px 32px 24px 32px;
        background-color: #f8fafc;
        display: flex;
        gap: 16px;
    }
    .modal-footer .btn {
        height: 48px !important;
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .modal-footer .btn-secondary {
        flex: 2;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
    }
    .modal-footer .btn-secondary:hover {
        background-color: #f1f5f9;
        border-color: #94a3b8;
    }
    .modal-footer .btn-primary {
        flex: 3;
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
    }
    .modal-footer .btn-primary:hover {
        background-color: #4338ca;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
        display: block;
    }
    .form-control {
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        padding: 0 16px !important;
        font-size: 0.95rem !important;
        color: #1e293b !important;
        height: 48px !important;
        width: 100% !important;
        transition: all 0.2s ease-in-out !important;
        background-color: #ffffff !important;
        box-sizing: border-box !important;
    }
    .form-control:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12) !important;
        outline: 0 !important;
    }
    .form-control::placeholder {
        color: #94a3b8 !important;
        opacity: 0.8 !important;
    }
    
    /* Dynamic input symbol styling */
    .input-group-custom {
        display: flex;
        position: relative;
        width: 100%;
        height: 48px;
    }
    .input-addon-symbol {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-right: none;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
        color: #475569;
        font-weight: 600;
        font-size: 0.95rem;
        height: 48px;
        box-sizing: border-box;
    }
    .input-group-custom .form-control {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        flex: 1;
        height: 48px !important;
    }

    /* Custom select style */
    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 14px 10px;
        padding-right: 40px !important;
    }

    /* iOS SWITCH STYLING */
    .ios-switch-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        height: 48px; /* Vertical centering helper for form row */
    }
    .ios-switch-container {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
        cursor: pointer;
        margin: 0;
    }
    .ios-switch-input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .ios-switch-slider {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-radius: 34px;
    }
    .ios-switch-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    .ios-switch-input:checked + .ios-switch-slider {
        background-color: #4f46e5;
    }
    .ios-switch-input:checked + .ios-switch-slider:before {
        transform: translateX(24px);
    }
    .ios-switch-label {
        font-weight: 600;
        color: #334155;
        user-select: none;
        font-size: 0.9rem;
    }

    /* Form Validation overrides */
    .form-control.is-invalid {
        border-color: #ef4444 !important;
        background-image: none !important;
    }
    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12) !important;
    }
    .invalid-feedback-custom {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 6px;
        font-weight: 500;
    }
    .form-control.is-valid {
        border-color: #10b981 !important;
    }
</style>

<div class="page-container mt-3">
    <div class="d-flex align-items-center justify-content-between mb-4 page-header">
        <div>
            <h4 class="fw-bold mb-0">Coupons Management</h4>
            <small class="text-muted">Create and manage coupon codes for promotional discounts.</small>
        </div>
        @canany(['coupons.create', 'coupons.add'])
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCouponModal">
            <i class="fas fa-plus me-2"></i> Add New Coupon
        </button>
        @endcanany
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4 border" role="alert">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start mb-4 border" role="alert">
            <i class="fas fa-exclamation-circle me-3 fs-4 mt-1"></i>
            <div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min Spend</th>
                            <th>Max Uses</th>
                            <th>Uses</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">{{ $coupon->id }}</td>
                            <td><span class="fw-bold text-slate-800 fs-6">{{ $coupon->code }}</span></td>
                            <td>
                                <span class="badge-soft-indigo">{{ $coupon->type }}</span>
                            </td>
                            <td class="fw-bold text-slate-800">
                                @if($coupon->type == 'percent')
                                    {{ $coupon->value }}%
                                @else
                                    Rs. {{ number_format($coupon->value, 2) }}
                                @endif
                            </td>
                            <td class="fw-medium text-slate-600">
                                @if($coupon->min_spend)
                                    Rs. {{ number_format($coupon->min_spend, 2) }}
                                @else
                                    <span class="text-muted small">No Minimum</span>
                                @endif
                            </td>
                            <td class="fw-medium text-slate-600">{{ $coupon->max_uses ?? 'Unlimited' }}</td>
                            <td class="fw-semibold text-slate-700">{{ $coupon->uses }}</td>
                            <td>
                                @if($coupon->is_active)
                                    <span class="badge-soft-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                                @else
                                    <span class="badge-soft-danger"><i class="fas fa-times-circle me-1"></i> Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    @can('coupons.edit')
                                    <button class="btn btn-sm btn-outline-indigo" data-toggle="modal" data-target="#editCouponModal{{ $coupon->id }}">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    @endcan
                                    @can('coupons.delete')
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this coupon?')">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($coupons as $coupon)
<!-- Edit Modal -->
<div class="modal fade" id="editCouponModal{{ $coupon->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit text-indigo me-2"></i> Edit Coupon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Coupon Code</label>
                                <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" placeholder="e.g. SAVE20" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Discount Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Discount Value</label>
                                <div class="input-group-custom">
                                    <span class="input-addon-symbol">Rs.</span>
                                    <input type="number" step="0.01" name="value" class="form-control" value="{{ $coupon->value }}" placeholder="Enter percentage or fixed amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Minimum Spend</label>
                                <input type="number" step="0.01" name="min_spend" class="form-control" value="{{ $coupon->min_spend }}" placeholder="Leave empty for no limit">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Maximum Uses</label>
                                <input type="number" name="max_uses" class="form-control" value="{{ $coupon->max_uses }}" placeholder="Leave empty for unlimited">
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-group mb-0 mt-3">
                                <div class="ios-switch-wrapper">
                                    <label class="ios-switch-container">
                                        <input type="checkbox" class="ios-switch-input" id="customSwitch{{ $coupon->id }}" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }}>
                                        <span class="ios-switch-slider"></span>
                                    </label>
                                    <span class="ios-switch-label" for="customSwitch{{ $coupon->id }}">Active Status</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Add Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle text-indigo me-2"></i> Add New Coupon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Coupon Code</label>
                                <input type="text" name="code" class="form-control" placeholder="e.g. SUMMERSALE" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Discount Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="fixed">Fixed Amount</option>
                                    <option value="percent">Percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Discount Value</label>
                                <div class="input-group-custom">
                                    <span class="input-addon-symbol" id="symbolAdd">Rs.</span>
                                    <input type="number" step="0.01" name="value" class="form-control" placeholder="Enter percentage or fixed amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Minimum Spend</label>
                                <input type="number" step="0.01" name="min_spend" class="form-control" placeholder="Leave empty for no limit">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Maximum Uses</label>
                                <input type="number" name="max_uses" class="form-control" placeholder="Leave empty for unlimited">
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-group mb-0 mt-3">
                                <div class="ios-switch-wrapper">
                                    <label class="ios-switch-container">
                                        <input type="checkbox" class="ios-switch-input" id="customSwitchAdd" name="is_active" value="1" checked>
                                        <span class="ios-switch-slider"></span>
                                    </label>
                                    <span class="ios-switch-label" for="customSwitchAdd">Active Status</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Coupon</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Function to update symbol
        function updateSymbol(selectEl, symbolEl) {
            let val = $(selectEl).val();
            if (val === 'percent') {
                $(symbolEl).text('%');
            } else {
                $(symbolEl).text('Rs.');
            }
        }

        // Add modal symbol update
        $('#addCouponModal select[name="type"]').on('change', function() {
            updateSymbol(this, '#symbolAdd');
        });
        // Run on load
        updateSymbol('#addCouponModal select[name="type"]', '#symbolAdd');

        // Edit modal symbol updates
        $('[id^="editCouponModal"]').each(function() {
            let modal = $(this);
            let select = modal.find('select[name="type"]');
            let symbol = modal.find('.input-addon-symbol');
            
            select.on('change', function() {
                updateSymbol(this, symbol);
            });
            // Run on load
            updateSymbol(select, symbol);
        });
    });
</script>
@endsection
