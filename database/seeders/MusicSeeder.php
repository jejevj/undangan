<?php

namespace Database\Seeders;

use App\Models\Music;
use Illuminate\Database\Seeder;

class MusicSeeder extends Seeder
{
    public function run(): void
    {
        $songs = [
            // ── Gratis ────────────────────────────────────────────────
            [
                'title'     => 'Perfect',
                'artist'    => 'Ed Sheeran',
                'file_path' => 'invitation-assets/music/perfect-ed-sheeran.mp3',
                'duration'  => '4:23',
                'type'      => 'free',
                'price'     => 0,
                'is_active' => true,
            ],
            [
                'title'     => 'A Thousand Years',
                'artist'    => 'Christina Perri',
                'file_path' => 'invitation-assets/music/a-thousand-years.mp3',
                'duration'  => '4:45',
                'type'      => 'free',
                'price'     => 0,
                'is_active' => true,
            ],
            [
                'title'     => "Can't Help Falling in Love",
                'artist'    => 'Elvis Presley',
                'file_path' => 'invitation-assets/music/cant-help-falling-in-love.mp3',
                'duration'  => '3:01',
                'type'      => 'free',
                'price'     => 0,
                'is_active' => true,
            ],
            // ── Premium ───────────────────────────────────────────────
            [
                'title'     => 'All of Me',
                'artist'    => 'John Legend',
                'file_path' => 'invitation-assets/music/all-of-me-john-legend.mp3',
                'duration'  => '4:29',
                'type'      => 'premium',
                'price'     => 10000,
                'is_active' => true,
            ],
            [
                'title'     => 'Thinking Out Loud',
                'artist'    => 'Ed Sheeran',
                'file_path' => 'invitation-assets/music/thinking-out-loud.mp3',
                'duration'  => '4:41',
                'type'      => 'premium',
                'price'     => 10000,
                'is_active' => true,
            ],
            [
                'title'     => 'Marry You',
                'artist'    => 'Bruno Mars',
                'file_path' => 'invitation-assets/music/marry-you-bruno-mars.mp3',
                'duration'  => '3:50',
                'type'      => 'premium',
                'price'     => 10000,
                'is_active' => true,
            ],
        ];

        foreach ($songs as $song) {
            Music::firstOrCreate(['file_path' => $song['file_path']], $song);
        }

        // Buat placeholder file agar tidak 404 saat preview
        foreach ($songs as $song) {
            $path = public_path($song['file_path']);
            if (!file_exists($path)) {
                // File kosong sebagai placeholder — ganti dengan file MP3 asli
                file_put_contents($path, '');
            }
        }

        $this->command->info('Music seeded: ' . count($songs) . ' lagu (3 gratis, 3 premium)');
        $this->command->warn('Catatan: Ganti file placeholder di public/invitation-assets/music/ dengan file MP3 asli.');
    }
}
