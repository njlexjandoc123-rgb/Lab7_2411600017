<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    public function products(array $filters): Builder
    {
        return Product::query()
            ->with('supplier')
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $query) => $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhereHas('supplier', fn (Builder $supplier) => $supplier->where('name', 'like', '%'.$search.'%')));
            })
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->when($filters['supplier_id'] ?? null, fn (Builder $query, int $supplierId) => $query->where('supplier_id', $supplierId));
    }

    public function purchaseOrders(array $filters): Builder
    {
        return PurchaseOrder::query()
            ->with(['supplier', 'createdBy', 'items.product'])
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $query) => $query
                    ->where('reference_number', 'like', '%'.$search.'%')
                    ->orWhereHas('supplier', fn (Builder $supplier) => $supplier->where('name', 'like', '%'.$search.'%')));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['supplier_id'] ?? null, fn (Builder $query, int $supplierId) => $query->where('supplier_id', $supplierId))
            ->when($filters['start_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('order_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('order_date', '<=', $date));
    }

    public function transactions(array $filters): Builder
    {
        return InventoryTransaction::query()
            ->with(['product', 'user'])
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $query) => $query
                    ->where('reference_number', 'like', '%'.$search.'%')
                    ->orWhereHas('product', fn (Builder $product) => $product
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%')));
            })
            ->when($filters['product_id'] ?? null, fn (Builder $query, int $id) => $query->where('product_id', $id))
            ->when($filters['transaction_type'] ?? null, fn (Builder $query, string $type) => $query->where('transaction_type', $type))
            ->when($filters['user_id'] ?? null, fn (Builder $query, int $id) => $query->where('user_id', $id))
            ->when($filters['start_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date));
    }
}
