# Cara Mendapatkan Private Key - Panduan Lengkap

## 🎯 Ringkasan Cepat

Private Key untuk DOKU SNAP API harus Anda generate sendiri menggunakan RSA algorithm. Ada 3 cara:

1. **Menggunakan Script PHP** (Paling Mudah) ⭐ RECOMMENDED
2. **Menggunakan OpenSSL** (Command Line)
3. **Menggunakan Online Tool** (Tidak Recommended untuk Production)

---

## ✅ Cara 1: Menggunakan Script PHP (RECOMMENDED)

### Langkah-langkah:

#### 1. Jalankan Script Generator
Saya sudah buatkan script untuk Anda. Jalankan command ini:

```bash
cd C:\laragon\www\idorganizer\undangan
php generate_doku_keys.php
```

#### 2. Output yang Dihasilkan
Script akan membuat 2 file:
- `doku_private.key` - Private Key Anda (RAHASIA!)
- `doku_public.key` - Public Key Anda (Upload ke DOKU)

Dan menampilkan isi kedua keys di terminal.

#### 3. Backup Keys
```bash
# Backup ke folder aman
mkdir C:\DOKU_Keys_Backup
copy doku_private.key C:\DOKU_Keys_Backup\
copy doku_public.key C:\DOKU_Keys_Backup\
```

#### 4. Upload Public Key ke DOKU
1. Login ke https://jokul.doku.com/
2. Menu: **Settings** > **SNAP API** > **Public Key Management**
3. Klik **Add Public Key**
4. Copy isi file `doku_public.key` dan paste
5. Klik **Submit**
6. Tunggu approval (biasanya instant untuk sandbox)

#### 5. Download DOKU Public Key
1. Setelah approved, klik **Download DOKU Public Key**
2. Simpan sebagai `doku_public_key.pem`

#### 6. Input ke Aplikasi
1. Login ke dashboard admin
2. Menu **Payment Gateway** > **Tambah Konfigurasi**
3. Isi basic fields (Client ID, Secret Key, dll)
4. Scroll ke **SNAP API Configuration**
5. Copy-paste keys:

**Private Key:**
```bash
# Windows
type doku_private.key | clip

# Atau buka file dan copy manual
notepad doku_private.key
```

**Public Key:**
```bash
type doku_public.key | clip
```

**DOKU Public Key:**
```bash
type doku_public_key.pem | clip
```

6. Klik **Simpan**
7. Test Connection

---

## 🔧 Cara 2: Menggunakan OpenSSL (Command Line)

### Windows (Git Bash)

#### 1. Buka Git Bash
Klik kanan di folder > **Git Bash Here**

#### 2. Generate Private Key
```bash
openssl genrsa -out doku_private.key 2048
```

#### 3. Generate Public Key
```bash
openssl rsa -in doku_private.key -pubout -out doku_public.key
```

#### 4. Lihat Isi Keys
```bash
cat doku_private.key
cat doku_public.key
```

#### 5. Lanjutkan ke Langkah 4-6 dari Cara 1

### Linux / macOS

Same commands, OpenSSL biasanya sudah terinstall:

```bash
# Generate keys
openssl genrsa -out doku_private.key 2048
openssl rsa -in doku_private.key -pubout -out doku_public.key

# View keys
cat doku_private.key
cat doku_public.key
```

---

## 🌐 Cara 3: Online Tool (TESTING ONLY!)

⚠️ **WARNING**: Jangan gunakan untuk production!

### Langkah-langkah:

1. Buka: https://travistidwell.com/jsencrypt/demo/
2. Klik **Generate New Keys**
3. Copy **Private Key** dan **Public Key**
4. Lanjutkan ke upload ke DOKU

⚠️ **Risiko**:
- Keys di-generate di server orang lain
- Tidak aman untuk production
- Hanya untuk testing/development

---

## 📋 Checklist Lengkap

### ✅ Generate Keys
- [ ] Jalankan `php generate_doku_keys.php`
- [ ] File `doku_private.key` dibuat
- [ ] File `doku_public.key` dibuat
- [ ] Backup kedua file ke tempat aman

### ✅ Upload ke DOKU
- [ ] Login ke DOKU Jokul Dashboard
- [ ] Upload `doku_public.key` ke DOKU
- [ ] Tunggu approval
- [ ] Download DOKU Public Key
- [ ] Simpan sebagai `doku_public_key.pem`

