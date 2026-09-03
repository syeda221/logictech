@extends('admin_panel.layout.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .page-container {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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
        font-size: 24px;
    }
    .btn-outline-danger {
        border-color: #fca5a5;
        color: #ef4444;
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 500;
        background: transparent;
        border: 1px solid #fca5a5;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-outline-danger:hover {
        background-color: #fee2e2;
        border-color: #ef4444;
        color: #b91c1c;
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
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .badge-soft-primary {
        background-color: #e0e7ff;
        color: #3730a3;
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 20px;
    }
</style>

<div class="page-container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h4>Website Registered Users (Web Customers)</h4>
    </div>

    <!-- Filters Card -->
    <div class="card filter-card mb-4">
        <form action="{{ route('web_users.index') }}" method="GET" class="row g-3 align-items-end">
            <!-- Search field -->
            <div class="col-12 col-sm-6 col-md-5">
                <label for="search" class="form-label small fw-semibold text-slate-600">Search User</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Name, Email, or Phone..." value="{{ request('search') }}">
            </div>

            <!-- Linked Customer Status -->
            <div class="col-12 col-sm-6 col-md-4">
                <label for="linked_status" class="form-label small fw-semibold text-slate-600">Linked Customer Status</label>
                <select name="linked_status" id="linked_status" class="form-select">
                    <option value="">All Users</option>
                    <option value="linked" {{ request('linked_status') == 'linked' ? 'selected' : '' }}>Linked to ERP Customer</option>
                    <option value="unlinked" {{ request('linked_status') == 'unlinked' ? 'selected' : '' }}>Unlinked (Website Only)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12 col-md-3 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary px-3 shadow-sm d-flex align-items-center gap-1" style="height: 38px; border-radius: 8px;"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('web_users.index') }}" class="btn btn-light px-3 border shadow-sm d-flex align-items-center gap-1" style="height: 38px; border-radius: 8px; background-color: #f8fafc; color: #475569; border-color: #cbd5e1;"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Linked Customer ID</th>
                        <th>Source</th>
                        @can('web_users.delete')
                        <th class="text-center pe-4">Action</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($webCustomers as $customer)
                        <tr>
                            <td class="ps-4 fw-semibold text-slate-700">{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                            <td>{{ $customer->address ? ($customer->address . ($customer->city ? ', ' . $customer->city : '')) : 'N/A' }}</td>
                            <td>
                                @if($customer->customer_id)
                                    <span class="badge-soft-primary"><i class="fas fa-link me-1"></i> {{ $customer->customer_id }}</span>
                                @else
                                    <span class="text-muted small">Not linked</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-soft-success">Website</span>
                            </td>
                            @can('web_users.delete')
                            <td class="text-center pe-4">
                                <form action="{{ route('web_users.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this website user?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-outline-danger">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-users-slash fs-3 mb-2 d-block text-gray-300"></i>
                                No website users registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
