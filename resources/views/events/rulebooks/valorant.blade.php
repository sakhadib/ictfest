@extends('layouts.app')

@section('title', 'Valorant Rulebook | '.config('app.name'))
@section('meta_description', 'Read the Valorant tournament rulebook for IUT 12th ICT FEST 2026.')
@section('canonical', route('events.rulebook', ['eventSlug' => 'valorant']))
@section('og_image', asset('assets/logos/valorant.png'))

@section('content')
@php
    $sections = [
        ['id' => 'registration', 'label' => 'Registration'],
        ['id' => 'guidelines', 'label' => 'Guidelines'],
        ['id' => 'lobby', 'label' => 'Lobby Protocol'],
        ['id' => 'format', 'label' => 'Format'],
        ['id' => 'prize', 'label' => 'Prize Pool'],
        ['id' => 'bracket', 'label' => 'Bracket Flow'],
        ['id' => 'match', 'label' => 'Match Rules'],
        ['id' => 'conduct', 'label' => 'Conduct'],
        ['id' => 'contact', 'label' => 'Contact'],
    ];

    $bracketRounds = [
        ['round' => 'Upper Bracket Round 01', 'rule' => '32 teams are paired randomly for best-of-1 knockout matches, leaving 16 teams in the upper bracket.'],
        ['round' => 'Lower Bracket Round 01', 'rule' => 'The 16 losing teams from Upper Bracket Round 01 play in the lower bracket. Loser of match 1 faces loser of match 2, loser of match 3 faces loser of match 4, and so on.'],
        ['round' => 'Upper Bracket Round 02', 'rule' => 'The 16 winners are paired by previous match order. Winner of match 1 faces winner of match 2, winner of match 3 faces winner of match 4, and so on. This is best-of-1 and leaves 8 teams in the upper bracket.'],
        ['round' => 'Lower Bracket Round 02', 'rule' => 'The 8 losing teams from Upper Bracket Round 02 move to the lower bracket. Lower bracket winners are paired against upper bracket losers in reverse match order.'],
        ['round' => 'Upper Bracket Quarter Finals', 'rule' => 'The 8 upper bracket winners are paired by previous match order for best-of-1 quarter finals.'],
        ['round' => 'Lower Bracket Round 03', 'rule' => 'The 8 lower bracket winners are paired by previous match order for best-of-1 matches.'],
        ['round' => 'Upper Bracket Semi-Finals', 'rule' => 'QF1 winner faces QF2 winner, and QF3 winner faces QF4 winner. Winners go to the upper bracket finals, losers go to lower bracket semi-finals. This round is best-of-3.'],
        ['round' => 'Lower Bracket Round 04', 'rule' => 'Lower bracket winners are paired with the losers of the upper bracket quarter finals.'],
        ['round' => 'Lower Bracket Quarter Finals', 'rule' => 'Winners from Lower Bracket Round 04 proceed to the lower bracket quarter finals.'],
        ['round' => 'Lower Bracket Semi-Finals', 'rule' => 'Lower bracket quarter final winners face the upper bracket semi-final losers. Winners proceed to the lower bracket finals. This round is best-of-3.'],
        ['round' => 'Upper Bracket Finals', 'rule' => 'The two upper bracket semi-final winners play a best-of-3. Winner advances to the LAN grand finale; loser moves to the lower bracket finals.'],
        ['round' => 'Lower Bracket Final Qualifier', 'rule' => 'Lower bracket semi-final winners face each other. Winner proceeds to the lower bracket finals against the upper bracket finals loser.'],
        ['round' => 'Lower Bracket Finals', 'rule' => 'Winner proceeds to the grand finale. Loser is eliminated. This match is best-of-3.'],
        ['round' => 'Grand Finale', 'rule' => 'Upper bracket finals winner faces lower bracket finals winner in a best-of-3 LAN match. Winner is crowned champion.'],
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
        <a href="{{ url('/valorant') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/54 transition hover:text-white">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Valorant
        </a>

        <div class="mt-10 grid min-w-0 gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,.92fr)] lg:items-end">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[.24em] text-iris/80">Official Rulebook</p>
                <h1 class="mt-5 text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">Valorant Tournament</h1>
                <p class="mt-6 max-w-3xl text-base leading-8 text-white/60">
                    Full tournament rules for team formation, lobby protocol, match setup, double elimination bracket flow, result reporting, and player conduct.
                </p>
            </div>

            <div class="min-w-0 rounded-lg border border-white/10 bg-white/[.035] p-5 shadow-glow">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Team Cap</p>
                        <p class="mt-2 text-sm font-semibold text-white">32 teams</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Registration Fee</p>
                        <p class="mt-2 text-sm font-semibold text-white">600 BDT per team</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Team Size</p>
                        <p class="mt-2 text-sm font-semibold text-white">5-7 players</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Deadline</p>
                        <p class="mt-2 text-sm font-semibold text-white">June 30, 2026, 11:59 PM</p>
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
                <h2 class="text-2xl font-semibold text-white">Registration and Team Formation</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-white/62">
                    <li>Registration fee for each team is 600 BDT.</li>
                    <li>Any college (Higher Secondary), undergraduate, or postgraduate student can register and form a team.</li>
                    <li>Cross-institute teams are allowed.</li>
                    <li>Each team must consist of at least 5 players and no more than 7 players including substitutes.</li>
                    <li>Each team can have a coach, but it is not mandatory.</li>
                    <li>A player can play for only one team.</li>
                    <li>There are no rank limits. Players of any rank can register.</li>
                    <li>The grand finale will be in LAN and teams must be present with the full lineup to participate.</li>
                    <li>All players must be registered through the provided form.</li>
                    <li><strong class="text-white">Registration deadline:</strong> June 30, 2026, 11:59 PM. Registration will close earlier if 32 teams are registered.</li>
                </ul>
            </section>

            <section id="guidelines" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Mandatory Guidelines</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-iris/20 bg-iris/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Reporting and Eligibility</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Teams must report 15 minutes before their match. Failure to comply may result in disqualification.</li>
                            <li>For LAN events, every team member must carry their student ID card for verification.</li>
                            <li>Any issue between teams will be resolved by calling the team leaders.</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Equipment and Fair Play</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>All LAN teams must bring their own keyboard, mouse, headset, and mousepad.</li>
                            <li>Hacking or exploiting glitches will result in a permanent ban from future IUT Valorant events.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="lobby" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Lobby Protocol, Match Setup, and Result Reporting</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-white/62">
                    <li>Every player must join the Discord server and be present at the time of their match.</li>
                    <li>Everyone must wait in the respective Discord server game lobby.</li>
                    <li>According to schedule, an observer will conduct the toss for match selection and sides.</li>
                    <li>After the toss, all members should wait inside the assigned voice channels.</li>
                    <li>The winning team must post a screenshot in the match result text channel before leaving the voice channel. Failure to do so may result in disqualification.</li>
                    <li>Players may only play with the Valorant ID provided by the leader during registration.</li>
                </ul>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-white/36">Map Pool</p>
                        <p class="mt-2 text-sm font-semibold text-white">All current competitive pool maps</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-white/36">Agents</p>
                        <p class="mt-2 text-sm font-semibold text-white">All agents are allowed</p>
                    </div>
                </div>
            </section>

            <section id="format" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Tournament Format</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">
                    The tournament consists of 32 teams and will be played in a double elimination format. There will be an upper bracket and a lower bracket. Teams that lose in the upper bracket are sent to the lower bracket for another chance. Teams that lose in the lower bracket are eliminated from the tournament.
                </p>
            </section>

            <section id="prize" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Prize Pool</h2>
                <div class="mt-6 rounded-lg border border-iris/25 bg-iris/10 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[.18em] text-iris/80">Total Prize Pool</p>
                    <p class="mt-2 text-3xl font-semibold text-white">50,000 BDT</p>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-xs uppercase tracking-[.16em] text-white/38">Champions</p>
                        <p class="mt-2 text-2xl font-semibold text-white">30,000 BDT</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-xs uppercase tracking-[.16em] text-white/38">Runner-up</p>
                        <p class="mt-2 text-2xl font-semibold text-white">15,000 BDT</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-xs uppercase tracking-[.16em] text-white/38">MVP</p>
                        <p class="mt-2 text-2xl font-semibold text-white">5,000 BDT</p>
                    </div>
                </div>
            </section>

            <section id="bracket" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Bracket Flow</h2>
                <div class="mt-6 grid gap-4">
                    @foreach($bracketRounds as $round)
                        <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                            <h3 class="text-base font-semibold text-white">{{ $round['round'] }}</h3>
                            <p class="mt-2 text-sm leading-7 text-white/62">{{ $round['rule'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section id="match" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Before and During the Match</h2>
                <div class="mt-6 grid gap-5 lg:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Check In</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">All team members must join their scheduled lobby on time. From each team, only one member, preferably the leader, must join the observer channel for map selection 15 minutes before the scheduled time. Toss, map, and side selection must be completed within this time. Organizers or observers decide whether to allow a team to participate.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Server Selection</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">Matches will be played in Singapore. If there is an issue, server changes can be made if both team leaders agree.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Server Setup</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Mode: Standard</li>
                            <li>Allow Cheats: Off</li>
                            <li>Tournament Mode: On</li>
                            <li>Overtime: On</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Map Selection</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">A toss will be done by the observer. The winning team bans the first map. Map bans will be done through <a href="https://www.mapban.gg/en/ban/valorant/all" target="_blank" rel="noopener noreferrer" class="break-all font-semibold text-iris hover:text-white">mapban.gg</a>. Teams will receive unique URLs and are encouraged to get familiar with the banning and side selection process.</p>
                    </div>
                </div>

                <div class="mt-7 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-iris/20 bg-iris/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Number of Players</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">A total of 5 registered team players must be present in the voice channel. Benched team members cannot be in the same channel as playing members. If teams use in-game voice, they must use team voice instead of party voice.</p>
                    </div>
                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Timeouts</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li><strong class="text-white">Tactical timeout:</strong> 2 pauses per game, 1 for each half, maximum 2 minutes each. Ask the observer first.</li>
                            <li><strong class="text-white">Technical timeout:</strong> 1 pause per game for technical issues, maximum 5-8 minutes. Ask the observer first.</li>
                            <li>Any pause made without prior notice may result in disqualification.</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 rounded-lg border border-white/10 bg-black/15 p-5">
                    <h3 class="text-lg font-semibold text-white">Screenshot and MOSS Submission</h3>
                    <p class="mt-3 text-sm leading-7 text-white/62">The winning captain must post a screenshot of the end-game scoreboard and the MOSS zip file of each player in the designated Discord channel. A match result is not official until the screenshot is posted by the winning captain. Failure to provide MOSS zip files may result in disqualification.</p>
                </div>
            </section>

            <section id="conduct" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Conduct and Rule Changes</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-red-400/20 bg-red-400/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Toxicity</h3>
                        <p class="mt-3 text-sm leading-7 text-red-100">No toxicity is allowed in chat or in game. If noticed, the team may be disqualified. Offensive language may lead to a ban from the tournament. Participants must show sportsmanship and cooperative behavior.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-lg font-semibold text-white">Rule Changes</h3>
                        <p class="mt-3 text-sm leading-7 text-white/62">Tournament administration reserves the right to amend, remove, or change rules without further notice. Administration may also make judgments on cases not detailed in the rulebook, including decisions that go against the written rulebook in extreme cases to preserve fair play and sportsmanship.</p>
                    </div>
                </div>
            </section>

            <section id="contact" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Contact</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">If you face any issues with these rules, please contact:</p>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-lg font-semibold text-white">Md Abid Hasan</p>
                        <p class="mt-2 text-sm text-white/62">Event Management Executive (Esports), IUT Computer Society</p>
                        <div class="mt-4 grid gap-2 text-sm">
                            <a href="tel:01812005769" class="inline-flex items-center gap-2 font-semibold text-iris transition hover:text-white"><i class="fa-solid fa-phone text-xs"></i>01812005769</a>
                            <a href="mailto:mdabidhasan@iut-dhaka.edu" class="break-all font-semibold text-iris transition hover:text-white">mdabidhasan@iut-dhaka.edu</a>
                        </div>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <p class="text-lg font-semibold text-white">Istiaq Prodhan</p>
                        <p class="mt-2 text-sm text-white/62">Event Management Executive (Valorant), IUT Computer Society</p>
                        <div class="mt-4 grid gap-2 text-sm">
                            <a href="tel:01708070250" class="inline-flex items-center gap-2 font-semibold text-iris transition hover:text-white"><i class="fa-solid fa-phone text-xs"></i>01708070250</a>
                            <a href="mailto:istiaqprodhan@iut-dhaka.edu" class="break-all font-semibold text-iris transition hover:text-white">istiaqprodhan@iut-dhaka.edu</a>
                        </div>
                    </div>
                </div>
            </section>
        </article>
    </div>
</section>
@endsection
