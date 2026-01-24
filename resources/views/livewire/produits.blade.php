<div class="product-filter-container">
    {{-- Bouton mobile filter --}}
    <button class="btn btn-sm btn-phoenix-secondary text-body-tertiary mb-5 d-lg-none" data-phoenix-toggle="offcanvas"
        data-phoenix-target="#productFilterColumn">
        <span class="fa-solid fa-filter me-2"></span>Filter
    </button>

    <div class="row">
        {{-- SIDEBAR FILTRES - 12 sections EXACTES --}}
        <div class="col-lg-3 col-xxl-2 ps-2 ps-xxl-3">
            <div class="phoenix-offcanvas-filter bg-body scrollbar phoenix-offcanvas phoenix-offcanvas-fixed"
                id="productFilterColumn" style="top: 92px" data-breakpoint="lg">

                {{-- Header Filters --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Filters</h3>
                    <button class="btn d-lg-none p-0" data-phoenix-dismiss="offcanvas">
                        <span class="uil uil-times fs-8"></span>
                    </button>
                </div>

                {{-- 1. AVAILABILITY --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseAvailability">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Availability</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseAvailability">
                    <div class="mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="inStockInput" type="checkbox"
                                wire:model.live="filters.availability.in_stock" name="availability">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="inStockInput">
                                In stock
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="preBookInput" type="checkbox"
                                wire:model.live="filters.availability.pre_book" name="availability">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="preBookInput">
                                Pre-book
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="outOfStockInput" type="checkbox"
                                wire:model.live="filters.availability.out_of_stock" name="availability">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="outOfStockInput">
                                Out of stock
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 2. COLOR FAMILY --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseColorFamily">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Color family</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseColorFamily">
                    <div class="mb-2">
                        {{-- Noir --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckNoir" type="checkbox" value="Noir"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckNoir">
                                {{ __('noir') }}
                            </label>
                        </div>

                        {{-- Blanc --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckBlanc" type="checkbox" value="Blanc"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckBlanc">
                                {{ __('blanc') }}
                            </label>
                        </div>

                        {{-- Bleu --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckBleu" type="checkbox" value="Bleu"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckBleu">
                                {{ __('bleu') }}
                            </label>
                        </div>

                        {{-- Rouge --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckRouge" type="checkbox" value="Rouge"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckRouge">
                                {{ __('rouge') }}
                            </label>
                        </div>

                        {{-- Vert --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckVert" type="checkbox" value="Vert"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckVert">
                                {{ __('vert') }}
                            </label>
                        </div>

                        {{-- Jaune --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckJaune" type="checkbox" value="Jaune"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckJaune">
                                {{ __('jaune') }}
                            </label>
                        </div>

                        {{-- Gris --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckGris" type="checkbox" value="Gris"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckGris">
                                {{ __('gris') }}
                            </label>
                        </div>

                        {{-- Argent --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckArgent" type="checkbox" value="Argent"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckArgent">
                                {{ __('argent') }}
                            </label>
                        </div>

                        {{-- Or --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckOr" type="checkbox" value="Or"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckOr">
                                {{ __('or') }}
                            </label>
                        </div>

                        {{-- Rose --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckRose" type="checkbox" value="Rose"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckRose">
                                {{ __('rose') }}
                            </label>
                        </div>

                        {{-- Violet --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckViolet" type="checkbox" value="Violet"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckViolet">
                                {{ __('violet') }}
                            </label>
                        </div>

                        {{-- Marron --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckMarron" type="checkbox" value="Marron"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckMarron">
                                {{ __('marron') }}
                            </label>
                        </div>

                        {{-- Orange --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckOrange" type="checkbox" value="Orange"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckOrange">
                                {{ __('orange') }}
                            </label>
                        </div>

                        {{-- Cyan --}}
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flexCheckCyan" type="checkbox" value="Cyan"
                                wire:model.live="filters.couleur" name="couleur">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flexCheckCyan">
                                {{ __('cyan') }}
                            </label>
                        </div>
                    </div>
                </div>


                {{-- 3. BRANDS (5 exactes du HTML) --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseBrands">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">
                            {{ __('marques') }}
                            @if (count($filters['brands'] ?? []) > 0)
                                <span class="badge bg-primary fs-10 ms-1">{{ count($filters['brands'] ?? []) }}</span>
                            @endif
                        </div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseBrands">
                    <div class="mb-2">
                        @forelse($brands_list ?? [] as $brand_id => $brand_name)
                            <div class="form-check mb-0">
                                <input class="form-check-input mt-0" id="flexCheck{{ Str::slug($brand_name) }}"
                                    type="checkbox" value="{{ $brand_name }}" wire:model.live="filters.brands"
                                    name="brands">
                                <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                    for="flexCheck{{ Str::slug($brand_name) }}">
                                    {{ $brand_name }}
                                </label>
                            </div>
                        @empty
                            <div class="text-center py-2 text-body-tertiary fs-9">
                                {{ __('aucune_marque') }}
                            </div>
                        @endforelse
                    </div>
                </div>


                {{-- 4. PRICE RANGE --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapsePriceRange">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Price range</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapsePriceRange">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="input-group me-2">
                            <input class="form-control" type="number" wire:model.live="filters.min_price"
                                placeholder="Min">
                            <input class="form-control" type="number" wire:model.live="filters.max_price"
                                placeholder="Max">
                        </div>
                    </div>
                </div>

                {{-- 5. RATING (5 étoiles radio EXACT) --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseRating">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">
                            {{ __('note') }}
                            @if ($filters['rating'])
                                <span class="badge bg-primary fs-10 ms-1">{{ $filters['rating'] }}★</span>
                            @endif
                        </div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseRating">
                    {{-- 5 étoiles et plus --}}
                    <div class="d-flex align-items-center mb-1">
                        <input class="form-check-input me-3" id="flexRadio5" type="radio" name="flexRadio"
                            value="5" wire:model.live="filters.rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="fa fa-star text-warning fs-9 me-1"></span>
                        @endfor
                        <span class="ms-1 mb-0">& {{ __('et_plus') }}</span>
                    </div>

                    {{-- 4 étoiles et plus --}}
                    <div class="d-flex align-items-center mb-1">
                        <input class="form-check-input me-3" id="flexRadio4" type="radio" name="flexRadio"
                            value="4" wire:model.live="filters.rating">
                        @for ($i = 1; $i <= 4; $i++)
                            <span class="fa fa-star text-warning fs-9 me-1"></span>
                        @endfor
                        <span class="fa-regular fa-star text-warning-light fs-9 me-1"></span>
                        <span class="ms-1 mb-0">& {{ __('et_plus') }}</span>
                    </div>

                    {{-- 3 étoiles et plus --}}
                    <div class="d-flex align-items-center mb-1">
                        <input class="form-check-input me-3" id="flexRadio3" type="radio" name="flexRadio"
                            value="3" wire:model.live="filters.rating">
                        @for ($i = 1; $i <= 3; $i++)
                            <span class="fa fa-star text-warning fs-9 me-1"></span>
                        @endfor
                        @for ($i = 1; $i <= 2; $i++)
                            <span class="fa-regular fa-star text-warning-light fs-9 me-1"></span>
                        @endfor
                        <span class="ms-1 mb-0">& {{ __('et_plus') }}</span>
                    </div>

                    {{-- 2 étoiles et plus --}}
                    <div class="d-flex align-items-center mb-1">
                        <input class="form-check-input me-3" id="flexRadio2" type="radio" name="flexRadio"
                            value="2" wire:model.live="filters.rating">
                        @for ($i = 1; $i <= 2; $i++)
                            <span class="fa fa-star text-warning fs-9 me-1"></span>
                        @endfor
                        @for ($i = 1; $i <= 3; $i++)
                            <span class="fa-regular fa-star text-warning-light fs-9 me-1"></span>
                        @endfor
                        <span class="ms-1 mb-0">& {{ __('et_plus') }}</span>
                    </div>

                    {{-- 1 étoile et plus --}}
                    <div class="d-flex align-items-center mb-1">
                        <input class="form-check-input me-3" id="flexRadio1" type="radio" name="flexRadio"
                            value="1" wire:model.live="filters.rating">
                        <span class="fa fa-star text-warning fs-9 me-1"></span>
                        @for ($i = 1; $i <= 4; $i++)
                            <span class="fa-regular fa-star text-warning-light fs-9 me-1"></span>
                        @endfor
                        <span class="ms-1 mb-0">& {{ __('et_plus') }}</span>
                    </div>
                </div>


                {{-- 6. DISPLAY TYPE --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseDisplayType">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Display type</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseDisplayType">
                    <div class="mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="lcdInput" type="checkbox" value="LCD"
                                wire:model.live="filters.displayType" name="displayType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="lcdInput">LCD</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="ipsInput" type="checkbox" value="IPS"
                                wire:model.live="filters.displayType" name="displayType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="ipsInput">IPS</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="oledInput" type="checkbox" value="OLED"
                                wire:model.live="filters.displayType" name="displayType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="oledInput">OLED</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="amoledInput" type="checkbox" value="AMOLED"
                                wire:model.live="filters.displayType" name="displayType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="amoledInput">AMOLED</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="retinaInput" type="checkbox" value="Retina"
                                wire:model.live="filters.displayType" name="displayType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="retinaInput">Retina</label>
                        </div>
                    </div>
                </div>

                {{-- 7. CONDITION --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseCondition">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Condition</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseCondition">
                    <div class="mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="newInput" type="checkbox" value="New"
                                wire:model.live="filters.condition" name="condition">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="newInput">New</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="usedInput" type="checkbox" value="Used"
                                wire:model.live="filters.condition" name="condition">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="usedInput">Used</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="refurbishedInput" type="checkbox"
                                value="Refurbished" wire:model.live="filters.condition" name="condition">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="refurbishedInput">Refurbished</label>
                        </div>
                    </div>
                </div>

                {{-- 8. DELIVERY --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseDelivery">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Delivery</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseDelivery">
                    <div class="mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="freeShippingInput" type="checkbox"
                                value="Free Shipping" wire:model.live="filters.delivery" name="delivery">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="freeShippingInput">
                                Free Shipping
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="oneDayShippingInput" type="checkbox"
                                value="One-day Shipping" wire:model.live="filters.delivery" name="delivery">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="oneDayShippingInput">
                                One-day Shipping
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="codInput" type="checkbox"
                                value="Cash on Delivery" wire:model.live="filters.delivery" name="delivery">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="codInput">
                                Cash on Delivery
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 9. CAMPAIGN --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseCampaign">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Campaign</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseCampaign">
                    <div class="mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="summerSaleInput" type="checkbox"
                                value="Summer Sale" wire:model.live="filters.campaign" name="campaign">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="summerSaleInput">
                                Summer Sale
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="marchMadnessInput" type="checkbox"
                                value="March Madness" wire:model.live="filters.campaign" name="campaign">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="marchMadnessInput">
                                March Madness
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="flashSaleInput" type="checkbox"
                                value="Flash Sale" wire:model.live="filters.campaign" name="campaign">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="flashSaleInput">
                                Flash Sale
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="bogoBlastInput" type="checkbox"
                                value="BOGO Blast" wire:model.live="filters.campaign" name="campaign">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="bogoBlastInput">
                                BOGO Blast
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 10. WARRANTY --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse" href="#collapseWarranty">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Warranty</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseWarranty">
                    <div class="mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="threeMonthInput" type="checkbox"
                                value="3 months" wire:model.live="filters.warranty" name="warranty">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="threeMonthInput">3 months</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="sixMonthInput" type="checkbox" value="6 months"
                                wire:model.live="filters.warranty" name="warranty">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="sixMonthInput">6 months</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="oneYearInput" type="checkbox" value="1 year"
                                wire:model.live="filters.warranty" name="warranty">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="oneYearInput">1 year</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="twoYearsInput" type="checkbox" value="2 years"
                                wire:model.live="filters.warranty" name="warranty">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="twoYearsInput">2 years</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="threeYearsInput" type="checkbox"
                                value="3 years" wire:model.live="filters.warranty" name="warranty">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="threeYearsInput">3 years</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="fiveYearsInput" type="checkbox" value="5 years"
                                wire:model.live="filters.warranty" name="warranty">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="fiveYearsInput">5 years</label>
                        </div>
                    </div>
                </div>

                {{-- 11. WARRANTY TYPE --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse"
                    href="#collapseWarrantyType">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Warranty Type</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseWarrantyType">
                    <div class="mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="replacementInput" type="checkbox"
                                value="Replacement" wire:model.live="filters.warrantyType" name="warrantyType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="replacementInput">Replacement</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="serviceInput" type="checkbox" value="Service"
                                wire:model.live="filters.warrantyType" name="warrantyType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="serviceInput">Service</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="partialCoverageInput" type="checkbox"
                                value="Partial Coverage" wire:model.live="filters.warrantyType" name="warrantyType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="partialCoverageInput">Partial Coverage</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="appleCareInput" type="checkbox"
                                value="Apple Care" wire:model.live="filters.warrantyType" name="warrantyType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="appleCareInput">Apple Care</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="moneyBackInput" type="checkbox"
                                value="Money back" wire:model.live="filters.warrantyType" name="warrantyType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="moneyBackInput">Money back</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="extendableInput" type="checkbox"
                                value="Extendable" wire:model.live="filters.warrantyType" name="warrantyType">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="extendableInput">Extendable</label>
                        </div>
                    </div>
                </div>

                {{-- 12. CERTIFICATION --}}
                <a class="btn px-0 d-block collapse-indicator" data-bs-toggle="collapse"
                    href="#collapseCertification">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="fs-8 text-body-highlight">Certification</div>
                        <span class="fa-solid fa-angle-down toggle-icon text-body-quaternary"></span>
                    </div>
                </a>
                <div class="collapse show" id="collapseCertification">
                    <div class="mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="rohsInput" type="checkbox" value="RoHS"
                                wire:model.live="filters.certification" name="certification">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="rohsInput">RoHS</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="fccInput" type="checkbox" value="FCC"
                                wire:model.live="filters.certification" name="certification">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="fccInput">FCC</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="conflictInput" type="checkbox"
                                value="Conflict Free" wire:model.live="filters.certification" name="certification">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="conflictInput">Conflict Free</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="isoOneInput" type="checkbox"
                                value="ISO 9001:2015" wire:model.live="filters.certification" name="certification">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="isoOneInput">ISO 9001:2015</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="isoTwoInput" type="checkbox"
                                value="ISO 27001:2013" wire:model.live="filters.certification" name="certification">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="isoTwoInput">ISO 27001:2013</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-0" id="isoThreeInput" type="checkbox"
                                value="IEC 61000-4-2" wire:model.live="filters.certification" name="certification">
                            <label class="form-check-label d-block lh-sm fs-8 text-body fw-normal mb-0"
                                for="isoThreeInput">IEC 61000-4-2</label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Backdrop mobile --}}
            <div class="phoenix-offcanvas-backdrop d-lg-none" data-phoenix-backdrop style="top: 92px"></div>
        </div>

        {{-- GRID PRODUITS - 100% IDENTIQUE HTML --}}
        <div class="col-lg-9 col-xxl-10">
            <div class="row gx-3 gy-6 mb-8">
                @forelse($products as $product)
                    <div class="col-12 col-sm-6 col-md-4 col-xxl-2">
                        <div class="product-card-container h-100">
                            <div class="position-relative text-decoration-none product-card h-100">
                                <div class="d-flex flex-column justify-content-between h-100">
                                    {{-- Image + Wishlist --}}
                                    <div>
                                        <div
                                            class="border border-1 border-translucent rounded-3 position-relative mb-3">
                                            <!-- ✅ Bouton Livewire qui ENREGISTRE DIRECT dans wishlist_user_produit -->
                                            @if (auth()->check())
                                                {{-- ✅ CONNECTÉ : wire:click ACTIF --}}
                                                <button wire:click="toggleWishlist({{ $product->id }})"
                                                    class="btn btn-wish btn-wish-primary z-2 p-2" tabindex="-1"
                                                    style="box-shadow: none; outline: none;"**
                                                    data-bs-toggle="tooltip" title="Wishlist">
                                                    <i
                                                        class="{{ auth()->user()->wishlistProducts->contains($product->id) ? 'fas fa-heart text-danger' : 'far fa-heart' }}"></i>
                                                </button>
                                            @else
                                                {{-- ❌ NON CONNECTÉ : bouton disabled --}}
                                                <a class="btn btn-wish btn-wish-primary z-2 d-toggle-container"
                                                    href="{{ route('login') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Add to wishlist"><span
                                                        class="fas fa-heart d-block-hover"
                                                        data-fa-transform="down-1"></span><span
                                                        class="far fa-heart d-none-hover"
                                                        data-fa-transform="down-1"></span>
                                                </a>
                                            @endif



                                            @if ($product->primaryPhoto)
                                                <img class="img-fluid"
                                                    src="{{ $product->getPhoto()->getImageUrl(530, 530) }}"
                                                    alt="{{ $product->name }}" />
                                            @else
                                                <img class="img-fluid"
                                                    src="{{ $product->image ?? '/assets/img/products/1.png' }}"
                                                    alt="{{ $product->name }}" />
                                            @endif

                                            @if ($product->is_verified)
                                                <span class="badge text-bg-success fs-10 product-verified-badge">
                                                    Verified<span class="fas fa-check ms-1"></span>
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Nom + Rating --}}
                                        <a href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}"
                                            class="stretched-link">
                                            <h6 class="mb-2 lh-sm line-clamp-3 product-name">{{ $product->name }}</h6>
                                        </a>
                                        <p class="fs-9">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= round($product->average_rating ?? 5))
                                                    <span class="fa fa-star text-warning"></span>
                                                @else
                                                    <span class="fa-regular fa-star text-warning-light"></span>
                                                @endif
                                            @endfor
                                            <span class="text-body-quaternary fw-semibold ms-1">
                                                ({{ $product->reviews_count ?? 67 }} people rated)
                                            </span>
                                        </p>
                                    </div>

                                    {{-- Prix --}}
                                    <div>
                                        @if ($product->sale_price)
                                            <p class="fs-9 text-body-tertiary mb-2">
                                                {{ $product->sale_badge ?? 'dbrand skin available' }}</p>
                                            <div class="d-flex align-items-center mb-1">
                                                <p class="me-2 text-body text-decoration-line-through mb-0">
                                                    ${{ number_format($product->price, 2) }}
                                                </p>
                                                <h3 class="text-body-emphasis mb-0">
                                                    ${{ number_format($product->sale_price, 2) }}</h3>
                                            </div>
                                        @else
                                            <h3 class="text-body-emphasis">${{ number_format($product->price, 2) }}
                                            </h3>
                                        @endif

                                        @if ($product->stock_status == 'limited')
                                            <p class="fs-9 text-body-highlight fw-bold mb-2">Stock limited</p>
                                        @elseif($product->sale_ends_soon)
                                            <p class="text-success fw-bold fs-9 lh-1 mb-0">Deal time ends in days</p>
                                        @endif

                                        <p class="text-body-tertiary fw-semibold fs-9 lh-1 mb-0">
                                            {{ $product->colors_count ?? 1 }} colors
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="mb-4">
                            <span class="fa-solid fa-magnifying-glass fa-2x text-body-tertiary mb-3 d-block"></span>
                        </div>
                        <h5 class="mb-3">No products found</h5>
                        <p class="text-body-secondary mb-4">Try adjusting your filters to see more products</p>
                        <button class="btn btn-phoenix-primary" wire:click="clearFilters">Clear all filters</button>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            @if ($products->hasPages())
                <nav>
                    {{ $products->links() }}
                </nav>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Auto-close filters on mobile après sélection
        document.addEventListener('livewire:navigated', () => {
            const offcanvas = document.getElementById('productFilterColumn');
            if (window.innerWidth < 992 && offcanvas.classList.contains('show')) {
                bootstrap.Offcanvas.getInstance(offcanvas)?.hide();
            }
        });
    </script>
    <!-- Dans votre layout ou après Livewire -->
    <script>
        window.addEventListener('load', function() {
            // ✅ IMMÉDIAT après chargement
            if (window.location.search.includes('brands=')) {
                window.history.replaceState({}, '', '{{ route('products') }}');
            }
        });

        // ✅ OU encore plus rapide : directement dans le lien brands
        document.addEventListener('DOMContentLoaded', function() {
            // Nettoie TOUS les liens brands au clic
            document.querySelectorAll('a[href*="brands="]').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Nettoie URL au moment du clic
                    setTimeout(() => {
                        window.history.replaceState({}, '', '{{ route('products') }}');
                    }, 100); // 100ms après Livewire mount()
                });
            });
        });
    </script>
@endpush
