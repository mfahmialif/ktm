<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'KTM Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 6px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background-color: #fff;
            font-family: 'Lexend', sans-serif;
            font-size: 0.875rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #111418;
            line-height: 28px;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #617589 transparent transparent transparent;
        }

        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #617589 transparent;
        }

        .select2-dropdown {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            font-family: 'Lexend', sans-serif;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #137fec;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 8px 12px;
            font-family: 'Lexend', sans-serif;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #137fec;
            outline: none;
            box-shadow: 0 0 0 2px rgba(19, 127, 236, 0.2);
        }

        .select2-results__option {
            padding: 10px 12px;
        }

        /* Dark mode support */
        .dark .select2-container--default .select2-selection--single {
            background-color: #23303d;
            border-color: #2a3b4d;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff;
        }

        .dark .select2-dropdown {
            background-color: #1a2632;
            border-color: #2a3b4d;
        }

        .dark .select2-container--default .select2-results__option {
            color: #94a3b8;
        }

        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #137fec;
            color: #fff;
        }

        .dark .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #23303d;
            border-color: #2a3b4d;
            color: #fff;
        }

    </style>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Additional CSS Stack -->
    {{ $styles ?? '' }}
</head>
<body class="bg-background-light dark:bg-background-dark text-[#111418] dark:text-white min-h-screen flex flex-col">
    <!-- Header -->
    @include('admin.partials.header')

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-6 lg:p-10">
        <div class="max-w-[1200px] mx-auto flex flex-col gap-8">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    @include('admin.partials.footer')

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Alpine.js (loaded after jQuery/Select2 for proper init order) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Initialize Select2 -->
    <script>
        function initSelect2() {
            $('.select2').select2({
                width: '100%'
                , dropdownAutoWidth: true
                , minimumResultsForSearch: 5
            });
        }

        // Initialize on page load
        $(document).ready(function() {
            initSelect2();
        });

        // Reinitialize after Livewire updates
        document.addEventListener('livewire:navigated', () => {
            initSelect2();
        });

        // For Livewire 3
        Livewire.hook('morph.updated', ({
            el
        }) => {
            initSelect2();
        });

    </script>

    <!-- Scripts Stack -->
    {{ $scripts ?? '' }}
</body>
</html>
