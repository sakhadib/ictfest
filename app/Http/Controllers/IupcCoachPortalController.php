<?php

namespace App\Http\Controllers;

use App\Actions\SendRegistrationTelegramNotification;
use App\Models\FinalRegistration;
use App\Models\IupcCoachActivityLog;
use App\Models\IupcCoachLink;
use App\Models\Registration;
use App\Rules\StrictEmail;
use App\Services\IupcCoachPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class IupcCoachPortalController extends Controller
{
    public function __construct(
        private readonly IupcCoachPortalService $portal,
    ) {
    }

    public function show(string $token): View
    {
        $link = $this->activeLink($token);
        abort_unless($link, 404);

        $allocation = $link->allocation()->with(['aliases', 'coaches'])->firstOrFail();
        $registrations = $this->portal->registrationsQuery($allocation)
            ->with(['participants', 'coach', 'payment', 'finalRegistration'])
            ->orderBy('team_name')
            ->get();

        return view('iupc-coach.portal', [
            'link' => $link->loadMissing('coach'),
            'allocation' => $allocation,
            'registrations' => $registrations,
            'submittedCount' => $this->portal->submittedCount($allocation),
            'remainingSlots' => $this->portal->remainingSlots($allocation),
            'tshirtSizes' => IupcCoachPortalService::TSHIRT_SIZES,
            'packageAmounts' => IupcCoachPortalService::PACKAGE_AMOUNTS,
            'logs' => $allocation->activityLogs()
                ->with(['coachLink.coach', 'registration'])
                ->latest()
                ->limit(50)
                ->get(),
        ]);
    }

    public function submit(Request $request, string $token, Registration $registration): RedirectResponse
    {
        $link = $this->activeLink($token);
        abort_unless($link, 404);

        $allocation = $link->allocation;
        abort_unless($this->portal->registrationsQuery($allocation)->whereKey($registration->id)->exists(), 404);

        $registration->load(['participants', 'coach', 'payment', 'finalRegistration', 'event']);

        $validated = $request->validate([
            'team_name' => ['required', 'string', 'max:255'],
            'payment_package' => ['required', Rule::in(array_keys(IupcCoachPortalService::PACKAGE_AMOUNTS))],
            'payment_method' => ['required', Rule::in(['bkash', 'nagad'])],
            'trx_id' => ['required', 'string', 'max:255'],
            'participants' => ['required', 'array', 'size:3'],
            'participants.*.id' => ['required', 'integer'],
            'participants.*.full_name' => ['required', 'string', 'max:255'],
            'participants.*.email' => ['required', new StrictEmail(), 'max:255'],
            'participants.*.phone' => ['required', 'string', 'max:30'],
            'participants.*.student_id' => ['nullable', 'string', 'max:255'],
            'participants.*.tshirt_size' => ['required', Rule::in(IupcCoachPortalService::TSHIRT_SIZES)],
            'coach.id' => ['nullable', 'integer'],
            'coach.name' => ['nullable', 'required_with:coach.id', 'string', 'max:255'],
            'coach.designation' => ['nullable', 'required_with:coach.id', 'string', 'max:255'],
            'coach.official_email' => ['nullable', 'required_with:coach.id', new StrictEmail(), 'max:255'],
            'coach.contact_number' => ['nullable', 'required_with:coach.id', 'string', 'max:30'],
            'coach.tshirt_size' => ['nullable', 'required_with:coach.id', Rule::in(IupcCoachPortalService::TSHIRT_SIZES)],
        ]);

        $amount = IupcCoachPortalService::PACKAGE_AMOUNTS[$validated['payment_package']];
        $before = $this->registrationSnapshot($registration);

        DB::transaction(function () use ($allocation, $registration, $validated, $amount, $link, $request, $before): void {
            $registration = Registration::query()
                ->with(['participants', 'coach', 'payment', 'finalRegistration', 'event'])
                ->whereKey($registration->id)
                ->lockForUpdate()
                ->firstOrFail();

            $alreadySubmitted = in_array($registration->finalRegistration?->status, [
                FinalRegistration::STATUS_SUBMITTED,
                FinalRegistration::STATUS_APPROVED,
            ], true);

            if (! $alreadySubmitted && $this->portal->submittedCount($allocation) >= $allocation->slot_count) {
                throw ValidationException::withMessages([
                    'slot' => 'No slots are left for this university.',
                ]);
            }

            $registration->update([
                'team_name' => $validated['team_name'],
                'status' => 'verified',
                'payment_status' => 'submitted',
            ]);

            $registration->payment()->updateOrCreate(
                ['registration_id' => $registration->id],
                [
                    'amount' => $amount,
                    'method' => $validated['payment_method'],
                    'trx_id' => $validated['trx_id'],
                    'status' => 'submitted',
                    'submitted_at' => now(),
                    'verified_at' => null,
                ],
            );

            $registration->finalRegistration()->updateOrCreate(
                ['registration_id' => $registration->id],
                [
                    'trx_id' => $validated['trx_id'],
                    'status' => FinalRegistration::STATUS_SUBMITTED,
                    'payment_package' => $validated['payment_package'],
                    'payment_amount' => $amount,
                ],
            );

            foreach ($validated['participants'] as $participantData) {
                $participant = $registration->participants->firstWhere('id', (int) $participantData['id']);

                if (! $participant) {
                    throw ValidationException::withMessages([
                        'participants' => 'Invalid participant submitted.',
                    ]);
                }

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

            if (
                $registration->coach &&
                isset($validated['coach']['id']) &&
                (int) $validated['coach']['id'] === $registration->coach->id
            ) {
                $registration->coach->update([
                    'name' => $validated['coach']['name'],
                    'designation' => $validated['coach']['designation'],
                    'official_email' => $validated['coach']['official_email'],
                    'contact_number' => $validated['coach']['contact_number'],
                    'tshirt_size' => $validated['coach']['tshirt_size'],
                ]);
            }

            $registration->refresh()->load(['participants', 'coach', 'payment', 'finalRegistration']);

            IupcCoachActivityLog::query()->create([
                'iupc_university_allocation_id' => $allocation->id,
                'iupc_coach_link_id' => $link->id,
                'registration_id' => $registration->id,
                'action' => $alreadySubmitted ? 'team_updated' : 'team_submitted',
                'summary' => sprintf(
                    '%s %s team %s (%s).',
                    $link->coach?->name ?? 'A coach',
                    $alreadySubmitted ? 'updated' : 'submitted',
                    $registration->team_name,
                    $registration->registration_code,
                ),
                'before' => $before,
                'after' => $this->registrationSnapshot($registration),
                'ip_address' => $request->ip(),
                'user_agent' => str((string) $request->userAgent())->limit(255)->toString(),
            ]);
        });

        $registration->refresh();
        SendRegistrationTelegramNotification::queue($registration, 'final_registration_submitted');

        return back()->with('status', 'Team final registration submitted for payment verification.');
    }

    private function activeLink(string $token): ?IupcCoachLink
    {
        return IupcCoachLink::query()
            ->with(['allocation', 'coach'])
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('disabled_at')
            ->first();
    }

    private function registrationSnapshot(Registration $registration): array
    {
        $registration->loadMissing(['participants', 'coach', 'payment', 'finalRegistration']);

        return [
            'team_name' => $registration->team_name,
            'status' => $registration->status,
            'payment_status' => $registration->payment_status,
            'payment' => $registration->payment?->only(['amount', 'method', 'trx_id', 'status']),
            'final_registration' => $registration->finalRegistration?->only(['trx_id', 'status', 'payment_package', 'payment_amount']),
            'participants' => $registration->participants
                ->map(fn ($participant): array => $participant->only(['id', 'full_name', 'email', 'phone', 'student_id', 'tshirt_size']))
                ->all(),
            'coach' => $registration->coach?->only(['name', 'designation', 'official_email', 'contact_number', 'tshirt_size']),
        ];
    }
}
