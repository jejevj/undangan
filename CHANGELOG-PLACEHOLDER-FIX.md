# Changelog - Placeholder Image Fix

## Tanggal: 28 Maret 2026

### Fix: Menghapus External Placeholder Dependencies

Mengganti semua external placeholder image (via.placeholder.com) dengan gradient background lokal untuk menghindari:
- ERR_NAME_NOT_RESOLVED error
- Dependency ke external service
- Slow loading jika external service down

## Perubahan

### 1. Landing Page - About Section
**File**: `resources/views/landing/index.blade.php`

**Sebelum:**
```html
<img alt="customize" src="https://via.placeholder.com/714x593">
```

**Sesudah:**
```html
<div style="width:100%; height:400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display:flex; align-items:center; justify-content:center;">
    <span style="color:white; font-size:24px; font-weight:600;">Platform Undangan Digital</span>
</div>
```

### 2. Landing Page - Template Section
**File**: `resources/views/landing/index.blade.php`

**Sebelum:**
```html
<img alt="customize" src="https://via.placeholder.com/636x571">
```

**Sesudah:**
```html
<div style="width:100%; height:400px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; display:flex; align-items:center; justify-content:center;">
    <span style="color:white; font-size:24px; font-weight:600;">Template Elegan</span>
</div>
```

### 3. Template Grid - Thumbnail Fallback
**File**: `resources/views/landing/partials/template-grid.blade.php`

**Sebelum:**
```html
<img src="https://via.placeholder.com/300x400" alt="{{ $template->name }}">
```

**Sesudah:**
```html
<div style="width:100%; height:100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center;">
    <span style="color:white; font-size:18px; font-weight:600; text-align:center; padding:20px;">{{ $template->name }}</span>
</div>
```

### 4. CSS Path Fix - Newsletter Background
**File**: `public/demos-assets/css/style.css`

**Sebelum:**
```css
background-image: url(assets/img/newsletter.jpg);
```

**Sesudah:**
```css
background-image: url(../img/newsletter.jpg);
```

**Penjelasan:** Path relatif dari `css/style.css` ke `img/newsletter.jpg` adalah `../img/`

## Keuntungan

1. **No External Dependencies**: Tidak bergantung pada via.placeholder.com
2. **Faster Loading**: Gradient CSS lebih cepat dari load image external
3. **Always Available**: Tidak ada risk service down
4. **Better UX**: Gradient dengan text lebih informatif
5. **Customizable**: Mudah diganti warna/style sesuai brand

## Gradient Colors Used

- **Purple to Pink**: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- **Pink to Red**: `linear-gradient(135deg, #f093fb 0%, #f5576c 100%)`

Warna ini bisa diganti sesuai brand color di GeneralConfig.

## Testing

1. Buka landing page
2. ✅ Tidak ada error `ERR_NAME_NOT_RESOLVED` di console
3. ✅ Section About dan Template menampilkan gradient background
4. ✅ Template tanpa thumbnail menampilkan gradient dengan nama template
5. ✅ Newsletter section background image load dengan benar

## Notes

- Jika ingin menggunakan image asli, upload ke `public/demos-assets/img/` dan ganti div dengan `<img>`
- Gradient colors bisa disesuaikan dengan brand color
- File `newsletter.jpg` sudah ada di path yang benar
