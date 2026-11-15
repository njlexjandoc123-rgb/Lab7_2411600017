<?php

namespace App\Exports;

use App\Models\InventoryTransaction;
use Illuminate\Support\Collection;

class InventoryTransactionsExport implements ReportExporter
{
    public function headings(): array
    {
        return ['Date', 'Product', 'SKU', 'Type', 'Quantity', 'Reference', 'User'];
    }

    public function rows(array $filters): Collection
    {
        return InventoryTransaction::query()->with(['product', 'user'])
            ->when($filters['start_date'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->latest('created_at')->get()->map(fn (InventoryTransaction $transaction): array => [
                $transaction->created_at->format('Y-m-d H:i:s'), $transaction->product?->name, $transaction->product?->sku,
                $transaction->transaction_type, $transaction->quantity, $transaction->reference_number, $transaction->user?->name,
            ]);
    }
}
