# Changelog - Premium Template Access Control

## Tanggal: 28 Maret 2026

### Fitur Baru: Pembatasan Akses Template Premium Berdasarkan Paket

User sekarang dibatasi aksesnya ke template premium berdasarkan paket subscription mereka.

## Batasan Per Paket

### 1. Free Plan
- ❌ **Tidak bisa menggunakan template premium sama sekali**
- ✅ Hanya bisa menggunakan template gratis
- Jika klik template premium → Modal upgrade muncul

### 2. Basic Plan (Rp 49.000)
- ✅ **Bisa menggunakan 3 template premium gratis**
- Tidak perlu bayar biaya template
- Setelah 3 slot terpakai → Modal upgrade muncul

### 3. Pro Plan (Rp 99.000)
- ✅ **Template premium unlimited**
- Tidak ada batasan

### 4. Admin
- ✅ **Bypass semua batasan**
- Akses unlimited ke semua template

## Implementasi Teknis

### 1. Migration
**File**: `database/migrations/2026_03_28_001328_add_max_premium_templates_to_pricing_plans_table.php`

Menambahkan kolom:
- `max_premium_templates` (integer, nullable)
- 0 = tidak bisa akses premium
- null = unlimited
- Angka = jumlah maksimal template premium

### 2. PricingPlan Model Update
**File**: `app/Models/PricingPlan.php`

- Menambahkan `max_premium_templates` ke fillable
- Menambahkan cast ke integer

### 3. PricingPlanSeeder Update
**File**: `database/seeders/PricingPlanSeeder.php`

Update data plan:
```php
Free: max_premium_templates = 0
Basic: max_premium_templates = 3
Pro: max_premium_templates = null (unlimited)
```

### 4. User Model - New Methods
**File**: `app/Models/User.php`

**Method `premiumInvitationCount()`:**
- Menghitung jumlah undangan dengan template premium yang sudah dibuat user
- Return: integer

**Method `canUsePremiumTemplate()`:**
- Cek apakah user masih bisa menggunakan template premium
- Logic:
  - Admin → true
  - max_premium_templates = 0 → false
  - max_premium_templates = null → true (unlimited)
  - Lainnya → cek apakah masih ada slot
- Return: boolean

**Method `remainingPremiumTemplates()`:**
- Menghitung sisa slot template premium
- Return: int|null (null = unlimited)

### 5. Template Grid Partial Update
**File**: `resources/views/invitations/partials/template-grid.blade.php`

Fitur:
- Cek akses user ke setiap template
- Template premium yang tidak bisa diakses:
  - Border warning
  - Opacity 85%
  - Thumbnail grayscale 50%
  - Badge "Premium" dengan lock icon
  - Button "Upgrade untuk Akses" (trigger modal)
- Template yang bisa diakses:
  - Button "Gunakan" normal

### 6. Upgrade Modal
**File**: `resources/views/invitations/select-template.blade.php`

Modal Bootstrap dengan:
- Header warning dengan crown icon
- Info paket user saat ini
- Jumlah template premium yang sudah digunakan
- Sisa slot premium (jika ada)
- Keuntungan upgrade ke Basic
- Harga paket Basic (Rp 49.000)
- Button "Upgrade Sekarang" → redirect ke subscription page
- Button "Nanti Saja" → close modal

JavaScript:
- Update nama template di modal saat button diklik
- Data attribute `data-template-name` untuk pass nama template

## User Experience Flow

### Scenario 1: Free User Klik Template Premium
1. User dengan paket Free klik template premium
2. Modal upgrade muncul
3. Modal menampilkan:
   - "Paket Anda: Free"
   - "❌ Tidak bisa menggunakan template premium"
   - Keuntungan upgrade ke Basic
4. User klik "Upgrade Sekarang" → redirect ke subscription page

### Scenario 2: Basic User dengan Slot Tersisa
1. User dengan paket Basic (sudah pakai 1 premium template)
2. Klik template premium lain
3. Template bisa digunakan langsung (masih ada 2 slot)
4. Button "Gunakan" → lanjut ke create invitation

### Scenario 3: Basic User Slot Habis
1. User dengan paket Basic (sudah pakai 3 premium template)
2. Klik template premium lain
3. Modal upgrade muncul
4. Modal menampilkan:
   - "Paket Anda: Basic"
   - "Template Premium: 3 / 3"
   - "Limit template premium tercapai"
   - Keuntungan upgrade ke Pro
