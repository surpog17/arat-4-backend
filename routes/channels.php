<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('game.{gameId}', function ($user, $gameId) {
    return ['id' => $user->id, 'name' => $user->name];
});
