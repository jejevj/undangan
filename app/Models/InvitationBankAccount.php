<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationBankAccount extends Model
{
    protected $fillable = [
        'invitation_id', 'bank_name', 'account_number', 'account_name', 'logo', 'order',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /** Warna gradient berdasarkan nama bank */
    public function bankColor(): string
    {
        return match (strtolower($this->bank_name)) {
            'bca'           => 'linear-gradient(135deg, #005baa, #0078d4)',
            'mandiri'       => 'linear-gradient(135deg, #003d82, #f5a623)',
            'bni'           => 'linear-gradient(135deg, #f77f00, #d62828)',
            'bri'           => 'linear-gradient(135deg, #003087, #0066cc)',
            'bsi'           => 'linear-gradient(135deg, #00843d, #005a2b)',
            'cimb niaga'    => 'linear-gradient(135deg, #c8102e, #8b0000)',
            'danamon'       => 'linear-gradient(135deg, #e31837, #a50021)',
            'permata'       => 'linear-gradient(135deg, #00a651, #007a3d)',
            'gopay'         => 'linear-gradient(135deg, #00aed6, #0082a0)',
            'ovo'           => 'linear-gradient(135deg, #4c3494, #2d1f5e)',
            'dana'          => 'linear-gradient(135deg, #118eea, #0066cc)',
            'shopeepay'     => 'linear-gradient(135deg, #ee4d2d, #c0392b)',
            default         => 'linear-gradient(135deg, #4a4a4a, #2d2d2d)',
        };
    }

    /** Inisial bank untuk fallback logo */
    public function bankInitial(): string
    {
        $words = explode(' ', strtoupper($this->bank_name));
        return count($words) >= 2
            ? $words[0][0] . $words[1][0]
            : substr($words[0], 0, 3);
    }
}