5. User klik "Upgrade Sekarang" → redirect ke subscription page

### Scenario 4: Pro User
1. User dengan paket Pro
2. Semua template (free & premium) bisa diakses tanpa batasan
3. Tidak ada modal upgrade

## Visual Indicators

### Template Card yang Tidak Bisa Diakses:
- Border kuning (border-warning)
- Opacity 85%
- Thumbnail grayscale 50%
- Badge "Premium" dengan lock icon di pojok kanan atas
- Button kuning "Upgrade untuk Akses"

### Template Card yang Bisa Diakses:
- Border normal
- Opacity 100%
- Thumbnail full color
- Button biru "Gunakan"

## Database Changes

### Table: pricing_plans
```sql
ALTER TABLE pricing_plans 
ADD COLUMN max_premium_templates INT NULL DEFAULT 0 
COMMENT 'Jumlah maksimal template premium. 0=tidak bisa, null=unlimited';
```

### Data Update:
```sql
UPDATE pricing_plans SET max_premium_templates = 0 WHERE slug = 'free';
UPDATE pricing_plans SET max_premium_templates = 3 WHERE slug = 'basic';
UPDATE pricing_plans SET max_premium_templates = NULL WHERE slug = 'pro';
```

## Testing

### Test 1: Free User
1. Login sebagai user dengan paket Free
2. Akses `/dash/invitations/select-template`
3. Klik template premium
4. ✅ Modal upgrade muncul
5. ✅ Tidak bisa akses template premium

### Test 2: Basic User (Slot Tersedia)
1. Login sebagai user dengan paket Basic (belum pakai premium)
2. Klik template premium
3. ✅ Bisa langsung gunakan
4. Buat undangan dengan template premium
5. Cek database: `invitations` → template_id mengarah ke premium template

### Test 3: Basic User (Slot Habis)
1. Login sebagai user dengan paket Basic (sudah pakai 3 premium)
2. Klik template premium lain
3. ✅ Modal upgrade muncul
4. ✅ Tidak bisa akses template premium lagi

### Test 4: Pro User
1. Login sebagai user dengan paket Pro
2. ✅ Semua template bisa diakses
3. ✅ Tidak ada batasan

### Test 5: Admin
1. Login sebagai admin
2. ✅ Semua template bisa diakses
3. ✅ Bypass semua batasan

## SQL Query untuk Testing

```sql
-- Cek paket user
SELECT u.name, u.email, pp.name as plan_name, pp.max_premium_templates
FROM users u
JOIN user_subscriptions us ON u.id = us.user_id
JOIN pricing_plans pp ON us.pricing_plan_id = pp.id
WHERE us.status = 'active';

-- Cek jumlah undangan premium per user
SELECT u.name, u.email, COUNT(*) as premium_count
FROM users u
JOIN invitations i ON u.id = i.user_id
JOIN templates t ON i.template_id = t.id
WHERE t.type = 'premium'
GROUP BY u.id;

-- Cek sisa slot premium user
SELECT 
    u.name,
    pp.max_premium_templates,
    COUNT(i.id) as used_premium,
    CASE 
        WHEN pp.max_premium_templates IS NULL THEN 'Unlimited'
        ELSE CAST(pp.max_premium_templates - COUNT(i.id) AS CHAR)
    END as remaining
FROM users u
JOIN user_subscriptions us ON u.id = us.user_id
JOIN pricing_plans pp ON us.pricing_plan_id = pp.id
LEFT JOIN invitations i ON u.id = i.user_id 
    AND i.template_id IN (SELECT id FROM templates WHERE type = 'premium')
WHERE us.status = 'active'
GROUP BY u.id;
```

## Catatan Penting

1. **Admin Bypass**: Admin tidak terkena batasan apapun
2. **Counting**: Hanya undangan yang sudah dibuat yang dihitung, draft juga dihitung
3. **Upgrade Path**: User bisa upgrade kapan saja dari subscription page
4. **No Downgrade**: Jika user downgrade, undangan premium yang sudah dibuat tetap bisa diakses
5. **Template Price**: Dengan paket Basic/Pro, user tidak perlu bayar harga template premium lagi
