<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationStatusController extends Controller
{
    /**
     * Show registration status lookup.
     */
    public function index(Request $request): View
    {
        $code = strtoupper(trim((string) $request->query('code')));
        $registration = null;
        $searched = $code !== '';

        if ($searched) {
            $registration = Registration::with(['event', 'participants', 'payment', 'coach', 'finalRegistration'])
                ->where('registration_code', $code)
                ->first();
        }

        return view('registrations.status', compact('code', 'registration', 'searched'));
    }
}
