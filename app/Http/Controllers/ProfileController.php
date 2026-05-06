<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        // عدد الإجابات الصحيحة
        $correctAnswers = DB::table('game_moves')
            ->where('player_id', $user->player?->id)
            ->where('correct_answer', true)
            ->count();

        // الإحصائيات الأسبوعية
        $weeklyStats = \App\Services\PlayerStatsService::getWeeklyStats($user->id);

        // آخر 5 ألعاب
        $recentGames = \App\Models\Game::with(['player1.user','player2.user'])
            ->where(function($q) use ($user) {
                $q->where('player1_id', $user->player?->id)
                ->orWhere('player2_id', $user->player?->id);
            })
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        // دمج الإنجازات
        $player = $user->player;
        $achievements = [
            [
                'icon' => 'trophy',
                'title' => 'الفوز الأول',
                'description' => 'فاز في أول لعبة',
                'unlocked' => $player && $player->games_won > 0
            ],
            [
                'icon' => 'star',
                'title' => '100 نقطة',
                'description' => 'جمع 100 نقطة',
                'unlocked' => $player && $player->points >= 100
            ],
            [
                'icon' => 'bolt',
                'title' => 'سريع',
                'description' => 'فاز في 50 لعبة',
                'unlocked' => $player && $player->games_won >= 50
            ],
            [
                'icon' => 'users',
                'title' => 'منافس',
                'description' => 'لعب مع 100 منافس',
                'unlocked' => $player && $player->games_played >= 100
            ],
            [
                'icon' => 'star',
                'title' => '250 نقطة',
                'description' => 'جمع 250 نقطة',
                'unlocked' => $player && $player->points >= 250
            ],
            [
                'icon' => 'bolt',
                'title' => 'سريع',
                'description' => 'فاز في 200 لعبة',
                'unlocked' => $player && $player->games_won >= 200
            ],
            [
                'icon' => 'users',
                'title' => 'منافس',
                'description' => 'لعب مع 500 منافس',
                'unlocked' => $player && $player->games_played >= 500
            ],
            [
                'icon' => 'star',
                'title' => '500 نقطة',
                'description' => 'جمع 500 نقطة',
                'unlocked' => $player && $player->points >= 500
            ],
            [
                'icon' => 'bolt',
                'title' => 'سريع',
                'description' => 'فاز في 600 لعبة',
                'unlocked' => $player && $player->games_won >= 600
            ],
            [
                'icon' => 'users',
                'title' => 'منافس',
                'description' => 'لعب مع 1000 منافس',
                'unlocked' => $player && $player->games_played >= 1000
            ]
        ];

        // حساب الترتيب العام وترتيب الدولة
        $globalRank = $this->getGlobalRank($user);
        $countryRank = $this->getCountryRank($user);

        return view('profile.show', compact(
            'user','correctAnswers','weeklyStats','recentGames','achievements','globalRank','countryRank'
        ));
    }

    private function getGlobalRank(User $user)
    {
        if (!$user->player) {
            return 'N/A';
        }

        // طريقة بديلة لحساب الترتيب
        $rank = DB::table('players')
            ->select('user_id')
            ->whereNotNull('user_id')
            ->orderBy('points', 'DESC')
            ->get()
            ->search(function ($item) use ($user) {
                return $item->user_id == $user->id;
            });

        return $rank !== false ? $rank + 1 : 'N/A';
    }

    private function getCountryRank(User $user)
    {
        if (!$user->country || !$user->player) {
            return 'N/A';
        }

        // طريقة بديلة لحساب الترتيب حسب الدولة
        $playersInCountry = DB::table('users')
            ->join('players', 'users.id', '=', 'players.user_id')
            ->where('users.country', $user->country)
            ->whereNotNull('players.user_id')
            ->select('users.id', 'players.points')
            ->orderBy('players.points', 'DESC')
            ->get();

        $rank = $playersInCountry->search(function ($item) use ($user) {
            return $item->id == $user->id;
        });

        return $rank !== false ? $rank + 1 : 'N/A';
    }

    public function shareCard(User $user)
    {
        $globalRank = $this->getGlobalRank($user);
        $countryRank = $this->getCountryRank($user);
        
        $player = $user->player;
        $achievements = $player ? [
            'points' => $player->points,
            'games_won' => $player->games_won,
            'games_played' => $player->games_played,
            'win_rate' => $player->win_rate
        ] : null;

        return view('profile.share-card', compact('user', 'globalRank', 'countryRank', 'achievements'));
    }

    public function uploadAvatar(User $user, Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048'
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json(['avatar' => Storage::url($path)]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'country' => 'required|string|max:100',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'تم تحديث البيانات بنجاح']);
    }
}