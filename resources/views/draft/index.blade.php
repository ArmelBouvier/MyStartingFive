@extends('layouts.master')
@section('content')
    <div class="container text-white" id="draft-container">
        <div class="row mt-5">
            <div class="col-12">
                <h1 class="text-center">DRAFT</h1>
            </div>
        </div>
        <div class="row alert-div">

        {{-----------------------VALIDER DRAFT ---------------------}}

            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
        </div>
        <div class="row alert-div">
            @if(session()->get('errors'))
                <div class="alert alert-danger">
                    {{ session()->get('errors') }}
                </div><br/>
            @endif
        </div>
        {{-----------------------INFOS SUR EQUIPE---------------------}}

        <div class="row my-5">
            <div class="col-lg-3 px-0">
                <div class="col-md-12 MS5card d-flex flex-row align-items-center h-100">
                    <div class="col-6 px-0">

                        <img
                            src="{{$userLogo}}"
                            class="h-auto w-100">
                    </div>
                    <div class="px-0 col-6 d-flex flex-column align-items-center justify-content-between">
                        <h2 class="text-center font-weight-bold">{{$team->name}}</h2>
                        <p class="text-center">Stade {{$team->stadium_name}}</p>
                        <p class="text-center tertiary">Ligue {{$team->getLeague->name}}</p>
                    </div>
                </div>
            </div>
            {{-----------------------DONNEES SUR SALARY CAP ---------------------}}
            <div class="col-md-4 px-0">
                <div class="col-md-12 MS5card px-0 h-100">
                    <div class="bg-card-title text-center mb-0">
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
            <div class="col-md-2 px-0">
                <div class="col-md-12 MS5card h-100 d-flex justify-content-center align-items-center flex-column">
                    <p id="nb-players" class="tertiary">{{count($team->getPlayers)}}</p>
                    <p>Joueurs</p>
                    <p>Draftés</p>
                </div>
            </div>

            {{-----------------------DONNEES SUR LA FIN DE DRAFT ---------------------}}
            <div class="col-md-3 flex-column text-center px-0">
                <div class="col-12 bg-countdown-draft h-100 d-flex flex-column justify-content-center">
                    <h2 class="text-white">Fin de la Draft dans :</h2>
                    <div class="d-flex w-100" id="countdown"></div>
                    <div class="d-none" id="DraftEndTime">{{$draftEnd}}</div>
                </div>
            </div>
        </div>

        {{-----------------------FILTRES TABLEAU JOUEURS NBA ---------------------}}
        <div class="row justify-content-around">
            <div class="col-md-8">
                <div class="row mx-3">
                    <div class="col-8 d-flex py-1 flex-start">
                        <div class="">
                            <a href="/draft" class="btn MS5card" id="btn-allPlayers">Tous les joueurs</a>
                        </div>
                        <div class="dropdown">
                            <button class="btn MS5card dropdown-toggle" type="button" id="dropdownMenuButton"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Prix
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="/draft?order=asc" class="btn p-1 dropdown-item text-left">croissant</a>
                                <a href="/draft?order=desc" class="btn p-1 dropdown-item text-left">decroissant</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn MS5card dropdown-toggle textleft" type="button"
                                    id="dropdownMenuButtonPosition" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                Position
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonPosition">
                                <a href="/draft?position=G" class="bt p-1 dropdown-item text-left">Arrieres</a>
                                <a href="/draft?position=F" class="bt p-1 dropdown-item text-left">Ailiers</a>
                                <a href="/draft?position=C" class="bt p-1 dropdown-item text-left">Pivot</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 d-flex py-1 flex-start h-100">
                        <a href="/draft" class="btn MS5card">Actualiser les enchères</a>
                    </div>
                </div>
                {{-----------------------TABLEAU JOUEURS NBA ---------------------}}
                <div class="row">
                    <div class="col-md-12 text-white w-100 mt-3">
                        <table class="table table-striped table-dark table-sm text-white w-100"
                               style="font-size: 0.8rem;">
                            <thead>
                            <tr>
                                <th scope="col" width="35%">Joueur</th>
                                <th scope="col">Poste</th>
                                <th id="stat-none" scope="col">Min</th>
                                <th id="stat-none" scope="col">Pts</th>
                                <th id="stat-none" scope="col">Pass</th>
                                <th id="stat-none" scope="col">Reb</th>
                                <th id="stat-none" scope="col">Blk</th>
                                <th id="stat-none" scope="col">Int</th>
                                <th id="stat-none" scope="col">PdB</th>
                                <th scope="col">Prix initial</th>
                                <th scope="col">Prix Actuel</th>
                                <th scope="col">Statut Enchère</th>

                            </tr>
                            </thead>
                            <tbody class="table-hover" id="playersToDraft">
                            @foreach($players as $player)
                                @php
                                    $playerStats = json_decode($player->data)->pl;
                                    if(isset($playerStats->ca->sa)) {
                                       $currentSeasonStats = $playerStats->ca->sa;
                                       $currentSeasonStats = last($currentSeasonStats);
                                    } else {
                                        $currentSeasonStats = $playerStats->ca;
                                    }

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
                                        @if($player->photo_url === 'image')
                                            <i class="fas fa-user fa-2x ml-3 mr-4 main-color"></i>
                                        @else
                                            <img src="{{$player->photo_url}}"
                                                 class="w-25 rounded-circle pr-1">
                                        @endif
                                        {{$playerStats->fn}} {{$playerStats->ln}}
                                    </th>
                                    <td class="align-middle">{{$position}}</td>
                                    <td id="stat-none" class="align-middle">{{$currentSeasonStats->min}}</td>
                                    <td id="stat-none" class="align-middle">{{$currentSeasonStats->pts}}</td>
                                    <td id="stat-none" class="align-middle">{{$currentSeasonStats->ast}}</td>
                                    <td id="stat-none" class="align-middle">{{$currentSeasonStats->reb}}</td>
                                    <td id="stat-none" class="align-middle">{{$currentSeasonStats->stl}}</td>
                                    <td id="stat-none" class="align-middle">{{$currentSeasonStats->blk}}</td>
                                    <td id="stat-none" class="align-middle">{{$currentSeasonStats->tov}}</td>
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
                                                  method="POST" class="mb-0" onsubmit="return confirmAuction('{{$playerStats->fn}}','{{$playerStats->ln}}')">
                                                <input hidden value="{{$playerStats->ln}}">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-light p-0 px-1"> enchérir
                                                </button >
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
                @if(!empty($team->getPlayers[0]))
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
                                    <th>{{count($guards)}} / 5</th>
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
                                            @if($guard->photo_url === 'image')
                                                <i class="fas fa-user fa-2x ml-3 mr-4 main-color"></i>
                                            @else
                                                <img src="{{$guard->photo_url}}"
                                                     class="w-25 rounded-circle pr-1">
                                            @endif
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
                                    <th>{{count($forwards)}} / 5</th>
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
                                            @if($forward->photo_url === 'image')
                                                <i class="fas fa-user fa-2x ml-3 mr-4 main-color"></i>
                                            @else
                                                <img src="{{$forward->photo_url}}"
                                                     class="w-25 rounded-circle pr-1">
                                            @endif
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
                                    <th>{{count($centers)}} / 2</th>
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
                                            @if($center->photo_url === 'image')
                                                <i class="fas fa-user fa-2x ml-3 mr-4 main-color"></i>
                                            @else
                                                <img src="{{$center->photo_url}}"
                                                     class="w-25 rounded-circle pr-1">
                                            @endif
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
                @endif
                {{-----------------------ENCHERES EN COURS ---------------------}}
                <div class="row mt-5">
                    <div class="col-md-12 text-white">
                        <div class="bg-card-title text-center py-1">
                            <h2 class="mb-0 text-center">Enchères En cours</h2>
                        </div>
                    </div>
                </div>
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
                    <div class="row mx-0 my-1">
                        <div class="col-12 MS5card">
                            <div class="row">
                                <div class="col-md-4">
                                    @if($auction->getPlayerData->photo_url === 'image')
                                        <i class="fas fa-user fa-2x ml-3 mr-4 main-color"></i>
                                    @else
                                        <img src="{{$auction->getPlayerData->photo_url}}"
                                             class="w-25 rounded-circle pr-1">
                                    @endif
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

                                        $differenceMin = abs($nowMin - $limitTimeMin);

                                        $differenceSec= abs($nowSec - $limitTimeSec);

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
@section('script-footer')
    <script>

        setInterval( function () {

            $('.alert-div').empty();
        }, 3000);

        //pour bloquer envoie du formulaire
        function confirmAuction(firstname, lastname){

            let message = 'Es-tu sûr de vouloir mettre une enchère sur ' +firstname + ' '+lastname + ' ?';
            let r = confirm(message);

            if(r === false) {
                return false;
            }
        }



        // --------------------- DECOMPTE AVANT Fin de Draft --------------------------------------//
        function CountdownTracker(label, value) {
            var el = document.createElement('span');
            el.className = 'flip-clock__piece';
            el.innerHTML = '<b class="flip-clock__card cardcountdown"><b class="card__top"></b><b class="card__bottom"></b>' +
                '<b class="card__back"><b class="card__bottom"></b></b></b>' + '<span class="flip-clock__slot">' + label + '</span>';
            this.el = el;
            var top = el.querySelector('.card__top'),
                bottom = el.querySelector('.card__bottom'),
                back = el.querySelector('.card__back'),
                backBottom = el.querySelector('.card__back .card__bottom');
            this.update = function (val) {
                val = ('0' + val).slice(-2);
                if (val !== this.currentValue) {
                    if (this.currentValue >= 0) {
                        back.setAttribute('data-value', this.currentValue);
                        bottom.setAttribute('data-value', this.currentValue);
                    }
                    this.currentValue = val;
                    top.innerText = this.currentValue;
                    backBottom.setAttribute('data-value', this.currentValue);
                    this.el.classList.remove('flip');
                    void this.el.offsetWidth;
                    this.el.classList.add('flip');
                }
            };
            this.update(value);
        }

        // Calculation adapted from https://www.sitepoint.com/build-javascript-countdown-timer-no-dependencies/
        function getTimeRemaining(endtime) {
            var t = Date.parse(endtime) - Date.parse(new Date());

            return {
                'Total': t,
                'Jours': Math.floor(t / (1000 * 60 * 60 * 24)),
                'Heures': Math.floor((t / (1000 * 60 * 60)) % 24),
                'Mins': Math.floor((t / 1000 / 60) % 60),
                'Secs': Math.floor((t / 1000) % 60)
            };
        }

        function getTime() {
            var t = new Date();
            return {
                'Total': t,
                'Hours': t.getHours() % 12,
                'Minutes': t.getMinutes(),
                'Seconds': t.getSeconds()
            };
        }

        function Clock(countdown, callback) {
            countdown = countdown ? new Date(Date.parse(countdown)) : false;
            callback = callback || function () {
            };
            var updateFn = countdown ? getTimeRemaining : getTime;
            this.el = document.createElement('div');
            this.el.className = 'flip-clock';
            var trackers = {},
                t = updateFn(countdown),
                key, timeinterval;
            for (key in t) {
                if (key === 'Total') {
                    continue;
                }
                trackers[key] = new CountdownTracker(key, t[key]);
                this.el.appendChild(trackers[key].el);
            }
            var i = 0;

            function updateClock() {
                timeinterval = requestAnimationFrame(updateClock);
                // throttle so it's not constantly updating the time.
                if (i++ % 10) {
                    return;
                }
                var t = updateFn(countdown);
                if (t.Total < 0) {
                    cancelAnimationFrame(timeinterval);
                    for (key in trackers) {
                        trackers[key].update(0);
                    }
                    callback();
                    return;
                }
                for (key in trackers) {
                    trackers[key].update(t[key]);
                }
            }

            setTimeout(updateClock, 500);
        }

        var draftEndTime = document.querySelector('#DraftEndTime').textContent;
        var deadline = new Date(draftEndTime);
        var c = new Clock(deadline, function () {
           // alert('countdown complete')
        });
        document.getElementById('countdown').appendChild(c.el);
        var clock = new Clock();
        //document.body.appendChild(clock.el);
    </script>
@endsection




