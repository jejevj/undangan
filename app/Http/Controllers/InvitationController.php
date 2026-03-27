<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Invitation;
use App\Models\InvitationData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with(['template', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('invitations.index', compact('invitations'));
    }

    /**
     * Pilih template sebelum membuat undangan
     */
    public function selectTemplate()
    {
        $templates = Template::where('is_active', true)->get();
        return view('invitations.select-template', compact('templates'));
    }

    /**
     * Form isi data undangan berdasarkan template yang dipilih
     */
    public function create(Request $request)
    {
        $request->validate(['template_id' => 'required|exists:templates,id']);

        $template = Template::with(['fields' => fn($q) => $q->orderBy('order')])->findOrFail($request->template_id);
        $fieldsByGroup = $template->fields->groupBy('group');

        return view('invitations.create', compact('template', 'fieldsByGroup'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
            'title'       => 'required|string|max:255',
        ]);

        $template = Template::with('fields')->findOrFail($request->template_id);

        // Validasi field required dari template
        $rules = ['title' => 'required|string|max:255'];
        foreach ($template->fields->where('required', true) as $field) {
            $rules['fields.' . $field->key] = 'required';
        }
        $request->validate($rules);

        // Buat invitation
        $invitation = Invitation::create([
            'user_id'     => auth()->id(),
            'template_id' => $template->id,
            'slug'        => Str::uuid(),
            'title'       => $request->title,
            'status'      => 'draft',
        ]);

        // Simpan data field
        $this->saveInvitationData($invitation, $template, $request->input('fields', []));

        return redirect()->route('invitations.edit', $invitation)
            ->with('success', 'Undangan berhasil dibuat. Silakan lengkapi data dan preview.');
    }

    public function edit(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $template = $invitation->template()->with(['fields' => fn($q) => $q->orderBy('order')])->first();
        $fieldsByGroup = $template->fields->groupBy('group');

        // Map existing data: key => value
        $existingData = $invitation->data->mapWithKeys(fn($d) => [
            $d->templateField->key => $d->value
        ])->toArray();

        return view('invitations.edit', compact('invitation', 'template', 'fieldsByGroup', 'existingData'));
    }

    public function update(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $request->validate(['title' => 'required|string|max:255']);

        $template = $invitation->template()->with('fields')->first();

        $rules = [];
        foreach ($template->fields->where('required', true) as $field) {
            $rules['fields.' . $field->key] = 'required';
        }
        if ($rules) $request->validate($rules);

        $invitation->update(['title' => $request->title]);

        $this->saveInvitationData($invitation, $template, $request->input('fields', []));

        return redirect()->route('invitations.edit', $invitation)->with('success', 'Data undangan berhasil disimpan.');
    }

    /**
     * Preview undangan menggunakan blade view dari template
     */
    public function preview(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $invitation->load(['data.templateField', 'template']);
        $data = $invitation->getDataMap();

        return view($invitation->template->blade_view, compact('invitation', 'data'));
    }

    /**
     * Halaman publik undangan (tanpa auth)
     */
    public function show(string $slug)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $invitation->load(['data.templateField', 'template']);
        $data = $invitation->getDataMap();

        return view($invitation->template->blade_view, compact('invitation', 'data'));
    }

    public function publish(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $invitation->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        return redirect()->route('invitations.edit', $invitation)
            ->with('success', 'Undangan berhasil dipublikasikan. Link: ' . route('invitation.show', $invitation->slug));
    }

    public function unpublish(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);
        $invitation->update(['status' => 'draft']);
        return redirect()->route('invitations.edit', $invitation)->with('success', 'Undangan dikembalikan ke draft.');
    }

    public function destroy(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);
        $invitation->delete();
        return redirect()->route('invitations.index')->with('success', 'Undangan berhasil dihapus.');
    }

    // --- Private helpers ---

    private function authorizeInvitation(Invitation $invitation): void
    {
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }

    private function saveInvitationData(Invitation $invitation, Template $template, array $fields): void
    {
        foreach ($template->fields as $field) {
            $value = $fields[$field->key] ?? null;

            InvitationData::updateOrCreate(
                [
                    'invitation_id'     => $invitation->id,
                    'template_field_id' => $field->id,
                ],
                ['value' => $value]
            );
        }
    }
}
