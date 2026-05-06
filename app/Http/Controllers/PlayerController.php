<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function getOnlinePlayers()
    {
        try {
            // محاكاة بيانات اللاعبين المتصلين
            $onlinePlayers = User::where('id', '!=', Auth::id())
                ->inRandomOrder()
                ->limit(8)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'points' => $user->player->points ?? rand(100, 2000),
                        'is_online' => true
                    ];
                });

            return response()->json([
                'success' => true,
                'players' => $onlinePlayers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching online players'
            ], 500);
        }
    }
}