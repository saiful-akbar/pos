@php
    $class = [
        'form-control',
        'is-invalid' => $error,
        'border' => !$error,
        'form-control-lg' => strtolower($size) === 'lg',
        'form-control-sm' => strtolower($size) === 'sm',
    ];

    $merge = [
        'required' => $required,
        'name' => $name,
        'id' => $id
    ];
@endphp

@if ($label)
<label for="{{ $id }}" class="form-label">
    {{ $label }} {{ $required ? '*' : '' }}
</label>
@endif

<input {{ $attributes->class($class)->merge($merge) }}>

@if ($helperText)
    <div class="form-text">{{ $helperText }}</div>
@endif

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror