# Live Edit Feature - User Guide

## Overview
Fitur Live Edit memungkinkan user untuk mengedit konten undangan secara langsung di preview template tanpa harus membuka form edit tradisional. Perubahan akan tersimpan otomatis via AJAX.

## Features

### 1. Toggle Edit Mode
- Tombol "Live Edit" di pojok kanan atas
- Klik untuk mengaktifkan/menonaktifkan mode edit
- Indikator visual saat mode edit aktif

### 2. Inline Text Editing
- Klik langsung pada teks untuk mengedit
- Auto-save setelah 1 detik tidak ada perubahan
- Save otomatis saat blur (klik di luar element)

### 3. Image Upload
- Hover pada gambar untuk melihat tombol "Ganti Foto"
- Klik untuk upload gambar baru
- Preview langsung setelah upload

### 4. Date/Time Editing
- Klik pada tanggal/waktu untuk membuka picker
- Perubahan tersimpan otomatis

### 5. Status Indicator
- "Menyimpan..." - sedang menyimpan
- "Tersimpan" - berhasil disimpan
- Error message jika gagal

## Implementation for Template Developers

### Step 1: Add Live Edit Script to Template

Add to your template's `<head>` section:

```blade
@auth
@if($invitation->user_id === auth()->id() || auth()->user()->hasRole('admin'))
<script src="{{ asset('assets/js/live-edit.js') }}" defer></script>
@endif
@endauth
```

### Step 2: Add Data Attributes to Body

```blade
<body 
    data-invitation-id="{{ $invitation->id }}"
    data-is-owner="{{ $invitation->user_id === auth()->id() || auth()->user()->hasRole('admin') ? 'true' : 'false' }}"
>
```

### Step 3: Mark Editable Elements

Add `data-editable` attribute to elements you want to make editable:

#### Text Fields
```blade
<h1 
    data-editable
    data-field-key="event_title"
    data-field-type="text"
    data-field-label="Judul Acara"
>
    {{ $data['event_title'] ?? 'Event Title' }}
</h1>
```

#### Textarea Fields
```blade
<p 
    data-editable
    data-field-key="event_description"
    data-field-type="textarea"
    data-field-label="Deskripsi"
>
    {{ $data['event_description'] ?? '' }}
</p>
```

#### Image Fields
```blade
<div 
    data-editable
    data-field-key="company_logo"
    data-field-type="image"
    data-field-label="Logo Perusahaan"
>
    @if(!empty($data['company_logo']))
    <img src="{{ asset('storage/' . $data['company_logo']) }}" alt="Logo">
    @endif
</div>
```

#### Date Fields
```blade
<div 
    data-editable
    data-field-key="event_date"
    data-field-type="date"
    data-field-label="Tanggal Acara"
>
    {{ \Carbon\Carbon::parse($data['event_date'])->translatedFormat('d F Y') }}
</div>
```

#### Time Fields
```blade
<div 
    data-editable
    data-field-key="event_time"
    data-field-type="time"
    data-field-label="Waktu"
>
    {{ $data['event_time'] }} WIB
</div>
```

### Step 4: Supported Field Types

| Type | Description | Editing Method |
|------|-------------|----------------|
| text | Single line text | Inline contentEditable |
| textarea | Multi-line text | Inline contentEditable |
| image | Image upload | Click overlay button |
| date | Date picker | Click to open picker |
| time | Time picker | Click to open picker |
| url | URL input | Inline contentEditable |

## Complete Example: Sanno Template

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $invitation->title }}</title>
    
    <link href="{{ asset('invitation-assets/sanno/css/style.css') }}" rel="stylesheet">
    
    {{-- Live Edit Script (only for owner) --}}
    @auth
    @if($invitation->user_id === auth()->id() || auth()->user()->hasRole('admin'))
    <script src="{{ asset('assets/js/live-edit.js') }}" defer></script>
    @endif
    @endauth
</head>
<body 
    data-invitation-id="{{ $invitation->id }}"
    data-is-owner="{{ $invitation->user_id === auth()->id() || auth()->user()->hasRole('admin') ? 'true' : 'false' }}"
>

