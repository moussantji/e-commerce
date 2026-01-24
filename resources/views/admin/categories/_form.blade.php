@csrf

<div class="mb-3">
    <label for="name" class="form-label">Nom de la catégorie *</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror"
           id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror"
              id="description" name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="parent_id" class="form-label">Catégorie parente</label>
    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
        <option value="">Aucune (catégorie principale)</option>
        @foreach($categories as $cat)
            @if(!isset($category) || $cat->id !== $category->id)
                <option value="{{ $cat->id }}"
                    {{ (old('parent_id', $category->parent_id ?? '') == $cat->id) ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endif
        @endforeach
    </select>
    @error('parent_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="image" class="form-label">Image</label>
    <input type="file" class="form-control @error('image') is-invalid @enderror"
           id="image" name="image" accept="image/*">
    @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if(isset($category) && $category->image)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $category->image) }}"
                 alt="Image de la catégorie" class="img-thumbnail" style="max-height: 100px;">
        </div>
    @endif
</div>

<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
        {{ old('is_active', isset($category) ? $category->is_active : true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Activer la catégorie</label>
</div>

<button type="submit" class="btn btn-primary">
    {{ isset($category) ? 'Mettre à jour' : 'Créer' }}
</button>
<a href="{{ route('admin.categories.index') }}" class="btn btn-light ms-2">Annuler</a>
