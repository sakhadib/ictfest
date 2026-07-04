@extends('layouts.dashboard')

@section('title', 'Tshirt')
@section('page-title', 'Tshirt')
@section('page-subtitle', 'View event-wise T-shirt size counts from submitted participant and coach data.')

@section('content')
    <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Inventory view</p>
                    <h2 class="mt-1 text-lg font-semibold text-slate-950">T-shirt size summary</h2>
                </div>
                <div class="rounded-lg border border-primary/20 bg-primary/5 px-4 py-3 text-right">
                    <p class="text-xs font-semibold uppercase tracking-wide text-primary/70">Total Shirts</p>
                    <p class="mt-1 text-3xl font-semibold text-primary">{{ number_format($overallTotal) }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="sticky left-0 z-10 bg-slate-50 px-5 py-3 font-semibold">Event</th>
                        @foreach($sizes as $size)
                            <th class="px-5 py-3 text-right font-semibold">{{ $size }}</th>
                        @endforeach
                        <th class="px-5 py-3 text-right font-semibold">Participants</th>
                        <th class="px-5 py-3 text-right font-semibold">Coaches</th>
                        <th class="px-5 py-3 text-right font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rows as $row)
                        <tr>
                            <td class="sticky left-0 z-10 bg-white px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ $row['event']->code === '01' ? 'IUPC' : $row['event']->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">Event {{ $row['event']->code }}</p>
                            </td>
                            @foreach($sizes as $size)
                                <td class="px-5 py-4 text-right font-mono text-slate-700">
                                    {{ number_format($row['totals'][$size]) }}
                                    @if($row['coaches'][$size] > 0)
                                        <span class="mt-1 block text-[11px] text-slate-400">+{{ number_format($row['coaches'][$size]) }} coach</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-5 py-4 text-right font-mono text-slate-700">{{ number_format($row['participant_total']) }}</td>
                            <td class="px-5 py-4 text-right font-mono text-slate-700">{{ number_format($row['coach_total']) }}</td>
                            <td class="px-5 py-4 text-right font-mono font-semibold text-slate-950">{{ number_format($row['grand_total']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-slate-200 bg-slate-50">
                    <tr>
                        <th class="sticky left-0 z-10 bg-slate-50 px-5 py-4 text-left text-sm font-semibold text-slate-950">All Events</th>
                        @foreach($sizes as $size)
                            <th class="px-5 py-4 text-right font-mono text-sm font-semibold text-slate-950">{{ number_format($overall[$size]) }}</th>
                        @endforeach
                        <th colspan="3" class="px-5 py-4 text-right font-mono text-sm font-semibold text-primary">{{ number_format($overallTotal) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($rows as $row)
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Event {{ $row['event']->code }}</p>
                        <h3 class="mt-1 text-base font-semibold text-slate-950">{{ $row['event']->code === '01' ? 'IUPC' : $row['event']->name }}</h3>
                    </div>
                    <p class="text-3xl font-semibold text-primary">{{ number_format($row['grand_total']) }}</p>
                </div>
                <div class="mt-5 grid grid-cols-3 gap-2">
                    @foreach($sizes as $size)
                        <div class="rounded-md bg-slate-50 px-3 py-2 text-center">
                            <p class="text-xs font-semibold text-slate-500">{{ $size }}</p>
                            <p class="mt-1 font-mono text-lg font-semibold text-slate-950">{{ number_format($row['totals'][$size]) }}</p>
                        </div>
                    @endforeach
                </div>
            </article>
        @endforeach
    </section>
@endsection
