@extends('layouts.master')

@section('content')
    {{--Titre de la page--}}
    <div class="container">
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <h1 class="text-white">Tableau de bord</h1>
            </div>
        </div>
    </div>

    {{-- Dashboard Match --}}
    @if(isset($draftIsOver) && $draftIsOver===1)

        <div class="container">
            <div class="row no-gutters justify-content-around mb-4">
                {{-- Card prochain match --}}
                <div class="col-md-5 mt-4 MS5card p-0">

                    <div class="row flex-column text-center bg-countdown no-gutters pb-5">
                        <h2 class="text-white py-5">Prochain Match</h2>
                        <span class="d-flex" id="countdown"></span>
                        @if(isset($userNextMatchs))
                            <div class="d-none" id="MatchDateTime">{{$userNextMatchs->start_at}}</div>
                        @endif
                    </div>

                    <div class="row justify-content-center no-gutters my-1">

                        <div class="col-md-12 text-center">
                            @if ($homeTeamNextMatch !== 'Match fini' || $awayTeamNextMatch !== 'Match fini' )
                                <div class="row flex-wrap justify-content-between pt-4">

                                    <div class="col-md-4">
                                        <div class="col-md-12 text-center pb-2">
                                            @if ( ( $userHomeNextMatchLogo  !== 'Pas de logo' ||  $userAwayNextMatchLogo !== 'Pas de logo' ))
                                                <img class="w-50" src="{{$userHomeNextMatchLogo}}">
                                            @else
                                            @endif
                                        </div>
                                        <div class="col-md-12 ">
                                            <h4 class="text-white text-center">{{$homeTeamNextMatch->name}}</h4>
                                            @if ($homeTeamNextMatch !== 'Match fini' || $awayTeamNextMatch !== 'Match fini' )
                                                <p class="tertiary text-center">{{$userHomeNextMatch->pseudo}}</p>
                                            @else
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="col-md-12 text-center pt-1">
                                            <img style="height: 75px; width: 75px;"
                                                 src="{{asset('storage/images/vs_dashboard.png')}}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-12 text-center pb-2">
                                            @if ( ( $userHomeNextMatchLogo  !== 'Pas de logo' ||  $userAwayNextMatchLogo !== 'Pas de logo' ))
                                                <img class="w-50" src="{{$userAwayNextMatchLogo}}">
                                            @else
                                            @endif
                                        </div>
                                        <div class="col-md-12">
                                            <h4 class="text-white text-center">{{$awayTeamNextMatch->name}}</h4>
                                            @if ($homeTeamNextMatch !== 'Match fini' || $awayTeamNextMatch !== 'Match fini' )
                                                <p class="tertiary text-center">{{$userAwayNextMatch->pseudo}}</p>
                                            @else
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h1>Match fini</h1>
                            @endif
                        </div>
                    </div>
                    <div class="row no-gutters justify-content-center mt-4">
                        <a href="{{route('match.index')}}" class="text-white bouton-inscription">Coaching</a>
                    </div>
                </div>

                {{-- Card League --}}
                <div class="col-md-5 p-0 mt-4">
                    <div class="row no-gutters">
                        <div class="col-12">
                            <div class="row no-gutters MS5card text-center">
                                <div class="col-12">
                                    <h2 class="text-white">League</h2>
                                </div>


                                <div class="col-12">
                                    <table class="table table-bordered bg-card m-0">
                                        <thead>
                                        <tr>
                                            <th scope="col">Équipe</th>
                                            <th scope="col">Stade</th>
                                            <th scope="col">%</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach( $userLeague->users as $user)
                                            <tr>
                                                <td class="align-middle">  {{$user->team->name}}</td>
                                                <td class="align-middle">  {{$user->team->stadium_name}}</td>
                                                @if($user->team !== null)
                                                    <td class="align-middle">{{$teamVictoryRatio[$user->team->id]}}</td>
                                                @else
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row no-gutters MS5card text-center mt-4">
                                <div class="col-12">
                                    <h2 class="text-white">Team</h2>
                                </div>
                                <div class="col-12">
                                    <table class="table table-bordered bg-card w-100 m-0">
                                        <thead>
                                        <tr>
                                            <th scope="col">Joueur</th>
                                            <th scope="col">Position</th>
                                            <th scope="col">Score</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($userBestPlayersTeam as $player)
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

                                                    <img
                                                        src="https://nba-players.herokuapp.com/players/{{$playerStats->ln}}/{{$playerStats->fn}}"
                                                        class="w-25 rounded-circle pr-1">

                                                    {{$playerStats->fn}} {{$playerStats->ln}}
                                                </th>
                                                <td class="align-middle">{{$position}}</td>
                                                <td class="align-middle">{{$player->score}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="row no-gutters justify-content-center mt-4">
                                        <a href="{{route('teams.show', $userTeamId)}}" class="text-white bouton-inscription">
                                            Roster
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">

            <div class="row no-gutters justify-content-around mb-4">
                {{-- Card Dernier Match --}}
                <div class="col-md-5 MS5card mt-4 p-0">

                    <div class="row no-gutters">
                        <div class="col-12 text-center my-2">
                            <h2 class="text-white">Dernier Match</h2>
                        </div>


                        <div class="row justify-content-center no-gutters my-4">
                            <div class="col-md-12 d-flex justify-content-around">
                                @if ( $homeTeamLastMatch  !== 'Match pas fini' || $awayTeamLastMatch !== 'Match pas fini' )
                                    <div class="row flex-wrap justify-content-between">
                                        <div class="col-md-12">
                                            <div class="row flex-wrap justify-content-around">
                                                <div class="col-md-4 pb-2 text-center">
                                                    @if ( ( $userHomeLastMatchLogo  !== 'Pas de logo' ||  $userAwayLastMatchLogo !== 'Pas de logo' ))
                                                       <div class="col-md-12 h-75">
                                                           <img class="w-75" src="{{$userHomeLastMatchLogo}}">
                                                       </div>
                                                    @else
                                                    @endif
                                                    <div class="col-md-12 mt-2">
                                                        <h4 class="text-white">{{$homeTeamLastMatch->name}}</h4>
                                                        @if ( ($homeTeamLastMatch  !== 'Match pas fini' || $awayTeamLastMatch !== 'Match pas fini' ))
                                                            <p class="tertiary text-center">{{$userHomeLastMatch->pseudo}}</p>
                                                        @else
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <h1 class="tertiary">{{ $userLastMatch->home_team_score }}
                                                        - {{$userLastMatch->away_team_score}}</h1>
                                                </div>
                                                <div class="col-md-4 pb-2 text-center">
                                                    @if ( ( $userHomeLastMatchLogo  !== 'Pas de logo' ||  $userAwayLastMatchLogo !== 'Pas de logo' ))
                                                        <div class="col-md-12 h-75">
                                                            <img class="w-75" src="{{$userAwayLastMatchLogo}}">
                                                        </div>
                                                    @else
                                                    @endif
                                                    <div class="col-md-12 mt-2">
                                                        <h4 class="text-white">{{$awayTeamLastMatch->name}}</h4>
                                                        @if ( $homeTeamLastMatch  !== 'Match pas fini' || $awayTeamLastMatch !== 'Match pas fini' )
                                                            <p class="tertiary text-center">{{$userAwayLastMatch->pseudo}}</p>
                                                        @else
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-12 d-flex justify-content-center">
                                        <h3>Match pas commencer</h3>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
                {{-- Card Tweets si il y des matchs --}}
                <div class="col-md-5 mt-4 MS5card">
                    @if($userTwitterFeed !== null)
                        <a class="twitter-timeline" data-width="460" data-height="550" data-theme="dark"
                           href="{{$userTwitterFeed->twitter_feed}}">
                            Tweets</a>
                        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    @else
                        <a class="twitter-timeline" data-width="500" data-height="600" data-theme="dark"
                           href="https://twitter.com/NBAFRANCE?ref_src=twsrc%5Etfw">Tweets
                        </a>
                        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    @endif
                </div>

            </div>

            {{-- Dashboard Evolutif--}}
            @elseif (!isset($draftIsOver) || $draftIsOver===0)

                <div class="container">
                    <div class="row no-gutters justify-content-center my-3">
                        {{-- Card Evolutif--}}
                        <div class="col-md-5 MS5card ">
                            @if(isset($league))
                                @if(isset($team) && $team->exists()=== true)
                                    @if($team->getLeague->isActive === 1)

                                        @if(isset($draftIsOver) && $draftIsOver === 0)
                                            <div class="row flex-column text-center bg-countdown no-gutters pb-5">
                                                <h2 class="text-white py-5">Finis ta draft !</h2>
                                            </div>
                                            <div class="row no-gutters justify-content-center mt-5">
                                                <a href="{{route('draft.index')}}"
                                                   class="text-white bouton-inscription">Continuer ma draft</a>
                                            </div>
                                        @else
                                            <div class="row flex-column text-center bg-countdown no-gutters pb-5">
                                                <h2 class="text-white py-5">Rejoins la draft !</h2>
                                            </div>
                                            <div class="row no-gutters justify-content-center mt-5">
                                                <a href="{{route('leagues.show', $league->league_id)}}"
                                                   class="text-white bouton-inscription">Rejoindre la draft</a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="row flex-column text-center bg-countdown no-gutters pb-5">
                                            <h2 class="text-white py-5">En attente du lancement de la league</h2>
                                        </div>
                                        <div class="row no-gutters justify-content-center mt-5">
                                            <a href="{{route('leagues.show', $league->league_id)}}"
                                               class="text-white bouton-inscription">Check ta league</a>
                                        </div>
                                    @endif
                                @else
                                    <div class="row flex-column text-center bg-countdown no-gutters pb-5">
                                        <h2 class="text-white py-5">Créer une team</h2>
                                    </div>
                                    <div class="row no-gutters justify-content-center mt-5">
                                        <a href="{{route('leagues.index')}}" class="text-white bouton-inscription">Créer
                                            une team</a>
                                    </div>
                                @endif
                            @else
                                <div class="row flex-column text-center bg-countdown no-gutters pb-5">
                                    <h2 class="text-white py-5">Rejoindre une league</h2>
                                </div>
                                <div class="row no-gutters justify-content-center mt-5">
                                    <a href="{{route('leagues.index')}}" class="text-white bouton-inscription">Rejoindre
                                        une league</a>
                                </div>
                            @endif

                        </div>
                        {{-- Card SI il n'y pas de matchs Tweets --}}
                        <div class="col-md-5 ml-4 MS5card">
                            @if($userTwitterFeed !== null)
                                <a class="twitter-timeline" data-width="600" data-height="600" data-theme="dark"
                                   href="{{$userTwitterFeed->twitter_feed}}">
                                    Tweets</a>
                                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                            @else
                                <a class="twitter-timeline" data-width="500" data-height="600" data-theme="dark"
                                   href="https://twitter.com/NBAFRANCE?ref_src=twsrc%5Etfw">Tweets
                                </a>
                                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @endsection
            @section('script-footer')
                <script>
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
                        }
                        this.update(value);
                    }
                    // Calculation adapted from https://www.sitepoint.com/build-javascript-countdown-timer-no-dependencies/
                    function getTimeRemaining(endtime) {
                        var t = Date.parse(endtime) - Date.parse(new Date());
                        return {
                            'Total': t,
                            'Jours': Math.floor(t / (1000 * 60 * 60 * 24)),
                            'Heures': Math.floor((t / (1000 * 60 * 60)) % 24),
                            'Minutes': Math.floor((t / 1000 / 60) % 60),
                            'Secondes': Math.floor((t / 1000) % 60)
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
                    var matchDate = document.querySelector('#MatchDateTime').textContent;
                    var deadline = new Date(matchDate);
                    var c = new Clock(deadline, function () {
                        //alert('countdown complete')
                    });
                    document.getElementById('countdown').appendChild(c.el);
                    var clock = new Clock();
                    document.body.appendChild(clock.el);
                </script>
@endsection
