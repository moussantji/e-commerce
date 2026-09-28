{{-- Formulaire tag boutique — inclus par create / edit. Variables : $tag (nullable). --}}
<form action="{{ isset($tag) && $tag ? route('admin.tags.update', $tag) : route('admin.tags.store') }}"
    method="POST">
    @csrf
    @if (isset($tag) && $tag)
        @method('PUT')
    @endif

    <div class="field">
        <label for="name">Nom du tag *</label>
        <input class="ctrl" id="name" type="text" name="name" required
            value="{{ old('name', $tag->name ?? '') }}">
        @error('name') <span class="avis-err">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="description">Description</label>
        <textarea class="ctrl" id="description" name="description" rows="3" style="border-radius:12px;resize:vertical">{{ old('description', $tag->description ?? '') }}</textarea>
        @error('description') <span class="avis-err">{{ $message }}</span> @enderror
    </div>

    <div class="pdp-actions" style="margin-top:18px">
        <button class="btn-solid" type="submit"><svg class="ic">
                <use href="#i-b2-check" />
            </svg> Enregistrer</button>
        <a class="btn-line" href="{{ route('admin.tags.index') }}">Retour</a>
    </div>
</form>
