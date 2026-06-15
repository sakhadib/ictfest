@extends('layouts.app')

@section('title', 'Registration Status | '.config('app.name'))

@section('content')
@php
    $inputClass = 'w-full rounded-md border border-white/10 bg-white/[.05] px-4 py-4 text-base text-white outline-none transition placeholder:text-white/28 focus:border-volt/60 focus:bg-white/[.07]';
    $paymentStyles = [
        'confirmed' => ['label' => 'Payment Confirmed', 'class' => 'border-volt/30 bg-volt/10 text-volt', 'icon' => 'fa-circle-check'],
        'submitted' => ['label' => 'Payment Submitted', 'class' => 'border-ember/35 bg-ember/10 text-ember', 'icon' => 'fa-clock'],
        'unpaid' => ['label' => 'No Payment Required', 'class' => 'border-white/12 bg-white/[.06] text-white/72', 'icon' => 'fa-circle-info'],
    ];
    $registrationStyles = [
        'paid' => ['label' => 'Approved', 'class' => 'border-volt/30 bg-volt/10 text-volt', 'icon' => 'fa-circle-check'],
        'verified' => ['label' => 'Final Round Qualified', 'class' => 'border-volt/30 bg-volt/10 text-volt', 'icon' => 'fa-circle-check'],
        'pending' => ['label' => 'Pending Review', 'class' => 'border-ember/35 bg-ember/10 text-ember', 'icon' => 'fa-hourglass-half'],
        'rejected' => ['label' => 'Rejected', 'class' => 'border-red-400/25 bg-red-500/10 text-red-200', 'icon' => 'fa-circle-xmark'],
    ];
    $paymentStatus = $registration?->payment_status;
    $paymentStyle = $paymentStyles[$paymentStatus] ?? ['label' => 'Payment Status Unknown', 'class' => 'border-white/12 bg-white/[.06] text-white/72', 'icon' => 'fa-circle-question'];
    $registrationStatus = $registration?->status;
    $registrationStyle = $registrationStyles[$registrationStatus] ?? ['label' => ucfirst((string) $registrationStatus), 'class' => 'border-white/12 bg-white/[.06] text-white/72', 'icon' => 'fa-circle-question'];
@endphp

<section class="px-4 pb-14 pt-32 sm:px-6 lg:px-8 lg:pb-20">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-end gap-10 lg:grid-cols-[1.05fr_.95fr]">
            <div>
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-volt/70"></span>
                    Registration Status
                </div>
                <h1 class="mt-8 max-w-4xl text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    Find your registration in seconds.
                </h1>
                <p class="mt-7 max-w-2xl text-base leading-8 text-white/58">
                    Use your registration code to check event, payment, and participant details.
                </p>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[.035] p-5 sm:p-6">
                <div class="flex gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-volt/10 text-volt">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-white">Forgot your code?</h2>
                        <p class="mt-2 text-sm leading-7 text-white/56">
                            We sent your registration code by email and SMS after submission. Search your inbox and messages for {{ config('app.name') }} or the event name.
                        </p>
                        <a href="{{ route('contact') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-volt transition hover:text-white">
                            Need human support
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('registration.status') }}" class="mt-12">
            <div class="grid gap-3 lg:grid-cols-[1fr_auto]">
                <label>
                    <span class="sr-only">Registration code</span>
                    <input name="code" value="{{ $code }}" class="{{ $inputClass }} uppercase" placeholder="Example: 01-48372" autocomplete="off" spellcheck="false">
                </label>
                <button class="inline-flex min-h-[3.5rem] items-center justify-center gap-3 rounded-md bg-white px-6 py-3 text-sm font-semibold text-ink transition hover:bg-volt">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    Check Status
                </button>
            </div>
            <p class="mt-4 text-sm leading-7 text-white/46">
                Codes look like <span class="font-mono text-white/64">01-48372</span>. The first two digits identify the event.
            </p>
        </form>
    </div>
</section>

