{{-- Topnav (base) --}}
<nav class="navbar navbar-expand-xl topnav-base bg-body shadow-sm">
    <div class="container-lg">
        <a class="navbar-brand d-flex align-items-center fs-5 fw-bold" href="index-2.html">
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
                    <li><a class="dropdown-item logout" href="#">keluar</a></li>
                </ul>
            </div>
        </div>

        {{-- Toggler --}}
        <button class="navbar-toggler ms-3" type="button" data-bs-toggle="collapse" data-bs-target="#topnavBaseCollapse" aria-controls="topnavBaseCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapse --}}
        <div class="collapse navbar-collapse" id="topnavBaseCollapse">
            <nav class="navbar-nav nav-pills mx-auto mt-4 mt-xl-0">
                <div class="nav-item">
                    <a class="nav-link {{ active_path('/dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" role="button">
                        Dashboard
                    </a>
                </div>

                @foreach ($modules as $module)
                    <div class="nav-item dropdown ms-xl-2">
                        <a class="nav-link dropdown-toggle {{ active_path($module->path) }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $module->name }}
                        </a>
                        
                        <div class="dropdown-menu">
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
            <nav class="navbar-nav nav-pills">
                <div class="nav-item dropdown">
                    <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" data-bs-settings-switcher aria-expanded="false">
                        <span class="material-symbols-outlined">settings</span>
                        <span class="d-xl-none ms-3">Settings</span>
                    </a>

                    <div class="dropdown-menu border">
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
                
                {{-- Divider --}}
                <div class="vr align-self-center bg-dark mx-2 d-none d-xl-flex"></div>
                
                <div class="dropdown my-n2 d-none d-xl-flex">
                    <a class="btn btn-link d-inline-flex align-items-center dropdown-toggle" href="#"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar avatar-sm avatar-status avatar-status-success me-3">
                            <img class="avatar-img" src="{{ user_photo(user()->photo) }}" alt="{{ user()->full_name }}" />
                        </span>
                        <span class="d-none d-xl-block ms-xl-2">{{ user()->full_name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end border">
                        <li><a class="dropdown-item logout" href="#">Keluar</a></li>
                    </ul>
                </div>
            </nav>

            {{-- Divider --}}
            <hr class="mt-4 d-xl-none" />
        </div>
    </div>
</nav>

{{-- Topnav (toolbar) --}}
<nav class="navbar topnav-toolbar d-none d-xl-flex px-xl-6" id="topnavToolbar">
    <div class="container flex-column align-items-stretch">
        <div class="row align-items-center justify-content-center">
            <div class="col">
                <h6 class="h6" id="clock"></h6>
            </div>

            <div class="col-auto d-flex">
                <div class="dropdown my-n2">
                    <a class="btn btn-link d-inline-flex align-items-center dropdown-toggle" href="#"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar avatar-sm avatar-status avatar-status-success me-3">
                            <img class="avatar-img" src="{{ user_photo(user()->photo) }}" alt="{{ user()->full_name }}" />
                        </span>
                        <span class="d-none d-xl-block ms-xl-2">{{ user()->full_name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end border">
                        <li><a class="dropdown-item logout" href="#">Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    (() => {
        const navigationPosition = document.documentElement.getAttribute("data-bs-navigation-position");
        const topnavToolbar = document.querySelector('#topnavToolbar');

        if (navigationPosition === 'topnav') {
            topnavToolbar.classList.remove('d-xl-flex');
        }

        function updateClock() {
            const now = new Date();
            const day = now.getDate();
            const month = now.getMonth();
            const year = now.getFullYear();

            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();

            const months = [
                "Jan",
                "Feb",
                "Mar",
                "apr",
                "Mei",
                "Jun",
                "Jul",
                "Agu",
                "Sep",
                "Okt",
                "Nov",
                "Des",
            ];

            hours = hours < 10 ? `0${hours}` : hours;
            minutes = minutes < 10 ? `0${minutes}` : minutes;

            document.querySelector('#clock').textContent = `${day} ${months[month]} ${year}, ${hours}:${minutes}`;
        }

        updateClock();

        setInterval(updateClock, 1000);
    })();
</script>
