<?php

namespace App\Providers;

use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\ReorderService;
use App\Services\SupplierPerformanceService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('widgets.pending-orders', function ($view): void {
            $view->with('pendingOrdersCount', Cache::remember('dashboard-pending-orders', now()->addMinute(), fn (): int => PurchaseOrder::pending()->count()));
        });

        View::composer('widgets.recent-orders', function ($view): void {
            $orders = Cache::remember('dashboard-recent-orders', now()->addMinute(), fn () => PurchaseOrder::query()
                ->with('supplier')
                ->latest('order_date')
                ->take(5)
                ->get());
            $view->with('recentPurchaseOrders', $orders);
        });

        View::composer('widgets.supplier-performance', function ($view): void {
            $metrics = app(SupplierPerformanceService::class)->allMetrics();
            $suppliers = Supplier::active()->get()
                ->sortByDesc(fn (Supplier $supplier): float => (float) ($metrics[$supplier->id]['on_time_percentage'] ?? 0))
                ->take(5)
                ->values();
            $view->with('topSuppliers', $suppliers)->with('supplierPerformanceMetrics', $metrics);
        });

        View::composer('widgets.reorder-alerts', function ($view): void {
            $products = Cache::remember('dashboard-reorder-alerts', now()->addMinute(), fn () => app(ReorderService::class)->getProductsNeedingReorder()
                ->take(5)
                ->values());
            $view->with('reorderProducts', $products);
        });
    }
}
