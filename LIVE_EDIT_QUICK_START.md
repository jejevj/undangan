# Live Edit - Quick Start Guide

## Cara Kerja Live Edit

### Untuk User (Pemilik Undangan)

1. **Buka Preview Undangan**
   - Login ke dashboard
   - Pilih undangan Anda
   - Klik tombol "Preview"
   - URL: `/dash/invitations/{id}/preview`

2. **Aktifkan Mode Live Edit**
   - Tombol "Live Edit" akan muncul otomatis di pojok kanan atas
   - Tombol HANYA muncul untuk pemilik undangan
   - Klik tombol untuk mengaktifkan mode edit

3. **Edit Konten**
   - **Teks**: Klik langsung pada teks untuk mengedit
   - **Gambar**: Hover pada gambar, klik "Ganti Foto"
   - **Tanggal/Waktu**: Klik untuk membuka picker
   - Perubahan tersimpan otomatis setelah 1 detik

4. **Status Penyimpanan**
   - "Menyimpan..." - sedang proses
   - "Tersimpan" - berhasil disimpan
   - Pesan error jika gagal

5. **Keluar dari Mode Edit**
   - Klik tombol "Live Edit" lagi
   - Atau refresh halaman

### Untuk Anonymous User / Tamu

- Tombol Live Edit TIDAK akan muncul
- Hanya bisa melihat undangan (read-only)
- Tidak ada akses edit sama sekali

## Kondisi Tombol Live Edit Muncul

Tombol akan muncul HANYA jika:
1. ✅ User sudah login (`@auth`)
2. ✅ User adalah pemilik undangan (`$invitation->user_id === auth()->id()`)
3. ✅ ATAU user adalah admin (`auth()->user()->hasRole('admin')`)
4. ✅ Sedang di halaman preview (`/dash/invitations/{id}/preview`)

Tombol TIDAK akan muncul jika:
- ❌ User belum login (anonymous)
- ❌ User login tapi bukan pemilik undangan
- ❌ Di halaman publik (`/inv/{slug}`)

## Implementasi di Template

### 1. Tambahkan CSRF Token
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### 2. Load Script (Conditional)
```blade
@auth
@if($invitation->user_id === auth()->id() || auth()->user()->hasRole('admin'))
<script src="{{ asset('assets/js/live-edit.js') }}" defer></script>
@endif
@endauth
```

### 3. Set Body Attributes (Conditional)
```blade
<body 
    @auth
    @if($invitation->user_id === auth()->id() || auth()->user()->hasRole('admin'))
    data-invitation-id="{{ $invitation->id }}"
    data-is-owner="true"
    @endif
    @endauth
>
```

### 4. Tandai Element yang Editable
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

## Field Types yang Didukung

| Type | Cara Edit | Contoh |
|------|-----------|--------|
| text | Klik & ketik | Judul, nama, tempat |
| textarea | Klik & ketik (multi-line) | Deskripsi, alamat |
| image | Hover & klik "Ganti Foto" | Logo, foto |
| date | Klik untuk date picker | Tanggal acara |
| time | Klik untuk time picker | Waktu acara |

## Keamanan

### Authorization Check
```php
// Di LiveEditController
if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
    return response()->json(['error' => 'Unauthorized'], 403);
}
```

### CSRF Protection
- Semua request menggunakan CSRF token
- Token diambil dari meta tag
- Validasi di setiap API call

### File Upload Validation
- Max size: 2MB (recommended)
- Allowed types: jpg, png, gif, webp
- Stored in: `storage/invitations/{invitation_id}/`

## Troubleshooting

### Tombol Live Edit Tidak Muncul

**Cek:**
1. Apakah user sudah login?
2. Apakah user adalah pemilik undangan?
3. Apakah di halaman preview (`/dash/invitations/{id}/preview`)?
4. Buka console browser, cek error JavaScript
5. Pastikan `live-edit.js` ter-load

**Solusi:**
```javascript
// Cek di console browser
console.log('Invitation ID:', document.body.getAttribute('data-invitation-id'));
console.log('Is Owner:', document.body.getAttribute('data-is-owner'));
console.log('Live Editor:', window.liveEditor);
```

### Perubahan Tidak Tersimpan

**Cek:**
1. Network tab di browser - lihat response API
2. Console browser - cek error
3. CSRF token ada di meta tag
4. Field key sesuai dengan database

**Debug:**
```javascript
// Cek di console
fetch('/dash/api/invitations/1/live-edit', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({
        field_key: 'event_title',
        value: 'Test'
    })
}).then(r => r.json()).then(console.log);
```

### Upload Gambar Gagal

**Cek:**
1. File size < 2MB
2. File type: jpg, png, gif, webp
3. Storage link: `php artisan storage:link`
4. Permissions folder storage

## Flow Diagram

```
User Login
    ↓
Buka Preview (/dash/invitations/{id}/preview)
    ↓
Cek Authorization (owner atau admin?)
    ↓ YES
Load live-edit.js
    ↓
Tombol "Live Edit" Muncul
    ↓
User Klik "Live Edit"
    ↓
Mode Edit Aktif
    ↓
User Edit Konten
    ↓
Auto-save (debounce 1s)
    ↓
POST /dash/api/invitations/{id}/live-edit
    ↓
Authorization Check di Backend
    ↓
Update Database
    ↓
Return Success/Error
    ↓
Update UI Status
```

## API Endpoints

### Update Field
```
POST /dash/api/invitations/{invitation}/live-edit
Authorization: Required (owner or admin)
Content-Type: application/json

Body:
{
    "field_key": "event_title",
    "value": "New Value"
}

Response:
{
    "success": true,
    "message": "Field updated successfully",
    "field_key": "event_title",
    "value": "New Value"
}
```

### Upload Image
```
POST /dash/api/invitations/{invitation}/live-edit
Authorization: Required (owner or admin)
Content-Type: multipart/form-data

Body:
field_key: company_logo
value: [file]

Response:
{
    "success": true,
    "message": "Field updated successfully",
    "field_key": "company_logo",
    "value": "invitations/1/logo.jpg"
}
```

## Testing

### Manual Test
1. Login sebagai user
2. Buat undangan baru
3. Buka preview
4. Cek tombol "Live Edit" muncul
5. Aktifkan mode edit
6. Edit beberapa field
7. Cek database apakah tersimpan
8. Logout dan buka sebagai anonymous
9. Cek tombol TIDAK muncul

### Test dengan User Lain
1. Login sebagai user A
2. Buat undangan
3. Logout
4. Login sebagai user B
5. Buka preview undangan user A
6. Tombol "Live Edit" TIDAK boleh muncul

## Best Practices

1. **Selalu cek authorization** di backend
2. **Gunakan CSRF token** untuk semua request
3. **Validate input** sebelum save
4. **Debounce auto-save** untuk performa
5. **Berikan feedback visual** ke user
6. **Handle error** dengan baik
7. **Test di berbagai browser**
8. **Test di mobile device**

## Version
1.0.0 - Initial Release

## Support
Untuk pertanyaan atau issue, hubungi tim development.
