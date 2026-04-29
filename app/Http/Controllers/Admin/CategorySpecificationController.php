<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategorySpecification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategorySpecificationController extends Controller
{
    public function index(Category $category)
    {
        // Cek apakah category ada
        if (!$category) {
            abort(404, 'Kategori tidak ditemukan');
        }
        
        $specifications = $category->specifications()
                                   ->ordered()
                                   ->get();
        
        // Debug: cek data
        // dd($category->toArray(), $specifications->toArray());
        
        return view('admin.categories.specifications.index', compact('category', 'specifications'));
    }

    public function store(Request $request, Category $category)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,textarea,date,boolean,select',
            'options' => 'nullable|array|required_if:type,select',
            'options.*.value' => 'required_with:options|string',
            'options.*.label' => 'required_with:options|string',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        // Auto-generate key dari label (bisa diedit manual jika perlu)
        $key = Str::slug($validated['label'], '_');
        
        // Cek duplikasi key
        $counter = 1;
        $originalKey = $key;
        while ($category->specifications()->where('key', $key)->exists()) {
            $key = $originalKey . '_' . $counter;
            $counter++;
        }

        $category->specifications()->create([
            'key' => $key,
            'label' => $validated['label'],
            'type' => $validated['type'],
            'options' => $validated['options'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'placeholder' => $validated['placeholder'],
            'help_text' => $validated['help_text'],
            'sort_order' => $validated['sort_order'] ?? 0
        ]);

        return back()->with('success', 'Spesifikasi berhasil ditambahkan');
    }

    public function update(Request $request, Category $category, CategorySpecification $specification)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,textarea,date,boolean,select',
            'options' => 'nullable|array|required_if:type,select',
            'options.*.value' => 'required_with:options|string',
            'options.*.label' => 'required_with:options|string',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $specification->update([
            'label' => $validated['label'],
            'type' => $validated['type'],
            'options' => $validated['options'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'placeholder' => $validated['placeholder'],
            'help_text' => $validated['help_text'],
            'sort_order' => $validated['sort_order'] ?? 0
        ]);

        return back()->with('success', 'Spesifikasi berhasil diupdate');
    }

    public function destroy(Category $category, CategorySpecification $specification)
    {
        $specification->delete();
        return back()->with('success', 'Spesifikasi berhasil dihapus');
    }

    public function toggleActive(Category $category, CategorySpecification $specification)
    {
        $specification->update(['is_active' => !$specification->is_active]);
        
        return response()->json([
            'success' => true,
            'is_active' => $specification->is_active,
            'message' => 'Status spesifikasi berhasil diubah'
        ]);
    }

    public function updateOrder(Request $request, Category $category)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|exists:category_specifications,id'
        ]);

        foreach ($request->orders as $index => $id) {
            CategorySpecification::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan berhasil diupdate']);
    }
}