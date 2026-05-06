<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - {{ $header ?? '' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap');
        
        body {
            font-family: 'Tajawal', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #7e22ce 100%);
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans">


    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8 lg:py-0" >

        <div class="w-full max-w-m1d">
            <div class="gla1ss rounded-3xl p-6 lg:p-1 card-hover">
                {{ $slot }}
            </div>
            
            <!-- Mobile Footer Links -->
            <div class="lg:hidden mt-6 text-center">
                <div class="flex justify-center space-x-6 space-x-reverse text-sm">
                    <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition">
                        إنشاء حساب
                    </a>
                    <a href="/" class="text-gray-400 hover:text-white transition">
                        الرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Space -->
    <div class="lg:hidden h-20"></div>

    <script>
        // Chart.js default config for guest pages
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = '#fff';
            Chart.defaults.borderColor = 'rgba(255,255,255,.2)';
        }
    </script>
</body>
</html>