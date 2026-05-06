<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'games_played',
        'games_won',
        'games_lost',
        'games_drawn',
        'is_premium',
        'level',
        'experience'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gamesAsPlayer1()
    {
        return $this->hasMany(Game::class, 'player1_id');
    }

    public function gamesAsPlayer2()
    {
        return $this->hasMany(Game::class, 'player2_id');
    }

    public function gameMoves()
    {
        return $this->hasMany(GameMove::class);
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_participants')
                    ->withTimestamps();
    }

    public function getWinRateAttribute()
    {
        if ($this->games_played == 0) return 0;
        return round(($this->games_won / $this->games_played) * 100, 2);
    }

    public function getTotalGamesAttribute()
    {
        return $this->games_played;
    }

    public function getGamesAttribute()
    {
        return $this->gamesAsPlayer1->merge($this->gamesAsPlayer2);
    }

    // الحصول على الإحصائيات الشهرية
    public function getMonthlyStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyGames = $this->games()
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->get();

        return [
            'games_played' => $monthlyGames->count(),
            'games_won' => $monthlyGames->where('winner', $this->id)->count(),
            'points_earned' => $monthlyGames->sum('points_earned')
        ];
    }
}