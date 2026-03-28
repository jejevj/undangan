# DOKU Payment Gateway - Final Setup Guide

## Error: Unauthorized. Signature (4017300)

Error ini terjadi karena **private key belum diisi** atau **tidak match** dengan public key yang di-upload ke DOKU.

## ✅ Solusi Lengkap - Step by Step

### Step 1: Generate RSA Keys

Jalankan script generator yang sudah saya buatkan:

```bash
cd C:\laragon\www\idorganizer\undangan
php generate_doku_keys.php
```

**Output:**
```
===========================================
  DOKU RSA Key Generator
===========================================

Generating RSA key pair (2048 bit)...
✓ Keys generated successfully!

===========================================
  FILES CREATED
===========================================
1. doku_private.key (PRIVATE - Keep Secret!)
2. doku_public.key (PUBLIC - Upload to DOKU)
```

File yang dibuat:
- `doku_private.key` - **RAHASIA!** Jangan share ke siapapun
- `doku_public.key` - Upload ke DOKU Dashboard

### Step 2: Upload Public Key ke DOKU Dashboard

#### A. Login ke DOKU Jokul
1. Buka: https://jokul.doku.com/
2. Login dengan akun Anda
3. Pilih environment:
   - **Sandbox** (untuk testing)
   - **Production** (untuk live)

#### B. Upload Public Key
1. Menu: **Settings** > **SNAP API** > **Public Key Management**
2. Klik **Add Public Key** atau **Upload Public Key**
3. Copy isi file `doku_public.key`:
   ```bash
   notepad doku_public.key
   # Copy semua isi (termasuk BEGIN dan END)
   ```
4. Paste ke form di DOKU Dashboard
5. Klik **Submit**

#### C. Tunggu Approval
- **Sandbox**: Biasanya instant approval (langsung aktif)
- **Production**: Bisa butuh 1-2 hari kerja untuk review

#### D. Verify Status
- Status harus **Approved** atau **Active**
- Jika masih **Pending**, tunggu approval dari DOKU

### Step 3: Input Keys ke Aplikasi

#### A. Login Dashboard Admin
1. Buka browser
2. Login ke dashboard admin aplikasi
3. Menu: **Payment Gateway**

#### B. Edit atau Create Konfigurasi
- Jika sudah ada: Klik **Edit** (icon pensil)
- Jika belum ada: Klik **Tambah Konfigurasi**

#### C. Isi Basic Configuration

**Provider**: DOKU (default)

**Environment**: 
- Pilih **Production** (sesuai credentials Anda)

**Client ID**: 
```
BRN-0204-1754870435962
```
(dari DOKU Dashboard)

**Secret Key**: 
```
SK-xxxxxxxxxxxxx
```
(dari DOKU Dashboard)

**Base URL**:
```
https://api.doku.com
```
(untuk production)

#### D. Isi SNAP API Configuration

Scroll ke section **SNAP API Configuration (Optional)**

**Private Key**:
```bash
# Copy dari file
notepad doku_private.key
# Copy SEMUA isi termasuk:
# -----BEGIN PRIVATE KEY-----
# ...
# -----END PRIVATE KEY-----
```

Paste ke field **Private Key** di form.

**Public Key**:
```bash
# Copy dari file
notepad doku_public.key
# Copy SEMUA isi termasuk:
# -----BEGIN PUBLIC KEY-----
# ...
# -----END PUBLIC KEY-----
```

Paste ke field **Public Key** di form.

**DOKU Public Key**:
- Download dari DOKU Dashboard setelah public key Anda di-approve
- Copy isi file dan paste ke field **DOKU Public Key**

**Issuer** (optional):
```
nama-merchant-anda
```

**Auth Code** (optional):
- Kosongkan jika tidak ada

#### E. Simpan
1. Klik **Simpan** atau **Update**
2. Verify status indicator:
   - ✅ Check icon = Field sudah terisi
   - ❌ Times icon = Field belum terisi

### Step 4: Test Connection

1. Kembali ke **Payment Gateway** index
2. Klik tombol **Test Connection** (icon plug)
3. Tunggu response

**Expected Success Response:**
```json
{
    "success": true,
    "message": "Koneksi ke DOKU API berhasil! Credentials valid.",
    "environment": "production",
    "token_expires_in": "900 seconds"
}
```

**If Still Error:**
Lanjut ke troubleshooting di bawah.

## 🔍 Troubleshooting

### Error Masih "Unauthorized. Signature"

#### Kemungkinan 1: Private Key Belum Diisi
**Cek:**
- Login dashboard admin
- Edit konfigurasi payment gateway
- Lihat status indicator di sidebar
- Jika ❌ Times icon di Private Key = belum terisi

