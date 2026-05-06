@extends('layouts.app')
@section('title','البطولات')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-900 via-orange-900 to-red-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="glass rounded-3xl p-8 mb-8 shadow-2xl">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
                <div class="text-center lg:text-right">
                    <h1 class="text-4xl font-bold bg-gradient-to-l from-amber-200 to-orange-200 bg-clip-text text-transparent">
                        البطولات
                    </h1>
                    <p class="mt-2 text-amber-200">انضم إلى البطولات وتحدى اللاعبين الآخرين</p>
                </div>
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl px-6 py-3 text-white transition-all duration-300 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-arrow-right transform group-hover:-translate-x-1 transition-transform"></i>
                    <span>رجوع للرئيسية</span>
                </a>
            </div>
        </div>

        <!-- Tournaments Grid -->
        @if($tournaments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @foreach($tournaments as $t)
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-300"></div>
                <div class="relative bg-gray-900/80 backdrop-blur-xl border border-white/10 rounded-2xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                    <!-- Tournament Header -->
                    <div class="flex justify-between items-center mb-4">
                        @php
                            $statusColors = [
                                'مفتوحة' => 'bg-emerald-500/30 text-emerald-200 border-emerald-400/30',
                                'جارية' => 'bg-amber-500/30 text-amber-200 border-amber-400/30',
                                'منتهية' => 'bg-gray-500/30 text-gray-200 border-gray-400/30',
                                'معلقة' => 'bg-blue-500/30 text-blue-200 border-blue-400/30'
                            ];
                            $statusColor = $statusColors[$t->status] ?? 'bg-gray-500/30 text-gray-200';
                        @endphp
                        <span class="text-xs {{ $statusColor }} px-3 py-1.5 rounded-full border font-medium">
                            {{ $t->status }}
                        </span>
                        <span class="text-xs text-gray-300 bg-white/5 px-3 py-1.5 rounded-full">
                            <i class="fas fa-calendar ml-1"></i>
                            {{ $t->start_date->format('Y/m/d') }}
                        </span>
                    </div>

                    <!-- Tournament Info -->
                    <div class="mb-4">
                        <h2 class="font-bold text-xl text-white mb-3 line-clamp-2">{{ $t->name }}</h2>
                        <p class="text-sm text-gray-300 line-clamp-3 leading-relaxed">{{ $t->description }}</p>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-400 mb-2">
                            <span>عدد اللاعبين</span>
                            <span>{{ $t->current_players }}/{{ $t->max_players }}</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-2">
                            @php
                                $progress = ($t->current_players / $t->max_players) * 100;
                                $progressColor = $progress >= 90 ? 'bg-red-500' : ($progress >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                            @endphp
                            <div class="h-2 rounded-full {{ $progressColor }} transition-all duration-500" 
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2 text-sm text-gray-300">
                            <i class="fas fa-users"></i>
                            <span>{{ $t->current_players }} لاعب</span>
                        </div>
                        <a href="{{ route('tournaments.show', $t->id) }}" 
                           class="group flex items-center gap-2 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-400/30 text-amber-300 hover:text-amber-200 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg">
                            <span class="font-medium">التفاصيل</span>
                            <i class="fas fa-arrow-left transform group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <!-- Ribbon for featured tournaments -->
                    @if($t->status === 'مفتوحة' && $t->current_players < $t->max_players)
                    <div class="absolute -top-2 -right-2">
                        <div class="bg-gradient-to-r from-amber-400 to-orange-400 text-gray-900 text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            <i class="fas fa-bolt ml-1"></i>
                            انضم الآن
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="glass rounded-3xl p-12 text-center shadow-2xl">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 mx-auto mb-6 bg-white/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-trophy text-3xl text-amber-300"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">لا توجد بطولات حالية</h3>
                <p class="text-amber-200 mb-6">سيتم الإعلان عن البطولات الجديدة قريباً</p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-medium px-6 py-3 rounded-xl transition-all duration-300 hover:scale-105">
                        <i class="fas fa-home"></i>
                        الرئيسية
                    </a>
                    <button onclick="location.reload()" 
                            class="inline-flex items-center gap-2 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 font-medium px-6 py-3 rounded-xl transition-all duration-300 hover:scale-105">
                        <i class="fas fa-redo"></i>
                        تحديث
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Pagination (if needed) -->
        @if($tournaments->count() > 0 && method_exists($tournaments, 'links'))
        <div class="glass rounded-2xl p-6 shadow-2xl">
            {{ $tournaments->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection