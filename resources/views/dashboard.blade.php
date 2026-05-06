@extends('layouts.app')
@section('title', 'لوحة التحكم - XO Pro')

@section('content')
<style>
    :root {
        --primary: #667eea;
        --secondary: #764ba2;
        --accent: #f093fb;
        --success: #4ade80;
        --warning: #fbbf24;
        --danger: #f87171;
        --dark: #1e293b;
        --light: #f8fafc;
    }
    
    .game-gradient-bg {
        background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
        position: relative;
        overflow: hidden;
    }
    
    .game-gradient-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    .neon-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        position: relative;
        overflow: hidden;
    }
    
    .neon-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.6s;
    }
    
    .neon-card:hover::before {
        left: 100%;
    }
    
    .stats-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.05) 100%);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #a855f7, #ec4899, #f59e0b);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    .floating-card {
        transform: translateY(0);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .floating-card:hover {
        transform: translateY(-12px) scale(1.02);
    }
    
    .pulse-glow {
        animation: pulse-glow 2s infinite;
    }
    
    @keyframes pulse-glow {
        0%, 100% { 
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.4);
        }
        50% { 
            box-shadow: 0 0 40px rgba(168, 85, 247, 0.8);
        }
    }
    
    .game-btn {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        border-radius: 16px;
        padding: 16px 24px;
        color: white;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .game-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .game-btn:hover::before {
        left: 100%;
    }
    
    .game-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    
    .particles-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }
    
    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 50%;
        animation: float-particle 6s infinite linear;
    }
    
    @keyframes float-particle {
        0% {
            transform: translateY(100vh) translateX(0);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100px) translateX(100px);
            opacity: 0;
        }
    }
    
    .achievement-badge {
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        border-radius: 16px;
        padding: 16px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
        overflow: hidden;
    }
    
    .achievement-badge::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .achievement-badge:hover::before {
        opacity: 1;
    }
    
    .achievement-badge:hover {
        transform: scale(1.05) rotate(2deg);
    }
    
    .glowing-border {
        border: 2px solid transparent;
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05)) padding-box,
                    linear-gradient(135deg, #a855f7, #ec4899) border-box;
    }
    
    .typewriter {
        overflow: hidden;
        border-right: 3px solid var(--primary);
        white-space: nowrap;
        animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
    }
    
    @keyframes typing {
        from { width: 0 }
        to { width: 100% }
    }
    
    @keyframes blink-caret {
        from, to { border-color: transparent }
        50% { border-color: var(--primary) }
    }
    
    .bounce-in {
        animation: bounceIn 0.8s ease;
    }
    
    @keyframes bounceIn {
        0% { transform: scale(0.3); opacity: 0; }
        50% { transform: scale(1.05); opacity: 1; }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); opacity: 1; }
    }

    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
    @media (min-width: 640px) {
        .chart-container {
            height: 320px;
        }
    }

    .chart-tooltip {
        background: rgba(0, 0, 0, 0.8);
        border: 1px solid rgba(168, 85, 247, 0.5);
        border-radius: 12px;
        padding: 12px;
        color: white;
        font-size: 14px;
        backdrop-filter: blur(10px);
    }
</style>

