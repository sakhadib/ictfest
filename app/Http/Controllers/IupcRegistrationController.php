<?php

namespace App\Http\Controllers;

use App\Actions\SendRegistrationConfirmationEmail;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class IupcRegistrationController extends Controller
{
    private const EVENT_CODE = '01';
    /**
     * Show the IUPC registration form.
     */
    public function create(): View
    {
        $event = Event::where('code', self::EVENT_CODE)->firstOrFail();

        return view('registrations.iupc-create', compact('event'));
    }

    /**
     * Store a new IUPC registration.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'team_name' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'coach.name' => ['required', 'string', 'max:255'],
            'coach.designation' => ['required', 'string', 'max:255'],
            'coach.official_email' => ['required', 'email', 'max:255'],
            'coach.contact_number' => ['required', 'string', 'max:30'],
            'participants' => ['required', 'array', 'size:3'],
            'participants.*.full_name' => ['required', 'string', 'max:255'],
            'participants.*.email' => ['required', 'email', 'max:255'],
            'participants.*.phone' => ['required', 'string', 'max:30'],
            'participants.*.student_id' => ['required', 'string', 'max:255'],
            'participants.*.university' => ['required', 'string', 'max:255'],
        ]);

        $event = Event::where('code', self::EVENT_CODE)->firstOrFail();
        $leader = $validated['participants'][0];

        $registration = DB::transaction(function () use ($event, $leader, $validated) {
            $registration = Registration::create([
                'registration_code' => $this->generateRegistrationCode(),
                'event_id' => $event->id,
                'team_name' => $validated['team_name'],
                'institution' => $validated['institution'],
                'contact_name' => $leader['full_name'],
                'contact_email' => $leader['email'],
                'contact_phone' => $leader['phone'],
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($validated['participants'] as $index => $participant) {
                $registration->participants()->create([
                    'full_name' => $participant['full_name'],
                    'email' => $participant['email'],
                    'phone' => $participant['phone'],
                    'student_id' => $participant['student_id'],
                    'university' => $participant['university'],
                    'is_leader' => $index === 0,
                ]);
            }

            $registration->coach()->create($validated['coach']);

            return $registration;
        });

        SendRegistrationConfirmationEmail::queue($registration);

        return redirect()->route('iupc.register.success', ['code' => $registration->registration_code]);
    }

    /**
     * Show the registration confirmation page.
     */
    public function success(string $code): View
    {
        $registration = Registration::where('registration_code', $code)->firstOrFail();

        abort_unless($registration->event?->code === self::EVENT_CODE, 404);

        $registration->load(['event', 'participants', 'payment', 'coach']);

        return view('registrations.iupc-success', compact('registration'));
    }

    /**
     * Generate a unique code in the format 01-48372.
     */
    private function generateRegistrationCode(): string
    {
        for ($attempt = 0; $attempt < 200; $attempt++) {
            $code = self::EVENT_CODE.'-'.random_int(10000, 99999);

            if (! Registration::where('registration_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Unable to generate a unique registration code.');
    }
}
