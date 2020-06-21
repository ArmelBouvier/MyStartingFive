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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">


</head>
<body>

<div id="app">
    {{--Header--}}
    <header class="container-fluid p-0">
        <div class="row no-gutters">
            <div class="col-md-12">
                <div class="banner justify-content-center h-100 w-100">
                    <div class="container">
                        {{--Réseaux Sociaux--}}
                        <div class="row no-gutters justify-content-end py-5">
                            <a class="text-dark" target="_blank" href="https://www.instagram.com/nbafra/"><i class="fab fa-instagram fa-2x icon text-center pt-1 m-2"></i></a>
                            <a class="text-dark" target="_blank" href="https://www.facebook.com/NBAFrance"><i class="fab fa-facebook fa-2x icon text-center pt-1 m-2"></i></a>
                            <a class="text-dark" target="_blank" href="https://twitter.com/starting5fr"><i class="fab fa-twitter fa-2x icon text-center pt-1 m-2"></i></a>
                        </div>
                        {{--Logo Header--}}
                        <div class="row no-gutters justify-content-center py-4">
                            <div class="col-md-12">
                                <img class="img-fluid mt-5" src="{{asset('storage/images/Logo.png')}}" alt="logo">
                            </div>
                        </div>
                        {{--Bouton--}}
                        <div class="row no-gutters justify-content-center py-5">
                            <li class="nav-item" >
                                <a class="bouton-connexion"
                                   href="{{ route('login') }}">{{ __('Connexion') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item ml-2"style="list-style: none;">
                                    <a class="bouton-inscription"
                                       href="{{ route('register') }}">{{ __('Inscription') }}</a>
                                </li>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="">
        @yield('content')
    </main>
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
                    <p><a class="text-white ml-3" href="{{'mentions_legales'}}">Mentions Légales</a></p>

                </div>

                <hr class="white">
            </div>

        </div>

    </footer>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
</div>
</body>
</html>
