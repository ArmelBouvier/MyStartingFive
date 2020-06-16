<?php

namespace App\Http\Controllers;

use App\Model\Match;
use App\Model\Player;
use App\Model\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    //
    public function index()
    {
        //-------------------------------------  RECUPERATION DONNES UTILISATEUR -------------------------------------//
        // récupération des données users
        $user = Auth::user();
        //dd($user);


        // league à laquelle appartient l'utilisateur
        $userLeagueId = $user->team->league_id;
        //dd($userLeagueId);

        // Le nom de la league à laquelle appartient l'utilisateur
        $userNameLeague = $user->team->getLeague->name;
        //dd($userNameLeague);

        // $team récupère l'équipe de l'utilisateur
        $userTeam = Team::where('user_id', $user->id)->first();
        //dd($userTeam);

        //-------------------------  RECUPERATION  DONNES JOEURS DE LA TEAM DE L'UTILISATEUR -------------------------//


        // $userPlayersTeam récupère tout joueurs de l'utilisateur dans ça team
        $userPlayersTeam = $userTeam->getPlayers;
        //dd($userPlayersTeam);


        //------------------------------------- CALCUL DU SCORE D'UNE TEAM -------------------------------------------//

        // $playersHomeTeamMatchs récupère les joeurs de la home_team du matchs en cours

        $playersHomeTeamMatchs = Match::where([['league_id', $userLeagueId],['home_team_id', $userTeam->id]])->get();
        //dd( $playersHomeTeamMatchs);




        // Calcule du score de la team de l'utilisateur
        $scoreTeam = 0;
        foreach ($userPlayersTeam as $playersTeam){
            $scoreTeam += $playersTeam->score;
        }
        //dd($scoreTeam);

        //dd($user->match->homeTeamName());
        //------------------------------------------  RECUPERATION DONNES  MATCH --------------------------------------//

        // $allMatchs récupère tout les matchs présent dans match
        $allMatchs = Match::all();
        //dd($allMatchs);


        //  $AllHomeTeamsNames récupère tout les noms des équipes qui sont à domicile dans les matchs
        // $AllTeamsNames permet d'avoir un tableau avec tout les noms de tout les équipes.

        $AllHomeTeamsNames = [];
        $AllTeamsNames =  [];
        foreach ( $allMatchs as $match) {
            $AllHomeTeamsNames[] = $match->homeTeamName;
            $AllTeamsNames[] =  $AllHomeTeamsNames;
        }

        //  $AllAwayTeamsNames  récupère tout les noms des équipes qui sont en tant que visiteur dans les matchs
        $AllAwayTeamsNames = [];
        foreach ( $allMatchs as $match) {
            $AllAwayTeamsNames[] = $match->awayTeamName;
            $AllTeamsNames[] =   $AllAwayTeamsNames;
        }
        //dd($AllTeamsNames);



        // $userMatchs récupère tout les matchs jouer par l'utilisateur dans match
        $userMatchs  = Match::where([['league_id', $userLeagueId],['away_team_id', $userTeam->id]])->orwhere([['league_id', $userLeagueId],['home_team_id', $userTeam->id]])->get();
        //dd($userMatchs);


        // $userNextMatchs récupère le prochain matchs jouer par l'utilisateur dans match
        $userNextMatchs = Match::whereNull('home_team_score')->where('league_id', $userLeagueId)->orderBy('start_at','asc')->first();
        //dd( $userNextMatchs );




        // $userLastMatch récupère le dernière matchs jouer par l'utilisateur dans match
        $userLastMatch  = Match::where([['league_id', $userLeagueId],['away_team_id', $userTeam->id]])
            ->orwhere([['league_id', $userLeagueId],['home_team_id', $userTeam->id]])
            ->whereNotNull('home_team_score')
            ->orderBy('start_at','desc')
            ->get()
            ->first();
        //dd($userLastMatch);

        $hometeamLastMatch = Team::where('id', $userLastMatch->home_team_id)
            ->get()
            ->first();
        //dd($hometeamLastMatch);

        $awayteamLastMatch = Team::where('id', $userLastMatch->away_team_id)
            ->get()
            ->first();
        dd($awayteamLastMatch);




        return view('match.index');
    }
}
