<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Permissions ---
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
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // --- Roles ---
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->syncPermissions([
            'view-dashboard',
            'view-invitations', 'create-invitations', 'edit-invitations', 'delete-invitations',
        ]);

        // --- Admin user ---
        $admin = User::firstOrCreate(
            ['email' => 'admin@undangan.test'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );
        $admin->syncRoles(['admin']);

        // --- Menus ---
        Menu::truncate();

        Menu::create([
            'name' => 'Dashboard', 'slug' => 'dashboard',
            'url' => '/', 'icon' => 'flaticon-381-networking',
            'order' => 1, 'is_active' => true, 'permission_name' => 'view-dashboard',
        ]);

        Menu::create([
            'name' => 'Undangan Saya', 'slug' => 'invitations',
            'url' => '/invitations', 'icon' => 'flaticon-381-notepad',
            'order' => 2, 'is_active' => true, 'permission_name' => 'view-invitations',
        ]);

        $settings = Menu::create([
            'name' => 'Pengaturan', 'slug' => 'pengaturan',
            'url' => null, 'icon' => 'flaticon-381-settings-2',
            'order' => 3, 'is_active' => true, 'permission_name' => null,
        ]);

        $subMenus = [
            ['name' => 'Manajemen Template',   'slug' => 'templates',   'url' => '/templates',   'order' => 1, 'permission_name' => 'view-templates'],
            ['name' => 'Manajemen User',        'slug' => 'users',       'url' => '/users',       'order' => 2, 'permission_name' => 'view-users'],
            ['name' => 'Manajemen Role',        'slug' => 'roles',       'url' => '/roles',       'order' => 3, 'permission_name' => 'view-roles'],
            ['name' => 'Manajemen Permission',  'slug' => 'permissions', 'url' => '/permissions', 'order' => 4, 'permission_name' => 'view-permissions'],
            ['name' => 'Manajemen Menu',        'slug' => 'menus',       'url' => '/menus',       'order' => 5, 'permission_name' => 'view-menus'],
        ];

        foreach ($subMenus as $sub) {
            Menu::create(array_merge($sub, [
                'parent_id' => $settings->id,
                'icon' => null, 'is_active' => true,
            ]));
        }

        // --- Template: Premium White 1 ---
        $template = Template::firstOrCreate(
            ['slug' => 'premium-white-1'],
            [
                'name'        => 'Premium White 1',
                'blade_view'  => 'invitation-templates.premium-white-1',
                'description' => 'Template undangan pernikahan elegan dengan tema putih premium.',
                'is_active'   => true,
            ]
        );

        // Field definitions untuk Premium White 1
        $fields = [
            // Mempelai Pria
            ['key' => 'groom_name',         'label' => 'Nama Mempelai Pria',          'type' => 'text',     'group' => 'mempelai', 'required' => true,  'order' => 1],
            ['key' => 'groom_nickname',      'label' => 'Nama Panggilan Pria',         'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 2],
            ['key' => 'groom_photo',         'label' => 'Foto Mempelai Pria',          'type' => 'image',    'group' => 'mempelai', 'required' => false, 'order' => 3],
            ['key' => 'groom_father',        'label' => 'Nama Ayah Mempelai Pria',     'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 4],
            ['key' => 'groom_mother',        'label' => 'Nama Ibu Mempelai Pria',      'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 5],
            // Mempelai Wanita
            ['key' => 'bride_name',          'label' => 'Nama Mempelai Wanita',        'type' => 'text',     'group' => 'mempelai', 'required' => true,  'order' => 6],
            ['key' => 'bride_nickname',      'label' => 'Nama Panggilan Wanita',       'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 7],
            ['key' => 'bride_photo',         'label' => 'Foto Mempelai Wanita',        'type' => 'image',    'group' => 'mempelai', 'required' => false, 'order' => 8],
            ['key' => 'bride_father',        'label' => 'Nama Ayah Mempelai Wanita',   'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 9],
            ['key' => 'bride_mother',        'label' => 'Nama Ibu Mempelai Wanita',    'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 10],
            // Akad
            ['key' => 'akad_date',           'label' => 'Tanggal Akad',                'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 11],
            ['key' => 'akad_time',           'label' => 'Waktu Akad',                  'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 12],
            ['key' => 'akad_venue',          'label' => 'Tempat Akad',                 'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 13],
            ['key' => 'akad_address',        'label' => 'Alamat Akad',                 'type' => 'textarea', 'group' => 'acara',    'required' => false, 'order' => 14],
            // Resepsi
            ['key' => 'reception_date',      'label' => 'Tanggal Resepsi',             'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 15],
            ['key' => 'reception_time',      'label' => 'Waktu Resepsi',               'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 16],
            ['key' => 'reception_venue',     'label' => 'Tempat Resepsi',              'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 17],
            ['key' => 'reception_address',   'label' => 'Alamat Resepsi',              'type' => 'textarea', 'group' => 'acara',    'required' => false, 'order' => 18],
            // Tambahan
            ['key' => 'maps_url',            'label' => 'Link Google Maps',            'type' => 'url',      'group' => 'tambahan', 'required' => false, 'order' => 19],
            ['key' => 'love_story',          'label' => 'Cerita Cinta',                'type' => 'textarea', 'group' => 'tambahan', 'required' => false, 'order' => 20],
            ['key' => 'cover_photo',         'label' => 'Foto Cover',                  'type' => 'image',    'group' => 'tambahan', 'required' => false, 'order' => 21],
        ];

        foreach ($fields as $field) {
            $template->fields()->firstOrCreate(
                ['key' => $field['key']],
                array_merge($field, ['template_id' => $template->id])
            );
        }
    }
}
