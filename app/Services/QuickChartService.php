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

        return $this->storePng($response->body(), 'ictfest-trend-');
    }

    /**
     * @param list<string> $labels
     * @param list<int> $data
     */
    public function barChartPng(array $labels, array $data, string $title, string $datasetLabel): string
    {
        $wrappedLabels = array_map(fn (string $label) => $this->wrapLabel($label), $labels);
        $width = min(4200, max(1400, count($labels) * 150));

        $response = Http::timeout(30)->post('https://quickchart.io/chart', [
            'format' => 'png',
            'width' => $width,
            'height' => 850,
            'backgroundColor' => 'white',
            'chart' => [
                'type' => 'bar',
                'data' => [
                    'labels' => $wrappedLabels,
                    'datasets' => [[
                        'label' => $datasetLabel,
                        'data' => $data,
                        'backgroundColor' => '#d4574e',
                        'borderColor' => '#b9423a',
                        'borderWidth' => 1,
                    ]],
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
                    'layout' => [
                        'padding' => [
                            'bottom' => 20,
                        ],
                    ],
                    'scales' => [
                        'xAxes' => [[
                            'scaleLabel' => [
                                'display' => true,
                                'labelString' => 'University',
                            ],
                            'ticks' => [
                                'autoSkip' => false,
                                'minRotation' => 0,
                                'maxRotation' => 0,
                                'fontSize' => count($labels) > 18 ? 9 : 11,
                            ],
                        ]],
                        'yAxes' => [[
                            'scaleLabel' => [
                                'display' => true,
                                'labelString' => 'Participant Count',
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

        return $this->storePng($response->body(), 'ictfest-univ-');
    }

    /**
     * @return string|list<string>
     */
    private function wrapLabel(string $label): string|array
    {
        $label = trim($label);

        $length = function_exists('mb_strlen') ? mb_strlen($label) : strlen($label);

        if ($length <= 18) {
            return $label;
        }

        return explode("\n", wordwrap($label, 18, "\n", false));
    }

    private function storePng(string $contents, string $prefix): string
    {
        $path = tempnam(sys_get_temp_dir(), $prefix);

        if (! $path) {
            throw new RuntimeException('Could not create a temporary chart file.');
        }

        $pngPath = $path.'.png';
        rename($path, $pngPath);
        file_put_contents($pngPath, $contents);

        return $pngPath;
    }
}
