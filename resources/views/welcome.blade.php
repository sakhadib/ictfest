@extends('layouts.app')

@section('content')
@php
    try {
        $eventRecords = \App\Models\Event::whereIn('code', ['01', '02', '03', '04', '05', '06'])
            ->get()
            ->keyBy('code');
    } catch (\Throwable $exception) {
        $eventRecords = collect();
    }

    $events = [
        [
            'code' => '01',
            'name' => 'Programming Contest',
            'short' => 'IUPC',
            'logo' => 'iupc.png',
            'icon' => 'fa-code',
            'accent' => 'volt',
            'fee' => '5099 BDT per team',
            'team' => '3 members',
            'window' => '15 June - 30 June',
            'date' => 'Mock: 24 July, Main: 25 July',
            'venue' => 'AB2 CSE Labs 1-6, ICT Center Labs',
            'signal' => 'Five-hour main contest with onsite lab pressure and a dedicated prize giving slot.',
            'url' => '/iupc',
            'register_url' => '/iupc/register',
            'start_date' => '2026-07-24T15:00:00+06:00',
            'end_date' => '2026-07-25T17:00:00+06:00',
            'price' => 5099,
        ],
        [
            'code' => '02',
            'name' => 'Agentic AI Hackathon',
            'short' => 'Hackathon',
            'logo' => 'hackathon.png',
            'icon' => 'fa-plug-circle-bolt',
            'accent' => 'ember',
            'fee' => 'Free preliminary, 1500 BDT final',
            'team' => '1-3 members',
            'window' => '19 June - 3 July',
            'date' => 'Preli: 9 July, 6-10 PM; Final: 24-25 July',
            'venue' => 'AB2 301, 302 & Auditorium',
            'signal' => 'Two rounds of Hackathon, 4-hour online preli on 9 July, then onsite final build.',
            'url' => '/hackathon',
            'register_url' => '/hackathon/register',
            'start_date' => '2026-07-09T18:00:00+06:00',
            'end_date' => '2026-07-24T18:00:00+06:00',
            'price' => 0,
        ],
        [
            'code' => '03',
            'name' => 'Datathon',
            'short' => 'Datathon',
            'logo' => 'datathon.png',
            'icon' => 'fa-chart-line',
            'accent' => 'iris',
            'fee' => '600 BDT per team',
            'team' => '1-4 members',
            'window' => '19 June - 15 July',
            'date' => 'Final: 10 AM - 3 PM, 25 July',
            'venue' => 'Onsite final round',
            'signal' => 'Phase II registration stays open until 15 July while teams work toward papers, code, notebooks, and the onsite final.',
            'url' => '/datathon',
            'register_url' => '/datathon/register',
            'start_date' => '2026-07-01T00:00:00+06:00',
            'end_date' => '2026-07-25T15:00:00+06:00',
            'price' => 600,
        ],
        [
            'code' => '04',
            'name' => 'Gamejam',
            'short' => 'Gamejam',
            'logo' => 'gamejam.png',
            'icon' => 'fa-gamepad',
            'accent' => 'volt',
            'fee' => 'Free preliminary, 700 BDT final',
            'team' => '1-3 members',
            'window' => '17 June - 10 July',
            'date' => 'Online: 14-19 July, Final: 25 July',
            'venue' => 'Onsite showcase',
            'signal' => 'Online build round selects 15 teams for the final onsite showcase.',
            'url' => '/gamejam',
            'register_url' => '/gamejam/register',
            'start_date' => '2026-07-13T00:00:00+06:00',
            'end_date' => '2026-07-25T13:00:00+06:00',
            'price' => 0,
        ],
        [
            'code' => '05',
            'name' => 'FIFA',
            'short' => 'EA FC 26',
            'logo' => 'fifa.png',
            'icon' => 'fa-futbol',
            'accent' => 'ember',
            'fee' => '200 BDT',
            'team' => '1 member',
            'window' => '18 June - 3 July',
            'date' => '24 July, 9 AM - 6 PM',
            'venue' => 'Auditorium',
            'signal' => '64-player PC controller-only tournament, escalating to best-of-five finals.',
            'url' => '/fifa',
            'register_url' => '/fifa/register',
            'start_date' => '2026-07-24T09:00:00+06:00',
            'end_date' => '2026-07-24T18:00:00+06:00',
            'price' => 200,
        ],
        [
            'code' => '06',
            'name' => 'Valorant',
            'short' => 'Valorant',
            'logo' => 'valorant.png',
            'icon' => 'fa-crosshairs',
            'accent' => 'iris',
            'fee' => '600 BDT per team',
            'team' => '5-7 members',
            'window' => '18 June - 30 June',
            'date' => 'Online: 4-24 July, LAN final: 25 July',
            'venue' => 'Final onsite LAN',
            'signal' => '32-team path through knockout and double elimination into a LAN grand finale.',
            'url' => '/valorant',
            'register_url' => '/valorant/register',
            'start_date' => '2026-07-04T00:00:00+06:00',
            'end_date' => '2026-07-25T18:00:00+06:00',
            'price' => 600,
        ],
    ];

    $accentClasses = [
        'volt' => 'text-volt bg-volt/10',
        'ember' => 'text-ember bg-ember/10',
        'iris' => 'text-iris bg-iris/10',
    ];

    $prizePools = [
        [
            'code' => '01',
            'name' => 'IUPC',
            'logo' => 'iupc.png',
            'amount' => '175K BDT',
            'detail' => 'Across top 15 teams',
            'accent' => 'volt',
        ],
        [
            'code' => '02',
            'name' => 'Agentic AI Hackathon',
            'logo' => 'hackathon.png',
            'amount' => '100K BDT',
            'detail' => 'Plus a serious pool of Codex usage credits, kept under wraps for now.',
            'accent' => 'ember',
            'featured' => true,
        ],
        [
            'code' => '03',
            'name' => 'Datathon',
            'logo' => 'datathon.png',
            'amount' => '120K BDT',
            'detail' => 'Across top 5 teams',
            'accent' => 'iris',
        ],
        [
            'code' => '04',
            'name' => 'Gamejam',
            'logo' => 'gamejam.png',
            'amount' => '75K BDT',
            'detail' => 'Across top 3 teams',
            'accent' => 'volt',
        ],
        [
            'code' => '05',
            'name' => 'FIFA',
            'logo' => 'fifa.png',
            'amount' => '30K BDT',
            'detail' => 'Across top 3 players',
            'accent' => 'ember',
        ],
        [
            'code' => '06',
            'name' => 'Valorant',
            'logo' => 'valorant.png',
            'amount' => '50K BDT',
            'detail' => 'For the winning esports roster',
            'accent' => 'iris',
        ],
    ];
