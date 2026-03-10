<div class="row gy-6">
    @forelse($livraisons as $livraison)
        <div class="col-12 col-md-6">
            <div class="d-flex flex-wrap align-items-center mb-3">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="radio" name="shippingRadio" id="livraison_{{ $livraison->id }}"
                        value="{{ $livraison->id }}" wire:model.live="deliveryMethodId" /> <!-- ✅ SEUL CHANGEMENT -->
                    <label class="form-check-label fs-8 text-body" for="livraison_{{ $livraison->id }}">
                        {{ $livraison->method_name }}
                    </label>
                </div>
                <span class="d-inline-block text-body-emphasis fw-bold ms-2">
                    {{ $livraison->price == 0 ? 'Gratuit' : number_format($livraison->price, 0) . ' FCFA' }}
                </span>
                @if($livraison->id == 4)
                    <span class="badge badge-phoenix badge-phoenix-warning ms-2">Popular</span>
                @endif
            </div>
            <div class="ps-4">
                <h6 class="text-body-tertiary mb-2">
                    Est. delivery: Jun 21 – Jul 20
                </h6>
                <h6 class="text-info lh-base mb-0">
                    {{ $livraison->description }}
                </h6>
            </div>
        </div>
    @empty
        <div class="col-12 text-danger">Aucune option de livraison disponible.</div>
    @endforelse
</div>