<section id="hero" class="hero">
    <div class="hero-inner">
        {{-- Editable Logo --}}
        @if(!empty($data['company_logo']))
        <div class="hero-logo" 
             data-editable
             data-field-key="company_logo"
             data-field-type="image"
             data-field-label="Logo Perusahaan">
            <img src="{{ asset('storage/' . $data['company_logo']) }}" alt="Logo">
        </div>
        @endif
        
        {{-- Editable Title --}}
        <h1 class="hero-title"
            data-editable
            data-field-key="event_title"
            data-field-type="text"
            data-field-label="Judul Acara">
            {{ $data['event_title'] ?? 'Event Title' }}
        </h1>
        
        {{-- Editable Subtitle --}}
        @if(!empty($data['event_subtitle']))
        <p class="hero-subtitle"
           data-editable
           data-field-key="event_subtitle"
           data-field-type="text"
           data-field-label="Sub Judul">
            {{ $data['event_subtitle'] }}
        </p>
        @endif
        
        {{-- Editable Description --}}
        @if(!empty($data['event_description']))
        <p class="hero-description"
           data-editable
           data-field-key="event_description"
           data-field-type="textarea"
           data-field-label="Deskripsi">
            {{ $data['event_description'] }}
        </p>
        @endif
    </div>
</section>

<section id="event" class="section">
    <div class="event-card">
        {{-- Editable Date --}}
        @if(!empty($data['event_date']))
        <div class="event-date"
             data-editable
             data-field-key="event_date"
             data-field-type="date"
             data-field-label="Tanggal">
            {{ \Carbon\Carbon::parse($data['event_date'])->translatedFormat('l, d F Y') }}
        </div>
        @endif
        
        {{-- Editable Time --}}
        @if(!empty($data['event_time']))
        <div class="event-time"
             data-editable
             data-field-key="event_time"
             data-field-type="time"
             data-field-label="Waktu">
            {{ $data['event_time'] }} WIB
        </div>
        @endif
        
        {{-- Editable Venue --}}
        <div class="event-venue"
             data-editable
             data-field-key="event_venue"
             data-field-type="text"
             data-field-label="Tempat">
            {{ $data['event_venue'] ?? 'Venue' }}
        </div>
        
        {{-- Editable Address --}}
        @if(!empty($data['event_address']))
        <div class="event-address"
             data-editable
             data-field-key="event_address"
             data-field-type="textarea"
             data-field-label="Alamat">
            {{ $data['event_address'] }}
        </div>
        @endif
    </div>
</section>

<script src="{{ asset('invitation-assets/sanno/js/app.js') }}"></script>
</body>
</html>
```

## API Endpoints

### Update Field
```
POST /dash/api/invitations/{invitation}/live-edit
```

**Request:**
```json
{
    "field_key": "event_title",
    "value": "New Title"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Field updated successfully",
    "field_key": "event_title",
    "value": "New Title"
}
```

### Upload Image
```
POST /dash/api/invitations/{invitation}/live-edit
Content-Type: multipart/form-data
```

**Request:**
```
field_key: company_logo
value: [file]
```

**Response:**
```json
{
    "success": true,
    "message": "Field updated successfully",
    "field_key": "company_logo",
    "value": "invitations/1/logo.jpg"
}
```

## User Experience

### For Template Owners:
1. Open invitation preview
2. Click "Live Edit" button in top-right corner
3. Click on any text to edit inline
4. Hover on images to see "Ganti Foto" button
5. Changes save automatically
6. Click "Live Edit" again to exit edit mode

### For Non-Owners:
- Live Edit button and functionality not visible
- View-only mode

## Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Security
- Only invitation owner and admin can access live edit
- CSRF token validation
- File upload validation
- Authorization check on every API call

## Performance
- Debounced auto-save (1 second delay)
- Optimistic UI updates
- Minimal DOM manipulation
- Lazy loading of edit controls

## Troubleshooting

### Live Edit button not showing
- Check if user is logged in
- Verify user is the invitation owner
- Check if `data-invitation-id` and `data-is-owner` attributes are set on body

### Changes not saving
- Check browser console for errors
- Verify CSRF token is present
- Check network tab for API responses
- Ensure field_key matches template field key in database

### Image upload not working
- Check file size (max 2MB recommended)
- Verify file type (jpg, png, gif, webp)
- Check storage permissions
- Verify storage link: `php artisan storage:link`

## Best Practices

1. **Always add field labels** - Helps users know what they're editing
2. **Use appropriate field types** - Match the data type (text, textarea, date, etc.)
3. **Test on mobile** - Ensure touch interactions work well
4. **Provide visual feedback** - Use the status indicator
5. **Keep it simple** - Don't make everything editable, focus on key content

## Future Enhancements

- [ ] Undo/Redo functionality
- [ ] Revision history
- [ ] Collaborative editing
- [ ] Rich text editor for textarea
- [ ] Drag & drop image upload
- [ ] Bulk edit mode
- [ ] Preview before save
- [ ] Keyboard shortcuts

## Version
1.0.0 - Initial Release

## Support
For issues or questions, contact the development team.
