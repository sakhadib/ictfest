<?php

namespace App\Services;

use App\Models\Event;
use App\Models\FinalRegistration;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\University;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CompleteRegistrationReportService
{
    private const EVENT_COLUMNS = [
        '01' => 'iupc',
        '02' => 'hackathon',
        '03' => 'datathon',
        '04' => 'gamejam',
        '05' => 'fifa',
        '06' => 'valorant',
    ];

    /**
     * @return array<string, mixed>
     */
    public function build(?string $eventCode = null, bool $includeDetails = true): array
    {
        $events = Event::query()
            ->orderBy('code')
            ->get();

        return [
            'title' => $eventCode
                ? config('app.name').' Complete Registration Report - Event '.$eventCode
                : config('app.name').' Complete Registration Report',
            'generated_at' => now()->format('d M Y, h:i A'),
            'event_code' => $eventCode,
            'totals' => $this->totals($eventCode),
            'events' => $this->eventOverview($eventCode),
            'registration_statuses' => $this->registrationStatusSummary($eventCode),
            'payment_statuses' => $this->paymentStatusSummary($eventCode),
            'payment_record_statuses' => $this->paymentRecordStatusSummary($eventCode),
            'final_statuses' => $this->finalRegistrationStatusSummary($eventCode),
            'ca_rows' => $this->campusAmbassadorSummary($eventCode),
            'university_rows' => $this->universityParticipantMatrix($eventCode),
            'university_directory' => $eventCode ? collect() : $this->universityDirectory(),
            'daily_rows' => $this->dailyRegistrationSummary($eventCode, $events),
            'registrations' => $includeDetails ? $this->registrations($eventCode) : collect(),
        ];
    }

    /**
     * @return Collection<int, Event>
     */
    public function events(): Collection
    {
        return Event::query()->orderBy('code')->get();
    }

    /**
     * @return array<string, int>
     */
    private function totals(?string $eventCode): array
    {
        return [
            'events' => Event::count(),
            'registrations' => $this->registrationScope($eventCode)->count(),
            'participants' => $this->participantScope($eventCode)->count(),
            'payments' => $this->paymentScope($eventCode)->count(),
            'final_registrations' => $this->finalRegistrationScope($eventCode)->count(),
            'university_directory_entries' => University::count(),
        ];
    }

    private function eventOverview(?string $eventCode): Collection
    {
        return Event::query()
            ->leftJoin('registrations', 'registrations.event_id', '=', 'events.id')
            ->leftJoin('participants', 'participants.registration_id', '=', 'registrations.id')
            ->leftJoin('payments', 'payments.registration_id', '=', 'registrations.id')
            ->leftJoin('final_registrations', 'final_registrations.registration_id', '=', 'registrations.id')
            ->when($eventCode, fn ($query) => $query->where('events.code', $eventCode))
            ->select([
                'events.id',
                'events.code',
                'events.name',
                'events.min_team_size',
                'events.max_team_size',
                'events.rulebook_link',
                'events.is_live',
                'events.amount',
                DB::raw('COUNT(DISTINCT registrations.id) as registrations_count'),
                DB::raw('COUNT(DISTINCT participants.id) as participants_count'),
                DB::raw('COUNT(DISTINCT payments.id) as payments_count'),
                DB::raw('COUNT(DISTINCT final_registrations.id) as final_registrations_count'),
            ])
            ->groupBy(
                'events.id',
                'events.code',
                'events.name',
                'events.min_team_size',
                'events.max_team_size',
                'events.rulebook_link',
                'events.is_live',
                'events.amount',
            )
            ->orderBy('events.code')
            ->get();
    }

    private function registrationStatusSummary(?string $eventCode): Collection
    {
        return $this->registrationScope($eventCode)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();
    }

    private function paymentStatusSummary(?string $eventCode): Collection
    {
        return $this->registrationScope($eventCode)
            ->select('payment_status', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_status')
            ->orderBy('payment_status')
            ->get();
    }

    private function paymentRecordStatusSummary(?string $eventCode): Collection
    {
        return $this->paymentScope($eventCode)
            ->select('payments.status', DB::raw('COUNT(*) as total'))
            ->groupBy('payments.status')
            ->orderBy('payments.status')
            ->get();
    }

    private function finalRegistrationStatusSummary(?string $eventCode): Collection
    {
        return $this->finalRegistrationScope($eventCode)
            ->select('final_registrations.status', DB::raw('COUNT(*) as total'))
            ->groupBy('final_registrations.status')
            ->orderBy('final_registrations.status')
            ->get();
    }

    private function campusAmbassadorSummary(?string $eventCode): Collection
    {
        return $this->registrationScope($eventCode)
            ->select([
                DB::raw("COALESCE(NULLIF(TRIM(ca), ''), 'Unspecified') as ca_name"),
                DB::raw('COUNT(*) as registrations_count'),
            ])
            ->groupBy(DB::raw("COALESCE(NULLIF(TRIM(ca), ''), 'Unspecified')"))
            ->orderByDesc('registrations_count')
            ->orderBy('ca_name')
            ->get();
    }

    private function universityParticipantMatrix(?string $eventCode): Collection
    {
        $rows = Participant::query()
            ->join('registrations', 'registrations.id', '=', 'participants.registration_id')
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->select([
                DB::raw("COALESCE(NULLIF(TRIM(participants.university), ''), 'Unspecified') as university_name"),
                'events.code as event_code',
                DB::raw('COUNT(participants.id) as participants_count'),
            ])
            ->whereIn('events.code', array_keys(self::EVENT_COLUMNS))
            ->when($eventCode, fn ($query) => $query->where('events.code', $eventCode))
            ->groupBy(
                DB::raw("COALESCE(NULLIF(TRIM(participants.university), ''), 'Unspecified')"),
                'events.code',
            )
            ->get();

        return $rows
            ->groupBy('university_name')
            ->map(fn (Collection $eventRows, string $university): array => $this->universityRow($university, $eventRows))
            ->sortBy([
                ['total', 'desc'],
                ['university', 'asc'],
            ])
            ->values();
    }

    private function dailyRegistrationSummary(?string $eventCode, Collection $events): Collection
    {
        $rows = Registration::query()
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->select([
                DB::raw('DATE(registrations.created_at) as registration_date'),
                'events.code as event_code',
                DB::raw('COUNT(*) as registrations_count'),
            ])
            ->when($eventCode, fn ($query) => $query->where('events.code', $eventCode))
            ->groupBy(DB::raw('DATE(registrations.created_at)'), 'events.code')
            ->orderBy('registration_date')
            ->orderBy('events.code')
            ->get();

        $running = array_fill_keys($events->pluck('code')->all(), 0);

        return $rows
            ->groupBy('registration_date')
            ->map(function (Collection $dateRows, string $date) use (&$running, $events): array {
                $counts = array_fill_keys($events->pluck('code')->all(), 0);

                foreach ($dateRows as $row) {
                    $counts[$row->event_code] = (int) $row->registrations_count;
                    $running[$row->event_code] += (int) $row->registrations_count;
                }

                return [
                    'date' => $date,
                    'counts' => $counts,
                    'total' => array_sum($counts),
                    'cumulative_total' => array_sum($running),
                ];
            })
            ->values();
    }

    private function universityDirectory(): Collection
    {
        return University::query()
            ->orderBy('university_name')
            ->get();
    }

    private function registrations(?string $eventCode): Collection
    {
        return Registration::query()
            ->with([
                'event',
                'payment',
                'coach',
                'finalRegistration',
                'participants' => fn ($query) => $query->orderByDesc('is_leader')->orderBy('id'),
            ])
            ->when($eventCode, fn ($query) => $query->whereHas('event', fn ($query) => $query->where('code', $eventCode)))
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->select('registrations.*')
            ->orderBy('events.code')
            ->orderBy('registrations.created_at')
            ->get();
    }

    private function universityRow(string $university, Collection $eventRows): array
    {
        $counts = array_fill_keys(array_values(self::EVENT_COLUMNS), 0);

        foreach ($eventRows as $eventRow) {
            $column = self::EVENT_COLUMNS[(string) $eventRow->event_code] ?? null;

            if ($column) {
                $counts[$column] = (int) $eventRow->participants_count;
            }
        }

        return [
            'university' => $university,
            ...$counts,
            'total' => array_sum($counts),
        ];
    }

    private function registrationScope(?string $eventCode)
    {
        return Registration::query()
            ->when($eventCode, fn ($query) => $query->whereHas('event', fn ($query) => $query->where('code', $eventCode)));
    }

    private function participantScope(?string $eventCode)
    {
        return Participant::query()
            ->when($eventCode, fn ($query) => $query->whereHas('registration.event', fn ($query) => $query->where('code', $eventCode)));
    }

    private function paymentScope(?string $eventCode)
    {
        return Payment::query()
            ->when($eventCode, fn ($query) => $query->whereHas('registration.event', fn ($query) => $query->where('code', $eventCode)));
    }

    private function finalRegistrationScope(?string $eventCode)
    {
        return FinalRegistration::query()
            ->when($eventCode, fn ($query) => $query->whereHas('registration.event', fn ($query) => $query->where('code', $eventCode)));
    }
}
