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

class GameEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $winner;

    public function __construct(Room $room, User $winner = null)
    {
        $this->room = $room;
        $this->winner = $winner;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room.' . $this->room->id);
    }

    public function broadcastAs()
    {
        return 'game.ended';
    }

    public function broadcastWith()
    {
        return [
            'room' => $this->room->load('players.user'),
            'winner' => $this->winner
        ];
    }
}
