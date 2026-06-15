<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\View\View;

abstract class Controller
{
    protected function registrationEvent(string $code): Event
    {
        return Event::where('code', $code)->firstOrFail();
    }

    protected function registrationComingSoon(Event $event): View
    {
        return view('registrations.coming-soon', compact('event'));
    }

    protected function registrationSlotsFull(Event $event): View
    {
        return view('registrations.coming-soon', [
            'event' => $event,
            'title' => 'Registration slots are full.',
            'message' => $event->name.' registration is currently closed because all available slots are occupied.',
            'status' => 'Slots full',
        ]);
    }

    protected function ensureRegistrationIsLive(Event $event): void
    {
        abort_unless($event->is_live, 403, 'Registration is not live yet.');
    }

    protected function ensureRegistrationHasAvailableSlots(Event $event): void
    {
        abort_if($event->hasSlotLimit() && ! $event->hasAvailableSlots(), 403, 'Registration slots are full.');
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function universitySearchOptions(): Collection
    {
        try {
            return University::query()
                ->select([
                    'university_name',
                    'acronym',
                    'estd',
                    'type',
                    'location',
                    'specialization',
                    'website',
                ])
                ->orderBy('university_name')
                ->get()
                ->map(fn (University $university): array => [
                    'university_name' => $university->university_name,
                    'acronym' => $university->acronym,
                    'estd' => $university->estd,
                    'type' => $university->type,
                    'location' => $university->location,
                    'specialization' => $university->specialization,
                    'website' => $university->website,
                ]);
        } catch (\Throwable) {
            return collect();
        }
    }
}
