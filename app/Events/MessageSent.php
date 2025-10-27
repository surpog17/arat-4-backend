<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $room;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, Room $room)
    {
        $this->message = $message;
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('room.' . $this->room->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'type' => $this->message->type,
                'user' => $this->message->user ? [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'display_name' => $this->message->user->display_name,
                    'initials' => $this->message->initials,
                ] : null,
                'display_name' => $this->message->display_name,
                'initials' => $this->message->initials,
                'created_at' => $this->message->created_at->toISOString(),
            ],
        ];
    }
}
