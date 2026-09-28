@extends('admin.base')

@section('title', ($banner->exists ? 'Modifier la bannière' : 'Nouvelle bannière'))

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.banners.index') }}">Bannières</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ $banner->exists ? 'Modifier' : 'Nouvelle' }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>{{ $banner->exists ? 'Modifier la bannière' : 'Nouvelle bannière' }}</h1>
            <p>Titres, promotion, lien et visuel.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form
                    action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($banner->exists)
                        @method('PUT')
                    @endif

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="bn-t1">Titre principal *</label>
                            <input class="ctrl" id="bn-t1" type="text" name="title1" required maxlength="255"
                                value="{{ old('title1', $banner->title1 ?? '') }}">
                            @error('title1') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="bn-t2">Titre secondaire</label>
                            <input class="ctrl" id="bn-t2" type="text" name="title2" maxlength="255"
                                value="{{ old('title2', $banner->title2 ?? '') }}">
                            @error('title2') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="bn-pct">Pourcentage promotionnel (%)</label>
                            <input class="ctrl" id="bn-pct" type="number" name="percentage" step="0.01"
                                min="0" max="100" value="{{ old('percentage', $banner->percentage ?? '') }}">
                            @error('percentage') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="bn-link">Lien du bouton</label>
                            <input class="ctrl" id="bn-link" type="url" name="button_link"
                                value="{{ old('button_link', $banner->button_link ?? '') }}"
                                placeholder="https://...">
                            @error('button_link') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label>Visuel</label>
                        @if ($banner->exists && $banner->getPhoto())
                            <div style="margin-bottom:12px;position:relative;display:inline-block">
                                <img src="{{ $banner->getPhoto()->getImageUrl(530, 530) }}" alt="Bannière"
                                    style="max-width:100%;max-height:140px;object-fit:cover;border-radius:12px;border:1px solid var(--line)">
                            </div>
                        @endif
                        <input class="ctrl" type="file" name="image" accept="image/*">
                        @error('image') <span class="avis-err">{{ $message }}</span> @enderror
                        <span class="muted-sm">{{ $banner->exists ? "Laisser vide pour garder l'image actuelle." : 'JPG, PNG, WebP — 2 Mo max. Recommandé : 1920x600px.' }}</span>
                    </div>

                    <div class="field">
                        <label class="switch"><input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}> Bannière
                            active</label>
                    </div>

                    <div class="pdp-actions" style="margin-top:18px">
                        <button class="btn-solid" type="submit"><svg class="ic">
                                <use href="#i-b2-check" />
                            </svg> {{ $banner->exists ? 'Modifier la bannière' : 'Créer la bannière' }}</button>
                        <a class="btn-line" href="{{ route('admin.banners.index') }}">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
