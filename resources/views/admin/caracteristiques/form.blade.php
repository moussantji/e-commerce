@extends('admin.base')

@section('title', (isset($caracteristique) ? 'Modifier « ' . $caracteristique->name . ' »' : 'Ajouter une caractéristique'))

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.caracteristiques.index') }}">Caractéristiques</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ isset($caracteristique) ? $caracteristique->name : 'Nouvelle' }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>{{ isset($caracteristique) ? 'Modifier « ' . $caracteristique->name . ' »' : 'Ajouter une caractéristique' }}
            </h1>
            <p>Nom, type, unité et filtrabilité.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form
                    action="{{ isset($caracteristique) ? route('admin.caracteristiques.update', $caracteristique) : route('admin.caracteristiques.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($caracteristique))
                        @method('PUT')
                    @endif

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="name">Nom de la caractéristique *</label>
                            <input class="ctrl" id="name" type="text" name="name" required
                                value="{{ old('name', $caracteristique->name ?? '') }}">
                            @error('name') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="type">Type *</label>
                            <input class="ctrl" id="type" type="text" name="type" required
                                placeholder="ex : stockage, taille, couleur..."
                                value="{{ old('type', $caracteristique->type ?? '') }}">
                            @error('type') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="unite">Unité</label>
                            <input class="ctrl" id="unite" type="text" name="unite"
                                placeholder="ex : Go, kg, cm..." value="{{ old('unite', $caracteristique->unite ?? '') }}">
                            @error('unite') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0;justify-content:end">
                            <label class="switch"><input type="checkbox" id="is_filterable" name="is_filterable"
                                    value="1"
                                    {{ old('is_filterable', $caracteristique->is_filterable ?? false) ? 'checked' : '' }}>
                                Filtrable dans les recherches</label>
                        </div>
                    </div>

                    <div class="pdp-actions" style="margin-top:18px">
                        <button class="btn-solid" type="submit"><svg class="ic">
                                <use href="#i-b2-check" />
                            </svg> {{ isset($caracteristique) ? 'Modifier' : 'Ajouter' }}</button>
                        <a class="btn-line" href="{{ route('admin.caracteristiques.index') }}">Retour</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
