<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class RacesController extends Controller
{
    public function index(): View
    {
        $season = request()->integer('season', (int) date('Y'));
        $type = request()->string('type', 'Race')->toString();

        $apiKey = config('services.api_sports.key');
        $baseUrl = rtrim(config('services.api_sports.base_url'), '/');

        $error = null;
        $races = [];
        $meta = [
            'season' => $season,
            'type' => $type,
            'results' => 0,
        ];

        if (blank($apiKey)) {
            $error = 'API_SPORTS_KEY is not configured. Add it to your .env file.';
        } else {
            try {
                $response = Http::withHeaders([
                    'x-apisports-key' => $apiKey,
                ])
                    ->timeout(15)
                    ->get("{$baseUrl}/races", [
                        'season' => $season,
                        'type' => $type,
                    ]);

                if ($response->failed()) {
                    $error = 'The Formula 1 API returned an error (HTTP '.$response->status().').';
                } else {
                    $payload = $response->json();

                    if (! empty($payload['errors'])) {
                        $error = collect($payload['errors'])->flatten()->implode(' ');
                    } else {
                        $races = $payload['response'] ?? [];
                        $meta['results'] = $payload['results'] ?? count($races);
                    }
                }
            } catch (ConnectionException) {
                $error = 'Could not reach the Formula 1 API. Check your network connection and try again.';
            }
        }

        return view('races', compact('races', 'error', 'meta'));
    }
}
