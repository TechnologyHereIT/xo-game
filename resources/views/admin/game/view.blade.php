@extends('layouts.app')
@section('title', 'تفاصيل اللعبة - لوحة التحكم')

@section('content')
<div class="glass rounded-2xl p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">تفاصيل اللعبة #{{ $game->id }}</h1>
        <a href="{{ route('admin.games') }}" class="text-gray-300 hover:text-white">
            <i class="fas fa-arrow-left mr-2"></i>رجوع إلى الألعاب
        </a>
    </div>

    <!-- معلومات اللعبة الأساسية -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="glass rounded-xl p-4">
            <h3 class="text-lg font-bold mb-4">معلومات اللعبة</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-300">الحالة:</span>
                    <span class="px-2 py-1 rounded-full text-xs 
                        @if($game->status == 'active') bg-green-500/20 text-green-300
                        @elseif($game->status == 'completed') bg-blue-500/20 text-blue-300
                        @else bg-gray-500/20 text-gray-300
                        @endif">
                        {{ $game->status }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-300">نوع اللعبة:</span>
                    <span>{{ $game->game_type == 'computer' ? 'ضد الكمبيوتر' : 'ضد لاعب' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-300">تاريخ الإنشاء:</span>
                    <span>{{ $game->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-300">المدة:</span>
                    <span>{{ $game->created_at->diffForHumans($game->updated_at) }}</span>
                </div>
            </div>
        </div>

        <div class="glass rounded-xl p-4">
            <h3 class="text-lg font-bold mb-4">اللاعبين</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $game->player1->user->avatar ? Storage::url($game->player1->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($game->player1->user->name) }}" 
                             class="w-10 h-10 rounded-full">
                        <div>
                            <div class="font-semibold">{{ $game->player1->user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $game->player1->points }} نقطة</div>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-blue-400">X</span>
                </div>
                
                @if($game->player2)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $game->player2->user->avatar ? Storage::url($game->player2->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($game->player2->user->name) }}" 
                             class="w-10 h-10 rounded-full">
                        <div>
                            <div class="font-semibold">{{ $game->player2->user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $game->player2->points }} نقطة</div>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-red-400">O</span>
                </div>
                @else
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-robot text-3xl text-gray-400"></i>
                        <div>
                            <div class="font-semibold">الكمبيوتر</div>
                            <div class="text-xs text-gray-400">مستوى: {{ $game->difficulty ?? 'متوسط' }}</div>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-red-400">O</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- سجل الحركات -->
    <div class="glass rounded-xl p-4 mb-6">
        <h3 class="text-lg font-bold mb-4">سجل الحركات</h3>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @foreach($game->moves as $move)
            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                        @if($move->player == 1) bg-blue-500/20 text-blue-300
                        @else bg-red-500/20 text-red-300
                        @endif">
                        @if($move->player == 1) X @else O @endif
                    </span>
                    <span>
                        @if($move->player == 1)
                            {{ $game->player1->user->name }}
                        @else
                            {{ $game->player2 ? $game->player2->user->name : 'الكمبيوتر' }}
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-gray-300">الصف {{ $move->row }}، العمود {{ $move->col }}</span>
                    <span class="text-xs text-gray-500">{{ $move->created_at->format('H:i:s') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- لوحة اللعبة -->
    <div class="glass rounded-xl p-4 mb-6">
        <h3 class="text-lg font-bold mb-4">حالة اللوحة</h3>
        <div class="grid grid-cols-3 gap-2 max-w-md mx-auto">
            @for($i = 0; $i < 3; $i++)
                @for($j = 0; $j < 3; $j++)
                    <div class="aspect-square glass rounded-lg flex items-center justify-center text-4xl font-bold
                        @if($game->board[$i][$j] == 'X') text-blue-400
                        @elseif($game->board[$i][$j] == 'O') text-red-400
                        @else text-gray-600
                        @endif">
                        {{ $game->board[$i][$j] ?? '' }}
                    </div>
                @endfor
            @endfor
        </div>
    </div>

    <!-- الفائز -->
    @if($game->winner)
    <div class="glass rounded-xl p-4 bg-green-500/10 border border-green-500/30">
        <div class="flex items-center justify-center gap-3">
            <i class="fas fa-trophy text-2xl text-yellow-400"></i>
            <span class="text-xl font-bold">الفائز: 
                @if($game->winner == 'draw')
                    تعادل
                @elseif($game->winner == 'X')
                    {{ $game->player1->user->name }}
                @else
                    {{ $game->player2 ? $game->player2->user->name : 'الكمبيوتر' }}
                @endif
            </span>
        </div>
    </div>
    @endif

    <!-- أزرار التحكم -->
    <div class="flex gap-4 mt-6">
        <button onclick="deleteGame({{ $game->id }})" class="bg-red-600 hover:bg-red-700 px-6 py-2 rounded-lg transition">
            <i class="fas fa-trash mr-2"></i>حذف اللعبة
        </button>
        <a href="{{ route('game.debug', $game->id) }}" class="bg-yellow-600 hover:bg-yellow-700 px-6 py-2 rounded-lg transition">
            <i class="fas fa-bug mr-2"></i>تصحيح الأخطاء
        </a>
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
                window.location.href = '{{ route("admin.games") }}';
            }
        });
    }
}
</script>
@endsection