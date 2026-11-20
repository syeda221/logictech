<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (['http://localhost:8002/sale', 'http://127.0.0.1:8002/sale'] as $url) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $app->instance('request', $request);
    $app['url']->setRequest($request);

    echo "Host: $url\n";
    echo "  route('quotations.index'): " . route('quotations.index') . "\n";
    echo "  url('quotations'): " . url('quotations') . "\n";
    echo "  route('sale.index'): " . route('sale.index') . "\n";
    echo "  url('sale'): " . url('sale') . "\n";
}
