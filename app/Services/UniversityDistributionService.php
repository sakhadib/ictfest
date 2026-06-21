<?php

namespace App\Services;

use App\Models\Participant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UniversityDistributionService
{
    private const MAX_CHART_BUCKETS = 20;

    private const EVENT_COLUMNS = [
        '01' => 'iupc',
        '02' => 'hackathon',
        '03' => 'datathon',
        '04' => 'gamejam',
        '05' => 'fifa',
        '06' => 'valorant',
    ];

    /**
     * @return array<string, mixed>
     */
    public function participantCounts(): array
    {
        $rows = Participant::query()
            ->select([
                DB::raw("COALESCE(NULLIF(TRIM(university), ''), 'Unspecified') as university_name"),
                DB::raw('COUNT(*) as participants_count'),
            ])
            ->groupBy(DB::raw("COALESCE(NULLIF(TRIM(university), ''), 'Unspecified')"))
            ->orderByDesc('participants_count')
            ->orderBy('university_name')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'ok' => false,
                'message' => 'No participant university data found yet.',
            ];
        }

        if ($rows->count() > self::MAX_CHART_BUCKETS) {
            $visible = $rows->take(self::MAX_CHART_BUCKETS - 1)->values();
            $otherCount = $rows
                ->slice(self::MAX_CHART_BUCKETS - 1)
                ->sum(fn ($row) => (int) $row->participants_count);

            $rows = $visible->push((object) [
                'university_name' => 'Other universities',
                'participants_count' => $otherCount,
            ]);
        }

        return [
            'ok' => true,
            'title' => config('app.name').' University-wise Participant Count Distribution',
            'labels' => $rows->pluck('university_name')->map(fn ($name) => (string) $name)->all(),
            'data' => $rows->pluck('participants_count')->map(fn ($count) => (int) $count)->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function participantCountTable(): array
    {
        $rows = Participant::query()
            ->join('registrations', 'registrations.id', '=', 'participants.registration_id')
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->select([
                DB::raw("COALESCE(NULLIF(TRIM(participants.university), ''), 'Unspecified') as university_name"),
                'events.code as event_code',
                DB::raw('COUNT(participants.id) as participants_count'),
            ])
            ->whereIn('events.code', array_keys(self::EVENT_COLUMNS))
            ->groupBy(
                DB::raw("COALESCE(NULLIF(TRIM(participants.university), ''), 'Unspecified')"),
                'events.code',
            )
            ->orderBy('university_name')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'ok' => false,
                'message' => 'No participant university data found yet.',
            ];
        }

        $universities = $rows
            ->groupBy('university_name')
            ->map(fn (Collection $eventRows, string $university): array => $this->universityTableRow($university, $eventRows))
            ->sortBy([
                ['total', 'desc'],
                ['university', 'asc'],
            ])
            ->values();

        return [
            'ok' => true,
            'title' => config('app.name').' University-wise Participant Count Report',
            'generated_at' => now()->format('d M Y, h:i A'),
            'columns' => self::EVENT_COLUMNS,
            'rows' => $universities,
            'totals' => $this->totalsRow($universities),
        ];
    }

    private function universityTableRow(string $university, Collection $eventRows): array
    {
        $counts = array_fill_keys(array_values(self::EVENT_COLUMNS), 0);

        foreach ($eventRows as $eventRow) {
            $column = self::EVENT_COLUMNS[(string) $eventRow->event_code] ?? null;

            if ($column) {
                $counts[$column] = (int) $eventRow->participants_count;
            }
        }

        return [
            'university' => $university,
            ...$counts,
            'total' => array_sum($counts),
        ];
    }

    private function totalsRow(Collection $rows): array
    {
        $totals = ['university' => 'Total'];

        foreach (self::EVENT_COLUMNS as $column) {
            $totals[$column] = $rows->sum($column);
        }

        $totals['total'] = $rows->sum('total');

        return $totals;
    }
}
