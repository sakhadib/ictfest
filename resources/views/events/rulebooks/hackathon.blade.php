@extends('layouts.app')

@section('title', 'Agentic AI Hackathon Rulebook | '.config('app.name'))
@section('meta_description', 'Read the Agentic AI Hackathon rulebook for IUT 12th ICT FEST 2026.')
@section('canonical', route('events.rulebook', ['eventSlug' => 'hackathon']))
@section('og_image', asset('assets/logos/hackathon.png'))

@section('content')
@php
    $sections = [
        ['id' => 'event', 'label' => 'The Event'],
        ['id' => 'structure', 'label' => 'Competition Structure'],
        ['id' => 'rules', 'label' => 'Rules & Guidelines'],
        ['id' => 'technical', 'label' => 'Technical Guidelines'],
        ['id' => 'facilities', 'label' => 'Facilities'],
        ['id' => 'submission', 'label' => 'Submission Requirements'],
        ['id' => 'conduct', 'label' => 'Code of Conduct'],
        ['id' => 'contact', 'label' => 'Contact Information'],
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
        <a href="{{ url('/hackathon') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/54 transition hover:text-white">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Agentic AI Hackathon
        </a>

        <div class="mt-10 grid min-w-0 gap-10 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,.9fr)] lg:items-end">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[.24em] text-ember/80">Official Rulebook</p>
                <h1 class="mt-5 text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">Agentic AI Hackathon</h1>
                <p class="mt-6 max-w-3xl text-base leading-8 text-white/60">
                    A complete guide for team eligibility, competition phases, technical boundaries, facilities, submissions, and conduct for the Agentic AI Hackathon at {{ config('app.name') }}.
                </p>
            </div>

            <div class="min-w-0 rounded-lg border border-white/10 bg-white/[.035] p-5 shadow-glow">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Event Dates</p>
                        <p class="mt-2 text-sm font-semibold text-white">10 AM, 24 July - 10 AM, 25 July, 2026</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Location</p>
                        <p class="mt-2 text-sm font-semibold text-white">IUT Premises, K B Bazar Rd, Gazipur 1704</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Final Fee</p>
                        <p class="mt-2 text-sm font-semibold text-white">2500 BDT per team</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Registration</p>
                        <a href="https://iutictfest26.tech/hackathon" class="mt-2 inline-flex text-sm font-semibold text-ember transition hover:text-white">iutictfest26.tech/hackathon</a>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Event Page</p>
                        <a href="https://www.facebook.com/events/1300321915602853/" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex text-sm font-semibold text-ember transition hover:text-white">Facebook Event</a>
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
            <section id="event" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">1. The Event</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">
                    The data never stops flowing, and neither do the innovators connecting the digital dots. As part of the grand return of the ultimate tech celebration of the year, the IUT Computer Society presents one of its most dynamic and creative events: the <strong class="text-white">Agentic AI Hackathon</strong>. Keep your endpoints ready, assemble your team, and get ready to build under pressure as raw data transforms into real-world solutions. Let the requests route and your innovations speak!
                </p>
            </section>

            <section id="structure" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">2. Competition Structure</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">
                    The campus will transform into an electrifying battleground where you will race against the clock to design powerful applications. The competition is divided into two distinct phases:
                </p>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <p class="text-sm font-semibold text-white">Phase 1: Preliminary Round</p>
                        <p class="mt-2 text-sm leading-7 text-white/62">A fast-paced online screening round lasting <strong class="text-white">4 hours</strong>.</p>
                    </div>
                    <div class="rounded-lg border border-volt/20 bg-volt/10 p-5">
                        <p class="text-sm font-semibold text-white">Phase 2: 24-hour Final Challenge</p>
                        <p class="mt-2 text-sm leading-7 text-white/62">An intense, on-site challenge lasting <strong class="text-white">24 hours</strong>, starting at 10:00 AM on 24 July and leading into final judgement from 10:00 AM on 25 July.</p>
                    </div>
                </div>
            </section>

            <section id="rules" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">3. Rules & Guidelines</h2>
                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-semibold text-white">3.1 Team Composition</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li><strong class="text-white">Team Size:</strong> Each team must consist of 1 to 3 members.</li>
                            <li><strong class="text-white">Eligibility:</strong> All participants must be currently pursuing an undergraduate degree.</li>
                            <li><strong class="text-white">Team Formation:</strong> Cross-university teams are allowed; students from different institutions may form a team together.</li>
                            <li><strong class="text-white">Participation Limit:</strong> No participant may be a member of more than one team.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">3.2 Project Rules</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Participants must create a new GitHub repository on-site immediately after the Opening Ceremony and submit it at the end of the competition.</li>
                            <li>The project must be developed entirely on the day of the hackathon.</li>
                            <li>Any project developed fully or partially before the event will not be accepted.</li>
                            <li>Teams may use publicly available open-source libraries, frameworks, APIs, and SDKs.</li>
                            <li>All open-source tools must be mentioned in the project README.</li>
                            <li>No pre-written source code may be brought into the venue.</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-6 rounded-lg border border-red-400/20 bg-red-400/10 p-5 text-sm font-semibold leading-7 text-red-100">
                    IMPORTANT: Any unfair attempts, misbehaviour, or breach of rules will result in immediate disqualification.
                </div>
            </section>

            <section id="technical" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">4. Technical Guidelines</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-volt/20 bg-volt/10 p-5">
                        <h3 class="text-lg font-semibold text-white">What is Allowed</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Any publicly available APIs, SDKs, or your own trained AI/ML models.</li>
                            <li>Pre-trained models accessed via API, such as OpenAI, Hugging Face, Azure Cognitive Services, Google Cloud AI, and Anthropic Claude API.</li>
                            <li>Any programming language, web framework, or mobile development toolkit.</li>
                            <li>Cloud services including AWS, Google Cloud, Azure, and similar platforms.</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-red-400/20 bg-red-400/10 p-5">
                        <h3 class="text-lg font-semibold text-white">What is NOT Allowed</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Pre-written source code of any kind brought into the venue.</li>
                            <li>Projects developed, even partially, before the hackathon began.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="facilities" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">5. Facilities</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-semibold text-white">What We Provide</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li><strong class="text-white">Internet:</strong> Wi-Fi connectivity will be provided throughout the venue for the duration of the hackathon.</li>
                            <li><strong class="text-white">Food:</strong> Meals and refreshments will be provided for all participants for the duration of the overnight stay.</li>
                            <li><strong class="text-white">Security:</strong> The venue will be secured throughout the event by the organizing team and campus security.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">What Participants Should Bring</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li><strong class="text-white">Laptop(s):</strong> Each team member should bring a personal laptop.</li>
                            <li><strong class="text-white">Chargers:</strong> Laptop chargers and any device-specific chargers.</li>
                            <li><strong class="text-white">Multiplugs / Power Strips:</strong> To manage multiple devices at a single workstation.</li>
                            <li><strong class="text-white">Mobile Data (Backup):</strong> Strongly recommended due to potential infrastructural constraints.</li>
                            <li><strong class="text-white">Other Devices:</strong> Any other necessary personal or development devices, such as external keyboards or mouse.</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-6 rounded-lg border border-ember/20 bg-ember/10 p-5 text-sm leading-7 text-white/68">
                    NOTE: The organizing committee will do its best to ensure stable internet connectivity. However, due to the high number of concurrent users and possible infrastructure limitations, mobile data is recommended as a backup.
                </div>
            </section>

            <section id="submission" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">6. Submission Requirements</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">
                    Every team must submit all required deliverables within the allotted time. Submissions after the deadline will not be considered under any circumstances.
                </p>
                <div class="mt-6 max-w-full overflow-x-auto rounded-lg border border-white/10">
                    <table class="w-full divide-y divide-white/10 text-left text-sm">
                        <thead class="bg-white/[.06] text-xs uppercase tracking-[.16em] text-white/48">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Deliverable</th>
                                <th class="px-5 py-3 font-semibold">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 text-white/64">
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">GitHub Repository</td>
                                <td class="px-5 py-4 leading-7">Public repo with all code written during the hackathon. Include a README.md with setup, run instructions, and tech stack.</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">Demonstration</td>
                                <td class="px-5 py-4 leading-7">Teams must demonstrate a working prototype of their developed application before the judges.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="conduct" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">7. Code of Conduct</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">The IUT Computer Society is committed to providing a safe, inclusive, and respectful environment.</p>
                <div class="mt-6 grid gap-4">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-base font-semibold text-white">Expected Behaviour</h3>
                        <p class="mt-2 text-sm leading-7 text-white/62">Treat all individuals with respect and professionalism at all times. Foster a collaborative spirit, share knowledge, and support fellow participants.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-base font-semibold text-white">Prohibited Conduct</h3>
                        <p class="mt-2 text-sm leading-7 text-white/62">Harassment, intimidation, bullying, or discrimination of any kind against any person is strictly forbidden. Any form of plagiarism, academic dishonesty, or misrepresentation of work will lead to disqualification.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-base font-semibold text-white">Enforcement</h3>
                        <p class="mt-2 text-sm leading-7 text-white/62">If a violation is suspected, the concerned team will be formally notified and given a reasonable opportunity to respond. Disqualified teams forfeit all prizes and recognition.</p>
                    </div>
                </div>
            </section>

            <section id="contact" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">8. Contact Information</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">For any queries, please contact:</p>
                <div class="mt-5 rounded-lg border border-white/10 bg-black/15 p-5">
                    <p class="text-lg font-semibold text-white">Tasnim Ashraf</p>
                    <p class="mt-2 text-sm text-white/62">Director of Technical Affairs, IUT Computer Society</p>
                    <a href="tel:01608873666" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-ember transition hover:text-white">
                        <i class="fa-solid fa-phone text-xs"></i>
                        01608873666
                    </a>
                </div>
            </section>
        </article>
    </div>
</section>
@endsection
