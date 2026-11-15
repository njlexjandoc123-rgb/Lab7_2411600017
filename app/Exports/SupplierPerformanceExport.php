<?php

namespace App\Exports;

use App\Models\Supplier;
use App\Services\SupplierPerformanceService;
use Illuminate\Support\Collection;

class SupplierPerformanceExport implements ReportExporter
{
    public function __construct(private SupplierPerformanceService $performance)
    {
    }

    public function headings(): array
    {
        return ['Supplier', 'Orders', 'Delivered', 'On-time delivery %', 'Order accuracy %', 'Average delivery days', 'Average days vs expected', 'Total value'];
    }

    public function rows(array $filters): Collection
    {
        return Supplier::query()->active()->orderBy('name')->get()->map(function (Supplier $supplier): array {
            $metrics = $this->performance->metricsForSupplier($supplier);

            return [
                $supplier->name, $metrics['total_orders'], $metrics['delivered_orders'], $metrics['on_time_percentage'],
                $metrics['accuracy_percentage'], $metrics['average_delivery_days'], $metrics['average_delivery_variance_days'], $metrics['total_value'],
            ];
        });
    }
}
