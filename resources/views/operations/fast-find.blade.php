@extends('layouts.operations')

@section('title', 'Fast Find')
@section('page-title', 'Fast Find')
@section('page-subtitle', 'Search people by name, email, or phone.')

@section('content')
    <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
        <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Fast Find</p>
                <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Find the person now.</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-coal/58">
                    Search tolerates case, spaces, punctuation, hyphens, and Bangladesh phone number formats.
                </p>
            </div>
            <a href="{{ route('operations.personnel.index') }}" class="rounded-xl border border-black/10 bg-paper px-4 py-3 text-center text-sm font-semibold text-coal transition hover:bg-white">
                Personnel Directory
            </a>
        </div>

        <form method="GET" action="{{ route('operations.fast-find.index') }}" class="mt-6">
            <label for="q" class="text-xs font-semibold uppercase tracking-[.16em] text-coal/45">Name, email, phone</label>
            <div class="mt-2 grid gap-3 sm:grid-cols-[1fr_auto]">
                <input id="q" name="q" value="{{ $searchQuery }}" autofocus class="w-full rounded-2xl border border-black/10 bg-paper px-4 py-4 text-xl font-semibold outline-none transition placeholder:text-coal/30 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10" placeholder="Search a person">
                <button class="rounded-2xl bg-primary px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                    Search
                </button>
            </div>
        </form>
    </section>

    @if($searchQuery !== '')
        <section class="mt-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold">{{ $results->count() }} result{{ $results->count() === 1 ? '' : 's' }}</p>
                    <p class="mt-1 text-xs text-coal/45">Query: {{ $searchQuery }}</p>
                </div>
                <a href="{{ route('operations.fast-find.index') }}" class="rounded-xl border border-black/10 bg-white px-4 py-2.5 text-sm font-semibold text-coal shadow-sm">
                    Clear
                </a>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @forelse($results as $result)
                    <article class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">{{ $result['type'] }}</span>
                                <h3 class="mt-4 break-words text-xl font-semibold">{{ $result['title'] }}</h3>
                                <p class="mt-1 break-words text-sm text-coal/55">{{ $result['subtitle'] }}</p>
                            </div>
                        </div>

                        <dl class="mt-5 grid gap-2 text-sm">
                            @foreach($result['lines'] as $key => $value)
                                <div class="grid grid-cols-[6.75rem_1fr] gap-3 rounded-xl bg-paper px-3 py-2">
                                    <dt class="text-xs font-semibold uppercase tracking-[.12em] text-coal/40">{{ $key }}</dt>
                                    <dd class="break-words font-medium text-coal/75">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-black/10 bg-white px-4 py-14 text-center text-sm text-coal/50 lg:col-span-2">
                        No match found.
                    </div>
                @endforelse
            </div>
        </section>
    @else
        <section class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/42">Try</p>
                <p class="mt-3 text-lg font-semibold">017..., +88017..., or 88017...</p>
            </div>
            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/42">Find</p>
                <p class="mt-3 text-lg font-semibold">Participants, coaches, and personnel</p>
            </div>
            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/42">Match</p>
                <p class="mt-3 text-lg font-semibold">Names, emails, and phone numbers</p>
            </div>
        </section>
    @endif
@endsection
