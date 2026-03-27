<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TemplateField;
use App\Support\TemplateFieldPreset;
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
        $presets = TemplateFieldPreset::all();
        return view('templates.create', compact('presets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'asset_folder' => 'required|string|max:100|alpha_dash',
            'blade_view'   => 'required|string|max:100',
            'thumbnail'    => 'nullable|image|max:2048',
            'field_preset' => 'nullable|string',
            'category_id'  => 'nullable|exists:template_categories,id',
        ]);

        $data = $request->only('name', 'description', 'blade_view', 'type', 'price', 'asset_folder', 'version', 'preview_url', 'category_id');
        $data['slug']             = Str::slug($request->name);
        $data['is_active']        = $request->boolean('is_active', true);
        $data['free_photo_limit'] = $request->filled('free_photo_limit') ? (int) $request->free_photo_limit : null;
        $data['extra_photo_price']= (int) ($request->extra_photo_price ?? 5000);
        $data['gift_feature_price'] = (int) ($request->gift_feature_price ?? 10000);
        $data['guest_limit']      = $request->filled('guest_limit') ? (int) $request->guest_limit : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        $template = Template::create($data);

        // Load default fields dari preset yang dipilih
        if ($request->field_preset && $request->field_preset !== 'empty') {
            foreach (TemplateFieldPreset::get($request->field_preset) as $field) {
                $template->fields()->create(array_merge($field, ['template_id' => $template->id]));
            }
        }

        return redirect()->route('templates.edit', $template)
            ->with('success', 'Template berhasil dibuat.' . ($request->field_preset !== 'empty' ? ' Default fields sudah dimuat.' : ''));
    }

    public function show(Template $template)
    {
        $template->load(['fields' => fn($q) => $q->orderBy('order')]);
        return view('templates.show', compact('template'));
    }

    public function edit(Template $template)
    {
        $template->load(['fields' => fn($q) => $q->orderBy('order')]);
        $presets = TemplateFieldPreset::all();
        return view('templates.edit', compact('template', 'presets'));
    }

    public function update(Request $request, Template $template)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'asset_folder' => 'required|string|max:100|alpha_dash',
            'blade_view'   => 'required|string|max:100',
            'thumbnail'    => 'nullable|image|max:2048',
            'category_id'  => 'nullable|exists:template_categories,id',
        ]);

        $data = $request->only('name', 'description', 'blade_view', 'type', 'price', 'asset_folder', 'version', 'preview_url', 'category_id');
        $data['slug']             = Str::slug($request->name);
        $data['is_active']        = $request->boolean('is_active');
        $data['free_photo_limit'] = $request->filled('free_photo_limit') ? (int) $request->free_photo_limit : null;
        $data['extra_photo_price']= (int) ($request->extra_photo_price ?? 5000);
        $data['gift_feature_price'] = (int) ($request->gift_feature_price ?? 10000);
        $data['guest_limit']      = $request->filled('guest_limit') ? (int) $request->guest_limit : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        $template->update($data);
        return redirect()->route('templates.edit', $template)->with('success', 'Template berhasil diupdate.');
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus.');
    }

    public function toggle(Template $template)
    {
        $template->update(['is_active' => !$template->is_active]);
        $status = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Template '{$template->name}' berhasil {$status}.");
    }

    /**
     * Load preset fields ke template yang sudah ada.
     * Field yang sudah ada (key sama) tidak akan ditimpa.
     */
    public function loadPreset(Request $request, Template $template)
    {
        $request->validate(['preset' => 'required|string']);

        $fields = TemplateFieldPreset::get($request->preset);

        if (empty($fields)) {
            return back()->with('error', 'Preset tidak ditemukan.');
        }

        $added = 0;
        foreach ($fields as $field) {
            if (!$template->fields()->where('key', $field['key'])->exists()) {
                $template->fields()->create(array_merge($field, ['template_id' => $template->id]));
                $added++;
            }
        }

        return back()->with('success', "{$added} field berhasil dimuat dari preset.");
    }

    // ── Field Management ──────────────────────────────────────────────

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
