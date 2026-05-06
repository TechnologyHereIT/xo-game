<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function getPlayerStats(Request $request)
    {
        try {
            $range = $request->get('range', 'week');
            $player = Auth::user()->player;
            
            if (!$player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Player not found'
                ], 404);
            }

            $data = match($range) {
                'week' => $this->getWeeklyStats($player->id),
                'month' => $this->getMonthlyStats($player->id),
                'year' => $this->getYearlyStats($player->id),
                default => $this->getWeeklyStats($player->id)
            };

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Stats API Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getWeeklyStats($playerId)
    {
        $startDate = Carbon::now()->startOfWeek();
        $labels = [];
        $points = [];
        $games = [];
        $wins = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            $dayGames = Game::where(function($query) use ($playerId) {
                    $query->where('player1_id', $playerId)
                          ->orWhere('player2_id', $playerId);
                })
                ->where('status', 'completed')
                ->whereDate('completed_at', $date)
                ->get();

            $dayPoints = 0;
            $dayWins = 0;

            foreach ($dayGames as $game) {
                // حساب النقاط
                if ($game->winner === 'X' && $game->player1_id == $playerId) {
                    $dayWins++;
                    $dayPoints += 20;
                } elseif ($game->winner === 'O' && $game->player2_id == $playerId) {
                    $dayWins++;
                    $dayPoints += 20;
                } elseif ($game->winner === null) { // تعادل
                    $dayPoints += 5;
                } else { // خسارة
                    $dayPoints += 2;
                }
            }

            $labels[] = $date->locale('ar')->translatedFormat('l');
            $points[] = $dayPoints;
            $games[] = $dayGames->count();
            $wins[] = $dayWins;
        }

        return [
            'labels' => $labels,
            'points' => $points,
            'games' => $games,
            'wins' => $wins
        ];
    }

    private function getMonthlyStats($playerId)
    {
        $startDate = Carbon::now()->startOfMonth();
        $labels = [];
        $points = [];
        $games = [];
        $wins = [];

        for ($i = 0; $i < 4; $i++) {
            $weekStart = $startDate->copy()->addWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek();
            
            $weekGames = Game::where(function($query) use ($playerId) {
                    $query->where('player1_id', $playerId)
                          ->orWhere('player2_id', $playerId);
                })
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$weekStart, $weekEnd])
                ->get();

            $weekPoints = 0;
            $weekWins = 0;

            foreach ($weekGames as $game) {
                if ($game->winner === 'X' && $game->player1_id == $playerId) {
                    $weekWins++;
                    $weekPoints += 20;
                } elseif ($game->winner === 'O' && $game->player2_id == $playerId) {
                    $weekWins++;
                    $weekPoints += 20;
                } elseif ($game->winner === null) {
                    $weekPoints += 5;
                } else {
                    $weekPoints += 2;
                }
            }

            $labels[] = 'الأسبوع ' . ($i + 1);
            $points[] = $weekPoints;
            $games[] = $weekGames->count();
            $wins[] = $weekWins;
        }

        return [
            'labels' => $labels,
            'points' => $points,
            'games' => $games,
            'wins' => $wins
        ];
    }

    private function getYearlyStats($playerId)
    {
        $startDate = Carbon::now()->startOfYear();
        $labels = [];
        $points = [];
        $games = [];
        $wins = [];

        for ($i = 0; $i < 12; $i++) {
            $monthStart = $startDate->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            $monthGames = Game::where(function($query) use ($playerId) {
                    $query->where('player1_id', $playerId)
                          ->orWhere('player2_id', $playerId);
                })
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$monthStart, $monthEnd])
                ->get();

            $monthPoints = 0;
            $monthWins = 0;

            foreach ($monthGames as $game) {
                if ($game->winner === 'X' && $game->player1_id == $playerId) {
                    $monthWins++;
                    $monthPoints += 20;
                } elseif ($game->winner === 'O' && $game->player2_id == $playerId) {
                    $monthWins++;
                    $monthPoints += 20;
                } elseif ($game->winner === null) {
                    $monthPoints += 5;
                } else {
                    $monthPoints += 2;
                }
            }

            $labels[] = $monthStart->locale('ar')->translatedFormat('F');
            $points[] = $monthPoints;
            $games[] = $monthGames->count();
            $wins[] = $monthWins;
        }

        return [
            'labels' => $labels,
            'points' => $points,
            'games' => $games,
            'wins' => $wins
        ];
    }
}