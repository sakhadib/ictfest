<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const DEFAULT_COLUMNS = [
        'registration_code',
        'event',
        'team_name',
        'institution',
        'contact_name',
        'contact_email',
        'contact_phone',
        'coach_name',
        'coach_designation',
        'coach_official_email',
        'coach_contact_number',
        'registration_status',
        'payment_status',
        'payment_method',
        'trx_id',
        'payment_amount',
        'participants_count',
        'registered_at',
    ];

    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $columns = $this->columns($request);

        $registrations = $this->reportQuery($filters)
            ->limit(50)
            ->get();

        return view('dashboard.reports.index', [
            'events' => Event::orderBy('code')->get(),
            'filters' => $filters,
            'columns' => $columns,
            'availableColumns' => $this->availableColumns(),
            'registrations' => $registrations,
        ]);
    }

    public function download(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $columns = $this->columns($request);
        $availableColumns = $this->availableColumns();
        $fileName = 'registration-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($filters, $columns, $availableColumns): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, array_map(fn (string $column) => $availableColumns[$column], $columns));

            $this->reportQuery($filters)
                ->chunk(200, function ($registrations) use ($handle, $columns): void {
                    foreach ($registrations as $registration) {
                        fputcsv($handle, $this->csvRow($registration, $columns));
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function availableColumns(): array
    {
        return [
            'registration_code' => 'Registration Code',
            'event_code' => 'Event Code',
            'event' => 'Event',
            'team_name' => 'Team Name',
            'institution' => 'Institution',
            'contact_name' => 'Team Lead Name',
            'contact_email' => 'Team Lead Email',
            'contact_phone' => 'Team Lead Phone',
            'coach_name' => 'Coach Name',
            'coach_designation' => 'Coach Designation',
            'coach_official_email' => 'Coach Official Email',
            'coach_contact_number' => 'Coach Contact Number',
            'registration_status' => 'Registration Status',
            'payment_status' => 'Payment Status',
            'payment_method' => 'Payment Method',
            'trx_id' => 'TRX ID',
            'payment_amount' => 'Payment Amount',
            'payment_record_status' => 'Payment Record Status',
            'payment_submitted_at' => 'Payment Submitted At',
            'payment_verified_at' => 'Payment Verified At',
            'participants_count' => 'Participants Count',
            'participant_names' => 'Participant Names',
            'participant_emails' => 'Participant Emails',
            'participant_phones' => 'Participant Phones',
            'participant_student_ids' => 'Participant Student IDs',
            'participant_universities' => 'Participant Universities',
            'registered_at' => 'Registered At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return list<string>
     */
    private function columns(Request $request): array
    {
        $requested = $request->input('columns', self::DEFAULT_COLUMNS);

        if (! is_array($requested)) {
            return self::DEFAULT_COLUMNS;
        }

        $columns = array_values(array_intersect($requested, array_keys($this->availableColumns())));

        return $columns === [] ? self::DEFAULT_COLUMNS : $columns;
    }

    /**
     * @return array<string, mixed>
     */
    private function filters(Request $request): array
    {
        return [
            'event_id' => $request->query('event_id'),
            'registration_status' => $request->query('registration_status'),
            'payment_status' => $request->query('payment_status'),
            'payment_method' => $request->query('payment_method'),
            'payment_record_status' => $request->query('payment_record_status'),
            'has_payment' => $request->query('has_payment'),
            'registered_from' => $request->query('registered_from'),
            'registered_to' => $request->query('registered_to'),
            'payment_submitted_from' => $request->query('payment_submitted_from'),
            'payment_submitted_to' => $request->query('payment_submitted_to'),
            'payment_verified_from' => $request->query('payment_verified_from'),
            'payment_verified_to' => $request->query('payment_verified_to'),
            'amount_min' => $request->query('amount_min'),
            'amount_max' => $request->query('amount_max'),
            'institution' => trim((string) $request->query('institution')),
            'search' => trim((string) $request->query('search')),
            'sort' => $request->query('sort', 'registered_at_desc'),
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return Builder<Registration>
     */
    private function reportQuery(array $filters): Builder
    {
        $query = Registration::query()
            ->with(['event', 'payment', 'coach', 'participants' => fn ($query) => $query->orderByDesc('is_leader')->orderBy('id')])
            ->withCount('participants');

        $query
            ->when($filters['event_id'], fn (Builder $query, string $eventId) => $query->where('event_id', $eventId))
            ->when($filters['registration_status'], fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['payment_status'], fn (Builder $query, string $status) => $query->where('payment_status', $status))
            ->when($filters['registered_from'], fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['registered_to'], fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['institution'], fn (Builder $query, string $institution) => $query->where('institution', 'like', "%{$institution}%"));

        if ($filters['search']) {
            $search = $filters['search'];
            $query->where(function (Builder $query) use ($search): void {
                $query->where('registration_code', strtoupper($search))
                    ->orWhere('team_name', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")
                    ->orWhere('contact_phone', 'like', "%{$search}%")
                    ->orWhereHas('coach', fn (Builder $query) => $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('official_email', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%"))
                    ->orWhereHas('payment', fn (Builder $query) => $query->where('trx_id', 'like', "%{$search}%"));
            });
        }

        if ($filters['has_payment'] === 'yes') {
            $query->whereHas('payment');
        } elseif ($filters['has_payment'] === 'no') {
            $query->whereDoesntHave('payment');
        }

        if ($this->hasPaymentFilters($filters)) {
            $query->whereHas('payment', function (Builder $query) use ($filters): void {
                $query
                    ->when($filters['payment_method'], fn (Builder $query, string $method) => $query->where('method', $method))
                    ->when($filters['payment_record_status'], fn (Builder $query, string $status) => $query->where('status', $status))
                    ->when($filters['payment_submitted_from'], fn (Builder $query, string $date) => $query->whereDate('submitted_at', '>=', $date))
                    ->when($filters['payment_submitted_to'], fn (Builder $query, string $date) => $query->whereDate('submitted_at', '<=', $date))
                    ->when($filters['payment_verified_from'], fn (Builder $query, string $date) => $query->whereDate('verified_at', '>=', $date))
                    ->when($filters['payment_verified_to'], fn (Builder $query, string $date) => $query->whereDate('verified_at', '<=', $date))
                    ->when($filters['amount_min'], fn (Builder $query, string $amount) => $query->where('amount', '>=', (int) $amount))
                    ->when($filters['amount_max'], fn (Builder $query, string $amount) => $query->where('amount', '<=', (int) $amount));
            });
        }

        match ($filters['sort']) {
            'registered_at_asc' => $query->oldest(),
            'team_name_asc' => $query->orderBy('team_name'),
            'team_name_desc' => $query->orderByDesc('team_name'),
            'event_asc' => $query->join('events', 'registrations.event_id', '=', 'events.id')
                ->select('registrations.*')
                ->orderBy('events.code')
                ->orderBy('registrations.created_at', 'desc'),
            default => $query->latest(),
        };

        return $query;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function hasPaymentFilters(array $filters): bool
    {
        foreach (['payment_method', 'payment_record_status', 'payment_submitted_from', 'payment_submitted_to', 'payment_verified_from', 'payment_verified_to', 'amount_min', 'amount_max'] as $key) {
            if (filled($filters[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $columns
     * @return list<string|int|null>
     */
    private function csvRow(Registration $registration, array $columns): array
    {
        return array_map(fn (string $column) => $this->csvValue($registration, $column), $columns);
    }

    private function csvValue(Registration $registration, string $column): string|int|null
    {
        return match ($column) {
            'registration_code' => $registration->registration_code,
            'event_code' => $registration->event?->code,
            'event' => $registration->event?->name,
            'team_name' => $registration->team_name,
            'institution' => $registration->institution,
            'contact_name' => $registration->contact_name,
            'contact_email' => $registration->contact_email,
            'contact_phone' => $registration->contact_phone,
            'coach_name' => $registration->coach?->name,
            'coach_designation' => $registration->coach?->designation,
            'coach_official_email' => $registration->coach?->official_email,
            'coach_contact_number' => $registration->coach?->contact_number,
            'registration_status' => $registration->status,
            'payment_status' => $registration->payment_status,
            'payment_method' => $registration->payment?->method,
            'trx_id' => $registration->payment?->trx_id ?? '---',
            'payment_amount' => $registration->payment?->amount,
            'payment_record_status' => $registration->payment?->status,
            'payment_submitted_at' => $registration->payment?->submitted_at?->format('Y-m-d H:i:s'),
            'payment_verified_at' => $registration->payment?->verified_at?->format('Y-m-d H:i:s'),
            'participants_count' => $registration->participants_count,
            'participant_names' => $registration->participants->pluck('full_name')->join('; '),
            'participant_emails' => $registration->participants->pluck('email')->join('; '),
            'participant_phones' => $registration->participants->pluck('phone')->join('; '),
            'participant_student_ids' => $registration->participants->pluck('student_id')->join('; '),
            'participant_universities' => $registration->participants->pluck('university')->join('; '),
            'registered_at' => $registration->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $registration->updated_at?->format('Y-m-d H:i:s'),
            default => null,
        };
    }
}
