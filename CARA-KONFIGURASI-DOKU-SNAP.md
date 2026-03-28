# Cara Konfigurasi DOKU dengan SNAP API

## Langkah-langkah Setup

### 1. Persiapan Credentials dari DOKU

#### A. Login ke DOKU Jokul Dashboard
1. Buka https://jokul.doku.com/
2. Login dengan akun Anda
3. Pilih environment (Sandbox atau Production)

#### B. Dapatkan Basic Credentials
1. Buka menu **Settings** > **API Credentials**
2. Copy **Client ID**
3. Copy **Secret Key**
4. Catat **Base URL**:
   - Sandbox: `https://api-sandbox.doku.com`
   - Production: `https://api.doku.com`

#### C. Setup SNAP API (Optional tapi Recommended)

##### Generate RSA Key Pair
Anda perlu generate RSA key pair untuk SNAP API:

```bash
# Generate private key
openssl genrsa -out private.key 2048

# Generate public key dari private key
openssl rsa -in private.key -pubout -out public.key
```

##### Upload Public Key ke DOKU
1. Buka DOKU Dashboard
2. Menu **Settings** > **SNAP API**
3. Upload file `public.key` Anda
4. DOKU akan approve dan memberikan **DOKU Public Key**

##### Download DOKU Public Key
1. Setelah approved, download **DOKU Public Key**
2. Simpan sebagai `doku_public.key`

### 2. Input ke Aplikasi

#### A. Buka Form Konfigurasi
1. Login ke dashboard admin
2. Menu **Payment Gateway** > **Tambah Konfigurasi**

#### B. Isi Basic Configuration

**Provider**: DOKU (default)

**Environment**: 
- Pilih **Sandbox** untuk testing
- Pilih **Production** untuk live

**Client ID**: 
```
BRN-0204-1754870435962
```
(paste dari DOKU Dashboard)

**Secret Key**: 
```
SK-xxxxxxxxxxxxx
```
(paste dari DOKU Dashboard)

**Base URL**:
- Sandbox: `https://api-sandbox.doku.com`
- Production: `https://api.doku.com`

**Status**: 
- ✅ Centang "Aktifkan konfigurasi ini"

#### C. Isi SNAP API Configuration (Optional)

**Private Key**:
```
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC...
...
-----END PRIVATE KEY-----
```
(paste isi file `private.key`)

**Public Key**:
```
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...
...
-----END PUBLIC KEY-----
```
(paste isi file `public.key`)

**DOKU Public Key**:
```
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...
...
-----END PUBLIC KEY-----
```
(paste isi file `doku_public.key` yang di-download dari DOKU)

**Issuer** (optional):
```
nama-merchant-anda
```

**Auth Code** (optional):
```
AUTH-CODE-JIKA-ADA
```

#### D. Simpan dan Test

1. Klik tombol **Simpan**
2. Kembali ke halaman index
3. Klik tombol **Test Connection** (icon plug)
4. Jika berhasil, akan muncul pesan sukses

## Troubleshooting

### Error: "Client ID atau Secret Key salah"
- Pastikan Client ID dan Secret Key benar
- Pastikan environment sesuai (sandbox/production)
- Pastikan Base URL sesuai dengan environment

### Error: "Invalid Signature"
- Pastikan Private Key format PEM benar
- Pastikan Public Key sudah di-upload ke DOKU
- Pastikan DOKU Public Key benar
- Pastikan tidak ada spasi atau karakter tambahan

### Error: "Token B2B gagal"
- Periksa koneksi internet
- Pastikan Base URL benar
- Pastikan credentials valid

### SNAP API Status "Not Set"
- Ini normal jika Anda belum setup SNAP API
- Anda masih bisa menggunakan Non-SNAP API
- SNAP API optional tapi recommended untuk fitur lengkap

## Fitur yang Tersedia

### Dengan Basic Configuration (Client ID + Secret Key)
- ✅ Payment basic
- ✅ Transaction status check
- ⚠️ Fitur terbatas

### Dengan SNAP API Configuration (+ RSA Keys)
- ✅ Virtual Account
- ✅ Direct Debit
- ✅ E-Wallet (OVO, GoPay, Dana, dll)
- ✅ QRIS
- ✅ Credit Card
- ✅ Account Binding
- ✅ Refund
- ✅ Balance Inquiry
- ✅ Full features

## Security Notes

### Data yang Dienkripsi
- ✅ Secret Key
- ✅ Private Key
- ✅ Auth Code

### Data yang Tidak Dienkripsi
- Client ID (bukan rahasia)
- Public Key (memang bersifat public)
- DOKU Public Key (memang bersifat public)
- Issuer (identifier saja)

### Best Practices
1. **Jangan share** Secret Key dan Private Key
2. **Gunakan HTTPS** untuk production
3. **Backup** Private Key di tempat aman
4. **Rotate keys** secara berkala
5. **Monitor** transaction logs
6. **Test** di sandbox sebelum production

## Edit Konfigurasi

### Update Credentials
1. Buka menu **Payment Gateway**
2. Klik tombol **Edit** (icon pensil)
3. Update field yang ingin diubah
4. **Kosongkan** field yang tidak ingin diubah
5. Klik **Update**

### Status Indicator
Di halaman edit, Anda bisa lihat status setiap field:
- ✅ **Check icon** = Field sudah terisi
- ❌ **Times icon** = Field belum terisi

### Update Partial
Anda bisa update hanya beberapa field:
- Update Client ID saja → kosongkan Secret Key
- Update SNAP keys saja → kosongkan Basic credentials
- Update semua → isi semua field

## FAQ

**Q: Apakah SNAP API wajib?**
A: Tidak wajib, tapi recommended untuk fitur lengkap.

**Q: Bisa pakai sandbox dan production bersamaan?**
A: Bisa, buat 2 konfigurasi terpisah. Aktifkan yang ingin dipakai.

**Q: Bagaimana cara switch environment?**
A: Edit konfigurasi, ubah environment dan base URL.

**Q: Private key aman?**
A: Ya, private key dienkripsi di database menggunakan Laravel encryption.

**Q: Bisa test tanpa SNAP API?**
A: Bisa, test connection hanya butuh Client ID dan Secret Key.

**Q: Bagaimana cara tahu SNAP API sudah configured?**
A: Lihat badge di halaman index, akan muncul "Configured" jika sudah lengkap.

---

**Updated**: 2026-03-28
**Version**: 1.0
