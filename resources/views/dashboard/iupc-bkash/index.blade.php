@extends('layouts.dashboard')

@section('title', 'IUPC Payment Numbers')
@section('page-title', 'IUPC Payment Numbers')
@section('page-subtitle', 'Manage the rotating send money recipient shown in the coach portal.')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[22rem_1fr]">
        <section class="h-fit rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Add recipient</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-950">New recipient number</h2>

            @if (session('status'))
                <div class="mt-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    Please review the submitted data.
                </div>
            @endif

            <form method="POST" action="{{ route('dashboard.iupc-bkash.store') }}" class="mt-5 grid gap-4">
                @csrf
                <label>
                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recipient Name</span>
                    <input name="recipient_name" value="{{ old('recipient_name') }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary/50">
                    @error('recipient_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </label>
                <label>
                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recipient Number</span>
                    <input name="bkash_number" value="{{ old('bkash_number') }}" placeholder="017XXXXXXXX" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary/50">
                    @error('bkash_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-2 text-xs leading-5 text-slate-500">Accepted formats: 017..., +88017..., 88017.... It will be stored as 01XXXXXXXXX.</p>
                </label>
                <button type="submit" class="rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">
                    Add Number
                </button>
            </form>

            <div class="mt-6 rounded-lg border border-primary/15 bg-primary/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-primary/70">Current Coach Portal Number</p>
                @if($currentRecipient)
                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $currentRecipient->recipient_name }}</p>
                    <p class="mt-1 text-3xl font-semibold tracking-wide text-primary">{{ $currentRecipient->bkash_number }}</p>
                @else
                    <p class="mt-2 text-sm leading-6 text-slate-600">No active payment recipient is available. The coach portal will show a payment unavailable notice.</p>
                @endif
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Rotation pool</p>
                        <h2 class="mt-1 text-lg font-semibold text-slate-950">Payment recipients</h2>
                    </div>
                    <p class="text-sm text-slate-500">Hourly rotation uses Bangladesh time. Manual deactivation auto-reactivates at 12:15 AM next day.</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recipients as $recipient)
                    <article class="p-5">
                        <div class="grid gap-4 xl:grid-cols-[1fr_20rem] xl:items-start">
                            <form id="recipient-{{ $recipient->id }}-update" method="POST" action="{{ route('dashboard.iupc-bkash.update', ['recipient' => $recipient]) }}" class="grid gap-4 md:grid-cols-[1fr_13rem_7rem]">
                                @csrf
                                @method('PATCH')
                                <label>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recipient Name</span>
                                    <input name="recipient_name" value="{{ old("recipients.$recipient->id.recipient_name", $recipient->recipient_name) }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold outline-none focus:border-primary/50">
                                </label>
                                <label>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recipient Number</span>
                                    <input name="bkash_number" value="{{ old("recipients.$recipient->id.bkash_number", $recipient->bkash_number) }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold tracking-wide outline-none focus:border-primary/50">
                                </label>
                                <label>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Order</span>
                                    <input name="rotation_order" type="number" min="0" max="9999" value="{{ old("recipients.$recipient->id.rotation_order", $recipient->rotation_order) }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold outline-none focus:border-primary/50">
                                </label>
                            </form>

                            <div class="flex flex-wrap gap-2 xl:justify-end">
                                @if($recipient->is_current)
                                    <span class="rounded-md bg-primary px-3 py-2 text-xs font-semibold text-white">Current</span>
                                @elseif($recipient->is_enabled)
                                    <form method="POST" action="{{ route('dashboard.iupc-bkash.current', ['recipient' => $recipient]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-md border border-primary/25 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/5">Make Current</button>
                                    </form>
                                @endif

                                <button form="recipient-{{ $recipient->id }}-update" type="submit" class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Save</button>

                                @if($recipient->is_enabled)
                                    <form method="POST" action="{{ route('dashboard.iupc-bkash.deactivate', ['recipient' => $recipient]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-md border border-amber-200 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50">Deactivate</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dashboard.iupc-bkash.activate', ['recipient' => $recipient]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-md border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">Activate</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('dashboard.iupc-bkash.destroy', ['recipient' => $recipient]) }}" onsubmit="return confirm('Remove this bKash recipient?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50">Remove</button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-medium {{ $recipient->is_enabled ? 'text-emerald-700' : 'text-slate-500' }}">{{ $recipient->is_enabled ? 'Active in pool' : 'Inactive' }}</span>
                            @if($recipient->reactivate_at)
                                <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 font-medium text-amber-700">
                                    Reactivates {{ $recipient->reactivate_at->format('d M, h:i A') }} BST
                                </span>
                            @endif
                            @if($recipient->last_selected_at)
                                <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-medium text-slate-500">
                                    Last selected {{ $recipient->last_selected_at->format('d M, h:i A') }} BST
                                </span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center text-slate-500">
                        No payment recipients added yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
