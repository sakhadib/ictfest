<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id',
    'full_name',
    'email',
    'phone',
    'student_id',
    'university',
    'is_leader',
])]
class Participant extends Model
{
    public $timestamps = false;

    /**
     * Get the registration for this participant.
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
            'is_leader' => 'boolean',
        ];
    }
}
