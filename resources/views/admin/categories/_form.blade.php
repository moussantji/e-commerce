{{-- Formulaire catégorie boutique — partagé création / édition.
    Variables : $action, $method ('POST'|'PUT'), $category (nullable), $categories, $submitLabel --}}
@csrf
@if ($method === 'PUT')
    @method('PUT')
@endif

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="cat-name">Nom *</label>
        <input class="ctrl" id="cat-name" type="text" name="name" required maxlength="255"
            placeholder="Ex : Smartphones" value="{{ old('name', $category->name ?? '') }}">
        @error('name') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="cat-slug">Slug *</label>
        <input class="ctrl" id="cat-slug" type="text" name="slug" required maxlength="255"
            placeholder="smartphones" value="{{ old('slug', $category->slug ?? '') }}">
        @error('slug') <span class="avis-err">{{ $message }}</span> @enderror
        <span class="muted-sm">URL conviviale (auto-généré si vide).</span>
    </div>
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="cat-parent">Catégorie parente</label>
        @if (isset($category) && $category->children()->exists())
            <select class="ctrl" disabled>
                <option>-- Catégorie principale (racine) --</option>
            </select>
            <input type="hidden" name="parent_id" value="">
            <span class="muted-sm">Possède des sous-catégories : reste principale.</span>
        @else
            <select class="ctrl" id="cat-parent" name="parent_id">
                <option value="">-- Catégorie principale (racine) --</option>
                @foreach ($categories as $parentCategory)
                    @if (!isset($category) || $parentCategory->id !== $category->id)
                        <option value="{{ $parentCategory->id }}"
                            {{ old('parent_id', $category->parent_id ?? '') == $parentCategory->id ? 'selected' : '' }}>
                            {{ $parentCategory->name }}</option>
                    @endif
                @endforeach
            </select>
            @error('parent_id') <span class="avis-err">{{ $message }}</span> @enderror
            <span class="muted-sm">2 niveaux maximum.</span>
        @endif
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="cat-sort">Ordre de tri</label>
        <input class="ctrl" id="cat-sort" type="number" name="sort_order" min="0" max="999"
            value="{{ old('sort_order', $category->sort_order ?? 0) }}">
        @error('sort_order') <span class="avis-err">{{ $message }}</span> @enderror
        <span class="muted-sm">Plus petit = affiché en premier.</span>
    </div>
</div>

<div class="field">
    <label for="cat-desc">Description</label>
    <textarea class="ctrl" id="cat-desc" name="description" rows="3" style="border-radius:12px;resize:vertical"
        placeholder="Description détaillée...">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description') <span class="avis-err">{{ $message }}</span> @enderror
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="cat-image">Image</label>
        @if (isset($category) && $category->getPhoto())
            <div style="margin-bottom:10px">
                <img src="{{ $category->getPhoto()->getImageUrl(200, 200) }}" alt="{{ $category->name }}"
                    style="width:96px;height:96px;object-fit:cover;border-radius:14px;border:1px solid var(--line)">
            </div>
        @endif
        <input class="ctrl" id="cat-image" type="file" name="image" accept="image/*">
        @error('image') <span class="avis-err">{{ $message }}</span> @enderror
        <span class="muted-sm">300x300px recommandé.</span>
    </div>
    <div class="field" style="margin-bottom:0">
        <label>Bannières (accueil mobile)</label>
        <input class="ctrl" type="file" name="banner_image_1" accept="image/*" style="margin-bottom:10px">
        @if (isset($category) && $category->bannerImageUrl(1))
            <img src="{{ $category->bannerImageUrl(1) }}" alt="Bannière 1"
                style="max-height:70px;border-radius:10px;border:1px solid var(--line);margin-bottom:10px">
        @endif
        <input class="ctrl" type="file" name="banner_image_2" accept="image/*">
        @if (isset($category) && $category->bannerImageUrl(2))
            <img src="{{ $category->bannerImageUrl(2) }}" alt="Bannière 2"
                style="max-height:70px;border-radius:10px;border:1px solid var(--line);margin-top:10px">
        @endif
    </div>
</div>

<div class="field">
    <label class="switch"><input type="checkbox" name="is_active" value="1"
            {{ old('is_active', isset($category) ? $category->is_active : true) ? 'checked' : '' }}> Catégorie
        active</label>
</div>

<div class="pdp-actions" style="margin-top:18px">
    <button class="btn-solid" type="submit"><svg class="ic">
            <use href="#i-b2-check" />
        </svg> {{ $submitLabel }}</button>
    <a class="btn-line" href="{{ route('admin.categories.index') }}">Annuler</a>
</div>
