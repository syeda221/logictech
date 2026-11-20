<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();

function testRoute($app, $user, $uri) {
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $app->instance('request', $request);
    
    // Authenticate
    $app['auth']->guard()->setUser($user);

    $router = $app['router'];
    $response = $router->dispatch($request);
    
    echo "URI: $uri\n";
    echo "  Status: " . $response->getStatusCode() . "\n";
    if ($response->isRedirection()) {
        echo "  Redirect: " . $response->headers->get('Location') . "\n";
    } else {
        echo "  Length: " . strlen($response->getContent()) . "\n";
        if (strpos($response->getContent(), 'Quotations Management') !== false) {
            echo "  -> OK: Found Quotations Management in HTML!\n";
        }
        if (strpos($response->getContent(), 'New Quotation') !== false) {
            echo "  -> OK: Found New Quotation in HTML!\n";
        }
    }
}

testRoute($app, $user, '/sale');
testRoute($app, $user, '/quotations');
testRoute($app, $user, '/quotations/create');
