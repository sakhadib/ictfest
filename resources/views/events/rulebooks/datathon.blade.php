@extends('layouts.app')

@section('title', 'অলীকবচন Datathon Rulebook | '.config('app.name'))
@section('meta_description', 'Read the Datathon rulebook for অলীকবচন: Bengali LLM Hallucination Detection Challenge at IUT 12th ICT FEST 2026.')
@section('canonical', route('events.rulebook', ['eventSlug' => 'datathon']))
@section('og_image', asset('assets/logos/datathon.png'))

@section('content')
@php
    $sections = [
        ['id' => 'description', 'label' => 'Description'],
        ['id' => 'eligibility', 'label' => 'Eligibility'],
        ['id' => 'process', 'label' => 'Participation'],
        ['id' => 'timeline', 'label' => 'Timeline'],
        ['id' => 'rules', 'label' => 'Rules'],
        ['id' => 'submission', 'label' => 'Submission'],
        ['id' => 'links', 'label' => 'Links & Contacts'],
        ['id' => 'scoring', 'label' => 'Scoring'],
        ['id' => 'judging', 'label' => 'Judging'],
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
        <a href="{{ url('/datathon') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/54 transition hover:text-white">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Datathon
        </a>

        <div class="mt-10 grid min-w-0 gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,.92fr)] lg:items-end">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[.24em] text-iris/80">Official Rulebook</p>
                <h1 class="mt-5 text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">অলীকবচন</h1>
                <p class="mt-4 text-2xl font-semibold leading-snug text-iris">Bengali LLM Hallucination Detection Challenge</p>
                <p class="mt-6 max-w-3xl text-base leading-8 text-white/60">
                    BrainLab presents Datathon at the IUT 12th ICT Fest 2026, powered by Institute of Policy Dynamics. This year's theme challenges teams to detect hallucinated Bengali outputs from large language models and build reliable, explainable language technology systems.
                </p>
            </div>

            <div class="min-w-0 rounded-lg border border-white/10 bg-white/[.035] p-5 shadow-glow">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Theme</p>
                        <p class="mt-2 text-sm font-semibold text-white">অলীকবচন</p>
                        <p class="mt-1 text-xs leading-5 text-white/54">Bengali LLM Hallucination Detection</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Team Size</p>
                        <p class="mt-2 text-sm font-semibold text-white">1-4 members</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Registration Fee</p>
                        <p class="mt-2 text-sm font-semibold text-white">600 BDT per team</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/36">Competition Type</p>
                        <p class="mt-2 text-sm font-semibold text-white">Private Kaggle competition</p>
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
            <section id="description" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Competition Description</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">
                    This year's challenge is <strong class="text-white">অলীকবচন : Bengali LLM Hallucination Detection Challenge</strong>. Participants will work on detecting hallucinated outputs from Bengali Large Language Models (LLMs), encouraging innovative solutions for real-world language technology problems.
                </p>
            </section>

            <section id="eligibility" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Eligibility Criteria</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-white/62">
                    <li>Teams can consist of up to 4 members.</li>
                    <li>Participants must be currently enrolled undergraduate students from any recognized university in Bangladesh.</li>
                    <li>Each participant must submit a valid student ID card during registration to verify student status.</li>
                    <li>International teams are allowed, but at least one team member must be Bangladeshi.</li>
                    <li>Cross-university teams are allowed.</li>
                </ul>
            </section>

            <section id="process" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Participation Process</h2>
                <p class="mt-5 text-sm leading-8 text-white/62">Fill up the registration form while keeping the following points in mind:</p>
                <ol class="mt-4 list-decimal space-y-3 pl-5 text-sm leading-7 text-white/62">
                    <li>Team size: 1-4 members from any university.</li>
                    <li>The team must assign a team leader with whom all sorts of communication will be done via email.</li>
                    <li>This will be a private Kaggle competition.</li>
                    <li>Ensure that the email address provided has a valid Kaggle account associated with it for each team member.</li>
                    <li>The registration fee for each team is 600 tk.</li>
                    <li>The Kaggle link will be given after the registration when the competition launches.</li>
                </ol>
                <a href="https://iutictfest26.tech/datathon" class="mt-6 inline-flex items-center gap-2 rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-iris">
                    Registration Link
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>
            </section>

            <section id="timeline" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Timeline</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-iris/20 bg-iris/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Registration Timeline</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li><strong class="text-white">Phase I Registration:</strong> 19th June - 30 June</li>
                            <li><strong class="text-white">Phase II Registration:</strong> 1 July - 15 July</li>
                            <li>The competition started on 1 July, but participants can still join until 15 July.</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-volt/20 bg-volt/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Event Timeline</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li><strong class="text-white">Preliminary/Online Round:</strong> 1 July - 20 July</li>
                            <li><strong class="text-white">Paper and Notebook Submission Deadline:</strong> 21 July</li>
                            <li><strong class="text-white">Final Round Participants Announcement:</strong> 23 July</li>
                            <li><strong class="text-white">Final/Onsite Round:</strong> 25 July</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 max-w-full overflow-x-auto rounded-lg border border-white/10">
                    <table class="w-full divide-y divide-white/10 text-left text-sm">
                        <thead class="bg-white/[.06] text-xs uppercase tracking-[.16em] text-white/48">
                            <tr>
                                <th class="px-5 py-3 font-semibold text-center">Event</th>
                                <th class="px-5 py-3 font-semibold text-center">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 text-white/64">
                            <tr><td class="px-5 py-4 text-center">Registration Starts</td><td class="px-5 py-4 text-center">19th June, 2026</td></tr>
                            <tr><td class="px-5 py-4 text-center">Datathon starts</td><td class="px-5 py-4 text-center">1st July, 2026</td></tr>
                            <tr><td class="px-5 py-4 text-center">Phase I Registration Closes</td><td class="px-5 py-4 text-center">30 June, 2026</td></tr>
                            <tr><td class="px-5 py-4 text-center">Phase II Registration Closes</td><td class="px-5 py-4 text-center">15th July, 2026</td></tr>
                            <tr><td class="px-5 py-4 text-center">Online Round Closes</td><td class="px-5 py-4 text-center">20th July, 2026</td></tr>
                            <tr><td class="px-5 py-4 text-center">Paper and Notebook Submission</td><td class="px-5 py-4 text-center">21st July, 2026</td></tr>
                            <tr><td class="px-5 py-4 text-center">Final Round Participants Announcement</td><td class="px-5 py-4 text-center">23rd July, 2026</td></tr>
                            <tr><td class="px-5 py-4 text-center">Final Presentation and Prize Giving</td><td class="px-5 py-4 text-center">25th July, 2026</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="rules" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Rules and Guidelines</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-white/62">
                    <li>Only one account per participant in Kaggle will be allowed.</li>
                    <li>All teams must have unique participants and no participant can be a member of multiple teams.</li>
                    <li>It will be a 25 day long competition with multiple phases.</li>
                    <li>No private code sharing outside teams.</li>
                </ul>

                <div class="mt-7 grid gap-5">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-lg font-semibold text-white">Models, Training and Compute Rules</h3>
                        <div class="mt-4 grid gap-5 md:grid-cols-2">
                            <div>
                                <p class="font-semibold text-white">Pretrained Models</p>
                                <p class="mt-2 text-sm leading-7 text-white/62">Publicly available open-source pretrained models are allowed, provided they are appropriately licensed.</p>
                                <ul class="mt-3 space-y-2 text-sm leading-7 text-white/62">
                                    <li>Model name and version with repository link</li>
                                    <li>License information</li>
                                    <li>Claimed pretraining data as stated by the model authors</li>
                                </ul>
                            </div>
                            <div>
                                <p class="font-semibold text-white">Fine-Tuning</p>
                                <p class="mt-2 text-sm leading-7 text-white/62">Fine-tuning of pretrained models is permitted.</p>
                            </div>
                            <div>
                                <p class="font-semibold text-white">API Prohibition</p>
                                <p class="mt-2 text-sm leading-7 text-white/62">The use of any external API is strictly prohibited. This includes OpenAI, Claude, or any hosted services. All inference must be performed locally within Kaggle notebooks.</p>
                            </div>
                            <div>
                                <p class="font-semibold text-white">External Infrastructure</p>
                                <p class="mt-2 text-sm leading-7 text-white/62">External infrastructure may be used for training purposes only. Final submissions must be fully reproducible on Kaggle.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-ember/20 bg-ember/10 p-5">
                        <h3 class="text-lg font-semibold text-white">External Data Policy</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/66">
                            <li>The data must be publicly available, curated from public sources, or created during the competition runtime.</li>
                            <li>All external data must be clearly declared and made public with proper citations.</li>
                            <li>External data must not include or be derived from any competition test set.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="submission" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Submission Requirements</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-white/62">
                    <li>A training notebook, preferably Kaggle-runnable, if applicable.</li>
                    <li>An inference notebook that must be Kaggle-runnable and capable of generating the final submission.</li>
                    <li>A script to run the code. Submissions will not be considered if the script is not provided.</li>
                    <li>Model checkpoints or weights, preferably hosted on Hugging Face.</li>
                    <li>Clear documentation, either README within the notebook or markdown cells.</li>
                    <li>A video presentation of maximum 7 minutes covering methodology, submitted within the competition deadline.</li>
                    <li>A 4-page report following the IEEE paper format.</li>
                    <li>Teams are strongly encouraged to upload their paper to <a href="https://arxiv.org/" target="_blank" rel="noopener noreferrer" class="font-semibold text-iris hover:text-white">arXiv</a> or GitHub and submit the corresponding link.</li>
                </ul>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-base font-semibold text-white">Re-run Policy</h3>
                        <p class="mt-2 text-sm leading-7 text-white/62">All submissions will be rerun on both the public and hidden test sets after Phase 1 concludes. The inference notebook must execute within Kaggle runtime limits, CPU or GPU as permitted.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-base font-semibold text-white">Submission Limit</h3>
                        <p class="mt-2 text-sm leading-7 text-white/62">Will be set when the competition launches.</p>
                    </div>
                </div>

                <h3 class="mt-7 text-lg font-semibold text-white">Evaluation Basis</h3>
                <ol class="mt-4 list-decimal space-y-3 pl-5 text-sm leading-7 text-white/62">
                    <li>Model accuracy and efficacy of data interpretation.</li>
                    <li>Inference result.</li>
                    <li>Video presentation and comprehensive report.</li>
                    <li>Functional script of the code.</li>
                </ol>

                <div class="mt-6 space-y-3 rounded-lg border border-red-400/20 bg-red-400/10 p-5 text-sm leading-7 text-red-100">
                    <p>Participants must refrain from any form of plagiarism or unethical practices. All work must be original, and proper credit must be given to external sources if used.</p>
                    <p>Manually labelling inference data is strictly prohibited and will result in disqualification.</p>
                    <p><strong>NOTE:</strong> The authority has the prerogative to make conclusive decisions at any point during the entire competition duration.</p>
                    <p><strong>NOTE:</strong> Use of common sense is highly recommended. Deliberate mistakes like idea sharing between teams or trying to submit anything without joining teams will not be tolerated.</p>
                </div>
            </section>

            <section id="links" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Links and Contacts</h2>
                <div class="mt-6 max-w-full overflow-x-auto rounded-lg border border-white/10">
                    <table class="w-full divide-y divide-white/10 text-left text-sm">
                        <tbody class="divide-y divide-white/10 text-white/64">
                            <tr>
                                <td class="w-1/3 px-5 py-4 font-semibold text-white">Registration form</td>
                                <td class="px-5 py-4"><a href="http://iutictfest26.tech/datathon" class="break-all font-semibold text-iris hover:text-white">http://iutictfest26.tech/datathon</a></td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">Contact for queries</td>
                                <td class="px-5 py-4">Abdullah Al Jubaer, Event Head, Datathon — <a href="tel:01736587392" class="font-semibold text-iris hover:text-white">01736-587392</a></td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">Competition Link</td>
                                <td class="px-5 py-4">Will be updated later</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">Discord Link</td>
                                <td class="px-5 py-4"><a href="https://discord.gg/yXcrdM2f5" target="_blank" rel="noopener noreferrer" class="break-all font-semibold text-iris hover:text-white">https://discord.gg/yXcrdM2f5</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="scoring" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Prize Pool and Scoring System</h2>
                <div class="mt-5 rounded-lg border border-white/10 bg-black/15 p-5">
                    <p class="text-sm font-semibold text-white">Prize Pool</p>
                    <p class="mt-2 text-sm leading-7 text-white/62">Will be updated later.</p>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-lg border border-iris/20 bg-iris/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Online Round: 70%</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Public Leaderboard: 30% of online score</li>
                            <li>Private Leaderboard: 70% of online score</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-volt/20 bg-volt/10 p-5">
                        <h3 class="text-lg font-semibold text-white">Offline Round: 30%</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Presentation: 50% of offline score</li>
                            <li>Q&amp;A: 30% of offline score</li>
                            <li>Documentation / Report: 20% of offline score</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 rounded-lg border border-white/10 bg-black/15 p-5">
                    <h3 class="text-lg font-semibold text-white">Overall Scoring Scheme</h3>
                    <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                        <li>Total = 0.7 * online score + 0.3 * offline score</li>
                        <li>Online score = 0.3 * public leaderboard score + 0.7 * private leaderboard score</li>
                        <li>Offline score = 0.5 * presentation score + 0.3 * Q&amp;A + 0.2 * report score</li>
                    </ul>
                </div>
            </section>

            <section id="judging" class="rounded-lg border border-white/10 bg-white/[.035] p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Judging Criteria</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-base font-semibold text-white">Presentation</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Clear explanation of assumptions, models, and decisions</li>
                            <li>Informatics and visualization of the experiments</li>
                            <li>Novelty of the approach</li>
                            <li>Team's ability to articulate their contributions</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-base font-semibold text-white">Q&amp;A</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Problem understanding and motivation</li>
                            <li>Data and preprocessing</li>
                            <li>Modeling and methodology</li>
                            <li>Evaluation and metrics</li>
                            <li>Novelty and insights</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-5">
                        <h3 class="text-base font-semibold text-white">Report and Documentation</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-white/62">
                            <li>Methodology and explanation</li>
                            <li>Data visualization and informatics</li>
                            <li>References for the methodology</li>
                            <li>References for new datasets used, if applicable</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 rounded-lg border border-ember/20 bg-ember/10 p-5 text-sm leading-7 text-white/68">
                    For <strong class="text-white">Best Efficiency</strong> and <strong class="text-white">Best Paper</strong>, the evaluation criteria is different and will be disclosed when the competition starts.
                </div>

                <p class="mt-6 text-sm font-semibold leading-7 text-white">For any sort of questions and confusions, please ask in the Discord server.</p>
            </section>
        </article>
    </div>
</section>
@endsection
