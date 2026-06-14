@extends('layouts.app')

@section('title', 'Contact | '.config('app.name'))

@section('content')
@php
    $contacts = [
        [
            'name' => 'Subarno Neel',
            'role' => 'General Secretary, IUT Computer Society',
            'phone' => '01601810022',
            'whatsapp' => '8801601810022',
        ],
        [
            'name' => 'Abrar Mahmud Hasan',
            'role' => 'Joint Secretary, IUT Computer Society',
            'phone' => '+8801793241773',
            'whatsapp' => '8801793241773',
        ],
    ];
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8 lg:pb-24">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-center gap-12 lg:grid-cols-[1.05fr_.95fr]">
            <div>
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-volt/70"></span>
                    Contact
                </div>
                <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    Talk to the IUTCS team.
                </h1>
                <p class="mt-7 max-w-2xl text-base leading-8 text-white/58">
                    Reach out through call or WhatsApp for festival coordination, event support, and society-related queries.
                </p>
                <div class="mt-10 flex flex-wrap gap-3">
                    <a href="mailto:iutcs@iut-dhaka.edu" class="inline-flex items-center gap-3 rounded-md border border-white/12 bg-white/[.04] px-5 py-3 text-sm font-medium text-white/72 transition hover:border-volt/60 hover:text-white">
                        <i class="fa-solid fa-envelope text-volt"></i>
                        iutcs@iut-dhaka.edu
                    </a>
                </div>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">WhatsApp Support</p>
                <p class="mt-4 text-sm leading-7 text-white/58">
                    The numbers below are available on WhatsApp and regular phone calls.
                </p>
                <div class="mt-8 grid gap-4">
                    @foreach($contacts as $contact)
                        <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-white">{{ $contact['name'] }}</h2>
                                    <p class="mt-2 text-sm leading-6 text-white/56">{{ $contact['role'] }}</p>
                                </div>
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-[#25D366]/10 text-[#25D366]">
                                    <i class="fa-brands fa-whatsapp text-lg"></i>
                                </span>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                <a href="tel:{{ $contact['phone'] }}" class="inline-flex items-center gap-2 rounded-md border border-white/12 bg-white/[.04] px-4 py-2.5 text-sm font-semibold text-white/75 transition hover:border-white/24 hover:text-white">
                                    <i class="fa-solid fa-phone text-xs text-volt"></i>
                                    {{ $contact['phone'] }}
                                </a>
                                <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-md bg-[#25D366] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#1fb85b]">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-32 pt-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="rounded-lg border border-white/10 bg-white/[.035] px-6 py-10 sm:px-8">
            <div class="grid gap-8 lg:grid-cols-3">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">General Email</p>
                    <p class="mt-4 text-lg font-semibold text-white">iutcs@iut-dhaka.edu</p>
                    <p class="mt-3 text-sm leading-7 text-white/56">Use this for non-urgent festival and society communication.</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Response Channel</p>
                    <p class="mt-4 text-lg font-semibold text-white">WhatsApp and phone</p>
                    <p class="mt-3 text-sm leading-7 text-white/56">Both listed officers can be reached using the same number formats shown above.</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Location</p>
                    <p class="mt-4 text-lg font-semibold text-white">IUT, Gazipur</p>
                    <p class="mt-3 text-sm leading-7 text-white/56">For in-person coordination, use the society office at the Islamic University of Technology campus.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
