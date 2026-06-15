<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\SendRegistrationStageEmail;
use App\Actions\SendRegistrationStageSms;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FinalRegistration;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventRegistrationController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $tab = in_array($request->query('tab'), ['pending', 'final', 'review', 'done'], true)
            ? $request->query('tab')
            : 'pending';

        $registrations = $event->registrations()
            ->with(['payment', 'finalRegistration', 'participants'])
            ->when($tab === 'pending', fn ($query) => $this->pendingReview($query, $event))
            ->when($tab === 'final', fn ($query) => $this->awaitingFinalSubmission($query, $event))
            ->when($tab === 'review', fn ($query) => $this->finalReview($query, $event))
            ->when($tab === 'done', fn ($query) => $query
                ->where(function ($query) use ($event): void {
                    if ($event->isFinalRoundPaidType()) {
                        $query->where('status', 'paid')->where('payment_status', 'confirmed');
                    } else {
                        $query->whereHas('finalRegistration', fn ($query) => $query->where('status', FinalRegistration::STATUS_APPROVED));
                    }
                }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.events.registrations', [
            'event' => $event,
            'events' => Event::orderBy('code')->get(),
            'registrations' => $registrations,
            'tab' => $tab,
            'counts' => [
                'pending' => $this->pendingReview($event->registrations(), $event)->count(),
                'final' => $this->awaitingFinalSubmission($event->registrations(), $event)->count(),
                'review' => $this->finalReview($event->registrations(), $event)->count(),
                'done' => $event->isFinalRoundPaidType()
                    ? $event->registrations()->where('status', 'paid')->where('payment_status', 'confirmed')->count()
                    : $event->registrations()->whereHas('finalRegistration', fn ($query) => $query->where('status', FinalRegistration::STATUS_APPROVED))->count(),
            ],
        ]);
    }

    public function approve(Event $event, Registration $registration): RedirectResponse
    {
        $this->ensureRegistrationBelongsToEvent($event, $registration);

        $stage = DB::transaction(function () use ($event, $registration): ?string {
            if ($event->isFinalRoundPaidType() && $registration->status === 'pending') {
                $registration->update([
                    'status' => 'verified',
                    'payment_status' => 'unpaid',
                ]);

                return 'final_qualified';
            }

            if ($event->isFinalRoundPaidType() && $registration->payment_status === 'submitted') {
                $registration->update([
                    'status' => 'paid',
                    'payment_status' => 'confirmed',
                ]);

                $registration->payment?->update([
                    'status' => 'confirmed',
                    'verified_at' => now(),
                ]);

                $registration->finalRegistration?->update([
                    'status' => FinalRegistration::STATUS_APPROVED,
                ]);

                return 'final_payment_confirmed';
            }

            if (
                $event->isInitialPaidType() &&
                $registration->status === 'pending' &&
                $registration->payment_status !== 'confirmed'
            ) {
                $registration->update(['payment_status' => 'confirmed']);

                $registration->payment?->update([
                    'status' => 'confirmed',
                    'verified_at' => now(),
                ]);

                return 'initial_payment_confirmed';
            }

            if (
                $event->isInitialPaidType() &&
                $registration->payment_status === 'confirmed' &&
                (
                    ! $registration->finalRegistration ||
                    $registration->finalRegistration->status === FinalRegistration::STATUS_REJECTED
                )
            ) {
                $registration->finalRegistration()->updateOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'trx_id' => null,
                        'status' => FinalRegistration::STATUS_INVITED,
                    ],
                );

                return 'final_intake_invited';
            }

            if ($event->isInitialPaidType() && $registration->finalRegistration?->status === FinalRegistration::STATUS_SUBMITTED) {
                $registration->finalRegistration->update([
                    'status' => FinalRegistration::STATUS_APPROVED,
                ]);

                return 'final_intake_confirmed';
            }

            return null;
        });

        if ($stage) {
            $registration->refresh();
            SendRegistrationStageEmail::queue($registration, $stage);
            SendRegistrationStageSms::queue($registration, $stage);

            return back()->with('status', 'Registration approved successfully.');
        }

        return back()->with('status', 'No approval action was available for this registration.');
    }

    public function rejectFinal(Event $event, Registration $registration): RedirectResponse
    {
        $this->ensureRegistrationBelongsToEvent($event, $registration);

        $registration->finalRegistration?->update([
            'status' => FinalRegistration::STATUS_REJECTED,
        ]);

        return back()->with('status', 'Final submission rejected.');
    }

    public function unapprove(Event $event, Registration $registration): RedirectResponse
    {
        $this->ensureRegistrationBelongsToEvent($event, $registration);

        DB::transaction(function () use ($registration): void {
            $registration->update([
                'status' => 'pending',
                'payment_status' => $registration->payment ? 'submitted' : 'unpaid',
            ]);

            $registration->payment?->update([
                'status' => 'submitted',
                'verified_at' => null,
            ]);

            $registration->finalRegistration?->update([
                'status' => FinalRegistration::STATUS_SUBMITTED,
            ]);
        });

        return back()->with('status', 'Registration moved back to pending.');
    }

    private function ensureRegistrationBelongsToEvent(Event $event, Registration $registration): void
    {
        abort_unless($registration->event_id === $event->id, 404);

        $registration->loadMissing(['payment', 'finalRegistration']);
    }

    private function pendingReview($query, Event $event)
    {
        if ($event->isInitialPaidType()) {
            return $query
                ->where('status', 'pending')
                ->where('payment_status', '!=', 'confirmed');
        }

        return $query->where('status', 'pending');
    }

    private function awaitingFinalSubmission($query, Event $event)
    {
        if ($event->isFinalRoundPaidType()) {
            return $query
                ->where('status', 'verified')
                ->where(function ($query): void {
                    $query->whereDoesntHave('finalRegistration')
                        ->orWhereHas('finalRegistration', fn ($query) => $query->where('status', FinalRegistration::STATUS_REJECTED));
                });
        }

        return $query
            ->where('payment_status', 'confirmed')
            ->where(function ($query): void {
                $query->whereDoesntHave('finalRegistration')
                    ->orWhereHas('finalRegistration', fn ($query) => $query->where('status', FinalRegistration::STATUS_REJECTED));
            });
    }

    private function finalReview($query, Event $event)
    {
        if ($event->isFinalRoundPaidType()) {
            return $query->whereHas('finalRegistration', fn ($query) => $query->where('status', FinalRegistration::STATUS_SUBMITTED));
        }

        return $query->whereHas('finalRegistration', fn ($query) => $query->whereIn('status', [
            FinalRegistration::STATUS_INVITED,
            FinalRegistration::STATUS_SUBMITTED,
        ]));
    }
}
