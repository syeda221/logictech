<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usertype = Auth::user()->usertype;
        $userId = Auth::id();

        if ($usertype == 'user') {
            return view('user_panel.dashboard', compact('userId'));
        } elseif ($usertype == 'admin') {
            // Counts
            $categoryCount = Auth::user()->can('categories.view') ? DB::table('categories')->count() : 0;
            $subcategoryCount = Auth::user()->can('subcategories.view') ? DB::table('subcategories')->count() : 0;
            $productCount = Auth::user()->can('products.view') ? DB::table('products')->count() : 0;
            $customerscount = Auth::user()->can('customers.view') ? DB::table('customers')->count() : 0;

            // Stats
            $totalPurchases = Auth::user()->can('purchases.view') ? DB::table('purchases')->sum('net_amount') : 0;
            $totalPurchaseReturns = Auth::user()->can('purchase.returns.view') ? DB::table('purchase_returns')->sum('net_amount') : 0;
            $totalSales = Auth::user()->can('sales.view') ? DB::table('sales')->sum('total_net') : 0;
            $totalSalesReturns = Auth::user()->can('sales.returns.view') ? DB::table('sale_returns')->sum('net_amount') : 0;

            // Financial Summary (Accounting Based)
            $financialSummary = [];
            if (Auth::user()->can('purchases.view') || Auth::user()->can('sales.view')) {
                try {
                    $balanceService = app(\App\Services\BalanceService::class);
                    $fromDate = request('from_date', now()->startOfMonth()->format('Y-m-d'));
                    $toDate = request('to_date', now()->endOfMonth()->format('Y-m-d'));
                    $financialSummary = $balanceService->getFinancialSummary($fromDate, $toDate);
                } catch (\Exception $e) {
                     \Log::error("Dashboard Financial Summary Error: " . $e->getMessage());
                }
            }

            // ===== SALES REPORT CHARTS =====
            $salesChartStats = ['daily' => ['series' => [], 'categories' => []]];
            if (Auth::user()->can('sales.view')) {
                // DAILY (last 7 days)
                $dailyLabels = collect(range(6, 0))->map(fn($i) => \Carbon\Carbon::today()->subDays($i)->format('Y-m-d'));
                $dailyData = $dailyLabels->map(function ($date) {
                    return DB::table('sales')
                        ->whereDate('created_at', $date)
                        ->sum('total_net');
                });

                // WEEKLY (This + Last 2 weeks)
                $weeklyLabels = ['This Week', 'Last Week', '2 Weeks Ago'];
                $weeklyData = collect([0, 1, 2])->map(function ($i) {
                    $start = \Carbon\Carbon::now()->startOfWeek()->subWeeks($i);
                    $end = $start->copy()->endOfWeek();
                    return DB::table('sales')
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('total_net');
                })->reverse()->values();

                // MONTHLY (Jan → Current month)
                $months = range(1, \Carbon\Carbon::now()->month);
                $monthLabels = collect($months)->map(fn($m) => \Carbon\Carbon::create()->month($m)->format('F'));
                $monthlyData = collect($months)->map(function ($month) {
                    return DB::table('sales')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', \Carbon\Carbon::now()->year)
                        ->sum('total_net');
                });

                $salesChartStats = [
                    'daily' => [
                        'categories' => $dailyLabels,
                        'series' => [['name' => 'Sales', 'data' => $dailyData]]
                    ],
                    'weekly' => [
                        'categories' => $weeklyLabels,
                        'series' => [['name' => 'Sales', 'data' => $weeklyData]]
                    ],
                    'monthly' => [
                        'categories' => $monthLabels,
                        'series' => [['name' => 'Sales', 'data' => $monthlyData]]
                    ]
                ];
            }

            // ===== PURCHASE CHARTS =====
            $purchaseChartStats = ['daily' => ['series' => [], 'categories' => []]];
            if (Auth::user()->can('purchases.view')) {
                // DAILY
                $purchaseDailyLabels = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i)->format('Y-m-d'));
                $purchaseDailySeries = [[
                    'name' => 'Purchases',
                    'data' => $purchaseDailyLabels->map(function ($date) {
                        return DB::table('purchases')
                            ->whereDate('created_at', $date)
                            ->sum('net_amount');
                    })
                ]];

                // WEEKLY
                $purchaseWeeklyLabels = ['This Week', 'Last Week', '2 Weeks Ago'];
                $purchaseWeeklySeries = [[
                    'name' => 'Purchases',
                    'data' => collect([0, 1, 2])->map(function ($i) {
                        $start = Carbon::now()->startOfWeek()->subWeeks($i);
                        $end = $start->copy()->endOfWeek();
                        return DB::table('purchases')
                            ->whereBetween('created_at', [$start, $end])
                            ->sum('net_amount');
                    })->reverse()->values()
                ]];

                // MONTHLY
                $months = range(1, Carbon::now()->month);
                $purchaseMonthLabels = collect($months)->map(fn($m) => Carbon::create()->month($m)->format('F'));
                $purchaseMonthlySeries = [[
                    'name' => 'Purchases',
                    'data' => collect($months)->map(function ($month) {
                        return DB::table('purchases')
                            ->whereMonth('created_at', $month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->sum('net_amount');
                    })
                ]];

                $purchaseChartStats = [
                    'daily' => [
                        'categories' => $purchaseDailyLabels,
                        'series' => $purchaseDailySeries
                    ],
                    'weekly' => [
                        'categories' => $purchaseWeeklyLabels,
                        'series' => $purchaseWeeklySeries
                    ],
                    'monthly' => [
                        'categories' => $purchaseMonthLabels,
                        'series' => $purchaseMonthlySeries
                    ]
                ];
            }

            // ===== PAYMENT IN / OUT STATS =====
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();

            // 1. Payment IN (Today)
            $v2ReceiptsMonth = DB::table('voucher_masters')->where('voucher_type', 'receipt')->whereBetween('date', [$todayStart, $todayEnd])->sum('total_amount');
            $v1ReceiptsMonth = DB::table('receipts_vouchers')->whereBetween('receipt_date', [$todayStart, $todayEnd])->sum('total_amount');
            $custPaymentsMonth = DB::table('customer_payments')->whereBetween('payment_date', [$todayStart, $todayEnd])->sum('amount');
            $paymentInMonth = $v2ReceiptsMonth + $v1ReceiptsMonth + $custPaymentsMonth;

            // 2. Payment IN (Overall)
            $v2ReceiptsAll = DB::table('voucher_masters')->where('voucher_type', 'receipt')->sum('total_amount');
            $v1ReceiptsAll = DB::table('receipts_vouchers')->sum('total_amount');
            $custPaymentsAll = DB::table('customer_payments')->sum('amount');
            $paymentInOverall = $v2ReceiptsAll + $v1ReceiptsAll + $custPaymentsAll;

            // 3. Payment OUT (Today)
            $v2PaymentsMonth = DB::table('voucher_masters')->whereIn('voucher_type', ['payment', 'expense'])->whereBetween('date', [$todayStart, $todayEnd])->sum('total_amount');
            $v1PaymentsMonth = DB::table('payment_vouchers')->whereBetween('receipt_date', [$todayStart, $todayEnd])->sum('total_amount');
            $v1ExpensesMonth = DB::table('expense_vouchers')->whereBetween('entry_date', [$todayStart, $todayEnd])->sum('total_amount');
            $vendorPaymentsMonth = DB::table('vendor_payments')->whereBetween('payment_date', [$todayStart, $todayEnd])->sum('amount');
            $paymentOutMonth = $v2PaymentsMonth + $v1PaymentsMonth + $v1ExpensesMonth + $vendorPaymentsMonth;

            // 4. Payment OUT (Overall)
            $v2PaymentsAll = DB::table('voucher_masters')->whereIn('voucher_type', ['payment', 'expense'])->sum('total_amount');
            $v1PaymentsAll = DB::table('payment_vouchers')->sum('total_amount');
            $v1ExpensesAll = DB::table('expense_vouchers')->sum('total_amount');
            $vendorPaymentsAll = DB::table('vendor_payments')->sum('amount');
            $paymentOutOverall = $v2PaymentsAll + $v1PaymentsAll + $v1ExpensesAll + $vendorPaymentsAll;

            // ===== TOP 10 PRODUCTS & CUSTOMERS (Needs Sales Permission) =====
            $topProducts = collect();
            $topCustomers = collect();

            if (Auth::user()->can('sales.view')) {
                $topProducts = DB::table('sale_items')
                    ->select('product_name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(total) as total_revenue'))
                    ->groupBy('product_name')
                    ->orderByDesc('total_qty')
                    ->limit(10)
                    ->get();

                $topCustomers = DB::table('sales')
                    ->join('customers', 'sales.customer_id', '=', 'customers.id')
                    ->select('customers.customer_name', DB::raw('COUNT(sales.id) as total_orders'), DB::raw('SUM(sales.total_net) as total_sales'))
                    ->groupBy('customers.id', 'customers.customer_name')
                    ->orderByDesc('total_sales')
                    ->limit(10)
                    ->get();
            }

            $cashAndBankAccounts = \App\Models\Account::whereHas('head', function ($query) {
                $query->whereIn('name', ['Cash', 'Bank']);
            })->where('status', 1)->get();

            $totalCashAndBankBalance = $cashAndBankAccounts->sum('current_balance');

            // ===== LOW STOCK PRODUCTS =====
            $lowStockProducts = collect();
            if (Auth::user()->can('products.view')) {
                $lowStockProducts = \App\Models\Product::withSum('warehouseStocks', 'total_pieces')
                    ->whereNotNull('alert_carton_quantity')
                    ->where('is_active', 1)
                    ->get()
                    ->filter(function ($p) {
                        $stock = (float) ($p->warehouse_stocks_sum_total_pieces ?? 0);
                        $ppb = $p->pieces_per_box > 0 ? $p->pieces_per_box : 1;
                        $cartons = floor($stock / $ppb);
                        $p->current_cartons = $cartons;
                        return $cartons < $p->alert_carton_quantity;
                    })
                    ->sortBy(function ($p) {
                        return $p->current_cartons - $p->alert_carton_quantity; // most critical (most negative) first
                    })
                    ->take(15)
                    ->values();
            }

            $startOfMonth = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
            $endOfMonth   = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59');

            $salesThisMonth = DB::table('sales')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->whereNotIn('sale_status', ['cancelled', 'returned'])
                ->sum('total_net');

            $purchasesThisMonth = DB::table('purchases')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('net_amount');

            $salesToday = DB::table('sales')
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->whereNotIn('sale_status', ['cancelled', 'returned'])
                ->sum('total_net');

            $purchasesToday = DB::table('purchases')
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->sum('net_amount');

            // Calculate real profit for current month (matching Profit & Loss Report)
            $saleItemsThisMonth = DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
                ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
                ->where('sales.sale_status', '!=', 'cancelled')
                ->select(
                    'sale_items.price',
                    'sale_items.color',
                    'sale_items.total_pieces',
                    'sale_items.total',
                    'sale_items.is_manual',
                    'sale_items.purchase_price as manual_purchase_price',
                    'sales.total_extradiscount',
                    'sales.total_bill_amount',
                    'products.size_mode',
                    'products.pieces_per_m2',
                    'products.purchase_price_per_m2',
                    'products.purchase_price_per_piece'
                )->get();

            $totalCostThisMonth = 0;
            $totalRevenueThisMonth = 0;
            foreach ($saleItemsThisMonth as $si) {
                $qty = (float)($si->total_pieces ?? 1);
                
                if ($si->is_manual) {
                    $purchPrice = (float) $si->manual_purchase_price;
                } else {
                    $purchPrice = 0;
                    if (!empty($si->color)) {
                        $decoded = base64_decode($si->color, true);
                        $json = $decoded ? json_decode($decoded, true) : json_decode($si->color, true);
                        if (is_array($json)) {
                            $purchPrice = (float)($json['purch_price'] ?? 0);
                        }
                    }

                    if ($purchPrice <= 0) {
                        if ($si->size_mode === 'by_size') {
                            $m2PerPiece = (float) ($si->pieces_per_m2 ?? 0);
                            $purchPerM2 = (float) ($si->purchase_price_per_m2 ?? 0);
                            $purchPrice = $m2PerPiece * $purchPerM2;
                        } else {
                            $purchPrice = (float) ($si->purchase_price_per_piece ?? 0);
                        }
                    }
                }

                $itemNet = (float) $si->total;
                if ($si->total_bill_amount > 0 && $si->total_extradiscount > 0) {
                    $proportion = $itemNet / (float) $si->total_bill_amount;
                    $itemNet -= ($si->total_extradiscount * $proportion);
                }

                $totalRevenueThisMonth += $itemNet;
                $totalCostThisMonth    += $purchPrice * $qty;
            }

            // Accurately deduct any returned items in current month
            $saleReturnsThisMonth = DB::table('sale_return_items')
                ->join('sale_returns', 'sale_returns.id', '=', 'sale_return_items.sale_return_id')
                ->leftJoin('products', 'products.id', '=', 'sale_return_items.product_id')
                ->whereBetween('sale_returns.created_at', [$startOfMonth, $endOfMonth])
                ->select(
                    'sale_return_items.qty',
                    'sale_return_items.line_total',
                    'sale_return_items.color',
                    'sale_return_items.is_manual',
                    'sale_return_items.purchase_price as manual_purchase_price',
                    'products.size_mode',
                    'products.pieces_per_m2',
                    'products.purchase_price_per_m2',
                    'products.purchase_price_per_piece'
                )->get();

            foreach ($saleReturnsThisMonth as $sr) {
                $qty = (float)($sr->qty ?? 1);
                
                if ($sr->is_manual) {
                    $purchPrice = (float) $sr->manual_purchase_price;
                } else {
                    $purchPrice = 0;
                    if (!empty($sr->color)) {
                        $decoded = base64_decode($sr->color, true);
                        $json = $decoded ? json_decode($decoded, true) : json_decode($sr->color, true);
                        if (is_array($json)) {
                            $purchPrice = (float)($json['purch_price'] ?? 0);
                        }
                    }

                    if ($purchPrice <= 0) {
                        if ($sr->size_mode === 'by_size') {
                            $m2PerPiece = (float) ($sr->pieces_per_m2 ?? 0);
                            $purchPerM2 = (float) ($sr->purchase_price_per_m2 ?? 0);
                            $purchPrice = $m2PerPiece * $purchPerM2;
                        } else {
                            $purchPrice = (float) ($sr->purchase_price_per_piece ?? 0);
                        }
                    }
                }

                $totalRevenueThisMonth -= (float) $sr->line_total;
                $totalCostThisMonth    -= $purchPrice * $qty;
            }
            $profitThisMonth = $totalRevenueThisMonth - $totalCostThisMonth;

            // Accurate Accounting Receivables & Payables in Single SQL Queries (Instant Execution)
            $totalReceivables = DB::table('journal_entries')
                ->where('party_type', \App\Models\Customer::class)
                ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as total')
                ->value('total') ?? 0;

            $apId = app(\App\Services\BalanceService::class)->getAccountsPayableId();
            $totalPayables = DB::table('journal_entries')
                ->where('party_type', \App\Models\Vendor::class)
                ->where('account_id', $apId)
                ->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as total')
                ->value('total') ?? 0;

            // Expenses Today
            $expensesTodayV1 = DB::table('expense_vouchers')->whereDate('entry_date', \Carbon\Carbon::today())->sum('total_amount');
            $expensesTodayV2 = DB::table('voucher_masters')->where('voucher_type', 'expense')->whereDate('date', \Carbon\Carbon::today())->sum('total_amount');
            $expensesToday = $expensesTodayV1 + $expensesTodayV2;

            // Total Stock Valuation in Single SQL Query
            $totalStockValue = DB::table('warehouse_stocks')
                ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                ->selectRaw('COALESCE(SUM(warehouse_stocks.total_pieces * (CASE WHEN products.size_mode = "by_size" THEN COALESCE(products.pieces_per_m2, 0) * COALESCE(products.purchase_price_per_m2, 0) ELSE COALESCE(products.purchase_price_per_piece, 0) END)), 0) as total')
                ->value('total') ?? 0;

            $vendorCount = DB::table('vendors')->count();
            $employeeCount = \Illuminate\Support\Facades\Schema::hasTable('hr_employees') ? DB::table('hr_employees')->count() : DB::table('users')->count();

            // Sales by Category (Current Month)
            $salesByCategory = DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->join('products', 'products.id', '=', 'sale_items.product_id')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
                ->where('sales.sale_status', '!=', 'cancelled')
                ->select(DB::raw('COALESCE(categories.name, "General") as category_name'), DB::raw('SUM(sale_items.total) as total_amount'))
                ->groupBy('category_name')
                ->orderByDesc('total_amount')
                ->limit(5)
                ->get();

            // Expenses This Month
            $expensesThisMonthV1 = DB::table('expense_vouchers')->whereBetween('entry_date', [substr($startOfMonth, 0, 10), substr($endOfMonth, 0, 10)])->sum('total_amount');
            $expensesThisMonthV2 = DB::table('voucher_masters')->where('voucher_type', 'expense')->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('total_amount');
            $expensesThisMonth = $expensesThisMonthV1 + $expensesThisMonthV2;

            $grossProfitThisMonth = $profitThisMonth;
            $netProfitThisMonth = $grossProfitThisMonth - $expensesThisMonth;

            // Expense Breakdown by type (column name is 'type', not 'expense_type')
            $expenseBreakdown = DB::table('expense_vouchers')
                ->select('type as name', DB::raw('SUM(total_amount) as total'))
                ->groupBy('type')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            // Recent Activities Feed
            $recentSales = DB::table('sales')->latest()->limit(3)->get()->map(function($s) {
                return [
                    'icon' => 'fa-receipt text-success',
                    'bg' => '#ecfdf5',
                    'title' => 'New Sale Invoice #' . $s->invoice_no,
                    'subtitle' => 'Sale Transaction',
                    'amount' => 'Rs ' . number_format($s->total_net, 0),
                    'time' => \Carbon\Carbon::parse($s->created_at)->diffForHumans()
                ];
            });

            $recentPurchases = DB::table('purchases')->latest()->limit(2)->get()->map(function($p) {
                return [
                    'icon' => 'fa-cart-shopping text-primary',
                    'bg' => '#eef2ff',
                    'title' => 'Purchase Bill #' . $p->invoice_no,
                    'subtitle' => 'Procurement',
                    'amount' => 'Rs ' . number_format($p->net_amount, 0),
                    'time' => \Carbon\Carbon::parse($p->created_at)->diffForHumans()
                ];
            });

            $recentActivities = $recentSales->concat($recentPurchases)->values();

            // ===== 6 DASHBOARD ITEMS GRID INITIAL DATA =====
            $pendingSalesData = $this->getPendingSalesData('recent');
            $pendingRepairsData = $this->getPendingRepairsData('recent');
            $rawLowStockData = $this->getRawLowStockData('critical');
            $receivablesGridData = $this->getReceivablesGridData('top');
            $payablesGridData = $this->getPayablesGridData('top');
            $recentActivitiesGridData = $this->getRecentActivitiesGridData('recent');

            return view('admin_panel.dashboard', compact(
                'categoryCount',
                'subcategoryCount',
                'productCount',
                'customerscount',
                'vendorCount',
                'employeeCount',
                'totalPurchases',
                'totalPurchaseReturns',
                'totalSales',
                'totalSalesReturns',
                'salesChartStats',
                'purchaseChartStats',
                'financialSummary',
                'paymentInMonth',
                'paymentInOverall',
                'paymentOutMonth',
                'paymentOutOverall',
                'topProducts',
                'topCustomers',
                'cashAndBankAccounts',
                'totalCashAndBankBalance',
                'lowStockProducts',
                'salesThisMonth',
                'purchasesThisMonth',
                'profitThisMonth',
                'totalRevenueThisMonth',
                'totalCostThisMonth',
                'totalReceivables',
                'totalPayables',
                'expensesToday',
                'expensesThisMonth',
                'grossProfitThisMonth',
                'netProfitThisMonth',
                'totalStockValue',
                'salesByCategory',
                'expenseBreakdown',
                'recentActivities',
                'pendingSalesData',
                'pendingRepairsData',
                'rawLowStockData',
                'receivablesGridData',
                'payablesGridData',
                'recentActivitiesGridData'
            ));
        } else {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
    }

    /**
     * AJAX endpoint to fetch dashboard grid card data based on selected filter
     */
    public function fetchDashboardGrid(Request $request)
    {
        $card = $request->card;
        $filter = $request->filter ?? 'recent';

        $data = match ($card) {
            'pending_sales' => $this->getPendingSalesData($filter),
            'pending_repairs' => $this->getPendingRepairsData($filter),
            'low_stock' => $this->getRawLowStockData($filter),
            'receivables' => $this->getReceivablesGridData($filter),
            'payables' => $this->getPayablesGridData($filter),
            'activities' => $this->getRecentActivitiesGridData($filter),
            default => collect(),
        };

        return response()->json([
            'status' => 'success',
            'card' => $card,
            'filter' => $filter,
            'data' => $data
        ]);
    }

    public function getPendingSalesData($filter = 'recent')
    {
        $query = DB::table('sales')
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->select(
                'sales.id',
                'sales.invoice_no',
                'sales.reference',
                'sales.sale_status',
                'sales.total_net',
                'sales.created_at',
                'customers.customer_name'
            );

        if ($filter == 'top') {
            $query->orderByDesc('sales.total_net');
        } elseif ($filter == 'delay') {
            $query->where('sales.created_at', '<=', Carbon::now()->subDays(3))
                  ->orderBy('sales.created_at', 'asc');
        } elseif ($filter == 'nearest') {
            $query->where('sales.created_at', '>=', Carbon::now()->subDays(7))
                  ->orderByDesc('sales.created_at');
        } else {
            $query->orderByDesc('sales.created_at');
        }

        $items = $query->limit(15)->get();

        return $items->map(function ($s, $idx) {
            $statusStr = strtolower($s->sale_status ?? '');
            $statusLabel = match ($statusStr) {
                'posted' => 'Posted',
                'completed' => 'Completed',
                'booked' => 'Booked',
                'draft' => 'Draft',
                'cancelled' => 'Cancelled',
                default => 'Pending'
            };

            $badgeClass = match ($statusStr) {
                'posted', 'completed' => 'bg-success-subtle text-success border border-success-subtle',
                'booked', 'draft', 'pending' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
                default => 'bg-primary-subtle text-primary border border-primary-subtle',
            };

            return [
                'sr' => $idx + 1,
                'client' => $s->customer_name ?: 'Walk-in Customer',
                'details' => 'Invoice #' . ($s->invoice_no ?: $s->id) . ($s->reference ? ' (' . $s->reference . ')' : ''),
                'status' => $statusLabel,
                'badge' => $badgeClass,
                'amount' => 'Rs ' . number_format($s->total_net ?? 0, 0),
                'date' => Carbon::parse($s->created_at)->diffForHumans()
            ];
        });
    }

    public function getPendingRepairsData($filter = 'recent')
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('repair_orders')) {
            return collect();
        }

        $query = DB::table('repair_orders')
            ->leftJoin('customers', 'customers.id', '=', 'repair_orders.customer_id')
            ->select(
                'repair_orders.*',
                'customers.customer_name as cust_rel_name'
            )
            ->whereNotIn('repair_orders.status', ['delivered', 'cancelled']);

        if ($filter == 'top') {
            $query->orderByDesc('repair_orders.total_charges');
        } elseif ($filter == 'delay') {
            $query->orderBy('repair_orders.received_date', 'asc');
        } elseif ($filter == 'nearest') {
            $query->whereNotNull('repair_orders.expected_delivery_date')
                  ->orderBy('repair_orders.expected_delivery_date', 'asc');
        } else {
            $query->orderByDesc('repair_orders.created_at');
        }

        $items = $query->limit(15)->get();

        return $items->map(function ($r, $idx) {
            $statusLabel = match($r->status ?? '') {
                'received' => 'Received',
                'diagnosing' => 'Diagnosing',
                'in_progress' => 'In Progress',
                'waiting_parts' => 'Waiting Parts',
                'completed' => 'Ready',
                default => ucfirst(str_replace('_', ' ', $r->status ?? 'pending')),
            };

            $badgeClass = match($r->status ?? '') {
                'completed' => 'bg-success-subtle text-success border border-success-subtle',
                'in_progress', 'diagnosing' => 'bg-info-subtle text-info border border-info-subtle',
                'waiting_parts' => 'bg-warning-subtle text-warning border border-warning-subtle',
                default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            };

            return [
                'sr' => $idx + 1,
                'client' => $r->customer_name ?: ($r->cust_rel_name ?: 'Walk-in Client'),
                'details' => ($r->repair_no ?: ('#' . $r->id)) . ' - ' . ($r->item_name ?: 'Repair Item'),
                'status' => $statusLabel,
                'badge' => $badgeClass,
                'amount' => 'Rs ' . number_format($r->total_charges ?: $r->estimated_cost ?: 0, 0),
                'date' => $r->expected_delivery_date ? Carbon::parse($r->expected_delivery_date)->format('d M Y') : 'Pending'
            ];
        });
    }

    public function getRawLowStockData($filter = 'critical')
    {
        $products = \App\Models\Product::withSum('warehouseStocks', 'total_pieces')
            ->where('is_active', 1)
            ->get();

        $mapped = $products->map(function ($p) {
            $stock = (float) ($p->warehouse_stocks_sum_total_pieces ?? 0);
            $ppb = $p->pieces_per_box > 0 ? $p->pieces_per_box : 1;
            $cartons = floor($stock / $ppb);
            $p->current_cartons = $cartons;
            $p->alert_qty = (float) ($p->alert_carton_quantity ?? 0);
            $p->deficit = $cartons - $p->alert_qty;
            return $p;
        });

        if ($filter == 'critical') {
            $filtered = $mapped->filter(fn($p) => $p->alert_qty > 0 && $p->current_cartons < $p->alert_qty)
                               ->sortBy('deficit');
        } elseif ($filter == 'low') {
            $filtered = $mapped->filter(fn($p) => $p->alert_qty > 0 && $p->current_cartons < $p->alert_qty)
                               ->sortBy('current_cartons');
        } elseif ($filter == 'out_of_stock') {
            $filtered = $mapped->filter(fn($p) => $p->current_cartons <= 0)
                               ->sortBy('current_cartons');
        } else {
            $filtered = $mapped->sortBy('current_cartons');
        }

        return $filtered->take(15)->values()->map(function ($p, $idx) {
            return [
                'sr' => $idx + 1,
                'product' => $p->item_name ?: ($p->product_name ?: 'Product Item'),
                'stock' => $p->current_cartons . ' ctns',
                'alert' => $p->alert_qty . ' ctns',
                'pieces' => number_format($p->warehouse_stocks_sum_total_pieces ?? 0) . ' pcs',
                'is_low' => $p->current_cartons < $p->alert_qty
            ];
        });
    }

    public function getReceivablesGridData($filter = 'top')
    {
        $custBalances = DB::table('journal_entries')
            ->where('party_type', \App\Models\Customer::class)
            ->selectRaw('party_id, COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->groupBy('party_id')
            ->pluck('balance', 'party_id');

        $customers = DB::table('customers')->get();
        $parties = [];

        foreach ($customers as $c) {
            $balance = (float) ($custBalances[$c->id] ?? 0);
            if ($balance > 0) {
                $parties[] = [
                    'id' => $c->id,
                    'code' => sprintf("C%04d", $c->id),
                    'party' => $c->customer_name,
                    'balance' => $balance,
                    'mobile' => $c->mobile ?? '-'
                ];
            }
        }

        $collection = collect($parties);

        if ($filter == 'top') {
            $collection = $collection->sortByDesc('balance');
        } elseif ($filter == 'recent') {
            $collection = $collection->sortByDesc('id');
        } elseif ($filter == 'delay') {
            $collection = $collection->sortByDesc('balance');
        }

        return $collection->take(15)->values()->map(function ($p, $idx) {
            return [
                'sr' => $idx + 1,
                'code' => $p['code'],
                'party' => $p['party'],
                'amount' => 'Rs ' . number_format($p['balance'], 2)
            ];
        });
    }

    public function getPayablesGridData($filter = 'top')
    {
        $apId = app(\App\Services\BalanceService::class)->getAccountsPayableId();
        $vendorBalances = DB::table('journal_entries')
            ->where('party_type', \App\Models\Vendor::class)
            ->where('account_id', $apId)
            ->selectRaw('party_id, COALESCE(SUM(credit) - SUM(debit), 0) as balance')
            ->groupBy('party_id')
            ->pluck('balance', 'party_id');

        $vendors = DB::table('vendors')->get();
        $parties = [];

        foreach ($vendors as $v) {
            $balance = (float) ($vendorBalances[$v->id] ?? 0);
            if ($balance > 0) {
                $parties[] = [
                    'id' => $v->id,
                    'code' => sprintf("V%04d", $v->id),
                    'party' => $v->name,
                    'balance' => $balance,
                    'mobile' => $v->phone ?? '-'
                ];
            }
        }

        $collection = collect($parties);

        if ($filter == 'top') {
            $collection = $collection->sortByDesc('balance');
        } elseif ($filter == 'recent') {
            $collection = $collection->sortByDesc('id');
        } elseif ($filter == 'delay') {
            $collection = $collection->sortByDesc('balance');
        }

        return $collection->take(15)->values()->map(function ($p, $idx) {
            return [
                'sr' => $idx + 1,
                'code' => $p['code'],
                'party' => $p['party'],
                'amount' => 'Rs ' . number_format($p['balance'], 2)
            ];
        });
    }

    public function getRecentActivitiesGridData($filter = 'recent')
    {
        $sales = collect();
        $purchases = collect();
        $vouchers = collect();

        if ($filter == 'recent' || $filter == 'sales') {
            $sales = DB::table('sales')->latest()->limit(10)->get()->map(function($s) {
                return [
                    'icon' => 'fa-receipt text-success',
                    'bg' => '#ecfdf5',
                    'title' => 'Sale #' . ($s->invoice_no ?: $s->id),
                    'subtitle' => 'Sale Transaction',
                    'amount' => 'Rs ' . number_format($s->total_net ?? 0, 0),
                    'time' => Carbon::parse($s->created_at)->diffForHumans(),
                    'created_at' => $s->created_at
                ];
            });
        }

        if ($filter == 'recent' || $filter == 'purchases') {
            $purchases = DB::table('purchases')->latest()->limit(10)->get()->map(function($p) {
                return [
                    'icon' => 'fa-cart-shopping text-primary',
                    'bg' => '#eef2ff',
                    'title' => 'Purchase #' . ($p->invoice_no ?: $p->id),
                    'subtitle' => 'Procurement',
                    'amount' => 'Rs ' . number_format($p->net_amount ?? 0, 0),
                    'time' => Carbon::parse($p->created_at)->diffForHumans(),
                    'created_at' => $p->created_at
                ];
            });
        }

        if ($filter == 'recent' || $filter == 'vouchers') {
            $vouchers = DB::table('voucher_masters')->latest()->limit(10)->get()->map(function($v) {
                return [
                    'icon' => 'fa-file-invoice-dollar text-warning',
                    'bg' => '#fffbeb',
                    'title' => 'Voucher #' . ($v->voucher_no ?: $v->id),
                    'subtitle' => ucfirst($v->voucher_type ?? 'Voucher'),
                    'amount' => 'Rs ' . number_format($v->total_amount ?? 0, 0),
                    'time' => Carbon::parse($v->created_at ?? $v->date)->diffForHumans(),
                    'created_at' => $v->created_at ?? $v->date
                ];
            });
        }

        $all = $sales->concat($purchases)->concat($vouchers)->sortByDesc('created_at')->take(15)->values();

        return $all->map(function ($act, $idx) {
            return [
                'sr' => $idx + 1,
                'party' => $act['title'],
                'subtitle' => $act['subtitle'],
                'amount' => $act['amount'],
                'time' => $act['time'],
                'icon' => $act['icon'],
                'bg' => $act['bg']
            ];
        });
    }
}