**Solution:**
- Paste private key dari file `doku_private.key`
- Pastikan lengkap dengan header/footer
- Simpan

#### Kemungkinan 2: Public Key Belum Di-Upload ke DOKU
**Cek:**
- Login ke DOKU Dashboard
- Menu: Settings > SNAP API > Public Key Management
- Lihat status public key

**Solution:**
- Upload file `doku_public.key` ke DOKU
- Tunggu approval
- Test lagi

#### Kemungkinan 3: Public Key Belum Di-Approve
**Cek:**
- Status di DOKU Dashboard
- Jika **Pending** = belum di-approve

**Solution:**
- Tunggu approval dari DOKU (1-2 hari kerja untuk production)
- Atau hubungi support DOKU untuk mempercepat

#### Kemungkinan 4: Keys Tidak Match
**Cek:**
- Apakah public key yang di-upload match dengan private key yang diinput?

**Solution:**
```bash
# Generate ulang public key dari private key
openssl rsa -in doku_private.key -pubout -out doku_public_verify.key

# Compare
diff doku_public.key doku_public_verify.key
```

Jika berbeda:
1. Upload `doku_public_verify.key` ke DOKU
2. Tunggu approval
3. Test lagi

#### Kemungkinan 5: Format Private Key Salah
**Cek:**
- Apakah private key dimulai dengan `-----BEGIN PRIVATE KEY-----`?
- Apakah diakhiri dengan `-----END PRIVATE KEY-----`?
- Apakah ada spasi di awal/akhir?

**Solution:**
- Copy ulang dari file `doku_private.key`
- Jangan ketik manual
- Pastikan tidak ada karakter tambahan

### Error: "Invalid credentials" atau "Client ID not found"

**Solution:**
- Verify Client ID dan Secret Key dari DOKU Dashboard
- Pastikan environment sama (sandbox/production)
- Pastikan Base URL sesuai environment

### Error: "Connection timeout"

**Solution:**
- Cek koneksi internet
- Cek firewall tidak block DOKU API
- Cek Base URL benar

## 📝 Checklist Final

### Persiapan
- [ ] Generate RSA keys (`php generate_doku_keys.php`)
- [ ] Backup `doku_private.key` ke tempat aman
- [ ] Verify private key valid (`openssl rsa -in doku_private.key -check`)

### DOKU Dashboard
- [ ] Login ke https://jokul.doku.com/
- [ ] Pilih environment (Production)
- [ ] Upload `doku_public.key`
- [ ] Tunggu approval (status = Approved/Active)
- [ ] Download DOKU public key (optional)

### Aplikasi
- [ ] Login dashboard admin
- [ ] Edit/Create konfigurasi payment gateway
- [ ] Isi Client ID
- [ ] Isi Secret Key
- [ ] Isi Base URL (https://api.doku.com)
- [ ] Paste Private Key (lengkap dengan header/footer)
- [ ] Paste Public Key
- [ ] Paste DOKU Public Key (optional)
- [ ] Simpan
- [ ] Verify status indicator ✅

### Test
- [ ] Klik Test Connection
- [ ] Verify response sukses
- [ ] Token expires in = 900 seconds

## 🎯 Quick Reference

### Files Generated
```
doku_private.key    - Private key (RAHASIA!)
doku_public.key     - Public key (Upload ke DOKU)
```

### DOKU Dashboard URLs
```
Sandbox:    https://jokul.doku.com/
Production: https://jokul.doku.com/
```

### API Base URLs
```
Sandbox:    https://api-sandbox.doku.com
Production: https://api.doku.com
```

### Constructor Parameters (Fixed)
```php
new Snap(
    $privateKey,      // 1. Private key (required)
    $publicKey,       // 2. Public key (required)
    $dokuPublicKey,   // 3. DOKU public key (required)
    $clientId,        // 4. Client ID (required)
    $issuer,          // 5. Issuer (optional)
    $isProduction,    // 6. Boolean (required)
    $secretKey,       // 7. Secret key (required)
    $authCode         // 8. Auth code (optional)
);
```

## 📞 Support

### DOKU Support
- Website: https://jokul.doku.com/
- Email: support@doku.com
- Phone: (021) 2953 2888

### Pertanyaan ke DOKU:
1. "Apakah public key saya sudah approved?"
2. "Bagaimana cara verify public key sudah benar?"
3. "Kenapa signature saya invalid?"

---

**Updated**: 2026-03-28
**Status**: Ready for production
**Next**: Test connection dengan credentials real
