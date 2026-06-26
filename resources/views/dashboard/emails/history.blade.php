@extends('layouts.dashboard')

@section('title', 'Email History')
@section('page-title', 'Email History')
@section('page-subtitle', 'Review previous dashboard notification emails and delivery status.')

@section('content')
    <section class="overflow-hidden rounded-2xl border border-black/5 bg-white shadow-soft">
        <div class="flex flex-col gap-3 border-b border-black/5 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold">Previous Notifications</h2>
                <p class="mt-1 text-sm text-coal/55">Each row is one dashboard email action.</p>
            </div>
            <a href="{{ route('dashboard.emails.index') }}" class="rounded-lg bg-primary px-4 py-2.5 text-center text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                Compose Email
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-black/5 text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wide text-coal/45">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Subject</th>
                        <th class="px-5 py-3 font-semibold">Sender</th>
                        <th class="px-5 py-3 font-semibold">Mode</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Delivery</th>
                        <th class="px-5 py-3 font-semibold">Queued</th>
                        <th class="px-5 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($notifications as $notification)
                        <tr>
                            <td class="max-w-sm px-5 py-4">
                                <p class="truncate font-semibold text-coal">{{ $notification->subject }}</p>
                                <p class="mt-1 line-clamp-1 text-xs text-coal/50">{{ str($notification->body)->limit(120) }}</p>
                            </td>
                            <td class="px-5 py-4 text-coal/65">
                                <p class="font-medium">{{ $notification->sender?->name ?? 'Unknown' }}</p>
                                <p class="mt-1 text-xs text-coal/45">{{ $notification->sender?->email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-paper px-3 py-1 text-xs font-semibold uppercase tracking-wide text-coal/60">{{ $notification->mode }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $notification->status === 'sent' ? 'bg-green-100 text-green-700' : ($notification->status === 'failed' ? 'bg-red-100 text-red-700' : ($notification->status === 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ $notification->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-coal/65">
                                <span class="font-semibold text-green-700">{{ $notification->sent_count }}</span>
                                sent /
                                <span class="font-semibold text-red-700">{{ $notification->failed_count }}</span>
                                failed /
                                <span class="font-semibold text-slate-700">{{ $notification->pending_count }}</span>
                                pending
                            </td>
                            <td class="px-5 py-4 text-coal/55">{{ $notification->queued_at?->format('d M Y, h:i A') ?? $notification->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('dashboard.emails.show', $notification) }}" class="rounded-lg border border-black/10 bg-white px-3 py-2 text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
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
