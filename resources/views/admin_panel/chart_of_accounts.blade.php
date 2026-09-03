@extends('admin_panel.layout.app')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Header Title & Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Chart Of Accounts</h4>
                        <p class="text-muted mb-0 small">Manage Cash &amp; Bank accounts</p>
                    </div>
                    @can('chart.of.accounts.create')
                        <div class="d-flex align-items-center flex-nowrap w-100 w-md-auto" style="gap: 8px;">
                            <button class="btn btn-primary shadow-sm fw-bold d-inline-flex align-items-center justify-content-center text-nowrap flex-fill"
                                style="height: 36px; border-radius: 6px; font-size: .80rem; padding: 4px 8px;"
                                data-toggle="modal" data-target="#addAccountModal">
                                <i class="fas fa-plus" style="margin-right: 5px;"></i> Add New Account
                            </button>
                            <button class="btn btn-outline-primary fw-bold d-inline-flex align-items-center justify-content-center text-nowrap flex-fill"
                                style="height: 36px; border-radius: 6px; font-size: .80rem; padding: 4px 8px; background: #ffffff; border-color: #4f46e5; color: #4f46e5;"
                                data-toggle="modal" data-target="#addHeadModal">
                                <i class="fas fa-folder-plus" style="margin-right: 5px;"></i> Add Category
                            </button>
                            <button class="btn btn-outline-secondary fw-bold d-inline-flex align-items-center justify-content-center text-nowrap flex-fill"
                                style="height: 36px; border-radius: 6px; font-size: .80rem; padding: 4px 8px; background: #ffffff; border-color: #64748b; color: #475569;"
                                data-toggle="modal" data-target="#manageHeadsModal">
                                <i class="fas fa-layer-group" style="margin-right: 5px;"></i> All Categories ({{ $heads->count() }})
                            </button>
                        </div>
                    @endcan
                </div>

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════════
                     DESKTOP TABLE VIEW (Visible on tablet / desktop screens)
                ══════════════════════════════════════════════════════════════ --}}
                <div class="card border-0 shadow-sm rounded-4 d-none d-md-block">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle datanew" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 text-secondary fw-semibold text-uppercase small" style="width: 5%">#</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small" style="width: 10%">Code</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small" style="width: 15%">Head / Group</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small" style="width: 20%">Account Title</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small" style="width: 8%">Type</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small" style="width: 14%">Opening / Current</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small" style="width: 8%">Status</th>
                                        <th class="py-3 pe-3 text-secondary fw-semibold text-uppercase small text-center" style="width: 20%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accounts as $acc)
                                        <tr class="border-bottom-0">
                                            <td class="ps-3 fw-bold text-muted">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border font-monospace">{{ $acc->account_code ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $acc->head->name ?? '-' }}</span>
                                                @if ($acc->head && $acc->head->parent_id)
                                                    <small class="text-muted d-block" style="font-size: 0.8em;">({{ $acc->head->parent->name ?? '' }})</small>
                                                @endif
                                            </td>
                                            <td class="fw-bold text-dark">{{ $acc->title }}</td>
                                            <td>
                                                @if ($acc->type == 'Debit')
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">Debit</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">Credit</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="{{ $acc->current_balance < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                                    {{ number_format(abs($acc->current_balance), 2) }}
                                                    <small class="text-secondary fw-normal ms-1">{{ $acc->current_balance >= 0 ? 'Dr' : 'Cr' }}</small>
                                                </div>
                                                <small class="text-muted font-monospace" style="font-size:.70rem;">Opening: {{ number_format($acc->opening_balance, 2) }}</small>
                                            </td>
                                            <td>
                                                @if ($acc->status)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="pe-3 text-center">
                                                <div class="d-flex justify-content-center align-items-center flex-wrap">
                                                    {{-- Ledger Button --}}
                                                    <a href="{{ route('accounts.ledger', $acc->id) }}" class="btn btn-sm btn-outline-info d-inline-flex align-items-center" style="margin-right: 6px !important; margin-bottom: 4px;" title="View Ledger">
                                                        <i class="fas fa-book" style="margin-right: 4px;"></i> Ledger
                                                    </a>

                                                    {{-- Edit Account Button --}}
                                                    <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center" style="margin-right: 6px !important; margin-bottom: 4px;" data-toggle="modal" data-target="#editAccountModal{{ $acc->id }}" title="Edit Account / Ledger Balance">
                                                        <i class="fas fa-edit" style="margin-right: 4px;"></i> Edit
                                                    </button>

                                                    {{-- Audit History Button --}}
                                                    <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" style="margin-right: 6px !important; margin-bottom: 4px;" data-toggle="modal" data-target="#historyAccountModal{{ $acc->id }}" title="View Edit History">
                                                        <i class="fas fa-history" style="margin-right: 4px;"></i> History ({{ $acc->histories->count() }})
                                                    </button>

                                                    {{-- Toggle Status Form --}}
                                                    <form action="{{ route('accounts.toggleStatus', $acc->id) }}" method="POST" style="display:inline-block; margin-bottom: 4px;">
                                                        @csrf
                                                        <button type="button" onclick="this.closest('form').submit()" class="btn btn-sm {{ $acc->status ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $acc->status ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fas {{ $acc->status ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     MOBILE CARDS VIEW (Clean 2x2 grid with balanced spacing)
                ══════════════════════════════════════════════════════════════ --}}
                <div class="d-block d-md-none">
                    @foreach ($accounts as $acc)
                        <div class="card border shadow-sm rounded-3 mb-3" style="background: #ffffff; border-color: #cbd5e1 !important;">
                            <div class="card-body p-3">
                                
                                {{-- Code & Status Header --}}
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark font-monospace fs-6">{{ $acc->account_code ?? 'N/A' }}</span>
                                    @if ($acc->status)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: .72rem;">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1" style="font-size: .72rem;">Inactive</span>
                                    @endif
                                </div>

                                {{-- Head Category & Account Title --}}
                                <div class="text-muted small mb-0" style="font-size: .80rem;">{{ $acc->head->name ?? '-' }}</div>
                                <div class="fw-bold text-dark fs-6 mb-3">{{ $acc->title }}</div>

                                {{-- Type & Opening Grid --}}
                                <div class="row g-2 mb-2 text-start" style="font-size: .85rem;">
                                    <div class="col-6">
                                        <div class="text-muted small">Type</div>
                                        <div class="fw-bold text-dark">{{ $acc->type }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Opening</div>
                                        <div class="fw-bold text-dark">{{ number_format($acc->opening_balance, 2) }}</div>
                                    </div>
                                </div>

                                {{-- Current Balance --}}
                                <div class="d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
                                    <span class="text-muted small">Current</span>
                                    <span class="fw-bold fs-6 {{ $acc->current_balance < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format(abs($acc->current_balance), 2) }}
                                        <small class="fw-normal text-secondary ms-1">{{ $acc->current_balance >= 0 ? 'Dr' : 'Cr' }}</small>
                                    </span>
                                </div>

                                {{-- 2x2 Action Button Grid: Balanced row g-2 --}}
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('accounts.ledger', $acc->id) }}" class="btn btn-outline-primary btn-sm w-100 fw-bold d-flex align-items-center justify-content-center" style="height: 36px; font-size: .80rem; border-radius: 6px;">
                                            <i class="fas fa-book" style="margin-right: 5px;"></i> Ledger
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-bold d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#editAccountModal{{ $acc->id }}" style="height: 36px; font-size: .80rem; border-radius: 6px;">
                                            <i class="fas fa-edit" style="margin-right: 5px;"></i> Edit
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-bold d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#historyAccountModal{{ $acc->id }}" style="height: 36px; font-size: .80rem; border-radius: 6px;">
                                            <i class="fas fa-history" style="margin-right: 5px;"></i> History ({{ $acc->histories->count() }})
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <form action="{{ route('accounts.toggleStatus', $acc->id) }}" method="POST">
                                            @csrf
                                            <button type="button" onclick="this.closest('form').submit()" class="btn {{ $acc->status ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm w-100 fw-bold d-flex align-items-center justify-content-center" style="height: 36px; font-size: .80rem; border-radius: 6px;">
                                                <i class="fas {{ $acc->status ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     ALL MODALS RENDERED OUTSIDE (FULL MOBILE & DESKTOP COMPATIBILITY)
                ══════════════════════════════════════════════════════════════ --}}
                @foreach ($accounts as $acc)

                    {{-- EDIT ACCOUNT & LEDGER BALANCE MODAL --}}
                    <div class="modal fade" id="editAccountModal{{ $acc->id }}" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel{{ $acc->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form class="modal-content border-0 shadow-lg rounded-4" action="{{ route('accounts.update', $acc->id) }}" method="POST">
                                @csrf
                                <div class="modal-header border-bottom bg-light px-4 py-3">
                                    <h5 class="modal-title fw-bold text-dark mb-0" id="editAccountModalLabel{{ $acc->id }}">
                                        <i class="fas fa-edit text-primary me-2"></i>Edit Account &amp; Ledger - {{ $acc->title }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-4 text-start">
                                    <div class="alert alert-info py-2 px-3 small border-0 rounded-3 mb-3">
                                        <i class="fas fa-info-circle me-1"></i> Aap <strong>Current Ledger Balance</strong> ko direct edit / update karke naya amount daal sakte hain.
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="small text-secondary fw-bold mb-1">Select Head (Category)</label>
                                        <select class="form-control form-select" name="head_id" required style="height: 42px;">
                                            @foreach ($heads as $head)
                                                <option value="{{ $head->id }}" {{ $acc->head_id == $head->id ? 'selected' : '' }}>{{ $head->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="small text-secondary fw-bold mb-1">Account Title</label>
                                        <input type="text" name="title" class="form-control" value="{{ $acc->title }}" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                                            <div class="form-group mb-0">
                                                <label class="small text-secondary fw-bold mb-1">Type</label>
                                                <select class="form-control form-select" name="type" style="height: 42px;">
                                                    <option value="Debit" {{ $acc->type == 'Debit' ? 'selected' : '' }}>Debit</option>
                                                    <option value="Credit" {{ $acc->type == 'Credit' ? 'selected' : '' }}>Credit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <div class="form-group mb-0">
                                                <label class="small text-secondary fw-bold mb-1">Opening Balance</label>
                                                <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ $acc->opening_balance }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <div class="form-group mb-0">
                                                <label class="small text-success fw-bold mb-1">Current Balance</label>
                                                <input type="number" step="0.01" name="current_balance" class="form-control fw-bold text-success border-success" value="{{ $acc->current_balance }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-3 mb-3">
                                        <label class="small text-secondary fw-bold mb-1">Reason / Note for Ledger Edit (Optional)</label>
                                        <input type="text" name="note" class="form-control" placeholder="e.g., 'Manual payment addition of Rs 5,000'">
                                    </div>

                                    <div class="form-group mb-0">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="statusCheck{{ $acc->id }}" name="status" {{ $acc->status ? 'checked' : '' }}>
                                            <label class="custom-control-label small text-secondary" for="statusCheck{{ $acc->id }}">Active Account</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top px-4 py-3">
                                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- ACCOUNT AUDIT HISTORY MODAL --}}
                    <div class="modal fade" id="historyAccountModal{{ $acc->id }}" tabindex="-1" role="dialog" aria-labelledby="historyAccountModalLabel{{ $acc->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                <div class="modal-header border-bottom bg-light px-4 py-3">
                                    <div>
                                        <h5 class="modal-title fw-bold text-dark mb-0" id="historyAccountModalLabel{{ $acc->id }}">
                                            <i class="fas fa-history text-primary me-2"></i>Audit History - {{ $acc->title }}
                                        </h5>
                                        <small class="text-muted">Record of all balance edits and modifications</small>
                                    </div>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body p-0 custom-scroll" style="max-height: 60vh; overflow-y: auto;">
                                    @if($acc->histories->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size:.84rem;">
                                                <thead class="bg-light sticky-top">
                                                    <tr>
                                                        <th class="ps-4">Date &amp; Time</th>
                                                        <th>User</th>
                                                        <th class="text-end">Old Balance</th>
                                                        <th class="text-end">New Balance</th>
                                                        <th class="ps-4">Remarks / Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($acc->histories as $h)
                                                        <tr>
                                                            <td class="ps-4 font-monospace small">{{ $h->created_at->format('d-M-Y h:i:s A') }}</td>
                                                            <td>
                                                                <span class="badge bg-light text-dark border">{{ $h->user_name ?? ($h->user->name ?? 'User') }}</span>
                                                            </td>
                                                            <td class="text-end text-muted">Rs {{ number_format($h->old_balance, 2) }}</td>
                                                            <td class="text-end fw-bold text-primary">Rs {{ number_format($h->new_balance, 2) }}</td>
                                                            <td class="ps-4 text-muted small">{{ $h->note ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5 text-muted">
                                            <i class="fas fa-history fa-3x mb-3" style="color:#cbd5e1;"></i>
                                            <p class="fw-bold mb-0">No edit history recorded yet</p>
                                            <small>History entries will automatically appear here whenever opening balance or account details are updated.</small>
                                        </div>
                                    @endif
                                </div>

                                <div class="modal-footer bg-light py-2 px-4 border-top">
                                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach

                <!-- Add New Account Modal -->
                <div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form class="modal-content border-0 shadow-lg rounded-4" action="{{ route('accounts.store') }}" method="POST">
                            @csrf
                            <div class="modal-header border-bottom bg-light px-4 py-3">
                                <h5 class="modal-title fw-bold text-dark mb-0" id="addAccountModalLabel">Add New Account</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="text-muted small mb-3">Create a new financial account.</p>

                                <div class="form-group mb-3">
                                    <label class="small text-secondary fw-bold mb-1">Select Head (Category)</label>
                                    <select class="form-control form-select" name="head_id" required style="height: 42px;">
                                        <option value="">Select Head</option>
                                        @foreach ($heads as $head)
                                            <option value="{{ $head->id }}">{{ $head->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small text-secondary fw-bold mb-1">Account Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g., UBL Current" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="small text-secondary fw-bold mb-1">Type</label>
                                            <select class="form-control form-select" name="type" style="height: 42px;">
                                                <option value="Debit">Debit</option>
                                                <option value="Credit">Credit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="small text-secondary fw-bold mb-1">Opening Balance</label>
                                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="statusCheck" name="status" checked>
                                        <label class="custom-control-label small text-secondary" for="statusCheck">Active Account</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top px-4 py-3">
                                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">Save Account</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Add Head Modal -->
                {{-- ADD CATEGORY / HEAD MODAL --}}
                <div class="modal fade" id="addHeadModal" tabindex="-1" role="dialog" aria-labelledby="addHeadLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form class="modal-content border-0 shadow-lg rounded-4" action="{{ route('account-heads.store') }}" method="POST">
                            @csrf
                            <div class="modal-header border-bottom bg-light px-4 py-3">
                                <h5 class="modal-title fw-bold text-dark mb-0" id="addHeadLabel">
                                    <i class="fas fa-folder-plus text-primary me-2"></i>Add New Category / Head
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="alert alert-info py-2 px-3 small border-0 rounded-3 mb-3">
                                    <i class="fas fa-info-circle me-1"></i> <strong>Note:</strong> Categories / Heads are top-level parent groups (e.g. <strong>Cash</strong>, <strong>Bank</strong>). Individual accounts (like <em>Meezan Bank</em>, <em>Jazz Cash</em>) should be created via <strong>Add New Account</strong> under a head.
                                </div>
                                <div class="form-group mb-0">
                                    <label class="small text-secondary fw-bold mb-1">Category / Head Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g., Cash, Bank" required>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top px-4 py-3">
                                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">Save Category</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MANAGE CATEGORIES / HEADS MODAL --}}
                <div class="modal fade" id="manageHeadsModal" tabindex="-1" role="dialog" aria-labelledby="manageHeadsLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header bg-light border-bottom px-4 py-3">
                                <h5 class="modal-title fw-bold text-dark mb-0" id="manageHeadsLabel">
                                    <i class="fas fa-layer-group text-primary me-2"></i>Account Categories / Heads Management
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 10%;">#</th>
                                                <th style="width: 45%;">Category / Head Name</th>
                                                <th style="width: 20%;">Accounts Linked</th>
                                                <th style="width: 25%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($heads as $h)
                                                <tr>
                                                    <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="fw-bold text-dark">{{ $h->name }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2">
                                                            {{ $h->accounts_count }} {{ Str::plural('Account', $h->accounts_count) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                                            {{-- Edit Head Button --}}
                                                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editHeadModal{{ $h->id }}" data-dismiss="modal" title="Edit Head Name">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            
                                                            {{-- Delete Head Form (only if 0 accounts) --}}
                                                            @if ($h->accounts_count == 0)
                                                                <form action="{{ route('account-heads.delete', $h->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Category/Head?');" style="display: inline-block;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Unused Head">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="text-muted small" title="Cannot delete: Accounts are linked to this head" style="font-size: 0.75rem;">In Use</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">No Categories / Heads found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top px-4 py-3">
                                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- EDIT HEAD MODALS --}}
                @foreach ($heads as $h)
                    <div class="modal fade" id="editHeadModal{{ $h->id }}" tabindex="-1" role="dialog" aria-labelledby="editHeadLabel{{ $h->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form class="modal-content border-0 shadow-lg rounded-4" action="{{ route('account-heads.update', $h->id) }}" method="POST">
                                @csrf
                                <div class="modal-header border-bottom bg-light px-4 py-3">
                                    <h5 class="modal-title fw-bold text-dark mb-0" id="editHeadLabel{{ $h->id }}">
                                        <i class="fas fa-edit text-primary me-2"></i>Edit Category / Head
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="form-group mb-0">
                                        <label class="small text-secondary fw-bold mb-1">Category / Head Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $h->name }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top px-4 py-3">
                                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">Update Category</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().destroy();
            }
            $('.datanew').DataTable({
                "pageLength": 10,
                "aaSorting": [],
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search accounts..."
                }
            });
        });
    </script>
@endsection
