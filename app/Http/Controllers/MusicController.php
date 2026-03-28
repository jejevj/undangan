<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\MusicOrder;
use App\Models\MusicUploadOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    /**
     * Galeri lagu — user pilih lagu untuk undangan
     * Hanya tampilkan:
     * 1. Lagu gratis (sistem)
     * 2. Lagu premium yang sudah dibeli
     * 3. Lagu yang diupload sendiri
     * 4. Lagu premium gratis untuk paket Basic/Pro
     * 
     * UPDATE: Tampilkan juga musik premium yang belum dibeli (dengan opsi beli)
     */
    public function index()
    {
        $user = auth()->user();
        $plan = $user->activePlan();
        
        // Cek apakah user punya akses premium gratis (Basic/Pro)
        $hasPremiumAccess = in_array($plan->slug, ['basic', 'pro']) || $user->isAdmin();
        
        // Ambil semua musik aktif
        $allSongs = Music::where('is_active', true)
            ->orderByRaw("CASE WHEN uploaded_by = {$user->id} THEN 0 ELSE 1 END")
            ->orderBy('type')
            ->orderBy('title')
            ->get();
        
        // ID musik yang sudah dimiliki user
        $myIds = $user->musicLibrary()->pluck('music_id')->toArray();
        
        // Filter: musik yang bisa diakses (untuk dropdown form)
        $accessibleSongs = $allSongs->filter(function($song) use ($user, $myIds, $hasPremiumAccess) {
            return $song->isFree() 
                || in_array($song->id, $myIds) 
                || $song->uploaded_by === $user->id
                || ($hasPremiumAccess && $song->type === 'premium'); // Premium gratis untuk Basic/Pro
        });

        return view('music.index', compact('allSongs', 'myIds', 'accessibleSongs', 'hasPremiumAccess'));
    }

    /**
     * Form upload lagu oleh user
     * Menampilkan informasi biaya upload (jika ada)
     */
    public function uploadForm()
    {
        $user = auth()->user();
        $activePlan = $user->activePlan();
        
        // Cek apakah user bisa upload
        if ($activePlan->max_music_uploads === 0) {
            return redirect()->route('music.index')
                ->with('error', 'Fitur upload musik tidak tersedia di paket ' . $activePlan->name . '. Upgrade ke paket Basic atau Pro untuk upload lagu.');
        }
        
        // Cek limit upload
        $uploadedCount = \App\Models\Music::where('uploaded_by', $user->id)->count();
        if ($activePlan->max_music_uploads !== null && $uploadedCount >= $activePlan->max_music_uploads) {
            return redirect()->route('music.index')
                ->with('error', 'Limit upload lagu tercapai (' . $uploadedCount . '/' . $activePlan->max_music_uploads . '). Upgrade ke paket Pro untuk upload unlimited.');
        }
        
        $uploadFee = 0; // Gratis untuk Basic/Pro
        return view('music.upload', compact('uploadFee', 'activePlan', 'uploadedCount'));
    }

    /**
     * Proses upload lagu oleh user
     * Step 1: Upload file dan langsung buat record Music (gratis untuk Basic/Pro)
     */
    public function userUpload(Request $request)
    {
        $user = auth()->user();
        $activePlan = $user->activePlan();
        
        // Cek apakah user bisa upload
        if ($activePlan->max_music_uploads === 0) {
            return redirect()->route('music.index')
                ->with('error', 'Fitur upload musik tidak tersedia di paket ' . $activePlan->name);
        }
        
        // Cek limit upload
        $uploadedCount = \App\Models\Music::where('uploaded_by', $user->id)->count();
        if ($activePlan->max_music_uploads !== null && $uploadedCount >= $activePlan->max_music_uploads) {
            return redirect()->route('music.index')
                ->with('error', 'Limit upload lagu tercapai (' . $uploadedCount . '/' . $activePlan->max_music_uploads . ')');
        }
        
        $request->validate([
            'title'  => 'required|string|max:100',
            'artist' => 'nullable|string|max:100',
            'file'   => 'required|file|mimes:mp3,ogg,wav|max:15360',
        ]);

        $file     = $request->file('file');
        $filename = \Illuminate\Support\Str::slug($request->title) . '-' . auth()->id() . '-' . time() . '.' . $file->getClientOriginalExtension();

        // Simpan langsung ke permanent folder (gratis untuk Basic/Pro)
        $permanentPath = $file->storeAs('music-uploads', $filename, 'public');

        // Buat record Music langsung
        $music = Music::create([
            'title'       => $request->title,
            'artist'      => $request->artist,
            'file_path'   => $permanentPath,
            'type'        => 'free', // Lagu upload user selalu free type
            'price'       => 0,
            'is_active'   => true,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('music.index')
            ->with('success', "Lagu \"{$music->title}\" berhasil diupload dan sudah tersedia di library Anda!");
    }

    /**
     * Halaman checkout untuk upload musik
     */
    public function uploadCheckout(MusicUploadOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->isPaid()) {
            return redirect()->route('music.index')
                ->with('info', 'Upload sudah selesai.');
        }

        return view('music.upload-checkout', compact('order'));
    }

    /**
     * Simulasi pembayaran upload musik
     * Setelah paid: pindahkan file dari temp ke permanent dan buat record Music
     */
    public function uploadPay(MusicUploadOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if($order->isPaid(), 400, 'Order sudah dibayar.');

        // Update order status
        $order->update([
            'status'         => 'paid',
            'paid_at'        => now(),
            'payment_method' => 'simulation',
        ]);

        // Pindahkan file dari temp ke permanent
        $tempPath = $order->temp_file_path;
        $filename = basename($tempPath);
        $newFilename = str_replace('-temp-', '-', $filename);
        $permanentPath = 'music-uploads/' . $newFilename;

        // Copy file
        Storage::disk('public')->copy($tempPath, $permanentPath);

        // Buat record Music
        $music = Music::create([
            'title'       => $order->temp_title,
            'artist'      => $order->temp_artist,
            'file_path'   => $permanentPath,
            'type'        => 'free',
            'price'       => 0,
            'is_active'   => true,
            'uploaded_by' => auth()->id(),
        ]);

        // Update order dengan music_id
        $order->update(['music_id' => $music->id]);

        // Hapus file temporary
        Storage::disk('public')->delete($tempPath);

        return redirect()->route('music.index')
            ->with('success', "Pembayaran berhasil! Lagu \"{$music->title}\" sudah tersedia di library Anda.");
    }
    /**
     * Halaman konfirmasi beli lagu premium (simulasi)
     */
    public function buy(Music $music)
    {
        $user = auth()->user();
        $plan = $user->activePlan();
        
        if ($music->isFree()) {
            return redirect()->route('music.index')->with('info', 'Lagu ini gratis, tidak perlu dibeli.');
        }

        // Basic dan Pro bisa akses semua lagu premium gratis
        if (in_array($plan->slug, ['basic', 'pro']) || $user->isAdmin()) {
            return redirect()->route('music.index')
                ->with('info', 'Paket ' . $plan->name . ' Anda sudah termasuk akses ke semua lagu premium secara gratis!');
        }

        if ($user->hasAccessToMusic($music)) {
            return redirect()->route('music.index')->with('info', 'Anda sudah memiliki akses ke lagu ini.');
        }

        // Buat order pending (hanya untuk Free plan)
        $order = MusicOrder::firstOrCreate(
            ['user_id' => $user->id, 'music_id' => $music->id, 'status' => 'pending'],
            [
                'order_number'   => MusicOrder::generateOrderNumber(),
                'amount'         => $music->price,
                'payment_method' => 'simulation',
            ]
        );

        return view('music.buy', compact('music', 'order'));
    }

    /**
     * Simulasi pembayaran — langsung set paid tanpa gateway
     * Nanti diganti dengan callback dari payment gateway
     */
    public function simulatePay(MusicOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if($order->isPaid(), 400, 'Order sudah dibayar.');

        $order->update([
            'status'         => 'paid',
            'paid_at'        => now(),
            'payment_method' => 'simulation',
        ]);

        // Grant akses ke user
        $order->user->musicLibrary()->syncWithoutDetaching([
            $order->music_id => ['granted_at' => now()],
        ]);

        return redirect()->route('music.index')
            ->with('success', "Pembayaran berhasil! Lagu \"{$order->music->title}\" sudah tersedia di library Anda.");
    }

    // ── Admin: CRUD lagu ──────────────────────────────────────────────

    public function adminIndex()
    {
        $songs = Music::withCount('users')->orderBy('type')->orderBy('title')->get();
        return view('music.admin.index', compact('songs'));
    }

    public function adminCreate()
    {
        return view('music.admin.create');
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:100',
            'artist'  => 'nullable|string|max:100',
            'type'    => 'required|in:free,premium',
            'price'   => 'required_if:type,premium|integer|min:0',
            'file'    => 'required|file|mimes:mp3,ogg,wav|max:15360', // 15MB
            'cover'   => 'nullable|image|max:2048',
            'duration'=> 'nullable|string|max:10',
        ]);

        // Simpan file audio ke public/invitation-assets/music/
        $file     = $request->file('file');
        $filename = \Illuminate\Support\Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('invitation-assets/music'), $filename);

        $data = $request->only('title', 'artist', 'type', 'duration');
        $data['file_path'] = 'invitation-assets/music/' . $filename;
        $data['price']     = $request->type === 'free' ? 0 : (int) $request->price;
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('music-covers', 'public');
        }

        Music::create($data);

        return redirect()->route('music.admin.index')->with('success', 'Lagu berhasil ditambahkan.');
    }

    public function adminDestroy(Music $music)
    {
        // Hapus file fisik
        $fullPath = public_path($music->file_path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $music->delete();
        return redirect()->route('music.admin.index')->with('success', 'Lagu berhasil dihapus.');
    }

    public function adminToggle(Music $music)
    {
        $music->update(['is_active' => !$music->is_active]);
        return back()->with('success', 'Status lagu diupdate.');
    }
}
