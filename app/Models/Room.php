<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'status',
        'host_id',
        'winner_id',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function players()
    {
        return $this->hasMany(RoomPlayer::class);
    }

    public function guesses()
    {
        return $this->hasMany(Guess::class);
    }

    public function gameHistory()
    {
        return $this->hasOne(GameHistory::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
