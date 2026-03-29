# Live Edit - Auto Enable untuk Semua Template

## Implementasi

Live edit sekarang **otomatis aktif** di semua template tanpa perlu modifikasi template individual.

### Cara Kerja

1. **Controller Level Wrapper**
   - `InvitationController::preview()` membungkus template dengan wrapper
   - Wrapper menambahkan live edit script dan data attributes
   - Template content di-parse dan di-inject ke wrapper

2. **Preview Wrapper**
   - File: `resources/views/invitations/preview-wrapper.blade.php`
   - Mengekstrak head elements (CSS, fonts, meta tags)
   - Mengekstrak body content
   - Mengekstrak dan menjalankan scripts
   - Menambahkan live edit toolbar

3. **Automatic Detection**
   - JavaScript otomatis detect `data-invitation-id` dan `data-is-owner`
   - Jika keduanya ada, live edit toolbar muncul
   - Tidak perlu modifikasi template sama sekali

### Keuntungan

✅ **Tidak perlu edit setiap template**
✅ **Template lama otomatis support live edit**
✅ **Template baru otomatis support live edit**
✅ **Centralized management**
✅ **Easy maintenance**

### Template yang Sudah Support

- ✅ Basic (ID 2)
- ✅ Premium White 1 (ID 1)
- ✅ Sanno (ID 3)
- ✅ **SEMUA template lainnya otomatis support!**

### Cara Menggunakan

1. Login sebagai owner undangan
2. Buka preview: `/dash/invitations/{id}/preview`
3. Tombol "Live Edit" muncul otomatis
4. Klik untuk aktifkan mode edit
5. Edit konten langsung

### Untuk Template Developer

**Tidak perlu lagi menambahkan:**
- ❌ `<meta name="csrf-token">`
- ❌ `<script src="live-edit.js">`
- ❌ `data-invitation-id` di body
- ❌ `data-is-owner` di body

**Cukup tambahkan `data-editable` di element yang ingin diedit:**

```blade
<h1 
    data-editable
    data-field-key="event_title"
    data-field-type="text"
    data-field-label="Judul Acara"
>
    {{ $data['event_title'] }}
</h1>
```

### Authorization

- Hanya owner undangan yang bisa akses live edit
- Admin juga bisa akses live edit
- Anonymous user tidak bisa akses
- User lain tidak bisa akses

### Testing

Test di berbagai template:
```
http://127.0.0.1:8000/dash/invitations/6/preview  (Premium White 1)
http://127.0.0.1:8000/dash/invitations/7/preview  (Basic)
http://127.0.0.1:8000/dash/invitations/8/preview  (Sanno)
```

Semua akan memiliki tombol "Live Edit" otomatis!

## Technical Details

### Flow

```
User → Preview URL
    ↓
InvitationController::preview()
    ↓
Check if owner/admin
    ↓ YES
Render template → Get HTML string
    ↓
Wrap with preview-wrapper.blade.php
    ↓
Parse HTML → Extract head/body/scripts
    ↓
Inject to wrapper
    ↓
Add live-edit.js
    ↓
Add data attributes
    ↓
Return wrapped HTML
    ↓
Browser renders
    ↓
JavaScript detects data attributes
    ↓
Initialize LiveEditor
    ↓
Toolbar appears
```

### Files Modified

1. `app/Http/Controllers/InvitationController.php`
   - Modified `preview()` method
   - Added wrapper logic

2. `resources/views/invitations/preview-wrapper.blade.php`
   - New file
   - Wraps template content
   - Adds live edit support

3. `public/assets/js/live-edit.js`
   - Auto-initialize logic
   - CSRF token handling

### Backward Compatibility

✅ Templates yang sudah ada tetap berfungsi
✅ Templates yang sudah punya live edit support tetap berfungsi
✅ Tidak ada breaking changes

## Version
2.0.0 - Auto Enable untuk Semua Template

## Support
Untuk pertanyaan atau issue, hubungi tim development.
