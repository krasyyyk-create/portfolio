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
        return $this->resolveCredentials() !== null;
    }

    public function serviceAccountEmail(): ?string
    {
        $credentials = $this->resolveCredentials();

        if (is_array($credentials)) {
            return $credentials['client_email'] ?? null;
        }

        if (! is_string($credentials) || ! is_readable($credentials)) {
            return null;
        }

        $json = json_decode((string) file_get_contents($credentials), true);

        return is_array($json) ? ($json['client_email'] ?? null) : null;
    }

    public function calendarLabel(): string
    {
        if (! $this->isConfigured()) {
            return 'not configured';
        }

        try {
            $calendar = $this->calendarService()->calendars->get($this->calendarId());

            return (string) ($calendar->getSummary() ?: $this->calendarId());
        } catch (\Throwable) {
            return $this->calendarId();
        }
    }

    /**
     * @return array{ok: bool, message: string, calendar?: string, service_account?: string}
     */
    public function verify(): array
    {
        if (! $this->isConfigured()) {
            return [
                'ok' => false,
                'message' => 'Google Calendar credentials are missing. Set GOOGLE_CALENDAR_CREDENTIALS to a service-account JSON file, or GOOGLE_CALENDAR_CREDENTIALS_JSON for production deployments.',
            ];
        }

        $serviceAccount = $this->serviceAccountEmail();
        $calendarId = $this->calendarId();

        try {
            $reservation = new Reservation([
                'name' => 'Calendar Connection Test',
                'email' => 'calendar-test@example.com',
                'starts_at' => now($this->timezone())->addWeek()->startOfHour(),
                'ends_at' => now($this->timezone())->addWeek()->startOfHour()->addHour(),
                'notes' => 'Automatic connectivity test — safe to delete.',
            ]);
            $reservation->id = 0;

            $eventId = $this->createEvent($reservation);

            if (blank($eventId)) {
                return [
                    'ok' => false,
                    'message' => 'Credentials loaded, but Google rejected event creation. Share your calendar with the service account and confirm GOOGLE_CALENDAR_ID is correct.',
                    'calendar' => $calendarId,
                    'service_account' => $serviceAccount,
                ];
            }

            $this->deleteEvent($eventId);
            $calendarLabel = $this->calendarLabel();

            $message = "Successfully created a test event on \"{$calendarLabel}\".";

            if (str_contains($calendarId, '@group.calendar.google.com')) {
                $message .= ' Bookings are saved to this separate calendar — enable it under "My calendars" in Google Calendar, or point GOOGLE_CALENDAR_ID at your personal Gmail after sharing it with the service account.';
            }

            return [
                'ok' => true,
                'message' => $message,
                'calendar' => $calendarLabel,
                'service_account' => $serviceAccount,
            ];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'message' => $exception->getMessage(),
                'calendar' => $calendarId,
                'service_account' => $serviceAccount,
            ];
        }
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

        $credentials = $this->resolveCredentials();

        if ($credentials === null) {
            throw new \RuntimeException(
                'Google Calendar credentials are missing. '
                .'Set GOOGLE_CALENDAR_CREDENTIALS or GOOGLE_CALENDAR_CREDENTIALS_JSON in your .env file.'
            );
        }

        $client = new GoogleClient;
        $client->setAuthConfig($credentials);
        $client->setScopes([Calendar::CALENDAR]);

        return $this->calendar = new Calendar($client);
    }

    /**
     * @return array<string, mixed>|string|null
     */
    private function resolveCredentials(): array|string|null
    {
        $json = config('services.google_calendar.credentials_json');

        if (filled($json)) {
            $decoded = json_decode($json, true);

            return is_array($decoded) ? $decoded : null;
        }

        $path = config('services.google_calendar.credentials');

        if (blank($path)) {
            return null;
        }

        if (! str_starts_with($path, '/') && ! preg_match('/^[A-Za-z]:[\\\\\/]/', $path)) {
            $path = base_path($path);
        }

        return is_readable($path) ? $path : null;
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
