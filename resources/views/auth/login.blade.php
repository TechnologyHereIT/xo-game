<!-- login.blade.php -->
<x-guest-layout>
    <x-slot name="header">تسجيل الدخول</x-slot>

    <section class="min-h-screen flex items-center justify-center px-4 relative overflow-hidden"  style="padding:20px">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMSkiLz48L3N2Zz4=')]"></div>
            <!-- Floating XO Elements -->
            <div class="absolute top-1/4 left-1/4 animate-float">
                <div class="text-4xl text-purple-400/20">❌</div>
            </div>
            <div class="absolute top-1/3 right-1/4 animate-float-delayed">
                <div class="text-4xl text-blue-400/20">⭕</div>
            </div>
            <div class="absolute bottom-1/4 left-1/3 animate-float-slow">
                <div class="text-4xl text-indigo-400/20">❌</div>
            </div>
        </div>

        <div class="w-full max-w-md relative z-10">
            <!-- Header Card -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/30 animate-pulse">
                    <span class="text-2xl font-bold text-white">XO</span>
                </div>
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-300 to-indigo-300 mb-2">
                    XO Pro
                </h1>
                <p class="text-gray-300 text-lg">استعد للمعركة! 🎮</p>
            </div>

            <!-- Login Card -->
            <div class="glass-card rounded-3xl shadow-2xl p-8 border border-white/10 backdrop-blur-sm">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">مرحبًا بعودتك! 👋</h2>
                    <p class="text-gray-300">أدخل بياناتك للانضمام إلى المعركة</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="flex items-center gap-2 text-sm font-medium text-gray-300">
                            <i class="fas fa-envelope text-purple-400"></i>
                            البريد الإلكتروني
                        </label>
                        <div class="relative">
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus
                                   class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="flex items-center gap-2 text-sm font-medium text-gray-300">
                            <i class="fas fa-lock text-purple-400"></i>
                            كلمة المرور
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                   class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-2 space-x-reverse cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="remember" class="sr-only peer">
                                <div class="w-4 h-4 bg-gray-700 border border-gray-600 rounded peer-checked:bg-purple-500 peer-checked:border-purple-500 transition-all duration-300"></div>
                                <svg class="absolute top-0.5 left-0.5 w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-300">تذكرني</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-purple-400 hover:text-purple-300 transition-colors duration-300">
                                نسيت كلمة المرور؟
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 rounded-xl text-white font-bold text-lg shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all duration-300 transform hover:scale-105 active:scale-95 group">
                        <span class="flex items-center justify-center gap-3">
                            <i class="fas fa-play group-hover:animate-pulse"></i>
                            بدء المعركة
                            <i class="fas fa-chess-knight animate-bounce"></i>
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

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-gray-400">
                        جديد في XO Pro؟
                        <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors duration-300 group">
                            انضم إلى المعركة
                            <i class="fas fa-arrow-left group-hover:animate-bounce ml-1"></i>
                        </a>
                    </p>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                <div class="bg-white/5 rounded-xl p-3 backdrop-blur-sm">
                    <div class="text-purple-400 font-bold text-lg">1K+</div>
                    <div class="text-gray-400 text-xs">لاعب نشط</div>
                </div>
                <div class="bg-white/5 rounded-xl p-3 backdrop-blur-sm">
                    <div class="text-blue-400 font-bold text-lg">5K+</div>
                    <div class="text-gray-400 text-xs">معركة يومية</div>
                </div>
                <div class="bg-white/5 rounded-xl p-3 backdrop-blur-sm">
                    <div class="text-green-400 font-bold text-lg">99%</div>
                    <div class="text-gray-400 text-xs">معدل رضا</div>
                </div>
            </div>
        </div>
    </section>

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