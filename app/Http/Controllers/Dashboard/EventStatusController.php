<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventStatusController extends Controller
{
    public function index(): View
    {
        return view('dashboard.events.status', [
            'events' => Event::orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'rulebook_link' => ['nullable', 'url', 'max:255'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'action' => ['required', 'in:save,live,down'],
        ]);

        $event->update([
            'rulebook_link' => $validated['rulebook_link'] ?? null,
            'amount' => $validated['amount'] ?? 0,
            'is_live' => match ($validated['action']) {
                'live' => true,
                'down' => false,
                default => $event->is_live,
            },
        ]);

        return back()->with('status', "{$event->name} updated successfully.");
    }
}
