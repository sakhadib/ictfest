@extends('layouts.app')

@section('title', 'Final Registration | '.config('app.name'))

@section('content')
@php
    $inputClass = 'mt-2 w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/24 focus:border-volt/50';
    $selectClass = 'mt-2 w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition focus:border-volt/50';
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid items-start gap-10 lg:grid-cols-[.88fr_1.12fr]">
            <div>
                <div class="hidden items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45 sm:inline-flex">
                    <span class="h-px w-10 bg-volt/70"></span>
                    Final Registration
                </div>
                <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    Submit your final round details.
                </h1>
                <p class="mt-7 max-w-2xl text-base leading-8 text-white/58">
                    This page is available only after your registration has been approved from the admin panel. Fill in the team transaction ID and shirt sizes for all listed attendees.
                </p>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Competition Information</p>
                <div class="mt-6 grid gap-4">
                    <div class="rounded-lg bg-black/15 p-4">
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Event</p>
                        <p class="mt-2 text-lg font-semibold text-white">{{ $registration->event?->name }}</p>
                    </div>
                    <div class="rounded-lg bg-black/15 p-4">
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Registration Code</p>
                        <p class="mt-2 font-mono text-2xl font-semibold text-white">{{ $registration->registration_code }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-black/15 p-4">
                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Team / Player</p>
                            <p class="mt-2 text-sm font-semibold text-white">{{ $registration->team_name }}</p>
                        </div>
                        <div class="rounded-lg bg-black/15 p-4">
                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Institution</p>
                            <p class="mt-2 text-sm font-semibold text-white">{{ $registration->institution }}</p>
                        </div>
                    </div>
                    <div class="rounded-lg bg-black/15 p-4">
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Primary Contact</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $registration->contact_name }}</p>
                        <p class="mt-1 break-words text-sm leading-6 text-white/50">{{ $registration->contact_email }}</p>
                        <p class="text-sm text-white/50">{{ $registration->contact_phone }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        @if (session('status'))
            <div class="mb-8 rounded-lg border border-emerald-300/25 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('final-registration.store', ['registration_code' => $registration->registration_code]) }}" class="grid gap-8">
            @csrf

            @if($errors->any())
                <div class="rounded-lg border border-red-400/25 bg-red-500/10 px-5 py-4 text-sm text-red-100">
                    Please review the highlighted fields and submit again.
                </div>
            @endif

            <section class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                <div class="max-w-2xl">
                    <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Team Submission</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Final transaction detail</h2>
                    <p class="mt-3 text-sm leading-7 text-white/56">
                        Submit the transaction ID for the team payment linked to the final round.
                    </p>
                </div>

                <div class="mt-6 max-w-xl">
                    <label>
                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Transaction ID</span>
                        <input name="trx_id" value="{{ old('trx_id', $registration->finalRegistration?->trx_id) }}" class="{{ $inputClass }}" placeholder="TRX ID">
                        @error('trx_id')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                <div class="max-w-2xl">
                    <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Participants</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Shirt sizes</h2>
                    <p class="mt-3 text-sm leading-7 text-white/56">
                        Every listed participant must have a selected size before you submit.
                    </p>
                </div>

                <div class="mt-8 grid gap-4">
                    @foreach($registration->participants as $participant)
                        @php($participantIndex = $loop->index)
                        <article class="rounded-lg bg-black/15 p-5">
                            <input type="hidden" name="participants[{{ $participantIndex }}][id]" value="{{ $participant->id }}">
                            <div class="grid gap-6 lg:grid-cols-[1fr_13rem] lg:items-end">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-lg font-semibold text-white">{{ $participant->full_name }}</h3>
                                        @if($participant->is_leader)
                                            <span class="rounded-full border border-volt/30 px-2.5 py-1 text-xs text-volt">Leader</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 break-words text-sm leading-6 text-white/50">{{ $participant->email }}</p>
                                    <p class="text-sm text-white/50">{{ $participant->phone }}</p>
                                    <p class="mt-2 text-sm text-white/58">{{ $participant->student_id }} | {{ $participant->university }}</p>
                                </div>

                                <label>
                                    <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">T-shirt Size</span>
                                    <select name="participants[{{ $participantIndex }}][tshirt_size]" class="{{ $selectClass }}">
                                        <option value="">Select size</option>
                                        @foreach($tshirtSizes as $size)
                                            <option value="{{ $size }}" @selected(old("participants.$participantIndex.tshirt_size", $participant->tshirt_size) === $size)>{{ $size }}</option>
                                        @endforeach
                                    </select>
                                    @error("participants.$participantIndex.tshirt_size")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                            </div>
                        </article>
                    @endforeach

                    @if($registration->coach)
                        <article class="rounded-lg bg-black/15 p-5">
                            <input type="hidden" name="coach[id]" value="{{ $registration->coach->id }}">
                            <div class="grid gap-6 lg:grid-cols-[1fr_13rem] lg:items-end">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-lg font-semibold text-white">{{ $registration->coach->name }}</h3>
                                        <span class="rounded-full border border-white/12 px-2.5 py-1 text-xs text-white/56">Coach</span>
                                    </div>
                                    <p class="mt-2 text-sm leading-6 text-white/50">{{ $registration->coach->designation }}</p>
                                    <p class="break-words text-sm leading-6 text-white/50">{{ $registration->coach->official_email }}</p>
                                    <p class="text-sm text-white/50">{{ $registration->coach->contact_number }}</p>
                                </div>

                                <label>
                                    <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">T-shirt Size</span>
                                    <select name="coach[tshirt_size]" class="{{ $selectClass }}">
                                        <option value="">Select size</option>
                                        @foreach($tshirtSizes as $size)
                                            <option value="{{ $size }}" @selected(old('coach.tshirt_size', $registration->coach->tshirt_size) === $size)>{{ $size }}</option>
                                        @endforeach
                                    </select>
                                    @error('coach.tshirt_size')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                            </div>
                        </article>
                    @endif
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-ink transition hover:bg-volt sm:w-auto">
                    Save Final Registration
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
