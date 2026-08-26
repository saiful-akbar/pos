<aside class="aside aside-base sidenav-base shadow-sm">
    <nav class="navbar navbar-expand-xl navbar-vertical">
        <div class="container-lg">
            <a class="navbar-brand d-flex align-items-center fs-5 fw-bold px-xl-3 mb-xl-4" href="{{ route('dashboard') }}">
                @isset($setting->store_logo)
                    <img src="{{ storage_asset($setting->store_logo) }}" alt="Logo" width="25" class="me-2">
                @else
                    <img src="{{ asset('assets/images/logo.svg') }}" alt="Logo" width="25" class="me-2">
                @endisset

                @isset($setting->appName)
                    <span>{{ $setting->appName }}</span>
                @else
                    <span>{{ config('app.name') }}</span>
                @endisset
            </a>

            {{-- User --}}
            <div class="d-flex ms-auto d-xl-none">
                <div class="dropdown my-n2">
                    <a class="btn btn-link d-inline-flex align-items-center dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar avatar-sm avatar-status avatar-status-success me-3">
                            <img class="avatar-img" src="{{ user_photo(user()->photo) }}" alt="{{ user()->full_name }}" />
                        </span>

                        <span class="d-none d-xl-block">{{ user()->full_name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('setting') }}">Pengaturan</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item logout" href="#">Keluar</a></li>
                    </ul>
                </div>
            </div>

            {{-- Toggler --}}
            <button class="navbar-toggler ms-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidenavBaseCollapse" aria-controls="sidenavBaseCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Collapse --}}
            <div class="collapse navbar-collapse" id="sidenavBaseCollapse">
                <nav class="navbar-nav nav-pills my-4 mt-xl-0">
                    <div class="nav-item">
                        <a class="nav-link fs-7 {{ active_path('/dashboard', true) ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <span class="material-symbols-outlined me-3">dashboard</span>
                            <span>Dashboard</span>
                        </a>
                    </div>

                    @foreach ($modules as $module)
                        <div class="nav-item">
                            <a class="nav-link {{ active_path($module->path) ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#{{ $module->id }}" role="button" aria-expanded="{{ active_path($module->path) ? 'true' : 'false' }}" aria-controls="{{ $module->id }}">
                                <span class="material-symbols-outlined me-3">{{ $module->icon }}</span>
                                <span>{{ $module->name }}</span>
                            </a>

                            <div class="collapse {{ active_path($module->path) ? 'show' : '' }}" id="{{ $module->id }}">
                                <nav class="nav nav-pills">
                                    @foreach ($module->details as $detail)
                                        <a class="nav-link {{ active_path($detail->path) ? 'active' : '' }}" href="{{ url($detail->path) }}">
                                            {{ $detail->name }}
                                        </a>
                                    @endforeach
                                </nav>
                            </div>
                        </div>
                    @endforeach
                </nav>

                {{-- Divider --}}
                <hr class="my-4 d-xl-none" />

                {{-- Nav --}}
                <nav class="navbar-nav nav-pills d-xl-none">
                    <div class="nav-item dropdown">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" data-bs-settings-switcher aria-expanded="false">
                            <span class="material-symbols-outlined me-3">settings</span>
                            <span>Pengaturan</span>
                        </a>

                        <div class="dropdown-menu shadow border">
                            <h6 class="dropdown-header">Tema</h6>
                            <a class="dropdown-item d-flex" data-bs-theme-value="light" href="#" role="button">
                                <span class="material-symbols-outlined me-2">light_mode</span>
                                <span>Terang</span>
                            </a>
                            <a class="dropdown-item d-flex" data-bs-theme-value="dark" href="#" role="button">
                                <span class="material-symbols-outlined me-2">dark_mode</span>
                                <span>Gelap</span>
                            </a>
                            <a class="dropdown-item d-flex" data-bs-theme-value="auto" href="#" role="button">
                                <span class="material-symbols-outlined me-2">contrast</span>
                                <span>Otomatis</span>
                            </a>

                            {{-- Navigation position --}}
                            <hr class="dropdown-divider" />
                            <h6 class="dropdown-header">Posisi Navigasi</h6>
                            <a class="dropdown-item d-flex" data-bs-navigation-position-value="sidenav" href="#" role="button">
                                <span class="material-symbols-outlined me-2">keyboard_tab_rtl</span>
                                <span>Samping</span>
                            </a>
                            <a class="dropdown-item d-flex" data-bs-navigation-position-value="topnav" href="#" role="button">
                                <span class="material-symbols-outlined me-2">vertical_align_top</span>
                                <span>Atas</span>
                            </a>

                            {{-- Sidenav sizing --}}
                            <div class="sidenav-sizing">
                                <hr class="dropdown-divider" />
                                <h6 class="dropdown-header">Ukuran navigasi</h6>
                                <a class="dropdown-item d-flex" data-bs-sidenav-sizing-value="base" href="#"role="button">
                                    <span class="material-symbols-outlined me-2">density_large</span>
                                    <span>Bawaan</span>
                                </a>
                                <a class="dropdown-item d-flex" data-bs-sidenav-sizing-value="md" href="#" role="button">
                                    <span class="material-symbols-outlined me-2">density_medium</span>
                                    <span>Sedang</span>
                                </a>
                                <a class="dropdown-item d-flex" data-bs-sidenav-sizing-value="sm" href="#" role="button">
                                    <span class="material-symbols-outlined me-2">density_small</span>
                                    <span>Kecil</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>

                {{-- Divider --}}
                <hr class="mt-4 d-xl-none" />
            </div>
        </div>
    </nav>
</aside>