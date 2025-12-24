<x-admin-layout>
    <x-slot name="title">Tambah Tahun Akademik</x-slot>

    <div class="max-w-2xl">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-[#617589] dark:text-slate-400 font-medium mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-primary">Dashboard</a>
            <span class="mx-2">›</span>
            <a href="{{ route('academic-years.index') }}" class="hover:text-primary">Tahun Akademik</a>
            <span class="mx-2">›</span>
            <span class="text-[#111418] dark:text-white">Tambah</span>
        </div>

        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-[#111418] dark:text-white">Tambah Tahun Akademik</h2>
            <p class="mt-1 text-sm text-[#617589] dark:text-slate-400">Isi form berikut untuk menambahkan tahun akademik baru.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm">
            <form action="{{ route('academic-years.store') }}" method="POST">
                @csrf

                <div class="p-6 space-y-5">
                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Kode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="Contoh: 20241" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('code') border-red-500 @enderror" />
                        @error('code')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Nama Tahun Akademik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: 2024/2025 Ganjil" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('name') border-red-500 @enderror" />
                        @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Semester -->
                    <div>
                        <label for="semester" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Semester <span class="text-red-500">*</span>
                        </label>
                        <select name="semester" id="semester" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27%23617589%27%3e%3cpath d=%27M7 10l5 5 5-5z%27/%3e%3c/svg%3e'); background-position: right 8px center; background-size: 20px; background-repeat: no-repeat; padding-right: 36px;">
                            <option value="ganjil" {{ old('semester') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ old('semester') === 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Active -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary dark:bg-[#23303d] dark:border-[#2a3b4d]" />
                        <label for="is_active" class="text-sm font-medium text-[#111418] dark:text-gray-200">
                            Set sebagai tahun akademik aktif
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 dark:bg-[#23303d] px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                    <a href="{{ route('academic-years.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1a2632] border border-gray-300 dark:border-[#2a3b4d] rounded-lg hover:bg-gray-50 dark:hover:bg-[#2a3b4d] transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
