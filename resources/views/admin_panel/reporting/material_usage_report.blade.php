@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --rpt-primary:    #2563eb;
        --rpt-primary-lt: #eff6ff;
        --rpt-success:    #059669;
        --rpt-success-lt: #ecfdf5;
        --rpt-danger:     #dc2626;
        --rpt-danger-lt:  #fef2f2;
        --rpt-border:     #e2e8f0;
        --rpt-bg:         #f8fafc;
        --rpt-card-bg:    #ffffff;
        --rpt-text:       #1e293b;
        --rpt-muted:      #64748b;
        --rpt-radius:     12px;
        --rpt-shadow:     0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
    }

    .rpt-page-header {
        margin-bottom: 1.25rem;
    }
    .rpt-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .rpt-subtitle {
        font-size: 0.815rem;
        color: #64748b;
        margin-top: 0.15rem;
    }

    /* Filter Card */
    .rpt-filter-card {
        background: #ffffff;
        border-radius: var(--rpt-radius);
        border: 1.5px solid #dbeafe;
        box-shadow: 0 2px 8px -2px rgba(37, 99, 235, 0.08);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .rpt-flabel {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--rpt-muted);
        margin-bottom: 0.25rem;
        display: block;
    }
    .rpt-finput {
        width: 100%;
        height: 36px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.825rem;
        padding: 0 0.6rem;
        color: var(--rpt-text);
        background: #fff;
    }
    .rpt-finput:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
    }

    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 1.5rem;
    }
    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--rpt-radius);
        padding: 1.15rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px -2px rgba(0, 0, 0, 0.07);
    }
    .kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.35rem;
    }
    .kpi-val {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
    }
    .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    /* Report Tabs */
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.25rem;
    }
    .nav-tabs-custom .nav-link {
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        border: none;
        padding: 0.75rem 1.25rem;
        border-radius: 0;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
    }
    .nav-tabs-custom .nav-link.active {
        color: #2563eb;
        border-bottom: 2px solid #2563eb;
        background: transparent;
    }

    /* Card Table Container */
    .rpt-table-container {
        background: #ffffff;
        border: 1.5px solid #dbeafe;
        border-radius: var(--rpt-radius);
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
        padding: 1.25rem;
    }
    .table thead th {
        background-color: #eff6ff !important;
        color: #1e40af !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        border: 1px solid #bfdbfe !important;
        border-bottom: 2px solid #60a5fa !important;
        padding: 0.6rem 0.5rem !important;
        vertical-align: middle;
    }
    .table tbody td {
        font-size: 0.78rem !important;
        color: #334155 !important;
        vertical-align: middle !important;
        padding: 0.5rem 0.5rem !important;
        border: 1px solid #dbeafe !important;
    }
    .table tbody tr:hover td {
        background-color: #f0f7ff !important;
    }

    @media print {
        .no-print { display: none !important; }
        .rpt-filter-card { display: none !important; }
        .nav-tabs-custom { display: none !important; }
        .rpt-table-container { border: none !important; box-shadow: none !important; padding: 0 !important; }
    }
</style>

