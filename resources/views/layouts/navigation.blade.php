<header class="bg-white dark:bg-[#1a2632] border-b border-[#e5e7eb] dark:border-[#2a3b4d] sticky top-0 z-50">
    <div class="max-w-[1200px] mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <!-- Logo -->
            <div class="flex items-center gap-2 text-[#111418] dark:text-white mr-6">
                <span class="material-symbols-outlined text-primary text-3xl">admin_panel_settings</span>
                <h1 class="text-lg font-bold leading-tight tracking-[-0.015em] hidden sm:block">KTM Admin</h1>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-1">
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 dark:hover:bg-[#2a3b4d] text-[#617589] dark:text-slate-400 hover:text-[#111418] dark:hover:text-white' }} transition-colors" href="{{ route('dashboard') }}">
                    <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'icon-filled' : '' }} text-xl">dashboard</span>
                    <span class="text-sm font-{{ request()->routeIs('dashboard') ? 'semibold' : 'medium' }} leading-normal">Dashboard</span>
                </a>
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg {{ request()->routeIs('templates.*') ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 dark:hover:bg-[#2a3b4d] text-[#617589] dark:text-slate-400 hover:text-[#111418] dark:hover:text-white' }} transition-colors" href="{{ route('templates.index') }}">
                    <span class="material-symbols-outlined text-xl">description</span>
                    <span class="text-sm font-medium leading-normal">Template KTM</span>
                </a>
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg {{ request()->routeIs('ktm-generator.*') ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 dark:hover:bg-[#2a3b4d] text-[#617589] dark:text-slate-400 hover:text-[#111418] dark:hover:text-white' }} transition-colors" href="{{ route('ktm-generator.index') }}">
                    <span class="material-symbols-outlined text-xl">manufacturing</span>
                    <span class="text-sm font-medium leading-normal">Generate KTM</span>
                </a>
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg {{ request()->routeIs('download-jobs.*') ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 dark:hover:bg-[#2a3b4d] text-[#617589] dark:text-slate-400 hover:text-[#111418] dark:hover:text-white' }} transition-colors" href="{{ route('download-jobs.index') }}">
                    <span class="material-symbols-outlined text-xl">history</span>
                    <span class="text-sm font-medium leading-normal">Download History</span>
                </a>
            </nav>
        </div>

        <div class="flex items-center gap-4">
            <!-- Search -->
            <label class="hidden lg:flex flex-col min-w-40 w-full max-w-[240px] h-9">
                <div class="flex w-full flex-1 items-stretch rounded-lg h-full bg-[#f0f2f4] dark:bg-[#2a3b4d] focus-within:ring-2 focus-within:ring-primary/50 transition-shadow">
                    <div class="text-[#617589] dark:text-slate-400 flex items-center justify-center pl-3 pr-1">
                        <span class="material-symbols-outlined text-lg">search</span>
                    </div>
                    <input class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] dark:text-white focus:outline-0 bg-transparent h-full placeholder:text-[#617589] dark:placeholder:text-slate-500 px-2 text-sm font-normal leading-normal" placeholder="Search..." />
                </div>
            </label>

            <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 hidden lg:block"></div>

            <!-- Notifications -->
            <button class="relative text-[#111418] dark:text-white hover:bg-gray-100 dark:hover:bg-[#2a3b4d] p-2 rounded-full transition-colors">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-[#1a2632]"></span>
            </button>

            <!-- User Avatar -->
            <div class="relative group">
                <div class="flex items-center gap-3 cursor-pointer pl-2">
                    <div class="relative bg-center bg-no-repeat aspect-square bg-cover rounded-full size-9 shadow-sm border border-gray-100 dark:border-gray-700 bg-primary flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                </div>

                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#1a2632] rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-[#111418] dark:text-white">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="text-xs text-[#617589] dark:text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[#617589] dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#2a3b4d] hover:text-[#111418] dark:hover:text-white">
                            <span class="material-symbols-outlined text-lg">person</span>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-[#617589] dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#2a3b4d] hover:text-red-500">
                                <span class="material-symbols-outlined text-lg">logout</span>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button class="md:hidden p-2 text-gray-600 dark:text-gray-300" onclick="toggleMobileMenu()">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div id="mobile-menu" class="md:hidden border-t border-[#e5e7eb] dark:border-[#2a3b4d] hidden">
        <nav class="flex flex-col p-2 gap-1">
            <a class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-[#617589] dark:text-slate-400' }}" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'icon-filled' : '' }}">dashboard</span>
                <span class="text-sm font-semibold">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('templates.*') ? 'bg-primary/10 text-primary' : 'text-[#617589] dark:text-slate-400' }}" href="{{ route('templates.index') }}">
                <span class="material-symbols-outlined">description</span>
                <span class="text-sm font-medium">Template KTM</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('ktm-generator.*') ? 'bg-primary/10 text-primary' : 'text-[#617589] dark:text-slate-400' }}" href="{{ route('ktm-generator.index') }}">
                <span class="material-symbols-outlined">manufacturing</span>
                <span class="text-sm font-medium">Generate KTM</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('download-jobs.*') ? 'bg-primary/10 text-primary' : 'text-[#617589] dark:text-slate-400' }}" href="{{ route('download-jobs.index') }}">
                <span class="material-symbols-outlined">history</span>
                <span class="text-sm font-medium">Download History</span>
            </a>
        </nav>
    </div>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }

</script>
