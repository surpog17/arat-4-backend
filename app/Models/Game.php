<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = ['code','status','host_id','winner_id'];

    public function players(){ return $this->hasMany(GamePlayer::class); }
    public function rounds(){ return $this->hasMany(Round::class); }
}
