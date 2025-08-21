<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    protected $fillable = ['game_id','number','status','target','winner_user_id','opened_at','closed_at'];
    protected $casts = ['opened_at'=>'datetime','closed_at'=>'datetime'];
    public function guesses(){ return $this->hasMany(Guess::class); }
}
