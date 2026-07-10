@extends('layouts.app')

@section('title', 'Gamejam 2026 | IUT ICT Fest Game Development Competition')
@section('meta_description', 'Enter Gamejam at IUT 12th ICT FEST 2026: build a game online, submit a pitch, and qualify for the onsite final showcase at IUT.')
@section('canonical', url('/gamejam'))
@section('og_image', asset('assets/logos/gamejam.png'))

@push('head')
    @include('partials.seo.event-jsonld', [
        'name' => 'Gamejam - IUT 12th ICT FEST 2026',
        'description' => 'A game development competition with an online build round, pitch submission, and onsite final showcase.',
        'url' => url('/gamejam'),
        'image' => asset('assets/logos/gamejam.png'),
        'startDate' => '2026-07-13T00:00:00+06:00',
        'endDate' => '2026-07-25T13:00:00+06:00',
        'locationName' => 'Islamic University of Technology',
        'registrationUrl' => route('gamejam.register'),
        'price' => 0,
        'isAvailable' => (bool) ($eventRecord?->is_live ?? false),
    ])
@endpush

@section('content')
@php
    $facts = [
        ['label' => 'Preliminary Fee', 'value' => 'No fees', 'icon' => 'fa-ticket'],
        ['label' => 'Final Round Fee', 'value' => '700 BDT per team', 'icon' => 'fa-receipt'],
        ['label' => 'Team Size', 'value' => '1-3 members', 'icon' => 'fa-users'],
        ['label' => 'Final Round', 'value' => '10 AM - 1 PM, 25 July', 'icon' => 'fa-flag-checkered'],
    ];

    $timeline = [
        [
            'date' => '17 June - 10 July',
            'label' => 'Registration Closed',
            'accent' => 'volt',
            'icon' => 'fa-pen-to-square',
            'items' => [
                ['value' => '17 June', 'label' => 'Opens', 'icon' => 'fa-door-open'],
                ['value' => '10 July', 'label' => 'Closed', 'icon' => 'fa-lock'],
            ],
        ],
        [
            'date' => '13-19 July',
            'label' => 'Online Round',
            'accent' => 'iris',
            'icon' => 'fa-gamepad',
            'items' => [
                ['value' => '7 Days', 'label' => 'Build Window', 'icon' => 'fa-hourglass-half'],
                ['value' => 'Online', 'label' => 'Preliminary Round', 'icon' => 'fa-wifi'],
            ],
        ],
        [
            'date' => '20-21 July',
            'label' => 'Pitch Submission',
            'accent' => 'volt',
            'icon' => 'fa-circle-play',
            'items' => [
                ['value' => 'YouTube', 'label' => 'Video Submission', 'icon' => 'fa-circle-play'],
                ['value' => '21 July, 11:59 PM', 'label' => 'Bangladesh Time Deadline', 'icon' => 'fa-clock'],
            ],
        ],
        [
            'date' => '22-23 July',
            'label' => 'Finalist Phase',
            'accent' => 'ember',
            'icon' => 'fa-bullhorn',
            'items' => [
                ['value' => '22 July', 'label' => 'Finalists Announced', 'icon' => 'fa-list-check'],
                ['value' => '22-23 July', 'label' => 'Final Registration', 'icon' => 'fa-calendar-check'],
            ],
        ],
        [
            'date' => '25 July',
            'label' => 'Onsite Final',
            'accent' => 'volt',
            'icon' => 'fa-trophy',
            'items' => [
                ['value' => '10 AM', 'label' => 'Final Starts', 'icon' => 'fa-play'],
                ['value' => '1 PM', 'label' => 'Final Ends', 'icon' => 'fa-stop'],
            ],
        ],
    ];

    $rules = [
        'Team members can be from different universities.',
        'Graduates will not be allowed to participate.',
        'Every team should have a unique name.',
        'Once formed, team members cannot be substituted for the remainder of the competition.',
        'Any undergraduate student and at max on graduation can participate in the preliminary round with a team.',
        'From the preliminary round, 20 teams will be selected for the final onsite round.',
    ];

    $requirements = [
        ['title' => 'Project Files', 'text' => 'Participants should bring their project files for the onsite final.', 'icon' => 'fa-folder-open'],
        ['title' => 'Game Executable', 'text' => 'Teams should bring playable game executable files.', 'icon' => 'fa-gamepad'],
        ['title' => 'Laptop Accessories', 'text' => 'Mouse, keyboard, and other accessories may be required.', 'icon' => 'fa-computer-mouse'],
        ['title' => 'Lab Showcase Option', 'text' => 'Participants may choose to showcase games on lab computers.', 'icon' => 'fa-desktop'],
    ];
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8 lg:pb-24">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-center gap-12 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,.65fr)]">
            <div class="order-2 max-w-4xl lg:order-1">
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-volt/70"></span>
                    Gamejam
                </div>
                <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    Build a game, then bring it to the floor.
                </h1>
                <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                    A game development challenge with an online creation round, a finalist selection, and an onsite showcase for the selected teams. IUT 12th ICT FEST 2026 Gamejam powered by <a href="https://batterylowglobal.com/en" style="color:#c94045">Battery Low Interactive Limited</a>
                </p>
                <div class="mt-10">
                    <a href="{{ route('events.rulebook', ['eventSlug' => 'gamejam']) }}" class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-volt">
                        See Rulebook
                    </a>
                </div>
            </div>

            <div class="order-1 flex justify-center lg:order-2 lg:justify-end">
                <img src="{{ asset('assets/logos/gamejam_sponsor.png') }}" alt="Gamejam" class="w-full max-w-sm object-contain">
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

