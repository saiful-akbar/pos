@php
    $class = [
        'btn',
        'btn-primary' => $color === 'primary' && !$outline,
        'btn-secondary' => $color === 'secondary' && !$outline,
        'btn-success' => $color === 'success' && !$outline,
        'btn-warning' => $color === 'warning' && !$outline,
        'btn-danger' => $color === 'danger' && !$outline,
        'btn-light' => $color === 'light' && !$outline,
        'btn-dark' => $color === 'dark' && !$outline,
        'btn-outline-primary border-primary' => $color === 'primary' && $outline,
        'btn-outline-secondary border-secondary' => $color === 'secondary' && $outline,
        'btn-outline-success border-success' => $color === 'success' && $outline,
        'btn-outline-warning border-warning' => $color === 'warning' && $outline,
        'btn-outline-danger border-danger' => $color === 'danger' && $outline,
        'btn-outline-light border-light' => $color === 'light' && $outline,
        'btn-outline-dark border-dark' => $color === 'dark' && $outline,
        'btn-lg' => $size === 'lg',
        'btn-sm' => $size === 'sm',
        'w-100' => $fullWidth
    ];

    $merge = [
        'type' => $type,
        'data-bs-toggle' => is_null($title) ? null : 'tooltip',
        'data-bs-title' => $title,
    ];
@endphp

@if ($type === 'link')
    <a {{ $attributes->class($class)->merge($merge) }}>
        @if ($startIcon) <x-icon class="me-1">{{ $startIcon }}</x-icon> @endif
        {{ $slot }}
        @if ($endIcon) <x-icon class="ms-1">{{ $endIcon }}</x-icon> @endif
    </a>
@else
    <button {{ $attributes->class($class)->merge($merge) }}>
        @if ($startIcon) <x-icon class="me-1">{{ $startIcon }}</x-icon> @endif
        {{ $slot }}
        @if ($endIcon) <x-icon class="ms-1">{{ $endIcon }}</x-icon> @endif
    </button>
@endif