<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class FunnelReportMenuSeeder extends Seeder
{
    public function run()
    {
        // Find or create "Laporan" parent menu
        $laporanMenu = Menu::firstOrCreate(
            ['name' => 'Laporan'],
            [
                'slug' => 'laporan',
                'url' => null,
                'icon' => 'fa fa-chart-line',
                'parent_id' => null,
                'order' => 100,
                'is_active' => true,
            ]
        );

        // Create Funnel Analysis menu item
        Menu::firstOrCreate(
            ['slug' => 'funnel-analysis'],
            [
                'name' => 'Funnel Analysis',
                'url' => route('admin.funnel-report'),
                'icon' => 'fa fa-filter',
                'parent_id' => $laporanMenu->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Funnel Report menu created successfully');
    }
}
