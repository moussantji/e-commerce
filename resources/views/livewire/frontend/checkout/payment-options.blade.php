<div class="col-12">
    <div class="row gx-lg-11">
        @forelse($paymentMethods as $method)
            <div class="col-md-auto">
                <div class="form-check">
                    <input class="form-check-input" id="payment_{{ $method->id }}" type="radio" name="paymentMethod"
                        value="{{ $method->id }}" wire:model.live="paymentMethodId" x-ref="payment_{{ $method->id }}" />

                    <label class="form-check-label fs-8 text-body text-nowrap d-flex gap-2" for="payment_{{ $method->id }}">
                        {{ $method->method_name }}
                    </label>
                </div>
            </div>
        @empty
            <div class="col-12 text-danger">Aucun moyen de paiement disponible.</div>
        @endforelse
    </div>
</div>
