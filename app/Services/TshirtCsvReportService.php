<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Collection;
use RuntimeException;

class TshirtCsvReportService
{
    /**
     * @return array{ok: bool, message?: string, event?: Event, rows?: Collection<int, array<string, string|int|null>>, path?: string}
     */
    public function buildForEvent(string $eventCode): array
    {
        $eventCode = str_pad($eventCode, 2, '0', STR_PAD_LEFT);
        $event = Event::query()->where('code', $eventCode)->first();

        if (! $event) {
            return [
                'ok' => false,
                'message' => "No event found for code {$eventCode}.",
            ];
        }

        $rows = $this->rows($event);

        if ($rows->isEmpty()) {
            return [
                'ok' => false,
                'message' => "No paid T-shirt rows found for {$this->eventLabel($event)}.",
            ];
        }

        return [
            'ok' => true,
            'event' => $event,
            'rows' => $rows,
            'path' => $this->writeCsv($event, $rows),
        ];
    }

    /**
     * @return Collection<int, array<string, string|int|null>>
     */
    private function rows(Event $event): Collection
    {
        return Registration::query()
            ->with([
                'payment',
                'finalRegistration',
                'coach',
                'event',
                'participants' => fn ($query) => $query->orderByDesc('is_leader')->orderBy('id'),
            ])
            ->whereBelongsTo($event)
            ->where('payment_status', 'confirmed')
            ->where(function ($query): void {
                $query
                    ->whereHas('payment', fn ($query) => $query->whereNotNull('amount')->where('amount', '>', 0))
                    ->orWhereHas('finalRegistration', fn ($query) => $query->whereNotNull('payment_amount')->where('payment_amount', '>', 0));
            })
            ->orderBy('team_name')
            ->orderBy('id')
            ->get()
            ->flatMap(function (Registration $registration) use ($event): array {
                $amount = $this->paidAmount($registration);

                $rows = $registration->participants
                    ->map(fn ($participant): array => [
                        'registration_code' => $registration->registration_code,
                        'team_name' => $registration->team_name,
                        'person' => $participant->full_name,
                        'person_type' => $participant->is_leader ? 'leader' : 'participant',
                        'participant_institution' => $participant->university,
                        'paid_amount' => $amount,
                        'tshirt_size' => $this->formatSize($participant->tshirt_size),
                    ])
                    ->all();

                if ($registration->coach) {
                    $rows[] = [
                        'registration_code' => $registration->registration_code,
                        'team_name' => $registration->team_name,
                        'person' => $registration->coach->name,
                        'person_type' => 'coach',
                        'participant_institution' => $registration->institution,
                        'paid_amount' => $amount,
                        'tshirt_size' => $this->formatSize($registration->coach->tshirt_size),
                    ];
                }

                return $rows;
            })
            ->values();
    }

    private function writeCsv(Event $event, Collection $rows): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'ictfest-tshirt-');

        if (! $tempPath) {
            throw new RuntimeException('Could not create temporary T-shirt CSV file.');
        }

        $csvPath = $tempPath.'.csv';
        rename($tempPath, $csvPath);

        $handle = fopen($csvPath, 'w');

        if (! $handle) {
            throw new RuntimeException('Could not open temporary T-shirt CSV file.');
        }

        try {
            fputcsv($handle, ['Registration Code', 'Team Name', 'Person', 'Person Type', 'Participant Institution', 'Paid Amount', 'T-shirt Size']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['registration_code'],
                    $row['team_name'],
                    $row['person'],
                    $row['person_type'],
                    $row['participant_institution'],
                    $row['paid_amount'],
                    $row['tshirt_size'],
                ]);
            }
        } finally {
            fclose($handle);
        }

        $safeEvent = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $this->eventLabel($event)) ?? $event->code);
        $namedPath = dirname($csvPath).DIRECTORY_SEPARATOR.'ictfest-tshirts-'.$event->code.'-'.$safeEvent.'-'.now()->format('Ymd-His').'.csv';
        rename($csvPath, $namedPath);

        return $namedPath;
    }

    private function paidAmount(Registration $registration): ?int
    {
        if ($registration->event?->code === '01') {
            return $registration->finalRegistration?->payment_amount ?? $registration->payment?->amount;
        }

        return $registration->payment?->amount ?? $registration->finalRegistration?->payment_amount;
    }

    private function eventLabel(Event $event): string
    {
        return $event->code === '01' ? 'IUPC' : $event->name;
    }

    private function formatSize(?string $size): ?string
    {
        $size = trim((string) $size);

        return $size === '' ? null : strtoupper($size);
    }
}
