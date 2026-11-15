<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
        ]);

        DB::transaction(function () use ($category, $validated): void {
            $category->products()->update(['category' => $validated['name']]);
            $category->update($validated);
        });

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        DB::transaction(function () use ($category): void {
            $category->products()->update(['category' => null]);
            $category->delete();
        });

        return redirect()->route('categories.index')->with('success', 'Category deleted. Its products are now unassigned.');
    }
}
