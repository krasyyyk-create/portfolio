<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\GoogleCalendarService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class ReservationController extends Controller
{
    public function index(): View
    {
        return view('reservations.index', [
            'timezone' => config('services.google_calendar.timezone', config('app.timezone', 'UTC')),
            'slotDurationMinutes' => (int) config('services.google_calendar.slot_duration_minutes', 60),
            'googleCalendarConnected' => app(GoogleCalendarService::class)->isConfigured(),
        ]);
    }

    public function slots(Request $request, ReservationService $reservations): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(
                (int) config('services.google_calendar.advance_days', 30)
            )->toDateString()],
        ]);

        $date = Carbon::parse($validated['date'], config('services.google_calendar.timezone', config('app.timezone', 'UTC')));

        return response()->json([
            'date' => $date->toDateString(),
            'slots' => $reservations->getAvailableSlots($date),
        ]);
    }

    public function store(Request $request, ReservationService $reservations): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'starts_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($user = $request->user()) {
            $validated['name'] = $user->name;
            $validated['email'] = $user->email;
        }

        try {
            $reservation = $reservations->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'starts_at' => $validated['starts_at'],
                'notes' => $validated['notes'] ?? null,
                'user' => $request->user(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput($request->only(['name', 'email', 'starts_at', 'notes']))
                ->withErrors(['starts_at' => $exception->getMessage()]);
        }

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Your '.$reservation->starts_at->timezone(config('services.google_calendar.timezone', config('app.timezone')))->format('M j, Y \a\t g:i A').' consultation has been booked.');
    }

    public function destroy(Reservation $reservation, ReservationService $reservations): RedirectResponse
    {
        $user = auth()->user();

        if ($reservation->user_id !== $user?->id && ! $user?->isAdmin()) {
            abort(403);
        }

        if ($reservation->starts_at->isPast()) {
            return back()->withErrors(['reservation' => 'Past reservations cannot be cancelled.']);
        }

        $reservations->cancel($reservation);

        return back()->with('success', 'Your reservation has been cancelled.');
    }
}
