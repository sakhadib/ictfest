@extends('layouts.app')

@section('title', 'Datathon Registration Submitted | 12th IUT ICT FEST 2026')

@section('content')
<section class="px-4 pb-32 pt-36 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl">
        <div class="rounded-lg border border-white/10 bg-white/[.035] p-6 text-center sm:p-10">
            <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-iris/10 text-iris">
                <i class="fa-solid fa-check"></i>
            </div>
            <p class="mt-8 text-xs font-medium uppercase tracking-[.22em] text-white/38">Registration Submitted</p>
            <h1 class="mt-4 text-3xl font-semibold text-white sm:text-4xl">Keep this registration code.</h1>
            <p class="mx-auto mt-5 max-w-xl text-sm leading-7 text-white/56">
                Your Datathon registration and payment information have been submitted for review.
            </p>

            <div class="mx-auto mt-10 max-w-md rounded-lg border border-iris/25 bg-iris/10 p-6">
                <p class="text-xs uppercase tracking-[.18em] text-white/42">Registration Code</p>
                <p class="mt-3 text-4xl font-semibold text-white">{{ $registration->registration_code }}</p>
            </div>

            <div class="mt-10 grid gap-4 text-left sm:grid-cols-2">
                <div class="rounded-md border border-white/10 bg-black/15 p-4">
                    <p class="text-xs uppercase tracking-[.16em] text-white/32">Team</p>
                    <p class="mt-2 text-sm font-medium text-white">{{ $registration->team_name }}</p>
                </div>
                <div class="rounded-md border border-white/10 bg-black/15 p-4">
                    <p class="text-xs uppercase tracking-[.16em] text-white/32">Members</p>
                    <p class="mt-2 text-sm font-medium text-white">{{ $registration->participants->count() }}</p>
                </div>
                <div class="rounded-md border border-white/10 bg-black/15 p-4">
                    <p class="text-xs uppercase tracking-[.16em] text-white/32">Payment</p>
                    <p class="mt-2 text-sm font-medium text-white">{{ ucfirst($registration->payment?->method) }} / {{ $registration->payment?->trx_id }}</p>
                </div>
                <div class="rounded-md border border-white/10 bg-black/15 p-4">
                    <p class="text-xs uppercase tracking-[.16em] text-white/32">Status</p>
                    <p class="mt-2 text-sm font-medium text-white">{{ ucfirst($registration->status) }}</p>
                </div>
            </div>

            <div class="mt-10 flex justify-center">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-md bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:bg-iris">
                    Back Home
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
