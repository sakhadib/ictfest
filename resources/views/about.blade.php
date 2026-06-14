@extends('layouts.app')

@section('title', 'About IUTCS | '.config('app.name'))

@section('content')
@php
    $stats = [
        ['value' => '17+', 'label' => 'Years of Excellence'],
        ['value' => '1000+', 'label' => 'Active Members'],
        ['value' => '30+', 'label' => 'Events Annually'],
    ];

    $quickFacts = [
        'Founded in 2008',
        'Open to all IUT students',
        'Interdisciplinary collaboration',
        '9 specialized teams',
        'National recognition',
    ];

    $activities = [
        ['title' => 'Programming & Development Classes', 'text' => 'Regular ACM-standard programming classes and comprehensive sessions on app and web development.', 'icon' => 'fa-code', 'accent' => 'volt'],
        ['title' => 'Research Seminars', 'text' => 'Seminars with industry professionals and academics on emerging technologies and research.', 'icon' => 'fa-microscope', 'accent' => 'iris'],
        ['title' => 'Workshops & Projects', 'text' => 'Hands-on workshops and collaborative co-curricular projects built around practical experience.', 'icon' => 'fa-screwdriver-wrench', 'accent' => 'ember'],
    ];

    $events = [
        ['title' => 'Programming Contests', 'text' => 'Competitive coding events built to sharpen algorithmic thinking and problem solving.', 'icon' => 'fa-terminal'],
        ['title' => 'Hackathons', 'text' => 'Innovation marathons, including Bangladesh\'s first university-level game jam.', 'icon' => 'fa-bolt'],
        ['title' => 'IUT ICT Fest', 'text' => 'The flagship event and a landmark in Bangladesh\'s ICT scene since 2008.', 'icon' => 'fa-flag-checkered'],
        ['title' => 'IT Olympiads', 'text' => 'Knowledge-based competitions across multiple IT domains.', 'icon' => 'fa-trophy'],
        ['title' => 'E-sports Tournaments', 'text' => 'Competitive gaming events for the student technology community.', 'icon' => 'fa-gamepad'],
        ['title' => 'Business Case Competitions', 'text' => 'Entrepreneurship and business strategy challenges for emerging builders.', 'icon' => 'fa-briefcase'],
    ];

    $collaborations = [
        ['name' => 'Robi Axiata', 'text' => 'Strategic industry collaboration'],
        ['name' => 'StreamsTech', 'text' => 'Technical webinar collaboration'],
        ['name' => 'BDApps', 'text' => 'Competition sponsorship platform'],
        ['name' => 'Brain Station 23', 'text' => 'Game development hackathon'],
        ['name' => 'NetCom Learning', 'text' => 'AI competition collaboration'],
        ['name' => 'Bengali.AI', 'text' => 'Research collaboration datathon'],
        ['name' => 'Prime Bank', 'text' => 'First ICT Fest sponsor'],
        ['name' => 'FSIBL', 'text' => 'Event powering sponsor'],
    ];

    $highlights = [
        ['title' => 'First University Game Jam', 'text' => 'Partnered with Brain Station 23 for Bangladesh\'s pioneering university-level game development hackathon.'],
        ['title' => 'National AI Competition', 'text' => 'Collaborated with NetCom Learning for Agent X, involving 50,000+ students across Bangladesh.'],
    ];

    $leadership = [
        [
            'name' => 'Dr. Md. Hasanul Kabir',
            'role' => 'Chairman, IUT Computer Society',
            'meta' => 'Professor and Head, CSE Department, IUT',
            'image' => 'https://cse.iutoic-dhaka.edu/uploads/img/1601046852_1755.jpg',
        ],
        [
            'name' => 'Dr. Md Moniruzzaman',
            'role' => 'Moderator, IUT Computer Society',
            'meta' => 'Assistant Professor, CSE Department, IUT',
            'image' => 'https://cse.iutoic-dhaka.edu/uploads/img/1617768281_1035.png',
        ],
        [
            'name' => 'Miraj Mahmud Mahee',
            'role' => 'President, IUT Computer Society',
            'meta' => 'Final Year, Department of Computer Science and Engineering, IUT',
            'image' => asset('assets/miraj.jpeg'),
        ],
        [
            'name' => 'Adib Sakhawat',
            'role' => 'Vice President (Management), IUT Computer Society',
            'meta' => 'Final Year, Department of Computer Science and Engineering, IUT',
            'image' => 'https://avatars.githubusercontent.com/u/111243915?v=4',
        ],
        [
            'name' => 'Md Taimum Ibne Sayed',
            'role' => 'Vice President (Operations), IUT Computer Society',
            'meta' => 'Final Year, Department of Computer Science and Engineering, IUT',
            'image' => asset('assets/taimum.jpeg'),
        ],
    ];

    $impact = [
        ['value' => '90%+', 'label' => 'Job Placement Rate'],
        ['value' => '15+', 'label' => 'Countries Worldwide'],
        ['value' => '50+', 'label' => 'Top Companies'],
    ];
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8 lg:pb-24">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-center gap-14 lg:grid-cols-[1.15fr_.85fr]">
            <div>
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-volt/70"></span>
                    About IUTCS
                </div>
                <h1 class="mt-8 max-w-4xl text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    IUT Computer Society
                </h1>
                <p class="mt-7 max-w-2xl text-base leading-8 text-white/58">
                    Empowering future technology leaders since 2008 through technical learning, collaborative projects, national competitions, and community-led innovation.
                </p>
                <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                    <a href="#iutcs-overview" class="inline-flex items-center justify-center gap-3 rounded-md border border-white/12 bg-white/[.04] px-5 py-3 text-sm font-medium text-white/72 transition hover:border-volt/60 hover:text-white">
                        Learn More
                    </a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                @foreach($stats as $stat)
                    <div class="rounded-lg border border-white/10 bg-white/[.035] p-6">
                        <p class="text-4xl font-semibold text-white">{{ $stat['value'] }}</p>
                        <p class="mt-3 text-sm leading-6 text-white/56">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section id="iutcs-overview" class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-[.85fr_1.15fr]">
        <div>
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">What is IUTCS?</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">A student society built around technical excellence.</h2>
        </div>

        <div class="grid gap-8">
            <div class="text-sm leading-7 text-white/60">
                <p>
                    The IUT Computer Society is a prominent student organization at the Islamic University of Technology, founded in 2008 by students from the Department of Computer Science and Engineering.
                </p>
                <p class="mt-5">
                    It serves young computer engineers and students from all departments at IUT by helping them develop technical skills, collaborate on projects, and engage with the wider technology community.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($quickFacts as $fact)
                    <div class="flex items-center gap-3 rounded-lg border border-white/10 bg-white/[.035] px-4 py-3">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-md bg-volt/10 text-volt">
                            <i class="fa-solid fa-check text-xs"></i>
                        </span>
                        <span class="text-sm font-medium text-white/72">{{ $fact }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-2xl">
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">What We Do</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Education, competition, and collaboration.</h2>
            <p class="mt-5 text-sm leading-7 text-white/56">
                IUTCS nurtures technical excellence through structured learning, hands-on work, and events that expose students to real technical pressure.
            </p>
        </div>

        <div class="mt-12 grid gap-5 md:grid-cols-3">
            @foreach($activities as $activity)
                <article class="rounded-lg border border-white/10 bg-white/[.035] p-6">
                    <div class="grid h-11 w-11 place-items-center rounded-md {{ $activity['accent'] === 'volt' ? 'bg-volt/10 text-volt' : ($activity['accent'] === 'iris' ? 'bg-iris/10 text-iris' : 'bg-ember/10 text-ember') }}">
                        <i class="fa-solid {{ $activity['icon'] }}"></i>
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-white">{{ $activity['title'] }}</h3>
                    <p class="mt-4 text-sm leading-7 text-white/56">{{ $activity['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-[.7fr_1.3fr]">
        <div>
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Events & Competitions</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Platforms for talent to become visible.</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach($events as $event)
                <article class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                    <div class="flex items-start gap-4">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-white/[.055] text-white/56">
                            <i class="fa-solid {{ $event['icon'] }}"></i>
                        </span>
                        <div>
                            <h3 class="text-base font-semibold text-white">{{ $event['title'] }}</h3>
                            <p class="mt-3 text-sm leading-6 text-white/56">{{ $event['text'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-12 lg:grid-cols-[.85fr_1.15fr]">
            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Previous Collaborations</p>
                <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">A history of work with industry and research partners.</h2>
                <div class="mt-8 grid gap-4">
                    @foreach($highlights as $highlight)
                        <div class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                            <h3 class="text-base font-semibold text-white">{{ $highlight['title'] }}</h3>
                            <p class="mt-3 text-sm leading-6 text-white/56">{{ $highlight['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($collaborations as $partner)
                    <div class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                        <h3 class="text-base font-semibold text-white">{{ $partner['name'] }}</h3>
                        <p class="mt-2 text-sm text-white/50">{{ $partner['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-2xl">
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Leadership</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">Guided by faculty and student leadership.</h2>
        </div>

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-6 lg:items-start lg:gap-y-20">
            @foreach($leadership as $index => $person)
                <article class="{{ match ($index) {
                    0 => 'lg:col-span-3',
                    1 => 'lg:col-span-3',
                    2 => 'lg:order-4 lg:col-span-2 lg:-translate-y-12',
                    3 => 'lg:order-3 lg:col-span-2',
                    4 => 'lg:order-5 lg:col-span-2',
                    default => 'lg:col-span-2',
                } }} rounded-lg border border-white/10 bg-white/[.035] p-5">
                    <img src="{{ $person['image'] }}" alt="{{ $person['name'] }}" class="aspect-square w-full rounded-md object-cover">
                    <h3 class="mt-5 text-lg font-semibold text-white">{{ $person['name'] }}</h3>
                    <p class="mt-2 text-sm font-medium leading-6 text-volt/90">{{ $person['role'] }}</p>
                    <p class="mt-3 text-sm leading-6 text-white/52">{{ $person['meta'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="px-4 pb-32 pt-16 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-10 rounded-lg border border-white/10 bg-white/[.035] px-6 py-12 sm:px-10 lg:grid-cols-[.8fr_1.2fr]">
        <div>
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Alumni Network</p>
            <h2 class="mt-5 text-3xl font-semibold text-white sm:text-4xl">IUTCS alumni carry the work into the global technology industry.</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            @foreach($impact as $item)
                <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                    <p class="text-3xl font-semibold text-white">{{ $item['value'] }}</p>
                    <p class="mt-3 text-sm leading-6 text-white/56">{{ $item['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
