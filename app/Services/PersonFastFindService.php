<?php

namespace App\Services;

use App\Models\OperationsPersonnel;
use App\Models\Participant;
use App\Models\RegistrationCoach;
use Illuminate\Support\Collection;

class PersonFastFindService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function results(string $query, int $limit = 60): Collection
    {
        $normalized = $this->normalizeSearch($query);
        $phoneNeedles = $this->phoneVariants($query);

        if ($normalized === '' && $phoneNeedles === []) {
            return collect();
        }

        return collect()
            ->merge($this->personnelResults($normalized, $phoneNeedles))
            ->merge($this->participantResults($normalized, $phoneNeedles))
            ->merge($this->coachResults($normalized, $phoneNeedles))
            ->unique('dedupe_key')
            ->take($limit)
            ->values()
            ->map(function (array $result): array {
                unset($result['dedupe_key']);

                return $result;
            });
    }

    public function telegramText(string $query, int $limit = 8): string
    {
        $query = trim($query);

        if ($query === '') {
            return "Use /who name, /who email@example.com, or /who 017...\nExample: /who rahman";
        }

        $results = $this->results($query, $limit);

        if ($results->isEmpty()) {
            return "No person found for: {$query}";
        }

        $lines = [
            config('app.name').' Fast Find',
            'Query: '.$query,
            'Matches shown: '.$results->count(),
            '',
        ];

        foreach ($results as $index => $result) {
            $lines[] = ($index + 1).'. '.$result['title'];
            $lines[] = 'Type: '.$result['type'];
            $lines[] = 'Context: '.$result['subtitle'];

            foreach (array_slice($result['lines'], 0, 8, true) as $label => $value) {
                if ($value !== '-') {
                    $lines[] = $label.': '.$value;
                }
            }

            $lines[] = '';
        }

        if ($results->count() === $limit) {
            $lines[] = 'Showing first '.$limit.' matches. Use a more specific query if needed.';
        }

        return trim(implode("\n", $lines));
    }

    private function personnelResults(string $normalized, array $phoneNeedles): Collection
    {
        return OperationsPersonnel::query()
            ->latest()
            ->get()
            ->filter(fn (OperationsPersonnel $person): bool => $this->matches($normalized, $phoneNeedles, [
                $person->name,
                $person->phone,
            ]))
            ->map(fn (OperationsPersonnel $person): array => [
                'dedupe_key' => $this->dedupeKey($person->name, null, $person->phone, 'personnel:'.$person->id),
                'type' => 'Internal Personnel',
                'title' => $person->name,
                'subtitle' => ucfirst($person->status).' / '.($person->team ?: 'No team'),
                'lines' => [
                    'Phone' => $person->phone ?: '-',
                    'Student ID' => $person->student_id ?: '-',
                    'Team' => $person->team ?: '-',
                    'Status' => ucfirst($person->status),
                    'Comments' => $person->comments ?: '-',
                ],
            ]);
    }

    private function participantResults(string $normalized, array $phoneNeedles): Collection
    {
        return Participant::query()
            ->with(['registration.event', 'registration.payment'])
            ->get()
            ->filter(fn (Participant $participant): bool => $this->matches($normalized, $phoneNeedles, [
                $participant->full_name,
                $participant->email,
                $participant->phone,
            ]))
            ->map(fn (Participant $participant): array => [
                'dedupe_key' => $this->dedupeKey($participant->full_name, $participant->email, $participant->phone, 'participant:'.$participant->id),
                'type' => $participant->is_leader ? 'Participant / Leader' : 'Participant',
                'title' => $participant->full_name,
                'subtitle' => ($participant->registration?->event?->code ?? '--').' / '.($participant->registration?->team_name ?? 'No team'),
                'lines' => [
                    'Phone' => $participant->phone ?: '-',
                    'Email' => $participant->email ?: '-',
                    'Student ID' => $participant->student_id ?: '-',
                    'Institution' => $participant->university ?: '-',
                    'Registration Code' => $participant->registration?->registration_code ?: '-',
                    'Team' => $participant->registration?->team_name ?: '-',
                    'Registration Status' => $participant->registration?->status ?: '-',
                    'Payment Status' => $participant->registration?->payment_status ?: '-',
                ],
            ]);
    }

    private function coachResults(string $normalized, array $phoneNeedles): Collection
    {
        return RegistrationCoach::query()
            ->with(['registration.event'])
            ->get()
            ->filter(fn (RegistrationCoach $coach): bool => $this->matches($normalized, $phoneNeedles, [
                $coach->name,
                $coach->official_email,
                $coach->contact_number,
            ]))
            ->map(fn (RegistrationCoach $coach): array => [
                'dedupe_key' => $this->dedupeKey($coach->name, $coach->official_email, $coach->contact_number, 'coach:'.$coach->id),
                'type' => 'Coach',
                'title' => $coach->name,
                'subtitle' => ($coach->registration?->event?->code ?? '--').' / '.($coach->registration?->team_name ?? 'No team'),
                'lines' => [
                    'Phone' => $coach->contact_number ?: '-',
                    'Official Email' => $coach->official_email ?: '-',
                    'Designation' => $coach->designation ?: '-',
                    'Institution' => $coach->registration?->institution ?: '-',
                    'Registration Code' => $coach->registration?->registration_code ?: '-',
                    'Team' => $coach->registration?->team_name ?: '-',
                ],
            ]);
    }

    private function matches(string $normalizedNeedle, array $phoneNeedles, array $values): bool
    {
        foreach ($values as $value) {
            $value = (string) $value;

            if ($normalizedNeedle !== '') {
                $normalizedValue = $this->normalizeSearch($value);

                if ($normalizedValue !== '' && str_contains($normalizedValue, $normalizedNeedle)) {
                    return true;
                }
            }

            $phoneValues = $this->phoneVariants($value);

            foreach ($phoneNeedles as $needle) {
                foreach ($phoneValues as $phoneValue) {
                    if (str_contains($phoneValue, $needle)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function dedupeKey(?string $name, ?string $email, ?string $phone, string $fallback): string
    {
        $email = $this->normalizeSearch((string) $email);

        if ($email !== '') {
            return 'email:'.$email;
        }

        $phone = $this->canonicalPhone((string) $phone);

        if ($phone !== '') {
            return 'phone:'.$phone;
        }

        $name = $this->normalizeSearch((string) $name);

        return $name !== '' ? 'name:'.$name.':'.$fallback : $fallback;
    }

    private function normalizeSearch(string $value): string
    {
        $value = preg_replace('/[^\p{L}\p{N}]+/u', '', $value) ?? '';

        return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    }

    private function canonicalPhone(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (str_starts_with($digits, '880')) {
            return '0'.substr($digits, 3);
        }

        return $digits;
    }

    /**
     * @return list<string>
     */
    private function phoneVariants(string $value): array
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return [];
        }

        $variants = [$digits];

        if (str_starts_with($digits, '880')) {
            $variants[] = '0'.substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $variants[] = '880'.substr($digits, 1);
        }

        return array_values(array_unique($variants));
    }
}
