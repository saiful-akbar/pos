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
        <link rel="shortcut icon" href="{{ logo($setting?->storeLogo) }}" type="image/x-icon" />
        
        {{-- Settings JS --}}
        <script src="{{ asset('assets/js/settings.js') }}"></script>
        
        {{-- Vite --}}
        @vite(['resources/css/app.css'])

        {{-- Page CSS --}}
        @isset($style)
            {{ $style }}
        @endisset
    </head>

    <body>
        <main class="main">
            <div class="container-lg">
                {{ $slot }}
            </div>
        </main>

        {{-- Vendor JS --}}
        <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/datatables/datatables.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/bootbox/bootbox.all.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/theme.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/app.js') }}"></script>

        {{-- Pages script --}}
        @isset($script)
            {{ $script }}
        @endisset

        {{-- Global script --}}
        <script>
            $(document).ready(function() {
                $('#preloader').fadeOut();
            });
        </script>
    </body>
</html>