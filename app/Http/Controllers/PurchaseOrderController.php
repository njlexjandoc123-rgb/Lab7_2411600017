<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseOrderApproved;
use App\Mail\PurchaseOrderCreated;
use App\Mail\PurchaseOrderSent;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request, SearchService $search): View
    {
        $this->authorize('viewAny', PurchaseOrder::class);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(PurchaseOrder::STATUSES)],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
        $orders = $search->purchaseOrders($filters)->latest('order_date')->paginate(15)->withQueryString();
        $suppliers = Supplier::query()->active()->orderBy('name')->get();

        return view('purchase-orders.index', compact('orders', 'suppliers'));
    }

    public function create(): View
    {
        $this->authorize('create', PurchaseOrder::class);
        $suppliers = Supplier::query()->active()->with('products')->orderBy('name')->get();

        return view('purchase-orders.create', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', PurchaseOrder::class);
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', Rule::exists('suppliers', 'id')->where('is_active', true)],
            'status' => ['required', Rule::in(['draft', 'pending'])],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')->where('supplier_id', $request->input('supplier_id'))],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000000'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);
        $supplier = Supplier::query()->active()->findOrFail($validated['supplier_id']);

        $order = DB::transaction(function () use ($validated, $supplier): PurchaseOrder {
            $items = collect($validated['items'])->map(function (array $item) use ($supplier): array {
                $product = Product::query()->where('supplier_id', $supplier->id)->findOrFail($item['product_id']);

                return [
                    'product_id' => $product->id,
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => (float) $product->unit_price,
                ];
            });
            $subtotal = (float) $items->sum(fn (array $item): float => $item['quantity'] * $item['unit_price']);
            $order = PurchaseOrder::query()->create([
                'reference_number' => PurchaseOrder::generateReferenceNumber(),
                'supplier_id' => $supplier->id,
                'created_by' => auth()->id(),
                'status' => $validated['status'],
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'total_amount' => $subtotal + (float) ($validated['tax_amount'] ?? 0),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            return $order;
        });

        $this->sendMailTo($request->user(), new PurchaseOrderCreated($order->load(['supplier', 'items.product'])));

        return redirect()->route('purchase-orders.show', $order)->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('view', $purchaseOrder);
        $purchaseOrder->load(['supplier', 'createdBy', 'approvedBy', 'items.product']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('update', $purchaseOrder);
        $purchaseOrder->load('items.product');
        $suppliers = Supplier::query()->active()->with('products')->orderBy('name')->get();

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', Rule::exists('suppliers', 'id')->where('is_active', true)],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')->where('supplier_id', $request->input('supplier_id'))],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000000'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $supplier = Supplier::query()->active()->findOrFail($validated['supplier_id']);
        DB::transaction(function () use ($validated, $purchaseOrder, $supplier): void {
            $items = collect($validated['items'])->map(function (array $item) use ($supplier): array {
                $product = Product::query()->where('supplier_id', $supplier->id)->findOrFail($item['product_id']);

                return [
                    'product_id' => $product->id,
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => (float) $product->unit_price,
                ];
            });
            $subtotal = (float) $items->sum(fn (array $item): float => $item['quantity'] * $item['unit_price']);
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'total_amount' => $subtotal + (float) ($validated['tax_amount'] ?? 0),
                'notes' => $validated['notes'] ?? null,
            ]);
            $purchaseOrder->items()->delete();
            foreach ($items as $item) {
                $purchaseOrder->items()->create($item);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Draft purchase order updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('delete', $purchaseOrder);
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'Draft purchase order archived.');
    }

    public function approve(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('approve', $purchaseOrder);
        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        $purchaseOrder->load(['createdBy', 'supplier', 'items.product']);
        $this->sendMailTo($purchaseOrder->createdBy, new PurchaseOrderApproved($purchaseOrder));

        return back()->with('success', 'Purchase order approved.');
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('reject', $purchaseOrder);
        $validated = $request->validate(['reason' => ['required', 'string', 'max:2000']]);
        $purchaseOrder->update(['status' => 'rejected', 'rejection_reason' => $validated['reason']]);

        return back()->with('success', 'Purchase order rejected.');
    }

    public function send(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('send', $purchaseOrder);
        $purchaseOrder->update(['status' => 'sent', 'sent_at' => now()]);
        $purchaseOrder->load(['supplier', 'items.product']);
        if ($purchaseOrder->supplier->email) {
            Mail::to($purchaseOrder->supplier->email)->send(new PurchaseOrderSent($purchaseOrder));
        }

        return back()->with('success', $purchaseOrder->supplier->email
            ? 'Purchase order marked as sent and emailed to the supplier.'
            : 'Purchase order marked as sent. No supplier email is configured.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('receive', $purchaseOrder);
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder): void {
            $order = PurchaseOrder::query()->lockForUpdate()->findOrFail($purchaseOrder->id);
            $receivedAnything = false;
            foreach ($validated['items'] as $itemId => $input) {
                $quantity = (int) $input['quantity'];
                if ($quantity === 0) {
                    continue;
                }

                $item = PurchaseOrderItem::query()->where('purchase_order_id', $order->id)->lockForUpdate()->findOrFail($itemId);
                if ($quantity > $item->outstanding_quantity) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.$itemId.quantity" => 'Received quantity cannot exceed the outstanding quantity.',
                    ]);
                }

                Product::query()->lockForUpdate()->findOrFail($item->product_id);
                $item->increment('received_quantity', $quantity);
                InventoryTransaction::query()->create([
                    'product_id' => $item->product_id,
                    'user_id' => auth()->id(),
                    'transaction_type' => 'stock_in',
                    'quantity' => $quantity,
                    'reference_number' => $order->reference_number,
                    'notes' => $validated['notes'] ?? 'Received against purchase order.',
                ]);
                $receivedAnything = true;
            }

            if (! $receivedAnything) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'items' => 'Enter a positive received quantity for at least one item.',
                ]);
            }

            $order->load('items');
            if ($order->items->every(fn (PurchaseOrderItem $item): bool => $item->received_quantity >= $item->quantity)) {
                $order->update(['status' => 'received', 'received_at' => now()]);
            }
        });

        return back()->with('success', 'Received quantities recorded and inventory updated.');
    }

    private function sendMailTo(?User $recipient, Mailable $mail): void
    {
        if ($recipient?->email) {
            Mail::to($recipient->email)->send($mail);
        }
    }
}
