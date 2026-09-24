@php
    $merge = [
        'name' => $name,
        'id' => $id,
        'required' => $required,
        'placeholder' => $label,
        'data-invalid' => $name,
    ];

    $class = [
        'form-control',
        'is-invalid' => $error,
    ]
@endphp

<div @class(['form-floating', 'is-invalid' => $error]) data-invalid="{{ $name }}">
    @if ($type == 'textarea')
        <textarea {{ $attributes->merge($merge)->class($class) }}>{{ $value }}</textarea>
    @else
        <input value="{{ $value }}" type="{{ $type }}" {{ $attributes->merge($merge)->class($class) }}>
    @endif
    
    <label for="{{ $id }}">
        {{ $label }} @if($required) <span class="text-danger">*</span> @endif
    </label>
</div>

@isset($helperText)
    <div class="form-text">{{ $helperText }}</div>
@endisset

<div class="invalid-feedback" data-invalid-feedback="{{ $name }}">{{ $errors->first($name) }}</div>