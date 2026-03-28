# Test Canonical URL Implementation

## Quick Test Guide

### 1. Test Landing Page
```bash
# Akses landing page
http://127.0.0.1:8000/

# View source (Ctrl+U) dan cari:
<link rel="canonical" href="http://127.0.0.1:8000/">
```

### 2. Test Dashboard
```bash
# Login sebagai user
# Akses dashboard
http://127.0.0.1:8000/dash

# View source dan cari:
<link rel="canonical" href="http://127.0.0.1:8000/dash">
```

### 3. Test Login Page
```bash
# Akses halaman login
http://127.0.0.1:8000/login

# View source dan cari:
<link rel="canonical" href="http://127.0.0.1:8000/login">
```

### 4. Test Invitation Template (Public)
```bash
# Buat undangan baru atau akses undangan existing
# Contoh: http://127.0.0.1:8000/invitation/john-jane-wedding

# View source dan cari:
<link rel="canonical" href="http://127.0.0.1:8000/invitation/john-jane-wedding">
```

### 5. Test Query String Removal
```bash
# Akses halaman dengan query string
http://127.0.0.1:8000/dash/invitations?page=2&sort=date

# View source dan cari:
# Canonical harus TANPA query string:
<link rel="canonical" href="http://127.0.0.1:8000/dash/invitations">
```

## Expected Results

✅ Semua halaman harus memiliki canonical tag di head section
✅ Canonical URL harus tanpa query string
✅ Format: `<link rel="canonical" href="URL">`
✅ URL harus sesuai dengan halaman yang diakses

## Browser DevTools Test

1. Buka halaman
2. Tekan F12 (Developer Tools)
3. Tab "Elements" atau "Inspector"
4. Cari `<head>` section
5. Verifikasi ada tag: `<link rel="canonical" href="...">`

## Production Test (HTTPS)

Setelah deploy ke production:

```bash
# Test dengan curl
curl -s https://yourdomain.com | grep canonical

# Expected output:
# <link rel="canonical" href="https://yourdomain.com/">
```

## Google Search Console Verification

1. Login ke Google Search Console
2. Pilih property website Anda
3. Menu: Coverage
4. Periksa tidak ada "Duplicate without user-selected canonical"
5. Menu: URL Inspection
6. Test URL dan lihat "User-declared canonical"

## Common Issues & Solutions

### Issue: Canonical tag tidak muncul
**Solution:** 
- Pastikan `AppServiceProvider::boot()` memiliki view composer
- Clear cache: `php artisan view:clear`
- Clear config: `php artisan config:clear`

### Issue: Canonical masih ada query string
**Solution:**
- `url()->current()` otomatis menghapus query string
- Jika masih ada, periksa custom canonical di controller

### Issue: HTTPS tidak muncul di production
**Solution:**
- Pastikan `URL::forceScheme('https')` aktif di `AppServiceProvider`
- Check environment: `APP_ENV=production`

## Files Modified

- ✅ `app/Providers/AppServiceProvider.php` - View composer
- ✅ `resources/views/layouts/app.blade.php` - Dashboard layout
- ✅ `resources/views/landing/index.blade.php` - Landing page
- ✅ `resources/views/auth/login.blade.php` - Login page
- ✅ `resources/views/invitation-templates/basic/index.blade.php` - Basic template
- ✅ `resources/views/invitation-templates/premium-white-1/index.blade.php` - Premium template
- ✅ `resources/views/invitation-templates/cover.blade.php` - Cover template

## Next Steps

1. Test semua halaman di local development
2. Deploy ke staging/production
3. Verify HTTPS canonical URLs
4. Submit sitemap ke Google Search Console
5. Monitor duplicate content issues
6. Check canonical in Google Search results (may take days/weeks)
