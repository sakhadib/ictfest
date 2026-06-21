<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0.75in;
        }

        body {
            color: #18212f;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8px;
            line-height: 1.35;
        }

        h1,
        h2,
        h3 {
            margin: 0;
        }

        h1 {
            color: #d4574e;
            font-size: 20px;
        }

        h2 {
            border-bottom: 2px solid #d4574e;
            color: #111827;
            font-size: 13px;
            margin: 20px 0 8px;
            padding-bottom: 4px;
        }

        h3 {
            color: #374151;
            font-size: 10px;
            margin: 12px 0 6px;
        }

        p {
            margin: 0;
        }

        .muted {
            color: #667085;
        }

        .note {
            border-left: 3px solid #d4574e;
            margin-top: 10px;
            padding: 6px 0 6px 8px;
        }

        .grid {
            display: table;
            margin-top: 14px;
            table-layout: fixed;
            width: 100%;
        }

        .grid-row {
            display: table-row;
        }

        .metric {
            border: 1px solid #d8dee9;
            display: table-cell;
            padding: 7px;
            width: 16.66%;
        }

        .metric-label {
            color: #667085;
            font-size: 7px;
            text-transform: uppercase;
        }

        .metric-value {
            color: #111827;
            font-size: 14px;
            font-weight: 700;
            margin-top: 3px;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #d8dee9;
            padding: 4px 3px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #d4574e;
            color: #ffffff;
            font-size: 7px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .num {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        .registration-block {
            border: 1px solid #cfd6e3;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .registration-header {
            background: #fff3f2;
            border-bottom: 1px solid #cfd6e3;
            padding: 7px;
        }

        .registration-title {
            color: #111827;
            font-size: 11px;
            font-weight: 700;
        }

        .registration-meta {
            color: #667085;
            font-size: 8px;
            margin-top: 3px;
        }

        .two-col {
            display: table;
            table-layout: fixed;
            width: 100%;
        }

        .two-col > div {
            display: table-cell;
            padding: 7px;
            width: 50%;
        }
    </style>
</head>
<body>
    @php
        $events = $report['events'];
        $registrations = $report['registrations'];
    @endphp

    <h1>{{ $report['title'] }}</h1>
    <p class="muted">Generated at {{ $report['generated_at'] }}</p>
    <p class="note">
        This report contains participant count and registration, payment, coach, and final-stage data available in the system.
        Payment screenshot files are intentionally omitted.
    </p>

    <div class="grid">
        <div class="grid-row">
            <div class="metric">
                <p class="metric-label">Events</p>
                <p class="metric-value">{{ number_format($report['totals']['events']) }}</p>
            </div>
            <div class="metric">
                <p class="metric-label">Registrations</p>
                <p class="metric-value">{{ number_format($report['totals']['registrations']) }}</p>
            </div>
            <div class="metric">
                <p class="metric-label">Participants</p>
                <p class="metric-value">{{ number_format($report['totals']['participants']) }}</p>
            </div>
            <div class="metric">
                <p class="metric-label">Payments</p>
                <p class="metric-value">{{ number_format($report['totals']['payments']) }}</p>
            </div>
            <div class="metric">
                <p class="metric-label">Final Reg.</p>
                <p class="metric-value">{{ number_format($report['totals']['final_registrations']) }}</p>
            </div>
            <div class="metric">
                <p class="metric-label">University DB</p>
                <p class="metric-value">{{ number_format($report['totals']['university_directory_entries']) }}</p>
            </div>
        </div>
    </div>

    <h2>Event Overview</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 7%;">Code</th>
                <th style="width: 24%;">Event</th>
                <th>Team Size</th>
                <th>Amount</th>
                <th>Live</th>
                <th>Regs</th>
                <th>Participants</th>
                <th>Payments</th>
                <th>Final</th>
                <th style="width: 16%;">Rulebook</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr>
                    <td>{{ $event->code }}</td>
                    <td>{{ $event->name }}</td>
                    <td class="center">{{ $event->min_team_size }}-{{ $event->max_team_size }}</td>
                    <td class="num">{{ number_format((int) $event->amount) }}</td>
                    <td class="center">{{ $event->is_live ? 'Yes' : 'No' }}</td>
                    <td class="num">{{ number_format((int) $event->registrations_count) }}</td>
                    <td class="num">{{ number_format((int) $event->participants_count) }}</td>
                    <td class="num">{{ number_format((int) $event->payments_count) }}</td>
                    <td class="num">{{ number_format((int) $event->final_registrations_count) }}</td>
                    <td>{{ $event->rulebook_link ?: '---' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Status Summaries</h2>
    <table>
        <thead>
            <tr>
                <th>Registration Status</th>
                <th>Count</th>
                <th>Payment Status</th>
                <th>Count</th>
                <th>Payment Record</th>
                <th>Count</th>
                <th>Final Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @php
                $maxStatusRows = max(
                    $report['registration_statuses']->count(),
                    $report['payment_statuses']->count(),
                    $report['payment_record_statuses']->count(),
                    $report['final_statuses']->count(),
                );
            @endphp
            @for ($index = 0; $index < $maxStatusRows; $index++)
                <tr>
                    <td>{{ $report['registration_statuses'][$index]->status ?? '' }}</td>
                    <td class="num">{{ isset($report['registration_statuses'][$index]) ? number_format((int) $report['registration_statuses'][$index]->total) : '' }}</td>
                    <td>{{ $report['payment_statuses'][$index]->payment_status ?? '' }}</td>
                    <td class="num">{{ isset($report['payment_statuses'][$index]) ? number_format((int) $report['payment_statuses'][$index]->total) : '' }}</td>
                    <td>{{ $report['payment_record_statuses'][$index]->status ?? '' }}</td>
                    <td class="num">{{ isset($report['payment_record_statuses'][$index]) ? number_format((int) $report['payment_record_statuses'][$index]->total) : '' }}</td>
                    <td>{{ $report['final_statuses'][$index]->status ?? '' }}</td>
                    <td class="num">{{ isset($report['final_statuses'][$index]) ? number_format((int) $report['final_statuses'][$index]->total) : '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <h2>Campus Ambassador Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Campus Ambassador</th>
                <th style="width: 18%;">Registrations</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['ca_rows'] as $row)
                <tr>
                    <td>{{ $row->ca_name }}</td>
                    <td class="num">{{ number_format((int) $row->registrations_count) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No CA data found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>University-wise Participant Count</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 34%;">University</th>
                <th>IUPC</th>
                <th>Hackathon</th>
                <th>Datathon</th>
                <th>Gamejam</th>
                <th>FIFA</th>
                <th>Valorant</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['university_rows'] as $row)
                <tr>
                    <td>{{ $row['university'] }}</td>
                    <td class="num">{{ number_format($row['iupc']) }}</td>
                    <td class="num">{{ number_format($row['hackathon']) }}</td>
                    <td class="num">{{ number_format($row['datathon']) }}</td>
                    <td class="num">{{ number_format($row['gamejam']) }}</td>
                    <td class="num">{{ number_format($row['fifa']) }}</td>
                    <td class="num">{{ number_format($row['valorant']) }}</td>
                    <td class="num">{{ number_format($row['total']) }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No university participant data found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Daily Registration Trend</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                @foreach ($events as $event)
                    <th>{{ $event->code }}</th>
                @endforeach
                <th>Daily Total</th>
                <th>Cumulative Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['daily_rows'] as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    @foreach ($events as $event)
                        <td class="num">{{ number_format($row['counts'][$event->code] ?? 0) }}</td>
                    @endforeach
                    <td class="num">{{ number_format($row['total']) }}</td>
                    <td class="num">{{ number_format($row['cumulative_total']) }}</td>
                </tr>
            @empty
                <tr><td colspan="{{ $events->count() + 3 }}">No registration trend data found.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($report['university_directory']->isNotEmpty())
        <h2>University Helper Directory</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 28%;">University</th>
                    <th>Acronym</th>
                    <th>ESTD</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Specialization</th>
                    <th>Website</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report['university_directory'] as $university)
                    <tr>
                        <td>{{ $university->university_name }}</td>
                        <td>{{ $university->acronym ?: '---' }}</td>
                        <td>{{ $university->estd ?: '---' }}</td>
                        <td>{{ $university->type ?: '---' }}</td>
                        <td>{{ $university->location ?: '---' }}</td>
                        <td>{{ $university->specialization ?: '---' }}</td>
                        <td>{{ $university->website ?: '---' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($registrations->isNotEmpty())
        <div class="page-break"></div>
        <h2>Registration Details</h2>

        @foreach ($registrations as $registration)
            <div class="registration-block">
                <div class="registration-header">
                    <p class="registration-title">{{ $registration->registration_code }} - {{ $registration->team_name }}</p>
                    <p class="registration-meta">
                        {{ $registration->event?->code }} - {{ $registration->event?->name }} |
                        Registered {{ $registration->created_at?->format('Y-m-d H:i:s') }} |
                        Updated {{ $registration->updated_at?->format('Y-m-d H:i:s') }}
                    </p>
                </div>

                <div class="two-col">
                    <div>
                        <h3>Team & Lead</h3>
                        <p><strong>Institution:</strong> {{ $registration->institution }}</p>
                        <p><strong>CA:</strong> {{ $registration->ca ?: '---' }}</p>
                        <p><strong>Lead:</strong> {{ $registration->contact_name }}</p>
                        <p><strong>Email:</strong> {{ $registration->contact_email }}</p>
                        <p><strong>Phone:</strong> {{ $registration->contact_phone }}</p>
                        <p><strong>Registration Status:</strong> {{ $registration->status }}</p>
                        <p><strong>Payment Status:</strong> {{ $registration->payment_status }}</p>
                    </div>
                    <div>
                        <h3>Payment & Final Stage</h3>
                        <p><strong>Payment Amount:</strong> {{ $registration->payment ? number_format((int) $registration->payment->amount) : '---' }}</p>
                        <p><strong>Method:</strong> {{ $registration->payment?->method ?: '---' }}</p>
                        <p><strong>TRX ID:</strong> {{ $registration->payment?->trx_id ?: '---' }}</p>
                        <p><strong>Payment Record Status:</strong> {{ $registration->payment?->status ?: '---' }}</p>
                        <p><strong>Submitted:</strong> {{ $registration->payment?->submitted_at?->format('Y-m-d H:i:s') ?: '---' }}</p>
                        <p><strong>Verified:</strong> {{ $registration->payment?->verified_at?->format('Y-m-d H:i:s') ?: '---' }}</p>
                        <p><strong>Final TRX:</strong> {{ $registration->finalRegistration?->trx_id ?: '---' }}</p>
                        <p><strong>Final Status:</strong> {{ $registration->finalRegistration?->status ?: '---' }}</p>
                    </div>
                </div>

                @if ($registration->coach)
                    <h3 style="padding: 0 7px;">Coach</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Official Email</th>
                                <th>Contact</th>
                                <th>T-Shirt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $registration->coach->name }}</td>
                                <td>{{ $registration->coach->designation }}</td>
                                <td>{{ $registration->coach->official_email }}</td>
                                <td>{{ $registration->coach->contact_number }}</td>
                                <td>{{ $registration->coach->tshirt_size ?: '---' }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                <h3 style="padding: 0 7px;">Participants</h3>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 18%;">Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Student ID</th>
                            <th>University</th>
                            <th>Leader</th>
                            <th>T-Shirt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registration->participants as $participant)
                            <tr>
                                <td>{{ $participant->full_name }}</td>
                                <td>{{ $participant->email }}</td>
                                <td>{{ $participant->phone }}</td>
                                <td>{{ $participant->student_id }}</td>
                                <td>{{ $participant->university }}</td>
                                <td class="center">{{ $participant->is_leader ? 'Yes' : 'No' }}</td>
                                <td>{{ $participant->tshirt_size ?: '---' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</body>
</html>
