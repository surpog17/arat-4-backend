<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'secret_number',
        'is_host',
        'has_set_secret'
    ];

    protected $casts = [
        'is_host' => 'boolean',
        'has_set_secret' => 'boolean'
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
