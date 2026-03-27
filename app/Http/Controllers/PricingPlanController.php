<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PricingPlanController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::withCount('subscriptions')
            ->orderBy('price')
            ->get();

        return view('pricing-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('pricing-plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'slug'                   => 'nullable|string|unique:pricing_plans,slug',
            'price'                  => 'required|integer|min:0',
            'badge_color'            => 'required|string|max:50',
            'max_invitations'        => 'required|integer|min:1',
            'max_gallery_photos'     => 'required|integer|min:0',
            'max_music_uploads'      => 'required|integer|min:0',
            'gift_section_included'  => 'boolean',
            'can_delete_music'       => 'boolean',
            'is_popular'             => 'boolean',
            'is_active'              => 'boolean',
            'features'               => 'nullable|array',
            'features.*'             => 'string',
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ?: Str::slug($request->name);
        $data['gift_section_included'] = $request->boolean('gift_section_included');
        $data['can_delete_music'] = $request->boolean('can_delete_music');
        $data['is_popular'] = $request->boolean('is_popular');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['features'] = array_filter($request->features ?? []);

        PricingPlan::create($data);

        return redirect()->route('pricing-plans.index')
            ->with('success', 'Paket pricing berhasil dibuat.');
    }

    public function edit(PricingPlan $pricingPlan)
    {
        return view('pricing-plans.edit', compact('pricingPlan'));
    }

    public function update(Request $request, PricingPlan $pricingPlan)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'slug'                   => 'nullable|string|unique:pricing_plans,slug,' . $pricingPlan->id,
            'price'                  => 'required|integer|min:0',
            'badge_color'            => 'required|string|max:50',
            'max_invitations'        => 'required|integer|min:1',
            'max_gallery_photos'     => 'required|integer|min:0',
            'max_music_uploads'      => 'required|integer|min:0',
            'gift_section_included'  => 'boolean',
            'can_delete_music'       => 'boolean',
            'is_popular'             => 'boolean',
            'is_active'              => 'boolean',
            'features'               => 'nullable|array',
            'features.*'             => 'string',
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ?: Str::slug($request->name);
        $data['gift_section_included'] = $request->boolean('gift_section_included');
        $data['can_delete_music'] = $request->boolean('can_delete_music');
        $data['is_popular'] = $request->boolean('is_popular');
        $data['is_active'] = $request->boolean('is_active');
        $data['features'] = array_filter($request->features ?? []);

        $pricingPlan->update($data);

        return redirect()->route('pricing-plans.index')
            ->with('success', 'Paket pricing berhasil diupdate.');
    }

    public function destroy(PricingPlan $pricingPlan)
    {
        // Cegah hapus jika ada subscription aktif
        if ($pricingPlan->subscriptions()->where('status', 'active')->exists()) {
            return redirect()->route('pricing-plans.index')
                ->with('error', 'Tidak dapat menghapus paket yang masih memiliki subscription aktif.');
        }

        $pricingPlan->delete();

        return redirect()->route('pricing-plans.index')
            ->with('success', 'Paket pricing berhasil dihapus.');
    }

    public function toggle(PricingPlan $pricingPlan)
    {
        $pricingPlan->update(['is_active' => !$pricingPlan->is_active]);

        $status = $pricingPlan->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('pricing-plans.index')
            ->with('success', "Paket {$pricingPlan->name} berhasil {$status}.");
    }
}
