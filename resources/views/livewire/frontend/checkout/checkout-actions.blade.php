<div class="row g-2 mb-5 mb-lg-0">
    <div class="col-md-8 col-lg-9 d-grid">
        <button class="btn btn-primary w-100" style="font-size: 0.9rem;" wire:click="payNow"
            wire:loading.attr="disabled">
            Payer {{ number_format($finalTotal, 0) }} FCFA
        </button>
    </div>
    <div class="col-md-4 col-lg-3 d-grid">
        <button class="btn btn-phoenix-secondary text-nowrap w-100" style="font-size: 0.85rem;" wire:click="saveAndExit"
            wire:loading.attr="disabled">
            Sauvegarder & Quitter
        </button>
    </div>



</div>
