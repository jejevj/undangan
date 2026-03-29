<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SannoPreviewSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::where('slug', 'sanno')->first();
        
        if (!$template) {
            $this->command->error('Template Sanno tidak ditemukan!');
            return;
        }

        // Find or create preview user
        $previewUser = User::firstOrCreate(
            ['email' => 'preview@system.local'],
            [
                'name' => 'Preview System',
                'password' => bcrypt('preview-system-' . Str::random(16)),
            ]
        );

        // Create or update preview invitation
        $invitation = Invitation::updateOrCreate(
            [
                'template_id' => $template->id,
                'user_id' => $previewUser->id,
                'slug' => 'sanno-preview',
            ],
            [
                'title' => 'Grand Opening - Tempered Glass Factory',
                'is_published' => true,
                'gallery_display' => 'grid',
                'gift_enabled' => false,
            ]
        );

        // Set invitation data using InvitationData model
        $dataValues = [
            'company_logo' => null,
            'event_title' => 'Grand Opening',
            'event_subtitle' => 'Of our new Tempered Glass Factory',
            'event_description' => 'We are pleased to invite you to the grand opening of our new state-of-the-art tempered glass manufacturing facility in South Sulawesi.',
            'event_date' => '2026-12-29',
            'event_time' => '10:00',
            'event_venue' => 'Kawasan Industri Makassar',
            'event_address' => 'Jl. Kima XIV Kav. SS-14, Daya, Kec. Biringkanaya, Kota Makassar, Sulawesi Selatan 90242',
            'maps_url' => 'https://maps.google.com/',
            'rsvp_note' => 'Setiap instansi maksimal 2 orang perwakilan. Mohon konfirmasi kehadiran Anda untuk membantu kami mempersiapkan acara dengan lebih baik.',
            'qr_note' => 'Jangan lupa mengisi konfirmasi kehadiran untuk mendapatkan QR Code. Gunakan QR-Code untuk melakukan check-in dan penukaran souvenir.',
            'cover_photo' => null,
        ];

        // Save data to invitation_data table
        foreach ($dataValues as $key => $value) {
            $field = $template->fields()->where('key', $key)->first();
            if ($field) {
                \App\Models\InvitationData::updateOrCreate(
                    [
                        'invitation_id' => $invitation->id,
                        'template_field_id' => $field->id,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        }

        // Update template with preview URL
        $previewUrl = route('invitation.show', ['slug' => $invitation->slug]);
        $template->update(['preview_url' => $previewUrl]);

        $this->command->info("Preview invitation untuk template 'Sanno' berhasil dibuat!");
        $this->command->info("Preview URL: {$previewUrl}");
    }
}
