@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
    <span class="flex items-center justify-center px-3 h-8 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-l-lg cursor-not-allowed dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-gray-600">
        Previous
    </span>
    @else
    <button wire:click="previousPage" wire:loading.attr="disabled" class="flex items-center justify-center px-3 h-8 text-sm font-medium text-[#617589] bg-white border border-gray-200 rounded-l-lg hover:bg-gray-50 hover:text-[#111418] dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white transition-colors">
        Previous
    </button>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
    {{-- "Three Dots" Separator --}}
    @if (is_string($element))
    <span class="flex items-center justify-center px-3 h-8 text-sm text-gray-400 dark:text-gray-600">{{ $element }}</span>
    @endif

    {{-- Array Of Links --}}
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <span class="flex items-center justify-center px-3 h-8 text-sm font-medium text-white bg-primary border border-primary rounded-lg">
        {{ $page }}
    </span>
    @else
    <button wire:click="gotoPage({{ $page }})" class="flex items-center justify-center px-3 h-8 text-sm font-medium text-[#617589] bg-white border border-gray-200 hover:bg-gray-50 hover:text-[#111418] dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white transition-colors">
        {{ $page }}
    </button>
    @endif
    @endforeach
    @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
    <button wire:click="nextPage" wire:loading.attr="disabled" class="flex items-center justify-center px-3 h-8 text-sm font-medium text-[#617589] bg-white border border-gray-200 rounded-r-lg hover:bg-gray-50 hover:text-[#111418] dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-slate-400 dark:hover:bg-[#2a3b4d] dark:hover:text-white transition-colors">
        Next
    </button>
    @else
    <span class="flex items-center justify-center px-3 h-8 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-r-lg cursor-not-allowed dark:bg-[#1a2632] dark:border-[#2a3b4d] dark:text-gray-600">
        Next
    </span>
    @endif
</nav>
@endif
