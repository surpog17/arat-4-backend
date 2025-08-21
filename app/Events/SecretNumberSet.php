<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Room;
use App\Models\User;

class SecretNumberSet implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $user;

    public function __construct(Room $room, User $user)
    {
        $this->room = $room;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room.' . $this->room->id);
    }

    public function broadcastAs()
    {
        return 'secret.set';
    }

    public function broadcastWith()
    {
        return [
            'room' => $this->room->load('players.user'),
            'user' => $this->user
        ];
    }
}
