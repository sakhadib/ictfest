<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class QuickChartService
{
    /**
     * @param list<string> $labels
     * @param list<array{label: string, data: list<int>}> $datasets
     */
    public function lineChartPng(array $labels, array $datasets, string $title): string
    {
        $colors = ['#d4574e', '#7cf7d4', '#9b8cff', '#ffb454', '#4ea8de', '#57cc99'];

        $chartDatasets = collect($datasets)
            ->values()
            ->map(fn (array $dataset, int $index): array => [
                'label' => $dataset['label'],
                'data' => $dataset['data'],
                'borderColor' => $colors[$index % count($colors)],
                'backgroundColor' => $colors[$index % count($colors)].'22',
                'borderWidth' => 3,
                'pointRadius' => count($labels) <= 20 ? 3 : 0,
                'tension' => 0.25,
                'fill' => false,
            ])
            ->all();

        $response = Http::timeout(30)->post('https://quickchart.io/chart', [
            'format' => 'png',
            'width' => 1200,
            'height' => 700,
            'backgroundColor' => 'white',
            'chart' => [
                'type' => 'line',
                'data' => [
                    'labels' => $labels,
                    'datasets' => $chartDatasets,
                ],
                'options' => [
                    'title' => [
                        'display' => true,
                        'text' => $title,
                        'fontSize' => 22,
                    ],
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                    ],
                    'scales' => [
                        'xAxes' => [[
                            'scaleLabel' => [
                                'display' => true,
                                'labelString' => 'Date',
                            ],
                            'ticks' => [
                                'autoSkip' => true,
                                'maxTicksLimit' => 12,
                            ],
                        ]],
                        'yAxes' => [[
                            'scaleLabel' => [
                                'display' => true,
                                'labelString' => 'Cumulative Registrations',
                            ],
                            'ticks' => [
                                'beginAtZero' => true,
                                'precision' => 0,
                            ],
                        ]],
                    ],
                ],
            ],
        ]);

        if ($response->failed() || ! str_contains((string) $response->header('Content-Type'), 'image/png')) {
            throw new RuntimeException('QuickChart returned an invalid response: '.$response->status().' '.$response->body());
        }

        $path = tempnam(sys_get_temp_dir(), 'ictfest-trend-');

        if (! $path) {
            throw new RuntimeException('Could not create a temporary chart file.');
        }

        $pngPath = $path.'.png';
        rename($path, $pngPath);
        file_put_contents($pngPath, $response->body());

        return $pngPath;
    }
}
