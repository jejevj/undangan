<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Role "Pengguna" adalah role default untuk user yang melakukan registrasi.
     * Role ini memiliki akses terbatas hanya untuk mengelola undangan mereka sendiri.
     */
    public function run(): void
    {
        // Buat role "Pengguna"
        $userRole = Role::firstOrCreate(['name' => 'pengguna', 'guard_name' => 'web']);

        // Permission yang diberikan ke role "Pengguna"
        $userPermissions = [
            'view-dashboard',
            'view-invitations',
            'create-invitations',
            'edit-invitations',
            // Tidak ada 'delete-invitations' - pengguna biasa tidak bisa hapus undangan
            'view-music',
            'upload-music',
        ];

        // Pastikan semua permission ada
        foreach ($userPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Sync permission ke role
        $userRole->syncPermissions($userPermissions);

        $this->command->info('✓ Role "Pengguna" berhasil dibuat dengan permission terbatas.');
    }
}
