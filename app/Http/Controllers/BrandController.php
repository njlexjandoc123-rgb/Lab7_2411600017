<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('brands', 'name')->ignore($brand->id)],
        ]);

        DB::transaction(function () use ($brand, $validated): void {
            $brand->products()->update(['brand' => $validated['name']]);
            $brand->update($validated);
        });

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        DB::transaction(function () use ($brand): void {
            $brand->products()->update(['brand' => null]);
            $brand->delete();
        });

        return redirect()->route('brands.index')->with('success', 'Brand deleted. Its products are now unassigned.');
    }
}
