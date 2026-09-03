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

        .premium-card {
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03) !important;
            background-color: #ffffff;
            margin-top: 15px;
            overflow: hidden;
        }
        
        .filter-panel {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            padding: 24px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
            transition: box-shadow 0.3s ease;
        }
        
        .filter-panel:hover {
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.05) !important;
        }
        
        .filter-panel label {
            font-size: 11px;
            font-weight: 800 !important;
            color: #64748b !important;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        
        .filter-panel .form-control,
        .filter-panel .form-select {
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #f8fafc !important;
            padding: 10px 14px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: auto !important;
        }
        
        .filter-panel .form-control:focus,
        .filter-panel .form-select:focus {
            background-color: #ffffff !important;
            border-color: #0f172a !important;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.1) !important;
        }
        
        .btn-premium-primary {
            background: linear-gradient(135deg, #1e293b, #000000) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            height: 44px !important;
            padding: 0 24px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-premium-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3) !important;
        }
        
        .btn-premium-secondary {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            height: 44px !important;
            padding: 0 24px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-premium-secondary:hover {
            background-color: #0f172a !important;
            color: #ffffff !important;
            border-color: #0f172a !important;
            transform: translateY(-1px);
        }

        .table-responsive {
            border-radius: 12px !important;
            box-shadow: 0 0 0 1px #e2e8f0;
        }

        .premium-table {
            border: none !important;
            margin-bottom: 0 !important;
        }
        
        .premium-table thead th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.8px;
            border-bottom: none !important;
            border-right: none !important;
            border-left: none !important;
            padding: 18px 16px !important;
        }
        
        .premium-table tbody td {
            border-bottom: 1px solid #f1f5f9 !important;
            border-right: none !important;
            border-left: none !important;
            padding: 16px !important;
            font-size: 13.5px !important;
            color: #334155 !important;
            background-color: #ffffff;
            transition: background-color 0.2s;
        }
        
        .premium-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }
        
        .summary-card {
            border-radius: 20px;
            padding: 24px;
            color: #fff;
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1;
            border: 1px solid rgba(255,255,255,0.15);
        }
        
        .summary-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
            z-index: -1;
        }
        
        .summary-card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15), 0 10px 10px -5px rgba(0,0,0,0.1);
        }

        .summary-card.total-in { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
        .summary-card.total-out { background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%); }
        .summary-card.net-balance { background: linear-gradient(135deg, #1e293b 0%, #000000 100%); }
        .summary-card.cash-in-hand { background: linear-gradient(135deg, #d97706 0%, #fbbf24 100%); }

        .summary-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; margin-bottom: 6px; }
        .summary-value { font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
        
        .summary-icon { 
            font-size: 50px; 
            opacity: 0.15; 
            position: absolute; 
            right: -10px; 
            bottom: -15px; 
            transform: rotate(-15deg); 
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); 
        }

        .summary-card:hover .summary-icon {
            transform: rotate(0deg) scale(1.1);
            opacity: 0.25;
        }

        .page-title-box h4 { font-size: 22px; letter-spacing: -0.5px; color: #0f172a !important; }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                <div class="d-flex justify-content-between align-items-center mb-4 page-title-box">
                    <div>
                        <h4 class="fw-bolder mb-1 text-dark">Cashbook History & Ledger</h4>
                        <p class="text-secondary mb-0" style="font-size: 14px;">Detailed transaction log of all cash and bank accounts</p>
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="row mb-4" id="summaryCards">
                    <div class="col-md-3">
                        <div class="summary-card total-in mb-3 mb-md-0">
                            <div>
                                <div class="summary-title">Total In (Receipts)</div>
                                <div class="summary-value" id="valTotalIn">{{ number_format($totalIn, 2) }}</div>
                            </div>
                            <i class="fas fa-arrow-down summary-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-card total-out mb-3 mb-md-0">
                            <div>
                                <div class="summary-title">Total Out (Payments)</div>
                                <div class="summary-value" id="valTotalOut">{{ number_format($totalOut, 2) }}</div>
                            </div>
                            <i class="fas fa-arrow-up summary-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-card net-balance mb-3 mb-md-0">
                            <div>
                                <div class="summary-title">Net Balance</div>
                                <div class="summary-value" id="valNetBalance">{{ number_format($netBalance, 2) }}</div>
                            </div>
                            <i class="fas fa-balance-scale summary-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-card cash-in-hand mb-3 mb-md-0">
                            <div>
                                <div class="summary-title">Total Cash in Hand</div>
                                <div class="summary-value" id="valCashInHand">{{ number_format($cashInHand, 2) }}</div>
                            </div>
                            <i class="fas fa-wallet summary-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="card premium-card">
                    <div class="card-body p-4">
                        {{-- AJAX Filter Panel --}}
                        <div class="card filter-panel mb-4">
                            <div class="card-body p-0">
                                <form id="filterForm" class="row g-3 align-items-end" autocomplete="off">
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">Shift / Day Closing</label>
                                        <select class="form-select" name="day_closing_id" id="filter_day_closing_id">
                                            <option value="">All Shifts / Day Closings</option>
                                            @foreach ($closings as $c)
                                                <option value="{{ $c->id }}">
                                                    Shift: {{ $c->opened_at->format('d/m/Y h:i A') }} to 
                                                    {{ $c->closed_at ? $c->closed_at->format('d/m/Y h:i A') : 'Active (Open)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 border-start ps-3" id="periodFilterCol">
                                        <label class="form-label mb-1">Period</label>
                                        <select class="form-select" name="period" id="filter_period">
                                            <option value="all">All Time</option>
                                            <option value="day" selected>Today</option>
                                            <option value="week">This Week</option>
                                            <option value="month">This Month</option>
                                            <option value="year">This Year</option>
                                            <option value="custom">Custom Range</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 custom-date-group" style="display: none;">
                                        <label class="form-label mb-1">From Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="from_date" id="filter_from_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-md-2 custom-date-group" style="display: none;">
                                        <label class="form-label mb-1">To Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="to_date" id="filter_to_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label mb-1">Account</label>
                                        <select class="form-select" name="account_id" id="filter_account_id">
                                            <option value="">All Cash/Bank Accounts</option>
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label mb-1">Type</label>
                                        <select class="form-select" name="type" id="filter_type">
                                            <option value="">All (In/Out)</option>
                                            <option value="in">Money In (Debit)</option>
                                            <option value="out">Money Out (Credit)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex justify-content-end gap-2 mt-3 mb-1 ms-auto">
                                        <button type="button" class="btn btn-premium-secondary" id="btnReset">
                                            <i class="fas fa-undo me-1"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-premium-primary" id="btnSearch">
                                            <i class="fas fa-search me-1"></i>Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="checkbook-table" class="table table-hover align-middle datanew premium-table" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 rounded-start text-secondary fw-semibold text-uppercase small">Date</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Description</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Account</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Source</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">In (Debit)</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Out (Credit)</th>
                                        <th class="py-3 pe-3 rounded-end text-secondary fw-semibold text-uppercase small text-end">Running Bal.</th>
                                    </tr>
                                </thead>
                                <tbody id="checkbookTableBody">
                                    @include('admin_panel.checkbook.partials.table_body')
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
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Toggle custom date range
            $('#filter_period').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('.custom-date-group').show();
                } else {
                    $('.custom-date-group').hide();
                    $('#filter_from_date').val('');
                    $('#filter_to_date').val('');
                }
            });

            // Toggle shift closing overrides period filter
            $('#filter_day_closing_id').on('change', function() {
                if ($(this).val() !== '') {
                    // Reset and disable period and custom dates
                    $('#filter_period').val('all').trigger('change').prop('disabled', true);
                    $('#periodFilterCol').css('opacity', '0.5');
                } else {
                    // Re-enable period filter
                    $('#filter_period').prop('disabled', false);
                    $('#periodFilterCol').css('opacity', '1');
                }
            });

            // Initialize Datepicker
            flatpickr(".datepicker-custom", {
                dateFormat: "Y-m-d",
                allowInput: true
            });

            // Function to initialize DataTable
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('.datanew')) {
                    $('.datanew').DataTable().destroy();
                }
                $('.datanew').DataTable({
                    "pageLength": 25,
                    "order": [],
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search transactions..."
                    },
                    "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                });
            }

            // Initial call
            initDataTable();

            // Submit form via AJAX
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSearch');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Searching...');

                // Temporarily enable period to serialize its value (in case it is disabled)
                $('#filter_period').prop('disabled', false);
                let formData = $(this).serialize();
                // Restore disabled state if shift is selected
                if ($('#filter_day_closing_id').val() !== '') {
                    $('#filter_period').prop('disabled', true);
                }

                $.ajax({
                    url: '{{ route("checkbook.transactions") }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        $btn.prop('disabled', false).html(origHtml);
                        
                        // Update Summaries
                        if(response.summary) {
                            $('#valTotalIn').text(response.summary.totalIn);
                            $('#valTotalOut').text(response.summary.totalOut);
                            $('#valNetBalance').text(response.summary.netBalance);
                            $('#valCashInHand').text(response.summary.cashInHand);
                        }

                        // Update Table Data
                        if ($.fn.DataTable.isDataTable('.datanew')) {
                            $('.datanew').DataTable().destroy();
                        }
                        
                        $('#checkbookTableBody').html(response.html);
                        initDataTable();
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).html(origHtml);
                        Swal.fire('Error', 'Failed to retrieve checkbook history data.', 'error');
                    }
                });
            });

            // Reset form
            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                $('#filter_day_closing_id').trigger('change');
                $('#filter_period').val('day').trigger('change');
                $('#filterForm').trigger('submit');
            });
        });
    </script>
@endsection
