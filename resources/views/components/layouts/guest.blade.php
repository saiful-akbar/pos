<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="inventory dan pos system." />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="base-url" content="{{ url('/') }}" />

    {{-- Title --}}
    <title>{{ $title }} - {{ config('app.name') }}</title>

    {{-- Icon --}}
    <link rel="shortcut icon" href="{{ asset('assets/images/logo.svg') }}" type="image/x-icon" />

    {{-- Settings --}}
    <script src="{{ asset('assets/js/settings.js') }}"></script>

    {{-- CSS AND JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- style page --}}
    @isset($style)
        {{ $style }}
    @endisset
</head>

<body>
    <x-preloader></x-preloader>

    <div class="container-fluid">
        {{ $slot }}
    </div>

    {{-- Vendor JS --}}
    <script src="{{ asset('assets/js/vendor.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/theme.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/datatables.min.js') }}"></script>

    {{-- Custom script --}}
    <script>
        $(document).ready(function() {
            $('#preloader').fadeOut();
        });
    </script>

    {{-- page JS --}}
    @isset($script)
        {{ $script }}
    @endisset
</body>

</html>
