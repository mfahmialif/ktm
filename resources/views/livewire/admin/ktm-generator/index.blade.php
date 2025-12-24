@php use Illuminate\Support\Facades\Storage; @endphp
<div wire:poll.1s="$refresh">
    <!-- Flash Message - Success -->
    @if (session('success'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
        <span class="material-symbols-outlined icon-filled text-emerald-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Flash Message - Error -->
    @if (session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" x-transition>
        <span class="material-symbols-outlined icon-filled text-red-500">error</span>
        {{ session('error') }}
    </div>
    @endif

    <!-- Progress Bar for Batch Generation -->
    @if ($activeBatch)
    <div class="mb-4 p-4 rounded-xl bg-gradient-to-r from-blue-50 to-primary/10 dark:from-blue-900/20 dark:to-primary/20 border border-primary/30">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                @if ($activeBatch->isRunning())
                <span class="material-symbols-outlined text-primary animate-spin">progress_activity</span>
                <span class="text-sm font-bold text-primary">Generating KTM...</span>
                @else
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Complete!</span>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <span class="text-2xl font-black text-primary">{{ $activeBatch->processed_students }}</span>
                    <span class="text-sm text-gray-500">/ {{ $activeBatch->total_students }}</span>
                </div>
                @if (!$activeBatch->isRunning())
                <button wire:click="dismissBatch" class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="relative h-4 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="absolute inset-0 h-full bg-gradient-to-r from-primary to-blue-400 transition-all duration-500 ease-out rounded-full" style="width: {{ $activeBatch->progress_percentage }}%">
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-[10px] font-bold text-white drop-shadow-sm">{{ $activeBatch->progress_percentage }}%</span>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex items-center gap-4 mt-3 text-xs">
            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-[14px]">check</span>
                Sukses: {{ $activeBatch->success_count }}
            </span>
            @if ($activeBatch->failed_count > 0)
            <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400">
                <span class="material-symbols-outlined text-[14px]">error</span>
                Gagal: {{ $activeBatch->failed_count }}
            </span>
            @endif
            @if ($activeBatch->template)
            <span class="inline-flex items-center gap-1 text-gray-500">
                <span class="material-symbols-outlined text-[14px]">badge</span>
                {{ $activeBatch->template->name }}
            </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Progress Bar for Download ZIP -->
    @if ($activeDownload)
    <div class="mb-4 p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-800/20 border border-emerald-500/30">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                @if ($activeDownload->isRunning())
                <span class="material-symbols-outlined text-emerald-600 animate-spin">progress_activity</span>
                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400">Creating ZIP...</span>
                @else
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">ZIP Ready!</span>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <span class="text-2xl font-black text-emerald-600">{{ $activeDownload->processed_files }}</span>
                    <span class="text-sm text-gray-500">/ {{ $activeDownload->total_files }}</span>
                </div>
                @if ($activeDownload->isCompleted())
                <a href="{{ route('download-jobs.download', $activeDownload->id) }}" class="flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    Download ZIP
                </a>
                @endif
                @if (!$activeDownload->isRunning())
                <button wire:click="dismissDownload" class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="relative h-4 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="absolute inset-0 h-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-500 ease-out rounded-full" style="width: {{ $activeDownload->progress_percentage }}%">
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-[10px] font-bold text-white drop-shadow-sm">{{ $activeDownload->progress_percentage }}%</span>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex items-center gap-4 mt-3 text-xs">
            @if ($activeDownload->isCompleted())
            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-[14px]">folder_zip</span>
                Size: {{ $activeDownload->formatted_file_size }}
            </span>
            @endif
            @if ($activeDownload->template)
            <span class="inline-flex items-center gap-1 text-gray-500">
                <span class="material-symbols-outlined text-[14px]">badge</span>
                {{ $activeDownload->template->name }}
            </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Breadcrumb -->
    <div class="flex flex-wrap gap-2 mb-4">
        <a class="text-[#617589] dark:text-gray-400 text-sm font-medium hover:text-primary" href="{{ route('dashboard') }}">Dashboard</a>
        <span class="text-[#617589] dark:text-gray-500 text-sm font-medium">/</span>
        <span class="text-[#111418] dark:text-white text-sm font-medium">Generate KTM</span>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-6 mb-6">
        <div class="flex flex-col gap-2 max-w-2xl">
            <h1 class="text-[#111418] dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">Student Data & KTM Generation</h1>
            <p class="text-[#617589] dark:text-gray-400 text-base font-normal">Fetch latest student data, review details, and generate ID cards in bulk or individually.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 items-end w-full md:w-auto">
            <div class="flex flex-col gap-1.5 w-full sm:w-auto flex-1">
                <label class="text-xs font-bold text-[#617589] dark:text-gray-400 uppercase tracking-wider">Template</label>
                <div class="relative">
                    <select wire:model.live="selectedTemplateId" class="w-full sm:w-[220px] appearance-none bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-gray-700 text-[#111418] dark:text-white text-sm font-bold rounded-lg h-11 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary">
                        <option value="">-- Pilih Template --</option>
                        @foreach($allTemplates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#617589]">
                        <span class="material-symbols-outlined">expand_more</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="button" wire:click="generateAll" wire:confirm="Generate KTM untuk semua mahasiswa yang ready?" wire:loading.attr="disabled" wire:target="generateAll" class="flex shrink-0 min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-11 px-6 bg-primary text-white text-sm font-bold hover:bg-blue-600 transition-all shadow-md gap-2 flex-1 sm:flex-initial disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="generateAll" class="material-symbols-outlined text-[20px]">print_connect</span>
                    <span wire:loading wire:target="generateAll" class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="generateAll">Generate All</span>
                    <span wire:loading wire:target="generateAll">Generating...</span>
                </button>
                <button type="button" wire:click="downloadAll" wire:confirm="Download semua KTM yang sudah ter-generate?" wire:loading.attr="disabled" wire:target="downloadAll" class="flex shrink-0 min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-11 px-6 bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 transition-all shadow-md gap-2 flex-1 sm:flex-initial disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="downloadAll" class="material-symbols-outlined text-[20px]">download</span>
                    <span wire:loading wire:target="downloadAll" class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="downloadAll">Download All</span>
                    <span wire:loading wire:target="downloadAll">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center bg-white dark:bg-[#1a2632] p-4 rounded-xl shadow-sm border border-[#e5e7eb] dark:border-gray-800 mb-6">
        <label class="flex flex-col h-11 w-full md:max-w-md">
            <div class="flex w-full flex-1 items-stretch rounded-lg h-full group">
                <div class="text-[#617589] flex border-none bg-[#f0f2f4] dark:bg-gray-800 items-center justify-center pl-4 rounded-l-lg">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input wire:model.live.debounce.300ms="search" class="flex w-full min-w-0 flex-1 rounded-lg text-[#111418] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary border-none bg-[#f0f2f4] dark:bg-gray-800 h-full placeholder:text-[#617589] px-4 rounded-l-none pl-2 text-sm" placeholder="Search by NIM, Name or Prodi..." />
            </div>
        </label>
        <div class="flex gap-2 flex-wrap items-center w-full md:w-auto overflow-x-auto pb-1 md:pb-0 justify-start md:justify-end">
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
            <!-- Angkatan Filter -->
            <div class="relative">
                <select wire:model.live="filterAngkatan" class="appearance-none h-9 rounded-lg bg-[#f0f2f4] dark:bg-gray-800 text-[#111418] dark:text-white pl-4 pr-8 text-sm font-medium border-none">
                    <option value="">All Angkatan</option>
                    @foreach($angkatanList as $angkatan)
                    <option value="{{ $angkatan }}">{{ $angkatan }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined text-[18px] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">expand_more</span>
            </div>
            <!-- Prodi Filter -->
            <div class="relative">
                <select wire:model.live="filterProdi" class="appearance-none h-9 rounded-lg bg-[#f0f2f4] dark:bg-gray-800 text-[#111418] dark:text-white pl-4 pr-8 text-sm font-medium border-none">
                    <option value="">All Prodi</option>
                    @foreach($prodiList as $prodi)
                    <option value="{{ $prodi }}">{{ $prodi }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined text-[18px] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">expand_more</span>
            </div>
            <!-- Status Filter -->
            <div class="relative">
                <select wire:model.live="filterStatus" class="appearance-none h-9 rounded-lg bg-[#f0f2f4] dark:bg-gray-800 text-[#111418] dark:text-white pl-4 pr-8 text-sm font-medium border-none">
                    <option value="">All Status</option>
                    <option value="ready">Ready</option>
                    <option value="generated">Generated</option>
                    <option value="no_photo">No Photo</option>
                    <option value="error">Error</option>
                </select>
                <span class="material-symbols-outlined text-[18px] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">expand_more</span>
            </div>
        </div>
    </div>

    <!-- Bulk Action -->
    {{-- @if(count($selectedStudents) > 0) --}}
    {{-- <div class="flex items-center justify-between bg-primary/10 border border-primary/30 p-4 rounded-xl mb-4"> --}}
    <div class="flex items-center justify-between bg-primary/10 border border-primary/30 p-4 rounded-xl mb-4 {{ count($selectedStudents) === 0 ? 'hidden' : '' }}">
        <span class="text-sm font-medium text-primary">{{ count($selectedStudents) }} mahasiswa dipilih</span>
        <div class="flex gap-2">
            <button type="button" wire:click="generateBulk" wire:confirm="Generate KTM untuk {{ count($selectedStudents) }} mahasiswa yang dipilih?" wire:loading.attr="disabled" wire:target="generateBulk" class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="generateBulk" class="material-symbols-outlined text-[18px]">print_connect</span>
                <span wire:loading wire:target="generateBulk" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                <span wire:loading.remove wire:target="generateBulk">Generate Selected</span>
                <span wire:loading wire:target="generateBulk">Generating...</span>
            </button>
            <button type="button" wire:click="downloadBulk" wire:confirm="Download KTM untuk {{ count($selectedStudents) }} mahasiswa yang dipilih?" wire:loading.attr="disabled" wire:target="downloadBulk" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="downloadBulk" class="material-symbols-outlined text-[18px]">download</span>
                <span wire:loading wire:target="downloadBulk" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                <span wire:loading.remove wire:target="downloadBulk">Download Selected</span>
                <span wire:loading wire:target="downloadBulk">Processing...</span>
            </button>
        </div>
    </div>
    {{-- @endif --}}

    <!-- Table -->
    <div class="w-full overflow-hidden rounded-xl border border-[#e5e7eb] dark:border-gray-800 bg-white dark:bg-[#1a2632] shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-[#f9fafb] dark:bg-gray-900/50 border-b border-[#e5e7eb] dark:border-gray-800">
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer" />
                        </th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">NIM</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Student Name</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Study Program</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Angkatan</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400">Status</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[#617589] dark:text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb] dark:divide-gray-800">
                    @forelse($students as $student)
                    @php $status = $this->getStudentStatus($student); @endphp
                    <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="p-4 text-center">
                            <input type="checkbox" wire:model.live="selectedStudents" value="{{ $student->id }}" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer" />
                        </td>
                        <td class="p-4 text-sm font-medium text-[#111418] dark:text-white font-mono">{{ $student->nim }}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                @if($student->photo)
                                <div class="h-9 w-9 rounded-full bg-gray-200 dark:bg-gray-700 bg-cover bg-center border border-gray-100 dark:border-gray-700 shadow-sm" style="background-image: url('{{ Storage::url($student->photo) }}');"></div>
                                @else
                                <div class="h-9 w-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 text-xs font-bold border border-gray-200 dark:border-gray-700">
                                    {{ strtoupper(substr($student->name, 0, 2)) }}
                                </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-[#111418] dark:text-white">{{ $student->name }}</span>
                                    <span class="text-xs text-[#617589] dark:text-gray-500">{{ $student->email ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-sm text-[#111418] dark:text-white">{{ $student->prodi ?? $student->major ?? '-' }}</td>
                        <td class="p-4 text-sm text-[#111418] dark:text-white">{{ $student->angkatan ?? '-' }}</td>
                        <td class="p-4">
                            @if($status === 'ready')
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Ready
                            </span>
                            @elseif($status === 'generated')
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-900/50">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                Generated
                            </span>
                            @elseif($status === 'no_photo')
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50">
                                <span class="material-symbols-outlined text-[14px]">image</span>
                                No Photo
                            </span>
                            @elseif($status === 'error')
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-900/50" title="{{ $student->ktm_error_message ?? 'Error' }}">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                Error
                            </span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                @php
                                $ktmStatus = $this->getStudentKtmStatus($student);
                                @endphp

                                @if($ktmStatus && $ktmStatus->file_path && Storage::disk('public')->exists($ktmStatus->file_path))
                                <a href="{{ Storage::url($ktmStatus->file_path) }}" target="_blank" class="p-2 text-primary hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="View KTM">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </a>
                                @else
                                <button disabled class="p-2 text-gray-300 dark:text-gray-600 cursor-not-allowed rounded-lg" title="No KTM generated yet">
                                    <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                                </button>
                                @endif

                                @if($status === 'ready')
                                <button type="button" wire:click="generateSingle({{ $student->id }})" wire:loading.attr="disabled" wire:target="generateSingle({{ $student->id }})" class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-primary hover:bg-blue-600 rounded-lg transition-colors shadow-sm disabled:opacity-50">
                                    <span wire:loading wire:target="generateSingle({{ $student->id }})" class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span>
                                    <span wire:loading.remove wire:target="generateSingle({{ $student->id }})">Generate</span>
                                    <span wire:loading wire:target="generateSingle({{ $student->id }})">Processing...</span>
                                </button>
                                @elseif($status === 'no_photo')
                                <button type="button" wire:click="generateSingle({{ $student->id }})" wire:loading.attr="disabled" wire:target="generateSingle({{ $student->id }})" class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition-colors shadow-sm disabled:opacity-50" title="Generate dengan foto default">
                                    <span wire:loading wire:target="generateSingle({{ $student->id }})" class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span>
                                    <span wire:loading.remove wire:target="generateSingle({{ $student->id }})">Generate</span>
                                    <span wire:loading wire:target="generateSingle({{ $student->id }})">Processing...</span>
                                </button>
                                @elseif($status === 'generated')
                                <button type="button" wire:click="generateSingle({{ $student->id }})" wire:loading.attr="disabled" wire:target="generateSingle({{ $student->id }})" class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors disabled:opacity-50">
                                    <span wire:loading wire:target="generateSingle({{ $student->id }})" class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span>
                                    <span wire:loading.remove wire:target="generateSingle({{ $student->id }})">Regenerate</span>
                                    <span wire:loading wire:target="generateSingle({{ $student->id }})">Processing...</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">person_off</span>
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada data mahasiswa</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="flex items-center justify-between border-t border-[#e5e7eb] dark:border-gray-800 px-4 py-3 sm:px-6 bg-white dark:bg-[#1a2632] rounded-b-xl">
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        Showing <span class="font-bold text-[#111418] dark:text-white">{{ $students->firstItem() }}</span>
                        to <span class="font-bold text-[#111418] dark:text-white">{{ $students->lastItem() }}</span>
                        of <span class="font-bold text-[#111418] dark:text-white">{{ $students->total() }}</span> results
                    </p>
                </div>
                <div>
                    {{ $students->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
