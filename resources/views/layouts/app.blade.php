<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MS5</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>
<body>
<div id="app">
    {{--Navbar--}}
    <nav class="navbar navbar-expand-md bg-primary shadow-sm">
        <div class="container">
            <span class="w-50">
                <a href="{{ url('/') }}">
                    <img class="logo-header" src="{{asset('storage/images/Logo.png')}}" alt="logo">
                </a>
            </span>

            <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-expanded="false" aria-controls="navbarSupportedContent"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto d-flex justify-content-around">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item active">
                            <a class="nav-link bouton-connexion" href="{{ route('login') }}">{{ __('Connexion') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item active ml-2">
                                <a class="nav-link bouton-inscription" href="{{ route('register') }}">{{ __('Inscription') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    {{--Footer--}}
    <footer>
        <div class="container">
            <div class="row">

                <div class="col-12 d-flex justify-content-center p-1">

                    <img class="logo-footer" src="{{asset('storage/images/logo_footer.png')}}" alt="logo">

                </div>
            </div>
            <div class="container-fluid py-4">
                <div class="row">
                    <p><a class="text-white" href="mailto:MyStarting5fr@gmail.com">Contact</a></p>
                    <p><a class="text-white ml-3" href="#">Mentions Légales</a></p>

                </div>

                <hr class="white">
            </div>

        </div>

    </footer>
</div>
</body>
</html>
