<?php

namespace App\Http\Controllers;

use App\Actions\SendRegistrationConfirmationEmail;
use App\Actions\SendRegistrationConfirmationSms;
use App\Actions\SendRegistrationTelegramNotification;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class GamejamRegistrationController extends Controller
{
    private const EVENT_CODE = '04';

    /**
     * Show the Gamejam registration form.
     */
    public function create(): View
    {
        $event = $this->registrationEvent(self::EVENT_CODE);

        if (! $event->is_live) {
            return $this->registrationComingSoon($event);
        }

        return view('registrations.gamejam-create', [
            'event' => $event,
            'universities' => $this->universitySearchOptions(),
        ]);
    }

    /**
     * Store a new Gamejam registration.
     */
    public function store(Request $request): RedirectResponse
    {
        $event = $this->registrationEvent(self::EVENT_CODE);
        $this->ensureRegistrationIsLive($event);

        $validated = $request->validate([
            'team_name' => ['required', 'string', 'max:255'],
            'participants' => ['required', 'array', 'min:1', 'max:3'],
            'participants.*.full_name' => ['required', 'string', 'max:255'],
            'participants.*.email' => ['required', 'email', 'max:255'],
            'participants.*.phone' => ['required', 'string', 'max:30'],
            'participants.*.student_id' => ['required', 'string', 'max:255'],
            'participants.*.university' => ['required', 'string', 'max:255'],
        ]);

        $participants = array_values($validated['participants']);
        $leader = $participants[0];

        $registration = DB::transaction(function () use ($event, $leader, $participants, $validated) {
            $registration = Registration::create([
                'registration_code' => $this->generateRegistrationCode(),
                'event_id' => $event->id,
                'team_name' => $validated['team_name'],
                'institution' => $leader['university'],
                'contact_name' => $leader['full_name'],
                'contact_email' => $leader['email'],
                'contact_phone' => $leader['phone'],
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($participants as $index => $participant) {
                $registration->participants()->create([
                    'full_name' => $participant['full_name'],
                    'email' => $participant['email'],
                    'phone' => $participant['phone'],
                    'student_id' => $participant['student_id'],
                    'university' => $participant['university'],
                    'is_leader' => $index === 0,
                ]);
            }

            return $registration;
        });

        SendRegistrationConfirmationEmail::queue($registration);
        SendRegistrationConfirmationSms::queue($registration);
        SendRegistrationTelegramNotification::queue($registration);

        return redirect()->route('gamejam.register.success', ['code' => $registration->registration_code]);
    }

    /**
     * Show the registration confirmation page.
     */
    public function success(string $code): View
    {
        $registration = Registration::where('registration_code', $code)->firstOrFail();

        abort_unless($registration->event?->code === self::EVENT_CODE, 404);

        $registration->load(['event', 'participants']);

        return view('registrations.gamejam-success', compact('registration'));
    }

    /**
     * Generate a unique code in the format 04-48372.
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
