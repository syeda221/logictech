@extends('admin_panel.layout.app')

@section('content')
    @include('hr.partials.hr-styles')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="page-title"><i class="fa fa-users"></i> Employee Management</h1>
                        <p class="page-subtitle">Manage your organization's employee database</p>
                    </div>
                    @can('hr.employees.create')
                        <button type="button" class="btn btn-create" id="createBtn" data-bs-toggle="modal" data-bs-target="#employeeModal" data-toggle="modal" data-target="#employeeModal">
                            <i class="fa fa-user-plus"></i> Add Employee
                        </button>
                    @endcan
                </div>

                <!-- Stats Row -->
                @php
                    $activeCount = $employees->where('status', 'active')->count();
                    $nonActiveCount = $employees->where('status', 'non-active')->count();
                    $terminatedCount = $employees->where('status', 'terminated')->count();
                @endphp
                <div class="stats-row">
                    <div class="stat-card primary">
                        <div class="stat-icon"><i class="fa fa-users"></i></div>
                        <div class="stat-value">{{ $employees->count() }}</div>
                        <div class="stat-label">Total Employees</div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="fa fa-user-check"></i></div>
                        <div class="stat-value">{{ $activeCount }}</div>
                        <div class="stat-label">Active</div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon"><i class="fa fa-user-clock"></i></div>
                        <div class="stat-value">{{ $nonActiveCount }}</div>
                        <div class="stat-label">Inactive</div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-icon"><i class="fa fa-user-times"></i></div>
                        <div class="stat-value">{{ $terminatedCount }}</div>
                        <div class="stat-label">Terminated</div>
                    </div>
                </div>

                <!-- Row Wise Employees Card / Table -->
                <div class="hr-card">
                    <div class="hr-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="search-box">
                                <i class="fa fa-search"></i>
                                <input type="search" id="empSearch" placeholder="Search employees...">
                            </div>
                            <!-- Status Filter -->
                            <select id="statusFilter" class="form-select form-select-sm" style="width: 140px;">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="non-active">Inactive</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                        <span class="text-muted small" id="empCount">{{ $employees->count() }} employees</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="empTable">
                            <thead class="bg-light text-muted uppercase small" style="font-size: 0.78rem; letter-spacing: 0.04em;">
                                <tr>
                                    <th class="ps-4 py-3">Employee</th>
                                    <th class="py-3">Contact</th>
                                    <th class="py-3">Salary</th>
                                    <th class="py-3">Timing / Shift</th>
                                    <th class="py-3">Status</th>
                                    <th class="pe-4 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="empTableBody">
                                @forelse($employees as $emp)
                                    <tr class="hr-table-row" data-id="{{ $emp->id }}"
                                        data-name="{{ strtolower($emp->full_name) }}"
                                        data-email="{{ strtolower($emp->email) }}">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="hr-avatar me-3 mr-3" style="width: 44px; height: 44px; min-width: 44px; border-radius: 10px; font-size: 1.1rem; flex-shrink: 0; margin-right: 14px !important;">
                                                    {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?: $emp->first_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $emp->full_name }}</div>
                                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                                        ID: #{{ $emp->id }} @if($emp->joining_date) • Joined {{ \Carbon\Carbon::parse($emp->joining_date)->format('d/m/Y') }} @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if($emp->phone)
                                                <span class="fw-semibold text-dark" style="font-size: 0.88rem;"><i class="fa fa-phone text-primary me-1"></i>{{ $emp->phone }}</span>
                                            @else
                                                <span class="text-muted small"><i class="fa fa-envelope me-1"></i>{{ $emp->email }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($emp->basic_salary > 0)
                                                <span class="emp-badge emp-badge-salary">
                                                    <i class="fa fa-money-bill-wave text-success"></i>Rs. {{ number_format($emp->basic_salary, 0) }}
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if ($emp->custom_start_time)
                                                <span class="emp-badge emp-badge-shift"><i class="fa fa-clock"></i>Custom Timing</span>
                                            @elseif($emp->shift)
                                                <span class="emp-badge emp-badge-shift"><i class="fa fa-clock"></i>{{ $emp->shift->name }}</span>
                                            @else
                                                <span class="text-muted small">Standard</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <span class="emp-badge {{ $emp->status == 'active' ? 'emp-badge-active' : ($emp->status == 'non-active' ? 'emp-badge-inactive' : 'emp-badge-terminated') }} status-badge">
                                                {{ $emp->status == 'non-active' ? 'Inactive' : ucfirst($emp->status) }}
                                            </span>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <div class="hr-actions d-inline-flex align-items-center gap-1">
                                                @can('hr.employees.edit')
                                                    <button type="button" class="btn btn-toggle-status {{ $emp->status == 'active' ? 'active-status' : 'inactive-status' }}"
                                                        data-url="{{ route('hr.employees.toggle-status', $emp->id) }}"
                                                        title="Click to toggle Active / Inactive">
                                                        <i class="fa {{ $emp->status == 'active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted' }}"></i>
                                                        <span class="status-text">{{ $emp->status == 'active' ? 'Active' : 'Inactive' }}</span>
                                                    </button>
                                                    <button type="button" class="btn btn-action-icon btn-edit edit-btn"
                                                        data-id="{{ $emp->id }}"
                                                        data-first_name="{{ $emp->first_name }}"
                                                        data-phone="{{ $emp->phone }}"
                                                        data-basic_salary="{{ $emp->basic_salary }}"
                                                        data-status="{{ $emp->status }}"
                                                        data-bs-toggle="modal" data-bs-target="#employeeModal"
                                                        data-toggle="modal" data-target="#employeeModal"
                                                        title="Edit Employee">
                                                        <i class="fa fa-pen"></i>
                                                    </button>
                                                @endcan
                                                @can('hr.employees.delete')
                                                    <button type="button" class="btn btn-action-icon btn-delete delete-btn"
                                                        data-url="{{ route('hr.employees.destroy', $emp->id) }}" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            <!-- Hidden fields for fallback -->
                                            <input type="hidden" class="first_name" value="{{ $emp->first_name }}">
                                            <input type="hidden" class="phone" value="{{ $emp->phone }}">
                                            <input type="hidden" class="basic_salary" value="{{ $emp->basic_salary }}">
                                            <input type="hidden" class="status" value="{{ $emp->status }}">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fa fa-users text-muted mb-2" style="font-size: 2rem;"></i>
                                                <p class="text-muted mb-0">No employees found. Add your first employee!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-top">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header gradient d-flex align-items-center justify-content-between">
                    <h5 class="modal-title text-white" id="modalLabel">
                        <i class="fa fa-user-plus"></i>
                        <span>Add Employee</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.5rem; opacity:0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="employeeForm" action="{{ route('hr.employees.store') }}" method="POST"
                    enctype="multipart/form-data" data-ajax-validate="true">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group-modern mb-2">
                                    <label class="form-label fw-bold text-dark"><i class="fa fa-user me-1 text-primary"></i> Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name" class="form-control"
                                        placeholder="Enter employee name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern mb-2">
                                    <label class="form-label fw-bold text-dark"><i class="fa fa-phone me-1 text-primary"></i> Phone Number</label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        placeholder="Enter phone number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern mb-2">
                                    <label class="form-label fw-bold text-dark"><i class="fa fa-money-bill me-1 text-success"></i> Basic Salary <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="basic_salary" id="basic_salary"
                                        class="form-control" placeholder="Enter basic salary" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern mb-2">
                                    <label class="form-label fw-bold text-dark"><i class="fa fa-toggle-on me-1 text-info"></i> Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="active">Active</option>
                                        <option value="non-active">Inactive</option>
                                        <option value="terminated">Terminated</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer-modern">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                            <i class="fa fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-save">
                            <i class="fa fa-check"></i>
                            <span>Save Employee</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <!-- Scripts -->
    <script>
        $(document).ready(function() {
            // Submit Employee Form via AJAX
            $('#employeeForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var actionUrl = form.attr('action');
                var formData = new FormData(this);
                var submitBtn = form.find('button[type="submit"]');

                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        submitBtn.prop('disabled', false).html('<i class="fa fa-check"></i> <span>Save Employee</span>');
                        if (response.success) {
                            $('.modal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: response.success,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        } else if (response.error) {
                            Swal.fire('Error', response.error, 'error');
                        }
                    },
                    error: function(err) {
                        submitBtn.prop('disabled', false).html('<i class="fa fa-check"></i> <span>Save Employee</span>');
                        var msg = 'Failed to save employee';
                        if (err.responseJSON) {
                            if (err.responseJSON.errors) {
                                var errList = [];
                                $.each(err.responseJSON.errors, function(key, val) {
                                    errList.push(val.join(' '));
                                });
                                msg = errList.join('<br>');
                            } else if (err.responseJSON.error) {
                                msg = err.responseJSON.error;
                            }
                        }
                        Swal.fire('Validation Error', msg, 'error');
                    }
                });
            });
            // Fill Edit Form
            function fillEditModal(btn) {
                if (!btn || !btn.length) return;
                var row = btn.closest('.hr-table-row');

                var empId = btn.attr('data-id') || btn.data('id') || (row.length ? row.attr('data-id') : '');
                var firstName = btn.attr('data-first_name') || btn.data('first_name') || (row.length ? row.find('.first_name').val() : '');
                var phone = btn.attr('data-phone') || btn.data('phone') || (row.length ? row.find('.phone').val() : '');
                var basicSalary = btn.attr('data-basic_salary') || btn.data('basic_salary') || (row.length ? row.find('.basic_salary').val() : '');
                var status = btn.attr('data-status') || btn.data('status') || (row.length ? row.find('.status').val() : 'active');

                $('#edit_id').val(empId);
                $('#first_name').val(firstName);
                $('#phone').val(phone);
                $('#basic_salary').val(basicSalary);
                $('#status').val(status || 'active');

                $('#modalLabel').html('<i class="fa fa-pen me-2"></i><span>Edit Employee</span>');
            }

            // Fill Create Form
            function fillCreateModal() {
                $('#edit_id').val('');
                if ($('#employeeForm').length > 0 && $('#employeeForm')[0]) {
                    $('#employeeForm')[0].reset();
                }
                $('#status').val('active');
                $('#modalLabel').html('<i class="fa fa-user-plus me-2"></i><span>Add Employee</span>');
            }

            // Create Employee Click
            $(document).on('click', '#createBtn', function(e) {
                fillCreateModal();
            });

            // Edit Employee Click
            $(document).on('click', '.edit-btn', function(e) {
                var btn = $(this).closest('.edit-btn');
                fillEditModal(btn);
            });

            // Modal Show Listener (Bootstrap 4 & 5 native data-api hook)
            $('#employeeModal').on('show.bs.modal', function(e) {
                var target = e.relatedTarget ? $(e.relatedTarget) : null;
                if (target && target.length) {
                    var editBtn = target.hasClass('edit-btn') ? target : target.closest('.edit-btn');
                    var createBtn = target.is('#createBtn') ? target : target.closest('#createBtn');

                    if (editBtn.length) {
                        fillEditModal(editBtn);
                    } else if (createBtn.length) {
                        fillCreateModal();
                    }
                }
            });

            // Delete Employee
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var btn = $(this).closest('.delete-btn');
                var url = btn.attr('data-url') || btn.data('url');

                Swal.fire({
                    title: 'Delete Employee?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.success, 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error', response.error || 'Failed to delete employee', 'error');
                                }
                            },
                            error: function(err) {
                                var msg = (err.responseJSON && err.responseJSON.error) ? err.responseJSON.error : 'Failed to delete employee';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // Toggle Status Button Click
            $(document).on('click', '.btn-toggle-status', function(e) {
                e.preventDefault();
                var btn = $(this).closest('.btn-toggle-status');
                var url = btn.attr('data-url') || btn.data('url');
                if (!url) return;

                btn.prop('disabled', true);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        btn.prop('disabled', false);
                        if (response.status) {
                            var isNowActive = (response.status === 'active');
                            var row = btn.closest('.hr-table-row');
                            
                            // Update hidden input
                            row.find('.status').val(response.status);

                            // Update button style & text
                            if (isNowActive) {
                                btn.removeClass('inactive-status').addClass('active-status');
                                btn.find('i').attr('class', 'fa fa-toggle-on text-success');
                                btn.find('.status-text').text('Active');
                            } else {
                                btn.removeClass('active-status').addClass('inactive-status');
                                btn.find('i').attr('class', 'fa fa-toggle-off text-muted');
                                btn.find('.status-text').text('Inactive');
                            }

                            // Update table row status badge
                            var badge = row.find('.status-badge');
                            badge.removeClass('emp-badge-active emp-badge-inactive emp-badge-terminated');
                            if (isNowActive) {
                                badge.addClass('emp-badge-active').text('Active');
                            } else if (response.status === 'non-active') {
                                badge.addClass('emp-badge-inactive').text('Inactive');
                            } else {
                                badge.addClass('emp-badge-terminated').text('Terminated');
                            }

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.success,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else if (response.error) {
                            Swal.fire('Error', response.error, 'error');
                        }
                    },
                    error: function(err) {
                        btn.prop('disabled', false);
                        var msg = (err.responseJSON && err.responseJSON.error) ? err.responseJSON.error : 'Failed to update status';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Table Search and Filter
            function filterEmployees() {
                var searchText = $('#empSearch').val().toLowerCase();
                var selectedStatus = $('#statusFilter').val();

                $('.hr-table-row').each(function() {
                    var row = $(this);

                    var name = (row.data('name') || '').toString().toLowerCase();
                    var email = (row.data('email') || '').toString().toLowerCase();
                    var phone = (row.find('.phone').val() || '').toString().toLowerCase();
                    var status = (row.find('.status').val() || '').toString().toLowerCase();

                    // 1. Text Search Check
                    var matchSearch = (!searchText || name.indexOf(searchText) !== -1 || email.indexOf(searchText) !== -1 || phone.indexOf(searchText) !== -1);

                    // 2. Status Filter Check
                    var matchStatus = (selectedStatus === 'all' || status === selectedStatus);

                    if (matchSearch && matchStatus) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
                $('#empCount').text($('.hr-table-row:visible').length + ' employees');
            }

            // Event Listeners
            $('#empSearch').on('input', filterEmployees);
            $('#statusFilter').on('change', filterEmployees);
        });
    </script>
@endsection
