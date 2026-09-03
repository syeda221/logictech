@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ── Purchase Requisition Modern ERP Design ── */
    :root {
        --pr-primary:    #4f46e5;
        --pr-primary-lt: #eef2ff;
        --pr-success:    #10b981;
        --pr-success-lt: #ecfdf5;
        --pr-warning:    #f59e0b;
        --pr-warning-lt: #fffbeb;
        --pr-danger:     #ef4444;
        --pr-danger-lt:  #fef2f2;
        --pr-border:     #e2e8f0;
        --pr-bg:         #f8fafc;
        --pr-card-bg:    #ffffff;
        --pr-text:       #0f172a;
        --pr-muted:      #64748b;
        --pr-radius:     14px;
        --pr-shadow:     0 2px 8px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.02);
        --pr-shadow-md:  0 8px 24px rgba(15,23,42,0.08);
    }

    .pr-page-container {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        padding: 20px 0;
        min-height: calc(100vh - 80px);
        background: #f8fafc;
    }

    /* ── Stats Grid ── */
    .pr-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .pr-stat-card {
        background: var(--pr-card-bg);
        border: 1px solid var(--pr-border);
        border-radius: var(--pr-radius);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--pr-shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .pr-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--pr-shadow-md);
    }

    .pr-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .pr-stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--pr-muted);
        margin-bottom: 2px;
    }

    .pr-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--pr-text);
        line-height: 1.2;
    }

    .pr-stat-sub {
        font-size: 0.72rem;
        color: var(--pr-muted);
        margin-top: 2px;
    }

    /* ── Main ERP Card ── */
    .pr-card {
        background: var(--pr-card-bg);
        border-radius: var(--pr-radius);
        border: 1px solid var(--pr-border);
        box-shadow: var(--pr-shadow);
        overflow: hidden;
    }

    .pr-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--pr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        background: #ffffff;
    }

    .pr-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--pr-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.02em;
    }

    .pr-subtitle {
        font-size: 0.8rem;
        color: var(--pr-muted);
        margin: 3px 0 0 0;
    }

    /* ── Primary Action Buttons ── */
    .btn-pr-primary {
        background: var(--pr-primary) !important;
        border: 1px solid var(--pr-primary) !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 10px;
        padding: 9px 20px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        transition: all 0.15s ease-in-out;
        text-decoration: none;
    }

    .btn-pr-primary:hover {
        background: #4338ca !important;
        border-color: #4338ca !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
    }

    /* ── Filter Panel ── */
    .pr-filter-panel {
        background: #f8fafc;
        border-bottom: 1px solid var(--pr-border);
        padding: 16px 24px;
    }

    .pr-filter-row {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .pr-filter-field {
        display: flex;
        flex-direction: column;
        flex: 1 1 150px;
        min-width: 140px;
    }

    .pr-filter-search {
        flex: 2 1 240px;
    }

    .pr-flabel {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--pr-muted);
        margin-bottom: 6px;
    }

    .pr-finput {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        border: 1px solid var(--pr-border);
        border-radius: 8px;
        font-size: 0.84rem;
        font-weight: 500;
        color: var(--pr-text);
        background: #ffffff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .pr-finput:focus {
        border-color: var(--pr-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    .search-input-wrap {
        position: relative;
    }

    .search-input-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--pr-muted);
        font-size: 13px;
        pointer-events: none;
    }

    .search-input-wrap .pr-finput {
        padding-left: 36px;
    }

    .btn-pr-filter {
        background: var(--pr-primary);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        height: 40px;
        padding: 0 16px;
        font-size: 0.84rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }

    .btn-pr-filter:hover {
        background: #4338ca;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-pr-clear {
        background: #ffffff;
        color: var(--pr-muted);
        border: 1px solid var(--pr-border);
        border-radius: 8px;
        height: 40px;
        padding: 0 14px;
        font-size: 0.84rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.15s;
    }

    .btn-pr-clear:hover {
        border-color: var(--pr-danger);
        color: var(--pr-danger);
        background: var(--pr-danger-lt);
    }

    /* ── Table Styling ── */
    .pr-table-wrap {
        overflow-x: auto;
    }

    .pr-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }

    .pr-table th {
        background-color: #f8fafc !important;
        color: var(--pr-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--pr-border) !important;
        padding: 14px 18px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .pr-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: var(--pr-text);
        font-size: 0.86rem;
    }

    .pr-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* ── Badges ── */
    .pr-num-badge {
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        color: var(--pr-primary);
        background: var(--pr-primary-lt);
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.8rem;
        border: 1px solid #c7d2fe;
        display: inline-block;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }

    .status-pill.draft { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .status-pill.pending_approval { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .status-pill.approved { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .status-pill.rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; }
    .status-pill.partially_ordered { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .status-pill.closed { background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; }

    .items-count-badge {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid var(--pr-border);
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 0.76rem;
        font-weight: 600;
    }

    /* ── Action Buttons ── */
    .btn-table-act {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.76rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-table-view { background: var(--pr-primary-lt); color: var(--pr-primary); border-color: #c7d2fe; }
    .btn-table-view:hover { background: var(--pr-primary); color: #ffffff; }

    .btn-table-convert { background: var(--pr-success-lt); color: var(--pr-success); border-color: #a7f3d0; }
    .btn-table-convert:hover { background: var(--pr-success); color: #ffffff; }

    .btn-table-del { background: var(--pr-danger-lt); color: var(--pr-danger); border-color: #fca5a5; }
    .btn-table-del:hover { background: var(--pr-danger); color: #ffffff; }

    @media (max-width: 768px) {
        .pr-stat-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .pr-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .btn-pr-primary {
            width: 100%;
            justify-content: center;
        }
        .pr-filter-field, .pr-filter-search {
            flex: 1 1 100%;
        }
    }
</style>

<div class="pr-page-container">
    <div class="container-fluid px-3 px-md-4">

        {{-- Top Stats Row --}}
        <div class="pr-stat-grid">
            <div class="pr-stat-card">
                <div class="pr-stat-icon" style="background:#eef2ff; color:#4f46e5;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <div class="pr-stat-label">Total PRs</div>
                    <div class="pr-stat-value">{{ $stats['total'] ?? 0 }}</div>
                    <div class="pr-stat-sub">Requisitions created</div>
                </div>
            </div>

            <div class="pr-stat-card">
                <div class="pr-stat-icon" style="background:#fffbeb; color:#d97706;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="pr-stat-label">Pending Approval</div>
                    <div class="pr-stat-value" style="color:#d97706;">{{ $stats['pending'] ?? 0 }}</div>
                    <div class="pr-stat-sub">Requires review</div>
                </div>
            </div>

            <div class="pr-stat-card">
                <div class="pr-stat-icon" style="background:#ecfdf5; color:#059669;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="pr-stat-label">Approved</div>
                    <div class="pr-stat-value" style="color:#059669;">{{ $stats['approved'] ?? 0 }}</div>
                    <div class="pr-stat-sub">Ready for PO</div>
                </div>
            </div>

            <div class="pr-stat-card">
                <div class="pr-stat-icon" style="background:#f1f5f9; color:#475569;">
                    <i class="fas fa-archive"></i>
                </div>
                <div>
                    <div class="pr-stat-label">Closed / Ordered</div>
                    <div class="pr-stat-value" style="color:#334155;">{{ $stats['closed'] ?? 0 }}</div>
                    <div class="pr-stat-sub">Completed PRs</div>
                </div>
            </div>
        </div>

        {{-- Main Requisitions Card --}}
        <div class="pr-card">
            <div class="pr-card-header">
                <div>
                    <h4 class="pr-title">
                        <i class="fas fa-file-invoice-dollar text-primary"></i>
                        Purchase Requisitions
                    </h4>
                    <p class="pr-subtitle">Manage internal material demands, department requisitions &amp; approval workflows</p>
                </div>
                <div>
                    <a href="{{ route('purchase-requisitions.create') }}" class="btn-pr-primary">
                        <i class="fas fa-plus"></i>
                        <span>Create New PR</span>
                    </a>
                </div>
            </div>

            {{-- Filter Panel --}}
            <form action="{{ route('purchase-requisitions.index') }}" method="GET" class="pr-filter-panel">
                <div class="pr-filter-row">
                    <div class="pr-filter-field pr-filter-search">
                        <label class="pr-flabel">Search</label>
                        <div class="search-input-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="pr-finput" placeholder="Search PR Number (e.g. PR-2026)..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="pr-filter-field">
                        <label class="pr-flabel">Status</label>
                        <select name="status" class="pr-finput">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>⏳ Pending Approval</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>📁 Closed</option>
                        </select>
                    </div>

                    <div class="pr-filter-field">
                        <label class="pr-flabel">From Date</label>
                        <input type="date" name="from_date" class="pr-finput" value="{{ request('from_date') }}">
                    </div>

                    <div class="pr-filter-field">
                        <label class="pr-flabel">To Date</label>
                        <input type="date" name="to_date" class="pr-finput" value="{{ request('to_date') }}">
                    </div>

                    <div class="pr-filter-field" style="flex:0 0 auto;">
                        <label class="pr-flabel">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-pr-filter">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('purchase-requisitions.index') }}" class="btn-pr-clear">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Requisitions Table --}}
            <div class="pr-table-wrap">
                <table class="pr-table">
                    <thead>
                        <tr>
                            <th>PR Number</th>
                            <th>Date</th>
                            <th>Branch &amp; Warehouse</th>
                            <th>Required By</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requisitions ?? [] as $pr)
                        <tr>
                            <td>
                                <span class="pr-num-badge">{{ $pr->pr_number }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $pr->created_at ? $pr->created_at->format('d M, Y') : 'N/A' }}</div>
                                <small class="text-muted">{{ $pr->created_at ? $pr->created_at->format('h:i A') : '' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><i class="fas fa-building text-muted me-1"></i>{{ $pr->branch->name ?? 'Main Branch' }}</div>
                                <small class="text-muted"><i class="fas fa-warehouse text-muted me-1"></i>{{ $pr->warehouse->warehouse_name ?? ($pr->warehouse->name ?? 'Main Warehouse') }}</small>
                            </td>
                            <td>
                                @if($pr->required_by_date)
                                    <div class="fw-semibold text-dark"><i class="far fa-calendar-alt text-primary me-1"></i>{{ date('d M, Y', strtotime($pr->required_by_date)) }}</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="items-count-badge">
                                    <i class="fas fa-cubes me-1 text-muted"></i>{{ $pr->items->count() }} item(s)
                                </span>
                            </td>
                            <td>
                                @php
                                    $st = $pr->status ?? 'draft';
                                @endphp
                                @if($st === 'pending_approval')
                                    <span class="status-pill pending_approval"><i class="fas fa-clock"></i> Pending Approval</span>
                                @elseif($st === 'approved')
                                    <span class="status-pill approved"><i class="fas fa-check-circle"></i> Approved</span>
                                @elseif($st === 'rejected')
                                    <span class="status-pill rejected"><i class="fas fa-times-circle"></i> Rejected</span>
                                @elseif($st === 'partially_ordered')
                                    <span class="status-pill partially_ordered"><i class="fas fa-truck-loading"></i> Partial PO</span>
                                @elseif($st === 'closed')
                                    <span class="status-pill closed"><i class="fas fa-check-double"></i> Closed</span>
                                @else
                                    <span class="status-pill draft"><i class="fas fa-pencil-alt"></i> Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('purchase-requisitions.show', $pr->id) }}" class="btn-table-act btn-table-view" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    @if($pr->status === 'approved')
                                        <a href="{{ route('purchase-requisitions.convert-to-po', $pr->id) }}" class="btn-table-act btn-table-convert" title="Convert to Purchase Order">
                                            <i class="fas fa-shopping-cart"></i> Create PO
                                        </a>
                                    @endif

                                    @if(in_array($pr->status, ['draft', 'pending_approval']))
                                        <form action="{{ route('purchase-requisitions.destroy', $pr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this Requisition?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-table-act btn-table-del" title="Delete PR">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block" style="opacity: 0.4;"></i>
                                <h6 class="fw-bold mb-1">No Purchase Requisitions Found</h6>
                                <p class="small text-muted mb-3">Create your first internal purchase requisition or adjust your search filters.</p>
                                <a href="{{ route('purchase-requisitions.create') }}" class="btn-pr-primary btn-sm d-inline-flex">
                                    <i class="fas fa-plus"></i> Create New PR
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(isset($requisitions) && method_exists($requisitions, 'links') && $requisitions->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Showing {{ $requisitions->firstItem() }} to {{ $requisitions->lastItem() }} of {{ $requisitions->total() }} entries
                    </div>
                    <div>
                        {{ $requisitions->links() }}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
