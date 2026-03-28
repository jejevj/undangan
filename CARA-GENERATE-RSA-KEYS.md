# Cara Mendapatkan Private Key untuk DOKU SNAP API

## Overview

Private Key diperlukan untuk SNAP API DOKU. Anda perlu generate RSA key pair (Private Key + Public Key) sendiri, kemudian upload Public Key ke DOKU Dashboard.

## Metode 1: Menggunakan OpenSSL (Recommended)

### Windows (Git Bash / WSL)

#### 1. Install OpenSSL
OpenSSL biasanya sudah terinstall jika Anda punya Git for Windows.

**Cek apakah sudah terinstall:**
```bash
openssl version
```

**Jika belum terinstall:**
- Download Git for Windows: https://git-scm.com/download/win
- Atau download OpenSSL: https://slproweb.com/products/Win32OpenSSL.html

#### 2. Generate Private Key
Buka Git Bash atau Command Prompt, lalu jalankan:

```bash
# Generate private key (2048 bit)
openssl genrsa -out doku_private.key 2048
```

Output:
```
Generating RSA private key, 2048 bit long modulus
.....+++
.....+++
e is 65537 (0x10001)
```

File `doku_private.key` akan dibuat di folder saat ini.

#### 3. Generate Public Key dari Private Key
```bash
# Generate public key dari private key
openssl rsa -in doku_private.key -pubout -out doku_public.key
```

Output:
```
writing RSA key
```

File `doku_public.key` akan dibuat di folder saat ini.

#### 4. Lihat Isi File

**Private Key:**
```bash
cat doku_private.key
```

Output contoh:
```
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC7VJTUt9Us8cKj
MzEfYyjiWA4R4/M2bS1+fWIcPm15A8+raZ4dp+/PJE8lQwR06zXTnNBe3VqDpDqY
dGWKGxWbjcvHOdSxRk9Jx9HgYpsBUnQaBH+bLZER4Ub8KPBUNUnV4+sQn66R5o5E
...
-----END PRIVATE KEY-----
```

**Public Key:**
```bash
cat doku_public.key
```

Output contoh:
```
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAu1SU1LfVLPHCozMxH2Mo
4lgOEePzNm0tfn1iHD5teQPPq2meHafvzyRPJUMEdOs105zQXt1ag6Q6mHRlihsV
m43LxznUsUZPScfR4GKbAVJ0GgR/my2REeFG/CjwVDVJ1ePrEJ+ukeaORPQ==
-----END PUBLIC KEY-----
```

### Linux / macOS

OpenSSL biasanya sudah terinstall. Gunakan command yang sama:

```bash
# Generate private key
openssl genrsa -out doku_private.key 2048

# Generate public key
openssl rsa -in doku_private.key -pubout -out doku_public.key

# Lihat isi file
cat doku_private.key
cat doku_public.key
```

## Metode 2: Menggunakan Online Tool (Tidak Recommended untuk Production)

⚠️ **WARNING**: Jangan gunakan untuk production! Hanya untuk testing.

### Website Generator
1. Buka: https://travistidwell.com/jsencrypt/demo/
2. Klik "Generate New Keys"
3. Copy Private Key dan Public Key

⚠️ **Risiko**: 
- Keys di-generate di browser orang lain
- Tidak aman untuk production
- Hanya untuk testing/development

## Metode 3: Menggunakan PHP (Jika OpenSSL tidak tersedia)

Buat file PHP untuk generate keys:

```php
<?php
// generate_keys.php

// Configuration
$config = [
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
];

// Generate private key
$privateKey = openssl_pkey_new($config);

// Extract private key
openssl_pkey_export($privateKey, $privateKeyPEM);

// Extract public key
$publicKeyDetails = openssl_pkey_get_details($privateKey);
$publicKeyPEM = $publicKeyDetails["key"];

// Save to files
file_put_contents('doku_private.key', $privateKeyPEM);
file_put_contents('doku_public.key', $publicKeyPEM);

echo "Keys generated successfully!\n\n";
echo "Private Key:\n";
echo $privateKeyPEM . "\n\n";
echo "Public Key:\n";
echo $publicKeyPEM . "\n";
?>
```

Jalankan:
```bash
php generate_keys.php
```

## Upload Public Key ke DOKU Dashboard

### Langkah-langkah:

1. **Login ke DOKU Jokul**
   - Buka: https://jokul.doku.com/
   - Login dengan akun Anda

2. **Pilih Environment**
   - Sandbox (untuk testing)
   - Production (untuk live)

3. **Buka Menu SNAP API Settings**
   - Menu: Settings > SNAP API
   - Atau: API Management > SNAP Configuration

4. **Upload Public Key**
   - Klik "Upload Public Key" atau "Add Public Key"
   - Paste isi file `doku_public.key` Anda
   - Atau upload file langsung
   - Klik "Submit"