<section class="px-4 pb-20 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-5 md:grid-cols-2">
        <a href="https://itch.io/jam/12th-iut-ict-fest-2026-gamejam" target="_blank" rel="noopener noreferrer" class="group rounded-lg border border-volt/25 bg-volt/10 p-6 transition hover:-translate-y-0.5 hover:border-volt/45 hover:bg-volt/15 sm:p-7">
            <div class="flex items-start justify-between gap-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.2em] text-volt/85">Competition Link</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Submit on itch.io</h2>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-white/62">
                        Open the official GameJam page for submissions, updates, and online round materials.
                    </p>
                </div>
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-white/10 text-volt transition group-hover:bg-volt group-hover:text-ink">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </span>
            </div>
            <p class="mt-6 break-all text-sm font-semibold text-white">itch.io/jam/12th-iut-ict-fest-2026-gamejam</p>
        </a>

        <a href="https://discord.gg/kXEfDVcRx2" target="_blank" rel="noopener noreferrer" class="group rounded-lg border border-white/10 bg-white/[.035] p-6 transition hover:-translate-y-0.5 hover:border-iris/35 hover:bg-white/[.055] sm:p-7">
            <div class="flex items-start justify-between gap-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.2em] text-iris/85">Discord</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Join the GameJam server</h2>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-white/62">
                        Use the Discord server for announcements, coordination, and submission support.
                    </p>
                </div>
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-white/10 text-iris transition group-hover:bg-iris group-hover:text-white">
                    <i class="fa-brands fa-discord"></i>
                </span>
            </div>
            <p class="mt-6 break-all text-sm font-semibold text-white">discord.gg/kXEfDVcRx2</p>
        </a>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-16 lg:grid-cols-[.75fr_1.25fr]">
            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Gamejam Timeline</p>
                <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">From online build to onsite showcase.</h2>
            </div>

            <div class="relative">
                <div class="absolute left-4 top-4 hidden h-[calc(100%-2rem)] w-px bg-gradient-to-b from-volt/70 via-white/14 to-ember/70 sm:block"></div>

                @foreach($timeline as $block)
                    <div class="relative grid gap-5 pb-12 last:pb-0 sm:grid-cols-[4rem_1fr]">
                        <div class="relative z-10 grid h-8 w-8 place-items-center rounded-full text-xs ring-8 ring-ink sm:mt-2 {{ $block['accent'] === 'iris' ? 'bg-iris/10 text-iris' : ($block['accent'] === 'ember' ? 'bg-ember/10 text-ember' : 'bg-volt/10 text-volt') }}">
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
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-md {{ $block['accent'] === 'iris' ? 'bg-iris/10 text-iris' : ($block['accent'] === 'ember' ? 'bg-ember/10 text-ember' : 'bg-volt/10 text-volt') }}">
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
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Rules</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">How teams enter and continue.</h2>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
            <div class="grid gap-4">
                @foreach($rules as $item)
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
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Showcase Kit</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">What finalists should bring.</h2>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
            <div class="grid gap-5">
                @foreach($requirements as $item)
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
        <p class="text-xs font-medium uppercase tracking-[.22em] text-volt/80">Gamejam Registration</p>
        <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Registration closed on 10 July.</h2>
        <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-white/56">
            Registered teams should follow the online round, submit through itch.io, and prepare their files for the onsite showcase.
        </p>
        <div class="mt-10 flex justify-center">
            @if($eventRecord?->is_live)
                <a href="{{ route('gamejam.register') }}" class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-volt">
                    Register Now
                </a>
            @else
                <span class="inline-flex items-center justify-center gap-3 rounded-md border border-white/12 bg-white/[.04] px-5 py-3 text-sm font-semibold text-white/58">
                    Registration Closed
                </span>
            @endif
        </div>
    </div>
</section>
@endsection
