<?php

namespace App\Http\Controllers;

use App\Models\OperationsPersonnel;
use App\Models\Participant;
use App\Models\Registration;
use App\Models\RegistrationCoach;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OperationsController extends Controller
{
    private const CSV_REQUIRED_HEADERS = ['name', 'student_id', 'phone', 'team', 'status'];

    private const CSV_OPTIONAL_HEADERS = ['comments'];

    public function index(Request $request): View
    {
        $personnelCounts = OperationsPersonnel::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('operations.index', [
            'personnelTotal' => OperationsPersonnel::query()->count(),
            'personnelCounts' => $personnelCounts,
            'registrationsTotal' => Registration::query()->count(),
            'participantsTotal' => Participant::query()->count(),
            'recentPersonnel' => OperationsPersonnel::query()->latest()->limit(6)->get(),
        ]);
    }

    public function personnel(): View
    {
        return view('operations.personnel', [
            'personnel' => OperationsPersonnel::query()
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'statuses' => OperationsPersonnel::STATUSES,
        ]);
    }

    public function fastFind(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        return view('operations.fast-find', [
            'searchQuery' => $query,
            'results' => $query === '' ? collect() : $this->fastFindResults($query),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        OperationsPersonnel::query()->create($this->validatedPersonnel($request));

        return redirect()
            ->route('operations.personnel.index')
            ->with('status', 'Personnel added.');
    }

    public function update(Request $request, OperationsPersonnel $personnel): RedirectResponse
    {
        $personnel->update($this->validatedPersonnel($request));

        return redirect()
            ->route('operations.personnel.index')
            ->with('status', 'Personnel updated.');
    }

    public function destroy(OperationsPersonnel $personnel): RedirectResponse
    {
        $personnel->delete();

        return redirect()
            ->route('operations.personnel.index')
            ->with('status', 'Personnel deleted.');
    }

    public function upload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'personnel_csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $path = $validated['personnel_csv']->getRealPath();

        if (! $path || ! is_readable($path)) {
            return back()->withErrors(['personnel_csv' => 'Could not read the uploaded CSV file.']);
        }

        $handle = fopen($path, 'r');

        if (! $handle) {
            return back()->withErrors(['personnel_csv' => 'Could not open the uploaded CSV file.']);
        }

        try {
            $headers = fgetcsv($handle);

            if (! is_array($headers)) {
                return back()->withErrors(['personnel_csv' => 'The CSV file is empty. Required columns: name, student_id, phone, team, status. Optional: comments.']);
            }

            $headers = array_map(fn ($header): string => $this->normalizeHeader((string) $header), $headers);
            $headerErrors = $this->headerErrors($headers);

            if ($headerErrors !== []) {
                return back()->withErrors(['personnel_csv' => implode(' ', $headerErrors)]);
            }

            $rows = [];
            $line = 1;

            while (($csvRow = fgetcsv($handle)) !== false) {
                $line++;

                if ($this->isBlankCsvRow($csvRow)) {
                    continue;
                }

                if (count($csvRow) !== count($headers)) {
                    return back()->withErrors([
                        'personnel_csv' => "Row {$line} has ".count($csvRow).' cells, but the header has '.count($headers).' columns.',
                    ]);
                }

                $row = array_combine($headers, array_map(fn ($value): string => trim((string) $value), $csvRow));

                if (! is_array($row)) {
                    return back()->withErrors(['personnel_csv' => "Row {$line} could not be parsed."]);
                }

                $status = strtolower($row['status'] ?? '') ?: 'other';

                if (! in_array($status, OperationsPersonnel::STATUSES, true)) {
                    return back()->withErrors([
                        'personnel_csv' => "Row {$line} has invalid status '{$row['status']}'. Allowed: volunteer, organizer, staff, faculty, other. Blank status becomes other.",
                    ]);
                }

                if (trim((string) ($row['name'] ?? '')) === '') {
                    return back()->withErrors(['personnel_csv' => "Row {$line} is missing name."]);
                }

                $rows[] = [
                    'name' => $row['name'],
                    'student_id' => $row['student_id'] !== '' ? $row['student_id'] : null,
                    'phone' => $row['phone'] !== '' ? $row['phone'] : null,
                    'team' => $row['team'] !== '' ? $row['team'] : null,
                    'status' => $status,
                    'comments' => ($row['comments'] ?? '') !== '' ? $row['comments'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        } finally {
            fclose($handle);
        }

        if ($rows === []) {
            return back()->withErrors(['personnel_csv' => 'No personnel rows were found in the CSV.']);
        }

        OperationsPersonnel::query()->insert($rows);

        return redirect()
            ->route('operations.personnel.index')
            ->with('status', count($rows).' personnel record'.(count($rows) === 1 ? '' : 's').' imported.');
    }

    /**
     * @return array{name: string, student_id: ?string, phone: ?string, team: ?string, status: string, comments: ?string}
     */
    private function validatedPersonnel(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'team' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(OperationsPersonnel::STATUSES)],
            'comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['status'] = $data['status'] ?? 'other';

        return $data;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function fastFindResults(string $query): Collection
    {
        $normalized = $this->normalizeSearch($query);
        $phoneNeedles = $this->phoneVariants($query);

        if ($normalized === '' && $phoneNeedles === []) {
            return collect();
        }

        return collect()
            ->merge($this->personnelResults($normalized, $phoneNeedles))
            ->merge($this->participantResults($normalized, $phoneNeedles))
            ->merge($this->registrationResults($normalized, $phoneNeedles))
            ->merge($this->coachResults($normalized, $phoneNeedles))
            ->take(60)
            ->values();
    }

    private function personnelResults(string $normalized, array $phoneNeedles): Collection
    {
        return OperationsPersonnel::query()
            ->latest()
            ->get()
            ->filter(fn (OperationsPersonnel $person): bool => $this->matches($normalized, $phoneNeedles, [
                $person->name,
                $person->student_id,
                $person->phone,
                $person->team,
                $person->status,
                $person->comments,
            ]))
            ->map(fn (OperationsPersonnel $person): array => [
                'type' => 'Internal Personnel',
                'title' => $person->name,
                'subtitle' => ucfirst($person->status).' / '.($person->team ?: 'No team'),
                'lines' => [
                    'Phone' => $person->phone ?: '-',
                    'Student ID' => $person->student_id ?: '-',
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
                $participant->student_id,
                $participant->university,
                $participant->registration?->registration_code,
                $participant->registration?->team_name,
            ]))
            ->map(fn (Participant $participant): array => [
                'type' => $participant->is_leader ? 'Participant / Leader' : 'Participant',
                'title' => $participant->full_name,
                'subtitle' => ($participant->registration?->event?->code ?? '--').' / '.($participant->registration?->team_name ?? 'No team'),
                'lines' => [
                    'Phone' => $participant->phone ?: '-',
                    'Email' => $participant->email ?: '-',
                    'Student ID' => $participant->student_id ?: '-',
                    'Institution' => $participant->university ?: '-',
                    'Registration Code' => $participant->registration?->registration_code ?: '-',
                    'Registration Status' => $participant->registration?->status ?: '-',
                    'Payment Status' => $participant->registration?->payment_status ?: '-',
                ],
            ]);
    }

    private function registrationResults(string $normalized, array $phoneNeedles): Collection
    {
        return Registration::query()
            ->with(['event', 'payment', 'participants'])
            ->get()
            ->filter(fn (Registration $registration): bool => $this->matches($normalized, $phoneNeedles, [
                $registration->registration_code,
                $registration->team_name,
                $registration->institution,
                $registration->contact_name,
                $registration->contact_email,
                $registration->contact_phone,
                $registration->participants->pluck('full_name')->join(' '),
                $registration->participants->pluck('email')->join(' '),
                $registration->participants->pluck('phone')->join(' '),
            ]))
            ->map(fn (Registration $registration): array => [
                'type' => 'Registration',
                'title' => $registration->registration_code.' / '.$registration->team_name,
                'subtitle' => ($registration->event?->code ?? '--').' / '.($registration->event?->name ?? 'Unknown event'),
                'lines' => [
                    'Lead' => $registration->contact_name ?: '-',
                    'Lead Phone' => $registration->contact_phone ?: '-',
                    'Lead Email' => $registration->contact_email ?: '-',
                    'Institution' => $registration->institution ?: '-',
                    'Registration Status' => $registration->status ?: '-',
                    'Payment Status' => $registration->payment_status ?: '-',
                    'TRX ID' => $registration->payment?->trx_id ?: '-',
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
                $coach->designation,
                $coach->official_email,
                $coach->contact_number,
                $coach->registration?->registration_code,
                $coach->registration?->team_name,
                $coach->registration?->institution,
            ]))
            ->map(fn (RegistrationCoach $coach): array => [
                'type' => 'Coach',
                'title' => $coach->name,
                'subtitle' => ($coach->registration?->event?->code ?? '--').' / '.($coach->registration?->team_name ?? 'No team'),
                'lines' => [
                    'Phone' => $coach->contact_number ?: '-',
                    'Official Email' => $coach->official_email ?: '-',
                    'Designation' => $coach->designation ?: '-',
                    'Institution' => $coach->registration?->institution ?: '-',
                    'Registration Code' => $coach->registration?->registration_code ?: '-',
                ],
            ]);
    }

    private function matches(string $normalizedNeedle, array $phoneNeedles, array $values): bool
    {
        foreach ($values as $value) {
            $value = (string) $value;

            if ($normalizedNeedle !== '') {
                $normalizedValue = $this->normalizeSearch($value);

                if (
                    $normalizedValue !== '' &&
                    (str_contains($normalizedValue, $normalizedNeedle) || str_contains($normalizedNeedle, $normalizedValue))
                ) {
                    return true;
                }
            }

            $phoneValues = $this->phoneVariants($value);

            foreach ($phoneNeedles as $needle) {
                if (in_array($needle, $phoneValues, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;

        return strtolower(trim(str_replace([' ', '-'], '_', $header)));
    }

    private function normalizeSearch(string $value): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', $value) ?? '');
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

    /**
     * @param list<string> $headers
     * @return list<string>
     */
    private function headerErrors(array $headers): array
    {
        $allowed = array_merge(self::CSV_REQUIRED_HEADERS, self::CSV_OPTIONAL_HEADERS);
        $missing = array_values(array_diff(self::CSV_REQUIRED_HEADERS, $headers));
        $unknown = array_values(array_diff($headers, $allowed));
        $duplicates = array_values(array_unique(array_diff_assoc($headers, array_unique($headers))));
        $errors = [];

        if ($missing !== []) {
            $errors[] = 'Missing required column'.(count($missing) === 1 ? '' : 's').': '.implode(', ', $missing).'.';
        }

        if ($unknown !== []) {
            $errors[] = 'Unknown column'.(count($unknown) === 1 ? '' : 's').': '.implode(', ', $unknown).'.';
        }

        if ($duplicates !== []) {
            $errors[] = 'Duplicate column'.(count($duplicates) === 1 ? '' : 's').': '.implode(', ', $duplicates).'.';
        }

        $errors[] = 'Required columns: name, student_id, phone, team, status. Optional column: comments.';

        return $missing === [] && $unknown === [] && $duplicates === [] ? [] : $errors;
    }

    /**
     * @param list<string|null> $row
     */
    private function isBlankCsvRow(array $row): bool
    {
        return collect($row)->every(fn ($value): bool => trim((string) $value) === '');
    }
}
