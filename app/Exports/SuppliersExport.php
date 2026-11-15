<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Support\Collection;

class SuppliersExport implements ReportExporter
{
    public function headings(): array
    {
        return ['Company', 'Contact', 'Email', 'Phone', 'Payment terms', 'Lead time (days)', 'Minimum order', 'Active'];
    }

    public function rows(array $filters): Collection
    {
        return Supplier::query()->orderBy('name')->get()->map(fn (Supplier $supplier): array => [
            $supplier->name, $supplier->contact_person, $supplier->email, $supplier->phone, $supplier->payment_terms,
            $supplier->lead_time_days, $supplier->minimum_order_amount, $supplier->is_active ? 'Yes' : 'No',
        ]);
    }
}
