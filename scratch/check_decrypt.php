<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$cookieName = config('session.cookie');
$sessionId = 'K848c12GGrvmsM34fJJsrbVYyqIv2cYB5iRiEZxy';
$encrypter = $app['encrypter'];
$encryptedValue = $encrypter->encrypt($sessionId, false);

try {
    $val = $encrypter->decrypt($encryptedValue, false);
    echo "Direct decrypt: $val\n";
} catch (\Throwable $e) {
    echo "Direct decrypt error: " . $e->getMessage() . "\n";
}

// Let's check decryptCookie in EncryptCookies
$encMiddleware = new \App\Http\Middleware\EncryptCookies($encrypter);
$ref = new ReflectionMethod($encMiddleware, 'decryptCookie');
$ref->setAccessible(true);
try {
    $val2 = $ref->invoke($encMiddleware, $cookieName, $encryptedValue);
    echo "decryptCookie: $val2\n";
} catch (\Throwable $e) {
    echo "decryptCookie error: " . $e->getMessage() . "\n";
}
