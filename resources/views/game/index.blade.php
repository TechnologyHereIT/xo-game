@extends('layouts.app')
@section('title','ألعابي')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 py-4 sm:py-8 pb-24 lg:pb-8">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Header -->
        <div class="glass rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 mb-4 sm:mb-6 lg:mb-8 shadow-2xl">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4 sm:gap-6">
                <div class="text-center lg:text-right w-full lg:w-auto">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-l from-purple-200 to-blue-200 bg-clip-text text-transparent">
                        ألعابي
                    </h1>
                    <p class="mt-2 text-purple-200 text-sm sm:text-base">تابع جميع مبارياتك وتقدمك في اللعبة</p>
                </div>
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center justify-center gap-2 bg-white/10 active:bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-white transition-all duration-300 active:scale-105 active:shadow-lg touch-manipulation min-h-[44px] w-full sm:w-auto">
                    <i class="fas fa-arrow-right transform group-active:-translate-x-1 transition-transform"></i>
                    <span class="text-sm sm:text-base">رجوع للرئيسية</span>
                </a>
            </div>
        </div>

        <!-- Games Grid -->
        @if($games->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6 lg:mb-8">
            @foreach($games as $game)
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl sm:rounded-2xl blur opacity-25 group-active:opacity-75 transition duration-300"></div>
                <div class="relative bg-gray-900/80 backdrop-blur-xl border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-6 card-hover transition-all duration-300 active:scale-105 active:shadow-2xl touch-manipulation">
                    <!-- Game Header -->
                    <div class="flex justify-between items-center mb-3 sm:mb-4 flex-wrap gap-2">
                        <span class="text-xs bg-purple-500/30 text-purple-200 px-2 sm:px-3 py-1 sm:py-1.5 rounded-full font-medium">
                            #{{ $game->id }}
                        </span>
                        <span class="text-xs text-gray-300 bg-white/5 px-2 sm:px-3 py-1 sm:py-1.5 rounded-full">
                            <i class="fas fa-clock ml-1"></i>
                            <span class="hidden sm:inline">{{ $game->created_at->diffForHumans() }}</span>
                            <span class="sm:hidden">{{ $game->created_at->shortAbsoluteDiffForHumans() }}</span>
                        </span>
                    </div>

                    <!-- Players -->
                    <div class="space-y-3 sm:space-y-4 mb-3 sm:mb-4">
                        <!-- Player 1 -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $game->player1->user->avatar ? Storage::url($game->player1->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($game->player1->user->name).'&color=7F9CF5&background=EBF4FF' }}" 
                                         class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 border-purple-400 shadow-lg">
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 sm:w-5 sm:h-5 bg-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-[10px] sm:text-xs font-bold text-white">X</span>
                                    </div>
                                </div>
                                <span class="text-white font-medium text-xs sm:text-sm truncate">{{ Str::limit($game->player1->user->name, 15) }}</span>
                            </div>
                        </div>

                        <!-- VS Separator -->
                        <div class="flex items-center justify-center">
                            <div class="h-px bg-white/10 flex-1"></div>
                            <span class="px-2 sm:px-3 text-xs text-gray-400 font-bold">VS</span>
                            <div class="h-px bg-white/10 flex-1"></div>
                        </div>

                        <!-- Player 2 -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 sm:gap-3 ml-auto min-w-0 flex-1 justify-end">
                                <span class="text-white font-medium text-xs sm:text-sm truncate">{{ Str::limit($game->player2->user->name ?? 'الكمبيوتر', 15) }}</span>
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $game->player2 && $game->player2->user->avatar ? Storage::url($game->player2->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($game->player2->user->name ?? 'Computer').'&color=7F9CF5&background=EBF4FF' }}" 
                                         class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 border-blue-400 shadow-lg">
                                    <div class="absolute -bottom-1 -left-1 w-4 h-4 sm:w-5 sm:h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-[10px] sm:text-xs font-bold text-white">O</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Result -->
                    @php
                        $userSymbol = ($game->player1_id === ($player->id ?? Auth::user()->player?->id ?? 0)) ? 'X' : 'O';
                        $result = $game->winner ? ($game->winner === $userSymbol ? 'فوز' : 'خسارة') : 'تعادل';
                        $colorClasses = [
                            'فوز' => 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30',
                            'خسارة' => 'bg-rose-500/20 text-rose-300 border-rose-400/30', 
                            'تعادل' => 'bg-amber-500/20 text-amber-300 border-amber-400/30'
                        ];
                    @endphp
                    <div class="text-center mb-4">
                        <span class="text-sm {{ $colorClasses[$result] }} px-4 py-2 rounded-full border font-medium">
                            {{ $result }}
                        </span>
                    </div>

                    <!-- View Game Button -->
                    <a href="{{ route('game.show', $game->id) }}" 
                       class="group w-full flex items-center justify-center gap-2 bg-white/5 active:bg-white/10 border border-white/10 rounded-xl py-2 sm:py-3 text-white transition-all duration-300 active:scale-105 active:shadow-lg touch-manipulation min-h-[44px]">
                        <i class="fas fa-eye transform group-active:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm sm:text-base">عرض اللعبة</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="glass rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12 text-center shadow-2xl">
            <div class="max-w-md mx-auto">
                <div class="w-16 h-16 sm:w-20 sm:h-24 mx-auto mb-4 sm:mb-6 bg-white/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-gamepad text-2xl sm:text-3xl text-purple-300"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 sm:mb-3">لا توجد ألعاب حتى الآن</h3>
                <p class="text-purple-200 mb-4 sm:mb-6 text-sm sm:text-base">ابدأ لعبة جديدة وسيظهر سجل الألعاب هنا</p>
                <a href="{{ route('game.create') }}" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-500 to-blue-500 active:from-purple-600 active:to-blue-600 text-white font-medium px-4 sm:px-6 py-2 sm:py-3 rounded-xl transition-all duration-300 active:scale-105 active:shadow-lg touch-manipulation min-h-[44px] text-sm sm:text-base">
                    <i class="fas fa-plus"></i>
                    بدء لعبة جديدة
                </a>
            </div>
        </div>
        @endif

        <!-- Pagination -->
        @if($games->count() > 0)
        <div class="glass rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-2xl">
            <div class="flex justify-center">
                {{ $games->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.card-hover {
    transition: all 0.3s ease;
}
.card-hover:hover {
    transform: translateY(-5px);
}
.glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
</style>
@endsection