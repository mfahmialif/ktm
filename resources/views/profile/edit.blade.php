<x-admin-layout>
    <x-slot:title>
        Profile Settings
    </x-slot:title>

    <!-- Breadcrumb and Header -->
    <div class="flex flex-col gap-2 mb-6">
        <div class="flex items-center text-sm text-[#617589] dark:text-slate-400 font-medium">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Home</a>
            <span class="mx-2">/</span>
            <span class="text-[#111418] dark:text-white">Profile</span>
        </div>
        <h1 class="text-[#111418] dark:text-white text-3xl font-bold leading-tight tracking-tight">Profile Settings</h1>
    </div>

    <div class="flex flex-col gap-6">
        <!-- Profile Information -->
        <div class="p-6 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="p-6 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="p-6 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-admin-layout>
