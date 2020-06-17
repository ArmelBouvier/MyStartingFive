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
        <h1 class="my-5 text-white">{{ $team->name }}</h1>
        <div class="row justify-content-between">

{{--            Infos sur les joueurs--}}
            <div class="MS5card card mb-5" style="width: 18rem;">
                <div id="card_private_league" class="py-4 d-flex flex-column align-items-center">
                    <h2 class="text-white display-3" ><i class="fas fa-users"></i></h2>
                    <h2 class="text-white">dernier 5</h2>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    @foreach($allPlayersMatch as $player)
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
                    </div>
                </div>

    {{--            Stats de l'équipe--}}
                <div class="MS5card card mb-5" style="width: 18rem;">
                    <div id="card_public_league" class="py-4 d-flex flex-column align-items-center">
                        <h2 class="text-white display-3" ><i class="fas fa-chart-bar"></i></h2>
                        <h2 class="text-white">Stats</h2>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <p class="card-text"><span class="tertiary">Ratio V/D : </span>3</p>
                        <p class="card-text"><span class="tertiary">Pts. marqués /match : </span>123</p>
                        <p class="card-text"><span class="tertiary">Pts. encaissés /match : </span>87</p>
                    </div>
                </div>

    {{--            Classement de l'équipe--}}
                <div class="MS5card card mb-5" style="width: 18rem;">
                    <div id="card_create_league" class="py-4 d-flex flex-column align-items-center">
                        <h2 class="text-white display-3" ><i class="fas fa-medal"></i></h2>
                        <h2 class="text-white">Classement</h2>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <p class="card-text"><span class="tertiary">Classement : </span>1/4</p>
                        <p class="card-text"><span class="tertiary">% Victoires : </span>58%</p>
                        <p class="card-text"><span class="tertiary">Série / 3 matchs </span>V/D/V</p>
                    </div>
                </div>
            </div>

    {{--            Affichage du roster--}}
            <div class="row">
                <h2 class="text-white">Roster :</h2>
                <p class="tertiary">filtres</p>
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


