<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\GalleryOrder;
use App\Models\PaymentChannel;
use App\Services\DokuVirtualAccountService;
use App\Services\DokuQrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GalleryCheckoutController extends Controller
{
    public function __construct(
        private DokuVirtualAccountService $vaService,
        private DokuQrisService $qrisService
    ) {}

    /**
     * Halaman pilih jumlah slot foto
     */
    public function selectQuantity()
    {
        $userSlots = auth()->user()->getGallerySlots();
        $pricePerPhoto = 5000; // Fixed price per slot
        $currentSlots = $userSlots->totalSlots();
        $usedSlots = $userSlots->usedSlots();

        return view('gallery.select-quantity', compact('pricePerPhoto', 'currentSlots', 'usedSlots'));
    }

    /**
     * Halaman checkout dengan pilihan payment method (GET)
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:50',
            'order_id' => 'nullable|exists:gallery_orders,id',
        ]);

        $qty = (int) $request->qty;
        $pricePerPhoto = 5000;
        $subtotal = $qty * $pricePerPhoto;

        // Check if order_id is provided and belongs to user
        if ($request->order_id) {
            $order = GalleryOrder::where('id', $request->order_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        } else {
            // Create pending order
            $order = GalleryOrder::create([
                'order_number' => GalleryOrder::generateOrderNumber(),
                'user_id' => auth()->id(),
                'qty' => $qty,
                'amount' => $subtotal, // Will be updated when payment is created
                'price_per_photo' => $pricePerPhoto,
                'admin_fee' => 0, // Will be updated when payment is created
                'status' => 'pending',
                'payment_method' => null,
                'payment_channel_id' => null,
            ]);
        }

        // Check if there's existing VA or QRIS payment
        $virtualAccount = \App\Models\DokuVirtualAccount::where('payment_type', 'gallery_order')
            ->where('reference_id', $order->id)
            ->where('status', 'pending')
            ->first();

        $qrisPayment = \App\Models\DokuQrisPayment::where('payment_type', 'gallery_order')
            ->where('reference_id', $order->id)
            ->where('status', 'pending')
            ->first();
        
        // Check if QRIS is enabled for merchant (from .env)
        $qrisEnabled = env('DOKU_QRIS_ENABLED', 'false');
        $qrisEnabled = filter_var($qrisEnabled, FILTER_VALIDATE_BOOLEAN);

        return view('gallery.checkout', compact(
            'order',
            'qty',
            'pricePerPhoto',
            'subtotal',
            'virtualAccount',
            'qrisPayment',
            'qrisEnabled'
        ));
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:50',
            'payment_channel_id' => 'required|exists:payment_channels,id',
        ]);

        $qty = (int) $request->qty;
        $pricePerPhoto = 5000;
        $subtotal = $qty * $pricePerPhoto;

        $paymentChannel = PaymentChannel::findOrFail($request->payment_channel_id);
        
        if (!$paymentChannel->is_active) {
            return back()->with('error', 'Metode pembayaran tidak tersedia.');
        }

        // Calculate admin fee
        $adminFee = $paymentChannel->calculateFee($subtotal);
        $total = $subtotal + $adminFee;

        try {
            DB::beginTransaction();

            // Create gallery order (user-level, no invitation_id)
            $order = GalleryOrder::create([
                'order_number' => GalleryOrder::generateOrderNumber(),
                'user_id' => auth()->id(),
                'qty' => $qty,
                'amount' => $total,
                'price_per_photo' => $pricePerPhoto,
                'admin_fee' => $adminFee,
                'status' => 'pending',
                'payment_method' => $paymentChannel->code,
                'payment_channel_id' => $paymentChannel->id,
            ]);

            // Generate payment based on method
            if ($paymentChannel->category === 'virtual_account') {
                // Create VA using existing service
                $vaRecord = $this->vaService->createVirtualAccount(
                    auth()->user(),
                    'gallery_order',
                    $total,
                    $order->id,
                    ['channel' => $paymentChannel->code]
                );
                
                $order->update([
                    'va_number' => $vaRecord->virtual_account_no,
                    'payment_url' => null,
                    'expired_at' => $vaRecord->expired_at,
                ]);

            } elseif ($paymentChannel->category === 'qris') {
                // Create QRIS using existing service
                $result = $this->qrisService->generateQris(
                    auth()->user(),
                    [
                        'payment_type' => 'gallery_order',
                        'reference_id' => $order->id,
                        'amount' => $total,
                        'postal_code' => '12345',
                        'expired_minutes' => 30,
                    ]
                );
                
                if (!$result['success']) {
                    throw new \Exception($result['error'] ?? 'Gagal membuat QRIS');
                }
                
                $qrisRecord = $result['qris'];
                
                $order->update([
                    'qr_string' => $qrisRecord->qr_content,
                    'qr_url' => $qrisRecord->qr_url,
                    'expired_at' => $qrisRecord->expired_at,
                ]);

            } else {
                throw new \Exception('Payment method not supported yet');
            }

            DB::commit();

            return redirect()->route('gallery.payment', $order)
                ->with('success', 'Order berhasil dibuat. Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gallery checkout error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Create Virtual Account
     */
    public function createVA(GalleryOrder $order, Request $request)
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
                'gallery_order',
                $order->amount,
                $order->id,
                ['channel' => $request->channel]
            );

            $order->update([
                'payment_method' => $request->channel,
                'va_number' => $vaRecord->virtual_account_no,
                'expired_at' => $vaRecord->expired_at,
            ]);

            return redirect()->route('gallery.checkout', ['qty' => $order->qty, 'order_id' => $order->id])
                ->with('success', 'Virtual Account berhasil dibuat!');

        } catch (\Exception $e) {
            Log::error('Create VA error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat Virtual Account: ' . $e->getMessage());
        }
    }

    /**
     * Create QRIS Payment
     */
    public function createQris(GalleryOrder $order, Request $request)
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
                    'payment_type' => 'gallery_order',
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
     * Payment page
     */
    public function payment(GalleryOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->isPaid()) {
            return redirect()->route('dashboard')
                ->with('success', 'Pembayaran sudah berhasil!');
        }

        $order->load('paymentChannel');

        return view('gallery.payment', compact('order'));
    }

    /**
     * Check payment status
     */
    public function checkStatus(GalleryOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->isPaid()) {
            return response()->json([
                'status' => 'paid',
                'message' => 'Pembayaran berhasil!',
                'redirect' => route('dashboard'),
            ]);
        }

        // Check with payment gateway
        try {
            if ($order->payment_method === 'qris' || strpos($order->payment_method, 'QRIS') !== false) {
                // Check QRIS status via DOKU
                $qrisRecord = \App\Models\DokuQrisPayment::where('payment_type', 'gallery_order')
                    ->where('reference_id', $order->id)
                    ->first();
                
                if ($qrisRecord && $qrisRecord->status === 'paid') {
                    // Add slots to user
                    auth()->user()->getGallerySlots()->addPurchasedSlots($order->qty);
                    
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    return response()->json([
                        'status' => 'paid',
                        'message' => 'Pembayaran berhasil!',
                        'redirect' => route('dashboard'),
                    ]);
                }
            } elseif (strpos($order->payment_method, 'VIRTUAL_ACCOUNT') !== false) {
                // Check VA status via DOKU
                $vaRecord = \App\Models\DokuVirtualAccount::where('payment_type', 'gallery_order')
                    ->where('reference_id', $order->id)
                    ->first();
                
                if ($vaRecord && $vaRecord->status === 'paid') {
                    // Add slots to user
                    auth()->user()->getGallerySlots()->addPurchasedSlots($order->qty);
                    
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    return response()->json([
                        'status' => 'paid',
                        'message' => 'Pembayaran berhasil!',
                        'redirect' => route('dashboard'),
                    ]);
                }
            }

            return response()->json([
                'status' => 'pending',
                'message' => 'Menunggu pembayaran...',
            ]);

        } catch (\Exception $e) {
            Log::error('Check gallery payment status error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengecek status pembayaran',
            ], 500);
        }
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }
}