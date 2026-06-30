@extends('layouts.app')

@section('title', $event->name.' Registration | '.config('app.name'))

@section('content')
<section class="px-4 pb-32 pt-36 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <div class="grid items-center gap-12 lg:grid-cols-[.9fr_1.1fr]">
            <div>
                <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Registration</p>
                <h1 class="mt-6 text-4xl font-semibold leading-[1.06] text-white sm:text-5xl lg:text-6xl">
                    {{ $title ?? 'Registration Closed' }}
                </h1>
                <p class="mt-7 max-w-2xl text-base leading-8 text-white/58">
                    {{ $message ?? $event->name.' registration is currently closed.' }}
                </p>
                <div class="mt-10">
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="inline-flex items-center justify-center rounded-md border border-white/12 bg-white/[.04] px-5 py-3 text-sm font-semibold text-white/72 transition hover:border-white/28 hover:text-white">
                        Go back
                    </a>
                </div>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <p class="text-xs font-medium uppercase tracking-[.2em] text-white/35">Event</p>
                <h2 class="mt-4 text-2xl font-semibold text-white">{{ $event->name }}</h2>
                <div class="mt-8 grid gap-4">
                    <div class="flex items-center justify-between gap-4 border-t border-white/10 pt-4">
                        <span class="text-sm text-white/48">Event Code</span>
                        <span class="font-mono text-sm font-semibold text-white">{{ $event->code }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-t border-white/10 pt-4">
                        <span class="text-sm text-white/48">Status</span>
                        <span class="rounded-full border border-white/12 bg-white/[.06] px-3 py-1 text-xs font-semibold text-white/72">
                            {{ $status ?? 'Closed' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
