<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Collection;

class RegistrationCardService
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->report($this->baseQuery()->get(), 'All Events Registration Cards');
    }

    /**
     * @return array<string, mixed>
     */
    public function event(string $eventCode): array
    {
        $eventCode = str_pad($eventCode, 2, '0', STR_PAD_LEFT);
        $event = Event::where('code', $eventCode)->first();

        if (! $event) {
            return [
                'ok' => false,
                'message' => 'Invalid event code. Use 01, 02, 03, 04, 05, or 06.',
            ];
        }

        return $this->report(
            $this->baseQuery()
                ->whereHas('event', fn ($query) => $query->where('code', $eventCode))
                ->get(),
            $event->code.' - '.$event->name.' Registration Cards',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function team(string $registrationCode): array
    {
        $registration = $this->baseQuery()
            ->where('registration_code', strtoupper($registrationCode))
            ->first();

        if (! $registration) {
            return [
                'ok' => false,
                'message' => 'No registration found for code '.strtoupper($registrationCode).'.',
            ];
        }

        return $this->report(
            collect([$registration]),
            $registration->registration_code.' Registration Card',
        );
    }

    /**
     * @return Collection<int, Event>
     */
    public function eventsWithRegistrations(): Collection
    {
        return Event::query()
            ->whereHas('registrations')
            ->orderBy('code')
            ->get();
    }

    private function baseQuery()
    {
        return Registration::query()
            ->with([
                'event',
                'payment',
                'coach',
                'finalRegistration',
                'participants' => fn ($query) => $query->orderByDesc('is_leader')->orderBy('id'),
            ])
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->select('registrations.*')
            ->orderBy('events.code')
            ->orderBy('registrations.team_name')
            ->orderBy('registrations.registration_code');
    }

    /**
     * @param Collection<int, Registration> $registrations
     * @return array<string, mixed>
     */
    private function report(Collection $registrations, string $title): array
    {
        if ($registrations->isEmpty()) {
            return [
                'ok' => false,
                'message' => 'No registrations found for this registration card request.',
            ];
        }

        return [
            'ok' => true,
            'title' => $title,
            'generated_at' => now()->format('d M Y, h:i A'),
            'assets' => [
                'ictfest_logo' => $this->pdfLogoDataUri(public_path('assets/logo_black.jpg'), 'ICT FEST', '2026'),
                'iutcs_logo' => $this->pdfLogoDataUri(public_path('assets/iutcs.jpg'), 'IUTCS'),
                'cse_logo' => $this->pdfLogoDataUri(public_path('assets/cse.jpg'), 'CSE'),
            ],
            'registrations' => $registrations,
        ];
    }

    private function pdfLogoDataUri(string $path, string $label, ?string $subLabel = null): ?string
    {
        if (is_file($path) && in_array(mime_content_type($path), ['image/jpeg', 'image/jpg'], true)) {
            return $this->imageDataUri($path);
        }

        return $this->svgLogoDataUri($label, $subLabel);
    }

    private function imageDataUri(string $path): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';
        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }

    private function svgLogoDataUri(string $label, ?string $subLabel = null): string
    {
        $subLabel ??= '';

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="240" height="120" viewBox="0 0 240 120">'
            .'<rect width="240" height="120" rx="18" fill="#ffffff"/>'
            .'<rect x="8" y="8" width="224" height="104" rx="15" fill="none" stroke="#d4574e" stroke-width="5"/>'
            .'<text x="120" y="'.($subLabel ? '58' : '70').'" text-anchor="middle" font-family="DejaVu Sans, Arial, sans-serif" font-size="34" font-weight="700" fill="#111827">'.e($label).'</text>'
            .($subLabel ? '<text x="120" y="86" text-anchor="middle" font-family="DejaVu Sans, Arial, sans-serif" font-size="22" font-weight="700" fill="#d4574e">'.e($subLabel).'</text>' : '')
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
