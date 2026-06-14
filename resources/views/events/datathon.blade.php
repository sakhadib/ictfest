@extends('layouts.app')

@section('title', 'Datathon | '.config('app.name'))

@section('content')
@php
    $facts = [
        ['label' => 'Registration Fee', 'value' => '800 BDT per team', 'icon' => 'fa-ticket'],
        ['label' => 'Team Size', 'value' => '1-4 members', 'icon' => 'fa-users'],
        ['label' => 'Timeline', 'value' => '18 June - 13 July', 'icon' => 'fa-calendar-days'],
        ['label' => 'Final Round', 'value' => '10 AM - 3 PM, 25 July', 'icon' => 'fa-flag-checkered'],
    ];

    $timeline = [
        [
            'date' => '18-30 June',
            'label' => 'First Stage Registration',
            'accent' => 'iris',
            'icon' => 'fa-pen-to-square',
            'items' => [
                ['value' => '18 June', 'label' => 'Opens', 'icon' => 'fa-door-open'],
                ['value' => '30 June', 'label' => 'First Deadline', 'icon' => 'fa-lock'],
            ],
        ],
        /*
        [
            'date' => '1-13 July',
            'label' => 'Extended Registration',
            'accent' => 'volt',
            'icon' => 'fa-calendar-plus',
            'items' => [
                ['value' => '1 July', 'label' => 'Extension Starts', 'icon' => 'fa-forward'],
                ['value' => '13 July', 'label' => 'Final Deadline', 'icon' => 'fa-hourglass-end'],
            ],
        ],
        */
        [
            'date' => '1-20 July',
            'label' => 'First Round',
            'accent' => 'ember',
            'icon' => 'fa-chart-line',
            'items' => [
                ['value' => '20 Days', 'label' => 'Preliminary', 'icon' => 'fa-chart-simple'],
                ['value' => '21 July', 'label' => 'Paper & Code Submission', 'icon' => 'fa-file-arrow-up'],
            ],
        ],
        [
            'date' => '23-25 July',
            'label' => 'Final Stage',
            'accent' => 'iris',
            'icon' => 'fa-trophy',
            'items' => [
                ['value' => '23 July', 'label' => 'Finalists Announced', 'icon' => 'fa-bullhorn'],
                ['value' => '10 AM - 3 PM', 'label' => '25 July Onsite Final', 'icon' => 'fa-clock'],
            ],
        ],
    ];

    $eligibility = [
        'Participants from any department of any university are allowed.',
        'Cross-university teams are allowed in the competition.',
        'Eligible participants include 1st year to 4th year students.',
        'Each participant needs a current university ID for verification.',
        'Every squad needs to register before participating and have a team name.',
    ];

    $requirements = [
        ['title' => 'Student ID Card', 'text' => 'Required for participant verification.', 'icon' => 'fa-id-card'],
        ['title' => 'Laptop and Charger', 'text' => 'Teams must bring a working laptop with its charger.', 'icon' => 'fa-laptop'],
        ['title' => 'Presentation Slide', 'text' => 'Prepare slides for final round explanation and judging.', 'icon' => 'fa-display'],
        ['title' => 'Report', 'text' => 'Submit a written report with the solution.', 'icon' => 'fa-file-lines'],
        ['title' => 'Code and Notebooks', 'text' => 'Bring training and inference notebooks or code repository.', 'icon' => 'fa-code'],
    ];
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8 lg:pb-24">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-center gap-12 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,.65fr)]">
            <div class="order-2 max-w-4xl lg:order-1">
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-iris/70"></span>
                    Datathon
                </div>
                <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    Turn data into a defensible story.
                </h1>
                <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                    A university-level data competition where teams move from analysis and modeling to paper, codebase, report, slides, and an onsite final.
                </p>
            </div>

            <div class="order-1 flex justify-center lg:order-2 lg:justify-end">
                <img src="{{ asset('assets/logos/datathon.png') }}" alt="Datathon" class="w-full max-w-sm object-contain">
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

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-16 lg:grid-cols-[.75fr_1.25fr]">
            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Datathon Timeline</p>
                <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">A full track from registration to final defense.</h2>
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
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Eligibility</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Who can participate.</h2>
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

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[.9fr_1.1fr]">
        <div>
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Final Round Kit</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">What teams should bring.</h2>
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
        <p class="text-xs font-medium uppercase tracking-[.22em] text-iris/80">Datathon Registration</p>
        <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Ready to prove your model?</h2>
        <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-white/56">
            Register your squad, prepare your notebooks, and keep your submission materials ready before the deadlines.
        </p>
        <div class="mt-10 flex justify-center">
            <a href="{{ route('datathon.register') }}" class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-iris">
                Register Now
            </a>
        </div>
    </div>
</section>
@endsection
