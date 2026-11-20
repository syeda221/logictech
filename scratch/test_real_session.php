<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$encrypter = app('encrypter');
$sessionId = 'A7UfcxKRnWQsH8tZKA8hXab2iMGHL1mlP64CycWg';
// Laravel's CookieJar encrypts with serialize = true!
$encryptedCookie = urlencode($encrypter->encrypt($sessionId, true));
$cookieName = config('session.cookie');

function testUrl($url, $cookieName, $encryptedCookie) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIE, "$cookieName=$encryptedCookie");
    $resp = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($resp, 0, $headerSize);
    $body = substr($resp, $headerSize);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "=== Request: $url ===\n";
    echo "HTTP Status: $httpCode\n";
    foreach (explode("\r\n", $headers) as $h) {
        if (stripos($h, 'Location:') !== false) {
            echo "  $h\n";
        }
    }
    echo "Body length: " . strlen($body) . "\n";
    if (strpos($body, 'Quotations Management') !== false) {
        echo "  -> Found 'Quotations Management' in response!\n";
    }
    if (strpos($body, 'Sales Management') !== false) {
        echo "  -> Found 'Sales Management' in response!\n";
    }
    if (strpos($body, 'Sign in to Workspace') !== false) {
        echo "  -> Found 'Sign in to Workspace' in response!\n";
    }
    echo "\n";
}

testUrl('http://127.0.0.1:8002/sale', $cookieName, $encryptedCookie);
testUrl('http://127.0.0.1:8002/quotations', $cookieName, $encryptedCookie);
testUrl('http://127.0.0.1:8002/quotations/create', $cookieName, $encryptedCookie);
