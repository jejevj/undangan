<?php

namespace App\Http\Controllers;

use App\Models\DokuVirtualAccount;
use App\Models\PaymentGatewayConfig;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\PricingPlan;
use App\Models\GalleryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DokuWebhookController extends Controller
{
    /**
     * Handle payment notification from DOKU
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handlePaymentNotification(Request $request)
    {
        // Log incoming webhook
        Log::info('DOKU Webhook Received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        try {
            // Verify signature
            if (!$this->verifySignature($request)) {
                Log::error('DOKU Webhook: Invalid signature', [
                    'body' => $request->all(),
                ]);

                return response()->json([
                    'responseCode' => '4017300',
                    'responseMessage' => 'Unauthorized. Invalid signature',
                ], 401);
            }

            // Process payment
            $result = $this->processPayment($request);

            // Return success response
            return response()->json([
                'responseCode' => '2002500',
                'responseMessage' => 'Success',
            ], 200);

        } catch (\Exception $e) {
            Log::error('DOKU Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'responseCode' => '5002500',
                'responseMessage' => 'Internal Server Error',
            ], 500);
        }
    }

    /**
     * Verify DOKU signature
     * 
     * @param Request $request
     * @return bool
     */
    protected function verifySignature(Request $request): bool
    {
        // Get signature from header
        $signature = $request->header('X-SIGNATURE');
        $timestamp = $request->header('X-TIMESTAMP');
        $clientId = $request->header('X-CLIENT-KEY');

        if (!$signature || !$timestamp || !$clientId) {
            Log::warning('DOKU Webhook: Missing signature headers');
            return false;
        }

        // Get DOKU config
        $config = PaymentGatewayConfig::getActive('doku');
        if (!$config) {
            Log::error('DOKU Webhook: No active DOKU configuration');
            return false;
        }

        // Verify client ID matches
        if ($clientId !== $config->client_id) {
            Log::error('DOKU Webhook: Client ID mismatch', [
                'expected' => $config->client_id,
                'received' => $clientId,
            ]);
            return false;
        }

        // Get request body
        $body = $request->getContent();

        // Build string to sign
        // Format: HTTP_METHOD:RELATIVE_PATH:ACCESS_TOKEN:REQUEST_BODY:TIMESTAMP
        $method = $request->method();
        $path = $request->path();
        $stringToSign = "{$method}:{$path}::{$body}:{$timestamp}";

        Log::info('DOKU Signature Verification', [
            'string_to_sign' => $stringToSign,
            'received_signature' => $signature,
        ]);

        // Verify signature using DOKU public key
        try {
            $dokuPublicKey = $config->doku_public_key;
            
            // Remove header/footer if present
            $dokuPublicKey = str_replace(['-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----', "\n", "\r"], '', $dokuPublicKey);
            $dokuPublicKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($dokuPublicKey, 64, "\n") . "-----END PUBLIC KEY-----";

            // Decode signature from base64
            $signatureDecoded = base64_decode($signature);

            // Verify signature
            $publicKeyResource = openssl_pkey_get_public($dokuPublicKey);
            if (!$publicKeyResource) {
                Log::error('DOKU Webhook: Invalid public key format');
                return false;
            }

            $verified = openssl_verify(
                $stringToSign,
                $signatureDecoded,
                $publicKeyResource,
                OPENSSL_ALGO_SHA256
            );

            openssl_free_key($publicKeyResource);

            if ($verified === 1) {
                Log::info('DOKU Webhook: Signature verified successfully');
                return true;
            } else {
                Log::error('DOKU Webhook: Signature verification failed', [
                    'openssl_result' => $verified,
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('DOKU Webhook: Signature verification exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Process payment notification
     * 
     * @param Request $request
     * @return bool
     */
    protected function processPayment(Request $request): bool
    {
        $data = $request->all();

        // Get virtual account number
        $virtualAccountNo = $data['virtualAccountNo'] ?? null;
        $trxId = $data['trxId'] ?? null;

        if (!$virtualAccountNo && !$trxId) {
            Log::error('DOKU Webhook: Missing virtualAccountNo or trxId');
            return false;
        }

        // Find VA record
        $va = DokuVirtualAccount::where(function ($query) use ($virtualAccountNo, $trxId) {
            if ($virtualAccountNo) {
                $query->where('virtual_account_no', $virtualAccountNo);
            }
            if ($trxId) {
                $query->orWhere('trx_id', $trxId);
            }
        })->first();

        if (!$va) {
            Log::error('DOKU Webhook: Virtual Account not found', [
                'virtual_account_no' => $virtualAccountNo,
                'trx_id' => $trxId,
            ]);
            return false;
        }

        // Check if already paid
        if ($va->isPaid()) {
            Log::info('DOKU Webhook: Payment already processed', [
                'va_id' => $va->id,
            ]);
            return true;
        }

        // Update VA status
        DB::beginTransaction();
        try {
            // Mark as paid
            $va->markAsPaid();

            // Update with DOKU response
            $va->update([
                'doku_response' => array_merge($va->doku_response ?? [], [
                    'payment_notification' => $data,
                ]),
            ]);

            // Trigger action based on payment type
            $this->triggerPaymentAction($va);

            DB::commit();

            Log::info('DOKU Webhook: Payment processed successfully', [
                'va_id' => $va->id,
                'payment_type' => $va->payment_type,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DOKU Webhook: Payment processing failed', [
                'va_id' => $va->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Trigger action based on payment type
     * 
     * @param DokuVirtualAccount $va
     * @return void
     */
    protected function triggerPaymentAction(DokuVirtualAccount $va): void
    {
        switch ($va->payment_type) {
            case 'subscription':
                $this->triggerSubscriptionActivation($va);
                break;

            case 'gift':
                $this->triggerGiftActivation($va);
                break;

            case 'gallery':
                $this->triggerGalleryActivation($va);
                break;

            case 'music_upload':
                $this->triggerMusicActivation($va);
                break;

            default:
                Log::warning('DOKU Webhook: Unknown payment type', [
                    'payment_type' => $va->payment_type,
                ]);
        }
    }

    /**
     * Activate subscription when paid
     * 
     * @param DokuVirtualAccount $va
     * @return void
     */
    protected function triggerSubscriptionActivation(DokuVirtualAccount $va): void
    {
        // reference_id should be pricing_plan_id
        $pricingPlanId = $va->reference_id;
        
        if (!$pricingPlanId) {
            Log::error('DOKU Webhook: Missing pricing_plan_id for subscription', [
                'va_id' => $va->id,
            ]);
            return;
        }

        $pricingPlan = PricingPlan::find($pricingPlanId);
        if (!$pricingPlan) {
            Log::error('DOKU Webhook: Pricing plan not found', [
                'pricing_plan_id' => $pricingPlanId,
            ]);
            return;
        }

        $user = $va->user;

        // Create or update subscription
        $subscription = UserSubscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'pricing_plan_id' => $pricingPlan->id,
                'start_date' => now(),
                'end_date' => now()->addDays($pricingPlan->duration_days),
                'is_active' => true,
            ]
        );

        Log::info('DOKU Webhook: Subscription activated', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'pricing_plan' => $pricingPlan->name,
        ]);
    }

    /**
     * Activate gift feature when paid
     * 
     * @param DokuVirtualAccount $va
     * @return void
     */
    protected function triggerGiftActivation(DokuVirtualAccount $va): void
    {
        // reference_id should be invitation_id
        $invitationId = $va->reference_id;
        
        if (!$invitationId) {
            Log::error('DOKU Webhook: Missing invitation_id for gift', [
                'va_id' => $va->id,
            ]);
            return;
        }

        $invitation = \App\Models\Invitation::find($invitationId);
        if (!$invitation) {
            Log::error('DOKU Webhook: Invitation not found', [
                'invitation_id' => $invitationId,
            ]);
            return;
        }

        // Activate gift feature
        $invitation->update([
            'gift_enabled' => true,
        ]);

        Log::info('DOKU Webhook: Gift feature activated', [
            'invitation_id' => $invitation->id,
        ]);
    }

    /**
     * Add gallery slots when paid
     * 
     * @param DokuVirtualAccount $va
     * @return void
     */
    protected function triggerGalleryActivation(DokuVirtualAccount $va): void
    {
        // reference_id should be gallery_order_id
        $galleryOrderId = $va->reference_id;
        
        if (!$galleryOrderId) {
            Log::error('DOKU Webhook: Missing gallery_order_id', [
                'va_id' => $va->id,
            ]);
            return;
        }

        $galleryOrder = GalleryOrder::find($galleryOrderId);
        if (!$galleryOrder) {
            Log::error('DOKU Webhook: Gallery order not found', [
                'gallery_order_id' => $galleryOrderId,
            ]);
            return;
        }

        // Mark order as paid
        $galleryOrder->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Add gallery slots to invitation
        $invitation = $galleryOrder->invitation;
        $invitation->increment('gallery_limit', $galleryOrder->quantity);

        Log::info('DOKU Webhook: Gallery slots added', [
            'invitation_id' => $invitation->id,
            'quantity' => $galleryOrder->quantity,
        ]);
    }

    /**
     * Activate music upload when paid
     * 
     * @param DokuVirtualAccount $va
     * @return void
     */
    protected function triggerMusicActivation(DokuVirtualAccount $va): void
    {
        // reference_id should be music_id
        $musicId = $va->reference_id;
        
        if (!$musicId) {
            Log::error('DOKU Webhook: Missing music_id', [
                'va_id' => $va->id,
            ]);
            return;
        }

        $music = \App\Models\Music::find($musicId);
        if (!$music) {
            Log::error('DOKU Webhook: Music not found', [
                'music_id' => $musicId,
            ]);
            return;
        }

        // Activate music
        $music->update([
            'is_active' => true,
            'is_public' => true,
        ]);

        Log::info('DOKU Webhook: Music activated', [
            'music_id' => $music->id,
        ]);
    }
}
