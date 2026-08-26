<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-read="{{ $access?->read }}" data-create="{{ $access?->create }}" data-update="{{ $access?->update }}" data-remove="{{ $access?->remove }}" data-delete="{{ $access?->delete }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="inventory dan pos system." />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="base-url" content="{{ url('/') }}" />

    @isset($setting?->store_logo)
        <link rel="shortcut icon" href="{{ storage_asset($setting?->store_logo) }}" type="image/x-icon" />
    @else
        <link rel="shortcut icon" href="{{ asset('assets/images/logo.svg') }}" type="image/x-icon" />
    @endisset

    <title>{{ $title }} - {{ config('app.name') }}</title>
    
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @isset($style)
        {{ $style }}
    @endisset
</head>

<body>
    <x-preloader></x-preloader>
    <x-layouts.sidenav-toolbar></x-layouts.sidenav-toolbar>
    <x-layouts.sidenav-small></x-layouts.sidenav-small>
    <x-layouts.sidenav-medium></x-layouts.sidenav-medium>
    <x-layouts.sidenav-base></x-layouts.sidenav-base>
    <x-layouts.topnav></x-layouts.topnav>

    <main class="main px-lg-6">
        <div class="container-lg">
            @session('alert')
                <div class="row mb-7">
                    <div class="col-12">
                        <div class="alert alert-{{ session('alert')['status'] }} border border-{{ session('alert')['status'] }} m-0">
                            {{ session('alert')['message'] }}
                        </div>
                    </div>
                </div>    
            @endsession

            {{ $slot }}
        </div>
    </main>

    <form action="{{ route('logout') }}" method="post" class="d-none" name="logout" id="logout">
        @csrf @method('delete')
    </form>

    <script src="{{ asset('assets/js/vendor.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/theme.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @isset($script)
        {{ $script }}
    @endisset

    <script>
        $(document).ready(function() {
            $('#preloader').fadeOut();
        });

        $('.logout').click(function (e) { 
            e.preventDefault();
            $('form[name=logout]').submit();
        });
    </script>
</body>

</html>
