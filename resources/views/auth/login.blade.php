<x-guest-layout>
    <!-- Main Card Container -->
    <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col md:flex-row min-h-[600px] border border-gray-100 dark:border-gray-800">

        <!-- Left Side: Contextual Illustration / Branding -->
        <div class="hidden md:flex md:w-1/2 relative bg-gray-100 dark:bg-gray-800 items-center justify-center overflow-hidden group">
            <!-- Background Image -->
            <div class="absolute inset-0 w-full h-full bg-cover bg-center transition-transform duration-700 hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuC-8r11DkjX3W0tU2FbnmSa_J3FIB9vx4TGuP1-YL-I_-U9Zsa1_gWV6LReeh5lPdSOJpybsMdJm9sCc28c4VFPzcPiKzSZeKbb8ensEzeK9PQMsqejkBL0TUyo4JFzftnbmamyKDjwZdEQwRPXLQf9xCsMHIA260bKQEu-OfTJn3ZqpO5da633W4WHseU728GCWmhCgC5SR3IA3yxe4Xc80MmNHwX33T7HKXg4RCo00vAY2AoNaQmIz-D9BqsXpmeyDJCJXzAkSz4M');">
            </div>
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-primary/40 mix-blend-multiply"></div>
            <!-- Overlay Text Content -->
            <div class="relative z-10 p-10 text-white flex flex-col h-full justify-between">
                <div>
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-lg flex items-center justify-center mb-6 border border-white/30">
                        <span class="material-symbols-outlined text-white text-3xl">badge</span>
                    </div>
                    <h2 class="text-3xl font-bold mb-4 tracking-tight">Student Identity Management</h2>
                    <p class="text-white/90 text-lg leading-relaxed font-light">
                        Securely generate, manage, and print student KTM cards with our centralized admin portal.
                    </p>
                </div>
                <div class="flex items-center gap-3 text-sm font-medium text-white/80">
                    <span class="material-symbols-outlined text-lg">verified_user</span>
                    <span>Official University System</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-white dark:bg-[#1a2632]">
            <div class="w-full max-w-md mx-auto">
                <!-- Mobile Logo (visible only on small screens) -->
                <div class="md:hidden mb-6 flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined text-3xl">badge</span>
                    <span class="font-bold text-xl tracking-tight">KTM Admin</span>
                </div>

                <!-- Header Section -->
                <div class="mb-8">
                    <h1 class="text-[#111418] dark:text-white tracking-tight text-[32px] font-bold leading-tight mb-2">
                        Welcome Back
                    </h1>
                    <p class="text-[#617589] dark:text-gray-400 text-base font-normal leading-normal">
                        Please sign in to your admin account to manage student IDs.
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
                    @csrf

                    <!-- Email Field -->
                    <div class="flex flex-col gap-2">
                        <label class="text-[#111418] dark:text-gray-200 text-base font-medium leading-normal" for="email">
                            Email Address
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#617589]">mail</span>
                            <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#dbe0e6] dark:border-gray-600 bg-white dark:bg-[#23303d] focus:border-primary h-14 placeholder:text-[#617589] pl-12 pr-4 text-base font-normal leading-normal transition-all" id="email" name="email" type="email" placeholder="admin@university.edu" value="{{ old('email') }}" required autofocus autocomplete="username" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password Field -->
                    <div class="flex flex-col gap-2">
                        <label class="text-[#111418] dark:text-gray-200 text-base font-medium leading-normal" for="password">
                            Password
                        </label>
                        <div class="flex w-full flex-1 items-stretch rounded-lg relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#617589] z-10">lock</span>
                            <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#dbe0e6] dark:border-gray-600 bg-white dark:bg-[#23303d] focus:border-primary h-14 placeholder:text-[#617589] pl-12 pr-12 text-base font-normal leading-normal transition-all" id="password" name="password" type="password" placeholder="Enter your password" required autocomplete="current-password" />
                            <button type="button" class="absolute right-0 top-0 h-full px-4 text-[#617589] hover:text-primary transition-colors flex items-center justify-center focus:outline-none" onclick="togglePassword()">
                                <span class="material-symbols-outlined" id="password-toggle-icon">visibility</span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />

                        @if (Route::has('password.request'))
                        <div class="flex justify-end mt-1">
                            <a class="text-sm font-medium text-primary hover:text-primary/80 transition-colors" href="{{ route('password.request') }}">
                                Forgot Password?
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full h-14 rounded-lg bg-primary hover:bg-primary/90 text-white text-base font-bold leading-normal transition-all shadow-md hover:shadow-lg focus:ring-4 focus:ring-primary/30 active:scale-[0.99] flex items-center justify-center gap-2">
                            <span>Sign In</span>
                            <span class="material-symbols-outlined text-xl">login</span>
                        </button>
                    </div>
                </form>

                <!-- Footer / Helper -->
                <div class="mt-8 text-center">
                    <p class="text-[#617589] dark:text-gray-500 text-sm">
                        © {{ date('Y') }} University KTM System. <br>
                        Need help? Contact <a class="text-primary hover:underline" href="#">IT Support</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = 'visibility';
            }
        }

    </script>
</x-guest-layout>
