<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use JsonException;

class BusRouteController extends Controller
{
    public function day1(): View
    {
        $path = base_path('bus_day1.json');
        $payload = is_readable($path) ? file_get_contents($path) : null;

        try {
            $schedule = is_string($payload) ? json_decode($payload, true, 512, JSON_THROW_ON_ERROR) : [];
        } catch (JsonException) {
            $schedule = [];
        }

        return view('bus.day1', [
            'date' => $schedule['date'] ?? '2026-07-24',
            'destination' => $schedule['destination'] ?? 'Islamic University of Technology (IUT)',
            'routes' => collect($schedule['routes'] ?? [])->values(),
        ]);
    }
}
