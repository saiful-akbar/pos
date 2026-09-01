@php
    $merge = [
        'name' => $name,
        'id' => $id,
        'required' => $required,
    ];

    $class = [
        'form-control',
        'is-invalid' => $error,
    ]
@endphp

<div @class(['form-floating', 'is-invalid' => $error])>
    @if ($type == 'textarea')
        <textarea {{ $attributes->merge($merge)->class($class) }}>{{ $value }}</textarea>
    @else
        <input value="{{ $value }}" type="{{ $type }}" {{ $attributes->merge($merge)->class($class) }}>
    @endif
    
    <label for="{{ $id }}">{{ $label }} {{ $required ? '*' : '' }}</label>
</div>

@isset($helperText)
    <div class="form-text">{{ $helperText }}</div>
@endisset

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror