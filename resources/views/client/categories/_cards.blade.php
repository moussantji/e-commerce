{{-- resources/views/client/categories/_card.blade.php --}}
<div class="col-6 col-sm-4 col-md-3 col-lg-2 hover-actions-trigger btn-reveal-trigger position-relative js-hover-actions">
    <div class="border border-translucent d-flex flex-center rounded-3 mb-3 p-4"
     style="height:180px; width: 100%;">
    @if ($item->getPhoto())
        <img style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.25rem; display: block;" 
             src="{{ $item->getPhoto()->getImageUrl(700,700) }}"
             alt="{{ $item->name }}" />
    @else
        <div class="bg-light d-flex flex-center rounded-2" style="width:100%; height:100%;">
            <span class="fas fa-image fs-3 text-body-tertiary"></span>
        </div>
    @endif
</div>
    <h5 class="mb-2">
        <a href="{{ isset($parentSlug) ? route('products', ['category' => $item->slug]) : route('categories.show', $item->slug) }}"
           class="text-decoration-none {{ request()->routeIs('categories.show') && request('category') == $item->slug ? 'text-primary fw-bold' : '' }}">
            {{ $item->name }}
        </a>
    </h5>

    <div class="mb-1 fs-9">
        @php $rating = $item->avg_rating ?? 0 @endphp
        @for ($i = 1; $i <= 5; $i++)
            @if($i <= floor($rating))
                <span class="fas fa-star text-warning"></span>
            @elseif($i == floor($rating) + 1 && $rating >= $i - 0.5)
                <span class="fas fa-star-half-alt text-warning"></span>
            @else
                <span class="far fa-star text-warning-light"></span>
            @endif
        @endfor
        <span class="ms-1 text-muted fs-10">({{ number_format($rating, 1) }})</span>
    </div>

    <p class="text-body-quaternary fs-9 mb-2 fw-semibold">
        ({{ $item->avis_count ?? 0 }} {{ Str::plural('avis', $item->avis_count ?? 0) }})
    </p>

    <a class="btn btn-link p-0" href="{{ isset($parentSlug) ? route('categories.show', [$parentSlug, $item->slug]) : route('categories.show', $item->slug) }}">
        Voir catégorie<span class="fas fa-chevron-right ms-1 fs-10"></span>
    </a>

    {{-- ✅ TON MENU HOVER IDENTIQUE --}}
    <div class="hover-actions top-0 end-0 mt-2 me-3 js-hover-actions-item">
        <div class="btn-reveal-trigger">
            <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal lh-1 bg-body-highlight rounded-1" type="button" data-bs-toggle="dropdown">
                <span class="fas fa-ellipsis-h fs-9"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end py-2">
                <a class="dropdown-item" href="{{ isset($parentSlug) ? route('categories.show', [$parentSlug, $item->slug]) : route('categories.show', $item->slug) }}">Voir produits</a>
                @if ($item->children->count())
                    <a class="dropdown-item" href="{{ route('categories.show', $item->slug) }}">Sous-catégories</a>
                @endif
            </div>
        </div>
    </div>
</div>
