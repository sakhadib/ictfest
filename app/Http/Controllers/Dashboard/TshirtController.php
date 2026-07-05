<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TshirtController extends Controller
{
    private const STANDARD_SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'];

    public function index(): View
    {
        $events = Event::query()->orderBy('code')->get();
        $participantCounts = $this->countsFrom('participants');
        $coachCounts = $this->countsFrom('registration_coaches');
        $sizes = $this->sizes($participantCounts, $coachCounts);

        $rows = $events->map(function (Event $event) use ($participantCounts, $coachCounts, $sizes): array {
            $participantBySize = $this->emptySizeMap($sizes);
            $coachBySize = $this->emptySizeMap($sizes);

            foreach ($participantCounts->get($event->code, collect()) as $size => $count) {
                $participantBySize[$size] = $count;
            }

            foreach ($coachCounts->get($event->code, collect()) as $size => $count) {
                $coachBySize[$size] = $count;
            }

            $totalBySize = $this->emptySizeMap($sizes);

            foreach ($sizes as $size) {
                $totalBySize[$size] = $participantBySize[$size] + $coachBySize[$size];
            }

            return [
                'event' => $event,
                'participants' => $participantBySize,
                'coaches' => $coachBySize,
                'totals' => $totalBySize,
                'participant_total' => array_sum($participantBySize),
                'coach_total' => array_sum($coachBySize),
                'grand_total' => array_sum($totalBySize),
            ];
        });

        $overall = $this->emptySizeMap($sizes);

        foreach ($rows as $row) {
            foreach ($sizes as $size) {
                $overall[$size] += $row['totals'][$size];
            }
        }

        return view('dashboard.tshirts.index', [
            'rows' => $rows,
            'sizes' => $sizes,
            'overall' => $overall,
            'overallTotal' => array_sum($overall),
        ]);
    }

    private function countsFrom(string $table): Collection
    {
        return DB::table($table)
            ->join('registrations', 'registrations.id', '=', "{$table}.registration_id")
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->whereNotNull("{$table}.tshirt_size")
            ->where("{$table}.tshirt_size", '!=', '')
            ->select([
                'events.code',
                DB::raw("upper({$table}.tshirt_size) as size"),
                DB::raw('count(*) as total'),
            ])
            ->groupBy('events.code', DB::raw("upper({$table}.tshirt_size)"))
            ->get()
            ->groupBy('code')
            ->map(fn (Collection $counts): Collection => $counts->mapWithKeys(
                fn ($count): array => [$count->size => (int) $count->total],
            ));
    }

    private function sizes(Collection $participantCounts, Collection $coachCounts): array
    {
        $discovered = $participantCounts
            ->flatMap(fn (Collection $counts): array => $counts->keys()->all())
            ->merge($coachCounts->flatMap(fn (Collection $counts): array => $counts->keys()->all()))
            ->unique()
            ->values()
            ->all();

        return collect(self::STANDARD_SIZES)
            ->merge($discovered)
            ->unique()
            ->values()
            ->all();
    }

    private function emptySizeMap(array $sizes): array
    {
        return array_fill_keys($sizes, 0);
    }
}
