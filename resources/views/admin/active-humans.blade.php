@extends('layouts.app')
@section('title', 'المباريات المباشرة - لوحة التحكم')

@section('content')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #1e3a8a 0%, #7e22ce 100%);
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .game-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    .game-card:hover {
        transform: translateY(-5px);
        background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
    }
</style>

<div class="min-h-screen gradient-bg pb-12">
    <div class="container mx-auto px-4 pt-8">
        <!-- Header -->
        <div class="glass-card rounded-3xl p-8 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-purple-500/20 flex items-center justify-center">
                    <i class="fas fa-eye text-2xl text-purple-300"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">المباريات المباشرة</h1>
                    <p class="text-purple-200 mt-1">مراقبة الألعاب النشطة حالياً في النظام</p>
                </div>
            </div>
        </div>

        <!-- Active Games Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($games as $game)
            <a href="{{ route('admin.game.view', $game->id) }}" class="game-card p-6 block">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-green-500/20 flex items-center justify-center">
                            <i class="fas fa-play text-green-300"></i>
                        </div>
                        <div>
                            <span class="text-white font-semibold">لعبة #{{ $game->id }}</span>
                            <div class="text-xs text-gray-400">نشطة الآن</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400">منذ</div>
                        <div class="text-sm text-white">{{ $game->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                <!-- Players -->
                <div class="space-y-4">
                    <!-- Player 1 -->
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl">
                        <div class="flex items-center gap-3">
                            <img src="{{ optional($game->player1->user)->avatar ? Storage::url($game->player1->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($game->player1->user->name) }}" 
                                 class="w-10 h-10 rounded-xl border-2 border-blue-400">
                            <div>
                                <div class="font-semibold text-blue-300">{{ $game->player1->user->name }}</div>
                                <div class="text-xs text-gray-400">X</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-white">{{ $game->player1->points }} نقطة</div>
                            <div class="text-xs text-gray-400">{{ $game->player1->win_rate }}% فوز</div>
                        </div>
                    </div>

                    <!-- VS Separator -->
                    <div class="flex items-center justify-center">
                        <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <span class="text-purple-300 text-sm font-bold">VS</span>
                        </div>
                    </div>

                    <!-- Player 2 -->
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl">
                        <div class="flex items-center gap-3">
                            @if($game->player2)
                            <img src="{{ optional($game->player2->user)->avatar ? Storage::url($game->player2->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($game->player2->user->name) }}" 
                                 class="w-10 h-10 rounded-xl border-2 border-red-400">
                            <div>
                                <div class="font-semibold text-red-300">{{ $game->player2->user->name }}</div>
                                <div class="text-xs text-gray-400">O</div>
                            </div>
                            @else
                            <div class="w-10 h-10 rounded-xl bg-gray-500/20 flex items-center justify-center">
                                <i class="fas fa-robot text-gray-400"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-300">الكمبيوتر</div>
                                <div class="text-xs text-gray-400">O</div>
                            </div>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($game->player2)
                            <div class="text-sm text-white">{{ $game->player2->points }} نقطة</div>
                            <div class="text-xs text-gray-400">{{ $game->player2->win_rate }}% فوز</div>
                            @else
                            <div class="text-sm text-gray-400">مستوى متوسط</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Game Info -->
                <div class="mt-4 pt-4 border-t border-white/10">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">النوع:</span>
                        <span class="text-white">{{ $game->game_type == 'computer' ? 'ضد الكمبيوتر' : 'ضد لاعب' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="text-gray-400">المدة:</span>
                        <span class="text-white">{{ $game->created_at->diffInMinutes(now()) }} دقيقة</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full glass-card rounded-3xl p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gray-500/20 flex items-center justify-center">
                    <i class="fas fa-chess-board text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">لا توجد مباريات نشطة</h3>
                <p class="text-gray-400">لا توجد ألعاب نشطة حالياً للمراقبة</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection