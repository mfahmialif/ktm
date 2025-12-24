@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex flex-wrap items-center gap-1.5">
    {{-- First Page Link --}}
    @if ($paginator->onFirstPage())
    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-gray-600" title="First">
        <span class="material-symbols-outlined text-[16px]">first_page</span>
    </span>
    @else
    <button wire:click="gotoPage(1)" wire:loading.attr="disabled" class="flex items-center justify-center w-8 h-8 text-sm font-medium text-[#617589] bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#111418] dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white transition-colors" title="First">
        <span class="material-symbols-outlined text-[16px]">first_page</span>
    </button>
    @endif

    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-gray-600" title="Previous">
        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
    </span>
    @else
    <button wire:click="previousPage" wire:loading.attr="disabled" class="flex items-center justify-center w-8 h-8 text-sm font-medium text-[#617589] bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#111418] dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white transition-colors" title="Previous">
        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
    </button>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
    {{-- "Three Dots" Separator --}}
    @if (is_string($element))
    <span class="flex items-center justify-center w-8 h-8 text-sm text-gray-400 dark:text-gray-600">{{ $element }}</span>
    @endif

    {{-- Array Of Links --}}
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <span class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-primary border border-primary rounded-lg">
        {{ $page }}
    </span>
    @else
    <button wire:click="gotoPage({{ $page }})" class="flex items-center justify-center w-8 h-8 text-sm font-medium text-[#617589] bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#111418] dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white transition-colors">
        {{ $page }}
    </button>
    @endif
    @endforeach
    @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
    <button wire:click="nextPage" wire:loading.attr="disabled" class="flex items-center justify-center w-8 h-8 text-sm font-medium text-[#617589] bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#111418] dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white transition-colors" title="Next">
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    </button>
    @else
    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-gray-600" title="Next">
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    </span>
    @endif

    {{-- Last Page Link --}}
    @if ($paginator->hasMorePages())
    <button wire:click="gotoPage({{ $paginator->lastPage() }})" wire:loading.attr="disabled" class="flex items-center justify-center w-8 h-8 text-sm font-medium text-[#617589] bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#111418] dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white transition-colors" title="Last">
        <span class="material-symbols-outlined text-[16px]">last_page</span>
    </button>
    @else
    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-gray-600" title="Last">
        <span class="material-symbols-outlined text-[16px]">last_page</span>
    </span>
    @endif

</nav>
@endif
