<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\FreeBusyRequest;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private ?Calendar $calendar = null;

    public function isConfigured(): bool
    {
        $path = $this->credentialsPath();

        return filled($path) && is_readable($path);
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    public function getBusyPeriods(Carbon $rangeStart, Carbon $rangeEnd): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $service = $this->calendarService();
            $request = new FreeBusyRequest;
            $request->setTimeMin($rangeStart->toRfc3339String());
            $request->setTimeMax($rangeEnd->toRfc3339String());
            $request->setTimeZone($this->timezone());
            $request->setItems([
                ['id' => $this->calendarId()],
            ]);

            $response = $service->freebusy->query($request);
            $calendarBusy = $response->getCalendars()[$this->calendarId()] ?? null;

            if ($calendarBusy === null) {
                return [];
            }

            $periods = [];

            foreach ($calendarBusy->getBusy() ?? [] as $busy) {
                $periods[] = [
                    'start' => Carbon::parse($busy->getStart())->setTimezone($this->timezone()),
                    'end' => Carbon::parse($busy->getEnd())->setTimezone($this->timezone()),
                ];
            }

            return $periods;
        } catch (\Throwable $exception) {
            Log::warning('Google Calendar free/busy lookup failed.', [
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    public function createEvent(Reservation $reservation): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $service = $this->calendarService();
            $event = new Event([
                'summary' => 'Consultation: '.$reservation->name,
                'description' => $this->eventDescription($reservation),
                'start' => [
                    'dateTime' => $reservation->starts_at->setTimezone($this->timezone())->toRfc3339String(),
                    'timeZone' => $this->timezone(),
                ],
                'end' => [
                    'dateTime' => $reservation->ends_at->setTimezone($this->timezone())->toRfc3339String(),
                    'timeZone' => $this->timezone(),
                ],
            ]);

            $created = $service->events->insert($this->calendarId(), $event);

            return $created->getId();
        } catch (\Throwable $exception) {
            Log::warning('Google Calendar event creation failed.', [
                'reservation_id' => $reservation->id,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public function deleteEvent(?string $eventId): void
    {
        if (! $this->isConfigured() || blank($eventId)) {
            return;
        }

        try {
            $this->calendarService()->events->delete($this->calendarId(), $eventId);
        } catch (\Throwable $exception) {
            Log::warning('Google Calendar event deletion failed.', [
                'event_id' => $eventId,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function calendarService(): Calendar
    {
        if ($this->calendar !== null) {
            return $this->calendar;
        }

        $credentialsPath = $this->credentialsPath();

        if (blank($credentialsPath) || ! is_readable($credentialsPath)) {
            throw new \RuntimeException(
                'Google Calendar credentials file is missing or unreadable. '
                .'Set GOOGLE_CALENDAR_CREDENTIALS in your .env file.'
            );
        }

        $client = new GoogleClient;
        $client->setAuthConfig($credentialsPath);
        $client->setScopes([Calendar::CALENDAR]);

        return $this->calendar = new Calendar($client);
    }

    private function credentialsPath(): ?string
    {
        $path = config('services.google_calendar.credentials');

        if (blank($path)) {
            return null;
        }

        if (! str_starts_with($path, '/') && ! preg_match('/^[A-Za-z]:[\\\\\/]/', $path)) {
            $path = base_path($path);
        }

        return $path;
    }

    private function calendarId(): string
    {
        return (string) config('services.google_calendar.calendar_id', 'primary');
    }

    private function timezone(): string
    {
        return (string) config('services.google_calendar.timezone', config('app.timezone', 'UTC'));
    }

    private function eventDescription(Reservation $reservation): string
    {
        $lines = [
            'Email: '.$reservation->email,
        ];

        if ($reservation->notes) {
            $lines[] = '';
            $lines[] = $reservation->notes;
        }

        return implode("\n", $lines);
    }
}
