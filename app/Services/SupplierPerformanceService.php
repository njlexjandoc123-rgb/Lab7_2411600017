<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Facades\Cache;

class SupplierPerformanceService
{
    /**
     * @return array<int, array<string, int|float>>
     */
    public function allMetrics(): array
    {
        return Cache::remember('supplier-performance-metrics', now()->addMinute(), function (): array {
            return Supplier::withTrashed()
                ->with(['purchaseOrders' => function ($query): void {
                    $query->with('items:id,purchase_order_id,quantity,received_quantity');
                }])
                ->get()
                ->mapWithKeys(fn (Supplier $supplier): array => [$supplier->id => $this->calculate($supplier)])
                ->all();
        });
    }

    /**
     * @return array<string, int|float>
     */
    public function metricsForSupplier(Supplier $supplier): array
    {
        return $this->allMetrics()[$supplier->id] ?? $this->emptyMetrics();
    }

    /**
     * @return array<string, int|float>
     */
    private function calculate(Supplier $supplier): array
    {
        $orders = $supplier->purchaseOrders;
        $delivered = $orders->where('status', 'received')->whereNotNull('received_at');
        $onTime = $delivered->filter(
            fn ($order): bool => $order->expected_delivery_date === null
                || $order->received_at->toDateString() <= $order->expected_delivery_date->toDateString()
        );
        $accurate = $delivered->filter(
            fn ($order): bool => $order->items->isNotEmpty()
                && $order->items->every(fn ($item): bool => $item->received_quantity === $item->quantity)
        );

        return [
            'total_orders' => $orders->count(),
            'delivered_orders' => $delivered->count(),
            'on_time_orders' => $onTime->count(),
            'on_time_percentage' => $delivered->isEmpty() ? 0.0 : round($onTime->count() / $delivered->count() * 100, 1),
            'accuracy_percentage' => $delivered->isEmpty() ? 0.0 : round($accurate->count() / $delivered->count() * 100, 1),
            'average_delivery_days' => $delivered->isEmpty()
                ? 0.0
                : round($delivered->avg(fn ($order): int => (int) $order->order_date->diffInDays($order->received_at)), 1),
            'average_delivery_variance_days' => $delivered->filter(fn ($order): bool => $order->expected_delivery_date !== null)->isEmpty()
                ? 0.0
                : round($delivered->filter(fn ($order): bool => $order->expected_delivery_date !== null)
                    ->avg(fn ($order): int => (int) $order->expected_delivery_date->diffInDays($order->received_at, false)), 1),
            'total_value' => round((float) $orders->sum('total_amount'), 2),
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function emptyMetrics(): array
    {
        return [
            'total_orders' => 0,
            'delivered_orders' => 0,
            'on_time_orders' => 0,
            'on_time_percentage' => 0.0,
            'accuracy_percentage' => 0.0,
            'average_delivery_days' => 0.0,
            'average_delivery_variance_days' => 0.0,
            'total_value' => 0.0,
        ];
    }

    /**
     * @return array<int, array{month: string, deliveries: int, on_time_percentage: float}>
     */
    public function deliveryTrend(): array
    {
        return Cache::remember('supplier-delivery-trend', now()->addMinute(), function (): array {
            $orders = \App\Models\PurchaseOrder::query()
                ->where('status', 'received')
                ->whereNotNull('received_at')
                ->where('received_at', '>=', now()->subMonths(5)->startOfMonth())
                ->get(['received_at', 'expected_delivery_date']);

            return collect(range(5, 0))->map(function (int $offset) use ($orders): array {
                $month = now()->subMonths($offset)->format('Y-m');
                $deliveries = $orders->filter(fn ($order): bool => $order->received_at->format('Y-m') === $month);
                $onTime = $deliveries->filter(fn ($order): bool => $order->expected_delivery_date === null
                    || $order->received_at->toDateString() <= $order->expected_delivery_date->toDateString());

                return [
                    'month' => $month,
                    'deliveries' => $deliveries->count(),
                    'on_time_percentage' => $deliveries->isEmpty() ? 0.0 : round($onTime->count() / $deliveries->count() * 100, 1),
                ];
            })->all();
        });
    }
}
