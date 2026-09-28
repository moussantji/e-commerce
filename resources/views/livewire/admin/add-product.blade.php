<div>
    @if ($errors->any())
        <div class="tagline-band" style="background:#ffe4e6;border-color:#fecdd3;color:#be123c">
            <svg class="ic">
                <use href="#i-b2-alert" />
            </svg>
            <span><b>Corrigez les erreurs :</b>
                <br>{{ implode(' · ', $errors->all()) }}</span>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="tagline-band">
            <svg class="ic">
                <use href="#i-b2-check" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="submit" enctype="multipart/form-data">
        <div class="dash-head">
            <div>
                <h1>Ajouter un produit</h1>
                <p>Produit placé à travers votre boutique.</p>
            </div>
            <div class="pdp-actions" style="margin:0">
                <button class="btn-line" type="button" wire:click="$reset">Annuler</button>
                <button class="btn-solid" type="submit"><svg class="ic">
                        <use href="#i-b2-check" />
                    </svg> Publier le produit</button>
            </div>
        </div>

        <div class="dash-grid">
            <div>
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-tag" />
                        </svg> Titre & description</h2>
                    <div class="field">
                        <label for="ap-name">Titre du produit *</label>
                        <input class="ctrl" id="ap-name" type="text" placeholder="Écrivez le titre ici..."
                            wire:model.defer="name">
                        @error('name') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="ap-desc">Description</label>
                        <textarea class="ctrl" id="ap-desc" rows="3" wire:model.defer="description" style="border-radius:12px;resize:vertical"></textarea>
                        @error('description') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="ap-sku">SKU (optionnel)</label>
                        <input class="ctrl" id="ap-sku" type="text" maxlength="100" placeholder="SKU unique"
                            wire:model.defer="sku">
                        @error('sku') <span class="avis-err">{{ $message }}</span> @enderror
                        <span class="muted-sm">Code unique par variante (ex : TSHIRT-NIKE-BLANC-M).</span>
                    </div>
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Images du produit</h2>
                    <input type="file" wire:model="images" id="ap-images" multiple accept="image/*" hidden>
                    @if (count($images) > 0)
                        <div class="g-thumbs" style="margin:0 0 12px">
                            @foreach ($images as $index => $image)
                                <span class="aprev">
                                    <img src="{{ $image->temporaryUrl() }}" alt="Aperçu">
                                    <button type="button" wire:click="removeImage({{ $index }})"
                                        aria-label="Retirer">×</button>
                                </span>
                            @endforeach
                        </div>
                        <p class="muted-sm">{{ count($images) }} image(s) sélectionnée(s)</p>
                    @endif
                    <label class="dropzone" for="ap-images"><svg class="ic">
                            <use href="#i-bag" />
                        </svg><span>Glissez des images ici ou cliquez pour parcourir<br><small
                                style="color:var(--grey)">JPG, PNG — 2 Mo max par image</small></span></label>
                    @error('images.*') <span class="avis-err">{{ $message }}</span> @enderror
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-card" />
                        </svg> Prix & stock</h2>
                    <div class="dash-grid" style="margin-bottom:0">
                        <div class="field" style="margin-bottom:0">
                            <label for="ap-price">Prix normal (FCFA) *</label>
                            <input class="ctrl" id="ap-price" type="number" wire:model="regular_price"
                                placeholder="25000" min="0">
                            @error('regular_price') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="ap-sale">Prix promo (FCFA)</label>
                            <input class="ctrl" id="ap-sale" type="number" wire:model="sale_price"
                                placeholder="22500" min="0">
                            @error('sale_price') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="dash-grid" style="margin-bottom:0;margin-top:13px">
                        <div class="field" style="margin-bottom:0">
                            <label for="ap-stock">Quantité en stock</label>
                            <input class="ctrl" id="ap-stock" type="number" wire:model="stock_quantity"
                                placeholder="Quantité" min="0">
                            @error('stock_quantity') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0;justify-content:end">
                            <button class="btn-line" type="button" wire:click="addStock">Confirmer le stock</button>
                        </div>
                    </div>
                    @if ($produit_created)
                        <table class="spec" style="margin-top:14px">
                            <tbody>
                                <tr>
                                    <td>Stock actuel</td>
                                    <td><b>{{ number_format($stock_actuel ?? 0) }} unités</b></td>
                                </tr>
                                <tr>
                                    <td>En transit</td>
                                    <td>{{ number_format($stock_transit ?? 0) }} unités</td>
                                </tr>
                                <tr>
                                    <td>Dernier réappro</td>
                                    <td>{{ $last_restock ? $last_restock->format('d/m/Y') : 'Jamais' }}</td>
                                </tr>
                                <tr>
                                    <td>Stock total</td>
                                    <td><b>{{ number_format($stock_total ?? 0) }} unités</b></td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="muted-sm" style="margin-top:12px">Stock et historique disponibles après création.</p>
                    @endif
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-grid" />
                        </svg> Variantes (stockage, taille, couleur...)</h2>
                    @foreach ($variant_options as $index => $option)
                        <div class="opt-grp">
                            <label>Option {{ $loop->iteration }}</label>
                            <div class="dash-grid" style="margin-bottom:10px">
                                <select class="ctrl" wire:model="variant_options.{{ $index }}.type"
                                    aria-label="Caractéristique">
                                    <option value="">Choisir une caractéristique</option>
                                    @foreach ($caracteristiques_list as $caracteristique)
                                        <option value="{{ $caracteristique->type }}">
                                            {{ $caracteristique->name }} ({{ $caracteristique->type }})
                                            @if ($caracteristique->unite)
                                                ({{ $caracteristique->unite }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input class="ctrl" type="text"
                                    wire:model.live="variant_options.{{ $index }}.values_input"
                                    placeholder="ex : 128 Go, 256 Go">
                            </div>
                            @if (!empty($option['values']))
                                <div class="sel-recap"><svg class="ic">
                                        <use href="#i-b2-check" />
                                    </svg><span>{{ implode(', ', $option['values']) }}</span></div>
                            @endif
                            <button class="lien" style="margin-top:8px" type="button"
                                wire:click="removeVariantOption({{ $index }})">Supprimer cette option</button>
                        </div>
                    @endforeach
                    <div class="pdp-actions" style="margin-top:14px">
                        <button class="btn-line" type="button" wire:click="addVariantOption()">Ajouter une autre
                            option</button>
                    </div>
                    <p class="muted-sm" style="margin-top:8px">Valeurs séparées par des virgules.</p>
                </div>
            </div>

            <div>
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-grid" />
                        </svg> Catégorie</h2>
                    <div class="field" style="margin-bottom:10px">
                        <select class="ctrl" wire:model.defer="category_id" aria-label="Catégorie">
                            <option value="">Aucune catégorie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <a class="lien" href="{{ route('admin.categories.create') }}">+ Ajouter une catégorie</a>
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-tag" />
                        </svg> Marque</h2>
                    <div class="field" style="margin-bottom:10px">
                        <select class="ctrl" wire:model.defer="brand_id" aria-label="Marque">
                            <option value="">Aucune marque</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <a class="lien" href="{{ route('admin.brands.create') }}">+ Ajouter une marque</a>
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-bolt" />
                        </svg> Tags</h2>
                    <div class="field" style="margin-bottom:6px">
                        <select class="ctrl" wire:model="selected_tags" multiple size="6" aria-label="Tags">
                            @foreach ($all_tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                        @error('selected_tags') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <p class="muted-sm">Ctrl+clic pour sélection multiple.</p>
                    <a class="lien" href="{{ route('admin.tags.create') }}">+ Ajouter un tag</a>
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-check" />
                        </svg> Statut</h2>
                    <label class="switch" style="margin-bottom:12px"><input type="checkbox"
                            wire:model="is_active"> Produit actif</label>
                    <br>
                    <label class="switch"><input type="checkbox" wire:model="is_featured"> Produit en vedette</label>
                </div>
            </div>
        </div>
    </form>
</div>
