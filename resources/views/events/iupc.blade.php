@extends('layouts.app')

@section('title', 'IUPC | 12th IUT ICT FEST 2026')

@section('content')
@php
    $facts = [
        ['label' => 'Registration Fee', 'value' => '5099 BDT per team', 'icon' => 'fa-ticket'],
        ['label' => 'Registration Window', 'value' => '18 June - 3 July', 'icon' => 'fa-calendar-plus'],
        ['label' => 'Team Size', 'value' => '3 members', 'icon' => 'fa-users'],
        ['label' => 'Venue', 'value' => 'IUT Computer Labs', 'icon' => 'fa-location-dot'],
    ];

    $schedule = [
        [
            'date' => '24 July, 2026',
            'label' => 'Mock Contest',
            'accent' => 'volt',
            'items' => [
                ['time' => '03:00 PM', 'label' => 'Contest Start', 'icon' => 'fa-play'],
                ['time' => '06:00 PM', 'label' => 'Contest End', 'icon' => 'fa-stop'],
            ],
        ],
        [
            'date' => '25 July, 2026',
            'label' => 'Main Contest',
            'accent' => 'ember',
            'items' => [
                ['time' => '08:00 AM', 'label' => 'Reporting', 'icon' => 'fa-id-card'],
                ['time' => '09:00 AM', 'label' => 'Contest Start', 'icon' => 'fa-play'],
                ['time' => '02:00 PM', 'label' => 'Contest End', 'icon' => 'fa-stop'],
                ['time' => '05:00 PM', 'label' => 'Prize Giving', 'icon' => 'fa-trophy'],
            ],
        ],
    ];

    $eligibility = [
        'Must be enrolled university student undergrad or postgrad.',
        '2 teams will be allowed from school and college.',
        'All fees must be paid.',
        'Registration must be completed.',
        'All contestants are required to present a valid student identification card.',
    ];
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8 lg:pb-24">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-4xl">
            <div class="inline-flex items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45">
                <span class="h-px w-10 bg-volt/70"></span>
                Programming Contest
            </div>
            <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                Inter University Programming Contest
            </h1>
            <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                A three-member team contest with a dedicated mock round, a five-hour main round, and onsite lab execution at 12th IUT ICT FEST 2026.
            </p>
        </div>
    </div>
</section>

<section class="px-4 pb-24 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl border-y border-white/10 py-10">
        <div class="grid gap-x-10 gap-y-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($facts as $fact)
                <div>
                    <div class="flex items-center gap-4">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-volt/10 text-volt">
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
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Contest Schedule</p>
                <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Two rounds across two days.</h2>
            </div>

            <div class="relative">
                <div class="absolute left-4 top-4 hidden h-[calc(100%-2rem)] w-px bg-gradient-to-b from-volt/70 via-white/14 to-ember/70 sm:block"></div>

                @foreach($schedule as $index => $block)
                    <div class="relative grid gap-5 pb-12 last:pb-0 sm:grid-cols-[4rem_1fr]">
                        <div class="relative z-10 grid h-8 w-8 place-items-center rounded-full bg-volt/10 text-xs text-volt ring-8 ring-ink sm:mt-2">
                            <i class="fa-solid {{ $index === 0 ? 'fa-stopwatch' : 'fa-code' }}"></i>
                        </div>

                        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                            <p class="text-xs uppercase tracking-[.18em] text-white/35">{{ $block['date'] }}</p>
                            <h3 class="mt-2 text-xl font-semibold text-white">{{ $block['label'] }}</h3>

                            <div class="mt-7 grid gap-3 sm:grid-cols-2">
                                @foreach($block['items'] as $item)
                                    <div class="rounded-lg border border-white/10 bg-black/15 p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <span class="text-2xl font-semibold leading-none text-white">{{ $item['time'] }}</span>
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-md {{ $block['accent'] === 'ember' ? 'bg-ember/10 text-ember' : 'bg-volt/10 text-volt' }}">
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
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Eligibility</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">What participants need.</h2>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
            <div class="grid gap-4">
                @foreach($eligibility as $item)
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

<section class="px-4 pb-32 pt-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl rounded-lg border border-white/10 bg-white/[.035] px-6 py-14 text-center sm:px-10">
        <p class="text-xs font-medium uppercase tracking-[.22em] text-volt/80">IUPC Registration</p>
        <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Ready for the contest floor?</h2>
        <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-white/56">
            Keep your team details, payment, and valid student identification ready before registration closes.
        </p>
        <div class="mt-10 flex justify-center">
            <a href="{{ route('iupc.register') }}" class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-volt">
                Register Now
            </a>
        </div>
    </div>
</section>
@endsection
