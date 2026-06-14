<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'registration_code',
    'event_id',
    'team_name',
    'institution',
    'contact_name',
    'contact_email',
    'contact_phone',
    'status',
    'payment_status',
])]
class Registration extends Model
{
    /**
     * Get the event for this registration.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the participants for this registration.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get the payment for this registration.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the coach information for this registration.
     */
    public function coach(): HasOne
    {
        return $this->hasOne(RegistrationCoach::class);
    }
}
