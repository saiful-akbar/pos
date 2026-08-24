<x-layouts.guest title="login">
    <x-slot:style>
        <style>
            #cover {
                background-image: linear-gradient(to left, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1)), url("{{ asset('assets/images/login_cover.webp') }}");
                background-repeat: no-repeat;
                background-position: center;
                background-size: cover;
                height: 100dvh;
            }

            html[data-bs-theme="dark"] #cover {
                background-image: linear-gradient(to left, rgba(23, 25, 28, 0), rgba(23, 25, 28, 1)), url("{{ asset('assets/images/login_cover.webp') }}");
                background-repeat: no-repeat;
                background-position: center;
                background-size: cover;
                height: 100dvh;
            }
        </style>
    </x-slot:style>

    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-12 col-md-6 col-lg-4 p-lg-11 p-7">
            <h1 class="fs-1">{{ config('app.name') }}</h1>
            <p class="lead text-body-secondary">Kelola penjualan dan inventori toko Anda dalam satu sistem.</p>

            <form class="mt-7" method="POST" action="{{ route('login.store') }}" autocomplete="off">
                @csrf @method('POST')

                <div class="mb-5">
                    <div class="input-group @error('username')is-invalid @enderror">
                        <span class="input-group-text bi-person-circle"></span>

                        <div class="form-floating">
                            <input type="text" id="username" name="username"
                                class="form-control @error('username')is-invalid @enderror" placeholder="Username"
                                value="{{ old('username') }}" required>

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
                        <span class="input-group-text bi bi-unlock2"></span>

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
                    Masuk
                </button>
            </form>
        </div>

        <div id="cover" class="col-md-6 col-lg-8 d-none d-md-flex"></div>
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
