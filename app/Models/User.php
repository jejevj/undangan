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

    protected $fillable = ['name', 'email', 'password'];

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
        return $this->hasMany(Invitation::class)->count();
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
}