@endphp

@section('title', 'IUT 12th ICT FEST 2026 | Programming, AI, Data, Game Dev & Esports')
@section('meta_description', 'Join IUT 12th ICT FEST 2026 at Islamic University of Technology: six university competitions with 550K+ BDT prize pool, Codex credits, and onsite finals.')
@section('canonical', url('/'))
@section('og_image', asset('assets/logo-white.png'))

@push('head')
@php
    $prizePoolLookup = collect($prizePools)->keyBy('code');

    $homeStructuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => url('/').'#organization',
                'name' => 'IUT Computer Society',
                'url' => url('/'),
                'logo' => asset('assets/logo-white.png'),
                'sameAs' => [
                    'https://www.facebook.com/IUTCS/',
                    'https://bd.linkedin.com/company/iutcs',
                    'https://www.youtube.com/channel/UCPVwRaP-wK6lSUEqTK7iLng',
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/').'#website',
                'name' => config('app.name'),
                'url' => url('/'),
                'publisher' => ['@id' => url('/').'#organization'],
            ],
            [
                '@type' => 'Event',
                '@id' => url('/').'#event',
                'name' => 'IUT 12th ICT FEST 2026',
                'description' => 'A university technology festival featuring programming, AI, data science, game development, FIFA, and Valorant competitions.',
                'url' => url('/'),
                'image' => [asset('assets/logo-white.png')],
                'startDate' => '2026-07-24T08:00:00+06:00',
                'endDate' => '2026-07-25T18:00:00+06:00',
                'eventStatus' => 'https://schema.org/EventScheduled',
                'eventAttendanceMode' => 'https://schema.org/MixedEventAttendanceMode',
                'organizer' => ['@id' => url('/').'#organization'],
                'location' => [
                    '@type' => 'Place',
                    'name' => 'Islamic University of Technology',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => 'Gazipur',
                        'addressCountry' => 'BD',
                    ],
                ],
                'subEvent' => collect($events)->map(fn (array $event): array => [
                    '@type' => 'Event',
                    'name' => $event['short'].' - '.$event['name'],
                    'description' => $event['signal'],
                    'url' => url($event['url']),
                    'image' => asset('assets/logos/'.$event['logo']),
                    'award' => trim(data_get($prizePoolLookup->get($event['code']), 'amount', '').' '.data_get($prizePoolLookup->get($event['code']), 'detail', '')),
                    'startDate' => $event['start_date'],
                    'endDate' => $event['end_date'],
                    'eventStatus' => 'https://schema.org/EventScheduled',
                    'eventAttendanceMode' => 'https://schema.org/MixedEventAttendanceMode',
                    'organizer' => ['@id' => url('/').'#organization'],
                    'location' => [
                        '@type' => 'Place',
                        'name' => $event['venue'],
                        'address' => [
                            '@type' => 'PostalAddress',
                            'addressLocality' => 'Gazipur',
                            'addressCountry' => 'BD',
                        ],
                    ],
                    'offers' => [
                        '@type' => 'Offer',
                        'url' => url($event['register_url']),
                        'price' => $event['price'],
                        'priceCurrency' => 'BDT',
                        'availability' => (($eventRecords->get($event['code'])?->is_live ?? false) && ! (($eventRecords->get($event['code'])?->hasSlotLimit() ?? false) && ! $eventRecords->get($event['code'])?->hasAvailableSlots()))
                            ? 'https://schema.org/InStock'
                            : 'https://schema.org/SoldOut',
                    ],
                ])->values()->all(),
            ],
            [
                '@type' => 'ItemList',
                '@id' => url('/').'#prize-pool',
                'name' => 'IUT 12th ICT FEST 2026 Prize Pool',
                'description' => '550K+ BDT total prize money across IUPC, Agentic AI Hackathon, Datathon, Gamejam, FIFA, and Valorant, plus Codex usage credits for the Hackathon.',
                'url' => url('/#prize-pool'),
                'numberOfItems' => count($prizePools),
                'itemListElement' => collect($prizePools)->map(fn (array $pool, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $pool['name'].' Prize Pool',
                    'item' => [
                        '@type' => 'Thing',
                        'name' => $pool['name'],
                        'image' => asset('assets/logos/'.$pool['logo']),
                        'description' => trim($pool['amount'].' '.$pool['detail']),
                    ],
                ])->values()->all(),
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($homeStructuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@push('styles')
<style>
    .liquid-field {
        filter: blur(34px) saturate(1.25);
        mix-blend-mode: screen;
        opacity: .38;
    }

    @media (prefers-reduced-motion: reduce) {
        .liquid-field {
            display: none;
        }
    }
</style>
@endpush

<canvas id="liquidField" class="liquid-field pointer-events-none fixed inset-0 z-0 h-full w-full"></canvas>

<section id="home" class="relative z-10 px-4 pb-28 pt-36 sm:px-6 lg:px-8 lg:pb-36">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-center gap-16 lg:grid-cols-[1.05fr_.95fr]">
            <div class="order-2 max-w-3xl lg:order-1">
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-volt/70"></span>
                    {{ config('app.name') }}
                </div>
                <h1 class="mt-8 max-w-4xl text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    A quieter stage for sharp minds and competitive craft.
                </h1>
                <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                    Six events across code, data, game development, and esports. Built around the IUT campus, online qualifiers, and a focused July finale.
                </p>

                <div class="mt-12 flex flex-wrap gap-4">
                    <a href="#events" class="inline-flex items-center gap-3 bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-volt">
                        Explore events
                    </a>
                </div>
            </div>

            <div class="order-1 relative flex justify-center lg:order-2 lg:justify-end">
                <div class="relative w-full max-w-xl rounded-[2rem] px-10 py-12">
                    <img src="{{ asset('assets/main_sp.png') }}" alt="{{ config('app.name') }}" class="mx-auto w-full max-w-md drop-shadow-[0_24px_80px_rgba(255,255,255,.10)]">
                    <!-- <p class="mt-8 text-center text-xs font-medium uppercase tracking-[.22em] text-white/42">{{ config('app.name') }}</p> -->
                </div>
            </div>
        </div>

        <div class="mt-24 grid grid-cols-2 gap-6 py-8 sm:grid-cols-3 lg:grid-cols-6 lg:gap-8">
            @foreach([
                ['file' => 'iupc.png', 'alt' => 'IUPC', 'url' => '/iupc'],
                ['file' => 'hackathon.png', 'alt' => 'Agentic AI Hackathon', 'url' => '/hackathon'],
                ['file' => 'datathon.png', 'alt' => 'Datathon', 'url' => '/datathon'],
                ['file' => 'gamejam.png', 'alt' => 'Game Jam', 'url' => '/gamejam'],
                ['file' => 'fifa.png', 'alt' => 'FIFA', 'url' => '/fifa'],
                ['file' => 'valorant.png', 'alt' => 'Valorant', 'url' => '/valorant'],
            ] as $logo)
                <a href="{{ url($logo['url']) }}" aria-label="View {{ $logo['alt'] }} details" class="flex items-center justify-center px-3 py-4 opacity-78 transition hover:-translate-y-0.5 hover:opacity-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40">
                    <img
                        src="{{ asset('assets/logos/' . $logo['file']) }}"
                        alt="{{ $logo['alt'] }}"
                        class="h-12 w-full max-w-[8.5rem] object-contain sm:h-14 lg:h-16"
                    >
                </a>
            @endforeach
        </div>
    </div>
</section>

<section id="events" class="relative z-10 px-4 py-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-2xl">
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Events</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Six ways to enter the festival.</h2>
        </div>

        <div class="mt-16 grid items-stretch gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($events as $event)
                @php
                    $eventRecord = $eventRecords[$event['code']] ?? null;
                    $eventIsLive = (bool) ($eventRecord?->is_live ?? false);
                    $remainingSlots = $eventRecord?->remainingSlots();
                    $slotLimit = $eventRecord?->slotLimit();
                @endphp
                <article class="group flex h-full min-h-[29rem] flex-col rounded-lg border border-white/10 bg-white/[.035] p-5 transition duration-300 hover:-translate-y-1 hover:border-white/24 hover:bg-white/[.055]">
                    <div class="flex items-start gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-md bg-white/[.06] p-1">
                            <img src="{{ asset('assets/logos/' . $event['logo']) }}" alt="{{ $event['name'] }}" class="h-full w-full object-contain">
                        </div>
                        <div>
                            <h3 class="mt-2 text-xl font-semibold leading-tight text-white">{{ $event['name'] }}</h3>
                        </div>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-white/58">{{ $event['signal'] }}</p>

                    <div class="mt-5 grid gap-3 border-t border-white/10 pt-5">
                        <div class="grid min-h-14 grid-cols-[2.25rem_1fr] items-start gap-3">
                            <div class="grid h-9 w-9 place-items-center rounded-md bg-white/[.055] text-white/46">
                                <i class="fa-solid fa-users text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-[.16em] text-white/32">Team</p>
                                <p class="mt-1 text-sm font-medium leading-5 text-white/78">{{ $event['team'] }}</p>
                            </div>
                        </div>

                        <div class="grid min-h-14 grid-cols-[2.25rem_1fr] items-start gap-3">
                            <div class="grid h-9 w-9 place-items-center rounded-md bg-white/[.055] text-white/46">
                                <i class="fa-solid fa-calendar-days text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-[.16em] text-white/32">Date</p>
                                <p class="mt-1 text-sm font-medium leading-5 text-white/78">{{ $event['date'] }}</p>
                            </div>
                        </div>

                        <div class="grid min-h-14 grid-cols-[2.25rem_1fr] items-start gap-3">
                            <div class="grid h-9 w-9 place-items-center rounded-md bg-white/[.055] text-white/46">
                                <i class="fa-solid fa-ticket text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-[.16em] text-white/32">Fee</p>
                                <p class="mt-1 text-sm font-medium leading-5 text-white/78">{{ $event['fee'] }}</p>
                            </div>
                        </div>

                        <div class="grid min-h-14 grid-cols-[2.25rem_1fr] items-start gap-3">
                            <div class="grid h-9 w-9 place-items-center rounded-md bg-white/[.055] text-white/46">
                                <i class="fa-solid fa-location-dot text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-[.16em] text-white/32">Venue</p>
                                <p class="mt-1 text-sm font-medium leading-5 text-white/78">{{ $event['venue'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 {{ isset($event['register_url']) ? 'sm:grid-cols-2' : '' }}">
                        @if(isset($event['url']))
                            <a href="{{ url($event['url']) }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-white/12 bg-white/[.04] px-4 py-2.5 text-sm font-medium text-white/72 transition hover:border-volt/60 hover:text-white">
                                Details
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        @endif

                        @if(isset($event['register_url']) && $eventIsLive && ($remainingSlots === null || $remainingSlots > 0))
                            <a href="{{ url($event['register_url']) }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-volt">
                                {{ $event['name'] === 'Programming Contest' ? 'Pre-register' : 'Register' }}
                            </a>
                        @elseif(isset($event['register_url']) && $eventIsLive)
                            <span class="inline-flex items-center justify-center gap-2 rounded-md border border-white/12 bg-white/[.04] px-4 py-2.5 text-sm font-semibold text-white/58">
                                Slots full
                            </span>
                        @elseif(isset($event['register_url']))
                            <span class="inline-flex items-center justify-center gap-2 rounded-md border border-white/12 bg-white/[.04] px-4 py-2.5 text-sm font-semibold text-white/58">
                                Registration Closed
                            </span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="prize-pool" class="relative z-10 overflow-hidden px-4 py-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-10 lg:grid-cols-[.82fr_1.18fr] lg:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.24em] text-[#ff9a92]">Prize Pool</p>
                <h2 class="mt-5 text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
                    550K+ BDT, credits, and serious campus bragging rights.
                </h2>
                <!-- <p class="mt-6 max-w-xl text-base leading-8 text-white/62">
                    Every track has a focused reward pool, from deep team ladders to individual esports finishes. The Agentic AI Hackathon adds Codex usage credits on top, with the exact scale staying undisclosed for now.
                </p> -->
            </div>

            <div class="rounded-lg border border-[#d4574e]/35 bg-[#d4574e]/10 p-6 shadow-[0_30px_110px_rgba(212,87,78,.22)] sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[.22em] text-white/48">Cash Pool</p>
                        <p class="mt-3 text-5xl font-semibold leading-none text-white sm:text-6xl">550K</p>
                        <p class="mt-3 text-sm font-semibold uppercase tracking-[.2em] text-[#ffb0aa]">BDT total prize money</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/20 px-5 py-4">
                        <p class="text-sm leading-6 text-white/70">
                            + Codex credits for Hackathon winners
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($prizePools as $pool)
                @php
                    $accent = $pool['accent'] === 'ember'
                        ? 'border-[#d4574e]/35 bg-[#d4574e]/12 text-[#ffaaa3]'
                        : ($pool['accent'] === 'iris'
                            ? 'border-iris/30 bg-iris/10 text-iris'
                            : 'border-volt/30 bg-volt/10 text-volt');
                @endphp
                <article class="group relative overflow-hidden rounded-lg border border-white/10 bg-white/[.04] p-5 transition hover:-translate-y-1 hover:border-white/24 hover:bg-white/[.06] sm:p-6">
                    <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/5 blur-2xl transition group-hover:bg-[#d4574e]/15"></div>
                    <div class="relative flex items-start justify-between gap-5">
                        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-md bg-white/[.06] p-2">
                            <img src="{{ asset('assets/logos/' . $pool['logo']) }}" alt="{{ $pool['name'] }}" class="h-full w-full object-contain">
                        </div>
                        <span class="{{ $accent }} rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[.16em]">
                            Prize
                        </span>
                    </div>

                    <div class="relative mt-8">
                        <h3 class="text-xl font-semibold text-white">{{ $pool['name'] }}</h3>
                        <p class="mt-5 text-4xl font-semibold leading-none text-white sm:text-5xl">{{ $pool['amount'] }}</p>
                        @if($pool['featured'] ?? false)
                            <p class="mt-4 inline-flex w-fit items-center gap-2 rounded-md border border-[#d4574e]/30 bg-[#d4574e]/12 px-3 py-2 text-sm font-semibold text-[#ffb0aa]">
                                <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                                + Mystery Codex credits
                            </p>
                        @endif
                        <p class="mt-5 text-sm leading-7 text-white/58">{{ $pool['detail'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="schedule" class="relative z-10 px-4 py-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-16 lg:grid-cols-[.75fr_1.25fr]">
            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Timeline</p>
                <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">The festival unfolds slowly, then all at once.</h2>
            </div>

            <div class="relative">
                <div class="absolute left-4 top-4 hidden h-[calc(100%-2rem)] w-px bg-gradient-to-b from-volt/70 via-white/14 to-ember/70 sm:block"></div>

                @foreach([
                    ['date' => '15 June', 'label' => 'Registration opens', 'text' => 'Teams start reserving their place across programming, data, game development, hackathon, and esports tracks.', 'icon' => 'fa-door-open', 'accent' => 'volt'],
                    ['date' => '1-21 July', 'label' => 'Online rounds', 'text' => 'Remote rounds, submissions, and qualifiers shape the finalist pool before the campus finale.', 'icon' => 'fa-laptop-code', 'accent' => 'iris'],
                    ['date' => '24-25 July', 'label' => 'IUT campus finals', 'text' => 'Onsite contests, showcases, LAN finals, and prize moments bring the festival into one compact finish.', 'icon' => 'fa-flag-checkered', 'accent' => 'ember'],
                ] as $index => $item)
                    <div class="relative grid gap-5 pb-12 last:pb-0 sm:grid-cols-[4rem_1fr]">
                        <div class="{{ $accentClasses[$item['accent']] }} relative z-10 grid h-8 w-8 place-items-center rounded-full text-xs ring-8 ring-ink sm:mt-2">
                            <i class="fa-solid {{ $item['icon'] }}"></i>
                        </div>

                        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[.18em] text-white/35">{{ $item['date'] }}</p>
                                    <h3 class="mt-2 text-xl font-semibold text-white">{{ $item['label'] }}</h3>
                                </div>
                                <span class="w-fit rounded-full border border-white/10 px-3 py-1 text-xs text-white/42">Step {{ $index + 1 }}</span>
                            </div>
                            <p class="mt-6 max-w-xl text-sm leading-7 text-white/56">{{ $item['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section id="about" class="relative z-10 px-4 pb-32 pt-12 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl rounded-lg border border-white/10 bg-white/[.035] px-6 py-16 text-center sm:px-10">
        <p class="text-xs font-medium uppercase tracking-[.22em] text-volt/80">Join the festival</p>
        <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Are you ready to participate ?</h2>
        <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-white/56">
            Choose your event, form your team, and get ready for the July rounds at {{ config('app.name') }}.
        </p>

        <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a href="#" class="inline-flex w-full items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-volt sm:w-auto">
                Register Now
            </a>
            <a href="https://www.facebook.com/share/1cA7Hr4JZV/" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center gap-3 rounded-md border border-white/12 bg-white/[.04] px-5 py-3 text-sm font-medium text-white/72 transition hover:border-[#1877F2]/60 hover:text-white sm:w-auto">
                <i class="fa-brands fa-facebook-f"></i>
                Visit Facebook Page
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    (() => {
        const canvas = document.getElementById('liquidField');

        if (!canvas || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const ctx = canvas.getContext('2d');
        const colors = [
            ['rgba(255, 58, 90, .19)', 'rgba(255, 116, 86, .05)'],
            ['rgba(68, 130, 255, .19)', 'rgba(81, 213, 255, .05)'],
            ['rgba(135, 92, 255, .15)', 'rgba(255, 76, 146, .045)'],
            ['rgba(28, 186, 255, .13)', 'rgba(255, 60, 70, .04)'],
        ];

        const blobs = colors.map((color, index) => ({
            color,
            radius: .21 + index * .038,
            speed: .00008 + index * .000025,
            phase: index * 1.9,
            points: 8 + index,
            pull: .05 + index * .018,
        }));
        const pointer = {
            x: window.innerWidth / 2,
            y: window.innerHeight / 2,
            targetX: window.innerWidth / 2,
            targetY: window.innerHeight / 2,
            active: false,
        };
        const ripples = [];

        const resize = () => {
            const ratio = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = Math.floor(window.innerWidth * ratio);
            canvas.height = Math.floor(window.innerHeight * ratio);
            canvas.style.width = `${window.innerWidth}px`;
            canvas.style.height = `${window.innerHeight}px`;
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        };

        const drawBlob = (blob, time, index) => {
            const width = window.innerWidth;
            const height = window.innerHeight;
            const driftX = width * (.5 + Math.sin(time * blob.speed + blob.phase) * .24);
            const driftY = height * (.48 + Math.cos(time * blob.speed * 1.23 + blob.phase) * .22);
            const influence = pointer.active ? blob.pull : blob.pull * .34;
            const centerX = driftX + (pointer.x - driftX) * influence;
            const centerY = driftY + (pointer.y - driftY) * influence;
            const baseRadius = Math.min(width, height) * blob.radius;
            const gradient = ctx.createRadialGradient(centerX, centerY, 0, centerX, centerY, baseRadius * 1.7);

            gradient.addColorStop(0, blob.color[0]);
            gradient.addColorStop(1, blob.color[1]);

            ctx.beginPath();

            for (let point = 0; point <= blob.points; point++) {
                const angle = (Math.PI * 2 * point) / blob.points;
                const wave = Math.sin(time * blob.speed * 2.8 + point * 1.7 + index) * .22;
                const ripple = Math.cos(time * blob.speed * 2.1 + point * 2.4) * .12;
                const radius = baseRadius * (1 + wave + ripple);
                const x = centerX + Math.cos(angle) * radius;
                const y = centerY + Math.sin(angle) * radius;

                if (point === 0) {
                    ctx.moveTo(x, y);
                } else {
                    const prevAngle = (Math.PI * 2 * (point - .5)) / blob.points;
                    const controlX = centerX + Math.cos(prevAngle) * radius * 1.08;
                    const controlY = centerY + Math.sin(prevAngle) * radius * 1.08;
                    ctx.quadraticCurveTo(controlX, controlY, x, y);
                }
            }

            ctx.closePath();
            ctx.fillStyle = gradient;
            ctx.fill();
        };

        const drawRipples = (time) => {
            for (let index = ripples.length - 1; index >= 0; index--) {
                const ripple = ripples[index];
                const age = time - ripple.startedAt;
                const progress = age / 1700;

                if (progress >= 1) {
                    ripples.splice(index, 1);
                    continue;
                }

                const radius = ripple.radius + progress * Math.min(window.innerWidth, window.innerHeight) * .34;
                const opacity = (1 - progress) * .2;
                const gradient = ctx.createRadialGradient(ripple.x, ripple.y, 0, ripple.x, ripple.y, radius);

                gradient.addColorStop(0, `rgba(255, 78, 104, ${opacity})`);
                gradient.addColorStop(.46, `rgba(79, 144, 255, ${opacity * .72})`);
                gradient.addColorStop(1, 'rgba(79, 144, 255, 0)');

                ctx.beginPath();
                ctx.arc(ripple.x, ripple.y, radius, 0, Math.PI * 2);
                ctx.fillStyle = gradient;
                ctx.fill();
            }
        };

        const render = (time) => {
            pointer.x += (pointer.targetX - pointer.x) * .045;
            pointer.y += (pointer.targetY - pointer.y) * .045;

            ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);
            ctx.globalCompositeOperation = 'lighter';
            blobs.forEach((blob, index) => drawBlob(blob, time, index));
            drawRipples(time);
            requestAnimationFrame(render);
        };

        resize();
        window.addEventListener('resize', resize);
        window.addEventListener('pointermove', (event) => {
            pointer.targetX = event.clientX;
            pointer.targetY = event.clientY;
            pointer.active = true;
        }, { passive: true });
        window.addEventListener('pointerleave', () => {
            pointer.active = false;
        });
        window.addEventListener('click', (event) => {
            ripples.push({
                x: event.clientX,
                y: event.clientY,
                radius: 24,
                startedAt: performance.now(),
            });

            if (ripples.length > 5) {
                ripples.shift();
            }
        });
        requestAnimationFrame(render);
    })();
</script>
@endpush
@endsection
