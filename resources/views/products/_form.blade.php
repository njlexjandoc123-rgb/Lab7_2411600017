@php
    $isEdit = isset($product);
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $product->name ?? '') }}" required maxlength="255"
               placeholder="e.g. Introduction to Programming (Python)">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
        <input type="text" name="sku" id="sku"
               class="form-control @error('sku') is-invalid @enderror"
               value="{{ old('sku', $product->sku ?? '') }}" required maxlength="50"
               placeholder="e.g. TXT-PROG-001">
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="3"
                  class="form-control @error('description') is-invalid @enderror"
                  maxlength="1000"
                  placeholder="Optional details about the item...">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
        <select name="category" id="category"
                class="form-select @error('category') is-invalid @enderror" required>
            <option value="">Select category...</option>
            @php
                $categories = ['Textbooks', 'Stationery', 'Lab Equipment', 'Uniforms', 'Electronics', 'Sports Gear', 'ID & Accessories', 'Other'];
                $current = old('category', $product->category ?? '');
            @endphp
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ $current === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="supplier" class="form-label">Supplier</label>
        <input type="text" name="supplier" id="supplier"
               class="form-control @error('supplier') is-invalid @enderror"
               value="{{ old('supplier', $product->supplier ?? '') }}" maxlength="255"
               placeholder="e.g. Academic Press Co.">
        @error('supplier')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="quantity" class="form-label">Quantity in Stock <span class="text-danger">*</span></label>
        <input type="number" name="quantity" id="quantity" min="0"
               class="form-control @error('quantity') is-invalid @enderror"
               value="{{ old('quantity', $product->quantity ?? 0) }}" required>
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="reorder_level" class="form-label">Reorder Level <span class="text-danger">*</span></label>
        <input type="number" name="reorder_level" id="reorder_level" min="0"
               class="form-control @error('reorder_level') is-invalid @enderror"
               value="{{ old('reorder_level', $product->reorder_level ?? 10) }}" required>
        <div class="form-text">Alert when stock reaches this level</div>
        @error('reorder_level')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="unit_price" class="form-label">Unit Price ($) <span class="text-danger">*</span></label>
        <input type="number" name="unit_price" id="unit_price" min="0" step="0.01"
               class="form-control @error('unit_price') is-invalid @enderror"
               value="{{ old('unit_price', $product->unit_price ?? 0) }}" required>
        @error('unit_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
