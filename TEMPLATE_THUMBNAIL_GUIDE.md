# 📸 Template Thumbnail Generation Guide

## Overview

Sistem untuk generate thumbnail template secara otomatis dari preview URL atau manual upload.

---

## 🎯 Methods Available

### 1. **Automatic via Screenshot API** (Recommended)
Generate thumbnail otomatis menggunakan external screenshot service.

### 2. **Manual Upload**
Upload thumbnail secara manual oleh admin.

### 3. **Puppeteer Service** (Advanced)
Self-hosted screenshot service menggunakan Puppeteer/Playwright.

---

## 🚀 Quick Start

### Generate All Templates

```bash
php artisan templates:generate-thumbnails
```

### Generate Specific Template

```bash
php artisan templates:generate-thumbnails --template=basic
```

### Force Regenerate

```bash
php artisan templates:generate-thumbnails --force
```

### Manual Mode

```bash
php artisan templates:generate-thumbnails --method=manual
```

---

## 📋 Method 1: Screenshot API (Automatic)

### Option A: ScreenshotAPI.net

**Pros:**
- Free tier: 100 screenshots/month
- Fast response
- Good quality
- No server setup needed

**Setup:**

1. Register at https://screenshotapi.net
2. Get your API token
3. Add to `.env`:

```env
SCREENSHOT_API_KEY=your_api_key_here
```

4. Add to `config/services.php`:

```php
'screenshot_api' => [
    'key' => env('SCREENSHOT_API_KEY'),
],
```

5. Uncomment Method 1 in `GenerateTemplateThumbnails.php`

**Usage:**

```bash
php artisan templates:generate-thumbnails --method=api
```

---

### Option B: URLBox.io

**Pros:**
- High quality screenshots
- Advanced options (retina, full page, etc)
- Reliable service

**Cons:**
- Paid service (starts at $9/month)

**Setup:**

1. Register at https://urlbox.io
2. Get API key and secret
3. Add to `.env`:

```env
URLBOX_API_KEY=your_api_key
URLBOX_API_SECRET=your_api_secret
```

4. Add to `config/services.php`:

```php
'urlbox' => [
    'key' => env('URLBOX_API_KEY'),
    'secret' => env('URLBOX_API_SECRET'),
],
```

5. Uncomment Method 2 in `GenerateTemplateThumbnails.php`

---

### Option C: Other Services

**Alternatives:**
- **ApiFlash** - https://apiflash.com
- **ScreenshotOne** - https://screenshotone.com  
- **Microlink** - https://microlink.io
- **Browshot** - https://browshot.com

All follow similar setup pattern.

---

## 📋 Method 2: Manual Upload

### Steps:

1. Open preview URL di browser
2. Take screenshot (Full page recommended)
3. Crop/resize to 1200x800px (or 16:10 ratio)
4. Save as JPG (quality 80-90%)
5. Upload to `storage/app/public/thumbnails/[template-slug].jpg`
6. Run command:

```bash
php artisan templates:generate-thumbnails --method=manual
```

### Recommended Tools:

- **Windows:** Snipping Tool, ShareX
- **Mac:** Screenshot (Cmd+Shift+4), CleanShot X
- **Linux:** Flameshot, Spectacle
- **Online:** Awesome Screenshot, Nimbus Screenshot

### Image Specs:

- **Dimensions:** 1200x800px (16:10 ratio)
- **Format:** JPG
- **Quality:** 80-90%
- **File Size:** < 200KB
- **Color Space:** sRGB

---

## 📋 Method 3: Puppeteer Service (Self-Hosted)

### Setup Node.js Service

1. Create `screenshot-service` folder:

```bash
mkdir screenshot-service
cd screenshot-service
npm init -y
npm install puppeteer express
```

2. Create `server.js`:

```javascript
const express = require('express');
const puppeteer = require('puppeteer');
const app = express();

app.get('/screenshot', async (req, res) => {
    const { url, width = 1200, height = 800 } = req.query;
    
    if (!url) {
        return res.status(400).json({ error: 'URL required' });
    }
    
    try {
        const browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });
        
        const page = await browser.newPage();
        await page.setViewport({ width: parseInt(width), height: parseInt(height) });
        await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
        
        // Wait for content to load
        await page.waitForTimeout(2000);
        
        const screenshot = await page.screenshot({
            type: 'jpeg',
            quality: 85,
            fullPage: false
        });
        
        await browser.close();
        
        res.set('Content-Type', 'image/jpeg');
        res.send(screenshot);
        
    } catch (error) {
        console.error('Screenshot error:', error);
        res.status(500).json({ error: error.message });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Screenshot service running on port ${PORT}`);
});
```

3. Run service:

```bash
node server.js
```

4. Update `GenerateTemplateThumbnails.php`:

```php
protected function getScreenshotFromAPI(string $url): ?string
{
    $screenshotServiceUrl = config('services.screenshot.url', 'http://localhost:3000');
    
    $response = Http::timeout(60)->get("{$screenshotServiceUrl}/screenshot", [
        'url' => $url,
        'width' => 1200,
        'height' => 800,
    ]);
    
    if ($response->successful()) {
        return $response->body();
    }
    
    return null;
}
```

5. Add to `.env`:

```env
SCREENSHOT_SERVICE_URL=http://localhost:3000
```

---

## 🎨 Thumbnail Specifications

### Recommended Dimensions

**Landing Page Grid:**
- Width: 400px
- Height: 300px
- Ratio: 4:3

**Template Detail:**
- Width: 1200px
- Height: 800px
- Ratio: 3:2

**Mobile:**
- Width: 600px
- Height: 400px
- Ratio: 3:2

### File Format

**Primary:** JPG
- Quality: 80-85%
- Progressive: Yes
- Color Space: sRGB

**Alternative:** WebP
- Quality: 80%
- Better compression
- Modern browsers only

### Optimization

Use image optimization tools:
- **TinyPNG** - https://tinypng.com
- **ImageOptim** - https://imageoptim.com
- **Squoosh** - https://squoosh.app

Target file size: < 150KB

---

## 🔧 Integration with Landing Page

### Update Landing Page View

File: `resources/views/welcome.blade.php` (or your landing page)

```blade
@foreach($templates as $template)
<div class="template-card">
    @if($template->thumbnail)
        <img src="{{ asset('storage/' . $template->thumbnail) }}" 
             alt="{{ $template->name }}"
             loading="lazy">
    @else
        <div class="template-placeholder">
            <i class="icon-template"></i>
            <span>{{ $template->name }}</span>
        </div>
    @endif
    
    <h3>{{ $template->name }}</h3>
    <p>{{ $template->description }}</p>
    <a href="{{ $template->preview_url }}" class="btn-preview">Preview</a>
