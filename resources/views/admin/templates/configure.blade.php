@php
use Illuminate\Support\Facades\Storage;
use App\Models\Student;

$fieldsJson = json_encode($enabledFields ?: new stdClass());
$availableJson = json_encode($availableFields ?: []);
@endphp
<x-admin-layout>
    <x-slot name="title">Configure Template Fields</x-slot>

    <x-slot name="styles">
        <style>
            .bg-grid-pattern {
                background-image:
                    linear-gradient(45deg, #e2e8f0 25%, transparent 25%),
                    linear-gradient(-45deg, #e2e8f0 25%, transparent 25%),
                    linear-gradient(45deg, transparent 75%, #e2e8f0 75%),
                    linear-gradient(-45deg, transparent 75%, #e2e8f0 75%);
                background-size: 20px 20px;
                background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            }

            .dark .bg-grid-pattern {
                background-image:
                    linear-gradient(45deg, #1e293b 25%, transparent 25%),
                    linear-gradient(-45deg, #1e293b 25%, transparent 25%),
                    linear-gradient(45deg, transparent 75%, #1e293b 75%),
                    linear-gradient(-45deg, transparent 75%, #1e293b 75%);
            }

            .field-item {
                cursor: move;
                user-select: none;
            }

            .field-item:hover {
                z-index: 50;
            }

            [x-cloak] {
                display: none !important;
            }

        </style>
    </x-slot>

    <div class="max-w-[1400px] mx-auto">
        <!-- Flash Message -->
        @if (session('success'))
        <div class="mb-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <span class="material-symbols-outlined icon-filled text-emerald-500">check_circle</span>
            {{ session('success') }}
        </div>
        @endif

        <!-- Breadcrumb -->
        <div class="flex flex-wrap gap-2 mb-4">
            <a class="text-[#617589] dark:text-gray-400 text-sm font-medium hover:text-primary" href="{{ route('dashboard') }}">Dashboard</a>
            <span class="text-[#617589] dark:text-gray-500 text-sm font-medium">/</span>
            <a class="text-[#617589] dark:text-gray-400 text-sm font-medium hover:text-primary" href="{{ route('templates.index') }}">Templates</a>
            <span class="text-[#617589] dark:text-gray-500 text-sm font-medium">/</span>
            <span class="text-[#111418] dark:text-white text-sm font-medium">Configure Fields</span>
        </div>

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[#111418] dark:text-white tracking-tight text-3xl font-bold leading-tight">Configure Template Fields</h1>
                <p class="text-[#617589] dark:text-gray-400 text-sm font-normal mt-1">
                    Adjust field positions and styles for <strong class="text-[#111418] dark:text-white">{{ $template->name }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('templates.reset-settings', $template) }}" method="POST" class="inline" onsubmit="return confirm('Reset semua pengaturan ke default?')">
                    @csrf
                    <button type="submit" class="h-10 flex items-center justify-center gap-2 px-4 bg-white dark:bg-gray-800 border border-[#e5e7eb] dark:border-gray-700 rounded-lg text-sm font-medium text-[#111418] dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">restart_alt</span>
                        Reset
                    </button>
                </form>
                <button type="submit" form="configForm" class="h-10 flex items-center justify-center gap-2 px-4 bg-primary hover:bg-blue-600 rounded-lg text-sm font-medium text-white transition-colors shadow-sm shadow-blue-500/20">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    Save
                </button>
            </div>
        </div>

        <form id="configForm" action="{{ route('templates.configure.save', $template) }}" method="POST">
            @csrf

            <div class="flex flex-col lg:flex-row gap-6 items-start" x-data="{
                    fields: {{ $fieldsJson }},
                    selectedField: null,
                    fieldToAdd: '',
                    availableFields: {{ $availableJson }},
                    
                    selectField(name) { this.selectedField = name; },
                    
                    hasField(name) { return this.fields.hasOwnProperty(name); },
                    
                    addField() {
                        if (!this.fieldToAdd || this.hasField(this.fieldToAdd)) return;
                        let info = null;
                        for (let f of this.availableFields) {
                            if (f.column === this.fieldToAdd) { info = f; break; }
                        }
                        if (!info) return;
                        
                        let maxY = 80;
                        for (let k in this.fields) { if (this.fields[k].y > maxY) maxY = this.fields[k].y; }
                        
                        this.fields[this.fieldToAdd] = {
                            label: info.label,
                            type: info.type,
                            x: info.type === 'image' ? 30 : 180,
                            y: maxY + 35,
                            width: 120,
                            height: 160,
                            font_family: 'Lexend',
                            font_size: 16,
                            font_color: '#111418',
                            font_weight: 'normal'
                        };
                        this.selectedField = this.fieldToAdd;
                        this.fieldToAdd = '';
                    },
                    
                    removeField(name) {
                        delete this.fields[name];
                        if (this.selectedField === name) this.selectedField = null;
                    },
                    
                    getSample(col) {
                        const samples = {'nim':'210510001','name':'Amanda Pratiwi','email':'amanda@univ.edu','class':'IF-A','major':'Teknik Informatika','prodi':'Teknik Informatika','tempat_lahir':'Bandung','tanggal_lahir':'15 Mei 2003','angkatan':'2021','jenis_kelamin':'Perempuan','alamat':'Jl. Contoh 123'};
                        return samples[col] || col;
                    }
                 }">

                <!-- Preview Panel -->
                <div class="flex-1 w-full bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-[#e5e7eb] dark:border-gray-800 flex flex-col overflow-hidden min-h-[500px]">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-[#e5e7eb] dark:border-gray-800">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-[#111418] dark:text-white">Preview</span>
                            @if($template->front_template)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="size-1.5 rounded-full bg-green-500"></span>
                                Template Loaded
                            </span>
                            @endif
                        </div>
                    </div>

                    @php
                    // Calculate scale to fit preview in container (max 600px wide)
                    $maxPreviewWidth = 600;
                    $previewScale = min(1, $maxPreviewWidth / $templateWidth);
                    $displayWidth = $templateWidth * $previewScale;
                    $displayHeight = $templateHeight * $previewScale;
                    @endphp
                    <div class="relative flex-1 bg-grid-pattern overflow-hidden flex items-center justify-center p-4">
                        <!-- Wrapper with scaled dimensions to prevent overflow -->
                        <div style="width: {{ $displayWidth }}px; height: {{ $displayHeight }}px;" class="relative">
                            <div class="absolute bg-white shadow-2xl rounded-lg overflow-hidden ring-1 ring-black/5" style="width: {{ $templateWidth }}px; height: {{ $templateHeight }}px; transform: scale({{ $previewScale }}); transform-origin: top left;">
                                @if($template->front_template)
                                <img src="{{ Storage::url($template->front_template) }}" alt="Card Template" class="absolute inset-0 w-full h-full object-contain pointer-events-none">
                                @else
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-6xl text-blue-300">credit_card</span>
                                </div>
                                @endif

                                <!-- Fields Preview -->
                                <template x-for="(field, name) in fields" :key="name">
                                    <div class="absolute field-item border-2 border-dashed flex items-center justify-center px-2" :class="selectedField === name ? 'border-primary bg-primary/5' : 'border-transparent hover:border-gray-400'" :style="'left:' + field.x + 'px; top:' + field.y + 'px;' + (field.type === 'image' ? 'width:' + field.width + 'px; height:' + field.height + 'px;' : '')" @click="selectField(name)">
                                        <template x-if="field.type === 'image'">
                                            <div class="w-full h-full flex items-center justify-center bg-black/10 border border-gray-300">
                                                <span class="material-symbols-outlined text-gray-500 text-3xl">person</span>
                                            </div>
                                        </template>
                                        <template x-if="field.type !== 'image'">
                                            <p class="whitespace-nowrap" :style="'font-family:' + field.font_family + ',sans-serif; font-size:' + field.font_size + 'px; color:' + field.font_color + '; font-weight:' + field.font_weight" x-text="getSample(name)"></p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Properties Panel (Right Side) -->
                <div class="w-full lg:w-[320px] flex flex-col gap-5 shrink-0">
                    <!-- Add Field -->
                    <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-[#e5e7eb] dark:border-gray-800 p-5">
                        <h3 class="text-[#111418] dark:text-white text-lg font-bold mb-4">Add Field</h3>
                        <div class="flex gap-2">
                            <select x-model="fieldToAdd" class="flex-1 h-10 bg-[#f6f7f8] dark:bg-gray-800 border-none rounded-lg px-3 text-sm">
                                <option value="">-- Pilih Field --</option>
                                <template x-for="f in availableFields" :key="f.column">
                                    <option :value="f.column" :disabled="hasField(f.column)" x-text="f.label + ' (' + f.type + ')'"></option>
                                </template>
                            </select>
                            <button type="button" @click="addField()" class="h-10 px-4 bg-primary hover:bg-blue-600 rounded-lg text-white text-sm font-medium">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                            </button>
                        </div>
                    </div>

                    <!-- Active Fields -->
                    <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-[#e5e7eb] dark:border-gray-800 p-5">
                        <h3 class="text-[#111418] dark:text-white text-lg font-bold mb-4">Active Fields</h3>
                        <div class="flex flex-col gap-2 max-h-48 overflow-y-auto">
                            <template x-if="Object.keys(fields).length === 0">
                                <p class="text-gray-500 text-sm text-center py-4">Belum ada field</p>
                            </template>
                            <template x-for="(field, name) in fields" :key="name">
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer" :class="selectedField === name ? 'bg-primary/10 ring-1 ring-primary/30' : ''" @click="selectField(name)">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[18px] text-gray-500" x-text="field.type === 'image' ? 'image' : 'text_fields'"></span>
                                        <span class="text-sm font-medium" x-text="field.label"></span>
                                    </div>
                                    <button type="button" @click.stop="removeField(name)" class="p-1 text-red-500 hover:bg-red-50 rounded">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Field Properties -->
                    <div class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-[#e5e7eb] dark:border-gray-800" x-show="selectedField && fields[selectedField]" x-cloak>
                        <div class="px-5 py-3 border-b border-[#e5e7eb] dark:border-gray-800">
                            <h3 class="text-base font-bold" x-text="(fields[selectedField]?.label || '') + ' Properties'"></h3>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <!-- Position -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">X (px)</label>
                                    <input type="number" x-model.number="fields[selectedField].x" class="w-full bg-[#f6f7f8] dark:bg-gray-800 rounded px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Y (px)</label>
                                    <input type="number" x-model.number="fields[selectedField].y" class="w-full bg-[#f6f7f8] dark:bg-gray-800 rounded px-3 py-2 text-sm">
                                </div>
                            </div>

                            <!-- Image Dimensions -->
                            <template x-if="fields[selectedField]?.type === 'image'">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs text-gray-500 block mb-1">Width</label>
                                        <input type="number" x-model.number="fields[selectedField].width" class="w-full bg-[#f6f7f8] dark:bg-gray-800 rounded px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 block mb-1">Height</label>
                                        <input type="number" x-model.number="fields[selectedField].height" class="w-full bg-[#f6f7f8] dark:bg-gray-800 rounded px-3 py-2 text-sm">
                                    </div>
                                </div>
                            </template>

                            <!-- Text Typography -->
                            <template x-if="fields[selectedField]?.type !== 'image'">
                                <div class="flex flex-col gap-3">
                                    <div>
                                        <label class="text-xs text-gray-500 block mb-1">Font</label>
                                        <select x-model="fields[selectedField].font_family" class="w-full bg-[#f6f7f8] dark:bg-gray-800 rounded px-3 py-2 text-sm">
                                            <option value="Lexend">Lexend</option>
                                            <option value="Roboto">Roboto</option>
                                            <option value="Arial">Arial</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs text-gray-500 block mb-1">Size (px)</label>
                                            <input type="number" x-model.number="fields[selectedField].font_size" class="w-full bg-[#f6f7f8] dark:bg-gray-800 rounded px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 block mb-1">Color</label>
                                            <input type="color" x-model="fields[selectedField].font_color" class="w-full h-9 rounded cursor-pointer">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 block mb-1">Weight</label>
                                        <select x-model="fields[selectedField].font_weight" class="w-full bg-[#f6f7f8] dark:bg-gray-800 rounded px-3 py-2 text-sm">
                                            <option value="normal">Normal</option>
                                            <option value="bold">Bold</option>
                                        </select>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tip -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4 flex gap-3">
                        <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                        <p class="text-xs text-[#617589] dark:text-gray-400">
                            Pilih field dari dropdown untuk menambahkan ke template. Klik field untuk edit properties.
                        </p>
                    </div>
                </div>

                <!-- Hidden inputs -->
                <template x-for="(field, name) in fields" :key="name + '_hidden'">
                    <div>
                        <input type="hidden" :name="'settings[' + name + '][enabled]'" value="1">
                        <input type="hidden" :name="'settings[' + name + '][label]'" :value="field.label">
                        <input type="hidden" :name="'settings[' + name + '][type]'" :value="field.type">
                        <input type="hidden" :name="'settings[' + name + '][x]'" :value="field.x">
                        <input type="hidden" :name="'settings[' + name + '][y]'" :value="field.y">
                        <input type="hidden" :name="'settings[' + name + '][width]'" :value="field.width">
                        <input type="hidden" :name="'settings[' + name + '][height]'" :value="field.height">
                        <input type="hidden" :name="'settings[' + name + '][font_family]'" :value="field.font_family">
                        <input type="hidden" :name="'settings[' + name + '][font_size]'" :value="field.font_size">
                        <input type="hidden" :name="'settings[' + name + '][font_color]'" :value="field.font_color">
                        <input type="hidden" :name="'settings[' + name + '][font_weight]'" :value="field.font_weight">
                    </div>
                </template>
            </div>
        </form>
    </div>
</x-admin-layout>
