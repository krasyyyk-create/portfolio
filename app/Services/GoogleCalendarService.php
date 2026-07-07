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
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google_calendar.refresh_token'));
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

        $refreshToken = (string) config('services.google_calendar.refresh_token');

        if (str_starts_with($refreshToken, 'ya29.')) {
            throw new \RuntimeException(
                'GOOGLE_CALENDAR_REFRESH_TOKEN contains an access token (ya29.*). '
                .'Run `php artisan google:calendar-token` to obtain a refresh token instead.'
            );
        }

        $client = new GoogleClient;
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([Calendar::CALENDAR]);

        $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);

        if (! is_array($token) || isset($token['error'])) {
            $message = is_array($token)
                ? ($token['error_description'] ?? $token['error'] ?? 'Unknown OAuth error')
                : 'Google OAuth did not return an access token.';

            throw new \RuntimeException(
                'Google Calendar authentication failed: '.$message
                .'. Run `php artisan google:calendar-token` to generate a new refresh token.'
            );
        }

        $client->setAccessToken($token);

        return $this->calendar = new Calendar($client);
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
