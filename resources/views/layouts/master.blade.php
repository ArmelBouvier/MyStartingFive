<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('css')
    <title>MS5</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @yield('scripts-header')
</head>
<body>
{{--Navbar--}}
<nav class="container navbar navbar-expand-md navbar-dark bg-primary shadow-sm">

    <div class="row">
        {{--Logo--}}
        <div class="col-md-4 w-75">
            <a href="{{ route('dashboard.index') }}">
                <img class="img-fluid" width="50%" src="{{asset('storage/images/Logo.png')}}" alt="logo">
            </a>
        </div>

        {{--Bouton responsive--}}
        <button class="navbar-toggler h-50" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Colonne droite -->
        <div class="col-md-8 collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Bouton de navigation -->
            <ul class="navbar-nav ml-auto">
                @guest
                <li class="nav-item active">
                    <a class="nav-link bouton-header"
                       href="{{ ('/tuto') }}">{{ __('Tuto') }}</a>
                </li>

                @else

                    <li class="nav-item active">
                        <a class="nav-link bouton-header"
                           href="{{ ('/tuto') }}">{{ __('Tuto') }}</a>
                    </li>

                <li class="nav-item active">
                    <a class="nav-link bouton-header" href="{{ route('leagues.index')}}">Leagues</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link bouton-header" href="{{ route('dashboard.index') }}">Tableau de bord</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link bouton-header" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">Déconnexion</a>
                    <form class="m-0" id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                    </form>
                </li>
                @endguest
            </ul>
        </div>
    </div>

</nav>

<div id="id">
    @yield('content')
</div>
{{--Footer--}}
<footer>
    <div class="container pt-5">
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


<script src="{{ asset('js/app.js') }}" defer></script>


@yield('script-footer')

</body>
</html>
