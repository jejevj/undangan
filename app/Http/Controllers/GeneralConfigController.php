<?php

namespace App\Http\Controllers;

use App\Models\GeneralConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralConfigController extends Controller
{
    public function index()
    {
        $config = [
            'site_name' => GeneralConfig::get('site_name', config('app.name')),
            'site_description' => GeneralConfig::get('site_description', 'Platform undangan online terlengkap'),
            'contact_email' => GeneralConfig::get('contact_email', 'info@undanganberpesta.com'),
            'contact_phone' => GeneralConfig::get('contact_phone', '+62 xxx xxxx xxxx'),
            'logo_icon' => GeneralConfig::get('logo_icon'),
            'logo_dark' => GeneralConfig::get('logo_dark'),
            'logo_light' => GeneralConfig::get('logo_light'),
            'favicon' => GeneralConfig::get('favicon'),
            'hero_title' => GeneralConfig::get('hero_title', 'Buat Undangan Digital'),
            'hero_highlight' => GeneralConfig::get('hero_highlight', 'Impianmu'),
            'hero_subtitle' => GeneralConfig::get('hero_subtitle', 'Mulai Sekarang Gratis!'),
            'about_subtitle' => GeneralConfig::get('about_subtitle', 'SELAMAT DATANG DI UNDANGAN DIGITAL'),
            'about_title' => GeneralConfig::get('about_title', 'Platform Undangan Online Terlengkap'),
            'about_description' => GeneralConfig::get('about_description', 'Buat undangan digital yang elegan dan profesional dengan mudah.'),
            // SEO
            'meta_title' => GeneralConfig::get('meta_title', config('app.name') . ' - Undangan Digital'),
            'meta_description' => GeneralConfig::get('meta_description', 'Platform undangan online terlengkap dengan template elegan'),
            'meta_keywords' => GeneralConfig::get('meta_keywords', 'undangan digital, undangan online, wedding invitation'),
            'og_image' => GeneralConfig::get('og_image'),
            'google_analytics_id' => GeneralConfig::get('google_analytics_id'),
            'google_site_verification' => GeneralConfig::get('google_site_verification'),
        ];
        
        return view('general-config.index', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'logo_icon' => 'nullable|image|max:1024',
            'logo_dark' => 'nullable|image|max:2048',
            'logo_light' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
            'hero_title' => 'nullable|string|max:255',
            'hero_highlight' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'about_subtitle' => 'nullable|string|max:255',
            'about_title' => 'nullable|string|max:255',
            'about_description' => 'nullable|string',
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
            'google_analytics_id' => 'nullable|string|max:50',
            'google_site_verification' => 'nullable|string|max:100',
        ]);

        // Handle logo icon upload
        if ($request->hasFile('logo_icon')) {
            $oldLogoIcon = GeneralConfig::get('logo_icon');
            if ($oldLogoIcon && Storage::disk('public')->exists($oldLogoIcon)) {
                Storage::disk('public')->delete($oldLogoIcon);
            }
            $logoIconPath = $request->file('logo_icon')->store('config', 'public');
            GeneralConfig::set('logo_icon', $logoIconPath);
        }

        // Handle logo dark upload
        if ($request->hasFile('logo_dark')) {
            $oldLogoDark = GeneralConfig::get('logo_dark');
            if ($oldLogoDark && Storage::disk('public')->exists($oldLogoDark)) {
                Storage::disk('public')->delete($oldLogoDark);
            }
            $logoDarkPath = $request->file('logo_dark')->store('config', 'public');
            GeneralConfig::set('logo_dark', $logoDarkPath);
        }

        // Handle logo light upload
        if ($request->hasFile('logo_light')) {
            $oldLogoLight = GeneralConfig::get('logo_light');
            if ($oldLogoLight && Storage::disk('public')->exists($oldLogoLight)) {
                Storage::disk('public')->delete($oldLogoLight);
            }
            $logoLightPath = $request->file('logo_light')->store('config', 'public');
            GeneralConfig::set('logo_light', $logoLightPath);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $oldFavicon = GeneralConfig::get('favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $faviconPath = $request->file('favicon')->store('config', 'public');
            GeneralConfig::set('favicon', $faviconPath);
        }

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            $oldOgImage = GeneralConfig::get('og_image');
            if ($oldOgImage && Storage::disk('public')->exists($oldOgImage)) {
                Storage::disk('public')->delete($oldOgImage);
            }
            $ogImagePath = $request->file('og_image')->store('config', 'public');
            GeneralConfig::set('og_image', $ogImagePath);
        }

        // Save other configs
        foreach ($validated as $key => $value) {
            if (!in_array($key, ['logo_icon', 'logo_dark', 'logo_light', 'favicon', 'og_image']) && $value !== null) {
                GeneralConfig::set($key, $value);
            }
        }

        return redirect()->route('general-config.index')
            ->with('success', 'Konfigurasi berhasil diperbarui');
    }
}
