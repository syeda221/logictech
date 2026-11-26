<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TechnicalDocumentOrderWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithPermissions(array $permissionNames): User
    {
        $user = User::factory()->create();
        foreach ($permissionNames as $name) {
            $perm = Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $user->givePermissionTo($perm);
        }
        return $user;
    }

    private function createTestCustomer(): Customer
    {
        return Customer::create([
            'customer_id' => 'CUST-TST-' . rand(1000, 9999),
            'customer_name' => 'Regal Cables Test Customer',
            'mobile' => '03361500082',
            'address' => 'Lahore Daroghawala',
            'customer_type' => 'Main Customer',
            'opening_balance' => 0,
            'previous_balance' => 0,
            'balance_range' => 0,
            'status' => 'active',
        ]);
    }

    private function getOrCreateTestProduct(): Product
    {
        $product = Product::where('is_active', true)->first();
        if ($product) {
            return $product;
        }

        return Product::create([
            'item_code' => 'ITEM-TST-' . rand(100, 999),
            'item_name' => 'Induction Melting Furnace For Copper Melting',
            'size_mode' => 'by_pieces',
            'pieces_per_box' => 1,
            'sale_price_per_piece' => 150000,
            'retail_price' => 150000,
            'is_active' => true,
            'item_type' => 'finish_goods',
        ]);
    }

    public function test_new_pending_order_exposes_technical_sheet_with_blank_delivery_fields_and_completes_on_delivery(): void
    {
        $user = $this->createUserWithPermissions([
            'sales.view',
            'sales.create',
            'sales.edit',
            'bookings.view',
            'bookings.edit',
        ]);
        $customer = $this->createTestCustomer();
        $product = $this->getOrCreateTestProduct();

        // 1. Create a new pending order via POST /sales/store (AJAX Book Order flow)
        $invoiceNo = 'TST-ORD-' . rand(10000, 99999);
        $storeResp = $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->post('/sales/store', [
                'action' => 'booking',
                'is_walkin' => '0',
                'customer' => $customer->id,
                'Invoice_no' => $invoiceNo,
                'order_status' => 'pending',
                'estimated_delivery_date' => now()->addDays(10)->format('Y-m-d'),
                'delivery_date' => '',
                'reference' => 'New Order Process Test',
                'product_id' => [$product->id],
                'qty' => [1],
                'total_pieces' => [1],
                'price_per_piece' => [150000],
                'item_disc' => [0],
                'discount_type' => ['percent'],
                'model' => [''],
                'serial_no' => [''],
                'color' => [base64_encode(json_encode(['name' => $product->item_name, 'color' => '-']))],
                'technical_name' => [''],
                'technical_specs' => [''],
                'technical_remarks' => [''],
                'total_extra_cost' => 0,
            ]);

        $this->flushHeaders();

        $storeResp->assertStatus(200)
            ->assertJson(['ok' => true]);

        $saleId = $storeResp->json('booking_id');
        $this->assertNotEmpty($saleId);
        $this->assertSame(route('sales.technical_doc', $saleId), $storeResp->json('technical_doc_url'));

        $sale = Sale::with('items')->findOrFail($saleId);
        $this->assertSame('booked', $sale->sale_status);
        $this->assertSame('pending', $sale->order_status);
        $this->assertNull($sale->delivery_date);
        $saleItem = $sale->items->first();
        $this->assertNotNull($saleItem);

        // 2. Sales index and Bookings index must show Technical Document link while order is pending
        $saleIndexResp = $this->actingAs($user)->get(route('sale.index'));
        $saleIndexResp->assertStatus(200);
        $saleIndexResp->assertSee(route('sales.technical_doc', $sale->id), false);

        $bookingIndexResp = $this->actingAs($user)->get(route('bookings.index'));
        $bookingIndexResp->assertStatus(200);
        $bookingIndexResp->assertSee(route('sales.technical_doc', $sale->id), false);

        // 3. Open Technical Sheet while order is PENDING
        $pendingDocResp = $this->actingAs($user)->get(route('sales.technical_doc', $sale->id));
        $pendingDocResp->assertStatus(200);
        $pendingDocResp->assertSee(route('sales.technical_doc.download', $sale->id), false);
        $pendingDocResp->assertSee('Regal Cables Test Customer');
        $pendingDocResp->assertSee('03361500082');
        $pendingDocResp->assertSee('Lahore Daroghawala');
        $pendingDocResp->assertSee('pending');
        $pendingDocResp->assertSee($invoiceNo);
        $pendingDocResp->assertSee('class="manual-write-line"', false);
        $pendingDocResp->assertSee('class="spec-content-box spec-blank-box', false);
        $pendingDocResp->assertDontSee('Self Collection');
        $pendingDocResp->assertDontSee('Standard factory specification parameters apply.');
        $pendingDocResp->assertDontSee('eyJuYW1l'); // No raw base64 output

        // 3b. Download Technical Sheet PDF while PENDING
        $pendingPdfResp = $this->actingAs($user)->get(route('sales.technical_doc.download', $sale->id));
        $pendingPdfResp->assertStatus(200);
        $pendingPdfResp->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pendingPdfResp->getContent());

        // 4. Transition to READY: still accessible and keeps delivery-time fields blank
        $readyResp = $this->actingAs($user)->post(route('sales.update_order_status', $sale->id), [
            'order_status' => 'ready',
        ]);
        $readyResp->assertStatus(200)->assertJson(['success' => true, 'order_status' => 'ready']);

        $readyDocResp = $this->actingAs($user)->get(route('sales.technical_doc', $sale->id));
        $readyDocResp->assertStatus(200);
        $readyDocResp->assertSee('ready');
        $readyDocResp->assertSee('class="manual-write-line"', false);
        $readyDocResp->assertDontSee('Self Collection');

        // 5. Edit view renders cleanly and exposes Technical Sheet button
        $editResp = $this->actingAs($user)->get(route('sales.edit', $sale->id));
        $editResp->assertStatus(200);
        $editResp->assertSee(route('sales.technical_doc', $sale->id), false);

        // 6. Confirm booking and mark Delivered with full specs via Delivery Prompt endpoint
        $sale->sale_status = 'posted';
        $sale->save();

        $deliverResp = $this->actingAs($user)->post(route('sales.save_delivery_details', $sale->id), [
            'delivery_date' => '2026-11-26',
            'delivery_source' => 'Self Collection',
            'delivery_remarks' => 'Regal cable gari',
            'items' => [
                $saleItem->id => [
                    'technical_name' => 'Induction Melting Furnace For Copper Melting',
                    'technical_specs' => 'LTZ Model : New Coil : Tilting System',
                    'technical_remarks' => 'Full load tested & QC verified',
                    'model' => 'LTZMODEL',
                    'serial_no' => 'SN-2026-00991',
                    'specs' => '3-Phase 440V',
                ],
            ],
        ]);

        $deliverResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order_status' => 'delivered',
                'technical_doc_url' => route('sales.technical_doc', $sale->id),
            ]);

        // 7. Technical Sheet when DELIVERED now shows all entered values and NO blank placeholders
        $deliveredDocResp = $this->actingAs($user)->get(route('sales.technical_doc', $sale->id));
        $deliveredDocResp->assertStatus(200);
        $deliveredDocResp->assertSee('delivered');
        $deliveredDocResp->assertSee('LTZMODEL');
        $deliveredDocResp->assertSee('SN-2026-00991');
        $deliveredDocResp->assertSee('26-Nov-2026');
        $deliveredDocResp->assertSee('Self Collection');
        $deliveredDocResp->assertSee('Regal cable gari');
        $deliveredDocResp->assertSee('LTZ Model : New Coil : Tilting System');
        $deliveredDocResp->assertSee('Full load tested &amp; QC verified', false);
        $deliveredDocResp->assertDontSee('class="manual-write-line"', false);
        $deliveredDocResp->assertDontSee('class="spec-content-box spec-blank-box', false);

        // 7b. Download Technical Sheet PDF after DELIVERED
        $deliveredPdfResp = $this->actingAs($user)->get(route('sales.technical_doc.download', $sale->id));
        $deliveredPdfResp->assertStatus(200);
        $deliveredPdfResp->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $deliveredPdfResp->getContent());
    }

    public function test_quotation_conversion_leaves_delivery_date_blank_until_delivered_and_allows_bookings_view_user(): void
    {
        $bookingsOnlyUser = $this->createUserWithPermissions(['bookings.view']);
        $customer = $this->createTestCustomer();
        $product = $this->getOrCreateTestProduct();

        $quotation = Quotation::create([
            'quote_no' => 'QT-TST-' . rand(1000, 9999),
            'customer_id' => $customer->id,
            'quotation_date' => now()->format('Y-m-d'),
            'estimated_delivery_date' => now()->addDays(14)->format('Y-m-d'),
            'total_bill_amount' => 100000,
            'total_discount' => 0,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'total_net' => 100000,
            'total_items' => 1,
            'status' => 'sent',
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $product->id,
            'product_name' => $product->item_name,
            'qty' => 1,
            'total_pieces' => 1,
            'price' => 100000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'total' => 100000,
        ]);

        $convResp = $this->actingAs($bookingsOnlyUser)
            ->post(route('quotations.convert_to_order', $quotation->id));
        $convResp->assertRedirect(route('sale.index'));
        $convResp->assertSessionHas('technical_doc_url');

        $quotation->refresh();
        $convertedSale = Sale::findOrFail($quotation->converted_sale_id);
        $this->assertSame('pending', $convertedSale->order_status);
        $this->assertNull($convertedSale->delivery_date);

        // Bookings-only user can open technical sheet
        $docResp = $this->actingAs($bookingsOnlyUser)->get(route('sales.technical_doc', $convertedSale->id));
        $docResp->assertStatus(200);
        $docResp->assertSee('pending');
        $docResp->assertSee('class="manual-write-line"', false);
    }

    public function test_sale_create_page_renders_and_delivery_details_defaults_to_today_for_pending_order(): void
    {
        $user = $this->createUserWithPermissions(['sales.view', 'sales.create', 'sales.edit']);

        $createResp = $this->actingAs($user)->get(route('sale.add'));
        $createResp->assertStatus(200);

        $customer = $this->createTestCustomer();
        $product = $this->getOrCreateTestProduct();

        // Simulate legacy pending order that accidentally had delivery_date copied from estimated_delivery_date
        $legacyPending = Sale::create([
            'customer_id' => $customer->id,
            'invoice_no' => 'TST-LEG-' . rand(1000, 9999),
            'sale_status' => 'booked',
            'order_status' => 'pending',
            'is_booking' => 1,
            'estimated_delivery_date' => '2026-12-30',
            'delivery_date' => '2026-12-30',
            'total_bill_amount' => 1000,
            'total_net' => 1000,
            'total_items' => 1,
        ]);

        SaleItem::create([
            'sale_id' => $legacyPending->id,
            'product_id' => $product->id,
            'product_name' => $product->item_name,
            'qty' => 1,
            'total_pieces' => 1,
            'price' => 1000,
            'total' => 1000,
        ]);

        // Technical doc still keeps Delivery Date blank while pending
        $docResp = $this->actingAs($user)->get(route('sales.technical_doc', $legacyPending->id));
        $docResp->assertStatus(200);
        $docResp->assertDontSee('30-Dec-2026');
        $docResp->assertSee('class="manual-write-line"', false);

        // getDeliveryDetails defaults delivery_date to today when not yet delivered
        $detailsResp = $this->actingAs($user)->get(route('sales.delivery_details', $legacyPending->id));
        $detailsResp->assertStatus(200)
            ->assertJsonPath('sale.delivery_date', date('Y-m-d'));
    }

    public function test_walkin_multi_item_and_manual_item_pending_technical_sheet_renders_safely(): void
    {
        $user = $this->createUserWithPermissions(['sales.view', 'sales.create']);
        $product = $this->getOrCreateTestProduct();

        $sale = Sale::create([
            'customer_id' => null,
            'walkin_name' => 'Walk-in Test Client',
            'invoice_no' => 'TST-WLK-' . rand(1000, 9999),
            'sale_status' => 'booked',
            'order_status' => 'pending',
            'is_booking' => 1,
            'total_bill_amount' => 50000,
            'total_net' => 50000,
            'total_items' => 3,
        ]);

        // Item 1: Product with pre-filled technical_specs, empty technical_remarks
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->item_name,
            'technical_name' => 'Custom Induction Heater 60KW',
            'technical_specs' => 'Input: 440V 3-Phase',
            'technical_remarks' => null,
            'model' => 'LTZ-60KW',
            'qty' => 2,
            'total_pieces' => 2,
            'price' => 20000,
            'total' => 40000,
        ]);

        // Item 2: Manual item (no product relation) with empty specs
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => null,
            'is_manual' => 1,
            'product_name' => 'Custom Copper Coil',
            'technical_name' => 'Custom Copper Coil',
            'technical_specs' => null,
            'technical_remarks' => null,
            'model' => null,
            'qty' => 1,
            'total_pieces' => 1,
            'price' => 10000,
            'total' => 10000,
        ]);

        $docResp = $this->actingAs($user)->get(route('sales.technical_doc', $sale->id));
        $docResp->assertStatus(200);
        $docResp->assertSee('Walk-in Test Client');
        $docResp->assertSee('Custom Induction Heater 60KW');
        $docResp->assertSee('Input: 440V 3-Phase');
        $docResp->assertSee('Custom Copper Coil');
        $docResp->assertSee('LTZ-60KW');
        $docResp->assertSee('Qty: 2');
        $docResp->assertSee('Qty: 1');
        $docResp->assertSee('class="manual-write-line"', false);
        $docResp->assertSee('class="spec-content-box spec-blank-box', false);
    }
}
