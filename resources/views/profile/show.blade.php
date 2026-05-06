@extends('layouts.app')
@section('title','الملف الشخصي')
@section('content')

<div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- البطاقة الشخصية --}}
        <section class="glass-card rounded-3xl overflow-hidden shadow-2xl relative">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-blue-500/10"></div>
            <div class="relative p-8 flex flex-col lg:flex-row items-center gap-8">
                {{-- الصورة --}}
                <div class="relative shrink-0">
                    <div class="relative">
                        <img id="avatarPreview"
                             class="w-32 h-32 rounded-2xl object-cover border-4 border-purple-400 shadow-lg"
                             src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF' }}"
                             alt="Avatar">
                        <div class="absolute -inset-4 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl blur-lg opacity-30"></div>
                    </div>
                    @if(Auth::id() === $user->id)
                        <label for="avatarInput"
                               class="absolute -bottom-2 -right-2 bg-gradient-to-r from-purple-500 to-blue-500 text-white p-3 rounded-xl cursor-pointer hover:from-purple-600 hover:to-blue-600 transition-all duration-300 shadow-lg hover:scale-110 group">
                            <i class="fas fa-camera text-sm group-hover:animate-pulse"></i>
                        </label>
                        <input type="file" id="avatarInput" class="hidden" accept="image/*"
                               onchange="uploadAvatar(this)">
                    @endif
                </div>

                {{-- المعلومات الأساسية --}}
                <div class="text-center lg:text-right flex-1">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-200 to-blue-200 bg-clip-text text-transparent mb-2">
                        {{ $user->name }}
                    </h1>
                    <p class="text-gray-300 text-lg mb-1">{{ $user->email }}</p>
                    <p class="text-sm text-purple-300">
                        <i class="fas fa-calendar ml-1"></i>
                        منضم منذ {{ $user->created_at->diffForHumans() }}
                    </p>
                    
                    @if(Auth::id() === $user->id)
                        <button onclick="openEditModal()"
                                class="mt-6 inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg group">
                            <i class="fas fa-edit group-hover:animate-pulse"></i>
                            تعديل البيانات
                        </button>
                    @endif
                </div>

                {{-- النقاط --}}
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center min-w-[140px] border border-white/10">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <i class="fas fa-gem text-purple-300 text-xl"></i>
                        <span class="text-3xl font-bold text-white">{{ $user->player->points ?? 0 }}</span>
                    </div>
                    <div class="text-purple-200 text-sm">إجمالي النقاط</div>
                </div>
            </div>

            {{-- الدولة والترتيب --}}
            <div class="relative pb-8 px-8">
                <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                    {{-- الدولة --}}
                    @if($user->country)
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 text-center min-w-[140px] border border-white/10">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <i class="fas fa-flag text-purple-300 text-xl"></i>
                            <span class="text-lg font-bold text-white">
                                @php
                                    $countries = [
                                        'SA' => 'السعودية', 'EG' => 'مصر', 'AE' => 'الإمارات', 'KW' => 'الكويت',
                                        'QA' => 'قطر', 'BH' => 'البحرين', 'OM' => 'عمان', 'JO' => 'الأردن',
                                        'LB' => 'لبنان', 'SY' => 'سوريا', 'IQ' => 'العراق', 'DZ' => 'الجزائر',
                                        'MA' => 'المغرب', 'TN' => 'تونس', 'SD' => 'السودان', 'YE' => 'اليمن',
                                        'LY' => 'ليبيا', 'PS' => 'فلسطين'
                                    ];
                                @endphp
                                {{ $countries[$user->country] ?? $user->country }}
                            </span>
                        </div>
                        <div class="text-purple-200 text-sm">الدولة</div>
                    </div>
                    @endif

                    {{-- الترتيب العام --}}
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 text-center min-w-[140px] border border-white/10">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <i class="fas fa-globe text-blue-300 text-xl"></i>
                            <span class="text-lg font-bold text-white">#{{ $globalRank }}</span>
                        </div>
                        <div class="text-blue-200 text-sm">الترتيب العام</div>
                    </div>

                    {{-- الترتيب في الدولة --}}
                    @if($user->country)
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 text-center min-w-[140px] border border-white/10">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <i class="fas fa-trophy text-yellow-300 text-xl"></i>
                            <span class="text-lg font-bold text-white">#{{ $countryRank }}</span>
                        </div>
                        <div class="text-yellow-200 text-sm">في {{ $countries[$user->country] ?? $user->country }}</div>
                    </div>
                    @endif

                    {{-- زر مشاركة البطاقة --}}
                    @if(Auth::id() === $user->id)
                    <button onclick="generateShareCard()"
                            class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 text-center min-w-[140px] border border-white/10 hover:bg-white/20 transition-all duration-300 group">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <i class="fas fa-share-alt text-green-300 text-xl group-hover:animate-pulse"></i>
                        </div>
                        <div class="text-green-200 text-sm">مشاركة البروفايل</div>
                    </button>
                    @endif
                </div>
            </div>
        </section>

        {{-- الإحصائيات السريعة --}}
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $stats = [
                    [
                        'title' => 'النقاط',
                        'value' => $user->player->points ?? 0,
                        'icon' => 'star',
                        'color' => 'from-purple-500 to-indigo-500',
                        'bg' => 'bg-purple-500/20',
                        'max' => 1000
                    ],
                    [
                        'title' => 'الألعاب',
                        'value' => $user->player->games_played ?? 0,
                        'icon' => 'chess-board',
                        'color' => 'from-emerald-500 to-green-500',
                        'bg' => 'bg-emerald-500/20',
                        'max' => 100
                    ],
                    [
                        'title' => 'معدل الفوز',
                        'value' => $user->player->win_rate ?? 0,
                        'icon' => 'trophy',
                        'color' => 'from-amber-500 to-yellow-500',
                        'bg' => 'bg-amber-500/20',
                        'max' => 100
                    ],
                    [
                        'title' => 'الإجابات الصحيحة',
                        'value' => $correctAnswers ?? 0,
                        'icon' => 'check-circle',
                        'color' => 'from-sky-500 to-blue-500',
                        'bg' => 'bg-sky-500/20',
                        'max' => 50
                    ]
                ];
            @endphp
            
            @foreach($stats as $stat)
                @php
                    // تحويل القيمة إلى رقم والتأكد من أنها صالحة
                    $numericValue = floatval($stat['value']);
                    $percentage = $stat['max'] > 0 ? min(100, ($numericValue / $stat['max']) * 100) : 0;
                @endphp
                <div class="glass-card rounded-2xl p-6 floating-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-300 text-sm mb-2">{{ $stat['title'] }}</p>
                            <p class="text-2xl font-bold text-white">{{ $stat['value'] }}</p>
                        </div>
                        <div class="w-14 h-14 {{ $stat['bg'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-{{ $stat['icon'] }} text-xl bg-gradient-to-r {{ $stat['color'] }} bg-clip-text text-transparent"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-600 rounded-full h-2">
                            <div class="h-2 rounded-full bg-gradient-to-r {{ $stat['color'] }} transition-all duration-1000" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- الرسم البياني والألعاب الحديثة --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            {{-- الرسم البياني الأسبوعي --}}
            <section class="glass-card rounded-3xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <i class="fas fa-chart-line text-purple-300"></i>
                    أداؤك هذا الأسبوع
                </h3>
                <div class="h-64">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </section>

            {{-- آخر 5 ألعاب --}}
            <section class="glass-card rounded-3xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-gamepad text-purple-300"></i>
                        آخر الألعاب
                    </h3>
                    <a href="{{ route('game.history', $user) }}" 
                       class="text-sm text-purple-300 hover:text-purple-200 transition-colors duration-300 group">
                        عرض الكل
                        <i class="fas fa-arrow-left group-hover:animate-bounce ml-1"></i>
                    </a>
                </div>
                @if($recentGames && $recentGames->count())
                    <div class="space-y-4">
                        @foreach($recentGames as $game)
                            <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition-all duration-300 group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-chess text-purple-300"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400">{{ $game->created_at->diffForHumans() }}</span>
                                        <p class="text-sm text-gray-200">
                                            ضد <span class="text-purple-300">{{ $game->opponentNameFor($user) }}</span>
                                        </p>
                                    </div>
                                </div>
                                <span class="px-4 py-2 text-xs rounded-full font-medium
                                    {{ $game->resultBadgeFor($user)['class'] ?? 'bg-gray-500 text-white' }} group-hover:scale-110 transition-transform duration-300">
                                    {{ $game->resultBadgeFor($user)['text'] ?? 'غير معروف' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-gamepad text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-300">لا توجد ألعاب بعد</p>
                    </div>
                @endif
            </section>
        </div>

        {{-- الإنجازات --}}
        <section class="glass-card rounded-3xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <i class="fas fa-trophy text-purple-300"></i>
                    الإنجازات
                </h3>
                @php
                    $unlockedCount = 0;
                    $totalCount = 0;
                    if (isset($achievements) && is_array($achievements)) {
                        $unlockedCount = count(array_filter($achievements, function($ach) {
                            return isset($ach['unlocked']) && $ach['unlocked'];
                        }));
                        $totalCount = count($achievements);
                    }
                @endphp
                <span class="text-sm text-gray-300">
                    {{ $unlockedCount }}/{{ $totalCount }} مكتمل
                </span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @if(isset($achievements) && is_array($achievements) && count($achievements) > 0)
                    @foreach($achievements as $ach)
                        <div class="group relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl blur opacity-0 group-hover:opacity-20 transition duration-300"></div>
                            <div @class([
                                    'relative p-4 rounded-2xl border text-center transition-all duration-300 transform hover:scale-105',
                                    (isset($ach['unlocked']) && $ach['unlocked'])
                                        ? 'bg-gradient-to-br from-emerald-500/20 to-green-500/10 border-emerald-400/30'
                                        : 'bg-white/5 border-gray-600/30'
                                 ])>
                                <i @class([
                                        'text-3xl mb-3',
                                        (isset($ach['unlocked']) && $ach['unlocked']) ? 'text-emerald-300' : 'text-gray-400'
                                   ]) class="fas fa-{{ $ach['icon'] ?? 'question' }}"></i>
                                <div @class([
                                        'text-sm font-medium mb-2',
                                        (isset($ach['unlocked']) && $ach['unlocked']) ? 'text-emerald-200' : 'text-gray-400'
                                     ])>{{ $ach['title'] ?? 'إنجاز' }}</div>
                                @if(isset($ach['unlocked']) && $ach['unlocked'])
                                    <div class="absolute -top-2 -right-2">
                                        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-xs text-white"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-trophy text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-300">لا توجد إنجازات متاحة</p>
                    </div>
                @endif
            </div>
        </section>

    </div>
</div>

{{-- مودال تعديل البيانات --}}
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 z-50 transition-opacity duration-300">
    <div class="glass-card rounded-3xl shadow-2xl w-full max-w-xl transform transition-all duration-300 scale-95"
         id="modalContent">
        <form id="editForm" class="p-8 space-y-6">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-white mb-2">تعديل البيانات</h3>
                <p class="text-gray-300">قم بتحديث معلوماتك الشخصية</p>
            </div>
            
            {{-- الاسم --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
                    <i class="fas fa-user text-purple-400"></i>
                    الاسم
                </label>
                <input name="name" value="{{ $user->name }}" required
                       class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
            </div>
            
            {{-- البريد --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
                    <i class="fas fa-envelope text-purple-400"></i>
                    البريد الإلكتروني
                </label>
                <input type="email" name="email" value="{{ $user->email }}" required
                       class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
            </div>

            {{-- الدولة --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
                    <i class="fas fa-flag text-purple-400"></i>
                    الدولة
                </label>
                <select name="country" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                    <option value="">اختر الدولة</option>
                    @php
                        $countries = [
                            'SA' => 'السعودية', 'EG' => 'مصر', 'AE' => 'الإمارات', 'KW' => 'الكويت',
                            'QA' => 'قطر', 'BH' => 'البحرين', 'OM' => 'عمان', 'JO' => 'الأردن',
                            'LB' => 'لبنان', 'SY' => 'سوريا', 'IQ' => 'العراق', 'DZ' => 'الجزائر',
                            'MA' => 'المغرب', 'TN' => 'تونس', 'SD' => 'السودان', 'YE' => 'اليمن',
                            'LY' => 'ليبيا', 'PS' => 'فلسطين'
                        ];
                    @endphp
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" {{ $user->country == $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- تغيير كلمة المرور --}}
            <div class="space-y-4 pt-4 border-t border-gray-600/30">
                <label class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
                    <i class="fas fa-lock text-purple-400"></i>
                    كلمة مرور جديدة (اختياري)
                </label>
                <input type="password" name="password" autocomplete="new-password" placeholder="اتركها فارغة إن لم ترغب بالتغيير"
                       class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                
                <input type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور"
                       class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
            </div>

            {{-- أزرار الحفظ والإلغاء --}}
            <div class="flex items-center justify-end gap-4 pt-6">
                <button type="button" onclick="closeEditModal()"
                        class="px-6 py-3 text-sm rounded-xl border border-gray-600 text-gray-300 hover:bg-gray-700/50 transition-all duration-300 transform hover:scale-105">
                    إلغاء
                </button>
                <button type="submit" 
                        class="px-6 py-3 text-sm rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 text-white hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('weeklyChart');
        if (!canvas) return;

        const labels = @json($weeklyStats['labels'] ?? []);
        const points = @json($weeklyStats['points'] ?? []);

        // التأكد من أن البيانات صالحة
        const validPoints = points.map(point => {
            const num = parseFloat(point);
            return isNaN(num) ? 0 : num;
        });

        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'النقاط',
                    data: validPoints,
                    borderColor: '#a855f7',
                    backgroundColor: 'rgba(168, 85, 247, 0.15)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#a855f7',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: false 
                    } 
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    }
                }
            }
        });
    });
