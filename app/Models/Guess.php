<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guess extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'guess',
        'accuracy',
        'position',
        'round_number',
        'is_winner',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_winner' => 'boolean'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
