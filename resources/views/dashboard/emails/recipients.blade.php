@extends('layouts.dashboard')

@section('title', 'Email Recipients')
@section('page-title', 'Email Recipients')
@section('page-subtitle', 'Choose who should receive this notification.')

@section('content')
    @include('dashboard.emails.partials.nav')

    @if(session('status'))
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $mode = old('mode', $draft['mode'] ?? 'events');
        $selectedCodes = old('event_codes', $draft['event_codes'] ?? []);
    @endphp

    <form method="POST" action="{{ route('dashboard.emails.recipients.store') }}" class="mx-auto max-w-5xl">
        @csrf
        <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
            <div class="border-b border-black/5 pb-5">
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Step 2</p>
                <h2 class="mt-3 text-2xl font-semibold text-coal">Select recipients</h2>
                <p class="mt-2 text-sm leading-6 text-coal/60">
                    Choose event team leads for a broadcast, or send one custom email for a test or special case.
                </p>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <label class="cursor-pointer rounded-2xl border p-5 transition {{ $mode === 'events' ? 'border-primary bg-primary/5' : 'border-black/10 bg-paper/60 hover:border-primary/30' }}">
                    <span class="flex items-start gap-3">
                        <input type="radio" name="mode" value="events" class="mt-1 h-4 w-4 border-black/20 text-primary focus:ring-primary" @checked($mode === 'events') data-recipient-mode>
                        <span>
                            <span class="block text-base font-semibold text-coal">Event team leads</span>
                            <span class="mt-1 block text-sm leading-6 text-coal/55">Send to unique team lead emails from selected events.</span>
                        </span>
                    </span>
                </label>

                <label class="cursor-pointer rounded-2xl border p-5 transition {{ $mode === 'custom' ? 'border-primary bg-primary/5' : 'border-black/10 bg-paper/60 hover:border-primary/30' }}">
                    <span class="flex items-start gap-3">
                        <input type="radio" name="mode" value="custom" class="mt-1 h-4 w-4 border-black/20 text-primary focus:ring-primary" @checked($mode === 'custom') data-recipient-mode>
                        <span>
                            <span class="block text-base font-semibold text-coal">Custom email</span>
                            <span class="mt-1 block text-sm leading-6 text-coal/55">Send to one manually entered address.</span>
                        </span>
                    </span>
                </label>
            </div>

            <div class="mt-6" data-events-panel>
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach($events as $event)
                        <label for="event-{{ $event->code }}" class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-black/10 bg-paper/60 px-4 py-3 transition hover:border-primary/30 hover:bg-primary/5">
                            <span class="flex min-w-0 items-center gap-3">
                                <input
                                    id="event-{{ $event->code }}"
                                    type="checkbox"
                                    name="event_codes[]"
                                    value="{{ $event->code }}"
                                    class="h-4 w-4 rounded border-black/20 text-primary focus:ring-primary"
                                    @checked(in_array($event->code, $selectedCodes, true))
                                >
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold text-coal">{{ $event->code === '01' ? 'IUPC' : $event->name }}</span>
                                    <span class="mt-1 block text-xs text-coal/50">Event {{ $event->code }}</span>
                                </span>
                            </span>
                            <span class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-semibold text-coal/60 shadow-sm">
                                {{ $event->team_lead_email_count }} emails
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-6" data-custom-panel>
                <label for="custom_email" class="text-sm font-semibold text-coal">Custom email address</label>
                <input
                    id="custom_email"
                    type="email"
                    name="custom_email"
                    value="{{ old('custom_email', $draft['custom_email'] ?? '') }}"
                    placeholder="name@example.com"
                    class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10"
                >
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-between">
                <a href="{{ route('dashboard.emails.compose') }}" class="rounded-lg border border-black/10 bg-white px-4 py-2.5 text-center text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
                    Back to Compose
                </a>
                <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                    Continue to Review
                </button>
            </div>
        </section>
    </form>

    <script>
        (() => {
            const modeInputs = document.querySelectorAll('[data-recipient-mode]');
            const eventsPanel = document.querySelector('[data-events-panel]');
            const customPanel = document.querySelector('[data-custom-panel]');

            const sync = () => {
                const mode = document.querySelector('[data-recipient-mode]:checked')?.value || 'events';
                eventsPanel.classList.toggle('hidden', mode !== 'events');
                customPanel.classList.toggle('hidden', mode !== 'custom');
            };

            modeInputs.forEach((input) => input.addEventListener('change', sync));
            sync();
        })();
    </script>
@endsection
