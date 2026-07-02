<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id',
    'trx_id',
    'status',
    'payment_package',
    'payment_amount',
])]
class FinalRegistration extends Model
{
    public const STATUS_INVITED = 'invited';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * Get the registration for this final registration submission.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
