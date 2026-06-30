<?php

namespace App\Http\Controllers;

use App\Actions\SendRegistrationTelegramNotification;
use App\Models\Registration;
use App\Models\FinalRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinalRegistrationController extends Controller
{
    private const TSHIRT_SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

    public function show(string $registration_code): View|RedirectResponse
    {
        $registration = $this->eligibleRegistration($registration_code);

        if (! $registration) {
            return redirect('/');
        }

        return view('registrations.final-registration', [
            'registration' => $registration,
            'tshirtSizes' => self::TSHIRT_SIZES,
            'requiresPayment' => $registration->event?->isFinalRoundPaidType() ?? false,
        ]);
    }

    public function store(Request $request, string $registration_code): RedirectResponse
    {
        $registration = $this->eligibleRegistration($registration_code);

        if (! $registration) {
            return redirect('/');
        }

        $requiresPayment = $registration->event?->isFinalRoundPaidType() ?? false;

        $validated = $request->validate([
            'team_name' => ['required', 'string', 'max:255'],
            'payment_method' => [$requiresPayment ? 'required' : 'nullable', Rule::in(['bkash', 'nagad'])],
            'trx_id' => [$requiresPayment ? 'required' : 'nullable', 'string', 'max:255'],
            'participants' => ['required', 'array'],
            'participants.*.id' => ['required', 'integer'],
            'participants.*.full_name' => ['required', 'string', 'max:255'],
            'participants.*.email' => ['required', new \App\Rules\StrictEmail(), 'max:255'],
            'participants.*.phone' => ['required', 'string', 'max:30'],
            'participants.*.student_id' => ['nullable', 'string', 'max:255'],
            'participants.*.tshirt_size' => ['required', Rule::in(self::TSHIRT_SIZES)],
            'coach.id' => ['nullable', 'integer'],
            'coach.name' => ['nullable', 'required_with:coach.id', 'string', 'max:255'],
            'coach.designation' => ['nullable', 'required_with:coach.id', 'string', 'max:255'],
            'coach.official_email' => ['nullable', 'required_with:coach.id', new \App\Rules\StrictEmail(), 'max:255'],
            'coach.contact_number' => ['nullable', 'required_with:coach.id', 'string', 'max:30'],
            'coach.tshirt_size' => ['nullable', 'required_with:coach.id', Rule::in(self::TSHIRT_SIZES)],
        ]);

        DB::transaction(function () use ($registration, $validated, $requiresPayment): void {
            $registration->update([
                'team_name' => $validated['team_name'],
            ]);

            if ($requiresPayment) {
                $registration->payment()->updateOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'amount' => $registration->event?->amount ?? 0,
                        'method' => $validated['payment_method'],
                        'trx_id' => $validated['trx_id'],
                        'status' => 'submitted',
                        'submitted_at' => now(),
                        'verified_at' => null,
                    ],
                );

                $registration->update([
                    'payment_status' => 'submitted',
                ]);
            }

            $registration->finalRegistration()->updateOrCreate(
                ['registration_id' => $registration->id],
                [
                    'trx_id' => $requiresPayment ? $validated['trx_id'] : null,
                    'status' => 'submitted',
                ],
            );

            foreach ($validated['participants'] as $participantData) {
                $participant = $registration->participants->firstWhere('id', (int) $participantData['id']);

                if ($participant) {
                    $participant->update([
                        'full_name' => $participantData['full_name'],
                        'email' => $participantData['email'],
                        'phone' => $participantData['phone'],
                        'student_id' => $this->participantStudentId($participantData['student_id'] ?? null),
                        'tshirt_size' => $participantData['tshirt_size'],
                    ]);

                    if ($participant->is_leader) {
                        $registration->update([
                            'contact_name' => $participantData['full_name'],
                            'contact_email' => $participantData['email'],
                            'contact_phone' => $participantData['phone'],
                        ]);
                    }
                }
            }

            if (
                $registration->coach &&
                isset($validated['coach']['id']) &&
                (int) $validated['coach']['id'] === $registration->coach->id &&
                filled($validated['coach']['tshirt_size'] ?? null)
            ) {
                $registration->coach->update([
                    'name' => $validated['coach']['name'],
                    'designation' => $validated['coach']['designation'],
                    'official_email' => $validated['coach']['official_email'],
                    'contact_number' => $validated['coach']['contact_number'],
                    'tshirt_size' => $validated['coach']['tshirt_size'],
                ]);
            }
        });

        $registration->refresh();
        SendRegistrationTelegramNotification::queue($registration, 'final_registration_submitted');

        return back()->with('status', 'Final registration details submitted successfully.');
    }

    private function eligibleRegistration(string $registrationCode): ?Registration
    {
        $registration = Registration::with(['event', 'participants', 'coach', 'finalRegistration', 'payment'])
            ->where('registration_code', strtoupper(trim($registrationCode)))
            ->first();

        if (! $registration || ! $registration->event) {
            return null;
        }

        if ($registration->finalRegistration?->status === 'approved') {
            return null;
        }

        if ($registration->event->isFinalRoundPaidType()) {
            return $registration->status === 'verified' ? $registration : null;
        }

        if ($registration->event->isInitialPaidType()) {
            return $registration->payment_status === 'confirmed' &&
                in_array($registration->finalRegistration?->status, [FinalRegistration::STATUS_INVITED, FinalRegistration::STATUS_SUBMITTED], true)
                ? $registration
                : null;
        }

        return null;
    }
}
