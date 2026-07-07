<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Illuminate\Console\Command;

class GoogleCalendarTokenCommand extends Command
{
    protected $signature = 'google:calendar-token {--code= : Authorization code from Google OAuth redirect}';

    protected $description = 'Generate a Google Calendar refresh token for GOOGLE_CALENDAR_REFRESH_TOKEN';

    public function handle(): int
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (blank($clientId) || blank($clientSecret)) {
            $this->error('Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in your .env file first.');

            return self::FAILURE;
        }

        $client = new GoogleClient;
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([Calendar::CALENDAR]);

        $code = $this->option('code');

        if (blank($code)) {
            $this->line('Open this URL in your browser and authorize calendar access:');
            $this->newLine();
            $this->info($client->createAuthUrl());
            $this->newLine();
            $this->line('After approving, Google redirects to your callback URL with a `code` query parameter.');
            $this->line('Run again with that code:');
            $this->comment('php artisan google:calendar-token --code="PASTE_CODE_HERE"');

            return self::SUCCESS;
        }

        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            $this->error($token['error_description'] ?? $token['error']);

            return self::FAILURE;
        }

        if (blank($token['refresh_token'] ?? null)) {
            $this->error('No refresh token was returned. Revoke app access in your Google account and run this command again.');
            $this->line('Google Account → Security → Third-party access → remove this app, then retry.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Add this to your .env file:');
        $this->newLine();
        $this->line('GOOGLE_CALENDAR_REFRESH_TOKEN='.$token['refresh_token']);
        $this->newLine();
        $this->comment('Then run: php artisan config:clear');

        return self::SUCCESS;
    }
}
