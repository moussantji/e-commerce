<div>
    <div class="border-dashed border-bottom border-translucent mt-4">
        <div class="d-flex justify-content-between mb-2">
            <h5 class="text-body fw-semibold">Items subtotal:</h5>
            <h5 class="text-body fw-semibold">
                {{ number_format($itemsSubtotal, 0) }} FCFA
            </h5>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <h5 class="text-body fw-semibold">Discount:</h5>
            <h5 class="text-danger fw-semibold">
                -{{ number_format($discount, 0) }} FCFA
            </h5>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <h5 class="text-body fw-semibold">Sous-total:</h5>
            <h5 class="text-body fw-semibold">
                {{ number_format($itemsSubtotal + $tax - $discount, 0) }} FCFA
            </h5>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <h5 class="text-body fw-semibold">Frais de livraison:</h5>
            <h5 class="text-body fw-semibold">
                {{ number_format($shippingCost, 0) }} FCFA
            </h5>
        </div>
    </div>
    <div class="d-flex justify-content-between border-dashed-y pt-3">
        <h4 class="mb-0">Total :</h4>
        <h4 class="mb-0 text-primary">{{ number_format($finalTotal, 0) }} FCFA</h4>
    </div>

</div>
