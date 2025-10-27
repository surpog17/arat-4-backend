<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'message',
        'type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room that the message belongs to.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope to get messages for a specific room.
     */
    public function scopeForRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Scope to get recent messages.
     */
    public function scopeRecent($query, $limit = 20)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get the user's display name or initials for the message.
     */
    public function getDisplayNameAttribute()
    {
        if ($this->type === 'system') {
            return 'System';
        }
        
        if (!$this->user) {
            return 'Anonymous';
        }
        
        return $this->user->display_name ?? $this->user->name ?? 'Anonymous';
    }

    /**
     * Get the user's initials for avatar display.
     */
    public function getInitialsAttribute()
    {
        if ($this->type === 'system') {
            return 'S';
        }
        
        if (!$this->user) {
            return 'A';
        }
        
        $name = $this->user->display_name ?? $this->user->name ?? 'Anonymous';
        $words = explode(' ', $name);
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($name, 0, 2));
    }
}
