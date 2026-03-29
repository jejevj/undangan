<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\UserSubscription;
use App\Models\DokuVirtualAccount;
use App\Models\DokuEWalletPayment;
use App\Services\DokuVirtualAccountService;
use App\Services\DokuEWalletService;
use App\Services\EventTrackingService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Track funnel event
        EventTrackingService::subscriptionFunnel('view_plans');

        // Hanya tampilkan paket public (sembunyikan business)
        $plans       = PricingPlan::publicPlans()->where('is_active', true)->orderBy('price')->get();
        $activePlan  = auth()->user()->activePlan();
        $activeSub   = auth()->user()->activeSubscription();

        return view('subscription.index', compact('plans', 'activePlan', 'activeSub'));
    }

    public function checkout(PricingPlan $plan)
    {
        // Track funnel events
        EventTrackingService::subscriptionFunnel('select_plan', ['plan_id' => $plan->id, 'plan_name' => $plan->name]);
        EventTrackingService::subscriptionFunnel('view_checkout', ['plan_id' => $plan->id, 'plan_name' => $plan->name]);

        $user = auth()->user();
        $activePlan = $user->activePlan();
        
        // Cegah checkout untuk business plan
        if ($plan->isBusinessPlan()) {
            return redirect()->route('subscription.index')
                ->with('error', 'Paket Business hanya bisa diaktifkan melalui admin. Silakan hubungi kami.');
        }
        
        if ($plan->isFree()) {
            return redirect()->route('subscription.index')
                ->with('info', 'Paket Free sudah aktif secara default.');
        }
        
        // Cegah downgrade ke paket lebih rendah
        if ($plan->isLowerThan($activePlan)) {
            return redirect()->route('subscription.index')
                ->with('error', "Anda tidak dapat membeli paket {$plan->name} karena sudah menggunakan paket {$activePlan->name} yang lebih tinggi.");
        }
        
        // Cegah checkout paket yang sama
        if ($plan->id === $activePlan->id) {
            return redirect()->route('subscription.index')
                ->with('info', "Anda sudah menggunakan paket {$plan->name}.");
        }

        // Buat order pending jika belum ada
        $order = UserSubscription::where('user_id', auth()->id())
            ->where('pricing_plan_id', $plan->id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            $order = UserSubscription::create([
                'user_id'         => auth()->id(),
                'pricing_plan_id' => $plan->id,
                'order_number'    => UserSubscription::generateOrderNumber(),
                'amount'          => $plan->price,
                'status'          => 'pending',
                'payment_method'  => 'doku_va',
            ]);
        } else {
            // Update amount jika harga paket berubah
            if ($order->amount != $plan->price) {
                $order->update(['amount' => $plan->price]);
                
                // Cancel VA lama jika ada karena amount berubah
                DokuVirtualAccount::where('payment_type', 'subscription')
                    ->where('reference_id', $order->id)
                    ->whereIn('status', ['pending', 'active'])
                    ->update(['status' => 'cancelled']);
                    
                // Cancel E-Wallet payment lama jika ada
                DokuEWalletPayment::where('payment_type', 'subscription')
                    ->where('reference_id', $order->id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->update(['status' => 'cancelled']);
            }
        }

        // Cek apakah sudah ada VA aktif untuk order ini
        $virtualAccount = DokuVirtualAccount::where('payment_type', 'subscription')
            ->where('reference_id', $order->id)
            ->whereIn('status', ['pending', 'active'])
            ->where('expired_at', '>', now())
            ->first();

        // Cek apakah sudah ada E-Wallet payment aktif untuk order ini
        $ewalletPayment = DokuEWalletPayment::where('payment_type', 'subscription')
            ->where('reference_id', $order->id)
            ->whereIn('status', ['pending', 'processing'])
            ->where('expired_at', '>', now())
            ->first();

        // Cek apakah sudah ada QRIS payment aktif untuk order ini
        $qrisPayment = \App\Models\DokuQrisPayment::where('payment_type', 'subscription')
            ->where('reference_id', $order->id)
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->first();

        return view('subscription.checkout', compact('plan', 'order', 'virtualAccount', 'ewalletPayment', 'qrisPayment'));
    }

    public function createVirtualAccount(Request $request, UserSubscription $order)
    {
        // Validasi ownership
        abort_if($order->user_id !== auth()->id(), 403);
        
        // Validasi status
        if ($order->status !== 'pending') {
            return redirect()->route('subscription.checkout', $order->plan)
                ->with('error', 'Order ini sudah diproses.');
        }

        // Validasi input
        $validated = $request->validate([
            'channel' => 'required|string|in:' . implode(',', array_keys(DokuVirtualAccountService::getAvailableChannels())),
            'phone' => 'required|string|min:9|max:15|regex:/^[0-9+]+$/',
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.min' => 'Nomor telepon minimal 9 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 digit.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda +.',
        ]);

        try {
            // Cek apakah sudah ada metode pembayaran aktif lainnya
            $existingEWallet = DokuEWalletPayment::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->whereIn('status', ['pending', 'processing'])
                ->where('expired_at', '>', now())
                ->first();

            if ($existingEWallet) {
                return redirect()->route('subscription.checkout', $order->plan)
                    ->with('error', 'Anda sudah memiliki pembayaran E-Wallet yang aktif. Selesaikan pembayaran tersebut terlebih dahulu atau tunggu hingga kadaluarsa.');
            }

            $existingQris = \App\Models\DokuQrisPayment::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->where('status', 'pending')
                ->where('expired_at', '>', now())
                ->first();

            if ($existingQris) {
                return redirect()->route('subscription.checkout', $order->plan)
                    ->with('error', 'Anda sudah memiliki pembayaran QRIS yang aktif. Selesaikan pembayaran tersebut terlebih dahulu atau tunggu hingga kadaluarsa.');
            }

            // Cek apakah sudah ada VA aktif
            $existingVA = DokuVirtualAccount::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->whereIn('status', ['pending', 'active'])
                ->where('expired_at', '>', now())
                ->first();

            if ($existingVA) {
                \Log::info('VA already exists for this order', [
                    'order_id' => $order->id,
                    'va_id' => $existingVA->id,
                    'va_number' => $existingVA->virtual_account_no,
                ]);
                
                return redirect()->route('subscription.checkout', $order->plan)
                    ->with('info', 'Virtual Account sudah dibuat sebelumnya. Silakan gunakan nomor VA yang sama untuk pembayaran.');
            }

            // Update user phone if not set
            $user = auth()->user();
            if (empty($user->phone)) {
                $user->update(['phone' => $validated['phone']]);
            }

            // Track funnel event
            EventTrackingService::subscriptionFunnel('select_payment', [
                'method' => 'virtual_account',
                'channel' => $validated['channel'],
                'order_id' => $order->id,
            ]);

            // Buat Virtual Account
            $vaService = new DokuVirtualAccountService();
            $virtualAccount = $vaService->createVirtualAccount(
                $user,
                'subscription',
                $order->amount,
                $order->id,
                [
                    'channel' => $validated['channel'],
                    'phone' => $validated['phone'],
                    'expired_hours' => 24,
                ]
            );

            // Track payment initiated
            EventTrackingService::subscriptionFunnel('payment_initiated', [
                'method' => 'virtual_account',
                'channel' => $validated['channel'],
                'order_id' => $order->id,
                'amount' => $order->amount,
            ]);

            \Log::info('VA created successfully', [
                'order_id' => $order->id,
                'va_id' => $virtualAccount->id,
                'va_number' => $virtualAccount->virtual_account_no,
            ]);

            return redirect()->route('subscription.checkout', $order->plan)
                ->with('success', 'Virtual Account berhasil dibuat. Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            \Log::error('Failed to create VA for subscription', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('subscription.checkout', $order->plan)
                ->with('error', 'Gagal membuat Virtual Account: ' . $e->getMessage());
        }
    }

    public function createEWalletPayment(Request $request, UserSubscription $order)
    {
        // Validasi ownership
        abort_if($order->user_id !== auth()->id(), 403);
        
        // Validasi status
        if ($order->status !== 'pending') {
            return redirect()->route('subscription.checkout', $order->plan)
                ->with('error', 'Order ini sudah diproses.');
        }

        // Validasi input
        $validated = $request->validate([
            'channel' => 'required|string|in:' . implode(',', array_keys(DokuEWalletService::getAvailableChannels())),
            'phone' => 'required|string|min:9|max:15|regex:/^[0-9+]+$/',
        ], [
            'channel.required' => 'Pilih metode pembayaran E-Wallet.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.min' => 'Nomor telepon minimal 9 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 digit.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda +.',
        ]);

        try {
            // Cek apakah sudah ada metode pembayaran aktif lainnya
            $existingVA = DokuVirtualAccount::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->whereIn('status', ['pending', 'active'])
                ->where('expired_at', '>', now())
                ->first();

            if ($existingVA) {
                return redirect()->route('subscription.checkout', $order->plan)
                    ->with('error', 'Anda sudah memiliki pembayaran Virtual Account yang aktif. Selesaikan pembayaran tersebut terlebih dahulu atau tunggu hingga kadaluarsa.');
            }

            $existingQris = \App\Models\DokuQrisPayment::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->where('status', 'pending')
                ->where('expired_at', '>', now())
                ->first();

            if ($existingQris) {
                return redirect()->route('subscription.checkout', $order->plan)
                    ->with('error', 'Anda sudah memiliki pembayaran QRIS yang aktif. Selesaikan pembayaran tersebut terlebih dahulu atau tunggu hingga kadaluarsa.');
            }

            // Cek apakah sudah ada payment aktif
            $existingPayment = DokuEWalletPayment::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->whereIn('status', ['pending', 'processing'])
                ->where('expired_at', '>', now())
                ->first();

            if ($existingPayment) {
                \Log::info('E-Wallet payment already exists for this order', [
                    'order_id' => $order->id,
                    'payment_id' => $existingPayment->id,
                    'partner_reference_no' => $existingPayment->partner_reference_no,
                ]);
                
                // Redirect to payment URL
                if ($existingPayment->web_redirect_url) {
                    return redirect($existingPayment->web_redirect_url);
                }
                
                // If no redirect URL, mark as failed and allow retry
                $existingPayment->markAsFailed();
                
                return redirect()->route('subscription.checkout', $order->plan)
                    ->with('warning', 'Pembayaran sebelumnya gagal. Silakan coba lagi dengan metode pembayaran yang sama atau berbeda.');
            }

            // Update user phone if not set
            $user = auth()->user();
            if (empty($user->phone)) {
                $user->update(['phone' => $validated['phone']]);
            }

            // Track funnel event
            EventTrackingService::subscriptionFunnel('select_payment', [
                'method' => 'ewallet',
                'channel' => $validated['channel'],
                'order_id' => $order->id,
            ]);

            // Buat E-Wallet Payment
            $ewalletService = new DokuEWalletService();
            $payment = $ewalletService->createPayment(
                $user,
                'subscription',
                $order->amount,
                $validated['channel'],
                $order->id,
                [
                    'success_url' => route('subscription.ewallet-success', $order),
                    'failed_url' => route('subscription.ewallet-failed', $order),
                    'expired_minutes' => 30,
                ]
            );

            // Track payment initiated
            EventTrackingService::subscriptionFunnel('payment_initiated', [
                'method' => 'ewallet',
                'channel' => $validated['channel'],
                'order_id' => $order->id,
                'amount' => $order->amount,
            ]);

            \Log::info('E-Wallet payment created successfully', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'partner_reference_no' => $payment->partner_reference_no,
                'channel' => $payment->channel,
                'web_redirect_url' => $payment->web_redirect_url,
            ]);

            // Redirect to payment URL if available
            if ($payment->web_redirect_url) {
                return redirect($payment->web_redirect_url);
            }

            // If no redirect URL, show error
            return redirect()->route('subscription.checkout', $order->plan)
                ->with('error', 'Pembayaran E-Wallet dibuat tapi link pembayaran tidak tersedia. Silakan coba lagi atau hubungi admin.');

        } catch (\Exception $e) {
            \Log::error('Failed to create E-Wallet payment for subscription', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('subscription.checkout', $order->plan)
                ->with('error', 'Gagal membuat pembayaran E-Wallet: ' . $e->getMessage());
        }
    }

    public function ewalletSuccess(UserSubscription $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        return redirect()->route('subscription.index')
            ->with('success', 'Pembayaran berhasil! Paket Anda akan segera diaktifkan.');
    }

    public function ewalletFailed(UserSubscription $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        return redirect()->route('subscription.checkout', $order->plan)
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }

    public function simulatePay(UserSubscription $subscription)
    {
        abort_if($subscription->user_id !== auth()->id(), 403);
        abort_if($subscription->isActive(), 400);

        // Nonaktifkan subscription lama
        auth()->user()->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $subscription->update([
            'status'    => 'active',
            'paid_at'   => now(),
            'starts_at' => now(),
            'expires_at'=> null,
        ]);

        return redirect()->route('subscription.index')
            ->with('success', "Selamat! Paket {$subscription->plan->name} berhasil diaktifkan.");
    }

    public function createQrisPayment(Request $request, UserSubscription $order)
    {
        // Validasi ownership
        abort_if($order->user_id !== auth()->id(), 403);
        
        // Validasi status
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order ini sudah diproses.',
            ], 400);
        }

        // Validasi input
        $validated = $request->validate([
            'phone' => 'required|string|min:9|max:15|regex:/^[0-9+]+$/',
            'postal_code' => 'nullable|string|size:5|regex:/^[0-9]+$/',
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.min' => 'Nomor telepon minimal 9 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 digit.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda +.',
            'postal_code.size' => 'Kode pos harus 5 digit.',
            'postal_code.regex' => 'Kode pos hanya boleh berisi angka.',
        ]);

        try {
            // Cek apakah sudah ada metode pembayaran aktif lainnya
            $existingVA = DokuVirtualAccount::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->whereIn('status', ['pending', 'active'])
                ->where('expired_at', '>', now())
                ->first();

            if ($existingVA) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki pembayaran Virtual Account yang aktif. Selesaikan pembayaran tersebut terlebih dahulu atau tunggu hingga kadaluarsa.',
                ], 400);
            }

            $existingEWallet = DokuEWalletPayment::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->whereIn('status', ['pending', 'processing'])
                ->where('expired_at', '>', now())
                ->first();

            if ($existingEWallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki pembayaran E-Wallet yang aktif. Selesaikan pembayaran tersebut terlebih dahulu atau tunggu hingga kadaluarsa.',
                ], 400);
            }

            // Cek apakah sudah ada QRIS aktif
            $existingQris = \App\Models\DokuQrisPayment::where('payment_type', 'subscription')
                ->where('reference_id', $order->id)
                ->where('status', 'pending')
                ->where('expired_at', '>', now())
                ->first();

            if ($existingQris) {
                \Log::info('QRIS payment already exists for this order', [
                    'order_id' => $order->id,
                    'qris_id' => $existingQris->id,
                    'partner_reference_no' => $existingQris->partner_reference_no,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'QRIS sudah dibuat sebelumnya. Silakan scan kode QR untuk melakukan pembayaran.',
                ]);
            }

            // Update user phone if not set
            $user = auth()->user();
            if (empty($user->phone)) {
                $user->update(['phone' => $validated['phone']]);
            }

            // Track funnel event
            EventTrackingService::subscriptionFunnel('select_payment', [
                'method' => 'qris',
                'order_id' => $order->id,
            ]);

            // Buat QRIS Payment
            $qrisService = new \App\Services\DokuQrisService();
            $result = $qrisService->generateQris($user, [
                'payment_type' => 'subscription',
                'amount' => $order->amount,
                'reference_id' => $order->id,
                'postal_code' => $validated['postal_code'] ?? '12345',
                'expired_minutes' => 30,
            ]);

            if ($result['success']) {
                // Track payment initiated
                EventTrackingService::subscriptionFunnel('payment_initiated', [
                    'method' => 'qris',
                    'order_id' => $order->id,
                    'amount' => $order->amount,
                ]);

                \Log::info('QRIS payment created successfully', [
                    'order_id' => $order->id,
                    'qris_id' => $result['qris']->id,
                    'partner_reference_no' => $result['qris']->partner_reference_no,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Kode QRIS berhasil dibuat. Silakan scan kode QR untuk melakukan pembayaran.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Gagal membuat QRIS',
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to create QRIS payment for subscription', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QRIS: ' . $e->getMessage(),
            ], 500);
        }
    }

}
