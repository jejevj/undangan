<?php

namespace App\Http\Controllers;

use App\Models\TemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateCategoryController extends Controller
{
    public function index()
    {
        $categories = TemplateCategory::withCount('templates')->orderBy('order')->get();
        return view('template-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('template-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:template_categories,slug',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        TemplateCategory::create($validated);

        return redirect()->route('template-categories.index')
            ->with('success', 'Kategori template berhasil ditambahkan');
    }

    public function edit(TemplateCategory $templateCategory)
    {
        return view('template-categories.edit', compact('templateCategory'));
    }

    public function update(Request $request, TemplateCategory $templateCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:template_categories,slug,' . $templateCategory->id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $templateCategory->update($validated);

        return redirect()->route('template-categories.index')
            ->with('success', 'Kategori template berhasil diperbarui');
    }

    public function destroy(TemplateCategory $templateCategory)
    {
        // Check if category has templates
        if ($templateCategory->templates()->count() > 0) {
            return redirect()->route('template-categories.index')
                ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki template');
        }

        $templateCategory->delete();

        return redirect()->route('template-categories.index')
            ->with('success', 'Kategori template berhasil dihapus');
    }
}
