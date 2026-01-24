@props([
    'name',
    'label',
    'value',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'class' => '',
    'help' => null,
    'id' => null,
    'wrapperClass' => 'mb-3',
    'inline' => false,
])

@php
    $id = $id ?? $name . '_' . $value;
    $errorName = str_replace(['[', ']', '.'], ['.', '', '_'], $name);
    $hasError = $errors->has($errorName);
    $radioClass = 'form-check-input ' . ($hasError ? 'is-invalid ' : '') . $class;
    $radioClass = trim($radioClass);
    $checked = old($errorName, $checked) == $value;
    $wrapperClass = 'form-check ' . ($inline ? 'form-check-inline ' : '') . $wrapperClass;
@endphp

<div class="{{ $wrapperClass }}">
    <input
        type="radio"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        @if($checked) checked @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => $radioClass]) }}
    >

    <label class="form-check-label" for="{{ $id }}">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($errorName)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
