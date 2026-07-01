<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\SendIupcCoachPortalSms;
use App\Mail\IupcCoachPortalLinkMail;
use App\Models\IupcCoachContact;
use App\Models\IupcCoachLink;
use App\Models\IupcUniversityAlias;
use App\Models\IupcUniversityAllocation;
use App\Rules\StrictEmail;
use App\Services\IupcCoachPortalService;
use App\Services\IupcUniversitySyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IupcSlotController extends Controller
{
    public function __construct(
        private readonly IupcUniversitySyncService $sync,
        private readonly IupcCoachPortalService $portal,
    ) {
    }

    public function index(): View
    {
        $this->sync->sync();

        $allocations = IupcUniversityAllocation::query()
            ->with(['aliases' => fn ($query) => $query->orderBy('raw_name'), 'coaches.links'])
            ->orderBy('name')
            ->get()
            ->map(function (IupcUniversityAllocation $allocation): IupcUniversityAllocation {
                $allocation->setAttribute('submitted_count', $this->portal->submittedCount($allocation));
                $allocation->setAttribute('remaining_count', $this->portal->remainingSlots($allocation));
                $allocation->setAttribute('registration_count', $this->portal->registrationsQuery($allocation)->count());

                return $allocation;
            });

        return view('dashboard.iupc-slots.index', [
            'allocations' => $allocations,
        ]);
    }

    public function updateSlots(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slots' => ['required', 'array'],
            'slots.*' => ['nullable', 'integer', 'min:0', 'max:999'],
            'names' => ['nullable', 'array'],
            'names.*' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['slots'] as $allocationId => $slotCount) {
            $name = trim((string) ($validated['names'][$allocationId] ?? ''));
            $updates = [
                'slot_count' => (int) ($slotCount ?? 0),
            ];

            if ($name !== '') {
                $updates['name'] = $name;
            }

            IupcUniversityAllocation::query()->whereKey($allocationId)->update($updates);
        }

        return back()->with('status', 'IUPC university slots updated.');
    }

    public function moveAlias(Request $request, IupcUniversityAlias $alias): RedirectResponse
    {
        $validated = $request->validate([
            'iupc_university_allocation_id' => ['required', 'exists:iupc_university_allocations,id'],
        ]);

        $alias->update([
            'iupc_university_allocation_id' => $validated['iupc_university_allocation_id'],
        ]);

        $this->sync->sync();

        return back()->with('status', 'University alias moved.');
    }

    public function sendLinks(IupcUniversityAllocation $allocation): RedirectResponse
    {
        $this->sync->sync();

        $coaches = $allocation->coaches()
            ->where('is_active', true)
            ->get();

        $sent = 0;

        foreach ($coaches as $coach) {
            $link = $this->activeOrNewLink($allocation, $coach);
            $url = route('iupc.coach.show', ['token' => Crypt::decryptString($link->token_encrypted)]);

            if (StrictEmail::isValid($coach->official_email)) {
                Mail::to(trim($coach->official_email), $coach->name)
                    ->queue(new IupcCoachPortalLinkMail($link, $url));

                $link->update([
                    'last_email_sent_at' => now(),
                    'last_sent_at' => now(),
                ]);
            } else {
                Log::warning('IUPC coach portal email skipped because email is invalid.', [
                    'coach_id' => $coach->id,
                    'email' => $coach->official_email,
                ]);
            }

            SendIupcCoachPortalSms::dispatch($link->id, $url);
            $sent++;
        }

        return back()->with('status', "Coach links queued for {$sent} coach".($sent === 1 ? '' : 'es').'.');
    }

    public function disableLink(IupcCoachLink $link): RedirectResponse
    {
        $link->update([
            'disabled_at' => now(),
            'disabled_by' => auth()->id(),
        ]);

        return back()->with('status', 'Coach link disabled.');
    }

    public function regenerateLink(IupcCoachLink $link): RedirectResponse
    {
        $token = Str::random(72);

        DB::transaction(function () use ($link, $token): void {
            $link->update([
                'token_hash' => hash('sha256', $token),
                'token_encrypted' => Crypt::encryptString($token),
                'disabled_at' => null,
                'disabled_by' => null,
            ]);
        });

        return back()->with('status', 'Coach link regenerated. Use send again to deliver the new link.');
    }

    private function activeOrNewLink(IupcUniversityAllocation $allocation, IupcCoachContact $coach): IupcCoachLink
    {
        $activeLink = $coach->links()
            ->where('iupc_university_allocation_id', $allocation->id)
            ->whereNull('disabled_at')
            ->latest()
            ->first();

        if ($activeLink) {
            return $activeLink;
        }

        $token = Str::random(72);

        return $coach->links()->create([
            'iupc_university_allocation_id' => $allocation->id,
            'token_hash' => hash('sha256', $token),
            'token_encrypted' => Crypt::encryptString($token),
        ]);
    }
}
