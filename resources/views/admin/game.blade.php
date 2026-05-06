@extends('layouts.app')
@section('title', 'تفاصيل اللعبة #' . optional($game)->id . ' – لوحة التحكم')

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
    .board-cell {
        transition: all 0.3s ease;
    }
    .board-cell:hover {
        transform: scale(1.05);
    }
</style>

<div class="min-h-screen gradient-bg pb-12">
    <div class="container mx-auto px-4 pt-8">
        <!-- Header -->
        <div class="glass-card rounded-3xl p-8 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-purple-500/20 flex items-center justify-center">
                        <i class="fas fa-chess text-2xl text-purple-300"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">تفاصيل اللعبة 
                            <span class="text-purple-300">#{{ optional($game)->id ?? '-' }}</span>
                        </h1>
                        <p class="text-purple-200 mt-1">
                            أُنشئت فى {{ optional(optional($game)->created_at)->format('Y-m-d H:i') ?? 'غير متاح' }}
                        </p>
                    </div>
                </div>
                
                <a href="{{ route('admin.games') }}" 
                   class="glass-card px-6 py-3 rounded-2xl text-white hover:bg-white/10 transition flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    رجوع إلى الألعاب
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @php
                $gameStatus = optional($game)->status ?? '-';
                $movesCount = optional($game)->moves ? $game->moves->count() : 0;
                $stats = [
                    [
                        'label' => 'الحالة',
                        'value' => $gameStatus,
                        'color' => $gameStatus == 'active' ? 'green' : ($gameStatus == 'completed' ? 'blue' : 'gray'),
                        'icon' => 'info-circle'
                    ],
                    [
                        'label' => 'النوع',
                        'value' => optional($game)->game_type == 'computer' ? 'ضد الكمبيوتر' : 'ضد لاعب',
                        'color' => 'purple',
                        'icon' => 'users'
                    ],
                    [
                        'label' => 'عدد الحركات',
                        'value' => $movesCount,
                        'color' => 'yellow',
                        'icon' => 'route'
                    ],
                    [
                        'label' => 'الفائز',
                        'value' => optional($game)->winner ? (optional($game)->winner == 'draw' ? 'تعادل' : optional($game)->winner) : '–',
                        'color' => 'red',
                        'icon' => 'trophy'
                    ]
                ];
            @endphp
            
            @foreach($stats as $s)
            <div class="stats-card p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-300 text-sm mb-2">{{ $s['label'] }}</p>
                        <p class="text-2xl font-bold text-{{ $s['color'] }}-400">{{ $s['value'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-{{ $s['color'] }}-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-{{ $s['icon'] }} text-{{ $s['color'] }}-300 text-xl"></i>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Players Section -->
        <div class="grid lg:grid-cols-2 gap-6 mb-8">
            <!-- Player 1 -->
            <div class="glass-card rounded-3xl p-6">
                <div class="flex items-center gap-4 mb-4">
                    @if(optional($game)->player1 && optional($game->player1)->user)
                        <img src="{{ optional($game->player1->user)->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($game->player1->user->name) }}"
                             class="w-16 h-16 rounded-2xl border-4 border-blue-400">
                        <div>
                            <div class="font-bold text-2xl text-blue-300">X – {{ $game->player1->user->name }}</div>
                            <div class="text-gray-300">{{ optional($game->player1)->points ?? 0 }} نقطة</div>
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-gray-500/20 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-400"></i>
                        </div>
                        <div>
                            <div class="font-bold text-2xl text-blue-300">X – لاعب غير متاح</div>
                            <div class="text-gray-300">0 نقطة</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Player 2 -->
            <div class="glass-card rounded-3xl p-6">
                <div class="flex items-center gap-4 mb-4">
                    @if(optional($game)->player2 && optional($game->player2)->user)
                        <img src="{{ optional($game->player2->user)->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($game->player2->user->name) }}"
                             class="w-16 h-16 rounded-2xl border-4 border-red-400">
                        <div>
                            <div class="font-bold text-2xl text-red-300">O – {{ $game->player2->user->name }}</div>
                            <div class="text-gray-300">{{ optional($game->player2)->points ?? 0 }} نقطة</div>
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-gray-500/20 flex items-center justify-center">
                            <i class="fas fa-robot text-2xl text-gray-400"></i>
                        </div>
                        <div>
                            <div class="font-bold text-2xl text-red-300">O – الكمبيوتر</div>
                            <div class="text-gray-300">المستوى: {{ optional($game)->difficulty ?? 'متوسط' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Game Board -->
        <div class="glass-card rounded-3xl p-6 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">لوحة اللعبة</h2>
            @php
                $flat  = json_decode(optional($game)->board ?? '[]', true);
                $board = array_chunk(array_pad($flat, 9, ''), 3);
            @endphp

            <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                @foreach ($board as $rowIndex => $row)
                    @foreach ($row as $colIndex => $cell)
                        <div class="aspect-square glass-card board-cell flex items-center justify-center text-5xl font-bold
                            {{ $cell === 'X' ? 'text-blue-400 border-blue-400' : ($cell === 'O' ? 'text-red-400 border-red-400' : 'text-gray-600 border-gray-600') }} 
                            border-2 rounded-2xl">
                            {{ $cell }}
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <!-- Moves History -->
        <div class="glass-card rounded-3xl p-6">
            <h2 class="text-2xl font-bold text-white mb-6">سجل الحركات</h2>
            <div class="space-y-4">
                @php
                    $gameMoves = optional($game)->moves ? $game->moves()->orderBy('id')->get() : collect();
                @endphp
                
                @forelse($gameMoves as $move)
                    @php
                        $playerSymbol = optional($move->player)->id === optional($game->player1)->id ? 'X' : 'O';
                        $playerName   = $playerSymbol === 'X'
                                    ? optional(optional($game)->player1)->user->name ?? 'لاعب غير متاح'
                                    : optional(optional($game)->player2)->user->name ?? 'الكمبيوتر';
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition">
                        <div class="flex items-center gap-4">
                            <span class="w-12 h-12 rounded-xl flex items-center justify-center text-lg font-bold
                                {{ $playerSymbol === 'X' ? 'bg-blue-500/20 text-blue-300' : 'bg-red-500/20 text-red-300' }}">
                                {{ $playerSymbol }}
                            </span>
                            <div>
                                <span class="font-semibold text-white">{{ $playerName }}</span>
                                <div class="text-sm text-gray-400">الموقع: {{ $move->position ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-white">{{ optional($move->created_at)->format('H:i:s') ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ optional($move->created_at)->format('Y-m-d') ?? '-' }}</div>
                            @if(isset($move->correct_answer))
                                <div class="text-xs {{ $move->correct_answer ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $move->correct_answer ? '✓ صحيح' : '✗ خطأ' }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-500/20 flex items-center justify-center">
                            <i class="fas fa-route text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-400 text-lg">لا توجد حركات بعد.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Action Buttons -->
        @if($game)
        <div class="flex items-center gap-4 flex-wrap mt-8">
            @if(optional($game)->status === 'active')
                <button onclick="endGame({{ $game->id }})" 
                        class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 px-6 py-3 rounded-2xl text-white font-semibold transition flex items-center gap-2">
                    <i class="fas fa-stop"></i>
                    إنهاء اللعبة
                </button>
            @endif
            
            @if(optional($game)->id)
                <a href="{{ route('game.debug', $game->id) }}"
                   class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 px-6 py-3 rounded-2xl text-white font-semibold transition flex items-center gap-2">
                    <i class="fas fa-bug"></i>
                    تصحيح الأخطاء
                </a>
            @endif

            <button onclick="deleteGame({{ $game->id }})" 
                    class="bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 px-6 py-3 rounded-2xl text-white font-semibold transition flex items-center gap-2">
                <i class="fas fa-trash"></i>
                حذف اللعبة
            </button>
        </div>
        @endif
    </div>
</div>

<script>
function deleteGame(id) {
    if (confirm('متأكد من حذف اللعبة رقم ' + id + '؟')) {
        fetch(`/admin/game/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(r => { if (r.ok) window.location.href = '{{ route("admin.games") }}'; });
    }
}

function endGame(id) {
    if (confirm('إنهاء اللعبة الآن؟')) {
        fetch(`/admin/game/${id}/end`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(r => { if (r.ok) location.reload(); });
    }
}
</script>
@endsection