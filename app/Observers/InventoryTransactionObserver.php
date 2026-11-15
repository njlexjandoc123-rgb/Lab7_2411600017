<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Notifications\LowStockNotification;

class InventoryTransactionObserver
{
    public function created(InventoryTransaction $inventoryTransaction): void
    {
        $this->applyStockChange($inventoryTransaction, 1);
    }

    public function deleted(InventoryTransaction $inventoryTransaction): void
    {
        $this->applyStockChange($inventoryTransaction, -1);
    }

    protected function applyStockChange(InventoryTransaction $transaction, int $direction): void
    {
        $product = Product::query()->find($transaction->product_id);

        if (! $product) {
            return;
        }

        $wasLowStock = $product->isLowStock();
        $quantity = (int) $transaction->quantity;
        $updatedQty = match ($transaction->transaction_type) {
            'stock_in' => $product->qty + ($direction * $quantity),
            'stock_out' => $product->qty - ($direction * $quantity),
            'adjustment' => $product->qty + ($direction * $quantity),
            default => $product->qty,
        };

        if ($updatedQty < 0) {
            throw new \RuntimeException('Stock cannot go below zero.');
        }

        $product->forceFill(['qty' => $updatedQty])->saveQuietly();

        $product = $product->fresh();

        if (! $wasLowStock && $product->isLowStock() && $transaction->user) {
            $transaction->user->notify(new LowStockNotification($product));
        }
    }
}
