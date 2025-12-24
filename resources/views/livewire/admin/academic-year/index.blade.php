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
                <span>Dashboard</span>
                <span class="mx-2">›</span>
                <span class="text-[#111418] dark:text-white">Tahun Akademik</span>
            </div>
            <h2 class="text-3xl font-bold tracking-tight text-[#111418] dark:text-white">Kelola Tahun Akademik</h2>
            <p class="mt-1 text-sm text-[#617589] dark:text-slate-400">Manage academic years, semesters, and active status.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('academic-years.create') }}" class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-white transition-colors bg-primary rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <span class="material-symbols-outlined mr-2 text-[20px]">add</span>
                Tambah Tahun
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-[#1a2632] rounded-t-xl border border-b-0 border-[#e5e7eb] dark:border-[#2a3b4d] p-4 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="block w-full p-2.5 pl-10 text-sm text-[#111418] border border-[#e5e7eb] rounded-lg bg-[#f8fafc] focus:ring-primary focus:border-primary dark:bg-[#23303d] dark:border-[#2a3b4d] dark:placeholder-[#617589] dark:text-white" placeholder="Cari kode atau nama tahun..." />
            <div wire:loading wire:target="search" class="absolute inset-y-0 right-3 flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-primary border-t-transparent"></div>
            </div>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <select wire:model.live="filterSemester" class="px-4 py-2.5 text-sm font-medium text-[#111418] bg-white border border-[#e5e7eb] rounded-lg focus:ring-primary focus:border-primary dark:bg-[#1a2632] dark:text-white dark:border-[#2a3b4d] cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27%23617589%27%3e%3cpath d=%27M7 10l5 5 5-5z%27/%3e%3c/svg%3e'); background-position: right 8px center; background-size: 20px; background-repeat: no-repeat; padding-right: 36px;">
                <option value="">Semua Semester</option>
                <option value="ganjil">Ganjil</option>
                <option value="genap">Genap</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="relative overflow-x-auto border border-[#e5e7eb] dark:border-[#2a3b4d] rounded-b-xl bg-white dark:bg-[#1a2632] shadow-sm">
        <table class="w-full text-sm text-left text-[#617589] dark:text-slate-400">
            <thead class="text-xs text-[#617589] uppercase bg-gray-50 dark:bg-[#23303d] dark:text-slate-400 border-b border-[#e5e7eb] dark:border-[#2a3b4d]">
                <tr>
                    <th scope="col" class="px-6 py-4 font-semibold">Kode</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Nama Tahun Akademik</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Semester</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody wire:loading.class="opacity-50" wire:target="search, filterSemester, gotoPage, previousPage, nextPage">
                @forelse($academicYears as $year)
                <tr class="bg-white border-b dark:bg-[#1a2632] dark:border-[#2a3b4d] hover:bg-gray-50 dark:hover:bg-[#23303d] transition-colors">
                    <th scope="row" class="px-6 py-4 font-medium text-[#111418] whitespace-nowrap dark:text-white">
                        {{ $year->code }}
                    </th>
                    <td class="px-6 py-4 font-medium text-[#111418] dark:text-white">
                        {{ $year->name }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $year->semester === 'ganjil' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' }}">
                            {{ ucfirst($year->semester) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($year->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700/30 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                            Inactive
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('academic-years.toggle-active', $year) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="p-2 text-[#617589] hover:text-primary hover:bg-blue-50 rounded-lg transition-colors dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white" title="{{ $year->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <span class="material-symbols-outlined text-[20px]">{{ $year->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                                </button>
                            </form>
                            <a href="{{ route('academic-years.edit', $year) }}" class="p-2 text-[#617589] hover:text-primary hover:bg-blue-50 rounded-lg transition-colors dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white" title="Edit">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            <form action="{{ route('academic-years.destroy', $year) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahun akademik ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-[#617589] hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-red-400" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">calendar_month</span>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada data tahun akademik</p>
                            <a href="{{ route('academic-years.create') }}" class="mt-2 text-primary hover:underline text-sm font-medium">
                                + Tambah Tahun Akademik
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($academicYears->hasPages())
        <div class="flex items-center justify-between p-4 bg-white dark:bg-[#1a2632] border-t border-[#e5e7eb] dark:border-[#2a3b4d]">
            <span class="text-sm font-normal text-[#617589] dark:text-slate-400">
                Showing <span class="font-semibold text-[#111418] dark:text-white">{{ $academicYears->firstItem() }}-{{ $academicYears->lastItem() }}</span> of <span class="font-semibold text-[#111418] dark:text-white">{{ $academicYears->total() }}</span>
            </span>
            <div class="flex gap-1">
                {{ $academicYears->links('livewire.admin.academic-year.pagination') }}
            </div>
        </div>
        @endif
    </div>
</div>
