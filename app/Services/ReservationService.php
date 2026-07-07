<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReservationService
{
    public function __construct(
        private GoogleCalendarService $googleCalendar,
    ) {}

    /**
     * @return array<int, array{starts_at: string, ends_at: string, label: string}>
     */
    public function getAvailableSlots(Carbon $date): array
    {
        $date = $date->copy()->setTimezone($this->timezone())->startOfDay();

        if (! $this->isBookableDate($date)) {
            return [];
        }

        $slots = $this->generateSlotsForDate($date);

        if ($slots->isEmpty()) {
            return [];
        }

        $rangeStart = $slots->first()['start']->copy();
        $rangeEnd = $slots->last()['end']->copy();

        $busyPeriods = array_merge(
            $this->getDatabaseBusyPeriods($rangeStart, $rangeEnd),
            $this->googleCalendar->getBusyPeriods($rangeStart, $rangeEnd),
        );

        return $slots
            ->reject(fn (array $slot) => $this->slotIsBusy($slot['start'], $slot['end'], $busyPeriods))
            ->reject(fn (array $slot) => $slot['start']->isPast())
            ->map(fn (array $slot) => [
                'starts_at' => $slot['start']->toIso8601String(),
                'ends_at' => $slot['end']->toIso8601String(),
                'label' => $slot['start']->format('g:i A').' – '.$slot['end']->format('g:i A'),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array{name: string, email: string, starts_at: string, notes?: ?string, user?: ?User}  $data
     */
    public function create(array $data): Reservation
    {
        $startsAt = Carbon::parse($data['starts_at'])->setTimezone($this->timezone())->seconds(0);
        $endsAt = $startsAt->copy()->addMinutes($this->slotDurationMinutes());

        $this->assertSlotIsBookable($startsAt, $endsAt);

        return DB::transaction(function () use ($data, $startsAt, $endsAt) {
            $reservation = Reservation::query()->create([
                'user_id' => $data['user']?->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'notes' => $data['notes'] ?? null,
                'status' => ReservationStatus::Confirmed,
            ]);

            $eventId = $this->googleCalendar->createEvent($reservation);

            if ($eventId) {
                $reservation->update(['google_event_id' => $eventId]);
            }

            return $reservation->fresh();
        });
    }

    public function cancel(Reservation $reservation): void
    {
        if ($reservation->status === ReservationStatus::Cancelled) {
            return;
        }

        DB::transaction(function () use ($reservation) {
            $this->googleCalendar->deleteEvent($reservation->google_event_id);

            $reservation->update([
                'status' => ReservationStatus::Cancelled,
            ]);
        });
    }

    public function syncToGoogleCalendar(Reservation $reservation): bool
    {
        if (! $this->googleCalendar->isConfigured() || $reservation->status !== ReservationStatus::Confirmed) {
            return false;
        }

        if (filled($reservation->google_event_id)) {
            return true;
        }

        $eventId = $this->googleCalendar->createEvent($reservation);

        if (blank($eventId)) {
            return false;
        }

        $reservation->update(['google_event_id' => $eventId]);

        return true;
    }

    private function assertSlotIsBookable(Carbon $startsAt, Carbon $endsAt): void
    {
        if (! $this->isBookableDate($startsAt)) {
            throw new InvalidArgumentException('This date is not available for booking.');
        }

        if ($startsAt->isPast()) {
            throw new InvalidArgumentException('This time slot is no longer available.');
        }

        $slots = $this->generateSlotsForDate($startsAt->copy()->startOfDay());
        $matchingSlot = $slots->first(
            fn (array $slot) => $slot['start']->timestamp === $startsAt->timestamp
                && $slot['end']->timestamp === $endsAt->timestamp,
        );

        if ($matchingSlot === null) {
            throw new InvalidArgumentException('The selected time is outside business hours.');
        }

        $busyPeriods = array_merge(
            $this->getDatabaseBusyPeriods($startsAt, $endsAt),
            $this->googleCalendar->getBusyPeriods($startsAt, $endsAt),
        );

        if ($this->slotIsBusy($startsAt, $endsAt, $busyPeriods)) {
            throw new InvalidArgumentException('This time slot has already been booked.');
        }
    }

    private function isBookableDate(Carbon $date): bool
    {
        $today = now($this->timezone())->startOfDay();
        $maxDate = $today->copy()->addDays($this->advanceDays());

        if ($date->lt($today) || $date->gt($maxDate)) {
            return false;
        }

        return in_array($date->dayOfWeekIso, $this->workingDays(), true);
    }

    /**
     * @return Collection<int, array{start: Carbon, end: Carbon}>
     */
    private function generateSlotsForDate(Carbon $date): Collection
    {
        if (! $this->isBookableDate($date)) {
            return collect();
        }

        $slots = collect();
        $slotStart = $date->copy()->setTime($this->startHour(), 0);
        $dayEnd = $date->copy()->setTime($this->endHour(), 0);

        while ($slotStart->copy()->addMinutes($this->slotDurationMinutes())->lte($dayEnd)) {
            $slotEnd = $slotStart->copy()->addMinutes($this->slotDurationMinutes());

            $slots->push([
                'start' => $slotStart->copy(),
                'end' => $slotEnd->copy(),
            ]);

            $slotStart->addMinutes($this->slotDurationMinutes());
        }

        return $slots;
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    private function getDatabaseBusyPeriods(Carbon $rangeStart, Carbon $rangeEnd): array
    {
        return Reservation::query()
            ->where('status', ReservationStatus::Confirmed)
            ->where('starts_at', '<', $rangeEnd)
            ->where('ends_at', '>', $rangeStart)
            ->get(['starts_at', 'ends_at'])
            ->map(fn (Reservation $reservation) => [
                'start' => $reservation->starts_at->copy()->setTimezone($this->timezone()),
                'end' => $reservation->ends_at->copy()->setTimezone($this->timezone()),
            ])
            ->all();
    }

    /**
     * @param  array<int, array{start: Carbon, end: Carbon}>  $busyPeriods
     */
    private function slotIsBusy(Carbon $start, Carbon $end, array $busyPeriods): bool
    {
        foreach ($busyPeriods as $busy) {
            if ($start < $busy['end'] && $end > $busy['start']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, int>
     */
    private function workingDays(): array
    {
        return config('services.google_calendar.working_days', [1, 2, 3, 4, 5]);
    }

    private function startHour(): int
    {
        return (int) config('services.google_calendar.start_hour', 9);
    }

    private function endHour(): int
    {
        return (int) config('services.google_calendar.end_hour', 17);
    }

    private function slotDurationMinutes(): int
    {
        return (int) config('services.google_calendar.slot_duration_minutes', 60);
    }

    private function advanceDays(): int
    {
        return (int) config('services.google_calendar.advance_days', 30);
    }

    private function timezone(): string
    {
        return (string) config('services.google_calendar.timezone', config('app.timezone', 'UTC'));
    }
}
