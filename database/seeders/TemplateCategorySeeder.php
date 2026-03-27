<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateCategory;

class TemplateCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Semua Template',
                'slug' => 'all',
                'description' => 'Tampilkan semua template yang tersedia',
                'order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Pernikahan',
                'slug' => 'pernikahan',
                'description' => 'Template undangan pernikahan yang elegan dan romantis',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Ulang Tahun',
                'slug' => 'ulang-tahun',
                'description' => 'Template undangan ulang tahun yang ceria dan menarik',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Acara Perusahaan',
                'slug' => 'acara-perusahaan',
                'description' => 'Template undangan untuk acara perusahaan dan bisnis',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Khitanan',
                'slug' => 'khitanan',
                'description' => 'Template undangan khitanan yang meriah',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            TemplateCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
