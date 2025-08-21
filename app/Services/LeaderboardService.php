<?php

namespace App\Services;

use App\Models\LeaderboardEntry;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{
    public function placeWinnerAtFourth(int $userId, int $gameId): void
    {
        DB::transaction(function(){
            $entries = LeaderboardEntry::whereNotNull('rank')->where('rank','>=',4)
                ->orderBy('rank','desc')->lockForUpdate()->get();
            foreach ($entries as $e) { $e->rank = $e->rank + 1; $e->save(); }
        });

        LeaderboardEntry::create([
            'user_id'=>$userId,
            'game_id'=>$gameId,
            'wins'=>1,
            'pinned_rank'=>4,
            'rank'=>4,
        ]);
    }

    public function top(): array
    {
        return LeaderboardEntry::orderBy('rank')
            ->orderByDesc('created_at')->with('user')->limit(20)->get()->toArray();
    }
}
