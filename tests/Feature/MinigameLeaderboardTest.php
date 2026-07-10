<?php

use App\Enums\Minigame;
use App\Enums\UserRole;
use App\Models\AdminMinigameScore;
use App\Models\User;
use App\Services\MinigameLeaderboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view minigame leaderboard', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->getJson(route('admin.leaderboard.index', ['game' => 'snake']))
        ->assertOk()
        ->assertJsonPath('score_label', 'Points');
});

test('snake score only updates when player beats their record', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $service = app(MinigameLeaderboardService::class);

    $service->recordSnakeScore($admin, 50);
    $service->recordSnakeScore($admin, 30);

    expect(AdminMinigameScore::query()->where('user_id', $admin->id)->value('score'))->toBe(50);

    $service->recordSnakeScore($admin, 80);

    expect(AdminMinigameScore::query()->where('user_id', $admin->id)->value('score'))->toBe(80);
});

test('each admin appears once per game on the leaderboard', function () {
    $first = User::factory()->create(['role' => UserRole::Admin, 'name' => 'Alpha']);
    $second = User::factory()->create(['role' => UserRole::Admin, 'name' => 'Beta']);
    $service = app(MinigameLeaderboardService::class);

    $service->recordWin($first, Minigame::Pong);
    $service->recordWin($first, Minigame::Pong);
    $service->recordWin($second, Minigame::Pong);

    $entries = $service->leaderboard(Minigame::Pong);

    expect($entries)->toHaveCount(2)
        ->and($entries->firstWhere('user_id', $first->id)['score'])->toBe(2)
        ->and($entries->firstWhere('user_id', $second->id)['score'])->toBe(1);
});

test('admin can submit snake score via api', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->postJson(route('admin.leaderboard.store'), [
            'game' => 'snake',
            'score' => 120,
        ])
        ->assertOk()
        ->assertJsonPath('entry.score', 120);

    $this->actingAs($admin)
        ->postJson(route('admin.leaderboard.store'), [
            'game' => 'snake',
            'score' => 90,
        ])
        ->assertOk()
        ->assertJsonPath('entry.score', 120);
});

test('admin dashboard shows minigame leaderboard', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Minigame Leaderboard');
});

test('tic tac toe win records a leaderboard win for the winner', function () {
    $playerX = User::factory()->create(['role' => UserRole::Admin]);
    $playerO = User::factory()->create(['role' => UserRole::Admin]);

    $game = \App\Models\TicTacToeGame::create([
        'player_x_id' => $playerX->id,
        'player_o_id' => $playerO->id,
        'board' => ['x', 'x', null, 'o', 'o', null, null, null, null],
        'current_turn' => 'x',
        'status' => \App\Enums\TicTacToeGameStatus::Active,
    ]);

    $this->actingAs($playerX)
        ->postJson(route('admin.tic-tac-toe.move', $game), ['cell' => 2])
        ->assertOk();

    expect(AdminMinigameScore::query()
        ->where('user_id', $playerX->id)
        ->where('game', Minigame::TicTacToe)
        ->value('score'))->toBe(1);
});
