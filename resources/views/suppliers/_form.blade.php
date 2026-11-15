<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Company name *</label>
        <input name="name" required maxlength="255" value="{{ old('name', $supplier->name) }}" class="w-full rounded border px-3 py-2">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Contact person</label>
        <input name="contact_person" maxlength="255" value="{{ old('contact_person', $supplier->contact_person) }}" class="w-full rounded border px-3 py-2">
        @error('contact_person') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Email</label>
        <input type="email" name="email" maxlength="255" value="{{ old('email', $supplier->email) }}" class="w-full rounded border px-3 py-2">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Phone</label>
        <input name="phone" maxlength="40" value="{{ old('phone', $supplier->phone) }}" class="w-full rounded border px-3 py-2">
        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Payment terms *</label>
        <input name="payment_terms" required maxlength="100" value="{{ old('payment_terms', $supplier->payment_terms ?? 'COD') }}" placeholder="Net 30, COD" class="w-full rounded border px-3 py-2">
        @error('payment_terms') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Lead time (days) *</label>
        <input type="number" name="lead_time_days" required min="0" max="3650" value="{{ old('lead_time_days', $supplier->lead_time_days ?? 7) }}" class="w-full rounded border px-3 py-2">
        @error('lead_time_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Minimum order amount *</label>
        <input type="number" name="minimum_order_amount" required min="0" step="0.01" value="{{ old('minimum_order_amount', $supplier->minimum_order_amount ?? 0) }}" class="w-full rounded border px-3 py-2">
        @error('minimum_order_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Status</label>
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-2 rounded border px-3 py-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $supplier->is_active ?? true))>
            <span>Active supplier</span>
        </label>
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium">Physical address</label>
        <textarea name="address" rows="3" maxlength="2000" class="w-full rounded border px-3 py-2">{{ old('address', $supplier->address) }}</textarea>
        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
<div class="mt-6 flex justify-between">
    <a href="{{ route('suppliers.index') }}" class="rounded border px-4 py-2 text-sm">Cancel</a>
    <button class="rounded bg-sky-700 px-5 py-2 text-sm font-semibold text-white hover:bg-sky-800">Save supplier</button>
</div>
