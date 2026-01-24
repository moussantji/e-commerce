@props([
    'name',
    'label',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'rows' => 3,
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
    $textareaClass = 'form-control ' . ($hasError ? 'is-invalid ' : '') . $class;
    $textareaClass = trim($textareaClass);
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

    <textarea
        name="{{ $name }}"
        id="{{ $id }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        {{ $attributes->merge(['class' => $textareaClass]) }}
    >{{ old($errorName, $value) }}</textarea>

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($errorName)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
