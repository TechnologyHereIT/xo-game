<?php

namespace App\Services;

use App\Models\Game;
use Carbon\Carbon;

class PlayerStatsService
{
public static function getWeeklyStats($playerId)
{
    $week = collect(range(6, 0))->map(function ($daysAgo) use ($playerId) {
        $date   = Carbon::now()->subDays($daysAgo);

        $games  = Game::whereDate('completed_at', $date)
                      ->where(function ($q) use ($playerId) {
                          $q->where('player1_id', $playerId)
                            ->orWhere('player2_id', $playerId);
                      })
                      ->get();

        // احسب النقاط حسب النتيجة
        $points = $games->sum(function ($game) use ($playerId) {
            if ($game->winner === null) return 5;                       // تعادل
            if ($game->winner === 'X' && $game->player1_id == $playerId) return 20;
            if ($game->winner === 'O' && $game->player2_id == $playerId) return 20;
            return 2;                                                   // خسارة
        });

        return [
            'day'    => $date->format('D'),
            'points' => $points,
            'games'  => $games->count(),
        ];
    });

    return [
        'labels' => $week->pluck('day')->map(fn($d) => __(Carbon::parse($d)->format('l'))),
        'points' => $week->pluck('points'),
        'games'  => $week->pluck('games'),
    ];
}
}