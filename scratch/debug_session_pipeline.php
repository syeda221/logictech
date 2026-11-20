<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$sessionId = 'K848c12GGrvmsM34fJJsrbVYyqIv2cYB5iRiEZxy';
$encrypter = $app['encrypter'];
$encryptedValue = $encrypter->encrypt($sessionId, false);
$cookieName = config('session.cookie');

$request = Illuminate\Http\Request::create('/quotations', 'GET', [], [$cookieName => $encryptedValue]);

// Run through EncryptCookies middleware:
$encMiddleware = new \App\Http\Middleware\EncryptCookies($encrypter);
$response = $encMiddleware->handle($request, function($req) use ($app, $cookieName) {
    echo "Cookie after EncryptCookies: " . $req->cookies->get($cookieName) . "\n";

    // Run through StartSession middleware:
    $sessionManager = $app['session'];
    $sessionMiddleware = new \Illuminate\Session\Middleware\StartSession($sessionManager);
    return $sessionMiddleware->handle($req, function($r) use ($app) {
        echo "Session ID in StartSession: " . $r->session()->getId() . "\n";
        echo "Auth check in StartSession: " . ($app['auth']->guard('web')->check() ? 'LOGGED IN (' . $app['auth']->id() . ')' : 'NOT LOGGED IN') . "\n";
        
        // Run Authenticate middleware:
        $authMiddleware = new \App\Http\Middleware\Authenticate($app['auth']);
        return $authMiddleware->handle($r, function($reqAuth) {
            echo "Authenticate middleware: PASSED!\n";
            return response('OK');
        });
    });
});

echo "Final Response Status: " . $response->getStatusCode() . "\n";
if ($response->isRedirection()) {
    echo "Final Response Location: " . $response->headers->get('Location') . "\n";
}
