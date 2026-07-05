@extends('layouts.app')

@section('title', 'IUPC Coach Portal | '.config('app.name'))

@section('content')
@php
    $inputClass = 'mt-2 w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/24 focus:border-volt/50';
    $labelClass = 'text-xs font-medium uppercase tracking-[.16em] text-white/38';
@endphp

<section class="px-4 pb-16 pt-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_20rem] lg:items-start">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.24em] text-volt/80">IUPC Coach Portal</p>
                <h1 class="mt-5 text-4xl font-semibold leading-tight text-white sm:text-5xl">{{ $allocation->name }}</h1>
                <p class="mt-5 max-w-3xl text-sm leading-7 text-white/58">
                    Review teams from your university, submit final registration information, and provide payment details for verification by the event dashboard.
                </p>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[.035] p-5">
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-white/38">Quota</p>
                <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-md bg-black/15 p-3">
                        <p class="text-2xl font-semibold text-white">{{ $allocation->slot_count }}</p>
                        <p class="mt-1 text-[11px] uppercase tracking-wide text-white/38">Slots</p>
                    </div>
                    <div class="rounded-md bg-black/15 p-3">
                        <p class="text-2xl font-semibold text-white">{{ $submittedCount }}</p>
                        <p class="mt-1 text-[11px] uppercase tracking-wide text-white/38">Submitted</p>
                    </div>
                    <div class="rounded-md bg-black/15 p-3">
                        <p class="text-2xl font-semibold text-white">{{ $remainingSlots }}</p>
                        <p class="mt-1 text-[11px] uppercase tracking-wide text-white/38">Left</p>
                    </div>
                </div>
                <p class="mt-4 text-xs leading-5 text-white/46">Accessed as {{ $link->coach?->name }}. This private link stays active until disabled by the admin.</p>
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-28 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[1fr_22rem]">
        <div class="min-w-0">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-300/25 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-lg border border-red-400/25 bg-red-500/10 px-5 py-4 text-sm text-red-100">
                    Please review the highlighted fields and submit again.
                </div>
            @endif

            <div class="grid gap-4">
                @forelse($registrations as $registration)
                    @php
                        $isSubmitted = in_array($registration->finalRegistration?->status, ['submitted', 'approved'], true);
                        $canSubmit = $isSubmitted || $remainingSlots > 0;
                        $selectedPackage = old('payment_package', $registration->finalRegistration?->payment_package);
                        $selectedMethod = old('payment_method', $registration->payment?->method ?? 'bkash');
                    @endphp

                    <details class="group rounded-lg border border-white/10 bg-white/[.035]">
                        <summary class="flex cursor-pointer list-none flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-lg font-semibold text-white">{{ $registration->team_name }}</h2>
                                    <span class="rounded-full border border-white/12 px-2.5 py-1 text-xs text-white/54">{{ $registration->registration_code }}</span>
                                    @if($isSubmitted)
                                        <span class="rounded-full border border-emerald-300/30 bg-emerald-400/10 px-2.5 py-1 text-xs font-semibold text-emerald-100">Submitted</span>
                                    @elseif(! $canSubmit)
                                        <span class="rounded-full border border-red-300/30 bg-red-400/10 px-2.5 py-1 text-xs font-semibold text-red-100">Quota full</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-white/46">{{ $registration->contact_name }} · {{ $registration->contact_email }}</p>
                            </div>
                            <span class="text-sm font-semibold text-volt group-open:hidden">Expand</span>
                            <span class="hidden text-sm font-semibold text-volt group-open:inline">Collapse</span>
                        </summary>

                        <form method="POST" action="{{ route('iupc.coach.teams.submit', ['token' => request()->route('token'), 'registration' => $registration]) }}" class="border-t border-white/10 p-5">
                            @csrf

                            <div class="grid gap-6">
                                <section>
                                    <p class="{{ $labelClass }}">Team</p>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <label>
                                            <span class="{{ $labelClass }}">Team Name</span>
                                            <input name="team_name" value="{{ old('team_name', $registration->team_name) }}" class="{{ $inputClass }}">
                                            @error('team_name')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                        </label>
                                        <div class="rounded-lg bg-black/15 p-4">
                                            <p class="text-xs uppercase tracking-[.16em] text-white/32">Institution</p>
                                            <p class="mt-2 text-sm font-semibold text-white">{{ $registration->institution }}</p>
                                        </div>
                                    </div>
                                </section>

                                <section class="rounded-lg border border-volt/20 bg-volt/10 p-5">
                                    <p class="{{ $labelClass }}">Payment Package</p>
                                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                                        <label class="cursor-pointer rounded-lg border border-white/10 bg-black/15 p-4 transition has-[:checked]:border-volt/70 has-[:checked]:bg-volt/15">
                                            <input type="radio" name="payment_package" value="with_coach_kit" class="sr-only" @checked($selectedPackage === 'with_coach_kit')>
                                            <span class="block text-lg font-semibold text-white">With coach kit</span>
                                            <span class="mt-2 block text-3xl font-semibold text-white">BDT {{ number_format($packageAmounts['with_coach_kit']) }}</span>
                                        </label>
                                        <label class="cursor-pointer rounded-lg border border-white/10 bg-black/15 p-4 transition has-[:checked]:border-volt/70 has-[:checked]:bg-volt/15">
                                            <input type="radio" name="payment_package" value="without_coach_kit" class="sr-only" @checked($selectedPackage === 'without_coach_kit')>
                                            <span class="block text-lg font-semibold text-white">Without coach kit</span>
                                            <span class="mt-2 block text-3xl font-semibold text-white">BDT {{ number_format($packageAmounts['without_coach_kit']) }}</span>
                                        </label>
                                    </div>
                                    @error('payment_package')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror

                                    @if($currentBkashRecipient)
                                        <input type="hidden" name="iupc_bkash_recipient_payload" value="{{ $currentBkashPayload }}">
                                        <div class="mt-5 rounded-lg border border-white/10 bg-black/15 p-4">
                                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-[.18em] text-white/38">Send Money Recipient</p>
                                                    <p class="mt-2 text-3xl font-semibold tracking-wide text-white" data-bkash-number="{{ $currentBkashRecipient->bkash_number }}">{{ $currentBkashRecipient->bkash_number }}</p>
                                                </div>
                                                <button type="button" data-copy-bkash="{{ $currentBkashRecipient->bkash_number }}" class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-volt">
                                                    Copy Number
                                                </button>
                                            </div>
                                            <p class="mt-3 text-sm text-white/50">Use bKash or Nagad Send Money. This exact number and selected method will be recorded with your transaction for verification.</p>
                                        </div>
                                    @else
                                        <div class="mt-5 rounded-lg border border-amber-300/25 bg-amber-400/10 p-4">
                                            <p class="text-sm font-semibold text-amber-100">Payment recipient is not available right now.</p>
                                            <p class="mt-2 text-sm leading-6 text-white/54">Please wait for the event team to publish the payment number before submitting final registration.</p>
                                        </div>
                                    @endif
                                    @error('payment')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                    @error('iupc_bkash_recipient_payload')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror

                                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <span class="{{ $labelClass }}">Payment Method</span>
                                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                                @foreach (['bkash' => 'bKash', 'nagad' => 'Nagad'] as $value => $label)
                                                    <label class="flex min-h-16 cursor-pointer items-center justify-center rounded-lg border border-white/10 bg-black/15 px-4 py-3 transition has-[:checked]:border-volt/50 has-[:checked]:bg-volt/10 hover:border-white/24">
                                                        <input type="radio" name="payment_method" value="{{ $value }}" @checked($selectedMethod === $value) class="sr-only">
                                                        <img src="{{ asset($value === 'bkash' ? 'assets/bkash.webp' : 'assets/nagad.png') }}" alt="{{ $label }}" class="h-8 w-auto">
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error('payment_method')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                        </div>
                                        <label>
                                            <span class="{{ $labelClass }}">Transaction ID</span>
                                            <input name="trx_id" value="{{ old('trx_id', $registration->payment?->trx_id ?? $registration->finalRegistration?->trx_id) }}" class="{{ $inputClass }}">
                                            @error('trx_id')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                        </label>
                                    </div>
                                </section>

                                <section>
                                    <p class="{{ $labelClass }}">Participants</p>
                                    <div class="mt-4 grid gap-4">
                                        @foreach($registration->participants as $participant)
                                            @php($participantIndex = $loop->index)
                                            <article class="rounded-lg bg-black/15 p-4">
                                                <input type="hidden" name="participants[{{ $participantIndex }}][id]" value="{{ $participant->id }}">
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <h3 class="font-semibold text-white">{{ $participant->full_name }}</h3>
                                                    @if($participant->is_leader)
                                                        <span class="rounded-full border border-volt/30 px-2.5 py-1 text-xs text-volt">Leader</span>
                                                    @endif
                                                </div>
                                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                                    <label>
                                                        <span class="{{ $labelClass }}">Full Name</span>
                                                        <input name="participants[{{ $participantIndex }}][full_name]" value="{{ old("participants.$participantIndex.full_name", $participant->full_name) }}" class="{{ $inputClass }}">
                                                        @error("participants.$participantIndex.full_name")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                                    </label>
                                                    <label>
                                                        <span class="{{ $labelClass }}">Email</span>
                                                        <input type="email" name="participants[{{ $participantIndex }}][email]" value="{{ old("participants.$participantIndex.email", $participant->email) }}" class="{{ $inputClass }}">
                                                        @error("participants.$participantIndex.email")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                                    </label>
                                                    <label>
                                                        <span class="{{ $labelClass }}">Phone</span>
                                                        <input name="participants[{{ $participantIndex }}][phone]" value="{{ old("participants.$participantIndex.phone", $participant->phone) }}" class="{{ $inputClass }}">
                                                        @error("participants.$participantIndex.phone")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                                    </label>
                                                    <label>
                                                        <span class="{{ $labelClass }}">Student ID <span class="normal-case tracking-normal text-white/28">(if applicable)</span></span>
                                                        <input name="participants[{{ $participantIndex }}][student_id]" value="{{ old("participants.$participantIndex.student_id", $participant->student_id) }}" class="{{ $inputClass }}">
                                                        @error("participants.$participantIndex.student_id")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                                    </label>
                                                    <div class="rounded-lg bg-black/15 p-4 md:col-span-2">
                                                        <p class="text-xs uppercase tracking-[.16em] text-white/32">University</p>
                                                        <p class="mt-2 text-sm font-semibold text-white">{{ $participant->university }}</p>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <span class="{{ $labelClass }}">T-shirt Size</span>
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
                                            </article>
                                        @endforeach
                                    </div>
                                </section>

                                @if($registration->coach)
                                    <section>
                                        <p class="{{ $labelClass }}">Coach</p>
                                        <article class="mt-4 rounded-lg bg-black/15 p-4">
                                            <input type="hidden" name="coach[id]" value="{{ $registration->coach->id }}">
                                            <div class="grid gap-4 md:grid-cols-2">
                                                <label>
                                                    <span class="{{ $labelClass }}">Coach Name</span>
                                                    <input name="coach[name]" value="{{ old('coach.name', $registration->coach->name) }}" class="{{ $inputClass }}">
                                                    @error('coach.name')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                                </label>
                                                <label>
                                                    <span class="{{ $labelClass }}">Designation</span>
                                                    <input name="coach[designation]" value="{{ old('coach.designation', $registration->coach->designation) }}" class="{{ $inputClass }}">
                                                    @error('coach.designation')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                                </label>
                                                <label>
                                                    <span class="{{ $labelClass }}">Official Email</span>
                                                    <input type="email" name="coach[official_email]" value="{{ old('coach.official_email', $registration->coach->official_email) }}" class="{{ $inputClass }}">
                                                    @error('coach.official_email')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                                </label>
                                                <label>
                                                    <span class="{{ $labelClass }}">Contact Number</span>
                                                    <input name="coach[contact_number]" value="{{ old('coach.contact_number', $registration->coach->contact_number) }}" class="{{ $inputClass }}">
                                                    @error('coach.contact_number')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                                </label>
                                            </div>
                                            <div class="mt-4">
                                                <span class="{{ $labelClass }}">Coach T-shirt Size</span>
                                                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
                                                    @foreach($tshirtSizes as $size)
                                                        <label class="flex cursor-pointer items-center justify-center rounded-lg border border-white/10 bg-white/[.035] px-3 py-3 text-sm text-white transition has-[:checked]:border-volt/50 has-[:checked]:bg-volt/10">
                                                            <input type="radio" name="coach[tshirt_size]" value="{{ $size }}" @checked(old('coach.tshirt_size', $registration->coach->tshirt_size) === $size) class="sr-only">
                                                            <span class="font-medium">{{ $size }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @error('coach.tshirt_size')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                            </div>
                                        </article>
                                    </section>
                                @endif

                                <div class="flex justify-end">
                                    @if($canSubmit && $currentBkashRecipient)
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-ink transition hover:bg-volt sm:w-auto">
                                            Submit Team
                                        </button>
                                    @elseif(! $currentBkashRecipient)
                                        <span class="rounded-md border border-white/12 bg-white/[.04] px-6 py-3 text-sm font-semibold text-white/50">
                                            Payment number unavailable
                                        </span>
                                    @else
                                        <span class="rounded-md border border-white/12 bg-white/[.04] px-6 py-3 text-sm font-semibold text-white/50">
                                            University quota is full
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </details>
                @empty
                    <div class="rounded-lg border border-white/10 bg-white/[.035] px-6 py-12 text-center text-white/54">
                        No IUPC teams were found for this university.
                    </div>
                @endforelse
            </div>
        </div>

        <aside class="h-fit rounded-lg border border-white/10 bg-white/[.035] p-5">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-white/38">Activity Log</p>
            <div class="mt-5 grid gap-4">
                @forelse($logs as $log)
                    <article class="rounded-lg border border-white/10 bg-black/15 p-4">
                        <p class="text-sm font-semibold leading-6 text-white">{{ $log->summary }}</p>
                        <p class="mt-2 text-xs text-white/38">{{ $log->created_at?->format('d M Y, h:i A') }}</p>
                    </article>
                @empty
                    <p class="rounded-lg border border-white/10 bg-black/15 p-4 text-sm leading-6 text-white/50">No coach activity yet.</p>
                @endforelse
            </div>
        </aside>
    </div>
</section>

<script>
    document.querySelectorAll('[data-copy-bkash]').forEach((button) => {
        button.addEventListener('click', async () => {
            const number = button.dataset.copyBkash;
            if (!/^01[3-9]\d{8}$/.test(number || '')) {
                button.textContent = 'Invalid Number';
                return;
            }

            try {
                await navigator.clipboard.writeText(number);
                button.textContent = 'Copied';
                setTimeout(() => {
                    button.textContent = 'Copy Number';
                }, 1600);
            } catch (error) {
                button.textContent = 'Copy Failed';
                setTimeout(() => {
                    button.textContent = 'Copy Number';
                }, 1600);
            }
        });
    });
</script>
@endsection
