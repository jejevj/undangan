<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Support\TemplateFieldPreset;
use Illuminate\Database\Seeder;

class BasicTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::firstOrCreate(
            ['slug' => 'basic'],
            [
                'name'              => 'Basic',
                'type'              => 'free',
                'price'             => 0,
                'free_photo_limit'  => 2,
                'extra_photo_price' => 5000,
                'gift_feature_price'=> 10000,
                'guest_limit'       => 40,
                'blade_view'        => 'invitation-templates.basic.index',
                'asset_folder'      => 'basic',
                'version'           => '1.0.0',
                'description'       => 'Template undangan pernikahan gratis dengan tampilan bersih dan elegan.',
                'is_active'         => true,
            ]
        );

        // Gunakan preset wedding (24 field)
        foreach (TemplateFieldPreset::wedding() as $field) {
            $template->fields()->firstOrCreate(
                ['key' => $field['key']],
                array_merge($field, ['template_id' => $template->id])
            );
        }

        $this->command->info("Template 'Basic' seeded — free, 2 foto gratis, 12 fields.");
    }
}
