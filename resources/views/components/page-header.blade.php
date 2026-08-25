<div class="card shadow-sm">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="avatar avatar-xl rounded text-primary">
                    <span class="material-symbols-outlined fs-2 text-secondary">{{ $icon }}</span>
                </div>
    
            </div>
    
            <div class="col">
                <h1 class="fs-4 mb-0">{{ $title }}</h1>
            </div>

            @isset($action)
                <div class="col-12 col-sm-auto mt-4 mt-sm-0">
                    {{ $action }}
                </div>
            @endisset
        </div>
    </div>
</div>