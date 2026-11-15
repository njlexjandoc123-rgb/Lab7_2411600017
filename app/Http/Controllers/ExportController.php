<?php

namespace App\Http\Controllers;

use App\Exports\InventoryTransactionsExport;
use App\Exports\ProductsExport;
use App\Exports\PurchaseOrdersExport;
use App\Exports\ReportExporter;
use App\Exports\SupplierPerformanceExport;
use App\Exports\SuppliersExport;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function download(Request $request, string $type, string $format)
    {
        abort_unless(in_array($type, ['products', 'purchase-orders', 'transactions', 'suppliers', 'supplier-performance'], true), 404);
        abort_unless(in_array($format, ['csv', 'pdf'], true), 404);
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', Rule::in(PurchaseOrder::STATUSES)],
        ]);
        $export = $this->exporter($type);
        $headers = $export->headings();
        $rows = $export->rows($filters);
        $file = str_replace('-', '_', $type).'-'.now()->format('Ymd-His');

        if ($format === 'pdf') {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.report', compact('headers', 'rows', 'type'))
                ->setPaper('a4', 'landscape')
                ->download($file.'.pdf');
        }

        return new StreamedResponse(function () use ($headers, $rows): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, $headers);
            foreach ($rows as $row) {
                $row = array_map(function ($value) {
                    if (is_string($value) && preg_match('/^[\t\r\n ]*[=+\-@]/', $value)) {
                        return "'".$value;
                    }

                    return $value;
                }, $row);
                fputcsv($stream, $row);
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$file.'.csv"',
        ]);
    }

    private function exporter(string $type): ReportExporter
    {
        return match ($type) {
            'products' => App::make(ProductsExport::class),
            'purchase-orders' => App::make(PurchaseOrdersExport::class),
            'transactions' => App::make(InventoryTransactionsExport::class),
            'suppliers' => App::make(SuppliersExport::class),
            'supplier-performance' => App::make(SupplierPerformanceExport::class),
        };
    }
}
