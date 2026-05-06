<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <title>@yield('title', 'لوحة التحكم - XO')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap');
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }
        body { 
            font-family: 'Tajawal', sans-serif;
            padding-bottom: 80px; /* مساحة للتنقل السفلي على الموبايل */
        }
        @media (min-width: 1024px) {
            body {
                padding-bottom: 0;
            }
        }
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,.15), 0 10px 10px -5px rgba(0,0,0,.08);
        }
        .mobile-menu {
            transition: all 0.3s ease-in-out;
        }
        /* تحسينات للهواتف */
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            /* أزرار أكبر للهواتف */
            button, a.button, .btn {
                min-height: 44px;
                min-width: 44px;
                touch-action: manipulation;
            }
            /* نص قابل للقراءة */
            body {
                font-size: 16px;
                line-height: 1.6;
            }
            /* تحسين المسافات */
            .space-y-6 > * + * {
                margin-top: 1.5rem;
            }
        }
        /* منع التكبير عند النقر المزدوج */
        * {
            touch-action: manipulation;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100">
<!-- Mobile Menu Button -->
<div class="lg:hidden fixed bottom-24 left-4 z-50">
    <button id="mobileMenuButton" class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full shadow-lg flex items-center justify-center text-white touch-manipulation active:scale-95 transition-transform">
        <i class="fas fa-bars text-xl"></i>
    </button>
</div>

<!-- Mobile Navigation Menu -->
<div id="mobileMenu" class="mobile-menu fixed inset-0 z-40 bg-gray-900/95 backdrop-blur-sm lg:hidden hidden">
    <div class="flex flex-col items-center justify-center h-full space-y-8">
        <button id="closeMobileMenu" class="absolute top-6 left-6 text-2xl text-white">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="text-center mb-8">
            <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" 
                 class="w-20 h-20 rounded-full border-4 border-purple-400 mx-auto mb-4">
            <h2 class="text-xl font-bold text-white">{{ Auth::user()->name }}</h2>
            <p class="text-purple-300">{{ Auth::user()->player->points ?? 0 }} نقطة</p>
        </div>

        <nav class="space-y-6 text-center">
            <a href="{{ route('dashboard') }}" class="block text-2xl font-semibold text-white hover:text-purple-300 transition">
                <i class="fas fa-home ml-3"></i>الرئيسية
            </a>
            <a href="{{ route('games') }}" class="block text-2xl font-semibold text-white hover:text-purple-300 transition">
                <i class="fas fa-chess-board ml-3"></i>ألعابي
            </a>
            <a href="{{ route('tournaments') }}" class="block text-2xl font-semibold text-white hover:text-purple-300 transition">
                <i class="fas fa-trophy ml-3"></i>البطولات
            </a>
            <a href="{{ route('leaderboard') }}" class="block text-2xl font-semibold text-white hover:text-purple-300 transition">
                <i class="fas fa-chart-line ml-3"></i>التصنيف
            </a>
            @auth
                @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="block text-2xl font-semibold text-white hover:text-purple-300 transition">
                    <i class="fas fa-cog ml-3"></i>لوحة التحكم
                </a>
                @endif
            @endauth
        </nav>

        <div class="absolute bottom-8 left-0 right-0 text-center">
            <a href="{{ route('profile.show', Auth::user()) }}" class="block text-lg text-gray-300 hover:text-white transition mb-4">
                <i class="fas fa-user ml-2"></i>الملف الشخصي
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline-block">
                @csrf
                <button type="submit" class="text-lg text-gray-300 hover:text-white transition">
                    <i class="fas fa-sign-out-alt ml-2"></i>تسجيل الخروج
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Top Navigation - Desktop -->
<nav class="glass sticky top-0 z-30 px-4 lg:px-6 py-3 lg:py-4 flex justify-between items-center">
    <!-- Logo and Main Nav -->
    <div class="flex items-center space-x-4 space-x-reverse">
        <div class="text-xl lg:text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
            XO Pro
        </div>
        
        <!-- Desktop Navigation -->
        <div class="hidden lg:flex space-x-4 space-x-reverse text-sm">
            <a href="{{ route('dashboard') }}" class="hover:text-purple-300 transition flex items-center">
                <i class="fas fa-home ml-1"></i>الرئيسية
            </a>
            <a href="{{ route('games') }}" class="hover:text-purple-300 transition flex items-center">
                <i class="fas fa-chess-board ml-1"></i>ألعابي
            </a>
            <a href="{{ route('tournaments') }}" class="hover:text-purple-300 transition flex items-center">
                <i class="fas fa-trophy ml-1"></i>البطولات
            </a>
            <a href="{{ route('leaderboard') }}" class="hover:text-purple-300 transition flex items-center">
                <i class="fas fa-chart-line ml-1"></i>التصنيف
            </a>
            @auth
                @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="hover:text-purple-300 transition flex items-center">
                    <i class="fas fa-cog ml-1"></i>الإدارة
                </a>
                @endif
            @endauth
        </div>
    </div>

    <!-- User Profile - Desktop -->
    <div class="hidden lg:flex items-center space-x-3 space-x-reverse">
        <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" 
             class="w-10 h-10 rounded-full border-2 border-purple-400">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="text-sm hover:text-purple-300 transition flex items-center">
                {{ Auth::user()->name }}
                <i class="fas fa-chevron-down mr-1 text-xs"></i>
            </button>
            <div x-show="open" @click.away="open = false" 
                 class="absolute left-0 mt-2 w-48 glass rounded-lg shadow-lg py-2 text-sm z-40">
                <a href="{{ route('profile.show', Auth::user()) }}" class="block px-4 py-2 hover:bg-white/10 transition flex items-center">
                    <i class="fas fa-user ml-2"></i>الملف الشخصي
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-right px-4 py-2 hover:bg-white/10 transition flex items-center justify-end">
                        <i class="fas fa-sign-out-alt ml-2"></i>تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <div class="lg:hidden flex items-center space-x-3 space-x-reverse">
        <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" 
             class="w-8 h-8 rounded-full border-2 border-purple-400">
        <span class="text-sm font-semibold">{{ Str::limit(Auth::user()->name, 12) }}</span>
    </div>
</nav>

<!-- Bottom Navigation - Mobile -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 glass border-t border-white/20 z-30 safe-area-inset-bottom">
    <div class="flex justify-around items-center py-2 px-2">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-xs text-gray-300 active:text-purple-300 transition touch-manipulation py-2 px-3 rounded-lg active:bg-white/10 min-w-[60px]">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-[10px]">الرئيسية</span>
        </a>
        <a href="{{ route('games') }}" class="flex flex-col items-center text-xs text-gray-300 active:text-purple-300 transition touch-manipulation py-2 px-3 rounded-lg active:bg-white/10 min-w-[60px]">
            <i class="fas fa-chess-board text-xl mb-1"></i>
            <span class="text-[10px]">ألعابي</span>
        </a>
        <a href="{{ route('tournaments') }}" class="flex flex-col items-center text-xs text-gray-300 active:text-purple-300 transition touch-manipulation py-2 px-3 rounded-lg active:bg-white/10 min-w-[60px]">
            <i class="fas fa-trophy text-xl mb-1"></i>
            <span class="text-[10px]">البطولات</span>
        </a>
        <a href="{{ route('leaderboard') }}" class="flex flex-col items-center text-xs text-gray-300 active:text-purple-300 transition touch-manipulation py-2 px-3 rounded-lg active:bg-white/10 min-w-[60px]">
            <i class="fas fa-chart-line text-xl mb-1"></i>
            <span class="text-[10px]">التصنيف</span>
        </a>
    </div>
</nav>

<!-- Main Content -->
<main class="pb-20 lg:pb-0">
    @yield('content')
</main>

<script>
    // Mobile Menu Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const closeMobileMenu = document.getElementById('closeMobileMenu');

        if (mobileMenuButton && mobileMenu && closeMobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.remove('hidden');
                setTimeout(() => {
                    mobileMenu.classList.add('opacity-100');
                }, 10);
            });

            closeMobileMenu.addEventListener('click', function() {
                mobileMenu.classList.remove('opacity-100');
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 300);
            });

            // Close menu when clicking on links
            mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.remove('opacity-100');
                    setTimeout(() => {
                        mobileMenu.classList.add('hidden');
                    }, 300);
                });
            });
        }
    });

    // Chart.js default config
    Chart.defaults.color = '#fff';
    Chart.defaults.borderColor = 'rgba(255,255,255,.2)';
</script>

@stack('scripts')
</body>
</html>