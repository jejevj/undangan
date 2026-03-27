# Test Results - Music Privacy & Access Control

## Tanggal: 28 Maret 2026

### ✅ Automated Testing

#### 1. Syntax Validation
**Status**: ✅ BERHASIL

Files checked:
- ✅ `app/Http/Controllers/MusicController.php` - No syntax errors
- ✅ `app/Models/Music.php` - No syntax errors

#### 2. View Compilation
**Status**: ✅ BERHASIL

Views compiled:
- ✅ `resources/views/music/index.blade.php`
- ✅ `resources/views/invitations/_fields.blade.php`

#### 3. Route Registration
**Status**: ✅ BERHASIL

Routes verified:
```
GET  /music                    → index (galeri musik)
GET  /music/upload             → uploadForm
POST /music/upload             → userUpload
GET  /music/{music}/buy        → buy
POST /music/orders/{order}/pay → simulatePay
```

---

### 📋 Manual Testing Checklist

#### Test Scenario 1: User Upload Musik
**Objective**: Memastikan user bisa upload musik dan hanya muncul untuk dirinya sendiri

**Steps**:
1. Login sebagai User A (email: usera@test.com)
2. Buka `/music`
3. Klik "Upload Lagu Saya"
4. Upload file MP3:
   - Title: "Lagu Personal A"
   - Artist: "User A"
   - File: lagu-test.mp3
5. Submit form

**Expected Results**:
- [ ] Upload berhasil
- [ ] Redirect ke `/music` dengan success message
- [ ] "Lagu Personal A" muncul di galeri
- [ ] Badge "Upload Saya" muncul pada lagu tersebut
- [ ] Badge "Gratis" muncul (musik upload = free)
- [ ] Audio preview berfungsi
- [ ] Tombol copy URL tersedia

---

#### Test Scenario 2: Privacy - User Lain Tidak Bisa Lihat
**Objective**: Memastikan musik upload User A tidak muncul untuk User B

**Steps**:
1. Logout dari User A
2. Login sebagai User B (email: userb@test.com)
3. Buka `/music`
4. Cari "Lagu Personal A" di galeri

**Expected Results**:
- [ ] "Lagu Personal A" TIDAK muncul di galeri User B
- [ ] Hanya musik sistem (gratis/premium) yang muncul
- [ ] Musik upload User B sendiri (jika ada) yang muncul

---

#### Test Scenario 3: Musik Sistem Muncul untuk Semua
**Objective**: Memastikan musik sistem tetap accessible untuk semua user

**Steps**:
1. Login sebagai Admin
2. Buka `/admin/music`
3. Tambah musik sistem:
   - Title: "Lagu Sistem"
   - Type: Free
   - Upload file
4. Logout
5. Login sebagai User A → buka `/music`
6. Logout
7. Login sebagai User B → buka `/music`

**Expected Results**:
- [ ] "Lagu Sistem" muncul di galeri User A
- [ ] "Lagu Sistem" muncul di galeri User B
- [ ] Badge "Gratis" muncul (bukan "Upload Saya")
- [ ] Semua user bisa akses musik sistem

---

#### Test Scenario 4: Form Invitation - Dropdown Musik
**Objective**: Memastikan dropdown musik di form invitation memisahkan upload personal dan sistem

**Steps**:
1. Login sebagai User A (punya upload "Lagu Personal A")
2. Buka `/invitations/create?template_id=1`
3. Scroll ke field "URL Lagu (mp3)"
4. Buka dropdown musik

**Expected Results**:
- [ ] Dropdown memiliki 2 optgroup:
  - "📁 Upload Saya" - berisi "Lagu Personal A"
  - "🎵 Lagu Tersedia" - berisi musik sistem
- [ ] Pilih "Lagu Personal A"
- [ ] Audio preview muncul dan bisa diputar
- [ ] Submit form berhasil
- [ ] Musik tersimpan di undangan

---

#### Test Scenario 5: Edit Invitation - Musik Tetap Tersedia
**Objective**: Memastikan musik yang sudah dipilih tetap bisa diakses saat edit

**Steps**:
1. Login sebagai User A
2. Buka undangan yang sudah pakai "Lagu Personal A"
3. Klik Edit
4. Cek field musik

**Expected Results**:
- [ ] "Lagu Personal A" terpilih di dropdown
- [ ] Audio preview muncul
- [ ] Bisa ganti ke musik lain
- [ ] Bisa save tanpa error

---

#### Test Scenario 6: Musik Premium yang Dibeli
**Objective**: Memastikan musik premium yang dibeli muncul di galeri

**Steps**:
1. Login sebagai Admin
2. Tambah musik premium (price: 50000)
3. Logout, login sebagai User A
4. Buka `/music`
5. Klik "Beli" pada musik premium
6. Simulasi pembayaran
7. Kembali ke `/music`

**Expected Results**:
- [ ] Musik premium muncul di galeri
- [ ] Badge "Premium" muncul
- [ ] Status "Sudah dibeli" muncul
- [ ] Tombol copy URL tersedia (bukan tombol beli)
- [ ] Musik bisa digunakan di undangan

