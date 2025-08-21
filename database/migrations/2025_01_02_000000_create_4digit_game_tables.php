<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $t) {
            $t->id();
            $t->string('code', 8)->unique();
            $t->enum('status', ['waiting', 'active', 'ended'])->default('waiting');
            $t->foreignId('host_id')->constrained('users');
            $t->foreignId('winner_id')->nullable()->constrained('users');
            $t->timestamp('started_at')->nullable();
            $t->timestamp('ended_at')->nullable();
            $t->timestamps();
        });

        Schema::create('room_players', function (Blueprint $t) {
            $t->id();
            $t->foreignId('room_id')->constrained();
            $t->foreignId('user_id')->constrained();
            $t->string('secret_number', 4)->nullable(); // 4-digit secret number
            $t->boolean('is_host')->default(false);
            $t->boolean('has_set_secret')->default(false);
            $t->timestamps();
            $t->unique(['room_id','user_id']);
        });

        Schema::create('guesses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('room_id')->constrained();
            $t->foreignId('user_id')->constrained();
            $t->string('guess', 4); // 4-digit guess
            $t->unsignedTinyInteger('accuracy'); // count of correct digits (any position)
            $t->unsignedTinyInteger('position'); // count of correct digits in right position
            $t->unsignedTinyInteger('round_number');
            $t->boolean('is_winner')->default(false);
            $t->timestamp('submitted_at');
            $t->timestamps();
        });

        Schema::create('game_history', function (Blueprint $t) {
            $t->id();
            $t->foreignId('room_id')->constrained();
            $t->foreignId('winner_id')->nullable()->constrained('users');
            $t->enum('result', ['player1_win', 'player2_win', 'draw'])->nullable();
            $t->unsignedTinyInteger('total_rounds')->default(0);
            $t->timestamp('started_at');
            $t->timestamp('ended_at');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_history');
        Schema::dropIfExists('guesses');
        Schema::dropIfExists('room_players');
        Schema::dropIfExists('rooms');
    }
};
