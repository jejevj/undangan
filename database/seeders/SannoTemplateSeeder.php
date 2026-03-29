<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class SannoTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::firstOrCreate(
            ['slug' => 'sanno'],
            [
                'category_id'        => 4, // Acara Perusahaan
                'name'               => 'Sanno',
                'type'               => 'premium',
                'price'              => 149000,
                'free_photo_limit'   => 15,
                'extra_photo_price'  => 5000,
                'gift_feature_price' => 10000,
                'guest_limit'        => null,
                'blade_view'         => 'invitation-templates.sanno.index',
                'asset_folder'       => 'sanno',
                'version'            => '1.0.0',
                'description'        => 'Template undangan acara perusahaan, seminar, dan grand opening dengan desain profesional dan modern.',
                'is_active'          => true,
            ]
        );

        // Custom fields untuk acara perusahaan
        $fields = [
            ['key' => 'company_logo', 'label' => 'Logo Perusahaan', 'type' => 'image', 'group' => 'perusahaan', 'required' => false, 'order' => 1],
            ['key' => 'event_title', 'label' => 'Judul Acara', 'type' => 'text', 'group' => 'acara', 'required' => true, 'order' => 2],
            ['key' => 'event_subtitle', 'label' => 'Sub Judul Acara', 'type' => 'text', 'group' => 'acara', 'required' => false, 'order' => 3],
            ['key' => 'event_description', 'label' => 'Deskripsi Acara', 'type' => 'textarea', 'group' => 'acara', 'required' => true, 'order' => 4],
            ['key' => 'event_date', 'label' => 'Tanggal Acara', 'type' => 'date', 'group' => 'acara', 'required' => true, 'order' => 5],
            ['key' => 'event_time', 'label' => 'Waktu Acara', 'type' => 'time', 'group' => 'acara', 'required' => true, 'order' => 6],
            ['key' => 'event_venue', 'label' => 'Tempat Acara', 'type' => 'text', 'group' => 'acara', 'required' => true, 'order' => 7],
            ['key' => 'event_address', 'label' => 'Alamat Lengkap', 'type' => 'textarea', 'group' => 'acara', 'required' => true, 'order' => 8],
            ['key' => 'maps_url', 'label' => 'Link Google Maps', 'type' => 'url', 'group' => 'acara', 'required' => false, 'order' => 9],
            ['key' => 'rsvp_note', 'label' => 'Catatan RSVP', 'type' => 'textarea', 'group' => 'tambahan', 'required' => false, 'order' => 10],
            ['key' => 'qr_note', 'label' => 'Catatan QR Code', 'type' => 'textarea', 'group' => 'tambahan', 'required' => false, 'order' => 11],
            ['key' => 'cover_photo', 'label' => 'Foto Cover', 'type' => 'image', 'group' => 'tambahan', 'required' => false, 'order' => 12],
        ];

        foreach ($fields as $field) {
            $template->fields()->firstOrCreate(
                ['key' => $field['key']],
                array_merge($field, ['template_id' => $template->id])
            );
        }

        $this->command->info("Template 'Sanno' seeded successfully.");
    }
}
