{{-- resources/views/admin/caracteristiques/form.blade.php --}}
@extends('admin.base')

@section('content')
    <div class="content">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    {{ isset($caracteristique) ? 'Modifier "' . $caracteristique->name . '"' : 'Ajouter' }} une
                    caractéristique
                </h4>
            </div>
            <div class="card-body">
                <form
                    action="{{ isset($caracteristique) ? route('admin.caracteristiques.update', $caracteristique) : route('admin.caracteristiques.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($caracteristique))
                        @method('PUT')
                    @endif

                    {{-- Nom + Type --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom de la caractéristique <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $caracteristique->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('type') is-invalid @enderror" id="type"
                                name="type" value="{{ old('type', $caracteristique->type ?? '') }}"
                                placeholder="ex: dimension, poids, couleur..." required>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Unité + Filtrable --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="unit" class="form-label">Unité</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit"
                                name="unit" value="{{ old('unit', $caracteristique->unit ?? '') }}"
                                placeholder="ex: kg, cm, L, px...">
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch pt-4">
                                <input class="form-check-input" type="checkbox" id="is_filterable" name="is_filterable"
                                    value="1"
                                    {{ old('is_filterable', $caracteristique->is_filterable ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_filterable">Filtrable dans les recherches</label>
                            </div>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.caracteristiques.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ isset($caracteristique) ? 'Modifier' : 'Ajouter' }} la caractéristique
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