<section class="px-4 pb-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        @if($searched && ! $registration)
            <div class="rounded-lg border border-red-400/25 bg-red-500/10 p-6 sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-md bg-red-500/15 text-red-200">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </span>
                    <div>
                        <h2 class="text-2xl font-semibold text-white">No registration found.</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-white/60">
                            Recheck the code from your confirmation email or SMS. If you still cannot find it, contact the IUTCS support team.
                        </p>
                        <a href="{{ route('contact') }}" class="mt-5 inline-flex items-center justify-center gap-3 rounded-md border border-white/12 bg-white/[.05] px-4 py-2.5 text-sm font-semibold text-white/76 transition hover:border-white/24 hover:text-white">
                            Contact Support
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if($registration)
            <div class="grid gap-6 lg:grid-cols-[.72fr_1.28fr]">
                <aside class="grid gap-5">
                    <div class="rounded-lg border border-white/10 bg-white/[.04] p-6">
                        <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Registration Code</p>
                        <p class="mt-4 font-mono text-4xl font-semibold leading-none text-white">{{ $registration->registration_code }}</p>
                        <p class="mt-5 text-sm leading-7 text-white/52">{{ $registration->event?->name }}</p>
                    </div>

                    <div class="grid gap-3">
                        <div class="rounded-lg border p-5 {{ $registrationStyle['class'] }}">
                            <div class="flex items-center gap-4">
                                <span class="grid h-11 w-11 place-items-center rounded-md bg-black/15">
                                    <i class="fa-solid {{ $registrationStyle['icon'] }}"></i>
                                </span>
                                <div>
                                    <p class="text-xs uppercase tracking-[.16em] opacity-70">Registration</p>
                                    <p class="mt-1 text-xl font-semibold text-white">{{ $registrationStyle['label'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border p-5 {{ $paymentStyle['class'] }}">
                            <div class="flex items-center gap-4">
                                <span class="grid h-11 w-11 place-items-center rounded-md bg-black/15">
                                    <i class="fa-solid {{ $paymentStyle['icon'] }}"></i>
                                </span>
                                <div>
                                    <p class="text-xs uppercase tracking-[.16em] opacity-70">Payment</p>
                                    <p class="mt-1 text-xl font-semibold text-white">{{ $paymentStyle['label'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($registration->payment)
                        <div class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Payment Detail</p>
                            <div class="mt-5 grid gap-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-white/48">Method</span>
                                    <span class="text-sm font-semibold capitalize text-white">{{ $registration->payment->method }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-white/48">Transaction ID</span>
                                    <span class="font-mono text-sm font-semibold text-white">{{ $registration->payment->trx_id }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($registration->finalRegistration)
                        <div class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Final Submission</p>
                            <div class="mt-5 grid gap-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-white/48">Status</span>
                                    <span class="text-sm font-semibold capitalize text-white">{{ $registration->finalRegistration->status }}</span>
                                </div>
                                @if($registration->finalRegistration->trx_id)
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-sm text-white/48">Transaction ID</span>
                                        <span class="font-mono text-sm font-semibold text-white">{{ $registration->finalRegistration->trx_id }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </aside>

                <div class="grid gap-6">
                    <section class="rounded-lg border border-white/10 bg-white/[.04] p-6 sm:p-7">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Team / Player</p>
                                <h2 class="mt-3 text-2xl font-semibold text-white">{{ $registration->team_name }}</h2>
                            </div>
                            <span class="w-fit rounded-full border border-white/10 bg-white/[.05] px-3 py-1 text-xs font-medium text-white/56">
                                {{ $registration->participants->count() }} participant{{ $registration->participants->count() === 1 ? '' : 's' }}
                            </span>
                        </div>

                        <div class="mt-7 grid gap-4 md:grid-cols-2">
                            <div class="rounded-lg bg-black/15 p-4">
                                <p class="text-xs uppercase tracking-[.16em] text-white/32">Institution</p>
                                <p class="mt-2 text-sm font-medium leading-6 text-white/78">{{ $registration->institution }}</p>
                            </div>
                            <div class="rounded-lg bg-black/15 p-4">
                                <p class="text-xs uppercase tracking-[.16em] text-white/32">Contact</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ $registration->contact_name }}</p>
                                <p class="mt-1 break-words text-sm leading-6 text-white/50">{{ $registration->contact_email }}</p>
                                <p class="text-sm text-white/50">{{ $registration->contact_phone }}</p>
                            </div>
                        </div>

                        @if($registration->coach)
                            <div class="mt-4 rounded-lg bg-black/15 p-4">
                                <p class="text-xs uppercase tracking-[.16em] text-white/32">Coach</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ $registration->coach->name }}</p>
                                <p class="mt-1 text-sm leading-6 text-white/54">{{ $registration->coach->designation }}</p>
                                <p class="mt-1 break-words text-sm leading-6 text-white/46">{{ $registration->coach->official_email }} / {{ $registration->coach->contact_number }}</p>
                            </div>
                        @endif
                    </section>

                    <section class="rounded-lg border border-white/10 bg-white/[.04] p-6 sm:p-7">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Participants</p>
                                <h2 class="mt-3 text-2xl font-semibold text-white">Submitted roster</h2>
                            </div>
                        </div>

                        <div class="mt-7 grid gap-4">
                            @foreach($registration->participants as $participant)
                                <article class="rounded-lg bg-black/15 p-4">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-3">
                                                <h3 class="text-base font-semibold text-white">{{ $participant->full_name }}</h3>
                                                @if($participant->is_leader)
                                                    <span class="rounded-full border border-volt/30 px-2.5 py-1 text-xs text-volt">Leader</span>
                                                @endif
                                            </div>
                                            <p class="mt-2 break-words text-sm leading-6 text-white/50">{{ $participant->email }}</p>
                                            <p class="text-sm text-white/50">{{ $participant->phone }}</p>
                                        </div>
                                        <div class="sm:max-w-xs sm:text-right">
                                            <p class="text-sm font-semibold text-white/78">{{ $participant->student_id }}</p>
                                            <p class="mt-2 text-sm leading-6 text-white/50">{{ $participant->university }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
