# Setup DOKU dengan Keys yang Sudah Ada di Dashboard

## Situasi Anda:

Anda sudah punya di DOKU Dashboard:
- ✅ Client ID: `BRN-0204-1754870435962`
- ✅ Secret Key (Active)
- ✅ DOKU Public Key
- ✅ Merchant Public Key (sudah di-upload)

## 2 Skenario:

### Skenario A: Anda Masih Punya Private Key

Jika Anda masih punya file private key yang match dengan Merchant Public Key di dashboard:

#### 1. Copy Keys dari Dashboard
```bash
# Di DOKU Dashboard:
# 1. Klik "Reveal Key" untuk Secret Key → Copy
# 2. Klik "Salin" untuk DOKU Public Key → Copy
# 3. Klik "Salin" untuk Merchant Public Key → Copy
```

#### 2. Input ke Aplikasi
1. Login dashboard admin aplikasi
2. Menu: **Payment Gateway** > **Edit** konfigurasi
3. Isi:
   - **Client ID**: `BRN-0204-1754870435962`
   - **Secret Key**: (paste dari dashboard)
   - **Base URL**: `https://api.doku.com`
   - **Environment**: Production
4. Scroll ke **SNAP API Configuration**:
   - **Private Key**: (paste dari file private key Anda)
   - **Public Key**: (paste Merchant Public Key dari dashboard)
   - **DOKU Public Key**: (paste dari dashboard)
5. Klik **Simpan**
6. Test Connection

### Skenario B: Private Key Hilang (RECOMMENDED)

Jika Anda tidak punya private key lagi, generate ulang:

#### 1. Generate Keys Baru
```bash
cd C:\laragon\www\idorganizer\undangan
php generate_doku_keys.php
```

File yang dibuat:
- `doku_private.key` - Private key baru (SIMPAN!)
- `doku_public.key` - Public key baru

#### 2. Upload Public Key Baru ke DOKU
1. Login ke DOKU Dashboard
2. Klik **"Edit Merchant Public Key"**
3. Copy isi file `doku_public.key`:
   ```bash
   notepad doku_public.key
   # Copy SEMUA isi
   ```
4. Paste ke form di DOKU
5. Klik **Save** atau **Update**

#### 3. Copy Keys dari Dashboard
```bash
# Di DOKU Dashboard:
# 1. Klik "Reveal Key" untuk Secret Key → Copy
# 2. Klik "Salin" untuk DOKU Public Key → Copy
# 3. Merchant Public Key sekarang sudah yang baru
```

#### 4. Input ke Aplikasi
1. Login dashboard admin aplikasi
2. Menu: **Payment Gateway** > **Edit** konfigurasi
3. Isi:
   - **Client ID**: `BRN-0204-1754870435962`
   - **Secret Key**: (paste dari dashboard)
   - **Base URL**: `https://api.doku.com`
   - **Environment**: Production
4. Scroll ke **SNAP API Configuration**:
   - **Private Key**: (paste dari file `doku_private.key`)
   - **Public Key**: (paste dari file `doku_public.key`)
   - **DOKU Public Key**: (paste dari dashboard)
5. Klik **Simpan**
6. Test Connection

## Quick Commands

### Copy Keys dari File
```bash
# Windows
notepad doku_private.key
notepad doku_public.key

# Copy dengan clipboard
type doku_private.key | clip
type doku_public.key | clip
```

### Verify Keys Match
```bash
# Generate public key dari private key
openssl rsa -in doku_private.key -pubout -out verify_public.key

# Compare dengan public key yang ada
diff doku_public.key verify_public.key
```

## Expected Result

Setelah setup selesai, test connection harus sukses:

```json
{
    "success": true,
    "message": "Koneksi ke DOKU API berhasil! Credentials valid.",
    "environment": "production",
    "token_expires_in": "900 seconds"
}
```

## Troubleshooting

### Error: "Unauthorized. Signature"

**Penyebab**: Private key tidak match dengan Merchant Public Key di dashboard

**Solution**:
1. Generate keys baru
2. Upload public key baru ke DOKU (Edit Merchant Public Key)
3. Input private key baru ke aplikasi
4. Test lagi

### Error: "Invalid credentials"

**Penyebab**: Client ID atau Secret Key salah

**Solution**:
1. Copy ulang dari DOKU Dashboard
2. Pastikan tidak ada spasi di awal/akhir
3. Input ulang ke aplikasi

### Error: "Connection timeout"

**Penyebab**: Network issue atau Base URL salah

**Solution**:
1. Cek koneksi internet
2. Verify Base URL: `https://api.doku.com` (production)
3. Cek firewall tidak block

## Checklist

### Dari DOKU Dashboard
- [ ] Copy Client ID
- [ ] Reveal dan copy Secret Key
- [ ] Copy DOKU Public Key
- [ ] Copy atau upload Merchant Public Key

### Generate Keys (jika perlu)
- [ ] Run `php generate_doku_keys.php`
- [ ] Backup `doku_private.key`
- [ ] Upload `doku_public.key` ke DOKU

### Input ke Aplikasi
- [ ] Client ID
- [ ] Secret Key
- [ ] Base URL
- [ ] Environment = Production
- [ ] Private Key
- [ ] Public Key (Merchant)
- [ ] DOKU Public Key
- [ ] Simpan

### Test
- [ ] Test Connection
- [ ] Verify response sukses

---

**Rekomendasi**: Gunakan **Skenario B** (generate keys baru) untuk memastikan keys match 100%.