<div x-data="advancedDashboard()" 
     x-init="initDashboard()"
     class="min-h-screen game-gradient-bg pb-12 relative">
    
    <!-- Floating Particles -->
    <div class="particles-container" id="particles"></div>
    
    <!-- Header Section -->
    <div class="container mx-auto px-3 sm:px-4 pt-4 sm:pt-8 relative z-10">
        <div class="neon-card p-4 sm:p-6 lg:p-8 mb-4 sm:mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500/10 rounded-full -translate-y-48 translate-x-48 hidden sm:block"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-pink-500/10 rounded-full translate-y-36 -translate-x-36 hidden sm:block"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center">
                <div class="flex-1 w-full">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-6 mb-4 sm:mb-6">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-2xl">
                            <i class="fas fa-chess-king text-2xl sm:text-3xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 typewriter">مرحباً، {{ Auth::user()->name }}! 👑</h1>
                            <p class="text-purple-200 text-sm sm:text-base lg:text-lg">
                                <i class="fas fa-clock mr-2"></i>
                                آخر ظهور: <span dir="ltr" class="font-mono">{{ Auth::user()->last_seen?->diffForHumans() ?? 'غير متاح' }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Quick Stats Bar -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4 mt-4 sm:mt-6">
                        <div class="text-center p-2 sm:p-3 bg-white/5 rounded-xl">
                            <div class="text-white font-bold text-base sm:text-lg" x-text="liveStats.onlinePlayers"></div>
                            <div class="text-purple-200 text-xs sm:text-sm">لاعبون متصلون</div>
                        </div>
                        <div class="text-center p-2 sm:p-3 bg-white/5 rounded-xl">
                            <div class="text-white font-bold text-base sm:text-lg" x-text="liveStats.activeGames"></div>
                            <div class="text-purple-200 text-xs sm:text-sm">ألعاب نشطة</div>
                        </div>
                        <div class="text-center p-2 sm:p-3 bg-white/5 rounded-xl">
                            <div class="text-white font-bold text-base sm:text-lg" x-text="liveStats.tournaments"></div>
                            <div class="text-purple-200 text-xs sm:text-sm">بطولات</div>
                        </div>
                        <div class="text-center p-2 sm:p-3 bg-white/5 rounded-xl">
                            <div class="text-white font-bold text-base sm:text-lg">24/7</div>
                            <div class="text-purple-200 text-xs sm:text-sm">خدمة مستمرة</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-3xl p-4 sm:p-6 text-center w-full sm:min-w-[240px] mt-4 sm:mt-6 lg:mt-0 glowing-border">
                    <div class="flex items-center justify-center gap-2 sm:gap-3 mb-2 sm:mb-3">
                        <i class="fas fa-gem text-purple-300 text-xl sm:text-2xl pulse-glow"></i>
                        <span class="text-3xl sm:text-4xl font-bold text-white">{{ Auth::user()->player->points ?? 0 }}</span>
                    </div>
                    <div class="text-purple-200 text-sm sm:text-base lg:text-lg font-semibold">إجمالي النقاط</div>
                </div>
            </div>
        </div>

        <!-- Animated Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-8">
            @php
                // استخدام البيانات الممررة من الـ controller مع قيم افتراضية
                $userPoints = isset($userPoints) ? $userPoints : (isset($player) && $player ? $player->points : 0);
                $gamesPlayed = isset($gamesPlayed) ? $gamesPlayed : (isset($player) && $player ? $player->games_played : 0);
                $winRate = isset($winRate) ? $winRate : (isset($player) && $player && $player->games_played > 0 ? round(($player->games_won / $player->games_played) * 100, 1) : 0);
            @endphp

            <div class="stats-card p-4 sm:p-6 floating-card bounce-in" style="animation-delay: 0.1s">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-xs sm:text-sm mb-2">النقاط</p>
                        <p class="text-2xl sm:text-3xl font-bold text-white">{{ number_format($userPoints ?? 0) }}</p>
                        <p class="text-green-400 text-xs mt-1">
                            @if(isset($pointsChangeThisWeek))
                                @if($pointsChangeThisWeek > 0)
                                    +{{ $pointsChangeThisWeek }} هذا الأسبوع
                                @elseif($pointsChangeThisWeek < 0)
                                    {{ $pointsChangeThisWeek }} هذا الأسبوع
                                @else
                                    بدون تغيير
                                @endif
                            @else
                                - 
                            @endif
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500/30 to-purple-600/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-star text-purple-300 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card p-6 floating-card bounce-in" style="animation-delay: 0.2s">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-sm mb-2">الألعاب</p>
                        <p class="text-3xl font-bold text-white">{{ number_format($gamesPlayed ?? 0) }}</p>
                        <p class="text-blue-400 text-xs mt-1">
                            @if(isset($gamesChangeThisWeek))
                                @if($gamesChangeThisWeek > 0)
                                    +{{ $gamesChangeThisWeek }} هذا الأسبوع
                                @elseif($gamesChangeThisWeek < 0)
                                    {{ $gamesChangeThisWeek }} هذا الأسبوع
                                @else
                                    بدون تغيير
                                @endif
                            @else
                                - 
                            @endif
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500/30 to-green-600/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-chess-board text-green-300 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card p-6 floating-card bounce-in" style="animation-delay: 0.3s">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-sm mb-2">معدل الفوز</p>
                        <p class="text-3xl font-bold text-white">{{ number_format($winRate ?? 0, 1) }}%</p>
                        <p class="text-yellow-400 text-xs mt-1">
                            @if(isset($winRateChange))
                                @if($winRateChange > 0)
                                    +{{ number_format($winRateChange, 1) }}% هذا الشهر
                                @elseif($winRateChange < 0)
                                    {{ number_format($winRateChange, 1) }}% هذا الشهر
                                @else
                                    بدون تغيير
                                @endif
                            @else
                                - 
                            @endif
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-500/30 to-yellow-600/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-trophy text-yellow-300 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card p-6 floating-card bounce-in" style="animation-delay: 0.4s">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-sm mb-2">الترتيب</p>
                        <p class="text-3xl font-bold text-white">#{{ $playerRank ?? 999 }}</p>
                        <p class="text-pink-400 text-xs mt-1">
                            @php
                                $rank = $playerRank ?? 999;
                                if ($rank <= 3) echo 'بطل أسطوري';
                                elseif ($rank <= 10) echo 'سيد اللعبة';
                                elseif ($rank <= 25) echo 'محترف';
                                elseif ($rank <= 50) echo 'متقدم';
                                elseif ($rank <= 100) echo 'مبتكر';
                                else echo 'مبتدئ واعد';
                            @endphp
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-pink-500/30 to-pink-600/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-medal text-pink-300 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Chart Section -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mb-4 sm:mb-8">
            <!-- Advanced Chart -->
            <div class="xl:col-span-2 neon-card p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-white">📊 أداؤك خلال الأسبوع</h3>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button @click="loadChartData('week')" 
                                :class="chartRange === 'week' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : 'bg-white/10 text-gray-300'"
                                class="flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-300 touch-manipulation min-h-[44px]">
                            أسبوع
                        </button>
                        <button @click="loadChartData('month')" 
                                :class="chartRange === 'month' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : 'bg-white/10 text-gray-300'"
                                class="flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-300 touch-manipulation min-h-[44px]">
                            شهر
                        </button>
                        <button @click="loadChartData('year')" 
                                :class="chartRange === 'year' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : 'bg-white/10 text-gray-300'"
                                class="flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-300 touch-manipulation min-h-[44px]">
                            سنة
                        </button>
                    </div>
                </div>
                <div class="chart-container" style="height: 250px; width: 100%;">
                    <canvas id="advancedPerformanceChart"></canvas>
                </div>
                <div x-show="chartLoading" class="text-center py-4">
                    <div class="inline-flex items-center gap-2 text-purple-300">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>جاري تحميل البيانات...</span>
                    </div>
                </div>
            </div>

            <!-- Game Actions -->
            <div class="space-y-4 sm:space-y-6">
                <div class="neon-card p-4 sm:p-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">🎮 بدء لعبة جديدة</h3>
                    <div class="space-y-3 sm:space-y-4">
                        <!-- Computer Game -->
                        <button onclick="startGame('computer')" 
                                class="w-full neon-card rounded-2xl p-4 sm:p-5 text-right active:bg-white/10 transition-all duration-300 floating-card group relative overflow-hidden touch-manipulation min-h-[80px]">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-transparent opacity-0 group-active:opacity-100 transition-opacity"></div>
                            <div class="flex items-center justify-between gap-3">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500/30 to-blue-600/30 rounded-2xl flex items-center justify-center group-active:scale-110 transition-transform flex-shrink-0">
                                    <i class="fas fa-robot text-xl sm:text-2xl text-blue-300"></i>
                                </div>
                                <div class="text-right flex-1">
                                    <div class="font-bold text-white text-base sm:text-lg">ضد الكمبيوتر</div>
                                    <div class="text-gray-300 text-xs sm:text-sm">تدرب ضد الذكاء الاصطناعي المتقدم</div>
                                </div>
                                <i class="fas fa-arrow-left text-gray-400 group-active:text-blue-400 transition-colors flex-shrink-0"></i>
                            </div>
                        </button>

                        <!-- Online Player -->
                        <button onclick="showPlayersModal()" 
                                class="w-full neon-card rounded-2xl p-4 sm:p-5 text-right active:bg-white/10 transition-all duration-300 floating-card group relative overflow-hidden touch-manipulation min-h-[80px]">
                            <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 to-transparent opacity-0 group-active:opacity-100 transition-opacity"></div>
                            <div class="flex items-center justify-between gap-3">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500/30 to-green-600/30 rounded-2xl flex items-center justify-center group-active:scale-110 transition-transform flex-shrink-0">
                                    <i class="fas fa-users text-xl sm:text-2xl text-green-300"></i>
                                </div>
                                <div class="text-right flex-1">
                                    <div class="font-bold text-white text-base sm:text-lg">ضد لاعب اونلاين</div>
                                    <div class="text-gray-300 text-xs sm:text-sm">تحدي لاعبين حقيقيين حول العالم</div>
                                </div>
                                <i class="fas fa-arrow-left text-gray-400 group-active:text-green-400 transition-colors flex-shrink-0"></i>
                            </div>
                        </button>

                        <!-- Tournaments -->
                        <button onclick="window.location.href='{{ route('tournaments') }}'" 
                                class="w-full neon-card rounded-2xl p-4 sm:p-5 text-right active:bg-white/10 transition-all duration-300 floating-card group relative overflow-hidden touch-manipulation min-h-[80px]">
                            <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/10 to-transparent opacity-0 group-active:opacity-100 transition-opacity"></div>
                            <div class="flex items-center justify-between gap-3">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-yellow-500/30 to-yellow-600/30 rounded-2xl flex items-center justify-center group-active:scale-110 transition-transform flex-shrink-0">
                                    <i class="fas fa-trophy text-xl sm:text-2xl text-yellow-300"></i>
                                </div>
                                <div class="text-right flex-1">
                                    <div class="font-bold text-white text-base sm:text-lg">البطولات</div>
                                    <div class="text-gray-300 text-xs sm:text-sm">انضم إلى البطولات التنافسية</div>
                                </div>
                                <i class="fas fa-arrow-left text-gray-400 group-active:text-yellow-400 transition-colors flex-shrink-0"></i>
                            </div>
                        </button>

                        @if(Auth::user()->is_admin)
                        <div class="border-t border-white/10 pt-4 mt-4">
                            <button onclick="window.location.href='{{ route('admin.dashboard') }}'" 
                                    class="w-full neon-card rounded-2xl p-5 text-right hover:bg-white/10 transition-all duration-300 floating-card group relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-red-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="flex items-center justify-between">
                                    <div class="w-14 h-14 bg-gradient-to-br from-red-500/30 to-red-600/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-cogs text-2xl text-red-300"></i>
                                    </div>
                                    <div class="text-right flex-1 mr-4">
                                        <div class="font-bold text-white text-lg">لوحة الأدمن</div>
                                        <div class="text-gray-300 text-sm">إدارة النظام واللاعبين</div>
                                    </div>
                                    <i class="fas fa-arrow-left text-gray-400 group-hover:text-red-400 transition-colors"></i>
                                </div>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Game Stats -->
                <div class="neon-card p-6">
                    <h3 class="text-xl font-bold text-white mb-4">⚡ إحصائيات سريعة</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-xl">
                            <span class="text-gray-300">أسرع فوز</span>
                            <span class="text-white font-bold">{{ $fastestWin ?? 'لا توجد بيانات' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-xl">
                            <span class="text-gray-300">أطول مباراة</span>
                            <span class="text-white font-bold">{{ $longestGame ?? 'لا توجد بيانات' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-xl">
                            <span class="text-gray-300">متوسط الوقت</span>
                            <span class="text-white font-bold">{{ $averageTime ?? 'لا توجد بيانات' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            <!-- Top Players -->
            <div class="neon-card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-white">🏆 أفضل اللاعبين</h3>
                    <button onclick="window.location.href='{{ route('leaderboard') }}'" 
                            class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:from-purple-600 hover:to-pink-600 transition-all floating-card flex items-center gap-2">
                        عرض الكل <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    @foreach($topPlayers as $index => $player)
                    <div class="flex items-center justify-between bg-white/5 rounded-2xl p-4 hover:bg-white/10 transition-all duration-300 group relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent transform -skew-x-12 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="relative">
                                <img src="{{ $player->user->avatar ? Storage::url($player->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($player->user->name).'&background=7c3aed&color=fff' }}" 
                                     class="w-14 h-14 rounded-2xl border-2 border-purple-400 shadow-lg">
                                @if($index < 3)
                                <div class="absolute -top-2 -right-2 w-7 h-7 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-xs font-bold text-white">{{ $index + 1 }}</span>
                                </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-white text-lg">{{ $player->user->name }}</p>
                                <p class="text-xs text-gray-300 flex items-center gap-1">
                                    <i class="fas fa-chess-board"></i>
                                    {{ $player->games_played }} لعبة
                                </p>
                            </div>
                        </div>
                        <div class="text-right relative z-10">
                            <p class="font-bold text-purple-300 text-lg">{{ $player->points }} نقطة</p>
                            <p class="text-xs text-gray-400 flex items-center gap-1 justify-end">
                                <i class="fas fa-trophy"></i>
                                {{ $player->win_rate }}% فوز
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Achievements -->
            <div class="neon-card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-white">🎯 إنجازاتك</h3>
                    @php
                        $unlockedCount = 0;
                        if (isset($achievements) && is_array($achievements)) {
                            $unlockedCount = count(array_filter($achievements, function($ach) {
                                return isset($ach['unlocked']) && $ach['unlocked'];
                            }));
                        }
                        $achievementPercentage = count($achievements ?? []) > 0 ? ($unlockedCount / count($achievements ?? [])) * 100 : 0;
                    @endphp
                    <div class="text-right">
                        <span class="text-sm text-gray-300 block">
                            {{ $unlockedCount }}/{{ count($achievements ?? []) }} مكتمل
                        </span>
                        <div class="w-24 bg-gray-700 rounded-full h-2 mt-1">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full" 
                                 style="width: {{ $achievementPercentage }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @if(isset($achievements) && is_array($achievements))
                        @foreach(array_slice($achievements, 0, 6) as $index => $ach)
                        <div class="achievement-badge">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-2xl {{ isset($ach['unlocked']) && $ach['unlocked'] ? 'bg-gradient-to-br from-green-500/30 to-emerald-500/30' : 'bg-gray-600/30' }} flex items-center justify-center">
                                <i class="fas fa-{{ $ach['icon'] ?? 'question' }} text-xl {{ isset($ach['unlocked']) && $ach['unlocked'] ? 'text-green-300' : 'text-gray-400' }}"></i>
                            </div>
                            <p class="font-bold text-sm text-white mb-1">{{ $ach['title'] ?? 'إنجاز' }}</p>
                            <p class="text-xs text-gray-300 leading-tight">{{ $ach['description'] ?? '' }}</p>
                            @if(isset($ach['unlocked']) && $ach['unlocked']) 
                            <div class="mt-2">
                                <span class="text-xs text-green-300 bg-green-500/20 px-2 py-1 rounded-full">✅ مُكتمل</span>
                            </div>
                            @else
                            <div class="mt-2">
                                <span class="text-xs text-gray-400 bg-gray-600/30 px-2 py-1 rounded-full">🔒 مقفل</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Live Tournament Feed -->
        <div class="neon-card p-6 mt-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-white">🏆 البطولات الحية</h3>
                <span class="text-green-400 text-sm flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    مباشر
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeTournaments as $tournament)
                <div class="bg-gradient-to-br from-purple-500/20 to-transparent rounded-2xl p-4 border border-purple-500/30">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-white font-bold">{{ $tournament->name }}</span>
                        <span class="text-{{ $tournament->status_color }}-400 text-xs bg-{{ $tournament->status_color }}-500/20 px-2 py-1 rounded-full">
                            {{ $tournament->status_text }}
                        </span>
                    </div>
                    <div class="text-gray-300 text-sm mb-2">{{ $tournament->participants_count }} لاعب • {{ $tournament->current_round }}</div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>الجائزة: {{ number_format($tournament->prize) }} نقطة</span>
                        <span>{{ $tournament->time_remaining }}</span>
                    </div>
                </div>
                @endforeach
                
                @if($activeTournaments->isEmpty())
                <div class="col-span-3 text-center py-8">
                    <i class="fas fa-trophy text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-300">لا توجد بطولات نشطة حالياً</p>
                    <p class="text-gray-400 text-sm mt-2">تابعنا لمعرفة البطولات القادمة</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div id="playersModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="neon-card w-full max-w-2xl p-6 relative">
        <button onclick="closePlayersModal()" class="absolute left-4 top-4 text-gray-400 hover:text-white text-xl">
            <i class="fas fa-times"></i>
        </button>
        
        <h3 class="text-2xl font-bold text-white mb-6 text-center">👥 اللاعبون المتصلون</h3>
        
        <div class="space-y-3 max-h-96 overflow-y-auto" id="onlinePlayersList">
            <!-- سيتم ملء هذا القسم باللاعبين المتصلين -->
        </div>
        
        <div id="noPlayersMessage" class="text-center py-8 hidden">
            <i class="fas fa-users-slash text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-300">لا يوجد لاعبون متصلون حالياً</p>
            <p class="text-gray-400 text-sm mt-2">يمكنك بدء لعبة ضد الكمبيوتر أو دعوة أصدقائك</p>
        </div>
        
        <div class="mt-6 flex gap-3">
            <button onclick="closePlayersModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-xl transition-all">
                إلغاء
            </button>
        </div>
    </div>
</div>
<div id="chart-data" 
     data-weekly-stats="{{ json_encode($weeklyStats) }}"
     data-monthly-stats="{{ json_encode($monthlyStats) }}"
     data-yearly-stats="{{ json_encode($yearlyStats) }}"
     style="display: none;">
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function advancedDashboard() {
        return {
            liveStats: {
                onlinePlayers: {{ $liveStats['onlinePlayers'] ?? 0 }},
                activeGames: {{ $liveStats['activeGames'] ?? 0 }},
                tournaments: {{ $liveStats['tournaments'] ?? 0 }}
            },
            chartRange: 'week',
            performanceChart: null,
            chartLoading: false,

            async initDashboard() {
                this.initParticles();
                this.startLiveUpdates();
                await this.loadChartData('week');
                // تحميل الإحصائيات الحية الحقيقية
                await this.loadLiveStats();
            },
            
            async loadLiveStats() {
                try {
                    const response = await fetch('/api/live-stats');
                    const data = await response.json();
                    if (data.success && data.stats) {
                        this.liveStats = data.stats;
                    }
                } catch (error) {
                    console.error('Error loading live stats:', error);
                }
            },

            initParticles() {
                const container = document.getElementById('particles');
                if (!container) return;
                
                const particleCount = 30;
                
                for (let i = 0; i < particleCount; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 6 + 's';
                    particle.style.animationDuration = (3 + Math.random() * 4) + 's';
                    container.appendChild(particle);
                }
            },

            async loadChartData(range) {
                this.chartLoading = true;
                this.chartRange = range;

                try {
                    // جلب البيانات من العنصر المخفي في الصفحة
                    const chartDataElement = document.getElementById('chart-data');
                    let chartData;

                    switch(range) {
                        case 'week':
                            chartData = JSON.parse(chartDataElement.dataset.weeklyStats);
                            break;
                        case 'month':
                            chartData = JSON.parse(chartDataElement.dataset.monthlyStats);
                            break;
                        case 'year':
                            chartData = JSON.parse(chartDataElement.dataset.yearlyStats);
                            break;
                        default:
                            chartData = JSON.parse(chartDataElement.dataset.weeklyStats);
                    }

                    this.initChart(chartData);
                    this.showNotification(`✅ تم تحميل بيانات ${this.getRangeName(range)}`, 'success');
                    
                } catch (error) {
                    console.error('Error loading chart data:', error);
                    this.showNotification('❌ فشل في تحميل بيانات الرسم البياني', 'error');
                    this.loadFallbackData(range);
                } finally {
                    this.chartLoading = false;
                }
            },

            getRangeName(range) {
                const names = {
                    'week': 'الأسبوع',
                    'month': 'الشهر', 
                    'year': 'السنة'
                };
                return names[range] || 'البيانات';
            },

            loadFallbackData(range) {
                // بيانات افتراضية للطوارئ فقط
                const fallbackData = {
                    week: {
                        labels: ['الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت', 'الأحد'],
                        points: [45, 52, 38, 65, 72, 48, 60],
                        games: [3, 5, 2, 6, 8, 4, 5],
                        wins: [2, 3, 1, 4, 6, 2, 3]
                    },
                    month: {
                        labels: ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4'],
                        points: [180, 220, 190, 280],
                        games: [15, 18, 12, 22],
                        wins: [8, 10, 6, 14]
                    },
                    year: {
                        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                        points: [1200, 1350, 1100, 1250, 1400, 1550, 1600, 1700, 1650, 1800, 1750, 1900],
                        games: [80, 90, 75, 85, 95, 100, 105, 110, 100, 115, 105, 120],
                        wins: [45, 50, 40, 48, 55, 60, 65, 70, 62, 75, 68, 80]
                    }
                };

                this.initChart(fallbackData[range] || fallbackData.week);
            },

            initChart(chartData) {
                const ctx = document.getElementById('advancedPerformanceChart');
                if (!ctx) return;

                // تدمير الرسم البياني القديم إذا موجود
                if (this.performanceChart) {
                    this.performanceChart.destroy();
                }

                // إنشاء التدرج اللوني
                const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(168, 85, 247, 0.4)');
                gradient.addColorStop(1, 'rgba(168, 85, 247, 0.1)');

                const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                gradient2.addColorStop(0, 'rgba(34, 197, 94, 0.4)');
                gradient2.addColorStop(1, 'rgba(34, 197, 94, 0.1)');

                const gradient3 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                gradient3.addColorStop(0, 'rgba(251, 191, 36, 0.4)');
                gradient3.addColorStop(1, 'rgba(251, 191, 36, 0.1)');

                this.performanceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'النقاط',
                                data: chartData.points,
                                borderColor: '#a855f7',
                                backgroundColor: gradient,
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointBackgroundColor: '#a855f7',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            },
                            {
                                label: 'الألعاب',
                                data: chartData.games,
                                borderColor: '#22c55e',
                                backgroundColor: gradient2,
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointBackgroundColor: '#22c55e',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            },
                            {
                                label: 'الفوز',
                                data: chartData.wins,
                                borderColor: '#fbbf24',
                                backgroundColor: gradient3,
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointBackgroundColor: '#fbbf24',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: '#fff',
                                    font: {
                                        size: 12,
                                        family: 'Tajawal, sans-serif'
                                    },
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#a855f7',
                                borderWidth: 1,
                                cornerRadius: 12,
                                displayColors: true
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#fff',
                                    font: {
                                        family: 'Tajawal, sans-serif',
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#fff',
                                    font: {
                                        family: 'Tajawal, sans-serif',
                                        size: 11
                                    }
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            },

            showNotification(message, type = 'info') {
                // إنشاء عنصر الإشعار
                const notification = document.createElement('div');
                notification.className = `fixed top-5 right-5 z-50 rounded-2xl shadow-2xl overflow-hidden ${
                    type === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-500' :
                    type === 'info' ? 'bg-gradient-to-r from-blue-500 to-cyan-500' :
                    type === 'warning' ? 'bg-gradient-to-r from-yellow-500 to-orange-500' :
                    'bg-gradient-to-r from-red-500 to-pink-500'
                }`;
                
                notification.innerHTML = `
                    <div class="flex items-center gap-4 px-6 py-4 text-white">
                        <i class="fas text-xl ${
                            type === 'success' ? 'fa-check-circle' :
                            type === 'info' ? 'fa-info-circle' :
                            type === 'warning' ? 'fa-exclamation-triangle' :
                            'fa-times-circle'
                        }"></i>
                        <span class="font-semibold">${message}</span>
                    </div>
                    <div class="h-1 bg-white/30">
                        <div class="h-full bg-white/70 transition-all duration-3000 notification-progress"></div>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                // تحريك شريط التقدم
                const progressBar = notification.querySelector('.notification-progress');
                let width = 100;
                const interval = setInterval(() => {
                    width -= 0.2;
                    progressBar.style.width = width + '%';
                    if (width <= 0) {
                        clearInterval(interval);
                    }
                }, 10);
                
                // إخفاء الإشعار تلقائياً بعد 5 ثواني
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 5000);
            },

            startLiveUpdates() {
                // تحديث الإحصائيات الحية كل 30 ثانية من قاعدة البيانات
                setInterval(async () => {
                    await this.loadLiveStats();
                }, 30000);
            }
        };
    }
    // نظام فحص الدعوات التلقائي
    function startInvitationPolling() {
        setInterval(() => {
            fetch('/api/my-invitations')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.invitations && data.invitations.length > 0) {
                        data.invitations.forEach(invitation => {
                            // التحقق إذا كانت الإشعار معروض مسبقاً
                            const existingNotification = document.querySelector(`[data-invitation-id="${invitation.id}"]`);
                            if (!existingNotification) {
                                showNewInvitationNotification(invitation);
                            }
                        });
                    }
                })
                .catch(error => console.error('Error checking invitations:', error));
        }, 5000); // فحص كل 5 ثواني
    }

    // نظام فحص الدعوات المقبولة (للشخص الذي أرسل الدعوة)
    function startAcceptedInvitationPolling() {
        setInterval(() => {
            fetch('/api/check-accepted-invitations')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.accepted && data.game_id) {
                        // تم قبول الدعوة! توجيه المرسل إلى اللعبة
                        showNotification('🎮 تم قبول دعوتك! جاري تحميل اللعبة...', 'success');
                        setTimeout(() => {
                            window.location.href = '/game/' + data.game_id;
                        }, 1500);
                    }
                })
                .catch(error => console.error('Error checking accepted invitations:', error));
        }, 3000); // فحص كل 3 ثواني
    }

    // بدء الفحص التلقائي عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        startInvitationPolling();
        startAcceptedInvitationPolling();
        console.log('Invitation polling started');
    });

    // دوال JavaScript المعدلة
    function showPlayersModal() {
        const modal = document.getElementById('playersModal');
        const playersList = document.getElementById('onlinePlayersList');
        const noPlayersMessage = document.getElementById('noPlayersMessage');
        
        modal.classList.remove('hidden');
        playersList.innerHTML = '';
        noPlayersMessage.classList.add('hidden');
        
        playersList.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-purple-400 mb-3"></i>
                <p class="text-gray-300">جاري تحميل اللاعبين المتصلين...</p>
            </div>
        `;
        
        fetch('/api/online-players')
        .then(response => response.json())
        .then(data => {
            playersList.innerHTML = '';
            
            if (data.success && data.players && data.players.length > 0) {
                data.players.forEach(player => {
                    if (player.id !== {{ Auth::id() }}) {
                        const playerElement = document.createElement('div');
                        playerElement.className = 'flex items-center justify-between bg-white/5 rounded-2xl p-4 hover:bg-white/10 transition-all duration-300';
                        playerElement.innerHTML = `
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl border-2 border-purple-400 bg-gradient-to-br from-purple-500/30 to-pink-500/30 flex items-center justify-center">
                                    <i class="fas fa-user text-white text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-white">${player.name}</p>
                                    <p class="text-xs text-gray-300">${player.points} نقطة</p>
                                </div>
                            </div>
                            <button onclick="sendGameInvitation(${player.id})" 
                                    class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-2 rounded-xl text-sm font-semibold transition-all">
                                <i class="fas fa-gamepad mr-2"></i>تحدي
                            </button>
                        `;
                        playersList.appendChild(playerElement);
                    }
                });
                
                if (playersList.children.length === 0) {
                    noPlayersMessage.classList.remove('hidden');
                }
            } else {
                noPlayersMessage.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            playersList.innerHTML = '';
            noPlayersMessage.classList.remove('hidden');
        });
    }

    function closePlayersModal() {
        document.getElementById('playersModal').classList.add('hidden');
    }

    function sendGameInvitation(playerId) {
        showNotification('🎮 جاري إرسال دعوة للاعب...', 'info');
        
        fetch('/api/send-invitation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                player_id: playerId,
                game_type: 'classic',
                time_limit: 300,
                message: 'تحدي XO جديد!'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('✅ تم إرسال الدعوة بنجاح!', 'success');
                closePlayersModal();
            } else {
                showNotification('❌ ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('❌ حدث خطأ في إرسال الدعوة', 'error');
        });
    }

    function showNewInvitationNotification(invitation) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-5 left-5 z-50 w-96 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-2xl overflow-hidden';
        notification.setAttribute('data-invitation-id', invitation.id);
        notification.innerHTML = `
            <div class="p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl border-2 border-white bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <i class="fas fa-user text-white text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-white text-lg">دعوة لعبة جديدة!</p>
                        <p class="text-white/90 text-sm">من: ${invitation.from_user.name}</p>
                        <p class="text-white/80 text-xs">${invitation.message}</p>
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button onclick="acceptInvitation(${invitation.id})" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl font-semibold transition-all">
                        قبول ✓
                    </button>
                    <button onclick="rejectInvitation(${invitation.id})" 
                            class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-xl font-semibold transition-all">
                        رفض ✗
                    </button>
                </div>
                <div class="text-white/60 text-xs text-center mt-2">
                    ${invitation.time_remaining}
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // إزالة الإشعار بعد 30 ثانية
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 30000);
    }

    function acceptInvitation(invitationId) {
        // إزالة الإشعار فوراً
        const notification = document.querySelector(`[data-invitation-id="${invitationId}"]`);
        if (notification) {
            notification.remove();
        }
        
        fetch('/api/accept-invitation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ invitation_id: invitationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('🎮 تم قبول الدعوة! جاري بدء اللعبة...', 'success');
                setTimeout(() => {
                    window.location.href = '/game/' + data.game_id;
                }, 2000);
            } else {
                showNotification('❌ ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('❌ حدث خطأ في قبول الدعوة', 'error');
        });
    }

    function rejectInvitation(invitationId) {
        // إزالة الإشعار فوراً
        const notification = document.querySelector(`[data-invitation-id="${invitationId}"]`);
        if (notification) {
            notification.remove();
        }
        
        fetch('/api/reject-invitation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ invitation_id: invitationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم رفض الدعوة', 'info');
            } else {
                showNotification('❌ ' + data.message, 'error');
            }
        });
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-5 right-5 z-50 rounded-2xl shadow-2xl overflow-hidden ${
            type === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-500' :
            type === 'info' ? 'bg-gradient-to-r from-blue-500 to-cyan-500' :
            type === 'warning' ? 'bg-gradient-to-r from-yellow-500 to-orange-500' :
            'bg-gradient-to-r from-red-500 to-pink-500'
        }`;

        notification.innerHTML = `
            <div class="flex items-center gap-4 px-6 py-4 text-white">
                <i class="fas text-xl ${
                    type === 'success' ? 'fa-check-circle' :
                    type === 'info' ? 'fa-info-circle' :
                    type === 'warning' ? 'fa-exclamation-triangle' :
                    'fa-times-circle'
                }"></i>
                <span class="font-semibold">${message}</span>
            </div>
            <div class="h-1 bg-white/30">
                <div class="h-full bg-white/70 transition-all duration-3000 notification-progress"></div>
            </div>
        `;

        document.body.appendChild(notification);

        const progressBar = notification.querySelector('.notification-progress');
        let width = 100;
        const interval = setInterval(() => {
            width -= 0.2;
            progressBar.style.width = width + '%';
            if (width <= 0) clearInterval(interval);
        }, 10);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    function startGame(type) {
        showNotification('🎮 جاري بدء اللعبة ضد الكمبيوتر...', 'info');
        
        setTimeout(() => {
            fetch('/game', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                },
                body: JSON.stringify({ 
                    game_type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.game_id) {
                    window.location.href = '/game/' + data.game_id;
                } else {
                    showNotification('❌ فشل في بدء اللعبة', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ في بدء اللعبة', 'error');
            });
        }, 1000);
    }

    function inviteFriends() {
        const inviteLink = window.location.origin + '/register?ref={{ Auth::id() }}';
        
        // نسخ الرابط للحافظة
        navigator.clipboard.writeText(inviteLink).then(() => {
            showNotification('✅ تم نسخ رابط الدعوة! شاركه مع أصدقائك', 'success');
        }).catch(() => {
            // إذا فشل نسخ الرابط، عرضه للمستخدم
            alert(`رابط الدعوة: ${inviteLink}`);
        });
        
        closePlayersModal();
    }

    // تهيئة الرسوم المتحركة عند التمرير
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
    } else {
        console.log('Chart.js is ready');
    }
    
    // تهيئة عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard loaded');
    });

    document.addEventListener('alpine:init', () => {
        console.log('Alpine.js initialized successfully');
    });
    
</script>
@endsection