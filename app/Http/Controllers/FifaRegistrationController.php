<?php

namespace App\Http\Controllers;

use App\Actions\SendRegistrationConfirmationEmail;
use App\Actions\SendRegistrationConfirmationSms;
use App\Actions\SendRegistrationTelegramNotification;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class FifaRegistrationController extends Controller
{
    private const EVENT_CODE = '05';
    private const FEE = 200;

    /**
     * Show the FIFA registration form.
     */
    public function create(): View
    {
        $event = $this->registrationEvent(self::EVENT_CODE);

        if (! $event->is_live) {
            return $this->registrationComingSoon($event);
        }

        if (! $event->hasAvailableSlots()) {
            return $this->registrationSlotsFull($event);
        }

        return view('registrations.fifa-create', [
            'event' => $event,
            'universities' => $this->universitySearchOptions(),
        ]);
    }

    /**
     * Store a new FIFA registration.
     */
    public function store(Request $request): RedirectResponse
    {
        $event = $this->registrationEvent(self::EVENT_CODE);
        $this->ensureRegistrationIsLive($event);
        $this->ensureRegistrationHasAvailableSlots($event);

        $validated = $request->validate([
            'institution' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', Rule::in(['bkash', 'nagad'])],
            'trx_id' => ['required', 'string', 'max:255'],
            'participant.full_name' => ['required', 'string', 'max:255'],
            'participant.email' => ['required', 'email', 'max:255'],
            'participant.phone' => ['required', 'string', 'max:30'],
            'participant.student_id' => ['required', 'string', 'max:255'],
            'participant.university' => ['required', 'string', 'max:255'],
        ]);

        $participant = $validated['participant'];

        $registration = DB::transaction(function () use ($event, $participant, $validated) {
            $registration = Registration::create([
                'registration_code' => $this->generateRegistrationCode(),
                'event_id' => $event->id,
                'team_name' => $participant['full_name'],
                'institution' => $validated['institution'],
                'contact_name' => $participant['full_name'],
                'contact_email' => $participant['email'],
                'contact_phone' => $participant['phone'],
                'status' => 'pending',
                'payment_status' => 'submitted',
            ]);

            $registration->participants()->create([
                'full_name' => $participant['full_name'],
                'email' => $participant['email'],
                'phone' => $participant['phone'],
                'student_id' => $participant['student_id'],
                'university' => $participant['university'],
                'is_leader' => true,
            ]);

            Payment::create([
                'registration_id' => $registration->id,
                'amount' => self::FEE,
                'method' => $validated['payment_method'],
                'trx_id' => $validated['trx_id'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            return $registration;
        });

        SendRegistrationConfirmationEmail::queue($registration);
        SendRegistrationConfirmationSms::queue($registration);
        SendRegistrationTelegramNotification::queue($registration);

        return redirect()->route('fifa.register.success', ['code' => $registration->registration_code]);
    }

    /**
     * Show the registration confirmation page.
     */
    public function success(string $code): View
    {
        $registration = Registration::where('registration_code', $code)->firstOrFail();

        abort_unless($registration->event?->code === self::EVENT_CODE, 404);

        $registration->load(['event', 'participants', 'payment']);

        return view('registrations.fifa-success', compact('registration'));
    }

    /**
     * Generate a unique code in the format 05-48372.
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
