<?php

namespace App\Support;

/**
 * Kumpulan preset field untuk template undangan.
 * Digunakan saat membuat template baru agar tidak perlu input field satu per satu.
 */
class TemplateFieldPreset
{
    /**
     * Daftar semua preset yang tersedia.
     * Key = identifier preset, value = label yang ditampilkan di UI.
     */
    public static function all(): array
    {
        return [
            'wedding_standard' => 'Pernikahan Standar (21 field)',
            'wedding_simple'   => 'Pernikahan Sederhana (12 field)',
            'empty'            => 'Kosong (tanpa field)',
        ];
    }

    /**
     * Ambil field definitions berdasarkan preset key.
     */
    public static function get(string $preset): array
    {
        return match ($preset) {
            'wedding_standard' => static::weddingStandard(),
            'wedding_simple'   => static::weddingSimple(),
            default            => [],
        };
    }

    /**
     * Preset lengkap — sama dengan Premium White 1 (21 field).
     */
    public static function weddingStandard(): array
    {
        return [
            // ── Mempelai Pria ──────────────────────────────────────────
            ['key' => 'groom_name',        'label' => 'Nama Mempelai Pria',        'type' => 'text',     'group' => 'mempelai', 'required' => true,  'order' => 1],
            ['key' => 'groom_nickname',    'label' => 'Nama Panggilan Pria',       'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 2],
            ['key' => 'groom_photo',       'label' => 'Foto Mempelai Pria',        'type' => 'image',    'group' => 'mempelai', 'required' => false, 'order' => 3],
            ['key' => 'groom_father',      'label' => 'Nama Ayah Mempelai Pria',   'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 4],
            ['key' => 'groom_mother',      'label' => 'Nama Ibu Mempelai Pria',    'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 5],
            // ── Mempelai Wanita ────────────────────────────────────────
            ['key' => 'bride_name',        'label' => 'Nama Mempelai Wanita',      'type' => 'text',     'group' => 'mempelai', 'required' => true,  'order' => 6],
            ['key' => 'bride_nickname',    'label' => 'Nama Panggilan Wanita',     'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 7],
            ['key' => 'bride_photo',       'label' => 'Foto Mempelai Wanita',      'type' => 'image',    'group' => 'mempelai', 'required' => false, 'order' => 8],
            ['key' => 'bride_father',      'label' => 'Nama Ayah Mempelai Wanita', 'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 9],
            ['key' => 'bride_mother',      'label' => 'Nama Ibu Mempelai Wanita',  'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 10],
            // ── Akad ───────────────────────────────────────────────────
            ['key' => 'akad_date',         'label' => 'Tanggal Akad',              'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 11],
            ['key' => 'akad_time',         'label' => 'Waktu Akad',                'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 12],
            ['key' => 'akad_venue',        'label' => 'Tempat Akad',               'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 13],
            ['key' => 'akad_address',      'label' => 'Alamat Akad',               'type' => 'textarea', 'group' => 'acara',    'required' => false, 'order' => 14],
            // ── Resepsi ────────────────────────────────────────────────
            ['key' => 'reception_date',    'label' => 'Tanggal Resepsi',           'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 15],
            ['key' => 'reception_time',    'label' => 'Waktu Resepsi',             'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 16],
            ['key' => 'reception_venue',   'label' => 'Tempat Resepsi',            'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 17],
            ['key' => 'reception_address', 'label' => 'Alamat Resepsi',            'type' => 'textarea', 'group' => 'acara',    'required' => false, 'order' => 18],
            // ── Tambahan ───────────────────────────────────────────────
            ['key' => 'maps_url',          'label' => 'Link Google Maps',          'type' => 'url',      'group' => 'tambahan', 'required' => false, 'order' => 19],
            ['key' => 'love_story',        'label' => 'Cerita Cinta',              'type' => 'textarea', 'group' => 'tambahan', 'required' => false, 'order' => 20],
            ['key' => 'cover_photo',       'label' => 'Foto Cover',                'type' => 'image',    'group' => 'tambahan', 'required' => false, 'order' => 21],
            // ── Musik ──────────────────────────────────────────────────
            ['key' => 'music_url',         'label' => 'URL Lagu (mp3)',            'type' => 'url',      'group' => 'musik',    'required' => false, 'order' => 22],
            ['key' => 'music_title',       'label' => 'Judul Lagu',               'type' => 'text',     'group' => 'musik',    'required' => false, 'order' => 23],
            ['key' => 'music_artist',      'label' => 'Nama Artis',               'type' => 'text',     'group' => 'musik',    'required' => false, 'order' => 24],
        ];
    }

    /**
     * Preset sederhana — hanya data inti (12 field).
     */
    public static function weddingSimple(): array
    {
        return [
            ['key' => 'groom_name',        'label' => 'Nama Mempelai Pria',   'type' => 'text',     'group' => 'mempelai', 'required' => true,  'order' => 1],
            ['key' => 'groom_photo',       'label' => 'Foto Mempelai Pria',   'type' => 'image',    'group' => 'mempelai', 'required' => false, 'order' => 2],
            ['key' => 'bride_name',        'label' => 'Nama Mempelai Wanita', 'type' => 'text',     'group' => 'mempelai', 'required' => true,  'order' => 3],
            ['key' => 'bride_photo',       'label' => 'Foto Mempelai Wanita', 'type' => 'image',    'group' => 'mempelai', 'required' => false, 'order' => 4],
            ['key' => 'akad_date',         'label' => 'Tanggal Akad',         'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 5],
            ['key' => 'akad_time',         'label' => 'Waktu Akad',           'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 6],
            ['key' => 'akad_venue',        'label' => 'Tempat Akad',          'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 7],
            ['key' => 'reception_date',    'label' => 'Tanggal Resepsi',      'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 8],
            ['key' => 'reception_time',    'label' => 'Waktu Resepsi',        'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 9],
            ['key' => 'reception_venue',   'label' => 'Tempat Resepsi',       'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 10],
            ['key' => 'maps_url',          'label' => 'Link Google Maps',     'type' => 'url',      'group' => 'tambahan', 'required' => false, 'order' => 11],
            ['key' => 'cover_photo',       'label' => 'Foto Cover',           'type' => 'image',    'group' => 'tambahan', 'required' => false, 'order' => 12],
        ];
    }
}
