<aside class="aside aside-sm sidenav-sm shadow-sm">
    <nav class="navbar navbar-expand-xl navbar-vertical">
        <div class="container-lg">
            <a class="navbar-brand fs-5 fw-bold text-xl-center mb-xl-4" href="{{ route('dashboard') }}">
                @empty($setting->store_logo)
                    <img src="{{ asset('assets/images/logo.svg') }}" alt="Logo" width="25">
                @else
                    <img src="{{ storage_asset($setting->store_logo) }}" alt="Logo" width="25">
                @endempty
                
                @empty($setting->appName)
                    <span class="d-xl-none ms-1">{{ config('app.name') }}</span>
                @else
                    <span class="d-xl-none ms-1">{{ $setting->appName }}</span>
                @endempty
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

                    <ul class="dropdown-menu dropdown-menu-end border">
                        <li><a class="dropdown-item logout" href="#">Keluar</a></li>
                    </ul>
                </div>
            </div>

            {{-- Toggler --}}
            <button class="navbar-toggler ms-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidenavSmallCollapse" aria-controls="sidenavSmallCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Collapse --}}
            <div class="collapse navbar-collapse" id="sidenavSmallCollapse">
                <nav class="navbar-nav nav-pills mt-4">
                    <div class="nav-item">
                        <a class="nav-link {{ active_path('/dashboard', true) ? 'active' : '' }}" href="{{ route('dashboard') }}" role="button">
                            <span class="material-symbols-outlined">space_dashboard</span>
                            <span class="ms-3 d-xl-none">Dashboard</span>
                        </a>
                    </div>
                    
                    @foreach ($modules as $module)
                        <div class="nav-item dropend">
                            <a class="nav-link flex-xl-column {{ active_path($module->path) }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                                <span class="material-symbols-outlined">{{ $module->icon }}</span>
                                <span class="ms-3 d-xl-none">{{ $module->name }}</span>
                            </a>

                            <div class="dropdown-menu ms-xl-3">
                                <h6 class="dropdown-header d-none d-xl-block">{{ $module->name }}</h6>
                                @foreach ($module->details as $detail)
                                    <a class="dropdown-item {{ active_path($detail->path) }}" href="{{ url($detail->path) }}">
                                        {{ $detail->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>

                {{-- Divider --}}
                <hr class="my-4 d-xl-none" />

                {{-- Nav --}}
                <nav class="navbar-nav nav-pills mt-auto">
                    <div class="nav-item dropend">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" data-bs-settings-switcher aria-expanded="false">
                            <span class="material-symbols-outlined">settings</span>
                            <span class="d-xl-none ms-3">Settings</span>
                        </a>

                        <div class="dropdown-menu top-auto bottom-0 ms-xl-3 border">
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
                                <h6 class="dropdown-header">Ukuran Navigasi</h6>
                                <a class="dropdown-item d-flex" data-bs-sidenav-sizing-value="base" href="#" role="button">
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