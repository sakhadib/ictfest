@extends('layouts.dashboard')

@section('title', 'Event Status')
@section('page-title', 'Event Status')
@section('page-subtitle', 'Control which event registrations are visible and open.')

@section('content')
    @if (session('status'))
        <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <section class="grid gap-5 lg:grid-cols-2">
        @foreach($events as $event)
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Event {{ $event->code }}</p>
                        <h2 class="mt-1 text-lg font-semibold text-slate-950">{{ $event->name }}</h2>
                    </div>

                    <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $event->is_live ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-600' }}">
                        {{ $event->is_live ? 'Live' : 'Offline' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('dashboard.event-status.update', ['event' => $event->code]) }}" class="mt-6">
                    @csrf
                    @method('PATCH')

                    <label>
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rulebook Link</span>
                        <input
                            type="url"
                            name="rulebook_link"
                            value="{{ old('rulebook_link', $event->rulebook_link) }}"
                            placeholder="https://example.com/rulebook.pdf"
                            class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-primary/10"
                        >
                    </label>

                    @error('rulebook_link')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <label class="mt-4 block">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</span>
                        <input
                            type="number"
                            name="amount"
                            min="0"
                            value="{{ old('amount', $event->amount ?? 0) }}"
                            placeholder="0"
                            class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-primary/10"
                        >
                    </label>

                    @error('amount')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <button type="submit" name="action" value="save" class="rounded-md border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Save Link
                        </button>

                        @if($event->is_live)
                            <button type="submit" name="action" value="down" class="rounded-md border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-800 transition hover:bg-amber-100">
                                Take Down
                            </button>
                        @else
                            <button type="submit" name="action" value="live" class="rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">
                                Make Live
                            </button>
                        @endif
                    </div>
                </form>
            </article>
        @endforeach
    </section>
@endsection