5. **Tunggu Approval**
   - DOKU akan review public key Anda
   - Biasanya instant approval untuk sandbox
   - Production mungkin butuh waktu review

6. **Download DOKU Public Key**
   - Setelah approved, download DOKU Public Key
   - Simpan sebagai `doku_public_key.pem`
   - Key ini untuk verifikasi callback dari DOKU

## Input ke Aplikasi

### 1. Copy Private Key
```bash
# Windows (Git Bash)
cat doku_private.key | clip

# Linux
cat doku_private.key | xclip -selection clipboard

# macOS
cat doku_private.key | pbcopy

# Manual
cat doku_private.key
# Lalu copy manual
```

### 2. Copy Public Key
```bash
cat doku_public.key | clip  # Windows
cat doku_public.key         # Manual copy
```

### 3. Copy DOKU Public Key
```bash
cat doku_public_key.pem | clip  # Windows
cat doku_public_key.pem         # Manual copy
```

### 4. Input ke Form
1. Login ke dashboard admin
2. Menu **Payment Gateway** > **Tambah Konfigurasi**
3. Scroll ke section **SNAP API Configuration**
4. Paste keys:
   - **Private Key**: Paste isi `doku_private.key`
   - **Public Key**: Paste isi `doku_public.key`
   - **DOKU Public Key**: Paste isi `doku_public_key.pem`
5. Klik **Simpan**

## Verifikasi Keys

### Cek Format Private Key
Private key harus dimulai dan diakhiri dengan:
```
-----BEGIN PRIVATE KEY-----
...
-----END PRIVATE KEY-----
```

Atau:
```
-----BEGIN RSA PRIVATE KEY-----
...
-----END RSA PRIVATE KEY-----
```

### Cek Format Public Key
Public key harus dimulai dan diakhiri dengan:
```
-----BEGIN PUBLIC KEY-----
...
-----END PUBLIC KEY-----
```

### Test Keys Valid
```bash
# Test private key valid
openssl rsa -in doku_private.key -check

# Test public key match dengan private key
openssl rsa -in doku_private.key -pubout | diff - doku_public.key
```

Jika tidak ada error, keys valid!

## Keamanan Private Key

### ⚠️ PENTING - Jangan Pernah:
- ❌ Share private key ke orang lain
- ❌ Commit private key ke Git/GitHub
- ❌ Upload private key ke public server
- ❌ Kirim private key via email/chat
- ❌ Screenshot private key

### ✅ Best Practices:
- ✅ Simpan private key di tempat aman
- ✅ Backup private key (encrypted)
- ✅ Gunakan environment variables jika perlu
- ✅ Rotate keys secara berkala
- ✅ Gunakan keys berbeda untuk sandbox dan production

### Backup Private Key
```bash
# Backup dengan password
openssl rsa -aes256 -in doku_private.key -out doku_private_backup.key

# Restore dari backup
openssl rsa -in doku_private_backup.key -out doku_private.key
```

## Troubleshooting

### Error: "openssl: command not found"
**Solution**: Install OpenSSL atau gunakan Metode 3 (PHP)

### Error: "unable to load Private Key"
**Solution**: 
- Cek format file (harus PEM format)
- Cek tidak ada karakter tambahan
- Generate ulang jika perlu

### Error: "Public key doesn't match private key"
**Solution**: 
- Generate ulang public key dari private key
- Pastikan menggunakan command yang benar

### Error: "Invalid key format" di DOKU Dashboard
**Solution**:
- Pastikan format PEM (bukan DER)
- Pastikan ada header/footer (BEGIN/END)
- Pastikan tidak ada spasi di awal/akhir

### Private Key Hilang
**Solution**:
- Restore dari backup jika ada
- Jika tidak ada backup, generate keys baru
- Upload public key baru ke DOKU
- Update konfigurasi di aplikasi

## FAQ

**Q: Berapa bit yang recommended?**
A: 2048 bit sudah cukup. 4096 bit lebih aman tapi lebih lambat.

**Q: Apakah bisa pakai keys yang sama untuk sandbox dan production?**
A: Bisa, tapi tidak recommended. Gunakan keys berbeda untuk keamanan.

**Q: Berapa lama keys valid?**
A: Keys tidak expire, tapi recommended rotate setiap 1-2 tahun.

**Q: Apakah private key tersimpan aman di aplikasi?**
A: Ya, private key dienkripsi menggunakan Laravel encryption sebelum disimpan di database.

**Q: Bagaimana jika private key bocor?**
A: Generate keys baru, upload public key baru ke DOKU, update konfigurasi, revoke keys lama.

**Q: Apakah harus generate keys sendiri?**
A: Ya, untuk keamanan. DOKU tidak menyediakan keys untuk Anda.

---

**Updated**: 2026-03-28
**Version**: 1.0
