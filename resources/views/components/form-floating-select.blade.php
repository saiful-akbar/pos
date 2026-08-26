<div @class(['form-floating', 'is-invalid' => $error])>
    <select {{ $attributes->merge(['name' => $name, 'id' => $id])->class(['form-select', 'border', 'is-invalid' => $error]) }}>
        {{ $slot }}
    </select>
    
    <label for="{{ $id }}">{{ $label }}</label>
</div>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror