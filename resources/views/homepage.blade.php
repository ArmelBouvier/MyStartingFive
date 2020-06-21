@extends('layouts.home')

@section('content')


    <div class="container-fluid p-0">
        {{--Règles--}}
        <div class="row no-gutters">
            <div class="col-md-6">
                <img class="w-100" src="{{asset('storage/images/image_tutos.jpg')}}" alt="photo">
            </div>
            <div class="col-md-6 d-flex justify-content-center flex-column  pl-5">
                <h4 class="quaternary p-2">Les tutos de M. MVP</h4>
                <h1 class="pl-2 text-white">D'ACCORD, <br> MAIS ÇA FONCTIONNE COMMENT ?</h1>
                <h4 class="quaternary p-2">° Découvres les règles en 5 minutes chrono</h4>
                <p class="pl-2 text-white">MyStarting5 est une fantasy league basée sur la NBA. <br>
                    Retrouves tous tes joueurs favoris, recrutes les pour former ta propre
                    <b class="quaternary">DREAM TEAM</b>
                    et défis/es la team de tes potes ! <br>
                    Les stats réels des joueurs sont prisent en compte pour calculer le score des match alors fais
                    les bons choix ! <br>
                    Une lutte sans merci commence jusqu'au titre de champion de <b class="quaternary">MS5</b>.</p>
            </div>
        </div>
    </div>

    {{--Infos--}}
    <div class="container-fluid bg-light pt-3">

        <div class="row no-gutters">
            <div class="col-md-12 text-center pt-5">
                <h2 class="text-dark">Crée ta ligue, invites tes potes <b class="quaternary">et marche leur dessus !</b></h2>
            </div>
        </div>

        <hr>

        <div class="container">
            <div class="row no-gutters">
                <div class="col-md-3 text-center p-3">
                    <img src="{{asset('storage/images/picto_joueur.png')}}" alt="joueur">
                    <h5 class="py-2"><b>+ de <span class="quaternary">5000</span> joueurs MS5</b></h5>
                    <p>Une communauté de plus en plus grande se forme, <b class="quaternary">rejoins-nous</b>
                        pour défier tes amis/es et prouver que tu es le <b class="quaternary">meilleur coach</b>. <br>
                        Plus on est de fou, plus on rit !</p>
                </div>
                <div class="col-md-3 text-center p-3">
                    <img src="{{asset('storage/images/picto_league.png')}}" alt="panier">
                    <h5 class="py-2"><b><span class="quaternary">125</span> ligues crées</b></h5>
                    <p>Deux systèmes s'offre à toi. Les <b class="quaternary">ligues publiques</b> pour jouer contre
                        la communauté MS5, les <b class="quaternary">ligues privées</b> pour défier des joueurs
                        aléatoires.</p>
                </div>
                <div class="col-md-3 text-center p-3">
                    <img src="{{asset('storage/images/picto_teams.png')}}" alt="maillot">
                    <h5 class="py-2"><b><span class="quaternary">800</span> équipes crées</b></h5>
                    <p>Du rapide <b class="quaternary">meneur</b> en passant par les adroits
                        <b class="quaternary">arrière</b>pour finir par les puissants
                        <b class="quaternary">pivots</b> composes ton
                        <b class="quaternary">roster de rêve</b> pour dominer ta ligue et écraser tout le monde !</p>
                </div>
                <div class="col-md-3 text-center p-3">
                    <img style="height: 75px; width: 75px;" src="{{asset('storage/images/picto_matchs.png')}}"
                         alt="terrain">
                    <h5 class="py-2"><b><span class="quaternary">10 000</span> matchs joués</b></h5>
                    <p>Tous les matchs de la <b class="quaternary">NBA</b> diputés sont récupérer, les
                        <b class="quaternary">stats</b> de chaques match sont utilisées pour
                        <b class="quaternary">calculer</b> le résultat des rencontres. </p>
                </div>

            </div>
            <div class="row no-gutters justify-content-center p-5">
                <a class="nav-link bouton-inscription" href="{{ route('register') }}">{{ __('Rejoins-nous !') }}</a>
            </div>

        </div>

    </div>

    {{--Tuto--}}
    <div class="container-fluid bg-jersey tutoimg ">
        <div class="row h-100 align-items-md-center">
            <div class="col-md-6 offset-md-6">
                <p class="quaternary py-2"><b>Les Tuto de Mr.MVP</b></p>
                <h1 class="text-white py-2">Créer une équipe équilibrée</h1>
                <p class="text-white py-2 overflow-auto w-50">Suis les conseils de <b class="quaternary">Mr.MVP</b>
                    à la lettre pour être sur d'avoir toutes les cartes en main pour <b class="quaternary">écraser</b>
                    tes adversaires et rouler jusqu'au titre de <b class="quaternary">champion</b>.</p>
                <a href="{{ ('/tuto') }}" class="bouton-inscription my-4">Lire l'article complet</a>
            </div>
        </div>
    </div>

    {{--Twitter--}}
    <div class="container-fluid my-3">
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <a class="twitter-timeline" data-width="500" data-height="700" data-theme="dark"
                   href="https://twitter.com/NBAFRANCE?ref_src=twsrc%5Etfw">Tweets by NBAFRANCE</a>
            </div>
        </div>
    </div>

    <hr class="bg-white">

    {{--Dev Teams--}}
    <div class="container">
        {{--Titre--}}
        <div class="row pt-2">
            <div class="col-12 d-flex justify-content-center">
                <h1 class="text-white">Rencontrez l'équipe !</h1>
            </div>
        </div>
        {{--Équipe dev--}}
        <div class="row pt-2">
            <div class="col-md-6 d-flex justify-content-around">
                <div class="p-5">
                    <div>
                        <img style="width: 100px;" class="imageHOF" src="{{asset('storage/images/anais.jpg')}}" alt="anais">
                    </div>
                    <div>
                        <h4 class="text-white">Mrs Invisible</h4>
                        <p class="tertiary">Anaïs Mourat</p>
                        <p class="text-white">Dev Backend</p>
                    </div>
                </div>

                <div class="p-5">
                    <div>
                        <img style="width: 100px;" class="imageHOF" src="{{asset('storage/images/florian.jpg')}}" alt="florian">
                    </div>
                    <div>
                        <h4 class="text-white">The Torch</h4>
                        <p class="tertiary">Florian Chabreyrou</p>
                        <p class="text-white">Dev Frontend</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 d-flex justify-content-around">
                <div class="p-5">
                    <div>
                        <img style="width: 100px;" class="imageHOF" src="{{asset('storage/images/jeremy.jpg')}}" alt="jeremy">
                    </div>
                    <div>
                        <h4 class="text-white">The Thing</h4>
                        <p class="tertiary">Jérémy Roy</p>
                        <p class="text-white">Dev Backend</p>
                    </div>
                </div>

                <div class="p-5">
                    <div>
                        <img style="width: 100px;" class="imageHOF" src="{{asset('storage/images/armel.jpg')}}" alt="armel">
                    </div>
                    <div>
                        <h4 class="text-white">Mr Fantastic</h4>
                        <p class="tertiary">Armel Bouvier</p>
                        <p class="text-white">Chargé de projet</p>
                        <p class="text-white">Dev Front/Backend</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
