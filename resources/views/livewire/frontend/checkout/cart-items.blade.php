<div class="border-dashed border-bottom border-translucent mt-4">
    <div class="ms-n2">
        @forelse($orderItems as $item)
            <div class="row align-items-center mb-2 g-3">
                <div class="col-8 col-md-7 col-lg-8">
                    <div class="d-flex align-items-center">
                        <img class="me-2 ms-1"
                             src="{{ $item->getPhoto() ? $item->getPhoto()->getImageUrl(50,50) : asset('assets/img/products/default.png') }}"
                             width="40" alt="{{ $item->name }}" />
                        <h6 class="fw-semibold text-body-highlight lh-base">
                            {{ Str::limit($item->name, 50) }}
                        </h6>
                    </div>
                </div>
                <div class="col-2 col-md-3 col-lg-2">
                    <h6 class="fs-10 mb-0">x{{ $item->quantite }}</h6>
                </div>
                <div class="col-2 ps-0">
                    <h5 class="mb-0 fw-semibold text-end">
                        {{ number_format($item->total, 0) }} FCFA
                    </h5>
                </div>
            </div>
        @empty
            <div class="row">
                <div class="col-12 text-muted text-center py-4">
                    Aucun produit commandé
                </div>
            </div>
        @endforelse
    </div>
</div>
