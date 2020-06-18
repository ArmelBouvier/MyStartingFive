@extends('layouts.master')
@section('content')
    @php @endphp
    <div class="container text-white" id="draft-container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center">DRAFT</h1>
            </div>
            {{-----------------------VALIDER DRAFT ---------------------}}
        </div>
        <div class="row">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
        </div>
        <div class="row">
            @if(session()->get('errors'))
                <div class="alert alert-success">
                    {{ session()->get('errors') }}
                </div><br/>
            @endif
        </div>
        {{-----------------------INFOS SUR EQUIPE---------------------}}
        <div class="row my-5">
            <div class="col-lg-3">
                <div class="col-md-12 MS5card d-flex flex-column align-items-center h-100">
                    <div class="col-6">
                        <img
                            src="https://nba-players.herokuapp.com/players/curry/stephen"
                            class="h-auto w-100 rounded-circle">
                    </div>
                    <div class="d-flex flex-column align-items-center justify-content-between">
                        <p class="text-center">{{$team->name}}</p>
                        <p class="text-center">{{$team->stadium_name}}</p>
                        <p class="text-center">{{$team->getLeague->name}}</p>
                    </div>
                </div>
            </div>
            {{-----------------------DONNEES SUR SALARY CAP ---------------------}}
            <div class="col-md-4 px-0">
                <div class="col-md-12 MS5card px-0 h-100">
                    <div class="bg-card-title text-center mb-0 py-1">
                        <h2 class="mb-0">SALARY CAP</h2>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table text-white table-borderless table-sm w-100 mb-0"
                                   style="font-size: 0.9rem;">
                                <tbody>
                                <tr>
                                    <th scope="row">Salary Cap Initial</th>
                                    <td>200 M$</td>
                                </tr>
                                <tr>
                                    <th scope="row">Salary Cap Restant</th>
                                    <td>{{$team->salary_cap}} M$</td>
                                </tr>
                                <tr>
                                    <th scope="row">Salary Cap après enchère</th>
                                    <td>{{$team->salary_cap - $auctions->sum("auction")}} M$</td>
                                </tr>
                                <tr>
                                    <th scope="row">Total des Enchères en cours</th>
                                    <td>{{$auctions->sum("auction")}} M$</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{----------------------- NOMBRE JOUEURS DRAFTES ---------------------}}
            <div class="col-md-2">
                <div class="col-md-12 MS5card h-100 d-flex justify-content-center align-items-center flex-column">
                    <p id="nb-players">{{count($team->getPlayers)}}</p>
                    <p>Joueurs</p>
                    <p>Draftés</p>
                </div>
            </div>
            {{-----------------------DONNEES SUR LA FIN DE DRAFT ---------------------}}
            <div class="col-md-3">
                <div class="col-md-12 MS5card h-100">
                    <h4 class="text-center">Fin de la draft dans :</h4>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                {{-----------------------FILTRES TABLEAU JOUEURS NBA ---------------------}}

                <div class="row mx-3">
                    <div class="col-12 d-flex py-1 justify-content-around MS5card">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Prix
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="/draft?order=asc" class="btn btn-secondary p-1 dropdown-item">croissant</a>
                                <a href="/draft?order=desc" class="btn btn-secondary p-1 dropdown-item">decroissant</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenuButtonPosition" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                position
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonPosition">
                                <a href="/draft" class="btn btn-secondary p-1 w-100">tous les joueurs</a>
                                <a href="/draft?position=G" class="btn btn-secondary p-1 dropdown-item">arrieres</a>
                                <a href="/draft?position=F" class="btn btn-secondary p-1 dropdown-item">ailiers</a>
                                <a href="/draft?position=C" class="btn btn-secondary p-1 dropdown-item">Pivot</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenuButtonPosition" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                joueurs draftés
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonPosition">
                                <a href="/draft" class="btn btn-secondary p-1 dropdown-item">montrer</a>
                                <a href="/draft?hide" class="btn btn-secondary p-1 dropdown-item">cacher</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenuButtonPosition" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                De A à Z
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonPosition">
                                <a href="/draft" class="btn btn-secondary p-1 dropdown-item">croissant</a>
                                <a href="/draft?hide" class="btn btn-secondary p-1 dropdown-item">décroissant</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-----------------------TABLEAU JOUEURS NBA ---------------------}}
                <div class="row">
                    <div class="col-md-12 text-white mt-3">
                        <table class="table table-striped table-dark table-sm text-white w-100"
                               style="font-size: 0.8rem;">
                            <thead>
                            <tr>
                                <th scope="col" width="35%">Joueur</th>
                                <th scope="col">Poste</th>
                                <th scope="col">Min</th>
                                <th scope="col">Pts</th>
                                <th scope="col">Pass</th>
                                <th scope="col">Reb</th>
                                <th scope="col">Blk</th>
                                <th scope="col">Int</th>
                                <th scope="col">PdB</th>
                                <th scope="col">Prix initial</th>
                                <th scope="col">Prix Actuel</th>
                                <th scope="col">Statut Enchère</th>

                            </tr>
                            </thead>
                            <tbody class="table-hover">
                            @foreach($players as $player)
                                @php
                                    $playerStats = json_decode($player->data)->pl;

                                        $currentSeasonStats = $playerStats->ca->sa;
                                        $currentSeasonStats = last($currentSeasonStats);

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
                                    <th scope="row" class="align-middle pr-0">
                                        <a href="/draft/{{$player->id}}" class="text-white">
                                            <img
                                                src="https://nba-players.herokuapp.com/players/{{$playerStats->ln}}/{{$playerStats->fn}}"
                                                class="w-25 rounded-circle pr-1">
                                            {{$playerStats->fn}} {{$playerStats->ln}}
                                        </a>
                                    </th>
                                    <td class="align-middle">{{$position}}</td>
                                    <td class="align-middle">{{$currentSeasonStats->min}}</td>
                                    <td class="align-middle">{{$currentSeasonStats->pts}}</td>
                                    <td class="align-middle">{{$currentSeasonStats->ast}}</td>
                                    <td class="align-middle">{{$currentSeasonStats->reb}}</td>
                                    <td class="align-middle">{{$currentSeasonStats->stl}}</td>
                                    <td class="align-middle">{{$currentSeasonStats->blk}}</td>
                                    <td class="align-middle">{{$currentSeasonStats->tov}}</td>
                                    <td class="align-middle">{{$player->price}}</td>
                                    @php $indicator = true; @endphp
                                    @foreach($auctionsOnPlayers as $auctionsOnPlayer)
                                        @if($auctionsOnPlayer->player_id === $player->id && $indicator)
                                            <td class="align-middle">{{$auctionsOnPlayer->auction}}</td>
                                            @php $indicator = false; @endphp
                                        @endif
                                    @endforeach
                                    @if(!in_array($player->id, $auctionPlayersId) && !in_array($player->id, $notDisplayedPlayers))
                                        <td colspan="2" class="align-middle">
                                            <form action="{{ route('draft.auction', ['id' => $player->id])}}"
                                                  method="POST" class="mb-0">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-light p-0 px-1">enchérir
                                                </button>
                                            </form>
                                        </td>
                                    @elseif(in_array($player->id, $auctionPlayersId))
                                        <td colspan="2" class="align-middle">
                                            en cours
                                        </td>
                                    @else
                                        <td colspan="2" class="align-middle">
                                            Trop tard !
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="m-auto d-flex justify-content-center">{{$players->links()}}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                {{-----------------------JOUEURS DRAFTES ---------------------}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="bg-card-title text-center py-1 mb-3">
                            <h2 class="mb-0">Joueurs Draftés</h2>
                        </div>
                        @if(isset($guards) && !empty($guards))
                            <table class="table table-dark table-sm">
                                <thead>
                                <tr>
                                    <th scope="col" colspan="3">Arrières</th>
                                    <th>{{count($guards)}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($guards as $guard)
                                    @php
                                        $playerInfos = json_decode($guard->data);
                                        $position  = substr($playerInfos->pl->pos, 0,1)
                                    @endphp
                                    <tr class="">
                                        <td width="30%">
                                            <img
                                                src="https://nba-players.herokuapp.com/players/{{$playerInfos->pl->ln}}/{{$playerInfos->pl->fn}}"
                                                class=" w-50 rounded-circle">
                                        </td>
                                        <td>{{$position}}</td>
                                        <td>{{$playerInfos->pl->fn}}</td>
                                        <td>{{$playerInfos->pl->ln}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                        @if(isset($forwards) && !empty($forwards))
                            <table class="table table-dark table-sm">
                                <thead>
                                <tr>
                                    <th scope="col" colspan="3">Ailiers</th>
                                    <th>{{count($forwards)}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($forwards as $forward)
                                    @php
                                        $playerInfos = json_decode($forward->data);
                                        $position  = substr($playerInfos->pl->pos, 0,1)
                                    @endphp
                                    <tr>
                                        <td width="30%">
                                            <img
                                                src="https://nba-players.herokuapp.com/players/{{$playerInfos->pl->ln}}/{{$playerInfos->pl->fn}}"
                                                class="w-50 rounded-circle">
                                        </td>
                                        <td>{{$position}}</td>
                                        <td>{{$playerInfos->pl->fn}}</td>
                                        <td>{{$playerInfos->pl->ln}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                        @if(isset($centers) && !empty($centers))
                            <table class="table table-dark table-sm">
                                <thead>
                                <tr>
                                    <th scope="col" colspan="3">Pivots</th>
                                    <th>{{count($centers)}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($centers as $center)
                                    @php
                                        $playerInfos = json_decode($center->data);
                                        $position  = substr($playerInfos->pl->pos, 0,1)
                                    @endphp
                                    <tr>
                                        <td width="30%">
                                            <img
                                                src="https://nba-players.herokuapp.com/players/{{$playerInfos->pl->ln}}/{{$playerInfos->pl->fn}}"
                                                class="w-50 rounded-circle pr-1">
                                        </td>
                                        <td>{{$position}}</td>
                                        <td>{{$playerInfos->pl->fn}}</td>
                                        <td>{{$playerInfos->pl->ln}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                {{-----------------------ENCHERES EN COURS ---------------------}}
                <div class="row mt-5">
                    <div class="col-md-12 text-white">
                        <div class="bg-card-title text-center py-1">
                            <h2 class="mb-0 text-center">Enchères En cours</h2>
                        </div>
                    </div>
                </div>
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
                @foreach($auctions as $auction)
                    @php $playerData = json_decode($auction->getPlayerData->data, false);
                                        $position  = substr($playerData->pl->pos, 0,1);
                                    if($position === "G") {
                                        $position = 'Arrière';
                                    } else if ($position === "F") {
                                        $position = 'Ailier';
                                    } else {
                                        $position = 'Pivot';
                                    }
                    @endphp
                    <div class="row my-1">
                        <div class="col-12 MS5card">
                            <div class="row">
                                <div class="col-md-4">
                                    <img
                                        src="https://nba-players.herokuapp.com/players/{{$playerData->pl->ln}}/{{$playerData->pl->fn}}"
                                        class="rounded-circle w-100">
                                </div>
                                <div class="col-md-6">
                                    <p>{{$position}}</p>
                                    <p>{{strtoupper($playerData->pl->fn)}} {{strtoupper($playerData->pl->ln)}}</p>
                                </div>
                                <div class="col-md-2">
                                    @php $indicator = true @endphp
                                    @foreach($auctionsOnPlayers as $auctionsOnPlayer)

                                        @if($auctionsOnPlayer->player_id === $auction->player_id && $indicator)
                                            <p class="align-middle font-weight-bold tertiary auction-price text-center">
                                                {{$auctionsOnPlayer->auction}}M$
                                            </p>
                                            @php $indicator = false @endphp
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <form
                                        action="{{ route('draft.delete.auction', ['id' => $auction->player_id])}}"
                                        method="POST" class="col-12">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-primary rounded-circle">X
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form
                                        action="{{ route('draft.auction.updateValue', ['id' => $auction->player_id])}}"
                                        method="POST" class="form-group w-100 d-flex justify-content-center">
                                        @php $indicator = true @endphp
                                        @foreach($auctionsOnPlayers as $auctionsOnPlayer)
                                            @if($auctionsOnPlayer->player_id === $auction->player_id && $indicator)
                                                <input class="form-control" type="number" name="auctionValue"
                                                       id="auctionValue" value="{{$auctionsOnPlayer->auction}}" step="5"
                                                       min="{{$auctionsOnPlayer->auction}}">
                                                @php $indicator = false @endphp
                                            @endif
                                        @endforeach
                                        @method('POST')
                                        @csrf
                                        <button type="submit" class="btn btn-primary">enchérir</button>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p>Ma dernière enchère: {{$auction->auction}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    @php
                                        $limitTime = new DateTime($auction->auction_time_limit);
                                        $limitTimeMin = $limitTime->format('i');
                                        $limitTimeSec= $limitTime->format('s');
                                        $now = new DateTime();
                                        $nowMin = $now->format('i');
                                        $nowSec = $now->format('s');

                                        $differenceMin = $nowMin - $limitTimeMin;
                                        $differenceSec= $nowSec - $limitTimeSec
                                    @endphp
                                    <p>Fin de l'enchère dans {{ $differenceMin}} min {{$differenceSec}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection




