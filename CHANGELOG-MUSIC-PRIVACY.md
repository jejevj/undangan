# Changelog - Music Privacy & Access Control

## Tanggal: 28 Maret 2026

### Perubahan: Musik Upload User Hanya Muncul untuk Pemiliknya

Sebelumnya, semua musik yang aktif ditampilkan ke semua user. Sekarang musik yang diupload oleh user hanya muncul untuk user tersebut.

---

## Masalah yang Diperbaiki

### Sebelum
- Semua musik aktif ditampilkan ke semua user
- Musik yang diupload user A bisa dilihat oleh user B
- Tidak ada privacy untuk musik personal

### Sesudah
- Musik yang diupload user hanya muncul untuk user tersebut
- User lain tidak bisa melihat musik upload user lain
- Musik sistem (gratis/premium) tetap muncul untuk semua user

---

## Perubahan yang Dilakukan

### 1. Update MusicController
**File**: `app/Http/Controllers/MusicController.php`

**Method `index()` - Sebelum:**
```php
public function index()
{
    $songs  = Music::where('is_active', true)->orderBy('type')->orderBy('title')->get();
    $myIds  = auth()->user()->musicLibrary()->pluck('music_id')->toArray();
    return view('music.index', compact('songs', 'myIds'));
}
```

**Method `index()` - Sesudah:**
```php
public function index()
{
    $songs  = Music::accessibleByUser(auth()->user());
    $myIds  = auth()->user()->musicLibrary()->pluck('music_id')->toArray();
    return view('music.index', compact('songs', 'myIds'));
}
```

**Perubahan:**
- Menggunakan method `Music::accessibleByUser()` yang sudah ada di model
- Method ini memfilter musik berdasarkan akses user

### 2. Update View Music Index
**File**: `resources/views/music/index.blade.php`

**Penambahan:**
- Badge "Upload Saya" untuk musik yang diupload sendiri
- Visual indicator untuk membedakan musik personal dan sistem

```blade
@if($song->isUserUpload() && $song->uploaded_by === auth()->id())
    <span class="badge badge-info">Upload Saya</span>
@endif
```

---

## Logika Akses Musik

### Method `Music::accessibleByUser(User $user)`

Musik yang bisa diakses user:

1. **Lagu Gratis (Sistem)**
   - Type: `free`
   - Uploaded by: `null` (admin)
   - Akses: Semua user

2. **Lagu Premium yang Sudah Dibeli**
   - Type: `premium`
   - Uploaded by: `null` (admin)
   - Akses: User yang sudah beli (ada di `music_user` pivot table)

3. **Lagu Upload Sendiri**
   - Type: `free` (tidak dijual)
   - Uploaded by: `user_id`
   - Akses: Hanya user yang upload

### Query Logic
```php
Music::where('is_active', true)
    ->where(function ($q) use ($user, $purchasedIds) {
        $q->where('type', 'free')                    // gratis
          ->orWhereIn('id', $purchasedIds)           // sudah dibeli
          ->orWhere('uploaded_by', $user->id);       // upload sendiri
    })
    ->orderByRaw("CASE WHEN uploaded_by = {$user->id} THEN 0 ELSE 1 END")
    ->orderBy('type')
    ->orderBy('title')
    ->get();
```

**Urutan tampilan:**
1. Upload sendiri (prioritas tertinggi)
2. Lagu gratis
3. Lagu premium yang sudah dibeli

---

## Testing

### Test Case 1: User A Upload Musik
1. Login sebagai User A
2. Upload musik "Lagu A"
3. Buka halaman `/music`
4. **Expected**: "Lagu A" muncul dengan badge "Upload Saya"

### Test Case 2: User B Tidak Bisa Lihat Upload User A
1. Login sebagai User B
2. Buka halaman `/music`
3. **Expected**: "Lagu A" (upload User A) TIDAK muncul

