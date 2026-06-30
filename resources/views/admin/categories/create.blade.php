@extends('admin.base')

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nouvelle catégorie</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0">Créer une nouvelle catégorie</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required maxlength="255" placeholder="Ex: Smartphones">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Catégorie parente</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">-- Catégorie racine --</option>
                            @foreach($categories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" {{ old('parent_id') == $parentCategory->id ? 'selected' : '' }}>
                                    {{ $parentCategory->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}" required maxlength="255">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">URL friendly (auto-généré si vide)</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ordre de tri</label>
                        <input type="number" name="sort_order" min="0" max="999"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', 0) }}" placeholder="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Plus petit = affiché en premier</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Description détaillée...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Image (optionnel)</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Recommandé: 300x300px, JPG/PNG</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bannière 1 (accueil mobile)</label>
                        <input type="file" name="banner_image_1" accept="image/*"
                               class="form-control @error('banner_image_1') is-invalid @enderror">
                        @error('banner_image_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Image de fond cliquable affichée dans l'app mobile</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bannière 2 (accueil mobile)</label>
                        <input type="file" name="banner_image_2" accept="image/*"
                               class="form-control @error('banner_image_2') is-invalid @enderror">
                        @error('banner_image_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Deuxième image de fond cliquable</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input"
                                   id="is_active" {{ old('is_active', true) ? 'checked' : '' }} value="1">
                            <label class="form-check-label" for="is_active">
                                Actif
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Créer la catégorie
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
