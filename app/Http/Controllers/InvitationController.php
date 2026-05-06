<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InvitationController extends Controller
{
    public function getAllInvitations()
    {
        try {
            $user = Auth::user();

            // الدعوات الواردة
            $receivedInvitations = Invitation::with(['fromUser', 'toUser'])
                ->where('to_user_id', $user->id)
                ->where('status', 'pending')
                ->get()
                ->map(function ($invitation) {
                    return [
                        'id' => $invitation->id,
                        'type' => 'received',
                        'from_user' => [
                            'name' => $invitation->fromUser?->name ?? 'غير معروف',
                            'id' => $invitation->fromUser?->id ?? null,
                        ],
                        'message' => $invitation->message,
                        'status' => $invitation->status,
                        'time_remaining' => '5 دقائق'
                    ];
                });

            // الدعوات الصادرة
            $sentInvitations = Invitation::with(['fromUser', 'toUser'])
                ->where('from_user_id', $user->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->get()
                ->map(function ($invitation) {
                    return [
                        'id' => $invitation->id,
                        'type' => 'sent',
                        'to_user' => [
                            'name' => $invitation->toUser?->name ?? 'غير معروف',
                            'id' => $invitation->toUser?->id ?? null,
                        ],
                        'message' => $invitation->message,
                        'status' => $invitation->status,
                        'time_remaining' => '5 دقائق'
                    ];
                });

            return response()->json([
                'success' => true,
                'invitations' => $receivedInvitations->merge($sentInvitations)
            ]);

        } catch (\Throwable $e) {
            \Log::error('getAllInvitations Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ داخلي',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMyInvitations()
    {
        try {
            $user = Auth::user();

            $invitations = Invitation::with('fromUser')
                ->where('to_user_id', $user->id)
                ->where('status', 'pending')
                ->get()
                ->map(function($invitation) {
                    return [
                        'id' => $invitation->id,
                        'from_user' => [
                            'name' => $invitation->fromUser->name ?? 'غير معروف',
                            'id' => $invitation->fromUser->id ?? null
                        ],
                        'message' => $invitation->message,
                        'status' => $invitation->status,
                        'time_remaining' => '5 دقائق'
                    ];
                });

            return response()->json([
                'success' => true,
                'invitations' => $invitations
            ]);

        } catch (\Exception $e) {
            \Log::error('getMyInvitations Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ داخلي',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSentInvitations()
    {
        try {
            $user = Auth::user();
            
            $invitations = Invitation::with('toUser')
                ->where('from_user_id', $user->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->get()
                ->map(function($invitation) {
                    return [
                        'id' => $invitation->id,
                        'to_user' => [
                            'name' => $invitation->toUser->name,
                            'id' => $invitation->toUser->id
                        ],
                        'message' => $invitation->message,
                        'status' => $invitation->status,
                        'created_at' => $invitation->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'invitations' => $invitations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sent invitations'
            ], 500);
        }
    }

    public function getSimpleInvitations()
    {
        try {
            $user = Auth::user();
            
            $invitations = Invitation::with('fromUser')
                ->where('to_user_id', $user->id)
                ->where('status', 'pending')
                ->get()
                ->map(function($invitation) {
                    return [
                        'id' => $invitation->id,
                        'from_user' => [
                            'name' => $invitation->fromUser->name,
                            'id' => $invitation->fromUser->id
                        ],
                        'message' => $invitation->message,
                        'status' => $invitation->status
                    ];
                });

            return response()->json([
                'success' => true,
                'invitations' => $invitations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invitations'
            ], 500);
        }
    }

    public function getInvitationStatus($invitationId)
    {
        try {
            $invitation = Invitation::findOrFail($invitationId);
            
            return response()->json([
                'success' => true,
                'status' => $invitation->status,
                'game_id' => $invitation->game_id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invitation not found'
            ], 404);
        }
    }

    public function checkAcceptedInvitations()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            // البحث عن الدعوات التي أرسلها المستخدم وتم قبولها خلال آخر 5 دقائق
            // هذا يضمن أننا نرجع فقط الدعوات المقبولة حديثاً
            $acceptedInvitation = Invitation::where('from_user_id', $user->id)
                ->where('status', 'accepted')
                ->whereNotNull('game_id')
                ->where('updated_at', '>=', now()->subMinutes(5))
                ->latest()
                ->first();
            
            if ($acceptedInvitation && $acceptedInvitation->game_id) {
                // التحقق من أن اللعبة لا تزال نشطة
                $game = Game::find($acceptedInvitation->game_id);
                if ($game && in_array($game->status, ['active', 'waiting'])) {
                    return response()->json([
                        'success' => true,
                        'accepted' => true,
                        'game_id' => $acceptedInvitation->game_id,
                        'invitation_id' => $acceptedInvitation->id
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'accepted' => false
            ]);

        } catch (\Exception $e) {
            \Log::error('checkAcceptedInvitations Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error checking invitations',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function sendInvitation(Request $request)
    {
        try {
            $request->validate([
                'player_id' => 'required|exists:users,id',
                'game_type' => 'required|string',
                'message' => 'nullable|string'
            ]);

            $invitation = Invitation::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => $request->player_id,
                'game_type' => $request->game_type,
                'message' => $request->message ?? 'تحدي XO جديد!',
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invitation sent successfully',
                'invitation_id' => $invitation->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitation'
            ], 500);
        }
    }

    public function acceptInvitation(Request $request)
    {
        try {
            $request->validate([
                'invitation_id' => 'required|exists:invitations,id'
            ]);

            $invitation = Invitation::findOrFail($request->invitation_id);
            
            if ($invitation->to_user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // الحصول على Player IDs من User IDs
            $fromUser = User::findOrFail($invitation->from_user_id);
            $toUser = User::findOrFail($invitation->to_user_id);
            
            // التأكد من وجود Player records
            $fromPlayer = $fromUser->player ?? Player::create([
                'user_id' => $fromUser->id,
                'points' => 0,
                'games_played' => 0,
                'games_won' => 0,
                'games_lost' => 0,
                'games_drawn' => 0
            ]);
            
            $toPlayer = $toUser->player ?? Player::create([
                'user_id' => $toUser->id,
                'points' => 0,
                'games_played' => 0,
                'games_won' => 0,
                'games_lost' => 0,
                'games_drawn' => 0
            ]);

            // إنشاء لعبة جديدة باستخدام Player IDs
            $game = Game::create([
                'player1_id' => $fromPlayer->id,
                'player2_id' => $toPlayer->id,
                'game_type' => $invitation->game_type,
                'status' => 'active',
                'current_turn' => 'player1',
                'board' => json_encode(array_fill(0, 9, null))
            ]);

            // تهيئة البطاقات للاعبين
            $this->initializePowerUps($game);

            // تحديث حالة الدعوة
            $invitation->update([
                'status' => 'accepted',
                'game_id' => $game->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invitation accepted',
                'game_id' => $game->id
            ]);

        } catch (\Exception $e) {
            \Log::error('acceptInvitation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept invitation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rejectInvitation(Request $request)
    {
        try {
            $request->validate([
                'invitation_id' => 'required|exists:invitations,id'
            ]);

            $invitation = Invitation::findOrFail($request->invitation_id);
            
            if ($invitation->to_user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $invitation->update([
                'status' => 'rejected'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invitation rejected'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject invitation'
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
}