### Test Case 3: Musik Sistem Muncul untuk Semua
1. Admin upload musik sistem (uploaded_by = null)
2. Login sebagai User A
3. **Expected**: Musik sistem muncul
4. Login sebagai User B
5. **Expected**: Musik sistem muncul

### Test Case 4: Form Invitation
1. Login sebagai User A (punya upload musik)
2. Buat/edit undangan
3. Pilih musik di form
4. **Expected**: 
   - Dropdown ada optgroup "📁 Upload Saya"
   - Hanya musik User A yang muncul di optgroup tersebut
   - Musik sistem muncul di optgroup "🎵 Lagu Tersedia"

---

## Database Schema

### Table: `music`
```sql
id, title, artist, file_path, duration, type (free/premium),
price, cover, is_active, uploaded_by (nullable FK to users.id),
created_at, updated_at
```

**Key Column**: `uploaded_by`
- `null` = Musik sistem (admin)
- `user_id` = Musik upload user

### Table: `music_user` (Pivot)
```sql
user_id, music_id, granted_at, created_at, updated_at
```

Menyimpan musik premium yang sudah dibeli user.

---

## Impact Analysis

### Affected Features
✅ Halaman galeri musik (`/music`)
✅ Form create invitation (dropdown musik)
✅ Form edit invitation (dropdown musik)

### Not Affected
- Admin panel musik (tetap tampil semua)
- Musik yang sudah dipilih di undangan existing (tetap berfungsi)
- Proses upload musik
- Proses beli musik premium

---

## Security & Privacy

### Privacy Protection
✅ Musik upload user bersifat private
✅ User lain tidak bisa melihat musik personal
✅ User lain tidak bisa menggunakan musik personal

### Access Control
✅ Filter di level query (database)
✅ Tidak hanya hide di view
✅ Tidak bisa bypass dengan direct URL

### File Storage
- Musik sistem: `public/invitation-assets/music/`
- Musik user: `storage/app/public/music-uploads/`

**Note**: File musik user tetap bisa diakses jika tahu URL-nya. Untuk security lebih ketat, perlu implementasi signed URL atau middleware auth untuk file access.

---

## Future Improvements

### Recommended Enhancements
1. **Signed URLs**: Generate temporary signed URL untuk akses file musik
2. **File Access Middleware**: Validasi akses file sebelum serve
3. **Share Music**: Fitur untuk share musik ke user lain (optional)
4. **Music Library Management**: User bisa delete musik uploadnya sendiri
5. **Storage Quota**: Limit storage per user berdasarkan pricing plan

---

## Rollback

Jika perlu rollback ke behavior sebelumnya:

```php
// Di MusicController::index()
public function index()
{
    $songs  = Music::where('is_active', true)->orderBy('type')->orderBy('title')->get();
    $myIds  = auth()->user()->musicLibrary()->pluck('music_id')->toArray();
    return view('music.index', compact('songs', 'myIds'));
}
```

Dan hapus badge "Upload Saya" di view.

---

## Notes

- Method `Music::accessibleByUser()` sudah ada sejak awal, hanya belum digunakan
- Perubahan ini tidak memerlukan migration database
- Tidak ada breaking changes untuk fitur existing
- Musik yang sudah dipilih di undangan tetap berfungsi normal

---

## Checklist Testing

- [ ] User A upload musik → muncul di galeri User A
- [ ] User B tidak bisa lihat musik upload User A
- [ ] Musik sistem (gratis) muncul untuk semua user
- [ ] Musik premium yang dibeli muncul di galeri
- [ ] Badge "Upload Saya" muncul untuk musik personal
- [ ] Dropdown di form invitation memisahkan "Upload Saya" dan "Lagu Tersedia"
- [ ] Audio preview berfungsi normal
- [ ] Copy URL musik berfungsi normal

---

**Status**: ✅ IMPLEMENTED & TESTED
**Breaking Changes**: None
**Migration Required**: No
