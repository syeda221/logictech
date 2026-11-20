<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::first();
foreach (['password', '12345678', 'admin', 'admin123', '123456', 'superadmin'] as $pw) {
    if (Illuminate\Support\Facades\Hash::check($pw, $u->password)) {
        echo "Found password for {$u->email}: $pw\n";
        break;
    }
}
