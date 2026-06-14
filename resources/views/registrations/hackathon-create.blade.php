@extends('layouts.app')

@section('title', 'Hackathon Registration | 12th IUT ICT FEST 2026')

@section('content')
@php
    $inputClass = 'mt-2 w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/24 focus:border-ember/50';
    $labelClass = 'text-xs font-medium uppercase tracking-[.16em] text-white/38';
    $showThirdMember = old('participants.2.full_name') || old('participants.2.email') || old('participants.2.phone') || old('participants.2.student_id') || old('participants.2.university');
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-4xl">
            <div class="inline-flex items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45">
                <span class="h-px w-10 bg-ember/70"></span>
                Hackathon Registration
            </div>
            <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                Register your API team.
            </h1>
            <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                Start with two members. Add a third teammate only if your team needs one. Member 1 will be treated as the leader and primary contact.
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
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-ember/10 text-ember"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Team Size</p>
                        <p class="mt-1 text-sm font-medium text-white">2-3 members</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-ember/10 text-ember"><i class="fa-solid fa-ticket"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Preliminary Fee</p>
                        <p class="mt-1 text-sm font-medium text-white">No fees</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-ember/10 text-ember"><i class="fa-solid fa-hashtag"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Code Format</p>
                        <p class="mt-1 text-sm font-medium text-white">02-48372</p>
                    </div>
                </div>
            </div>
        </aside>

        <form method="POST" action="{{ route('hackathon.register.store') }}" class="border-y border-white/10 py-8">
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
                        <span class="{{ $labelClass }}">Institution</span>
                        <input id="teamInstitution" name="institution" value="{{ old('institution') }}" class="{{ $inputClass }}" placeholder="Institution name">
                        @error('institution')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </label>
                </div>
            </div>

            <div class="mt-12">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Participants</p>
                    <button id="toggleThirdMember" type="button" class="inline-flex items-center justify-center rounded-md border border-white/12 bg-white/[.04] px-4 py-2.5 text-sm font-medium text-white/72 transition hover:border-ember/60 hover:text-white">
                        {{ $showThirdMember ? 'Remove 3rd Member' : 'Add 3rd Member' }}
                    </button>
                </div>

                <div class="mt-6">
                    @for($index = 0; $index < 3; $index++)
                        <div data-member-section="{{ $index }}" class="border-t border-white/10 py-8 first:border-t-0 first:pt-0 last:pb-0 {{ $index === 2 && ! $showThirdMember ? 'hidden' : '' }}">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="text-lg font-semibold text-white">Member {{ $index + 1 }}</h3>
                                @if($index === 0)
                                    <span class="rounded-full border border-ember/30 px-3 py-1 text-xs text-ember">Leader</span>
                                @endif
                            </div>

                            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                                <label>
                                    <span class="{{ $labelClass }}">Full Name</span>
                                    <input data-member-input="{{ $index }}" @disabled($index === 2 && ! $showThirdMember) name="participants[{{ $index }}][full_name]" value="{{ old("participants.$index.full_name") }}" class="{{ $inputClass }}" placeholder="Full name">
                                    @error("participants.$index.full_name")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Email</span>
                                    <input data-member-input="{{ $index }}" @disabled($index === 2 && ! $showThirdMember) type="email" name="participants[{{ $index }}][email]" value="{{ old("participants.$index.email") }}" class="{{ $inputClass }}" placeholder="name@example.com">
                                    @error("participants.$index.email")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Phone</span>
                                    <input data-member-input="{{ $index }}" @disabled($index === 2 && ! $showThirdMember) name="participants[{{ $index }}][phone]" value="{{ old("participants.$index.phone") }}" class="{{ $inputClass }}" placeholder="Phone number">
                                    @error("participants.$index.phone")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Student ID</span>
                                    <input data-member-input="{{ $index }}" @disabled($index === 2 && ! $showThirdMember) name="participants[{{ $index }}][student_id]" value="{{ old("participants.$index.student_id") }}" class="{{ $inputClass }}" placeholder="Student ID">
                                    @error("participants.$index.student_id")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label class="sm:col-span-2">
                                    <span class="{{ $labelClass }}">University</span>
                                    <input data-member-input="{{ $index }}" data-participant-university @disabled($index === 2 && ! $showThirdMember) name="participants[{{ $index }}][university]" value="{{ old("participants.$index.university") }}" class="{{ $inputClass }}" placeholder="University name">
                                    @error("participants.$index.university")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                            </div>
                        </div>
                    @endfor
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
        const institution = document.getElementById('teamInstitution');
        const universities = Array.from(document.querySelectorAll('[data-participant-university]'));
        const toggle = document.getElementById('toggleThirdMember');
        const thirdSection = document.querySelector('[data-member-section="2"]');
        const thirdInputs = Array.from(document.querySelectorAll('[data-member-input="2"]'));

        let previousInstitution = institution?.value.trim() ?? '';

        const syncUniversities = () => {
            const nextInstitution = institution.value.trim();

            universities.forEach((field) => {
                const current = field.value.trim();

                if (current === '' || current === previousInstitution) {
                    field.value = nextInstitution;
                }
            });

            previousInstitution = nextInstitution;
        };

        institution?.addEventListener('input', syncUniversities);
        institution?.addEventListener('change', syncUniversities);

        toggle?.addEventListener('click', () => {
            const isHidden = thirdSection.classList.toggle('hidden');

            thirdInputs.forEach((field) => {
                field.disabled = isHidden;

                if (isHidden) {
                    field.value = '';
                }
            });

            if (!isHidden) {
                thirdInputs.find((field) => field.matches('[data-participant-university]')).value = institution.value.trim();
            }

            toggle.textContent = isHidden ? 'Add 3rd Member' : 'Remove 3rd Member';
        });
    })();
</script>
@endpush
@endsection
