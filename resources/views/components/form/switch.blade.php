@props([
    'name',
    'label',
    'value' => 1,
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'class' => '',
    'help' => null,
    'id' => null,
    'wrapperClass' => 'mb-3',
    'onText' => 'Oui',
    'offText' => 'Non',
])

@php
    $id = $id ?? $name . '_switch';
    $errorName = str_replace(['[', ']', '.'], ['.', '', '_'], $name);
    $hasError = $errors->has($errorName);
    $switchClass = 'form-check-input ' . ($hasError ? 'is-invalid ' : '') . $class;
    $switchClass = trim($switchClass);
    $checked = old($errorName, $checked) == $value;
@endphp

<div class="form-check form-switch {{ $wrapperClass }}">
    <input
        type="checkbox"
        role="switch"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        @if($checked) checked @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => $switchClass]) }}
    >

    <label class="form-check-label" for="{{ $id }}">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <div class="form-text d-flex align-items-center">
        <span class="me-2">{{ $offText }}</span>
        <div class="form-check form-switch m-0">
            <input type="checkbox" class="form-check-input" disabled>
        </div>
        <span class="ms-2">{{ $onText }}</span>
    </div>

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($errorName)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
