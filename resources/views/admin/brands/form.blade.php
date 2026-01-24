@extends('admin.base')

@section('content')
    <div class="content">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    {{ isset($brand) ? 'Modifier "' . $brand->name . '"' : 'Ajouter' }} une marque
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ isset($brand) ? route('admin.brands.update', $brand) : route('admin.brands.store') }}"
                    method="POST" enctype="multipart/form-data">

                    @csrf
                    @if (isset($brand))
                        @method('PUT')
                    @endif

                    {{-- Nom + Slug --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom de la marque <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $brand->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                                name="slug" value="{{ old('slug', $brand->slug ?? '') }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Site web + Ordre --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="website" class="form-label">Site web</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website"
                                name="website" value="{{ old('website', $brand->website ?? '') }}"
                                placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="sort_order" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                id="sort_order" name="sort_order" min="0"
                                value="{{ old('sort_order', $brand->sort_order ?? 0) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Logo + Statut --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo"
                                name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Logo actuel en édition --}}
                            @if (isset($brand) && $brand->getPhoto())
                                <div class="mt-2">
                                    <img src="{{ $brand->getPhoto()->getImageUrl(80,80) }}" alt="{{ $brand->name }}"
                                        style="max-height: 50px; max-width: 100px; object-fit: cover;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo"
                                            value="1">
                                        <label class="form-check-label" for="remove_logo">Supprimer le logo</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $brand->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Marque active</label>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="4">
                        {{ old('description', $brand->description ?? '') }}
                    </textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ isset($brand) ? 'Modifier' : 'Ajouter' }} la marque
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-générer slug depuis nom
                document.getElementById('name').addEventListener('input', function() {
                    const name = this.value;
                    const slug = document.getElementById('slug');
                    if (!slug.value) {
                        slug.value = name
                            .toLowerCase()
                            .trim()
                            .replace(/[^\w\s-]/g, '')
                            .replace(/[\s_-]+/g, '-')
                            .replace(/^-+|-+$/g, '');
                    }
                });
            });
        </script>
    @endpush
@endsection
