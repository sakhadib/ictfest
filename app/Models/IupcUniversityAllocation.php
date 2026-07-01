<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'university_id',
    'name',
    'slot_count',
    'is_active',
])]
class IupcUniversityAllocation extends Model
{
    protected function casts(): array
    {
        return [
            'slot_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(IupcUniversityAlias::class);
    }

    public function coaches(): HasMany
    {
        return $this->hasMany(IupcCoachContact::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(IupcCoachLink::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(IupcCoachActivityLog::class);
    }
}
