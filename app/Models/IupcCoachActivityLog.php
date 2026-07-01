<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'iupc_university_allocation_id',
    'iupc_coach_link_id',
    'registration_id',
    'action',
    'summary',
    'before',
    'after',
    'ip_address',
    'user_agent',
])]
class IupcCoachActivityLog extends Model
{
    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
        ];
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(IupcUniversityAllocation::class, 'iupc_university_allocation_id');
    }

    public function coachLink(): BelongsTo
    {
        return $this->belongsTo(IupcCoachLink::class, 'iupc_coach_link_id');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
