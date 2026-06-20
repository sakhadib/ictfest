<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class RegistrationSummaryService
{
    public function telegramText(): string
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $totalRegistrations = Registration::count();
        $totalParticipants = Participant::count();
        $todayRegistrations = Registration::whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $todayParticipants = Participant::whereHas('registration', function ($query) use ($todayStart, $todayEnd): void {
            $query->whereBetween('created_at', [$todayStart, $todayEnd]);
        })->count();

        $eventRows = Event::query()
            ->leftJoin('registrations', 'registrations.event_id', '=', 'events.id')
            ->leftJoin('participants', 'participants.registration_id', '=', 'registrations.id')
            ->select([
                'events.code',
                'events.name',
                DB::raw('COUNT(DISTINCT registrations.id) as registrations_count'),
                DB::raw('COUNT(participants.id) as participants_count'),
            ])
            ->groupBy('events.id', 'events.code', 'events.name')
            ->orderBy('events.code')
            ->get();

        $lines = [
            config('app.name').' Registration Summary',
            now()->format('d M Y, h:i A'),
            '',
            'Total',
            'Registrations: '.number_format($totalRegistrations),
            'Participants: '.number_format($totalParticipants),
            '',
            'Today',
            'Registrations: '.number_format($todayRegistrations),
            'Participants: '.number_format($todayParticipants),
            '',
            'By Event',
        ];

        foreach ($eventRows as $event) {
            $lines[] = sprintf(
                '%s - %s: %s registrations, %s participants',
                $event->code,
                $event->name,
                number_format((int) $event->registrations_count),
                number_format((int) $event->participants_count),
            );
        }

        return implode("\n", $lines);
    }
}