</script>

<script>
    /* فتح/إغلاق المودال */
    function openEditModal() { 
        const modal = document.getElementById('editModal');
        const content = document.getElementById('modalContent');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.opacity = '1';
            content.style.transform = 'scale(1)';
        }, 10);
    }

    function closeEditModal() { 
        const modal = document.getElementById('editModal');
        const content = document.getElementById('modalContent');
        modal.style.opacity = '0';
        content.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    /* إرسال التعديلات */
    document.getElementById('editForm').addEventListener('submit', async (e)=>{
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
        submitBtn.disabled = true;

        const data = Object.fromEntries(new FormData(e.target));
        try {
            const res = await fetch('{{ route("profile.update", $user) }}', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(data)
            });
            if (!res.ok) throw new Error('فشل في تحديث البيانات');
            window.location.reload();
        } catch (err) {
            alert(err.message || 'حدث خطأ أثناء الحفظ');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    /* رفع الصورة */
    async function uploadAvatar(input) {
        if (!input.files.length) return;
        
        const formData = new FormData();
        formData.append('avatar', input.files[0]);
        try {
            const res = await fetch('{{ route("profile.avatar", $user) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            if (!res.ok) throw new Error('فشل في رفع الصورة');
            const { avatar } = await res.json();
            document.getElementById('avatarPreview').src = avatar;
        } catch (err) {
            alert(err.message || 'فشل رفع الصورة');
        }
    }

    /* إنشاء بطاقة المشاركة */
    function generateShareCard() {
        window.open('{{ route("profile.share", $user) }}', '_blank', 'width=800,height=600');
    }
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
</style>
@endsection