@extends('layouts.app')

@section('title', ($eventRecord->name ?? 'Event').' Rulebook | '.config('app.name'))
@section('meta_description', 'View the rulebook for '.$eventRecord->name.' at '.config('app.name').'.')
@section('canonical', route('events.rulebook', ['eventSlug' => $eventSlug]))

@section('content')
<section class="px-4 pb-14 pt-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-6 border-b border-white/10 pb-10 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <a href="{{ url('/'.$eventSlug) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/54 transition hover:text-white">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to {{ $eventRecord->name }}
                </a>
                <p class="mt-8 text-xs font-semibold uppercase tracking-[.22em] text-white/38">Rulebook</p>
                <h1 class="mt-4 text-4xl font-semibold leading-tight text-white sm:text-5xl">{{ $eventRecord->name }}</h1>
                <p class="mt-5 max-w-2xl text-sm leading-7 text-white/56">
                    The rulebook is loaded from the event configuration. If the embedded document does not appear, use the direct link.
                </p>
            </div>

            @if(filled($eventRecord->rulebook_link))
                <a href="{{ $eventRecord->rulebook_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-volt">
                    Open in New Tab
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>
            @else
                <span class="inline-flex items-center justify-center gap-3 rounded-md border border-white/12 bg-white/[.04] px-5 py-3 text-sm font-semibold text-white/58">
                    Rulebook not published
                </span>
            @endif
        </div>
    </div>
</section>

<section class="px-4 pb-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        @if(filled($eventRecord->rulebook_link))
            <div class="mb-5 flex flex-col gap-3 rounded-lg border border-white/10 bg-white/[.035] p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">If the rulebook is not visible</h2>
                    <p class="mt-1 text-sm text-white/54">Some document providers block iframe previews. Open the rulebook directly if the frame stays blank.</p>
                </div>
                <a href="{{ $eventRecord->rulebook_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-md border border-white/12 bg-white/[.06] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/12">
                    Open Direct Link
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>
            </div>

            <div class="overflow-hidden rounded-lg border border-white/10 bg-white/[.035] shadow-glow">
                <iframe
                    src="{{ $eventRecord->rulebook_link }}"
                    title="{{ $eventRecord->name }} Rulebook"
                    class="h-[76vh] min-h-[34rem] w-full bg-white"
                    loading="lazy"
                ></iframe>
            </div>
        @else
            <div class="rounded-lg border border-white/10 bg-white/[.035] px-6 py-16 text-center sm:px-10">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-full border border-white/10 bg-white/[.05] text-white/54">
                    <i class="fa-solid fa-book-open text-xl"></i>
                </div>
                <h2 class="mt-6 text-2xl font-semibold text-white">Rulebook is not published yet</h2>
                <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-white/56">
                    Please check again later. Once the rulebook link is added from the dashboard, it will appear here automatically.
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
