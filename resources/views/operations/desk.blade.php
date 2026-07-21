@extends('layouts.operations')

@section('title', 'Desk')
@section('page-title', 'Desk')
@section('page-subtitle', 'Search a registration code and inspect the full registration record.')

@section('content')
    @php
        $inputClass = 'w-full rounded-2xl border border-black/10 bg-paper px-4 py-4 text-xl font-semibold uppercase outline-none transition placeholder:text-coal/30 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10';
        $labelClass = 'text-xs font-semibold uppercase tracking-[.16em] text-coal/42';
        $valueClass = 'mt-1 break-words text-sm font-semibold text-coal/78';
        $formatDate = fn ($date) => $date ? $date->format('d M Y, h:i A') : '-';
        $packageLabels = [
            'with_coach_kit' => 'With coach kit',
            'without_coach_kit' => 'Without coach kit',
        ];
        $finalRegistration = $registration?->finalRegistration;
        $payment = $registration?->payment;
        $iupcPackage = $finalRegistration?->payment_package ? ($packageLabels[$finalRegistration->payment_package] ?? $finalRegistration->payment_package) : null;
        $iupcAmount = $finalRegistration?->payment_amount ?? $payment?->amount;
        $tshirtCounts = collect();
        if ($registration) {
            $includeCoachTshirt = $registration->event?->code === '01'
                && $finalRegistration?->payment_package === 'with_coach_kit'
                && $registration->coach?->tshirt_size;

            $tshirtCounts = $registration->participants
                ->pluck('tshirt_size')
                ->when($includeCoachTshirt, fn ($sizes) => $sizes->push($registration->coach->tshirt_size))
                ->filter(fn ($size) => trim((string) $size) !== '')
                ->map(fn ($size) => strtoupper((string) $size))
                ->countBy();
        }
    @endphp

    <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
        <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Desk Lookup</p>
                <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Registration intelligence by code.</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-coal/58">
                    Use this at the physical desk to verify team, participants, coach, payment, package, T-shirt sizes, and final-registration records.
                </p>
            </div>
            <a href="{{ route('operations.fast-find.index') }}" class="rounded-xl border border-black/10 bg-paper px-4 py-3 text-center text-sm font-semibold text-coal transition hover:bg-white">
                Fast Find
            </a>
        </div>

        <form method="GET" action="{{ route('operations.desk.index') }}" class="mt-6">
            <label for="code" class="{{ $labelClass }}">Registration code</label>
            <div class="mt-2 grid gap-3 sm:grid-cols-[1fr_auto]">
                <input id="code" name="code" value="{{ $code }}" autofocus class="{{ $inputClass }}" placeholder="Example: 01-48372" autocomplete="off" spellcheck="false">
                <button class="rounded-2xl bg-primary px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                    Search
                </button>
            </div>
            <p class="mt-3 text-sm leading-6 text-coal/50">
                Separators are tolerated, so <span class="font-mono">01-48372</span> and <span class="font-mono">0148372</span> resolve to the same registration when possible.
            </p>
        </form>
    </section>

    @if($searched && ! $registration)
        <section class="mt-6 rounded-2xl border border-dashed border-red-200 bg-red-50 p-8 text-center">
            <h2 class="text-2xl font-semibold text-red-900">No registration found.</h2>
            <p class="mt-3 text-sm leading-7 text-red-700">Recheck the code and try again.</p>
        </section>
    @endif

    @if($registration)
        @if($registration->event?->code === '01')
            <section class="mt-6 rounded-2xl border border-primary/20 bg-primary p-5 text-white shadow-soft sm:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[.18em] text-white/62">IUPC Package</p>
                        <h2 class="mt-2 text-3xl font-semibold">{{ $iupcPackage ?: 'Package not recorded' }}</h2>
                        <p class="mt-2 text-sm text-white/70">With coach kit: BDT 5,099 / Without coach kit: BDT 4,099</p>
                    </div>
                    <div class="rounded-2xl bg-white px-5 py-4 text-primary">
                        <p class="text-xs font-semibold uppercase tracking-[.16em] text-primary/60">Registered Amount</p>
                        <p class="mt-1 text-3xl font-semibold">{{ $iupcAmount ? 'BDT '.number_format((int) $iupcAmount) : '-' }}</p>
                    </div>
                </div>
            </section>
        @endif

        <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                <p class="{{ $labelClass }}">Registration</p>
                <p class="mt-3 font-mono text-3xl font-semibold">{{ $registration->registration_code }}</p>
                <p class="mt-2 text-sm text-coal/52">ID #{{ $registration->id }}</p>
            </div>
            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                <p class="{{ $labelClass }}">Event</p>
                <p class="mt-3 text-2xl font-semibold">{{ $registration->event?->code ?? '--' }}</p>
                <p class="mt-2 text-sm text-coal/58">{{ $registration->event?->name ?? 'Unknown event' }}</p>
            </div>
            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                <p class="{{ $labelClass }}">Tshirts</p>
                <p class="mt-3 text-lg font-semibold leading-8">
                    @forelse($tshirtCounts as $size => $count)
                        <span class="inline-flex">{{ $size }}({{ $count }}){{ ! $loop->last ? ',' : '' }}</span>
                    @empty
                        <span class="text-coal/42">No size data</span>
                    @endforelse
                </p>
                <p class="mt-2 text-sm text-coal/58">Participants{{ ($registration->event?->code === '01' && $finalRegistration?->payment_package === 'with_coach_kit' && $registration->coach?->tshirt_size) ? ' + coach' : '' }}</p>
            </div>
            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft">
                <p class="{{ $labelClass }}">Participants</p>
                <p class="mt-3 text-2xl font-semibold">{{ $registration->participants->count() }}</p>
                <p class="mt-2 text-sm text-coal/58">Created {{ $formatDate($registration->created_at) }}</p>
            </div>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-[1.08fr_.92fr]">
            <div class="space-y-6">
                <article class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="{{ $labelClass }}">Team Information</p>
                            <h2 class="mt-3 text-2xl font-semibold">{{ $registration->team_name }}</h2>
                        </div>
                        <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">{{ $registration->event?->code === '01' ? 'IUPC' : ($registration->event?->name ?? 'Event') }}</span>
                    </div>

                    <dl class="mt-6 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl bg-paper px-4 py-3">
                            <dt class="{{ $labelClass }}">Institution</dt>
                            <dd class="{{ $valueClass }}">{{ $registration->institution ?: '-' }}</dd>
                        </div>
                        <div class="rounded-xl bg-paper px-4 py-3">
                            <dt class="{{ $labelClass }}">Campus Ambassador</dt>
                            <dd class="{{ $valueClass }}">{{ $registration->ca ?: '-' }}</dd>
                        </div>
                        <div class="rounded-xl bg-paper px-4 py-3">
                            <dt class="{{ $labelClass }}">Created At</dt>
                            <dd class="{{ $valueClass }}">{{ $formatDate($registration->created_at) }}</dd>
                        </div>
                        <div class="rounded-xl bg-paper px-4 py-3">
                            <dt class="{{ $labelClass }}">Updated At</dt>
                            <dd class="{{ $valueClass }}">{{ $formatDate($registration->updated_at) }}</dd>
                        </div>
                    </dl>
                </article>

                <article class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                    <p class="{{ $labelClass }}">Participants</p>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-black/10 text-left text-sm">
                            <thead>
                                <tr class="text-xs font-semibold uppercase tracking-[.12em] text-coal/42">
                                    <th class="whitespace-nowrap px-3 py-3">Person</th>
                                    <th class="whitespace-nowrap px-3 py-3">Contact</th>
                                    <th class="whitespace-nowrap px-3 py-3">Institution</th>
                                    <th class="whitespace-nowrap px-3 py-3">Student ID</th>
                                    <th class="whitespace-nowrap px-3 py-3">T-shirt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                @foreach($registration->participants as $participant)
                                    <tr>
                                        <td class="px-3 py-4 align-top">
                                            <p class="font-semibold">{{ $participant->full_name }}</p>
                                            <p class="mt-1 text-xs text-coal/45">{{ $participant->is_leader ? 'Leader' : 'Participant' }} / ID #{{ $participant->id }}</p>
                                        </td>
                                        <td class="px-3 py-4 align-top">
                                            <p class="break-words font-medium">{{ $participant->email ?: '-' }}</p>
                                            <p class="mt-1 text-coal/55">{{ $participant->phone ?: '-' }}</p>
                                        </td>
                                        <td class="px-3 py-4 align-top">{{ $participant->university ?: '-' }}</td>
                                        <td class="px-3 py-4 align-top">{{ $participant->student_id ?: '-' }}</td>
                                        <td class="px-3 py-4 align-top font-semibold uppercase">{{ $participant->tshirt_size ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>

                @if($registration->coach)
                    <article class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                        <p class="{{ $labelClass }}">Coach Information</p>
                        <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Name</dt>
                                <dd class="{{ $valueClass }}">{{ $registration->coach->name ?: '-' }}{{ $registration->coach->tshirt_size ? ' ('.strtoupper($registration->coach->tshirt_size).')' : '' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Designation</dt>
                                <dd class="{{ $valueClass }}">{{ $registration->coach->designation ?: '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Official Email</dt>
                                <dd class="{{ $valueClass }}">{{ $registration->coach->official_email ?: '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Contact Number</dt>
                                <dd class="{{ $valueClass }}">{{ $registration->coach->contact_number ?: '-' }}</dd>
                            </div>
                        </dl>
                    </article>
                @endif
            </div>

            <aside class="space-y-6">
                <article class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                    <p class="{{ $labelClass }}">Lead Contact</p>
                    <dl class="mt-5 grid gap-3">
                        <div class="rounded-xl bg-paper px-4 py-3">
                            <dt class="{{ $labelClass }}">Name</dt>
                            <dd class="{{ $valueClass }}">{{ $registration->contact_name ?: '-' }}</dd>
                        </div>
                        <div class="rounded-xl bg-paper px-4 py-3">
                            <dt class="{{ $labelClass }}">Email</dt>
                            <dd class="{{ $valueClass }}">{{ $registration->contact_email ?: '-' }}</dd>
                        </div>
                        <div class="rounded-xl bg-paper px-4 py-3">
                            <dt class="{{ $labelClass }}">Phone</dt>
                            <dd class="{{ $valueClass }}">{{ $registration->contact_phone ?: '-' }}</dd>
                        </div>
                    </dl>
                </article>

                <article class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                    <p class="{{ $labelClass }}">Payment</p>
                    @if($payment)
                        <dl class="mt-5 grid gap-3">
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Amount</dt>
                                <dd class="{{ $valueClass }}">{{ $payment->amount ? 'BDT '.number_format((int) $payment->amount) : '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Method</dt>
                                <dd class="{{ $valueClass }}">{{ $payment->method ? ucfirst($payment->method) : '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">TRX ID</dt>
                                <dd class="{{ $valueClass }}">{{ $payment->trx_id ?: '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Recipient</dt>
                                <dd class="{{ $valueClass }}">{{ $payment->recipient_number ?: '-' }}{{ $payment->recipient_name ? ' / '.$payment->recipient_name : '' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Status</dt>
                                <dd class="{{ $valueClass }}">{{ $payment->status ?: '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Submitted / Verified</dt>
                                <dd class="{{ $valueClass }}">{{ $formatDate($payment->submitted_at) }} / {{ $formatDate($payment->verified_at) }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Screenshot Path</dt>
                                <dd class="{{ $valueClass }}">{{ $payment->screenshot_path ?: '-' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="mt-5 rounded-xl border border-dashed border-black/10 bg-paper px-4 py-8 text-center text-sm text-coal/50">No payment record found.</p>
                    @endif
                </article>

                <article class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                    <p class="{{ $labelClass }}">Final Registration</p>
                    @if($finalRegistration)
                        <dl class="mt-5 grid gap-3">
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Status</dt>
                                <dd class="{{ $valueClass }}">{{ $finalRegistration->status ?: '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Payment Package</dt>
                                <dd class="{{ $valueClass }}">{{ $iupcPackage ?: ($finalRegistration->payment_package ?: '-') }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">Payment Amount</dt>
                                <dd class="{{ $valueClass }}">{{ $finalRegistration->payment_amount ? 'BDT '.number_format((int) $finalRegistration->payment_amount) : '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-paper px-4 py-3">
                                <dt class="{{ $labelClass }}">TRX ID</dt>
                                <dd class="{{ $valueClass }}">{{ $finalRegistration->trx_id ?: '-' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="mt-5 rounded-xl border border-dashed border-black/10 bg-paper px-4 py-8 text-center text-sm text-coal/50">No final-registration record found.</p>
                    @endif
                </article>
            </aside>
        </section>

        @if($activityLogs->isNotEmpty())
            <section class="mt-6 rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
                <p class="{{ $labelClass }}">IUPC Coach Portal Activity</p>
                <div class="mt-5 grid gap-4">
                    @foreach($activityLogs as $log)
                        <article class="rounded-xl border border-black/5 bg-paper p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold">{{ $log->action }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-coal/58">{{ $log->summary }}</p>
                                </div>
                                <span class="text-xs font-semibold text-coal/42">{{ $formatDate($log->created_at) }}</span>
                            </div>
                            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
                                <div>
                                    <dt class="{{ $labelClass }}">University</dt>
                                    <dd class="{{ $valueClass }}">{{ $log->allocation?->name ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="{{ $labelClass }}">Coach</dt>
                                    <dd class="{{ $valueClass }}">{{ $log->coachLink?->coach?->name ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="{{ $labelClass }}">IP</dt>
                                    <dd class="{{ $valueClass }}">{{ $log->ip_address ?: '-' }}</dd>
                                </div>
                            </dl>
                            @if($log->before || $log->after)
                                <details class="mt-4 rounded-lg border border-black/10 bg-white p-3 text-xs">
                                    <summary class="cursor-pointer font-semibold text-coal/70">Raw change payload</summary>
                                    <div class="mt-3 grid gap-3 lg:grid-cols-2">
                                        <pre class="overflow-x-auto rounded-lg bg-coal p-3 text-white/80">{{ json_encode($log->before, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        <pre class="overflow-x-auto rounded-lg bg-coal p-3 text-white/80">{{ json_encode($log->after, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                </details>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
@endsection
