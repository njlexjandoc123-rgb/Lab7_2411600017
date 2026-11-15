<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Services\SearchService;

class InventoryTransactionController extends Controller
{
    public function index(Request $request, SearchService $search): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'transaction_type' => ['nullable', Rule::in(['stock_in', 'stock_out', 'adjustment'])],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
        $transactions = $search->transactions($filters)->latest('created_at')->paginate(20)->withQueryString();
        $products = Product::query()->orderBy('name')->get();
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('transactions.index', compact('transactions', 'products', 'users'));
    }

    public function create(): View
    {
        $products = Product::query()->orderBy('name')->get();
        $transactionTypes = [
            'stock_in' => 'Stock In',
            'stock_out' => 'Stock Out',
            'adjustment' => 'Adjustment',
        ];

        return view('transactions.create', compact('products', 'transactionTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'transaction_type' => ['required', Rule::in(['stock_in', 'stock_out', 'adjustment'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);

        if ($validated['transaction_type'] === 'stock_out' && $product->qty < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough stock to complete this stock-out transaction.'])->withInput();
        }

        InventoryTransaction::query()->create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'transaction_type' => $validated['transaction_type'],
            'quantity' => $validated['quantity'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Inventory transaction recorded successfully.');
    }

    public function show(InventoryTransaction $transaction): View
    {
        $transaction->load(['product', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    public function destroy(InventoryTransaction $transaction): RedirectResponse
    {
        if (auth()->user()?->email !== 'admin@studentportal.test') {
            return redirect()->route('transactions.index')->with('error', 'Only administrators can delete inventory transactions.');
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
