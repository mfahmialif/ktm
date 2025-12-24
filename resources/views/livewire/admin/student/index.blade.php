<div>
    <!-- Flash Message -->
    @if (session('success'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
        <span class="material-symbols-outlined icon-filled text-emerald-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Page Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center text-sm text-[#617589] dark:text-slate-400 font-medium mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="mx-2">›</span>
                <span class="text-[#111418] dark:text-white">Students</span>
            </div>
            <h2 class="text-3xl font-bold tracking-tight text-[#111418] dark:text-white">Student Management</h2>
            <p class="mt-1 text-sm text-[#617589] dark:text-slate-400">Manage student data, profiles, and academic information.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('students.export') }}" target="_blank" class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-[#111418] dark:text-white transition-colors bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] rounded-lg hover:bg-gray-50 dark:hover:bg-[#23303d] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 shadow-sm">
                <span class="material-symbols-outlined mr-2 text-[20px]">download</span>
                Export
            </a>
            <a href="{{ route('students.import') }}" class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-[#111418] dark:text-white transition-colors bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] rounded-lg hover:bg-gray-50 dark:hover:bg-[#23303d] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 shadow-sm">
                <span class="material-symbols-outlined mr-2 text-[20px]">upload</span>
                Import
            </a>
            <a href="{{ route('students.create') }}" class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-white transition-colors bg-primary rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-sm">
                <span class="material-symbols-outlined mr-2 text-[20px]">add</span>
                Add Student
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-[#1a2632] rounded-t-xl border border-b-0 border-[#e5e7eb] dark:border-[#2a3b4d] p-4 flex flex-col md:flex-row gap-4 justify-between items-center shadow-sm">
        <div class="relative w-full md:max-w-md">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="block w-full h-10 pl-10 pr-3 text-sm text-[#111418] border border-[#e5e7eb] rounded-lg bg-[#f8fafc] focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:placeholder-[#617589] dark:text-white transition-all" placeholder="Search by NIM or Name..." />
            <div wire:loading wire:target="search" class="absolute inset-y-0 right-3 flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-primary border-t-transparent"></div>
            </div>
        </div>
        <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
            <!-- Angkatan Filter -->
            <div class="relative min-w-[140px]">
                <select wire:model.live="filterAngkatan" class="w-full appearance-none h-10 rounded-lg bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] text-[#111418] dark:text-white pl-3 pr-8 text-sm font-medium focus:ring-primary focus:border-primary cursor-pointer">
                    <option value="">All Angkatan</option>
                    @foreach($angkatanList as $angkatan)
                    <option value="{{ $angkatan }}">{{ $angkatan }}</option>
                    @endforeach
                </select>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[#617589]">
                    <span class="material-symbols-outlined text-[20px]">expand_more</span>
                </div>
            </div>

            <!-- Prodi Filter -->
            <div class="relative min-w-[180px]">
                <select wire:model.live="filterProdi" class="w-full appearance-none h-10 rounded-lg bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] text-[#111418] dark:text-white pl-3 pr-8 text-sm font-medium focus:ring-primary focus:border-primary cursor-pointer">
                    <option value="">All Study Programs</option>
                    @foreach($prodiList as $prodi)
                    <option value="{{ $prodi }}">{{ $prodi }}</option>
                    @endforeach
                </select>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[#617589]">
                    <span class="material-symbols-outlined text-[20px]">expand_more</span>
                </div>
            </div>

            <!-- Gender Filter -->
            <div class="relative min-w-[140px]">
                <select wire:model.live="filterJenisKelamin" class="w-full appearance-none h-10 rounded-lg bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] text-[#111418] dark:text-white pl-3 pr-8 text-sm font-medium focus:ring-primary focus:border-primary cursor-pointer">
                    <option value="">All Genders</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[#617589]">
                    <span class="material-symbols-outlined text-[20px]">expand_more</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="relative overflow-x-auto border border-[#e5e7eb] dark:border-[#2a3b4d] rounded-b-xl bg-white dark:bg-[#1a2632] shadow-sm">
        <table class="w-full text-sm text-left text-[#617589] dark:text-slate-400">
            <thead class="text-xs text-[#617589] uppercase bg-gray-50 dark:bg-[#23303d] dark:text-slate-400 border-b border-[#e5e7eb] dark:border-[#2a3b4d]">
                <tr>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">NIM</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Student</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Prodi</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Angkatan</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody wire:loading.class="opacity-50" wire:target="search, filterProdi, filterAngkatan, gotoPage, previousPage, nextPage">
                @forelse($students as $student)
                <tr class="bg-white border-b dark:bg-[#1a2632] dark:border-[#2a3b4d] hover:bg-gray-50 dark:hover:bg-[#23303d] transition-colors group">
                    <th scope="row" class="px-6 py-4 font-medium text-[#111418] whitespace-nowrap dark:text-white font-mono">
                        {{ $student->nim }}
                    </th>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($student->photo)
                            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 bg-cover bg-center border border-gray-100 dark:border-gray-700 shadow-sm" style="background-image: url('{{ \Illuminate\Support\Facades\Storage::url($student->photo) }}');"></div>
                            @else
                            <div class="h-10 w-10 rounded-full bg-primary/10 dark:bg-primary/20 flex items-center justify-center text-primary text-xs font-bold border border-primary/20">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                            @endif
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-[#111418] dark:text-white group-hover:text-primary transition-colors">{{ $student->name }}</span>
                                <span class="text-xs text-[#617589] dark:text-gray-500">{{ $student->email ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-[#111418] dark:text-white">
                        {{ $student->prodi ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ $student->angkatan ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacit-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('students.edit', $student) }}" class="p-2 text-[#617589] hover:text-primary hover:bg-blue-50 rounded-lg transition-colors dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white" title="Edit">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            <button wire:click="delete({{ $student->id }})" wire:confirm="Are you sure you want to delete this student?" class="p-2 text-[#617589] hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-red-400" title="Delete">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl text-gray-400 dark:text-gray-600">person_off</span>
                            </div>
                            <h3 class="text-lg font-medium text-[#111418] dark:text-white">No Students Found</h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                No student data available based on your search or filters. Try adjusting them or add a new student.
                            </p>
                            <a href="{{ route('students.create') }}" class="mt-4 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors">
                                <span class="material-symbols-outlined mr-2 text-[18px]">add</span>
                                Add Student
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="flex items-center justify-between p-4 bg-white dark:bg-[#1a2632] border-t border-[#e5e7eb] dark:border-[#2a3b4d] rounded-b-xl">
            <span class="text-sm font-normal text-[#617589] dark:text-slate-400">
                Showing <span class="font-semibold text-[#111418] dark:text-white">{{ $students->firstItem() }}-{{ $students->lastItem() }}</span> of <span class="font-semibold text-[#111418] dark:text-white">{{ $students->total() }}</span>
            </span>
            <div class="flex gap-1">
                {{ $students->links('components.pagination') }}
            </div>
        </div>
        @endif
    </div>
</div>
