<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderboardEntry extends Model
{
    protected $fillable = ['user_id','game_id','wins','pinned_rank','rank'];
    public function user(){ return $this->belongsTo(User::class); }
}
