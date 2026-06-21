<?php

namespace App\Services;

use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class UniversityDistributionService
{
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

        return [
            'ok' => true,
            'title' => config('app.name').' University-wise Participant Count Distribution',
            'labels' => $rows->pluck('university_name')->map(fn ($name) => (string) $name)->all(),
            'data' => $rows->pluck('participants_count')->map(fn ($count) => (int) $count)->all(),
        ];
    }
}
