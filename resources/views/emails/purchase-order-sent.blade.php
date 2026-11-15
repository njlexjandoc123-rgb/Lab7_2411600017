<div style="font-family:Arial,sans-serif;color:#1e293b">
    <h2 style="color:#075985">Purchase order {{ $purchaseOrder->reference_number }}</h2>
    <p>Hello {{ $purchaseOrder->supplier?->contact_person ?: $purchaseOrder->supplier?->name }},</p>
    <p>Please review the attached order details in your procurement portal and contact us if you have questions.</p>
    <p>Total: {{ number_format((float) $purchaseOrder->total_amount, 2) }}</p>
    <table style="border-collapse:collapse;width:100%">
        <thead><tr><th style="text-align:left;border-bottom:1px solid #cbd5e1;padding:6px">Product</th><th style="text-align:right;border-bottom:1px solid #cbd5e1;padding:6px">Qty</th><th style="text-align:right;border-bottom:1px solid #cbd5e1;padding:6px">Unit price</th></tr></thead>
        <tbody>
        @foreach($purchaseOrder->items as $item)
            <tr><td style="padding:6px">{{ $item->product?->name }}</td><td style="padding:6px;text-align:right">{{ $item->quantity }}</td><td style="padding:6px;text-align:right">{{ number_format((float) $item->unit_price, 2) }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
