@php
    $class = [
        'form-select',
        'is-invalid' => $error,
        'form-control-lg' => strtolower($size) === 'lg',
        'form-control-sm' => strtolower($size) === 'sm',
    ];

    $merge = [
        'name' => $name,
        'id' => $id,
        'required' => $required,
        'data-invalid' => $name
    ];
@endphp

@if ($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }} <span class="text-danger">{{ $required ? '*' : '' }}</span>
    </label>
@endif

<select {{ $attributes->merge($merge)->class($class) }}>
    {{ $slot }}
</select>

<div class="invalid-feedback" data-invalid-feedback="{{ $name }}">
    {{ $errors->first($name) }}
</div>