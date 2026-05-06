@extends('layouts.app')
@section('title', 'إدارة الألعاب - لوحة التحكم')

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
                    <div class="w-16 h-16 rounded-2xl bg-blue-500/20 flex items-center justify-center">
                        <i class="fas fa-chess-board text-2xl text-blue-300"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">إدارة الألعاب</h1>
                        <p class="text-purple-200 mt-1">عرض وإدارة جميع الألعاب في النظام</p>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <input type="text" placeholder="ابحث عن لعبة..." 
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
                <div class="text-3xl font-bold text-blue-400 mb-2">{{ $totalGames }}</div>
                <div class="text-sm text-gray-300">إجمالي الألعاب</div>
            </div>
            <div class="stats-card p-6 text-center">
                <div class="text-3xl font-bold text-green-400 mb-2">{{ $activeGames }}</div>
                <div class="text-sm text-gray-300">نشطة حالياً</div>
            </div>
            <div class="stats-card p-6 text-center">
                <div class="text-3xl font-bold text-yellow-400 mb-2">{{ $completedGames }}</div>
                <div class="text-sm text-gray-300">مكتملة</div>
            </div>
            <div class="stats-card p-6 text-center">
                <div class="text-3xl font-bold text-red-400 mb-2">{{ $abandonedGames }}</div>
                <div class="text-sm text-gray-300">متروكة</div>
            </div>
        </div>

        <!-- Games Table -->
        <div class="glass-card rounded-3xl p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/20">
                            <th class="text-right p-4 font-semibold text-gray-300">معرف اللعبة</th>
                            <th class="text-right p-4 font-semibold text-gray-300">اللاعب 1</th>
                            <th class="text-right p-4 font-semibold text-gray-300">اللاعب 2</th>
                            <th class="text-right p-4 font-semibold text-gray-300">الحالة</th>
                            <th class="text-right p-4 font-semibold text-gray-300">تاريخ الإنشاء</th>
                            <th class="text-right p-4 font-semibold text-gray-300">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($games as $game)
                        <tr class="border-b border-white/10 hover:bg-white/5 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center">
                                        <i class="fas fa-chess text-purple-300"></i>
                                    </div>
                                    <span class="font-semibold text-white">#{{ $game->id }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $game->player1->user->avatar ? Storage::url($game->player1->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($game->player1->user->name) }}" 
                                         class="w-10 h-10 rounded-xl border-2 border-blue-400">
                                    <div>
                                        <div class="font-semibold text-white">{{ $game->player1->user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $game->player1->points }} نقطة</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($game->player2)
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $game->player2->user->avatar ? Storage::url($game->player2->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($game->player2->user->name) }}" 
                                             class="w-10 h-10 rounded-xl border-2 border-red-400">
                                        <div>
                                            <div class="font-semibold text-white">{{ $game->player2->user->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $game->player2->points }} نقطة</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-500/20 flex items-center justify-center">
                                            <i class="fas fa-robot text-gray-400"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-300">الكمبيوتر</div>
                                            <div class="text-xs text-gray-400">مستوى متوسط</div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-2 rounded-xl text-sm font-semibold 
                                    @if($game->status == 'active') bg-green-500/20 text-green-300 border border-green-500/30
                                    @elseif($game->status == 'completed') bg-blue-500/20 text-blue-300 border border-blue-500/30
                                    @else bg-gray-500/20 text-gray-300 border border-gray-500/30
                                    @endif">
                                    {{ $game->status }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="text-white">{{ $game->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-400">{{ $game->created_at->format('H:i') }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.game.view', $game->id) }}" 
                                       class="w-10 h-10 bg-blue-500/20 hover:bg-blue-500/30 rounded-xl flex items-center justify-center transition group">
                                        <i class="fas fa-eye text-blue-300 group-hover:text-blue-200"></i>
                                    </a>
                                    <button onclick="deleteGame({{ $game->id }})" 
                                            class="w-10 h-10 bg-red-500/20 hover:bg-red-500/30 rounded-xl flex items-center justify-center transition group">
                                        <i class="fas fa-trash text-red-300 group-hover:text-red-200"></i>
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
                {{ $games->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function deleteGame(gameId) {
    if (confirm('هل أنت متأكد من حذف هذه اللعبة؟')) {
        fetch(`/admin/game/${gameId}`, {
            method: 'DELETE',
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