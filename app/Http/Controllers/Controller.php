<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

abstract class Controller
{
    protected function registrationEvent(string $code): Event
    {
        return Event::where('code', $code)->firstOrFail();
    }

    protected function registrationComingSoon(Event $event): View
    {
        return view('registrations.coming-soon', compact('event'));
    }

    protected function ensureRegistrationIsLive(Event $event): void
    {
        abort_unless($event->is_live, 403, 'Registration is not live yet.');
    }
}
