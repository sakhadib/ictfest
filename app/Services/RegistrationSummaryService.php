<?php

namespace App\Services;

use App\Models\Event;
use App\Models\FinalRegistration;
use App\Models\Participant;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class RegistrationSummaryService
{
    /**
     * @return array<string, string>
     */
    public function commands(): array
    {
        return [
            'help' => 'Show available ICT Fest bot commands.',
            'status' => 'Overall registration summary.',
            'today' => 'Today registration summary grouped by event.',
            'events' => 'Event live/taken-down status and slot availability.',
            'pending' => 'Pending registration review counts grouped by event.',
            'payments' => 'Submitted payment counts waiting for confirmation.',
            'finals' => 'Final registration pipeline counts grouped by event.',
            'ca' => 'Registration counts grouped by Campus Ambassador.',
            'event <code>' => 'Detailed summary for one event, for example: event 03.',
            'trend <code>' => 'Send a cumulative registration trend chart for one event.',
            'trend all' => 'Send cumulative registration trend curves for all events.',
        ];
    }

    public function helpText(): string
    {
        $lines = [
            config('app.name').' Bot Commands',
            '',
        ];

        foreach ($this->commands() as $command => $description) {
            $lines[] = $command.' - '.$description;
        }

        return implode("\n", $lines);
    }

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

    public function todayText(): string
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $rows = $this->eventRows(function ($query) use ($todayStart, $todayEnd): void {
            $query->whereBetween('registrations.created_at', [$todayStart, $todayEnd]);
        });

        $lines = [
            config('app.name').' Today Summary',
            now()->format('d M Y, h:i A'),
            '',
            'By Event',
        ];

        foreach ($rows as $event) {
            $lines[] = $this->eventCountLine($event);
        }

        if ($rows->isEmpty()) {
            $lines[] = 'No registrations today.';
        }

        return implode("\n", $lines);
    }

    public function eventsText(): string
    {
        $events = Event::withCount([
            'registrations as active_registrations_count' => fn ($query) => $query->where('status', '!=', 'rejected'),
        ])->orderBy('code')->get();

        $lines = [
            config('app.name').' Events',
            now()->format('d M Y, h:i A'),
            '',
        ];

        foreach ($events as $event) {
            $slots = $event->hasSlotLimit()
                ? sprintf('%s/%s slots used, %s left', number_format($event->active_registrations_count), number_format((int) $event->slotLimit()), number_format((int) $event->remainingSlots()))
                : 'No slot limit';

            $lines[] = sprintf(
                '%s - %s: %s, %s',
                $event->code,
                $event->name,
                $event->is_live ? 'Live' : 'Taken down',
                $slots,
            );
        }

        return implode("\n", $lines);
    }

    public function pendingText(): string
    {
        $rows = $this->eventRows(fn ($query) => $query->where('registrations.status', 'pending'));

        $lines = [
            config('app.name').' Pending Registrations',
            now()->format('d M Y, h:i A'),
            '',
        ];

        foreach ($rows as $event) {
            $lines[] = $this->eventCountLine($event);
        }

        if ($rows->isEmpty()) {
            $lines[] = 'No pending registrations.';
        }

        return implode("\n", $lines);
    }

    public function paymentsText(): string
    {
        $rows = Event::query()
            ->leftJoin('registrations', 'registrations.event_id', '=', 'events.id')
            ->leftJoin('payments', 'payments.registration_id', '=', 'registrations.id')
            ->where(function ($query): void {
                $query->where('registrations.payment_status', 'submitted')
                    ->orWhere('payments.status', 'submitted');
            })
            ->select([
                'events.code',
                'events.name',
                DB::raw('COUNT(DISTINCT registrations.id) as registrations_count'),
                DB::raw('COALESCE(SUM(payments.amount), 0) as amount_total'),
            ])
            ->groupBy('events.id', 'events.code', 'events.name')
            ->orderBy('events.code')
            ->get();

        $lines = [
            config('app.name').' Submitted Payments',
            now()->format('d M Y, h:i A'),
            '',
        ];

        if ($rows->isEmpty()) {
            $lines[] = 'No submitted payments are waiting.';
        }

        foreach ($rows as $event) {
            $lines[] = sprintf(
                '%s - %s: %s registrations, BDT %s submitted',
                $event->code,
                $event->name,
                number_format((int) $event->registrations_count),
                number_format((int) $event->amount_total),
            );
        }

        return implode("\n", $lines);
    }

    public function finalsText(): string
    {
        $rows = Event::query()
            ->leftJoin('registrations', 'registrations.event_id', '=', 'events.id')
            ->leftJoin('final_registrations', 'final_registrations.registration_id', '=', 'registrations.id')
            ->select([
                'events.code',
                'events.name',
                DB::raw("SUM(CASE WHEN final_registrations.status = 'invited' THEN 1 ELSE 0 END) as invited_count"),
                DB::raw("SUM(CASE WHEN final_registrations.status = 'submitted' THEN 1 ELSE 0 END) as submitted_count"),
                DB::raw("SUM(CASE WHEN final_registrations.status = 'approved' THEN 1 ELSE 0 END) as approved_count"),
                DB::raw("SUM(CASE WHEN final_registrations.status = 'rejected' THEN 1 ELSE 0 END) as rejected_count"),
            ])
            ->groupBy('events.id', 'events.code', 'events.name')
            ->orderBy('events.code')
            ->get();

        $lines = [
            config('app.name').' Final Registration Pipeline',
            now()->format('d M Y, h:i A'),
            '',
        ];

        foreach ($rows as $event) {
            $lines[] = sprintf(
                '%s - %s: invited %s, submitted %s, approved %s, rejected %s',
                $event->code,
                $event->name,
                number_format((int) $event->invited_count),
                number_format((int) $event->submitted_count),
                number_format((int) $event->approved_count),
                number_format((int) $event->rejected_count),
            );
        }

        return implode("\n", $lines);
    }

    public function caText(): string
    {
        $rows = Registration::query()
            ->select([
                DB::raw("COALESCE(NULLIF(TRIM(ca), ''), 'Unassigned') as ca_name"),
                DB::raw('COUNT(*) as registrations_count'),
            ])
            ->groupBy(DB::raw("COALESCE(NULLIF(TRIM(ca), ''), 'Unassigned')"))
            ->orderByDesc('registrations_count')
            ->orderBy('ca_name')
            ->get();

        $lines = [
            config('app.name').' Campus Ambassador Registrations',
            now()->format('d M Y, h:i A'),
            '',
        ];

        if ($rows->isEmpty()) {
            $lines[] = 'No registrations found.';
        }

        foreach ($rows as $row) {
            $lines[] = $row->ca_name.': '.number_format((int) $row->registrations_count).' registrations';
        }

        return implode("\n", $lines);
    }

    public function eventText(string $code): string
    {
        $event = Event::where('code', str_pad($code, 2, '0', STR_PAD_LEFT))->first();

        if (! $event) {
            return 'No event found for code '.$code.'. Try event 01, event 02, etc.';
        }

        $registrationQuery = Registration::where('event_id', $event->id);
        $participantCount = Participant::whereHas('registration', fn ($query) => $query->where('event_id', $event->id))->count();
        $todayRegistrationCount = (clone $registrationQuery)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count();
        $todayParticipantCount = Participant::whereHas('registration', function ($query) use ($event): void {
            $query->where('event_id', $event->id)
                ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
        })->count();

        $lines = [
            $event->code.' - '.$event->name,
            now()->format('d M Y, h:i A'),
            '',
            'Status: '.($event->is_live ? 'Live' : 'Taken down'),
            'Registrations: '.number_format((clone $registrationQuery)->count()),
            'Participants: '.number_format($participantCount),
            'Today Registrations: '.number_format($todayRegistrationCount),
            'Today Participants: '.number_format($todayParticipantCount),
            'Pending Review: '.number_format((clone $registrationQuery)->where('status', 'pending')->count()),
            'Verified/Qualified: '.number_format((clone $registrationQuery)->where('status', 'verified')->count()),
            'Approved/Paid: '.number_format((clone $registrationQuery)->where('status', 'paid')->count()),
            'Rejected: '.number_format((clone $registrationQuery)->where('status', 'rejected')->count()),
            'Payment Submitted: '.number_format((clone $registrationQuery)->where('payment_status', 'submitted')->count()),
            'Payment Confirmed: '.number_format((clone $registrationQuery)->where('payment_status', 'confirmed')->count()),
            'Final Submitted: '.number_format(FinalRegistration::whereHas('registration', fn ($query) => $query->where('event_id', $event->id))->where('status', FinalRegistration::STATUS_SUBMITTED)->count()),
            'Final Approved: '.number_format(FinalRegistration::whereHas('registration', fn ($query) => $query->where('event_id', $event->id))->where('status', FinalRegistration::STATUS_APPROVED)->count()),
        ];

        if ($event->hasSlotLimit()) {
            $lines[] = 'Slots Used: '.number_format($event->occupiedSlots()).' / '.number_format((int) $event->slotLimit());
            $lines[] = 'Slots Left: '.number_format((int) $event->remainingSlots());
        }

        return implode("\n", $lines);
    }

    private function eventRows(?callable $registrationConstraint = null)
    {
        $query = Event::query()
            ->leftJoin('registrations', 'registrations.event_id', '=', 'events.id')
            ->leftJoin('participants', 'participants.registration_id', '=', 'registrations.id');

        if ($registrationConstraint) {
            $registrationConstraint($query);
        }

        return $query
            ->select([
                'events.code',
                'events.name',
                DB::raw('COUNT(DISTINCT registrations.id) as registrations_count'),
                DB::raw('COUNT(participants.id) as participants_count'),
            ])
            ->groupBy('events.id', 'events.code', 'events.name')
            ->orderBy('events.code')
            ->get();
    }

    private function eventCountLine(object $event): string
    {
        return sprintf(
            '%s - %s: %s registrations, %s participants',
            $event->code,
            $event->name,
            number_format((int) $event->registrations_count),
            number_format((int) $event->participants_count),
        );
    }
}
