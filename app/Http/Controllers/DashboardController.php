<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\ReorderService;
use App\Services\SupplierPerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the student portal inventory dashboard with real statistics.
     */
    public function index(): View
    {
        $totalProducts = Product::count();
        $totalBrands = Brand::count();
        $totalCategories = Category::count();
        $lowStockItems = Product::lowStock()->count();
        $outOfStockItems = Product::outOfStock()->count();
        $totalInventoryValue = Product::query()->sum(DB::raw('qty * unit_price'));

        $recentProducts = Product::query()->latest()->take(5)->get();
        $lowStockProducts = Product::query()->lowStock()->orderBy('qty')->take(5)->get();
        $brands = Brand::query()->withCount('products')->orderByDesc('products_count')->orderBy('name')->take(5)->get();
        $categories = Category::query()->withCount('products')->orderByDesc('products_count')->orderBy('name')->take(5)->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalBrands',
            'totalCategories',
            'lowStockItems',
            'outOfStockItems',
            'totalInventoryValue',
            'recentProducts',
            'lowStockProducts',
            'brands',
            'categories'
        ));
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total_products' => Product::count(),
            'low_stock_items' => Product::lowStock()->count(),
            'out_of_stock_items' => Product::outOfStock()->count(),
            'inventory_value' => (float) Product::query()->sum(DB::raw('qty * unit_price')),
            'last_updated' => now()->toIso8601String(),
        ]);
    }

    public function lowStockApi(): JsonResponse
    {
        $products = Product::query()->lowStock()->orderBy('qty')->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'qty' => $product->qty,
                'reorder_level' => $product->reorder_level,
            ];
        });

        return response()->json($products);
    }

    public function recentTransactionsApi(): JsonResponse
    {
        $transactions = InventoryTransaction::query()
            ->with(['product', 'user'])
            ->latest('created_at')
            ->take(8)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'product' => $transaction->product?->name,
                    'type' => $transaction->transaction_type,
                    'quantity' => $transaction->quantity,
                    'user' => $transaction->user?->name,
                    'created_at' => $transaction->created_at?->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($transactions);
    }

    public function pendingOrdersApi(): JsonResponse
    {
        return response()->json([
            'count' => PurchaseOrder::pending()->count(),
            'orders' => PurchaseOrder::pending()->with('supplier:id,name')->latest('order_date')->take(5)->get([
                'id', 'reference_number', 'supplier_id', 'total_amount', 'order_date',
            ]),
        ]);
    }

    public function recentOrdersApi(): JsonResponse
    {
        return response()->json(PurchaseOrder::query()
            ->with('supplier:id,name')
            ->latest('order_date')
            ->take(5)
            ->get(['id', 'reference_number', 'supplier_id', 'status', 'total_amount', 'order_date']));
    }

    public function supplierPerformanceApi(SupplierPerformanceService $performance): JsonResponse
    {
        $metrics = $performance->allMetrics();
        $suppliers = Supplier::active()->orderBy('name')->get(['id', 'name'])->map(fn (Supplier $supplier): array => [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'metrics' => $metrics[$supplier->id] ?? [],
        ]);

        return response()->json($suppliers);
    }

    public function reorderAlertsApi(ReorderService $reorderService): JsonResponse
    {
        return response()->json($reorderService->getProductsNeedingReorder()->map(fn (Product $product): array => [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'qty' => $product->qty,
            'reorder_point' => $product->calculated_reorder_point,
            'supplier' => $product->supplier?->name,
        ]));
    }
}
