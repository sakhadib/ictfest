@extends('layouts.app')

@section('title', 'GameJam Rulebook | '.config('app.name'))
@section('meta_description', 'Read the GameJam rulebook for IUT 12th ICT FEST 2026.')
@section('canonical', route('events.rulebook', ['eventSlug' => 'gamejam']))
@section('og_image', asset('assets/logos/gamejam.png'))

@section('content')
@php
    $sections = [
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'format', 'label' => 'Format'],
        ['id' => 'rounds', 'label' => 'Rounds'],
        ['id' => 'timeline', 'label' => 'Timeline'],
        ['id' => 'registration', 'label' => 'Registration'],
        ['id' => 'rules', 'label' => 'Rules'],
        ['id' => 'submission', 'label' => 'Submission'],
        ['id' => 'judging', 'label' => 'Judging'],
        ['id' => 'evaluation', 'label' => 'Evaluation'],
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
        <a href="{{ url('/gamejam') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/54 transition hover:text-white">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to GameJam
        </a>

        <div class="mt-10 grid min-w-0 gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,.92fr)] lg:items-end">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[.24em] text-volt/80">Official Rulebook</p>
                <h1 class="mt-5 text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">GameJam</h1>
                <p class="mt-6 max-w-3xl text-base leading-8 text-white/60">
                    A creative game development challenge where teams build a polished game from scratch within a limited timeframe, submit a working build, and compete for a place in the onsite finale.
                </p>
            </div>

            <div class="min-w-0 rounded-lg border border-white/10 bg-white/[.035] p-5 shadow-glow">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Online Round</p>
                        <p class="mt-2 text-sm font-semibold text-white">July 14-20, 2026</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Onsite Round</p>
                        <p class="mt-2 text-sm font-semibold text-white">July 25, 2026</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Team Size</p>
                        <p class="mt-2 text-sm font-semibold text-white">1-3 members</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Finalist Fee</p>
                        <p class="mt-2 text-sm font-semibold text-white">700 BDT per selected team</p>
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
            <section id="overview" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Overview &amp; Purpose</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">
                    GameJam is designed to push creative boundaries and challenge participants to develop a polished game within a limited timeframe. This rulebook outlines everything needed to participate, from the theme reveal to submission guidelines and judging criteria.
                </p>
                <p class="mt-4 text-lg font-semibold text-white">Unleash your creativity.</p>
            </section>

            <section id="format" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">How This Works</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-volt/20 bg-volt/10 p-5">
                        <p class="text-sm font-semibold text-white">Online Round</p>
                        <p class="mt-2 text-sm leading-7 text-white/62">The full theme will be revealed at the start of the online round. Teams will have a designated timeframe to design, develop, and submit a complete game based on the theme.</p>
                    </div>
                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <p class="text-sm font-semibold text-white">Onsite Round</p>
                        <p class="mt-2 text-sm leading-7 text-white/62">Online submissions will be evaluated to select the Top 15 teams. Selected teams will present the exact submitted game in the onsite grand finale. No modifications are allowed after online submission.</p>
                    </div>
                </div>
            </section>

            <section id="rounds" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Rounds to Face</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Online</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">The full theme will be revealed at the official start of the jam. Participants will develop games from scratch and submit every required component before the deadline.</p>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>A fully working build of the game on itch.io, either Windows or Web.</li>
                            <li>A gameplay video explaining the game's mechanics, design, and connection to the theme.</li>
                            <li>A public GitHub repository link to verify development history and project integrity.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Onsite</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">The onsite round will take place live at Islamic University of Technology, Boardbazar, Gazipur.</p>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>This is a presentation-only round for the Top 15 selected teams.</li>
                            <li>Teams will pitch and demo the exact game submitted during the online round.</li>
                            <li>Preliminary scores used to select the Top 15 will not carry over to the onsite round, except the video pitch submission score.</li>
                        </ul>
                        <div class="mt-5 rounded-lg border border-ember/30 bg-ember/10 p-5 text-sm font-semibold leading-7 text-white">
                            For onsite round, you don't have to make any new game and you can't update your previously submitted game. Here, you will have to present the game you already submitted in online round to the judges.
                        </div>
                    </div>
                </div>
            </section>

            <section id="timeline" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Event Timeline</h2>
                <div class="mt-6 max-w-full overflow-x-auto rounded-lg border border-white/10">
                    <table class="w-full divide-y divide-white/10 text-left text-sm">
                        <thead class="bg-white/[.06] text-xs uppercase tracking-[.16em] text-white/48">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Event</th>
                                <th class="px-5 py-3 font-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 text-white/64">
                            <tr><td class="px-5 py-4 font-semibold text-white">Registration</td><td class="px-5 py-4">17th June to the end of the online round</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Online Round</td><td class="px-5 py-4">Approximately July 14 - July 20, 2026</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Video Submission Deadline</td><td class="px-5 py-4">21st July, 2026, 11:59 PM Bangladesh time</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Onsite Round</td><td class="px-5 py-4">July 25, 2026 at Islamic University of Technology</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 rounded-lg border border-red-400/20 bg-red-400/10 p-5 text-sm font-semibold leading-7 text-red-100">
                    Once the online submission deadline passes, project files and GitHub repositories must freeze. Any post-deadline modification before the onsite event will result in immediate disqualification.
                </div>
            </section>

            <section id="registration" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Registration and Team Formation</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-white/62">
                    <li>Each team will consist of 1 to 3 members.</li>
                    <li>Once formed, team members cannot be substituted for the remainder of the competition.</li>
                    <li>Cross-university teams are permitted.</li>
                    <li>Eligible participants include higher secondary students, undergraduate students, and graduate students of HSC-21 or equivalent.</li>
                    <li>Registration for the online stage is free.</li>
                    <li>The onsite round will require a registration fee of 700 BDT per team, only for selected teams.</li>
                </ul>
            </section>

            <section id="rules" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Rules and Guidelines</h2>
                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-semibold text-white">General Rules</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Teams must have a unique name.</li>
                            <li>Participants will open an empty git repository and start developing from there.</li>
                            <li>Any engine or framework may be used as long as the end user does not need to install additional software.</li>
                            <li>Participants must create a new game project and cannot edit previous game project files for this game jam.</li>
                            <li>Participants are allowed to reuse assets within the stated restrictions.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Asset Restrictions</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>No NSFW content in visuals, audio, text, or any other form.</li>
                            <li>No assets that violate itch.io terms of service.</li>
                            <li>No copyrighted assets.</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-volt/20 bg-volt/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Online Round Rules</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Participants must submit their game within the deadline with proper information.</li>
                            <li>Participants must submit a short video explaining the submitted game.</li>
                            <li>The GameJam video submission deadline is <strong class="text-white">21st July, 2026, 11:59 PM Bangladesh time</strong>.</li>
                            <li>Participants must maintain a git repository of their game.</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Onsite Round Rules</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Participants must bring their project and game executable files.</li>
                            <li>Participants must bring the student ID card used for registration.</li>
                            <li>Participants must bring their own devices, including laptop, mouse, keyboard, and other required equipment.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="submission" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Game and Video Submission</h2>
                <div class="mt-6 grid gap-5 lg:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Game Submission</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>The game will be submitted to the GameJam event on itch.io.</li>
                            <li>One team can submit only one game.</li>
                            <li>Participants must submit builds for Windows or Web.</li>
                            <li>Multiple builds for different platforms are allowed, but all must be submitted before the deadline.</li>
                            <li>Participants may make changes to their submission before the deadline.</li>
                            <li>Participants may update game page information on itch.io after submission.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Required Submission Information</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Team name.</li>
                            <li>Name of the team members.</li>
                            <li>Used engine or frameworks.</li>
                            <li>How to install, if needed.</li>
                            <li>How to play.</li>
                            <li>Git repository link.</li>
                            <li>Video link from YouTube.</li>
                            <li>Any other important information, such as asset credits or game breaking bugs.</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-6 rounded-lg border border-white/10 bg-black/15 p-5">
                    <h3 class="text-lg font-semibold text-white">Video Submission</h3>
                    <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                        <li>Participants must create a short gameplay video and explain the game.</li>
                        <li>The video must be uploaded to YouTube or Drive and submitted as a link.</li>
                        <li>The deadline is <strong class="text-white">21st July, 2026, 11:59 PM Bangladesh time</strong>.</li>
                        <li>The video may be used to showcase the game on social media.</li>
                        <li>The video name must follow this format: <strong class="text-white">TeamName_GameName</strong>.</li>
                    </ul>
                </div>
                <div class="mt-6 rounded-lg border border-red-400/20 bg-red-400/10 p-5 text-sm font-semibold leading-7 text-red-100">
                    NOTE: The authority has the prerogative to make conclusive decisions at any point during the entire competition duration.
                </div>
            </section>

            <section id="judging" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Judging Criteria</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">Games will be judged based on five criteria.</p>
                <div class="mt-6 max-w-full overflow-x-auto rounded-lg border border-white/10">
                    <table class="w-full divide-y divide-white/10 text-left text-sm">
                        <thead class="bg-white/[.06] text-xs uppercase tracking-[.16em] text-white/48">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Criteria</th>
                                <th class="px-5 py-3 font-semibold">Description</th>
                                <th class="px-5 py-3 font-semibold">Weight</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 text-white/64">
                            <tr><td class="px-5 py-4 font-semibold text-white">Theme</td><td class="px-5 py-4">How well the theme was adopted</td><td class="px-5 py-4">25%</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Gameplay</td><td class="px-5 py-4">How interesting the gameplay features are</td><td class="px-5 py-4">25%</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Design</td><td class="px-5 py-4">How fun and challenging the gameplay is</td><td class="px-5 py-4">25%</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Visual &amp; Audio</td><td class="px-5 py-4">How well the game looks and sounds</td><td class="px-5 py-4">15%</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Video Pitch</td><td class="px-5 py-4">How well the video explains the game</td><td class="px-5 py-4">10%</td></tr>
                            <tr><td class="px-5 py-4 font-semibold text-white">Total</td><td class="px-5 py-4">Complete score</td><td class="px-5 py-4">100%</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="evaluation" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Evaluation</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-volt/20 bg-volt/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Online Round Evaluation</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Submitted prototypes will be played and scored.</li>
                            <li>Evaluation will check whether the prototype is working properly.</li>
                            <li>Alignment with the theme will be judged.</li>
                            <li>Innovativeness of the idea will be considered.</li>
                            <li>All submissions will be ranked based on respective scores.</li>
                            <li>Late submissions will be disqualified.</li>
                            <li>Cross checking will be carried out against video submissions.</li>
                            <li>These scores will not carry forward to the onsite round, except scores on pitch submission.</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Onsite Round Evaluation</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li><strong class="text-white">Game Design:</strong> The core gameplay loop should be engaging, fun, and offer a satisfying challenge.</li>
                            <li><strong class="text-white">Game Mechanics:</strong> Mechanics should support the theme and overall gameplay experience.</li>
                            <li><strong class="text-white">Theme Adaptation:</strong> The full theme, including the revealed element, should be creatively and meaningfully incorporated.</li>
                            <li><strong class="text-white">Aesthetics:</strong> Visual style and soundscape should complement the game's theme and gameplay.</li>
                            <li><strong class="text-white">Judge Discretion:</strong> Judges may consider innovation, technical polish, and overall polish.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="contact" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Reach Us</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">For registration, competition links, Discord, and event queries, use the following official contact points.</p>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-lg font-semibold text-white">MD. Nazmus Sadiq</p>
                        <p class="mt-2 text-sm text-white/62">Contact for GameJam queries</p>
                        <div class="mt-4 grid gap-2 text-sm">
                            <a href="mailto:nazmussadiq@iut-dhaka.edu" class="break-all font-semibold text-volt transition hover:text-white">nazmussadiq@iut-dhaka.edu</a>
                            <a href="tel:01881117072" class="inline-flex items-center gap-2 font-semibold text-volt transition hover:text-white">
                                <i class="fa-solid fa-phone text-xs"></i>
                                01881117072
                            </a>
                        </div>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-lg font-semibold text-white">Khan Mahi Al Atahar</p>
                        <p class="mt-2 text-sm text-white/62">Contact for GameJam queries</p>
                        <div class="mt-4 grid gap-2 text-sm">
                            <a href="mailto:mahiatahar@iut-dhaka.edu" class="break-all font-semibold text-volt transition hover:text-white">mahiatahar@iut-dhaka.edu</a>
                            <a href="tel:01982628872" class="inline-flex items-center gap-2 font-semibold text-volt transition hover:text-white">
                                <i class="fa-solid fa-phone text-xs"></i>
                                01982628872
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-lg border border-volt/30 bg-volt/10 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[.18em] text-volt/80">Competition Link</p>
                    <a href="https://itch.io/jam/12th-iut-ict-fest-2026-gamejam" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex break-all text-lg font-semibold leading-7 text-white transition hover:text-volt">
                        https://itch.io/jam/12th-iut-ict-fest-2026-gamejam
                    </a>
                </div>

                <div class="mt-6 max-w-full overflow-x-auto rounded-lg border border-white/10">
                    <table class="w-full divide-y divide-white/10 text-left text-sm">
                        <tbody class="divide-y divide-white/10 text-white/64">
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">Registration form</td>
                                <td class="px-5 py-4"><a href="https://iutictfest26.tech/gamejam" class="break-all font-semibold text-volt transition hover:text-white">iutictfest26.tech/gamejam</a></td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">Competition Link</td>
                                <td class="px-5 py-4"><a href="https://itch.io/jam/12th-iut-ict-fest-2026-gamejam" target="_blank" rel="noopener noreferrer" class="break-all font-semibold text-volt transition hover:text-white">12th IUT ICT FEST 2026 GameJam - itch.io</a></td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">Discord Link</td>
                                <td class="px-5 py-4"><a href="https://discord.gg/kXEfDVcRx2" target="_blank" rel="noopener noreferrer" class="break-all font-semibold text-volt transition hover:text-white">https://discord.gg/kXEfDVcRx2</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </article>
    </div>
</section>
@endsection
