# Changelog - Music Upload Fee Implementation

## Tanggal: 28 Maret 2026

### Fitur Baru: Biaya Upload Musik Rp 5.000

Upload musik sekarang dikenakan biaya Rp 5.000 per lagu untuk menjaga kualitas dan eksklusivitas fitur.

---

## Perubahan yang Dilakukan

### 1. Model & Migration Baru

**File**: `app/Models/MusicUploadOrder.php` (baru)
**Migration**: `create_music_upload_orders_table.php`

Model untuk menyimpan order upload musik dengan kolom:
- `user_id` - User yang upload
- `order_number` - Nomor order unik (MUP-xxxxx)
- `amount` - Biaya upload (default: 5000)
- `status` - pending, paid, failed
- `payment_method` - Metode pembayaran
- `paid_at` - Waktu pembayaran
- `temp_title` - Judul lagu (temporary)
- `temp_artist` - Artis (temporary)
- `temp_file_path` - Path file temporary
- `music_id` - ID musik setelah upload selesai

### 2. Update MusicController

**File**: `app/Http/Controllers/MusicController.php`

**Method Baru:**

#### `uploadForm()`
- Menampilkan form upload dengan informasi biaya
- Pass variable `$uploadFee = 5000` ke view

#### `userUpload()` - Updated
- Upload file ke folder temporary (`music-uploads-temp/`)
- Buat order dengan status `pending`
- Redirect ke halaman checkout

#### `uploadCheckout(MusicUploadOrder $order)`
- Menampilkan halaman konfirmasi pembayaran
- Preview lagu yang akan diupload
- Informasi total biaya

#### `uploadPay(MusicUploadOrder $order)`
- Simulasi pembayaran berhasil
- Pindahkan file dari temp ke permanent folder
- Buat record Music dengan `uploaded_by = user_id`
- Update order status menjadi `paid`
- Hapus file temporary

### 3. View Baru

**File**: `resources/views/music/upload-checkout.blade.php` (baru)

Halaman checkout dengan:
- Detail upload (judul, artis, nomor order)
- Total pembayaran (Rp 5.000)
- Preview audio
- Tombol "Bayar Sekarang" (simulasi)
- Tombol "Batal"

### 4. Update View Upload

**File**: `resources/views/music/upload.blade.php`

**Perubahan:**
- Tambah alert warning dengan informasi biaya
- Ubah tombol submit: "Upload" → "Lanjut ke Pembayaran"
- Tambah variable `$uploadFee` untuk display

### 5. Route Baru

**File**: `routes/web.php`

```php
Route::get('music/upload/{order}/checkout', [MusicController::class, 'uploadCheckout'])
    ->name('music.upload.checkout')
    ->middleware('can:upload-music');

Route::post('music/upload/{order}/pay', [MusicController::class, 'uploadPay'])
    ->name('music.upload.pay')
    ->middleware('can:upload-music');
```

---

## Flow Upload Musik

### Sebelum (Gratis)
```
1. User buka form upload
2. User isi form dan upload file
3. File langsung tersimpan
4. Musik langsung tersedia
```

### Sesudah (Berbayar)
```
1. User buka form upload
2. User lihat informasi biaya Rp 5.000
3. User isi form dan upload file
4. File tersimpan di folder temporary
5. Order dibuat dengan status pending
6. Redirect ke halaman checkout
7. User klik "Bayar Sekarang" (simulasi)
8. File dipindahkan ke folder permanent
9. Record Music dibuat
10. Order status = paid
11. File temporary dihapus
12. Musik tersedia di library user
```

---

## Database Schema

### Table: `music_upload_orders`

```sql
CREATE TABLE music_upload_orders (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    order_number VARCHAR(255) UNIQUE,
    amount INT DEFAULT 5000,
    status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(255) NULL,
    paid_at TIMESTAMP NULL,
    temp_title VARCHAR(255) NULL,
    temp_artist VARCHAR(255) NULL,
    temp_file_path VARCHAR(255) NULL,
    music_id BIGINT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (music_id) REFERENCES music(id) ON DELETE SET NULL
);
```

---

## File Storage Structure

### Temporary Files
```
storage/app/public/music-uploads-temp/
├── lagu-test-temp-1-1234567890.mp3
└── another-song-temp-2-1234567891.mp3
```

**Karakteristik:**
- Filename pattern: `{slug}-temp-{user_id}-{timestamp}.{ext}`
- Disimpan saat user submit form upload
- Dihapus setelah pembayaran berhasil

### Permanent Files
```
storage/app/public/music-uploads/
├── lagu-test-1-1234567890.mp3
└── another-song-2-1234567891.mp3
```

**Karakteristik:**
- Filename pattern: `{slug}-{user_id}-{timestamp}.{ext}`
- Dipindahkan dari temp setelah payment
- Digunakan untuk playback di undangan

---

## Security & Validation

### Upload Validation
- File type: `mp3, ogg, wav` only
- Max size: 15MB (15360 KB)
- Title: required, max 100 chars
- Artist: optional, max 100 chars

### Payment Validation
- User must own the order
- Order must be pending (not paid yet)
- File must exist in temporary folder

