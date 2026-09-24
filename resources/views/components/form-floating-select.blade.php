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
        'data-invalid' => $name,
    ];
@endphp

<div @class(['form-floating', 'is-invalid' => $error]) data-invalid="{{ $name }}">
    <select {{ $attributes->merge($merge)->class($class) }}>
        {{ $slot }}
    </select>
    
    <label for="{{ $id }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
</div>

<div class="invalid-feedback" data-invalid-feedback="{{ $name }}">{{ $errors->first($name) }}</div>