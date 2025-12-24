<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="KTM UII Dalwa">
    <meta name="author" content="TIM IT Banin & Banat">
    <meta name="keywords" content="KTM UII Dalwa, KTM System">

    <link rel="icon" href="{{ asset('img/favicon.ico') }}">
    <title>{{ config('app.name', 'KTM System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:400,500,600,700" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'media'
            , theme: {
                extend: {
                    fontFamily: {
                        sans: ['Lexend', 'sans-serif']
                    , }
                , }
            , }
        , }

    </script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }

    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white flex items-center justify-center min-h-screen relative overflow-hidden transition-colors duration-300">

    <!-- Background Gradient Blobs -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] bg-blue-500/10 dark:bg-blue-500/20 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[60%] h-[60%] bg-purple-500/10 dark:bg-purple-500/20 rounded-full blur-[120px] animate-pulse" style="animation-duration: 4s;"></div>
    </div>

    <!-- Main Content -->
    <div class="text-center px-6 max-w-3xl mx-auto z-10 w-full">
        <!-- Logo Icon -->
        <div class="inline-flex items-center justify-center w-24 h-24 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl mb-10 border border-gray-100 dark:border-gray-700 transform hover:scale-105 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
        </div>

        <!-- Title & Subtitle -->
        <h1 class="text-5xl md:text-6xl font-bold tracking-tight mb-6 text-gray-900 dark:text-white bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400">
            KTM System
        </h1>
        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 mb-12 leading-relaxed max-w-2xl mx-auto">
            Efficient and seamless student identity card generation for the modern academic era.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-5 w-full max-w-md mx-auto">
            @if (Route::has('login'))
            @auth
            <a href="{{ url('/dashboard') }}" class="group relative flex items-center justify-center w-full sm:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-lg transition-all shadow-xl shadow-blue-500/20 hover:shadow-blue-500/40 hover:-translate-y-1">
                Go to Dashboard
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="flex items-center justify-center w-full px-8 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 font-semibold rounded-2xl text-lg transition-all hover:shadow-lg hover:-translate-y-1">
                    Log Out
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="group relative flex items-center justify-center w-full sm:w-full px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-lg transition-all shadow-xl shadow-blue-500/20 hover:shadow-blue-500/40 hover:-translate-y-1">
                Log In to Continue
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
            </a>
            @endauth
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-20 text-sm font-medium text-gray-400 dark:text-gray-600">
            &copy; {{ date('Y') }} KTM UI DALWA - All rights reserved by TIM IT Banin & Banat.
        </div>
    </div>
</body>
</html>
