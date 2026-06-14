<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventRegistrationController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $tab = $request->query('tab') === 'done' ? 'done' : 'pending';

        $registrations = $event->registrations()
            ->with('payment')
            ->when(
                $tab === 'done',
                fn ($query) => $query->where('payment_status', 'confirmed'),
                fn ($query) => $query->where('payment_status', '!=', 'confirmed'),
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.events.registrations', [
            'event' => $event,
            'events' => Event::orderBy('code')->get(),
            'registrations' => $registrations,
            'tab' => $tab,
            'pendingCount' => $event->registrations()->where('payment_status', '!=', 'confirmed')->count(),
            'doneCount' => $event->registrations()->where('payment_status', 'confirmed')->count(),
        ]);
    }

    public function approve(Event $event, Registration $registration): RedirectResponse
    {
        $this->ensureRegistrationBelongsToEvent($event, $registration);

        DB::transaction(function () use ($registration): void {
            $registration->update([
                'status' => 'paid',
                'payment_status' => 'confirmed',
            ]);

            $registration->payment?->update([
                'status' => 'confirmed',
                'verified_at' => now(),
            ]);
        });

        return back()->with('status', 'Registration approved successfully.');
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
        });

        return back()->with('status', 'Registration moved back to pending.');
    }

    private function ensureRegistrationBelongsToEvent(Event $event, Registration $registration): void
    {
        abort_unless($registration->event_id === $event->id, 404);

        $registration->loadMissing('payment');
    }
}
