<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        $this->ensurePngResponse($response, 'trend');

        $imageBytes = $response->body();

        Log::info('Trend chart generated', [
            'datasets' => count($datasets),
            'points' => count($labels),
            'size' => strlen($imageBytes),
        ]);

        return $this->storePng($imageBytes, 'ictfest-trend-');
    }

    /**
     * @param list<string> $labels
     * @param list<int> $data
     */
    public function barChartPng(array $labels, array $data, string $title, string $datasetLabel): string
    {
        $width = min(4200, max(1400, count($labels) * 150));
        $fontSize = count($labels) > 24 ? 8 : (count($labels) > 14 ? 9 : 11);

        $response = Http::timeout(30)->post('https://quickchart.io/chart', [
            'format' => 'png',
            'width' => $width,
            'height' => 850,
            'backgroundColor' => 'white',
            'chart' => [
                'type' => 'bar',
                'data' => [
                    'labels' => array_map(fn (string $label) => $this->compactLabel($label), $labels),
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
                                'fontSize' => $fontSize,
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

        $this->ensurePngResponse($response, 'university distribution');

        $imageBytes = $response->body();

        Log::info('University distribution chart generated', [
            'universities' => count($labels),
            'size' => strlen($imageBytes),
        ]);

        return $this->storePng($imageBytes, 'ictfest-univ-');
    }

    private function compactLabel(string $label): string
    {
        $label = trim($label);
        $limit = 28;

        if ((function_exists('mb_strlen') ? mb_strlen($label) : strlen($label)) <= $limit) {
            return $label;
        }

        if (function_exists('mb_substr')) {
            return rtrim(mb_substr($label, 0, $limit - 1)).'...';
        }

        return rtrim(substr($label, 0, $limit - 1)).'...';
    }

    private function ensurePngResponse(Response $response, string $chartType): void
    {
        $contentType = (string) $response->header('Content-Type');
        $bodySize = strlen($response->body());

        if (! $response->failed() && str_contains($contentType, 'image/png')) {
            return;
        }

        Log::warning('QuickChart returned an invalid response', [
            'chart_type' => $chartType,
            'status' => $response->status(),
            'content_type' => $contentType,
            'body_size' => $bodySize,
        ]);

        throw new RuntimeException('QuickChart returned an invalid '.$chartType.' chart response.');
    }

    private function storePng(string $contents, string $prefix): string
    {
        $path = tempnam(sys_get_temp_dir(), $prefix);

        if (! $path) {
            throw new RuntimeException('Could not create a temporary chart file.');
        }

        $pngPath = $path.'.png';
        rename($path, $pngPath);
        $bytesWritten = file_put_contents($pngPath, $contents);

        if ($bytesWritten === false) {
            throw new RuntimeException('Could not write the temporary chart file.');
        }

        Log::info('Temporary chart file written', [
            'prefix' => $prefix,
            'path' => basename($pngPath),
            'size' => $bytesWritten,
        ]);

        return $pngPath;
    }
}
