<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>HD</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <script>
        window.locale= "{{ app()->getLocale() == config('app.fallback_locale') ? null : '/'.app()->getLocale()  }}";
    </script>
    <!-- Styles -->
    @include('layouts.css')
    @yield('css')
    <livewire:styles>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/hd-logo.png') }}" alt="">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                            @endif
                            {{-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('tracking.index') }}">Tracking</a>
                            </li> --}}
                        @else
                            <li class="nav-item">
                                <a class="btn btn-primary" href="{{ route('login') }}"> <i class="feather icon-home"></i> Dashboard</a>
                            </li>
                            <li class="new-item ml-1">
                                 <a href="{{ route('admin.home') }}" class="btn btn-primary">Go Back</a>
                            </li>
                            @can('viewAny', App\Models\Order::class)
                                {{-- <li class="nav-item">
                                    <a class="btn btn-primary ml-3" href="{{ route('admin.bulk-usps-label') }}">Buy USPS Label</a>
                                </li> --}}
                            @endcan
                        @endguest
                        {{-- <x-lang-switcher></x-lang-switcher> --}}
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    @include('layouts.js')
    
    @yield('jquery')

    <livewire:scripts>

</body>
</html>
