<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'university_name',
    'acronym',
    'estd',
    'type',
    'location',
    'specialization',
    'website',
])]
class University extends Model
{
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estd' => 'integer',
        ];
    }
}
