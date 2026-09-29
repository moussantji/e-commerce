@extends('base')

@section('title', ($product ? 'Modifier mon produit' : 'Ajouter un produit') . ' — Vendeur')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('vendeur.dashboard') }}">Espace vendeur</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('vendeur.products.index') }}">Mes produits</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ $product ? $product->name : 'Nouveau' }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>{{ $product ? 'Modifier mon produit' : 'Ajouter un produit' }}</h1>
            <p>Votre article sera visible dans le catalogue de la boutique.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form
                    action="{{ $product ? route('vendeur.products.update', $product) : route('vendeur.products.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($product)
                        @method('PUT')
                    @endif

                    <div class="field">
                        <label for="vp-name">Nom du produit *</label>
                        <input class="ctrl" id="vp-name" type="text" name="name" required maxlength="255"
                            value="{{ old('name', $product->name ?? '') }}">
                        @error('name') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="vp-desc">Description</label>
                        <textarea class="ctrl" id="vp-desc" name="description" rows="4" style="border-radius:12px;resize:vertical">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="vp-price">Prix (FCFA) *</label>
                            <input class="ctrl" id="vp-price" type="number" step="0.01" min="0" name="price"
                                required value="{{ old('price', $product->price ?? '') }}">
                            @error('price') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="vp-sale">Prix promo (FCFA)</label>
                            <input class="ctrl" id="vp-sale" type="number" step="0.01" min="0" name="sale_price"
                                value="{{ old('sale_price', $product->sale_price ?? '') }}">
                            @error('sale_price') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="vp-stock">Stock *</label>
                            <input class="ctrl" id="vp-stock" type="number" min="0" name="stock" required
                                value="{{ old('stock', $product->stock ?? 0) }}">
                            @error('stock') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="vp-sku">Référence (SKU)</label>
                            <input class="ctrl" id="vp-sku" type="text" name="sku" maxlength="100"
                                placeholder="Auto si vide"
                                value="{{ old('sku', $product->sku ?? '') }}">
                            @error('sku') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="vp-cat">Catégorie</label>
                            <select class="ctrl" id="vp-cat" name="category_id">
                                <option value="">—</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="vp-brand">Marque</label>
                            <select class="ctrl" id="vp-brand" name="brand_id">
                                <option value="">—</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="vp-tags">Tags</label>
                        <select class="ctrl" id="vp-tags" name="tags[]" multiple size="5">
                            @php $selTags = old('tags', $product ? $product->tags->pluck('id')->toArray() : []); @endphp
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}"
                                    {{ in_array($tag->id, (array) $selTags) ? 'selected' : '' }}>{{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="muted-sm">Ctrl+clic pour sélection multiple.</span>
                        @error('tags') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="vp-images">Photos (10 max)</label>
                        @if ($product && $product->photos->count())
                            <div class="g-thumbs" style="margin:0 0 12px">
                                @foreach ($product->photos as $ph)
                                    <span class="aprev"><img src="{{ $ph->getImageUrl(200, 200) }}"
                                            alt="Photo"></span>
                                @endforeach
                            </div>
                        @endif
                        <input class="ctrl" id="vp-images" type="file" name="images[]" multiple accept="image/*">
                        @error('images.*') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label class="switch"><input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}> Produit
                            visible dans la boutique</label>
                    </div>

                    <div class="pdp-actions" style="margin-top:18px">
                        <button class="btn-solid" type="submit"><svg class="ic">
                                <use href="#i-b2-check" />
                            </svg> {{ $product ? 'Mettre à jour' : 'Créer le produit' }}</button>
                        <a class="btn-line" href="{{ route('vendeur.products.index') }}">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
