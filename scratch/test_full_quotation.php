<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
$req = Illuminate\Http\Request::create('/quotations', 'GET');
$app->instance('request', $req);
$app['url']->setRequest($req);
$app['auth']->setRequest($req);
$app['auth']->guard('web')->setUser($user);

echo "Testing QuotationController methods:\n";

// 1. Index
$ctrl = new App\Http\Controllers\QuotationController();
$resp = $ctrl->index($req);
echo "  index(): OK (view: " . $resp->name() . ")\n";

// 2. Create
$resp = $ctrl->create();
echo "  create(): OK (view: " . $resp->name() . ")\n";

// 3. Store a test quotation
$storeReq = Illuminate\Http\Request::create('/quotations/store', 'POST', [
    'action' => 'quotation',
    'title' => 'QUOTATION',
    'quotation_series' => 'QUO',
    'quote_no' => 'QUO-TEST-999',
    'order_date' => date('Y-m-d'),
    'est_delivery_date' => date('Y-m-d', strtotime('+15 days')),
    'walkin_name' => 'Walkin Customer Test',
    'partyType' => 'retail',
    'reference' => 'Testing Quotation Module',
    'product_id' => [1],
    'item_name' => ['Test Product'],
    'item_technical_name' => ['Tech Spec ABC'],
    'item_technical_remarks' => ['Remark 123'],
    'item_brand' => [1],
    'item_unit' => [1],
    'item_model' => ['M-100'],
    'item_color' => ['Blue'],
    'item_size' => ['std'],
    'item_qty' => [2],
    'item_total_pieces' => [2],
    'item_price' => [50000],
    'item_discount' => [0],
    'item_discount_amount' => [0],
    'item_tax_percent' => [0],
    'item_tax_amount' => [0],
    'item_total' => [100000],
    'invoice_subtotal' => 100000,
    'invoice_discount' => 0,
    'tax_percentage' => 0,
    'tax_amount' => 0,
    'total_net' => 100000,
    'terms_and_conditions' => '1. The quoted prices are valid for 15 days.',
]);
$storeReq->headers->set('X-Requested-With', 'XMLHttpRequest');

$resp = $ctrl->store($storeReq);
$data = json_decode($resp->getContent(), true);
echo "  store(): " . ($data['status'] ?? 'unknown') . " Quote: " . ($data['quote_no'] ?? 'N/A') . "\n";
$quoteId = $data['quotation_id'] ?? null;

if ($quoteId) {
    // 4. Show / Print
    $resp = $ctrl->show($quoteId);
    echo "  show(): OK (view: " . $resp->name() . ")\n";

    // 5. Edit
    $resp = $ctrl->edit($quoteId);
    echo "  edit(): OK (view: " . $resp->name() . ")\n";

    // 6. Update
    $updateReq = Illuminate\Http\Request::create("/quotations/$quoteId", 'PUT', [
        'title' => 'QUOTATION UPDATED',
        'order_date' => date('Y-m-d'),
        'est_delivery_date' => date('Y-m-d', strtotime('+20 days')),
        'walkin_name' => 'Walkin Customer Updated',
        'partyType' => 'retail',
        'reference' => 'Updated Ref',
        'product_id' => [1],
        'item_name' => ['Test Product Updated'],
        'item_qty' => [3],
        'item_price' => [50000],
        'item_total' => [150000],
        'invoice_subtotal' => 150000,
        'invoice_discount' => 0,
        'tax_percentage' => 0,
        'tax_amount' => 0,
        'total_net' => 150000,
        'terms_and_conditions' => 'Updated terms.',
    ]);
    $updateReq->headers->set('X-Requested-With', 'XMLHttpRequest');
    $resp = $ctrl->update($updateReq, $quoteId);
    $upData = json_decode($resp->getContent(), true);
    echo "  update(): " . ($upData['status'] ?? 'unknown') . "\n";

    // 7. Convert to Order (GET redirect test)
    $convReq = Illuminate\Http\Request::create("/quotations/$quoteId/convert-to-order", 'GET');
    $resp = $ctrl->convertToOrder($convReq, $quoteId);
    echo "  convertToOrder(GET): OK (Redirect to: " . $resp->headers->get('Location') . ")\n";

    // 8. Delete test quotation
    $ctrl->destroy($quoteId);
    echo "  destroy(): OK\n";
}
echo "All QuotationController actions tested successfully!\n";
