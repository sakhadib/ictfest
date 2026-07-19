@extends('layouts.app')

@section('title', 'Find Personnel | '.config('app.name'))
@section('canonical', route('personnel.find'))

@section('content')
@php
    $inputClass = 'w-full rounded-md border border-white/10 bg-white/[.05] px-4 py-4 text-base text-white outline-none transition placeholder:text-white/28 focus:border-volt/60 focus:bg-white/[.07]';
@endphp

<section class="px-4 pb-14 pt-32 sm:px-6 lg:px-8 lg:pb-20">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-end gap-10 lg:grid-cols-[1.05fr_.95fr]">
            <div>
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-volt/70"></span>
                    Personnel Finder
                </div>
                <h1 class="mt-8 max-w-4xl text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    Find an event personnel by name.
                </h1>
                <p class="mt-7 max-w-2xl text-base leading-8 text-white/58">
                    Type a partial name to find listed operations personnel and their coordination contact.
                </p>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[.035] p-5 sm:p-6">
                <div class="flex gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-volt/10 text-volt">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-white">Personnel-only search</h2>
                        <p class="mt-2 text-sm leading-7 text-white/56">
                            This page searches the official personnel directory only. Participant, coach, and registration records are not searched here.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('personnel.find') }}" class="mt-12">
            <div class="grid gap-3 lg:grid-cols-[1fr_auto]">
                <label>
                    <span class="sr-only">Personnel name</span>
                    <input name="q" value="{{ $query }}" class="{{ $inputClass }}" placeholder="Write a partial name" autocomplete="off" spellcheck="false">
                </label>
                <button class="inline-flex min-h-[3.5rem] items-center justify-center gap-3 rounded-md bg-white px-6 py-3 text-sm font-semibold text-ink transition hover:bg-volt">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    Find Personnel
                </button>
            </div>
            <p class="mt-4 text-sm leading-7 text-white/46">
                Search is case-insensitive and works with partial names.
            </p>
        </form>
    </div>
</section>

<section class="px-4 pb-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        @if($searched)
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-white">{{ $personnel->count() }} personnel match{{ $personnel->count() === 1 ? '' : 'es' }}</p>
                    <p class="mt-1 text-xs text-white/42">Query: {{ $query }}</p>
                </div>
                <a href="{{ route('personnel.find') }}" class="inline-flex items-center justify-center rounded-md border border-white/12 bg-white/[.04] px-4 py-2.5 text-sm font-semibold text-white/72 transition hover:border-white/24 hover:text-white">
                    Clear Search
                </a>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($personnel as $person)
                    @php
                        $phoneHref = preg_replace('/\D+/', '', (string) $person->phone);
                    @endphp
                    <article class="rounded-lg border border-white/10 bg-white/[.04] p-5 shadow-[0_18px_60px_rgba(0,0,0,.18)]">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="break-words text-xl font-semibold text-white">{{ $person->name }}</h2>
                                <p class="mt-2 break-words text-sm leading-6 text-white/56">{{ ucfirst($person->status ?: 'other') }}{{ $person->team ? ' / '.$person->team : '' }}</p>
                            </div>
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-ember/10 text-ember">
                                <i class="fa-solid fa-user"></i>
                            </span>
                        </div>

                        <div class="mt-6 grid gap-3 text-sm">
                            <div class="rounded-md border border-white/10 bg-black/15 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-[.16em] text-white/34">Phone</p>
                                @if($person->phone)
                                    <a href="tel:{{ $phoneHref }}" class="mt-2 inline-flex break-all font-semibold text-white transition hover:text-volt">{{ $person->phone }}</a>
                                @else
                                    <p class="mt-2 font-semibold text-white/42">Not listed</p>
                                @endif
                            </div>

                            @if($person->comments)
                                <div class="rounded-md border border-white/10 bg-black/15 px-4 py-3">
                                    <p class="text-xs font-medium uppercase tracking-[.16em] text-white/34">Notes</p>
                                    <p class="mt-2 break-words leading-6 text-white/64">{{ $person->comments }}</p>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-white/14 bg-white/[.035] px-6 py-14 text-center md:col-span-2 xl:col-span-3">
                        <h2 class="text-2xl font-semibold text-white">No personnel found.</h2>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-white/56">
                            Try another spelling or a shorter part of the person&apos;s name.
                        </p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                    <p class="text-xs font-medium uppercase tracking-[.18em] text-white/38">Input</p>
                    <p class="mt-3 text-lg font-semibold text-white">Partial name</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                    <p class="text-xs font-medium uppercase tracking-[.18em] text-white/38">Source</p>
                    <p class="mt-3 text-lg font-semibold text-white">Personnel directory</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                    <p class="text-xs font-medium uppercase tracking-[.18em] text-white/38">Result</p>
                    <p class="mt-3 text-lg font-semibold text-white">Contact cards</p>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
