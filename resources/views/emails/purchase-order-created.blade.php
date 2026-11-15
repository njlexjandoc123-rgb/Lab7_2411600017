<div style="font-family:Arial,sans-serif;color:#1e293b">
    <h2 style="color:#075985">Purchase order created</h2>
    <p>Purchase order <strong>{{ $purchaseOrder->reference_number }}</strong> has been created for {{ $purchaseOrder->supplier?->name }}.</p>
    <p>Status: {{ ucfirst($purchaseOrder->status) }} · Total: {{ number_format((float) $purchaseOrder->total_amount, 2) }}</p>
    <p>Open the inventory system to review or approve this order.</p>
</div>
