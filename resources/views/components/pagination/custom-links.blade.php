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
                        <span class="page-link">
                            <span class="fas fa-chevron-left"></span>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="previousPage" wire:loading.attr="disabled">
                            <span class="fas fa-chevron-left"></span>
                        </button>
                    </li>
                @endif

                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                @endphp

                {{-- Si 5 pages ou moins : tout afficher --}}
                @if ($last <= 5)

                    @for ($page = 1; $page <= $last; $page++)
                        <li class="page-item {{ $page == $current ? 'active' : '' }}">
                            @if ($page == $current)
                                <span class="page-link">{{ $page }}</span>
                            @else
                                <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">
                                    {{ $page }}
                                </button>
                            @endif
                        </li>
                    @endfor
                @else
                    {{-- Première page --}}
                    <li class="page-item {{ $current == 1 ? 'active' : '' }}">
                        @if ($current == 1)
                            <span class="page-link">1</span>
                        @else
                            <button type="button" class="page-link" wire:click="gotoPage(1)">
                                1
                            </button>
                        @endif
                    </li>

                    {{-- Points de suspension à gauche --}}
                    @if ($current > 4)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif

                    {{-- Pages autour de la page courante --}}
                    @for ($page = max(2, $current - 2); $page <= min($last - 1, $current + 2); $page++)
                        <li class="page-item {{ $page == $current ? 'active' : '' }}">
                            @if ($page == $current)
                                <span class="page-link">{{ $page }}</span>
                            @else
                                <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">
                                    {{ $page }}
                                </button>
                            @endif
                        </li>
                    @endfor

                    {{-- Points de suspension à droite --}}
                    @if ($current < $last - 3)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif

                    {{-- Dernière page --}}
                    <li class="page-item {{ $current == $last ? 'active' : '' }}">
                        @if ($current == $last)
                            <span class="page-link">{{ $last }}</span>
                        @else
                            <button type="button" class="page-link" wire:click="gotoPage({{ $last }})">
                                {{ $last }}
                            </button>
                        @endif
                    </li>

                @endif

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="nextPage" wire:loading.attr="disabled">
                            <span class="fas fa-chevron-right"></span>
                        </button>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <span class="fas fa-chevron-right"></span>
                        </span>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
@endif
