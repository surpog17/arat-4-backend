<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the games where this user is the host.
     */
    public function hostedGames()
    {
        return $this->hasMany(Game::class, 'host_id');
    }

    /**
     * Get the games where this user is the winner.
     */
    public function wonGames()
    {
        return $this->hasMany(Game::class, 'winner_id');
    }

    /**
     * Get the game players for this user.
     */
    public function gamePlayers()
    {
        return $this->hasMany(GamePlayer::class);
    }

    /**
     * Get the games this user is participating in.
     */
    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_players');
    }

    /**
     * Get the guesses made by this user.
     */
    public function guesses()
    {
        return $this->hasMany(Guess::class);
    }

    /**
     * Get the rounds won by this user.
     */
    public function wonRounds()
    {
        return $this->hasMany(Round::class, 'winner_user_id');
    }

    /**
     * Get the leaderboard entries for this user.
     */
    public function leaderboardEntries()
    {
        return $this->hasMany(LeaderboardEntry::class);
    }
}
