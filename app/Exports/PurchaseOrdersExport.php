<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Illuminate\Support\Collection;

class PurchaseOrdersExport implements ReportExporter
{
    public function headings(): array
    {
        return ['Reference', 'Supplier', 'Status', 'Order date', 'Expected delivery', 'Subtotal', 'Tax', 'Total'];
    }

    public function rows(array $filters): Collection
    {
        return PurchaseOrder::query()->with('supplier')
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['start_date'] ?? null, fn ($query, string $date) => $query->whereDate('order_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, string $date) => $query->whereDate('order_date', '<=', $date))
            ->latest('order_date')->get()->map(fn (PurchaseOrder $order): array => [
                $order->reference_number, $order->supplier?->name, $order->status, $order->order_date->toDateString(),
                $order->expected_delivery_date?->toDateString(), $order->subtotal, $order->tax_amount, $order->total_amount,
            ]);
    }
}
