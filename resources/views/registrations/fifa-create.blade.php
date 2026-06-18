@extends('layouts.app')

@section('title', 'FIFA Registration | '.config('app.name'))

@section('content')
@php
    $inputClass = 'mt-2 w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/24 focus:border-ember/50';
    $labelClass = 'text-xs font-medium uppercase tracking-[.16em] text-white/38';
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-4xl">
            <div class="inline-flex items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45">
                <span class="h-px w-10 bg-ember/70"></span>
                FIFA Registration
            </div>
            <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                Register as an individual player.
            </h1>
            <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                FIFA is a single-player tournament. Your player details will be used as the registration contact.
            </p>
        </div>
    </div>
</section>

<section class="px-4 pb-32 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-14 lg:grid-cols-[.68fr_1.32fr]">
        <aside class="h-fit border-y border-white/10 py-8">
            <p class="text-xs font-medium uppercase tracking-[.18em] text-white/38">Event</p>
            <h2 class="mt-3 text-2xl font-semibold text-white">{{ $event->name }}</h2>
            <div class="mt-8 grid gap-5">
                <div class="flex items-center gap-4">
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-ember/10 text-ember"><i class="fa-solid fa-user"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Player</p>
                        <p class="mt-1 text-sm font-medium text-white">1 member</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-ember/10 text-ember"><i class="fa-solid fa-ticket"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Fee</p>
                        <p class="mt-1 text-sm font-medium text-white">200 BDT</p>
                    </div>
                </div>
            </div>
        </aside>

        <form method="POST" action="{{ route('fifa.register.store') }}" class="border-y border-white/10 py-8">
            @csrf

            @if($errors->any())
                <div class="mb-8 rounded-md border border-red-400/30 bg-red-500/10 p-4 text-sm leading-6 text-red-100">
                    Please review the highlighted fields and submit again.
                </div>
            @endif

            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Player</p>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label>
                        <span class="{{ $labelClass }}">Full Name</span>
                        <input name="participant[full_name]" value="{{ old('participant.full_name') }}" class="{{ $inputClass }}" placeholder="Full name">
                        @error('participant.full_name')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                    <label>
                        <span class="{{ $labelClass }}">Email</span>
                        <input type="email" name="participant[email]" value="{{ old('participant.email') }}" class="{{ $inputClass }}" placeholder="name@example.com">
                        @error('participant.email')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                    <label>
                        <span class="{{ $labelClass }}">Phone</span>
                        <input name="participant[phone]" value="{{ old('participant.phone') }}" class="{{ $inputClass }}" placeholder="Phone number">
                        @error('participant.phone')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                    <label>
                        <span class="{{ $labelClass }}">Student ID</span>
                        <input name="participant[student_id]" value="{{ old('participant.student_id') }}" class="{{ $inputClass }}" placeholder="Student ID">
                        @error('participant.student_id')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                    <label>
                        <span class="{{ $labelClass }}">Institution</span>
                        <input id="institution" data-university-search name="institution" value="{{ old('institution') }}" class="{{ $inputClass }}" placeholder="Institution name" autocomplete="off" spellcheck="false">
                        @error('institution')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                    <label>
                        <span class="{{ $labelClass }}">University</span>
                        <input id="university" data-university-search name="participant[university]" value="{{ old('participant.university') }}" class="{{ $inputClass }}" placeholder="University name" autocomplete="off" spellcheck="false">
                        @error('participant.university')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                </div>
            </div>

            <div class="mt-12">
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Payment</p>
                @include('registrations.partials.payment-instructions', [
                    'amount' => '200 BDT',
                    'number' => '01339562347',
                ])
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <span class="{{ $labelClass }}">Method</span>
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="bkash" class="peer sr-only" @checked(old('payment_method') === 'bkash')>
                                <span class="flex min-h-16 items-center justify-center rounded-md border border-white/10 bg-white/[.035] px-4 py-3 transition peer-checked:border-ember/60 peer-checked:bg-ember/10 hover:border-white/24">
                                    <img src="{{ asset('assets/bkash.webp') }}" alt="bKash" class="h-8 w-auto">
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="nagad" class="peer sr-only" @checked(old('payment_method') === 'nagad')>
                                <span class="flex min-h-16 items-center justify-center rounded-md border border-white/10 bg-white/[.035] px-4 py-3 transition peer-checked:border-ember/60 peer-checked:bg-ember/10 hover:border-white/24">
                                    <img src="{{ asset('assets/nagad.png') }}" alt="Nagad" class="h-8 w-auto">
                                </span>
                            </label>
                        </div>
                        @error('payment_method')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>
                    <label>
                        <span class="{{ $labelClass }}">Transaction ID</span>
                        <input name="trx_id" value="{{ old('trx_id') }}" class="{{ $inputClass }}" placeholder="TRX ID">
                        @error('trx_id')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                </div>
            </div>

            <div class="mt-10 flex justify-end">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-ink transition hover:bg-ember sm:w-auto">
                    Submit Registration
                </button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
    (() => {
        const institution = document.getElementById('institution');
        const university = document.getElementById('university');
        let previousInstitution = institution?.value.trim() ?? '';

        const syncUniversity = () => {
            const nextInstitution = institution.value.trim();
            const currentUniversity = university.value.trim();

            if (currentUniversity === '' || currentUniversity === previousInstitution) {
                university.value = nextInstitution;
            }

            previousInstitution = nextInstitution;
        };

        institution?.addEventListener('input', syncUniversity);
        institution?.addEventListener('change', syncUniversity);
    })();
</script>
@endpush
@include('registrations.partials.university-search', ['universities' => $universities])
@endsection
