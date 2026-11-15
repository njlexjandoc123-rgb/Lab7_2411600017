<?php

namespace Tests\Feature;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ReorderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProcurementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_be_created_and_archived_without_deleting_purchase_order_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::query()->create([
            'name' => 'Campus Supply',
            'email' => 'supply@example.test',
            'payment_terms' => 'Net 30',
            'lead_time_days' => 5,
            'minimum_order_amount' => 100,
            'is_active' => true,
        ]);
        $order = PurchaseOrder::query()->create([
            'reference_number' => PurchaseOrder::generateReferenceNumber(),
            'supplier_id' => $supplier->id,
            'created_by' => $admin->id,
            'status' => 'draft',
            'order_date' => today(),
        ]);

        $this->actingAs($admin)->delete(route('suppliers.destroy', $supplier))
            ->assertRedirect(route('suppliers.index'));

        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
        $this->assertSame($supplier->id, $order->fresh()->supplier->id);
        $this->actingAs($admin)->get(route('suppliers.index', ['status' => 'archived']))
            ->assertOk()->assertSee('Archived');
        $this->post(route('suppliers.restore', $supplier->id))->assertRedirect(route('suppliers.show', $supplier->id));
        $this->assertNotSoftDeleted('suppliers', ['id' => $supplier->id]);
        $this->post(route('suppliers.store'), [
            'name' => 'Duplicate email supplier',
            'email' => 'supply@example.test',
            'payment_terms' => 'COD',
            'lead_time_days' => 1,
            'minimum_order_amount' => 0,
            'is_active' => 1,
        ])->assertSessionHasErrors('email');
    }

    public function test_product_with_purchase_order_history_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = $this->supplier();
        $product = $this->product($supplier);
        $order = $this->order($supplier, $admin, 'draft');
        $order->items()->create(['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 12.50]);

        $this->actingAs($admin)->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_purchase_order_uses_saved_product_price_and_only_manager_can_approve(): void
    {
        Mail::fake();
        $staff = User::factory()->create(['role' => 'staff']);
        $manager = User::factory()->create(['role' => 'manager']);
        $supplier = $this->supplier();
        $product = $this->product($supplier, ['unit_price' => 12.50]);

        $this->actingAs($staff)->post(route('purchase-orders.store'), [
            'supplier_id' => $supplier->id,
            'status' => 'pending',
            'order_date' => today()->toDateString(),
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 4,
                'unit_price' => 0.01,
            ]],
        ])->assertRedirect();

        $order = PurchaseOrder::query()->firstOrFail();
        $this->assertSame('50.00', $order->fresh()->subtotal);
        $this->assertSame('12.50', $order->items()->firstOrFail()->unit_price);
        $this->actingAs($staff)->get(route('purchase-orders.show', $order))->assertOk()->assertSee($order->reference_number);
        Mail::assertSent(\App\Mail\PurchaseOrderCreated::class);

        $this->actingAs($staff)->post(route('purchase-orders.approve', $order))->assertForbidden();
        $this->actingAs($manager)->post(route('purchase-orders.approve', $order))->assertRedirect();

        $this->assertSame('approved', $order->fresh()->status);
        $this->assertSame($manager->id, $order->fresh()->approved_by);
        Mail::assertSent(\App\Mail\PurchaseOrderApproved::class);
        $this->actingAs($manager)->post(route('purchase-orders.send', $order))->assertRedirect();
        $this->assertSame('sent', $order->fresh()->status);
        Mail::assertSent(\App\Mail\PurchaseOrderSent::class);
    }

    public function test_partial_receipts_update_stock_and_only_close_order_when_fully_received(): void
    {
        $warehouse = User::factory()->create(['role' => 'warehouse']);
        $supplier = $this->supplier();
        $product = $this->product($supplier, ['qty' => 2]);
        $order = $this->order($supplier, $warehouse, 'sent');
        $item = $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => 12.50,
        ]);

        $this->actingAs(User::factory()->create(['role' => 'staff']))
            ->post(route('purchase-orders.receive', $order), ['items' => [$item->id => ['quantity' => 1]]])
            ->assertForbidden();

        $this->actingAs($warehouse)->post(route('purchase-orders.receive', $order), [
            'items' => [$item->id => ['quantity' => 3]],
        ])->assertRedirect();
        $this->assertSame(5, $product->fresh()->qty);
        $this->assertSame('sent', $order->fresh()->status);
        $this->assertSame(3, $item->fresh()->received_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $product->id,
            'transaction_type' => 'stock_in',
            'quantity' => 3,
            'reference_number' => $order->reference_number,
        ]);

        $this->actingAs($warehouse)->post(route('purchase-orders.receive', $order), [
            'items' => [$item->id => ['quantity' => 2]],
        ])->assertRedirect();
        $this->assertSame(7, $product->fresh()->qty);
        $this->assertSame('received', $order->fresh()->status);
    }

    public function test_reorder_generation_is_threshold_based_and_idempotent(): void
    {
        $supplier = $this->supplier();
        $product = $this->product($supplier, ['qty' => 12, 'reorder_level' => 5, 'unit_price' => 10]);
        $user = User::factory()->create();
        InventoryTransaction::query()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'transaction_type' => 'stock_out',
            'quantity' => 9,
        ]);
        $service = app(ReorderService::class);

        $this->assertGreaterThanOrEqual(5, $service->calculateReorderPoint($product));
        $firstRun = $service->generatePurchaseOrders();
        $secondRun = $service->generatePurchaseOrders();

        $this->assertCount(1, $firstRun);
        $this->assertCount(0, $secondRun);
        $this->assertSame('draft', $firstRun->first()->status);
        $this->assertSame(1, $firstRun->first()->items()->count());
    }

    public function test_reorder_command_generates_drafts_and_notifies_administrators(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = $this->supplier();
        $product = $this->product($supplier, ['qty' => 12, 'reorder_level' => 5]);
        InventoryTransaction::query()->create([
            'product_id' => $product->id,
            'user_id' => $admin->id,
            'transaction_type' => 'stock_out',
            'quantity' => 9,
        ]);

        $this->artisan('inventory:check-reorder-points')
            ->expectsOutputToContain('created 1 draft purchase order')
            ->assertSuccessful();

        $this->assertDatabaseHas('purchase_orders', ['supplier_id' => $supplier->id, 'status' => 'draft']);
        Mail::assertSent(\App\Mail\LowStockAlert::class, fn ($mail): bool => $mail->hasTo($admin->email));
    }

    public function test_dashboard_widgets_and_csv_and_pdf_exports_are_available(): void
    {
        $user = User::factory()->create();
        $supplier = $this->supplier();
        $this->product($supplier);

        $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee('Procurement');
        $this->get(route('suppliers.index'))->assertOk()->assertSee('Campus Supply');
        $this->get(route('suppliers.create'))->assertOk();
        $this->get(route('supplier-performance.index'))->assertOk();
        $this->get(route('purchase-orders.index'))->assertOk();
        $this->get(route('purchase-orders.create'))->assertOk();
        $this->get(route('products.index'))->assertOk();
        $this->get(route('transactions.index'))->assertOk();
        $this->get(route('api.dashboard.pending-orders'))->assertOk()->assertJsonStructure(['count', 'orders']);
        $this->get(route('api.dashboard.reorder-alerts'))->assertOk();
        $this->get(route('exports.download', ['type' => 'products', 'format' => 'csv']))
            ->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->get(route('exports.download', ['type' => 'products', 'format' => 'pdf']))
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    private function supplier(): Supplier
    {
        return Supplier::query()->create([
            'name' => 'Campus Supply',
            'email' => 'supply@example.test',
            'payment_terms' => 'Net 30',
            'lead_time_days' => 5,
            'minimum_order_amount' => 0,
            'is_active' => true,
        ]);
    }

    private function product(Supplier $supplier, array $overrides = []): Product
    {
        return Product::query()->create(array_merge([
            'sku' => 'SKU-'.strtoupper(\Illuminate\Support\Str::random(8)),
            'name' => 'Test product',
            'qty' => 10,
            'reorder_level' => 5,
            'unit_price' => 12.50,
            'supplier_id' => $supplier->id,
        ], $overrides));
    }

    private function order(Supplier $supplier, User $creator, string $status): PurchaseOrder
    {
        return PurchaseOrder::query()->create([
            'reference_number' => PurchaseOrder::generateReferenceNumber(),
            'supplier_id' => $supplier->id,
            'created_by' => $creator->id,
            'status' => $status,
            'order_date' => today(),
            'expected_delivery_date' => today()->addDays(5),
        ]);
    }
}
