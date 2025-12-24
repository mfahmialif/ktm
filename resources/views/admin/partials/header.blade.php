<header class="bg-white dark:bg-[#1a2632] border-b border-[#e5e7eb] dark:border-[#2a3b4d] sticky top-0 z-50">
    <div class="max-w-[1200px] mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <!-- Logo -->
            @include('admin.partials.logo')

            <!-- Desktop Navigation -->
            @include('admin.partials.navbar')
        </div>

        <div class="flex items-center gap-4">
            <!-- Search -->
            @include('admin.partials.search')

            <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 hidden lg:block"></div>

            <!-- Notifications -->
            <button class="relative text-[#111418] dark:text-white hover:bg-gray-100 dark:hover:bg-[#2a3b4d] p-2 rounded-full transition-colors">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-[#1a2632]"></span>
            </button>

            <!-- User Menu -->
            @include('admin.partials.user-menu')

            <!-- Mobile Menu Button -->
            <button class="md:hidden p-2 text-gray-600 dark:text-gray-300" onclick="toggleMobileMenu()">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation -->
    @include('admin.partials.navbar-mobile')
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }

</script>
