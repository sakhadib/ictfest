@extends('layouts.app')

@section('title', 'Registration Status | 12th IUT ICT FEST 2026')

@section('content')
@php
    $inputClass = 'w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/24 focus:border-volt/50';
    $paymentStyles = [
        'confirmed' => ['label' => 'Payment Confirmed', 'class' => 'border-volt/35 bg-volt/10 text-volt', 'icon' => 'fa-circle-check'],
        'submitted' => ['label' => 'Payment Submitted', 'class' => 'border-ember/35 bg-ember/10 text-ember', 'icon' => 'fa-clock'],
        'unpaid' => ['label' => 'Payment Unpaid', 'class' => 'border-white/12 bg-white/[.04] text-white/64', 'icon' => 'fa-circle-info'],
    ];
    $paymentStatus = $registration?->payment_status;
    $paymentStyle = $paymentStyles[$paymentStatus] ?? ['label' => 'Payment Status Unknown', 'class' => 'border-white/12 bg-white/[.04] text-white/64', 'icon' => 'fa-circle-question'];
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-4xl">
            <div class="inline-flex items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45">
                <span class="h-px w-10 bg-volt/70"></span>
                Registration Status
            </div>
            <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                Check your registration.
            </h1>
            <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                Enter the registration code you received after submission, such as 01-48372.
            </p>
        </div>
    </div>
</section>

<section class="px-4 pb-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <form method="GET" action="{{ route('registration.status') }}" class="grid gap-4 border-y border-white/10 py-8 sm:grid-cols-[1fr_auto]">
            <label>
                <span class="sr-only">Registration code</span>
                <input name="code" value="{{ $code }}" class="{{ $inputClass }} uppercase" placeholder="Enter registration code">
            </label>
            <button class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-ink transition hover:bg-volt">
                Check Status
            </button>
        </form>

        @if($searched && ! $registration)
            <div class="mt-10 rounded-lg border border-red-400/25 bg-red-500/10 p-6">
                <p class="text-lg font-semibold text-white">No registration found.</p>
                <p class="mt-2 text-sm leading-6 text-white/56">Check the code and try again. Registration codes are formatted like 01-48372.</p>
            </div>
        @endif

        @if($registration)
            <div class="mt-12 grid gap-10 lg:grid-cols-[.78fr_1.22fr]">
                <aside class="h-fit border-y border-white/10 py-8">
                    <div class="rounded-lg border p-6 {{ $paymentStyle['class'] }}">
                        <div class="flex items-center gap-4">
                            <div class="grid h-12 w-12 place-items-center rounded-md bg-black/15">
                                <i class="fa-solid {{ $paymentStyle['icon'] }}"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[.16em] opacity-70">Payment Status</p>
                                <p class="mt-1 text-2xl font-semibold text-white">{{ $paymentStyle['label'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-5">
                        <div>
                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Registration Code</p>
                            <p class="mt-2 text-3xl font-semibold text-white">{{ $registration->registration_code }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Registration Status</p>
                            <p class="mt-2 text-base font-semibold text-white">{{ ucfirst($registration->status) }}</p>
                        </div>
                        @if($registration->payment)
                            <div>
                                <p class="text-xs uppercase tracking-[.16em] text-white/32">Payment Method</p>
                                <p class="mt-2 text-base font-semibold text-white">{{ ucfirst($registration->payment->method) }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[.16em] text-white/32">Transaction ID</p>
                                <p class="mt-2 text-base font-semibold text-white">{{ $registration->payment->trx_id }}</p>
                            </div>
                        @endif
                    </div>
                </aside>

                <div class="border-y border-white/10 py-8">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Event</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ $registration->event?->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Team / Player</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ $registration->team_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Institution</p>
                            <p class="mt-2 text-sm font-medium leading-6 text-white/72">{{ $registration->institution }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Contact</p>
                            <p class="mt-2 text-sm font-medium leading-6 text-white/72">{{ $registration->contact_name }}</p>
                            <p class="mt-1 text-sm text-white/48">{{ $registration->contact_email }} / {{ $registration->contact_phone }}</p>
                        </div>
                    </div>

                    <div class="mt-12">
                        <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Participants</p>
                        <div class="mt-5 divide-y divide-white/10">
                            @foreach($registration->participants as $participant)
                                <div class="grid gap-3 py-5 first:pt-0 sm:grid-cols-[1fr_1fr]">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <p class="font-semibold text-white">{{ $participant->full_name }}</p>
                                            @if($participant->is_leader)
                                                <span class="rounded-full border border-volt/30 px-2.5 py-1 text-xs text-volt">Leader</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-sm text-white/46">{{ $participant->email }} / {{ $participant->phone }}</p>
                                    </div>
                                    <div class="sm:text-right">
                                        <p class="text-sm font-medium text-white/72">{{ $participant->student_id }}</p>
                                        <p class="mt-1 text-sm text-white/46">{{ $participant->university }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
