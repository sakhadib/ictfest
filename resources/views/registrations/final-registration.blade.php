@extends('layouts.app')

@section('title', 'Final Registration | '.config('app.name'))

@section('content')
@php
    $inputClass = 'mt-2 w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/24 focus:border-volt/50';
    $selectedPaymentMethod = old('payment_method', $registration->payment?->method);
    $selectedCoachSize = old('coach.tshirt_size', $registration->coach?->tshirt_size);
    $paymentInfo = [
        '02' => ['amount' => '2000 BDT', 'number' => '01746145346'],
        '04' => ['amount' => '700 BDT', 'number' => '01941435623'],
    ][$registration->event?->code] ?? null;

    $amount = $paymentInfo['amount'] ?? 'BDT '.number_format((int) ($registration->event?->amount ?? 0));
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
                    This page is available only after your registration has been approved from the admin panel. Fill in the required final details for all listed attendees.
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
                    <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Registration Details</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Team information</h2>
                    <p class="mt-3 text-sm leading-7 text-white/56">
                        You can update submitted details here. Institution and university fields are locked.
                    </p>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <label>
                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Team / Player Name</span>
                        <input name="team_name" value="{{ old('team_name', $registration->team_name) }}" class="{{ $inputClass }}" placeholder="Team / Player name">
                        @error('team_name')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>

                    <div class="rounded-lg bg-black/15 p-4">
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Institution</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $registration->institution }}</p>
                    </div>
                </div>
            </section>

            @if($requiresPayment)
                <section class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                    <div class="max-w-2xl">
                        <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Team Submission</p>
                        <h2 class="mt-4 text-2xl font-semibold text-white">Final transaction detail</h2>
                        <p class="mt-3 text-sm leading-7 text-white/56">
                            Submit the payment method and transaction ID for the team payment linked to the final round.
                        </p>
                    </div>

                    <div class="mt-6 rounded-lg border border-volt/30 bg-volt/10 p-5">
                        <p class="text-xs font-medium uppercase tracking-[.22em] text-volt/80">Payable Amount</p>
                        <p class="mt-3 text-4xl font-semibold text-white">{{ $amount }}</p>
                    </div>

                    @if($paymentInfo)
                        @include('registrations.partials.payment-instructions', $paymentInfo)
                    @endif

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div>
                            <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Payment Method</span>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                @foreach (['bkash' => 'Bkash', 'nagad' => 'Nagad'] as $value => $label)
                                    <label class="flex min-h-16 cursor-pointer items-center justify-center rounded-lg border border-white/10 bg-white/[.035] px-4 py-3 transition has-[:checked]:border-volt/50 has-[:checked]:bg-volt/10 hover:border-white/24">
                                        <input type="radio" name="payment_method" value="{{ $value }}" @checked($selectedPaymentMethod === $value) class="sr-only">
                                        <img src="{{ asset($value === 'bkash' ? 'assets/bkash.webp' : 'assets/nagad.png') }}" alt="{{ $label }}" class="h-8 w-auto">
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_method')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>

                        <label>
                            <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Transaction ID</span>
                            <input name="trx_id" value="{{ old('trx_id', $registration->payment?->trx_id ?? $registration->finalRegistration?->trx_id) }}" class="{{ $inputClass }}" placeholder="TRX ID">
                            @error('trx_id')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                        </label>
                    </div>
                </section>
            @endif

            <section class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-7">
                <div class="max-w-2xl">
                    <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Participants</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Participant details and shirt sizes</h2>
                    <p class="mt-3 text-sm leading-7 text-white/56">
                        Every listed participant must have complete details and a selected size before you submit. University cannot be changed from this page.
                    </p>
                </div>

                <div class="mt-8 grid gap-4">
                    @foreach($registration->participants as $participant)
                        @php($participantIndex = $loop->index)
                        <article class="rounded-lg bg-black/15 p-5">
                            <input type="hidden" name="participants[{{ $participantIndex }}][id]" value="{{ $participant->id }}">
                            <div class="grid gap-6">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-lg font-semibold text-white">{{ $participant->full_name }}</h3>
                                        @if($participant->is_leader)
                                            <span class="rounded-full border border-volt/30 px-2.5 py-1 text-xs text-volt">Leader</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <label>
                                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Full Name</span>
                                        <input name="participants[{{ $participantIndex }}][full_name]" value="{{ old("participants.$participantIndex.full_name", $participant->full_name) }}" class="{{ $inputClass }}" placeholder="Full name">
                                        @error("participants.$participantIndex.full_name")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    </label>

                                    <label>
                                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Email</span>
                                        <input type="email" name="participants[{{ $participantIndex }}][email]" value="{{ old("participants.$participantIndex.email", $participant->email) }}" class="{{ $inputClass }}" placeholder="Email">
                                        @error("participants.$participantIndex.email")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    </label>

                                    <label>
                                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Phone</span>
                                        <input name="participants[{{ $participantIndex }}][phone]" value="{{ old("participants.$participantIndex.phone", $participant->phone) }}" class="{{ $inputClass }}" placeholder="Phone">
                                        @error("participants.$participantIndex.phone")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    </label>

                                    <label>
                                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Student ID <span class="normal-case tracking-normal text-white/28">(if applicable)</span></span>
                                        <input name="participants[{{ $participantIndex }}][student_id]" value="{{ old("participants.$participantIndex.student_id", $participant->student_id) }}" class="{{ $inputClass }}" placeholder="Student ID, if applicable">
                                        @error("participants.$participantIndex.student_id")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    </label>

                                    <div class="rounded-lg bg-black/15 p-4 md:col-span-2">
                                        <p class="text-xs uppercase tracking-[.16em] text-white/32">University</p>
                                        <p class="mt-2 text-sm font-semibold text-white">{{ $participant->university }}</p>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">T-shirt Size</span>
                                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
                                        @foreach($tshirtSizes as $size)
                                            <label class="flex cursor-pointer items-center justify-center rounded-lg border border-white/10 bg-white/[.035] px-3 py-3 text-sm text-white transition has-[:checked]:border-volt/50 has-[:checked]:bg-volt/10">
                                                <input type="radio" name="participants[{{ $participantIndex }}][tshirt_size]" value="{{ $size }}" @checked(old("participants.$participantIndex.tshirt_size", $participant->tshirt_size) === $size) class="sr-only">
                                                <span class="font-medium">{{ $size }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error("participants.$participantIndex.tshirt_size")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </article>
                    @endforeach

                    @if($registration->coach)
                        <article class="rounded-lg bg-black/15 p-5">
                            <input type="hidden" name="coach[id]" value="{{ $registration->coach->id }}">
                            <div class="grid gap-6">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-lg font-semibold text-white">{{ $registration->coach->name }}</h3>
                                        <span class="rounded-full border border-white/12 px-2.5 py-1 text-xs text-white/56">Coach</span>
                                    </div>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <label>
                                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Coach Name</span>
                                        <input name="coach[name]" value="{{ old('coach.name', $registration->coach->name) }}" class="{{ $inputClass }}" placeholder="Coach name">
                                        @error('coach.name')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    </label>

                                    <label>
                                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Designation</span>
                                        <input name="coach[designation]" value="{{ old('coach.designation', $registration->coach->designation) }}" class="{{ $inputClass }}" placeholder="Designation">
                                        @error('coach.designation')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    </label>

                                    <label>
                                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Official Email</span>
                                        <input type="email" name="coach[official_email]" value="{{ old('coach.official_email', $registration->coach->official_email) }}" class="{{ $inputClass }}" placeholder="Official email">
                                        @error('coach.official_email')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    </label>

                                    <label>
                                        <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">Contact Number</span>
                                        <input name="coach[contact_number]" value="{{ old('coach.contact_number', $registration->coach->contact_number) }}" class="{{ $inputClass }}" placeholder="Contact number">
                                        @error('coach.contact_number')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    </label>
                                </div>

                                <div>
                                    <span class="text-xs font-medium uppercase tracking-[.16em] text-white/38">T-shirt Size</span>
                                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
                                        @foreach($tshirtSizes as $size)
                                            <label class="flex cursor-pointer items-center justify-center rounded-lg border border-white/10 bg-white/[.035] px-3 py-3 text-sm text-white transition has-[:checked]:border-volt/50 has-[:checked]:bg-volt/10">
                                                <input type="radio" name="coach[tshirt_size]" value="{{ $size }}" @checked($selectedCoachSize === $size) class="sr-only">
                                                <span class="font-medium">{{ $size }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('coach.tshirt_size')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </div>
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
