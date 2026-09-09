<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-read="{{ $access?->read }}" data-create="{{ $access?->create }}" data-update="{{ $access?->update }}" data-remove="{{ $access?->remove }}" data-delete="{{ $access?->delete }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="inventory dan pos system." />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="base-url" content="{{ url('/') }}" />

    {{-- Title --}}
    <title>{{ $title }} - {{ config('app.name') }}</title>
    
    {{-- Icon --}}
    @isset($setting?->store_logo)
        <link rel="shortcut icon" href="{{ storage_asset($setting?->store_logo) }}" type="image/x-icon" />
    @else
        <link rel="shortcut icon" href="{{ asset('assets/images/logo.svg') }}" type="image/x-icon" />
    @endisset
    
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
    <x-preloader></x-preloader>
    <x-layouts.sidenav-toolbar></x-layouts.sidenav-toolbar>
    <x-layouts.sidenav-small></x-layouts.sidenav-small>
    <x-layouts.sidenav-medium></x-layouts.sidenav-medium>
    <x-layouts.sidenav-base></x-layouts.sidenav-base>
    <x-layouts.topnav></x-layouts.topnav>

    <main class="main px-lg-6">
        <div class="container-lg">
            <div class="" id="alert">
                @session('alert')
                    <x-alert class="alert-{{ session('alert')['status'] }} border-{{ session('alert')['status'] }} mb-6">
                        {{ session('alert')['message'] }}
                    </x-alert>
                @endsession
    
                @if ($errors->any())
                    <x-alert class="alert-danger border-danger mb-6">
                        <ul class="m-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif
            </div>

            {{ $slot }}
        </div>
    </main>

    {{-- Form logout --}}
    <form action="{{ route('logout') }}" method="post" class="d-none" name="logout" id="logout">
        @csrf @method('delete')
    </form>

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
        $('.logout').click(function (e) { 
            e.preventDefault();
            $('form[name=logout]').submit();
        });

        $(document).ready(function() {
            $('#preloader').fadeOut();
        });
    </script>
</body>

</html>
