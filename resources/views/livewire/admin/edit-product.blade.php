<div>
    @if (session()->has('success'))
        <div class="tagline-band">
            <svg class="ic">
                <use href="#i-b2-check" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="update">
        <div class="dash-head">
            <div>
                <h1>Modifier le produit</h1>
                <p>{{ $produit->name ?? 'Chargement...' }}</p>
            </div>
            <div class="pdp-actions" style="margin:0">
                <a class="btn-line" href="{{ route('admin.products.index') }}">Retour à la liste</a>
                <button class="btn-solid" type="submit"><svg class="ic">
                        <use href="#i-b2-check" />
                    </svg> Mettre à jour</button>
            </div>
        </div>

        <div class="dash-grid">
            <div>
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-tag" />
                        </svg> Aperçu sur la boutique</h2>
                    @php
                        $pvFirst = !empty($old_images) && count($old_images) ? $old_images->first() : null;
                        $pvImg = $pvFirst ? $pvFirst->getImageUrl(530, 530) : asset('assets/img/products/1.png');
                        $pvReg = (float) ($regular_price ?? 0);
                        $pvSale = $sale_price !== null && $sale_price !== '' ? (float) $sale_price : null;
                        $pvPromo = $pvSale !== null && $pvSale < $pvReg && $pvReg > 0;
                        $pvCat = collect($categories ?? [])->firstWhere('id', $category_id);
                        $pvOff = $pvPromo ? round(($pvReg - $pvSale) / $pvReg * 100) : 0;
                        $pvUrl = $produit ? route('produits.show', ['slug' => $produit->getSlug(), 'id' => $produit->id]) : route('products');
                        $pvStock = (int) ($stock_actuel ?? 0);
                        $pvCls = $pvStock <= 0 ? 'out' : ($pvStock <= 8 ? 'low' : 'ok');
                        $pvTxt = $pvStock <= 0 ? 'Rupture de stock' : ($pvStock <= 8 ? 'Plus que ' . $pvStock . ' en stock' : 'En stock');
                    @endphp
                    <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap">
                        <a href="{{ $pvUrl }}" target="_blank" rel="noopener"
                            style="width:130px;height:130px;border-radius:14px;border:1px solid var(--line);background:#f5f3ff center/cover no-repeat;background-image:url('{{ $pvImg }}');flex:none"
                            aria-label="Voir la fiche publique"></a>
                        <div style="flex:1;min-width:200px">
                            <div class="name" style="font-size:15px;font-weight:700">{{ $name ?: 'Sans titre' }}</div>
                            <div class="muted-sm">{{ $pvCat->name ?? '' }}
                                @if ($pvPromo)
                                    <span class="off" style="position:static">-{{ $pvOff }}%</span>
                                @endif
                            </div>
                            <div style="margin:6px 0">
                                <span class="price"
                                    style="font-size:19px;font-weight:800;color:var(--pink)">{{ number_format($pvPromo ? $pvSale : $pvReg, 0, ',', ' ') }}
                                    FCFA</span>
                                @if ($pvPromo)
                                    <span class="was">{{ number_format($pvReg, 0, ',', ' ') }} FCFA</span>
                                @endif
                            </div>
                            <span class="stock {{ $pvCls }}">{{ $pvTxt }}</span>
                            <div class="pdp-actions" style="margin-top:12px">
                                <a class="btn-line" href="{{ $pvUrl }}" target="_blank" rel="noopener"><svg
                                        class="ic">
                                        <use href="#i-search" />
                                    </svg> Voir la fiche publique</a>
                            </div>
                        </div>
                    </div>
                    <p class="muted-sm" style="margin-top:10px">L'aperçu se met à jour à chaque modification
                        enregistrée.</p>
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-tag" />
                        </svg> Titre & description</h2>
                    <div class="field">
                        <label for="ep-name">Titre du produit *</label>
                        <input class="ctrl" id="ep-name" type="text" placeholder="Écrivez le titre ici..."
                            wire:model.defer="name">
                        @error('name') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="ep-desc">Description</label>
                        <textarea class="ctrl" id="ep-desc" rows="3" wire:model.defer="description" style="border-radius:12px;resize:vertical"></textarea>
                        @error('description') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="ep-sku">SKU (optionnel)</label>
                        <input class="ctrl" id="ep-sku" type="text" maxlength="255" placeholder="SKU unique"
                            wire:model.defer="sku">
                        @error('sku') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Images du produit</h2>
                    @if (!empty($old_images))
                        <label>Images actuelles</label>
                        <div class="g-thumbs" style="margin:0 0 12px">
                            @foreach ($old_images as $image)
                                <span class="aprev">
                                    <img src="{{ $image->getImageUrl(200, 200) }}" alt="Photo produit">
                                    <button type="button" wire:click="deletePhoto({{ $image->id }})"
                                        aria-label="Supprimer">×</button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                    <input type="file" wire:model="images" id="ep-images" multiple accept="image/*" hidden>
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
                    <label class="dropzone" for="ep-images"><svg class="ic">
                            <use href="#i-bag" />
                        </svg><span>Cliquez pour ajouter des images<br><small style="color:var(--grey)">JPG, PNG — 2
                                Mo max par image</small></span></label>
                    @error('images.*') <span class="avis-err">{{ $message }}</span> @enderror
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-card" />
                        </svg> Prix & stock</h2>
                    <div class="dash-grid" style="margin-bottom:0">
                        <div class="field" style="margin-bottom:0">
                            <label for="ep-price">Prix normal (FCFA) *</label>
                            <input class="ctrl" id="ep-price" type="number" wire:model="regular_price"
                                placeholder="25000" min="100">
                            @error('regular_price') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="ep-sale">Prix promo (FCFA)</label>
                            <input class="ctrl" id="ep-sale" type="number" wire:model="sale_price"
                                placeholder="22500" min="0">
                            @error('sale_price') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="dash-grid" style="margin-bottom:0;margin-top:13px">
                        <div class="field" style="margin-bottom:0">
                            <label for="ep-stock">Ajouter au stock</label>
                            <input class="ctrl" id="ep-stock" type="number" wire:model="stock_quantity"
                                placeholder="Quantité" min="0">
                        </div>
                        <div class="field" style="margin-bottom:0;justify-content:end">
                            <button class="btn-line" type="button" wire:click="addStock">Confirmer</button>
                        </div>
                    </div>
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
                                            {{ $caracteristique->name }}
                                            @if ($caracteristique->unite)
                                                ({{ $caracteristique->unite }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input class="ctrl" type="text"
                                    wire:model.live="variant_options.{{ $index }}.values_input"
                                    placeholder="S, M, L ou Rouge, Bleu">
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
                            <use href="#i-user" />
                        </svg> Vendeur (optionnel)</h2>
                    <div class="field" style="margin-bottom:6px">
                        <select class="ctrl" wire:model.defer="vendeur_id" aria-label="Vendeur">
                            <option value="">Boutique (aucun vendeur)</option>
                            @foreach ($vendeurs as $vendeur)
                                <option value="{{ $vendeur->id }}">{{ $vendeur->name }} ({{ $vendeur->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('vendeur_id') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <p class="muted-sm">Attribuer ce produit à un vendeur.</p>
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
