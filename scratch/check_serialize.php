<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$cookieName = config('session.cookie');
$ref = new ReflectionMethod(\App\Http\Middleware\EncryptCookies::class, 'serialized');
$ref->setAccessible(true);
$encMiddleware = new \App\Http\Middleware\EncryptCookies($app['encrypter']);
$isSerialized = $ref->invoke($encMiddleware, $cookieName);
var_dump($isSerialized);
