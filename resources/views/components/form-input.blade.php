@php
    $class = [
        'form-control',
        'border' => !$error,
        'is-invalid' => $error,
        'form-control-lg' => strtolower($size) === 'lg',
        'form-control-sm' => strtolower($size) === 'sm',
    ];

    $merge = [
        'required' => $required,
        'name' => $name,
        'id' => $id,
        'value' => $value,
        'data-invalid' => $name,
    ];
@endphp

@if ($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }} @if ($required) <span class="text-danger">*</span> @endif
    </label>
@endif

@if ($type == 'textarea')
    <textarea {{ $attributes->class($class)->merge($merge) }}>{{ $value }}</textarea>
@else
    <input type="{{ $type }}" {{ $attributes->class($class)->merge($merge) }}>
@endif

@if ($helperText)
    <div class="form-text">{{ $helperText }}</div>
@endif

<div class="invalid-feedback" data-invalid-feedback="{{ $name }}">
    {{ $errors->first($name) }}
</div>