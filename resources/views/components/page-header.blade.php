<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-xl rounded text-primary">
                            <span class="material-symbols-outlined fs-1 fw-bold text-secondary">{{ $icon }}</span>
                        </div>
                    </div>
            
                    <div class="col">
                        <h1 class="fs-4 mb-0">{{ $title }}</h1>
                    </div>

                    @isset($action)
                        <div class="col-12 col-lg-8 mt-5 mt-lg-0">
                            {{ $action }}
                        </div>
                    @endisset
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</div>