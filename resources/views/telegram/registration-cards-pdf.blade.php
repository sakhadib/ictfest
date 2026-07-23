<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0.32in;
            size: A4 portrait;
        }

        body {
            color: #17202f;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            line-height: 1.22;
            margin: 0;
        }

        .card {
            page-break-after: always;
            page-break-inside: avoid;
            position: relative;
            min-height: 10.95in;
        }

        .card:last-child {
            page-break-after: auto;
        }

        .brand-table,
        .info-table,
        .data-table {
            border-collapse: collapse;
            width: 100%;
        }

        .brand-table td {
            vertical-align: middle;
        }

        .left-logos {
            width: 24%;
        }

        .center-title {
            text-align: center;
            width: 52%;
        }

        .right-logos {
            text-align: right;
            width: 24%;
        }

        .ict-logo {
            height: 50px;
            max-width: 92px;
            object-fit: contain;
        }

        .org-logo {
            height: 43px;
            margin-left: 6px;
            max-width: 58px;
            object-fit: contain;
        }

        .center-title p {
            margin: 0;
        }

        .society {
            color: #d4574e;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .fest {
            color: #111827;
            font-size: 17px;
            font-weight: 800;
            margin-top: 2px;
        }

        .dept,
        .university {
            color: #4b5563;
            font-size: 10px;
            margin-top: 2px;
        }

        .divider {
            border-top: 2px solid #d4574e;
            margin: 9px 0 10px;
        }

        .card-title {
            text-align: center;
        }

        .card-title h1 {
            color: #111827;
            font-size: 27px;
            margin: 0;
            text-transform: uppercase;
        }

        .card-title p {
            color: #d4574e;
            font-size: 15px;
            font-weight: 700;
            margin: 2px 0 0;
        }

        .code-strip {
            background: #fff3f2;
            border: 1px solid #f0c8c4;
            border-radius: 7px;
            margin: 9px 0 8px;
            padding: 8px 10px;
        }

        .code-strip table {
            border-collapse: collapse;
            width: 100%;
        }

        .code-strip td {
            vertical-align: middle;
        }

        .reg-code {
            color: #d4574e;
            font-size: 23px;
            font-weight: 800;
        }

        .team-name {
            color: #111827;
            font-size: 17px;
            font-weight: 700;
            text-align: right;
        }

        .section {
            margin-top: 8px;
        }

        .section-title {
            color: #111827;
            font-size: 12px;
            font-weight: 800;
            margin: 0 0 4px;
            text-transform: uppercase;
        }

        .info-table td {
            border: 1px solid #d9dee7;
            padding: 5px 6px;
            vertical-align: top;
            width: 25%;
        }

        .label {
            color: #667085;
            display: block;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .value {
            color: #111827;
            display: block;
            font-size: 11px;
            font-weight: 600;
            margin-top: 2px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #d9dee7;
            padding: 4px 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .data-table th {
            background: #d4574e;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: 800;
            text-align: left;
            text-transform: uppercase;
        }

        .data-table td {
            font-size: 10px;
            line-height: 1.18;
        }

        .notice {
            bottom: 0;
            color: #7b8494;
            font-size: 8.5px;
            left: 0;
            position: absolute;
            right: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    @foreach ($report['registrations'] as $registration)
        <section class="card">
            <table class="brand-table">
                <tr>
                    <td class="left-logos">
                        @if ($report['assets']['ictfest_logo'])
                            <img src="{{ $report['assets']['ictfest_logo'] }}" class="ict-logo" alt="ICT Fest">
                        @endif
                    </td>
                    <td class="center-title">
                        <p class="society">IUT COMPUTER SOCIETY</p>
                        <p class="fest">IUT 12th ICT FEST 2026</p>
                        <p class="dept">Department of Computer Science and Engineering</p>
                        <p class="university">Islamic University of Technology</p>
                    </td>
                    <td class="right-logos">
                        @if ($report['assets']['iutcs_logo'])
                            <img src="{{ $report['assets']['iutcs_logo'] }}" class="org-logo" alt="IUTCS">
                        @endif
                        @if ($report['assets']['cse_logo'])
                            <img src="{{ $report['assets']['cse_logo'] }}" class="org-logo" alt="CSE">
                        @endif
                    </td>
                </tr>
            </table>

            <div class="divider"></div>

            <div class="card-title">
                <h1>Registration Card</h1>
                <p>{{ $registration->event?->name }}</p>
            </div>

            <div class="code-strip">
                <table>
                    <tr>
                        <td>
                            <span class="label">Registration Code</span>
                            <span class="reg-code">{{ $registration->registration_code }}</span>
                        </td>
                        <td class="team-name">{{ $registration->team_name }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <p class="section-title">Team Information</p>
                <table class="info-table">
                    <tr>
                        <td><span class="label">Institution</span><span class="value">{{ $registration->institution }}</span></td>
                        <td><span class="label">Campus Ambassador</span><span class="value">{{ $registration->ca ?: '---' }}</span></td>
                        <td><span class="label">Registration Status</span><span class="value">{{ ucfirst($registration->status) }}</span></td>
                        <td><span class="label">Payment Status</span><span class="value">{{ ucfirst($registration->payment_status) }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Team Lead</span><span class="value">{{ $registration->contact_name }}</span></td>
                        <td><span class="label">Lead Email</span><span class="value">{{ $registration->contact_email }}</span></td>
                        <td><span class="label">Lead Phone</span><span class="value">{{ $registration->contact_phone }}</span></td>
                        <td><span class="label">Team Size</span><span class="value">{{ $registration->participants->count() }}</span></td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <p class="section-title">Payment & Final Stage</p>
                <table class="info-table">
                    <tr>
                        <td><span class="label">Amount</span><span class="value">{{ $registration->payment ? number_format((int) $registration->payment->amount).' BDT' : '---' }}</span></td>
                        <td><span class="label">Method</span><span class="value">{{ $registration->payment?->method ? ucfirst($registration->payment->method) : '---' }}</span></td>
                        <td><span class="label">TRX ID</span><span class="value">{{ $registration->payment?->trx_id ?: '---' }}</span></td>
                        <td><span class="label">Payment Record</span><span class="value">{{ $registration->payment?->status ? ucfirst($registration->payment->status) : '---' }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Final Status</span><span class="value">{{ $registration->finalRegistration?->status ? ucfirst($registration->finalRegistration->status) : '---' }}</span></td>
                        <td><span class="label">Final TRX ID</span><span class="value">{{ $registration->finalRegistration?->trx_id ?: '---' }}</span></td>
                        <td><span class="label">Event Code</span><span class="value">{{ $registration->event?->code }}</span></td>
                        <td><span class="label">Event Fee</span><span class="value">{{ number_format((int) ($registration->event?->amount ?? 0)) }} BDT</span></td>
                    </tr>
                </table>
            </div>

            @if ($registration->coach)
                <div class="section">
                    <p class="section-title">Coach Information</p>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Official Email</th>
                                <th>Contact Number</th>
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
                </div>
            @endif

            <div class="section">
                <p class="section-title">Participants</p>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Student ID</th>
                            <th>University</th>
                            <th style="width: 7%;">Lead</th>
                            <th style="width: 8%;">T-Shirt</th>
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
                                <td>{{ $participant->is_leader ? 'Yes' : 'No' }}</td>
                                <td>{{ $participant->tshirt_size ?: '---' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="notice">Auto generated at {{ $report['generated_at'] }}. This registration card is system generated for campus verification.</p>
        </section>
    @endforeach
</body>
</html>
