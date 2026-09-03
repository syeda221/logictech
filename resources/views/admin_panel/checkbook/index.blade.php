@extends('admin_panel.layout.app')

@section('content')
    <style>
        body {
            background-color: #f4f7fe;
        }

        .main-content-inner {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Premium Box Styling */
        .closing-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
            transition: all 0.3s ease;
        }

        .closing-box:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        /* KPI Micro Cards */
        .kpi-card {
            border-radius: 16px;
            padding: 20px;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.1);
        }

        .kpi-card.opening { background: linear-gradient(135deg, #475569 0%, #1e293b 100%); }
        .kpi-card.inflows { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
        .kpi-card.outflows { background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%); }
        .kpi-card.expected { background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); }

        .kpi-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; opacity: 0.85; margin-bottom: 6px; }
        .kpi-value { font-size: 24px; font-weight: 800; }

        .btn-premium-primary {
            background: linear-gradient(135deg, #1e293b, #000000) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            height: 44px !important;
            padding: 0 24px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-premium-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2) !important;
        }

        .btn-premium-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            height: 44px !important;
            padding: 0 24px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-premium-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.2) !important;
        }

        .premium-table {
            border: none !important;
        }
        
        .premium-table thead th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.8px;
            padding: 16px 12px !important;
        }
        
        .premium-table tbody td {
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 14px 12px !important;
            font-size: 13px !important;
            color: #334155 !important;
        }

        .btn-xs {
            padding: 3px 8px !important;
            font-size: 11px !important;
            border-radius: 6px !important;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Page Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bolder mb-1 text-dark">Day & Shift Closing Manager</h4>
                        <p class="text-secondary mb-0" style="font-size: 14px;">Open, manage drawer flows, and close business days</p>
                    </div>
                    <div>
                        <a href="{{ route('checkbook.transactions') }}" class="btn btn-outline-dark fw-bold d-flex align-items-center" style="height:44px; border-radius:10px;">
                            <i class="fas fa-history me-2"></i> View Detailed Cashbook Logs
                        </a>
                    </div>
                </div>

                @if ($activeShift)
                    {{-- 1. Active Shift KPI Grid --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="kpi-card opening">
                                <div class="kpi-title">Opening Balance</div>
                                <div class="kpi-value">{{ number_format($activeShift->opening_balance, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kpi-card inflows">
                                <div class="kpi-title">Live Inflows (Sales)</div>
                                <div class="kpi-value">+{{ number_format($activeShift->inflow_amount + $activeShift->manual_in, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kpi-card outflows">
                                <div class="kpi-title">Live Outflows (Expenses)</div>
                                <div class="kpi-value">-{{ number_format($activeShift->outflow_amount + $activeShift->manual_out, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kpi-card expected">
                                <div class="kpi-title">Expected Cash</div>
                                <div class="kpi-value">{{ number_format($activeShift->expected_balance, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Adjustment / Shortage Alert Box --}}
                    <div class="closing-box mb-4 bg-light d-flex justify-content-between align-items-center border">
                        <div>
                            <span class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">Live Adjustment / Difference</span>
                            <strong class="fs-4 text-dark" id="liveDiffVal">0.00</strong>
                        </div>
                        <span id="diffIndicator" class="badge bg-secondary rounded-pill px-4 py-2 fs-6">Balanced</span>
                    </div>

                    {{-- 3. Drawer Operations & Close Shift Row --}}
                    <div class="row g-3 mb-4">
                        <!-- Drawer Operations -->
                        <div class="col-md-4">
                            <div class="closing-box h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-coins text-primary me-2"></i>Add Drawer Transaction</h6>
                                <form action="{{ route('drawer-transaction.store') }}" method="POST" autocomplete="off">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size:0.75rem;">Transaction Type</label>
                                        <select name="type" class="form-select text-xs" style="height:40px; border-radius:8px;" required>
                                            <option value="out">Cash Out (Withdrawal / Pay)</option>
                                            <option value="in">Cash In (Deposit / Add)</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size:0.75rem;">Category</label>
                                        <select name="category" class="form-select text-xs" style="height:40px; border-radius:8px;" required>
                                            <option value="temporary_market">Temporary Market Borrow</option>
                                            <option value="expense">Direct Expense</option>
                                            <option value="owner_withdrawal">Owner Withdrawal</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size:0.75rem;">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" style="height:40px; border-radius:8px;" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size:0.75rem;">Description / Person Name</label>
                                        <input type="text" name="description" class="form-control" placeholder="e.g. Aslam borrow..." style="height:40px; border-radius:8px;">
                                    </div>
                                    <button type="submit" class="btn btn-premium-primary w-100 fw-bold" style="height:40px; border-radius:8px;"><i class="fas fa-save me-1"></i>Save Transaction</button>
                                </form>
                            </div>
                        </div>

                        <!-- Current Shift Logs -->
                        <div class="col-md-4">
                            <div class="closing-box h-100 d-flex flex-column">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list text-secondary me-2"></i>Drawer Logs (This Shift)</h6>
                                <div class="flex-grow-1" style="max-height: 290px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    @forelse($drawerLogs as $log)
                                        <div class="d-flex justify-content-between align-items-center border-bottom p-2" style="font-size:0.8rem;">
                                            <div>
                                                <span class="badge {{ $log->type == 'in' ? 'bg-success' : 'bg-danger' }} me-1" style="font-size:0.6rem; padding: 2px 4px;">
                                                    {{ $log->type == 'in' ? 'IN' : 'OUT' }}
                                                </span>
                                                <strong class="text-dark">{{ number_format($log->amount, 2) }}</strong>
                                                <div class="text-secondary small mt-0.5" style="font-size: 0.72rem;">
                                                    {{ str_replace('_', ' ', $log->category) }} - {{ $log->description }}
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @if($log->status == 'pending')
                                                    <form action="{{ route('drawer-transaction.return', $log->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-success fw-bold text-white"><i class="fas fa-reply me-1"></i>Return</button>
                                                    </form>
                                                @elseif($log->status == 'returned')
                                                    <span class="badge bg-success rounded-pill" style="font-size:0.6rem;">Returned</span>
                                                @else
                                                    <span class="badge bg-light text-dark rounded-pill border" style="font-size:0.6rem;">Settled</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted text-center py-5" style="font-size:0.75rem;">No drawer movements logged in this shift.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Close Day/Shift Form -->
                        <div class="col-md-4">
                            <div class="closing-box h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-lock text-danger me-2"></i>Close Shift</h6>
                                <form action="{{ route('day-closing.close') }}" method="POST" autocomplete="off">
                                    @csrf
                                    <div class="mb-3 p-3 bg-light rounded-3 border">
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.65rem;">Opened At</small>
                                        <strong class="text-dark"><i class="far fa-calendar-alt text-secondary me-1"></i> {{ $activeShift->opened_at->format('d/m/Y h:i A') }}</strong>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size:0.75rem;">Actual Drawer Cash <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control fw-bold fs-5" name="actual_balance" id="actual_balance" placeholder="0.00" required style="height:44px; border-radius:8px;">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size:0.75rem;">Closing Notes</label>
                                        <textarea class="form-control" name="remarks" rows="2" placeholder="Describe any shortages or adjustments..." style="border-radius:8px;"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100 fw-bold" style="border-radius:10px; height:44px;"><i class="fas fa-power-off me-2"></i>Close Shift & Save</button>
                                </form>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- Shift is Closed, show Opening Box --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mx-auto">
                            <div class="closing-box">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-store-slash text-danger me-2"></i>Day / Shift Closed</h5>
                                    <span class="badge bg-danger rounded-pill px-3 py-2">Closed</span>
                                </div>
                                <p class="text-muted small mb-4">A shift is not currently active. Specify the starting opening cash to open a new shift and start selling.</p>
                                
                                <form action="{{ route('day-closing.open') }}" method="POST" autocomplete="off">
                                    @csrf

                                    @if($pendingReturns->isNotEmpty())
                                        <div class="mb-3 p-3 border rounded-3 bg-light">
                                            <label class="form-label mb-2 fw-bold text-danger d-block" style="font-size:0.75rem;"><i class="fas fa-undo me-1"></i> Pending Market Returns (Select to add to Opening Balance):</label>
                                            <div style="max-height: 120px; overflow-y: auto;">
                                                @foreach($pendingReturns as $pr)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="return_ids[]" value="{{ $pr->id }}" id="return_id_{{ $pr->id }}">
                                                        <label class="form-check-label text-dark fw-semibold" style="font-size: 13px; cursor: pointer;" for="return_id_{{ $pr->id }}">
                                                            <span class="text-danger fw-bold">{{ number_format($pr->amount, 2) }}</span> - {{ $pr->description }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-4">
                                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size:0.75rem;">Opening Cash/Bank Balance <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control fw-bold fs-5" name="opening_balance" placeholder="0.00" value="0.00" required style="height: 48px; border-radius: 8px;">
                                    </div>
                                    <button type="submit" class="btn btn-premium-success w-100 fw-bold" style="border-radius:10px; height:48px;"><i class="fas fa-play me-2"></i>Open New Day/Shift</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 4. Date-wise closing history --}}
                <div class="card premium-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-history text-secondary me-2"></i>Past Day Closing Records</h5>
                        <div class="table-responsive">
                            <table id="closings-history-table" class="table table-hover align-middle datanew premium-table" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 text-secondary fw-semibold text-uppercase small">Shift Interval</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Opening</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Inflows</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Outflows</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Expected</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Actual</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Adjustment / Diff</th>
                                        <th class="py-3 pe-3 text-secondary fw-semibold text-uppercase small">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($closings as $c)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold text-dark" style="font-size:13px;">Opened: {{ $c->opened_at->format('d/m/Y h:i A') }}</div>
                                                <div class="text-muted mt-1" style="font-size:12px;">Closed: {{ $c->closed_at->format('d/m/Y h:i A') }}</div>
                                            </td>
                                            <td class="text-end fw-bold">{{ number_format($c->opening_balance, 2) }}</td>
                                            <td class="text-end text-success fw-bold">+{{ number_format($c->inflow_amount, 2) }}</td>
                                            <td class="text-end text-danger fw-bold">-{{ number_format($c->outflow_amount, 2) }}</td>
                                            <td class="text-end fw-bold">{{ number_format($c->expected_balance, 2) }}</td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($c->actual_balance, 2) }}</td>
                                            <td class="text-end">
                                                @if ($c->difference == 0)
                                                    <span class="badge bg-success rounded-pill px-2 py-1">0.00 (Balanced)</span>
                                                @elseif ($c->difference > 0)
                                                    <span class="badge bg-primary rounded-pill px-2 py-1">+{{ number_format($c->difference, 2) }} (Excess)</span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill px-2 py-1">{{ number_format($c->difference, 2) }} (Shortage)</span>
                                                @endif
                                            </td>
                                            <td class="pe-3 text-secondary" style="font-size:13px; max-width:200px; white-space:normal; word-wrap:break-word;">
                                                {{ $c->remarks ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No closing history saved yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Live Difference Calculation
            $(document).on('input', '#actual_balance', function() {
                const expected = parseFloat('{{ $activeShift->expected_balance ?? 0 }}') || 0;
                const actual = parseFloat($(this).val()) || 0;
                const diff = actual - expected;
                
                $('#liveDiffVal').text((diff >= 0 ? '+' : '') + diff.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                
                const $indicator = $('#diffIndicator');
                if (diff === 0) {
                    $indicator.text('Balanced').removeClass().addClass('badge bg-success rounded-pill px-4 py-2 fs-6');
                } else if (diff > 0) {
                    $indicator.text('Excess').removeClass().addClass('badge bg-primary rounded-pill px-4 py-2 fs-6');
                } else {
                    $indicator.text('Shortage').removeClass().addClass('badge bg-danger rounded-pill px-4 py-2 fs-6');
                }
            });

            // Initialize Datatable for Closing Records at bottom
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().destroy();
            }
            $('.datanew').DataTable({
                "pageLength": 10,
                "order": [],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search closings..."
                },
                "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });
        });
    </script>
@endsection
