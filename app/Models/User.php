<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

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

    /**
     * Get the messages sent by this user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the user's initials for avatar display.
     */
    public function getInitialsAttribute()
    {
        $name = $this->display_name ?? $this->name ?? 'Anonymous';
        $words = explode(' ', $name);
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
