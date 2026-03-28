# Troubleshooting: Unauthorized. Signature Error

## Error Message
```json
{
    "success": false,
    "message": "Gagal mendapatkan token dari DOKU API.",
    "error_code": "4017300",
    "error_message": "Unauthorized. Signature"
}
```

## Penyebab

Error ini terjadi karena **signature yang di-generate tidak valid**. DOKU API menggunakan RSA signature untuk autentikasi.

### Kemungkinan Penyebab:

1. ❌ **Private Key tidak diisi** atau kosong
2. ❌ **Private Key format salah** (bukan PEM format)
3. ❌ **Private Key tidak match dengan Public Key** yang di-upload ke DOKU
4. ❌ **Public Key belum di-upload** ke DOKU Dashboard
5. ❌ **Public Key belum di-approve** oleh DOKU
6. ❌ **Menggunakan keys yang salah** (sandbox vs production)

## Solusi

### Langkah 1: Pastikan Private Key Sudah Diisi

1. Buka dashboard admin
2. Menu **Payment Gateway** > **Edit** konfigurasi Anda
3. Scroll ke section **SNAP API Configuration**
4. Pastikan field **Private Key** sudah terisi

**Cek Status:**
- ✅ Check icon = Private key sudah terisi
- ❌ Times icon = Private key belum terisi

### Langkah 2: Generate RSA Keys (Jika Belum)

Jika Anda belum punya RSA keys, generate sekarang:

```bash
cd C:\laragon\www\idorganizer\undangan
php generate_doku_keys.php
```

Output:
- `doku_private.key` - Private key Anda (RAHASIA!)
- `doku_public.key` - Public key Anda (Upload ke DOKU)

### Langkah 3: Upload Public Key ke DOKU Dashboard

1. **Login ke DOKU Jokul**
   - URL: https://jokul.doku.com/
   - Login dengan akun Anda

2. **Pilih Environment**
   - Sandbox (untuk testing)
   - Production (untuk live)

3. **Upload Public Key**
   - Menu: **Settings** > **SNAP API** > **Public Key Management**
   - Klik **Add Public Key** atau **Upload Public Key**
   - Copy isi file `doku_public.key` dan paste
   - Atau upload file langsung
   - Klik **Submit**

4. **Tunggu Approval**
   - Sandbox: Biasanya instant approval
   - Production: Mungkin butuh review 1-2 hari kerja

5. **Verify Status**
   - Status harus **Approved** atau **Active**
   - Jika masih **Pending**, tunggu approval

### Langkah 4: Input Private Key ke Aplikasi

1. **Copy Private Key**
   ```bash
   # Windows (Git Bash)
   cat doku_private.key | clip
   
   # Atau buka dengan notepad
   notepad doku_private.key
   ```

2. **Paste ke Aplikasi**
   - Login dashboard admin
   - Menu **Payment Gateway** > **Edit** konfigurasi
   - Scroll ke **SNAP API Configuration**
   - Paste ke field **Private Key**
   - Pastikan format lengkap:
     ```
     -----BEGIN PRIVATE KEY-----
     MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDKLwpDnaqVAQus
     ...
     -----END PRIVATE KEY-----
     ```

3. **Simpan**
   - Klik **Update**
   - Verify status indicator berubah jadi ✅

### Langkah 5: Test Connection Lagi

1. Kembali ke **Payment Gateway** index
2. Klik **Test Connection**
3. Jika masih error, lanjut ke langkah berikutnya

## Verifikasi Keys

### Cek Format Private Key

Private key harus:
- ✅ Dimulai dengan `-----BEGIN PRIVATE KEY-----`
- ✅ Diakhiri dengan `-----END PRIVATE KEY-----`
- ✅ Atau `-----BEGIN RSA PRIVATE KEY-----` ... `-----END RSA PRIVATE KEY-----`
- ✅ Tidak ada spasi di awal/akhir
- ✅ Tidak ada karakter tambahan

### Cek Public Key Match dengan Private Key

```bash
# Generate public key dari private key
openssl rsa -in doku_private.key -pubout -out doku_public_verify.key

# Compare dengan public key yang di-upload
diff doku_public.key doku_public_verify.key
```

