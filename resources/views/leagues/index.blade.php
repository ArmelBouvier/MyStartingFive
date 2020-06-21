@extends('layouts.master')

@section('content')
    <div class="container">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h1 class="my-5 text-white">Bienvenue sur le portail des Leagues MS5</h1>
        <h2 class="mb-5 bg-secondary text-center text-white"><i class="tertiary fas fa-exclamation-triangle"></i>
            Attention, tu ne peux jouer que dans <span class="tertiary">UNE</span> league à la fois, choisis bien !</h2>
        <div class="row justify-content-center">

            <div class="row justify-content-around mb-5">

                {{-----------------------Créer une league--------------------------}}

                <div class="MS5card card mr-2 mt-5" style="width: 18rem;">
                    <div id="card_create_league" class="py-4 d-flex flex-column align-items-center">
                        <img src="{{asset('storage/images/leagues_portal/picto_creer_league_min.png')}}" alt="photo">
                        <h2>Créér une league</h2>
                        <p class="tertiary">Crée ta league et choisis qui tu invites !</p>
                    </div>
                    <div class="card-body mt-1">
                        <p class="card-text mb-1">Crées ta propre league, et choisis qui tu veux affronter.</p>
                        <form method="post" role="form" action="{{ route('leagues.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Nom de la league :</label>
                                <input type="text" class="form-control" name="name" required/>
                            </div>
                            <div class="form-group">
                                <label for="number_teams">Nombre d'équipes :</label>
                                <select class="form-control" id="number_teams" name="number_teams" required>
                                    <option>choisir un nombre d'équipes !</option>
                                    <option value="2">2 équipes</option>
                                    <option value="4">4 équipes</option>
                                    <option value="6">6 équipes</option>
                                    <option value="8">8 équipes</option>
                                    <option value="10">10 équipes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="public">Statut de la league :</label>
                                <select class="form-control" id="public" name="public" required>
                                    <option>choisir un statut !</option>
                                    <option value="0">publique</option>
                                    <option value="1">privée</option>
                                </select>
                            </div>
                            <button type="submit" class="mt-3 btn btn-secondary w-100">Créer une league</button>
                        </form>

                    </div>
                </div>

                {{--                    Rejoindre une league privée--}}

                <div class="MS5card card mb-5 mt-2" style="width: 18rem;">
                    <div id="card_private_league" class="py-4 d-flex flex-column align-items-center">
                        <img src="{{asset('storage/images/leagues_portal/picto_league_privee_min.png')}}" alt="photo">
                        <h2>League privée</h2>
                        <p class="tertiary">Joue avec tes potes !</p>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <p class="card-text">Tu veux jouer au calme avec tes amis ? Loin de la lumière des projecteurs
                            et de la foule ?</p>
                        <p class="card-text">Tout ce que tu as à faire, c'est saisir le mot de passe
                            qu'on t'a envoyé.</p>
                        <form method="post" role="form" action="{{ route('leagues.joinPrivateLeague') }}">
                            @csrf
                            <div class="form-group">
                                <label for="token">Mot de passe de la league :</label>
                                <br>
                                <input type="text" class="form-control" name="token" required/>
                            </div>
                            <button type="submit" class="btn btn-secondary w-100 mt-5">Rejoindre</button>
                        </form>
                    </div>
                </div>

                {{--                    Rejoindre une league publique--}}

                <div class="MS5card card ml-2 mt-5" style="width: 18rem;">
                    <div id="card_public_league" class="py-4 d-flex flex-column align-items-center">
                        <img src="{{asset('storage/images/leagues_portal/picto_league_publique_min.png')}}" alt="photo">
                        <h2>League publique</h2>
                        <p class="tertiary">Frotte-toi aux meilleurs !</p>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <p class="card-text">Confrontes toi à la communauté (attention aux fessées ! ^^).</p>
                        <p>Ici tu pourras rejoindre des leagues ouvertes à tous, et où les bons sentiments sont laissés
                            au vestiaire. Bon courage !</p>
                        <a href="{{ route('leagues.public')}}" class="btn btn-secondary w-100">Voir les leagues
                            publiques</a>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

