<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;

class UpdateTemplatePreviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update Premium White 1
        Template::where('slug', 'premium-white-1')->update([
            'preview_url' => 'https://preview.undangan.example.com/premium-white-1',
        ]);

        // Update Basic
        Template::where('slug', 'basic')->update([
            'preview_url' => 'https://preview.undangan.example.com/basic',
        ]);

        $this->command->info('✓ Template preview URLs updated successfully!');
    }
}
