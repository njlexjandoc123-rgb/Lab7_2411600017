<div style="font-family:Arial,sans-serif;color:#1e293b">
    <h2 style="color:#b45309">Automatic replenishment check</h2>
    <p>Draft purchase orders were generated for products that reached their calculated reorder points. Please review and approve the purchase orders before sending them.</p>
    <ul>
        @foreach($products as $product)
            <li>{{ $product->name }} ({{ $product->sku }}): {{ $product->qty }} in stock; supplier {{ $product->supplier?->name ?? 'unassigned' }}</li>
        @endforeach
    </ul>
</div>
