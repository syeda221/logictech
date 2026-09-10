@extends('admin_panel.layout.app')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Bookings Management</h4>
                        <p class="text-muted mb-0 small">View and manage your product bookings</p>
                    </div>
                    <div>
                        @can('bookings.create')
                            <a href="{{ route('sale.add') }}?type=booking" class="btn btn-primary px-4 shadow-sm fw-medium align-items-center gap-2">
                                <i class="fas fa-plus"></i> Add Booking
                            </a>
                        @endcan
                    </div>
                </div>

                <style>
                    /* Dropdown overflow fix — prevent card/table-responsive from clipping dropdown menus */
                    .card { overflow: visible !important; }
                    .table-responsive { min-height: 380px; padding-bottom: 120px; overflow-x: auto; }
                    .table-responsive .dropdown-menu { z-index: 1060 !important; }
                </style>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="bookings-table" class="table table-hover align-middle datanew" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 rounded-start text-secondary fw-semibold text-uppercase small">ID</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Customer</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Reference</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Product</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-center">Qty</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Price</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-center">Discount</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Total</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Status</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">State</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Booking Date</th>
                                        <th class="py-3 pe-3 rounded-end text-secondary fw-semibold text-uppercase small text-center" style="min-width: 170px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        @php
                                            $bkCustDisplayName = optional($booking->customer_relation)->customer_name;
                                            $bkIsWalkin = false;
                                            if (empty($bkCustDisplayName)) {
                                                if (!empty($booking->walkin_name)) {
                                                    $bkCustDisplayName = $booking->walkin_name;
                                                    $bkIsWalkin = true;
                                                } else {
                                                    $bkCustDisplayName = 'Walk-in Customer';
                                                    $bkIsWalkin = true;
                                                }
                                            }
                                            $bkCustInitial = strtoupper(substr($bkCustDisplayName, 0, 1));
                                        @endphp
                                        <tr class="border-bottom-0">
                                            <td class="ps-3 fw-bold text-muted font-monospace">#{{ $booking->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle {{ $bkIsWalkin ? 'bg-secondary-subtle text-secondary' : 'bg-info-subtle text-info' }} me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width: 32px; height: 32px; font-size: 14px; background-color: {{ $bkIsWalkin ? '#f1f5f9' : '#e0f2fe' }}; color: {{ $bkIsWalkin ? '#475569' : '#0369a1' }};">
                                                        {{ $bkCustInitial }}
                                                    </div>
                                                    <div>
                                                        <span class="fw-medium text-dark">{{ $bkCustDisplayName }}</span>
                                                        @if ($bkIsWalkin && !empty($booking->walkin_name))
                                                            <span class="badge bg-light text-muted border ms-1" style="font-size: 9px; font-weight: 600;">Walk-in</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="font-monospace text-dark">{{ $booking->reference ?? '-' }}</td>
                                            <td class="text-muted small">
                                                @foreach ($booking->items as $item)
                                                    {{ optional($item->product)->item_name ?? 'N/A' }} <br>
                                                @endforeach
                                            </td>
                                            <td class="text-center font-monospace small">
                                                @foreach ($booking->items as $item)
                                                    {{ $item->qty }} <br>
                                                @endforeach
                                            </td>
                                            <td class="text-end font-monospace small">
                                                @foreach ($booking->items as $item)
                                                    {{ number_format($item->price, 2) }} <br>
                                                @endforeach
                                            </td>
                                            <td class="text-center text-danger small">
                                                @foreach ($booking->items as $item)
                                                    {{ $item->discount_percent }}% <br>
                                                @endforeach
                                            </td>
                                            <td class="text-end text-success fw-bold font-monospace">
                                                {{ number_format($booking->total_net, 2) }}
                                            </td>
                                            <td class="text-nowrap">
                                                @if ($booking->sale_status === 'booked')
                                                    <span class="badge badge-warning text-dark border border-warning px-2 py-1" style="font-size: 0.8rem; font-weight: 600;"><i class="fas fa-bookmark me-1"></i>Booked</span>
                                                @elseif ($booking->sale_status === 'posted')
                                                    <span class="badge badge-success border border-success px-2 py-1" style="font-size: 0.8rem; font-weight: 600;"><i class="fas fa-check-circle me-1"></i>Confirmed</span>
                                                @else
                                                    <span class="badge badge-secondary border border-secondary px-2 py-1" style="font-size: 0.8rem; font-weight: 600;">{{ ucfirst($booking->sale_status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap sale-state-cell" data-sale-id="{{ $booking->id }}">
                                                @php
                                                    $bkOrdStatus = strtolower($booking->order_status ?? 'pending');
                                                @endphp
                                                @if ($bkOrdStatus === 'ready')
                                                    <span class="badge text-white px-2 py-1 shadow-sm" style="background-color: #2563eb; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.3px;"><i class="fas fa-box me-1"></i>Ready</span>
                                                @elseif ($bkOrdStatus === 'delivered')
                                                    <span class="badge text-white px-2 py-1 shadow-sm" style="background-color: #10b981; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.3px;"><i class="fas fa-truck me-1"></i>Delivered</span>
                                                @elseif ($bkOrdStatus === 'cancelled')
                                                    <span class="badge text-dark px-2 py-1 shadow-sm" style="background-color: #f59e0b; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.3px;"><i class="fas fa-ban me-1"></i>Cancelled</span>
                                                @else
                                                    <span class="badge text-white px-2 py-1 shadow-sm" style="background-color: #ef4444; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.3px;"><i class="fas fa-hourglass-half me-1"></i>Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap small text-muted">
                                                {{ $booking->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="pe-3 text-center text-nowrap">
                                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                                    @can('sales.edit')
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-light border dropdown-toggle shadow-sm d-inline-flex align-items-center gap-1"
                                                                type="button" data-toggle="dropdown" aria-expanded="false"
                                                                style="font-size: 11px; font-weight: 700; height: 32px; border-radius: 6px; padding: 0 10px; text-transform: uppercase; letter-spacing: 0.5px;" title="Change State">
                                                                <i class="fas fa-tasks text-primary me-1"></i> State
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3 py-2" style="min-width: 170px; z-index: 1055;">
                                                                <li><h6 class="dropdown-header text-uppercase fw-bold text-muted px-3 py-1" style="font-size: 10px; letter-spacing: 0.5px;">Select State</h6></li>
                                                                <li><a class="dropdown-item btn-change-order-status d-flex align-items-center gap-2 py-2 px-3 {{ $bkOrdStatus === 'pending' ? 'active fw-bold' : '' }}" href="javascript:void(0)" onclick="changeSaleOrderStatus(event, {{ $booking->id }}, 'pending', this, '{{ $booking->sale_status }}')" data-id="{{ $booking->id }}" data-status="pending"><span class="badge rounded-circle p-1" style="background-color: #ef4444; width: 9px; height: 9px; display: inline-block;"></span> 🔴 Pending</a></li>
                                                                <li><a class="dropdown-item btn-change-order-status d-flex align-items-center gap-2 py-2 px-3 {{ $bkOrdStatus === 'ready' ? 'active fw-bold' : '' }}" href="javascript:void(0)" onclick="changeSaleOrderStatus(event, {{ $booking->id }}, 'ready', this, '{{ $booking->sale_status }}')" data-id="{{ $booking->id }}" data-status="ready"><span class="badge rounded-circle p-1" style="background-color: #2563eb; width: 9px; height: 9px; display: inline-block;"></span> 🔵 Ready</a></li>
                                                                <li><a class="dropdown-item btn-change-order-status d-flex align-items-center gap-2 py-2 px-3 {{ $bkOrdStatus === 'delivered' ? 'active fw-bold' : '' }}" href="javascript:void(0)" onclick="changeSaleOrderStatus(event, {{ $booking->id }}, 'delivered', this, '{{ $booking->sale_status }}')" data-id="{{ $booking->id }}" data-status="delivered"><span class="badge rounded-circle p-1" style="background-color: #10b981; width: 9px; height: 9px; display: inline-block;"></span> 🟢 Delivered</a></li>
                                                                <li><a class="dropdown-item btn-change-order-status d-flex align-items-center gap-2 py-2 px-3 {{ $bkOrdStatus === 'cancelled' ? 'active fw-bold' : '' }}" href="javascript:void(0)" onclick="changeSaleOrderStatus(event, {{ $booking->id }}, 'cancelled', this, '{{ $booking->sale_status }}')" data-id="{{ $booking->id }}" data-status="cancelled"><span class="badge rounded-circle p-1" style="background-color: #f59e0b; width: 9px; height: 9px; display: inline-block;"></span> 🟡 Cancelled</a></li>
                                                            </ul>
                                                        </div>
                                                    @endcan

                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light border dropdown-toggle shadow-sm"
                                                            type="button" data-toggle="dropdown" aria-expanded="false"
                                                            style="font-size: 11px; font-weight: 700; height: 32px; border-radius: 6px; padding: 0 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                                                            <i class="fas fa-ellipsis-v small me-1"></i> Actions
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">
                                                            @if ($booking->sale_status === 'booked')
                                                                <li>
                                                                    <form action="{{ route('sales.confirm', $booking->id) }}" method="POST" class="d-inline confirm-booking-form">
                                                                        @csrf
                                                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 text-success confirm-booking-btn">
                                                                            <i class="fas fa-check-circle fa-fw text-success"></i> Confirm Order
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.edit', $booking->id) }}">
                                                                        <i class="fas fa-edit text-primary fa-fw"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', $booking->id) }}" target="_blank">
                                                                        <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', $booking->id) }}" target="_blank">
                                                                        <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc', $booking->id) }}" target="_blank">
                                                                        <i class="fas fa-shipping-fast text-warning fa-fw"></i> DC Receipt
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.receipt', $booking->id) }}" target="_blank">
                                                                        <i class="fas fa-receipt text-success fa-fw"></i> View Receipt
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
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
        </div>
    </div>

    {{-- Delivery Product Specifications Modal --}}
    @include('admin_panel.sale.partials.delivery_specs_modal')
@endsection

@section('js')
    <script src="{{ asset('assets/vendors/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script>
        // Global function for instant order state change
        window.changeSaleOrderStatus = function(e, saleId, newStatus, el, saleStatus) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            if (!saleId || !newStatus) {
                console.error('Missing saleId or newStatus', saleId, newStatus);
                return;
            }

            // Save previous radio value before changing
            var prevStatus = $('input[name="order_state_' + saleId + '"]:checked').val()
                          || $('input[name="m_order_state_' + saleId + '"]:checked').val()
                          || 'pending';

            // Close active dropdowns
            $('.dropdown-menu.show').removeClass('show');
            $('.dropdown.show').removeClass('show');
            $('[data-toggle="dropdown"]').attr('aria-expanded', 'false');

            // If Delivered status is selected, check booking status first
            if (newStatus === 'delivered') {
                // Block if sale is still booked or draft - must be confirmed first
                if (saleStatus === 'booked' || saleStatus === 'draft') {
                    // Revert radio back to previous state
                    $('input[name="order_state_' + saleId + '"][value="' + prevStatus + '"], input[name="m_order_state_' + saleId + '"][value="' + prevStatus + '"]').prop('checked', true);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirm Order First!',
                        text: 'This sale has not been confirmed yet. Please confirm the order before marking it as delivered.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b',
                    });
                    return;
                }
                window.openDeliverySpecsModal(saleId);
                return;
            }

            let directUrl = '{{ url("sales") }}/' + saleId + '/order-status';
            let fallbackUrl = '{{ url("sale") }}/' + saleId + '/order-status';
            let pathLoc = window.location.pathname.replace(/\/(sale|sales|bookings)(\/.*)?$/i, '');
            let relativeUrl = (pathLoc ? pathLoc : '') + '/sales/' + saleId + '/order-status';

            function sendUpdate(urlToTry, nextFallback) {
                $.ajax({
                    url: urlToTry,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_status: newStatus
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response && response.success) {
                            $('.sale-state-cell[data-sale-id="' + saleId + '"]').html(response.badge_html);
                            $('[data-id="' + saleId + '"].btn-change-order-status').removeClass('active fw-bold');
                            $('[data-id="' + saleId + '"][data-status="' + newStatus + '"].btn-change-order-status').addClass('active fw-bold');

                            if (typeof Swal !== 'undefined') {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message || 'State updated successfully!'
                                });
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', response.message || 'Failed to update state.', 'error');
                            } else {
                                alert(response.message || 'Failed to update state.');
                            }
                        }
                    },
                    error: function(xhr) {
                        if (nextFallback) {
                            nextFallback();
                        } else {
                            let msg = 'Failed to update state.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', msg, 'error');
                            } else {
                                alert(msg);
                            }
                        }
                    }
                });
            }

            // Try directUrl -> fallbackUrl -> relativeUrl
            sendUpdate(directUrl, function() {
                sendUpdate(fallbackUrl, function() {
                    sendUpdate(relativeUrl, null);
                });
            });
        };

        // Global function to open Delivery Specs Modal
        window.openDeliverySpecsModal = function(saleId) {
            if (!saleId) return;

            // Ensure modal is attached to body so it is always on top without z-index/overflow clipping
            if ($('#deliverySpecsModal').parent()[0] !== document.body) {
                $('#deliverySpecsModal').appendTo('body');
            }

            $('#modalDeliverySaleId').val(saleId);
            $('#modalDeliveryLoader').removeClass('d-none');
            $('#modalDeliveryContent').addClass('d-none');
            $('#modalDeliveryItemsContainer').empty();
            $('#btnSubmitDeliverySpecs').prop('disabled', true);

            // Open modal using Bootstrap / jQuery
            try {
                if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                    $('#deliverySpecsModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                    $('#deliverySpecsModal').modal('show');
                } else if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                    const modalEl = document.getElementById('deliverySpecsModal');
                    const modalInst = window.bootstrap.Modal.getInstance(modalEl) || new window.bootstrap.Modal(modalEl);
                    modalInst.show();
                } else {
                    $('#deliverySpecsModal').modal('show');
                }
            } catch(e) {
                console.error('Modal show error', e);
                $('#deliverySpecsModal').modal('show');
            }

            let directUrl = '{{ url("sales") }}/' + saleId + '/delivery-details';
            let fallbackUrl = '{{ url("sale") }}/' + saleId + '/delivery-details';
            let pathLoc = window.location.pathname.replace(/\/(sale|sales|bookings)(\/.*)?$/i, '');
            let relativeUrl = (pathLoc ? pathLoc : '') + '/sales/' + saleId + '/delivery-details';

            function fetchSpecs(urlToTry, nextFallback) {
                $.ajax({
                    url: urlToTry,
                    type: 'GET',
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(res) {
                        if (res && res.success && res.sale) {
                            $('#modalDeliveryInvoiceNo').text('#' + res.sale.id + ' (' + res.sale.invoice_no + ')');
                            $('#modalDeliveryCustomer').text(res.sale.customer_name || 'Walk-in Customer');
                            $('#modalDeliveryDate').val(res.sale.delivery_date || new Date().toISOString().split('T')[0]);
                            $('#modalDeliverySource').val(res.sale.delivery_source || '');
                            $('#modalDeliveryRemarks').val(res.sale.delivery_remarks || '');
                            $('#modalDeliveryItemsCount').text(res.sale.items.length);

                            let html = '';
                            if (res.sale.items.length === 0) {
                                html = '<div class="alert alert-warning py-2 text-center small">No items found for this booking.</div>';
                            } else {
                                res.sale.items.forEach(function(item, idx) {
                                    let techNameVal = item.technical_name || item.product_name || '';
                                    html += `
                                    <div class="card border rounded-3 p-3 bg-white shadow-sm mb-3" style="border-color: #cbd5e1 !important;">
                                        {{-- Item Header --}}
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                            <div class="fw-bold text-dark d-flex align-items-center flex-wrap gap-1" style="font-size: 0.86rem;">
                                                <span class="badge bg-primary text-white font-monospace px-2 py-1">Item #${idx + 1}</span>
                                                <span class="text-dark fw-bold ms-1">${item.product_name}</span>
                                                ${item.brand ? '<span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">' + item.brand + '</span>' : ''}
                                                ${item.item_code ? '<span class="badge bg-light text-muted border font-monospace" style="font-size: 0.72rem;">SKU: ' + item.item_code + '</span>' : ''}
                                            </div>
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold font-monospace" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.76rem;">
                                                Quantity: ${parseFloat(item.qty) || item.qty}
                                            </span>
                                        </div>

                                        {{-- Side-by-Side Dual Card Grid --}}
                                        <div class="row g-3">
                                            {{-- LEFT CARD: Image 1 - Company Technical Document Card --}}
                                            <div class="col-lg-6">
                                                <div class="h-100 p-3 rounded-3 border" style="background-color: #f8fafc; border-color: #93c5fd !important; border-top: 3px solid #2563eb !important;">
                                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom" style="border-color: #e2e8f0 !important;">
                                                        <span class="fw-bold text-primary small d-flex align-items-center gap-1.5">
                                                            <i class="fas fa-file-contract"></i>
                                                            <span>Company Technical Document</span>
                                                        </span>
                                                        <span class="badge bg-primary text-white" style="font-size: 0.65rem; letter-spacing: 0.3px;">INTERNAL SPEC SHEET</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.73rem;">
                                                            Equipment / Technical Title <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control form-control-sm fw-bold bg-white text-dark" name="items[${item.id}][technical_name]" value="${techNameVal}" placeholder='e.g. Induction Heater 60 kw for Forging' required>
                                                        <div class="text-muted" style="font-size: 0.68rem;">Company technical sheet par title print hoga</div>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.73rem;">
                                                            Technical Specifications &amp; Parameters
                                                        </label>
                                                        <textarea class="form-control form-control-sm bg-white" rows="2" name="items[${item.id}][technical_specs]" placeholder="e.g. Power: 60KW, Input: 380V 3-Phase, Frequency: 30-100kHz, Water Cooled, Custom Coil">${item.technical_specs || ''}</textarea>
                                                    </div>

                                                    <div class="mb-0">
                                                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.73rem;">
                                                            Technical Details, QC &amp; Engineering Remarks
                                                        </label>
                                                        <input type="text" class="form-control form-control-sm bg-white" name="items[${item.id}][technical_remarks]" value="${item.technical_remarks || ''}" placeholder="e.g. Tested on full load, tuned coil, ready for dispatch">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- RIGHT CARD: Image 2 - Customer Sale Invoice Card --}}
                                            <div class="col-lg-6">
                                                <div class="h-100 p-3 rounded-3 border" style="background-color: #fcfdfd; border-color: #86efac !important; border-top: 3px solid #10b981 !important;">
                                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom" style="border-color: #e2e8f0 !important;">
                                                        <span class="fw-bold text-success small d-flex align-items-center gap-1.5">
                                                            <i class="fas fa-file-invoice"></i>
                                                            <span>Sale Invoice Specifications</span>
                                                        </span>
                                                        <span class="badge bg-success text-white" style="font-size: 0.65rem; letter-spacing: 0.3px;">CUSTOMER PRINT</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.73rem;">Model / Specifications</label>
                                                        <input type="text" class="form-control form-control-sm fw-semibold bg-white" name="items[${item.id}][model]" value="${item.model || ''}" placeholder="e.g. LTZ-60KW, 3-Phase 380V">
                                                        <div class="text-muted" style="font-size: 0.68rem;">Customer invoice ke Model field me aayega</div>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.73rem;">Serial Number / Unique Code</label>
                                                        <input type="text" class="form-control form-control-sm font-monospace fw-bold text-primary bg-white" name="items[${item.id}][serial_no]" value="${item.serial_no || ''}" placeholder="e.g. SN-2026-00891">
                                                    </div>

                                                    <div class="mb-0">
                                                        <label class="form-label text-secondary fw-bold mb-1" style="font-size: 0.73rem;">Configuration / Notes / Specs</label>
                                                        <input type="text" class="form-control form-control-sm bg-white" name="items[${item.id}][specs]" value="${item.specs || ''}" placeholder="e.g. T9 sport, 50Hz Water Cooled">
                                                        <div class="text-muted" style="font-size: 0.68rem;">Customer invoice par specifications me aayega</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                });
                            }

                            $('#modalDeliveryItemsContainer').html(html);
                            $('#modalDeliveryLoader').addClass('d-none');
                            $('#modalDeliveryContent').removeClass('d-none');
                            $('#btnSubmitDeliverySpecs').prop('disabled', false);
                        } else {
                            if (nextFallback) nextFallback();
                            else $('#modalDeliveryLoader').html('<div class="text-danger py-3"><i class="fas fa-exclamation-triangle me-1"></i> Failed to retrieve item specifications.</div>');
                        }
                    },
                    error: function() {
                        if (nextFallback) nextFallback();
                        else $('#modalDeliveryLoader').html('<div class="text-danger py-3"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load item details. Please check network.</div>');
                    }
                });
            }

            fetchSpecs(directUrl, function() {
                fetchSpecs(fallbackUrl, function() {
                    fetchSpecs(relativeUrl, null);
                });
            });
        };

        $(document).ready(function() {
            let submenuHoverTimer = null;

            // Hover into submenu or toggle button: show instantly and clear hide timer
            $(document).on('mouseenter', '.dropdown-submenu', function() {
                clearTimeout(submenuHoverTimer);
                let $submenu = $(this);
                $('.dropdown-submenu').not($submenu).removeClass('show is-hovered');
                $submenu.addClass('show is-hovered');
            });

            // Hover out: 1-second (1000ms) grace period delay before closing to prevent accidental collapse
            $(document).on('mouseleave', '.dropdown-submenu', function() {
                let $submenu = $(this);
                submenuHoverTimer = setTimeout(function() {
                    $submenu.removeClass('show is-hovered');
                }, 1000);
            });

            // Click toggle support
            $(document).on('click', '.dropdown-submenu-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearTimeout(submenuHoverTimer);
                let $submenu = $(this).closest('.dropdown-submenu');
                $('.dropdown-submenu').not($submenu).removeClass('show is-hovered');
                $submenu.toggleClass('show');
            });

            // Prevent dropdown from closing prematurely when clicking within submenu header/options
            $(document).on('click', '.dropdown-submenu-menu', function(e) {
                e.stopPropagation();
            });

            // Clean up when parent action dropdown is closed
            $(document).on('hidden.bs.dropdown', '.dropdown', function() {
                clearTimeout(submenuHoverTimer);
                $(this).find('.dropdown-submenu').removeClass('show is-hovered');
            });

            // Auto-dropup: flip dropdown upward if it would overflow the viewport bottom
            $(document).on('show.bs.dropdown', function(e) {
                var $toggle = $(e.target);
                var $menu = $toggle.next('.dropdown-menu');
                if (!$menu.length) return;
                var toggleBottom = $toggle[0].getBoundingClientRect().bottom;
                var menuHeight = $menu.outerHeight() || 200;
                var viewportHeight = $(window).height();
                if (toggleBottom + menuHeight > viewportHeight - 20) {
                    $menu.addClass('dropdown-menu-up');
                    $menu.css({ top: 'auto', bottom: '100%' });
                } else {
                    $menu.removeClass('dropdown-menu-up');
                    $menu.css({ top: '', bottom: '' });
                }
            });

            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().destroy();
            }
            $('.datanew').DataTable({
                "pageLength": 10,
                "order": [],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search bookings..."
                },
                "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });

            // Submit Delivery Specifications Form (delegated - modal is dynamically appended to body)
            $(document).on('submit', '#formDeliverySpecs', function(e) {
                e.preventDefault();
                const saleId = $('#modalDeliverySaleId').val();
                if (!saleId) return;

                const $btn = $('#btnSubmitDeliverySpecs');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving Specs...');

                let directUrl = '{{ url("sales") }}/' + saleId + '/delivery-details';
                let fallbackUrl = '{{ url("sale") }}/' + saleId + '/delivery-details';
                let pathLoc = window.location.pathname.replace(/\/(sale|sales|bookings)(\/.*)?$/i, '');
                let relativeUrl = (pathLoc ? pathLoc : '') + '/sales/' + saleId + '/delivery-details';

                function sendDelivery(urlToTry, nextFallback) {
                    $.ajax({
                        url: urlToTry,
                        type: 'POST',
                        data: $('#formDeliverySpecs').serialize(),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            $btn.prop('disabled', false).html(origHtml);
                            if (response && response.success) {
                                // Close modal first - BS4 jQuery approach + cleanup
                                $('#deliverySpecsModal').modal('hide');
                                setTimeout(function() {
                                    $('#deliverySpecsModal').removeClass('show').css('display', 'none');
                                    $('.modal-backdrop').remove();
                                    $('body').removeClass('modal-open').css('padding-right', '');
                                }, 300);

                                let docUrl = response.technical_doc_url || ('{{ url("sales") }}/' + saleId + '/technical-doc');

                                // Show SweetAlert with direct link to Technical Document
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Specifications Saved!',
                                        html: `<p class="mb-3 text-muted" style="font-size: 0.95rem;">${response.message || 'Specifications saved &amp; Technical Document created!'}</p>
                                               <a href="${docUrl}" target="_blank" class="btn btn-primary btn-sm px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="border-radius: 6px;">
                                                   <i class="fas fa-file-contract"></i> View Technical Document
                                               </a>`,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Done / Reload Page',
                                        confirmButtonColor: '#10b981',
                                    }).then(function() {
                                        window.location.reload();
                                    });
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                if (nextFallback) {
                                    nextFallback();
                                } else {
                                    if (typeof Swal !== 'undefined') Swal.fire('Error', response.message || 'Failed to save specifications.', 'error');
                                    else alert(response.message || 'Failed to save specifications.');
                                }
                            }
                        },
                        error: function(xhr) {
                            if (nextFallback) {
                                nextFallback();
                            } else {
                                $btn.prop('disabled', false).html(origHtml);
                                let msg = 'Failed to save specifications.';
                                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                                if (typeof Swal !== 'undefined') Swal.fire('Error', msg, 'error');
                                else alert(msg);
                            }
                        }
                    });
                }

                sendDelivery(directUrl, function() {
                    sendDelivery(fallbackUrl, function() {
                        sendDelivery(relativeUrl, null);
                    });
                });
            });

            $(document).on('click', '.btn-change-order-status', function(e) {
                const $btn = $(this);
                const saleId = $btn.attr('data-id') || $btn.data('id');
                const newStatus = $btn.attr('data-status') || $btn.data('status');
                window.changeSaleOrderStatus(e, saleId, newStatus, this);
            });

            // Confirm Booking SweetAlert Action
            $(document).on('click', '.confirm-booking-btn', function(e) {
                e.preventDefault();
                let form = $(this).closest("form");

                Swal.fire({
                    title: "Confirm Order?",
                    text: "This will convert the order into a confirmed sale, deduct stock, and update customer ledger.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Confirm it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
