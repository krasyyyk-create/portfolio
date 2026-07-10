<?php

namespace App\Console\Commands;

use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;

class VerifyGoogleCalendarCommand extends Command
{
    protected $signature = 'google-calendar:verify';

    protected $description = 'Verify Google Calendar credentials and write access for consultation bookings';

    public function handle(GoogleCalendarService $googleCalendar): int
    {
        $result = $googleCalendar->verify();

        if ($result['ok']) {
            $this->info($result['message']);
            $this->line('Calendar: '.($result['calendar'] ?? 'unknown'));
            $this->line('Service account: '.($result['service_account'] ?? 'unknown'));

            return self::SUCCESS;
        }

        $this->error($result['message']);

        if (filled($result['service_account'] ?? null)) {
            $this->newLine();
            $this->line('Service account: '.$result['service_account']);
            $this->line('Calendar ID: '.($result['calendar'] ?? config('services.google_calendar.calendar_id')));
            $this->newLine();
            $this->line('To write bookings to your personal Google Calendar:');
            $this->line('  1. Open Google Calendar → Settings → select your calendar');
            $this->line('  2. Share it with the service account above');
            $this->line('  3. Grant "Make changes to events"');
            $this->line('  4. Set GOOGLE_CALENDAR_ID='.config('services.contact.email').' in .env');
            $this->line('  5. Run php artisan google-calendar:verify again');
        }

        return self::FAILURE;
    }
}
