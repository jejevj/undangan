# Changelog - Role & Permission Update

## Tanggal: 28 Maret 2026

### Perubahan yang Dilakukan

#### 1. Perbaikan Tombol Hapus Undangan
- **File**: `resources/views/invitations/index.blade.php`
- **Masalah**: Tombol hapus undangan masih muncul meskipun user tidak memiliki permission `delete-invitations`
- **Solusi**: Menambahkan directive `@can('delete-invitations')` untuk menyembunyikan tombol hapus jika user tidak memiliki permission

```blade
@can('delete-invitations')
    <form action="{{ route('invitations.destroy', $inv) }}" method="POST">
        @csrf @method('DELETE')
        <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
    </form>
@endcan
```

#### 2. Validasi Permission di Controller
- **File**: `app/Http/Controllers/InvitationController.php`
- **Perubahan**: Menambahkan pengecekan permission di method `destroy()`
- **Tujuan**: Mencegah user menghapus undangan melalui direct request meskipun tombol sudah disembunyikan

```php
public function destroy(Invitation $invitation)
{
    $this->authorizeInvitation($invitation);
    
    // Cek permission delete-invitations
    if (!auth()->user()->can('delete-invitations')) {
        abort(403, 'Anda tidak memiliki izin untuk menghapus undangan.');
    }
    
    $invitation->delete();
    return redirect()->route('invitations.index')->with('success', 'Undangan berhasil dihapus.');
}
```

#### 3. Role "Pengguna" untuk User Registrasi
- **File**: `database/seeders/UserRoleSeeder.php` (baru)
- **Tujuan**: Membuat role default untuk user yang melakukan registrasi
- **Permission yang diberikan**:
  - `view-dashboard`
  - `view-invitations`
  - `create-invitations`
  - `edit-invitations`
  - `view-music`
  - `upload-music`
- **Permission yang TIDAK diberikan**:
  - `delete-invitations` (pengguna biasa tidak bisa hapus undangan)

#### 4. Update DatabaseSeeder
- **File**: `database/seeders/DatabaseSeeder.php`
- **Perubahan**: Menambahkan role "pengguna" ke dalam seeder utama
- **Struktur Role**:
  - **Admin**: Semua permission (akses penuh)
  - **Staff**: Dapat mengelola undangan termasuk menghapus
  - **Pengguna**: Role default, tidak dapat menghapus undangan

#### 5. Auto-Assign Role saat User Dibuat
- **File**: `app/Http/Controllers/UserController.php`
- **Perubahan**: Method `store()` sekarang otomatis assign role "pengguna" jika tidak ada role yang dipilih
- **Logika**:
  - Jika admin memilih role tertentu → gunakan role yang dipilih
  - Jika tidak ada role dipilih → otomatis assign role "pengguna"

```php
if ($request->roles && count($request->roles) > 0) {
    $user->syncRoles($request->roles);
} else {
    $user->assignRole('pengguna');
}
```

#### 6. Update Dokumentasi
- **File**: `Knowledge.md`
- **Penambahan**:
  - Dokumentasi lengkap tentang role dan permission
  - Cara implementasi proteksi permission di view dan controller
  - Panduan database seeding
  - Cara auto-assign role saat registrasi

### Cara Menjalankan

1. **Jalankan seeder untuk membuat role "pengguna"**:
   ```bash
   php artisan db:seed --class=UserRoleSeeder
   ```

2. **Atau jalankan ulang semua seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Test permission**:
   - Login sebagai user dengan role "staff" → tombol hapus muncul
   - Login sebagai user dengan role "pengguna" → tombol hapus tidak muncul
   - Coba akses langsung via URL → akan mendapat error 403

### Struktur Role & Permission

| Role | Delete Invitation | Keterangan |
|---|---|---|
| Admin | ✓ | Akses penuh |
| Staff | ✓ | Dapat menghapus undangan |
| Pengguna | ✗ | Tidak dapat menghapus undangan |

### Catatan Penting

- Semua user baru yang dibuat tanpa role akan otomatis mendapat role "pengguna"
- Proteksi permission dilakukan di 2 layer:
  1. **View layer**: Tombol disembunyikan dengan `@can`
  2. **Controller layer**: Request divalidasi dengan `can()`
- Jika aplikasi memiliki fitur registrasi publik, tambahkan `$user->assignRole('pengguna')` setelah user berhasil dibuat

### Testing Checklist

- [x] Tombol hapus tidak muncul untuk user dengan role "pengguna"
- [x] Tombol hapus muncul untuk user dengan role "staff" atau "admin"
- [x] Direct request ke delete endpoint ditolak jika user tidak punya permission
- [x] User baru otomatis mendapat role "pengguna"
- [x] Seeder berjalan tanpa error
- [x] Dokumentasi Knowledge.md sudah diperbarui
