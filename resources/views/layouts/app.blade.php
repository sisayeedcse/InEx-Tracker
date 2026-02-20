<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InEx Tracker')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .gradient-red {
            background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
        }

        .gradient-blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-purple-50 via-blue-50 to-pink-50 min-h-screen">
    <nav class="glass-effect sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}"
                        class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent flex items-center gap-2">
                        <span class="text-3xl">💰</span>
                        <span>InEx Tracker</span>
                    </a>
                </div>
                <div class="flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-100 hover:text-purple-700 transition-all duration-200 font-medium">
                        📊 Dashboard
                    </a>
                    <a href="{{ route('transactions.index') }}"
                        class="px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-100 hover:text-purple-700 transition-all duration-200 font-medium">
                        📝 Transactions
                    </a>
                    <a href="{{ route('accounts.index') }}"
                        class="px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-100 hover:text-purple-700 transition-all duration-200 font-medium">
                        💳 Accounts
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div
                class="animate-slide-up bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-lg mb-6 shadow-md">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">✓</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div
                class="animate-slide-up bg-gradient-to-r from-red-50 to-pink-100 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg mb-6 shadow-md">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">⚠</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="glass-effect border-t border-gray-200 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
            <p class="text-gray-600 font-medium">
                InEx Tracker &copy; {{ date('Y') }}
                <span class="mx-2">·</span>
                <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Personal
                    Finance Made Simple</span>
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>