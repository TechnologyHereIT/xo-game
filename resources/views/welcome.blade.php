<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>XO Pro - لعبة إكس أو المتقدمة | تحدَّى الكل وكن الأسطورة</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        /* تأثيرات بسيطة */
        .float{animation:float 5s ease-in-out infinite}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-15px)}}
        .gradient-text{background:linear-gradient(90deg,#c084fc 0%,#818cf8 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans">

<!-- ========== HERO ========== -->
<section class="min-h-screen flex items-center justify-center relative overflow-hidden px-6">
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-gray-900 to-indigo-900 opacity-70"></div>
    <!-- خلفية حبوب نجمية بسيطة -->
    <div class="absolute inset-0 stars opacity-20"></div>
    <div class="relative z-10 text-center">
        <h1 class="gradient-text float" style="font-size:60px; height:90px">XO Pro</h1>
        <p class="text-xl md:text-2xl text-gray-300 mb-8">
            تحدّى الكمبيوتر، أصدقاءك، أو انضمّ لدوري الإقصائيّ وكن البطل!
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            @auth
                <a href="{{ route('lobby') }}" class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 transition shadow-lg text-lg">ابدأ اللعب</a>
            @else
                <a href="{{ route('login') }}" class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 transition shadow-lg text-lg">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="px-8 py-3 rounded-xl bg-gray-700 hover:bg-gray-600 transition shadow-lg text-lg">إنشاء حساب</a>
            @endauth
        </div>
    </div>
</section>

<!-- ========== لماذا XO Pro؟ ========== -->
<section class="py-16 px-6">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-10 gradient-text">لماذا XO Pro؟</h2>
        <div class="grid md:grid-cols-3 gap-8 text-center">
            <div class="bg-gray-800/50 rounded-2xl p-6 shadow-lg border border-white/10">
                <div class="text-5xl mb-4">🤖</div>
                <h3 class="text-xl font-bold mb-2">ذكاء اصطناعي متكيّف</h3>
                <p class="text-gray-400">ثلاثة مستويات: سهل / طبيعي / مستحيل. تعلّم من خطئك ويتطوّر مع كل جولة.</p>
            </div>
            <div class="bg-gray-800/50 rounded-2xl p-6 shadow-lg border border-white/10">
                <div class="text-5xl mb-4">⚡</div>
                <h3 class="text-xl font-bold mb-2">مباريات زمنية سريعة</h3>
                <p class="text-gray-400">جولة واحدة = 30 ثانية. سرعة رد فعلك تُحدِث فارق النقاط.</p>
            </div>
            <div class="bg-gray-800/50 rounded-2xl p-6 shadow-lg border border-white/10">
                <div class="text-5xl mb-4">🏆</div>
                <h3 class="text-xl font-bold mb-2">دوري إقصائي 30 لاعبًا</h3>
                <p class="text-gray-400">يتأهّل الأربع الأوائل لنصف النهائي. الجوائز يومية وأسبوعية.</p>
            </div>
        </div>
    </div>
</section>

<!-- ========== آلية اللعب (التفاصيل المملة) ========== -->
<section class="py-16 px-6 bg-black/20">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-10 gradient-text">كيف ألعب؟</h2>

        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <ol class="space-y-4 text-lg">
                    <li class="flex items-start gap-3">
                        <span class="bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center shrink-0">1</span>
                        <span>سجِّل دخولك أو أنشئ حسابًا في ثوانٍ (البريد الإلكتروني فقط).</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center shrink-0">2</span>
                        <span>اختر وضع اللعب: ضد الكمبيوتر، صديق، أو انضمّ لدوري إقصائي.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center shrink-0">3</span>
                        <span>كل فوز يمنحك نقاطًا تُحدِّد مكانك في التصنيف العالمي.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center shrink-0">4</span>
                        <span> اجمع إنجازاتٍ واربح مكافآت يومية عند تسجيلك.</span>
                    </li>
                </ol>
            </div>
            <div class="flex justify-center">
                <img src="https://cdn-icons-png.flaticon.com/512/984/984114.png" alt="board" class="w-64 drop-shadow-xl float">
            </div>
        </div>

        <!-- تفاصيل النقاط -->
        <div class="mt-12 grid md:grid-cols-3 gap-6 text-center">
            <div class="bg-gray-800/60 rounded-xl p-4 border border-white/10">
                <div class="text-2xl font-bold text-yellow-400">+10</div>
                <div class="text-sm text-gray-400">فوز ضد الكمبيوتر (مستوى طبيعي)</div>
            </div>
            <div class="bg-gray-800/60 rounded-xl p-4 border border-white/10">
                <div class="text-2xl font-bold text-yellow-400">+25</div>
                <div class="text-sm text-gray-400">فوز في الدوري الإقصائي</div>
            </div>
            <div class="bg-gray-800/60 rounded-xl p-4 border border-white/10">
                <div class="text-2xl font-bold text-yellow-400">+3</div>
                <div class="text-sm text-gray-400">الإجابة الصحيحة على سؤال سرعة</div>
            </div>
        </div>
    </div>
</section>

<!-- ========== عبارات تحفيزية ========== -->
<section class="py-16 px-6">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6 gradient-text">نصائح ذهبية</h2>
        <div class="space-y-4 text-lg italic text-gray-300">
            <p>« الخطأ الأول لا يُعفيك من الخطأ الثاني، لكنه يعلمك أين تضع علامة O. »</p>
            <p>« لا تُكمل اللعلة سريعًا، خطط لثلاث حركات مُقبلة. »</p>
            <p>« التركيز ليس فقط في اللعبة، بل في اختيار الوقت المناسب لبدء الدوري. »</p>
        </div>
    </div>
</section>

<!-- ========== أسئلة شائعة ========== -->
<section class="py-16 px-6 bg-black/20">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-10 gradient-text">الأسئلة الأكثر تكرارًا</h2>
        <div class="space-y-4">
            <div class="bg-gray-800/60 rounded-xl p-4 border border-white/10">
                <button class="w-full text-left font-semibold" onclick="this.nextElementSibling.classList.toggle('hidden')">
                    هل أحتاج إلى بطاقة ائتمان للعب؟
                </button>
                <div class="hidden mt-2 text-gray-400">لا، اللعبة مجانية 100%.</div>
            </div>
            <div class="bg-gray-800/60 rounded-xl p-4 border border-white/10">
                <button class="w-full text-left font-semibold" onclick="this.nextElementSibling.classList.toggle('hidden')">
                    كيف أرفع صورتي الشخصية؟
                </button>
                <div class="hidden mt-2 text-gray-400">من صفحتك الشخصية اضغط على أيقونة الكاميرا أسفل الصورة.</div>
            </div>
            <div class="bg-gray-800/60 rounded-xl p-4 border border-white/10">
                <button class="w-full text-left font-semibold" onclick="this.nextElementSibling.classList.toggle('hidden')">
                    متى تُقام البطولات؟
                </button>
                <div class="hidden mt-2 text-gray-400">كل يوم جمعة الساعة 8 م بتوقيت مكة، يفتح التسجيل قبلها بـ24 ساعة.</div>
            </div>
        </div>
    </div>
</section>

<!-- ========== Footer ========== -->
<footer class="py-8 text-center text-gray-500 text-sm border-t border-white/10">
    &copy; 2025 XO Pro. كل الحقوق محفوظة. صُنعت بـ ❤️ في الوطن العربي.
</footer>

</body>
</html>