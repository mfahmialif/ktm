@php
$menuItems = [
['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
['route' => 'students.*', 'icon' => 'school', 'label' => 'Students', 'href' => route('students.index')],
['route' => 'academic-years.*', 'icon' => 'calendar_month', 'label' => 'Year', 'href' => route('academic-years.index')],
['route' => 'templates.*', 'icon' => 'description', 'label' => 'Template', 'href' => route('templates.index')],
['route' => 'ktm-generator.*', 'icon' => 'id_card', 'label' => 'Generate', 'href' => route('ktm-generator.index')],
['route' => 'download-jobs.*', 'icon' => 'history', 'label' => 'History', 'href' => route('download-jobs.index')],
];
@endphp

<div id="mobile-menu" class="md:hidden border-t border-[#e5e7eb] dark:border-[#2a3b4d] hidden">
    <nav class="flex flex-col p-2 gap-1">
        @foreach($menuItems as $item)
        @php
        $isActive = request()->routeIs($item['route']);
        $href = $item['href'] ?? route($item['route']);
        @endphp
        <a class="flex items-center gap-3 px-3 py-2 rounded-lg {{ $isActive ? 'bg-primary/10 text-primary' : 'text-[#617589] dark:text-slate-400' }}" href="{{ $href }}">
            <span class="material-symbols-outlined {{ $isActive ? 'icon-filled' : '' }}">{{ $item['icon'] }}</span>
            <span class="text-sm font-{{ $isActive ? 'semibold' : 'medium' }}">{{ $item['label'] }}</span>
        </a>
        @endforeach
    </nav>
</div>
