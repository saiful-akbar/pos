<div {{ $attributes->class(['m-0 alert']) }}>
    <div class="row align-items-center">
        <div class="col">
            {{ $slot }}
        </div>

        <div class="col-auto">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>