# Test SEO Implementation - Quick Guide

## ✅ Test Sekarang (5 Menit)

### 1. Test Robots.txt ✅
```
URL: http://127.0.0.1:8000/robots.txt
```

**Expected Output:**
```txt
User-agent: *
Allow: /
Disallow: /dash/
Disallow: /login
...
Sitemap: https://undanganberpesta.ourtestcloud.my.id/sitemap.xml
```

**Status:** ✅ PASS jika file muncul dengan content di atas

---

### 2. Test Sitemap.xml ✅
```
URL: http://127.0.0.1:8000/sitemap.xml
```

**Expected Output:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>http://127.0.0.1:8000/</loc>
    <lastmod>...</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  ...
</urlset>
```

**Status:** ✅ PASS jika XML muncul dengan list URLs

---

### 3. Test Meta Robots (Dashboard) ✅
```
1. Login ke dashboard
2. Buka: http://127.0.0.1:8000/dash
3. View Source (Ctrl+U)
4. Cari: "noindex"
```

**Expected:**
```html
<meta name="robots" content="noindex, nofollow">
```

**Status:** ✅ PASS jika meta tag ditemukan

---

### 4. Test Meta Tags (Undangan) ✅
```
1. Buka undangan public (jika ada)
2. Atau buat undangan baru dan publish
3. View Source (Ctrl+U)
4. Cari: "og:title" dan "twitter:card"
```

**Expected:**
```html
<meta property="og:type" content="website">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:image" content="...">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="...">
```

**Status:** ✅ PASS jika meta tags ditemukan

---

### 5. Test Structured Data (Landing) ✅
```
1. Buka: http://127.0.0.1:8000/
2. View Source (Ctrl+U)
3. Cari: "application/ld+json"
```

**Expected:**
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  ...
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  ...
}
</script>
```

**Status:** ✅ PASS jika structured data ditemukan

---

## 🔍 Test dengan Tools

### 1. Rich Results Test
```
URL: https://search.google.com/test/rich-results
```

**Steps:**
1. Paste URL landing page production
2. Click "Test URL"
3. Wait for results

**Expected:**
- ✅ Organization schema detected
- ✅ WebSite schema detected
- ✅ No errors

---

### 2. Facebook Sharing Debugger
```
URL: https://developers.facebook.com/tools/debug/
```

**Steps:**
1. Paste URL undangan
2. Click "Debug"
3. Check preview

**Expected:**
- ✅ Title muncul
- ✅ Description muncul
- ✅ Image muncul (foto mempelai)

---

### 3. Twitter Card Validator
```
URL: https://cards-dev.twitter.com/validator
```

**Steps:**
1. Paste URL undangan
2. Click "Preview card"

**Expected:**
- ✅ Card preview muncul
- ✅ Image & text correct

---

## 📊 Checklist

### Local Testing (Sekarang)
- [ ] Robots.txt accessible
- [ ] Sitemap.xml generates correctly
- [ ] Meta robots di dashboard
- [ ] Meta tags di undangan
- [ ] Structured data di landing
- [ ] Alt text di images

### Production Testing (Setelah Deploy)
- [ ] Robots.txt accessible (HTTPS)
- [ ] Sitemap.xml accessible (HTTPS)
- [ ] All URLs use HTTPS
- [ ] Canonical URLs correct
- [ ] Open Graph preview works
- [ ] Twitter Card preview works

### Google Search Console (Manual)
- [ ] Property added
- [ ] Ownership verified
- [ ] Sitemap submitted
- [ ] No coverage errors
- [ ] Pages getting indexed

---

## 🚨 Troubleshooting

### Sitemap Error: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
php artisan route:clear
```

### Sitemap Empty
```bash
# Check if invitations exist
php artisan tinker
>>> \App\Models\Invitation::where('is_published', true)->count()
```

### Meta Tags Not Showing
```bash
# Clear view cache
php artisan view:clear

# Hard refresh browser
Ctrl + Shift + R
```

### Structured Data Not Valid
```bash
# Check JSON syntax
# Use: https://jsonlint.com/
# Copy structured data from view source
# Validate
```

---

## ✅ Success Criteria

### All Tests Pass If:
1. ✅ Robots.txt returns 200 OK
2. ✅ Sitemap.xml returns valid XML
3. ✅ Dashboard has noindex meta
4. ✅ Undangan has OG tags
5. ✅ Landing has structured data
6. ✅ No console errors
7. ✅ All images have alt text

### Ready for Production If:
1. ✅ All local tests pass
2. ✅ No PHP errors
3. ✅ No JavaScript errors
4. ✅ Mobile responsive
5. ✅ HTTPS configured
6. ✅ APP_URL correct in .env

---

## 📈 Next Steps

### Immediate (Today)
1. Run all local tests above
2. Fix any errors found
3. Deploy to production
4. Test on production

### This Week
1. Submit to Google Search Console
2. Verify ownership
3. Submit sitemap
4. Monitor indexing

### This Month
1. Check coverage report
2. Monitor search queries
3. Track organic traffic
4. Optimize based on data

---

## 🎯 Expected Timeline

### Day 1 (Today)
- ✅ Implementation complete
- ✅ Local testing done
- ✅ Deploy to production

### Day 2-3
- Submit to Search Console
- Sitemap processed
- First pages indexed

### Week 1-2
- More pages indexed
- Appear in search (brand)
- Monitor errors

### Month 1
- Stable indexing
- Keyword rankings
- Organic traffic starts

### Month 2-3
- Better rankings
- More traffic
- Rich snippets appear

---

## 📞 Support

Jika ada masalah:
1. Check `SEO-IMPLEMENTATION-COMPLETE.md` untuk detail
2. Check `SEO-CHECKLIST-LENGKAP.md` untuk reference
3. Google error message
4. Check Laravel logs: `storage/logs/laravel.log`

**SEO Implementation sudah selesai! Tinggal test dan submit ke Google!** 🚀
