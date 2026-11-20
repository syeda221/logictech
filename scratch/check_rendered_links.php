<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();

$req = Illuminate\Http\Request::create('http://127.0.0.1:8002/sale', 'GET');
$app->instance('request', $req);
$app['url']->setRequest($req);
$app['auth']->setRequest($req);
$app['auth']->guard()->setUser($user);

$view = view('admin_panel.sale.index', [
    'sales' => collect([]),
    'customers' => collect([]),
    'stats' => [],
    'pendingApprovalCount' => 0,
    'draftCount' => 0,
    'totalSalesCount' => 0,
    'totalSalesAmount' => 0,
    'totalExtraDiscount' => 0,
    'confirmedCount' => 0,
    'cancelledCount' => 0,
    'users' => collect([]),
    'branches' => collect([]),
    'salesOfficers' => collect([]),
])->render();

preg_match_all('/<a[^>]+href="([^"]*quotation[^"]*)"[^>]*>/i', $view, $matches);
echo "Links with quotation in href:\n";
print_r($matches[1]);
