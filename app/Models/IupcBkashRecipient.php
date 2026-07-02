<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'recipient_name',
    'bkash_number',
    'is_enabled',
    'is_current',
    'current_lock',
    'rotation_order',
    'deactivated_at',
    'reactivate_at',
    'last_selected_at',
])]
class IupcBkashRecipient extends Model
{
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_current' => 'boolean',
            'rotation_order' => 'integer',
            'deactivated_at' => 'datetime',
            'reactivate_at' => 'datetime',
            'last_selected_at' => 'datetime',
        ];
    }
}
