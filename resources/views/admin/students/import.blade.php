<x-admin-layout>
    <x-slot name="title">Import Students</x-slot>

    <div class="max-w-2xl">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-[#617589] dark:text-slate-400 font-medium mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-primary">Dashboard</a>
            <span class="mx-2">›</span>
            <a href="{{ route('students.index') }}" class="hover:text-primary">Students</a>
            <span class="mx-2">›</span>
            <span class="text-[#111418] dark:text-white">Import</span>
        </div>

        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-[#111418] dark:text-white">Import Students</h2>
            <p class="mt-1 text-sm text-[#617589] dark:text-slate-400">Upload an Excel file to bulk import student data.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm">
            <form action="{{ route('students.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="p-6 space-y-6">
                    <!-- Instructions -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">info</span>
                            Instructions
                        </h4>
                        <ul class="list-disc list-inside text-sm text-blue-700 dark:text-blue-400 space-y-1 ml-1">
                            <li>File must be in <strong>.xlsx</strong> or <strong>.csv</strong> format.</li>
                            <li>Required columns: <strong>nim, name, prodi, angkatan, jenis_kelamin (L/P)</strong>.</li>
                            <li>Other columns: email, class, major, tempat_lahir, tanggal_lahir, alamat.</li>
                            <li>Ensure NIMs are unique.</li>
                        </ul>
                        <div class="mt-4">
                            <a href="{{ route('students.template') }}" class="inline-flex items-center text-sm font-medium text-blue-700 dark:text-blue-400 hover:underline">
                                <span class="material-symbols-outlined text-[18px] mr-1">download</span>
                                Download Import Template
                            </a>
                        </div>
                    </div>

                    <!-- File Input -->
                    <div>
                        <label class="block text-sm font-medium text-[#111418] dark:text-gray-200 mb-2">Select File</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <span class="material-symbols-outlined text-3xl text-gray-400 mb-2">upload_file</span>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">XLSX or CSV (MAX. 5MB)</p>
                                </div>
                                <input id="file" name="file" type="file" class="hidden" accept=".xlsx, .xls, .csv" required />
                            </label>
                        </div>
                        @error('file')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 dark:bg-[#23303d] px-6 py-4 flex justify-between items-center rounded-b-xl">
                    <a href="{{ route('students.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors shadow-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">upload</span>
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file');
        const dropZone = fileInput.parentElement;
        const dropZoneText = dropZone.querySelector('p.mb-2');
        const originalText = dropZoneText.innerHTML;

        // Handle file selection
        fileInput.addEventListener('change', function(e) {
            updateFileName(this.files[0]);
        });

        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-primary', 'bg-blue-50', 'dark:bg-blue-900/10');
            dropZone.classList.remove('border-gray-300', 'dark:border-gray-600', 'bg-gray-50', 'dark:bg-gray-700');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-primary', 'bg-blue-50', 'dark:bg-blue-900/10');
            dropZone.classList.add('border-gray-300', 'dark:border-gray-600', 'bg-gray-50', 'dark:bg-gray-700');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            fileInput.files = files;
            updateFileName(files[0]);
        }

        function updateFileName(file) {
            if (file) {
                dropZoneText.innerHTML = `<span class="font-bold text-primary">${file.name}</span>`;
            } else {
                dropZoneText.innerHTML = originalText;
            }
        }
    });

</script>
