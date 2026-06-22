@extends('layouts.app')

@section('title', 'Valorant Tournament 2026 | IUT ICT Fest')
@section('meta_description', 'Register for Valorant at IUT 12th ICT FEST 2026: 32-team university esports tournament with online brackets and an onsite LAN grand finale.')
@section('canonical', url('/valorant'))
@section('og_image', asset('assets/logos/valorant.png'))

@push('head')
    @include('partials.seo.event-jsonld', [
        'name' => 'Valorant - IUT 12th ICT FEST 2026',
        'description' => 'A 32-team Valorant tournament with online knockout rounds, double elimination, and an onsite LAN final.',
        'url' => url('/valorant'),
        'image' => asset('assets/logos/valorant.png'),
        'startDate' => '2026-07-04T00:00:00+06:00',
        'endDate' => '2026-07-25T18:00:00+06:00',
        'locationName' => 'Islamic University of Technology',
        'registrationUrl' => route('valorant.register'),
        'price' => 600,
        'isAvailable' => (bool) ($eventRecord?->is_live && $eventRecord?->hasAvailableSlots()),
    ])
@endpush

@section('content')
@php
    // Remaining slot counts are intentionally not shown publicly.
    // $remainingSlots = $eventRecord?->remainingSlots();
    // $slotLimit = $eventRecord?->slotLimit();

    $facts = [
        ['label' => 'Registration Fee', 'value' => '600 BDT per team', 'icon' => 'fa-ticket'],
        ['label' => 'Registration Window', 'value' => '18 June - 30 June', 'icon' => 'fa-calendar-plus'],
        ['label' => 'Team Size', 'value' => '5-7 members', 'icon' => 'fa-users'],
        ['label' => 'LAN Final', 'value' => '25 July, 2026', 'icon' => 'fa-flag-checkered'],
    ];

    $timeline = [
        [
            'date' => '18-30 June',
            'label' => 'Registration',
            'accent' => 'iris',
            'icon' => 'fa-pen-to-square',
            'items' => [
                ['value' => '18 June', 'label' => 'Opens', 'icon' => 'fa-door-open'],
                ['value' => '30 June', 'label' => 'Closes', 'icon' => 'fa-lock'],
            ],
        ],
        [
            'date' => '4-21 July',
            'label' => 'Online Match Window',
            'accent' => 'volt',
            'icon' => 'fa-crosshairs',
            'items' => [
                ['value' => '32', 'label' => 'Teams', 'icon' => 'fa-users'],
                ['value' => 'Online', 'label' => 'Match Window', 'icon' => 'fa-wifi'],
            ],
        ],
        [
            'date' => '25 July, 2026',
            'label' => 'Grand Finale LAN',
            'accent' => 'ember',
            'icon' => 'fa-trophy',
            'items' => [
                ['value' => 'LAN', 'label' => 'Onsite Final', 'icon' => 'fa-network-wired'],
                ['value' => 'Full Lineup', 'label' => 'Required', 'icon' => 'fa-user-group'],
            ],
        ],
    ];

    $teamRules = [
        'Any college (Higher Secondary), undergraduate, or postgraduate student can register and form a team.',
        'Cross-institution teams are allowed.',
        'Each team must have at least 5 players and no more than 7 players including substitutes.',
        'A team may have a coach, but it is not mandatory.',
        'A player can play for only one team.',
        'There are no rank limits.',
    ];

    $format = [
        'The tournament consists of 32 teams.',
        'The first 2 rounds will be direct knockout.',
        'The full tournament uses a double elimination format.',
        'Teams losing in the upper bracket move to the lower bracket for another chance.',
        'Teams losing in the lower bracket are eliminated.',
        'Grand Finale will be in LAN and teams must be present with full lineup.',
    ];

    $guidelines = [
        ['title' => 'Student ID Card', 'text' => 'Every LAN participant must carry university student ID for verification.', 'icon' => 'fa-id-card'],
        ['title' => 'Keyboard', 'text' => 'Teams must bring their own keyboard for LAN participation.', 'icon' => 'fa-keyboard'],
        ['title' => 'Mouse and Mousepad', 'text' => 'Teams must bring mouse and mousepad for the LAN event.', 'icon' => 'fa-computer-mouse'],
        ['title' => 'Headset', 'text' => 'Teams must bring their own headset.', 'icon' => 'fa-headset'],
    ];

    $prizes = [
        ['label' => 'Champions', 'value' => '30,000 BDT'],
        ['label' => 'Runner-up', 'value' => '15,000 BDT'],
        ['label' => 'MVP', 'value' => '5,000 BDT'],
    ];
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8 lg:pb-24">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-center gap-12 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,.65fr)]">
            <div class="order-2 max-w-4xl lg:order-1">
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-iris/70"></span>
                    Valorant
                </div>
                <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    Online brackets into a LAN grand finale.
                </h1>
                <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                    A 32-team Valorant tournament moving through online knockout and double elimination rounds before the onsite LAN final.
                </p>
                {{-- @if($remainingSlots !== null)
                    <div class="mt-8 inline-flex items-center gap-5 rounded-lg border border-iris/35 bg-iris/10 px-5 py-4">
                        <span class="grid h-11 w-11 place-items-center rounded-md bg-iris/15 text-iris">
                            <i class="fa-solid fa-ticket"></i>
                        </span>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[.18em] text-iris/85">Slots Left</p>
                            <p class="mt-1 text-3xl font-semibold text-white">{{ $remainingSlots }} / {{ $slotLimit }}</p>
                        </div>
                    </div>
                @endif --}}
                <div class="mt-10">
                    <a href="{{ route('events.rulebook', ['eventSlug' => 'valorant']) }}" class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-iris">
                        See Rulebook
                    </a>
                </div>
            </div>

            <div class="order-1 flex justify-center lg:order-2 lg:justify-end">
                <img src="{{ asset('assets/logos/valorant.png') }}" alt="Valorant" class="w-full max-w-sm object-contain">
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
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-iris/10 text-iris">
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

