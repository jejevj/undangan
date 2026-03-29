<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\PricingPlan;
use App\Models\TemplateCategory;
use App\Models\Partner;
use App\Services\EventTrackingService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Track landing page view (for anonymous users too)
        EventTrackingService::pageView('Landing Page');
        
        $categories = TemplateCategory::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // Load initial templates (all, limit 12)
        $templates = Template::with('category')
            ->where('is_active', true)
            ->limit(12)
            ->get();
            
        // Load all active plans (public and business)
        $plans = PricingPlan::where('is_active', true)->orderBy('price')->get();
        
        // Load active partners ordered by display order
        $partners = Partner::active()->ordered()->get();
        
        return view('landing.index', compact('categories', 'templates', 'plans', 'partners'));
    }
    
    public function getTemplates(Request $request)
    {
        $categoryFilter = $request->get('category', 'all');
        $typeFilter = $request->get('type', 'all');
        
        // Track template browsing
        EventTrackingService::track('browse_templates', 'landing', 'Browse Templates', [
            'category' => $categoryFilter,
            'type' => $typeFilter,
        ]);
        
        // Build query for templates
        $query = Template::with('category')->where('is_active', true);
        
        // Apply category filter
        if ($categoryFilter !== 'all') {
            $category = TemplateCategory::where('slug', $categoryFilter)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        // Apply type filter
        if ($typeFilter === 'free') {
            $query->where(function($q) {
                $q->where('type', 'free')->orWhere('price', 0);
            });
        } elseif ($typeFilter === 'premium') {
            $query->where('type', 'premium')->where('price', '>', 0);
        }
        
        // Limit to 12 templates
        $templates = $query->limit(12)->get();
        
        // Return HTML partial
        return view('landing.partials.template-grid', compact('templates'))->render();
    }
}
