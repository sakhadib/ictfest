@extends('layouts.dashboard')

@section('title', 'IUPC Slots')
@section('page-title', 'IUPC Slots')
@section('page-subtitle', 'Canonical university quotas, coaches, links, and IUPC final intake.')

@section('content')
    <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Inter University Programming Contest</p>
                    <h2 class="mt-1 text-lg font-semibold">University slot allocation</h2>
                </div>
                <div class="rounded-lg border border-primary/20 bg-primary/5 px-4 py-3 text-right">
                    <p class="text-xs font-semibold uppercase tracking-wide text-primary/70">Total Slots</p>
                    <p id="slotTotal" class="mt-1 text-3xl font-semibold text-primary">0</p>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="mx-5 mt-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mx-5 mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Please review the submitted data.
            </div>
        @endif

        <div class="divide-y divide-slate-100">
                @forelse($allocations as $allocation)
                    <article class="p-5">
                        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_16rem]">
                            <div class="min-w-0">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="min-w-0">
                                        <label class="block">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Canonical University</span>
                                            <input form="slot-save-form" name="names[{{ $allocation->id }}]" value="{{ old("names.$allocation->id", $allocation->name) }}" class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-lg font-semibold text-slate-950 outline-none focus:border-primary/50">
                                        </label>
                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-medium text-slate-600">{{ $allocation->registration_count }} registrations</span>
                                            <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-medium text-slate-600">{{ $allocation->coaches->count() }} coaches</span>
                                            <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-medium text-slate-600">{{ $allocation->submitted_count }} submitted</span>
                                            <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-medium text-slate-600">{{ $allocation->remaining_count }} remaining</span>
                                        </div>
                                    </div>

                                    <button form="send-links-{{ $allocation->id }}" type="submit" class="rounded-md bg-slate-950 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                        Send coach email/SMS
                                    </button>
                                </div>

                                <div class="mt-5 grid gap-4 lg:grid-cols-2">
                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Aliases</p>
                                        <div class="mt-3 grid gap-3">
                                            @foreach($allocation->aliases as $alias)
                                                <div class="rounded-md border border-slate-200 bg-white p-3">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div>
                                                            <p class="text-sm font-semibold text-slate-800">{{ $alias->raw_name }}</p>
                                                            <p class="mt-1 text-xs text-slate-500">{{ $alias->source_count }} source registrations</p>
                                                        </div>
                                                        <form method="POST" action="{{ route('dashboard.iupc-slots.aliases.update', ['alias' => $alias]) }}" class="flex shrink-0 gap-2">
                                                            @csrf
                                                            @method('PATCH')
                                                            <select name="iupc_university_allocation_id" class="w-44 rounded-md border border-slate-200 bg-white px-2 py-1.5 text-xs">
                                                                @foreach($allocations as $target)
                                                                    <option value="{{ $target->id }}" @selected($target->id === $allocation->id)>{{ $target->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="submit" class="rounded-md border border-slate-200 px-2 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Move</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Coaches & Links</p>
                                        <div class="mt-3 grid gap-3">
                                            @forelse($allocation->coaches as $coach)
                                                @php($activeLink = $coach->links->where('disabled_at', null)->sortByDesc('created_at')->first())
                                                <div class="rounded-md border border-slate-200 bg-white p-3">
                                                    <p class="text-sm font-semibold text-slate-800">{{ $coach->name }}</p>
                                                    <p class="mt-1 break-all text-xs text-slate-500">{{ $coach->official_email }}</p>
                                                    <p class="text-xs text-slate-500">{{ $coach->contact_number }}</p>
                                                    @if($activeLink)
                                                        <div class="mt-3 flex flex-wrap gap-2">
                                                            <form method="POST" action="{{ route('dashboard.iupc-slots.links.disable', ['link' => $activeLink]) }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="rounded-md border border-amber-200 px-2 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50">Disable</button>
                                                            </form>
                                                            <form method="POST" action="{{ route('dashboard.iupc-slots.links.regenerate', ['link' => $activeLink]) }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="rounded-md border border-slate-200 px-2 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Regenerate</button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <p class="mt-2 text-xs font-medium text-slate-400">No active link yet</p>
                                                    @endif
                                                </div>
                                            @empty
                                                <p class="rounded-md border border-slate-200 bg-white px-3 py-4 text-sm text-slate-500">No coaches found for this university.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <label class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Slots</span>
                                <input form="slot-save-form" data-slot-input name="slots[{{ $allocation->id }}]" value="{{ old("slots.$allocation->id", $allocation->slot_count) }}" type="number" min="0" max="999" class="mt-3 w-full rounded-md border border-slate-200 bg-white px-4 py-3 text-3xl font-semibold outline-none focus:border-primary/50">
                                <p class="mt-3 text-xs leading-5 text-slate-500">Coach-submitted final teams consume this quota.</p>
                            </label>
                        </div>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center text-slate-500">
                        No IUPC university registrations found yet.
                    </div>
                @endforelse
        </div>

        <div class="border-t border-slate-200 px-5 py-4 text-right">
            <button form="slot-save-form" type="submit" class="rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">Save Slots</button>
        </div>
    </section>

    <form id="slot-save-form" method="POST" action="{{ route('dashboard.iupc-slots.update') }}">
        @csrf
        @method('PATCH')
    </form>

    @foreach($allocations as $allocation)
        <form id="send-links-{{ $allocation->id }}" method="POST" action="{{ route('dashboard.iupc-slots.send-links', ['allocation' => $allocation]) }}">
            @csrf
        </form>
    @endforeach

    <script>
        const slotInputs = Array.from(document.querySelectorAll('[data-slot-input]'));
        const slotTotal = document.getElementById('slotTotal');

        function updateSlotTotal() {
            const total = slotInputs.reduce((sum, input) => sum + (parseInt(input.value || '0', 10) || 0), 0);
            slotTotal.textContent = total.toLocaleString();
        }

        slotInputs.forEach((input) => input.addEventListener('input', updateSlotTotal));
        updateSlotTotal();
    </script>
@endsection
