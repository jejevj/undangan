<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\MusicOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    /**
     * Galeri lagu — user pilih lagu untuk undangan
     */
    public function index()
    {
        $songs  = Music::where('is_active', true)->orderBy('type')->orderBy('title')->get();
        $myIds  = auth()->user()->musicLibrary()->pluck('music_id')->toArray();

        return view('music.index', compact('songs', 'myIds'));
    }

    /**
     * Form upload lagu oleh user (berbayar — cek payment nanti)
     */
    public function uploadForm()
    {
        return view('music.upload');
    }

    /**
     * Proses upload lagu oleh user
     * Untuk saat ini: gratis, nanti akan dicek payment
     */
    public function userUpload(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:100',
            'artist' => 'nullable|string|max:100',
            'file'   => 'required|file|mimes:mp3,ogg,wav|max:15360',
        ]);

        $file     = $request->file('file');
        $filename = \Illuminate\Support\Str::slug($request->title) . '-' . auth()->id() . '-' . time() . '.' . $file->getClientOriginalExtension();

        // Simpan ke storage/app/public/music-uploads/ (bukan public langsung)
        $path = $file->storeAs('music-uploads', $filename, 'public');

        $music = Music::create([
            'title'       => $request->title,
            'artist'      => $request->artist,
            'file_path'   => $path,
            'type'        => 'free',   // upload user = akses sendiri, tidak dijual
            'price'       => 0,
            'is_active'   => true,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('music.index')
            ->with('success', "Lagu \"{$music->title}\" berhasil diupload dan siap digunakan.");
    }
    /**
     * Halaman konfirmasi beli lagu premium (simulasi)
     */
    public function buy(Music $music)
    {
        if ($music->isFree()) {
            return redirect()->route('music.index')->with('info', 'Lagu ini gratis, tidak perlu dibeli.');
        }

        if (auth()->user()->hasAccessToMusic($music)) {
            return redirect()->route('music.index')->with('info', 'Anda sudah memiliki akses ke lagu ini.');
        }

        // Buat order pending
        $order = MusicOrder::firstOrCreate(
            ['user_id' => auth()->id(), 'music_id' => $music->id, 'status' => 'pending'],
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
