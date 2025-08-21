<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\Guess;
use App\Models\GameHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\GameEnded;

class GameService
{
    /**
     * Calculate accuracy and position for a guess against a secret number
     */
    public function calculateGuessResult(string $guess, string $secretNumber): array
    {
        if (strlen($guess) !== 4 || strlen($secretNumber) !== 4) {
            throw new \InvalidArgumentException('Both guess and secret number must be 4 digits');
        }

        $accuracy = 0;
        $position = 0;
        $secretDigits = str_split($secretNumber);
        $guessDigits = str_split($guess);

        // Calculate position (correct digits in correct positions)
        for ($i = 0; $i < 4; $i++) {
            if ($guessDigits[$i] === $secretDigits[$i]) {
                $position++;
            }
        }

        // Calculate accuracy (correct digits in any position)
        $secretCount = array_count_values($secretDigits);
        $guessCount = array_count_values($guessDigits);

        foreach ($guessCount as $digit => $count) {
            if (isset($secretCount[$digit])) {
                $accuracy += min($count, $secretCount[$digit]);
            }
        }

        return [
            'accuracy' => $accuracy,
            'position' => $position
        ];
    }

    /**
     * Submit a guess and calculate results
     */
    public function submitGuess(Room $room, int $userId, string $guess, int $roundNumber): array
    {
        // Validate guess format
        if (!preg_match('/^\d{4}$/', $guess)) {
            throw new \InvalidArgumentException('Guess must be exactly 4 digits');
        }

        // Check for duplicate digits
        if (count(array_unique(str_split($guess))) !== 4) {
            throw new \InvalidArgumentException('Guess must have 4 unique digits');
        }

        // Get the opponent's secret number
        $opponent = $room->players()
            ->where('user_id', '!=', $userId)
            ->where('has_set_secret', true)
            ->first();

        if (!$opponent || !$opponent->secret_number) {
            throw new \InvalidArgumentException('Opponent has not set their secret number yet');
        }

        // Calculate result
        $result = $this->calculateGuessResult($guess, $opponent->secret_number);
        
        // Check if this is a winning guess
        $isWinner = ($result['accuracy'] === 4 && $result['position'] === 4);

        // Save the guess
        $guessRecord = Guess::create([
            'room_id' => $room->id,
            'user_id' => $userId,
            'guess' => $guess,
            'accuracy' => $result['accuracy'],
            'position' => $result['position'],
            'round_number' => $roundNumber,
            'is_winner' => $isWinner,
            'submitted_at' => now()
        ]);

        // Check if game should end
        if ($isWinner) {
            $this->endGame($room, $userId);
        }

        return [
            'guess' => $guessRecord,
            'accuracy' => $result['accuracy'],
            'position' => $result['position'],
            'is_winner' => $isWinner
        ];
    }

    /**
     * Set a player's secret number
     */
    public function setSecretNumber(Room $room, int $userId, string $secretNumber): bool
    {
        // Validate secret number format
        if (!preg_match('/^\d{4}$/', $secretNumber)) {
            throw new \InvalidArgumentException('Secret number must be exactly 4 digits');
        }

        // Check for duplicate digits
        if (count(array_unique(str_split($secretNumber))) !== 4) {
            throw new \InvalidArgumentException('Secret number must have 4 unique digits');
        }

        $player = $room->players()->where('user_id', $userId)->first();
        if (!$player) {
            throw new \InvalidArgumentException('Player not found in room');
        }

        $player->update([
            'secret_number' => $secretNumber,
            'has_set_secret' => true
        ]);

        // Check if both players have set their secret numbers
        $allPlayersSet = $room->players()
            ->where('has_set_secret', true)
            ->count() === 2;

        if ($allPlayersSet) {
            $room->update([
                'status' => 'active',
                'started_at' => now()
            ]);
        }

        return true;
    }

    /**
     * End the game and determine winner
     */
    public function endGame(Room $room, int $winnerId = null): void
    {
        DB::transaction(function () use ($room, $winnerId) {
            $room->update([
                'status' => 'ended',
                'winner_id' => $winnerId,
                'ended_at' => now()
            ]);

            // Create game history
            $totalRounds = $room->guesses()->max('round_number') ?? 0;
            
            GameHistory::create([
                'room_id' => $room->id,
                'winner_id' => $winnerId,
                'result' => $winnerId ? 'player1_win' : 'draw',
                'total_rounds' => $totalRounds,
                'started_at' => $room->started_at,
                'ended_at' => now()
            ]);
        });
    }

    /**
     * Get current round number for a room
     */
    public function getCurrentRoundNumber(Room $room): int
    {
        $maxRound = $room->guesses()->max('round_number');
        return $maxRound ? $maxRound + 1 : 1;
    }

    /**
     * Check if both players have guessed in the current round
     */
    public function checkRoundCompletion(Room $room, int $roundNumber): bool
    {
        $guessCount = $room->guesses()
            ->where('round_number', $roundNumber)
            ->count();

        return $guessCount >= 2;
    }
}
