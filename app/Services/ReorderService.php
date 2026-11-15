<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\InventoryTransaction;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReorderService
{
    private const LOOKBACK_DAYS = 90;

    public function calculateReorderPoint(Product $product): int
    {
        return $this->reorderPoint($product, $this->averageDailyUsage($product));
    }

    private function reorderPoint(Product $product, float $usage): int
    {
        $leadTime = (int) ($product->supplier?->lead_time_days ?? 7);
        $safetyStock = $usage > 0 ? (int) ceil($usage * $leadTime * 0.25) : 0;
        $calculatedPoint = (int) ceil(($usage * $leadTime) + $safetyStock);

        return max((int) ($product->reorder_level ?? 0), $calculatedPoint);
    }

    public function calculateOptimalOrderQuantity(Product $product): int
    {
        $usages = $this->averageDailyUsages([$product->id]);
        $outstanding = $this->outstandingQuantities([$product->id]);

        return $this->optimalQuantity(
            $product,
            (float) $usages->get($product->id, 0) / self::LOOKBACK_DAYS,
            (int) $outstanding->get($product->id, 0)
        );
    }

    /**
     * @return EloquentCollection<int, Product>
     */
    public function getProductsNeedingReorder(): EloquentCollection
    {
        $products = Product::query()->with('supplier')->get();
        $usages = $this->averageDailyUsages($products->modelKeys());

        return $products->filter(function (Product $product) use ($usages): bool {
            $reorderPoint = $this->reorderPoint(
                $product,
                (float) $usages->get($product->id, 0) / self::LOOKBACK_DAYS
            );
            $product->setAttribute('calculated_reorder_point', $reorderPoint);

            return $product->qty <= $reorderPoint;
        })->values();
    }

    /**
     * @param iterable<Product> $products
     * @return Collection<int, Collection<int, Product>>
     */
    public function groupProductsBySupplier(iterable $products): Collection
    {
        return collect($products)->filter(fn (Product $product): bool => (bool) $product->supplier?->is_active)
            ->groupBy('supplier_id');
    }

    /**
     * @return EloquentCollection<int, PurchaseOrder>
     */
    public function generatePurchaseOrders(): EloquentCollection
    {
        return DB::transaction(function (): EloquentCollection {
            $products = $this->getProductsNeedingReorder();
            $createdOrders = new EloquentCollection;
            $usages = $this->averageDailyUsages($products->modelKeys());
            $outstanding = $this->outstandingQuantities($products->modelKeys());

            foreach ($this->groupProductsBySupplier($products) as $supplierId => $supplierProducts) {
                $supplier = Supplier::query()->active()->find($supplierId);
                if (! $supplier) {
                    continue;
                }

                $items = $supplierProducts
                    ->map(fn (Product $product): array => [
                        'product' => $product,
                        'quantity' => $this->optimalQuantity(
                            $product,
                            (float) $usages->get($product->id, 0) / self::LOOKBACK_DAYS,
                            (int) $outstanding->get($product->id, 0)
                        ),
                    ])
                    ->filter(fn (array $item): bool => $item['quantity'] > 0);

                if ($items->isEmpty()) {
                    continue;
                }

                $subtotal = (float) $items->sum(fn (array $item): float => $item['quantity'] * (float) $item['product']->unit_price);
                if ($subtotal < (float) $supplier->minimum_order_amount) {
                    $pricedItemKey = $items->search(fn (array $item): bool => (float) $item['product']->unit_price > 0);
                    if ($pricedItemKey === false) {
                        logger()->warning('Could not satisfy supplier minimum order amount because every reorder product has a zero price.', [
                            'supplier_id' => $supplier->id,
                        ]);
                        continue;
                    }
                    $firstItem = $items[$pricedItemKey];
                    $extra = (int) ceil(((float) $supplier->minimum_order_amount - $subtotal) / (float) $firstItem['product']->unit_price);
                    $firstItem['quantity'] += $extra;
                    $items[$pricedItemKey] = $firstItem;
                    $subtotal = (float) $items->sum(fn (array $item): float => $item['quantity'] * (float) $item['product']->unit_price);
                }

                $order = PurchaseOrder::query()->create([
                    'reference_number' => PurchaseOrder::generateReferenceNumber(),
                    'supplier_id' => $supplier->id,
                    'status' => 'draft',
                    'order_date' => today(),
                    'expected_delivery_date' => today()->addDays($supplier->lead_time_days),
                    'subtotal' => $subtotal,
                    'total_amount' => $subtotal,
                    'notes' => 'Automatically generated from reorder point check; requires review and approval.',
                ]);

                foreach ($items as $item) {
                    $order->items()->create([
                        'product_id' => $item['product']->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['product']->unit_price,
                    ]);
                }

                $createdOrders->push($order);
            }

            return $createdOrders;
        });
    }

    private function averageDailyUsage(Product $product): float
    {
        return (float) $this->averageDailyUsages([$product->id])->get($product->id, 0) / self::LOOKBACK_DAYS;
    }

    /**
     * @param array<int, int> $productIds
     * @return Collection<int, int>
     */
    private function averageDailyUsages(array $productIds): Collection
    {
        if ($productIds === []) {
            return collect();
        }

        return InventoryTransaction::query()
            ->whereIn('product_id', $productIds)
            ->where('transaction_type', 'stock_out')
            ->where('created_at', '>=', now()->subDays(self::LOOKBACK_DAYS))
            ->selectRaw('product_id, SUM(quantity) as usage')
            ->groupBy('product_id')
            ->pluck('usage', 'product_id');
    }

    /**
     * @param array<int, int> $productIds
     * @return Collection<int, int>
     */
    private function outstandingQuantities(array $productIds): Collection
    {
        if ($productIds === []) {
            return collect();
        }

        return PurchaseOrderItem::query()
            ->whereIn('product_id', $productIds)
            ->whereHas('purchaseOrder', fn ($query) => $query->active())
            ->selectRaw('product_id, SUM(quantity - received_quantity) as outstanding')
            ->groupBy('product_id')
            ->pluck('outstanding', 'product_id');
    }

    private function optimalQuantity(Product $product, float $averageDailyUsage, int $outstanding): int
    {
        $leadTime = (int) ($product->supplier?->lead_time_days ?? 7);
        $targetStock = max(
            (int) ($product->reorder_level ?? 0),
            (int) ceil($averageDailyUsage * ($leadTime + 30))
        );

        return max(0, $targetStock - $product->qty - $outstanding);
    }
}
