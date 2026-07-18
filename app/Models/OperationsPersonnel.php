<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'student_id',
    'phone',
    'team',
    'status',
    'comments',
])]
class OperationsPersonnel extends Model
{
    public const STATUSES = [
        'volunteer',
        'organizer',
        'staff',
        'faculty',
        'other',
    ];

    protected $table = 'operations_personnel';
}
