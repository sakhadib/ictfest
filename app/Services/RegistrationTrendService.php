<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RegistrationTrendService
{
    /**
     * @return array<string, mixed>
     */
    public function singleEvent(string $code): array
    {
        $event = Event::where('code', str_pad($code, 2, '0', STR_PAD_LEFT))->first();

        if (! $event) {
            return [
                'ok' => false,
                'message' => 'No event found for code '.$code.'. Try trend 01, trend 02, etc.',
            ];
        }

        $dailyCounts = $this->dailyCountsForEvent($event->id);

        if ($dailyCounts->isEmpty()) {
            return [
                'ok' => false,
                'message' => 'No registrations found yet for '.$event->code.' - '.$event->name.'.',
            ];
        }

        $series = $this->cumulativeSeries($dailyCounts);

        return [
            'ok' => true,
            'title' => $event->code.' - '.$event->name.' Registration Trend',
            'labels' => $series['labels'],
            'datasets' => [
                [
                    'label' => $event->name,
                    'data' => $series['data'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function allEvents(): array
    {
        $events = Event::whereIn('code', ['01', '02', '03', '04', '05', '06'])
            ->orderBy('code')
            ->get();

        $firstRegistrationDate = Registration::query()
            ->selectRaw('MIN(DATE(created_at)) as first_registration_date')
            ->value('first_registration_date');

        if (! $firstRegistrationDate) {
            return [
                'ok' => false,
                'message' => 'No registrations found yet.',
            ];
        }

        $labels = $this->dateLabels(Carbon::parse($firstRegistrationDate), now());
        $datasets = [];

        foreach ($events as $event) {
            $datasets[] = [
                'label' => $event->code.' - '.$event->name,
                'data' => $this->cumulativeSeriesForLabels($this->dailyCountsForEvent($event->id), $labels),
            ];
        }

        return [
            'ok' => true,
            'title' => config('app.name').' Registration Trend',
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    /**
     * @return Collection<string, int>
     */
    private function dailyCountsForEvent(int $eventId): Collection
    {
        return Registration::query()
            ->where('event_id', $eventId)
            ->select([
                DB::raw('DATE(created_at) as registration_date'),
                DB::raw('COUNT(*) as registrations_count'),
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('registration_date')
            ->get()
            ->mapWithKeys(fn ($row) => [
                Carbon::parse($row->registration_date)->toDateString() => (int) $row->registrations_count,
            ]);
    }

    /**
     * @param Collection<string, int> $dailyCounts
     * @return array{labels: list<string>, data: list<int>}
     */
    private function cumulativeSeries(Collection $dailyCounts): array
    {
        $labels = $this->dateLabels(Carbon::parse($dailyCounts->keys()->first()), now());

        return [
            'labels' => $labels,
            'data' => $this->cumulativeSeriesForLabels($dailyCounts, $labels),
        ];
    }

    /**
     * @param Collection<string, int> $dailyCounts
     * @param list<string> $labels
     * @return list<int>
     */
    private function cumulativeSeriesForLabels(Collection $dailyCounts, array $labels): array
    {
        $runningTotal = 0;
        $series = [];

        foreach ($labels as $label) {
            $runningTotal += (int) ($dailyCounts[$label] ?? 0);
            $series[] = $runningTotal;
        }

        return $series;
    }

    /**
     * @return list<string>
     */
    private function dateLabels(Carbon $start, Carbon $end): array
    {
        return collect(CarbonPeriod::create($start->startOfDay(), $end->startOfDay()))
            ->map(fn (Carbon $date) => $date->toDateString())
            ->values()
            ->all();
    }
}
