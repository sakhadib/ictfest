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
        $registration = $this->approvedRegistration($registration_code);

        if (! $registration) {
            return redirect('/');
        }

        return view('registrations.final-registration', [
            'registration' => $registration,
            'tshirtSizes' => self::TSHIRT_SIZES,
        ]);
    }

    public function store(Request $request, string $registration_code): RedirectResponse
    {
        $registration = $this->approvedRegistration($registration_code);

        if (! $registration) {
            return redirect('/');
        }

        $validated = $request->validate([
            'trx_id' => ['required', 'string', 'max:255'],
            'participants' => ['required', 'array'],
            'participants.*.id' => ['required', 'integer'],
            'participants.*.tshirt_size' => ['required', Rule::in(self::TSHIRT_SIZES)],
            'coach.id' => ['nullable', 'integer'],
            'coach.tshirt_size' => ['nullable', 'required_with:coach.id', Rule::in(self::TSHIRT_SIZES)],
        ]);

        DB::transaction(function () use ($registration, $validated): void {
            $registration->finalRegistration()->updateOrCreate(
                ['registration_id' => $registration->id],
                ['trx_id' => $validated['trx_id']],
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

        return back()->with('status', 'Final registration details saved successfully.');
    }

    private function approvedRegistration(string $registrationCode): ?Registration
    {
        return Registration::with(['event', 'participants', 'coach', 'finalRegistration'])
            ->where('registration_code', strtoupper(trim($registrationCode)))
            ->where('status', 'paid')
            ->where('payment_status', 'confirmed')
            ->first();
    }
}
