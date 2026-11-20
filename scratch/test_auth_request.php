<?php
$cookieFile = __DIR__ . '/test_cookies.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

// 1. GET /software to grab CSRF token
$ch = curl_init('http://127.0.0.1:8002/software');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$html = curl_exec($ch);

preg_match('/name="_token" value="([^"]+)"/', $html, $matches);
$token = $matches[1] ?? null;
echo "CSRF Token: " . ($token ? 'Found' : 'Missing') . "\n";

// 2. POST /login or /software
// Let's check user password in users table
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
echo "Logging in as: " . $user->email . "\n";

// Let's set password to something known or login via session directly
$sessionId = 'curl_test_session_' . time();
$encrypter = app('encrypter');

// In Laravel file session, let's inject a session file directly!
$sessionHandler = app('session')->driver();
$sessionHandler->setId($sessionId);
$sessionHandler->start();
$sessionHandler->put(app('auth')->guard()->getName(), $user->id);
$sessionHandler->save();

echo "Injected session ID: $sessionId\n";

// Now test accessing /quotations and /sale with this session cookie
function testWithSession($url, $sessionId) {
    $sessionCookieName = config('session.cookie');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIE, "$sessionCookieName=$sessionId");
    $resp = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($resp, 0, $headerSize);
    $body = substr($resp, $headerSize);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "Testing $url:\n";
    echo "  HTTP Code: $httpCode\n";
    foreach (explode("\r\n", $headers) as $h) {
        if (stripos($h, 'Location:') !== false || stripos($h, 'Set-Cookie:') !== false) {
            echo "  $h\n";
        }
    }
    echo "  Body length: " . strlen($body) . "\n";
    if (strpos($body, 'Quotations Management') !== false) {
        echo "  [FOUND] Quotations Management page rendered!\n";
    }
    if (strpos($body, 'Sign in to Workspace') !== false) {
        echo "  [FOUND] Sign in to Workspace rendered!\n";
    }
}

// But in Laravel, session cookie is encrypted!
$encryptedSessionId = $encrypter->encrypt($sessionId, false);
testWithSession('http://127.0.0.1:8002/quotations', $encryptedSessionId);
testWithSession('http://127.0.0.1:8002/sale', $encryptedSessionId);
