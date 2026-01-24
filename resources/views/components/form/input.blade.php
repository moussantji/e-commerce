@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autofocus' => false,
    'class' => '',
    'help' => null,
    'id' => null,
    'wrapperClass' => 'mb-4',
])

@php
    $id = $id ?? $name;
    $placeholder = $placeholder ?? $label;
    $errorName = str_replace(['[', ']', '.'], ['.', '', '_'], $name);
    $hasError = $errors->has($errorName);
    $inputClass = 'form-control ' . ($hasError ? 'is-invalid ' : '') . $class;
    $inputClass = trim($inputClass);
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

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ old($errorName, $value) }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($autofocus) autofocus @endif
        {{ $attributes->merge(['class' => $inputClass]) }}
    >

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($errorName)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
