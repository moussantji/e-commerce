<div class="search-box navbar-top-search-box d-none d-lg-block position-relative w-100" >

    {{-- INPUT --}}
    <form class="position-relative" wire:submit.prevent="search">
        <input class="form-control search-input fuzzy-search rounded-pill form-control-sm pe-5" type="search"
            wire:model.live.debounce.150ms="search" placeholder="produits, catégories, marques, tags..." />
        <span class="fas fa-search search-box-icon"></span>
    </form>

    {{-- CLOSE --}}
    @if($search)
        <div class="btn-close position-absolute end-0 top-50 translate-middle cursor-pointer shadow-none p-1 me-2"
            wire:click="clear">
            <button class="btn btn-link p-0"></button>
        </div>
    @endif

    {{-- DROPDOWN --}}
    @if(!empty($suggestions))
        <div class="suggestions-dropdown position-absolute top-100 start-0 w-100 mt-1 shadow-lg rounded-3 overflow-hidden"
            style="max-height: 1 8rem; z-index: 1060; border: 1px solid #e9ecef;">

            <div class="dropdown-scroll p-0" style="height: 28rem; overflow-y: auto;">
                @foreach($suggestions as $suggestion)
                    <a href="{{ $suggestion['url'] }}"
                        class="suggestion-item p-3 border-bottom hover-link d-flex align-items-center text-decoration-none">

                        <div class="flex-grow-1 pe-3">
                            <div class="title fw-semibold text-dark mb-1">{{ $suggestion['title'] }}</div>
                            <div class="type text-muted small">{{ $suggestion['type'] }}</div>
                        </div>

                        @if(isset($suggestion['count']) && $suggestion['count'] > 0)
                            <span class="badge bg-primary fs-10">{{ $suggestion['count'] }} produits</span>
                        @endif

                        <i class="fas fa-chevron-right text-muted ms-2"></i>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <style>
        .suggestions-dropdown {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .suggestion-item {
            transition: all 0.2s ease;
        }

        .hover-link:hover {
            background: #f8f9fa !important;
            color: #0d6efd !important;
        }

        .hover-link:hover .title {
            color: #0d6efd !important;
        }

        .dropdown-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-scroll::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 3px;
        }
    </style>

</div>
