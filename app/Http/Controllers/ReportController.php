<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $transactions = InventoryTransaction::query()
            ->with(['product', 'user'])
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $lowStockProducts = Product::query()->lowStock()->orderBy('qty')->get();
        $inventoryValue = Product::query()->get()->sum(fn ($product) => $product->qty * $product->unit_price);

        return view('reports.index', compact('transactions', 'lowStockProducts', 'inventoryValue', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $transactions = InventoryTransaction::query()
            ->with(['product', 'user'])
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-report.csv"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Product', 'Type', 'Quantity', 'Reference', 'User']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('Y-m-d'),
                    $transaction->product?->name,
                    $transaction->transaction_type,
                    $transaction->quantity,
                    $transaction->reference_number,
                    $transaction->user?->name,
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'inventory-report.csv', $headers);
    }
}
