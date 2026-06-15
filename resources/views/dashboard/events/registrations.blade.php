@extends('layouts.dashboard')

@section('title', $event->name)
@section('page-title', $event->name)
@section('page-subtitle', 'Review registrations and payment confirmations.')

@section('content')
    @php
        $tabs = [
            'pending' => 'Pending',
            'final' => 'Awaiting Final',
            'review' => 'Final Review',
            'done' => 'Done',
        ];
    @endphp

    <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Event {{ $event->code }}</p>
                    <h2 class="mt-1 text-lg font-semibold">{{ $event->name }}</h2>
                </div>

                <div class="flex flex-wrap rounded-md border border-slate-200 bg-slate-50 p-1 text-sm">
                    @foreach($tabs as $tabKey => $tabLabel)
                        <a href="{{ route('dashboard.events.registrations.index', ['event' => $event->code, 'tab' => $tabKey]) }}" class="rounded px-4 py-2 font-medium {{ $tab === $tabKey ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950' }}">
                            {{ $tabLabel }}
                            <span class="ml-2 text-slate-400">{{ $counts[$tabKey] ?? 0 }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="mx-5 mt-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Team name</th>
                        <th class="px-5 py-3 font-semibold">Institution</th>
                        <th class="px-5 py-3 font-semibold">Payment</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registrations as $registration)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-950">{{ $registration->team_name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $registration->registration_code }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $registration->institution }}</td>
                            <td class="px-5 py-4">
                                <p class="font-mono text-sm text-slate-700">{{ $registration->payment?->trx_id ?? '---' }}</p>
                                <p class="mt-1 text-xs capitalize text-slate-500">{{ $registration->payment?->method ?? 'No method' }}</p>
                                @if($registration->payment)
                                    <p class="mt-1 text-xs text-slate-500">BDT {{ number_format($registration->payment->amount) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium capitalize text-slate-700">
                                        {{ $registration->status }}
                                    </span>
                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium capitalize text-slate-700">
                                        {{ $registration->payment_status }}
                                    </span>
                                    @if($registration->finalRegistration)
                                        <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium capitalize text-slate-700">
                                            Final {{ $registration->finalRegistration->status }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    @if ($tab !== 'done')
                                        <form method="POST" action="{{ route('dashboard.events.registrations.approve', ['event' => $event->code, 'registration' => $registration]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-md bg-slate-950 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                                @if($event->isFinalRoundPaidType() && $registration->status === 'pending')
                                                    Qualify
                                                @elseif($event->isFinalRoundPaidType())
                                                    Approve Payment
                                                @elseif($registration->status === 'pending')
                                                    Approve Payment
                                                @else
                                                    Approve Intake
                                                @endif
                                            </button>
                                        </form>

                                        @if($tab === 'review')
                                            <form method="POST" action="{{ route('dashboard.events.registrations.reject-final', ['event' => $event->code, 'registration' => $registration]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('dashboard.events.registrations.unapprove', ['event' => $event->code, 'registration' => $registration]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-md border border-amber-200 px-3 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50">
                                                Unapprove
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                No {{ $tab }} registrations found for this event.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $registrations->links() }}
            </div>
        @endif
    </section>
@endsection
