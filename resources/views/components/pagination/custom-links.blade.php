<?php
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

/** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator */
/** @var array $elements */
/** @var \Illuminate\Pagination\UrlWindow $window */
/** @var int $currentPage */
/** @var int $perPage */
/** @var int $total */

?>

@if ($paginator->hasPages())
    <div class="d-flex justify-content-center">
        <nav>
            <ul class="pagination mb-0">

                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><span class="fas fa-chevron-left"></span></span>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="previousPage" wire:loading.attr="disabled">
                            <span class="fas fa-chevron-left"></span>
                        </button>
                    </li>
                @endif

                {{-- Pages DYNAMIQUES --}}
                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    $window = 2; // ±2 pages autour de la courante
                @endphp

                {{-- 1ère page toujours --}}
                @if ($current > 1)
                    <li class="page-item">
                        <button class="page-link" wire:click="gotoPage(1)">1</button>
                    </li>
                    @if ($current > 3)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                @else
                    <li class="page-item active"><span class="page-link">1</span></li>
                @endif

                {{-- Pages autour de la courante --}}
                @for ($page = max(2, $current - $window); $page <= min($last - 1, $current + $window); $page++)
                    @if ($page == $current)
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <button class="page-link"
                                wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                        </li>
                    @endif
                @endfor

                {{-- Dernière page toujours --}}
                @if ($current < $last)
                    @if ($current < $last - 2)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                    <li class="page-item {{ $current == $last ? 'active' : '' }}">
                        @if ($current == $last)
                            <span class="page-link">{{ $last }}</span>
                        @else
                            <button class="page-link"
                                wire:click="gotoPage({{ $last }})">{{ $last }}</button>
                        @endif
                    </li>
                @endif

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button class="page-link" wire:click="nextPage" wire:loading.attr="disabled">
                            <span class="fas fa-chevron-right"></span>
                        </button>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link"><span class="fas fa-chevron-right"></span></span>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
@endif
