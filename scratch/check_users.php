<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\User::all(['id', 'name', 'email']) as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Email: {$u->email}, ActiveEmployee: " . ($u->isEmployeeActive() ? 'YES' : 'NO') . "\n";
}