### ✅ Input ke Aplikasi
- [ ] Login ke dashboard admin
- [ ] Buka Payment Gateway > Tambah Konfigurasi
- [ ] Isi Client ID dan Secret Key
- [ ] Paste Private Key
- [ ] Paste Public Key
- [ ] Paste DOKU Public Key
- [ ] Isi Issuer (optional)
- [ ] Simpan konfigurasi
- [ ] Test Connection

---

## 🔒 Keamanan Private Key

### ⚠️ JANGAN PERNAH:
- ❌ Share private key ke siapapun
- ❌ Commit private key ke Git/GitHub
- ❌ Upload private key ke public server
- ❌ Kirim private key via email/chat
- ❌ Screenshot private key
- ❌ Simpan private key di cloud storage public

### ✅ HARUS:
- ✅ Backup private key di tempat aman (encrypted)
- ✅ Gunakan keys berbeda untuk sandbox dan production
- ✅ Rotate keys secara berkala (1-2 tahun)
- ✅ Simpan di aplikasi (sudah auto-encrypted)
- ✅ Delete file keys setelah input ke aplikasi

### 🛡️ Keamanan di Aplikasi
Private key Anda akan:
- ✅ Dienkripsi menggunakan Laravel encryption
- ✅ Disimpan di database terenkripsi
- ✅ Tidak pernah ditampilkan di UI
- ✅ Hanya di-decrypt saat digunakan

---

## 🔍 Verifikasi Keys

### Cek Format Private Key
Harus dimulai dan diakhiri dengan:
```
-----BEGIN PRIVATE KEY-----
...
-----END PRIVATE KEY-----
```

### Cek Format Public Key
Harus dimulai dan diakhiri dengan:
```
-----BEGIN PUBLIC KEY-----
...
-----END PUBLIC KEY-----
```

### Test Keys Valid (OpenSSL)
```bash
# Test private key valid
openssl rsa -in doku_private.key -check

# Test public key match dengan private key
openssl rsa -in doku_private.key -pubout | diff - doku_public.key
```

Jika tidak ada error, keys valid!

---

## 🆘 Troubleshooting

### Error: "openssl: command not found"
**Solution**: 
- Install Git for Windows (includes OpenSSL)
- Atau gunakan Cara 1 (PHP Script)

### Error: "unable to load Private Key"
**Solution**: 
- Cek format file (harus PEM format)
- Generate ulang jika perlu

### Error: "Invalid key format" di DOKU Dashboard
**Solution**:
- Pastikan format PEM (bukan DER)
- Pastikan ada header/footer (BEGIN/END)
- Pastikan tidak ada spasi di awal/akhir
- Copy ulang dari file, jangan ketik manual

### Private Key Hilang
**Solution**:
- Restore dari backup jika ada
- Jika tidak ada backup, generate keys baru
- Upload public key baru ke DOKU
- Update konfigurasi di aplikasi

### Keys Tidak Match
**Solution**:
- Generate ulang public key dari private key:
  ```bash
  openssl rsa -in doku_private.key -pubout -out doku_public.key
  ```

---

## 📞 Support

### DOKU Support
- Website: https://jokul.doku.com/
- Email: support@doku.com
- Dokumentasi: https://developers.doku.com/

### Aplikasi Support
- Lihat file: `CARA-KONFIGURASI-DOKU-SNAP.md`
- Lihat file: `DOKU-QUICK-REFERENCE.md`

---

## 📝 FAQ

**Q: Apakah private key aman di aplikasi?**
A: Ya, private key dienkripsi menggunakan Laravel encryption sebelum disimpan di database.

**Q: Berapa lama keys valid?**
A: Keys tidak expire, tapi recommended rotate setiap 1-2 tahun untuk keamanan.

**Q: Apakah bisa pakai keys yang sama untuk sandbox dan production?**
A: Bisa, tapi tidak recommended. Gunakan keys berbeda untuk keamanan.

**Q: Bagaimana jika private key bocor?**
A: Generate keys baru, upload public key baru ke DOKU, update konfigurasi, revoke keys lama di DOKU Dashboard.

**Q: Apakah harus generate keys sendiri?**
A: Ya, untuk keamanan. DOKU tidak menyediakan keys untuk Anda.

**Q: Berapa bit yang recommended?**
A: 2048 bit sudah cukup. 4096 bit lebih aman tapi lebih lambat.

---

**Updated**: 2026-03-28
**Version**: 1.0
**Status**: Ready to use
