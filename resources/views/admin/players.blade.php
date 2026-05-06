@extends('layouts.app')
@section('title', 'إدارة اللاعبين - لوحة التحكم')

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
</style>

<div class="min-h-screen gradient-bg pb-12">
    <div class="container mx-auto px-4 pt-8">
        <!-- Header -->
        <div class="glass-card rounded-3xl p-8 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-green-500/20 flex items-center justify-center">
                        <i class="fas fa-users text-2xl text-green-300"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">إدارة اللاعبين</h1>
                        <p class="text-purple-200 mt-1">عرض وإدارة جميع اللاعبين في النظام</p>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <input type="text" placeholder="ابحث عن لاعب..." 
                           class="glass-card px-4 py-3 rounded-2xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 w-64">
                    <button class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 px-6 py-3 rounded-2xl text-white font-semibold transition-all">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stats-card p-6 text-center">
                <div class="text-3xl font-bold text-blue-400 mb-2">{{ $totalPlayers }}</div>
                <div class="text-sm text-gray-300">إجمالي اللاعبين</div>
            </div>
            <div class="stats-card p-6 text-center">
                <div class="text-3xl font-bold text-green-400 mb-2">{{ $onlinePlayers }}</div>
                <div class="text-sm text-gray-300">متصل الآن</div>
            </div>
            <div class="stats-card p-6 text-center">
                <div class="text-3xl font-bold text-yellow-400 mb-2">{{ $activePlayers }}</div>
                <div class="text-sm text-gray-300">نشطون</div>
            </div>
            <div class="stats-card p-6 text-center">
                <div class="text-3xl font-bold text-purple-400 mb-2">{{ $avgPoints }}</div>
                <div class="text-sm text-gray-300">متوسط النقاط</div>
            </div>
        </div>

        <!-- Players Table -->
        <div class="glass-card rounded-3xl p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/20">
                            <th class="text-right p-4 font-semibold text-gray-300">اللاعب</th>
                            <th class="text-right p-4 font-semibold text-gray-300">البريد الإلكتروني</th>
                            <th class="text-right p-4 font-semibold text-gray-300">النقاط</th>
                            <th class="text-right p-4 font-semibold text-gray-300">الألعاب</th>
                            <th class="text-right p-4 font-semibold text-gray-300">معدل الفوز</th>
                            <th class="text-right p-4 font-semibold text-gray-300">الحالة</th>
                            <th class="text-right p-4 font-semibold text-gray-300">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($players as $player)
                        <tr class="border-b border-white/10 hover:bg-white/5 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $player->user->avatar ? Storage::url($player->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($player->user->name) }}" 
                                         class="w-12 h-12 rounded-xl border-2 border-purple-400">
                                    <div>
                                        <div class="font-semibold text-white">{{ $player->user->name }}</div>
                                        <div class="text-xs text-gray-400">#{{ $player->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="text-white">{{ $player->user->email }}</div>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-purple-300 text-lg">{{ $player->points }}</span>
                            </td>
                            <td class="p-4">
                                <span class="text-white font-semibold">{{ $player->games_played }}</span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-600 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $player->win_rate }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-white w-12">{{ $player->win_rate }}%</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-2 rounded-xl text-sm font-semibold 
                                    @if($player->user->last_seen && $player->user->last_seen->diffInMinutes(now()) < 5) 
                                        bg-green-500/20 text-green-300 border border-green-500/30
                                    @else 
                                        bg-gray-500/20 text-gray-300 border border-gray-500/30
                                    @endif">
                                    @if($player->user->last_seen && $player->user->last_seen->diffInMinutes(now()) < 5)
                                        متصل
                                    @else
                                        غير متصل
                                    @endif
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex gap-2">
                                    <button onclick="editPlayer({{ $player->id }})" 
                                            class="w-10 h-10 bg-blue-500/20 hover:bg-blue-500/30 rounded-xl flex items-center justify-center transition group">
                                        <i class="fas fa-edit text-blue-300 group-hover:text-blue-200"></i>
                                    </button>
                                    <button onclick="resetPlayer({{ $player->id }})" 
                                            class="w-10 h-10 bg-yellow-500/20 hover:bg-yellow-500/30 rounded-xl flex items-center justify-center transition group">
                                        <i class="fas fa-redo text-yellow-300 group-hover:text-yellow-200"></i>
                                    </button>
                                    <button onclick="banPlayer({{ $player->id }})" 
                                            class="w-10 h-10 bg-red-500/20 hover:bg-red-500/30 rounded-xl flex items-center justify-center transition group">
                                        <i class="fas fa-ban text-red-300 group-hover:text-red-200"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $players->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function editPlayer(playerId) {
    alert('تعديل اللاعب: ' + playerId);
}

function resetPlayer(playerId) {
    if (confirm('هل أنت متأكد من إعادة تعيين إحصائيات هذا اللاعب؟')) {
        fetch(`/admin/player/${playerId}/reset`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}

function banPlayer(playerId) {
    if (confirm('هل أنت متأكد من حظر هذا اللاعب؟')) {
        fetch(`/admin/player/${playerId}/ban`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endsection