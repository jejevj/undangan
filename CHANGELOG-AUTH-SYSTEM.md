# Changelog - Authentication System Update

## Tanggal: 28 Maret 2026

### Fitur Baru: Sistem Registrasi dengan Tab Login/Register

#### 1. RegisterController
**File**: `app/Http/Controllers/Auth/RegisterController.php`

Fitur:
- Validasi registrasi (name, email, password dengan konfirmasi)
- Password minimal 8 karakter
- Auto-assign role "pengguna" menggunakan Spatie Permission (`$user->assignRole('pengguna')`)
- Auto-assign free plan untuk user baru
- Auto-login setelah registrasi berhasil
- Redirect ke dashboard dengan pesan sukses

**PENTING**: Aplikasi ini menggunakan Spatie Permission untuk role management, bukan kolom `role` di table users.

#### 2. Routes Update
**File**: `routes/web.php`

Routes baru:
```php
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');
```

#### 3. View Login/Register dengan Tab
**File**: `resources/views/auth/login.blade.php`

Fitur:
- Tab switching antara Login dan Register (tanpa reload)
- Menggunakan logo dari GeneralConfig (logo_dark)
- Menggunakan site_name dan site_description dari GeneralConfig
- Menggunakan favicon dari GeneralConfig
- Tombol "Kembali ke Beranda" di pojok kiri atas
- Animasi smooth saat switch tab
- Responsive design
- Form validation dengan error display
- Success message display

Form Login:
- Email
- Password
- Remember me checkbox
- Link ke form register

Form Register:
- Nama Lengkap
- Email
- Password (minimal 8 karakter)
- Konfirmasi Password
- Link ke form login

#### 4. User Model
**File**: `app/Models/User.php`

- Menggunakan Spatie Permission trait `HasRoles`
- Tidak ada kolom `role` di table users
- Role dikelola melalui table `model_has_roles` (Spatie Permission)
- Default role untuk registrasi: "pengguna"

#### 5. Alur Registrasi

1. User mengisi form registrasi
2. Validasi data (name, email unique, password min 8 karakter + konfirmasi)
3. User dibuat (name, email, password)
4. Assign role "pengguna" menggunakan Spatie Permission
5. Auto-assign free pricing plan (plan dengan price = 0)
6. User subscription dibuat dengan status 'active'
7. Auto-login user
8. Redirect ke dashboard dengan pesan sukses

**Role System:**
- Aplikasi menggunakan Spatie Permission package
- Role "pengguna" harus sudah ada di database (jalankan `UserRoleSeeder`)
- Role disimpan di table `model_has_roles`, bukan di table `users`

#### 6. Styling & UX

- Tab dengan border bottom indicator
- Smooth fade-in animation saat switch tab
- Hover effects pada tab dan button
- Button dengan hover effect (translateY + shadow)
- Back to home button dengan arrow icon
- Password requirements hint
- Responsive untuk mobile dan desktop

### Testing

Untuk test fitur ini:

**Persiapan:**
1. Pastikan role "pengguna" sudah ada: `php artisan db:seed --class=UserRoleSeeder`
2. Pastikan ada free plan: `php artisan db:seed --class=PricingPlanSeeder`

**Test Registrasi:**
1. Akses `/login` atau `/register`
2. Klik tab "Daftar"
3. Test registrasi dengan data baru:
   - Nama: Test User
   - Email: test@example.com
   - Password: password123
   - Konfirmasi: password123
4. Setelah registrasi, user akan auto-login dan redirect ke dashboard
5. Cek di database:
   - Table `users` - user baru
   - Table `model_has_roles` - user memiliki role "pengguna"
   - Table `user_subscriptions` - subscription dengan free plan

### Catatan

- Role "pengguna" harus sudah ada di database (jalankan UserRoleSeeder)
- Free plan harus sudah ada di database (plan dengan price = 0)
- Jika tidak ada free plan, user tetap bisa register tapi tidak akan punya subscription
- Logo dan site info diambil dari GeneralConfig
- Jika GeneralConfig kosong, akan fallback ke default assets
