@php
    $class = [
        'form-select',
        'is-invalid' => $error,
    ];

    $merge = [
        'name' => $name,
        'id' => $id,
        'required' => $required,
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

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror