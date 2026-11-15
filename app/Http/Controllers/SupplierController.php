<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierPerformanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request, SupplierPerformanceService $performance): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'archived'])],
            'sort' => ['nullable', Rule::in(['name', 'lead_time_days', 'created_at'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);
        $query = Supplier::query();
        if (($validated['status'] ?? null) === 'archived') {
            $query->withTrashed()->whereNotNull('deleted_at');
        } elseif (isset($validated['status'])) {
            $query->where('is_active', $validated['status'] === 'active');
        }
        $query->withCount(['products', 'purchaseOrders'])
            ->when($validated['q'] ?? null, fn ($query, string $search) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('contact_person', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            }));
        $sort = $validated['sort'] ?? 'name';
        $suppliers = $query->orderBy($sort, $validated['direction'] ?? 'asc')->paginate(10)->withQueryString();
        $metrics = $performance->allMetrics();

        return view('suppliers.index', compact('suppliers', 'metrics'));
    }

    public function create(): View
    {
        return view('suppliers.create', ['supplier' => new Supplier]);
    }

    public function store(Request $request): RedirectResponse
    {
        $supplier = Supplier::query()->create($this->validateSupplier($request));

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier, SupplierPerformanceService $performance): View
    {
        $supplier->load([
            'products' => fn ($query) => $query->orderBy('name'),
            'purchaseOrders' => fn ($query) => $query->withCount('items')->latest('order_date')->limit(10),
        ]);
        $metrics = $performance->metricsForSupplier($supplier);

        return view('suppliers.show', compact('supplier', 'metrics'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validateSupplier($request, $supplier));

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchaseOrders()->whereIn('status', ['pending', 'approved', 'sent'])->exists()) {
            return back()->with('error', 'This supplier cannot be archived while purchase orders are pending, approved, or sent.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier archived. Historical purchase orders are preserved.');
    }

    public function restore(int $supplier): RedirectResponse
    {
        $this->authorize('restore', Supplier::class);
        $record = Supplier::withTrashed()->findOrFail($supplier);
        $record->restore();

        return redirect()->route('suppliers.show', $record)->with('success', 'Supplier restored successfully.');
    }

    public function performance(SupplierPerformanceService $performance): View
    {
        $suppliers = Supplier::query()->active()->orderBy('name')->get();
        $metrics = $performance->allMetrics();
        $rankedSuppliers = $suppliers->sortByDesc(
            fn (Supplier $supplier): float => (float) ($metrics[$supplier->id]['on_time_percentage'] ?? 0)
        )->values();
        $deliveryTrend = $performance->deliveryTrend();

        return view('reports.supplier-performance', compact('suppliers', 'metrics', 'rankedSuppliers', 'deliveryTrend'));
    }

    private function validateSupplier(Request $request, ?Supplier $supplier = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($supplier?->id)],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+().\\-\\s]+$/'],
            'address' => ['nullable', 'string', 'max:2000'],
            'payment_terms' => ['required', 'string', 'max:100'],
            'lead_time_days' => ['required', 'integer', 'min:0', 'max:3650'],
            'minimum_order_amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
