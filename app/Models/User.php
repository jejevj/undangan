<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'phone', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function musicLibrary(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Music::class, 'music_user')
                    ->withPivot('granted_at')
                    ->withTimestamps();
    }

    public function musicOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MusicOrder::class);
    }

    /** Cek apakah user punya akses ke lagu tertentu */
    public function hasAccessToMusic(Music $music): bool
    {
        if ($music->isFree()) return true;
        return $this->musicLibrary()->where('music_id', $music->id)->exists();
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function invitations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invitation::class);
    }
    /** Subscription aktif saat ini */
    public function activeSubscription(): ?UserSubscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->latest('starts_at')
            ->first();
    }

    /** Plan aktif user (fallback ke free jika tidak ada) */
    public function activePlan(): PricingPlan
    {
        $sub = $this->activeSubscription();
        if ($sub) return $sub->plan;

        return PricingPlan::where('slug', 'free')->first()
            ?? PricingPlan::orderBy('price')->first();
    }

    /** Apakah admin (bypass semua limit) */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /** Jumlah undangan yang sudah dibuat */
    public function invitationCount(): int
    {
        return $this->invitations()->count();
    }

    /** Apakah masih bisa buat undangan baru */
    public function canCreateInvitation(): bool
    {
        if ($this->isAdmin()) return true;
        $plan = $this->activePlan();
        return $this->invitationCount() < $plan->max_invitations;
    }

    /** Sisa slot undangan */
    public function remainingInvitations(): int|null
    {
        if ($this->isAdmin()) return null;
        $plan = $this->activePlan();
        return max(0, $plan->max_invitations - $this->invitationCount());
    }

    /** Jumlah undangan dengan template premium yang sudah dibuat */
    public function premiumInvitationCount(): int
    {
        return $this->invitations()
            ->whereHas('template', function($q) {
                $q->where('type', 'premium');
            })
            ->count();
    }

    /** Apakah masih bisa menggunakan template premium */
    public function canUsePremiumTemplate(): bool
    {
        if ($this->isAdmin()) return true;
        
        $plan = $this->activePlan();
        
        // Jika max_premium_templates = 0, tidak bisa akses premium
        if ($plan->max_premium_templates === 0) return false;
        
        // Jika null, unlimited
        if ($plan->max_premium_templates === null) return true;
        
        // Cek apakah masih ada slot
        return $this->premiumInvitationCount() < $plan->max_premium_templates;
    }

    /** Sisa slot template premium */
    public function remainingPremiumTemplates(): int|null
    {
        if ($this->isAdmin()) return null;
        
        $plan = $this->activePlan();
        
        // Jika 0, tidak bisa akses
        if ($plan->max_premium_templates === 0) return 0;
        
        // Jika null, unlimited
        if ($plan->max_premium_templates === null) return null;
        
        return max(0, $plan->max_premium_templates - $this->premiumInvitationCount());
    }
}
