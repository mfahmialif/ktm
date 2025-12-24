@php use Illuminate\Support\Facades\Storage; @endphp
<x-admin-layout>
    <x-slot name="title">Upload KTM Template</x-slot>

    <div class="max-w-5xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-[#111418] dark:text-white">Upload KTM Template</h1>
            <p class="mt-2 text-[#617589] dark:text-slate-400 text-lg max-w-2xl">
                Upload a high-resolution image of the blank ID card design to serve as the background for generating student IDs.
            </p>
        </div>

        <!-- Flash Message -->
        @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <span class="material-symbols-outlined icon-filled text-emerald-500">check_circle</span>
            {{ session('success') }}
        </div>
        @endif

        <!-- Upload Card -->
        <div class="bg-white dark:bg-[#1a2632] rounded-2xl shadow-sm border border-[#e5e7eb] dark:border-[#2a3b4d] p-6 md:p-10 mb-6">
            <form action="{{ route('templates.upload.store', $template) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf

                <!-- Template Info -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-[#111418] dark:text-white mb-2">
                            Template Name
                        </label>
                        <div class="relative">
                            <input type="text" value="{{ $template->name }}" disabled class="block w-full rounded-lg border-gray-300 dark:border-[#2a3b4d] bg-gray-50 dark:bg-[#23303d] text-[#111418] dark:text-white shadow-sm sm:text-sm py-2.5 px-3 opacity-75" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#111418] dark:text-white mb-2">
                            Academic Year
                        </label>
                        <div class="relative">
                            <input type="text" value="{{ $template->academicYear->name ?? 'Not assigned' }}" disabled class="block w-full rounded-lg border-gray-300 dark:border-[#2a3b4d] bg-gray-50 dark:bg-[#23303d] text-[#111418] dark:text-white shadow-sm sm:text-sm py-2.5 pl-3 pr-10 opacity-75" />
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="material-symbols-outlined text-gray-400 text-[20px]">calendar_month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Front Template Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-[#111418] dark:text-white mb-3">
                        Front Template (Depan) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative flex flex-col items-center justify-center gap-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-[#f6f7f8]/50 dark:bg-[#101922]/50 hover:bg-gray-50 dark:hover:bg-[#23303d]/50 hover:border-primary/50 transition-all cursor-pointer px-6 py-12 group" onclick="document.getElementById('front_template').click()" id="frontDropZone">
                        @if($template->front_template)
                        <div class="relative">
                            <img src="{{ Storage::url($template->front_template) }}" alt="Current Front" class="max-h-32 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="absolute -top-2 -right-2 bg-green-500 text-white p-1 rounded-full">
                                <span class="material-symbols-outlined text-[16px]">check</span>
                            </div>
                        </div>
                        <p class="text-[#617589] dark:text-slate-400 text-sm">Click to replace current front template</p>
                        @else
                        <div class="bg-white dark:bg-[#2a3b4d] p-4 rounded-full shadow-sm mb-2 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-primary text-4xl">cloud_upload</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 text-center">
                            <p class="text-[#111418] dark:text-white text-lg font-bold">Drag & drop your front template here</p>
                            <p class="text-[#617589] dark:text-slate-400 text-sm">or click to browse from your computer</p>
                        </div>
                        @endif
                        <input type="file" name="front_template" id="front_template" class="hidden" accept="image/png,image/jpg,image/jpeg" onchange="showPreview(this, 'frontPreview', 'frontDropZone')">
                    </div>
                    <div id="frontPreview" class="hidden mt-3 flex items-center gap-3 p-3 bg-primary/5 dark:bg-primary/10 rounded-lg border border-primary/20">
                        <span class="material-symbols-outlined text-primary">image</span>
                        <span id="frontFileName" class="text-sm font-medium text-[#111418] dark:text-white"></span>
                    </div>
                    @error('front_template')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Back Template Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-[#111418] dark:text-white mb-3">
                        Back Template (Belakang)
                    </label>
                    <div class="relative flex flex-col items-center justify-center gap-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-[#f6f7f8]/50 dark:bg-[#101922]/50 hover:bg-gray-50 dark:hover:bg-[#23303d]/50 hover:border-primary/50 transition-all cursor-pointer px-6 py-12 group" onclick="document.getElementById('back_template').click()" id="backDropZone">
                        @if($template->back_template)
                        <div class="relative">
                            <img src="{{ Storage::url($template->back_template) }}" alt="Current Back" class="max-h-32 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="absolute -top-2 -right-2 bg-green-500 text-white p-1 rounded-full">
                                <span class="material-symbols-outlined text-[16px]">check</span>
                            </div>
                        </div>
                        <p class="text-[#617589] dark:text-slate-400 text-sm">Click to replace current back template</p>
                        @else
                        <div class="bg-white dark:bg-[#2a3b4d] p-4 rounded-full shadow-sm mb-2 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-primary text-4xl">cloud_upload</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 text-center">
                            <p class="text-[#111418] dark:text-white text-lg font-bold">Drag & drop your back template here</p>
                            <p class="text-[#617589] dark:text-slate-400 text-sm">or click to browse from your computer</p>
                        </div>
                        @endif
                        <input type="file" name="back_template" id="back_template" class="hidden" accept="image/png,image/jpg,image/jpeg" onchange="showPreview(this, 'backPreview', 'backDropZone')">
                    </div>
                    <div id="backPreview" class="hidden mt-3 flex items-center gap-3 p-3 bg-primary/5 dark:bg-primary/10 rounded-lg border border-primary/20">
                        <span class="material-symbols-outlined text-primary">image</span>
                        <span id="backFileName" class="text-sm font-medium text-[#111418] dark:text-white"></span>
                    </div>
                    @error('back_template')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Info -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center text-xs sm:text-sm text-[#617589] dark:text-slate-400 gap-2 mb-8">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        Supported formats: PNG, JPG (Max 5MB)
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">aspect_ratio</span>
                        Recommended size: 1011x638px (CR80)
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex justify-end items-center gap-4 pt-6 border-t border-gray-200 dark:border-[#2a3b4d]">
                    <a href="{{ route('templates.index') }}" class="px-6 py-2.5 rounded-lg text-[#617589] dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-[#23303d] transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-2.5 rounded-lg bg-primary hover:bg-blue-600 text-white font-bold shadow-lg shadow-primary/30 transition-all flex items-center gap-2">
                        Upload Templates
                        <span class="material-symbols-outlined text-[20px]">upload</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Template Status -->
        <div class="bg-white dark:bg-[#1a2632] rounded-2xl shadow-sm border border-[#e5e7eb] dark:border-[#2a3b4d] overflow-hidden">
            <div class="px-6 py-4 border-b border-[#e5e7eb] dark:border-[#2a3b4d] bg-gray-50/50 dark:bg-[#23303d]/30">
                <h3 class="font-semibold text-[#111418] dark:text-white">Current Template Status</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-6 items-start p-4 rounded-xl border {{ $template->front_template && $template->back_template ? 'border-green-200 bg-green-50/50 dark:border-green-800 dark:bg-green-900/10' : 'border-yellow-200 bg-yellow-50/50 dark:border-yellow-800 dark:bg-yellow-900/10' }}">
                    <!-- Preview Grid -->
                    <div class="grid grid-cols-2 gap-3 w-full md:w-auto shrink-0">
                        <div class="w-32 aspect-[1.58] rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#23303d] flex items-center justify-center">
                            @if($template->front_template)
                            <img src="{{ Storage::url($template->front_template) }}" alt="Front" class="w-full h-full object-cover">
                            @else
                            <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600">image</span>
                            @endif
                        </div>
                        <div class="w-32 aspect-[1.58] rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#23303d] flex items-center justify-center">
                            @if($template->back_template)
                            <img src="{{ Storage::url($template->back_template) }}" alt="Back" class="w-full h-full object-cover">
                            @else
                            <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600">image</span>
                            @endif
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 w-full">
                        <div class="flex items-center gap-2 mb-2">
                            @if($template->academicYear)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                {{ $template->academicYear->name }}
                            </span>
                            @endif
                            @if($template->status === 'active')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 border border-green-200 dark:border-green-800">
                                <span class="size-1.5 rounded-full bg-green-500"></span>
                                Active
                            </span>
                            @elseif($template->status === 'incomplete')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800">
                                <span class="size-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                                Incomplete
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-400 border border-gray-200 dark:border-gray-800">
                                <span class="size-1.5 rounded-full bg-gray-500"></span>
                                Archived
                            </span>
                            @endif
                        </div>
                        <p class="text-[#111418] dark:text-white font-bold text-lg">{{ $template->name }}</p>
                        <p class="text-[#617589] dark:text-slate-400 text-sm">Last updated: {{ $template->updated_at->diffForHumans() }}</p>

                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700/50">
                            <div class="flex flex-wrap gap-4 text-sm">
                                <div class="flex items-center gap-2 {{ $template->front_template ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    <span class="material-symbols-outlined text-[18px]">{{ $template->front_template ? 'check_circle' : 'radio_button_unchecked' }}</span>
                                    <span>Front Template</span>
                                </div>
                                <div class="flex items-center gap-2 {{ $template->back_template ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    <span class="material-symbols-outlined text-[18px]">{{ $template->back_template ? 'check_circle' : 'radio_button_unchecked' }}</span>
                                    <span>Back Template</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            function showPreview(input, previewId, dropZoneId) {
                const preview = document.getElementById(previewId);
                const fileName = document.getElementById(previewId.replace('Preview', 'FileName'));
                const dropZone = document.getElementById(dropZoneId);

                if (input.files && input.files[0]) {
                    preview.classList.remove('hidden');
                    fileName.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
                    dropZone.classList.add('border-primary', 'bg-primary/5');
                    dropZone.classList.remove('border-gray-300');
                }
            }

            // Drag and drop handling
            ['frontDropZone', 'backDropZone'].forEach(id => {
                const dropZone = document.getElementById(id);
                const input = id === 'frontDropZone' ? document.getElementById('front_template') : document.getElementById('back_template');

                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.add('border-primary', 'bg-primary/5');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        if (!input.files.length) {
                            dropZone.classList.remove('border-primary', 'bg-primary/5');
                        }
                    });
                });

                dropZone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    if (files.length) {
                        input.files = files;
                        const previewId = id === 'frontDropZone' ? 'frontPreview' : 'backPreview';
                        showPreview(input, previewId, id);
                    }
                });
            });

        </script>
    </x-slot>
</x-admin-layout>
