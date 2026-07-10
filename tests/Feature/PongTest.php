<?php

use App\Enums\PongGameStatus;
use App\Enums\UserRole;
use App\Models\PongGame;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can invite another admin to pong', function () {
    $inviter = User::factory()->create(['role' => UserRole::Admin]);
    $invitee = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($inviter)
        ->postJson(route('admin.pong.invite'), [
            'opponent_id' => $invitee->id,
        ])
        ->assertCreated()
        ->assertJsonPath('game.status', 'pending')
        ->assertJsonPath('invite.game', 'pong');

    expect(PongGame::count())->toBe(1);
});

test('invitee can accept a pong invite', function () {
    $inviter = User::factory()->create(['role' => UserRole::Admin]);
    $invitee = User::factory()->create(['role' => UserRole::Admin]);

    $game = PongGame::create([
        'player_left_id' => $inviter->id,
        'player_right_id' => $invitee->id,
        'status' => PongGameStatus::Pending,
        ...PongGame::initialAttributes(),
    ]);

    $this->actingAs($invitee)
        ->postJson(route('admin.pong.accept', $game))
        ->assertOk()
        ->assertJsonPath('game.status', 'active');

    expect($game->fresh()->status)->toBe(PongGameStatus::Active);
});

test('players can sync paddle position in pong', function () {
    $playerLeft = User::factory()->create(['role' => UserRole::Admin]);
    $playerRight = User::factory()->create(['role' => UserRole::Admin]);

    $game = PongGame::create([
        'player_left_id' => $playerLeft->id,
        'player_right_id' => $playerRight->id,
        'status' => PongGameStatus::Active,
        ...PongGame::initialAttributes(),
    ]);

    $this->actingAs($playerLeft)
        ->postJson(route('admin.pong.paddle', $game), ['y' => 40])
        ->assertOk()
        ->assertJsonPath('game.left_paddle_y', 40);
});

test('admin dashboard shows pong launcher button', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('pong');
});
