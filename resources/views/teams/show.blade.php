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
            <br/>
        @endif
        @if(session()->get('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div><br/>
        @endif
        <div class="row mb-3">
            <div class="col-md-6 d-flex justify-content-end"><img class="w-25" src="{{$logo}}"></div>
            <div class="col-md-6"><h1 class="my-5 text-white">{{ $team->name }}</h1></div>
        </div>

        <div class="row justify-content-between">

{{--            Infos sur les joueurs--}}
            <div class="MS5card card mb-5" style="width: 18rem;">
                <div id="card_private_league" class="py-4 d-flex flex-column align-items-center">
                    <h2 class="text-white display-3" ><i class="fas fa-users"></i></h2>
                    <h2 class="text-white">5 majeur</h2>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    @if(isset($userBestPlayersTeam))
                        @foreach($userBestPlayersTeam as $player)
                            @php
                                $playerStats = json_decode($player->data)->pl;

                                $position  = substr($playerStats->pos, 0,1);
                                            if($position === "G") {
                                                $position = 'Arrière';
                                            } else if ($position === "F") {
                                                $position = 'Ailier';
                                            } else {
                                                $position = 'Pivot';
                                            }
                            @endphp
                            <p class="card-text"><span class="tertiary">
                                {{$playerStats->fn}} {{$playerStats->ln}}
                                : </span>{{$position}}</p>
                        @endforeach
                    @else
                        <p>Pas de match joué !</p>
                    @endif

                    </div>
                </div>

    {{--            Stats de l'équipe--}}
                <div class="MS5card card mb-5" style="width: 18rem;">
                    <div id="card_public_league" class="py-4 d-flex flex-column align-items-center">
                        <h2 class="text-white display-3" ><i class="fas fa-info"></i></h2>
                        <h2 class="text-white">Infos de ta team</h2>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <p class="card-text"><span class="tertiary">Coach : </span>{{$team->userTeam->pseudo}}</p>
                        <p class="card-text"><span class="tertiary">Stade : </span>{{$team->stadium_name}}</p>
                        <p class="card-text"><span class="tertiary">Valeur de l'équipe : </span>{{$teamValue}} M$</p>
                    </div>
                </div>

    {{--            Classement de l'équipe--}}
                <div class="MS5card card mb-5" style="width: 18rem;">
                    <div id="card_create_league" class="py-4 d-flex flex-column align-items-center">
                        <h2 class="text-white display-3" ><i class="fas fa-medal"></i></h2>
                        <h2 class="text-white">Classement</h2>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <p class="card-text"><span class="tertiary">League : </span>{{$team->getLeague->name}}</p>
                        @if($teamVictoryRatio !== 0)
                            <p class="card-text"><span class="tertiary">% Victoires : </span>{{$teamVictoryRatio}}%</p>
                        @else
                            <p class="card-text">Ton équipe n'a pas encore joué de match</p>
                        @endif
                    </div>
                </div>
            </div>

    {{--            Affichage du roster--}}
            <div class="row">
                <h2 class="text-white">Roster :</h2>
                <table class="table table-striped text-white">
                    <thead class="font-weight-bold">
                    <tr>
                        <th class="tertiary">Portrait</th>
                        <th class="tertiary">Joueur</th>
                        <th class="tertiary">Cote</th>
                        <th class="tertiary">Poste</th>
                        <th class="tertiary">nb. matchs joués</th>
                        <th class="tertiary">dernier score</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($team->getPlayers as $player)
                        @php
                            $playerStats = json_decode($player->data)->pl;

                            $position  = substr($playerStats->pos, 0,1);
                                        if($position === "G") {
                                            $position = 'Arrière';
                                        } else if ($position === "F") {
                                            $position = 'Ailier';
                                        } else {
                                            $position = 'Pivot';
                                        }
                    @endphp
                    <tr>
                            <td><img
                                    src="https://nba-players.herokuapp.com/players/{{$playerStats->ln}}/{{$playerStats->fn}}"
                                    class="w-25 rounded-circle pr-1"></td>
                            <td>{{$playerStats->fn}} {{$playerStats->ln}}</td>
                            <td>{{$player->price}}</td>
                            <td>{{$position}}</td>
                            <td>{{$player->playersMatchs->count()}}</td>
                            <td>{{$player->score}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection



