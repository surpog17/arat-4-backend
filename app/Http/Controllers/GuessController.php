<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\Guess;
use App\Services\GameService;
use App\Events\GuessSubmitted;
use App\Events\GameEnded;

class GuessController extends Controller
{
    public function submit(Request $request, Room $room, GameService $gameService)
    {
        $request->validate([
            'guess' => 'required|string|size:4|regex:/^\d{4}$/'
        ]);

        // Check if game is active
        if ($room->status !== 'active') {
            return response()->json(['error' => 'Game is not active'], 400);
        }

        // Check if user has already guessed in this round
        $currentRound = $gameService->getCurrentRoundNumber($room);
        $existingGuess = $room->guesses()
            ->where('user_id', $request->user()->id)
            ->where('round_number', $currentRound)
            ->first();

        if ($existingGuess) {
            return response()->json(['error' => 'Already guessed in this round'], 400);
        }

        try {
            $result = $gameService->submitGuess($room, $request->user()->id, $request->guess, $currentRound);
            
            // Broadcast the guess
            event(new GuessSubmitted($room, $result['guess']));
            
            // If game ended, broadcast the end event
            if ($result['is_winner']) {
                event(new GameEnded($room, $request->user()));
            }
            
            return response()->json([
                'guess' => $result['guess'],
                'accuracy' => $result['accuracy'],
                'position' => $result['position'],
                'is_winner' => $result['is_winner'],
                'round_number' => $currentRound
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getRoomGuesses(Request $request, Room $room)
    {
        $guesses = $room->guesses()
            ->with('user')
            ->orderBy('round_number', 'asc')
            ->orderBy('submitted_at', 'asc')
            ->get();

        return response()->json($guesses);
    }
}
