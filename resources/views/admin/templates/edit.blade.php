<x-admin-layout>
    <x-slot name="title">Edit Template</x-slot>

    <div class="max-w-2xl">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-[#617589] dark:text-slate-400 font-medium mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-primary">Dashboard</a>
            <span class="mx-2">›</span>
            <a href="{{ route('templates.index') }}" class="hover:text-primary">Template KTM</a>
            <span class="mx-2">›</span>
            <span class="text-[#111418] dark:text-white">Edit</span>
        </div>

        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-[#111418] dark:text-white">Edit Template</h2>
            <p class="mt-1 text-sm text-[#617589] dark:text-slate-400">Ubah data template KTM.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm">
            <form action="{{ route('templates.update', $template) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-5">
                    <!-- Template Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Nama Template <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $template->name) }}" placeholder="Contoh: Standard Undergraduate V1" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('name') border-red-500 @enderror" />
                        @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic Year -->
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Tahun Akademik
                        </label>
                        <select name="academic_year_id" id="academic_year_id" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27%23617589%27%3e%3cpath d=%27M7 10l5 5 5-5z%27/%3e%3c/svg%3e'); background-position: right 8px center; background-size: 20px; background-repeat: no-repeat; padding-right: 36px;">
                            <option value="">-- Pilih Tahun Akademik (Opsional) --</option>
                            @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id', $template->academic_year_id) == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27%23617589%27%3e%3cpath d=%27M7 10l5 5 5-5z%27/%3e%3c/svg%3e'); background-position: right 8px center; background-size: 20px; background-repeat: no-repeat; padding-right: 36px;">
                            <option value="incomplete" {{ old('status', $template->status) === 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                            <option value="active" {{ old('status', $template->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="archived" {{ old('status', $template->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 dark:bg-[#23303d] px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                    <a href="{{ route('templates.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1a2632] border border-gray-300 dark:border-[#2a3b4d] rounded-lg hover:bg-gray-50 dark:hover:bg-[#2a3b4d] transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
