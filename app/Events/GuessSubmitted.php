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
use App\Models\Guess;

class GuessSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $guess;

    public function __construct(Room $room, Guess $guess)
    {
        $this->room = $room;
        $this->guess = $guess;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room.' . $this->room->id);
    }

    public function broadcastAs()
    {
        return 'guess.submitted';
    }

    public function broadcastWith()
    {
        return [
            'guess' => $this->guess->load('user'),
            'room' => $this->room->load('players.user')
        ];
    }
}
