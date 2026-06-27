@extends('layouts.dashboard')

@section('title', 'Compose Email')
@section('page-title', 'Compose Email')
@section('page-subtitle', 'Write the notification message before selecting recipients.')

@section('content')
    @include('dashboard.emails.partials.nav')

    @if(session('status'))
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.emails.compose.store') }}" class="mx-auto max-w-4xl">
        @csrf
        <section class="rounded-2xl border border-black/5 bg-white p-5 shadow-soft sm:p-6">
            <div class="border-b border-black/5 pb-5">
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Step 1</p>
                <h2 class="mt-3 text-2xl font-semibold text-coal">Write the email</h2>
                <p class="mt-2 text-sm leading-6 text-coal/60">
                    The final email will automatically include the greeting and IUTCS signature. Tabs and line breaks in the body are preserved.
                </p>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <label for="subject" class="text-sm font-semibold text-coal">Subject</label>
                    <input
                        id="subject"
                        type="text"
                        name="subject"
                        value="{{ old('subject', $draft['subject'] ?? '') }}"
                        required
                        class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10"
                    >
                </div>

                <div>
                    <label for="body" class="text-sm font-semibold text-coal">Body</label>
                    <textarea
                        id="body"
                        name="body"
                        rows="16"
                        required
                        class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 font-mono text-sm leading-6 outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10"
                        placeholder="Write the message body here."
                    >{{ old('body', $draft['body'] ?? '') }}</textarea>
                </div>

                <div class="rounded-xl border border-black/10 bg-paper/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-coal/45">Automatic wrapper</p>
                    <pre class="mt-3 whitespace-pre-wrap break-words text-sm leading-6 text-coal/70">Hello &lt;name&gt;,

[your body]

Best Regards,

IUT 12th ICT FEST
IUT Computer Society.</pre>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                    Continue to Recipients
                </button>
            </div>
        </section>
    </form>
@endsection
