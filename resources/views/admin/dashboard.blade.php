@extends('layouts.app')
@section('title', 'لوحة تحكم الأدمن - XO Pro')

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
    .stats-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #a855f7, #ec4899);
    }
    .floating-card {
        transform: translateY(0);
        transition: all 0.3s ease;
    }
    .floating-card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="min-h-screen gradient-bg pb-12">
    <div class="container mx-auto px-4 pt-8">
        <!-- Header Section -->
        <div class="glass-card rounded-3xl p-8 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/10 rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-pink-500/10 rounded-full translate-y-24 -translate-x-24"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                        <i class="fas fa-crown text-2xl text-yellow-400"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">مرحباً، {{ Auth::user()->name }}! 👑</h1>
                        <p class="text-purple-200 mt-1">لوحة تحكم المسؤول - نظام XO Pro</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stats-card p-6 floating-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-sm mb-2">إجمالي اللاعبين</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_players'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-blue-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card p-6 floating-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-sm mb-2">إجمالي الألعاب</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_games'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chess-board text-green-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card p-6 floating-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-sm mb-2">الألعاب النشطة</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['active_games'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-play-circle text-yellow-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card p-6 floating-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-sm mb-2">إجمالي النقاط</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_points'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-star text-purple-300 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Actions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('admin.games') }}" 
               class="glass-card rounded-3xl p-6 text-center hover:bg-white/10 transition-all duration-300 floating-card group">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-500/20 rounded-2xl flex items-center justify-center group-hover:bg-blue-500/30 transition">
                    <i class="fas fa-chess-board text-2xl text-blue-300"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">إدارة الألعاب</h3>
                <p class="text-gray-300 text-sm">عرض وإدارة جميع الألعاب في النظام</p>
            </a>

            <a href="{{ route('admin.players') }}" 
               class="glass-card rounded-3xl p-6 text-center hover:bg-white/10 transition-all duration-300 floating-card group">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-500/20 rounded-2xl flex items-center justify-center group-hover:bg-green-500/30 transition">
                    <i class="fas fa-users text-2xl text-green-300"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">إدارة اللاعبين</h3>
                <p class="text-gray-300 text-sm">إدارة حسابات اللاعبين والإحصائيات</p>
            </a>

            <a href="{{ route('admin.active.humans') }}" 
               class="glass-card rounded-3xl p-6 text-center hover:bg-white/10 transition-all duration-300 floating-card group">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-500/20 rounded-2xl flex items-center justify-center group-hover:bg-purple-500/30 transition">
                    <i class="fas fa-eye text-2xl text-purple-300"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">المباريات المباشرة</h3>
                <p class="text-gray-300 text-sm">مراقبة الألعاب النشطة حالياً</p>
            </a>
        </div>

        <!-- Recent Games -->
        @if(isset($recentGames) && $recentGames->count() > 0)
        <div class="glass-card rounded-3xl p-6 mt-8">
            <h3 class="text-xl font-bold text-white mb-6">آخر الألعاب</h3>
            <div class="space-y-4">
                @foreach($recentGames as $game)
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center">
                            <i class="fas fa-chess text-purple-300"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white">لعبة #{{ $game->id }}</p>
                            <p class="text-sm text-gray-300">
                                {{ $game->player1->user->name }} vs 
                                {{ $game->player2 ? $game->player2->user->name : 'الكمبيوتر' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-xs 
                            @if($game->status == 'active') bg-green-500/20 text-green-300
                            @elseif($game->status == 'completed') bg-blue-500/20 text-blue-300
                            @else bg-gray-500/20 text-gray-300
                            @endif">
                            {{ $game->status }}
                        </span>
                        <p class="text-xs text-gray-400 mt-1">{{ $game->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection