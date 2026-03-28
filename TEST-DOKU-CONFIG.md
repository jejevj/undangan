# Test DOKU Configuration - Quick Guide

## Quick Test (5 Menit)

### 1. Login sebagai Admin ✅
```
URL: http://127.0.0.1:8000/login
Email: admin@example.com
Password: [your admin password]
```

### 2. Akses Menu Payment Gateway ✅
```
Sidebar → Pembayaran → Konfigurasi Gateway
URL: http://127.0.0.1:8000/dash/payment-gateway
```

**Expected:**
- Halaman list konfigurasi muncul
- Tombol "Tambah Konfigurasi" visible
- Table kosong (jika belum ada data)

### 3. Create Configuration ✅
```
1. Klik "Tambah Konfigurasi"
2. Isi form:
   Provider: DOKU
   Environment: Sandbox
   Client ID: TEST-CLIENT-ID-123
   Secret Key: TEST-SECRET-KEY-456
   Base URL: https://api-sandbox.doku.com
   Status: ✓ Aktifkan konfigurasi ini
3. Klik "Simpan"
```

**Expected:**
- Redirect ke list page
- Success message muncul
- Configuration baru muncul di table
- Badge "Sandbox" dan "Aktif" muncul

### 4. Test Connection ✅
```
1. Di list page
2. Klik tombol icon "plug" (Test Connection)
3. Wait for response
```

**Expected:**
- Loading spinner muncul
- Alert muncul dengan hasil test
- Success: "✅ Koneksi ke DOKU API berhasil!"
- Error: "❌ [error message]"

### 5. Edit Configuration ✅
```
1. Klik tombol "Edit" (icon pencil)
2. Ubah Environment ke "Production"
3. Ubah Base URL ke "https://api.doku.com"
4. Kosongkan Secret Key (tidak diubah)
5. Klik "Update"
```

**Expected:**
- Redirect ke list page
- Success message muncul
- Badge berubah jadi "Production"
- Data terupdate

### 6. Check Database ✅
```sql
SELECT * FROM payment_gateway_configs;
```

**Expected:**
- Record ada
- secret_key terenkripsi (bukan plain text)
- is_active = 1
- environment = 'production'

### 7. Check Encryption ✅
```php
// Di tinker
php artisan tinker

$config = \App\Models\PaymentGatewayConfig::first();
echo $config->secret_key; // Encrypted
echo $config->decrypted_secret_key; // Decrypted
```

**Expected:**
- `secret_key` = encrypted string
- `decrypted_secret_key` = plain text

### 8. Test Permission ✅
```
1. Logout
2. Login sebagai user biasa (bukan admin)
3. Coba akses: /dash/payment-gateway
```

**Expected:**
- 403 Forbidden atau redirect
- Menu tidak muncul di sidebar

### 9. Delete Configuration ✅
```
1. Login sebagai admin
2. Di list page, klik tombol "Delete" (icon trash)
3. Konfirmasi delete
```

**Expected:**
- Confirmation modal muncul
- Setelah confirm, record terhapus
- Success message muncul

---

## Test dengan DOKU Credentials Real

### Sandbox Test
```
1. Daftar di: https://jokul.doku.com/
2. Get sandbox credentials
3. Input ke form:
   Client ID: [Your Sandbox Client ID]
   Secret Key: [Your Sandbox Secret Key]
   Base URL: https://api-sandbox.doku.com
4. Test connection
```

**Expected:**
- Connection success
- Ready untuk create payment

### Production Test (Hati-hati!)
```
1. Get production credentials dari DOKU
2. Input ke form:
   Environment: Production
   Client ID: [Your Production Client ID]
   Secret Key: [Your Production Secret Key]
   Base URL: https://api.doku.com
3. Test connection
```

**Warning:** Production mode = real transactions!

---

## Troubleshooting

### Menu tidak muncul
```bash
# Clear cache
php artisan optimize:clear

# Re-seed menu
php artisan db:seed --class=PaymentGatewayMenuSeeder

# Refresh browser (Ctrl+F5)
```

### Permission denied
```bash
# Re-seed permissions
php artisan db:seed --class=PaymentGatewayPermissionSeeder

# Logout & login again
```

### Secret key tidak terenkripsi
```bash
# Check APP_KEY
cat .env | grep APP_KEY

# Regenerate if needed
php artisan key:generate

# Re-create config
```

### Test connection gagal
```
Check:
1. Internet connection
2. DOKU credentials correct
3. Base URL correct
4. Firewall/proxy settings
```

---

## Expected Results

### ✅ Success Criteria
- [x] Menu muncul di sidebar
- [x] Bisa create configuration
- [x] Secret key terenkripsi
- [x] Test connection works
- [x] Bisa edit & delete
- [x] Permission works
- [x] Only admin can access

### ❌ Common Issues
- Menu tidak muncul → Clear cache
- Permission denied → Re-seed permissions
- Test connection fail → Check credentials
- Encryption error → Check APP_KEY

---

## Database Check

### Check Table Structure
```sql
DESCRIBE payment_gateway_configs;
```

### Check Data
```sql
SELECT 
    id,
    provider,
    environment,
    client_id,
    LEFT(secret_key, 20) as secret_preview,
    base_url,
    is_active,
    created_at
FROM payment_gateway_configs;
```

### Check Encryption
```sql
-- Secret key should be encrypted (long string)
SELECT LENGTH(secret_key) as key_length 
FROM payment_gateway_configs;

-- Should be > 100 characters if encrypted
```

---

## API Test (Manual)

### Test DOKU API Directly
```bash
# Using curl
curl -X GET https://api-sandbox.doku.com \
  -H "Client-Id: YOUR_CLIENT_ID"

# Expected: 401 or 200 (API is reachable)
```

### Test from Application
```php
// In tinker
php artisan tinker

$config = \App\Models\PaymentGatewayConfig::getActive('doku');
$response = \Illuminate\Support\Facades\Http::get($config->base_url);
echo $response->status(); // Should be 401 or 200
```

---

## Next Steps

After successful testing:

1. ✅ Phase 1 complete
2. ⏳ Start Phase 2: Payment Methods
3. ⏳ Implement DOKU Service
4. ⏳ Create payment flow
5. ⏳ Test end-to-end

---

## Quick Commands

```bash
# Clear all cache
php artisan optimize:clear

# Re-seed permissions
php artisan db:seed --class=PaymentGatewayPermissionSeeder

# Re-seed menu
php artisan db:seed --class=PaymentGatewayMenuSeeder

# Check routes
php artisan route:list --name=payment-gateway

# Check permissions
php artisan tinker
>>> \Spatie\Permission\Models\Permission::where('name', 'like', 'payment-gateway%')->get();

# Check menu
php artisan tinker
>>> \App\Models\Menu::where('slug', 'like', '%payment%')->get();
```

---

## Screenshots Checklist

- [ ] Menu di sidebar
- [ ] List page (empty)
- [ ] Create form
- [ ] List page (with data)
- [ ] Test connection success
- [ ] Edit form
- [ ] Delete confirmation
- [ ] Permission denied (user)

---

**Testing Complete!** ✅

Ready untuk Phase 2 Implementation.
