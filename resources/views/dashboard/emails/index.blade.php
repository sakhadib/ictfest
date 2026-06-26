@extends('layouts.dashboard')

@section('title', 'Email')
@section('page-title', 'Email')
@section('page-subtitle', 'Send queued plaintext emails to team leads by event.')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[.85fr_1.15fr]">
        <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Recipients</p>
            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <h2 class="text-2xl font-semibold text-coal">Select event team leads</h2>
                <a href="{{ route('dashboard.emails.history') }}" class="inline-flex items-center justify-center rounded-lg border border-primary/20 bg-white px-4 py-2 text-sm font-semibold text-primary shadow-sm transition hover:bg-primary/5">
                    Email History
                </a>
            </div>
            <p class="mt-3 text-sm leading-6 text-coal/60">
                Select one or more events. Each unique team lead email from those events will receive one queued email.
            </p>

            <div class="mt-6 grid gap-3">
                @foreach($events as $event)
                    <label for="event-{{ $event->code }}" class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-black/10 bg-paper/60 px-4 py-3 transition hover:border-primary/30 hover:bg-primary/5">
                        <span class="flex min-w-0 items-center gap-3">
                            <input
                                form="dashboardEmailForm"
                                id="event-{{ $event->code }}"
                                type="checkbox"
                                name="event_codes[]"
                                value="{{ $event->code }}"
                                class="h-4 w-4 rounded border-black/20 text-primary focus:ring-primary"
                                @checked(in_array($event->code, old('event_codes', []), true))
                            >
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-coal">{{ $event->code === '01' ? 'IUPC' : $event->name }}</span>
                                <span class="mt-1 block text-xs text-coal/50">{{ $event->code }}</span>
                            </span>
                        </span>
                        <span class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-semibold text-coal/60 shadow-sm">
                            {{ $event->team_lead_email_count }} emails
                        </span>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
            @if(session('status'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="dashboardEmailForm" method="POST" action="{{ route('dashboard.emails.send') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="mode" value="{{ old('mode', 'events') }}" data-email-mode>

                <div class="flex flex-col gap-3 rounded-xl border border-black/10 bg-paper/60 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-coal">Send a custom test email</p>
                        <p class="mt-1 text-xs leading-5 text-coal/55">Use this when you want to send to one address instead of selected event team leads.</p>
                    </div>
                    <button type="button" data-custom-email-toggle class="rounded-lg border border-primary/20 bg-white px-4 py-2 text-sm font-semibold text-primary shadow-sm transition hover:bg-primary/5">
                        Custom
                    </button>
                </div>

                <div data-custom-email-panel class="{{ old('mode') === 'custom' ? '' : 'hidden' }}">
                    <label for="custom_email" class="text-sm font-semibold text-coal">Custom email address</label>
                    <input
                        id="custom_email"
                        type="email"
                        name="custom_email"
                        value="{{ old('custom_email') }}"
                        placeholder="name@example.com"
                        class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10"
                    >
                </div>

                <div>
                    <label for="subject" class="text-sm font-semibold text-coal">Subject</label>
                    <input
                        id="subject"
                        type="text"
                        name="subject"
                        value="{{ old('subject') }}"
                        required
                        class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10"
                    >
                </div>

                <div>
                    <label for="body" class="text-sm font-semibold text-coal">Body</label>
                    <textarea
                        id="body"
                        name="body"
                        rows="14"
                        required
                        class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 font-mono text-sm leading-6 outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10"
                        placeholder="Write the message body here. Tabs and line breaks will be preserved."
                    >{{ old('body') }}</textarea>
                    <p class="mt-2 text-xs leading-5 text-coal/55">
                        Final email format: Hello name, then your body, then the IUTCS signature.
                    </p>
                </div>

                <div class="rounded-xl border border-black/10 bg-paper/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/45">Preview wrapper</p>
                    <pre class="mt-3 whitespace-pre-wrap break-words text-sm leading-6 text-coal/70">Hello {{ old('mode') === 'custom' ? '' : '<name>' }},

[your body]

Best Regards,

IUT 12th ICT FEST
IUT Computer Society.</pre>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="button" data-event-email-mode class="rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
                        Use event recipients
                    </button>
                    <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                        Queue Low-Priority Email
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script>
        (() => {
            const mode = document.querySelector('[data-email-mode]');
            const panel = document.querySelector('[data-custom-email-panel]');
            const customButton = document.querySelector('[data-custom-email-toggle]');
            const eventButton = document.querySelector('[data-event-email-mode]');

            const setMode = (value) => {
                mode.value = value;
                panel.classList.toggle('hidden', value !== 'custom');
                customButton.textContent = value === 'custom' ? 'Using custom' : 'Custom';
            };

            customButton?.addEventListener('click', () => setMode('custom'));
            eventButton?.addEventListener('click', () => setMode('events'));
            setMode(mode.value || 'events');
        })();
    </script>
@endsection
