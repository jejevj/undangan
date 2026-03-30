<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Invitation;
use App\Models\InvitationData;
use App\Models\Music;
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
    public function selectTemplate(Request $request)
    {
        // Cek limit undangan berdasarkan plan
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->canCreateInvitation()) {
            $plan = $user->activePlan();
            return redirect()->route('subscription.index')
                ->with('error', "Batas undangan paket {$plan->name} ({$plan->max_invitations} undangan) sudah tercapai. Upgrade paket untuk membuat lebih banyak undangan.");
        }

        // Get categories for filter
        $categories = \App\Models\TemplateCategory::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Initial templates load
        $templates = Template::where('is_active', true)
            ->with('category')
            ->get();

        return view('invitations.select-template', compact('templates', 'categories'));
    }

    /**
     * AJAX endpoint untuk filter template di dashboard
     */
    public function getTemplates(Request $request)
    {
        $query = Template::where('is_active', true)->with('category');

        // Filter by category
        if ($request->category && $request->category !== 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by type
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $templates = $query->get();

        return view('invitations.partials.template-grid', compact('templates'));
    }

    /**
     * Form isi data undangan berdasarkan template yang dipilih
     */
    public function create(Request $request)
    {
        $request->validate(['template_id' => 'required|exists:templates,id']);

        $template      = Template::with(['fields' => fn($q) => $q->orderBy('order')])->findOrFail($request->template_id);
        $fieldsByGroup = $template->fields->groupBy('group');
        $accessibleMusic = Music::accessibleByUser(auth()->user());

        return view('invitations.create', compact('template', 'fieldsByGroup', 'accessibleMusic'));
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
            'slug'        => $this->generateInvitationSlug($request->input('fields', [])),
            'title'       => $request->title,
            'greeting'    => $request->greeting,
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

        $invitation->load('loveStoryTimeline');
        $template      = $invitation->template()->with(['fields' => fn($q) => $q->orderBy('order')])->first();
        $fieldsByGroup = $template->fields->groupBy('group');
        $accessibleMusic = Music::accessibleByUser(auth()->user());

        $existingData = $invitation->data->mapWithKeys(fn($d) => [
            $d->templateField->key => $d->value
        ])->toArray();

        return view('invitations.edit', compact('invitation', 'template', 'fieldsByGroup', 'existingData', 'accessibleMusic'));
    }

    public function update(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $request->validate(['title' => 'required|string|max:255']);

        $template = $invitation->template()->with('fields')->first();

        // Build validation rules
        $rules = [];
        foreach ($template->fields as $field) {
            if ($field->required) {
                // For image fields, only require if no existing value
                if ($field->type === 'image') {
                    $existingValue = $invitation->getValue($field->key);
                    if (!$existingValue) {
                        $rules['fields.' . $field->key] = 'required|image|max:2048';
                    } else {
                        $rules['fields.' . $field->key] = 'nullable|image|max:2048';
                    }
                } else {
                    $rules['fields.' . $field->key] = 'required';
                }
            } else if ($field->type === 'image') {
                // Optional image fields
                $rules['fields.' . $field->key] = 'nullable|image|max:2048';
            }
        }
        
        if ($rules) $request->validate($rules);

        $invitation->update([
            'title' => $request->title,
            'greeting' => $request->greeting,
            'gallery_display' => $request->gallery_display ?? 'grid'
        ]);

        $this->saveInvitationData($invitation, $template, $request->input('fields', []));

        return redirect()->route('invitations.edit', $invitation)->with('success', 'Data undangan berhasil disimpan.');
    }

    /**
     * Preview undangan menggunakan blade view dari template
     */
    public function preview(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $invitation->load(['data.templateField', 'template', 'gallery.photo', 'bankAccounts', 'loveStoryTimeline']);
        $data    = $invitation->getDataMap();
        $gallery = $invitation->gallery;

        // Debug: Log data
        \Log::info('Preview - Data loaded', [
            'groom_name' => $data['groom_name'] ?? 'NOT SET',
            'bride_name' => $data['bride_name'] ?? 'NOT SET',
            'data_count' => count($data),
        ]);

        // Debug: Log template info
        \Log::info('Preview - Template Name: ' . $invitation->template->name);
        \Log::info('Preview - Template Blade View: ' . $invitation->template->blade_view);
        \Log::info('Preview - Template View Path: ' . $invitation->template->viewPath());

        // Render template content
        $templateContent = view($invitation->template->viewPath(), compact('invitation', 'data', 'gallery'))->render();
        
        // Debug: Check if data-editable exists in rendered HTML
        $editableCount = substr_count($templateContent, 'data-editable');
        \Log::info('Preview - data-editable count in HTML: ' . $editableCount);
        
        // Check if user is owner/admin
        $isOwner = $invitation->user_id === auth()->id() || auth()->user()->hasRole('admin');
        
        // Debug: Log injection status
        \Log::info('Preview - Invitation ID: ' . $invitation->id);
        \Log::info('Preview - User ID: ' . auth()->id());
        \Log::info('Preview - Invitation Owner ID: ' . $invitation->user_id);
        \Log::info('Preview - Is Owner: ' . ($isOwner ? 'YES' : 'NO'));
        \Log::info('Preview - Will inject live edit: ' . ($isOwner ? 'YES' : 'NO'));
        
        if ($isOwner) {
            // Inject live edit support directly into template HTML
            $templateContent = $this->injectLiveEditSupport($templateContent, $invitation);
            \Log::info('Preview - Live edit injected');
        }
        
        return response($templateContent);
    }
    
    /**
     * Inject live edit support into template HTML
     */
    private function injectLiveEditSupport(string $html, Invitation $invitation): string
    {
        // Add CSRF token if not present
        if (strpos($html, 'name="csrf-token"') === false) {
            $csrfMeta = '<meta name="csrf-token" content="' . csrf_token() . '">';
            $html = preg_replace('/(<head[^>]*>)/i', '$1' . PHP_EOL . '    ' . $csrfMeta, $html);
        }
        
        // Add debug script
        $debugScript = '<script>console.log("Live Edit: Injection successful for invitation ' . $invitation->id . '");</script>';
        
        // Add live edit scripts before </head>
        $liveEditScript = $debugScript . PHP_EOL . 
            '    <script src="' . asset('assets/js/live-edit.js') . '"></script>' . PHP_EOL .
            '    <script src="' . asset('assets/js/live-edit-form-panel.js') . '"></script>';
        $html = preg_replace('/(<\/head>)/i', '    ' . $liveEditScript . PHP_EOL . '$1', $html);
        
        // Add data attributes to body tag
        $dataAttrs = 'data-invitation-id="' . $invitation->id . '" data-is-owner="true"';
        $html = preg_replace('/(<body[^>]*)(>)/i', '$1 ' . $dataAttrs . '$2', $html);
        
        return $html;
    }

    /**
     * Halaman publik undangan (tanpa auth)
     * - Tanpa ?to=slug  → cover generik tanpa nama tamu
     * - Dengan ?to=slug → cover dengan nama tamu
     * - Dengan ?open=1  → langsung ke detail undangan
     */
    public function show(Request $request, string $slug)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $invitation->load(['data.templateField', 'template', 'gallery.photo', 'bankAccounts', 'loveStoryTimeline', 'guestMessages']);
        $data    = $invitation->getDataMap();
        $gallery = $invitation->gallery;

        // Resolve nama tamu dari ?to parameter
        $guestName = null;
        $toParam   = $request->query('to');

        if ($toParam) {
            // Format guest name from URL parameter (e.g., "bapak-ibu-hendra" -> "Bapak Ibu Hendra")
            $guestName = ucwords(str_replace('-', ' ', $toParam));
        }

        if ($request->boolean('open')) {
            return view($invitation->template->viewPath(), compact('invitation', 'data', 'gallery', 'guestName'));
        }

        // Resolve nama tamu dari ?to parameter
        $guestName = null;
        $toParam   = $request->query('to');

        if ($toParam) {
            // Format guest name from URL parameter (e.g., "bapak-ibu-hendra" -> "Bapak Ibu Hendra")
            $guestName = ucwords(str_replace('-', ' ', $toParam));
        }

        // URL tombol "Buka Undangan" — pertahankan ?to agar terbawa
        $invitationUrl = route('invitation.show', $slug)
            . '?open=1'
            . ($toParam ? '&to=' . urlencode($toParam) : '');

        return view('invitation-templates.cover', compact('invitation', 'data', 'guestName', 'invitationUrl'));
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
        
        // Cek permission delete-invitations
        if (!auth()->user()->can('delete-invitations')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus undangan.');
        }
        
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

            // Handle file upload for image type fields
            if ($field->type === 'image' && request()->hasFile('fields.' . $field->key)) {
                $file = request()->file('fields.' . $field->key);
                
                // Delete old file if exists
                $oldData = InvitationData::where('invitation_id', $invitation->id)
                    ->where('template_field_id', $field->id)
                    ->first();
                    
                if ($oldData && $oldData->value) {
                    \Storage::disk('public')->delete($oldData->value);
                }
                
                // Store new file
                $path = $file->store('invitations/' . $invitation->id, 'public');
                $value = $path;
            }

            InvitationData::updateOrCreate(
                [
                    'invitation_id'     => $invitation->id,
                    'template_field_id' => $field->id,
                ],
                ['value' => $value]
            );
        }
    }

    /**
     * Generate slug dari nickname kedua mempelai
     * Format: prianickname-wanitanickname
     * Fallback ke UUID jika nickname tidak tersedia
     */
    private function generateInvitationSlug(array $fields): string
    {
        $groomNickname = $fields['groom_nickname'] ?? $fields['groom_name'] ?? null;
        $brideNickname = $fields['bride_nickname'] ?? $fields['bride_name'] ?? null;

        if ($groomNickname && $brideNickname) {
            $slug = Str::slug($groomNickname) . '-' . Str::slug($brideNickname);
            
            // Cek apakah slug sudah digunakan
            $count = 1;
            $originalSlug = $slug;
            while (Invitation::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            
            return $slug;
        }

        // Fallback ke UUID jika nickname tidak tersedia
        return Str::uuid();
    }
}
