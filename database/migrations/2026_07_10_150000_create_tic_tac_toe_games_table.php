<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tic_tac_toe_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_x_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('player_o_id')->constrained('users')->cascadeOnDelete();
            $table->json('board');
            $table->string('current_turn', 1)->default('x');
            $table->string('status')->default('pending');
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['player_o_id', 'status']);
            $table->index(['player_x_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tic_tac_toe_games');
    }
};
