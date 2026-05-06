<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Events\GameUpdated;
use App\Models\Game;
use App\Models\GameInvitation;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});
Route::middleware('auth')->get('/notifications/check', function () {
    $userId = Auth::id();
    $key = "game_request_$userId";
    $data = Cache::get($key);

    // ✅ منع التكرار: لو الدعوة سبق وتمّت معالجتها
    if ($data) {
        $handled = Cache::get("invite_handled_{$data['game_id']}_{$userId}");
        if ($handled) {
            Cache::forget($key); // امسح الدعوة الأصلية أيضاً
            return response()->json(['game_request' => false]);
        }
    }

    return response()->json([
        'game_request' => (bool) $data,
        'from_user' => $data['from_user'] ?? null,
        'game_id' => $data['game_id'] ?? null,
    ]);
});

Route::middleware('auth')->post('/notifications/clear', function () {
    $userId = Auth::id();
    $key = "game_request_$userId";
    Cache::forget($key);
    return response()->json(['success' => true]);
});
Route::middleware(['auth'])->group(function () {
    // Dashboard and Game Routes
    Route::get('/dashboard', [GameController::class, 'index'])->name('dashboard');
    Route::get('/game/{id}', [GameController::class, 'show'])->name('game.show');
    Route::post('/game', [GameController::class, 'create'])->name('game.create');
    Route::get('/game/{id}/state', [GameController::class, 'getGameState'])->name('game.state');
    Route::post('/game/{id}/accept', [GameController::class, 'acceptInvitation'])->name('game.accept');
    Route::post('/game/invite/{Id}/cancel', [GameController::class, 'cancelInvite'])->name('game.invite.cancel');
    Route::post('/game/{id}/move', [GameController::class, 'makeMove'])->name('game.move');
    Route::post('/game/{id}/computer-move', [GameController::class, 'forceComputerMove'])->name('game.computer.move');
    Route::get('/game/{id}/state', [GameController::class, 'getGameState'])->name('game.state');
    Route::post('/game/{id}/accept', [GameController::class, 'acceptInvitation'])->name('game.accept');
    Route::post('/game/{id}/restart', [GameController::class, 'restartGame'])->name('game.restart');
    Route::get('/game/{id}/debug', [GameController::class, 'debugGame'])->name('game.debug');
    Route::post('/game/{id}/speed-round-move', [GameController::class, 'speedRoundMove'])->name('game.speed-round.move');
    Route::post('/game/{id}/speed-round-status', [GameController::class, 'updateSpeedRoundStatus'])->name('game.speed-round.status');
    Route::get('/game/{id}/speed-round-info', [GameController::class, 'getSpeedRoundInfo'])->name('game.speed-round.info');
    Route::post('/game/{id}/skip-replace', [GameController::class, 'skipReplace'])->name('game.skip-replace');
    Route::get('games/history/{user}', [GameController::class, 'history'])->name('game.history');
    Route::post('/game/{id}/activate-speed-round', [GameController::class, 'activateSpeedRound']);
    Route::get('/game/{game}/powerups', [GameController::class, 'getPowerUps'])->name('game.powerups');
    Route::post('/game/{game}/use-powerup', [GameController::class, 'usePowerUp'])->name('game.usePowerUp');
    Route::post('/game/{id}/leave', [GameController::class, 'leaveGame'])->name('game.leave');
    
    Route::get('/games', [GameController::class, 'games'])->name('games');
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments');
    Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard');
    
    Route::get('/players/online', [GameController::class, 'getOnlinePlayers'])->name('players.online');

    Route::get('profile/{user}', [ProfileController::class,'show'])->name('profile.show');
    Route::patch('profile/{user}', [ProfileController::class,'update'])->name('profile.update');
    Route::post('profile/{user}/avatar', [ProfileController::class,'uploadAvatar'])->name('profile.avatar');
    Route::get('/profile/{user}/share', [ProfileController::class, 'shareCard'])->name('profile.share');
    // routes/web.php
    Route::get('/api/online-players', function() {
        // جلب اللاعبين المتصلين (آخر 5 دقائق)
        $onlinePlayers = \App\Models\User::where('last_seen', '>=', now()->subMinutes(5))
            ->where('id', '!=', auth()->id())
            ->with('player')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'points' => $user->player->points ?? 0,
                    'is_online' => $user->last_seen >= now()->subMinutes(2)
                ];
            });

        return response()->json([
            'success' => true,
            'players' => $onlinePlayers
        ]);
    });
});

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/my-invitations', [InvitationController::class, 'getMyInvitations']);
    Route::get('/all-invitations', [InvitationController::class, 'getAllInvitations']);
    Route::get('/sent-invitations', [InvitationController::class, 'getSentInvitations']);
    Route::get('/simple-invitations', [InvitationController::class, 'getSimpleInvitations']);
    Route::get('/invitation-status/{invitation}', [InvitationController::class, 'getInvitationStatus']);
    Route::get('/check-accepted-invitations', [InvitationController::class, 'checkAcceptedInvitations']);
    Route::post('/send-invitation', [InvitationController::class, 'sendInvitation']);
    Route::post('/accept-invitation', [InvitationController::class, 'acceptInvitation']);
    Route::post('/reject-invitation', [InvitationController::class, 'rejectInvitation']);

    // اللاعبين
    Route::get('/online-players', [PlayerController::class, 'getOnlinePlayers']);
    
    // الإحصائيات الحية
    Route::get('/live-stats', [GameController::class, 'getLiveStats']);
});

Broadcast::routes(['middleware' => ['auth']]);
// Questions API
Route::get('/questions/random', [QuestionController::class, 'random'])->name('questions.random');
Route::post('/questions/create-sample', [QuestionController::class, 'createSampleQuestions'])->name('questions.create-sample');

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/games', [AdminController::class, 'games'])->name('admin.games');
    Route::get('/admin/players', [AdminController::class, 'players'])->name('admin.players');
    Route::get('/admin/game/{id}', [AdminController::class, 'viewGame'])->name('admin.game.view');
    Route::get('/admin/active-humans', [AdminController::class, 'activeHumans'])->name('admin.active.humans');
});
Route::post('/game/{game}/forfeit', [GameController::class, 'forfeit'])
      ->middleware(['auth']);
      
Route::middleware('auth')->post('/game/{game}/broadcast-refresh', function (Game $game) {
    broadcast(new GameUpdated($game))->toOthers();
    return response()->json(['success' => true]);
});


require __DIR__.'/auth.php';