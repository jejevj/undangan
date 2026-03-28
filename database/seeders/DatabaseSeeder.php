<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use App\Models\Template;
use App\Support\TemplateFieldPreset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ───────────────────────────────────────────────
        $permissions = [
            'view-dashboard',
            // Users
            'view-users', 'create-users', 'edit-users', 'delete-users',
            // Roles
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
            // Permissions
            'view-permissions', 'create-permissions', 'delete-permissions',
            // Menus
            'view-menus', 'create-menus', 'edit-menus', 'delete-menus',
            // Templates
            'view-templates', 'create-templates', 'edit-templates', 'delete-templates',
            // Invitations
            'view-invitations', 'create-invitations', 'edit-invitations', 'delete-invitations',
            // Music (user)
            'view-music', 'upload-music',
            // Music (admin)
            'manage-music',
            // Pricing Plans
            'view-pricing-plans', 'create-pricing-plans', 'edit-pricing-plans', 'delete-pricing-plans',
            // Partners
            'view-partners', 'create-partners', 'edit-partners', 'delete-partners',
            // General Config
            'view-general-config', 'edit-general-config',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Roles ─────────────────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions); // admin dapat semua

        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->syncPermissions([
            'view-dashboard',
            'view-invitations', 'create-invitations', 'edit-invitations', 'delete-invitations',
            'view-music', 'upload-music',
        ]);

        // Role Pengguna (default untuk registrasi)
        $userRole = Role::firstOrCreate(['name' => 'pengguna', 'guard_name' => 'web']);
        $userRole->syncPermissions([
            'view-dashboard',
            'view-invitations', 'create-invitations', 'edit-invitations',
            // Tidak ada 'delete-invitations' - pengguna biasa tidak bisa hapus undangan
            'view-music', 'upload-music',
        ]);

        // ── Admin user ────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@undangan.test'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );
        $admin->syncRoles(['admin']);

        // ── Menus ─────────────────────────────────────────────────────
        Menu::truncate();

        // 1. Dashboard
        Menu::create([
            'name' => 'Dashboard', 'slug' => 'dashboard',
            'url' => '/dash', 'icon' => 'flaticon-381-networking',
            'order' => 1, 'is_active' => true, 'permission_name' => 'view-dashboard',
        ]);

        // 2. Undangan Saya
        Menu::create([
            'name' => 'Undangan Saya', 'slug' => 'invitations',
            'url' => '/dash/invitations', 'icon' => 'flaticon-381-notepad',
            'order' => 2, 'is_active' => true, 'permission_name' => 'view-invitations',
        ]);

        // 3. Musik (user: lihat galeri & upload)
        Menu::create([
            'name' => 'Musik', 'slug' => 'music',
            'url' => '/dash/music', 'icon' => 'flaticon-381-music-album',
            'order' => 3, 'is_active' => true, 'permission_name' => 'view-music',
        ]);

        Menu::create([
            'name' => 'Paket Langganan', 'slug' => 'subscription',
            'url' => '/dash/subscription', 'icon' => 'flaticon-381-layer-1',
            'order' => 4, 'is_active' => true, 'permission_name' => 'view-invitations',
        ]);

        // 4. Pengaturan (parent)
        $settings = Menu::create([
            'name' => 'Pengaturan', 'slug' => 'pengaturan',
            'url' => null, 'icon' => 'flaticon-381-settings-2',
            'order' => 5, 'is_active' => true, 'permission_name' => null,
        ]);

        // Sub-menu Pengaturan
        $subMenus = [
            ['name' => 'Konfigurasi Umum',    'slug' => 'general-config', 'url' => '/dash/general-config', 'order' => 1, 'permission_name' => 'view-general-config'],
            ['name' => 'Manajemen Template',  'slug' => 'templates',   'url' => '/dash/templates',   'order' => 2, 'permission_name' => 'view-templates'],
            ['name' => 'Manajemen Musik',     'slug' => 'admin-music', 'url' => '/dash/admin/music', 'order' => 3, 'permission_name' => 'manage-music'],
            ['name' => 'Manajemen Pricing',   'slug' => 'pricing-plans', 'url' => '/dash/pricing-plans', 'order' => 4, 'permission_name' => 'view-pricing-plans'],
            ['name' => 'Manajemen Partner',   'slug' => 'partners', 'url' => '/dash/partners', 'order' => 5, 'permission_name' => 'view-partners'],
            ['name' => 'Manajemen User',      'slug' => 'users',       'url' => '/dash/users',       'order' => 6, 'permission_name' => 'view-users'],
            ['name' => 'Manajemen Role',      'slug' => 'roles',       'url' => '/dash/roles',       'order' => 7, 'permission_name' => 'view-roles'],
            ['name' => 'Manajemen Permission','slug' => 'permissions', 'url' => '/dash/permissions', 'order' => 8, 'permission_name' => 'view-permissions'],
            ['name' => 'Manajemen Menu',      'slug' => 'menus',       'url' => '/dash/menus',       'order' => 9, 'permission_name' => 'view-menus'],
        ];

        foreach ($subMenus as $sub) {
            Menu::create(array_merge($sub, [
                'parent_id' => $settings->id,
                'icon'      => null,
                'is_active' => true,
            ]));
        }

        // ── Template: Premium White 1 ─────────────────────────────────
        $template = Template::firstOrCreate(
            ['slug' => 'premium-white-1'],
            [
                'name'              => 'Premium White 1',
                'type'              => 'premium',
                'price'             => 150000,
                'free_photo_limit'  => null,   // unlimited untuk premium
                'extra_photo_price' => 5000,
                'blade_view'        => 'invitation-templates.premium-white-1.index',
                'asset_folder'      => 'premium-white-1',
                'version'           => '1.0.0',
                'description'       => 'Template undangan pernikahan elegan dengan tema putih premium.',
                'is_active'         => true,
            ]
        );

        foreach (TemplateFieldPreset::weddingStandard() as $field) {
            $template->fields()->firstOrCreate(
                ['key' => $field['key']],
                array_merge($field, ['template_id' => $template->id])
            );
        }

        // ── Sub-seeders ───────────────────────────────────────────────
        $this->call(GeneralConfigSeeder::class);
        $this->call(TemplateCategorySeeder::class);
        $this->call(BasicTemplateSeeder::class);
        $this->call(PricingPlanSeeder::class);
        $this->call(MusicSeeder::class);
        $this->call(InvitationSeeder::class);
        $this->call(PartnerSeeder::class);
    }
}