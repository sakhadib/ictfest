<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\IupcBkashRecipient;
use App\Services\IupcBkashRecipientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IupcBkashRecipientController extends Controller
{
    public function __construct(
        private readonly IupcBkashRecipientService $recipients,
    ) {
    }

    public function index(): View
    {
        $currentRecipient = $this->recipients->current();

        return view('dashboard.iupc-bkash.index', [
            'recipients' => $this->recipients->all(),
            'currentRecipient' => $currentRecipient,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'bkash_number' => ['required', 'string', 'max:30'],
        ]);

        $this->recipients->create($validated);

        return back()->with('status', 'bKash recipient added.');
    }

    public function update(Request $request, IupcBkashRecipient $recipient): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'bkash_number' => ['required', 'string', 'max:30'],
            'rotation_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $this->recipients->update($recipient, $validated);

        return back()->with('status', 'bKash recipient updated.');
    }

    public function activate(IupcBkashRecipient $recipient): RedirectResponse
    {
        $this->recipients->activate($recipient);

        return back()->with('status', 'bKash recipient activated.');
    }

    public function deactivate(IupcBkashRecipient $recipient): RedirectResponse
    {
        $this->recipients->deactivate($recipient);

        return back()->with('status', 'bKash recipient deactivated until 12:15 AM tomorrow.');
    }

    public function current(IupcBkashRecipient $recipient): RedirectResponse
    {
        $this->recipients->setCurrent($recipient);

        return back()->with('status', 'Current bKash recipient changed.');
    }

    public function destroy(IupcBkashRecipient $recipient): RedirectResponse
    {
        $this->recipients->delete($recipient);

        return back()->with('status', 'bKash recipient removed.');
    }
}
