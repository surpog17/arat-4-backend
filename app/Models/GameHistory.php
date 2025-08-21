<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameHistory extends Model
{
    use HasFactory;

    protected $table = 'game_history';

    protected $fillable = [
        'room_id',
        'winner_id',
        'result',
        'total_rounds',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
}
