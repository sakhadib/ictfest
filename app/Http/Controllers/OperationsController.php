<?php

namespace App\Http\Controllers;

use App\Models\OperationsPersonnel;
use App\Models\Participant;
use App\Models\Registration;
use App\Models\IupcCoachActivityLog;
use App\Services\PersonFastFindService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function fastFind(Request $request, PersonFastFindService $people): View
    {
        $query = trim((string) $request->query('q', ''));

        return view('operations.fast-find', [
            'searchQuery' => $query,
            'results' => $query === '' ? collect() : $people->results($query),
        ]);
    }

    public function desk(Request $request): View
    {
        $code = trim((string) $request->query('code', ''));
        $registration = $code === '' ? null : $this->findRegistrationByCode($code);

        return view('operations.desk', [
            'code' => $code,
            'searched' => $code !== '',
            'registration' => $registration,
            'activityLogs' => $registration
                ? IupcCoachActivityLog::query()
                    ->with(['allocation', 'coachLink.coach'])
                    ->where('registration_id', $registration->id)
                    ->latest()
                    ->limit(30)
                    ->get()
                : collect(),
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

    private function findRegistrationByCode(string $code): ?Registration
    {
        $registration = Registration::query()
            ->with(['event', 'participants', 'coach', 'payment', 'finalRegistration'])
            ->whereRaw('LOWER(registration_code) = ?', [strtolower($code)])
            ->first();

        if ($registration) {
            return $registration;
        }

        $normalizedCode = $this->normalizeRegistrationCode($code);

        if ($normalizedCode === '') {
            return null;
        }

        $registrationId = Registration::query()
            ->select(['id', 'registration_code'])
            ->get()
            ->first(fn (Registration $candidate): bool => $this->normalizeRegistrationCode($candidate->registration_code) === $normalizedCode)
            ?->id;

        return $registrationId
            ? Registration::query()
                ->with(['event', 'participants', 'coach', 'payment', 'finalRegistration'])
                ->find($registrationId)
            : null;
    }

    private function normalizeRegistrationCode(string $code): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', $code) ?? '');
    }

    private function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;

        return strtolower(trim(str_replace([' ', '-'], '_', $header)));
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
