@extends('layouts.app')

@section('title', 'IUPC Slots | '.config('app.name'))
@section('meta_description', 'View university-wise assigned slots for the Inter University Programming Contest at IUT 12th ICT FEST 2026.')
@section('canonical', route('iupc.slots'))
@section('og_image', asset('assets/logos/iupc.png'))

@section('content')
<section class="px-4 pb-16 pt-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <a href="{{ url('/iupc') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/54 transition hover:text-white">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to IUPC
        </a>

        <div class="mt-10 grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.24em] text-volt/80">IUPC Slots</p>
                <h1 class="mt-5 text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">University-wise assigned slots.</h1>
                <p class="mt-6 max-w-3xl text-base leading-8 text-white/60">
                    Public allocation list for IUPC final registration. Slot counts are assigned per university and handled through the coach portal.
                </p>
            </div>

            <div class="rounded-lg border border-volt/25 bg-volt/10 p-5">
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-volt/80">Total Assigned Slots</p>
                <p class="mt-3 text-5xl font-semibold text-white">{{ number_format($totalSlots) }}</p>
                <p class="mt-3 text-sm leading-6 text-white/54">{{ number_format($allocations->count()) }} universities listed</p>
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        @if($allocations->isNotEmpty())
            <div class="overflow-hidden rounded-lg border border-white/10 bg-white/[.035]">
                <div class="grid grid-cols-[1fr_6rem] border-b border-white/10 bg-white/[.045] px-4 py-3 text-xs font-semibold uppercase tracking-[.16em] text-white/42 sm:grid-cols-[4rem_1fr_8rem] sm:px-5">
                    <span class="hidden sm:block">No.</span>
                    <span>University</span>
                    <span class="text-right">Slots</span>
                </div>

                <div class="divide-y divide-white/10">
                    @foreach($allocations as $allocation)
                        <article class="grid grid-cols-[1fr_6rem] items-center gap-4 px-4 py-4 sm:grid-cols-[4rem_1fr_8rem] sm:px-5">
                            <span class="hidden text-sm font-semibold text-white/36 sm:block">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="min-w-0">
                                <h2 class="break-words text-base font-semibold leading-6 text-white">{{ $allocation->name }}</h2>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex min-w-14 justify-center rounded-full border border-volt/30 bg-volt/10 px-3 py-1.5 text-sm font-semibold text-volt">
                                    {{ number_format($allocation->slot_count) }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-lg border border-white/10 bg-white/[.035] px-6 py-16 text-center">
                <p class="text-xs font-semibold uppercase tracking-[.22em] text-white/36">No Slots Published</p>
                <h2 class="mt-4 text-2xl font-semibold text-white">IUPC slot allocation is not published yet.</h2>
                <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-white/54">
                    Please check back later for the university-wise assigned slot list.
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
