<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\SendDashboardNotificationEmail;
use App\Models\Delivery;
use App\Models\Event;
use App\Models\Notification as EmailNotification;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class EmailController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->withCount(['registrations as team_lead_email_count' => fn ($query) => $query
                ->whereNotNull('contact_email')
                ->where('contact_email', '!=', '')
                ->selectRaw('count(distinct lower(contact_email))')])
            ->orderBy('code')
            ->get();

        return view('dashboard.emails.index', [
            'events' => $events,
        ]);
    }

    public function history(): View
    {
        $notifications = EmailNotification::query()
            ->with('sender')
            ->withCount([
                'deliveries',
                'deliveries as pending_count' => fn ($query) => $query->where('status', 'pending'),
                'deliveries as sent_count' => fn ($query) => $query->where('status', 'sent'),
                'deliveries as failed_count' => fn ($query) => $query->where('status', 'failed'),
            ])
            ->latest()
            ->paginate(20);

        return view('dashboard.emails.history', [
            'notifications' => $notifications,
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

    public function send(Request $request): RedirectResponse
    {
        $mode = $request->input('mode', 'events');

        $validated = $request->validate([
            'mode' => ['required', Rule::in(['events', 'custom'])],
            'event_codes' => [$mode === 'events' ? 'required' : 'nullable', 'array'],
            'event_codes.*' => ['string', Rule::exists('events', 'code')],
            'custom_email' => [$mode === 'custom' ? 'required' : 'nullable', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $body = str_replace(["\r\n", "\r"], "\n", $validated['body']);
        $subject = $validated['subject'];
        $eventCodes = array_values($validated['event_codes'] ?? []);
        $recipients = $validated['mode'] === 'custom'
            ? collect([[
                'name' => null,
                'email' => strtolower(trim($validated['custom_email'])),
            ]])
            : $this->eventRecipients($eventCodes);

        if ($recipients->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['event_codes' => 'No team lead emails were found for the selected events.']);
        }

        $notification = DB::transaction(function () use ($validated, $subject, $body, $eventCodes, $recipients): EmailNotification {
            $notification = EmailNotification::query()->create([
                'user_id' => auth()->id(),
                'subject' => $subject,
                'body' => $body,
                'mode' => $validated['mode'],
                'event_codes' => $validated['mode'] === 'events' ? $eventCodes : null,
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

        if ($validated['mode'] === 'custom') {
            return redirect()
                ->route('dashboard.emails.show', $notification)
                ->with('status', 'Custom email has been queued on the low-priority queue.');
        }

        return redirect()
            ->route('dashboard.emails.show', $notification)
            ->with('status', $recipients->count().' email'.($recipients->count() === 1 ? '' : 's').' queued on the low-priority queue.');
    }

    /**
     * @param  array<int, string>  $eventCodes
     * @return \Illuminate\Support\Collection<int, array{email: string, name: string}>
     */
    private function eventRecipients(array $eventCodes): Collection
    {
        return Registration::query()
            ->whereHas('event', fn ($query) => $query->whereIn('code', $eventCodes))
            ->whereNotNull('contact_email')
            ->where('contact_email', '!=', '')
            ->orderBy('id')
            ->get(['contact_name', 'contact_email'])
            ->map(fn (Registration $registration): array => [
                'name' => trim($registration->contact_name),
                'email' => strtolower(trim($registration->contact_email)),
            ])
            ->filter(fn (array $recipient): bool => filled($recipient['email']))
            ->unique('email')
            ->values();
    }
}