---

#### Test Scenario 7: Multiple Users Upload
**Objective**: Memastikan setiap user hanya lihat uploadnya sendiri

**Steps**:
1. Login sebagai User A
2. Upload "Lagu A1" dan "Lagu A2"
3. Logout
4. Login sebagai User B
5. Upload "Lagu B1" dan "Lagu B2"
6. Buka `/music`

**Expected Results**:
- [ ] User B hanya lihat "Lagu B1" dan "Lagu B2" dengan badge "Upload Saya"
- [ ] User B TIDAK lihat "Lagu A1" dan "Lagu A2"
- [ ] Logout, login sebagai User A
- [ ] User A hanya lihat "Lagu A1" dan "Lagu A2" dengan badge "Upload Saya"
- [ ] User A TIDAK lihat "Lagu B1" dan "Lagu B2"

---

#### Test Scenario 8: Admin Panel - Lihat Semua
**Objective**: Memastikan admin bisa lihat semua musik termasuk upload user

**Steps**:
1. Login sebagai Admin
2. Buka `/admin/music`

**Expected Results**:
- [ ] Semua musik muncul (sistem + upload user)
- [ ] Ada kolom "Uploaded By" yang menunjukkan pemilik
- [ ] Admin bisa toggle aktif/nonaktif semua musik
- [ ] Admin bisa hapus musik (dengan validasi)

---

### 🔍 Database Verification

#### Check Music Table
```sql
SELECT id, title, type, uploaded_by, is_active 
FROM music 
ORDER BY uploaded_by, title;
```

**Expected**:
- Musik sistem: `uploaded_by = NULL`
- Musik user: `uploaded_by = user_id`

#### Check Music Access
```sql
-- Musik yang bisa diakses User A (id=2)
SELECT m.id, m.title, m.type, m.uploaded_by,
       CASE 
         WHEN m.type = 'free' AND m.uploaded_by IS NULL THEN 'Gratis Sistem'
         WHEN m.uploaded_by = 2 THEN 'Upload Sendiri'
         WHEN mu.user_id = 2 THEN 'Sudah Dibeli'
         ELSE 'Tidak Akses'
       END as access_type
FROM music m
LEFT JOIN music_user mu ON m.id = mu.music_id AND mu.user_id = 2
WHERE m.is_active = 1
  AND (m.type = 'free' OR m.uploaded_by = 2 OR mu.user_id = 2);
```

---

### 🎯 Test Coverage

#### Functional Tests
- [x] Upload musik user
- [x] Privacy musik upload
- [x] Akses musik sistem
- [x] Dropdown di form invitation
- [x] Edit invitation dengan musik
- [x] Beli musik premium
- [x] Multiple users upload
- [x] Admin panel

#### Security Tests
- [ ] User tidak bisa akses musik upload user lain via direct URL
- [ ] User tidak bisa download file musik user lain
- [ ] API endpoint (jika ada) memvalidasi akses

#### Performance Tests
- [ ] Query `accessibleByUser()` efficient (< 100ms)
- [ ] Galeri musik load < 1 detik
- [ ] Dropdown musik di form load < 500ms

---

### 🐛 Known Issues

**File Access via Direct URL**:
- File musik user disimpan di `storage/app/public/music-uploads/`
- Jika user tahu URL lengkap, bisa akses file langsung
- **Recommendation**: Implement signed URL atau middleware untuk file access

**Workaround sementara**:
- File path tidak ditampilkan di UI
- URL hanya tersedia setelah user pilih musik
- Untuk security lebih ketat, perlu middleware auth

---

### ✅ Conclusion

**Status Keseluruhan**: ✅ SIAP UNTUK MANUAL TESTING

Automated tests passed:
- Syntax valid
- Views compiled
- Routes registered

**Next Steps**:
1. Lakukan manual testing sesuai scenario
2. Verify database queries
3. Test dengan multiple users
4. Check performance
5. Consider implementing signed URL untuk file access

---

### 📝 Notes

- Method `Music::accessibleByUser()` sudah ada sejak awal
- Perubahan hanya di controller (menggunakan method yang sudah ada)
- Tidak ada migration database diperlukan
- Tidak ada breaking changes
- Musik yang sudah dipilih di undangan tetap berfungsi

---

### 🔧 Troubleshooting

**Musik upload tidak muncul**:
```bash
# Check file exists
ls storage/app/public/music-uploads/

# Check symlink
php artisan storage:link

# Check database
php artisan tinker
>>> Music::where('uploaded_by', 1)->get()
```

**Musik user lain masih muncul**:
```bash
# Clear cache
php artisan cache:clear
php artisan view:clear

# Verify controller using accessibleByUser()
# Check MusicController::index()
```

**Audio tidak bisa diputar**:
```bash
# Check file permissions
chmod 644 storage/app/public/music-uploads/*

# Check storage link
php artisan storage:link

# Check file path di database
```
