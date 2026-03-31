# Thumbnail Generation - Rocky Linux Installation Guide

## Server Information
- OS: Rocky Linux 9.7 (Blue Onyx)
- Server Path: `/var/j/undangan`

## Installation Steps

### 1. Upload Installation Script
Upload `install-screenshot-dependencies-rocky.sh` to your server at `/var/j/undangan/`

### 2. Make Script Executable
```bash
cd /var/j/undangan
chmod +x install-screenshot-dependencies-rocky.sh
```

### 3. Run Installation Script
```bash
sudo bash install-screenshot-dependencies-rocky.sh
```

This will install:
- Node.js 18
- npm
- Puppeteer (globally and locally)
- Chromium browser
- All required Chrome dependencies for Rocky Linux

### 4. Verify Installation
```bash
# Check Node.js
node --version

# Check npm
npm --version

# Check Puppeteer
npm list puppeteer
```

### 5. Test Thumbnail Generation
```bash
cd /var/j/undangan
php artisan templates:generate-thumbnails --template=basic-preview
```

## Troubleshooting

### If you get permission errors:
```bash
sudo chown -R $USER:$USER /var/j/undangan/node_modules
chmod -R 755 /var/j/undangan/node_modules
```

### If Chromium is not found:
```bash
# Check if chromium is installed
which chromium-browser
which chromium

# If not found, install manually
sudo dnf install chromium -y
```

### If you get "No usable sandbox" error:
The script already includes `--no-sandbox` flag, but if you still get errors:
```bash
# Run with elevated permissions
sudo php artisan templates:generate-thumbnails --template=basic-preview
```

### Check Puppeteer installation:
```bash
cd /var/j/undangan
npm list puppeteer
ls -la node_modules/puppeteer
```

## Generate All Thumbnails

After successful installation, generate thumbnails for all templates:

```bash
cd /var/j/undangan
php artisan templates:generate-thumbnails
```

Or for specific template:
```bash
php artisan templates:generate-thumbnails --template=basic-preview
php artisan templates:generate-thumbnails --template=premium-white-1-preview
php artisan templates:generate-thumbnails --template=sanno-preview
```

Force regenerate existing thumbnails:
```bash
php artisan templates:generate-thumbnails --force
```

## Automated Thumbnail Generation

Thumbnails are automatically generated when you run preview seeders:
- `BasicPreviewSeeder`
- `PremiumWhite1PreviewSeeder`
- `SannoPreviewSeeder`

Each seeder will attempt to generate thumbnails after creating the template data.

## Manual Alternative

If automated generation fails, you can manually take screenshots:
1. Open preview URL in browser: `https://undanganberpesta.ourtestcloud.my.id/basic-preview?to=demo-user&open=1`
2. Wait for invitation to auto-open
3. Take screenshot (1200x800px recommended)
4. Save as JPG to `storage/app/public/thumbnails/{template-slug}.jpg`
5. Update template record in database with thumbnail path
