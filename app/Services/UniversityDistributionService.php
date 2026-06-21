<?php

namespace App\Services;

use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class UniversityDistributionService
{
    private const MAX_CHART_BUCKETS = 20;

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
}
