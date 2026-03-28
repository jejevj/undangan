# Fix: Mixed Content HTTPS Error

## Error yang Terjadi

```
Uncaught SecurityError: Failed to execute 'pushState' on 'History': 
A history state object with URL 'http://...' cannot be created in a 
document with origin 'https://...'
```

## Penyebab

1. Website diakses via HTTPS
2. JavaScript `pushState` mencoba menggunakan URL HTTP
3. Browser memblokir karena security policy (mixed content)

## Solusi yang Sudah Diterapkan

### 1. Fix JavaScript pushState (login.blade.php)

**Sebelum:**
```javascript
const newUrl = targetTab === 'login' ? '{{ route("login") }}' : '{{ route("register") }}';
window.history.pushState({}, '', newUrl);
```

**Sesudah:**
```javascript
// Replace http: with current protocol (https: if accessed via HTTPS)
const loginUrl = '{{ route("login") }}'.replace(/^http:/, window.location.protocol);
const registerUrl = '{{ route("register") }}'.replace(/^http:/, window.location.protocol);
const newUrl = targetTab === 'login' ? loginUrl : registerUrl;
window.history.pushState({}, '', newUrl);
```

**Cara Kerja:**
- Mengambil URL dari route helper
- Replace `http:` dengan protokol saat ini (`window.location.protocol`)
- Jika diakses via HTTPS, otomatis jadi HTTPS
- Jika diakses via HTTP (local), tetap HTTP

### 2. Force HTTPS di Production (AppServiceProvider.php)

**Ditambahkan:**
```php
if ($this->app->environment('production')) {
    \Illuminate\Support\Facades\URL::forceScheme('https');
    // Force root URL to use HTTPS
    $this->app['url']->forceRootUrl(config('app.url'));
}
```

**Fungsi:**
- Semua URL yang di-generate Laravel otomatis HTTPS
- Termasuk `route()`, `url()`, `asset()`, dll
- Hanya aktif di production environment

## Setup Production

### 1. Update .env di Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://undanganberpesta.ourtestcloud.my.id
```

**PENTING:** `APP_URL` harus HTTPS!

### 2. Clear Cache

Setelah update .env:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 3. Restart Web Server

```bash
# Jika menggunakan PHP-FPM
sudo systemctl restart php8.2-fpm

# Jika menggunakan Apache
sudo systemctl restart apache2

# Jika menggunakan Nginx
sudo systemctl restart nginx
```

## Testing

### 1. Test di Browser

1. Akses: `https://undanganberpesta.ourtestcloud.my.id/login`
2. Klik tab "Daftar" atau "Masuk"
3. Periksa Console (F12) - tidak boleh ada error
4. Periksa URL bar - harus tetap HTTPS

### 2. Test dengan DevTools

1. Buka halaman login
2. F12 → Console tab
3. Klik tab login/register
4. Tidak boleh ada error SecurityError

### 3. Verifikasi URL Generation

Di browser console:
```javascript
// Test current protocol
console.log(window.location.protocol); // Should be "https:"

// Test URL replacement
const testUrl = 'http://example.com/test';
console.log(testUrl.replace(/^http:/, window.location.protocol));
// Should output: "https://example.com/test"
```

## Troubleshooting

### Error Masih Muncul

**Solusi 1: Clear Browser Cache**
- Ctrl+Shift+Delete
- Clear cached images and files
- Reload halaman (Ctrl+F5)

**Solusi 2: Verify .env**
```bash
php artisan config:cache
php artisan tinker
>>> config('app.url')
# Should output: "https://yourdomain.com"
```

**Solusi 3: Check Web Server Config**

Pastikan web server sudah force HTTPS:

**Nginx:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl;
    server_name yourdomain.com;
    # ... SSL config
}
```

**Apache (.htaccess):**
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Mixed Content di Asset Files

Jika ada error mixed content di CSS/JS/images:

**Solusi:** Gunakan `secure_asset()` atau `asset()` dengan HTTPS

```blade
{{-- Otomatis HTTPS di production --}}
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<script src="{{ asset('js/app.js') }}"></script>
<img src="{{ asset('images/logo.png') }}" alt="Logo">
```

Laravel akan otomatis generate HTTPS URL jika `URL::forceScheme('https')` aktif.

## Files Modified

1. ✅ `resources/views/auth/login.blade.php` - Fix pushState
2. ✅ `app/Providers/AppServiceProvider.php` - Force HTTPS in production

## Prevention

Untuk mencegah masalah serupa di masa depan:

### 1. Selalu Gunakan Helper Laravel

```blade
{{-- GOOD --}}
<a href="{{ route('login') }}">Login</a>
<img src="{{ asset('images/logo.png') }}">

{{-- BAD --}}
<a href="http://domain.com/login">Login</a>
<img src="http://domain.com/images/logo.png">
```

### 2. JavaScript URL Generation

```javascript
// GOOD - Use current protocol
const url = '{{ route("page") }}'.replace(/^http:/, window.location.protocol);

// BAD - Hardcoded protocol
const url = 'http://domain.com/page';
```

### 3. AJAX Requests

```javascript
// GOOD - Relative URL
$.ajax({
    url: '/api/endpoint',
    // ...
});

// GOOD - Use route helper
$.ajax({
    url: '{{ route("api.endpoint") }}'.replace(/^http:/, window.location.protocol),
    // ...
});
```

## Summary

✅ JavaScript pushState fixed - otomatis menggunakan protokol saat ini
✅ HTTPS enforcement di production
✅ APP_URL harus HTTPS di production .env
✅ Clear cache setelah update config
✅ Test di browser - tidak ada error lagi

**Error mixed content sudah teratasi!** 🎉
