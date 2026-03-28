<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\DokuVirtualAccount;
use App\Models\DokuEWalletPayment;
use App\Models\UserSubscription;
use App\Models\PaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    /**
     * Handle DOKU payment notification webhook
     * 
     * DOKU will send POST request to this endpoint when payment is completed
     */
    public function handlePaymentNotification(Request $request)
    {
        Log::channel('va')->info('=== DOKU Webhook Received ===', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        try {
            // Validate webhook signature
            if (!$this->validateSignature($request)) {
                Log::channel('va')->error('Webhook signature validation failed', [
                    'headers' => $request->headers->all(),
                ]);
                
                return response()->json([
                    'responseCode' => '4010000',
                    'responseMessage' => 'Unauthorized. Invalid signature',
                ], 401);
            }

            // Get payment data from webhook
            $data = $request->all();
            
            // Process based on transaction type
            $transactionType = $data['additionalInfo']['channel'] ?? null;
            
            if (str_contains($transactionType, 'VIRTUAL_ACCOUNT')) {
                return $this->processVirtualAccountPayment($data);
            } elseif (str_contains($transactionType, 'EMONEY')) {
                return $this->processEWalletPayment($data);
            } elseif (isset($data['originalPartnerReferenceNo']) || isset($data['partnerReferenceNo'])) {
                // QRIS payment notification
                return $this->processQrisPayment($data);
            }

            Log::channel('va')->warning('Unknown transaction type', [
                'transaction_type' => $transactionType,
                'data' => $data,
            ]);

            return response()->json([
                'responseCode' => '2002700',
                'responseMessage' => 'Success',
            ]);

        } catch (\Exception $e) {
            Log::channel('va')->error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'responseCode' => '5002700',
                'responseMessage' => 'Internal Server Error',
            ], 500);
        }
    }

    /**
     * Process Virtual Account payment notification
     */
    protected function processVirtualAccountPayment(array $data)
    {
        $virtualAccountNo = $data['virtualAccountNo'] ?? null;
        $paidAmount = $data['paidAmount']['value'] ?? 0;
        $transactionDate = $data['transactionDate'] ?? null;
        $trxId = $data['trxId'] ?? null;

        Log::channel('va')->info('Processing VA payment', [
            'va_number' => $virtualAccountNo,
            'amount' => $paidAmount,
            'trx_id' => $trxId,
            'transaction_date' => $transactionDate,
        ]);

        // Find VA record by virtual account number or trx_id
        $va = DokuVirtualAccount::where('virtual_account_no', $virtualAccountNo)
            ->orWhere('trx_id', $trxId)
            ->first();

        if (!$va) {
            Log::channel('va')->warning('VA not found', [
                'va_number' => $virtualAccountNo,
                'trx_id' => $trxId,
            ]);

            return response()->json([
                'responseCode' => '4042712',
                'responseMessage' => 'Bill not found',
            ], 404);
        }

        // Check if already paid
        if ($va->status === 'paid') {
            Log::channel('va')->info('VA already paid', [
                'va_id' => $va->id,
                'va_number' => $virtualAccountNo,
            ]);

            return response()->json([
                'responseCode' => '2002700',
                'responseMessage' => 'Success',
            ]);
        }

        // Validate amount
        if ((float)$paidAmount != (float)$va->amount) {
            Log::channel('va')->error('Amount mismatch', [
                'expected' => $va->amount,
                'received' => $paidAmount,
                'va_id' => $va->id,
            ]);

            return response()->json([
                'responseCode' => '4002701',
                'responseMessage' => 'Invalid amount',
            ], 400);
        }

        // Mark VA as paid
        $va->markAsPaid();
        $va->update([
            'doku_response' => array_merge($va->doku_response ?? [], [
                'payment_notification' => $data,
            ]),
        ]);

        Log::channel('va')->info('VA marked as paid', [
            'va_id' => $va->id,
            'va_number' => $virtualAccountNo,
            'amount' => $paidAmount,
        ]);

        // Process based on payment type
        if ($va->payment_type === 'subscription') {
            $this->activateSubscription($va);
        } elseif ($va->payment_type === 'gift') {
            $this->activateGiftSection($va);
        } elseif ($va->payment_type === 'gallery') {
            $this->activateGalleryUpgrade($va);
        } elseif ($va->payment_type === 'music_upload') {
            $this->activateMusicUpload($va);
        }

        return response()->json([
            'responseCode' => '2002700',
            'responseMessage' => 'Success',
        ]);
    }

    /**
     * Process E-Wallet payment notification
     */
    protected function processEWalletPayment(array $data)
    {
        $referenceNo = $data['referenceNo'] ?? null;
        $amount = $data['amount']['value'] ?? 0;
        $transactionDate = $data['transactionDate'] ?? null;

        Log::channel('va')->info('Processing E-Wallet payment', [
            'reference_no' => $referenceNo,
            'amount' => $amount,
            'transaction_date' => $transactionDate,
        ]);

        // Find E-Wallet payment record
        $payment = DokuEWalletPayment::where('reference_no', $referenceNo)
            ->first();

        if (!$payment) {
            Log::channel('va')->warning('E-Wallet payment not found', [
                'reference_no' => $referenceNo,
            ]);

            return response()->json([
                'responseCode' => '4042712',
                'responseMessage' => 'Payment not found',
            ], 404);
        }

        // Check if already paid
        if ($payment->status === 'success') {
            return response()->json([
                'responseCode' => '2002700',
                'responseMessage' => 'Success',
            ]);
        }

        // Mark as success
        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
            'doku_response' => array_merge($payment->doku_response ?? [], [
                'payment_notification' => $data,
            ]),
        ]);

        // Process based on payment type
        if ($payment->payment_type === 'subscription') {
            $this->activateSubscription($payment);
        }

        return response()->json([
            'responseCode' => '2002700',
            'responseMessage' => 'Success',
        ]);
    }

    /**
     * Process QRIS payment notification
     */
    protected function processQrisPayment(array $data)
    {
        $partnerReferenceNo = $data['originalPartnerReferenceNo'] ?? $data['partnerReferenceNo'] ?? null;
        $amount = $data['transactionAmount']['value'] ?? $data['amount']['value'] ?? 0;
        $transactionDate = $data['transactionDate'] ?? null;
        $approvalCode = $data['additionalInfo']['approvalCode'] ?? null;

        Log::channel('va')->info('Processing QRIS payment', [
            'partner_reference_no' => $partnerReferenceNo,
            'amount' => $amount,
            'transaction_date' => $transactionDate,
            'approval_code' => $approvalCode,
        ]);

        // Find QRIS payment record
        $qris = \App\Models\DokuQrisPayment::where('partner_reference_no', $partnerReferenceNo)
            ->first();

        if (!$qris) {
            Log::channel('va')->warning('QRIS payment not found', [
                'partner_reference_no' => $partnerReferenceNo,
            ]);

            return response()->json([
                'responseCode' => '4042712',
                'responseMessage' => 'Payment not found',
            ], 404);
        }

        // Check if already paid
        if ($qris->status === 'paid') {
            Log::channel('va')->info('QRIS already paid', [
                'qris_id' => $qris->id,
                'partner_reference_no' => $partnerReferenceNo,
            ]);

            return response()->json([
                'responseCode' => '2002700',
                'responseMessage' => 'Success',
            ]);
        }

        // Validate amount
        if ((float)$amount != (float)$qris->amount) {
            Log::channel('va')->error('QRIS amount mismatch', [
                'expected' => $qris->amount,
                'received' => $amount,
                'qris_id' => $qris->id,
            ]);

            return response()->json([
                'responseCode' => '4002701',
                'responseMessage' => 'Invalid amount',
            ], 400);
        }

        // Mark QRIS as paid
        $qris->markAsPaid();
        $qris->update([
            'approval_code' => $approvalCode,
            'doku_response' => array_merge($qris->doku_response ?? [], [
                'payment_notification' => $data,
                'paid_at' => now()->toISOString(),
            ]),
        ]);

        Log::channel('va')->info('QRIS marked as paid', [
            'qris_id' => $qris->id,
            'partner_reference_no' => $partnerReferenceNo,
            'amount' => $amount,
            'approval_code' => $approvalCode,
        ]);

        // Process based on payment type
        if ($qris->payment_type === 'subscription') {
            $this->activateSubscription($qris);
        } elseif ($qris->payment_type === 'gift') {
            $this->activateGiftSection($qris);
        } elseif ($qris->payment_type === 'gallery') {
            $this->activateGalleryUpgrade($qris);
        } elseif ($qris->payment_type === 'music_upload') {
            $this->activateMusicUpload($qris);
        }

        return response()->json([
            'responseCode' => '2002700',
            'responseMessage' => 'Success',
        ]);
    }

    /**
     * Activate subscription after payment
     */
    protected function activateSubscription($payment)
    {
        $order = UserSubscription::find($payment->reference_id);
        
        if (!$order) {
            Log::channel('va')->error('Subscription order not found', [
                'reference_id' => $payment->reference_id,
            ]);
            return;
        }

        // Get plan details to set proper expiration
        $plan = $order->plan;

        // Update order status and activate subscription
        $order->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays(30), // Default 30 days, adjust based on plan if needed
            'paid_at' => now(),
            'payment_method' => 'doku_va',
        ]);

        Log::channel('va')->info('Subscription activated via webhook', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'plan_id' => $order->pricing_plan_id,
            'plan_name' => $plan->name ?? 'Unknown',
            'starts_at' => $order->starts_at,
            'expires_at' => $order->expires_at,
        ]);

        // TODO: Send email notification to user
    }

    /**
     * Activate gift section after payment
     */
    protected function activateGiftSection($payment)
    {
        // TODO: Implement gift section activation
        Log::channel('va')->info('Gift section payment received', [
            'reference_id' => $payment->reference_id,
        ]);
    }

    /**
     * Activate gallery upgrade after payment
     */
    protected function activateGalleryUpgrade($payment)
    {
        // TODO: Implement gallery upgrade activation
        Log::channel('va')->info('Gallery upgrade payment received', [
            'reference_id' => $payment->reference_id,
        ]);
    }

    /**
     * Activate music upload after payment
     */
    protected function activateMusicUpload($payment)
    {
        // TODO: Implement music upload activation
        Log::channel('va')->info('Music upload payment received', [
            'reference_id' => $payment->reference_id,
        ]);
    }

    /**
     * Validate DOKU webhook signature
     */
    protected function validateSignature(Request $request): bool
    {
        $signature = $request->header('X-SIGNATURE');
        $timestamp = $request->header('X-TIMESTAMP');
        
        if (!$signature || !$timestamp) {
            return false;
        }

        // Get DOKU configuration
        $config = PaymentGatewayConfig::getActive('doku');
        if (!$config) {
            return false;
        }

        $clientSecret = $config->secret_key;
        $httpMethod = 'POST';
        $endpointUrl = '/webhook/doku/payment-notification';
        $requestBody = $request->getContent();

        // Generate signature
        $stringToSign = "{$httpMethod}:{$endpointUrl}:{$requestBody}:{$timestamp}";
        $calculatedSignature = hash_hmac('sha256', $stringToSign, $clientSecret);

        Log::channel('va')->info('Signature validation', [
            'received_signature' => $signature,
            'calculated_signature' => $calculatedSignature,
            'timestamp' => $timestamp,
            'match' => hash_equals($calculatedSignature, $signature),
        ]);

        return hash_equals($calculatedSignature, $signature);
    }
}
