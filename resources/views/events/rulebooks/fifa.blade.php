@extends('layouts.app')

@section('title', 'FIFA Rulebook | '.config('app.name'))
@section('meta_description', 'Read the FIFA EA FC 26 tournament rulebook for IUT 12th ICT FEST 2026.')
@section('canonical', route('events.rulebook', ['eventSlug' => 'fifa']))
@section('og_image', asset('assets/logos/fifa.png'))

@section('content')
@php
    $sections = [
        ['id' => 'registration', 'label' => 'Registration'],
        ['id' => 'schedule', 'label' => 'Schedule'],
        ['id' => 'guidelines', 'label' => 'Guidelines'],
        ['id' => 'game', 'label' => 'Game Settings'],
        ['id' => 'prize', 'label' => 'Prize Pool'],
        ['id' => 'format', 'label' => 'Format'],
        ['id' => 'match', 'label' => 'Match Rules'],
        ['id' => 'conduct', 'label' => 'Conduct'],
        ['id' => 'contact', 'label' => 'Contact'],
    ];
@endphp

@push('styles')
<style>
    .rulebook-content {
        overflow-wrap: anywhere;
    }

    .rulebook-content table {
        table-layout: fixed;
        width: 100%;
    }
</style>
@endpush

<section class="px-4 pb-12 pt-32 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <a href="{{ url('/fifa') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/54 transition hover:text-white">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to FIFA
        </a>

        <div class="mt-10 grid min-w-0 gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,.92fr)] lg:items-end">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[.24em] text-ember/80">Official Rulebook</p>
                <h1 class="mt-5 text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">EA FC 26 Tournament</h1>
                <p class="mt-6 max-w-3xl text-base leading-8 text-white/60">
                    Full rulebook for player eligibility, event timing, device requirements, EA FC 26 settings, match format, result handling, and conduct.
                </p>
            </div>

            <div class="min-w-0 rounded-lg border border-white/10 bg-white/[.035] p-5 shadow-glow">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Registration Fee</p>
                        <p class="mt-2 text-sm font-semibold text-white">200 BDT</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Player Cap</p>
                        <p class="mt-2 text-sm font-semibold text-white">64 players</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Tournament</p>
                        <p class="mt-2 text-sm font-semibold text-white">24-25 July, 2026</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Deadline</p>
                        <p class="mt-2 text-sm font-semibold text-white">July 03, 2026, 11:59 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="px-4 pb-28 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl min-w-0 gap-8 lg:grid-cols-[minmax(0,16rem)_minmax(0,1fr)]">
        <aside class="hidden lg:block">
            <div class="sticky top-28 rounded-lg border border-white/10 bg-white/[.035] p-4">
                <p class="px-2 text-[11px] font-semibold uppercase tracking-[.2em] text-white/36">Contents</p>
                <nav class="mt-3 grid gap-1 text-sm">
                    @foreach($sections as $section)
                        <a href="#{{ $section['id'] }}" class="rounded-md px-2 py-2 text-white/62 transition hover:bg-white/[.06] hover:text-white">{{ $section['label'] }}</a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <article class="rulebook-content min-w-0 space-y-7">
            <section id="registration" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Registration Guidelines</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-white/62">
                    <li>Registration fee is 200 BDT.</li>
                    <li>Any undergraduate university or college student can register.</li>
                    <li>There are no rank limits. Players of any rank can register.</li>
                    <li>All players must be registered through the provided form.</li>
                    <li>The tournament format features head-to-head matches between individual players.</li>
                    <li><strong class="text-white">Registration deadline:</strong> July 03, 2026, 11:59 PM.</li>
                </ul>
            </section>

            <section id="schedule" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Schedule and Venue</h2>
                <div class="mt-6 max-w-full overflow-x-auto rounded-lg border border-white/10">
                    <table class="w-full divide-y divide-white/10 text-left text-sm">
                        <tbody class="divide-y divide-white/10 text-white/64">
                            <tr><td class="px-5 py-4 font-semibold text-white">Reporting Time</td><td class="px-5 py-4">09:00 AM</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Tournament Starts</td><td class="px-5 py-4">10:00 AM</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Tournament Duration</td><td class="px-5 py-4">24-25 July, 2026</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Daily Time</td><td class="px-5 py-4">9 AM - 5 PM for both days</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Location</td><td class="px-5 py-4">IUT Campus</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="guidelines" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Mandatory Guidelines</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Reporting</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">Players must report 1 hour before the event starts. Failure to comply may result in disqualification.</p>
                    </div>
                    <div class="rounded-lg border border-iris/20 bg-iris/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Student ID</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">Every player must carry their university or college student ID card for verification.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-lg font-semibold text-white">Equipment</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">All players must bring their own headset and controller.</p>
                    </div>
                </div>
            </section>

            <section id="game" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Game and Settings</h2>
                <div class="mt-6 grid gap-5 lg:grid-cols-2">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-lg font-semibold text-white">Core Setup</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li><strong class="text-white">Game:</strong> EA FC 26</li>
                            <li><strong class="text-white">Platform:</strong> PC</li>
                            <li><strong class="text-white">Device:</strong> Only controllers are allowed.</li>
                            <li><strong class="text-white">Team Selection:</strong> Participants may play with any available team.</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-lg font-semibold text-white">Match Settings</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Game mode: Kick-off</li>
                            <li>Settings: Standard Settings</li>
                            <li>Camera Angle: Tele Broadcast height 20-0 zoom</li>
                            <li>Difficulty Level: Legendary</li>
                            <li>Half Length: 6 minutes</li>
                            <li>Game Speed: Normal</li>
                            <li>Injury: Off</li>
                            <li>Season: Fall / Autumn</li>
                            <li>Time of Day: 10:00 PM</li>
                            <li>Pitch Wear: None</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-6 rounded-lg border border-red-400/20 bg-red-400/10 p-5">
                    <h3 class="text-lg font-semibold text-white">Gameplay Restrictions</h3>
                    <ul class="mt-4 space-y-3 text-sm font-semibold leading-7 text-red-100">
                        <li>Time wasting is not allowed before 80 minutes.</li>
                        <li>Tactical and advanced defending are allowed.</li>
                        <li>3-back and 5-back formations are banned.</li>
                        <li>Custom formations are forbidden. Only standard formations are allowed.</li>
                        <li>If a match ends in a draw after regulation time, extra time and penalties will follow to determine the winner.</li>
                    </ul>
                </div>
            </section>

            <section id="prize" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Prize Pool</h2>
                <div class="mt-6 rounded-lg border border-ember/25 bg-ember/10 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[.18em] text-ember/80">Total Prize Pool</p>
                    <p class="mt-2 text-3xl font-semibold text-white">24,000 BDT</p>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-xs uppercase tracking-[.16em] text-white/38">Champion</p>
                        <p class="mt-2 text-2xl font-semibold text-white">15,000 BDT</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-xs uppercase tracking-[.16em] text-white/38">Runner-up</p>
                        <p class="mt-2 text-2xl font-semibold text-white">9,000 BDT</p>
                    </div>
                </div>
            </section>

            <section id="format" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Tournament Format</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">
                    The tournament consists of 64 players in a single bracket. Round of 64 and Round of 32 will be direct knockout. From Round of 16 onward, matches will be played as best-of-3. The final will be played as best-of-5.
                </p>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-white/36">Before R16</p>
                        <p class="mt-2 text-sm font-semibold text-white">Best of 1</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-white/36">From R16</p>
                        <p class="mt-2 text-sm font-semibold text-white">Best of 3</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-white/36">Final</p>
                        <p class="mt-2 text-sm font-semibold text-white">Best of 5</p>
                    </div>
                </div>
            </section>

            <section id="match" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Before and During the Match</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Check In</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">Players should be available on the spot 15 minutes before their match time. Organizers or observers decide whether to allow a player to participate.</p>
                    </div>
                    <div class="rounded-lg border border-iris/20 bg-iris/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Technical Timeout</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">Players may take a timeout only if a technical difficulty is detected.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-lg font-semibold text-white">Match Results</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">All matches will be coordinated by an organizer on duty. Match results will be recorded by the organizer.</p>
                    </div>
                </div>
            </section>

            <section id="conduct" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Conduct and Rule Changes</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-red-400/20 bg-red-400/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Toxicity</h3>
                        <p class="mt-3 text-sm leading-7 text-red-100">No toxicity is allowed. If noticed, the player may be disqualified. Offensive language may lead to a tournament ban. All participants must demonstrate sportsmanship and cooperative behavior.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-lg font-semibold text-white">Rule Changes</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">Tournament administration reserves the right to amend, remove, or change rules without notice. Administration may also make judgments on cases not detailed in the rulebook, including decisions that go against the written rulebook in extreme cases to preserve fair play and sportsmanship.</p>
                    </div>
                </div>
                <p class="mt-6 text-sm font-semibold text-white/68">Rulebook Version: 1.2</p>
            </section>

            <section id="contact" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Contact</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">If you face any issues with these rules, please contact:</p>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-lg font-semibold text-white">Md Abid Hasan</p>
                        <p class="mt-2 text-sm text-white/62">Event Management Executive (Esports), IUT Computer Society</p>
                        <div class="mt-4 grid gap-2 text-sm">
                            <a href="tel:01812005769" class="inline-flex items-center gap-2 font-semibold text-ember transition hover:text-white"><i class="fa-solid fa-phone text-xs"></i>01812005769</a>
                            <a href="mailto:mdabidhasan@iut-dhaka.edu" class="break-all font-semibold text-ember transition hover:text-white">mdabidhasan@iut-dhaka.edu</a>
                        </div>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-lg font-semibold text-white">Istiaq Prodhan</p>
                        <p class="mt-2 text-sm text-white/62">Event Management Executive (Valorant), IUT Computer Society</p>
                        <div class="mt-4 grid gap-2 text-sm">
                            <a href="tel:01708070250" class="inline-flex items-center gap-2 font-semibold text-ember transition hover:text-white"><i class="fa-solid fa-phone text-xs"></i>01708070250</a>
                            <a href="mailto:istiaqprodhan@iut-dhaka.edu" class="break-all font-semibold text-ember transition hover:text-white">istiaqprodhan@iut-dhaka.edu</a>
                        </div>
                    </div>
                </div>
            </section>
        </article>
    </div>
</section>
@endsection
