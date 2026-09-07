@php
    $class = [
        'form-select',
        'is-invalid' => $error,
    ];

    $merge = [
        'name' => $name,
        'id' => $id,
        'required' => $required,
        'placeholder' => $label,
    ];
@endphp

<div @class(['form-floating', 'is-invalid' => $error])>
    <select {{ $attributes->merge($merge)->class($class) }}>{{ $slot }}</select>
    <label for="{{ $id }}">{{ $label }} {{ $required ? '*' : '' }}</label>
</div>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror