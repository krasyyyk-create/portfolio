<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pong_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_left_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('player_right_id')->constrained('users')->cascadeOnDelete();
            $table->float('left_paddle_y');
            $table->float('right_paddle_y');
            $table->float('ball_x');
            $table->float('ball_y');
            $table->float('ball_vx');
            $table->float('ball_vy');
            $table->unsignedTinyInteger('score_left')->default(0);
            $table->unsignedTinyInteger('score_right')->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_tick_at')->nullable();
            $table->timestamps();

            $table->index(['player_right_id', 'status']);
            $table->index(['player_left_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pong_games');
    }
};
