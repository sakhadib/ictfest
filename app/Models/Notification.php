<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'subject',
    'body',
    'mode',
    'event_codes',
    'metadata',
    'recipient_count',
    'status',
    'queued_at',
    'completed_at',
])]
class Notification extends Model
{
    protected function casts(): array
    {
        return [
            'event_codes' => 'array',
            'metadata' => 'array',
            'recipient_count' => 'integer',
            'queued_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
