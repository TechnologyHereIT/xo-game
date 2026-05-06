<x-guest-layout>
    <x-slot name="header">إنشاء حساب</x-slot>

    <section class="min-h-screen flex items-center justify-center px-4 relative overflow-hidden" style="padding:20px">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMSkiLz48L3N2Zz4=')]"></div>
            <!-- Floating XO Elements -->
            <div class="absolute top-1/4 right-1/4 animate-float">
                <div class="text-4xl text-blue-400/20">⭕</div>
            </div>
            <div class="absolute top-1/3 left-1/4 animate-float-delayed">
                <div class="text-4xl text-purple-400/20">❌</div>
            </div>
            <div class="absolute bottom-1/4 right-1/3 animate-float-slow">
                <div class="text-4xl text-indigo-400/20">⭕</div>
            </div>
        </div>

        <div class="w-full max-w-md relative z-10">
            <!-- Header Card -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 animate-pulse">
                    <span class="text-2xl font-bold text-white">XO</span>
                </div>
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-purple-300 mb-2">
                    XO Pro
                </h1>
                <p class="text-gray-300 text-lg">انضم إلى الأسطورة! 🏆</p>
            </div>

            <!-- Register Card -->
            <div class="glass-card rounded-3xl shadow-2xl p-8 border border-white/10 backdrop-blur-sm">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">انضم إلينا! 🚀</h2>
                    <p class="text-gray-300">أنشئ حسابك وابدأ رحلتك نحو البطولة</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div class="space-y-2">
                        <label for="name" class="flex items-center gap-2 text-sm font-medium text-gray-300">
                            <i class="fas fa-user text-blue-400"></i>
                            اسم المستخدم
                        </label>
                        <div class="relative">
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                   placeholder="اختر اسمًا أسطوريًا">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i class="fas fa-crown text-gray-400"></i>
                            </div>
                        </div>
                        @error('name')
                            <div class="mt-2 text-red-400 text-sm">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="flex items-center gap-2 text-sm font-medium text-gray-300">
                            <i class="fas fa-envelope text-blue-400"></i>
                            البريد الإلكتروني
                        </label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                   placeholder="بريدك السري للانضمام">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i class="fas fa-shield-alt text-gray-400"></i>
                            </div>
                        </div>
                        @error('email')
                            <div class="mt-2 text-red-400 text-sm">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="space-y-2">
                        <label for="country" class="flex items-center gap-2 text-sm font-medium text-gray-300">
                            <i class="fas fa-flag text-blue-400"></i>
                            الدولة
                        </label>
                        <select id="country" name="country" required
                                class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                            <option value="">اختر الدولة</option>
                            @foreach($countries as $code => $name)
                                <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <div class="mt-2 text-red-400 text-sm">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="flex items-center gap-2 text-sm font-medium text-gray-300">
                            <i class="fas fa-lock text-blue-400"></i>
                            كلمة المرور
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                   class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                   placeholder="كلمة المرور السرية">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                        </div>
                        @error('password')
                            <div class="mt-2 text-red-400 text-sm">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="flex items-center gap-2 text-sm font-medium text-gray-300">
                            <i class="fas fa-lock text-blue-400"></i>
                            تأكيد كلمة المرور
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                   placeholder="أعد كتابة كلمة المرور">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i class="fas fa-check-double text-gray-400"></i>
                            </div>
                        </div>
                        @error('password_confirmation')
                            <div class="mt-2 text-red-400 text-sm">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Strength Meter -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>قوة كلمة المرور</span>
                            <span id="passwordStrength">ضعيفة</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div id="passwordStrengthBar" class="h-2 rounded-full bg-red-500 transition-all duration-300" style="width: 20%"></div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-xl text-white font-bold text-lg shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 transform hover:scale-105 active:scale-95 group">
                        <span class="flex items-center justify-center gap-3">
                            <i class="fas fa-rocket group-hover:animate-bounce"></i>
                            ابدأ الرحلة
                            <i class="fas fa-medal animate-pulse"></i>
                        </span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-800 text-gray-400">أو</span>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-gray-400">
                        لديك حساب بالفعل؟
                        <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors duration-300 group">
                            ابدأ المعركة
                            <i class="fas fa-swords group-hover:animate-pulse ml-1"></i>
                        </a>
                    </p>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-6 grid grid-cols-2 gap-4 text-center">
                <div class="bg-white/5 rounded-xl p-3 backdrop-blur-sm">
                    <i class="fas fa-trophy text-yellow-400 text-lg mb-1"></i>
                    <div class="text-white font-bold text-sm">بطولات</div>
                </div>
                <div class="bg-white/5 rounded-xl p-3 backdrop-blur-sm">
                    <i class="fas fa-robot text-green-400 text-lg mb-1"></i>
                    <div class="text-white font-bold text-sm">ذكاء اصطناعي</div>
                </div>
                <div class="bg-white/5 rounded-xl p-3 backdrop-blur-sm">
                    <i class="fas fa-ranking-star text-purple-400 text-lg mb-1"></i>
                    <div class="text-white font-bold text-sm">تصنيف</div>
                </div>
                <div class="bg-white/5 rounded-xl p-3 backdrop-blur-sm">
                    <i class="fas fa-users text-blue-400 text-lg mb-1"></i>
                    <div class="text-white font-bold text-sm">منافسة</div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrength');

            if (passwordInput && strengthBar && strengthText) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length >= 8) strength += 25;
                    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
                    if (password.match(/\d/)) strength += 25;
                    if (password.match(/[^a-zA-Z\d]/)) strength += 25;

                    strengthBar.style.width = strength + '%';
                    
                    if (strength < 50) {
                        strengthBar.className = 'h-2 rounded-full bg-red-500 transition-all duration-300';
                        strengthText.textContent = 'ضعيفة';
                    } else if (strength < 75) {
                        strengthBar.className = 'h-2 rounded-full bg-yellow-500 transition-all duration-300';
                        strengthText.textContent = 'جيدة';
                    } else {
                        strengthBar.className = 'h-2 rounded-full bg-green-500 transition-all duration-300';
                        strengthText.textContent = 'قوية';
                    }
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
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes float-delayed {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-5deg); }
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float-delayed 7s ease-in-out infinite; }
        .animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
    </style>
</x-guest-layout>