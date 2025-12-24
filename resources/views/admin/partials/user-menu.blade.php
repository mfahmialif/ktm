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
