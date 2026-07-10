@extends('layouts.dashboard')

@section('title', 'Email Logs')
@section('page-title', 'Email Logs')
@section('page-subtitle', 'Monitor past dashboard email batches and delivery health.')

@section('content')
    @include('dashboard.emails.partials.nav')

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-coal/45">Bulk Emails</p>
            <p class="mt-3 text-3xl font-semibold text-coal">{{ $notificationCount }}</p>
        </div>
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-coal/45">Queued Recipients</p>
            <p class="mt-3 text-3xl font-semibold text-coal">{{ $deliveryStats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-green-100 bg-green-50 p-5 shadow-soft">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-green-700/70">Sent</p>
            <p class="mt-3 text-3xl font-semibold text-green-800">{{ $deliveryStats['sent'] }}</p>
        </div>
        <div class="rounded-2xl border border-red-100 bg-red-50 p-5 shadow-soft">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-red-700/70">Failed</p>
            <p class="mt-3 text-3xl font-semibold text-red-800">{{ $deliveryStats['failed'] }}</p>
            <p class="mt-2 text-xs text-red-700/70">{{ $deliveryStats['pending'] }} pending</p>
        </div>
    </div>

    <section class="mt-6 rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
        <form method="GET" action="{{ route('dashboard.email-logs.index') }}" class="grid gap-4 lg:grid-cols-[1fr_12rem_12rem_auto] lg:items-end">
            <label>
                <span class="text-sm font-semibold text-coal/70">Search subject, body, or sender</span>
                <input name="search" value="{{ $filters['search'] }}" class="mt-2 w-full rounded-xl border border-black/10 bg-paper px-4 py-3 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10" placeholder="Search email logs">
            </label>

            <label>
                <span class="text-sm font-semibold text-coal/70">Status</span>
                <select name="status" class="mt-2 w-full rounded-xl border border-black/10 bg-paper px-4 py-3 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10">
                    <option value="">All</option>
                    @foreach(['queued', 'sending', 'sent', 'partial', 'failed'] as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span class="text-sm font-semibold text-coal/70">Mode</span>
                <select name="mode" class="mt-2 w-full rounded-xl border border-black/10 bg-paper px-4 py-3 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10">
                    <option value="">All</option>
                    <option value="events" @selected($filters['mode'] === 'events')>Event team leads</option>
                    <option value="custom" @selected($filters['mode'] === 'custom')>Custom</option>
                </select>
            </label>

            <div class="flex gap-2">
                <a href="{{ route('dashboard.email-logs.index') }}" class="rounded-xl border border-black/10 bg-white px-4 py-3 text-center text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">Reset</a>
                <button type="submit" class="rounded-xl bg-coal px-4 py-3 text-sm font-semibold text-white transition hover:bg-black">Filter</button>
            </div>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-black/5 bg-white shadow-soft">
        <div class="flex flex-col gap-3 border-b border-black/5 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold">Email batches</h2>
                <p class="mt-1 text-sm text-coal/55">One row per dashboard email action. Open details for recipient-level success and failure.</p>
            </div>
            <a href="{{ route('dashboard.emails.compose') }}" class="rounded-lg bg-primary px-4 py-2.5 text-center text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                Compose Email
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-black/5 text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wide text-coal/45">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Batch</th>
                        <th class="px-5 py-3 font-semibold">Sender</th>
                        <th class="px-5 py-3 font-semibold">Target</th>
                        <th class="px-5 py-3 font-semibold">Delivery</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Timing</th>
                        <th class="px-5 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($notifications as $notification)
                        @php
                            $duration = $notification->completed_at && $notification->queued_at
                                ? $notification->queued_at->diffForHumans($notification->completed_at, true)
                                : null;
                        @endphp
                        <tr>
                            <td class="max-w-md px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 rounded-lg bg-paper px-2 py-1 font-mono text-xs font-semibold text-coal/50">#{{ $notification->id }}</span>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-coal">{{ $notification->subject }}</p>
                                        <p class="mt-1 line-clamp-2 text-xs leading-5 text-coal/50">{{ str($notification->body)->limit(160) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-coal/65">
                                <p class="font-medium">{{ $notification->sender?->name ?? 'Unknown' }}</p>
                                <p class="mt-1 text-xs text-coal/45">{{ $notification->sender?->email ?: '-' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-paper px-3 py-1 text-xs font-semibold uppercase tracking-wide text-coal/60">{{ $notification->mode }}</span>
                                @if($notification->event_codes)
                                    <div class="mt-2 flex max-w-xs flex-wrap gap-1.5">
                                        @foreach($notification->event_codes as $code)
                                            <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                                                {{ $eventsByCode[$code]?->code === '01' ? 'IUPC' : ($eventsByCode[$code]?->name ?? $code) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @if($notification->metadata['registration_statuses'] ?? null)
                                    <div class="mt-2 flex max-w-xs flex-wrap gap-1.5">
                                        @foreach($notification->metadata['registration_statuses'] as $status)
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold capitalize text-coal/60">
                                                {{ $status }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @if($notification->mode === 'events')
                                    <p class="mt-2 text-xs font-semibold text-coal/50">
                                        {{ ($notification->metadata['recipient_scope'] ?? 'team_lead') === 'all_participants' ? 'All Participant' : 'Only Team Lead' }}
                                    </p>
                                @endif
                                <p class="mt-2 text-xs text-coal/50">{{ $notification->recipient_count }} recipients planned</p>
                            </td>
                            <td class="px-5 py-4 text-coal/65">
                                <div class="grid gap-1 text-xs">
                                    <span><strong class="text-green-700">{{ $notification->sent_count }}</strong> sent</span>
                                    <span><strong class="text-red-700">{{ $notification->failed_count }}</strong> failed</span>
                                    <span><strong class="text-slate-700">{{ $notification->pending_count }}</strong> pending</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $notification->status === 'sent' ? 'bg-green-100 text-green-700' : ($notification->status === 'failed' ? 'bg-red-100 text-red-700' : ($notification->status === 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ $notification->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs leading-5 text-coal/55">
                                <p><span class="font-semibold text-coal/70">Queued:</span> {{ $notification->queued_at?->format('d M Y, h:i A') ?? $notification->created_at->format('d M Y, h:i A') }}</p>
                                <p><span class="font-semibold text-coal/70">Completed:</span> {{ $notification->completed_at?->format('d M Y, h:i A') ?? '-' }}</p>
                                <p><span class="font-semibold text-coal/70">Duration:</span> {{ $duration ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('dashboard.email-logs.show', $notification) }}" class="rounded-lg border border-black/10 bg-white px-3 py-2 text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-coal/50">No notification emails have been queued yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
@endsection
