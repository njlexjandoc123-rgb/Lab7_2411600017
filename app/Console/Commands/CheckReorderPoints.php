<?php

namespace App\Console\Commands;

use App\Mail\LowStockAlert;
use App\Models\Product;
use App\Models\User;
use App\Services\ReorderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckReorderPoints extends Command
{
    protected $signature = 'inventory:check-reorder-points';

    protected $description = 'Create draft purchase orders for products at or below reorder point.';

    public function handle(ReorderService $reorderService): int
    {
        $products = $reorderService->getProductsNeedingReorder();
        $orders = $reorderService->generatePurchaseOrders();
        $this->info(sprintf('Checked %d products and created %d draft purchase order(s).', $products->count(), $orders->count()));

        if ($orders->isNotEmpty()) {
            $reorderedProducts = Product::query()
                ->whereIn('id', $orders->flatMap(fn ($order) => $order->items()->pluck('product_id'))->unique())
                ->get();
            $admins = User::query()->whereIn('role', ['admin', 'manager'])->whereNotNull('email')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new LowStockAlert($reorderedProducts));
            }
        }

        foreach ($orders as $order) {
            $this->line($order->reference_number.' - Supplier #'.$order->supplier_id);
        }

        return self::SUCCESS;
    }
}
