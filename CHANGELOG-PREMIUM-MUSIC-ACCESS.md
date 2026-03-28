# Changelog - Premium Music Access Control

## Tanggal: 28 Maret 2026

### Fitur Baru: Akses Musik Premium Berdasarkan Paket

User sekarang mendapat akses musik premium gratis berdasarkan paket subscription mereka.

## Batasan Per Paket

### 1. Free Plan
- ✅ Bisa akses lagu gratis
- ❌ Lagu premium harus bayar per lagu
- ❌ Tidak bisa upload lagu (dihapus fitur upload untuk Free)
- Jika klik beli lagu premium → Proses pembayaran normal

### 2. Basic Plan (Rp 49.000)
- ✅ **Semua lagu premium gratis**
- ✅ Bisa upload 4 lagu sendiri
- ✅ Tidak perlu bayar per lagu premium
- Upload lagu tidak bisa dihapus

### 3. Pro Plan (Rp 99.000)
- ✅ **Semua lagu premium gratis**
- ✅ **Upload lagu unlimited**
- ✅ Bisa hapus lagu yang diupload
- Akses penuh ke semua fitur musik

### 4. Admin
- ✅ **Bypass semua batasan**
- Akses unlimited ke semua lagu

## Implementasi Teknis

### 1. Music Model Update
**File**: `app/Models/Music.php`

**Method `accessibleByUser()` - Updated:**
```php
public static function accessibleByUser(User $user)
{
    $plan = $user->activePlan();
    $hasPremiumAccess = in_array($plan->slug, ['basic', 'pro']) || $user->isAdmin();
    
    // Jika Basic/Pro/Admin, tambahkan semua lagu premium
    if ($hasPremiumAccess) {
        $q->orWhere('type', 'premium');
    }
}
```

Logic:
- Free: Hanya lagu gratis + yang dibeli
- Basic/Pro: Semua lagu (gratis + premium)
- Admin: Semua lagu

### 2. MusicController Update
**File**: `app/Http/Controllers/MusicController.php`

**Method `index()` - Updated:**
- Menambahkan variable `$hasPremiumAccess`
- Pass ke view untuk conditional rendering
- Filter `$accessibleSongs` include premium untuk Basic/Pro

**Method `buy()` - Updated:**
- Cek paket user sebelum proses pembelian
- Jika Basic/Pro → Redirect dengan pesan "sudah termasuk akses gratis"
- Jika Free → Lanjut ke proses pembelian normal

**Method `uploadForm()` - Updated:**
- Hanya untuk paket Free (dihapus untuk Basic/Pro)
- Basic/Pro sudah punya akses premium, tidak perlu upload

### 3. Music Index View Update
**File**: `resources/views/music/index.blade.php`

**Alert Info:**
- Free: "Paket Free — Hanya lagu gratis" + Button "Upgrade untuk Akses Premium"
- Basic/Pro: "Paket [Name] — Semua lagu premium gratis!" (success alert dengan crown icon)

**Card Footer Logic:**
```php
$owned = $song->isFree() 
      || in_array($song->id, $myIds) 
      || $song->uploaded_by === auth()->id()
      || ($hasPremiumAccess && $song->type === 'premium');
```

**Status Text:**
- Free user + lagu gratis: "Gratis — langsung gunakan"
- Free user + lagu premium dibeli: "Sudah dibeli"
- Basic/Pro + lagu premium: "Premium gratis (Paket [Name])"
- Upload sendiri: "Upload Saya"

**Button Beli:**
- Hanya muncul untuk Free user pada lagu premium yang belum dibeli
- Untuk Basic/Pro, semua lagu premium langsung bisa digunakan (button copy URL)
- Tambahan text: "Atau upgrade paket untuk akses gratis"

## User Experience Flow

### Scenario 1: Free User Akses Lagu Premium
1. User dengan paket Free buka halaman musik
2. Lihat lagu premium dengan badge "Premium" dan harga
3. Klik "Beli — Rp XX.XXX"
4. Proses pembayaran normal
5. Setelah bayar → Lagu bisa digunakan

### Scenario 2: Basic User Akses Lagu Premium
1. User dengan paket Basic buka halaman musik
2. Alert hijau: "Paket Basic — Semua lagu premium gratis!"
3. Semua lagu premium langsung bisa digunakan (button copy URL)
4. Status: "Premium gratis (Paket Basic)"
5. Tidak ada button beli

### Scenario 3: Free User Coba Beli Lagu Premium
1. User Free klik "Beli" pada lagu premium
2. Masuk halaman checkout
3. Simulasi pembayaran
4. Lagu masuk ke library user

