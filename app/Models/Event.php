<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'min_team_size', 'max_team_size', 'rulebook_link', 'is_live', 'amount'])]
class Event extends Model
{
    public const FINAL_ROUND_PAID_CODES = ['01', '02', '04'];

    public const INITIAL_PAID_CODES = ['03', '05', '06'];

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_team_size' => 'integer',
            'max_team_size' => 'integer',
            'is_live' => 'boolean',
            'amount' => 'integer',
        ];
    }

    /**
     * Get the registrations for this event.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function isFinalRoundPaidType(): bool
    {
        return in_array($this->code, self::FINAL_ROUND_PAID_CODES, true);
    }

    public function isInitialPaidType(): bool
    {
        return in_array($this->code, self::INITIAL_PAID_CODES, true);
    }
}
