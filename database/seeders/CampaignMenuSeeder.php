<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class CampaignMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create "Marketing" parent menu
        $marketingMenu = Menu::firstOrCreate(
            ['slug' => 'marketing'],
            [
                'name' => 'Marketing',
                'url' => null,
                'icon' => 'flaticon-381-promotion',
                'parent_id' => null,
                'order' => 50,
                'is_active' => true,
                'permission_name' => null, // Parent menu visible to all
            ]
        );

        // Create "Kampanye" child menu
        Menu::firstOrCreate(
            ['slug' => 'campaigns'],
            [
                'name' => 'Kampanye',
                'url' => '/dash/admin/campaigns',
                'icon' => 'fa fa-bullhorn',
                'parent_id' => $marketingMenu->id,
                'order' => 1,
                'is_active' => true,
                'permission_name' => 'campaigns.view',
            ]
        );

        $this->command->info('✓ Campaign menu created successfully');
    }
}
