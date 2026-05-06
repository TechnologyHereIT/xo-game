<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقة {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap');
        * { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen flex items-center justify-center p-4">
    <div id="shareCard" class="w-full max-w-2xl bg-gradient-to-br from-blue-900 to-blue-800 rounded-3xl border border-gray-700 shadow-2xl overflow-hidden">
        
        <div class="p-8">
            {{-- الهيدر --}}
            <div class="text-center mb-8">
                <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-2xl font-bold text-white">XO</span>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">بطاقة اللاعب</h1>
            </div>

            {{-- المحتوى الرئيسي --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- العمود الأول: الصورة والمعلومات الأساسية --}}
                <div class="lg:col-span-1 flex flex-col items-center">
                    {{-- الصورة --}}
                    <div class="relative mb-6">
                        <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF' }}" 
                             class="w-32 h-32 rounded-2xl object-cover border-4 border-purple-400 shadow-lg">
                    </div>

                    {{-- المعلومات الأساسية --}}
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-white mb-2">{{ $user->name }}</h2>
                        <p class="text-gray-300 text-lg mb-1">{{ $user->email }}</p>
                        
                        @if($user->country)
                        @php
                            $countries = [
                                'SA' => 'السعودية', 'EG' => 'مصر', 'AE' => 'الإمارات', 'KW' => 'الكويت',
                                'QA' => 'قطر', 'BH' => 'البحرين', 'OM' => 'عمان', 'JO' => 'الأردن',
                                'LB' => 'لبنان', 'SY' => 'سوريا', 'IQ' => 'العراق', 'DZ' => 'الجزائر',
                                'MA' => 'المغرب', 'TN' => 'تونس', 'SD' => 'السودان', 'YE' => 'اليمن',
                                'LY' => 'ليبيا', 'PS' => 'فلسطين'
                            ];
                        @endphp
                        <div class="flex items-center justify-center gap-2 text-purple-300 mb-4">
                            <i class="fas fa-flag"></i>
                            <span>{{ $countries[$user->country] ?? $user->country }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- العمود الثاني: الإحصائيات --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- الترتيب --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-800 rounded-2xl p-4 text-center">
                            <div class="text-white font-bold text-2xl mb-1">#{{ $globalRank }}</div>
                            <div class="text-blue-300 text-sm">الترتيب العام</div>
                        </div>
                        @if($user->country)
                        <div class="bg-gray-800 rounded-2xl p-4 text-center">
                            <div class="text-white font-bold text-2xl mb-1">#{{ $countryRank }}</div>
                            <div class="text-yellow-300 text-sm">الترتيب في الدولة</div>
                        </div>
                        @endif
                    </div>

                    {{-- الإحصائيات --}}
                    @if($achievements)
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-800 rounded-2xl p-4 text-center">
                            <div class="text-2xl font-bold text-purple-300 mb-1">{{ $achievements['points'] }}</div>
                            <div class="text-gray-300 text-sm">النقاط</div>
                        </div>
                        <div class="bg-gray-800 rounded-2xl p-4 text-center">
                            <div class="text-2xl font-bold text-green-300 mb-1">{{ $achievements['games_won'] }}</div>
                            <div class="text-gray-300 text-sm">مرات الفوز</div>
                        </div>
                        <div class="bg-gray-800 rounded-2xl p-4 text-center">
                            <div class="text-2xl font-bold text-blue-300 mb-1">{{ $achievements['games_played'] }}</div>
                            <div class="text-gray-300 text-sm">الألعاب</div>
                        </div>
                        <div class="bg-gray-800 rounded-2xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-300 mb-1">{{ $achievements['win_rate'] }}%</div>
                            <div class="text-gray-300 text-sm">معدل الفوز</div>
                        </div>
                    </div>
                    @endif

                    {{-- الإنجازات --}}
                    <div class="bg-gray-800 rounded-2xl p-4">
                        <h3 class="text-lg font-bold text-white mb-3 text-center">أبرز الإنجازات</h3>
                        <div class="flex justify-center gap-2 flex-wrap">
                            @if($achievements && $achievements['points'] >= 100)
                            <div class="bg-purple-700 px-3 py-1 rounded-full text-white text-sm">
                                <i class="fas fa-star ml-1"></i> 100+ نقطة
                            </div>
                            @endif
                            @if($achievements && $achievements['games_won'] >= 10)
                            <div class="bg-green-700 px-3 py-1 rounded-full text-white text-sm">
                                <i class="fas fa-trophy ml-1"></i> 10+ فوز
                            </div>
                            @endif
                            @if($achievements && $achievements['games_played'] >= 50)
                            <div class="bg-yellow-700 px-3 py-1 rounded-full text-white text-sm">
                                <i class="fas fa-users ml-1"></i> 50+ لعبة
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- الفوتر --}}
            <div class="text-center mt-8 pt-6 border-t border-gray-700">
                <p class="text-gray-300 text-sm">XO Pro - منصة الألعاب التنافسية</p>
                <p class="text-gray-400 text-xs mt-1">{{ url('/') }}</p>
            </div>
        </div>
    </div>

    {{-- أزرار المشاركة --}}
    <div class="fixed bottom-6 left-6 flex gap-3">
        <button onclick="downloadCard()" 
                class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl shadow-lg hover:from-purple-700 hover:to-blue-700 transition-all duration-300">
            <i class="fas fa-download ml-2"></i>
            حفظ البطاقة
        </button>
        <button onclick="shareCard()" 
                class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-300">
            <i class="fas fa-share-alt ml-2"></i>
            مشاركة
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadCard() {
            html2canvas(document.getElementById('shareCard')).then(canvas => {
                const link = document.createElement('a');
                link.download = 'بطاقة-{{ $user->name }}-XO-Pro.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }

        function shareCard() {
            if (navigator.share) {
                html2canvas(document.getElementById('shareCard')).then(canvas => {
                    canvas.toBlob(blob => {
                        const file = new File([blob], 'بطاقة-{{ $user->name }}-XO-Pro.png', { type: 'image/png' });
                        navigator.share({
                            title: 'بطاقة {{ $user->name }} في XO Pro',
                            text: 'تعرف على إنجازات {{ $user->name }} في منصة XO Pro!',
                            files: [file]
                        });
                    });
                });
            } else {
                downloadCard();
            }
        }
    </script>
</body>
</html>