<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KTM Admin') }} - Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Theme Configuration -->
    <script>
        tailwind.config = {
            darkMode: "class"
            , theme: {
                extend: {
                    colors: {
                        "primary": "#137fec"
                        , "primary-dark": "#0b63be"
                        , "background-light": "#f6f7f8"
                        , "background-dark": "#101922"
                    , }
                    , fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    }
                    , borderRadius: {
                        "DEFAULT": "0.25rem"
                        , "lg": "0.5rem"
                        , "xl": "0.75rem"
                        , "2xl": "1rem"
                        , "full": "9999px"
                    }
                , }
            , }
        , }

    </script>

    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL'0, 'wght'400, 'GRAD'0, 'opsz'24;
            font-size: 24px;
        }

        .icon-filled {
            font-variation-settings: 'FILL'1;
        }

    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-[#111418] dark:text-white min-h-screen flex flex-col">
    <!-- Header -->
    @include('layouts.navigation')

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-6 lg:p-10">
        <div class="max-w-[1200px] mx-auto flex flex-col gap-8">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