### Access Control
- Upload form: requires `upload-music` permission
- Checkout: requires `upload-music` permission
- Payment: requires `upload-music` permission
- User can only access their own orders

---

## Testing Scenarios

### Scenario 1: Upload dengan Pembayaran
1. Login sebagai user
2. Buka `/music/upload`
3. Lihat alert biaya Rp 5.000
4. Isi form (title, artist, file)
5. Submit → redirect ke checkout
6. Lihat detail order dan preview audio
7. Klik "Bayar Sekarang"
8. **Expected**: 
   - Payment berhasil
   - Redirect ke `/music`
   - Success message muncul
   - Lagu muncul di galeri dengan badge "Upload Saya"

### Scenario 2: Cancel Upload
1. Login sebagai user
2. Buka `/music/upload`
3. Isi form dan submit
4. Di halaman checkout, klik "Batal"
5. **Expected**:
   - Redirect ke `/music`
   - File temporary tetap ada (bisa dibersihkan dengan cron job)
   - Order tetap pending

### Scenario 3: Multiple Uploads
1. Login sebagai user
2. Upload 3 lagu berbeda
3. Bayar semua
4. **Expected**:
   - 3 order terbuat
   - 3 lagu muncul di galeri
   - Total biaya: Rp 15.000

### Scenario 4: Access Control
1. User A buat order upload
2. Logout, login sebagai User B
3. Coba akses `/music/upload/{order_A}/checkout`
4. **Expected**: 403 Forbidden

---

## Integration dengan Fitur Lain

### Music Library
- Musik yang diupload (setelah paid) muncul di galeri
- Badge "Upload Saya" untuk identifikasi
- Hanya muncul untuk pemilik (privacy)

### Invitation Form
- Musik upload muncul di dropdown "Upload Saya"
- Bisa dipilih untuk background music undangan
- Audio preview tersedia

### Admin Panel
- Admin bisa lihat semua musik termasuk upload user
- Admin bisa toggle aktif/nonaktif
- Admin bisa hapus musik user (dengan validasi)

---

## Future Enhancements

### Recommended
1. **Payment Gateway Integration**: Integrasi dengan Midtrans/Xendit
2. **Cleanup Cron Job**: Hapus file temporary yang > 24 jam
3. **Upload History**: Halaman riwayat upload dan pembayaran
4. **Bulk Upload**: Upload multiple lagu sekaligus dengan diskon
5. **Refund System**: Refund jika upload gagal

### Nice to Have
- Email notification setelah payment berhasil
- Invoice/receipt download
- Upload progress bar dengan percentage
- Audio quality check sebelum upload
- Automatic metadata extraction (duration, bitrate)

---

## Pricing Strategy

### Current
- **Upload Fee**: Rp 5.000 per lagu
- **Payment Method**: Simulasi (development)
- **Refund**: Not implemented

### Considerations
- Biaya cukup terjangkau untuk user
- Mencegah spam upload
- Menjaga kualitas dan eksklusivitas
- Revenue stream untuk maintenance server

---

## Rollback Plan

Jika perlu rollback ke upload gratis:

### 1. Revert Controller
```php
public function userUpload(Request $request)
{
    // Langsung simpan ke permanent folder
    $path = $file->storeAs('music-uploads', $filename, 'public');
    
    Music::create([
        'title' => $request->title,
        'artist' => $request->artist,
        'file_path' => $path,
        'type' => 'free',
        'price' => 0,
        'is_active' => true,
        'uploaded_by' => auth()->id(),
    ]);
    
    return redirect()->route('music.index')
        ->with('success', 'Lagu berhasil diupload.');
}
```

### 2. Remove Routes
```php
// Hapus route checkout dan pay
```

### 3. Revert View
```blade
<!-- Hapus alert biaya -->
<!-- Ubah tombol kembali ke "Upload" -->
```

### 4. Drop Table (Optional)
```bash
php artisan migrate:rollback --step=1
```

---

## Notes

- File temporary akan menumpuk jika user tidak menyelesaikan pembayaran
- Perlu cron job untuk cleanup file temporary
- Simulasi payment untuk development, perlu integrasi payment gateway untuk production
- Order number format: `MUP-{UNIQID}` (Music Upload)

---

## Checklist Testing

- [ ] Form upload menampilkan biaya Rp 5.000
- [ ] Upload file tersimpan di folder temporary
- [ ] Order terbuat dengan status pending
- [ ] Redirect ke checkout berhasil
- [ ] Halaman checkout menampilkan detail order
- [ ] Preview audio berfungsi
- [ ] Payment berhasil
- [ ] File dipindahkan ke permanent folder
- [ ] Record Music terbuat
- [ ] Order status berubah jadi paid
- [ ] File temporary terhapus
- [ ] Musik muncul di galeri dengan badge "Upload Saya"
- [ ] User lain tidak bisa akses order user lain

---

**Status**: ✅ IMPLEMENTED
**Migration**: ✅ DONE
**Testing**: ⏳ PENDING
**Production Ready**: ⏳ Need payment gateway integration
