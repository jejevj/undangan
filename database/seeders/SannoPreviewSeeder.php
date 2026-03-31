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
                'status' => 'published',
                'published_at' => now(),
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
            'music_url' => 'invitation-assets/music/wedding-song.mp3',
            'music_title' => 'Wedding Song',
            'music_artist' => 'Instrumental',
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

        // Gallery Photos (using external URLs)
        // Only delete photos linked to this specific invitation
        \App\Models\InvitationGallery::where('invitation_id', $invitation->id)->delete();
        
        $galleryPhotos = [
            'https://images.unsplash.com/photo-1519741497674-611481863552?w=800&q=80',
            'https://images.unsplash.com/photo-1606800052052-a08af7148866?w=800&q=80',
            'https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=800&q=80',
            'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=800&q=80',
            'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=800&q=80',
            'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=800&q=80',
        ];

        foreach ($galleryPhotos as $index => $photoUrl) {
            // Create user gallery photo
            $userPhoto = \App\Models\UserGalleryPhoto::create([
                'user_id' => $previewUser->id,
                'path' => $photoUrl, // Store URL directly
                'caption' => 'Preview Photo ' . ($index + 1),
            ]);

            // Link to invitation
            \App\Models\InvitationGallery::create([
                'invitation_id' => $invitation->id,
                'photo_id' => $userPhoto->id,
                'order' => $index,
            ]);
        }

        // Update template with preview URL (with demo user parameter)
        $previewUrl = route('invitation.show', ['slug' => $invitation->slug]) . '?to=demo-user';
        $template->update(['preview_url' => $previewUrl]);

        // Generate thumbnail
        \Artisan::call('templates:generate-thumbnails', [
            '--template' => $template->slug,
            '--force' => true,
        ]);

        $this->command->info("Preview invitation untuk template 'Sanno' berhasil dibuat!");
        $this->command->info("Preview URL: {$previewUrl}");
    }
}
