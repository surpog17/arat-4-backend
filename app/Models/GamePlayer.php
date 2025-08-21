<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamePlayer extends Model
{
    protected $fillable = ['game_id','user_id','score','is_host'];
    public function user(){ return $this->belongsTo(User::class); }
    public function game(){ return $this->belongsTo(Game::class); }
}
