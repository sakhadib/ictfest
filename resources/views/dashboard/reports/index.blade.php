@extends('layouts.dashboard')

@section('title', 'Report')
@section('page-title', 'Report')
@section('page-subtitle', 'Build registration reports and download clean CSV files.')

@section('content')
    @php
        $inputClass = 'mt-2 w-full rounded-lg border border-black/10 bg-paper px-3 py-2.5 text-sm outline-none transition focus:border-primary/40 focus:bg-white focus:ring-4 focus:ring-primary/10';
        $labelClass = 'text-sm font-semibold text-coal/70';
    @endphp

    <form method="GET" action="{{ route('dashboard.reports.index') }}" class="space-y-6">
        <section class="rounded-lg border border-black/5 bg-white p-5 shadow-soft">
            <div class="flex flex-col gap-3 border-b border-black/5 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Query Builder</h2>
                    <p class="mt-1 text-sm text-coal/55">Filters apply to the preview and the downloaded CSV.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard.reports.index') }}" class="rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-semibold text-coal transition hover:bg-black/[.03]">Reset</a>
                    <button type="submit" class="rounded-lg bg-coal px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-black">Preview</button>
                </div>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-4">
                <label>
                    <span class="{{ $labelClass }}">Event</span>
                    <select name="event_id" class="{{ $inputClass }}">
                        <option value="">All events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected((string) $filters['event_id'] === (string) $event->id)>{{ $event->code }} - {{ $event->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="{{ $labelClass }}">Registration status</span>
                    <select name="registration_status" class="{{ $inputClass }}">
                        <option value="">Any</option>
                        @foreach (['pending', 'verified', 'paid', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected($filters['registration_status'] === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="{{ $labelClass }}">Payment status</span>
                    <select name="payment_status" class="{{ $inputClass }}">
                        <option value="">Any</option>
                        @foreach (['unpaid', 'submitted', 'confirmed'] as $status)
                            <option value="{{ $status }}" @selected($filters['payment_status'] === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="{{ $labelClass }}">Has payment row</span>
                    <select name="has_payment" class="{{ $inputClass }}">
                        <option value="">Any</option>
                        <option value="yes" @selected($filters['has_payment'] === 'yes')>Yes</option>
                        <option value="no" @selected($filters['has_payment'] === 'no')>No</option>
                    </select>
                </label>

                <label>
                    <span class="{{ $labelClass }}">Payment method</span>
                    <select name="payment_method" class="{{ $inputClass }}">
                        <option value="">Any</option>
                        <option value="bkash" @selected($filters['payment_method'] === 'bkash')>Bkash</option>
                        <option value="nagad" @selected($filters['payment_method'] === 'nagad')>Nagad</option>
                    </select>
                </label>

                <label>
                    <span class="{{ $labelClass }}">Payment record status</span>
                    <select name="payment_record_status" class="{{ $inputClass }}">
                        <option value="">Any</option>
                        @foreach (['submitted', 'confirmed', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected($filters['payment_record_status'] === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="{{ $labelClass }}">Final registration status</span>
                    <select name="final_registration_status" class="{{ $inputClass }}">
                        <option value="">Any</option>
                        @foreach (['submitted', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected($filters['final_registration_status'] === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="{{ $labelClass }}">Institution contains</span>
                    <input name="institution" value="{{ $filters['institution'] }}" class="{{ $inputClass }}" placeholder="IUT">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Search</span>
                    <input name="search" value="{{ $filters['search'] }}" class="{{ $inputClass }}" placeholder="Code, team, lead, phone, trx">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Registered from</span>
                    <input name="registered_from" type="date" value="{{ $filters['registered_from'] }}" class="{{ $inputClass }}">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Registered to</span>
                    <input name="registered_to" type="date" value="{{ $filters['registered_to'] }}" class="{{ $inputClass }}">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Payment submitted from</span>
                    <input name="payment_submitted_from" type="date" value="{{ $filters['payment_submitted_from'] }}" class="{{ $inputClass }}">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Payment submitted to</span>
                    <input name="payment_submitted_to" type="date" value="{{ $filters['payment_submitted_to'] }}" class="{{ $inputClass }}">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Payment verified from</span>
                    <input name="payment_verified_from" type="date" value="{{ $filters['payment_verified_from'] }}" class="{{ $inputClass }}">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Payment verified to</span>
                    <input name="payment_verified_to" type="date" value="{{ $filters['payment_verified_to'] }}" class="{{ $inputClass }}">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Amount min</span>
                    <input name="amount_min" type="number" min="0" value="{{ $filters['amount_min'] }}" class="{{ $inputClass }}">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Amount max</span>
                    <input name="amount_max" type="number" min="0" value="{{ $filters['amount_max'] }}" class="{{ $inputClass }}">
                </label>

                <label>
                    <span class="{{ $labelClass }}">Sort</span>
                    <select name="sort" class="{{ $inputClass }}">
                        <option value="registered_at_desc" @selected($filters['sort'] === 'registered_at_desc')>Newest first</option>
                        <option value="registered_at_asc" @selected($filters['sort'] === 'registered_at_asc')>Oldest first</option>
                        <option value="team_name_asc" @selected($filters['sort'] === 'team_name_asc')>Team A-Z</option>
                        <option value="team_name_desc" @selected($filters['sort'] === 'team_name_desc')>Team Z-A</option>
                        <option value="event_asc" @selected($filters['sort'] === 'event_asc')>Event code</option>
                    </select>
                </label>
            </div>
        </section>

        <section class="rounded-lg border border-black/5 bg-white p-5 shadow-soft">
            <div class="flex flex-col gap-3 border-b border-black/5 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">CSV Columns</h2>
                    <p class="mt-1 text-sm text-coal/55">Participant fields are summarized into one cell per registration to avoid duplicate rows.</p>
                </div>
                <button type="submit" formaction="{{ route('dashboard.reports.download') }}" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                    Download Report
                </button>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($availableColumns as $key => $label)
                    <label class="flex items-start gap-3 rounded-lg border border-black/5 bg-paper/70 p-3 text-sm">
                        <input type="checkbox" name="columns[]" value="{{ $key }}" @checked(in_array($key, $columns, true)) class="mt-0.5 h-4 w-4 rounded border-black/20 text-primary">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </section>
    </form>

    <section class="mt-6 overflow-hidden rounded-lg border border-black/5 bg-white shadow-soft">
        <div class="border-b border-black/5 px-5 py-4">
            <h2 class="text-lg font-semibold">Preview</h2>
            <p class="mt-1 text-sm text-coal/55">Showing up to 50 matching registrations. CSV download includes all matches.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-black/5 text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wide text-coal/45">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Code</th>
                        <th class="px-5 py-3 font-semibold">Event</th>
                        <th class="px-5 py-3 font-semibold">Team</th>
                        <th class="px-5 py-3 font-semibold">Lead</th>
                        <th class="px-5 py-3 font-semibold">Payment</th>
                        <th class="px-5 py-3 font-semibold">Participants</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse ($registrations as $registration)
                        <tr>
                            <td class="px-5 py-4 font-mono text-xs font-semibold">{{ $registration->registration_code }}</td>
                            <td class="px-5 py-4 text-coal/65">{{ $registration->event?->code }} - {{ $registration->event?->name }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold">{{ $registration->team_name }}</p>
                                <p class="mt-1 text-xs text-coal/50">{{ $registration->institution }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold">{{ $registration->contact_name }}</p>
                                <p class="mt-1 text-xs text-coal/50">{{ $registration->contact_email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold capitalize">{{ $registration->payment_status }}</p>
                                <p class="mt-1 font-mono text-xs text-coal/50">{{ $registration->payment?->trx_id ?? '---' }}</p>
                            </td>
                            <td class="px-5 py-4 text-coal/65">{{ $registration->participants_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-coal/50">No registrations match the current report filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
