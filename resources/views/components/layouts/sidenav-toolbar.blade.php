<aside class="aside aside-sm sidenav-toolbar d-none d-xl-flex">
    <nav class="navbar navbar-expand-xl navbar-vertical">
        <div class="container-fluid">
            <nav class="navbar-nav nav-pills h-100">
                <div class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Home">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="material-symbols-outlined">home</span>
                    </a>
                </div>

                <div class="nav-item dropend mt-auto">
                    <a href="#" role="button" data-bs-toggle="dropdown" data-bs-settings-switcher aria-expanded="false">
                        <div class="nav-link">
                            <span class="material-symbols-outlined">settings</span>
                        </div>
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
                        <a class="dropdown-item d-flex" data-bs-navigation-position-value="topnav"
                            href="#" role="button">
                            <span class="material-symbols-outlined me-2">vertical_align_top</span>
                            <span>Atas</span>
                        </a>

                        {{-- Sidenav sizing --}}
                        <div class="sidenav-sizing">
                            <hr class="dropdown-divider" />
                            <h6 class="dropdown-header">Ukuran Naigasi</h6>
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
        </div>
    </nav>
</aside>