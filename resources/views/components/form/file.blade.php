@props([
    'name',
    'label',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'help' => null,
    'id' => null,
    'wrapperClass' => 'mb-4',
    'multiple' => false,
    'accept' => null,
    'preview' => false,
    'existingImages' => [],
])

@php
    $id = $id ?? $name;
    $errorName = str_replace(['[', ']', '.'], ['.', '', '_'], $name);
    $hasError = $errors->has($errorName);
    $fileClass = 'form-control ' . ($hasError ? 'is-invalid ' : '') . $class;
    $fileClass = trim($fileClass);
    $multiple = $multiple ? 'multiple' : '';
    $name = $multiple ? $name . '[]' : $name;
    $isImage = str_contains($accept ?? '', 'image/') || $accept === null;
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($isImage && ($preview || $existingImages))
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-2" id="{{ $id }}-preview">
                @foreach($existingImages as $image)
                    <div class="position-relative" style="width: 100px; height: 100px;">
                        <img src="{{ is_string($image) ? $image : $image->url }}"
                             class="img-thumbnail w-100 h-100"
                             style="object-fit: cover;">
                        <input type="hidden" name="existing_{{ $name }}" value="{{ is_string($image) ? basename($image) : $image->id }}">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-1 bg-white rounded-circle p-1"
                                onclick="this.closest('div').remove()" aria-label="Supprimer">
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="input-group">
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $id }}"
            {{ $multiple }}
            @if($accept) accept="{{ $accept }}" @endif
            @if($required && !$multiple) required @endif
            @if($disabled) disabled @endif
            data-preview="{{ $preview && $isImage ? 'true' : 'false' }}"
            {{ $attributes->merge(['class' => $fileClass]) }}
        >
        @if($isImage && $preview)
            <button class="btn btn-outline-secondary" type="button" id="{{ $id }}-clear">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($errorName)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

    @if($multiple)
        <div id="fileList" class="mt-2">
            <!-- Liste des fichiers sélectionnés sera affichée ici -->
        </div>

        @push('scripts')
            <script>
                document.getElementById('{{ $id }}').addEventListener('change', function(e) {
                    const fileList = document.getElementById('fileList');
                    fileList.innerHTML = '';

                    Array.from(this.files).forEach(file => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'badge bg-secondary me-1 mb-1';
                        fileItem.textContent = file.name;
                        fileList.appendChild(fileItem);
                    });
                });
            </script>
        @endpush
    @endif
</div>
