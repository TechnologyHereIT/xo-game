<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\Question;
use App\Models\Tournament;
use App\Events\GameUpdated;
use App\Events\GameInvitation;
use App\Events\SpeedRoundActivated;
use App\Events\SpeedRoundAnswered;
use App\Services\PlayerStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Events\GameRequestSent;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Get or create player for the authenticated user
     */
    private function getOrCreatePlayer()
    {
        $user = Auth::user();
        $player = $user->player;
        
        if (!$player) {
            $player = Player::create([
                'user_id' => $user->id,
                'points' => 0,
                'games_played' => 0,
                'games_won' => 0,
                'games_lost' => 0,
                'games_drawn' => 0
            ]);
        }
        
        return $player;
    }

    public function index()
    {
        $user = Auth::user();
        $player = $user->player;
        
        // Ensure player exists before accessing player->id
        if (!$player) {
            $player = Player::create([
                'user_id' => $user->id,
                'points' => 0,
                'games_played' => 0,
                'games_won' => 0,
                'games_lost' => 0,
                'games_drawn' => 0
            ]);
        }
        
        // Now we can safely access player->id
        $weeklyStats = $this->getWeeklyStats($player->id);
        $monthlyStats = $this->getMonthlyStats($player->id);
        $yearlyStats = $this->getYearlyStats($player->id);

        $activeGames = Game::where(function($query) use ($player) {
            $query->where('player1_id', $player->id)
                  ->orWhere('player2_id', $player->id);
        })->where('status', 'active')->get();

        $leaderboard = Player::with('user')
            ->orderBy('points', 'desc')
            ->limit(10)
            ->get();

        $topPlayers = Player::with('user')
            ->orderBy('points', 'desc')
            ->limit(5)
            ->get();

        $recentGames = Game::with(['player1.user', 'player2.user'])
            ->where(function($query) use ($player) {
                $query->where('player1_id', $player->id)
                      ->orWhere('player2_id', $player->id);
            })
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        $playerRank = Player::where('points', '>', $player->points)->count() + 1;
        $rankTitle = $this->getRankTitle($playerRank);
        $achievements = $this->getPlayerAchievements($player);
        $activeTournaments = $this->getActiveTournaments();
        
        // حساب الإحصائيات الحقيقية للأسبوع والشهر
        $weekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();
        
        // النقاط المكتسبة هذا الأسبوع
        $thisWeekPoints = Game::where(function($query) use ($player) {
            $query->where('player1_id', $player->id)
                  ->orWhere('player2_id', $player->id);
        })
        ->where('status', 'completed')
        ->where('completed_at', '>=', $weekStart)
        ->get()
        ->sum(function($game) use ($player) {
            if ($game->winner === 'X' && $game->player1_id == $player->id) return 20;
            if ($game->winner === 'O' && $game->player2_id == $player->id) return 20;
            if ($game->winner === null) return 5;
            return 2;
        });
        
        // النقاط المكتسبة الأسبوع الماضي
        $lastWeekPoints = Game::where(function($query) use ($player) {
            $query->where('player1_id', $player->id)
                  ->orWhere('player2_id', $player->id);
        })
        ->where('status', 'completed')
        ->whereBetween('completed_at', [$lastWeekStart, $lastWeekEnd])
        ->get()
        ->sum(function($game) use ($player) {
            if ($game->winner === 'X' && $game->player1_id == $player->id) return 20;
            if ($game->winner === 'O' && $game->player2_id == $player->id) return 20;
            if ($game->winner === null) return 5;
            return 2;
        });
        
        $pointsChangeThisWeek = $thisWeekPoints - $lastWeekPoints;
        
        // الألعاب هذا الأسبوع
        $thisWeekGames = Game::where(function($query) use ($player) {
            $query->where('player1_id', $player->id)
                  ->orWhere('player2_id', $player->id);
        })
        ->where('status', 'completed')
        ->where('completed_at', '>=', $weekStart)
        ->count();
        
        $lastWeekGames = Game::where(function($query) use ($player) {
            $query->where('player1_id', $player->id)
                  ->orWhere('player2_id', $player->id);
        })
        ->where('status', 'completed')
        ->whereBetween('completed_at', [$lastWeekStart, $lastWeekEnd])
        ->count();
        
        $gamesChangeThisWeek = $thisWeekGames - $lastWeekGames;
        
        // معدل الفوز - حساب التغيير الشهري
        $monthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        
        $thisMonthGames = Game::where(function($query) use ($player) {
            $query->where('player1_id', $player->id)
                  ->orWhere('player2_id', $player->id);
        })
        ->where('status', 'completed')
        ->where('completed_at', '>=', $monthStart)
        ->get();
        
        $thisMonthWins = $thisMonthGames->filter(function($game) use ($player) {
            return ($game->winner === 'X' && $game->player1_id == $player->id) ||
                   ($game->winner === 'O' && $game->player2_id == $player->id);
        })->count();
        
        $thisMonthWinRate = $thisMonthGames->count() > 0 
            ? round(($thisMonthWins / $thisMonthGames->count()) * 100, 1) 
            : 0;
        
        $lastMonthGames = Game::where(function($query) use ($player) {
            $query->where('player1_id', $player->id)
                  ->orWhere('player2_id', $player->id);
        })
        ->where('status', 'completed')
        ->whereBetween('completed_at', [$lastMonthStart, $lastMonthEnd])
        ->get();
        
        $lastMonthWins = $lastMonthGames->filter(function($game) use ($player) {
            return ($game->winner === 'X' && $game->player1_id == $player->id) ||
                   ($game->winner === 'O' && $game->player2_id == $player->id);
        })->count();
        
        $lastMonthWinRate = $lastMonthGames->count() > 0 
            ? round(($lastMonthWins / $lastMonthGames->count()) * 100, 1) 
            : 0;
        
        $winRateChange = $thisMonthWinRate - $lastMonthWinRate;

        // إحصائيات حية حقيقية
        $liveStats = [
            'onlinePlayers' => \App\Models\User::where('last_seen', '>', now()->subMinutes(5))->count(),
            'activeGames' => Game::where('status', 'active')->count(),
            'tournaments' => \App\Models\Tournament::whereIn('status', ['active', 'upcoming', 'registration'])->count()
        ];
        
        // تمرير بيانات اللاعب للـ view
        $userPoints = $player->points ?? 0;
        $gamesPlayed = $player->games_played ?? 0;
        // حساب معدل الفوز يدوياً للتأكد من ظهوره
        $winRate = 0;
        if ($player && $player->games_played > 0) {
            $winRate = round(($player->games_won / $player->games_played) * 100, 1);
        }
        
        // حساب إحصائيات سريعة حقيقية
        $playerGames = Game::where(function($query) use ($player) {
            $query->where('player1_id', $player->id)
                  ->orWhere('player2_id', $player->id);
        })
        ->where('status', 'completed')
        ->whereNotNull('completed_at')
        ->whereNotNull('created_at')
        ->get();
        
        $fastestWin = null;
        $longestGame = null;
        $averageTime = null;
        
        if ($playerGames->count() > 0) {
            $gameDurations = $playerGames->map(function($game) {
                if ($game->created_at && $game->completed_at) {
                    return $game->created_at->diffInSeconds($game->completed_at);
                }
                return null;
            })->filter();
            
            if ($gameDurations->count() > 0) {
                // أسرع فوز (أقصر مباراة فاز فيها اللاعب)
                $winningGames = $playerGames->filter(function($game) use ($player) {
                    return ($game->winner === 'X' && $game->player1_id == $player->id) ||
                           ($game->winner === 'O' && $game->player2_id == $player->id);
                });
                
                if ($winningGames->count() > 0) {
                    $fastestWinSeconds = $winningGames->map(function($game) {
                        if ($game->created_at && $game->completed_at) {
                            return $game->created_at->diffInSeconds($game->completed_at);
                        }
                        return null;
                    })->filter()->min();
                    
                    if ($fastestWinSeconds) {
                        $fastestWin = $this->formatDuration($fastestWinSeconds);
                    }
                }
                
                // أطول مباراة
                $longestSeconds = $gameDurations->max();
                if ($longestSeconds) {
                    $longestGame = $this->formatDuration($longestSeconds);
                }
                
                // متوسط الوقت
                $averageSeconds = $gameDurations->avg();
                if ($averageSeconds) {
                    $averageTime = $this->formatDuration(round($averageSeconds));
                }
            }
        }
        
        // قيم افتراضية إذا لم توجد بيانات
        $fastestWin = $fastestWin ?? 'لا توجد بيانات';
        $longestGame = $longestGame ?? 'لا توجد بيانات';
        $averageTime = $averageTime ?? 'لا توجد بيانات';

        return view('dashboard', compact(
            'activeGames', 
            'leaderboard', 
            'topPlayers',
            'recentGames',
            'playerRank',
            'rankTitle',
            'achievements',
            'weeklyStats',
            'monthlyStats', 
            'yearlyStats',
            'activeTournaments',
            'liveStats',
            'pointsChangeThisWeek',
            'gamesChangeThisWeek',
            'winRateChange',
            'userPoints',
            'gamesPlayed',
            'winRate',
            'player',
            'fastestWin',
            'longestGame',
            'averageTime'
        ));
    }
    
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' ث';
        }
        
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($minutes < 60) {
            if ($remainingSeconds > 0) {
                return $minutes . ':' . str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT) . ' د';
            }
            return $minutes . ' د';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return $hours . ':' . str_pad($remainingMinutes, 2, '0', STR_PAD_LEFT) . ' س';
    }
    
    public function getLiveStats()
    {
        try {
            $stats = [
                'onlinePlayers' => \App\Models\User::where('last_seen', '>', now()->subMinutes(5))->count(),
                'activeGames' => Game::where('status', 'active')->count(),
                'tournaments' => \App\Models\Tournament::whereIn('status', ['active', 'upcoming', 'registration'])->count()
            ];
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting live stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'stats' => [
                    'onlinePlayers' => 0,
                    'activeGames' => 0,
                    'tournaments' => 0
                ]
            ], 500);
        }
    }

    private function getWeeklyStats($playerId)
    {
        $startDate = now()->startOfWeek();
        $stats = [
            'labels' => [],
            'points' => [],
            'games' => [],
            'wins' => []
        ];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayGames = Game::where(function($q) use ($playerId) {
                    $q->where('player1_id', $playerId)
                    ->orWhere('player2_id', $playerId);
                })
                ->where('status', 'completed')
                ->whereDate('completed_at', $date)
                ->get();

            $dayPoints = 0;
            $dayWins = 0;
            
            foreach ($dayGames as $game) {
                if ($game->winner === 'X' && $game->player1_id === $playerId) {
                    $dayWins++;
                    $dayPoints += 20;
                } elseif ($game->winner === 'O' && $game->player2_id === $playerId) {
                    $dayWins++;
                    $dayPoints += 20;
                } elseif (!$game->winner) {
                    $dayPoints += 5;
                } else {
                    $dayPoints += 2;
                }
            }
            
            $stats['labels'][] = $date->translatedFormat('l');
            $stats['points'][] = $dayPoints;
            $stats['games'][] = $dayGames->count();
            $stats['wins'][] = $dayWins;
        }

        return $stats;
    }

        private function getMonthlyStats($playerId)
    {
        $startDate = now()->startOfMonth();
        $stats = [
            'labels' => [],
            'points' => [],
            'games' => [],
            'wins' => []
        ];

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

            $stats['labels'][] = 'الأسبوع ' . ($i + 1);
            $stats['points'][] = $weekPoints;
            $stats['games'][] = $weekGames->count();
            $stats['wins'][] = $weekWins;
        }

        return $stats;
    }

    private function getYearlyStats($playerId)
    {
        $startDate = now()->startOfYear();
        $stats = [
            'labels' => [],
            'points' => [],
            'games' => [],
            'wins' => []
        ];

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

            $stats['labels'][] = $monthStart->locale('ar')->translatedFormat('F');
            $stats['points'][] = $monthPoints;
            $stats['games'][] = $monthGames->count();
            $stats['wins'][] = $monthWins;
        }

        return $stats;
    }

    public function games()
    {
        $player = $this->getOrCreatePlayer();
        $playerId = $player->id;

        $games = Game::with(['player1.user', 'player2.user'])
            ->where(function ($q) use ($playerId) {
                $q->where('player1_id', $playerId)
                ->orWhere('player2_id', $playerId);
            })
            ->whereNotIn('status', ['cancelled']) // استبعاد الألعاب الملغية
            ->latest()
            ->paginate(12);

        return view('game.index', compact('games', 'player'));
    }
    
    public function create(Request $request)
    {
        \Log::info('create game request', $request->all());
        $request->validate([
            'game_type' => 'required|in:computer,online,tournament',
            'player2_id' => 'nullable|exists:players,id'
        ]);

        $user = Auth::user();
        $player = $user->player;
        if (!$player) {
            $player = Player::create([
                'user_id' => $user->id,
                'points' => 0,
                'games_played' => 0,
                'games_won' => 0,
                'games_lost' => 0,
                'games_drawn' => 0
            ]);
        }

        $gameData = [
            'game_type' => $request->game_type,
            'player1_id' => $player->id,
            'player2_id' => $request->game_type === 'computer' ? null : $request->player2_id,
            'status' => $request->game_type === 'computer' ? 'active' : 'waiting',
            'board' => json_encode(array_fill(0, 9, null))
        ];

        if ($request->tournament_id) {
            $gameData['tournament_id'] = $request->tournament_id;
        }

        $game = Game::create($gameData);
        
        // تهيئة البطاقات للاعبين
        $this->initializePowerUps($game);
        
        if ($request->game_type === 'online' && $request->player2_id) {
            $opponent = Player::find($request->player2_id);
            $data = [
                'from_user' => $user->only('id', 'name'),
                'game_id' => $game->id,
            ];

            // أرسل إيفنت Pusher (إن اشتغل)
            broadcast(new GameRequestSent($game, $user, $opponent->user))->toOthers();

            // احفظ للـ Polling Fallback
            Cache::put("game_request_{$opponent->user_id}", $data, now()->addMinutes(5));
        }

        return response()->json([
            'success' => true,
            'game_id' => $game->id,
            'invite_id' => $game->id, // إضافة هذا الحقل
            'message' => 'تم إنشاء اللعبة بنجاح'
        ]);
    }
    
    public function getInviteStatus($gameId)
    {
        try {
            $game = Game::with(['player1', 'player2'])->find($gameId);
            
            if (!$game) {
                return response()->json([
                    'status' => 'expired',
                    'message' => 'الدعوة غير موجودة'
                ]);
            }

            $player = $this->getOrCreatePlayer();
            $currentPlayerId = $player->id;

            // تحقق إذا كان اللاعب هو من أرسل الدعوة
            if ($game->player1_id !== $currentPlayerId) {
                return response()->json([
                    'status' => 'invalid',
                    'message' => 'غير مصرح بالوصول لهذه الدعوة'
                ]);
            }

            // تحقق من حالة اللعبة
            if ($game->status === 'active') {
                return response()->json([
                    'status' => 'accepted',
                    'game_id' => $game->id,
                    'message' => 'تم قبول الدعوة'
                ]);
            } elseif ($game->status === 'completed' || $game->status === 'cancelled') {
                return response()->json([
                    'status' => 'declined',
                    'message' => 'تم رفض الدعوة'
                ]);
            } else {
                return response()->json([
                    'status' => 'pending',
                    'game_id' => $game->id,
                    'message' => 'في انتظار الرد'
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Error checking invite status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في التحقق من حالة الدعوة'
            ], 500);
        }
    }

    public function cancelInvite($gameId)
    {
        try {
            $game = Game::find($gameId);
            $player = $this->getOrCreatePlayer();
            $currentPlayerId = $player->id;

            if (!$game) {
                return response()->json([
                    'success' => false,
                    'message' => 'الدعوة غير موجودة'
                ]);
            }

            // تحقق من أن اللاعب هو من أرسل الدعوة أو المستهدف بها
            if ($game->player1_id !== $currentPlayerId && $game->player2_id !== $currentPlayerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح بإلغاء هذه الدعوة'
                ]);
            }

            // مسح الدعوة من الكاش
            if ($game->player1_id) {
                $player1 = Player::find($game->player1_id);
                if ($player1) {
                    Cache::forget("game_request_{$player1->user_id}");
                }
            }
            
            if ($game->player2_id) {
                $player2 = Player::find($game->player2_id);
                if ($player2) {
                    Cache::forget("game_request_{$player2->user_id}");
                }
            }

            // تحديث حالة اللعبة
            $game->update([
                'status' => 'cancelled'
            ]);

            // بث حدث تحديث اللعبة
            broadcast(new GameUpdated($game))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الدعوة بنجاح'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error cancelling invite: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إلغاء الدعوة'
            ], 500);
        }
    }

    private function initializePowerUps(Game $game)
    {
        $powerUps = [
            'double_move' => ['name' => 'حركة مزدوجة', 'used' => false, 'icon' => '⚡'],
            'block_opponent' => ['name' => 'حجب الخصم', 'used' => false, 'icon' => '🚫'],
            'free_placement' => ['name' => 'وضع حر', 'used' => false, 'icon' => '🎯'],
            'shuffle_board' => ['name' => 'تبديل اللوحة', 'used' => false, 'icon' => '🔀']
        ];
        
        // تهيئة بطاقات اللاعب الأول
        Cache::put("powerups_{$game->id}_{$game->player1_id}", $powerUps, now()->addHours(2));
        
        // تهيئة بطاقات اللاعب الثاني إذا موجود
        if ($game->player2_id) {
            Cache::put("powerups_{$game->id}_{$game->player2_id}", $powerUps, now()->addHours(2));
        }
    }
    
    public function history(\App\Models\User $user)
    {
        $games = \App\Models\Game::with(['player1.user', 'player2.user'])
            ->where(function ($q) use ($user) {
                $q->where('player1_id', $user->player?->id)
                ->orWhere('player2_id', $user->player?->id);
            })
            ->latest()
            ->paginate(15);

        return view('game.history', compact('user', 'games'));
    }

    public function leaderboard()
    {
        // التصنيف العام
        $globalLeaderboard = Player::with('user')
            ->whereHas('user')
            ->orderBy('points', 'DESC')
            ->paginate(20);

        // قائمة الدول
        $countries = [
            'SA' => 'السعودية',
            'EG' => 'مصر',
            'AE' => 'الإمارات',
            'KW' => 'الكويت',
            'QA' => 'قطر',
            'BH' => 'البحرين',
            'OM' => 'عمان',
            'JO' => 'الأردن',
            'LB' => 'لبنان',
            'SY' => 'سوريا',
            'IQ' => 'العراق',
            'DZ' => 'الجزائر',
            'MA' => 'المغرب',
            'TN' => 'تونس',
            'SD' => 'السودان',
            'YE' => 'اليمن',
            'LY' => 'ليبيا',
            'PS' => 'فلسطين',
        ];

        // تصنيف الدول
        $countryLeaderboard = Player::join('users', 'players.user_id', '=', 'users.id')
            ->whereNotNull('users.country')
            ->select(
                'users.country as code',
                DB::raw('COUNT(players.id) as player_count'),
                DB::raw('SUM(players.points) as total_points'),
                DB::raw('AVG(players.points) as avg_points'),
                DB::raw('SUM(players.games_won) as total_wins')
            )
            ->groupBy('users.country')
            ->orderBy('avg_points', 'DESC')
            ->get()
            ->map(function($item) use ($countries) {
                $item->name = $countries[$item->code] ?? $item->code;
                return $item;
            });

        // إحصائيات عامة
        $totalCountries = $countryLeaderboard->count();
        $totalPlayers = Player::whereHas('user')->count();
        $highestPoints = Player::max('points');
        $topWinningCountry = $countryLeaderboard->sortByDesc('total_wins')->first(); 

        return view('leaderboard', compact(
            'globalLeaderboard',
            'countryLeaderboard',
            'countries',
            'totalCountries',
            'totalPlayers',
            'highestPoints',
            'topWinningCountry'
        ));
    }

    public function show($id)
    {
        $game = Game::with(['player1.user', 'player2.user', 'moves'])
                   ->findOrFail($id);
        
        $player = $this->getOrCreatePlayer();
        $playerId = $player->id;
        if ($game->player1_id !== $playerId && (!$game->player2_id || $game->player2_id !== $playerId)) {
            abort(403, 'غير مصرح لك بمشاهدة هذه اللعبة');
        }

        return view('game.play', compact('game', 'player'));
    }

    public function skipReplace(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        // ✅ تغيير الدور فقط
        $game->current_turn = $game->current_turn === 'player1' ? 'player2' : 'player1';
        $game->speed_round_used = true;
        $game->save();

        broadcast(new GameUpdated($game));

        return response()->json(['success' => true]);
    }
    
    public function makeMove(Request $request, $id)
    {
        try {
            // ✅ 1) Validation
            $request->validate([
                'position' => 'required|integer|between:0,8',
                'selected_option' => 'required|string|in:0,1,2,3',
                'correct_answer' => 'required|boolean'
            ]);

            // ✅ 2) Load Game & Player
            $game = Game::findOrFail($id);
            $user = Auth::user();
            $player = $user->player;

            if (!$player) {
                return response()->json(['error' => 'لم يتم العثور على ملف لاعب'], 404);
            }

            // ✅ 3) Check Turn
            $currentPlayer = $game->current_turn === 'player1' ? $game->player1_id : $game->player2_id;
            if ($currentPlayer !== $player->id) {
                return response()->json(['error' => 'ليس دورك للعب'], 403);
            }

            // ✅ 4) Board & Cell Check
            $position = $request->input('position');
            $board = $game->getBoardArrayAttribute();
            if ($board[$position] !== null) {
                return response()->json(['error' => 'الخلية محجوزة مسبقاً'], 400);
            }

            // ✅ 5) Fetch Question
            $question = Question::inRandomOrder()->first() ??
                        Question::create([
                            'question' => 'ما هو ناتج 5 + 3؟',
                            'options' => ['8', '7', '6', '9'],
                            'correct_option' => '0',
                            'difficulty' => 'medium',
                            'category' => 'رياضيات'
                        ]);

            // ✅ 6) Answer Check
            $selectedOption = $request->input('selected_option');
            $isCorrect = $question->isAnswerCorrect($selectedOption);

            // ✅ 7) Apply Move
            if ($isCorrect) {
                $symbol = $game->current_turn === 'player1' ? 'X' : 'O';
                $board[$position] = $symbol;
                $game->board = json_encode($board);
                $game->current_turn = $game->current_turn === 'player1' ? 'player2' : 'player1';

                // ✅ 8) Check for Speed Round Activation
                $remainingCells = count(array_filter($board, fn($cell) => $cell === null));
                if ($remainingCells === 5 && !$game->speed_round_activated) {
                    $game->speed_round_activated = true;
                    
                    // ✅ بث حدث جولة السرعة للاعبين - بدون showNotification
                    broadcast(new SpeedRoundActivated($game))->toOthers();
                    
                    // ❌ لا تستخدم showNotification هنا - إنها للـ JavaScript فقط
                    // showNotification('🎯 جولة السرعة مفعلة!', 'info');
                }

                // ✅ 9) Check Game Status
                if ($winner = $game->checkWinner()) {
                    $game->winner = $winner;
                    $game->status = 'completed';
                    $this->updatePlayerStats($game);
                } elseif ($game->isBoardFull()) {
                    $game->status = 'completed';
                    $this->updatePlayerStats($game, true);
                }
            } else {
                $game->current_turn = $game->current_turn === 'player1' ? 'player2' : 'player1';
            }

            // ✅ 10) Save Move
            $game->moves()->create([
                'player_id' => $player->id,
                'position' => $position,
                'correct_answer' => $isCorrect,
                'question_id' => $question->id,
                'selected_option' => $selectedOption
            ]);
            
            if ($isCorrect && $game->speed_round_activated) {
                broadcast(new SpeedRoundAnswered($game, $user))->toOthers();
            }

            $game->save();
            broadcast(new GameUpdated($game))->toOthers();

            // ✅ 11) Computer Move if Needed
            if ($game->game_type === 'computer' && $game->player2_id === null && $game->status === 'active' && $game->current_turn === 'player2') {
                $this->makeComputerMove($game);
            }

            return response()->json([
                'success' => true,
                'id' => $game->id,
                'board' => $board,
                'current_turn' => $game->current_turn,
                'status' => $game->status,
                'winner' => $game->winner,
                'player1_id' => $game->player1_id,
                'player2_id' => $game->player2_id,
                'speed_round_activated' => $game->speed_round_activated,
                'speed_round_used' => $game->speed_round_used ?? false,
                'game_type' => $game->game_type,
            ]);

        } catch (\Throwable $e) {
            \Log::error('makeMove exception: ' . $e->getMessage(), [
                'game_id' => $id,
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'خطأ داخلي في الخادم'], 500);
        }
    }

    // ✅ أضف هذه الدالة الجديدة للتعامل مع حركة جولة السرعة
    public function speedRoundMove(Request $request, $id)
    {
        try {
            // ✅ 1) Validation
            $request->validate([
                'position_to_replace' => 'required|integer|between:0,8',
                'new_symbol' => 'required|string|in:X,O',
                'speed_round_winner' => 'required|boolean'
            ]);

            // ✅ 2) Load Game & Player
            $game = Game::findOrFail($id);
            $user = Auth::user();
            $player = $user->player;

            if (!$player) {
                return response()->json(['error' => 'لم يتم العثور على ملف لاعب'], 404);
            }

            // ✅ 3) Check if Speed Round is Active
            if (!$game->speed_round_activated) {
                return response()->json(['error' => 'جولة السرعة غير مفعلة'], 400);
            }

            // ✅ 4) Check if Speed Round Already Used
            if ($game->speed_round_used) {
                return response()->json(['error' => 'تم استخدام جولة السرعة مسبقاً'], 400);
            }

            // ✅ 5) Check if Player is Speed Round Winner
            if ($request->input('speed_round_winner') !== true && $request->input('speed_round_winner') !== 'true') {
                return response()->json(['error' => 'لست الفائز في جولة السرعة'], 403);
            }

            // ✅ 6) Verify Player Turn
            $currentPlayer = $game->current_turn === 'player1' ? $game->player1_id : $game->player2_id;
            if ($currentPlayer !== $player->id) {
                return response()->json(['error' => 'ليس دورك للعب'], 403);
            }

            // ✅ 7) Check Position Validity
            $position = $request->input('position_to_replace');
            $newSymbol = $request->input('new_symbol');
            $board = $game->getBoardArrayAttribute();
            
            // التأكد من أن الخلية تحتوي على رمز الخصم
            $opponentSymbol = $newSymbol === 'X' ? 'O' : 'X';
            if ($board[$position] !== $opponentSymbol) {
                return response()->json(['error' => 'يمكنك استبدال رموز الخصم فقط'], 400);
            }

            // ✅ 8) Apply Speed Round Move
            $board[$position] = $newSymbol;
            $game->board = json_encode($board);
            $game->speed_round_used = true;
            $game->speed_round_activated = false;

            // ✅ 9) Check Game Status After Move
            if ($winner = $game->checkWinner()) {
                $game->winner = $winner;
                $game->status = 'completed';
                $this->updatePlayerStats($game);
            } elseif ($game->isBoardFull()) {
                $game->status = 'completed';
                $this->updatePlayerStats($game, true);
            } else {
                // تغيير الدور بعد الاستبدال
                $game->current_turn = $game->current_turn === 'player1' ? 'player2' : 'player1';
            }

            // ✅ 10) Save Speed Round Move
            $game->moves()->create([
                'player_id' => $player->id,
                'position' => $position,
                'correct_answer' => true, // في جولة السرعة يعتبر دائماً إجابة صحيحة
                'question_id' => null, // لا يوجد سؤال في جولة السرعة
                'selected_option' => null,
                'is_speed_round' => true
            ]);

            $game->save();
            event(new GameUpdated($game));
            broadcast(new GameUpdated($game))->toOthers();

            // ✅ 11) Computer Move if Needed
            if ($game->game_type === 'computer' && $game->player2_id === null && $game->status === 'active' && $game->current_turn === 'player2') {
                $this->makeComputerMove($game);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم استبدال المربع بنجاح',
                'id' => $game->id,
                'board' => $board,
                'current_turn' => $game->current_turn,
                'status' => $game->status,
                'winner' => $game->winner,
                'speed_round_activated' => $game->speed_round_activated,
                'speed_round_used' => $game->speed_round_used
            ]);

        } catch (\Throwable $e) {
            \Log::error('speedRoundMove exception: ' . $e->getMessage(), [
                'game_id' => $id,
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'خطأ داخلي في الخادم'], 500);
        }
    }

    private function isSpeedRoundWinner($game, $playerId)
    {
        // نعتبر أن اللاعب الذي أجاب بشكل صحيح في الجولة الأخيرة هو الفائز
        $lastMove = $game->moves()
            ->where('correct_answer', true)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastMove && $lastMove->player_id === $playerId;
    }

    public function getSpeedRoundInfo($id)
    {
        $game = Game::findOrFail($id);
        
        return response()->json([
            'speed_round_active' => $game->speed_round_activated,
            'speed_round_used' => $game->speed_round_used ?? false,
            'remaining_cells' => $game->getRemainingCells(),
            'available_replacements' => $this->getAvailableReplacements($game)
        ]);
    }

    public function updateSpeedRoundStatus(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        $game->speed_round_activated = $request->input('active', false);
        $game->save();

        return response()->json([
            'success' => true,
            'speed_round_activated' => $game->speed_round_activated
        ]);
    }

    private function getAvailableReplacements($game)
    {
        $board = $game->getBoardArrayAttribute();
        $player = $this->getOrCreatePlayer();
        $currentPlayerId = $player->id;
        $playerSymbol = $game->player1_id === $currentPlayerId ? 'X' : 'O';
        
        $available = [];
        foreach ($board as $position => $symbol) {
            if ($symbol !== null && $symbol !== $playerSymbol) {
                $available[] = $position;
            }
        }
        
        return $available;
    }

    public function forceComputerMove(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        
        if (!$game->isAgainstComputer() || $game->status !== 'active') {
            return response()->json([
                'error' => 'لا يمكن للكمبيوتر اللعب الآن',
                'game_status' => $game->status,
                'is_against_computer' => $game->isAgainstComputer()
            ], 400);
        }

        if ($game->current_turn !== 'player2') {
            return response()->json([
                'error' => 'ليس دور الكمبيوتر الآن',
                'current_turn' => $game->current_turn
            ], 400);
        }

        $result = $this->makeComputerMove($game);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'الكبيوتر قام باللعب',
                'game_id' => $game->id,
                'status' => $game->status
            ]);
        } else {
            return response()->json([
                'error' => 'فشل في جعل الكمبيوتر يلعب'
            ], 500);
        }
    }

    private function makeComputerMove(Game $game)
    {
        try {
            if ($game->status !== 'active') {
                return false;
            }

            $board = $game->getBoardArrayAttribute();
            $availableMoves = $this->getAvailableMoves($board);
            
            if (empty($availableMoves)) {
                return false;
            }

            // اختيار حركة ذكية للكمبيوتر
            $position = $this->chooseComputerMove($board, $availableMoves);
            
            // الحصول على سؤال عشوائي للكمبيوتر
            $question = Question::inRandomOrder()->first();
            if (!$question) {
                $question = Question::create([
                    'question' => 'سؤال الكمبيوتر الافتراضي',
                    'options' => ['الخيار أ', 'الخيار ب', 'الخيار ج', 'الخيار د'],
                    'correct_option' => '0',
                    'difficulty' => 'easy',
                    'category' => 'عام'
                ]);
            }

            // محاكاة إجابة الكمبيوتر على السؤال (75% فرصة إجابة صحيحة)
            $isCorrect = rand(1, 100) <= 75;
            $selectedOption = $isCorrect ? $question->correct_option : (string)rand(0, 3);

            if ($isCorrect) {
                $symbol = 'O'; // الكمبيوتر دائماً O
                $board[$position] = $symbol;
                
                $game->board = json_encode($board);
                $game->current_turn = 'player1'; // العودة للاعب

                // التحقق من الفائز
                $winner = $game->checkWinner();
                if ($winner) {
                    $game->winner = $winner;
                    $game->status = 'completed';
                    $this->updatePlayerStats($game);
                } elseif ($game->isBoardFull()) {
                    $game->status = 'completed';
                    $this->updatePlayerStats($game, true);
                }
            } else {
                // إذا أخطأ الكمبيوتر، يعود الدور للاعب
                $game->current_turn = 'player1';
            }

            // حفظ حركة الكمبيوتر
            $game->moves()->create([
                'player_id' => $game->player1_id,
                'position' => $position,
                'correct_answer' => $isCorrect,
                'question_id' => $question->id,
                'selected_option' => $selectedOption
            ]);

            $game->save();

            // بث تحديث اللعبة بعد حركة الكمبيوتر
            event(new GameUpdated($game));
            
            return true;

        } catch (\Exception $e) {
            Log::error('Error in computer move: ' . $e->getMessage());
            return false;
        }
    }
    
    public function activateSpeedRound($id)
    {
        try {
            $game = Game::findOrFail($id);
            
            if (!$game->speed_round_activated) {
                $game->speed_round_activated = true;
                $game->save();
                
                // بث حدث تفعيل جولة السرعة للجميع
                broadcast(new \App\Events\SpeedRoundActivated($game))->toOthers();
                
                return response()->json([
                    'success' => true,
                    'message' => 'تم تفعيل جولة السرعة'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'جولة السرعة مفعلة مسبقاً'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تفعيل جولة السرعة'
            ], 500);
        }
    }

    public function getGameState($id)
    {
        $game = Game::with(['player1.user', 'player2.user'])
                ->findOrFail($id);

        return response()->json([
            'success'      => true,
            'id'           => $game->id,
            'board'        => $game->getBoardArrayAttribute(),
            'speed_round_activated' => $game->speed_round_activated,
            'speed_round_used' => $game->speed_round_used ?? false,
            'current_turn' => $game->current_turn,
            'status'       => $game->status,
            'winner'       => $game->winner,
            'player1_id'   => $game->player1_id,
            'player2_id'   => $game->player2_id,
            'game_type'    => $game->game_type,
            'remaining_cells' => count($game->getRemainingCells()) // إضافة مهمة
        ]);
    }

    public function acceptInvitation($gameId)
    {
        $game = Game::findOrFail($gameId);
        $user = Auth::user();
        $player = $user->player;

        $game->player2_id = $player->id;
        $game->status = 'active';
        $game->save();
        
        // تهيئة البطاقات للاعب الجديد
        $this->initializePowerUps($game);
        
        Cache::put("invite_handled_{$game->id}_{$user->id}", true, now()->addMinutes(30));

        event(new GameUpdated($game));
        return response()->json(['success' => true]);
    }

    public function clearNotification(Request $request)
    {
        $userId = Auth::id();
        $key = "game_request_$userId";
        $data = Cache::get($key);

        if ($data) {
            // ✅ منع التكرار
            Cache::put("invite_handled_{$data['game_id']}_{$userId}", true, now()->addMinutes(30));
            Cache::forget($key);
        }

        return response()->json(['success' => true]);
    }

    public function restartGame(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        $user = Auth::user();
        $player = $user->player;

        // التحقق من أن اللاعب جزء من اللعبة
        if ($game->player1_id !== $player->id && $game->player2_id !== $player->id) {
            return response()->json(['error' => 'غير مصرح بإعادة تشغيل هذه اللعبة'], 403);
        }

        // إنشاء لعبة جديدة بنفس الإعدادات
        $newGame = Game::create([
            'game_type' => $game->game_type,
            'player1_id' => $game->player1_id,
            'player2_id' => $game->player2_id,
            'status' => 'active',
            'board' => json_encode(array_fill(0, 9, null)),
            'tournament_id' => $game->tournament_id
        ]);
        
        // تهيئة البطاقات للعبة الجديدة
        $this->initializePowerUps($newGame);

        return response()->json([
            'success' => true,
            'game_id' => $newGame->id,
            'message' => 'تم إعادة تشغيل اللعبة بنجاح'
        ]);
    }

    private function getAvailableMoves(array $board): array
    {
        $moves = [];
        foreach ($board as $index => $cell) {
            if ($cell === null) {
                $moves[] = $index;
            }
        }
        return $moves;
    }

    private function chooseComputerMove(array $board, array $availableMoves): int
    {
        // 1. حاول الفوز إذا أمكن
        $winningMove = $this->findWinningMove($board, 'O');
        if ($winningMove !== null) {
            return $winningMove;
        }

        // 2. حاول منع اللاعب من الفوز
        $blockingMove = $this->findWinningMove($board, 'X');
        if ($blockingMove !== null) {
            return $blockingMove;
        }

        // 3. حاول اللعب في المركز إذا كان متاحاً
        if (in_array(4, $availableMoves)) {
            return 4;
        }

        // 4. حاول اللعب في الزوايا
        $corners = [0, 2, 6, 8];
        $availableCorners = array_intersect($corners, $availableMoves);
        if (!empty($availableCorners)) {
            return $availableCorners[array_rand($availableCorners)];
        }

        // 5. اللعب في أي مكان متاح
        return $availableMoves[array_rand($availableMoves)];
    }

    private function findWinningMove(array $board, string $player): ?int
    {
        $winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // صفوف
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // أعمدة
            [0, 4, 8], [2, 4, 6] // أقطار
        ];

        foreach ($winningCombinations as $combination) {
            $values = [
                $board[$combination[0]], 
                $board[$combination[1]], 
                $board[$combination[2]]
            ];
            
            // عد عدد خلايا اللاعب والخلايا الفارغة
            $playerCells = array_filter($values, fn($cell) => $cell === $player);
            $emptyCells = array_keys(array_filter($values, fn($cell) => $cell === null));
            
            if (count($playerCells) === 2 && count($emptyCells) === 1) {
                // وجدنا فرصة للفوز، نرجع الخلية الفارغة
                $emptyIndex = $emptyCells[0];
                return $combination[$emptyIndex];
            }
        }

        return null;
    }

    private function updatePlayerStats(Game $game, bool $isDraw = false): void
    {
        $p1 = $game->player1;                       // دائماً موجود
        $p2 = $game->player2;                       // قد يكون null (ضد الكمبيوتر)

        // 1) عدد الألعاب
        $p1->increment('games_played');

        // 2) التعادل
        if ($isDraw) {
            $p1->increment('games_drawn');
            $p1->increment('points', 5);

            if ($p2) {                              // لا يوجد كمبيوتر
                $p2->increment('games_drawn');
                $p2->increment('points', 5);
                $p2->save();
            }
            $p1->save();
            $game->status = 'completed';
            $game->completed_at = now();
            $game->save();
            return;
        }

        // 3) فوز / خسارة
        $winnerPlayer = $game->winner === 'X' ? $p1 : $p2;   // قد يكون null (إذا فاز الكمبيوتر)
        $loserPlayer  = $game->winner === 'X' ? $p2 : $p1;   // قد يكون null

        // أ) الفائز
        if ($winnerPlayer) {                    // إنسان فاز
            $winnerPlayer->increment('games_won');
            $winnerPlayer->increment('points', 20);
            $winnerPlayer->save();
        }

        // ب) الخاسر
        if ($loserPlayer) {                     // إنسان خسر
            $loserPlayer->increment('games_lost');
            $loserPlayer->increment('points', 2);
            $loserPlayer->save();
        }

        $p1->save();
        $game->status = 'completed';                         // حفظ games_played للإنسان
        $game->completed_at = now();
        $game->save();
    }

    private function getRankTitle($rank)
    {
        if ($rank <= 3) return 'بطل';
        if ($rank <= 10) return 'محترف';
        if ($rank <= 25) return 'متقدم';
        if ($rank <= 50) return 'مبتكر';
        return 'مبتدئ';
    }

    private function getPlayerAchievements($player)
    {
        $achievements = [
            [
                'icon' => 'trophy',
                'title' => 'الفوز الأول',
                'description' => 'فاز في أول لعبة',
                'unlocked' => $player->games_won > 0
            ],
            [
                'icon' => 'star',
                'title' => '100 نقطة',
                'description' => 'جمع 100 نقطة',
                'unlocked' => $player->points >= 100
            ],
            [
                'icon' => 'bolt',
                'title' => 'سريع',
                'description' => 'فاز في 50 لعبة',
                'unlocked' => $player->games_won >= 50
            ],
            [
                'icon' => 'users',
                'title' => 'منافس',
                'description' => 'لعب مع 100 منافس',
                'unlocked' => $player->games_played >= 100
            ],
            [
                'icon' => 'star',
                'title' => '250 نقطة',
                'description' => 'جمع 250 نقطة',
                'unlocked' => $player->points >= 250
            ],
            [
                'icon' => 'bolt',
                'title' => 'سريع',
                'description' => 'فاز في 200 لعبة',
                'unlocked' => $player->games_won >= 200
            ],
            [
                'icon' => 'users',
                'title' => 'منافس',
                'description' => 'لعب مع 500 منافس',
                'unlocked' => $player->games_played >= 500
            ],
            [
                'icon' => 'star',
                'title' => '500 نقطة',
                'description' => 'جمع 500 نقطة',
                'unlocked' => $player->points >= 500
            ],
            [
                'icon' => 'bolt',
                'title' => 'سريع',
                'description' => 'فاز في 600 لعبة',
                'unlocked' => $player->games_won >= 600
            ],
            [
                'icon' => 'users',
                'title' => 'منافس',
                'description' => 'لعب مع 1000 منافس',
                'unlocked' => $player->games_played >= 1000
            ]
        ];

        return $achievements;
    }

    public function getQuestionsByCategory($category)
    {
        $questions = Question::where('category', $category)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'questions' => $questions
        ]);
    }

    public function getQuestionsStats()
    {
        $stats = Question::select('category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function forfeit(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        
        // تأكد أن اللعبة لم تنتهِ
        if ($game->status === 'completed') {
            return response()->json(['message' => 'Game already finished'], 422);
        }

        $me        = Auth::user()->player;
        $opponent  = $game->player1_id === $me->id ? $game->player2 : $game->player1;

        // نحدد الفائز والخاسر
        $winnerSymbol = $game->player1_id === $me->id ? 'O' : 'X';
        $loserSymbol  = $winnerSymbol === 'X' ? 'O' : 'X';

        // نغيّر الحالة ونحفظ النتيجة
        $game->update([
            'status'  => 'completed',
            'winner'  => $winnerSymbol,
        ]);

        // نقاط
        if ($opponent) {                       // لو فيه خصم حقيقي
             $opponent->increment('points',       20);
            $opponent->increment('games_won',     1);
            $opponent->increment('games_played',  1);
            $opponent->touch(); 
        }
        // الخارج لا يأخذ شيئاً (0 نقاط)

        // بث الحدث للخصم
        broadcast(new GameUpdated($game))->toOthers();

        return response()->json(['success' => true]);
    }

    public function debugGame($id)
    {
        $game = Game::findOrFail($id);
        
        return response()->json([
            'game_id' => $game->id,
            'board' => $game->getBoardArrayAttribute(),
            'current_turn' => $game->current_turn,
            'status' => $game->status,
            'player1_id' => $game->player1_id,
            'player2_id' => $game->player2_id,
            'is_against_computer' => $game->isAgainstComputer(),
            'moves_count' => $game->moves()->count(),
            'last_move' => $game->moves()->latest()->first()
        ]);
    }

    public function getOnlinePlayers()
    {
        try {
            // اللاعبين المتصلين خلال آخر 5 دقائق
            $onlinePlayers = Player::with('user')
                ->whereHas('user', function($query) {
                    $query->where('last_seen', '>', now()->subMinutes(5));
                })
                ->where('user_id', '!=', Auth::id())
                ->where(function($query) {
                    // استبعاد اللاعبين في ألعاب نشطة
                    $query->whereNotIn('id', function($subquery) {
                        $subquery->select('player1_id')
                                ->from('games')
                                ->where('status', 'active')
                                ->union(
                                    $subquery->newQuery()
                                            ->select('player2_id')
                                            ->from('games')
                                            ->where('status', 'active')
                                            ->whereNotNull('player2_id')
                                );
                    });
                })
                ->orderBy('points', 'desc')
                ->limit(20)
                ->get();

            return response()->json($onlinePlayers);

        } catch (\Exception $e) {
            \Log::error('Error fetching online players: ' . $e->getMessage());
            
            // في حالة الخطأ، أرجع لاعبين افتراضيين للتجربة
            return response()->json($this->getFallbackPlayers());
        }
    }
    
    private function getFallbackPlayers()
    {
        // لاعبين افتراضيين للتجربة
        return [
            (object)[
                'id' => 9991,
                'points' => 1500,
                'win_rate' => 75,
                'user' => (object)[
                    'name' => 'أحمد المحترف',
                    'avatar' => null
                ]
            ],
            (object)[
                'id' => 9992,
                'points' => 1200,
                'win_rate' => 65,
                'user' => (object)[
                    'name' => 'سارة الذكية',
                    'avatar' => null
                ]
            ],
            (object)[
                'id' => 9993,
                'points' => 900,
                'win_rate' => 55,
                'user' => (object)[
                    'name' => 'خالد السريع',
                    'avatar' => null
                ]
            ]
        ];
    }

    public function leaveGame(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        $user = Auth::user();
        
        // بث حدث خروج اللاعب
        broadcast(new GameUpdated($game))->toOthers();
        
        return response()->json(['success' => true]);
    }

    private function getActiveTournaments()
    {
        return \App\Models\Tournament::whereIn('status', ['active', 'upcoming', 'registration'])
            ->withCount(['participants'])
            ->orderBy('start_date')
            ->limit(3)
            ->get()
            ->map(function($tournament) {
                $statusInfo = $this->getTournamentStatusInfo($tournament);
                
                return (object)[
                    'name' => $tournament->name,
                    'status_text' => $statusInfo['text'],
                    'status_color' => $statusInfo['color'],
                    'participants_count' => $tournament->participants_count,
                    'current_round' => $this->getCurrentRound($tournament),
                    'prize' => $tournament->prize_points,
                    'time_remaining' => $this->getTimeRemaining($tournament)
                ];
            });
    }

    private function getTournamentStatusInfo($tournament)
    {
        $now = now();
        
        if ($tournament->status === 'active') {
            return ['text' => 'جاري', 'color' => 'green'];
        }
        
        if ($tournament->status === 'upcoming' && $tournament->start_date > $now) {
            return ['text' => 'قريب', 'color' => 'yellow'];
        }
        
        if ($tournament->status === 'registration') {
            return ['text' => 'مفتوح', 'color' => 'blue'];
        }
        
        return ['text' => 'مغلق', 'color' => 'gray'];
    }

    private function getCurrentRound($tournament)
    {
        $rounds = ['الجولة 1', 'الجولة 2', 'الجولة 3', 'الجولة 4', 'ربع النهائي', 'نصف النهائي', 'النهائي'];
        
        if ($tournament->status === 'active') {
            $progress = min(
                count($rounds) - 1, 
                floor(($tournament->participants_count / 32) * count($rounds))
            );
            return $rounds[$progress];
        }
        
        return 'يبدأ قريباً';
    }

    private function getTimeRemaining($tournament)
    {
        $now = now();
        
        if ($tournament->status === 'active' && $tournament->end_date) {
            $diff = $now->diff($tournament->end_date);
            return $diff->d . 'd ' . $diff->h . 'h';
        }
        
        if ($tournament->status === 'registration' && $tournament->registration_end) {
            $diff = $now->diff($tournament->registration_end);
            return 'ينتهي التسجيل: ' . $diff->d . 'd';
        }
        
        if ($tournament->start_date > $now) {
            $diff = $now->diff($tournament->start_date);
            return 'يبدأ: ' . $diff->d . 'd ' . $diff->h . 'h';
        }
        
        return 'مستمر';
    }

    private function getPlayerPowerUps($playerId)
    {
        // يمكنك استبدال هذا ببيانات حقيقية من قاعدة البيانات
        return [
            'double_move' => [
                'name' => 'حركة مزدوجة',
                'used' => false,
                'icon' => '⚡',
                'cost' => 10,
                'description' => 'العب مرتين متتاليتين'
            ],
            'block_opponent' => [
                'name' => 'حجب الخصم',
                'used' => false,
                'icon' => '🚫',
                'cost' => 5,
                'description' => 'احجب دور الخصم التالي'
            ],
            'free_placement' => [
                'name' => 'وضع حر',
                'used' => false,
                'icon' => '🎯',
                'cost' => 10,
                'description' => 'ضع علامتك في أي مكان'
            ],
            'shuffle_board' => [
                'name' => 'تبديل اللوحة',
                'used' => false,
                'icon' => '🔀',
                'cost' => 7,
                'description' => 'بدل مواقع العلامات'
            ]
        ];
    }

    public function usePowerUp(Request $request, Game $game)
    {
        try {
            $player = $this->getOrCreatePlayer();
            $powerUpKey = $request->power_up;
            $cost = $request->cost;

            // التحقق من أن اللاعب مشارك في اللعبة
            if ($game->player1_id !== $player->id && $game->player2_id !== $player->id) {
                return response()->json(['error' => 'غير مصرح باستخدام البطاقة'], 403);
            }

            // التحقق من أن اللعبة نشطة
            if ($game->status !== 'active') {
                return response()->json(['error' => 'اللعبة منتهية'], 400);
            }

            // التحقق من النقاط
            if ($player->points < $cost) {
                return response()->json(['error' => 'نقاط غير كافية'], 400);
            }

            // خصم النقاط
            $player->points -= $cost;
            $player->save();

            // تطبيق تأثير البطاقة
            $result = $this->applyPowerUpEffect($game, $powerUpKey, $request->position, $player);

            if (!$result['success']) {
                // إرجاع النقاط في حالة الخطأ
                $player->points += $cost;
                $player->save();
                return response()->json(['error' => $result['message']], 400);
            }

            // ✅ تحديث حالة اللعبة بعد استخدام البطاقة
            $game->refresh();
            
            // ✅ التحقق من الفائز بعد استخدام البطاقة
            $winner = $game->checkWinner();
            if ($winner) {
                $game->winner = $winner;
                $game->status = 'completed';
                $this->updatePlayerStats($game);
            } elseif ($game->isBoardFull()) {
                $game->status = 'completed';
                $this->updatePlayerStats($game, true);
            }
            
            $game->save();

            // ✅ بث تحديث اللعبة للطرف الآخر
            broadcast(new GameUpdated($game))->toOthers();

            // Get updated power-ups after use
            $powerUps = $this->getPlayerPowerUps($player->id);
            $powerUps[$powerUpKey]['used'] = true;
            
            return response()->json([
                'message' => 'تم استخدام البطاقة بنجاح',
                'player_points' => $player->points,
                'powerUps' => $powerUps,
                'game' => [
                    'id' => $game->id,
                    'board' => $game->getBoardArrayAttribute(),
                    'current_turn' => $game->current_turn,
                    'status' => $game->status,
                    'winner' => $game->winner,
                    'player1_id' => $game->player1_id,
                    'player2_id' => $game->player2_id,
                    'speed_round_activated' => $game->speed_round_activated,
                    'speed_round_used' => $game->speed_round_used ?? false,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in usePowerUp: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'حدث خطأ في استخدام البطاقة'
            ], 500);
        }
    }

    public function getPowerUps(Game $game)
    {
        try {
            $player = $this->getOrCreatePlayer();

            // التحقق من أن اللاعب مشارك في اللعبة
            if ($game->player1_id !== $player->id && $game->player2_id !== $player->id) {
                return response()->json([
                    'error' => 'غير مصرح بالوصول إلى البطاقات'
                ], 403);
            }

            // إرجاع البطاقات الافتراضية (يمكن استبدالها ببيانات من قاعدة البيانات)
            $powerUps = [
                'double_move' => [
                    'name' => 'حركة مزدوجة',
                    'used' => false,
                    'icon' => '⚡',
                    'cost' => 10,
                    'description' => 'العب مرتين متتاليتين'
                ],
                'block_opponent' => [
                    'name' => 'حجب الخصم',
                    'used' => false,
                    'icon' => '🚫',
                    'cost' => 5,
                    'description' => 'احجب دور الخصم التالي'
                ],
                'free_placement' => [
                    'name' => 'وضع حر',
                    'used' => false,
                    'icon' => '🎯',
                    'cost' => 10,
                    'description' => 'ضع علامتك في أي مكان'
                ],
                'shuffle_board' => [
                    'name' => 'تبديل اللوحة',
                    'used' => false,
                    'icon' => '🔀',
                    'cost' => 7,
                    'description' => 'بدل مواقع العلامات'
                ]
            ];

            return response()->json([
                'powerUps' => $powerUps,
                'player_points' => $player->points
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getPowerUps: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'حدث خطأ في تحميل البطاقات'
            ], 500);
        }
    }
    
    private function applyPowerUpEffect(Game $game, $powerUpKey, $position, $player)
    {
        $board = $game->getBoardArrayAttribute();
        $playerSymbol = $game->player1_id === $player->id ? 'X' : 'O';

        switch ($powerUpKey) {
            case 'double_move':
                // الحركة المزدوجة - يبقى الدور مع اللاعب
                Cache::put("double_move_{$game->id}", $player->id, now()->addMinutes(5));
                return ['success' => true, 'message' => '🎯 تم تفعيل الحركة المزدوجة! يمكنك اللعب مرتين'];

            case 'block_opponent':
                // حجب الخصم - يبقى الدور مع اللاعب
                $opponentId = $game->player1_id === $player->id ? $game->player2_id : $game->player1_id;
                Cache::put("blocked_{$game->id}", $opponentId, now()->addMinutes(5));
                
                // تغيير الدور للاعب الحالي مرة أخرى
                $game->current_turn = $game->current_turn === 'player1' ? 'player1' : 'player2';
                $game->save();
                
                return ['success' => true, 'message' => '🚫 تم حجب الخصم! دورك مرة أخرى'];

            case 'free_placement':
                // الوضع الحر
                if ($position === null) {
                    return ['success' => false, 'message' => 'يجب اختيار موقع للوضع الحر'];
                }

                // السماح بالوضع في أي مكان
                $board[$position] = $playerSymbol;
                $game->board = json_encode($board);
                
                // ✅ التحقق من الفوز فوراً بعد الوضع الحر
                $winner = $game->checkWinner();
                if ($winner) {
                    $game->winner = $winner;
                    $game->status = 'completed';
                    $this->updatePlayerStats($game);
                } elseif ($game->isBoardFull()) {
                    $game->status = 'completed';
                    $this->updatePlayerStats($game, true);
                } else {
                    // تغيير الدور للخصم فقط إذا لم تنته اللعبة
                    $game->current_turn = $game->player1_id === $player->id ? 'player2' : 'player1';
                }
                
                $game->save();
                return ['success' => true, 'message' => '🎁 تم وضع علامتك في الموقع المحدد!'];

            case 'shuffle_board':
                // تبديل اللوحة
                $this->shuffleBoard($game);
                
                // ✅ التحقق من الفوز بعد تبديل اللوحة
                $winner = $game->checkWinner();
                if ($winner) {
                    $game->winner = $winner;
                    $game->status = 'completed';
                    $this->updatePlayerStats($game);
                }
                
                $game->save();
                return ['success' => true, 'message' => '🔀 تم تبديل اللوحة! الوضع تغير كلياً'];

            default:
                return ['success' => false, 'message' => 'البطاقة غير معروفة'];
        }
    }

    private function activateDoubleMove($game, $player)
    {
        // تخزين حالة الحركة المزدوجة
        Cache::put("double_move_{$game->id}", $player->id, now()->addMinutes(5));
        
        return [
            'success' => true,
            'message' => '🎯 تم تفعيل الحركة المزدوجة! يمكنك اللعب مرتين'
        ];
    }

    private function activateBlockOpponent($game, $player)
    {
        $opponentId = $game->player1_id === $player->id ? $game->player2_id : $game->player1_id;
        
        // تخزين حالة الحجب
        Cache::put("blocked_{$game->id}", $opponentId, now()->addMinutes(5));
        
        // تغيير الدور للاعب الحالي مرة أخرى
        $game->current_turn = $game->current_turn === 'player1' ? 'player1' : 'player2';
        $game->save();
        
        return [
            'success' => true,
            'message' => '🚫 تم حجب الخصم! دورك مرة أخرى'
        ];
    }

    private function activateFreePlacement($game, $player, $position)
    {
        $board = $game->getBoardArrayAttribute();
        $playerSymbol = $game->player1_id === $player->id ? 'X' : 'O';
        
        // وضع العلامة في أي موقع (حتى لو كان محجوزاً)
        $board[$position] = $playerSymbol;
        $game->board = json_encode($board);
        
        // التحقق من الفائز
        if ($winner = $game->checkWinner()) {
            $game->winner = $winner;
            $game->status = 'completed';
            $this->updatePlayerStats($game);
        } elseif ($game->isBoardFull()) {
            $game->status = 'completed';
            $this->updatePlayerStats($game, true);
        } else {
            // تغيير الدور
            $game->current_turn = $game->current_turn === 'player1' ? 'player2' : 'player1';
        }
        
        $game->save();
        
        return [
            'success' => true,
            'message' => '🎁 تم وضع علامتك في الموقع المحدد!'
        ];
    }

    private function activateShuffleBoard($game, $player)
    {
        $board = $game->getBoardArrayAttribute();
        
        // جمع العلامات غير الفارغة
        $symbols = array_filter($board);
        
        // خلط العلامات
        shuffle($symbols);
        
        // إعادة بناء اللوحة
        $newBoard = [];
        $symbolIndex = 0;
        
        for ($i = 0; $i < 9; $i++) {
            if ($board[$i] !== null) {
                $newBoard[$i] = $symbols[$symbolIndex];
                $symbolIndex++;
            } else {
                $newBoard[$i] = null;
            }
        }
        
        $game->board = json_encode($newBoard);
        $game->save();
        
        return [
            'success' => true,
            'message' => '🔀 تم تبديل اللوحة! الوضع تغير كلياً'
        ];
    }

    private function shuffleBoard(Game $game)
    {
        $board = $game->getBoardArrayAttribute();
        
        // تصفية الخلايا غير الفارغة
        $symbols = array_filter($board, function($cell) {
            return $cell !== null;
        });

        // خلط العلامات
        $keys = array_keys($symbols);
        $values = array_values($symbols);
        shuffle($values);

        // إعادة بناء اللوحة
        $newBoard = $board;
        foreach ($keys as $index => $key) {
            $newBoard[$key] = $values[$index];
        }

        $game->board = json_encode($newBoard);
        $game->save();
    }

    public function checkPowerUpEffects($game)
    {
        // التحقق من الحجب
        $blockedPlayer = Cache::get("blocked_{$game->id}");
        if ($blockedPlayer) {
            $currentPlayer = $game->current_turn === 'player1' ? $game->player1_id : $game->player2_id;
            if ($currentPlayer == $blockedPlayer) {
                // تخطي دور اللاعب المحجوب
                $game->current_turn = $game->current_turn === 'player1' ? 'player2' : 'player1';
                $game->save();
                Cache::forget("blocked_{$game->id}");
                
                return ['blocked' => true, 'message' => '⏭️ تم تخطي دور اللاعب المحجوب'];
            }
        }

        // التحقق من الحركة المزدوجة
        $doubleMovePlayer = Cache::get("double_move_{$game->id}");
        if ($doubleMovePlayer) {
            $currentPlayer = $game->current_turn === 'player1' ? $game->player1_id : $game->player2_id;
            if ($currentPlayer == $doubleMovePlayer) {
                // إبقاء الدور مع نفس اللاعب
                $game->current_turn = $game->current_turn;
                $game->save();
                Cache::forget("double_move_{$game->id}");
                
                return ['double_move' => true, 'message' => '⚡ الحركة المزدوجة مفعلة! دورك مرة أخرى'];
            }
        }
        
        return ['blocked' => false, 'double_move' => false];
    }
}