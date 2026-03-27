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
            'name'                  => 'Free',
            'price'                 => 0,
            'badge_color'           => 'secondary',
            'is_popular'            => false,
            'max_invitations'       => 1,
            'max_gallery_photos'    => 2,
            'max_music_uploads'     => 0,
            'gift_section_included' => false,
            'can_delete_music'      => true,
            'is_active'             => true,
            'features'              => [
                '1 undangan digital',
                '2 foto galeri',
                'Musik dari library gratis',
                'Manajemen tamu (maks 40)',
                'Gift section: berbayar (Rp 10.000)',
            ],
        ]);

        // ── 2. Basic ──────────────────────────────────────────────────
        PricingPlan::updateOrCreate(['slug' => 'basic'], [
            'name'                  => 'Basic',
            'price'                 => 49000,
            'badge_color'           => 'primary',
            'is_popular'            => true,
            'max_invitations'       => 3,
            'max_gallery_photos'    => 50,
            'max_music_uploads'     => 4,
            'gift_section_included' => true,
            'can_delete_music'      => false,  // lagu upload tidak bisa dihapus
            'is_active'             => true,
            'features'              => [
                '3 undangan digital',
                '50 foto galeri (total)',
                'Upload 4 lagu sendiri',
                'Gift section (rekening bank) gratis',
                'Manajemen tamu (maks 40)',
                'Lagu yang diupload tidak bisa dihapus',
            ],
        ]);

        // ── 3. Pro ────────────────────────────────────────────────────
        PricingPlan::updateOrCreate(['slug' => 'pro'], [
            'name'                  => 'Pro',
            'price'                 => 99000,
            'badge_color'           => 'warning',
            'is_popular'            => false,
            'max_invitations'       => 10,
            'max_gallery_photos'    => null,   // unlimited
            'max_music_uploads'     => null,   // unlimited
            'gift_section_included' => true,
            'can_delete_music'      => true,
            'is_active'             => true,
            'features'              => [
                '10 undangan digital',
                'Foto galeri unlimited',
                'Upload lagu unlimited',
                'Gift section gratis',
                'Manajemen tamu unlimited',
                'Semua fitur premium',
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

        $this->command->info('Pricing plans seeded: Free, Basic, Pro');
    }
}