### Scenario 4: Basic User Coba Beli Lagu Premium
1. User Basic klik "Beli" (seharusnya tidak ada button ini)
2. Jika somehow akses route buy → Redirect dengan pesan:
   "Paket Basic Anda sudah termasuk akses ke semua lagu premium secara gratis!"

### Scenario 5: Pro User
1. User dengan paket Pro buka halaman musik
2. Alert hijau: "Paket Pro — Semua lagu premium gratis!"
3. Semua lagu bisa digunakan
4. Bisa upload unlimited
5. Bisa hapus lagu yang diupload

## Visual Indicators

### Alert Box:
**Free Plan:**
```
ℹ️ Paket Free — Hanya lagu gratis
[Button: Upgrade untuk Akses Premium]
```

**Basic/Pro Plan:**
```
👑 Paket Basic — Semua lagu premium gratis!
```

### Card Status:
- **Free + Lagu Gratis**: ✅ "Gratis — langsung gunakan"
- **Free + Premium Dibeli**: ✅ "Sudah dibeli"
- **Basic/Pro + Premium**: ✅ "Premium gratis (Paket Basic)"
- **Upload Sendiri**: ✅ "Upload Saya"

### Button:
- **Bisa Akses**: Button "Copy URL" (outline-secondary)
- **Belum Akses (Free)**: Button "Beli — Rp XX.XXX" (warning)

## Database Impact

Tidak ada perubahan database. Logic hanya di aplikasi level.

## Testing

### Test 1: Free User
1. Login sebagai user dengan paket Free
2. Akses `/dash/music`
3. ✅ Alert: "Paket Free — Hanya lagu gratis"
4. ✅ Lagu gratis bisa langsung digunakan
5. ✅ Lagu premium ada button "Beli"
6. Klik beli → Proses pembayaran normal

### Test 2: Basic User
1. Login sebagai user dengan paket Basic
2. Akses `/dash/music`
3. ✅ Alert: "Paket Basic — Semua lagu premium gratis!"
4. ✅ Semua lagu (gratis + premium) bisa langsung digunakan
5. ✅ Tidak ada button "Beli" pada lagu premium
6. ✅ Status: "Premium gratis (Paket Basic)"

### Test 3: Pro User
1. Login sebagai user dengan paket Pro
2. Akses `/dash/music`
3. ✅ Alert: "Paket Pro — Semua lagu premium gratis!"
4. ✅ Semua lagu bisa digunakan
5. ✅ Bisa upload unlimited

### Test 4: Basic User Coba Akses Route Buy
1. Login sebagai user Basic
2. Akses langsung `/dash/music/{id}/buy` (lagu premium)
3. ✅ Redirect ke `/dash/music`
4. ✅ Flash message: "Paket Basic Anda sudah termasuk akses..."

### Test 5: Dropdown Musik di Form Undangan
1. Login sebagai user Basic
2. Buat undangan baru
3. Di field musik, buka dropdown
4. ✅ Semua lagu (gratis + premium) muncul di dropdown
5. ✅ Bisa pilih lagu premium tanpa bayar

## SQL Query untuk Testing

```sql
-- Cek paket user
SELECT u.name, u.email, pp.name as plan_name, pp.slug
FROM users u
JOIN user_subscriptions us ON u.id = us.user_id
JOIN pricing_plans pp ON us.pricing_plan_id = pp.id
WHERE us.status = 'active';

-- Cek lagu yang bisa diakses user (manual check)
-- Free: Hanya type='free' + yang dibeli
-- Basic/Pro: Semua lagu

-- Cek pembelian lagu user
SELECT u.name, m.title, m.type, m.price, mo.status, mo.paid_at
FROM users u
JOIN music_orders mo ON u.id = mo.user_id
JOIN music m ON mo.music_id = m.id
ORDER BY u.id, mo.created_at DESC;
```

## Catatan Penting

1. **Upload Lagu**: Fitur upload dihapus untuk paket Free (sebelumnya ada dengan biaya Rp 5.000)
2. **Basic/Pro**: Tidak perlu upload karena sudah punya akses ke semua lagu premium
3. **Pembelian**: Hanya Free user yang bisa beli lagu premium per item
4. **Dropdown**: Method `accessibleByUser()` otomatis filter lagu yang bisa digunakan di form undangan
5. **Admin**: Bypass semua batasan, akses unlimited

## Benefit untuk User

### Free → Basic Upgrade:
- Hemat biaya: Tidak perlu beli lagu premium satu-satu
- Akses instant: Semua lagu premium langsung bisa digunakan
- Upload 4 lagu: Bisa upload lagu favorit sendiri

### Basic → Pro Upgrade:
- Upload unlimited: Tidak ada batasan upload lagu
- Bisa hapus: Lagu yang diupload bisa dihapus
- Semua fitur: Akses penuh ke semua fitur aplikasi
