<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\Invitation;
use App\Models\User;
use App\Models\InvitationData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BasicPreviewSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::where('slug', 'basic')->first();
        
        if (!$template) {
            $this->command->error('Template Basic tidak ditemukan!');
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
                'slug' => 'basic-preview',
            ],
            [
                'title' => 'Pernikahan Budi & Siti',
                'status' => 'published',
                'published_at' => now(),
                'gallery_display' => 'grid',
                'gift_enabled' => false, // Basic template: gift disabled by default
                'love_story_mode' => 'longtext', // Basic template: longtext mode
            ]
        );

        // Set invitation data (same as DemoInvitationSeeder but adapted for basic template)
        $dataValues = [
            // Mempelai Pria
            'groom_name' => 'Budi Santoso, S.Kom',
            'groom_nickname' => 'Budi',
            'groom_photo' => 'https://img.freepik.com/free-photo/portrait-smiling-friendly-male-waiter_171337-5266.jpg?semt=ais_hybrid&w=740&q=80',
            'groom_father' => 'Bapak Santoso',
            'groom_mother' => 'Ibu Sumiati',
            
            // Mempelai Wanita
            'bride_name' => 'Siti Nurhaliza, S.Pd',
            'bride_nickname' => 'Siti',
            'bride_photo' => 'https://static.vecteezy.com/system/resources/thumbnails/073/181/213/small/joyful-young-woman-holding-transparent-veil-over-head-in-bright-natural-light-photo.jpg',
            'bride_father' => 'Bapak Halim',
            'bride_mother' => 'Ibu Nurlaila',
            
            // Akad Nikah
            'akad_date' => '2026-05-15',
            'akad_time' => '08:00',
            'akad_venue' => 'Masjid Al-Ikhlas',
            'akad_address' => 'Jl. Merdeka No. 123, Jakarta Selatan',
            
            // Resepsi
            'reception_date' => '2026-05-15',
            'reception_time' => '11:00',
            'reception_venue' => 'Gedung Serbaguna Melati',
            'reception_address' => 'Jl. Melati Raya No. 45, Jakarta Selatan',
            
            // Tambahan
            'maps_url' => 'https://maps.google.com/?q=-6.2088,106.8456',
            'love_story' => 'Kami bertemu pertama kali di kampus pada tahun 2020. Dari pertemanan biasa, berkembang menjadi hubungan yang serius. Setelah 4 tahun bersama, kami memutuskan untuk melanjutkan ke jenjang pernikahan.',
            'cover_photo' => null,
            'music_url' => null,
            'music_title' => null,
            'music_artist' => null,
        ];

        // Save data to invitation_data table
        foreach ($dataValues as $key => $value) {
            $field = $template->fields()->where('key', $key)->first();
            if ($field) {
                InvitationData::updateOrCreate(
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

        // Guest Messages (basic template also has guest messages)
        \App\Models\GuestMessage::where('invitation_id', $invitation->id)->delete();
        $messages = [
            [
                'guest_name' => 'bapak-ibu-hendra',
                'message' => 'Selamat menempuh hidup baru Budi & Siti! Semoga menjadi keluarga yang sakinah, mawaddah, warahmah. Barakallah! 🤲',
                'likes_count' => 15,
                'created_at' => now()->subDays(2),
            ],
            [
                'guest_name' => 'keluarga-budi-santoso',
                'message' => 'Selamat ya adikku tersayang! Semoga langgeng sampai kakek nenek. Love you! ❤️',
                'likes_count' => 12,
                'created_at' => now()->subDays(1),
            ],
            [
                'guest_name' => 'ibu-ratna',
                'message' => 'MasyaAllah, senang sekali melihat kalian menikah. Semoga diberkahi Allah SWT. Aamiin 🤲',
                'likes_count' => 10,
                'created_at' => now()->subHours(6),
            ],
            [
                'guest_name' => 'pak-agus-keluarga',
                'message' => 'Congratulations Budi! Semoga bahagia selalu bersama Siti. Sukses untuk kalian berdua! 🎉',
                'likes_count' => 8,
                'created_at' => now()->subHours(12),
            ],
            [
                'guest_name' => 'dimas-pasangan',
                'message' => 'Bro Budi, akhirnya lo nikah juga! Hahaha. Selamat ya bro, semoga bahagia! 🎊',
                'likes_count' => 5,
                'created_at' => now()->subHours(2),
            ],
        ];

        foreach ($messages as $msgData) {
            \App\Models\GuestMessage::create(array_merge($msgData, [
                'invitation_id' => $invitation->id,
                'ip_address' => '127.0.0.1',
                'is_approved' => true,
            ]));
        }

        // Update template with preview URL (with demo user parameter)
        $previewUrl = route('invitation.show', ['slug' => $invitation->slug]) . '?to=demo-user';
        $template->update(['preview_url' => $previewUrl]);

        $this->command->info("Preview invitation untuk template 'Basic' berhasil dibuat!");
        $this->command->info("Preview URL: {$previewUrl}");
    }
}
