@extends('layouts.dashboard')

@section('title', 'Status')
@section('page-title', 'Status')
@section('page-subtitle', 'Find registrations by code or team lead email.')

@section('content')
    <section class="rounded-lg border border-black/5 bg-white p-5 shadow-soft">
        <form method="GET" action="{{ route('dashboard.status.index') }}" class="grid gap-3 lg:grid-cols-[1fr_auto]">
            <label>
                <span class="text-sm font-semibold text-coal/70">Registration code or team lead email</span>
                <input name="q" value="{{ $query }}" placeholder="01-48372 or lead@example.com" class="mt-2 w-full rounded-lg border border-black/10 bg-paper px-4 py-3 text-sm outline-none transition placeholder:text-coal/32 focus:border-primary/40 focus:bg-white focus:ring-4 focus:ring-primary/10">
            </label>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-lg bg-coal px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-coal/10 transition hover:bg-black lg:w-auto">
                    Search
                </button>
            </div>
        </form>
    </section>

    @if ($searched && $registrations->isEmpty())
        <section class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-5 text-amber-800">
            <p class="font-semibold">No registration found.</p>
            <p class="mt-1 text-sm">Search with an exact registration code or the team lead email address.</p>
        </section>
    @endif

    @if ($registrations->isNotEmpty())
        <div class="mt-6 grid gap-5 xl:grid-cols-2">
            @foreach ($registrations as $registration)
                @php
                    $statusClass = match ($registration->payment_status) {
                        'confirmed' => 'border-primary/30 bg-primary/10 text-primary',
                        'submitted' => 'border-saffron/40 bg-saffron/10 text-amber-800',
                        default => 'border-black/10 bg-paper text-coal/70',
                    };
                @endphp

                <article class="overflow-hidden rounded-lg border border-black/5 bg-white shadow-soft">
                    <div class="border-b border-black/5 p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[.18em] text-coal/40">{{ $registration->event?->name }}</p>
                                <h2 class="mt-2 text-xl font-semibold">{{ $registration->team_name }}</h2>
                                <p class="mt-1 text-sm text-coal/55">{{ $registration->institution }}</p>
                            </div>
                            <span class="w-fit rounded-full border px-3 py-1 text-xs font-semibold capitalize {{ $statusClass }}">
                                {{ $registration->payment_status }}
                            </span>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/35">Registration Code</p>
                            <p class="mt-1 font-mono text-sm font-semibold">{{ $registration->registration_code }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/35">Registration Status</p>
                            <p class="mt-1 text-sm font-semibold capitalize">{{ $registration->status }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/35">Team Lead</p>
                            <p class="mt-1 text-sm font-semibold">{{ $registration->contact_name }}</p>
                            <p class="mt-1 text-sm text-coal/55">{{ $registration->contact_email }}</p>
                            <p class="mt-1 text-sm text-coal/55">{{ $registration->contact_phone }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/35">Payment</p>
                            <p class="mt-1 text-sm font-semibold">{{ $registration->payment?->trx_id ?? '---' }}</p>
                            @if ($registration->payment)
                                <p class="mt-1 text-sm capitalize text-coal/55">{{ $registration->payment->method }} / {{ $registration->payment->status }}</p>
                            @endif
                        </div>
                        @if ($registration->coach)
                            <div class="sm:col-span-2">
                                <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/35">Coach</p>
                                <p class="mt-1 text-sm font-semibold">{{ $registration->coach->name }}</p>
                                <p class="mt-1 text-sm text-coal/55">{{ $registration->coach->designation }}</p>
                                <p class="mt-1 text-sm text-coal/55">{{ $registration->coach->official_email }} / {{ $registration->coach->contact_number }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-black/5 bg-paper/70 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/35">Participants</p>
                        <div class="mt-3 divide-y divide-black/5">
                            @foreach ($registration->participants as $participant)
                                <div class="grid gap-2 py-3 first:pt-0 last:pb-0 sm:grid-cols-[1fr_auto]">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold">{{ $participant->full_name }}</p>
                                            @if ($participant->is_leader)
                                                <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">Lead</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-coal/52">{{ $participant->email }} / {{ $participant->phone }}</p>
                                    </div>
                                    <div class="text-xs text-coal/52 sm:text-right">
                                        <p>{{ $participant->student_id }}</p>
                                        <p class="mt-1">{{ $participant->university }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
