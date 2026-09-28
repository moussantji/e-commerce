@extends('admin.base')

@section('title', (isset($brand) ? 'Modifier « ' . $brand->name . ' »' : 'Ajouter une marque'))

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.brands.index') }}">Marques</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ isset($brand) ? $brand->name : 'Nouvelle' }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>{{ isset($brand) ? 'Modifier « ' . $brand->name . ' »' : 'Ajouter une marque' }}</h1>
            <p>Nom, logo, site web et ordre d'affichage.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form
                    action="{{ isset($brand) ? route('admin.brands.update', $brand) : route('admin.brands.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($brand))
                        @method('PUT')
                    @endif

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="name">Nom de la marque *</label>
                            <input class="ctrl" id="name" type="text" name="name" required
                                value="{{ old('name', $brand->name ?? '') }}">
                            @error('name') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="slug">Slug</label>
                            <input class="ctrl" id="slug" type="text" name="slug"
                                value="{{ old('slug', $brand->slug ?? '') }}" placeholder="auto depuis le nom">
                            @error('slug') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="website">Site web</label>
                            <input class="ctrl" id="website" type="url" name="website"
                                value="{{ old('website', $brand->website ?? '') }}"
                                placeholder="https://example.com">
                            @error('website') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="sort_order">Ordre d'affichage</label>
                            <input class="ctrl" id="sort_order" type="number" name="sort_order" min="0"
                                value="{{ old('sort_order', $brand->sort_order ?? 0) }}">
                            @error('sort_order') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="logo">Logo</label>
                            @if (isset($brand) && $brand->getPhoto())
                                <div style="margin-bottom:10px">
                                    <img src="{{ $brand->getPhoto()->getImageUrl(80, 80) }}" alt="{{ $brand->name }}"
                                        style="max-height:50px;border-radius:10px;border:1px solid var(--line)">
                                </div>
                                <label class="switch" style="margin-bottom:10px"><input type="checkbox"
                                        id="remove_logo" name="remove_logo" value="1"> Supprimer le logo</label>
                            @endif
                            <input class="ctrl" id="logo" type="file" name="logo" accept="image/*">
                            @error('logo') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0;justify-content:end">
                            <label class="switch"><input type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', $brand->is_active ?? true) ? 'checked' : '' }}> Marque
                                active</label>
                        </div>
                    </div>

                    <div class="field">
                        <label for="description">Description</label>
                        <textarea class="ctrl" id="description" name="description" rows="3" style="border-radius:12px;resize:vertical">{{ old('description', $brand->description ?? '') }}</textarea>
                        @error('description') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="pdp-actions" style="margin-top:18px">
                        <button class="btn-solid" type="submit"><svg class="ic">
                                <use href="#i-b2-check" />
                            </svg> {{ isset($brand) ? 'Modifier' : 'Ajouter' }} la marque</button>
                        <a class="btn-line" href="{{ route('admin.brands.index') }}">Retour</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var name = document.getElementById('name');
            var slug = document.getElementById('slug');
            if (name && slug) {
                name.addEventListener('input', function() {
                    if (!slug.value) {
                        slug.value = name.value.toLowerCase().trim().replace(/[^\w\s-]/g, '')
                            .replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '');
                    }
                });
            }
        });
    </script>
@endpush
