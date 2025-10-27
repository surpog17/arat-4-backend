<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\GameHistory;
use App\Services\GameService;
use App\Events\PlayerJoinedRoom;
use App\Events\SecretNumberSet;
use App\Events\GameReset;
use App\Http\Controllers\ChatController;

class RoomController extends Controller
{
    public function store(Request $request)
    {
        $code = Str::upper(Str::random(6));
        $user = $request->user();

        $room = DB::transaction(function() use ($code, $user) {
            $room = Room::create([
                'code' => $code,
                'host_id' => $user->id,
                'status' => 'waiting'
            ]);
            
            RoomPlayer::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'is_host' => true
            ]);
            
            return $room;
        });

        return response()->json($room->load('players.user'));
    }

    public function join(Request $request, Room $room)
    {
        if ($room->status !== 'waiting') {
            return response()->json(['error' => 'Game already started'], 400);
        }

        if ($room->players()->count() >= 2) {
            return response()->json(['error' => 'Room is full'], 400);
        }

        // Check if user is already in the room
        $existingPlayer = $room->players()->where('user_id', $request->user()->id)->first();
        if ($existingPlayer) {
            return response()->json($room->load('players.user'));
        }

        RoomPlayer::create([
            'room_id' => $room->id,
            'user_id' => $request->user()->id,
            'is_host' => false
        ]);

        $room->load('players.user');
        
        // Send system message to chat
        $chatController = new ChatController();
        $chatController->sendSystemMessage($room, $request->user()->display_name . ' joined the room',$request->user()->id);
        
        // If this is the second player joining, start the game
        if ($room->players()->count() === 2) {
            $room->update([
                'status' => 'active',
                'started_at' => now()
            ]);
            $room->refresh();
        }
        
        event(new PlayerJoinedRoom($room, $request->user()));

        return response()->json($room);
    }

    public function show(Request $request, Room $room)
    {
        return response()->json($room->load(['players.user', 'guesses.user']));
    }

    public function findByCode(Request $request)
    {
        $code = $request->code;
        
        if (!$code) {
            return response()->json(['error' => 'Room code is required'], 400);
        }
        
        $room = Room::where('code', $code)->first();
        
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        return response()->json($room);
    }

    public function setSecretNumber(Request $request, Room $room, GameService $gameService)
    {
        $request->validate([
            'secret_number' => 'required|string|size:4|regex:/^\d{4}$/'
        ]);

        try {
            $gameService->setSecretNumber($room, $request->user()->id, $request->secret_number);
            $room->load('players.user');
            event(new SecretNumberSet($room, $request->user()));
            return response()->json(['message' => 'Secret number set successfully']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function playAgain(Request $request, Room $room)
    {
        // Check if user is in the room
        $player = $room->players()->where('user_id', $request->user()->id)->first();
        if (!$player) {
            return response()->json(['error' => 'You are not a player in this room'], 403);
        }

        // Only allow play again if game has ended
        if ($room->status !== 'ended') {
            return response()->json(['error' => 'Game must be ended to play again'], 400);
        }

        try {
            DB::transaction(function() use ($room) {
                // Reset room state
                $room->update([
                    'status' => 'waiting',
                    'winner_id' => null,
                    'started_at' => null,
                    'ended_at' => null
                ]);

                // Reset all players' secret numbers and guesses
                $room->players()->update([
                    'secret_number' => null,
                    'has_set_secret' => false
                ]);

                // Delete all guesses for this room
                $room->guesses()->delete();
            });

            $room->load('players.user');
            
            // Broadcast game reset event
            event(new GameReset($room, $request->user()));
            
            return response()->json([
                'message' => 'Room reset successfully',
                'room' => $room
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to reset room'], 500);
        }
    }

    public function getGameHistory(Request $request, Room $room)
    {
        $guesses = $room->guesses()
            ->with('user')
            ->orderBy('round_number', 'asc')
            ->orderBy('submitted_at', 'asc')
            ->get();

        return response()->json($guesses);
    }

    public function getAllGameHistory(Request $request)
    {
        $history = GameHistory::with(['room', 'winner'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }
}
