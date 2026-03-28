<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'name' => 'Wedding Organizer Indonesia',
                'logo' => null, // Admin akan upload logo nanti
                'site_url' => 'https://weddingorganizer.co.id',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Dekorasi Pernikahan Elegan',
                'logo' => null,
                'site_url' => 'https://dekorasipernikahan.com',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Catering Premium Jakarta',
                'logo' => null,
                'site_url' => 'https://cateringpremium.id',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Fotografer Profesional',
                'logo' => null,
                'site_url' => 'https://fotograferpro.com',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($partners as $partner) {
            Partner::updateOrCreate(
                ['name' => $partner['name']],
                $partner
            );
        }

        $this->command->info('4 partners created successfully!');
    }
}
