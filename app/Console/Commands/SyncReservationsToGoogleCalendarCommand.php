<?php

namespace App\Console\Commands;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Console\Command;

class SyncReservationsToGoogleCalendarCommand extends Command
{
    protected $signature = 'reservations:sync-google-calendar';

    protected $description = 'Create Google Calendar events for confirmed reservations missing a google_event_id';

    public function handle(ReservationService $reservations): int
    {
        $pending = Reservation::query()
            ->where('status', ReservationStatus::Confirmed)
            ->whereNull('google_event_id')
            ->orderBy('starts_at')
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No reservations need Google Calendar sync.');

            return self::SUCCESS;
        }

        $synced = 0;
        $failed = 0;

        foreach ($pending as $reservation) {
            if ($reservations->syncToGoogleCalendar($reservation)) {
                $synced++;
                $this->line("Synced reservation #{$reservation->id} ({$reservation->starts_at})");
            } else {
                $failed++;
                $this->error("Failed to sync reservation #{$reservation->id} ({$reservation->starts_at})");
            }
        }

        $this->newLine();
        $this->info("Synced: {$synced}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
