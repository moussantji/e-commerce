@php
    $isEdit = isset($product);
    $formAction = $isEdit ? route('admin.products.update', $product) : route('admin.products.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Modifier le produit' : 'Ajouter un produit';
    $buttonText = $isEdit ? 'Mettre à jour' : 'Publier le produit';
@endphp

<form action="{{ $formAction }}" method="POST" class="mb-9" enctype="multipart/form-data">
    @csrf
    @method($method)

    <div class="row g-3 flex-between-end mb-5">
        <div class="col-auto">
            <h2 class="mb-2">{{ $title }}</h2>
            <h5 class="text-body-tertiary fw-semibold">
                Gestion des produits
            </h5>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.products.index') }}" class="btn btn-phoenix-secondary me-2 mb-2 mb-sm-0">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary mb-2 mb-sm-0">
                {{ $buttonText }}
            </button>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-12 col-xl-8">
            <x-form.input
                name="name"
                label="Nom du produit"
                :value="old('name', $product->name ?? '')"
                placeholder="Entrez le nom du produit"
                required
            />

            <x-form.textarea
                name="description"
                label="Description du produit"
                :value="old('description', $product->description ?? '')"
                placeholder="Décrivez le produit en détail..."
                rows="5"
                class="tinymce"
                data-tinymce='{"height":"15rem","placeholder":"Écrivez une description ici..."}'
            />

            <x-form.file
                name="images"
                label="Images du produit"
                :multiple="true"
                accept="image/*"
                help="Formats acceptés : JPG, PNG, GIF, WEBP. Taille maximale : 5 Mo."
                wrapperClass="mb-5"
            />

            @if(isset($product) && $product->images->count() > 0)
                <div class="mb-4">
                    <h5 class="mb-3">Images existantes</h5>
                    <div class="d-flex flex-wrap">
                        @foreach($product->images as $image)
                            <div class="position-relative me-2 mb-2" style="width: 120px;">
                                <img src="{{ asset('storage/' . $image->path) }}"
                                     alt="{{ $product->name }}"
                                     class="img-thumbnail"
                                     style="width: 100%; height: 120px; object-fit: cover;">
                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                <button type="button"
                                        class="btn-close position-absolute top-0 end-0 m-1 bg-white rounded-circle p-1"
                                        aria-label="Supprimer"
                                        onclick="this.closest('div').remove()">
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="row g-0 border-top border-bottom">
                <div class="col-sm-12">
                    <div class="nav flex-sm-column border-bottom border-bottom-sm-0 border-end-sm fs-9 vertical-tab h-100 justify-content-between"
                         role="tablist"
                         aria-orientation="vertical">
                        <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center active"
                           id="pricingTab"
                           data-bs-toggle="tab"
                           data-bs-target="#pricingTabContent"
                           role="tab"
                           aria-controls="pricingTabContent"
                           aria-selected="true">
                            <span class="me-sm-2 fs-4 nav-icons" data-feather="tag"></span>
                            <span class="d-none d-sm-inline">Prix</span>
                        </a>
                        <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                           id="inventoryTab"
                           data-bs-toggle="tab"
                           data-bs-target="#inventoryTabContent"
                           role="tab"
                           aria-controls="inventoryTabContent"
                           aria-selected="false">
                            <span class="me-sm-2 fs-4 nav-icons" data-feather="package"></span>
                            <span class="d-none d-sm-inline">Inventaire</span>
                        </a>
                        <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                           id="seoTab"
                           data-bs-toggle="tab"
                           data-bs-target="#seoTabContent"
                           role="tab"
                           aria-controls="seoTabContent"
                           aria-selected="false">
                            <span class="me-sm-2 fs-4 nav-icons" data-feather="search"></span>
                            <span class="d-none d-sm-inline">SEO</span>
                        </a>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="tab-content py-3 ps-sm-4 h-100">
                        <!-- Prix -->
                        <div class="tab-pane fade show active" id="pricingTabContent" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-form.input
                                        name="price"
                                        type="number"
                                        label="Prix de vente"
                                        :value="old('price', $product->price ?? '')"
                                        placeholder="0.00"
                                        step="0.01"
                                        min="0"
                                        required
                                        :prepend-text="'€'"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-form.input
                                        name="compare_at_price"
                                        type="number"
                                        label="Prix barré (facultatif)"
                                        :value="old('compare_at_price', $product->compare_at_price ?? '')"
                                        placeholder="0.00"
                                        step="0.01"
                                        min="0"
                                        :prepend-text="'€'"
                                        help="Le prix barré sera affiché barré à côté du prix de vente"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Inventaire -->
                        <div class="tab-pane fade" id="inventoryTabContent" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-form.input
                                        name="sku"
                                        label="Référence SKU"
                                        :value="old('sku', $product->sku ?? '')"
                                        placeholder="ex: SKU-12345"
                                        help="Identifiant unique pour ce produit dans votre inventaire"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-form.input
                                        name="barcode"
                                        label="Code-barres (ISBN, UPC, GTIN, etc.)"
                                        :value="old('barcode', $product->barcode ?? '')"
                                        placeholder="ex: 123456789012"
                                        help="Code-barres standard pour ce produit"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-form.switch
                                        name="track_quantity"
                                        label="Suivre la quantité en stock"
                                        :checked="old('track_quantity', $product->track_quantity ?? true)"
                                        onText="Activé"
                                        offText="Désactivé"
                                        wrapperClass="mb-3"
                                    />

                                    <div id="quantityField">
                                        <x-form.input
                                            name="quantity"
                                            type="number"
                                            label="Quantité en stock"
                                            :value="old('quantity', $product->quantity ?? 0)"
                                            min="0"
                                            step="1"
                                            placeholder="0"
                                            :required="old('track_quantity', $product->track_quantity ?? true)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        Organisation
                    </h4>

                    <x-form.select
                        name="category_id"
                        label="Catégorie"
                        :options="$categories"
                        :selected="old('category_id', $product->category_id ?? '')"
                        required
                        placeholder="Sélectionnez une catégorie"
                        wrapperClass="mb-4"
                    />

                    <x-form.select
                        name="brand_id"
                        label="Marque"
                        :options="$brands"
                        :selected="old('brand_id', $product->brand_id ?? '')"
                        placeholder="Sélectionnez une marque (optionnel)"
                        wrapperClass="mb-4"
                    />

                    <x-form.select
                        name="status"
                        label="Statut"
                        :options="[
                            ['id' => 'draft', 'name' => 'Brouillon'],
                            ['id' => 'active', 'name' => 'Actif'],
                            ['id' => 'archived', 'name' => 'Archivé'],
                        ]"
                        :selected="old('status', $product->status ?? 'draft')"
                        option-value-field="id"
                        option-label-field="name"
                        wrapperClass="mb-4"
                    />

                    <x-form.select
                        name="brand_id"
                        label="Marque (facultatif)"
                        :options="$brands"
                        :selected="old('brand_id', $product->brand_id ?? '')"
                        placeholder="Sélectionnez une marque"
                        wrapperClass="mb-4"
                    />

                    <x-form.input
                        name="tags"
                        label="Tags"
                        :value="old('tags', isset($product) ? $product->tags->pluck('name')->implode(',') : '')"
                        placeholder="Saisissez des tags séparés par des virgules"
                        data-role="tagsinput"
                        help="Appuyez sur Entrée ou tapez une virgule pour ajouter un tag"
                    />
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <span class="fas fa-tags me-2"></span>Variantes
                    </h4>

                    <div id="variants-container">
                        <!-- Les variantes seront ajoutées ici dynamiquement -->
                        @if(isset($product) && $product->variants->count() > 0)
                            @foreach($product->variants as $index => $variant)
                                <div class="variant-item card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Variante #{{ $index + 1 }}</h6>
                                            <button type="button" class="btn-close" onclick="this.closest('.variant-item').remove()"></button>
                                        </div>

                                        <x-form.input
                                            name="variants[{{ $index }}][name]"
                                            label="Nom de la variante"
                                            :value="$variant->name"
                                            placeholder="ex: Taille, Couleur"
                                            wrapperClass="mb-3"
                                        />

                                        <x-form.input
                                            name="variants[{{ $index }}][value]"
                                            label="Valeurs (séparées par des virgules)"
                                            :value="$variant->value"
                                            placeholder="ex: S, M, L, XL"
                                            wrapperClass="mb-3"
                                        />

                                        <x-form.input
                                            name="variants[{{ $index }}][price_adjustment]"
                                            type="number"
                                            label="Ajustement de prix (optionnel)"
                                            :value="$variant->price_adjustment"
                                            placeholder="0.00"
                                            step="0.01"
                                            :prepend-text="'€'"
                                        />

                                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <button type="button" class="btn btn-phoenix-primary w-100" id="add-variant">
                        <span class="fas fa-plus me-2"></span>Ajouter une variante
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet" />
    <style>
        .bootstrap-tagsinput {
            width: 100%;
            padding: 0.5rem 0.75rem;
        }
        .bootstrap-tagsinput .tag {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            margin-right: 0.25rem;
        }
        .bootstrap-tagsinput input {
            margin-top: 0.25rem;
        }
        .dz-preview {
            margin: 0.5rem;
        }
        .dz-image {
            max-width: 100%;
            height: auto;
            border-radius: 0.25rem;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>

    <script>
        // Initialisation de TinyMCE
        tinymce.init({
            selector: '.tinymce',
            plugins: 'link lists table code',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link | table | code',
            menubar: false,
            height: 300,
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });

        // Initialisation de Select2
        $(document).ready(function() {
            $('.form-select').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('form')
            });

            // Gestion de l'affichage/masquage du champ de quantité
            const trackQuantity = document.getElementById('track_quantity');
            const quantityField = document.getElementById('quantityField');

            function toggleQuantityField() {
                if (trackQuantity) {
                    const quantityInput = quantityField.querySelector('input');
                    if (trackQuantity.checked) {
                        quantityField.style.display = 'block';
                        quantityInput.required = true;
                    } else {
                        quantityField.style.display = 'none';
                        quantityInput.required = false;
                    }
                }
            }

            if (trackQuantity) {
                trackQuantity.addEventListener('change', toggleQuantityField);
                toggleQuantityField(); // Initial call
            }

            // Initialisation de Dropzone pour le téléchargement des images
            if (document.getElementById('product-images-dropzone')) {
                Dropzone.autoDiscover = false;
                const myDropzone = new Dropzone("#product-images-dropzone", {
                    url: "{{ route('admin.media.upload') }}",
                    paramName: "image",
                    maxFilesize: 5, // MB
                    acceptedFiles: "image/*",
                    addRemoveLinks: true,
                    autoProcessQueue: true,
                    uploadMultiple: false,
                    parallelUploads: 10,
                    maxFiles: 10,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    init: function() {
                        this.on("success", function(file, response) {
                            // Ajouter un champ caché avec l'ID du média
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'uploaded_media_ids[]';
                            hiddenInput.value = response.id;
                            file.previewElement.appendChild(hiddenInput);
                        });

                        this.on("removedfile", function(file) {
                            // Supprimer le fichier du serveur si nécessaire
                            if (file.xhr && file.xhr.response) {
                                const response = JSON.parse(file.xhr.response);
                                if (response.id) {
                                    fetch(`/admin/media/${response.id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json'
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            }

            // Gestion des variantes
            const variantsContainer = document.getElementById('variants-container');
            const addVariantBtn = document.getElementById('add-variant');

            if (addVariantBtn) {
                let variantCount = variantsContainer ? variantsContainer.children.length : 0;

                addVariantBtn.addEventListener('click', function() {
                    variantCount++;
                    const variantHtml = `
                        <div class="variant-item card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Variante #${variantCount}</h6>
                                    <button type="button" class="btn-close" onclick="this.closest('.variant-item').remove()"></button>
                                </div>

                                <x-form.input
                                    name="variants[${variantCount}][name]"
                                    label="Nom de la variante"
                                    placeholder="ex: Taille, Couleur"
                                    wrapperClass="mb-3"
                                />

                                <x-form.input
                                    name="variants[${variantCount}][value]"
                                    label="Valeurs (séparées par des virgules)"
                                    placeholder="ex: S, M, L, XL"
                                    help="Séparez chaque valeur par une virgule"
                                    wrapperClass="mb-3"
                                />

                                <x-form.input
                                    name="variants[${variantCount}][price_adjustment]"
                                    type="number"
                                    label="Ajustement de prix (optionnel)"
                                    value="0.00"
                                    step="0.01"
                                    :prepend-text="'€'"
                                    help="Ce montant s'ajoutera au prix de base du produit"
                                />
                            </div>
                        </div>
                    `;

                    variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
                });
            }

            // Initialisation de Bootstrap Tags Input
            $('input[data-role="tagsinput"]').tagsinput({
                tagClass: 'badge bg-primary me-1 mb-1',
                trimValue: true,
                cancelConfirmKeysOnEmpty: false
            });

            // Amélioration de l'accessibilité pour les tags
            $('.bootstrap-tagsinput input').attr('placeholder', 'Appuyez sur Entrée pour ajouter un tag');
        });

        // Fonction pour formater les prix
        function formatPrice(price) {
            return parseFloat(price).toFixed(2).replace('.', ',') + ' €';
        }
    </script>

    <style>
        /* Styles pour les tags */
        .bootstrap-tagsinput {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
            box-shadow: none;
        }

        .bootstrap-tagsinput .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .bootstrap-tagsinput input {
            border: none;
            box-shadow: none;
            outline: none;
            background-color: transparent;
            padding: 0;
            margin: 0;
            width: auto !important;
            max-width: inherit;
        }

        .bootstrap-tagsinput input:focus {
            border: none;
            box-shadow: none;
        }

        /* Styles pour les aperçus Dropzone */
        .dz-preview {
            margin: 0.5rem;
        }

        .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dz-remove {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.25rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 0 0.25rem 0 0;
            color: #dc3545;
            text-decoration: none;
        }

        .dz-remove:hover {
            color: #b02a37;
            background: rgba(255, 255, 255, 0.9);
        }
    </style>
@endpush
