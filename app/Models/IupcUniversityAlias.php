<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'iupc_university_allocation_id',
    'raw_name',
    'normalized_name',
    'source_count',
])]
class IupcUniversityAlias extends Model
{
    protected function casts(): array
    {
        return [
            'source_count' => 'integer',
        ];
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(IupcUniversityAllocation::class, 'iupc_university_allocation_id');
    }
}
