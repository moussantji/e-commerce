<div>
    {{-- Alert global pour erreurs --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form class="mb-9" wire:submit.prevent="submit" enctype="multipart/form-data">
        <div class="row g-3 flex-between-end mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Ajouter un produit</h2>
                <h5 class="text-body-tertiary fw-semibold">
                    Produit placé à travers ta boutique
                </h5>
            </div>
            <div class="col-auto">
                <button class="btn btn-phoenix-secondary me-2 mb-2 mb-sm-0" type="button" wire:click="$reset">
                    Annuler
                </button>
                <button class="btn btn-primary mb-2 mb-sm-0" type="submit">
                    Publier produit
                </button>
            </div>
        </div>

        <div class="row g-5">
            {{-- Colonne principale --}}
            <div class="col-12 col-xl-8">
                {{-- Titre --}}
                <h4 class="mb-3">Titre Produit</h4>
                <input class="form-control mb-5 @error('name') is-invalid @enderror" type="text"
                    placeholder="Ecris le titre ici..." wire:model.defer="name" />
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                {{-- Description --}}
                <div class="mb-6">
                    <h4 class="mb-3">Description Produit</h4>
                    <textarea class="form-control @error('description') is-invalid @enderror" rows="3" wire:model.defer="description"></textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- IMAGES MULTIPLES --}}
                <h4 class="mb-3">Images du produit</h4>
                <div class="dropzone dropzone-single p-4 mb-5 border border-dashed @error('images.*') border-danger @enderror"
                    id="product-images-dropzone">
                    <input type="file" wire:model="images" class="d-none" id="images-input" multiple
                        accept="image/*">

                    <div class="text-center">
                        @if (count($images) > 0)
                            <div class="row g-2 mb-3">
                                @foreach ($images as $index => $image)
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <img src="{{ $image->temporaryUrl(60) }}" class="rounded-3 w-100"
                                                style="height: 80px; object-fit: cover;">
                                            <button type="button"
                                                class="btn btn-sm position-absolute top-0 end-0 p-0 m-1 rounded-circle bg-danger text-white border-0"
                                                wire:click="removeImage({{ $index }})" title="Supprimer">
                                                <i class="fas fa-times fs-9"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-success mb-1">{{ count($images) }} image(s) sélectionnée(s)</p>
                        @else
                            <i class="fas fa-cloud-upload-alt fa-3x text-body-tertiary mb-3"></i>
                            <p class="text-body-tertiary mb-1">Glisser des images ici ou</p>
                            <button type="button" class="btn btn-link p-0"
                                onclick="document.getElementById('images-input').click()">
                                parcourir les fichiers
                            </button>
                            <p class="text-body-secondary fs-9 mt-2 mb-0">JPG, PNG (Max 2MB par image)</p>
                        @endif
                    </div>
                </div>
                @error('images.*')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <h4 class="mb-3">Inventaire</h4>
                <div class="row g-0 border-top border-bottom">
                    <div class="col-sm-4">
                        <div class="nav flex-sm-column border-bottom border-bottom-sm-0 border-end-sm fs-9 vertical-tab h-100 justify-content-between"
                            role="tablist">
                            <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center active"
                                id="pricingTab" data-bs-toggle="tab" data-bs-target="#pricingTabContent">
                                <span class="me-sm-2 fs-4 nav-icons" data-feather="tag"></span><span
                                    class="d-none d-sm-inline">Prix</span>
                            </a>
                            <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                                id="restockTab" data-bs-toggle="tab" data-bs-target="#restockTabContent">
                                <span class="me-sm-2 fs-4 nav-icons" data-feather="package"></span><span
                                    class="d-none d-sm-inline">Réapprovisionnement</span>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-8">
                        <div class="tab-content py-3 ps-sm-4 h-100">

                            {{-- PRIX --}}
                            <div class="tab-pane fade show active" id="pricingTabContent">
                                <h4 class="mb-3 d-sm-none">Prix</h4>
                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <h5 class="mb-2 text-body-highlight">Prix normal</h5>
                                        <div class="input-group">
                                            <span class="input-group-text">FCFA</span>
                                            <input class="form-control" type="number" wire:model="regular_price"
                                                placeholder="25000" min="100">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <h5 class="mb-2 text-body-highlight">Prix promo</h5>
                                        <div class="input-group">
                                            <span class="input-group-text">FCFA</span>
                                            <input class="form-control" type="number" wire:model="sale_price"
                                                placeholder="22500" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STOCK --}}
                            <div class="tab-pane fade" id="restockTabContent">
                                <div class="d-flex flex-column h-100">
                                    <h5 class="mb-3 text-body-highlight">Ajouter au stock</h5>
                                    <div class="row g-3 flex-1 mb-4">
                                        <div class="col-sm-7">
                                            <input class="form-control" type="number" wire:model="stock_quantity"
                                                placeholder="Quantité" min="0">
                                        </div>
                                        <div class="col-sm">
                                            <button class="btn btn-primary w-100" wire:click="addStock">
                                                <i class="fas fa-check me-1"></i>Confirmer
                                            </button>
                                        </div>
                                    </div>
                                    <table class="table table-sm">
                                        <tbody>
                                            @if (!$this->produit_created)
                                                <tr>
                                                    <td colspan="2" class="text-center py-4">
                                                        <div class="alert alert-info mb-0">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            <strong>Nouveau produit</strong><br>
                                                            <small class="text-muted">Stock et historique disponibles
                                                                après création</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="text-body-highlight fw-bold py-1">Stock actuel :</td>
                                                    <td>{{ number_format($stock_actuel ?? 0) }} unités</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-body-highlight fw-bold py-1">En transit :</td>
                                                    <td>{{ number_format($stock_transit ?? 0) }} unités</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-body-highlight fw-bold py-1">Dernier réappro :</td>
                                                    <td>{{ $last_restock ? $last_restock->format('d M Y') : 'Jamais' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-body-highlight fw-bold py-1">Stock total :</td>
                                                    <td>{{ number_format($stock_total ?? 0) }} unités</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                {{-- SKU --}}
                <div class="mt-4">
                    <label class="form-label">SKU (optionnel)</label>
                    <input class="form-control @error('sku') is-invalid @enderror" type="text" maxlength="100"
                        placeholder="SKU unique" wire:model.defer="sku" />
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <small class="text-body-secondary">
                            <i class="fas fa-tag me-1"></i>SKU = Stock Keeping Unit.
                            Code unique par variante (ex: TSHIRT-NIKE-BLANC-M).
                            <strong>Unique obligatoire</strong> pour suivi stock.
                        </small>
                    </div>

                </div>
            </div>

            {{-- Sidebar droite avec Brand + Tags --}}
            <div class="col-12 col-xl-4">
                {{-- Catégorie --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Catégorie</h5>
                            <a class="fw-bold fs-9 text-primary" href="{{ route('admin.categories.create') }}">
                                Ajouter une catégorie
                            </a>
                        </div>

                        <select class="form-select @error('category_id') is-invalid @enderror"
                            wire:model.defer="category_id">
                            <option value="">Aucune catégorie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- BRAND --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Marque</h5>
                            <a class="fw-bold fs-9 text-primary" href="{{ route('admin.brands.create') }}">
                                <i class="fas fa-plus me-1"></i>Ajouter une marque
                            </a>
                        </div>

                        <select class="form-select @error('brand_id') is-invalid @enderror"
                            wire:model.defer="brand_id">
                            <option value="">Aucune marque</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>

                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- TAGS --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Tags</h5>
                            <a class="fw-bold fs-9 text-primary" href="{{ route('admin.tags.create') }}">
                                <i class="fas fa-plus me-1"></i>Ajouter un tag
                            </a>
                        </div>

                        <select class="form-select" wire:model="selected_tags" multiple size="8"
                            style="height: 200px;">
                            @foreach ($all_tags as $tag)
                                {{-- ✅ TOUJOURS objets --}}
                                <option value="{{ $tag->id }}"
                                    {{ in_array($tag->id, $selected_tags) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>

                        <small class="text-body-secondary mt-1 d-block">Ctrl+clic pour sélection multiple</small>
                    </div>
                </div>

                {{-- Statut --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Statut</h5>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                            <label class="form-check-label" for="is_active">Produit actif</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured"
                                wire:model="is_featured">
                            <label class="form-check-label" for="is_featured">Produit en vedette</label>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Variantes</h4>
                        <div class="row g-3">
                            @foreach ($variant_options as $index => $option)
                                <div class="col-12 col-sm-6 col-xl-12">
                                    <div class="border-bottom border-translucent border-dashed pb-4">
                                        <div class="d-flex flex-wrap flex-between-center mb-2">
                                            <h5 class="text-body-highlight me-2">Option {{ $loop->iteration }}</h5>
                                            <button class="fw-bold fs-9 btn btn-link p-0 text-danger"
                                                wire:click="removeVariantOption({{ $index }})">
                                                Supprimer
                                            </button>
                                        </div>

                                        {{-- ✅ TYPE : Select des CARACTÉRISTIQUES existantes --}}
                                        <select class="form-select mb-3"
                                            wire:model="variant_options.{{ $index }}.type">
                                            <option value="">Choisir une caractéristique</option>
                                            @foreach ($caracteristiques_list as $caracteristique)
                                                <option value="{{ $caracteristique->type }}"
                                                    {{ old('variant_options.' . $index . '.type', $option['type'] ?? '') == $caracteristique->type ? 'selected' : '' }}>
                                                    {{ $caracteristique->name }} ({{ $caracteristique->type }})
                                                    @if ($caracteristique->unite)
                                                        <span
                                                            class="text-muted">({{ $caracteristique->unite }})</span>
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- INPUT VALEURS --}}
                                        <div class="product-variant-select-menu">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control"
                                                    wire:model.live="variant_options.{{ $index }}.values_input"
                                                    placeholder="ex: 1.8kg, rouge, S,M,L">
                                            </div>

                                            {{-- APERÇU --}}
                                            @if (!empty($option['values']))
                                                <div class="alert alert-success p-2 mb-0">
                                                    <small><i class="fas fa-check me-1"></i>
                                                        {{ implode(', ', $option['values']) }}
                                                    </small>
                                                </div>
                                            @endif

                                            <small class="text-muted">Séparées par virgules</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button class="btn btn-phoenix-primary w-100 mt-3" type="button"
                            wire:click="addVariantOption()">
                            Ajouter une autre option
                        </button>
                    </div>
                </div>



            </div>



        </div>
    </form>
</div>

@push('scripts')
    <script>
        // Trigger file input click
        document.addEventListener('livewire:load', function() {
            document.querySelector('#product-image-dropzone').addEventListener('click', function(e) {
                if (e.target.closest('button')) return;
                if (!Livewire.find('{{ $this->getId() }}')) return;
                document.getElementById('image-input').click();
            });
        });
    </script>
@endpush
