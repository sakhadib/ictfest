<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Collection;

class RegistrationCardService
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->report($this->baseQuery()->get(), 'All Events Registration Cards');
    }

    /**
     * @return array<string, mixed>
     */
    public function event(string $eventCode): array
    {
        $eventCode = str_pad($eventCode, 2, '0', STR_PAD_LEFT);
        $event = Event::where('code', $eventCode)->first();

        if (! $event) {
            return [
                'ok' => false,
                'message' => 'Invalid event code. Use 01, 02, 03, 04, 05, or 06.',
            ];
        }

        return $this->report(
            $this->baseQuery()
                ->whereHas('event', fn ($query) => $query->where('code', $eventCode))
                ->get(),
            $event->code.' - '.$event->name.' Registration Cards',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function team(string $registrationCode): array
    {
        $registration = $this->baseQuery()
            ->where('registration_code', strtoupper($registrationCode))
            ->first();

        if (! $registration) {
            return [
                'ok' => false,
                'message' => 'No registration found for code '.strtoupper($registrationCode).'.',
            ];
        }

        return $this->report(
            collect([$registration]),
            $registration->registration_code.' Registration Card',
        );
    }

    /**
     * @return Collection<int, Event>
     */
    public function eventsWithRegistrations(): Collection
    {
        return Event::query()
            ->whereHas('registrations')
            ->orderBy('code')
            ->get();
    }

    private function baseQuery()
    {
        return Registration::query()
            ->with([
                'event',
                'payment',
                'coach',
                'finalRegistration',
                'participants' => fn ($query) => $query->orderByDesc('is_leader')->orderBy('id'),
            ])
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->select('registrations.*')
            ->orderBy('events.code')
            ->orderBy('registrations.team_name')
            ->orderBy('registrations.registration_code');
    }

    /**
     * @param Collection<int, Registration> $registrations
     * @return array<string, mixed>
     */
    private function report(Collection $registrations, string $title): array
    {
        if ($registrations->isEmpty()) {
            return [
                'ok' => false,
                'message' => 'No registrations found for this registration card request.',
            ];
        }

        return [
            'ok' => true,
            'title' => $title,
            'generated_at' => now()->format('d M Y, h:i A'),
            'assets' => [
                'ictfest_logo' => public_path('assets/logo_black.png'),
                'iutcs_logo' => public_path('assets/iutcs.png'),
                'cse_logo' => public_path('assets/cse.png'),
            ],
            'registrations' => $registrations,
        ];
    }
}
