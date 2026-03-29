<?php

namespace App\Support;

/**
 * Kumpulan preset field untuk template undangan berdasarkan kategori acara.
 * Setiap kategori memiliki field standar yang konsisten.
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
            'wedding'    => 'Pernikahan (24 field)',
            'corporate'  => 'Acara Perusahaan (15 field)',
            'birthday'   => 'Ulang Tahun (13 field)',
            'circumcision' => 'Khitanan (14 field)',
            'empty'      => 'Kosong (tanpa field)',
        ];
    }

    /**
     * Ambil field definitions berdasarkan preset key.
     */
    public static function get(string $preset): array
    {
        return match ($preset) {
            'wedding'      => static::wedding(),
            'corporate'    => static::corporate(),
            'birthday'     => static::birthday(),
            'circumcision' => static::circumcision(),
            default        => [],
        };
    }

    /**
     * PRESET: PERNIKAHAN
     * Field standar untuk undangan pernikahan (akad + resepsi)
     */
    public static function wedding(): array
    {
        return [
            // ── Mempelai Pria ──────────────────────────────────────────
            ['key' => 'groom_name',        'label' => 'Nama Lengkap Mempelai Pria',    'type' => 'text',     'group' => 'mempelai', 'required' => true,  'order' => 1],
            ['key' => 'groom_nickname',    'label' => 'Nama Panggilan Pria',           'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 2],
            ['key' => 'groom_photo',       'label' => 'Foto Mempelai Pria',            'type' => 'image',    'group' => 'mempelai', 'required' => false, 'order' => 3],
            ['key' => 'groom_father',      'label' => 'Nama Ayah Mempelai Pria',       'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 4],
            ['key' => 'groom_mother',      'label' => 'Nama Ibu Mempelai Pria',        'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 5],
            
            // ── Mempelai Wanita ────────────────────────────────────────
            ['key' => 'bride_name',        'label' => 'Nama Lengkap Mempelai Wanita',  'type' => 'text',     'group' => 'mempelai', 'required' => true,  'order' => 6],
            ['key' => 'bride_nickname',    'label' => 'Nama Panggilan Wanita',         'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 7],
            ['key' => 'bride_photo',       'label' => 'Foto Mempelai Wanita',          'type' => 'image',    'group' => 'mempelai', 'required' => false, 'order' => 8],
            ['key' => 'bride_father',      'label' => 'Nama Ayah Mempelai Wanita',     'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 9],
            ['key' => 'bride_mother',      'label' => 'Nama Ibu Mempelai Wanita',      'type' => 'text',     'group' => 'mempelai', 'required' => false, 'order' => 10],
            
            // ── Akad Nikah ─────────────────────────────────────────────
            ['key' => 'akad_date',         'label' => 'Tanggal Akad',                  'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 11],
            ['key' => 'akad_time',         'label' => 'Waktu Akad',                    'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 12],
            ['key' => 'akad_venue',        'label' => 'Tempat Akad',                   'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 13],
            ['key' => 'akad_address',      'label' => 'Alamat Lengkap Akad',           'type' => 'textarea', 'group' => 'acara',    'required' => false, 'order' => 14],
            
            // ── Resepsi ────────────────────────────────────────────────
            ['key' => 'reception_date',    'label' => 'Tanggal Resepsi',               'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 15],
            ['key' => 'reception_time',    'label' => 'Waktu Resepsi',                 'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 16],
            ['key' => 'reception_venue',   'label' => 'Tempat Resepsi',                'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 17],
            ['key' => 'reception_address', 'label' => 'Alamat Lengkap Resepsi',        'type' => 'textarea', 'group' => 'acara',    'required' => false, 'order' => 18],
            
            // ── Informasi Tambahan ─────────────────────────────────────
            ['key' => 'maps_url',          'label' => 'Link Google Maps',              'type' => 'url',      'group' => 'tambahan', 'required' => false, 'order' => 19],
            ['key' => 'love_story',        'label' => 'Cerita Cinta',                  'type' => 'textarea', 'group' => 'tambahan', 'required' => false, 'order' => 20],
            ['key' => 'cover_photo',       'label' => 'Foto Cover',                    'type' => 'image',    'group' => 'tambahan', 'required' => false, 'order' => 21],
            
            // ── Musik Latar ────────────────────────────────────────────
            ['key' => 'music_url',         'label' => 'URL Lagu (mp3)',                'type' => 'url',      'group' => 'musik',    'required' => false, 'order' => 22],
            ['key' => 'music_title',       'label' => 'Judul Lagu',                    'type' => 'text',     'group' => 'musik',    'required' => false, 'order' => 23],
            ['key' => 'music_artist',      'label' => 'Nama Artis',                    'type' => 'text',     'group' => 'musik',    'required' => false, 'order' => 24],
        ];
    }

    /**
     * PRESET: ACARA PERUSAHAAN
     * Field untuk grand opening, seminar, launching produk, dll
     */
    public static function corporate(): array
    {
        return [
            // ── Informasi Perusahaan ───────────────────────────────────
            ['key' => 'company_name',      'label' => 'Nama Perusahaan',               'type' => 'text',     'group' => 'perusahaan', 'required' => true,  'order' => 1],
            ['key' => 'company_logo',      'label' => 'Logo Perusahaan',               'type' => 'image',    'group' => 'perusahaan', 'required' => false, 'order' => 2],
            ['key' => 'company_tagline',   'label' => 'Tagline/Slogan',                'type' => 'text',     'group' => 'perusahaan', 'required' => false, 'order' => 3],
            
            // ── Detail Acara ───────────────────────────────────────────
            ['key' => 'event_title',       'label' => 'Judul Acara',                   'type' => 'text',     'group' => 'acara',      'required' => true,  'order' => 4],
            ['key' => 'event_subtitle',    'label' => 'Subjudul Acara',                'type' => 'text',     'group' => 'acara',      'required' => false, 'order' => 5],
            ['key' => 'event_description', 'label' => 'Deskripsi Acara',               'type' => 'textarea', 'group' => 'acara',      'required' => false, 'order' => 6],
            ['key' => 'event_date',        'label' => 'Tanggal Acara',                 'type' => 'date',     'group' => 'acara',      'required' => true,  'order' => 7],
            ['key' => 'event_time',        'label' => 'Waktu Acara',                   'type' => 'time',     'group' => 'acara',      'required' => true,  'order' => 8],
            ['key' => 'event_venue',       'label' => 'Tempat Acara',                  'type' => 'text',     'group' => 'acara',      'required' => true,  'order' => 9],
            ['key' => 'event_address',     'label' => 'Alamat Lengkap',                'type' => 'textarea', 'group' => 'acara',      'required' => false, 'order' => 10],
            
            // ── Informasi Tambahan ─────────────────────────────────────
            ['key' => 'maps_url',          'label' => 'Link Google Maps',              'type' => 'url',      'group' => 'tambahan',   'required' => false, 'order' => 11],
            ['key' => 'rsvp_note',         'label' => 'Catatan RSVP',                  'type' => 'textarea', 'group' => 'tambahan',   'required' => false, 'order' => 12],
            ['key' => 'qr_note',           'label' => 'Catatan QR Code',               'type' => 'textarea', 'group' => 'tambahan',   'required' => false, 'order' => 13],
            ['key' => 'cover_photo',       'label' => 'Foto Cover',                    'type' => 'image',    'group' => 'tambahan',   'required' => false, 'order' => 14],
            ['key' => 'music_url',         'label' => 'URL Musik Latar (mp3)',         'type' => 'url',      'group' => 'musik',      'required' => false, 'order' => 15],
        ];
    }

    /**
     * PRESET: ULANG TAHUN
     * Field untuk undangan ulang tahun anak atau dewasa
     */
    public static function birthday(): array
    {
        return [
            // ── Yang Berulang Tahun ────────────────────────────────────
            ['key' => 'celebrant_name',    'label' => 'Nama Yang Berulang Tahun',      'type' => 'text',     'group' => 'celebrant', 'required' => true,  'order' => 1],
            ['key' => 'celebrant_nickname','label' => 'Nama Panggilan',                'type' => 'text',     'group' => 'celebrant', 'required' => false, 'order' => 2],
            ['key' => 'celebrant_photo',   'label' => 'Foto',                          'type' => 'image',    'group' => 'celebrant', 'required' => false, 'order' => 3],
            ['key' => 'celebrant_age',     'label' => 'Usia (tahun ke-)',              'type' => 'number',   'group' => 'celebrant', 'required' => false, 'order' => 4],
            ['key' => 'celebrant_birthdate','label' => 'Tanggal Lahir',                'type' => 'date',     'group' => 'celebrant', 'required' => false, 'order' => 5],
            
            // ── Detail Acara ───────────────────────────────────────────
            ['key' => 'party_theme',       'label' => 'Tema Pesta',                    'type' => 'text',     'group' => 'acara',     'required' => false, 'order' => 6],
            ['key' => 'party_date',        'label' => 'Tanggal Pesta',                 'type' => 'date',     'group' => 'acara',     'required' => true,  'order' => 7],
            ['key' => 'party_time',        'label' => 'Waktu Pesta',                   'type' => 'time',     'group' => 'acara',     'required' => true,  'order' => 8],
            ['key' => 'party_venue',       'label' => 'Tempat Pesta',                  'type' => 'text',     'group' => 'acara',     'required' => true,  'order' => 9],
            ['key' => 'party_address',     'label' => 'Alamat Lengkap',                'type' => 'textarea', 'group' => 'acara',     'required' => false, 'order' => 10],
            
            // ── Informasi Tambahan ─────────────────────────────────────
            ['key' => 'maps_url',          'label' => 'Link Google Maps',              'type' => 'url',      'group' => 'tambahan',  'required' => false, 'order' => 11],
            ['key' => 'special_message',   'label' => 'Pesan Khusus',                  'type' => 'textarea', 'group' => 'tambahan',  'required' => false, 'order' => 12],
            ['key' => 'cover_photo',       'label' => 'Foto Cover',                    'type' => 'image',    'group' => 'tambahan',  'required' => false, 'order' => 13],
        ];
    }

    /**
     * PRESET: KHITANAN
     * Field untuk undangan khitanan/sunatan
     */
    public static function circumcision(): array
    {
        return [
            // ── Anak Yang Dikhitan ─────────────────────────────────────
            ['key' => 'child_name',        'label' => 'Nama Lengkap Anak',             'type' => 'text',     'group' => 'anak',     'required' => true,  'order' => 1],
            ['key' => 'child_nickname',    'label' => 'Nama Panggilan',                'type' => 'text',     'group' => 'anak',     'required' => false, 'order' => 2],
            ['key' => 'child_photo',       'label' => 'Foto Anak',                     'type' => 'image',    'group' => 'anak',     'required' => false, 'order' => 3],
            ['key' => 'child_age',         'label' => 'Usia Anak',                     'type' => 'number',   'group' => 'anak',     'required' => false, 'order' => 4],
            
            // ── Orang Tua ──────────────────────────────────────────────
            ['key' => 'father_name',       'label' => 'Nama Ayah',                     'type' => 'text',     'group' => 'orangtua', 'required' => false, 'order' => 5],
            ['key' => 'mother_name',       'label' => 'Nama Ibu',                      'type' => 'text',     'group' => 'orangtua', 'required' => false, 'order' => 6],
            
            // ── Detail Acara ───────────────────────────────────────────
            ['key' => 'event_date',        'label' => 'Tanggal Acara',                 'type' => 'date',     'group' => 'acara',    'required' => true,  'order' => 7],
            ['key' => 'event_time',        'label' => 'Waktu Acara',                   'type' => 'time',     'group' => 'acara',    'required' => true,  'order' => 8],
            ['key' => 'event_venue',       'label' => 'Tempat Acara',                  'type' => 'text',     'group' => 'acara',    'required' => true,  'order' => 9],
            ['key' => 'event_address',     'label' => 'Alamat Lengkap',                'type' => 'textarea', 'group' => 'acara',    'required' => false, 'order' => 10],
            
            // ── Informasi Tambahan ─────────────────────────────────────
            ['key' => 'maps_url',          'label' => 'Link Google Maps',              'type' => 'url',      'group' => 'tambahan', 'required' => false, 'order' => 11],
            ['key' => 'special_message',   'label' => 'Pesan Khusus',                  'type' => 'textarea', 'group' => 'tambahan', 'required' => false, 'order' => 12],
            ['key' => 'cover_photo',       'label' => 'Foto Cover',                    'type' => 'image',    'group' => 'tambahan', 'required' => false, 'order' => 13],
            ['key' => 'music_url',         'label' => 'URL Musik Latar (mp3)',         'type' => 'url',      'group' => 'musik',    'required' => false, 'order' => 14],
        ];
    }
}
