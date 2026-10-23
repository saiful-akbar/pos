<x-layouts.auth title="Pengaturan">
    <x-page-header title="Pengaturan" icon="settings">
        @if ($access->update)
            <x-slot:action>
                <div class="row justify-content-end align-items-center">
                    <div class="col-12 col-lg-6 col-xl-3">
                        <a href="{{ route('settings.applications.edit') }}" class="btn btn-secondary w-100" role="button">
                            <x-icon class="me-1">edit</x-icon>
                            <span>Edit</span>
                        </a>
                    </div>
                </div>
            </x-slot:action>
        @endif
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12 col-lg-3 mb-6">
            <div class="card shadow-sm position-sticky" style="top: 10px">
                <div class="card-body p-3">
                    <nav class="nav nav-pills flex-column" id="settingNav">
                        <a class="nav-link" href="#store">Toko</a>
                        <hr class="m-2">
                        <a class="nav-link" href="#application">Aplikasi</a>
                        <hr class="m-2">
                        <a class="nav-link" href="#backgroundImage">Gambar</a>
                    </nav>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-9" data-bs-spy="scroll" data-bs-target="#settingNav" data-bs-smooth-scroll="true"
            tabindex="0">
            <section class="card shadow-sm mb-6" id="store">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar avatar-xl">
                                <img class="avatar-img" src="{{ storage_image($setting?->storeLogo) }}"
                                    alt="Logo" />
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="fs-5 mb-0">{{ $setting?->storeName }}</h2>
                            <div class="text-body-secondary">Nama dan alamat store ini akan dimasukan dalam struk.</div>
                        </div>
                    </div>

                    <hr />

                    <div class="mb-4">
                        <div class="form-label text-body-secondary">Alamat</div>
                        <div>{{ $setting?->storeAddress }}</div>
                    </div>
                </div>
            </section>

            <!-- Application -->
            <section class="card mb-6 shadow-sm" id="application">
                <div class="card-body">
                    <h2 class="fs-5 mb-1">Aplikasi</h2>
                    <p class="text-body-secondary">Informasi mengenai aplikasi</p>

                    <hr>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">Nama</div>
                                <div class="col text-end text-body-secondary">{{ $setting?->appName }}</div>
                            </div>
                        </li>

                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">Versi</div>
                                <div class="col text-end text-body-secondary">{{ $setting?->appVersion }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Background image -->
            <section class="card shadow-sm" id="application">
                <div class="card-body">
                    <h2 class="fs-5 mb-1">Gambar latar belakang</h2>
                    <p class="text-body-secondary">Gambar ini akan ditampilkan sebagai latar belakang aplikasi</p>

                    <hr>

                    <img src="{{ storage_image($setting?->backgroundImage) }}" alt="Background" width="200"
                        height="200" class="img-fluid rounded">
                </div>
            </section>
        </div>
    </div>
</x-layouts.auth>
