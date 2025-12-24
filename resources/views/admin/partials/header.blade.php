<header class="bg-white dark:bg-[#1a2632] border-b border-[#e5e7eb] dark:border-[#2a3b4d] sticky top-0 z-50">
    <div class="max-w-[1200px] mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <!-- Logo -->
            @include('admin.partials.logo')

            <!-- Desktop Navigation -->
            @include('admin.partials.navbar')
        </div>

        <div class="flex items-center gap-4">
            <!-- Date & Time -->
            <div x-data="{ 
                date: new Date(),
                init() {
                    setInterval(() => this.date = new Date(), 1000);
                },
                get formattedDate() {
                    return this.date.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                },
                get formattedTime() {
                    return this.date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
                }
            }" class="flex flex-col items-end text-right mr-2 hidden md:flex">
                <span class="text-xs font-medium text-gray-500 dark:text-slate-400" x-text="formattedDate"></span>
                <span class="text-sm font-bold text-[#111418] dark:text-white font-mono" x-text="formattedTime"></span>
            </div>

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
