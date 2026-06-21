<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0.75in;
        }

        body {
            color: #1f2933;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8px;
            line-height: 1.35;
        }

        h1 {
            color: #d4574e;
            font-size: 17px;
            margin: 0 0 5px;
        }

        .meta {
            color: #5f6b7a;
            font-size: 9px;
            margin: 0 0 14px;
        }

        .note {
            border-left: 3px solid #d4574e;
            color: #364152;
            font-size: 9px;
            margin: 0 0 14px;
            padding: 5px 0 5px 8px;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #d9dee7;
            padding: 4px 3px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #d4574e;
            color: #ffffff;
            font-size: 7px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
        }

        td {
            text-align: center;
        }

        .university {
            text-align: left;
            width: 32%;
        }

        .event-col {
            width: 8.5%;
        }

        .total-col {
            font-weight: 700;
            width: 9%;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        tfoot td {
            background: #fff3f2;
            color: #1f2933;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <h1>{{ $report['title'] }}</h1>
    <p class="meta">Generated at {{ $report['generated_at'] }}</p>
    <p class="note">This report shows participant count by university and event. Each event column counts participants, not registrations.</p>

    <table>
        <thead>
            <tr>
                <th class="university">University</th>
                <th class="event-col">IUPC</th>
                <th class="event-col">Hackathon</th>
                <th class="event-col">Datathon</th>
                <th class="event-col">Gamejam</th>
                <th class="event-col">FIFA</th>
                <th class="event-col">Valorant</th>
                <th class="total-col">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['rows'] as $row)
                <tr>
                    <td class="university">{{ $row['university'] }}</td>
                    <td>{{ number_format($row['iupc']) }}</td>
                    <td>{{ number_format($row['hackathon']) }}</td>
                    <td>{{ number_format($row['datathon']) }}</td>
                    <td>{{ number_format($row['gamejam']) }}</td>
                    <td>{{ number_format($row['fifa']) }}</td>
                    <td>{{ number_format($row['valorant']) }}</td>
                    <td class="total-col">{{ number_format($row['total']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="university">Total</td>
                <td>{{ number_format($report['totals']['iupc']) }}</td>
                <td>{{ number_format($report['totals']['hackathon']) }}</td>
                <td>{{ number_format($report['totals']['datathon']) }}</td>
                <td>{{ number_format($report['totals']['gamejam']) }}</td>
                <td>{{ number_format($report['totals']['fifa']) }}</td>
                <td>{{ number_format($report['totals']['valorant']) }}</td>
                <td class="total-col">{{ number_format($report['totals']['total']) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
