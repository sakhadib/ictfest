@extends('layouts.app')

@section('title', 'Day 1 Bus Routes | '.config('app.name'))
@section('meta_description', 'Day 1 bus routes, pickup points, and arrival times for IUT 12th ICT FEST 2026.')
@section('canonical', route('bus.day1'))

@push('styles')
    <style>
        @keyframes busFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes routeGlow {
            0% { opacity: .32; transform: scaleY(.96); }
            50% { opacity: .88; transform: scaleY(1); }
            100% { opacity: .32; transform: scaleY(.96); }
        }

        @keyframes stopIn {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bus-float {
            animation: busFloat 3.4s ease-in-out infinite;
        }

        .route-glow {
            animation: routeGlow 2.8s ease-in-out infinite;
            transform-origin: top;
        }

        .stop-card {
            animation: stopIn .55s ease both;
            animation-delay: calc(var(--stop-index) * 55ms);
        }
    </style>
@endpush

@section('content')
@php
    $routeStyles = [
        ['accent' => '#d4574e', 'soft' => 'rgba(212,87,78,.14)', 'icon' => 'fa-route'],
        ['accent' => '#7cf7d4', 'soft' => 'rgba(124,247,212,.12)', 'icon' => 'fa-location-dot'],
        ['accent' => '#f5c542', 'soft' => 'rgba(245,197,66,.14)', 'icon' => 'fa-bus-simple'],
    ];
    $coordinators = [
        1 => [
            ['name' => 'Abrar Faiyaz', 'phone' => '01778144476'],
            ['name' => 'Mahib Rahman', 'phone' => '01783077383'],
        ],
        2 => [
            ['name' => 'Farhan Fuad', 'phone' => '01839777080'],
            ['name' => 'Ayman Ahnaf', 'phone' => '01978020926'],
        ],
        3 => [
            ['name' => 'Nafis', 'phone' => '01745838897'],
        ],
    ];
    $formattedDate = \Carbon\Carbon::parse($date)->format('l, d F Y');
    $totalStops = $routes->sum(fn ($route) => count($route['stops'] ?? []));
@endphp

<section class="relative overflow-hidden px-4 pb-16 pt-32 sm:px-6 lg:px-8 lg:pb-20">
    <div class="absolute inset-x-0 top-20 -z-0 mx-auto h-72 max-w-5xl rounded-full bg-ember/10 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl">
        <div class="grid items-center gap-12 lg:grid-cols-[1.05fr_.95fr]">
            <div>
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-volt/70"></span>
                    Day 1 Transport
                </div>
                <h1 class="mt-8 max-w-4xl text-4xl font-semibold leading-[1.04] text-white sm:text-5xl lg:text-6xl">
                    Three bus routes heading to IUT.
                </h1>
                <p class="mt-7 max-w-2xl text-base leading-8 text-white/60">
                    Pick your nearest pickup point and be there before the listed time. All routes arrive at {{ $destination }} for the first day of {{ config('app.name') }}.
                </p>

                <div class="mt-9 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border border-white/10 bg-white/[.04] p-4">
                        <p class="text-xs font-medium uppercase tracking-[.18em] text-white/36">Date</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $formattedDate }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/[.04] p-4">
                        <p class="text-xs font-medium uppercase tracking-[.18em] text-white/36">Routes</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $routes->count() }} active routes</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/[.04] p-4">
                        <p class="text-xs font-medium uppercase tracking-[.18em] text-white/36">Stops</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $totalStops }} timed stops</p>
                    </div>
                </div>
            </div>

            <div class="bus-float rounded-[2rem] border border-white/10 bg-white/[.045] p-7 shadow-glow">
                <div class="rounded-[1.5rem] border border-white/10 bg-black/20 p-6">
                    <div class="flex items-center justify-between">
                        <span class="grid h-16 w-16 place-items-center rounded-2xl bg-ember text-white shadow-[0_20px_60px_rgba(212,87,78,.28)]">
                            <i class="fa-solid fa-bus-simple text-2xl"></i>
                        </span>
                        <span class="rounded-full border border-volt/30 bg-volt/10 px-4 py-2 text-sm font-semibold text-volt">Day 1</span>
                    </div>
                    <h2 class="mt-8 text-3xl font-semibold text-white">Arrive before 8:00 AM.</h2>
                    <p class="mt-4 text-sm leading-7 text-white/58">
                        The routes converge at IUT from Khilgaon, Dhanmondi, and Uttara corridors.
                    </p>
                    <div class="mt-7 space-y-3">
                        @foreach($routes as $route)
                            @php
                                $style = $routeStyles[$loop->index % count($routeStyles)];
                            @endphp
                            <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[.035] px-4 py-3">
                                <span class="flex items-center gap-3 text-sm font-semibold text-white">
                                    <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $style['accent'] }}"></span>
                                    {{ $route['name'] ?? 'Bus Route' }}
                                </span>
                                <span class="font-mono text-sm font-semibold text-white/70">{{ $route['arrival_time'] ?? '--:--' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-6 xl:grid-cols-3">
            @forelse($routes as $route)
                @php
                    $style = $routeStyles[$loop->index % count($routeStyles)];
                    $stops = collect($route['stops'] ?? []);
                    $firstStop = $stops->first();
                    $lastStop = $stops->last();
                @endphp

                <article class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[.04] p-5 transition duration-300 hover:-translate-y-1 hover:border-white/24 hover:bg-white/[.06] sm:p-6">
                    <div class="absolute inset-x-8 top-0 h-px" style="background: linear-gradient(90deg, transparent, {{ $style['accent'] }}, transparent);"></div>
                    <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full opacity-20 blur-3xl" style="background: {{ $style['accent'] }}"></div>

                    <div class="relative">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Route {{ $route['id'] ?? $loop->iteration }}</p>
                                <h2 class="mt-3 text-2xl font-semibold text-white">{{ $route['name'] ?? 'Bus Route' }}</h2>
                            </div>
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl text-white" style="background: {{ $style['soft'] }}; color: {{ $style['accent'] }};">
                                <i class="fa-solid {{ $style['icon'] }}"></i>
                            </span>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-white/10 bg-black/15 p-4">
                                <p class="text-xs uppercase tracking-[.16em] text-white/32">Starts</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ $firstStop['name'] ?? '---' }}</p>
                                <p class="mt-1 font-mono text-sm text-white/54">{{ $firstStop['time'] ?? '--:--' }}</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-black/15 p-4">
                                <p class="text-xs uppercase tracking-[.16em] text-white/32">Arrives</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ $lastStop['name'] ?? 'IUT' }}</p>
                                <p class="mt-1 font-mono text-sm text-white/54">{{ $route['arrival_time'] ?? ($lastStop['time'] ?? '--:--') }}</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-white/10 bg-white/[.035] p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-medium uppercase tracking-[.18em] text-white/38">Bus Coordinators</p>
                                <i class="fa-solid fa-headset text-sm" style="color: {{ $style['accent'] }}"></i>
                            </div>
                            <div class="mt-4 grid gap-3">
                                @foreach($coordinators[(int) ($route['id'] ?? $loop->iteration)] ?? [] as $coordinator)
                                    <a href="tel:{{ preg_replace('/\D+/', '', $coordinator['phone']) }}" class="flex items-center justify-between gap-4 rounded-lg border border-white/10 bg-black/15 px-4 py-3 transition hover:border-white/24 hover:bg-white/[.04]">
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-white">{{ $coordinator['name'] }}</span>
                                            <span class="mt-1 block font-mono text-sm text-white/58">{{ $coordinator['phone'] }}</span>
                                        </span>
                                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white/[.06] text-white/70">
                                            <i class="fa-solid fa-phone text-xs"></i>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="relative mt-7">
                            <div class="route-glow absolute bottom-4 left-[1.18rem] top-4 w-px" style="background: linear-gradient(180deg, {{ $style['accent'] }}, rgba(255,255,255,.12));"></div>

                            <div class="space-y-3">
                                @foreach($stops as $stop)
                                    <div class="stop-card relative grid grid-cols-[2.5rem_1fr_auto] items-center gap-3 rounded-xl border border-white/10 bg-black/15 px-3 py-3" style="--stop-index: {{ $loop->index }}">
                                        <span class="relative z-10 grid h-9 w-9 place-items-center rounded-full border border-white/10 bg-ink font-mono text-xs font-semibold text-white" style="box-shadow: 0 0 0 4px {{ $style['soft'] }};">
                                            {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block break-words text-sm font-semibold text-white">{{ $stop['name'] }}</span>
                                            @if(($stop['name'] ?? '') === 'IUT')
                                                <span class="mt-1 inline-flex rounded-full border border-volt/25 bg-volt/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[.14em] text-volt">Destination</span>
                                            @endif
                                        </span>
                                        <span class="rounded-lg border border-white/10 bg-white/[.04] px-3 py-2 font-mono text-sm font-semibold text-white/74">{{ $stop['time'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-white/14 bg-white/[.035] px-6 py-16 text-center xl:col-span-3">
                    <h2 class="text-2xl font-semibold text-white">Bus schedule unavailable.</h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-white/56">
                        The Day 1 route file could not be loaded. Please check back later.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
