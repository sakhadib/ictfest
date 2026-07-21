@extends('layouts.operations')

@section('title', 'Operations')
@section('page-title', 'Operations')
@section('page-subtitle', 'Personnel management and fast on-ground lookup.')

@section('content')
    <div class="grid gap-6 lg:grid-cols-[1fr_.82fr]">
        <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Control Room</p>
            <h2 class="mt-4 text-3xl font-semibold leading-tight sm:text-4xl">Pick the operation you need.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-coal/58">
                Personnel work and lookup work are separate here, so phone-first users can get to the right screen without scrolling through unrelated forms.
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <a href="{{ route('operations.fast-find.index') }}" class="group rounded-2xl border border-primary/20 bg-primary/10 p-5 transition hover:-translate-y-0.5 hover:border-primary/40 hover:bg-primary/15">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-primary text-white shadow-lg shadow-primary/20">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <h3 class="mt-5 text-xl font-semibold">Fast Find</h3>
                    <p class="mt-2 text-sm leading-6 text-coal/58">Search people by name, phone, or email across participants, coaches, and personnel.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary">
                        Open lookup
                        <i class="fa-solid fa-arrow-right text-xs transition group-hover:translate-x-1"></i>
                    </span>
                </a>

                <a href="{{ route('operations.desk.index') }}" class="group rounded-2xl border border-black/5 bg-white p-5 transition hover:-translate-y-0.5 hover:border-primary/25">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-primary text-white shadow-lg shadow-primary/20">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </span>
                    <h3 class="mt-5 text-xl font-semibold">Desk</h3>
                    <p class="mt-2 text-sm leading-6 text-coal/58">Enter a registration code and inspect the full team, payment, participant, T-shirt, coach, and final-registration record.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary">
                        Open desk
                        <i class="fa-solid fa-arrow-right text-xs transition group-hover:translate-x-1"></i>
                    </span>
                </a>

                <a href="{{ route('operations.personnel.index') }}" class="group rounded-2xl border border-black/5 bg-paper p-5 transition hover:-translate-y-0.5 hover:border-primary/25 hover:bg-white">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-coal text-white shadow-lg shadow-coal/15">
                        <i class="fa-solid fa-people-group"></i>
                    </span>
                    <h3 class="mt-5 text-xl font-semibold">Personnel</h3>
                    <p class="mt-2 text-sm leading-6 text-coal/58">Import CSV files, add individual people, and edit or delete operational personnel records.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-coal">
                        Manage team
                        <i class="fa-solid fa-arrow-right text-xs transition group-hover:translate-x-1"></i>
                    </span>
                </a>
            </div>
        </section>

        <aside class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/42">Personnel</p>
                    <p class="mt-3 text-3xl font-semibold">{{ number_format($personnelTotal) }}</p>
                </div>
                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/42">Participants</p>
                    <p class="mt-3 text-3xl font-semibold">{{ number_format($participantsTotal) }}</p>
                </div>
                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/42">Registrations</p>
                    <p class="mt-3 text-3xl font-semibold">{{ number_format($registrationsTotal) }}</p>
                </div>
                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/42">Volunteers</p>
                    <p class="mt-3 text-3xl font-semibold">{{ number_format((int) ($personnelCounts['volunteer'] ?? 0)) }}</p>
                </div>
            </div>

            <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold">Recent personnel</h2>
                    <a href="{{ route('operations.personnel.index') }}" class="text-sm font-semibold text-primary">View all</a>
                </div>

                <div class="mt-4 grid gap-3">
                    @forelse($recentPersonnel as $person)
                        <div class="rounded-xl bg-paper px-4 py-3">
                            <p class="font-semibold">{{ $person->name }}</p>
                            <p class="mt-1 text-sm text-coal/55">{{ ucfirst($person->status) }} / {{ $person->team ?: 'No team' }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-black/10 bg-paper px-4 py-8 text-center text-sm text-coal/50">
                            No personnel records yet.
                        </div>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
@endsection
