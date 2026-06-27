@extends('layouts.dashboard')

@section('title', 'Review Email')
@section('page-title', 'Review Email')
@section('page-subtitle', 'Confirm the message and recipients before queueing.')

@section('content')
    @include('dashboard.emails.partials.nav')

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

    <div class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
        <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
            <div class="border-b border-black/5 pb-5">
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Step 3</p>
                <h2 class="mt-3 text-2xl font-semibold text-coal">Final preview</h2>
                <p class="mt-2 text-sm leading-6 text-coal/60">
                    This is the exact plaintext structure recipients will receive.
                </p>
            </div>

            <div class="mt-6">
                <p class="text-sm font-semibold text-coal">Subject</p>
                <p class="mt-2 rounded-xl border border-black/10 bg-paper/70 px-4 py-3 text-sm text-coal/70">{{ $draft['subject'] }}</p>
            </div>

            <div class="mt-5">
                <p class="text-sm font-semibold text-coal">Body preview</p>
                <pre class="mt-2 max-h-[34rem] overflow-auto whitespace-pre-wrap break-words rounded-xl border border-black/10 bg-paper/70 p-4 text-sm leading-6 text-coal/75">{{ ($draft['mode'] ?? null) === 'custom' ? 'Hello,' : 'Hello <name>,' }}

{{ $draft['body'] }}

Best Regards,

IUT 12th ICT FEST
IUT Computer Society.</pre>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Recipient Summary</p>
                <h2 class="mt-3 text-4xl font-semibold text-coal">{{ $recipients->count() }}</h2>
                <p class="mt-2 text-sm text-coal/55">{{ $recipients->count() === 1 ? 'email will' : 'emails will' }} be queued on the low-priority queue.</p>

                <div class="mt-5 rounded-xl border border-black/10 bg-paper/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/45">Mode</p>
                    <p class="mt-2 text-sm font-semibold text-coal">{{ ($draft['mode'] ?? null) === 'custom' ? 'Custom email' : 'Event team leads' }}</p>

                    @if(($draft['mode'] ?? null) === 'custom')
                        <p class="mt-2 break-all text-sm text-coal/60">{{ $draft['custom_email'] }}</p>
                    @else
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($selectedEvents as $event)
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-coal/60 shadow-sm">
                                    {{ $event->code === '01' ? 'IUPC' : $event->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800">
                    Emails are throttled through the shared Resend limiter and sent after normal registration emails, SMS, and Telegram jobs.
                </div>
            </section>

            <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                <form method="POST" action="{{ route('dashboard.emails.send') }}" class="space-y-3">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-primary px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                        Queue Email
                    </button>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('dashboard.emails.compose') }}" class="rounded-lg border border-black/10 bg-white px-4 py-2.5 text-center text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
                            Edit Message
                        </a>
                        <a href="{{ route('dashboard.emails.recipients') }}" class="rounded-lg border border-black/10 bg-white px-4 py-2.5 text-center text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
                            Edit Recipients
                        </a>
                    </div>
                </form>
            </section>
        </aside>
    </div>
@endsection
