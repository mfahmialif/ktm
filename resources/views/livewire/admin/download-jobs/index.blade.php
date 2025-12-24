@php use Illuminate\Support\Facades\Storage; @endphp
<div wire:poll.2s="$refresh">
    <!-- Flash Messages -->
    @if (session('success'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
        <span class="material-symbols-outlined icon-filled text-emerald-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Breadcrumb -->
    <div class="flex flex-wrap gap-2 mb-4">
        <a class="text-[#617589] dark:text-gray-400 text-sm font-medium hover:text-primary" href="{{ route('dashboard') }}">Dashboard</a>
        <span class="text-[#617589] dark:text-gray-500 text-sm font-medium">/</span>
        <a class="text-[#617589] dark:text-gray-400 text-sm font-medium hover:text-primary" href="{{ route('ktm-generator.index') }}">Generator</a>
        <span class="text-[#617589] dark:text-gray-500 text-sm font-medium">/</span>
        <span class="text-[#111418] dark:text-white text-sm font-medium">Download Jobs</span>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-6 mb-6">
        <div class="flex flex-col gap-2 max-w-2xl">
            <h1 class="text-[#111418] dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">KTM Download Jobs</h1>
            <p class="text-[#617589] dark:text-gray-400 text-base font-normal">Manage your bulk KTM download jobs. Download completed ZIP files or delete old jobs.</p>
        </div>
        <a href="{{ route('ktm-generator.index') }}" class="flex items-center gap-2 px-6 py-3 bg-primary text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition-colors shadow-md">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Back to Generator
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center bg-white dark:bg-[#1a2632] p-4 rounded-xl shadow-sm border border-[#e5e7eb] dark:border-gray-800 mb-6">
        <label class="flex flex-col h-11 w-full md:max-w-md">
            <div class="flex w-full flex-1 items-stretch rounded-lg h-full group">
                <div class="text-[#617589] flex border-none bg-[#f0f2f4] dark:bg-gray-800 items-center justify-center pl-4 rounded-l-lg">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input wire:model.live.debounce.300ms="search" class="flex w-full min-w-0 flex-1 rounded-lg text-[#111418] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary border-none bg-[#f0f2f4] dark:bg-gray-800 h-full placeholder:text-[#617589] px-4 rounded-l-none pl-2 text-sm" placeholder="Search by template or download ID..." />
            </div>
        </label>
        <div class="flex gap-2 flex-wrap items-center w-full md:w-auto">
            <!-- Template Filter -->
            <div class="relative">
                <select wire:model.live="filterTemplate" class="appearance-none h-9 rounded-lg bg-primary/10 text-primary pl-4 pr-8 text-sm font-medium border border-primary/30">
                    <option value="">All Templates</option>
                    @foreach($allTemplates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined text-[18px] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-primary">expand_more</span>
            </div>
            <!-- Status Filter -->
            <div class="relative">
                <select wire:model.live="filterStatus" class="appearance-none h-9 rounded-lg bg-[#f0f2f4] dark:bg-gray-800 text-[#111418] dark:text-white pl-4 pr-8 text-sm font-medium border-none">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                </select>
                <span class="material-symbols-outlined text-[18px] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">expand_more</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="w-full overflow-hidden rounded-xl border border-[#e5e7eb] dark:border-gray-800 bg-white dark:bg-[#1a2632] shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-[#f9fafb] dark:bg-gray-900/50 border-b border-[#e5e7eb] dark:border-gray-800">
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Template</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Files</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Status</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Size</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Created</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb] dark:divide-gray-800">
                    @forelse($downloadJobs as $job)
                    <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="p-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-[#111418] dark:text-white">{{ $job->template->name ?? '-' }}</span>
                                <span class="text-xs text-[#617589] dark:text-gray-500">ID: {{ Str::limit($job->download_id, 12) }}</span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                @if ($job->isRunning())
                                <span class="text-sm font-medium text-[#111418] dark:text-white">{{ $job->processed_files }} / {{ $job->total_files }}</span>
                                <div class="w-16 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 transition-all" style="width: {{ $job->progress_percentage }}%"></div>
                                </div>
                                @else
                                <span class="text-sm font-medium text-[#111418] dark:text-white">{{ $job->total_files }} files</span>
                                @endif
                            </div>
                        </td>
                        <td class="p-4">
                            @if($job->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                Completed
                            </span>
                            @elseif($job->status === 'processing')
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-900/50">
                                <span class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span>
                                Processing
                            </span>
                            @elseif($job->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 border border-gray-200 dark:border-gray-900/50">
                                Pending
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-900/50">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                Failed
                            </span>
                            @endif
                        </td>
                        <td class="p-4 text-sm text-[#111418] dark:text-white">{{ $job->formatted_file_size }}</td>
                        <td class="p-4 text-sm text-[#617589] dark:text-gray-400">{{ $job->created_at->diffForHumans() }}</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                @if($job->isCompleted() && $job->zip_path)
                                <a href="{{ route('download-jobs.download', $job->id) }}" class="p-2 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors" title="Download ZIP">
                                    <span class="material-symbols-outlined text-[20px]">download</span>
                                </a>
                                @endif
                                <button wire:click="deleteJob({{ $job->id }})" wire:confirm="Yakin ingin menghapus download job ini?" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">folder_off</span>
                                <p class="text-gray-500 dark:text-gray-400">Belum ada download job</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($downloadJobs->hasPages())
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-[#e5e7eb] dark:border-gray-800 px-4 py-3 sm:px-6 bg-white dark:bg-[#1a2632] rounded-b-xl">
            <div class="hidden sm:block">
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    Showing <span class="font-bold text-[#111418] dark:text-white">{{ $downloadJobs->firstItem() }}</span>
                    to <span class="font-bold text-[#111418] dark:text-white">{{ $downloadJobs->lastItem() }}</span>
                    of <span class="font-bold text-[#111418] dark:text-white">{{ $downloadJobs->total() }}</span> jobs
                </p>
            </div>
            <div class="w-full sm:w-auto overflow-x-auto">
                {{ $downloadJobs->links('components.pagination') }}
            </div>
        </div>
        @endif
    </div>
</div>
