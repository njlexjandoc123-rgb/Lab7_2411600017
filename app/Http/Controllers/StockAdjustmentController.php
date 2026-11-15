<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function create(): View
    {
        $products = Product::query()->orderBy('name')->get();

        return view('adjustments.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'physical_count' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $difference = $validated['physical_count'] - $product->qty;

        if ($difference === 0) {
            return back()->with('info', 'No adjustment needed because the physical count matches the system quantity.');
        }

        InventoryTransaction::query()->create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'transaction_type' => 'adjustment',
            'quantity' => $difference,
            'reference_number' => 'ADJ-'. strtoupper(substr(md5((string) time()), 0, 8)),
            'notes' => trim(($validated['reason'] ?? '').' '.($validated['notes'] ?? '')),
        ]);

        return redirect()->route('products.show', $product)->with('success', 'Stock adjustment recorded successfully.');
    }
}
