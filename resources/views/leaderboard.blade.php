@extends('layouts.app')

@section('title', 'لوحة المتصدرين')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 py-4 sm:py-8 pb-24 lg:pb-8">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        
        {{-- العنوان الرئيسي --}}
        <div class="text-center mb-6 sm:mb-8 lg:mb-12">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-purple-200 to-blue-200 bg-clip-text text-transparent mb-2 sm:mb-4">
                لوحة المتصدرين 🏆
            </h1>
            <p class="text-gray-300 text-base sm:text-lg lg:text-xl">تصفح أفضل اللاعبين في المنصة</p>
        </div>

        {{-- أزرار التبديل بين التصنيفات --}}
        <div class="flex justify-center mb-4 sm:mb-6 lg:mb-8">
            <div class="glass-card rounded-2xl p-1 sm:p-2 flex gap-1 sm:gap-2 w-full sm:w-auto">
                <button id="globalTab" 
                        class="flex-1 sm:flex-none px-3 sm:px-6 py-2 sm:py-3 rounded-xl font-medium transition-all duration-300 bg-gradient-to-r from-purple-600 to-blue-600 text-white shadow-lg text-sm sm:text-base touch-manipulation min-h-[44px]">
                    <i class="fas fa-globe ml-1 sm:ml-2"></i>
                    <span class="hidden sm:inline">التصنيف العام</span>
                    <span class="sm:hidden">العام</span>
                </button>
                <button id="countryTab" 
                        class="flex-1 sm:flex-none px-3 sm:px-6 py-2 sm:py-3 rounded-xl font-medium transition-all duration-300 text-gray-300 active:text-white active:bg-white/5 text-sm sm:text-base touch-manipulation min-h-[44px]">
                    <i class="fas fa-flag ml-1 sm:ml-2"></i>
                    <span class="hidden sm:inline">التصنيف حسب الدولة</span>
                    <span class="sm:hidden">الدولة</span>
                </button>
            </div>
        </div>

        {{-- قسم التصنيف العام --}}
        <div id="globalLeaderboard" class="space-y-4 sm:space-y-6">
            {{-- بطولة أفضل 3 لاعبين --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6 lg:mb-8">
                @php
                    $topPlayers = $globalLeaderboard->take(3);
                    $medals = ['🥇', '🥈', '🥉'];
                @endphp
                
                @foreach($topPlayers as $index => $player)
                    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 text-center transform active:scale-105 transition-all duration-300 touch-manipulation
                                {{ $index == 0 ? 'ring-2 ring-yellow-400 shadow-2xl shadow-yellow-500/20' : '' }}">
                        {{-- الرتبة --}}
                        <div class="mb-3 sm:mb-4">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto rounded-full flex items-center justify-center 
                                        {{ $index == 0 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 
                                           ($index == 1 ? 'bg-gradient-to-r from-gray-400 to-gray-600' : 
                                           'bg-gradient-to-r from-orange-400 to-orange-600') }}">
                                <span class="text-xl sm:text-2xl font-bold text-white">{{ $medals[$index] }}</span>
                            </div>
                        </div>

                        {{-- الصورة --}}
                        <div class="relative mx-auto mb-3 sm:mb-4 w-16 h-16 sm:w-20 sm:h-20">
                            <img src="{{ $player->user->avatar ? Storage::url($player->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($player->user->name).'&color=7F9CF5&background=EBF4FF' }}" 
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl object-cover border-2 
                                        {{ $index == 0 ? 'border-yellow-400' : 
                                           ($index == 1 ? 'border-gray-400' : 'border-orange-400') }}">
                        </div>

                        {{-- المعلومات --}}
                        <h3 class="text-lg sm:text-xl font-bold text-white mb-1 sm:mb-2">{{ Str::limit($player->user->name, 15) }}</h3>
                        <p class="text-gray-300 text-xs sm:text-sm mb-2">{{ Str::limit($player->user->country_name ?? $player->user->country, 20) }}</p>
                        
                        {{-- الإحصائيات --}}
                        <div class="flex justify-center gap-3 sm:gap-4 mb-3 sm:mb-4">
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-purple-300">{{ $player->points }}</div>
                                <div class="text-xs text-gray-400">النقاط</div>
                            </div>
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-green-300">{{ $player->games_won }}</div>
                                <div class="text-xs text-gray-400">الفوز</div>
                            </div>
                        </div>

                        {{-- معدل الفوز --}}
                        <div class="bg-white/5 rounded-xl p-2 sm:p-3">
                            <div class="flex justify-between text-xs sm:text-sm mb-1">
                                <span class="text-gray-300">معدل الفوز</span>
                                <span class="text-white font-bold">{{ $player->win_rate }}%</span>
                            </div>
                            <div class="w-full bg-gray-600 rounded-full h-1.5 sm:h-2">
                                <div class="h-1.5 sm:h-2 rounded-full bg-gradient-to-r from-green-500 to-emerald-500" 
                                     style="width: {{ min($player->win_rate, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- قائمة جميع اللاعبين --}}
            <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6">
                <h3 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                    <i class="fas fa-list-ol text-purple-300"></i>
                    جميع اللاعبين
                </h3>
                
                <div class="space-y-2 sm:space-y-3">
                    @foreach($globalLeaderboard as $index => $player)
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-3 sm:p-4 bg-white/5 rounded-xl sm:rounded-2xl active:bg-white/10 transition-all duration-300 group touch-manipulation">
                            {{-- الترتيب والمعلومات --}}
                            <div class="flex items-center gap-3 sm:gap-4 flex-1 w-full sm:w-auto mb-2 sm:mb-0">
                                {{-- الرتبة --}}
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-gradient-to-r from-purple-500/20 to-blue-500/20 
                                            flex items-center justify-center group-active:scale-110 transition-transform duration-300 flex-shrink-0">
                                    <span class="font-bold text-white text-sm sm:text-base">#{{ $index + 1 }}</span>
                                </div>

                                {{-- الصورة --}}
                                <img src="{{ $player->user->avatar ? Storage::url($player->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($player->user->name).'&color=7F9CF5&background=EBF4FF' }}" 
                                     class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl object-cover border border-purple-400/30 flex-shrink-0">

                                {{-- المعلومات --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-white text-base sm:text-lg truncate">{{ $player->user->name }}</h4>
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 text-xs sm:text-sm text-gray-300">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-flag text-purple-300"></i>
                                            <span class="truncate">{{ Str::limit($player->user->country_name ?? $player->user->country, 15) }}</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-trophy text-yellow-300"></i>
                                            {{ $player->games_won }} فوز
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- النقاط والإحصائيات --}}
                            <div class="flex items-center gap-3 sm:gap-6 w-full sm:w-auto justify-between sm:justify-end">
                                {{-- معدل الفوز --}}
                                <div class="text-center">
                                    <div class="text-xs sm:text-sm text-gray-300 mb-1">معدل الفوز</div>
                                    <div class="text-base sm:text-lg font-bold text-green-300">{{ $player->win_rate }}%</div>
                                </div>

                                {{-- النقاط --}}
                                <div class="text-center min-w-[80px] sm:min-w-[100px]">
                                    <div class="text-xs sm:text-sm text-gray-300 mb-1">النقاط</div>
                                    <div class="text-lg sm:text-xl font-bold text-purple-300 flex items-center gap-1 sm:gap-2 justify-center">
                                        <i class="fas fa-gem text-sm sm:text-base"></i>
                                        {{ $player->points }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- الترقيم --}}
                @if($globalLeaderboard->hasPages())
                    <div class="mt-6">
                        {{ $globalLeaderboard->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- قسم التصنيف حسب الدولة --}}
        <div id="countryLeaderboard" class="space-y-6 hidden">
            {{-- فلترة الدول --}}
            <div class="glass-card rounded-2xl p-6 mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-filter text-purple-300"></i>
                        اختر الدولة
                    </h3>
                    <select id="countryFilter" 
                            class="px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 min-w-[200px]">
                        <option value="">جميع الدول</option>
                        @foreach($countries as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- إحصائيات الدول --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
                @php
                    $countryStats = [
                        ['title' => 'إجمالي الدول', 'value' => $totalCountries, 'icon' => 'flag', 'color' => 'from-purple-500 to-indigo-500'],
                        ['title' => 'إجمالي اللاعبين', 'value' => $totalPlayers, 'icon' => 'users', 'color' => 'from-blue-500 to-cyan-500'],
                        ['title' => 'أعلى نقاط', 'value' => $highestPoints, 'icon' => 'crown', 'color' => 'from-yellow-500 to-orange-500'],
                        ['title' => 'أكثر دولة فوزاً', 'value' => $topWinningCountry ? $topWinningCountry->name : 'لا توجد', 'icon' => 'trophy', 'color' => 'from-green-500 to-emerald-500'],
                    ];
                @endphp
                
                @foreach($countryStats as $stat)
                    <div class="glass-card rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 text-center floating-card">
                        <div class="w-10 h-10 sm:w-12 sm:h-14 mx-auto mb-2 sm:mb-3 lg:mb-4 bg-gradient-to-r {{ $stat['color'] }} rounded-lg sm:rounded-xl flex items-center justify-center">
                            <i class="fas fa-{{ $stat['icon'] }} text-white text-base sm:text-lg lg:text-xl"></i>
                        </div>
                        <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1 sm:mb-2">{{ $stat['value'] }}</div>
                        <div class="text-gray-300 text-xs sm:text-sm">{{ $stat['title'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- تصنيف الدول --}}
            <div class="glass-card rounded-3xl p-6">
                <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <i class="fas fa-flag text-purple-300"></i>
                    تصنيف الدول حسب متوسط النقاط
                </h3>

                <div class="space-y-4">
                    @foreach($countryLeaderboard as $index => $country)
                        <div class="country-item flex items-center justify-between p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition-all duration-300 group"
                             data-country="{{ $country->code }}">
                            {{-- الترتيب والمعلومات --}}
                            <div class="flex items-center gap-4 flex-1">
                                {{-- الرتبة --}}
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-purple-500/20 to-blue-500/20 
                                            flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <span class="font-bold text-white">#{{ $index + 1 }}</span>
                                </div>

                                {{-- العلم --}}
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-purple-500 to-blue-500 
                                            flex items-center justify-center text-white text-lg font-bold">
                                    {{ substr($country->name, 0, 2) }}
                                </div>

                                {{-- المعلومات --}}
                                <div class="flex-1">
                                    <h4 class="font-bold text-white text-lg">{{ $country->name }}</h4>
                                    <div class="flex items-center gap-4 text-sm text-gray-300">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-users text-purple-300"></i>
                                            {{ $country->player_count }} لاعب
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-trophy text-yellow-300"></i>
                                            {{ $country->total_wins }} فوز
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- الإحصائيات --}}
                            <div class="flex items-center gap-6">
                                {{-- متوسط النقاط --}}
                                <div class="text-center">
                                    <div class="text-sm text-gray-300 mb-1">متوسط النقاط</div>
                                    <div class="text-lg font-bold text-purple-300">{{ number_format($country->avg_points, 1) }}</div>
                                </div>

                                {{-- إجمالي النقاط --}}
                                <div class="text-center min-w-[100px]">
                                    <div class="text-sm text-gray-300 mb-1">إجمالي النقاط</div>
                                    <div class="text-xl font-bold text-blue-300 flex items-center gap-2">
                                        <i class="fas fa-gem"></i>
                                        {{ $country->total_points }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const globalTab = document.getElementById('globalTab');
    const countryTab = document.getElementById('countryTab');
    const globalLeaderboard = document.getElementById('globalLeaderboard');
    const countryLeaderboard = document.getElementById('countryLeaderboard');
    const countryFilter = document.getElementById('countryFilter');
    const countryItems = document.querySelectorAll('.country-item');

    // تبديل بين التصنيفات
    function switchTab(tab) {
        if (tab === 'global') {
            globalTab.className = 'px-6 py-3 rounded-xl font-medium transition-all duration-300 bg-gradient-to-r from-purple-600 to-blue-600 text-white shadow-lg';
            countryTab.className = 'px-6 py-3 rounded-xl font-medium transition-all duration-300 text-gray-300 hover:text-white hover:bg-white/5';
            globalLeaderboard.classList.remove('hidden');
            countryLeaderboard.classList.add('hidden');
        } else {
            globalTab.className = 'px-6 py-3 rounded-xl font-medium transition-all duration-300 text-gray-300 hover:text-white hover:bg-white/5';
            countryTab.className = 'px-6 py-3 rounded-xl font-medium transition-all duration-300 bg-gradient-to-r from-purple-600 to-blue-600 text-white shadow-lg';
            globalLeaderboard.classList.add('hidden');
            countryLeaderboard.classList.remove('hidden');
        }
    }

    globalTab.addEventListener('click', () => switchTab('global'));
    countryTab.addEventListener('click', () => switchTab('country'));

    // فلترة الدول
    if (countryFilter) {
        countryFilter.addEventListener('change', function() {
            const selectedCountry = this.value;
            
            countryItems.forEach(item => {
                if (!selectedCountry || item.dataset.country === selectedCountry) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.floating-card {
    transform: translateY(0);
    transition: all 0.3s ease;
}

.floating-card:hover {
    transform: translateY(-5px);
}

/* تخصيص الترقيم */
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.pagination .page-item .page-link {
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.75rem;
    color: #fff;
    transition: all 0.3s ease;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(to right, #9333ea, #3b82f6);
    border-color: #9333ea;
}

.pagination .page-item .page-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}
</style>
@endsection