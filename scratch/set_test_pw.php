<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
echo "User email: " . $user->email . "\n";
// Let's set a password for testing or check existing
$user->password = bcrypt('12345678');
$user->save();
echo "Password set to 12345678\n";
