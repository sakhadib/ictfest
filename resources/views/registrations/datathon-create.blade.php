@extends('layouts.app')

@section('title', 'Datathon Registration | '.config('app.name'))

@section('content')
@php
    $inputClass = 'mt-2 w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/24 focus:border-iris/50';
    $labelClass = 'text-xs font-medium uppercase tracking-[.16em] text-white/38';
    $oldParticipants = old('participants', [[]]);
    $memberCount = max(1, min(4, count($oldParticipants)));
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-4xl">
            <div class="inline-flex items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45">
                <span class="h-px w-10 bg-iris/70"></span>
                Datathon Registration
            </div>
            <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                Register your data squad.
            </h1>
            <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                Start with one participant and add teammates as needed. Member 1 will be treated as the leader and primary contact.
            </p>
            <div class="mt-8 rounded-lg border border-iris/30 bg-iris/10 p-5">
                <p class="text-sm font-semibold text-white">Cross-institution teams are allowed.</p>
                <p class="mt-2 text-sm leading-6 text-white/60">If the system does not show your institution name in suggestions, do not panic. Write the full name yourself.</p>
            </div>
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
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-iris/10 text-iris"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Team Size</p>
                        <p class="mt-1 text-sm font-medium text-white">1-4 members</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-iris/10 text-iris"><i class="fa-solid fa-ticket"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Fee</p>
                        <p class="mt-1 text-sm font-medium text-white">600 BDT</p>
                    </div>
                </div>
            </div>
        </aside>

        <form method="POST" action="{{ route('datathon.register.store') }}" class="border-y border-white/10 py-8">
            @csrf

            @if($errors->any())
                <div class="mb-8 rounded-md border border-red-400/30 bg-red-500/10 p-4 text-sm leading-6 text-red-100">
                    Please review the highlighted fields and submit again.
                </div>
            @endif

            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Team</p>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label>
                        <span class="{{ $labelClass }}">Team Name</span>
                        <input name="team_name" value="{{ old('team_name') }}" class="{{ $inputClass }}" placeholder="Team name">
                        @error('team_name')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                    <label>
                        <span class="{{ $labelClass }}">Campus Ambassador Name <span class="normal-case tracking-normal text-white/28">(optional)</span></span>
                        <input name="ca" value="{{ old('ca') }}" class="{{ $inputClass }}" placeholder="CA name, if any">
                        @error('ca')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                </div>
            </div>

            <div class="mt-12">
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Participants</p>

                <div class="mt-6">
                    @for($index = 0; $index < 4; $index++)
                        @php($visible = $index < $memberCount)
                        <div data-member-section="{{ $index }}" class="border-t border-white/10 py-8 first:border-t-0 first:pt-0 last:pb-0 {{ ! $visible ? 'hidden' : '' }}">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="text-lg font-semibold text-white">Member {{ $index + 1 }}</h3>
                                @if($index === 0)
                                    <span class="rounded-full border border-iris/30 px-3 py-1 text-xs text-iris">Leader</span>
                                @else
                                    <button type="button" data-remove-member="{{ $index }}" class="text-sm font-medium text-white/42 transition hover:text-red-300">
                                        Remove
                                    </button>
                                @endif
                            </div>

                            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                                <label>
                                    <span class="{{ $labelClass }}">Full Name</span>
                                    <input data-member-input="{{ $index }}" @disabled(! $visible) name="participants[{{ $index }}][full_name]" value="{{ old("participants.$index.full_name") }}" class="{{ $inputClass }}" placeholder="Full name">
                                    @error("participants.$index.full_name")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Kaggle Associated Email</span>
                                    <input data-member-input="{{ $index }}" @disabled(! $visible) type="email" name="participants[{{ $index }}][email]" value="{{ old("participants.$index.email") }}" class="{{ $inputClass }}" placeholder="name@example.com">
                                    @error("participants.$index.email")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Phone</span>
                                    <input data-member-input="{{ $index }}" @disabled(! $visible) name="participants[{{ $index }}][phone]" value="{{ old("participants.$index.phone") }}" class="{{ $inputClass }}" placeholder="Phone number">
                                    @error("participants.$index.phone")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Student ID <span class="normal-case tracking-normal text-white/28">(if applicable)</span></span>
                                    <input data-member-input="{{ $index }}" @disabled(! $visible) name="participants[{{ $index }}][student_id]" value="{{ old("participants.$index.student_id") }}" class="{{ $inputClass }}" placeholder="Student ID, if applicable">
                                    @error("participants.$index.student_id")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label class="sm:col-span-2">
                                    <span class="{{ $labelClass }}">University</span>
                                    <input data-member-input="{{ $index }}" data-participant-university data-university-search @disabled(! $visible) name="participants[{{ $index }}][university]" value="{{ old("participants.$index.university") }}" class="{{ $inputClass }}" placeholder="University name" autocomplete="off" spellcheck="false">
                                    @error("participants.$index.university")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="mt-6">
                    <button id="addMember" type="button" class="inline-flex w-full items-center justify-center rounded-md border border-white/12 bg-white/[.04] px-4 py-2.5 text-sm font-medium text-white/72 transition hover:border-iris/60 hover:text-white sm:w-auto">
                        Add Member
                    </button>
                </div>
            </div>

            <div class="mt-12">
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Payment</p>
                @include('registrations.partials.payment-instructions', [
                    'amount' => '600 BDT',
                    'number' => '01552382540',
                ])
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <span class="{{ $labelClass }}">Method</span>
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="bkash" class="peer sr-only" @checked(old('payment_method') === 'bkash')>
                                <span class="flex min-h-16 items-center justify-center rounded-md border border-white/10 bg-white/[.035] px-4 py-3 transition peer-checked:border-iris/60 peer-checked:bg-iris/10 hover:border-white/24">
                                    <img src="{{ asset('assets/bkash.webp') }}" alt="bKash" class="h-8 w-auto">
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="nagad" class="peer sr-only" @checked(old('payment_method') === 'nagad')>
                                <span class="flex min-h-16 items-center justify-center rounded-md border border-white/10 bg-white/[.035] px-4 py-3 transition peer-checked:border-iris/60 peer-checked:bg-iris/10 hover:border-white/24">
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
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-ink transition hover:bg-iris sm:w-auto">
                    Submit Registration
                </button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
    (() => {
        const addButton = document.getElementById('addMember');
        const sections = Array.from(document.querySelectorAll('[data-member-section]'));

        const visibleSections = () => sections.filter((section) => !section.classList.contains('hidden'));
        const updateAddButton = () => {
            addButton.disabled = visibleSections().length >= 4;
            addButton.classList.toggle('opacity-40', addButton.disabled);
        };
        const setSectionState = (section, visible) => {
            section.classList.toggle('hidden', !visible);
            section.querySelectorAll('[data-member-input]').forEach((field) => {
                field.disabled = !visible;

                if (!visible) {
                    field.value = '';
                }
            });
        };

        addButton?.addEventListener('click', () => {
            const section = sections.find((item) => item.classList.contains('hidden'));

            if (!section) {
                return;
            }

            setSectionState(section, true);
            updateAddButton();
        });

        document.querySelectorAll('[data-remove-member]').forEach((button) => {
            button.addEventListener('click', () => {
                const index = Number(button.dataset.removeMember);
                let section = sections[index];

                setSectionState(section, false);

                sections.slice(index + 1).forEach((nextSection) => {
                    if (nextSection.classList.contains('hidden')) {
                        return;
                    }

                    const targetInputs = Array.from(section.querySelectorAll('[data-member-input]'));
                    const sourceInputs = Array.from(nextSection.querySelectorAll('[data-member-input]'));

                    targetInputs.forEach((target, inputIndex) => {
                        target.value = sourceInputs[inputIndex]?.value ?? '';
                    });

                    setSectionState(section, true);
                    setSectionState(nextSection, false);
                    section = nextSection;
                });

                updateAddButton();
            });
        });

        updateAddButton();
    })();
</script>
@endpush
@include('registrations.partials.university-search', ['universities' => $universities])
@endsection
