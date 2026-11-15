<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductsExport implements ReportExporter
{
    public function headings(): array
    {
        return ['SKU', 'Product', 'Category', 'Supplier', 'Quantity', 'Reorder level', 'Unit price', 'Inventory value'];
    }

    public function rows(array $filters): Collection
    {
        return Product::query()->with('supplier')->orderBy('name')->get()->map(fn (Product $product): array => [
            $product->sku, $product->name, $product->category, $product->supplier?->name,
            $product->qty, $product->reorder_level, $product->unit_price, $product->inventoryValue(),
        ]);
    }
}
