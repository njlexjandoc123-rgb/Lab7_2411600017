<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LowStockNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bell_lists_products_that_are_currently_low_on_stock(): void
    {
        $user = User::factory()->create();
        $lowStockProduct = $this->createProduct('LOW-001', 'Low stock item', 2, 5);
        $this->createProduct('OK-001', 'In stock item', 10, 5);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Current low-stock products')
            ->assertSee('data-notification-product="'.$lowStockProduct->sku.'"', false)
            ->assertSee('Low stock item')
            ->assertDontSee('data-notification-product="OK-001"', false);
    }

    public function test_stock_transaction_notifies_its_user_when_product_crosses_into_low_stock(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $product = $this->createProduct('STOCK-001', 'Transaction item', 10, 5);

        $this->actingAs($user)->post(route('transactions.store'), [
            'product_id' => $product->id,
            'transaction_type' => 'stock_out',
            'quantity' => 6,
        ])->assertRedirect(route('transactions.index'));

        Notification::assertSentTo($user, LowStockNotification::class);
        $this->assertSame(4, $product->fresh()->qty);
    }

    private function createProduct(string $sku, string $name, int $quantity, int $reorderLevel): Product
    {
        return Product::query()->create([
            'sku' => $sku,
            'name' => $name,
            'qty' => $quantity,
            'reorder_level' => $reorderLevel,
            'unit_price' => 10,
        ]);
    }
}
