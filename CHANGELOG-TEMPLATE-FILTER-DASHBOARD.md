# Changelog - Template Filter di Dashboard

## Tanggal: 28 Maret 2026

### Fitur Baru: Filter Template di Halaman Select Template (Dashboard)

User sekarang bisa melakukan filter template berdasarkan kategori dan tipe saat membuat undangan baru di dashboard, sama seperti di landing page.

#### 1. InvitationController Update
**File**: `app/Http/Controllers/InvitationController.php`

**Method `selectTemplate()` - Updated:**
- Menambahkan load categories untuk filter
- Load templates dengan relasi category
- Pass categories ke view

**Method `getTemplates()` - New:**
- AJAX endpoint untuk filter template
- Filter by category (slug)
- Filter by type (free/premium/all)
- Return partial view dengan template grid

#### 2. Partial View - Template Grid
**File**: `resources/views/invitations/partials/template-grid.blade.php`

Fitur:
- Card template dengan thumbnail
- Badge kategori template
- Badge tipe (free/premium)
- Harga template
- Button "Gunakan Template Ini"
- Empty state dengan icon
- Menggunakan style dari theme dashboard (Bootstrap classes)

#### 3. View Select Template - Updated
**File**: `resources/views/invitations/select-template.blade.php`

Fitur Filter:
- **Kategori Filter**: Dropdown dengan semua kategori aktif
- **Tipe Filter**: Dropdown (Semua/Gratis/Premium)
- Filter menggunakan form-select dari Bootstrap (theme dashboard)
- Loading indicator saat fetch data
- AJAX filtering tanpa reload page
- Smooth fade-in animation

UI Components:
- Card untuk filter section dengan icon
- Loading spinner dengan text
- Template grid dengan responsive columns
- Error handling dengan alert

#### 4. Routes Update
**File**: `routes/web.php`

Route baru:
```php
Route::get('invitations/templates', [InvitationController::class, 'getTemplates'])->name('invitations.templates');
```

#### 5. JavaScript Implementation

AJAX Features:
- Real-time filtering saat select berubah
- Loading indicator saat fetch
- Error handling dengan user-friendly message
- Smooth transitions (fadeIn/fadeOut)
- jQuery implementation

### Styling & UX

**Menggunakan Theme Dashboard:**
- Bootstrap form-select untuk dropdown
- Bootstrap card untuk filter section
- Bootstrap spinner untuk loading
- Bootstrap alert untuk error
- Font Awesome icons untuk visual enhancement

**Responsive:**
- col-md-6 untuk filter (2 kolom di desktop)
- col-xl-4 col-md-6 untuk template cards (3 kolom desktop, 2 kolom tablet)
- Mobile-friendly layout

### Testing

1. Login ke dashboard
2. Klik "Buat Undangan Baru" atau akses `/dash/invitations/select-template`
3. Test filter kategori:
   - Pilih kategori tertentu
   - Template akan difilter sesuai kategori
4. Test filter tipe:
   - Pilih "Gratis" → hanya template gratis
   - Pilih "Premium" → hanya template premium
   - Pilih "Semua Tipe" → semua template
5. Test kombinasi filter:
   - Pilih kategori + tipe
   - Template akan difilter berdasarkan kedua kriteria

### Perbedaan dengan Landing Page

**Similarities:**
- Filter kategori dan tipe yang sama
- AJAX filtering tanpa reload
- Loading indicator
- Smooth animations

**Differences:**
- Dashboard menggunakan Bootstrap theme (bukan custom landing CSS)
- Layout card menggunakan card-footer untuk button
- Menampilkan badge kategori di card
- Button langsung ke create invitation (bukan preview)
- Terintegrasi dengan subscription limit check

### Catatan

- Filter hanya menampilkan template yang `is_active = true`
- Categories harus sudah ada di database (jalankan TemplateCategorySeeder)
- Menggunakan jQuery yang sudah tersedia di theme dashboard
- Compatible dengan subscription system (limit check tetap berjalan)
