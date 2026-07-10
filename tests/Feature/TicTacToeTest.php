<?php

use App\Enums\TicTacToeGameStatus;
use App\Enums\UserRole;
use App\Models\TicTacToeGame;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can list other admins for tic tac toe invites', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $other = User::factory()->create(['role' => UserRole::Admin, 'name' => 'Other Admin']);

    $this->actingAs($admin)
        ->getJson(route('admin.tic-tac-toe.admins'))
        ->assertOk()
        ->assertJsonPath('admins.0.name', 'Other Admin')
        ->assertJsonMissing(['id' => $admin->id]);
});

test('admin can invite another admin to tic tac toe', function () {
    $inviter = User::factory()->create(['role' => UserRole::Admin]);
    $invitee = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($inviter)
        ->postJson(route('admin.tic-tac-toe.invite'), [
            'opponent_id' => $invitee->id,
        ])
        ->assertCreated()
        ->assertJsonPath('game.status', 'pending')
        ->assertJsonPath('game.player_x.id', $inviter->id)
        ->assertJsonPath('game.player_o.id', $invitee->id);

    expect(TicTacToeGame::count())->toBe(1);
});

test('invitee sees incoming invite in invite feed', function () {
    $inviter = User::factory()->create(['role' => UserRole::Admin]);
    $invitee = User::factory()->create(['role' => UserRole::Admin]);

    TicTacToeGame::create([
        'player_x_id' => $inviter->id,
        'player_o_id' => $invitee->id,
        'board' => TicTacToeGame::emptyBoard(),
        'current_turn' => 'x',
        'status' => TicTacToeGameStatus::Pending,
    ]);

    $this->actingAs($invitee)
        ->getJson(route('admin.tic-tac-toe.invites'))
        ->assertOk()
        ->assertJsonCount(1, 'incoming')
        ->assertJsonPath('incoming.0.opponent.name', $inviter->name);
});

test('invitee can accept a tic tac toe invite', function () {
    $inviter = User::factory()->create(['role' => UserRole::Admin]);
    $invitee = User::factory()->create(['role' => UserRole::Admin]);

    $game = TicTacToeGame::create([
        'player_x_id' => $inviter->id,
        'player_o_id' => $invitee->id,
        'board' => TicTacToeGame::emptyBoard(),
        'current_turn' => 'x',
        'status' => TicTacToeGameStatus::Pending,
    ]);

    $this->actingAs($invitee)
        ->postJson(route('admin.tic-tac-toe.accept', $game))
        ->assertOk()
        ->assertJsonPath('game.status', 'active');

    expect($game->fresh()->status)->toBe(TicTacToeGameStatus::Active);
});

test('invitee can decline a tic tac toe invite', function () {
    $inviter = User::factory()->create(['role' => UserRole::Admin]);
    $invitee = User::factory()->create(['role' => UserRole::Admin]);

    $game = TicTacToeGame::create([
        'player_x_id' => $inviter->id,
        'player_o_id' => $invitee->id,
        'board' => TicTacToeGame::emptyBoard(),
        'current_turn' => 'x',
        'status' => TicTacToeGameStatus::Pending,
    ]);

    $this->actingAs($invitee)
        ->postJson(route('admin.tic-tac-toe.decline', $game))
        ->assertOk();

    expect($game->fresh()->status)->toBe(TicTacToeGameStatus::Declined);
});

test('players can make moves and detect a winner', function () {
    $playerX = User::factory()->create(['role' => UserRole::Admin]);
    $playerO = User::factory()->create(['role' => UserRole::Admin]);

    $game = TicTacToeGame::create([
        'player_x_id' => $playerX->id,
        'player_o_id' => $playerO->id,
        'board' => ['x', 'x', null, 'o', 'o', null, null, null, null],
        'current_turn' => 'x',
        'status' => TicTacToeGameStatus::Active,
    ]);

    $this->actingAs($playerX)
        ->postJson(route('admin.tic-tac-toe.move', $game), ['cell' => 2])
        ->assertOk()
        ->assertJsonPath('game.status', 'won')
        ->assertJsonPath('game.winner_id', $playerX->id);
});

test('non participant cannot access a tic tac toe game', function () {
    $playerX = User::factory()->create(['role' => UserRole::Admin]);
    $playerO = User::factory()->create(['role' => UserRole::Admin]);
    $stranger = User::factory()->create(['role' => UserRole::Admin]);

    $game = TicTacToeGame::create([
        'player_x_id' => $playerX->id,
        'player_o_id' => $playerO->id,
        'board' => TicTacToeGame::emptyBoard(),
        'current_turn' => 'x',
        'status' => TicTacToeGameStatus::Active,
    ]);

    $this->actingAs($stranger)
        ->getJson(route('admin.tic-tac-toe.show', $game))
        ->assertForbidden();
});

test('non admins cannot access tic tac toe api', function () {
    $user = User::factory()->create(['role' => UserRole::User]);

    $this->actingAs($user)
        ->getJson(route('admin.tic-tac-toe.admins'))
        ->assertForbidden();
});

test('admin dashboard shows minigame launcher buttons', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('snake')
        ->assertSee('tic-tac-toe')
        ->assertSee('minesweeper');
});
