<div style="font-family:Arial,sans-serif;color:#1e293b">
    <h2 style="color:#047857">Purchase order approved</h2>
    <p>Purchase order <strong>{{ $purchaseOrder->reference_number }}</strong> for {{ $purchaseOrder->supplier?->name }} was approved.</p>
    <p>Total: {{ number_format((float) $purchaseOrder->total_amount, 2) }} · Expected delivery: {{ $purchaseOrder->expected_delivery_date?->format('M d, Y') ?? 'Not set' }}</p>
</div>
