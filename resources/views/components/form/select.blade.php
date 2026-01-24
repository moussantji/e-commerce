@props([
    'name',
    'label',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Sélectionnez une option',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'help' => null,
    'id' => null,
    'wrapperClass' => 'mb-4',
    'optionValueField' => 'id',
    'optionLabelField' => 'name',
    'hasEmptyOption' => true,
])

@php
    $id = $id ?? $name;
    $errorName = str_replace(['[', ']', '.'], ['.', '', '_'], $name);
    $hasError = $errors->has($errorName);
    $selectClass = 'form-select ' . ($hasError ? 'is-invalid ' : '') . $class;
    $selectClass = trim($selectClass);
    $selected = old($errorName, $selected);
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

    <select
        name="{{ $name }}"
        id="{{ $id }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => $selectClass]) }}
    >
        @if($hasEmptyOption || $placeholder)
            <option value="" @if(is_null($selected)) selected @endif disabled>
                {{ $placeholder }}
            </option>
        @endif

        @foreach($options as $key => $option)
            @php
                $value = is_array($option) ? $option[$optionValueField] : $option->{$optionValueField};
                $label = is_array($option) ? $option[$optionLabelField] : $option->{$optionLabelField};
                $isSelected = $selected == $value || (is_array($selected) && in_array($value, $selected));
            @endphp
            <option value="{{ $value }}" @if($isSelected) selected @endif>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($errorName)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
