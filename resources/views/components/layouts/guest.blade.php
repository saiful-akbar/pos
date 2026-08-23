<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="inventory dan pos system." />

    {{-- Icon --}}
    <link rel="shortcut icon" href="{{ asset('assets/favicon/favicon.html') }}" type="image/x-icon" />

    {{-- Settings --}}
    <script src="{{ asset('assets/vendor/js/settings.js') }}"></script>

    {{-- CSS AND JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/libs.bundle.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme.bundle.css') }}" />

    {{-- Title --}}
    <title>{{ $title }} - {{ config('app.name') }}</title>
</head>

<body class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12" style="max-width: 25rem">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Vendor JS --}}
    <script src="{{ asset('assets/vendor/js/vendor.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/theme.bundle.js') }}"></script>
</body>

</html>
