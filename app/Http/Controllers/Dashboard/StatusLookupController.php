<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusLookupController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q'));
        $searched = $query !== '';
        $registrations = collect();

        if ($searched) {
            $normalizedCode = strtoupper($query);

            $registrations = Registration::with(['event', 'participants', 'payment', 'coach'])
                ->where('registration_code', $normalizedCode)
                ->orWhere('contact_email', $query)
                ->orWhereHas('coach', fn ($coachQuery) => $coachQuery->where('official_email', $query))
                ->latest()
                ->get();
        }

        return view('dashboard.status.index', [
            'query' => $query,
            'searched' => $searched,
            'registrations' => $registrations,
        ]);
    }
}
