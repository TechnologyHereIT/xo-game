<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_players' => Player::count(),
            'total_games' => Game::count(),
            'active_games' => Game::where('status', 'active')->count(),
            'total_points' => Player::sum('points')
        ];

        $recentGames = Game::with(['player1.user', 'player2.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentGames'));
    }

    public function games()
    {
        $games = Game::with(['player1.user', 'player2.user'])
                    ->latest()
                    ->paginate(20);
        
        return view('admin.games', [
            'games' => $games,
            'totalGames' => Game::count(),
            'activeGames' => Game::where('status', 'active')->count(),
            'completedGames' => Game::where('status', 'completed')->count(),
            'abandonedGames' => Game::where('status', 'abandoned')->count(),
        ]);
    }

    public function players()
    {
        $players = Player::with('user')
                    ->latest()
                    ->paginate(20);
        
        return view('admin.players', [
            'players' => $players,
            'totalPlayers' => Player::count(),
            'onlinePlayers' => User::where('last_seen', '>', now()->subMinutes(5))->count(),
            'activePlayers' => Player::where('games_played', '>', 10)->count(),
            'avgPoints' => round(Player::avg('points')),
        ]);
    }

    // AdminController.php
    public function viewGame($id)
    {
        $game = Game::with(['player1.user', 'player2.user', 'moves'])->find($id);

        if (!$game) {
            return redirect()->route('admin.games')
                            ->with('warning', 'اللعبة المطلوبة غير موجودة.');
        }

        return view('admin.game', compact('game'));
    }

    public function activeHumans()
    {
        $games = Game::with(['player1.user', 'player2.user'])
             ->where('status', 'active')
             ->get();

        return view('admin.active-humans', compact('games'));
    }
}