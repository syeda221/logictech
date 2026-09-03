@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    /* =============================================
       ROOT & BASE
    ============================================= */
    :root {
        --f: 'Inter', sans-serif;
        --bg: #f1f5f9;
        --card: #ffffff;
        --border: #e2e8f0;
        --text: #0f172a;
        --muted: #64748b;
        --blue: #3b82f6;
        --indigo: #6366f1;
        --green: #10b981;
        --amber: #f59e0b;
        --red: #f43f5e;
        --purple: #8b5cf6;
        --cyan: #06b6d4;
    }

    .db-wrap {
        font-family: var(--f);
        background: var(--bg);
        min-height: 100vh;
        padding: 1.75rem 2rem 3rem;
        color: var(--text);
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    /* =============================================
       HEADER BAR
    ============================================= */
    .db-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .db-header-left h1 {
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0 0 0.25rem;
        color: var(--text);
    }

    .db-header-left p {
        margin: 0;
        font-size: 0.88rem;
        color: var(--muted);
        font-weight: 500;
    }

    .db-btn-sync {
        background: var(--indigo);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.55rem 1.25rem;
        font-size: 0.84rem;
        font-weight: 700;
        font-family: var(--f);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    .db-btn-sync:hover { background: #4f46e5; transform: translateY(-1px); }

    /* =============================================
       REPORT QUICK NAV
    ============================================= */
    .db-nav-pills {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 2rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .db-nav-pills::-webkit-scrollbar { display: none; }

    .db-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 99px;
        font-size: 0.79rem;
        font-weight: 600;
        color: #475569 !important;
        text-decoration: none !important;
        white-space: nowrap;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .db-pill:hover {
        background: var(--indigo);
        border-color: var(--indigo);
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99,102,241,0.2);
    }
    .db-pill:hover i { color: #fff !important; }

    /* =============================================
       SECTION LABELS
    ============================================= */
    .db-section-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--muted);
        margin-bottom: 1rem;
        margin-top: 0.25rem;
    }
    .db-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* =============================================
       KPI STAT CARDS (TOP ROW)
    ============================================= */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.1rem 1.15rem;
        position: relative;
        overflow: hidden;
        transition: all 0.25s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        box-sizing: border-radius;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.07);
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--kpi-color, var(--indigo));
        border-radius: 14px 14px 0 0;
    }

    .kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        line-height: 1.2;
    }
    .kpi-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        background: var(--kpi-icon-bg, #eef2ff);
        color: var(--kpi-color, var(--indigo));
        flex-shrink: 0;
    }
    .kpi-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 0.3rem;
        letter-spacing: -0.02em;
        line-height: 1.2;
        word-break: break-word;
    }
    .kpi-trend {
        font-size: 0.72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }
    .up { color: var(--green); }
    .down { color: var(--red); }
    .kpi-trend-sub {
        font-size: 0.68rem;
        color: var(--muted);
        font-weight: 500;
        margin-left: 0.15rem;
    }

    /* =============================================
       CHART PANEL CARDS
    ============================================= */
    .panel {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.35rem 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }
    .panel-hd {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.1rem;
    }
    .panel-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .panel-badge {
        font-size: 0.7rem;
        font-weight: 700;
        background: #f1f5f9;
        color: var(--muted);
        padding: 3px 10px;
        border-radius: 99px;
        border: 1px solid var(--border);
    }
    .panel-body-flex { flex: 1; min-width: 0; width: 100%; }

    /* =============================================
       MAIN GRID LAYOUTS
    ============================================= */
    .grid-3col {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.2fr;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .grid-4col {
        display: grid;
        grid-template-columns: 1fr 1.3fr 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .grid-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    /* =============================================
       PRODUCT RANK LIST
    ============================================= */
    .rank-list { display: flex; flex-direction: column; gap: 0; }
    .rank-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.7rem 0;
        border-bottom: 1px solid #f8fafc;
        gap: 0.5rem;
    }
    .rank-item:last-child { border-bottom: none; }
    .rank-num {
        width: 26px; height: 26px;
        border-radius: 7px;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.73rem;
        color: var(--muted);
        flex-shrink: 0;
    }
    .rank-item:nth-child(1) .rank-num { background: #fef3c7; color: #d97706; }
    .rank-item:nth-child(2) .rank-num { background: #e2e8f0; color: #64748b; }
    .rank-item:nth-child(3) .rank-num { background: #ffe4cc; color: #ea580c; }

    .rank-name { font-size: 0.82rem; font-weight: 700; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rank-sub  { font-size: 0.7rem; color: var(--muted); font-weight: 500; }
    .rank-val  { font-size: 0.82rem; font-weight: 800; color: var(--indigo); white-space: nowrap; }

    /* =============================================
       BUSINESS SUMMARY BOXES
    ============================================= */
    .biz-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        height: 100%;
    }
    .biz-box {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        min-width: 0;
    }
    .biz-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .biz-val  { font-size: 1.1rem; font-weight: 800; color: var(--text); line-height: 1; }
    .biz-lbl  { font-size: 0.68rem; color: var(--muted); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .biz-trend { font-size: 0.65rem; font-weight: 700; }

    /* =============================================
       LEGEND LIST (CATEGORY/EXPENSE)
    ============================================= */
    .legend-list { display: flex; flex-direction: column; gap: 0.4rem; margin-top: 0.5rem; }
    .legend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78rem;
        padding: 0.15rem 0;
        gap: 0.5rem;
    }
    .legend-dot { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; }
    .legend-name { color: var(--text); font-weight: 600; font-size: 0.78rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .legend-right { display: flex; align-items: center; gap: 0.75rem; white-space: nowrap; flex-shrink: 0; }
    .legend-pct { color: var(--muted); font-size: 0.72rem; font-weight: 600; min-width: 30px; text-align: right; }
    .legend-amt { color: var(--text); font-weight: 800; font-size: 0.78rem; white-space: nowrap; }

    /* =============================================
       ACTIVITY FEED
    ============================================= */
    .act-list { display: flex; flex-direction: column; gap: 0; }
    .act-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.65rem 0;
        border-bottom: 1px solid #f8fafc;
        gap: 0.5rem;
        min-width: 0;
    }
    .act-row:last-child { border-bottom: none; }
    .act-ico {
        width: 30px; height: 30px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .act-title { font-size: 0.79rem; font-weight: 700; color: var(--text); line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .act-sub   { font-size: 0.68rem; color: var(--muted); }
    .act-amt   { font-size: 0.79rem; font-weight: 800; color: var(--text); white-space: nowrap; }
    .act-time  { font-size: 0.66rem; color: var(--muted); text-align: right; white-space: nowrap; }

    /* =============================================
       SPARKLINE CARDS (BOTTOM ROW)
    ============================================= */
    .spark-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .spark-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1rem 1.15rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        transition: all 0.2s;
        box-sizing: border-box;
        width: 100%;
    }
    .spark-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.07); }
    .spark-label { font-size: 0.72rem; font-weight: 700; color: var(--muted); margin-bottom: 0.2rem; }
    .spark-value { font-size: 1.1rem; font-weight: 800; color: var(--text); }
    .spark-trend { font-size: 0.7rem; font-weight: 700; margin-top: 0.2rem; }
    .spark-canvas-wrap { width: 80px; height: 42px; flex-shrink: 0; }

    /* =============================================
       RESPONSIVE
    ============================================= */
    @media (max-width: 1400px) {
        .kpi-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 991px) {
        .db-wrap { padding: 1.25rem 1rem 2.5rem; }
        .grid-3col, .grid-4col, .grid-2col { grid-template-columns: 1fr; gap: 1rem; }
        .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 0.85rem; }
        .spark-grid { grid-template-columns: repeat(2, 1fr); gap: 0.85rem; }
        .db-nav-pills { margin-bottom: 1.25rem; }
    }
    @media (max-width: 768px) {
        .spark-grid { grid-template-columns: 1fr !important; gap: 0.75rem; }
        .kpi-grid { grid-template-columns: 1fr !important; gap: 0.75rem; }
    }
    @media (max-width: 576px) {
        .db-wrap { padding: 0.75rem 0.5rem 2rem; }
        .db-header { flex-direction: column; align-items: flex-start; gap: 0.75rem; margin-bottom: 1.25rem; }
        .db-header-left h1 { font-size: 1.25rem; }
        .db-header-left p { font-size: 0.78rem; }
        .db-btn-sync { width: 100%; justify-content: center; padding: 0.5rem 1rem; }
        .kpi-card { padding: 0.9rem 1rem; }
        .kpi-value { font-size: 1.15rem; }
        .biz-grid { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
        .biz-box { padding: 0.65rem; }
        .biz-val { font-size: 1rem; }
        .panel { padding: 0.9rem 0.75rem; border-radius: 12px; }
        .panel-hd { margin-bottom: 0.75rem; }
        .panel-title { font-size: 0.85rem; }
        .legend-row { font-size: 0.74rem; }
        .legend-amt { font-size: 0.74rem; }
        .act-row { padding: 0.55rem 0; }
        .act-title { font-size: 0.74rem; max-width: 110px; }
        .act-amt { font-size: 0.74rem; }
        .db-section-label { font-size: 0.68rem; margin-bottom: 0.75rem; }
    }
</style>

<div class="db-wrap">

    {{-- =============================================
         1. HEADER
    ============================================= --}}
    <div class="db-header">
        <div class="db-header-left">
            <h1>Welcome back, {{ Auth::user()->name }}! 👋</h1>
            <p>Here's what's happening with your business today — <span id="db-live-date">{{ date('l, d M Y') }}</span> &nbsp;|&nbsp; <span id="db-live-time">{{ date('h:i:s A') }}</span></p>
        </div>
        @if(Auth::user()->usertype == 'admin')
            <button class="db-btn-sync" id="btnSyncCloud">
                <i class="fas fa-cloud-arrow-up"></i> Sync to Cloud
            </button>
        @endif
    </div>

    {{-- =============================================
         2. QUICK REPORT NAVIGATION PILLS
    ============================================= --}}
    <div class="db-nav-pills">
        <a href="{{ route('report.sale') }}"           class="db-pill"><i class="fa-solid fa-receipt"              style="color: var(--green);"></i>  Sales Report</a>
        <a href="{{ route('report.purchase') }}"       class="db-pill"><i class="fa-solid fa-cart-shopping"        style="color: var(--blue);"></i>   Purchase Report</a>
        <a href="{{ route('report.profit_loss') }}"    class="db-pill"><i class="fa-solid fa-chart-line"           style="color: var(--cyan);"></i>   Profit & Loss</a>
        <a href="{{ route('report.executive') }}"      class="db-pill"><i class="fa-solid fa-briefcase"            style="color: var(--purple);"></i>  Executive Report</a>
        <a href="{{ route('report.recovery') }}"       class="db-pill"><i class="fa-solid fa-file-invoice-dollar"  style="color: var(--amber);"></i>  Recovery Report</a>
        <a href="{{ route('report.payable') }}"        class="db-pill"><i class="fa-solid fa-hand-holding-dollar"  style="color: var(--red);"></i>    Payable Report</a>
        <a href="{{ route('report.parties_balance') }}" class="db-pill"><i class="fa-solid fa-users-viewfinder"   style="color: var(--blue);"></i>   Parties Balance</a>
        <a href="{{ route('reports.onhand') }}"        class="db-pill"><i class="fa-solid fa-boxes-stacked"        style="color: var(--green);"></i>  On-Hand Stock</a>
        <a href="{{ route('report.balance_sheet') }}"  class="db-pill"><i class="fa-solid fa-scale-balanced"      style="color: var(--muted);"></i>  Balance Sheet</a>
    </div>

    {{-- =============================================
         3. TOP 6 KPI STAT CARDS
    ============================================= --}}
    <div class="db-section-label"><i class="fas fa-chart-bar text-primary"></i> Key Performance Indicators</div>
    <div class="kpi-grid">

        <div class="kpi-card" style="--kpi-color: var(--purple); --kpi-icon-bg: #f5f3ff;">
            <div class="kpi-top">
                <span class="kpi-label">Total Sales (This Month)</span>
                <div class="kpi-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($salesThisMonth, 0) }}</div>
            <span class="kpi-trend up"><i class="fas fa-arrow-up"></i> 18.6% <span class="kpi-trend-sub">vs last month</span></span>
        </div>

        <div class="kpi-card" style="--kpi-color: var(--red); --kpi-icon-bg: #fff1f2;">
            <div class="kpi-top">
                <span class="kpi-label">Total Purchases (This Month)</span>
                <div class="kpi-icon"><i class="fa-solid fa-bag-shopping"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($purchasesThisMonth, 0) }}</div>
            <span class="kpi-trend up"><i class="fas fa-arrow-up"></i> 12.3% <span class="kpi-trend-sub">vs last month</span></span>
        </div>

        <div class="kpi-card" style="--kpi-color: var(--amber); --kpi-icon-bg: #fffbeb;">
            <div class="kpi-top">
                <span class="kpi-label">Gross Profit (This Month)</span>
                <div class="kpi-icon"><i class="fa-solid fa-chart-line"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($grossProfitThisMonth, 0) }}</div>
            <span class="kpi-trend up"><i class="fas fa-arrow-up"></i> 22.5% <span class="kpi-trend-sub">vs last month</span></span>
        </div>

        <div class="kpi-card" style="--kpi-color: var(--red); --kpi-icon-bg: #fff1f2;">
            <div class="kpi-top">
                <span class="kpi-label">Total Expenses (This Month)</span>
                <div class="kpi-icon"><i class="fa-solid fa-file-invoice"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($expensesThisMonth, 0) }}</div>
            <span class="kpi-trend down"><i class="fas fa-arrow-down"></i> 5.4% <span class="kpi-trend-sub">vs last month</span></span>
        </div>

        <div class="kpi-card" style="--kpi-color: var(--green); --kpi-icon-bg: #ecfdf5;">
            <div class="kpi-top">
                <span class="kpi-label">Net Profit (This Month)</span>
                <div class="kpi-icon"><i class="fa-solid fa-circle-dollar-to-slot"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($netProfitThisMonth, 0) }}</div>
            <span class="kpi-trend up"><i class="fas fa-arrow-up"></i> 28.7% <span class="kpi-trend-sub">vs last month</span></span>
        </div>

        <div class="kpi-card" style="--kpi-color: var(--blue); --kpi-icon-bg: #eff6ff;">
            <div class="kpi-top">
                <span class="kpi-label">Cash Balance</span>
                <div class="kpi-icon"><i class="fa-solid fa-wallet"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($totalCashAndBankBalance, 0) }}</div>
            <span style="font-size: 0.68rem; color: var(--muted); font-weight: 600;">Available Liquid Balance</span>
        </div>

    </div>

    {{-- =============================================
         4. SALES OVERVIEW + CATEGORY + TOP PRODUCTS
    ============================================= --}}
    <div class="db-section-label"><i class="fas fa-chart-area text-success"></i> Sales Analytics</div>
    <div class="grid-3col">

        {{-- Sales Overview Line Chart --}}
        <div class="panel">
            <div class="panel-hd">
                <span class="panel-title"><i class="fas fa-chart-area" style="color:var(--indigo);"></i> Sales Overview</span>
                <span class="panel-badge">Last 7 Days</span>
            </div>
            <div class="panel-body-flex">
                <div style="height: 250px; position: relative;">
                    <canvas id="chartSalesOverview"></canvas>
                </div>
            </div>
        </div>

        {{-- Sales by Category Doughnut --}}
        <div class="panel">
            <div class="panel-hd">
                <span class="panel-title"><i class="fas fa-pie-chart" style="color:var(--blue);"></i> By Category</span>
            </div>
            <div class="panel-body-flex d-flex flex-column align-items-center">
                <div style="position: relative; width: 160px; height: 160px; margin-bottom: 1rem;">
                    <canvas id="chartSalesCat"></canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                        <div style="font-size:9px;font-weight:700;color:var(--muted);text-transform:uppercase;">Total</div>
                        <div style="font-size:12px;font-weight:800;color:var(--text);">Rs {{ number_format($salesThisMonth, 0) }}</div>
                    </div>
                </div>
                <div class="legend-list w-100" id="catLegend"></div>
            </div>
        </div>

        {{-- Top Selling Products --}}
        <div class="panel">
            <div class="panel-hd">
                <span class="panel-title"><i class="fas fa-fire" style="color:var(--red);"></i> Top Products</span>
                <span class="panel-badge">By Qty</span>
            </div>
            <div class="panel-body-flex">
                <div class="rank-list">
                    @if(isset($topProducts) && count($topProducts) > 0)
                        @php $r = 1; @endphp
                        @foreach($topProducts->take(6) as $tp)
                            <div class="rank-item">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rank-num">{{ $r++ }}</div>
                                    <div>
                                        <div class="rank-name">{{ Str::limit($tp->product_name ?: 'Standard Item', 20) }}</div>
                                        <div class="rank-sub">{{ number_format($tp->total_qty) }} units sold</div>
                                    </div>
                                </div>
                                <div class="rank-val">Rs {{ number_format($tp->total_revenue ?? 0) }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4" style="font-size:0.82rem;">No sales data available.</div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- =============================================
         5. BUSINESS SUMMARY + CASH FLOW + EXPENSE + ACTIVITY
    ============================================= --}}
    <div class="db-section-label"><i class="fas fa-briefcase text-warning"></i> Business Summary & Cash Flow</div>
    <div class="grid-4col">

        {{-- Business Summary Counter Boxes --}}
        <div class="panel">
            <div class="panel-hd">
                <span class="panel-title"><i class="fas fa-building" style="color:var(--indigo);"></i> Business Summary</span>
            </div>
            <div class="panel-body-flex">
                <div class="biz-grid">
                    <div class="biz-box">
                        <div class="biz-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="biz-val">{{ number_format($customerscount) }}</div>
                            <div class="biz-lbl">Customers</div>
                            <div class="biz-trend up">↑ 15.3%</div>
                        </div>
                    </div>
                    <div class="biz-box">
                        <div class="biz-icon" style="background:#f3e8ff; color:#8b5cf6;"><i class="fas fa-truck"></i></div>
                        <div>
                            <div class="biz-val">{{ number_format($vendorCount) }}</div>
                            <div class="biz-lbl">Suppliers</div>
                            <div class="biz-trend up">↑ 10.6%</div>
                        </div>
                    </div>
                    <div class="biz-box">
                        <div class="biz-icon" style="background:#fef3c7; color:#d97706;"><i class="fas fa-box-open"></i></div>
                        <div>
                            <div class="biz-val">{{ number_format($productCount) }}</div>
                            <div class="biz-lbl">Products</div>
                            <div class="biz-trend up">↑ 8.3%</div>
                        </div>
                    </div>
                    <div class="biz-box">
                        <div class="biz-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <div class="biz-val">{{ number_format($employeeCount) }}</div>
                            <div class="biz-lbl">Employees</div>
                            <div class="biz-trend up">↑ 5.2%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cash Flow Bar Chart --}}
        <div class="panel">
            <div class="panel-hd">
                <span class="panel-title"><i class="fas fa-exchange-alt" style="color:var(--green);"></i> Cash Flow Overview</span>
                <span class="panel-badge">This Month</span>
            </div>
            <div class="panel-body-flex">
                <div style="height: 200px; position: relative;">
                    <canvas id="chartCashFlow"></canvas>
                </div>
            </div>
        </div>

        {{-- Expense Breakdown Doughnut --}}
        <div class="panel">
            <div class="panel-hd">
                <span class="panel-title"><i class="fas fa-receipt" style="color:var(--red);"></i> Expense Breakdown</span>
            </div>
            <div class="panel-body-flex d-flex flex-column align-items-center">
                <div style="position: relative; width: 130px; height: 130px; margin-bottom: 0.85rem;">
                    <canvas id="chartExpBreak"></canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                        <div style="font-size:9px;font-weight:700;color:var(--muted);text-transform:uppercase;">Total</div>
                        <div style="font-size:11px;font-weight:800;color:var(--red);">Rs {{ number_format($expensesThisMonth, 0) }}</div>
                    </div>
                </div>
                <div class="legend-list w-100" id="expLegend"></div>
            </div>
        </div>

        {{-- Recent Activities Feed --}}
        <div class="panel">
            <div class="panel-hd">
                <span class="panel-title"><i class="fas fa-bell" style="color:var(--amber);"></i> Recent Activities</span>
                <a href="{{ route('report.sale') }}" style="font-size:0.72rem; color:var(--indigo); font-weight:700; text-decoration:none;">View All →</a>
            </div>
            <div class="panel-body-flex">
                <div class="act-list" style="max-height: 210px; overflow-y: auto;">
                    @if(isset($recentActivities) && count($recentActivities) > 0)
                        @foreach($recentActivities as $act)
                            <div class="act-row">
                                <div class="act-ico" style="background: {{ $act['bg'] }};">
                                    <i class="fa-solid {{ $act['icon'] }}"></i>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div class="act-title text-truncate">{{ $act['title'] }}</div>
                                    <div class="act-sub">{{ $act['subtitle'] }}</div>
                                </div>
                                <div style="text-align: right; flex-shrink:0;">
                                    <div class="act-amt">{{ $act['amount'] }}</div>
                                    <div class="act-time">{{ $act['time'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3" style="font-size:0.8rem;">No recent activities.</div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- =============================================
         6. RECEIVABLES / PAYABLES / STOCK / ACCOUNTS
    ============================================= --}}
    <div class="db-section-label"><i class="fas fa-balance-scale text-info"></i> Financial Position</div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">

        <div class="kpi-card" style="--kpi-color: var(--indigo); --kpi-icon-bg: #eef2ff;">
            <div class="kpi-top">
                <span class="kpi-label">Customer Receivables</span>
                <div class="kpi-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($totalReceivables, 0) }}</div>
            <a href="{{ route('report.recovery') }}" style="font-size:0.7rem; color:var(--indigo); font-weight:700; text-decoration:none;">View Recovery Report →</a>
        </div>

        <div class="kpi-card" style="--kpi-color: var(--amber); --kpi-icon-bg: #fffbeb;">
            <div class="kpi-top">
                <span class="kpi-label">Vendor Payables</span>
                <div class="kpi-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($totalPayables, 0) }}</div>
            <a href="{{ route('report.payable') }}" style="font-size:0.7rem; color:var(--amber); font-weight:700; text-decoration:none;">View Payable Report →</a>
        </div>

        <div class="kpi-card" style="--kpi-color: var(--purple); --kpi-icon-bg: #faf5ff;">
            <div class="kpi-top">
                <span class="kpi-label">Stock Inventory Value</span>
                <div class="kpi-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            </div>
            <div class="kpi-value">Rs {{ number_format($totalStockValue, 0) }}</div>
            <a href="{{ route('reports.onhand') }}" style="font-size:0.7rem; color:var(--purple); font-weight:700; text-decoration:none;">View On-Hand Stock →</a>
        </div>

        @if(isset($cashAndBankAccounts))
            @foreach($cashAndBankAccounts as $acc)
                @php $isCash = strtolower($acc->head->name) == 'cash'; @endphp
                <div class="kpi-card" style="--kpi-color: {{ $isCash ? 'var(--blue)' : 'var(--cyan)' }}; --kpi-icon-bg: {{ $isCash ? '#eff6ff' : '#ecfeff' }};">
                    <div class="kpi-top">
                        <span class="kpi-label">{{ $acc->title }}</span>
                        <div class="kpi-icon">
                            <i class="fa-solid {{ $isCash ? 'fa-wallet' : 'fa-building-columns' }}"></i>
                        </div>
                    </div>
                    <div class="kpi-value">Rs {{ number_format($acc->current_balance, 0) }}</div>
                    <a href="{{ route('accounts.ledger', $acc->id) }}" style="font-size:0.7rem; color:{{ $isCash ? 'var(--blue)' : 'var(--cyan)' }}; font-weight:700; text-decoration:none;">View Ledger →</a>
                </div>
            @endforeach
        @endif

    </div>

    {{-- =============================================
         7. MONTHLY PERFORMANCE SPARKLINES
    ============================================= --}}
    <div class="db-section-label"><i class="fas fa-chart-line text-success"></i> Monthly Performance Trend</div>
    <div class="spark-grid">

        <div class="spark-card">
            <div>
                <div class="spark-label">Total Sales</div>
                <div class="spark-value">Rs {{ number_format($salesThisMonth, 0) }}</div>
                <div class="spark-trend up">↑ 18.6% vs last month</div>
            </div>
            <div class="spark-canvas-wrap"><canvas id="spkSales"></canvas></div>
        </div>

        <div class="spark-card">
            <div>
                <div class="spark-label">Total Purchases</div>
                <div class="spark-value">Rs {{ number_format($purchasesThisMonth, 0) }}</div>
                <div class="spark-trend up">↑ 12.3% vs last month</div>
            </div>
            <div class="spark-canvas-wrap"><canvas id="spkPurchases"></canvas></div>
        </div>

        <div class="spark-card">
            <div>
                <div class="spark-label">Gross Profit</div>
                <div class="spark-value">Rs {{ number_format($grossProfitThisMonth, 0) }}</div>
                <div class="spark-trend up">↑ 22.5% vs last month</div>
            </div>
            <div class="spark-canvas-wrap"><canvas id="spkGross"></canvas></div>
        </div>

        <div class="spark-card">
            <div>
                <div class="spark-label">Net Profit</div>
                <div class="spark-value">Rs {{ number_format($netProfitThisMonth, 0) }}</div>
                <div class="spark-trend up">↑ 28.7% vs last month</div>
            </div>
            <div class="spark-canvas-wrap"><canvas id="spkNet"></canvas></div>
        </div>

    </div>

    {{-- Low Stock Alert --}}
    @can('products.view')
        @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
            <div class="db-section-label" style="margin-top: 0.5rem;">
                <i class="fas fa-triangle-exclamation text-danger"></i> Low Stock Alarm
            </div>
            <div class="panel" style="margin-bottom: 2rem;">
                <div class="panel-hd">
                    <span class="panel-title" style="color: var(--red);">
                        <i class="fas fa-triangle-exclamation"></i> Low Stock Alert Products
                        <span style="background:#fff1f2; color:var(--red); font-size:0.7rem; font-weight:800; padding:3px 10px; border-radius:99px; border:1px solid #fecdd3;">
                            {{ $lowStockProducts->count() }} Items
                        </span>
                    </span>
                    <a href="{{ route('product') }}?status=active" style="font-size:0.78rem; color:var(--red); font-weight:700; text-decoration:none;">Manage Inventory →</a>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="chartLowStock"></canvas>
                </div>
            </div>
        @endif
    @endcan

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Live Clock
    setInterval(function () {
        const n = new Date();
        const el = document.getElementById('db-live-time');
        if (el) el.innerText = n.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:true });
    }, 1000);

    const salesStats  = @json($salesChartStats);
    const catData     = @json($salesByCategory ?? []);
    const expData     = @json($expenseBreakdown ?? []);
    const topProds    = @json($topProducts ?? []);
    const topCusts    = @json($topCustomers ?? []);

    const COLORS = ['#6366f1','#3b82f6','#10b981','#f59e0b','#f43f5e','#8b5cf6','#06b6d4','#84cc16','#f97316','#ec4899'];

    function grad(ctx, c1, c2, h=250) {
        const g = ctx.createLinearGradient(0, 0, 0, h);
        g.addColorStop(0, c1); g.addColorStop(1, c2);
        return g;
    }

    const baseOpts = {
        responsive: true, maintainAspectRatio: false,
        legend: { display: false },
        plugins: { legend: { display: false }, tooltip: {
            backgroundColor: '#0f172a', cornerRadius: 8, padding: 10,
            titleFont: { family: 'Inter', size: 12, weight: 'bold' },
            bodyFont:  { family: 'Inter', size: 11 }
        }},
        scales: {
            xAxes: [{ gridLines: { display: false }, ticks: { fontColor: '#94a3b8', fontSize: 10, autoSkip: true } }],
            yAxes: [{ gridLines: { color: 'rgba(0,0,0,0.04)' }, ticks: { fontColor: '#94a3b8', fontSize: 10, callback: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v) } }],
            x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 10 }, color: '#94a3b8', autoSkip: true } },
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'Inter', size: 10 }, color: '#94a3b8', callback: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v) } }
        }
    };

    // ── 1. SALES OVERVIEW LINE CHART ──────────────────────
    const soCtx = document.getElementById('chartSalesOverview');
    if (soCtx) {
        const ctx = soCtx.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesStats.daily.categories,
                datasets: [
                    {
                        label: 'Sales',
                        data: salesStats.daily.series[0]?.data || [],
                        borderColor: '#6366f1', borderWidth: 2.5,
                        backgroundColor: grad(ctx, 'rgba(99,102,241,0.18)', 'rgba(99,102,241,0.0)'),
                        fill: true, tension: 0.42,
                        pointBackgroundColor: '#6366f1', pointBorderColor: '#fff',
                        pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6
                    },
                    {
                        label: 'Target',
                        data: (salesStats.daily.series[0]?.data || []).map(v => v * 1.12 + 3000),
                        borderColor: '#cbd5e1', borderWidth: 1.5,
                        borderDash: [5, 4], fill: false, tension: 0.42, pointRadius: 0
                    }
                ]
            },
            options: { ...baseOpts,
                legend: { display: true, position: 'top', align: 'end',
                    labels: { font: { family: 'Inter', size: 11, weight: '600' }, usePointStyle: true, boxWidth: 6, padding: 14 } },
                plugins: { ...baseOpts.plugins,
                    legend: { display: true, position: 'top', align: 'end',
                        labels: { font: { family: 'Inter', size: 11, weight: '600' }, usePointStyle: true, boxWidth: 6, padding: 14 } },
                    tooltip: { ...baseOpts.plugins.tooltip,
                        callbacks: { label: c => ` ${c.dataset.label}: Rs ${parseFloat(c.raw || c.value || 0).toLocaleString()}` } }
                }
            }
        });
    }

    // ── 2. SALES BY CATEGORY DOUGHNUT ─────────────────────
    const catCtx = document.getElementById('chartSalesCat');
    if (catCtx) {
        let labels = catData.length ? catData.map(c => c.category_name) : ['General', 'Others'];
        let values = catData.length ? catData.map(c => parseFloat(c.total_amount) || 0) : [1, 1];
        const total = values.reduce((a,b) => a+b, 0);

        new Chart(catCtx.getContext('2d'), {
            type: 'doughnut',
            data: { labels, datasets: [{ data: values, backgroundColor: COLORS, borderWidth: 3, borderColor: '#fff', hoverOffset: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, cutoutPercentage: 74, cutout: '74%',
                legend: { display: false },
                plugins: { legend: { display: false },
                    tooltip: { backgroundColor: '#0f172a', padding: 10, bodyFont: { family: 'Inter', size: 11 },
                        callbacks: { label: c => ` ${c.label}: Rs ${parseFloat(c.raw || c.value || 0).toLocaleString()}` } }
                }
            }
        });

        // Legend
        const el = document.getElementById('catLegend');
        if (el) {
            el.innerHTML = labels.map((l,i) => {
                const pct = total > 0 ? Math.round((values[i]/total)*100) : 0;
                return `<div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot" style="background:${COLORS[i]};"></span>
                        <span class="legend-name">${l}</span>
                    </div>
                    <div class="legend-right">
                        <span class="legend-pct">${pct}%</span>
                        <span class="legend-amt">Rs ${values[i].toLocaleString()}</span>
                    </div>
                </div>`;
            }).join('');
        }
    }

    // ── 3. CASH FLOW GROUPED BAR ────────────────────────
    const cfCtx = document.getElementById('chartCashFlow');
    if (cfCtx) {
        const payIn  = parseFloat('{{ $paymentInMonth }}')  || 0;
        const payOut = parseFloat('{{ $paymentOutMonth }}') || 0;
        const inAll  = parseFloat('{{ $paymentInOverall }}')  || 0;
        const outAll = parseFloat('{{ $paymentOutOverall }}') || 0;

        new Chart(cfCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Today In', 'Today Out', 'Total In', 'Total Out'],
                datasets: [
                    { data: [payIn, payOut, inAll, outAll],
                      backgroundColor: ['#10b981','#f43f5e','rgba(16,185,129,0.4)','rgba(244,63,94,0.4)'],
                      borderRadius: 6, barThickness: 24 }
                ]
            },
            options: { ...baseOpts,
                legend: { display: false },
                plugins: { ...baseOpts.plugins,
                    legend: { display: false },
                    tooltip: { ...baseOpts.plugins.tooltip,
                        callbacks: { label: c => ` Rs ${parseFloat(c.raw || c.value || 0).toLocaleString()}` } }
                }
            }
        });
    }

    // ── 4. EXPENSE BREAKDOWN DOUGHNUT ─────────────────────
    const exCtx = document.getElementById('chartExpBreak');
    if (exCtx) {
        const EXP_COLORS = ['#8b5cf6','#3b82f6','#0ea5e9','#f59e0b','#cbd5e1'];
        let labels = expData.length ? expData.map(e => e.name || 'General') : ['N/A'];
        let values = expData.length ? expData.map(e => parseFloat(e.total) || 0) : [1];
        const total = values.reduce((a,b) => a+b, 0);

        new Chart(exCtx.getContext('2d'), {
            type: 'doughnut',
            data: { labels, datasets: [{ data: values, backgroundColor: EXP_COLORS, borderWidth: 3, borderColor: '#fff', hoverOffset: 5 }] },
            options: { responsive: true, maintainAspectRatio: false, cutoutPercentage: 74, cutout: '74%',
                legend: { display: false },
                plugins: { legend: { display: false } }
            }
        });

        const el = document.getElementById('expLegend');
        if (el) {
            el.innerHTML = labels.map((l,i) => {
                const pct = total > 0 ? Math.round((values[i]/total)*100) : 0;
                return `<div class="legend-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="legend-dot" style="background:${EXP_COLORS[i]};"></span>
                        <span class="legend-name">${l}</span>
                    </div>
                    <div class="legend-right">
                        <span class="legend-pct">${pct}%</span>
                        <span class="legend-amt">Rs ${values[i].toLocaleString()}</span>
                    </div>
                </div>`;
            }).join('');
        }
    }

    // ── 5. SPARKLINES ─────────────────────────────────────
    function sparkline(id, color, data) {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el.getContext('2d'), {
            type: 'line',
            data: { labels: data.map(() => ''),
                datasets: [{ data, borderColor: color, borderWidth: 2, fill: false, tension: 0.4, pointRadius: 0 }] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: { enabled: false },
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: {
                    xAxes: [{ display: false, gridLines: { display: false } }],
                    yAxes: [{ display: false, gridLines: { display: false } }],
                    x: { display: false, grid: { display: false } },
                    y: { display: false, grid: { display: false } }
                }
            }
        });
    }

    sparkline('spkSales',     '#6366f1', [30,45,38,60,55,70,85]);
    sparkline('spkPurchases', '#3b82f6', [40,35,50,42,60,55,65]);
    sparkline('spkGross',     '#10b981', [20,35,30,45,42,55,62]);
    sparkline('spkNet',       '#059669', [15,28,24,38,35,48,55]);

    // ── 6. LOW STOCK BAR CHART ─────────────────────────────
    const lsCtx = document.getElementById('chartLowStock');
    if (lsCtx) {
        const lsData = @json($lowStockProducts ?? collect());
        if (lsData.length > 0) {
            const lsNames  = lsData.map(p => p.item_name ? p.item_name.substring(0,22) : 'Unknown');
            const lsStock  = lsData.map(p => parseFloat(p.current_cartons) || 0);
            const lsAlert  = lsData.map(p => parseFloat(p.alert_carton_quantity) || 0);
            new Chart(lsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: lsNames,
                    datasets: [
                        { label: 'Alert Level', data: lsAlert, backgroundColor: 'rgba(244,63,94,0.85)', borderRadius: 5, barPercentage: 0.55 },
                        { label: 'Current Stock', data: lsStock, backgroundColor: 'rgba(99,102,241,0.85)', borderRadius: 5, barPercentage: 0.55 }
                    ]
                },
                options: { ...baseOpts,
                    plugins: { ...baseOpts.plugins,
                        legend: { display: true, position: 'top', align: 'end',
                            labels: { font: { family:'Inter', size:11, weight:'600' }, usePointStyle:true, boxWidth:6 } },
                        tooltip: { ...baseOpts.plugins.tooltip,
                            callbacks: { label: c => ` ${c.dataset.label}: ${c.raw} cartons` } }
                    },
                    scales: { ...baseOpts.scales,
                        y: { ...baseOpts.scales.y,
                            ticks: { ...baseOpts.scales.y.ticks, callback: v => v + ' ctns' } }
                    }
                }
            });
        }
    }

    // ── 7. SYNC BUTTON ─────────────────────────────────────
    const syncBtn = document.getElementById('btnSyncCloud');
    if (syncBtn) {
        syncBtn.addEventListener('click', function () {
            syncBtn.disabled = true;
            syncBtn.innerHTML = '<i class="fa fa-sync-alt fa-spin"></i> Syncing...';
            Swal.fire({ title: 'Syncing Cloud...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('{{ route('admin.sync_to_cloud') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json()).then(d => {
                syncBtn.disabled = false;
                syncBtn.innerHTML = '<i class="fas fa-cloud-arrow-up"></i> Sync to Cloud';
                if (d.status === 'success') {
                    Swal.fire({ icon:'success', title:'Sync Successful', text:d.message, confirmButtonColor:'#6366f1' })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon:'error', title:'Sync Failed', text: d.message || 'Error occurred.', confirmButtonColor:'#6366f1' });
                }
            }).catch(() => {
                syncBtn.disabled = false;
                syncBtn.innerHTML = '<i class="fas fa-cloud-arrow-up"></i> Sync to Cloud';
                Swal.fire({ icon:'error', title:'Connection Error', text:'Could not reach cloud server.', confirmButtonColor:'#6366f1' });
            });
        });
    }

});
</script>
@endsection
