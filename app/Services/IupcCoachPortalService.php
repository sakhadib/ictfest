<?php

namespace App\Services;

use App\Models\Event;
use App\Models\FinalRegistration;
use App\Models\IupcUniversityAllocation;
use App\Models\Registration;

class IupcCoachPortalService
{
    public const EVENT_CODE = '01';

    public const WITH_COACH_KIT = 'with_coach_kit';

    public const WITHOUT_COACH_KIT = 'without_coach_kit';

    public const PACKAGE_AMOUNTS = [
        self::WITH_COACH_KIT => 5099,
        self::WITHOUT_COACH_KIT => 4099,
    ];

    public const TSHIRT_SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

    public function iupcEvent(): Event
    {
        return Event::query()->where('code', self::EVENT_CODE)->firstOrFail();
    }

    public function registrationsQuery(IupcUniversityAllocation $allocation)
    {
        $eventId = $this->iupcEvent()->id;
        $rawNames = $allocation->aliases()->pluck('raw_name')->all();

        return Registration::query()
            ->where('event_id', $eventId)
            ->where('status', '!=', 'rejected')
            ->whereIn('institution', $rawNames === [] ? ['__none__'] : $rawNames);
    }

    public function submittedCount(IupcUniversityAllocation $allocation): int
    {
        return (clone $this->registrationsQuery($allocation))
            ->whereHas('finalRegistration', fn ($query) => $query->whereIn('status', [
                FinalRegistration::STATUS_SUBMITTED,
                FinalRegistration::STATUS_APPROVED,
            ]))
            ->count();
    }

    public function remainingSlots(IupcUniversityAllocation $allocation): int
    {
        return max(0, $allocation->slot_count - $this->submittedCount($allocation));
    }

    public function packageLabel(?string $package): string
    {
        return match ($package) {
            self::WITH_COACH_KIT => 'With coach kit',
            self::WITHOUT_COACH_KIT => 'Without coach kit',
            default => 'Not selected',
        };
    }
}
