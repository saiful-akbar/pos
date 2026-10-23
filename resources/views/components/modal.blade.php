@php
    $merge = [
        // Klik di luar modal tidak menutup modal (berlaku untuk semua modal, termasuk yang dibuat tanpa opsi JS).
        'data-bs-backdrop' => 'static',
        'tabindex' => '-1',
        'aria-hidden' => 'true',
    ];

    $modalClass = [
        'modal',
        'fade',
    ];
    
    $dialogClass = [
        'modal-dialog',
        // Isi modal dapat di-scroll di dalam dialog (header dan footer tetap terlihat).
        'modal-dialog-scrollable',
        'modal-fullscreen-xxl-down' => $fullScreen === 'xl',
        'modal-fullscreen-xl-down' => $fullScreen === 'lg',
        'modal-fullscreen-lg-down' => $fullScreen === 'md',
        'modal-fullscreen-md-down' => $fullScreen === 'sm',
        'modal-fullscreen-sm-down' => $fullScreen === 'xs',
        'modal-xl' => $size === 'xl',
        'modal-lg' => $size === 'lg',
        'modal-sm' => $size === 'sm',
    ];
@endphp

<div {{ $attributes->merge($merge)->class($modalClass) }}>
    <div @class($dialogClass)>
        <div class="modal-content border">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ $title }}</h1>
                @isset($headerAction)
                    {{ $headerAction }}
                @else
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @endisset
            </div>

            <div class="modal-body">
                {{ $slot }}
            </div>
            
            <div class="modal-footer">
                <div class="w-100">
                    @isset($footer)
                        {{ $footer }}
                    @else
                        <div class="row justify-content-end">
                            <div class="col-6 col-lg-4 col-xl-3">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Tutup</span>
                                </button>
                            </div>
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>