<section class="px-4 pb-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl rounded-lg border border-iris/25 bg-iris/10 p-6 sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-iris/80">Prize Pool</p>
                <h2 class="mt-3 text-3xl font-semibold text-white">50,000 BDT</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-3 lg:flex-1">
                @foreach($prizes as $prize)
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-xs uppercase tracking-[.16em] text-white/38">{{ $prize['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ $prize['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-16 lg:grid-cols-[.75fr_1.25fr]">
            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Tournament Timeline</p>
                <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">From registration to LAN.</h2>
            </div>

            <div class="relative">
                <div class="absolute left-4 top-4 hidden h-[calc(100%-2rem)] w-px bg-gradient-to-b from-iris/70 via-white/14 to-ember/70 sm:block"></div>

                @foreach($timeline as $block)
                    <div class="relative grid gap-5 pb-12 last:pb-0 sm:grid-cols-[4rem_1fr]">
                        <div class="relative z-10 grid h-8 w-8 place-items-center rounded-full text-xs ring-8 ring-ink sm:mt-2 {{ $block['accent'] === 'volt' ? 'bg-volt/10 text-volt' : ($block['accent'] === 'ember' ? 'bg-ember/10 text-ember' : 'bg-iris/10 text-iris') }}">
                            <i class="fa-solid {{ $block['icon'] }}"></i>
                        </div>

                        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="text-xl font-semibold text-white">{{ $block['label'] }}</h3>
                                <p class="inline-flex w-fit rounded-full border border-white/12 bg-white/[.06] px-3 py-1 text-sm font-semibold tracking-[.12em] text-white">{{ $block['date'] }}</p>
                            </div>

                            <div class="mt-7 grid gap-3 sm:grid-cols-2">
                                @foreach($block['items'] as $item)
                                    <div class="rounded-lg border border-white/10 bg-black/15 p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <span class="text-2xl font-semibold leading-none text-white">{{ $item['value'] }}</span>
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-md {{ $block['accent'] === 'volt' ? 'bg-volt/10 text-volt' : ($block['accent'] === 'ember' ? 'bg-ember/10 text-ember' : 'bg-iris/10 text-iris') }}">
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
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Team Formation</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Roster rules.</h2>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
            <div class="grid gap-4">
                @foreach($teamRules as $item)
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
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Format</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Bracket structure.</h2>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
            <div class="grid gap-4">
                @foreach($format as $item)
                    <div class="grid grid-cols-[2.25rem_1fr] items-start gap-3">
                        <div class="grid h-9 w-9 place-items-center rounded-md bg-white/[.055] text-white/46">
                            <i class="fa-solid fa-sitemap text-sm"></i>
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
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">LAN Kit</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">What teams must bring.</h2>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
            <div class="grid gap-5">
                @foreach($guidelines as $item)
                    <div class="grid grid-cols-[2.5rem_1fr] items-start gap-4">
                        <div class="grid h-10 w-10 place-items-center rounded-md bg-white/[.055] text-white/46">
                            <i class="fa-solid {{ $item['icon'] }} text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-white">{{ $item['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-white/56">{{ $item['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-32 pt-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl rounded-lg border border-white/10 bg-white/[.035] px-6 py-14 text-center sm:px-10">
        <p class="text-xs font-medium uppercase tracking-[.22em] text-iris/80">Valorant Registration</p>
        <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Ready for the bracket?</h2>
        <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-white/56">
            Register your roster and prepare your LAN equipment before the online match window begins.
        </p>
        {{-- @if($remainingSlots !== null)
            <div class="mx-auto mt-8 max-w-sm rounded-lg border border-iris/35 bg-iris/10 px-5 py-4">
                <p class="text-xs font-medium uppercase tracking-[.18em] text-iris/85">Available Slots</p>
                <p class="mt-2 text-4xl font-semibold text-white">{{ $remainingSlots }}</p>
                <p class="mt-2 text-sm text-white/56">out of {{ $slotLimit }} team slots</p>
            </div>
        @endif --}}
        <div class="mt-10 flex justify-center">
            @if($eventRecord?->is_live && $eventRecord?->hasAvailableSlots())
                <a href="{{ route('valorant.register') }}" class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-iris">
                    Register Now
                </a>
            @elseif($eventRecord?->is_live)
                <span class="inline-flex items-center justify-center gap-3 rounded-md border border-iris/30 bg-iris/10 px-5 py-3 text-sm font-semibold text-iris">
                    Slots are full
                </span>
            @else
                <span class="inline-flex items-center justify-center gap-3 rounded-md border border-white/12 bg-white/[.04] px-5 py-3 text-sm font-semibold text-white/58">
                    Registration will be live soon
                </span>
            @endif
        </div>
    </div>
</section>
@endsection
