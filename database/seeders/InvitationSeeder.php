<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Template;
use App\Models\Invitation;
use App\Models\InvitationData;
use App\Models\Guest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvitationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@undangan.test')->firstOrFail();

        // Hapus semua undangan lama milik admin (idempotent)
        Invitation::where('user_id', $admin->id)->delete();

        // ── 1. Undangan Premium White 1 ───────────────────────────────
        $this->seedPremiumWhite1($admin);

        // ── 2. Undangan Basic ─────────────────────────────────────────
        $this->seedBasic($admin);
    }

    private function seedPremiumWhite1(User $admin): void
    {
        $template = Template::where('slug', 'premium-white-1')->firstOrFail();

        $invitation = Invitation::create([
            'user_id'      => $admin->id,
            'template_id'  => $template->id,
            'slug'         => Str::uuid(),
            'title'        => 'Pernikahan Budi Santoso & Ani Rahayu',
            'status'       => 'published',
            'published_at' => now(),
            'greeting'     => "Kepada Yth.\n{nama_tamu}\n\nDengan penuh kebahagiaan, kami mengundang Anda untuk hadir dan memberikan doa restu di hari pernikahan kami.\n\nSilakan buka undangan digital kami melalui tautan berikut:\n{link}\n\nMerupakan suatu kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir.\n\nHormat kami,\nBudi & Ani",
        ]);

        $fieldValues = [
            'groom_name'        => 'Budi Santoso, S.T.',
            'groom_nickname'    => 'Budi',
            'groom_photo'       => null,
            'groom_father'      => 'Bapak Hendra Santoso',
            'groom_mother'      => 'Ibu Sri Wahyuni',
            'bride_name'        => 'Ani Rahayu, S.Pd.',
            'bride_nickname'    => 'Ani',
            'bride_photo'       => null,
            'bride_father'      => 'Bapak Agus Rahayu',
            'bride_mother'      => 'Ibu Dewi Lestari',
            'akad_date'         => '2026-06-14',
            'akad_time'         => '08:00',
            'akad_venue'        => 'Masjid Al-Ikhlas',
            'akad_address'      => 'Jl. Mawar No. 12, Kelurahan Sukamaju, Bandung',
            'reception_date'    => '2026-06-14',
            'reception_time'    => '11:00',
            'reception_venue'   => 'Gedung Serbaguna Graha Indah',
            'reception_address' => 'Jl. Melati No. 45, Bandung',
            'maps_url'          => 'https://maps.google.com',
            'love_story'        => 'Kami pertama kali bertemu di bangku kuliah pada tahun 2018. Setelah menjalin persahabatan yang indah selama bertahun-tahun, kami menyadari bahwa kami adalah belahan jiwa satu sama lain.',
            'cover_photo'       => null,
        ];

        $this->saveFields($invitation, $template, $fieldValues);

        $invitation->guests()->createMany([
            ['name' => 'Bapak & Ibu Hendra',   'phone_code' => '+62', 'phone' => '08111234567', 'notes' => 'Keluarga pihak pria'],
            ['name' => 'J & Pasangan',          'phone_code' => '+62', 'phone' => '08129876543', 'notes' => null],
            ['name' => 'Keluarga Besar Rahayu', 'phone_code' => '+62', 'phone' => '08135551234', 'notes' => 'Keluarga pihak wanita'],
            ['name' => 'Rekan Kerja Budi',      'phone_code' => '+62', 'phone' => null,          'notes' => null],
        ]);

        $this->command->info("✓ Premium White 1 — {$invitation->title}");
        $this->command->info("  Preview: /invitations/{$invitation->id}/preview");
        $this->command->info("  Public:  /inv/{$invitation->slug}");
    }

    private function seedBasic(User $admin): void
    {
        $template = Template::where('slug', 'basic')->firstOrFail();

        $invitation = Invitation::create([
            'user_id'      => $admin->id,
            'template_id'  => $template->id,
            'slug'         => Str::uuid(),
            'title'        => 'Pernikahan Reza Pratama & Sari Indah',
            'status'       => 'published',
            'published_at' => now(),
            'greeting'     => "Kepada Yth.\n{nama_tamu}\n\nBersama ini kami mengundang Bapak/Ibu/Saudara/i untuk hadir di hari pernikahan kami.\n\nBuka undangan: {link}\n\nHormat kami,\nReza & Sari",
        ]);

        $fieldValues = [
            'groom_name'       => 'Reza Pratama, S.Kom.',
            'groom_photo'      => null,
            'bride_name'       => 'Sari Indah, S.E.',
            'bride_photo'      => null,
            'akad_date'        => '2026-07-20',
            'akad_time'        => '09:00',
            'akad_venue'       => 'Masjid Ar-Rahman',
            'reception_date'   => '2026-07-20',
            'reception_time'   => '12:00',
            'reception_venue'  => 'Aula Kelurahan Sukasari',
            'maps_url'         => 'https://maps.google.com',
            'cover_photo'      => null,
        ];

        $this->saveFields($invitation, $template, $fieldValues);

        $invitation->guests()->createMany([
            ['name' => 'Keluarga Pratama',  'phone_code' => '+62', 'phone' => '08122334455', 'notes' => 'Keluarga pihak pria'],
            ['name' => 'Keluarga Indah',    'phone_code' => '+62', 'phone' => '08133445566', 'notes' => 'Keluarga pihak wanita'],
            ['name' => 'Teman Kampus Reza', 'phone_code' => '+62', 'phone' => '08144556677', 'notes' => null],
        ]);

        $this->command->info("✓ Basic — {$invitation->title}");
        $this->command->info("  Preview: /invitations/{$invitation->id}/preview");
        $this->command->info("  Public:  /inv/{$invitation->slug}");
    }

    private function saveFields(Invitation $invitation, Template $template, array $values): void
    {
        $fields = $template->fields()->get()->keyBy('key');
        foreach ($values as $key => $value) {
            if ($fields->has($key)) {
                InvitationData::create([
                    'invitation_id'     => $invitation->id,
                    'template_field_id' => $fields[$key]->id,
                    'value'             => $value,
                ]);
            }
        }
    }
}
