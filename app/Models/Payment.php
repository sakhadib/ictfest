<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id',
    'amount',
    'method',
    'trx_id',
    'screenshot_path',
    'status',
    'submitted_at',
    'verified_at',
])]
class Payment extends Model
{
    public $timestamps = false;

    /**
     * Get the registration for this payment.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }
}
