<?php

namespace App\Services;

use App\Models\PaymentChannel;
use App\Models\PaymentGatewayConfig;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentChannelAvailabilityService
{
    /**
     * Check availability of all channels
     */
    public function checkAllChannels(): array
    {
        $results = [
            'virtual_account' => [],
            'ewallet' => [],
        ];

        // Check VA channels
        $vaChannels = PaymentChannel::ofType('virtual_account')->active()->get();
        foreach ($vaChannels as $channel) {
            $results['virtual_account'][$channel->code] = $this->checkVirtualAccountChannel($channel);
        }

        // Check E-Wallet channels
        $ewalletChannels = PaymentChannel::ofType('ewallet')->active()->get();
        foreach ($ewalletChannels as $channel) {
            $results['ewallet'][$channel->code] = $this->checkEWalletChannel($channel);
        }

        return $results;
    }

    /**
     * Check Virtual Account channel availability
     */
    public function checkVirtualAccountChannel(PaymentChannel $channel): bool
    {
        try {
            // Skip if recently checked
            if (!$channel->needsAvailabilityCheck()) {
                return $channel->is_available;
            }

            Log::info("Checking VA channel availability: {$channel->code}");

            // Get DOKU config
            $config = PaymentGatewayConfig::getActive('doku');
            if (!$config) {
                $channel->markAsUnavailable('DOKU not configured');
                return false;
            }

            // Check if partner_service_id is configured
            if (empty($config->partner_service_id)) {
                $channel->markAsUnavailable('Partner Service ID not configured');
                return false;
            }

            // Try to create a test VA (with very small amount)
            // Note: This will create actual VA in DOKU, so use with caution
            // Alternative: Just check if config is valid
            
            // For now, we'll just check if config exists
            // In production, you might want to do actual API call
            $channel->markAsAvailable();
            
            Log::info("VA channel available: {$channel->code}");
            return true;

        } catch (\Exception $e) {
            Log::error("VA channel check failed: {$channel->code}", [
                'error' => $e->getMessage(),
            ]);
            
            $channel->markAsUnavailable($e->getMessage());
            return false;
        }
    }

    /**
     * Check E-Wallet channel availability
     */
    public function checkEWalletChannel(PaymentChannel $channel): bool
    {
        try {
            // Skip if recently checked
            if (!$channel->needsAvailabilityCheck()) {
                return $channel->is_available;
            }

            Log::info("Checking E-Wallet channel availability: {$channel->code}");

            // Get DOKU config
            $config = PaymentGatewayConfig::getActive('doku');
            if (!$config) {
                $channel->markAsUnavailable('DOKU not configured');
                return false;
            }

            // For now, we'll just check if config exists
            // In production, you might want to do actual API call
            $channel->markAsAvailable();
            
            Log::info("E-Wallet channel available: {$channel->code}");
            return true;

        } catch (\Exception $e) {
            Log::error("E-Wallet channel check failed: {$channel->code}", [
                'error' => $e->getMessage(),
            ]);
            
            $channel->markAsUnavailable($e->getMessage());
            return false;
        }
    }

    /**
     * Check specific channel by code
     */
    public function checkChannel(string $code): bool
    {
        $channel = PaymentChannel::where('code', $code)->first();
        
        if (!$channel) {
            return false;
        }

        if ($channel->type === 'virtual_account') {
            return $this->checkVirtualAccountChannel($channel);
        } else {
            return $this->checkEWalletChannel($channel);
        }
    }

    /**
     * Get available VA channels for dropdown
     */
    public function getAvailableVAChannels(): array
    {
        $channels = PaymentChannel::getVirtualAccountChannels();
        
        $result = [];
        foreach ($channels as $channel) {
            $result[$channel->code] = $channel->name;
        }
        
        return $result;
    }

    /**
     * Get available E-Wallet channels for selection
     */
    public function getAvailableEWalletChannels(): array
    {
        $channels = PaymentChannel::getEWalletChannels();
        
        $result = [];
        foreach ($channels as $channel) {
            $result[$channel->code] = [
                'name' => $channel->name,
                'icon' => $channel->icon,
                'description' => $channel->description,
            ];
        }
        
        return $result;
    }
}
