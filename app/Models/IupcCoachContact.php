<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'iupc_university_allocation_id',
    'registration_coach_id',
    'name',
    'designation',
    'official_email',
    'normalized_email',
    'contact_number',
    'is_active',
])]
class IupcCoachContact extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(IupcUniversityAllocation::class, 'iupc_university_allocation_id');
    }

    public function registrationCoach(): BelongsTo
    {
        return $this->belongsTo(RegistrationCoach::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(IupcCoachLink::class);
    }
}
