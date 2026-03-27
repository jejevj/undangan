<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TemplateField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::withCount('invitations')->get();
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'blade_view' => 'required|string|max:100',
            'thumbnail'  => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'description', 'blade_view', 'is_active');
        $data['slug']      = Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        Template::create($data);
        return redirect()->route('templates.index')->with('success', 'Template berhasil dibuat.');
    }

    public function show(Template $template)
    {
        $template->load(['fields' => fn($q) => $q->orderBy('order')]);
        return view('templates.show', compact('template'));
    }

    public function edit(Template $template)
    {
        $template->load(['fields' => fn($q) => $q->orderBy('order')]);
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'blade_view' => 'required|string|max:100',
            'thumbnail'  => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'description', 'blade_view');
        $data['slug']      = Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        $template->update($data);
        return redirect()->route('templates.index')->with('success', 'Template berhasil diupdate.');
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus.');
    }

    // --- Field Management ---

    public function storeField(Request $request, Template $template)
    {
        $request->validate([
            'key'   => 'required|string|alpha_dash|max:50',
            'label' => 'required|string|max:100',
            'type'  => 'required|in:text,textarea,date,time,datetime,image,url,number,select',
            'group' => 'nullable|string|max:50',
            'order' => 'integer',
        ]);

        $template->fields()->create($request->only(
            'key', 'label', 'type', 'options', 'required',
            'placeholder', 'default_value', 'group', 'order'
        ));

        return redirect()->route('templates.edit', $template)->with('success', 'Field berhasil ditambahkan.');
    }

    public function destroyField(Template $template, TemplateField $field)
    {
        $field->delete();
        return redirect()->route('templates.edit', $template)->with('success', 'Field berhasil dihapus.');
    }
}