</div>
@endforeach
```

### CSS for Placeholder

```css
.template-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.template-card img {
    width: 100%;
    height: 300px;
    object-fit: cover;
}

.template-placeholder {
    width: 100%;
    height: 300px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
}

.template-placeholder i {
    font-size: 48px;
    margin-bottom: 16px;
}
```

---

## 🤖 Automation

### Schedule Thumbnail Generation

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Generate thumbnails for new templates daily
    $schedule->command('templates:generate-thumbnails')
             ->daily()
             ->at('02:00');
}
```

### On Template Creation

Add to Template Seeder:

```php
public function run(): void
{
    $template = Template::firstOrCreate([...]);
    
    // Generate thumbnail after template creation
    if (!$template->thumbnail) {
        Artisan::call('templates:generate-thumbnails', [
            '--template' => $template->slug,
        ]);
    }
}
```

---

## 📊 Admin Interface (Optional)

### Add Thumbnail Upload to Admin

File: `resources/views/admin/templates/edit.blade.php`

```blade
<div class="form-group">
    <label>Thumbnail</label>
    
    @if($template->thumbnail)
        <div class="current-thumbnail">
            <img src="{{ asset('storage/' . $template->thumbnail) }}" 
                 alt="Current thumbnail"
                 style="max-width: 400px;">
        </div>
    @endif
    
    <input type="file" 
           name="thumbnail" 
           accept="image/jpeg,image/jpg,image/png,image/webp"
           class="form-control">
    
    <small class="form-text text-muted">
        Recommended: 1200x800px, JPG format, < 200KB
    </small>
    
    <button type="button" 
            class="btn btn-secondary mt-2" 
            onclick="generateThumbnail('{{ $template->slug }}')">
        Generate from Preview
    </button>
</div>

<script>
function generateThumbnail(slug) {
    if (confirm('Generate thumbnail from preview URL?')) {
        fetch(`/admin/templates/${slug}/generate-thumbnail`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Thumbnail generated successfully!');
                location.reload();
            } else {
                alert('Failed to generate thumbnail: ' + data.message);
            }
        });
    }
}
</script>
```

### Add Route

```php
Route::post('/admin/templates/{slug}/generate-thumbnail', [TemplateController::class, 'generateThumbnail'])
    ->name('admin.templates.generate-thumbnail');
```

### Add Controller Method

```php
public function generateThumbnail($slug)
{
    $template = Template::where('slug', $slug)->firstOrFail();
    
    try {
        Artisan::call('templates:generate-thumbnails', [
            '--template' => $slug,
            '--force' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Thumbnail generated successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
```

---

## 🐛 Troubleshooting

### Issue: Thumbnail not showing

**Solutions:**
1. Check file exists: `storage/app/public/thumbnails/[slug].jpg`
2. Check symlink: `php artisan storage:link`
3. Check permissions: `chmod 755 storage/app/public/thumbnails`
4. Clear cache: `php artisan cache:clear`

### Issue: Screenshot API timeout

**Solutions:**
1. Increase timeout in HTTP client
2. Check preview URL is accessible
3. Check API rate limits
4. Try different screenshot service

### Issue: Poor quality thumbnail

**Solutions:**
1. Increase screenshot dimensions (1920x1280)
2. Increase JPEG quality (90%)
3. Use PNG instead of JPG
4. Wait longer for page load (increase timeout)

### Issue: Puppeteer crashes

**Solutions:**
1. Install dependencies: `apt-get install -y chromium-browser`
2. Add more memory to Node.js: `node --max-old-space-size=4096 server.js`
3. Use headless mode: `headless: 'new'`
4. Disable GPU: `args: ['--disable-gpu']`

---

## 💡 Best Practices

1. **Generate thumbnails after template creation**
2. **Use CDN for thumbnail delivery** (CloudFlare, AWS CloudFront)
3. **Implement lazy loading** on landing page
4. **Provide fallback placeholder** if thumbnail missing
5. **Optimize images** before upload (< 150KB)
6. **Use WebP format** with JPG fallback
7. **Cache thumbnails** in browser (1 month)
8. **Regenerate thumbnails** when template updated

---

## 📚 Resources

- [Puppeteer Documentation](https://pptr.dev/)
- [ScreenshotAPI.net](https://screenshotapi.net)
- [URLBox.io](https://urlbox.io)
- [TinyPNG](https://tinypng.com)
- [WebP Converter](https://developers.google.com/speed/webp)

---

**Last Updated: 2026-03-30**
