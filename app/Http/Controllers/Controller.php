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

    protected function ensureRegistrationIsLive(Event $event): void
    {
        abort_unless($event->is_live, 403, 'Registration is not live yet.');
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
