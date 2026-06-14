@extends('layouts.app')

@section('title', 'FIFA | '.config('app.name'))

@section('content')
@php
    $facts = [
        ['label' => 'Registration Fee', 'value' => '200 BDT', 'icon' => 'fa-ticket'],
        ['label' => 'Registration Window', 'value' => '18 June - 3 July', 'icon' => 'fa-calendar-plus'],
        ['label' => 'Player Type', 'value' => '1 member tournament', 'icon' => 'fa-user'],
        ['label' => 'Venue', 'value' => 'Auditorium', 'icon' => 'fa-location-dot'],
    ];

    $timeline = [
        [
            'date' => '18 June - 3 July',
            'label' => 'Registration',
            'accent' => 'ember',
            'icon' => 'fa-pen-to-square',
            'items' => [
                ['value' => '18 June', 'label' => 'Opens', 'icon' => 'fa-door-open'],
                ['value' => '3 July', 'label' => 'Closes', 'icon' => 'fa-lock'],
            ],
        ],
        [
            'date' => '24 July, 2026',
            'label' => 'Tournament Day',
            'accent' => 'volt',
            'icon' => 'fa-futbol',
            'items' => [
                ['value' => '9 AM', 'label' => 'Starts', 'icon' => 'fa-play'],
                ['value' => '6 PM', 'label' => 'Ends', 'icon' => 'fa-stop'],
            ],
        ],
    ];

    $format = [
        'EA FC 26 will be played on PS4.',
        'Only controllers are allowed.',
        'The tournament consists of 64 players.',
        'Round of 64 and Round of 32 are direct knockout rounds.',
        'From Round of 16, matches are played as best of 3.',
        'Finals are played as best of 5.',
        'Participants may play with any available team.',
    ];

    $guidelines = [
        'Any undergraduate university or college student can register.',
        'There are no rank limits.',
        'The tournament format features head-to-head matches between individual players.',
    ];
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8 lg:pb-24">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-center gap-12 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,.65fr)]">
            <div class="order-2 max-w-4xl lg:order-1">
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-ember/70"></span>
                    Esports - FIFA
                </div>
                <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    One controller. One bracket. No second screen.
                </h1>
                <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                    A 64-player individual EA FC 26 tournament on PS4, moving from direct knockout rounds into best-of series and a best-of-five final.
                </p>
            </div>

            <div class="order-1 flex justify-center lg:order-2 lg:justify-end">
                <img src="{{ asset('assets/logos/fifa.png') }}" alt="FIFA" class="w-full max-w-sm object-contain">
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-24 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl border-y border-white/10 py-10">
        <div class="grid gap-x-10 gap-y-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($facts as $fact)
                <div>
                    <div class="flex items-center gap-4">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-ember/10 text-ember">
                            <i class="fa-solid {{ $fact['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[.16em] text-white/32">{{ $fact['label'] }}</p>
                            <p class="mt-2 text-base font-semibold leading-6 text-white">{{ $fact['value'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-16 lg:grid-cols-[.75fr_1.25fr]">
            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Tournament Flow</p>
                <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">From registration to finals.</h2>
            </div>

            <div class="relative">
                <div class="absolute left-4 top-4 hidden h-[calc(100%-2rem)] w-px bg-gradient-to-b from-ember/70 via-white/14 to-iris/70 sm:block"></div>

                @foreach($timeline as $block)
                    <div class="relative grid gap-5 pb-12 last:pb-0 sm:grid-cols-[4rem_1fr]">
                        <div class="relative z-10 grid h-8 w-8 place-items-center rounded-full text-xs ring-8 ring-ink sm:mt-2 {{ $block['accent'] === 'volt' ? 'bg-volt/10 text-volt' : ($block['accent'] === 'iris' ? 'bg-iris/10 text-iris' : 'bg-ember/10 text-ember') }}">
                            <i class="fa-solid {{ $block['icon'] }}"></i>
                        </div>

                        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                            <p class="text-xs uppercase tracking-[.18em] text-white/35">{{ $block['date'] }}</p>
                            <h3 class="mt-2 text-xl font-semibold text-white">{{ $block['label'] }}</h3>

                            <div class="mt-7 grid gap-3 sm:grid-cols-2">
                                @foreach($block['items'] as $item)
                                    <div class="rounded-lg border border-white/10 bg-black/15 p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <span class="text-2xl font-semibold leading-none text-white">{{ $item['value'] }}</span>
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-md {{ $block['accent'] === 'volt' ? 'bg-volt/10 text-volt' : ($block['accent'] === 'iris' ? 'bg-iris/10 text-iris' : 'bg-ember/10 text-ember') }}">
                                                <i class="fa-solid {{ $item['icon'] }} text-sm"></i>
                                            </span>
                                        </div>
                                        <p class="mt-4 text-xs uppercase tracking-[.16em] text-white/38">{{ $item['label'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[.9fr_1.1fr]">
        <div>
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Format</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">How matches progress.</h2>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
            <div class="grid gap-4">
                @foreach($format as $item)
                    <div class="grid grid-cols-[2.25rem_1fr] items-start gap-3">
                        <div class="grid h-9 w-9 place-items-center rounded-md bg-white/[.055] text-white/46">
                            <i class="fa-solid fa-check text-sm"></i>
                        </div>
                        <p class="pt-1.5 text-sm leading-6 text-white/66">{{ $item }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[.9fr_1.1fr]">
        <div>
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Registration Guidelines</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Who can enter.</h2>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
            <div class="grid gap-4">
                @foreach($guidelines as $item)
                    <div class="grid grid-cols-[2.25rem_1fr] items-start gap-3">
                        <div class="grid h-9 w-9 place-items-center rounded-md bg-white/[.055] text-white/46">
                            <i class="fa-solid fa-user-check text-sm"></i>
                        </div>
                        <p class="pt-1.5 text-sm leading-6 text-white/66">{{ $item }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-32 pt-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl rounded-lg border border-white/10 bg-white/[.035] px-6 py-14 text-center sm:px-10">
        <p class="text-xs font-medium uppercase tracking-[.22em] text-ember/80">FIFA Registration</p>
        <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Ready for the bracket?</h2>
        <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-white/56">
            Register as an individual player and prepare your controller-only match setup.
        </p>
        <div class="mt-10 flex justify-center">
            <a href="{{ route('fifa.register') }}" class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-ember">
                Register Now
            </a>
        </div>
    </div>
</section>
@endsection
