<?php

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'services.google_calendar.timezone' => 'UTC',
        'services.google_calendar.working_days' => [1, 2, 3, 4, 5],
        'services.google_calendar.start_hour' => 9,
        'services.google_calendar.end_hour' => 17,
        'services.google_calendar.slot_duration_minutes' => 60,
        'services.google_calendar.advance_days' => 30,
    ]);

    $this->mock(GoogleCalendarService::class, function ($mock) {
        $mock->shouldReceive('isConfigured')->andReturn(false);
        $mock->shouldReceive('getBusyPeriods')->andReturn([]);
        $mock->shouldReceive('createEvent')->andReturn(null);
        $mock->shouldReceive('deleteEvent')->andReturnNull();
    });
});

function nextWeekdayAt(int $hour = 10): Carbon
{
    $date = now('UTC')->addDay();

    while (! in_array($date->dayOfWeekIso, [1, 2, 3, 4, 5], true)) {
        $date->addDay();
    }

    return $date->setTime($hour, 0)->seconds(0);
}

test('reservations page is publicly accessible', function () {
    $this->get(route('reservations.index'))
        ->assertOk()
        ->assertSee('Book a')
        ->assertSee('SCHEDULE_SESSION');
});

test('slots endpoint returns weekday availability', function () {
    $date = nextWeekdayAt();

    $response = $this->getJson(route('reservations.slots', ['date' => $date->toDateString()]));

    $response->assertOk()
        ->assertJsonStructure([
            'date',
            'slots' => [
                '*' => ['starts_at', 'ends_at', 'label'],
            ],
        ]);

    expect($response->json('slots'))->not->toBeEmpty();
});

test('slots endpoint returns empty array for weekends', function () {
    $saturday = now('UTC')->next(Carbon::SATURDAY);

    $this->getJson(route('reservations.slots', ['date' => $saturday->toDateString()]))
        ->assertOk()
        ->assertJson(['slots' => []]);
});

test('guests can create a reservation', function () {
    $startsAt = nextWeekdayAt(10);

    $this->post(route('reservations.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'starts_at' => $startsAt->toIso8601String(),
        'notes' => 'Need architecture review',
    ])
        ->assertRedirect(route('reservations.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reservations', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'status' => ReservationStatus::Confirmed->value,
    ]);
});

test('authenticated users book with their account details', function () {
    $user = User::factory()->create([
        'name' => 'Logged In User',
        'email' => 'logged-in@example.com',
    ]);
    $startsAt = nextWeekdayAt(11);

    $this->actingAs($user)
        ->post(route('reservations.store'), [
            'name' => 'Ignored Name',
            'email' => 'ignored@example.com',
            'starts_at' => $startsAt->toIso8601String(),
        ])
        ->assertRedirect(route('reservations.index'));

    $this->assertDatabaseHas('reservations', [
        'user_id' => $user->id,
        'name' => 'Logged In User',
        'email' => 'logged-in@example.com',
    ]);
});

test('double booking the same slot is rejected', function () {
    $startsAt = nextWeekdayAt(14);

    Reservation::query()->create([
        'name' => 'Existing Client',
        'email' => 'existing@example.com',
        'starts_at' => $startsAt,
        'ends_at' => $startsAt->copy()->addHour(),
        'status' => ReservationStatus::Confirmed,
    ]);

    $this->post(route('reservations.store'), [
        'name' => 'Another Client',
        'email' => 'another@example.com',
        'starts_at' => $startsAt->toIso8601String(),
    ])
        ->assertRedirect()
        ->assertSessionHasErrors('starts_at');
});

test('users can cancel their own upcoming reservations', function () {
    $user = User::factory()->create();
    $startsAt = nextWeekdayAt(15);

    $reservation = Reservation::query()->create([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'starts_at' => $startsAt,
        'ends_at' => $startsAt->copy()->addHour(),
        'status' => ReservationStatus::Confirmed,
    ]);

    $this->actingAs($user)
        ->delete(route('reservations.destroy', $reservation))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($reservation->fresh()->status)->toBe(ReservationStatus::Cancelled);
});

test('users cannot cancel another users reservation', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $startsAt = nextWeekdayAt(16);

    $reservation = Reservation::query()->create([
        'user_id' => $owner->id,
        'name' => $owner->name,
        'email' => $owner->email,
        'starts_at' => $startsAt,
        'ends_at' => $startsAt->copy()->addHour(),
        'status' => ReservationStatus::Confirmed,
    ]);

    $this->actingAs($other)
        ->delete(route('reservations.destroy', $reservation))
        ->assertForbidden();
});
