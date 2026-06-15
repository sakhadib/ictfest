<?php

namespace App\Http\Controllers;

use App\Models\Registration;
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
            'payment_method' => [$requiresPayment ? 'required' : 'nullable', Rule::in(['bkash', 'nagad'])],
            'trx_id' => [$requiresPayment ? 'required' : 'nullable', 'string', 'max:255'],
            'participants' => ['required', 'array'],
            'participants.*.id' => ['required', 'integer'],
            'participants.*.tshirt_size' => ['required', Rule::in(self::TSHIRT_SIZES)],
            'coach.id' => ['nullable', 'integer'],
            'coach.tshirt_size' => ['nullable', 'required_with:coach.id', Rule::in(self::TSHIRT_SIZES)],
        ]);

        DB::transaction(function () use ($registration, $validated, $requiresPayment): void {
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
                        'tshirt_size' => $participantData['tshirt_size'],
                    ]);
                }
            }

            if (
                $registration->coach &&
                isset($validated['coach']['id']) &&
                (int) $validated['coach']['id'] === $registration->coach->id &&
                filled($validated['coach']['tshirt_size'] ?? null)
            ) {
                $registration->coach->update([
                    'tshirt_size' => $validated['coach']['tshirt_size'],
                ]);
            }
        });

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

        if ($registration->event->isFinalRoundPaidType()) {
            return in_array($registration->status, ['verified', 'paid'], true) ? $registration : null;
        }

        if ($registration->event->isInitialPaidType()) {
            return $registration->status === 'paid' && $registration->payment_status === 'confirmed'
                ? $registration
                : null;
        }

        return null;
    }
}
