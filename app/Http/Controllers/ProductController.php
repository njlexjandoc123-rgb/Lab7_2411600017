<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\SearchService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Ipakita ang listahan ng lahat ng student portal products.
     */
    public function index(Request $request, SearchService $search)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
        ]);
        $products = $search->products($filters)->latest()->paginate(10)->withQueryString();
        $categories = Category::query()->orderBy('name')->pluck('name');
        $suppliers = Supplier::query()->active()->orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Ipakita ang form para sa paggawa ng bagong product.
     */
    public function create()
    {
        $brands = Brand::query()->orderBy('name')->pluck('name');
        $categories = Category::query()->orderBy('name')->pluck('name');
        $suppliers = Supplier::query()->active()->orderBy('name')->get();

        return view('products.create', compact('brands', 'categories', 'suppliers'));
    }

    /**
     * I-save ang bagong product sa database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100|exists:brands,name',
            'category' => 'nullable|string|max:100|exists:categories,name',
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'qty' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Ipakita ang details ng isang partikular na product.
     */
    public function show(Product $product)
    {
        $startDate = request('start_date', now()->subMonth()->toDateString());
        $endDate = request('end_date', now()->toDateString());

        $transactions = $product->transactions()
            ->with('user')
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $runningBalance = 0;
        foreach ($transactions as $transaction) {
            $runningBalance += match ($transaction->transaction_type) {
                'stock_in' => $transaction->quantity,
                'stock_out' => -$transaction->quantity,
                'adjustment' => $transaction->quantity,
                default => 0,
            };
            $transaction->running_balance = $runningBalance;
        }

        return view('products.show', compact('product', 'transactions', 'startDate', 'endDate'));
    }

    /**
     * Ipakita ang form para sa pag-edit ng napiling product.
     */
    public function edit(Product $product)
    {
        $brands = Brand::query()->orderBy('name')->pluck('name');
        $categories = Category::query()->orderBy('name')->pluck('name');
        $suppliers = Supplier::query()->active()->orderBy('name')->get();

        return view('products.edit', compact('product', 'brands', 'categories', 'suppliers'));
    }

    /**
     * I-update ang product sa database pagkatapos i-edit.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,'.$product->id,
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100|exists:brands,name',
            'category' => 'nullable|string|max:100|exists:categories,name',
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'qty' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Burahin ang product sa database.
     */
    public function destroy(Product $product)
    {
        if ($product->purchaseOrderItems()->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'This product is referenced by purchase order history and cannot be deleted.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
