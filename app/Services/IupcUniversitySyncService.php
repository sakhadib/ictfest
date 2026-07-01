<?php

namespace App\Services;

use App\Models\Event;
use App\Models\IupcCoachContact;
use App\Models\IupcUniversityAlias;
use App\Models\IupcUniversityAllocation;
use App\Models\Registration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class IupcUniversitySyncService
{
    public const EVENT_CODE = '01';

    public function sync(): void
    {
        $event = Event::query()->where('code', self::EVENT_CODE)->first();

        if (! $event) {
            return;
        }

        DB::transaction(function () use ($event): void {
            $this->syncAliases($event);
            $this->syncCoaches($event);
        });
    }

    public static function normalizeUniversity(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));
        $normalized = preg_replace('/[^\pL\pN]+/u', ' ', $normalized) ?? '';

        return trim(preg_replace('/\s+/u', ' ', $normalized) ?? '');
    }

    public static function normalizeEmail(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    /**
     * @return Collection<int, string>
     */
    public function aliasNormalizedNames(IupcUniversityAllocation $allocation): Collection
    {
        return $allocation->aliases()->pluck('normalized_name');
    }

    private function syncAliases(Event $event): void
    {
        $rows = Registration::query()
            ->where('event_id', $event->id)
            ->select('institution', DB::raw('count(*) as aggregate'))
            ->whereNotNull('institution')
            ->groupBy('institution')
            ->get();

        foreach ($rows as $row) {
            $rawName = trim((string) $row->institution);
            $normalizedName = self::normalizeUniversity($rawName);

            if ($normalizedName === '') {
                continue;
            }

            $alias = IupcUniversityAlias::query()->where('normalized_name', $normalizedName)->first();

            if ($alias) {
                $alias->update([
                    'raw_name' => $alias->raw_name ?: $rawName,
                    'source_count' => (int) $row->aggregate,
                ]);

                continue;
            }

            $allocation = IupcUniversityAllocation::query()->create([
                'name' => $rawName,
                'slot_count' => 0,
                'is_active' => true,
            ]);

            $allocation->aliases()->create([
                'raw_name' => $rawName,
                'normalized_name' => $normalizedName,
                'source_count' => (int) $row->aggregate,
            ]);
        }
    }

    private function syncCoaches(Event $event): void
    {
        $registrations = Registration::query()
            ->with('coach')
            ->where('event_id', $event->id)
            ->whereHas('coach')
            ->get();

        foreach ($registrations as $registration) {
            $coach = $registration->coach;
            $normalizedInstitution = self::normalizeUniversity($registration->institution);
            $normalizedEmail = self::normalizeEmail($coach?->official_email);

            if (! $coach || $normalizedInstitution === '' || $normalizedEmail === '') {
                continue;
            }

            $alias = IupcUniversityAlias::query()
                ->with('allocation')
                ->where('normalized_name', $normalizedInstitution)
                ->first();

            if (! $alias?->allocation) {
                continue;
            }

            IupcCoachContact::query()->updateOrCreate(
                [
                    'iupc_university_allocation_id' => $alias->allocation->id,
                    'normalized_email' => $normalizedEmail,
                ],
                [
                    'registration_coach_id' => $coach->id,
                    'name' => $coach->name,
                    'designation' => $coach->designation,
                    'official_email' => $coach->official_email,
                    'contact_number' => $coach->contact_number,
                    'is_active' => true,
                ],
            );
        }
    }
}
