<div @class(['form-floating', 'is-invalid' => $error])>
    <input {{ $attributes->merge(['name' => $name, 'id' => $id])->class(['form-control', 'border' => !$error, 'is-invalid' => $error]) }}>
    <label for="{{ $id }}">{{ $label }}</label>
</div>

@isset($helperText)
    <div class="form-text">{{ $helperText }}</div>
@endisset

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror