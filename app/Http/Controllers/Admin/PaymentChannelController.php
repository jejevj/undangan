<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use App\Services\PaymentChannelAvailabilityService;
use Illuminate\Http\Request;

class PaymentChannelController extends Controller
{
    protected $availabilityService;

    public function __construct(PaymentChannelAvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Display payment channels
     */
    public function index()
    {
        $channels = PaymentChannel::orderBy('type')->orderBy('sort_order')->get();
        
        return view('admin.payment-channels.index', compact('channels'));
    }

    /**
     * Toggle channel active status
     */
    public function toggleActive(PaymentChannel $channel)
    {
        $channel->update([
            'is_active' => !$channel->is_active,
        ]);

        return redirect()->route('admin.payment-channels.index')
            ->with('success', "Channel {$channel->name} " . ($channel->is_active ? 'activated' : 'deactivated'));
    }

    /**
     * Check channel availability
     */
    public function checkAvailability(PaymentChannel $channel)
    {
        $available = $this->availabilityService->checkChannel($channel->code);

        $message = $available 
            ? "Channel {$channel->name} is available"
            : "Channel {$channel->name} is not available. Check logs for details.";

        return redirect()->route('admin.payment-channels.index')
            ->with($available ? 'success' : 'warning', $message);
    }

    /**
     * Check all channels availability
     */
    public function checkAllAvailability()
    {
        $results = $this->availabilityService->checkAllChannels();

        $totalVA = count($results['virtual_account']);
        $availableVA = count(array_filter($results['virtual_account']));
        
        $totalEW = count($results['ewallet']);
        $availableEW = count(array_filter($results['ewallet']));

        $message = "Availability check completed. VA: {$availableVA}/{$totalVA} available, E-Wallet: {$availableEW}/{$totalEW} available";

        return redirect()->route('admin.payment-channels.index')
            ->with('info', $message);
    }

    /**
     * Update channel configuration
     */
    public function update(Request $request, PaymentChannel $channel)
    {
        $rules = [
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ];

        // Add validation for VA channels
        if ($channel->type === 'virtual_account') {
            $rules['partner_service_id'] = 'required|string|max:20';
            $rules['bin_notes'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // For VA channels, also update bin field to match partner_service_id
        if ($channel->type === 'virtual_account' && isset($validated['partner_service_id'])) {
            $validated['bin'] = $validated['partner_service_id'];
        }

        $channel->update($validated);

        return redirect()->route('admin.payment-channels.index')
            ->with('success', "Configuration for {$channel->name} updated successfully");
    }

    /**
     * Update channel BIN
     */
    public function updateBin(Request $request, PaymentChannel $channel)
    {
        $validated = $request->validate([
            'bin' => 'required|string|max:20',
            'bin_notes' => 'nullable|string',
        ]);

        $channel->update($validated);

        return redirect()->route('admin.payment-channels.index')
            ->with('success', "BIN for {$channel->name} updated successfully");
    }
}
