<?php

use App\Enums\UserRole;
use App\Models\AdminChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can access live chat page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('admin.chat.index'))
        ->assertOk()
        ->assertSee('Admin Chat');
});

test('non-admins cannot access admin live chat', function () {
    $user = User::factory()->create(['role' => UserRole::User]);

    $this->actingAs($user)
        ->get(route('admin.chat.index'))
        ->assertForbidden();
});

test('admin can list chat messages', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    AdminChatMessage::factory()->create([
        'user_id' => $admin->id,
        'body' => 'Hello team!',
    ]);

    $this->actingAs($admin)
        ->getJson(route('admin.chat.messages'))
        ->assertOk()
        ->assertJsonPath('messages.0.body', 'Hello team!')
        ->assertJsonPath('messages.0.sender_name', $admin->name);
});

test('admin can send a text message', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->postJson(route('admin.chat.messages.store'), [
            'body' => 'Anyone online?',
        ])
        ->assertCreated()
        ->assertJsonPath('message.body', 'Anyone online?')
        ->assertJsonPath('message.is_mine', true);

    expect(AdminChatMessage::count())->toBe(1);
});

test('admin can send an image message', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->post(route('admin.chat.messages.store'), [
            'image' => UploadedFile::fake()->image('screenshot.jpg'),
        ], ['Accept' => 'application/json'])
        ->assertCreated()
        ->assertJsonPath('message.body', null);

    $message = AdminChatMessage::first();
    expect($message->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($message->image_path);
});

test('admin message requires text or image', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->postJson(route('admin.chat.messages.store'), [])
        ->assertStatus(422);

    expect(AdminChatMessage::count())->toBe(0);
});

test('non-admins cannot access chat api', function () {
    $user = User::factory()->create(['role' => UserRole::User]);

    $this->actingAs($user)
        ->getJson(route('admin.chat.messages'))
        ->assertForbidden();

    $this->actingAs($user)
        ->postJson(route('admin.chat.messages.store'), ['body' => 'nope'])
        ->assertForbidden();
});

test('chat messages can be polled after a given id', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $first = AdminChatMessage::factory()->create(['user_id' => $admin->id, 'body' => 'First']);
    AdminChatMessage::factory()->create(['user_id' => $admin->id, 'body' => 'Second']);

    $this->actingAs($admin)
        ->getJson(route('admin.chat.messages', ['after' => $first->id]))
        ->assertOk()
        ->assertJsonCount(1, 'messages')
        ->assertJsonPath('messages.0.body', 'Second');
});
