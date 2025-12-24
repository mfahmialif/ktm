<x-admin-layout>
    <x-slot name="title">Edit Student</x-slot>

    <div class="max-w-4xl">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-[#617589] dark:text-slate-400 font-medium mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-primary">Dashboard</a>
            <span class="mx-2">›</span>
            <a href="{{ route('students.index') }}" class="hover:text-primary">Students</a>
            <span class="mx-2">›</span>
            <span class="text-[#111418] dark:text-white">Edit</span>
        </div>

        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-[#111418] dark:text-white">Edit Student</h2>
            <p class="mt-1 text-sm text-[#617589] dark:text-slate-400">Update student information.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm">
            <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Photo Header (Full Width) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-2">Student Photo</label>
                        <div class="flex items-center gap-4">
                            @if($student->photo)
                            <div class="h-20 w-20 rounded-full bg-cover bg-center border border-gray-200 dark:border-gray-700" style="background-image: url('{{ Storage::url($student->photo) }}')">
                            </div>
                            @else
                            <div class="h-20 w-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                <span class="material-symbols-outlined text-gray-400 text-3xl">person</span>
                            </div>
                            @endif

                            <div class="flex-1">
                                <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-lg file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-primary/10 file:text-primary
                                    hover:file:bg-primary/20
                                " />
                                <p class="mt-1 text-xs text-gray-500">Leave blank to keep current photo. Max 2MB.</p>
                            </div>
                        </div>
                        @error('photo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIM -->
                    <div>
                        <label for="nim" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            NIM <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim', $student->nim) }}" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('nim') border-red-500 @enderror" placeholder="e.g. 20240001" required />
                        @error('nim')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('name') border-red-500 @enderror" placeholder="Student Name" required />
                        @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $student->email) }}" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('email') border-red-500 @enderror" placeholder="student@example.com" />
                        @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Class -->
                    <div>
                        <label for="class" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Class
                        </label>
                        <input type="text" name="class" id="class" value="{{ old('class', $student->class) }}" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('class') border-red-500 @enderror" placeholder="e.g. IF-A" />
                        @error('class')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prodi -->
                    <div>
                        <label for="prodi" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Study Program <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="prodi" id="prodi" value="{{ old('prodi', $student->prodi) }}" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('prodi') border-red-500 @enderror" placeholder="e.g. Teknik Informatika" required />
                        @error('prodi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Angkatan -->
                    <div>
                        <label for="angkatan" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Angkatan / Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="angkatan" id="angkatan" value="{{ old('angkatan', $student->angkatan) }}" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('angkatan') border-red-500 @enderror" placeholder="e.g. 2024" required />
                        @error('angkatan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Place of Birth
                        </label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $student->tempat_lahir) }}" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('tempat_lahir') border-red-500 @enderror" placeholder="City Name" />
                        @error('tempat_lahir')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Date of Birth
                        </label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $student->tanggal_lahir ? $student->tanggal_lahir->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('tanggal_lahir') border-red-500 @enderror" />
                        @error('tanggal_lahir')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white cursor-pointer appearance-none" required>
                            <option value="">Select Gender</option>
                            <option value="L" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address (Full Width) -->
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-1">
                            Address
                        </label>
                        <textarea name="alamat" id="alamat" rows="3" class="w-full px-4 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:text-white @error('alamat') border-red-500 @enderror" placeholder="Full address">{{ old('alamat', $student->alamat) }}</textarea>
                        @error('alamat')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 dark:bg-[#23303d] px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                    <a href="{{ route('students.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1a2632] border border-gray-300 dark:border-[#2a3b4d] rounded-lg hover:bg-gray-50 dark:hover:bg-[#2a3b4d] transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors">
                        Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
