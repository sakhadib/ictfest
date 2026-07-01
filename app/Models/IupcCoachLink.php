<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'iupc_university_allocation_id',
    'iupc_coach_contact_id',
    'token_hash',
    'token_encrypted',
    'last_sent_at',
    'last_email_sent_at',
    'last_sms_sent_at',
    'disabled_at',
    'disabled_by',
])]
class IupcCoachLink extends Model
{
    protected function casts(): array
    {
        return [
            'last_sent_at' => 'datetime',
            'last_email_sent_at' => 'datetime',
            'last_sms_sent_at' => 'datetime',
            'disabled_at' => 'datetime',
        ];
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(IupcUniversityAllocation::class, 'iupc_university_allocation_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(IupcCoachContact::class, 'iupc_coach_contact_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(IupcCoachActivityLog::class);
    }

    public function isActive(): bool
    {
        return $this->disabled_at === null;
    }
}
