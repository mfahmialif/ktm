<div>
    <!-- Flash Message -->
    @if (session('success'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
        <span class="material-symbols-outlined icon-filled text-emerald-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Breadcrumbs -->
    <div class="flex flex-wrap gap-2 mb-2">
        <a class="text-[#617589] dark:text-gray-400 text-sm font-medium hover:text-primary transition-colors" href="{{ route('dashboard') }}">Home</a>
        <span class="text-[#617589] dark:text-gray-400 text-sm font-medium">/</span>
        <span class="text-[#111418] dark:text-white text-sm font-medium">Manage Templates</span>
    </div>

    <!-- Page Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-bold leading-tight tracking-tight">Template Management</h1>
            <p class="mt-1 text-sm text-[#617589] dark:text-slate-400">Create and configure KTM templates for different academic years.</p>
        </div>
        <a href="{{ route('templates.create') }}" class="flex shrink-0 items-center justify-center gap-2 rounded-lg h-11 px-5 bg-primary hover:bg-blue-600 transition-colors text-white text-sm font-bold shadow-md shadow-blue-500/20">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>Add New Template</span>
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="flex flex-col md:flex-row gap-4 bg-white dark:bg-[#1a2632] p-5 rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm mb-4">
        <div class="relative flex-1 min-w-[240px]">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#617589] material-symbols-outlined">search</span>
            <input wire:model.live.debounce.300ms="search" class="w-full h-12 rounded-lg border border-[#e5e7eb] dark:border-[#2a3b4d] bg-background-light dark:bg-[#23303d] text-[#111418] dark:text-white placeholder:text-[#617589] pl-11 pr-4 text-sm focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" placeholder="Search Template Name..." />
            <div wire:loading wire:target="search" class="absolute right-4 top-1/2 -translate-y-1/2">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-primary border-t-transparent"></div>
            </div>
        </div>
        <div class="relative w-full md:w-64">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#617589] material-symbols-outlined">calendar_month</span>
            <select wire:model.live="filterAcademicYear" class="w-full h-12 rounded-lg border border-[#e5e7eb] dark:border-[#2a3b4d] bg-background-light dark:bg-[#23303d] text-[#111418] dark:text-white pl-11 pr-8 text-sm focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27%23617589%27%3e%3cpath d=%27M7 10l5 5 5-5z%27/%3e%3c/svg%3e'); background-position: right 8px center; background-size: 20px; background-repeat: no-repeat;">
                <option value="">All Academic Years</option>
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="overflow-x-auto rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] bg-white dark:bg-[#1a2632] shadow-sm">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50 dark:bg-[#23303d] border-b border-[#e5e7eb] dark:border-[#2a3b4d]">
                <tr>
                    <th class="px-6 py-4 text-left text-[#617589] dark:text-gray-300 text-xs font-bold uppercase tracking-wider">Academic Year</th>
                    <th class="px-6 py-4 text-left text-[#617589] dark:text-gray-300 text-xs font-bold uppercase tracking-wider">Template Name</th>
                    <th class="px-6 py-4 text-left text-[#617589] dark:text-gray-300 text-xs font-bold uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-[#617589] dark:text-gray-300 text-xs font-bold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e7eb] dark:divide-[#2a3b4d]" wire:loading.class="opacity-50" wire:target="search, filterAcademicYear, gotoPage, previousPage, nextPage">
                @forelse($templates as $template)
                <tr class="hover:bg-gray-50 dark:hover:bg-[#23303d] transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#617589] text-[20px]">school</span>
                            <span class="text-[#111418] dark:text-white text-sm font-medium">
                                {{ $template->academicYear->name ?? 'N/A' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-[#111418] dark:text-white text-sm font-bold">{{ $template->name }}</span>
                            <span class="text-[#617589] dark:text-gray-400 text-xs">
                                {{ $template->updated_at ? 'Last modified: ' . $template->updated_at->diffForHumans() : 'Created: ' . $template->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($template->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                            <span class="size-1.5 rounded-full bg-green-500"></span>
                            Active
                        </span>
                        @elseif($template->status === 'incomplete')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800">
                            <span class="size-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                            Incomplete
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400 border border-gray-200 dark:border-gray-800">
                            <span class="size-1.5 rounded-full bg-gray-500"></span>
                            Archived
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('templates.upload', $template) }}" class="flex items-center justify-center size-9 rounded-lg text-primary hover:bg-primary/10 transition-colors border border-transparent hover:border-primary/20" title="Upload KTM Background">
                                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                            </a>
                            <a href="{{ route('templates.configure', $template) }}" class="flex items-center justify-center size-9 rounded-lg text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors border border-transparent hover:border-amber-200" title="Configure Fields">
                                <span class="material-symbols-outlined text-[20px]">tune</span>
                            </a>
                            <a href="{{ route('templates.edit', $template) }}" class="flex items-center justify-center size-9 rounded-lg text-[#617589] dark:text-gray-300 hover:text-primary hover:bg-primary/10 transition-colors border border-transparent hover:border-primary/20" title="Edit Template">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            @if($template->status !== 'incomplete')
                            <form action="{{ route('templates.toggle-status', $template) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="flex items-center justify-center size-9 rounded-lg text-[#617589] dark:text-gray-300 hover:text-primary hover:bg-primary/10 transition-colors border border-transparent hover:border-primary/20" title="{{ $template->status === 'active' ? 'Archive' : 'Activate' }}">
                                    <span class="material-symbols-outlined text-[20px]">{{ $template->status === 'active' ? 'archive' : 'unarchive' }}</span>
                                </button>
                            </form>
                            @endif
                            <div class="w-px h-4 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                            <form action="{{ route('templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center justify-center size-9 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border border-transparent hover:border-red-200" title="Delete Template">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">description</span>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada template KTM</p>
                            <a href="{{ route('templates.create') }}" class="mt-2 text-primary hover:underline text-sm font-medium">
                                + Tambah Template Baru
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($templates->hasPages())
        <div class="flex items-center justify-between p-4 bg-white dark:bg-[#1a2632] border-t border-[#e5e7eb] dark:border-[#2a3b4d]">
            <span class="text-sm text-[#617589] dark:text-slate-400">
                Showing <strong>{{ $templates->firstItem() }}-{{ $templates->lastItem() }}</strong> of <strong>{{ $templates->total() }}</strong> templates
            </span>
            <div class="flex gap-1">
                {{ $templates->links('livewire.admin.academic-year.pagination') }}
            </div>
        </div>
        @endif
    </div>
</div>
