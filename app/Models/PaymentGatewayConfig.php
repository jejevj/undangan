<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentGatewayConfig extends Model
{
    protected $fillable = [
        'provider',
        'environment',
        'client_id',
        'merchant_id',
        'partner_service_id',
        'secret_key',
        'private_key',
        'public_key',
        'doku_public_key',
        'issuer',
        'auth_code',
        'base_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'secret_key',
        'private_key',
        'auth_code',
    ];

    /**
     * Get decrypted secret key
     */
    public function getDecryptedSecretKeyAttribute()
    {
        try {
            return Crypt::decryptString($this->secret_key);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set encrypted secret key
     */
    public function setSecretKeyAttribute($value)
    {
        if (empty($value)) {
            return;
        }
        $this->attributes['secret_key'] = Crypt::encryptString($value);
    }

    /**
     * Get decrypted private key
     */
    public function getDecryptedPrivateKeyAttribute()
    {
        try {
            return $this->private_key ? Crypt::decryptString($this->private_key) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set encrypted private key
     */
    public function setPrivateKeyAttribute($value)
    {
        if (empty($value)) {
            return;
        }
        $this->attributes['private_key'] = Crypt::encryptString($value);
    }

    /**
     * Get decrypted auth code
     */
    public function getDecryptedAuthCodeAttribute()
    {
        try {
            return $this->auth_code ? Crypt::decryptString($this->auth_code) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set encrypted auth code
     */
    public function setAuthCodeAttribute($value)
    {
        if (empty($value)) {
            return;
        }
        $this->attributes['auth_code'] = Crypt::encryptString($value);
    }

    /**
     * Get active configuration
     */
    public static function getActive($provider = 'doku')
    {
        return static::where('provider', $provider)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if sandbox mode
     */
    public function isSandbox()
    {
        return $this->environment === 'sandbox';
    }

    /**
     * Check if production mode
     */
    public function isProduction()
    {
        return $this->environment === 'production';
    }
}
