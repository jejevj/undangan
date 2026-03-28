<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Free ───────────────────────────────────────────────────
        PricingPlan::updateOrCreate(['slug' => 'free'], [
            'name'                    => 'Free',
            'visibility'              => 'public',
            'price'                   => 0,
            'billing_period'          => 'lifetime',
            'badge_color'             => 'secondary',
            'is_popular'              => false,
            'max_invitations'         => 1,
            'max_premium_templates'   => 0,  // Tidak bisa akses premium template
            'max_gallery_photos'      => 2,
            'max_music_uploads'       => 0,
            'gift_section_included'   => false,
            'show_partnership_logo'   => false,
            'can_delete_music'        => true,
            'is_active'               => true,
            'features'                => [
                '1 undangan digital',
                'Template gratis saja',
                '2 foto galeri',
                'Musik dari library gratis',
                'Manajemen tamu (maks 40)',
                'Gift section: berbayar (Rp 10.000)',
            ],
        ]);

        // ── 2. Basic ──────────────────────────────────────────────────
        PricingPlan::updateOrCreate(['slug' => 'basic'], [
            'name'                    => 'Basic',
            'visibility'              => 'public',
            'price'                   => 49000,
            'billing_period'          => 'lifetime',
            'badge_color'             => 'primary',
            'is_popular'              => true,
            'max_invitations'         => 3,
            'max_premium_templates'   => 3,  // Bisa pakai 3 template premium gratis
            'max_gallery_photos'      => 50,
            'max_music_uploads'       => 4,
            'gift_section_included'   => true,
            'show_partnership_logo'   => false,
            'can_delete_music'        => false,  // lagu upload tidak bisa dihapus
            'is_active'               => true,
            'features'                => [
                '3 undangan digital',
                '3 template premium gratis',
                '50 foto galeri (total)',
                'Upload 4 lagu sendiri',
                'Gift section (rekening bank) gratis',
                'Manajemen tamu (maks 40)',
                'Lagu yang diupload tidak bisa dihapus',
            ],
        ]);

        // ── 3. Pro ────────────────────────────────────────────────────
        PricingPlan::updateOrCreate(['slug' => 'pro'], [
            'name'                    => 'Pro',
            'visibility'              => 'public',
            'price'                   => 99000,
            'billing_period'          => 'lifetime',
            'badge_color'             => 'warning',
            'is_popular'              => false,
            'max_invitations'         => 10,
            'max_premium_templates'   => null,   // unlimited premium templates
            'max_gallery_photos'      => null,   // unlimited
            'max_music_uploads'       => null,   // unlimited
            'gift_section_included'   => true,
            'show_partnership_logo'   => false,
            'can_delete_music'        => true,
            'is_active'               => true,
            'features'                => [
                '10 undangan digital',
                'Semua template premium unlimited',
                'Foto galeri unlimited',
                'Upload lagu unlimited',
                'Gift section gratis',
                'Manajemen tamu unlimited',
                'Semua fitur premium',
            ],
        ]);

        // ── 4. Business ───────────────────────────────────────────────
        PricingPlan::updateOrCreate(['slug' => 'business'], [
            'name'                    => 'Business',
            'visibility'              => 'business',
            'price'                   => 499000,
            'billing_period'          => 'monthly',
            'badge_color'             => 'info',
            'is_popular'              => false,
            'max_invitations'         => 999,
            'max_premium_templates'   => null,   // unlimited
            'max_gallery_photos'      => null,   // unlimited
            'max_music_uploads'       => null,   // unlimited
            'gift_section_included'   => true,
            'show_partnership_logo'   => true,   // tampilkan logo partnership
            'can_delete_music'        => true,
            'is_active'               => true,
            'features'                => [
                'Unlimited undangan digital',
                'Semua template premium unlimited',
                'Foto galeri unlimited',
                'Upload lagu unlimited',
                'Gift section gratis',
                'Manajemen tamu unlimited',
                'Logo Partnership di Landing Page',
                'Dedicated Account Manager',
                'Custom Branding',
                'API Access',
                'Priority Support 24/7',
            ],
        ]);

        // ── Assign free subscription ke semua user non-admin ─────────
        $freePlan = PricingPlan::where('slug', 'free')->first();
        User::whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->whereDoesntHave('subscriptions', fn($q) => $q->where('status', 'active'))
            ->each(function (User $user) use ($freePlan) {
                UserSubscription::create([
                    'user_id'         => $user->id,
                    'pricing_plan_id' => $freePlan->id,
                    'order_number'    => UserSubscription::generateOrderNumber(),
                    'amount'          => 0,
                    'status'          => 'active',
                    'payment_method'  => 'free',
                    'starts_at'       => now(),
                    'expires_at'      => null,
                    'paid_at'         => now(),
                ]);
            });

        $this->command->info('Pricing plans seeded: Free, Basic, Pro, Business');
    }
}
