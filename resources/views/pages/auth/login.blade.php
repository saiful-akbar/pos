<x-layouts.guest title="login">
    <x-slot:style>
        <style>
            [data-bs-theme='light'] body {
                background-color: #fff;
            }

            [data-bs-theme='dark'] body {
                background-color: #17191c;
            }
            
            .login {
                min-height: 100dvh;
                width: 100dvw;
            }

            .login .login-cover {
                min-height: 100dvh;
                background-repeat: no-repeat;
                background-position: center;
                background-size: cover;
                overflow-x: hidden;
            }

            .login .login-form {
                min-height: 100dvh;
                display: flex;
                justify-content: center;
                flex-direction: column;
            }

            [data-bs-theme='light'] body .login-cover {
                background-image:
                    linear-gradient(to left,
                        rgba(255, 255, 255, 0),
                        rgba(255, 255, 255, 1)),
                    url("{{ storage_image(setting()?->backgroundImage, 'assets/images/background.jpg') }}");
            }

            [data-bs-theme='dark'] body .login-cover {
                background-image:
                    linear-gradient(to left,
                        rgba(23, 25, 28, 0),
                        rgba(23, 25, 28, 1)),
                    url("{{ storage_image(setting()?->backgroundImage, 'assets/images/background.jpg') }}");
            }
        </style>
    </x-slot:style>

    <div class="container-fluid bg-body login">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-12 col-md-6 col-xl-4 p-lg-11 p-7">
                <div class="row align-items-center justify-content-start gx-3">
                    <div class="col-auto">
                        <img src="{{ logo(setting()->storeLogo) }}" alt="Logo" class="rounded" width="100" height="100">
                    </div>

                    <div class="col">
                        <h1 class="fs-1 mb-1">{{ setting()->appName ?? config('app.name') }}</h1>
                        <p class="text-body-secondary">Kelola penjualan dan inventori toko Anda dalam satu sistem.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <form class="mt-7" method="POST" action="{{ route('login.store') }}" autocomplete="off">
                            @csrf @method('POST')
        
                            <div class="mb-5">
                                <div class="input-group @error('username')is-invalid @enderror">
                                    <span class="input-group-text material-symbols-outlined fs-6">person</span>
        
                                    <div class="form-floating">
                                        <input type="text" id="username" name="username"
                                            class="form-control @error('username')is-invalid @enderror" placeholder="Username"
                                            value="{{ old('username') }}" required autofocus>
        
                                        <label for="username">Username</label>
                                    </div>
                                </div>
        
                                @error('username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
        
                            <div class="mb-5">
                                <div class="input-group">
                                    <span class="input-group-text material-symbols-outlined fs-6">lock</span>
        
                                    <div class="form-floating @error('password')is-invalid @enderror">
                                        <input type="password" id="password" name="password"
                                            class="form-control @error('password')is-invalid @enderror" placeholder="password"
                                            required>
        
                                        <label for="password">Kata Sandi</label>
                                    </div>
                                </div>
        
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
        
                            <div class="mb-7">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="togglePassword">
                                    <label class="form-check-label" for="togglePassword">
                                        Tampilkan kata sandi
                                    </label>
                                </div>
                            </div>
        
                            <button type="submit" class="btn btn-secondary w-100">
                                <x-icon class="me-1">login</x-icon>
                                <span>Masuk</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-8 d-none d-md-flex login-cover"></div>
        </div>
    </div>

    <x-slot:script>
        <script>
            $('#togglePassword').change(function(e) {
                e.preventDefault();

                if ($(this).is(':checked')) {
                    $('#password').attr('type', 'text');
                } else {
                    $('#password').attr('type', 'password');
                }
            });
        </script>
    </x-slot:script>
</x-layouts.guest>
