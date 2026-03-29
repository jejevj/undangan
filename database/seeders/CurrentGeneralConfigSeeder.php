<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeneralConfig;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CurrentGeneralConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates general configuration based on current production state.
     * Images are stored in public/images/config/ (git-tracked) and copied to storage.
     */
    public function run(): void
    {
        // Delete all existing configs
        GeneralConfig::query()->delete();

        // Copy images from public to storage if they don't exist
        $this->copyConfigImages();

        // Text configurations
        $configs = [
            'site_name' => 'Berpesta',
            'meta_title' => 'Berpesta - Platform Undangan Digital Terlengkap',
            'meta_description' => 'Buat undangan digital yang elegan dan profesional dengan mudah. Platform kami menyediakan berbagai template menarik, fitur musik, galeri foto, dan amplop digital.',
            'meta_keywords' => 'undangan digital, undangan online, wedding invitation, undangan pernikahan, undangan ulang tahun',
            'contact_email' => 'info@berpesta.com',
            'contact_phone' => '085133120213',
            'hero_title' => 'Buat Undangan Digital',
            'hero_highlight' => 'Pestamu',
            'hero_subtitle' => 'Mulai Sekarang Gratis!',
            'about_subtitle' => 'SELAMAT DATANG DI UNDANGAN BERPESTA',
            'about_title' => 'Platform Undangan Online Terlengkap',
            'about_description' => 'Buat undangan digital yang elegan dan profesional dengan mudah. Platform kami menyediakan berbagai template menarik, fitur musik, galeri foto, dan amplop digital untuk membuat undangan Anda sempurna.',
            'site_description' => 'Platform undangan online terlengkap untuk acara spesial Anda. Mudah, cepat, dan profesional.',
        ];

        foreach ($configs as $key => $value) {
            GeneralConfig::create([
                'key' => $key,
                'value' => $value,
            ]);
        }

        // Image configurations (paths relative to storage/app/public/)
        $imageConfigs = [
            'logo_icon' => 'config/logo-icon.png',
            'logo_dark' => 'config/logo-dark.png',
            'logo_light' => 'config/logo-light.png',
            'favicon' => 'config/favicon.png',
        ];

        foreach ($imageConfigs as $key => $path) {
            GeneralConfig::create([
                'key' => $key,
                'value' => $path,
            ]);
        }

        $this->command->info('General configuration seeded successfully!');
        $this->command->info('Config images copied from public/images/config/ to storage/app/public/config/');
    }

    /**
     * Copy config images from public directory to storage
     */
    private function copyConfigImages(): void
    {
        $publicPath = public_path('images/config');
        $storagePath = storage_path('app/public/config');

        // Create storage directory if it doesn't exist
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Copy each image
        $images = [
            'logo-icon.png',
            'logo-dark.png',
            'logo-light.png',
            'favicon.png',
        ];

        foreach ($images as $image) {
            $source = $publicPath . '/' . $image;
            $destination = $storagePath . '/' . $image;

            if (File::exists($source)) {
                File::copy($source, $destination);
            }
        }
    }
}
