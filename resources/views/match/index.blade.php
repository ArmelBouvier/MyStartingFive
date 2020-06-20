@extends('layouts.master')

@section('content')

    <div class="container text-white" id="next-game-coaching">
        <div class="row flex-column text-center bg-countdown no-gutters pb-5 mb-3">
            <h1 class="text-white py-5">Prochain Match</h1>
            <span class="d-flex" id="countdown"></span>
            <div class="d-none" id="MatchDateTime">{{$userNextMatch->start_at}}</div>
        </div>
        <div class="row m-auto justify-content-around mt-5">
            {{-----------            TABLEAU DES JOUEURS DE LEQUIPE----------------}}
            <div class="col-md-7 text-white">
                <div class="bg-card-title text-center py-1 mb-3">
                    <h2>Mon roster</h2>
                </div>
                <table class="table table-striped table-dark table-sm text-white w-100"
                       style="">
                    <thead class="">
                    <tr>
                        <th scope="col" width="35%">Joueur</th>
                        <th scope="col">Poste</th>
                        <th scope="col">Min</th>
                        <th id="stat-none" scope="col">Pts</th>
                        <th id="stat-none" scope="col">Pass</th>
                        <th id="stat-none" scope="col">Reb</th>
                        <th id="stat-none" scope="col">Blk</th>
                        <th id="stat-none" scope="col">Int</th>
                        <th id="stat-none" scope="col">PdB</th>
                        <th scope="col">Dernier Score</th>
                        <th scope="col">Blessé</th>
                        <th scope="col">choisir</th>
                    </tr>
                    </thead>
                    <tbody class="table-hover">
                    @foreach($userPlayersTeam as $player)
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
                            <td class="align-middle">{{$currentSeasonStats->min}}</td>
                            <td id="stat-none" class="align-middle">{{$currentSeasonStats->pts}}</td>
                            <td id="stat-none" class="align-middle">{{$currentSeasonStats->ast}}</td>
                            <td id="stat-none" class="align-middle">{{$currentSeasonStats->reb}}</td>
                            <td id="stat-none" class="align-middle">{{$currentSeasonStats->stl}}</td>
                            <td id="stat-none" class="align-middle">{{$currentSeasonStats->blk}}</td>
                            <td id="stat-none" class="align-middle">{{$currentSeasonStats->tov}}</td>
                            <td class="align-middle">{{$player->score}}</td>
                            <td class="align-middle">{{($player->injured === 0)? 'Non' : 'Oui' }}</td>
                            <td colspan="2" class="align-middle">
                                <form class="mb-0" data-player-id="{{$player->id}}">
                                    @csrf
                                    <button type="submit" id="player" class="btn btn-outline-light p-0 px-1 add-button"
                                            value="{{$player->id}}" name="player">choisir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{-----------            COMPOSITION POUR LE PROCHAIN MATCH ----------------}}
            <div class="col-md-5">
                <div class="bg-card-title text-center py-1 mb-3">
                    <h2 class="mb-0">Composition de l'équipe</h2>
                </div>
                <div id="errors" class="alert alert-danger d-none" role="alert"></div>

                <div class="col-md-12">
                    <table id="compo-table" class="w-100">
                        <thead>
                        </thead>
                        <tbody id="tableBody">
                        @foreach($playersSelected as $playerSelected)
                            @php
                                $playerDatas = json_decode($playerSelected->data)->pl;

                                    $currentSeasonStats = $playerDatas->ca->sa;
                                    $currentSeasonStats = last($currentSeasonStats);

                                $position  = substr($playerDatas->pos, 0,1);
                                if($position === "G") {
                                    $position = 'Arrière';
                                } else if ($position === "F") {
                                    $position = 'Ailier';
                                } else {
                                    $position = 'Pivot';
                                }
                            @endphp

                            <tr class="MS5card p-1 mt-2">
                                <td>
                                    <a href="{{route('match.delete.player', ['id' => $playerSelected->id])}}"
                                       type="submit" class="btn btn-primary rounded-circle delete-btn">X
                                    </a>
                                <td>
                                <td width="50%">
                                    <img
                                        src="https://nba-players.herokuapp.com/players/{{$playerDatas->ln}}/{{$playerDatas->fn}}"
                                        class="w-100 rounded-circle pr-1">

                                </td>
                                <td>{{$position}}</td>
                                <td class="ml-3">{{$playerDatas->fn}}</td>
                                <td class="ml-3">{{$playerDatas->ln}}</td>
                            </tr>
                        </tbody>
                        @endforeach
                    </table>

                    <a href="" id="modify-compo" class="btn btn-secondary d-none">Modifier La composition</a>
                </div>

            </div>
        </div>
        @endsection
        @section('script-footer')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.js"></script>
            <script>
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // setInterval('someFunc()', 5000);
                //
                // function someFunc()
                // {
                //
                //     $.ajax({
                //         async: true,
                //         type: "get",
                //         url: "",
                //         //data: data,
                //         success: function (html) {
                //             $('body').html(html);
                //         }
                //     });
                // }
                // setInterval('someFunc()', 5000);
                // function someFunc()
                //  {
                //      $('body').load(document.URL);
                //      console.log('test');
                //  }

                // On attend le chargement du document
                document.addEventListener("DOMContentLoaded", function () {
                    (function () {
                        "use strict";


// --------------------- DECOMPTE AVANT LE MATCH --------------------------------------//
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
                                'Minutes': Math.floor((t / 1000 / 60) % 60),
                                'Secondes': Math.floor((t / 1000) % 60)
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

                        var matchDate = document.querySelector('#MatchDateTime').textContent;
                        var deadline = new Date(matchDate);
                        var c = new Clock(deadline, function () {
                            alert('countdown complete')
                        });
                        document.getElementById('countdown').appendChild(c.el);
                        var clock = new Clock();
                        //document.body.appendChild(clock.el);


//-------------- ENREGISTREMENT DES JOUEURS DANS LA COMPOSITION ------------------------//
                        let errorElement = document.querySelector('#errors');

                        let table = document.querySelector('#tableBody');
                        let form = document.querySelector('.form');
                        const buttonsAdd = document.querySelectorAll('.add-button');
                        const buttonsDelete = document.querySelectorAll('.delete-btn');

                        let nbrOfElements = document.querySelector('#tableBody').childElementCount;

                        if (nbrOfElements === 0) {
                            document.querySelector('#modify-compo').classList.remove('d-block');
                            document.querySelector('#modify-compo').classList.add('d-none');
                        }


                        buttonsDelete.forEach(buttonDelete => buttonDelete.addEventListener('click', function (e) {
                            if (window.fetch) {
                                e.preventDefault ? e.preventDefault() : (e.returnValue = false);
                                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                // on récupère les valeurs
                                //let player = buttonDelete.href;
                                const url = buttonDelete.href;

                                let myInit = {
                                    method: 'GET',
                                    headers: {
                                        "Content-Type": "application/json",
                                        "Accept": "application/json, text-plain, */*",
                                        "X-Requested-With": "XMLHttpRequest",
                                        // Ici je redonne le token ) mon en-tête
                                        // sinon j'aurai une erreur Laravel
                                        "X-CSRF-TOKEN": token,
                                    },
                                };
                                fetch(url, myInit)
                                    .then(function (response) {
                                        // Je vérifie que j'ai bien un retour code : 200
                                        if (response.ok) {
                                            // Si c'est ok je capte la réponse
                                            // je la transforme transform en json()
                                            // puis je passe à la suite de mon script
                                            // Il est obligatoire de passer en deux then() deux étapes
                                            return response.json();
                                        } else {
                                            console.log('Mauvaise réponse du réseau');
                                        }
                                    })
                                    .then(function (data) {
                                        // Une fois que j'ai passé le test je récupère les informations
                                        // Je vide toujours les erreurs au chargement
                                        DeletePlayerFromView(data)
                                    })
                                    .catch(function (error) {
                                        // Ici l'erreur si le fetch n'a pas pu fonctionner
                                        console.log('Il y a eu un problème avec l\'opération fetch: ' + error.message);
                                    });

                                let DeletePlayerFromView = function (data) {

                                    buttonDelete.parentElement.parentElement.classList.add('d-none');
                                    //pour ne pas afficher le bouton valider la composition si il n'y a plus d'éléments
                                    let nbrOfElements = document.querySelector('#tableBody').childElementCount;
                                    console.log(nbrOfElements);
                                    if (nbrOfElements === 1) {
                                        document.querySelector('#modify-compo').classList.remove('d-block');
                                        document.querySelector('#modify-compo').classList.add('d-none');
                                    }


                                }
                            }


                        })); // fin du foreach des buttonsDelete


                        //ajout de l'écouteur d'évenement pour chaque bouton de la nodelist
                        buttonsAdd.forEach(button => button.addEventListener('click', function (e) {
                            // Au cas si le preventDefault ne marche pas (ex: IE10)
                            e.preventDefault ? e.preventDefault() : (e.returnValue = false);
                            // on récupère le token sinon erreur 419
                            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            // on récupère les valeurs
                            let player = button.value;

                            //display le bouton modifier la composition
                            document.querySelector('#modify-compo').classList.remove('d-none');
                            document.querySelector('#modify-compo').classList.add('d-block');

                            if (window.fetch) {
                                // exécuter ma requête fetch ici
                                // @link : https://developer.mozilla.org/fr/docs/Web/API/Fetch_API/Using_Fetch
                                //si je clique sur le un bouton delete j'envoie une requête sinon j'envoie l'autre requete par défaut
                                let myInit = {
                                    method: 'POST',
                                    headers: {
                                        "Content-Type": "application/json",
                                        "Accept": "application/json, text-plain, */*",
                                        "X-Requested-With": "XMLHttpRequest",
                                        // Ici je redonne le token ) mon en-tête
                                        // sinon j'aurai une erreur Laravel
                                        "X-CSRF-TOKEN": token,
                                    },
                                    // dans body je transmet mes données que j'encode en JSON
                                    body: JSON.stringify({
                                        player: player,
                                    })
                                };

                                fetch('', myInit)
                                    .then(function (response) {
                                        // Je vérifie que j'ai bien un retour code : 200
                                        if (response.ok) {
                                            // Si c'est ok je capte la réponse
                                            // je la transforme transform en json()
                                            // puis je passe à la suite de mon script
                                            // Il est obligatoire de passer en deux then() deux étapes
                                            return response.json();
                                        } else {
                                            console.log('Mauvaise réponse du réseau');
                                        }
                                    })
                                    .then(function (data) {
                                        // Une fois que j'ai passé le test je récupère les informations
                                        // Je vide toujours les erreurs au chargement
                                        errorElement.textContent = '';
                                        if (data[0] === 'Errors') {
                                            newError(data);
                                        } else {
                                            // Si je n'ai pas d'erreur je lance ma fonction
                                            newTab(data);

                                        }
                                    })
                                    .catch(function (error) {
                                        // Ici l'erreur si le fetch n'a pas pu fonctionner
                                        console.log('Il y a eu un problème avec l\'opération fetch: ' + error.message);
                                    });
                            } // Fin de Fetch
                            //fonctions qui gèrent affichage dans le tableau des joueurs de la compo
                            //rajouter dans le tableau de la compo
                            let newTab = function (data) {
                                let idPlayer = data.id;

                                let deleteRoute = "{{route('match.delete.player', ":id")}}";
                                deleteRoute = deleteRoute.replace(':id', data.id);

                                errorElement.classList.add('d-none');
                                document.querySelector('#modify-compo').classList.remove('d-none');
                                document.querySelector('#modify-compo').classList.add('d-block');

                                let nbrOfElements = document.querySelector('#tableBody').childElementCount;
                                console.log(document.querySelector('#tableBody').childElementCount);
                                if (nbrOfElements === 0) {
                                    document.querySelector('#modify-compo').classList.remove('d-none');
                                    document.querySelector('#modify-compo').classList.add('d-block');
                                }
                                let position = '';
                                if (data.position === "G") {
                                    position = 'Arrière';
                                } else if (data.position === "F") {
                                    position = 'Ailier';
                                } else {
                                    position = 'Pivot';
                                }

                                table.insertAdjacentHTML('beforeend',
                                    '<tr class="MS5card w-100">' + '<td>' + '</td>' +
                                    '<td width="50%">' + '<img src="https://nba-players.herokuapp.com/players/' + data.lastname + "/" + data.name + '"' + ' class="w-100 rounded-circle pr-1">' + '</td>' +
                                    '<td>' +
                                    '<td>' + position + '</td>' +
                                    '<td class="ml-3">' + data.name + '</td>' +
                                    '<td class="ml-3">' + data.lastname + '</td>' +
                                    '</tr>');
                            };
                            let newError = function (errors) {
                                errorElement.textContent = '';
                                errorElement.classList.remove('d-none');
                                // Je fais volontairement passer le compteur à 1
                                // Pour ne pas afficher le Errors juste les messages
                                for (let i = 1; i < errors.length; i++) {
                                    errorElement.insertAdjacentHTML('beforeend', errors[i] + '<br>');
                                }
                            }
                            //supprimer les joueurs de la view

                        })); // Fin de l'écouteur
                    })(); // Fin de mon script

                    // RECUPERATION DES ENCHERES EN COURS
                });
            </script>
@endsection