Jika ada perbedaan, berarti public key yang di-upload tidak match!

**Solution:**
1. Generate ulang public key dari private key
2. Upload public key yang baru ke DOKU
3. Tunggu approval
4. Test lagi

### Cek Private Key Valid

```bash
# Test private key valid
openssl rsa -in doku_private.key -check
```

Output jika valid:
```
RSA key ok
writing RSA key
```

Output jika invalid:
```
unable to load Private Key
```

## Skenario Khusus

### Skenario 1: Sudah Upload Public Key Tapi Masih Error

**Kemungkinan:**
- Public key belum di-approve
- Public key di-upload ke environment yang salah (sandbox vs production)
- Private key di aplikasi tidak match dengan public key yang di-upload

**Solution:**
1. Cek status approval di DOKU Dashboard
2. Pastikan environment sama (sandbox/production)
3. Generate ulang keys dan upload lagi

### Skenario 2: Keys Sudah Benar Tapi Masih Error

**Kemungkinan:**
- Private key tidak ter-decrypt dengan benar
- Ada masalah dengan Laravel encryption

**Solution:**
1. Cek APP_KEY di .env
2. Clear config cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
3. Input ulang private key

### Skenario 3: Sandbox Works, Production Tidak

**Kemungkinan:**
- Public key production belum di-upload
- Public key production belum di-approve
- Menggunakan keys sandbox untuk production

**Solution:**
1. Generate keys terpisah untuk production
2. Upload public key production ke DOKU
3. Tunggu approval (bisa 1-2 hari kerja)
4. Input private key production ke aplikasi

## Alternative: Test Tanpa SNAP API Keys

Jika Anda ingin test connection tanpa SNAP API keys terlebih dahulu:

### Catatan Penting:
DOKU library **MEMBUTUHKAN** private key untuk generate signature, bahkan untuk basic token B2B. Jadi Anda **HARUS** input private key yang valid.

### Tidak Ada Cara Bypass:
- ❌ Tidak bisa test connection tanpa private key
- ❌ Tidak bisa menggunakan secret key saja
- ✅ HARUS generate RSA keys dan upload public key ke DOKU

## Checklist Lengkap

### Persiapan Keys
- [ ] Generate RSA key pair (`php generate_doku_keys.php`)
- [ ] Backup private key ke tempat aman
- [ ] Verify private key valid (`openssl rsa -in doku_private.key -check`)
- [ ] Verify public key match (`openssl rsa -in doku_private.key -pubout`)

### Upload ke DOKU
- [ ] Login ke DOKU Jokul Dashboard
- [ ] Pilih environment yang benar (sandbox/production)
- [ ] Upload public key
- [ ] Tunggu approval
- [ ] Verify status = Approved/Active
- [ ] Download DOKU public key (optional)

### Input ke Aplikasi
- [ ] Login dashboard admin
- [ ] Edit konfigurasi payment gateway
- [ ] Paste private key (lengkap dengan header/footer)
- [ ] Paste public key (optional tapi recommended)
- [ ] Paste DOKU public key (optional)
- [ ] Isi issuer (optional)
- [ ] Simpan
- [ ] Verify status indicator ✅

### Test
- [ ] Test connection
- [ ] Jika sukses: Lanjut ke implementasi fitur
- [ ] Jika gagal: Cek log error dan ulangi langkah di atas

## Log untuk Debug

Jika masih error, cek log Laravel:

```bash
tail -f storage/logs/laravel.log
```

Atau buka file:
```
undangan/storage/logs/laravel.log
```

Cari error terkait DOKU atau signature.

## Kontak Support

### DOKU Support
- Website: https://jokul.doku.com/
- Email: support@doku.com
- Phone: (021) 2953 2888
- Dokumentasi: https://developers.doku.com/

### Pertanyaan ke DOKU:
1. "Apakah public key saya sudah approved?"
2. "Bagaimana cara verify public key sudah benar?"
3. "Kenapa signature saya invalid?"
4. "Apakah ada contoh private key yang valid?"

---

**Updated**: 2026-03-28
**Error Code**: 4017300
**Status**: Troubleshooting Guide
