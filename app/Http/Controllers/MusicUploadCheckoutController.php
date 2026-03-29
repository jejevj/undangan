<?php

namespace App\Http\Controllers;

use App\Models\MusicUploadOrder;
use App\Models\PaymentChannel;
use App\Services\DokuVirtualAccountService;
use App\Services\DokuQrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MusicUploadCheckoutController extends Controller
{
    public function __construct(
        private DokuVirtualAccountService $vaService,
        private DokuQrisService $qrisService
    ) {}

    /**
     * Halaman pilih jumlah slot upload
     */
    public function selectQuantity()
    {
        $pricePerSlot = 10000; // Fixed price Rp 10.000 per slot
        
        return view('music.select-quantity', compact('pricePerSlot'));
    }

    /**
     * Halaman checkout dengan pilihan payment method (GET)
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:10',
            'order_id' => 'nullable|exists:music_upload_orders,id',
        ]);

        $qty = (int) $request->qty;
        $pricePerSlot = 10000;
        $subtotal = $qty * $pricePerSlot;

        // Check if order_id is provided and belongs to user
        if ($request->order_id) {
            $order = MusicUploadOrder::where('id', $request->order_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        } else {
            // Create pending order
            $order = MusicUploadOrder::create([
                'order_number' => MusicUploadOrder::generateOrderNumber(),
                'user_id' => auth()->id(),
                'qty' => $qty,
                'amount' => $subtotal,
                'price_per_slot' => $pricePerSlot,
                'admin_fee' => 0,
                'status' => 'pending',
                'payment_method' => null,
                'payment_channel_id' => null,
            ]);
        }

        // Check if there's existing VA or QRIS payment
        $virtualAccount = \App\Models\DokuVirtualAccount::where('payment_type', 'music_upload')
            ->where('reference_id', $order->id)
            ->where('status', 'pending')
            ->first();

        $qrisPayment = \App\Models\DokuQrisPayment::where('payment_type', 'music_upload')
            ->where('reference_id', $order->id)
            ->where('status', 'pending')
            ->first();
        
        // Check if QRIS is enabled for merchant (from .env)
        $qrisEnabled = env('DOKU_QRIS_ENABLED', 'false');
        $qrisEnabled = filter_var($qrisEnabled, FILTER_VALIDATE_BOOLEAN);

        return view('music.checkout', compact(
            'order',
            'qty',
            'pricePerSlot',
            'subtotal',
            'virtualAccount',
            'qrisPayment',
            'qrisEnabled'
        ));
    }

    /**
     * Create Virtual Account
     */
    public function createVA(MusicUploadOrder $order, Request $request)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $request->validate([
            'phone' => 'required|string|min:9|max:15',
            'channel' => 'required|string',
        ]);

        try {
            // Create VA
            $vaRecord = $this->vaService->createVirtualAccount(
                auth()->user(),
                'music_upload',
                $order->amount,
                $order->id,
                ['channel' => $request->channel]
            );

            $order->update([
                'payment_method' => $request->channel,
                'va_number' => $vaRecord->virtual_account_no,
                'expired_at' => $vaRecord->expired_at,
            ]);

            return redirect()->route('music.slots.checkout', ['qty' => $order->qty, 'order_id' => $order->id])
                ->with('success', 'Virtual Account berhasil dibuat!');

        } catch (\Exception $e) {
            Log::error('Create VA error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat Virtual Account: ' . $e->getMessage());
        }
    }

    /**
     * Create QRIS Payment
     */
    public function createQris(MusicUploadOrder $order, Request $request)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $request->validate([
            'phone' => 'required|string|min:9|max:15',
        ]);

        try {
            // Create QRIS
            $result = $this->qrisService->generateQris(
                auth()->user(),
                [
                    'payment_type' => 'music_upload',
                    'reference_id' => $order->id,
                    'amount' => $order->amount,
                    'postal_code' => '12345',
                    'expired_minutes' => 30,
                ]
            );

            if ($result['success']) {
                $qrisRecord = $result['qris'];
                
                $order->update([
                    'payment_method' => 'qris',
                    'qr_string' => $qrisRecord->qr_content,
                    'qr_url' => $qrisRecord->qr_url,
                    'expired_at' => $qrisRecord->expired_at,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'QRIS berhasil dibuat',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Gagal membuat QRIS',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Create QRIS error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QRIS: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(MusicUploadOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->isPaid()) {
            return response()->json([
                'status' => 'paid',
                'message' => 'Pembayaran berhasil!',
                'redirect' => route('music.upload'),
            ]);
        }

        // Check with payment gateway
        try {
            if ($order->payment_method === 'qris' || strpos($order->payment_method, 'QRIS') !== false) {
                // Check QRIS status via DOKU
                $qrisRecord = \App\Models\DokuQrisPayment::where('payment_type', 'music_upload')
                    ->where('reference_id', $order->id)
                    ->first();
                
                if ($qrisRecord && $qrisRecord->status === 'paid') {
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    return response()->json([
                        'status' => 'paid',
                        'message' => 'Pembayaran berhasil!',
                        'redirect' => route('music.upload'),
                    ]);
                }
            } elseif (strpos($order->payment_method, 'VIRTUAL_ACCOUNT') !== false) {
                // Check VA status via DOKU
                $vaRecord = \App\Models\DokuVirtualAccount::where('payment_type', 'music_upload')
                    ->where('reference_id', $order->id)
                    ->first();
                
                if ($vaRecord && $vaRecord->status === 'paid') {
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    return response()->json([
                        'status' => 'paid',
                        'message' => 'Pembayaran berhasil!',
                        'redirect' => route('music.upload'),
                    ]);
                }
            }

            return response()->json([
                'status' => 'pending',
                'message' => 'Menunggu pembayaran...',
            ]);

        } catch (\Exception $e) {
            Log::error('Check music upload payment status error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengecek status pembayaran',
            ], 500);
        }
    }
}
