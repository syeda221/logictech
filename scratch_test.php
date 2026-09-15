<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sales = App\Models\Sale::with('items')->take(5)->get();
echo "--- SALES SUMMARY CHECK ---\n";
foreach ($sales as $s) {
    $inline_val = $s->items ? $s->items->sum('discount_amount') : 0;
    $bill_amount = $s->total_bill_amount > 0 ? $s->total_bill_amount : (float) $s->per_total;
    $gross_subtotal = $bill_amount + $inline_val;
    $salePaid = (float)($s->cash ?? 0) + (float)($s->card ?? 0);
    $remainingBalance = max(0, round($s->total_net - $salePaid, 2));

    echo "Bill #{$s->id} ({$s->invoice_no}):\n";
    echo "  Gross: Rs. " . number_format($gross_subtotal, 2) . "\n";
    echo "  Item Disc: Rs. " . number_format($inline_val, 2) . "\n";
    echo "  Add. Disc: Rs. " . number_format($s->total_extradiscount, 2) . "\n";
    echo "  Net Total: Rs. " . number_format($s->total_net, 2) . "\n";
    echo "  Payment Received: Rs. " . number_format($salePaid, 2) . "\n";
    echo "  Balance: Rs. " . number_format($remainingBalance, 2) . "\n\n";
}
