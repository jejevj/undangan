<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Template;
use App\Models\Invitation;
use App\Models\InvitationData;
use App\Models\InvitationBankAccount;
use App\Models\LoveStoryTimeline;
use App\Models\GuestMessage;
use App\Models\Guest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoInvitationSeeder extends Seeder
{
    /**
     * Seed demo invitation with complete data
     * - Published invitation for admin user
     * - Complete wedding field data
     * - Bank accounts for gift section
     * - Love story timeline with timeskip
     * - Guests with phone numbers
     * - Guest messages with likes
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('email', 'admin@undangan.test')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Skipping invitation seeder.');
            return;
        }

        // Get template
        $template = Template::where('slug', 'premium-white-1')->first();
        if (!$template) {
            $this->command->warn('Template premium-white-1 not found. Skipping invitation seeder.');
            return;
        }

        // Create invitation
        $invitation = Invitation::updateOrCreate(
            ['slug' => 'budi-siti'],
            [
                'user_id' => $admin->id,
                'template_id' => $template->id,
                'title' => 'Pernikahan Budi & Siti',
                'status' => 'published',
                'published_at' => now(),
                'gallery_display' => 'grid',
                'gift_enabled' => true,
                'love_story_mode' => 'timeline',
            ]
        );

        // Invitation Data (Field values)
        $fieldData = [
            // Mempelai Pria
            'groom_name' => 'Budi Santoso, S.Kom',
            'groom_nickname' => 'Budi',
            'groom_father' => 'Bapak Santoso',
            'groom_mother' => 'Ibu Sumiati',
            
            // Mempelai Wanita
            'bride_name' => 'Siti Nurhaliza, S.Pd',
            'bride_nickname' => 'Siti',
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
        ];

        foreach ($fieldData as $key => $value) {
            $field = $template->fields()->where('key', $key)->first();
            if ($field) {
                InvitationData::updateOrCreate(
                    [
                        'invitation_id' => $invitation->id,
                        'template_field_id' => $field->id,
                    ],
                    ['value' => $value]
                );
            }
        }

        // Bank Accounts (Gift Section)
        InvitationBankAccount::where('invitation_id', $invitation->id)->delete();
        $banks = [
            [
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'Budi Santoso',
                'order' => 1,
            ],
            [
                'bank_name' => 'Mandiri',
                'account_number' => '9876543210',
                'account_name' => 'Siti Nurhaliza',
                'order' => 2,
            ],
        ];

        foreach ($banks as $bank) {
            InvitationBankAccount::create(array_merge($bank, [
                'invitation_id' => $invitation->id,
            ]));
        }

        // Love Story Timeline
        LoveStoryTimeline::where('invitation_id', $invitation->id)->delete();
        $timelineItems = [
            [
                'sender' => 'groom',
                'message' => 'Hai Siti! Aku Budi dari kelas sebelah. Boleh kenalan? 😊',
                'event_date' => '2020-03-10',
                'event_time' => '10:30',
                'order' => 1,
                'is_timeskip' => false,
            ],
            [
                'sender' => 'bride',
                'message' => 'Hai Budi! Boleh dong, salam kenal ya 😊',
                'event_date' => '2020-03-10',
                'event_time' => '10:35',
                'order' => 2,
                'is_timeskip' => false,
            ],
            [
                'sender' => 'groom',
                'message' => 'Siti, aku suka sama kamu. Mau jadi pacarku? ❤️',
                'event_date' => '2020-06-20',
                'event_time' => '19:00',
                'order' => 3,
                'is_timeskip' => false,
            ],
            [
                'sender' => 'bride',
                'message' => 'Iya Budi, aku mau! 💕',
                'event_date' => '2020-06-20',
                'event_time' => '19:15',
                'order' => 4,
                'is_timeskip' => false,
            ],
            [
                'sender' => 'groom',
                'is_timeskip' => true,
                'timeskip_label' => '3 tahun kemudian...',
                'order' => 5,
            ],
            [
                'sender' => 'groom',
                'message' => 'Siti, aku sudah siap untuk melanjutkan hubungan kita ke jenjang yang lebih serius. Maukah kamu menikah denganku? 💍',
                'event_date' => '2023-12-25',
                'event_time' => '20:00',
                'order' => 6,
                'is_timeskip' => false,
            ],
            [
                'sender' => 'bride',
                'message' => 'Ya Budi! Aku mau! Ini hari paling bahagia dalam hidupku! 💖',
                'event_date' => '2023-12-25',
                'event_time' => '20:05',
                'order' => 7,
                'is_timeskip' => false,
            ],
        ];

        foreach ($timelineItems as $item) {
            LoveStoryTimeline::create(array_merge($item, [
                'invitation_id' => $invitation->id,
            ]));
        }

        // Guests
        Guest::where('invitation_id', $invitation->id)->delete();
        $guests = [
            ['name' => 'Bapak Ibu Hendra', 'phone_code' => '+62', 'phone' => '81234567890', 'notes' => 'Keluarga dari pihak pria'],
            ['name' => 'Keluarga Budi Santoso', 'phone_code' => '+62', 'phone' => '81234567891', 'notes' => 'Saudara kandung'],
            ['name' => 'Pak Agus & Keluarga', 'phone_code' => '+62', 'phone' => '81234567892', 'notes' => 'Teman kantor'],
            ['name' => 'Ibu Ratna', 'phone_code' => '+62', 'phone' => '81234567893', 'notes' => 'Tetangga'],
            ['name' => 'Dimas & Pasangan', 'phone_code' => '+62', 'phone' => '81234567894', 'notes' => 'Teman kuliah'],
        ];

        foreach ($guests as $guestData) {
            Guest::create(array_merge($guestData, [
                'invitation_id' => $invitation->id,
                'slug' => Str::slug($guestData['name']),
            ]));
        }

        // Guest Messages
        GuestMessage::where('invitation_id', $invitation->id)->delete();
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
            GuestMessage::create(array_merge($msgData, [
                'invitation_id' => $invitation->id,
                'ip_address' => '127.0.0.1',
                'is_approved' => true,
            ]));
        }

        $this->command->info('✓ Demo invitation seeded successfully!');
        $this->command->info('  - Slug: budi-siti');
        $this->command->info('  - Owner: admin@undangan.test');
        $this->command->info('  - Status: published');
        $this->command->info('  - URL: http://127.0.0.1:8000/budi-siti?open=1&to=bapak-ibu-hendra');
    }
}