<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="rpt-page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="rpt-title">
                <i class="fas fa-chart-pie text-primary"></i> Raw Material Consumption & Usage Report
            </h1>
            <p class="rpt-subtitle">Comprehensive analytical report on raw material issues, production costs, and consumption rates</p>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 8px; font-weight: 600;">
                <i class="fas fa-print mr-1"></i> Print Report
            </button>
            @can('material.usage.create')
                <a href="{{ route('material_usage.create') }}" class="btn btn-primary btn-sm px-3" style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-plus mr-1"></i> Issue Material
                </a>
            @endcan
        </div>
    </div>

    <!-- Filter Card -->
    <div class="rpt-filter-card no-print">
        <form method="GET" action="{{ route('report.material_usage') }}" class="row g-2 align-items-end">
            <div class="col-md-4 col-sm-6">
                <label class="rpt-flabel">Raw Material Item</label>
                <select name="product_id" class="rpt-finput">
                    <option value="">All Raw Materials</option>
                    @foreach($rawMaterials as $rm)
                        <option value="{{ $rm->id }}" {{ request('product_id') == $rm->id ? 'selected' : '' }}>
                            {{ $rm->item_name }} ({{ $rm->item_code ?? 'No Code' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="rpt-flabel">From Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rpt-finput">
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="rpt-flabel">To Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rpt-finput">
            </div>
            <div class="col-md-2 col-sm-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3 flex-grow-1" style="height: 36px; border-radius: 6px; font-weight: 600;">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('report.material_usage') }}" class="btn btn-outline-secondary btn-sm px-3" style="height: 36px; border-radius: 6px;" title="Reset Filters">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- KPI Metric Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div>
                <div class="kpi-label">Consumption Entries</div>
                <div class="kpi-val text-primary">{{ number_format($totalTransactions) }}</div>
            </div>
            <div class="kpi-icon" style="background-color: #dbeafe; color: #2563eb;">
                <i class="fas fa-list-check"></i>
            </div>
        </div>
        <div class="kpi-card">
            <div>
                <div class="kpi-label">Total Qty Consumed</div>
                <div class="kpi-val text-danger">{{ number_format($totalQtyUsed, 2) }}</div>
            </div>
            <div class="kpi-icon" style="background-color: #fee2e2; color: #dc2626;">
                <i class="fas fa-cubes"></i>
            </div>
        </div>
        <div class="kpi-card">
            <div>
                <div class="kpi-label">Total Cost Value</div>
                <div class="kpi-val text-dark">Rs. {{ number_format($totalCostUsed, 2) }}</div>
            </div>
            <div class="kpi-icon" style="background-color: #dcfce7; color: #16a34a;">
                <i class="fas fa-coins"></i>
            </div>
        </div>
        <div class="kpi-card">
            <div>
                <div class="kpi-label">Raw Materials Used</div>
                <div class="kpi-val text-info">{{ number_format($itemSummary->count()) }}</div>
            </div>
            <div class="kpi-icon" style="background-color: #cffafe; color: #0891b2;">
                <i class="fas fa-tags"></i>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs nav-tabs-custom no-print" id="reportTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="summary-tab" data-toggle="tab" href="#summaryPane" role="tab">
                <i class="fas fa-layer-group mr-1"></i> Item-Wise Consumption Summary ({{ $itemSummary->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="detailed-tab" data-toggle="tab" href="#detailedPane" role="tab">
                <i class="fas fa-receipt mr-1"></i> Detailed Consumption Transactions
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="reportTabContent">
        <!-- 1. Item-Wise Consumption Summary -->
        <div class="tab-pane fade show active" id="summaryPane" role="tabpanel">
            <div class="rpt-table-container">
                <h5 class="font-weight-bold text-dark mb-3" style="font-size: 1rem;">
                    <i class="fas fa-chart-bar text-primary mr-2"></i>Aggregated Raw Material Consumption
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Raw Material Name</th>
                                <th>Item Code</th>
                                <th>Unit</th>
                                <th class="text-right" style="width: 130px;">Issue Count</th>
                                <th class="text-right" style="width: 170px;">Total Qty Consumed</th>
                                <th class="text-right" style="width: 170px;">Total Cost Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($itemSummary as $idx => $row)
                                <tr>
                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <strong class="text-dark">{{ $row->item_name }}</strong>
                                    </td>
                                    <td class="text-muted small">{{ $row->item_code ?: '-' }}</td>
                                    <td>
                                        <span class="badge badge-light border text-muted">{{ $row->unit_name ?: 'Pcs' }}</span>
                                    </td>
                                    <td class="text-right text-muted">
                                        {{ $row->issue_count }} times
                                    </td>
                                    <td class="text-right font-weight-bold text-danger" style="font-size: 0.9rem;">
                                        {{ number_format($row->sum_qty, 2) }} {{ $row->unit_name ?: 'Pcs' }}
                                    </td>
                                    <td class="text-right font-weight-bold text-dark" style="font-size: 0.9rem;">
                                        Rs. {{ number_format($row->sum_cost, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-chart-pie fa-3x mb-3 text-light"></i>
                                        <p class="mb-0">No raw material consumption data recorded for the selected criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($itemSummary->count() > 0)
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="5" class="text-right py-2">Total Consumed:</td>
                                    <td class="text-right py-2 text-danger font-weight-bold" style="font-size: 1rem;">
                                        {{ number_format($totalQtyUsed, 2) }}
                                    </td>
                                    <td class="text-right py-2 text-dark font-weight-bold" style="font-size: 1rem;">
                                        Rs. {{ number_format($totalCostUsed, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. Detailed Consumption Logs -->
        <div class="tab-pane fade" id="detailedPane" role="tabpanel">
            <div class="rpt-table-container">
                <h5 class="font-weight-bold text-dark mb-3" style="font-size: 1rem;">
                    <i class="fas fa-history text-primary mr-2"></i>Detailed Issue Logs & Vouchers
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 110px;">Date</th>
                                <th style="width: 120px;">Voucher #</th>
                                <th>Raw Material</th>
                                <th class="text-right" style="width: 120px;">Qty Consumed</th>
                                <th class="text-right" style="width: 110px;">Unit Cost</th>
                                <th class="text-right" style="width: 130px;">Total Cost</th>
                                <th>Purpose / Notes</th>
                                <th style="width: 120px;">Issued By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($item->usage->date)->format('d M, Y') }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('material_usage.show', $item->material_usage_id) }}" target="_blank" class="badge badge-primary px-2 py-1" style="font-size: 0.78rem;">
                                            {{ $item->usage->usage_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <strong>{{ $item->product->item_name ?? 'N/A' }}</strong>
                                        @if($item->notes)
                                            <div class="text-muted small italic">{{ $item->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold text-danger">
                                        {{ number_format($item->qty_used, 2) }} {{ $item->unit_name }}
                                    </td>
                                    <td class="text-right text-muted">
                                        Rs. {{ number_format($item->unit_cost, 2) }}
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        Rs. {{ number_format($item->total_cost, 2) }}
                                    </td>
                                    <td>
                                        <span class="text-dark small">{{ $item->usage->purpose ?: ($item->usage->remarks ?: '-') }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $item->usage->user->name ?? 'Admin' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <p class="mb-0">No detailed consumption logs found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div class="d-flex justify-content-end mt-3 no-print">
                        {{ $items->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection