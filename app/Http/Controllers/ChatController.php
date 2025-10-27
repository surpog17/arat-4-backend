<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Room;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class ChatController extends Controller
{
    /**
     * Get recent messages for a room.
     */
    public function getMessages(Request $request, Room $room): JsonResponse
    {
        $limit = $request->get('limit', 20);
        
        $messages = Message::forRoom($room->id)
            ->with(['user:id,name,display_name'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'type' => $message->type,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'display_name' => $message->user->display_name,
                        'initials' => $message->initials,
                    ],
                    'display_name' => $message->display_name,
                    'initials' => $message->initials,
                    'created_at' => $message->created_at->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message to a room.
     */
    public function sendMessage(Request $request, Room $room): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $message = Message::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'message' => $request->message,
            'type' => 'user',
        ]);

        $message->load('user:id,name,display_name');

        // Broadcast the message to all users in the room
        broadcast(new MessageSent($message, $room));

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'type' => $message->type,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'display_name' => $message->user->display_name,
                    'initials' => $message->initials,
                ],
                'display_name' => $message->display_name,
                'initials' => $message->initials,
                'created_at' => $message->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Send a system message to a room.
     */
    public function sendSystemMessage(Room $room, string $message, int $userId): Message
    {
        $systemMessage = Message::create([
            'user_id' => $userId, // System messages don't have a user
            'room_id' => $room->id,
            'message' => $message,
            'type' => 'system',
        ]);

        // Broadcast the system message to all users in the room
        broadcast(new MessageSent($systemMessage, $room));

        return $systemMessage;
    }
}
