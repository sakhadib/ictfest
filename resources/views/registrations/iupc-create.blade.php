@extends('layouts.app')

@section('title', 'IUPC Registration | 12th IUT ICT FEST 2026')

@section('content')
@php
    $inputClass = 'mt-2 w-full rounded-md border border-white/10 bg-white/[.035] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/24 focus:border-volt/50';
    $labelClass = 'text-xs font-medium uppercase tracking-[.16em] text-white/38';
@endphp

<section class="px-4 pb-20 pt-36 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-4xl">
            <div class="inline-flex items-center gap-3 text-xs font-medium uppercase tracking-[.2em] text-white/45">
                <span class="h-px w-10 bg-volt/70"></span>
                IUPC Registration
            </div>
            <h1 class="mt-8 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                Register your squad for IUPC
            </h1>
            <p class="mt-8 max-w-2xl text-base leading-8 text-white/58">
                Member 1 will be treated as the team leader and primary contact.
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
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-volt/10 text-volt"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Team Size</p>
                        <p class="mt-1 text-sm font-medium text-white">3 members</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="grid h-10 w-10 place-items-center rounded-md bg-volt/10 text-volt"><i class="fa-solid fa-hashtag"></i></div>
                    <div>
                        <p class="text-xs uppercase tracking-[.16em] text-white/32">Code Format</p>
                        <p class="mt-1 text-sm font-medium text-white">01-48372</p>
                    </div>
                </div>
            </div>
        </aside>

        <form method="POST" action="{{ route('iupc.register.store') }}" class="border-y border-white/10 py-8">
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
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Participants</p>
                <div class="mt-6">
                    @for($index = 0; $index < 3; $index++)
                        <div class="border-t border-white/10 py-8 first:border-t-0 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="text-lg font-semibold text-white">Member {{ $index + 1 }}</h3>
                                @if($index === 0)
                                    <span class="rounded-full border border-volt/30 px-3 py-1 text-xs text-volt">Leader</span>
                                @endif
                            </div>

                            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                                <label>
                                    <span class="{{ $labelClass }}">Full Name</span>
                                    <input name="participants[{{ $index }}][full_name]" value="{{ old("participants.$index.full_name") }}" class="{{ $inputClass }}" placeholder="Full name">
                                    @error("participants.$index.full_name")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Email</span>
                                    <input type="email" name="participants[{{ $index }}][email]" value="{{ old("participants.$index.email") }}" class="{{ $inputClass }}" placeholder="name@example.com">
                                    @error("participants.$index.email")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Phone</span>
                                    <input name="participants[{{ $index }}][phone]" value="{{ old("participants.$index.phone") }}" class="{{ $inputClass }}" placeholder="Phone number">
                                    @error("participants.$index.phone")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label>
                                    <span class="{{ $labelClass }}">Student ID</span>
                                    <input name="participants[{{ $index }}][student_id]" value="{{ old("participants.$index.student_id") }}" class="{{ $inputClass }}" placeholder="Student ID">
                                    @error("participants.$index.student_id")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                                <label class="sm:col-span-2">
                                    <span class="{{ $labelClass }}">University</span>
                                    <input data-participant-university name="participants[{{ $index }}][university]" value="{{ old("participants.$index.university") }}" class="{{ $inputClass }}" placeholder="University name">
                                    @error("participants.$index.university")<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                                </label>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="mt-10 flex justify-end">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-ink transition hover:bg-volt sm:w-auto">
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

        if (!institution || universities.length === 0) {
            return;
        }

        let previousInstitution = institution.value.trim();

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

        institution.addEventListener('input', syncUniversities);
        institution.addEventListener('change', syncUniversities);
    })();
</script>
@endpush
@endsection
