<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GeneralConfig;

class GeneralConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            'site_name' => 'Undangan Berpesta',
            'meta_title' => 'Undangan Berpesta - Platform Undangan Digital Terlengkap',
            'meta_description' => 'Buat undangan digital yang elegan dan profesional dengan mudah. Platform kami menyediakan berbagai template menarik, fitur musik, galeri foto, dan amplop digital.',
            'meta_keywords' => 'undangan digital, undangan online, wedding invitation, undangan pernikahan, undangan ulang tahun',
            'contact_email' => 'info@undanganberpesta.com',
            'contact_phone' => '081234567890',
            'hero_title' => 'Buat Undangan Digital',
            'hero_highlight' => 'Impianmu',
            'hero_subtitle' => 'Mulai Sekarang Gratis!',
            'about_subtitle' => 'SELAMAT DATANG DI UNDANGAN DIGITAL',
            'about_title' => 'Platform Undangan Online Terlengkap',
            'about_description' => 'Buat undangan digital yang elegan dan profesional dengan mudah. Platform kami menyediakan berbagai template menarik, fitur musik, galeri foto, dan amplop digital untuk membuat undangan Anda sempurna.',
            'site_description' => 'Platform undangan online terlengkap untuk acara spesial Anda. Mudah, cepat, dan profesional.',
        ];

        foreach ($configs as $key => $value) {
            GeneralConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('General configuration seeded successfully!');
    }
}
