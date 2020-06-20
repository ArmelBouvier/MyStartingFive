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
        <h1 class="my-5 text-white">Récapitulatif de la league {{ $league->name }}</h1>
        <div class="row mb-3">
            <div class="col-md-6 text-left"><h2
                    class="mb-3 text-white">{{$league->number_teams - $league->users->count()}} place(s) restante(s)
                    sur {{$league->number_teams}}</h2></div>

            <div class="col-md-6 text-right">
                <div class="row">
                    @if($league->isActive === 1 && $draftStatus === 0)
                        <a href="{{route('draft.index')}}" class="btn btn-outline-secondary">Rejoindre la draft</a>
                    @else($league->isActive === 0)
                        @if(Auth::user()->id === $league->user->id && $draftStatus === 0)
                            <form action="{{ route('leagues.update', $league->id)}}" method="post">
                                @csrf
                                @method('PATCH')
                                <input type="text" class="d-none form-control" name="isActive" value="1"/>
                                <button class="bouton-inscription" type="submit">Lancer la league</button>
                            </form>
                            <form action="{{ route('leagues.destroy', $league->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="bouton-connexion" type="submit">Supprimer la league</button>
                            </form>
                        @endif
                    @endif

                </div>

            </div>


        </div>


        <div class="row my-5 pb-league">
            <table class="table table-striped text-white">
                <thead class="font-weight-bold">
                <tr>
                    <th class="tertiary">Participant</th>
                    <th class="tertiary">Nom d'équipe</th>
                    <th class="tertiary">% de victoires</th>
{{--                    <th class="tertiary">valeur de la team</th>--}}
                </tr>
                </thead>
                <tbody>
                {{$i = 1}}

                @foreach($league->users as $user)
                    <tr>
                        <td>{{ $user->pseudo }}</td>

                        <td>
                            @if(Auth::user()->id === $user->id && $user->team === null)
                                <a href="{{ route('teams.create')}}" class="btn btn-outline-secondary">Créér une
                                    équipe</a>
                            @else
                                @if($user->team !== null)
                                    {{$user->team->name}}
                                @else
                                    Création de l'équipe en cours.
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($league->isActive === 1)
                                @if($user->team !== null)
                                    {{$teamVictoryRatio[$user->team->id]}}
                                @else
                                    L'équipe doit être créée !
                                @endif
                            @else
                                En attente du lancement de la league !
                            @endif
                        </td>
                        <td>
                            @if($league->isActive === 1)
                                @if($user->team !== null)
                                    @if($league)
                                        {{$teamVictoryRatio[$user->team->id]}}
                                    @else
                                        La draft doit être achevée !
                                    @endif
                                @else
                                    L'équipe doit être créée !
                                @endif
                            @else
                                En attente du lancement de la league !
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


