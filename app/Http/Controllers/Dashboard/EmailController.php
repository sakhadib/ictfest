<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\SendDashboardNotificationEmail;
use App\Models\Delivery;
use App\Models\Event;
use App\Models\Notification as EmailNotification;
use App\Models\Participant;
use App\Models\Registration;
use App\Rules\StrictEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class EmailController extends Controller
{
    private const DRAFT_SESSION_KEY = 'dashboard_email_draft';

    private const REGISTRATION_STATUSES = ['pending', 'verified', 'paid', 'rejected'];

    private const RECIPIENT_SCOPES = [
        'team_lead' => 'Only Team Lead',
        'all_participants' => 'All Participant',
    ];

    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard.emails.compose');
    }

    public function compose(Request $request): View
    {
        return view('dashboard.emails.compose', [
            'draft' => $this->draft($request),
        ]);
    }

    public function storeCompose(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $this->putDraft($request, [
            'subject' => $validated['subject'],
            'body' => str_replace(["\r\n", "\r"], "\n", $validated['body']),
        ]);

        return redirect()->route('dashboard.emails.recipients');
    }

    public function recipients(Request $request): RedirectResponse|View
    {
        $draft = $this->draft($request);

        if (! $this->hasComposedMessage($draft)) {
            return redirect()
                ->route('dashboard.emails.compose')
                ->with('status', 'Write the email subject and body first.');
        }

        return view('dashboard.emails.recipients', [
            'draft' => $draft,
            'events' => $this->eventsWithRecipientCounts(),
            'registrationStatuses' => self::REGISTRATION_STATUSES,
            'recipientScopes' => self::RECIPIENT_SCOPES,
            'statusEmailCounts' => $this->statusEmailCounts(),
        ]);
    }

    public function storeRecipients(Request $request): RedirectResponse
    {
        $draft = $this->draft($request);

        if (! $this->hasComposedMessage($draft)) {
            return redirect()
                ->route('dashboard.emails.compose')
                ->with('status', 'Write the email subject and body first.');
        }

        $mode = $request->input('mode', 'events');

        $validated = $request->validate([
            'mode' => ['required', Rule::in(['events', 'custom'])],
            'event_codes' => [$mode === 'events' ? 'required' : 'nullable', 'array'],
            'event_codes.*' => ['string', Rule::exists('events', 'code')],
            'registration_statuses' => [$mode === 'events' ? 'required' : 'nullable', 'array'],
            'registration_statuses.*' => ['string', Rule::in(self::REGISTRATION_STATUSES)],
            'recipient_scope' => [$mode === 'events' ? 'required' : 'nullable', Rule::in(array_keys(self::RECIPIENT_SCOPES))],
            'custom_email' => [$mode === 'custom' ? 'required' : 'nullable', new \App\Rules\StrictEmail(), 'max:255'],
        ]);

        $this->putDraft($request, [
            'mode' => $validated['mode'],
            'event_codes' => $validated['mode'] === 'events' ? array_values($validated['event_codes'] ?? []) : [],
            'registration_statuses' => $validated['mode'] === 'events' ? array_values($validated['registration_statuses'] ?? []) : [],
            'recipient_scope' => $validated['mode'] === 'events' ? $validated['recipient_scope'] : null,
            'custom_email' => $validated['mode'] === 'custom' ? strtolower(trim($validated['custom_email'])) : null,
        ]);

        return redirect()->route('dashboard.emails.review');
    }

    public function review(Request $request): RedirectResponse|View
    {
        $draft = $this->draft($request);
        $redirect = $this->redirectForIncompleteDraft($draft);

        if ($redirect) {
            return $redirect;
        }

        $recipients = $this->draftRecipients($draft);

        if ($recipients->isEmpty()) {
            return redirect()
                ->route('dashboard.emails.recipients')
                ->withErrors(['event_codes' => 'No recipient emails were found for the selected event, status, and recipient filters.']);
        }

        return view('dashboard.emails.review', [
            'draft' => $draft,
            'recipients' => $recipients,
            'selectedEvents' => $this->selectedEvents($draft),
            'selectedStatusLabels' => $this->selectedStatusLabels($draft),
            'recipientScopeLabel' => $this->recipientScopeLabel($draft),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $draft = $this->draft($request);
        $redirect = $this->redirectForIncompleteDraft($draft);

        if ($redirect) {
            return $redirect;
        }

        $recipients = $this->draftRecipients($draft);

        if ($recipients->isEmpty()) {
            return redirect()
                ->route('dashboard.emails.recipients')
                ->withErrors(['event_codes' => 'No recipient emails were found for the selected event, status, and recipient filters.']);
        }

        $notification = DB::transaction(function () use ($draft, $recipients): EmailNotification {
            $notification = EmailNotification::query()->create([
                'user_id' => auth()->id(),
                'subject' => $draft['subject'],
                'body' => $draft['body'],
                'mode' => $draft['mode'],
                'event_codes' => $draft['mode'] === 'events' ? $draft['event_codes'] : null,
                'metadata' => $draft['mode'] === 'events' ? [
                    'registration_statuses' => $draft['registration_statuses'] ?? [],
                    'recipient_scope' => $draft['recipient_scope'] ?? 'team_lead',
                ] : null,
                'recipient_count' => $recipients->count(),
                'status' => 'queued',
                'queued_at' => now(),
            ]);

            $recipients->each(function (array $recipient) use ($notification): void {
                $notification->deliveries()->create([
                    'email' => $recipient['email'],
                    'name' => $recipient['name'] ?: null,
                    'status' => 'pending',
                ]);
            });

            return $notification;
        });

        $notification->forceFill([
            'status' => 'sending',
        ])->save();

        Delivery::query()
            ->where('notification_id', $notification->id)
            ->pluck('id')
            ->each(function (int $deliveryId): void {
                SendDashboardNotificationEmail::dispatch($deliveryId);
            });

        $request->session()->forget(self::DRAFT_SESSION_KEY);

        return redirect()
            ->route('dashboard.emails.show', $notification)
            ->with('status', $recipients->count().' email'.($recipients->count() === 1 ? '' : 's').' queued on the low-priority queue.');
    }

    public function history(Request $request): View
    {
        $filters = [
            'status' => $request->string('status')->toString(),
            'mode' => $request->string('mode')->toString(),
            'search' => $request->string('search')->toString(),
        ];

        $query = EmailNotification::query()
            ->with('sender')
            ->withCount([
                'deliveries',
                'deliveries as pending_count' => fn ($query) => $query->where('status', 'pending'),
                'deliveries as sent_count' => fn ($query) => $query->where('status', 'sent'),
                'deliveries as failed_count' => fn ($query) => $query->where('status', 'failed'),
            ]);

        if (in_array($filters['status'], ['queued', 'sending', 'sent', 'partial', 'failed'], true)) {
            $query->where('status', $filters['status']);
        }

        if (in_array($filters['mode'], ['events', 'custom'], true)) {
            $query->where('mode', $filters['mode']);
        }

        if (filled($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search): void {
                $query
                    ->where('subject', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('sender', function ($senderQuery) use ($search): void {
                        $senderQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $notifications = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $deliveryStats = [
            'total' => Delivery::query()->count(),
            'pending' => Delivery::query()->where('status', 'pending')->count(),
            'sent' => Delivery::query()->where('status', 'sent')->count(),
            'failed' => Delivery::query()->where('status', 'failed')->count(),
        ];

        return view('dashboard.emails.history', [
            'notifications' => $notifications,
            'filters' => $filters,
            'eventsByCode' => Event::query()->orderBy('code')->get()->keyBy('code'),
            'notificationCount' => EmailNotification::query()->count(),
            'deliveryStats' => $deliveryStats,
        ]);
    }

    public function show(EmailNotification $notification): View
    {
        $notification->load('sender');

        $deliveries = $notification->deliveries()
            ->orderByRaw("case status when 'failed' then 0 when 'pending' then 1 else 2 end")
            ->orderBy('email')
            ->paginate(100);

        $counts = $notification->deliveries()
            ->selectRaw("status, count(*) as aggregate")
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('dashboard.emails.show', [
            'notification' => $notification,
            'deliveries' => $deliveries,
            'counts' => $counts,
        ]);
    }

    private function eventsWithRecipientCounts(): Collection
    {
        return Event::query()
            ->orderBy('code')
            ->get()
            ->each(function (Event $event): void {
                $event->team_lead_email_count = (int) Registration::query()
                    ->whereBelongsTo($event)
                    ->whereNotNull('contact_email')
                    ->where('contact_email', '!=', '')
                    ->selectRaw('count(distinct lower(contact_email)) as aggregate')
                    ->value('aggregate');

                $event->participant_email_count = (int) Participant::query()
                    ->whereHas('registration', fn ($query) => $query->whereBelongsTo($event))
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->selectRaw('count(distinct lower(email)) as aggregate')
                    ->value('aggregate');
            });
    }

    /**
     * @return array{subject?: string, body?: string, mode?: string, event_codes?: array<int, string>, registration_statuses?: array<int, string>, recipient_scope?: ?string, custom_email?: ?string}
     */
    private function draft(Request $request): array
    {
        return $request->session()->get(self::DRAFT_SESSION_KEY, []);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private function putDraft(Request $request, array $values): void
    {
        $request->session()->put(self::DRAFT_SESSION_KEY, array_merge($this->draft($request), $values));
    }

    /**
     * @param  array<string, mixed>  $draft
     */
    private function hasComposedMessage(array $draft): bool
    {
        return filled($draft['subject'] ?? null) && filled($draft['body'] ?? null);
    }

    /**
     * @param  array<string, mixed>  $draft
     */
    private function redirectForIncompleteDraft(array $draft): ?RedirectResponse
    {
        if (! $this->hasComposedMessage($draft)) {
            return redirect()
                ->route('dashboard.emails.compose')
                ->with('status', 'Write the email subject and body first.');
        }

        if (! in_array($draft['mode'] ?? null, ['events', 'custom'], true)) {
            return redirect()
                ->route('dashboard.emails.recipients')
                ->with('status', 'Select recipients before reviewing the email.');
        }

        if (($draft['mode'] ?? null) === 'events' && empty($draft['event_codes'] ?? [])) {
            return redirect()
                ->route('dashboard.emails.recipients')
                ->with('status', 'Select at least one event before reviewing the email.');
        }

        if (($draft['mode'] ?? null) === 'events' && empty($draft['registration_statuses'] ?? [])) {
            return redirect()
                ->route('dashboard.emails.recipients')
                ->with('status', 'Select at least one registration status before reviewing the email.');
        }

        if (
            ($draft['mode'] ?? null) === 'events' &&
            ! array_key_exists((string) ($draft['recipient_scope'] ?? ''), self::RECIPIENT_SCOPES)
        ) {
            return redirect()
                ->route('dashboard.emails.recipients')
                ->with('status', 'Select whether this email should go to team leads or all participants.');
        }

        if (($draft['mode'] ?? null) === 'custom' && blank($draft['custom_email'] ?? null)) {
            return redirect()
                ->route('dashboard.emails.recipients')
                ->with('status', 'Enter a custom email address before reviewing the email.');
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $draft
     * @return \Illuminate\Support\Collection<int, array{email: string, name: ?string}>
     */
    private function draftRecipients(array $draft): Collection
    {
        if (($draft['mode'] ?? null) === 'custom') {
            return collect([[
                'name' => null,
                'email' => strtolower(trim((string) $draft['custom_email'])),
            ]])->filter(fn (array $recipient): bool => StrictEmail::isValid($recipient['email']))->values();
        }

        return $this->eventRecipients(
            $draft['event_codes'] ?? [],
            $draft['registration_statuses'] ?? [],
            (string) ($draft['recipient_scope'] ?? 'team_lead'),
        );
    }

    /**
     * @param  array<string, mixed>  $draft
     */
    private function selectedEvents(array $draft): Collection
    {
        if (($draft['mode'] ?? null) !== 'events') {
            return collect();
        }

        return Event::query()
            ->whereIn('code', $draft['event_codes'] ?? [])
            ->orderBy('code')
            ->get();
    }

    /**
     * @param  array<int, string>  $eventCodes
     * @param  array<int, string>  $registrationStatuses
     * @return \Illuminate\Support\Collection<int, array{email: string, name: string}>
     */
    private function eventRecipients(array $eventCodes, array $registrationStatuses, string $recipientScope): Collection
    {
        if ($recipientScope === 'all_participants') {
            return Participant::query()
                ->whereHas('registration', fn ($query) => $query
                    ->whereIn('status', $registrationStatuses)
                    ->whereHas('event', fn ($query) => $query->whereIn('code', $eventCodes)))
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->orderBy('registration_id')
                ->orderByDesc('is_leader')
                ->orderBy('id')
                ->get(['full_name', 'email'])
                ->map(fn (Participant $participant): array => [
                    'name' => trim($participant->full_name),
                    'email' => strtolower(trim($participant->email)),
                ])
                ->filter(fn (array $recipient): bool => StrictEmail::isValid($recipient['email']))
                ->unique('email')
                ->values();
        }

        return Registration::query()
            ->whereHas('event', fn ($query) => $query->whereIn('code', $eventCodes))
            ->whereIn('status', $registrationStatuses)
            ->whereNotNull('contact_email')
            ->where('contact_email', '!=', '')
            ->orderBy('id')
            ->get(['contact_name', 'contact_email'])
            ->map(fn (Registration $registration): array => [
                'name' => trim($registration->contact_name),
                'email' => strtolower(trim($registration->contact_email)),
            ])
            ->filter(fn (array $recipient): bool => StrictEmail::isValid($recipient['email']))
            ->unique('email')
            ->values();
    }

    private function statusEmailCounts(): array
    {
        $eventCodes = Event::query()->orderBy('code')->pluck('code');
        $counts = [
            'team_lead' => [],
            'all_participants' => [],
        ];

        foreach ($eventCodes as $eventCode) {
            foreach (self::REGISTRATION_STATUSES as $status) {
                $counts['team_lead'][$eventCode][$status] = (int) (Registration::query()
                    ->where('status', $status)
                    ->whereHas('event', fn ($query) => $query->where('code', $eventCode))
                    ->whereNotNull('contact_email')
                    ->where('contact_email', '!=', '')
                    ->selectRaw('count(distinct lower(contact_email)) as aggregate')
                    ->value('aggregate') ?? 0);

                $counts['all_participants'][$eventCode][$status] = (int) (Participant::query()
                    ->whereHas('registration', fn ($query) => $query
                        ->where('status', $status)
                        ->whereHas('event', fn ($query) => $query->where('code', $eventCode)))
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->selectRaw('count(distinct lower(email)) as aggregate')
                    ->value('aggregate') ?? 0);
            }
        }

        return $counts;
    }

    /**
     * @param  array<string, mixed>  $draft
     * @return array<int, string>
     */
    private function selectedStatusLabels(array $draft): array
    {
        return collect($draft['registration_statuses'] ?? [])
            ->filter(fn (string $status): bool => in_array($status, self::REGISTRATION_STATUSES, true))
            ->map(fn (string $status): string => ucfirst($status))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $draft
     */
    private function recipientScopeLabel(array $draft): string
    {
        return self::RECIPIENT_SCOPES[$draft['recipient_scope'] ?? 'team_lead'] ?? self::RECIPIENT_SCOPES['team_lead'];
    }
}
