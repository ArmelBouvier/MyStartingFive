@extends('layouts.master')

@section('content')
    <div class="container pb-league-public">
            <h1 class="my-5 text-white">Bienvenue sur le portail des Leagues publiques</h1>
            <h2 class="mb-5 text-white">Ici, pas de fairplay, que de l'humour !</h2>
            <div class="row">
                <div class="col-12">
                    <table class="table table-striped text-white">
                        <thead class="font-weight-bold">
                        <tr>
                            <th class="tertiary">Nom de la league</th>
                            <th class="tertiary">Créateur de la league</th>
                            <th class="tertiary">Places restantes</th>
                            <th class="tertiary">Rejoindre</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($leagues as $league)
{{--                            {{dd($league->users)}}--}}
                            <tr>
                                <td>{{$league->name}}</td>
                                <td>{{$league->user->pseudo}}</td>
                                <td>{{$league->number_teams - $league->users->count()}}</td>
                                @if($league->isActive == 1) )
                                    <td>League en cours</td>
                                @else
                                    @if($league->number_teams <= $league->users->count())
                                        <td>Complet</td>
                                    @else
                                        <td><a href="{{ route('leagues.joinPublicLeague', $league->id) }}" class="btn btn-secondary" >Rejoindre</a></td>
                                    @endif
                                @endif

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $leagues->links() }}
                </div>
            </div>
    </div>
@endsection


