@extends('layouts.dashboard')

@section('title', 'Email Log Details')
@section('page-title', 'Email Log Details')
@section('page-subtitle', 'Delivery success and failure details for one dashboard notification.')

@section('content')
    @include('dashboard.emails.partials.nav')

    @if(session('status'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[.78fr_1.22fr]">
        <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Notification</p>
                    <h2 class="mt-3 text-2xl font-semibold text-coal">{{ $notification->subject }}</h2>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $notification->status === 'sent' ? 'bg-green-100 text-green-700' : ($notification->status === 'failed' ? 'bg-red-100 text-red-700' : ($notification->status === 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700')) }}">
                    {{ $notification->status }}
                </span>
            </div>

            <dl class="mt-6 grid gap-4 text-sm">
                <div class="rounded-xl border border-black/5 bg-paper/70 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[.16em] text-coal/45">Sent by</dt>
                    <dd class="mt-2 font-semibold text-coal">{{ $notification->sender?->name ?? 'Unknown' }}</dd>
                    <dd class="mt-1 text-coal/55">{{ $notification->sender?->email }}</dd>
                </div>
                <div class="rounded-xl border border-black/5 bg-paper/70 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[.16em] text-coal/45">Recipients</dt>
                    <dd class="mt-2 text-coal/65">{{ $notification->recipient_count }} total</dd>
                    @if($notification->event_codes)
                        <dd class="mt-1 text-xs text-coal/50">Events: {{ implode(', ', $notification->event_codes) }}</dd>
                    @endif
                    @if($notification->metadata['registration_statuses'] ?? null)
                        <dd class="mt-1 text-xs text-coal/50">Statuses: {{ collect($notification->metadata['registration_statuses'])->map(fn ($status) => ucfirst($status))->join(', ') }}</dd>
                    @endif
                </div>
                <div class="rounded-xl border border-black/5 bg-paper/70 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[.16em] text-coal/45">Delivery summary</dt>
                    <dd class="mt-2 text-coal/65">
                        <span class="font-semibold text-green-700">{{ (int) ($counts['sent'] ?? 0) }}</span> sent,
                        <span class="font-semibold text-red-700">{{ (int) ($counts['failed'] ?? 0) }}</span> failed,
                        <span class="font-semibold text-slate-700">{{ (int) ($counts['pending'] ?? 0) }}</span> pending
                    </dd>
                </div>
            </dl>

            <div class="mt-6">
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-coal/45">Body</p>
                <pre class="mt-3 max-h-[28rem] overflow-auto whitespace-pre-wrap break-words rounded-xl border border-black/5 bg-paper/70 p-4 text-sm leading-6 text-coal/70">{{ $notification->body }}</pre>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('dashboard.email-logs.index') }}" class="rounded-lg border border-black/10 bg-white px-4 py-2.5 text-center text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
                    Back to Logs
                </a>
                <a href="{{ route('dashboard.emails.compose') }}" class="rounded-lg bg-primary px-4 py-2.5 text-center text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                    Compose New
                </a>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-black/5 bg-white shadow-soft">
            <div class="border-b border-black/5 px-5 py-4">
                <h2 class="text-lg font-semibold">Delivery Details</h2>
                <p class="mt-1 text-sm text-coal/55">Failed and pending deliveries are shown first.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-black/5 text-left text-sm">
                    <thead class="bg-paper text-xs uppercase tracking-wide text-coal/45">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Recipient</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Time</th>
                            <th class="px-5 py-3 font-semibold">Error</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @forelse($deliveries as $delivery)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-coal">{{ $delivery->name ?: 'Custom recipient' }}</p>
                                    <p class="mt-1 text-xs text-coal/50">{{ $delivery->email }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $delivery->status === 'sent' ? 'bg-green-100 text-green-700' : ($delivery->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $delivery->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-coal/55">
                                    {{ $delivery->sent_at?->format('d M Y, h:i A') ?? $delivery->failed_at?->format('d M Y, h:i A') ?? '-' }}
                                </td>
                                <td class="max-w-sm px-5 py-4 text-xs leading-5 text-red-700">
                                    {{ $delivery->error ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-sm text-coal/50">No deliveries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="mt-6">
        {{ $deliveries->links() }}
    </div>
@endsection
