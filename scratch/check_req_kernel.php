<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

// Create session
$session = $app['session']->driver();
$session->setId('test-session-id');
$session->start();
$session->put($app['auth']->guard()->getName(), $user->getAuthIdentifier());
$session->save();

$request = Illuminate\Http\Request::create('/quotations', 'GET');
$request->setLaravelSession($session);
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Run request through kernel
$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
if ($response->isRedirection()) {
    echo "Redirect target: " . $response->headers->get('Location') . "\n";
} else {
    echo "Response Length: " . strlen($response->getContent()) . "\n";
    if (strpos($response->getContent(), 'Quotations Management') !== false) {
        echo "SUCCESS: Quotations Management page rendered!\n";
    }
    if (strpos($response->getContent(), 'Sign in to Workspace') !== false) {
        echo "WARNING: Response contains 'Sign in to Workspace'!\n";
    }
}
$kernel->terminate($request, $response);
