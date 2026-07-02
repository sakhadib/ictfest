<?php

namespace App\Services;

use App\Models\IupcBkashRecipient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IupcBkashRecipientService
{
    public function all(): Collection
    {
        return IupcBkashRecipient::query()
            ->orderBy('rotation_order')
            ->orderBy('id')
            ->get();
    }

    public function current(): ?IupcBkashRecipient
    {
        $this->reactivateDue();
        $this->rotateIfDue();

        $current = IupcBkashRecipient::query()
            ->where('is_enabled', true)
            ->where('is_current', true)
            ->first();

        if ($current) {
            return $current;
        }

        return $this->selectFirstEnabled();
    }

    public function create(array $data): IupcBkashRecipient
    {
        return DB::transaction(function () use ($data): IupcBkashRecipient {
            $recipient = IupcBkashRecipient::query()->create([
                'recipient_name' => $data['recipient_name'],
                'bkash_number' => $this->normalizeNumber($data['bkash_number']),
                'is_enabled' => true,
                'rotation_order' => ((int) IupcBkashRecipient::query()->max('rotation_order')) + 1,
            ]);

            if (! IupcBkashRecipient::query()->where('is_current', true)->exists()) {
                $this->setCurrent($recipient);
            }

            return $recipient;
        });
    }

    public function update(IupcBkashRecipient $recipient, array $data): IupcBkashRecipient
    {
        $recipient->update([
            'recipient_name' => $data['recipient_name'],
            'bkash_number' => $this->normalizeNumber($data['bkash_number']),
            'rotation_order' => $data['rotation_order'] ?? $recipient->rotation_order,
        ]);

        return $recipient->refresh();
    }

    public function activate(IupcBkashRecipient $recipient): void
    {
        DB::transaction(function () use ($recipient): void {
            $recipient->update([
                'is_enabled' => true,
                'deactivated_at' => null,
                'reactivate_at' => null,
            ]);

            if (! IupcBkashRecipient::query()->where('is_current', true)->exists()) {
                $this->setCurrent($recipient);
            }
        });
    }

    public function deactivate(IupcBkashRecipient $recipient): void
    {
        DB::transaction(function () use ($recipient): void {
            $wasCurrent = $recipient->is_current;

            $recipient->update([
                'is_enabled' => false,
                'is_current' => false,
                'current_lock' => null,
                'deactivated_at' => now(),
                'reactivate_at' => $this->nextBangladeshReactivationAt(),
            ]);

            if ($wasCurrent) {
                $this->selectNextAfter($recipient);
            }
        });
    }

    public function delete(IupcBkashRecipient $recipient): void
    {
        DB::transaction(function () use ($recipient): void {
            $wasCurrent = $recipient->is_current;
            $previous = clone $recipient;

            $recipient->delete();

            if ($wasCurrent) {
                $this->selectNextAfter($previous);
            }
        });
    }

    public function setCurrent(IupcBkashRecipient $recipient): void
    {
        if (! $recipient->is_enabled) {
            throw ValidationException::withMessages([
                'recipient' => 'Only an active bKash number can be selected as current.',
            ]);
        }

        DB::transaction(function () use ($recipient): void {
            IupcBkashRecipient::query()->update([
                'is_current' => false,
                'current_lock' => null,
            ]);

            $recipient->update([
                'is_current' => true,
                'current_lock' => 'current',
                'last_selected_at' => now(),
            ]);
        });
    }

    public function rotate(): ?IupcBkashRecipient
    {
        return DB::transaction(function (): ?IupcBkashRecipient {
            $current = IupcBkashRecipient::query()
                ->where('is_enabled', true)
                ->where('is_current', true)
                ->lockForUpdate()
                ->first();

            if (! $current) {
                return $this->selectFirstEnabled();
            }

            return $this->selectNextAfter($current) ?? $current->refresh();
        });
    }

    public function rotateIfDue(): ?IupcBkashRecipient
    {
        $current = IupcBkashRecipient::query()
            ->where('is_enabled', true)
            ->where('is_current', true)
            ->first();

        if (! $current) {
            return $this->selectFirstEnabled();
        }

        if (! $current->last_selected_at || $current->last_selected_at->lt(now()->startOfHour())) {
            return $this->rotate();
        }

        return $current;
    }

    public function reactivateDue(): int
    {
        return DB::transaction(function (): int {
            $this->repairLegacyUtcReactivationTimes();

            $count = IupcBkashRecipient::query()
                ->where('is_enabled', false)
                ->whereNotNull('reactivate_at')
                ->where('reactivate_at', '<=', now())
                ->update([
                    'is_enabled' => true,
                    'deactivated_at' => null,
                    'reactivate_at' => null,
                    'updated_at' => now(),
                ]);

            if (! IupcBkashRecipient::query()->where('is_current', true)->exists()) {
                $this->selectFirstEnabled();
            }

            return $count;
        });
    }

    public function normalizeNumber(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($digits, '880')) {
            $digits = '0'.substr($digits, 3);
        }

        if (! preg_match('/^01[3-9]\d{8}$/', $digits)) {
            throw ValidationException::withMessages([
                'bkash_number' => 'Enter a valid Bangladesh mobile number, for example 017XXXXXXXX.',
            ]);
        }

        return $digits;
    }

    private function nextBangladeshReactivationAt(): \Carbon\CarbonInterface
    {
        return now(config('app.timezone'))->addDay()->startOfDay()->addMinutes(15);
    }

    private function repairLegacyUtcReactivationTimes(): void
    {
        IupcBkashRecipient::query()
            ->where('is_enabled', false)
            ->whereNotNull('reactivate_at')
            ->get()
            ->each(function (IupcBkashRecipient $recipient): void {
                if ($recipient->reactivate_at?->format('H:i') !== '18:15') {
                    return;
                }

                $recipient->update([
                    'reactivate_at' => $recipient->reactivate_at->copy()->addHours(6),
                ]);
            });
    }

    private function selectFirstEnabled(): ?IupcBkashRecipient
    {
        $recipient = IupcBkashRecipient::query()
            ->where('is_enabled', true)
            ->orderBy('rotation_order')
            ->orderBy('id')
            ->first();

        if ($recipient) {
            $this->setCurrent($recipient);
        }

        return $recipient;
    }

    private function selectNextAfter(IupcBkashRecipient $current): ?IupcBkashRecipient
    {
        $next = IupcBkashRecipient::query()
            ->where('is_enabled', true)
            ->where(function ($query) use ($current): void {
                $query->where('rotation_order', '>', $current->rotation_order)
                    ->orWhere(function ($query) use ($current): void {
                        $query->where('rotation_order', $current->rotation_order)
                            ->where('id', '>', $current->id);
                    });
            })
            ->orderBy('rotation_order')
            ->orderBy('id')
            ->first();

        $next ??= IupcBkashRecipient::query()
            ->where('is_enabled', true)
            ->orderBy('rotation_order')
            ->orderBy('id')
            ->first();

        if ($next) {
            $this->setCurrent($next);
        }

        return $next;
    }
}
