<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Services\GameService;

class GameController extends Controller
{
    public function store(Request $r)
    {
        $code = Str::upper(Str::random(6));
        $user = $r->user();

        $game = DB::transaction(function() use ($code, $user) {
            $game = Game::create(['code'=>$code,'host_id'=>$user->id,'status'=>'lobby']);
            GamePlayer::create(['game_id'=>$game->id,'user_id'=>$user->id,'is_host'=>true]);
            return $game;
        });

        return $game->fresh();
    }

    public function join(Request $r, Game $game)
    {
        abort_if($game->status !== 'lobby', 400, 'Game already started');
        abort_if($game->players()->count() >= 2, 400, 'Game is full');
        GamePlayer::firstOrCreate(['game_id'=>$game->id,'user_id'=>$r->user()->id]);
        return $game->load('players.user');
    }

    public function start(Request $r, Game $game, GameService $svc)
    {
        abort_if($game->host_id !== $r->user()->id, 403, 'Only host can start');
        abort_if($game->players()->count() < 2, 400, 'Need 2 players');
        $round = $svc->openNextRound($game);
        return ['game'=>$game->refresh(),'round'=>$round];
    }

    public function show(Request $r, Game $game)
    {
        return $game->load(['players.user','rounds'=>fn($q)=>$q->orderByDesc('number')->limit(1)]);
    }

    public function findByCode(Request $r)
    {
        $code = $r->query('code');
        $game = Game::where('code',$code)->firstOrFail();
        return $game;
    }
}
