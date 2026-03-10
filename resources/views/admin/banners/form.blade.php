@extends('admin.base')

@section('content')
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $banner->exists ? 'Modifier la bannière' : 'Nouvelle bannière' }}</h1>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    {{ $banner->exists ? 'Modifier la bannière #' . $banner->id : 'Créer une nouvelle bannière' }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($banner->exists)
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Titre principal</label>
                            <input type="text" name="title1" class="form-control @error('title1') is-invalid @enderror"
                                value="{{ old('title1', $banner->title1 ?? '') }}" required maxlength="255">
                            @error('title1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Titre secondaire</label>
                            <input type="text" name="title2" class="form-control @error('title2') is-invalid @enderror"
                                value="{{ old('title2', $banner->title2 ?? '') }}" maxlength="255">
                            @error('title2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pourcentage promotionnel</label>
                            <div class="input-group">
                                <input type="number" name="percentage" step="0.01" min="0" max="100"
                                    class="form-control @error('percentage') is-invalid @enderror"
                                    value="{{ old('percentage', $banner->percentage ?? '') }}">
                                <span class="input-group-text">%</span>
                            </div>
                            @error('percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lien du bouton</label>
                            <input type="url" name="button_link"
                                class="form-control @error('button_link') is-invalid @enderror"
                                value="{{ old('button_link', $banner->button_link ?? '') }}">
                            @error('button_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Aperçu image actuelle --}}
                    @if ($banner->exists && $banner->getPhoto())
                        <div class="mb-4 photo-container">
                            <label class="form-label">Image actuelle :</label><br>
                            <div class="position-relative d-inline-block me-3 mb-2 "
                                hx-target="closest .photo-container" hx-swap="outerHTML">
                                <img src="{{ $banner->getPhoto()->getImageUrl(530, 530) }}" alt="Bannière"
                                    style="max-width: 400px; max-height: 120px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                @php
                                    $photo = $banner->getPhoto();
                                @endphp

                                {{-- ✅ BOUTON SUPPRIMER --}}
                                <button hx-delete="/admin/photo/{{ $photo->id }}"
                                    hx-confirm="Supprimer cette image définitivement ?" hx-swap="delete"
                                    class="position-absolute top-0 end-0 btn btn-sm btn-outline-danger border-0 rounded-0"
                                    style="right: -8px; top: -8px;" onclick="confirmDeletePhoto({{ $banner->id }})"
                                    title="Supprimer cette image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif


                    <div class="mb-4">
                        <label class="form-label">Image {{ $banner->exists ? '(optionnel)' : '' }}</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                            accept="image/*">
                        <div class="form-text">
                            {{ $banner->exists ? 'Laisser vide pour conserver l\'image actuelle' : 'Formats JPG, PNG, WebP (Max 2Mo)' }}
                            @if (!$banner->exists)
                                - Recommandé : 1920x600px
                            @endif
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }} value="1">
                            <label class="form-check-label" for="is_active">Actif</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i>
                            {{ $banner->exists ? 'Modifier la bannière' : 'Créer la bannière' }}
                        </button>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
