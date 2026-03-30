<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\PricingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('pricingPlan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $plans = PricingPlan::orderBy('price')->get();
        return view('admin.campaigns.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:campaigns,code|alpha_dash',
            'description' => 'nullable|string',
            'pricing_plan_id' => 'required|exists:pricing_plans,id',
            'max_users' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Campaign::create($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanye berhasil dibuat!');
    }

    public function edit(Campaign $campaign)
    {
        $plans = PricingPlan::orderBy('price')->get();
        return view('admin.campaigns.edit', compact('campaign', 'plans'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|alpha_dash|unique:campaigns,code,' . $campaign->id,
            'description' => 'nullable|string',
            'pricing_plan_id' => 'required|exists:pricing_plans,id',
            'max_users' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $campaign->update($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanye berhasil diperbarui!');
    }

    public function destroy(Campaign $campaign)
    {
        // Check if campaign has users
        if ($campaign->users()->count() > 0) {
            return redirect()->route('admin.campaigns.index')
                ->with('error', 'Kampanye tidak dapat dihapus karena sudah digunakan oleh ' . $campaign->users()->count() . ' user.');
        }

        $campaign->delete();

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanye berhasil dihapus!');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['pricingPlan', 'users' => function($query) {
            $query->latest()->take(50);
        }]);

        return view('admin.campaigns.show', compact('campaign'));
    }

    public function toggleStatus(Campaign $campaign)
    {
        $campaign->update(['is_active' => !$campaign->is_active]);

        $status = $campaign->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', "Kampanye berhasil {$status}!");
    }
}
