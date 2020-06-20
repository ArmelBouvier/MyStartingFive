<?php

namespace App\Http\Controllers;

use App\Model\League;
use App\Model\Match;
use App\Model\Player;
use App\Model\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //-------------------------------------  RECUPERATION DONNES UTILISATEUR -------------------------------------//
        // récupération des données users
        $user = Auth::user();
        $userId = $user->id;
        $usersInleagues = DB::table('league_user')
            ->where('league_user.user_id', $userId)
            ->exists();

        //récupérer l'équipe favorite du joueur pour lui afficher le logo correspondant

        $userHasLogo = $user->nbaTeams;

        if(!$userHasLogo) {
            $userLogo ='/storage/images/leagues_portal/picto_league_publique.png';
        } else {
            $userLogo ='/storage/images/logos/' . $user->nbaTeams->name . '.png';
        }

        // Twitterfeed de l'équipe favorite de l'utilisateur
        $userTwitterFeed = $user->nbaTeams;

        if ($usersInleagues === true){
            $userLeague = DB::table('league_user')
                ->where('league_user.user_id', $userId)
                ->first();
            $userLeagueId = $userLeague->league_id;
            $userCurrentLeague = League::where('leagues.id', '=', $userLeagueId)->first();


            $userHasTeam = DB::table('teams')
                ->where('teams.user_id', '=', $userId)
                ->exists();

            if ($userHasTeam === true){
                $userLeagueActive = $userCurrentLeague->isActive;
                $userTeamId = $user->team->id;
                $userHasPlayers = DB::table('player_team')
                    ->where('player_team.team_id', $userTeamId)
                    ->exists();

                if ($userLeagueActive === 1 && $userHasPlayers === true){
                    $draftIsOver = $userCurrentLeague->draft->is_over;

                    if ($draftIsOver === 1) {
                        // league à laquelle appartient l'utilisateur
                        $userLeagueId = $user->team->league_id;

                        //$userNameLeague récupére Le nom de la league à laquelle appartient l'utilisateur
                        $userNameLeague = $user->team->getLeague->name;

                        // $userLeagueTeams récupére le nombre d'équipe dans la league
                        $userLeagueTeams = $user->leagues[0]->teams;
                        //dd($userLeagueTeams);

                        $userLeague = $user->leagues[0];

                        // $userTeamId récupère l'id de la team de l'utilisateur
                        $userTeamId = $user->team->id;
                        //dd($userTeamId );

                        //Récupération des matchs associés à chaque team de la league
                        $allLeagueMatches = Match::where('league_id', $userLeagueId)->get();

                        //recupération des équipes de la league
                        $allLeagueTeams = Team::where('league_id', $userLeagueId)->get();
                        $teamsID = [];
                        //stocakge des ID de chaque équipe depuis Team dans un tableau
                        foreach ($allLeagueTeams as $leagueTeam) {
                            $teamsID[]= $leagueTeam->id;
                        }
                        // Calcul du pourcentage de victoire de chaque équipe de la league
                        $teamVictoryRatio = [];
                        foreach ($teamsID as $team){

                            $teamWiningCount2 = Match::where('team_wining', '=', $team)->count();
                            $teamHomeCount = Match::where('home_team_id', $team)->count();
                            $teamAwayCount = Match::where('away_team_id', $team)->count();
                            $teamCountSum = $teamHomeCount + $teamAwayCount;
                            if ($teamCountSum !== 0) {
                                $teamVictoryRatio[$team] = number_format((($teamWiningCount2 /  $teamCountSum) * 100), 2, '.', '');
                            } else {
                                $teamVictoryRatio[$team] = '0%';
                            }
                        }


                        //-------------------------  RECUPERATION  DONNES JOEURS DE LA TEAM DE L'UTILISATEUR -------------------------//

                        // $team récupère l'équipe de l'utilisateur
                        $userTeam = Team::where('user_id', $user->id)->first();


                        // $userTeamId récupère l'id de la team de l'utilisateur
                        $userTeamId = $user->team->id;


                        // $userBestPlayersTeam récupère les 5 meilleurs joueurs de l'utilisateur dans son équipe
                        $userBestPlayersTeam = $userTeam->getPlayers->sortByDesc('score')->take(5);

                        //------------------------------------------  RECUPERATION DONNES  MATCH --------------------------------------//

                        // $allMatchs récupère tout les matchs présent dans match
                        $allMatchs = Match::all();
                        //dd($allMatchs);

                        // $userMatchs récupère tout les matchs jouer par l'utilisateur dans match
                        $userMatchs = Match::where([['league_id', $userLeagueId], ['away_team_id', $userTeam->id]])->orwhere([['league_id', $userLeagueId], ['home_team_id', $userTeam->id]])->get();

                        //----------------------------------  RECUPERATION DONNES DU  PROCHAIN  MATCH --------------------------------//

                        // $userNextMatchs récupère le prochain matchs jouer par l'utilisateur dans match
                        $userNextMatchs = Match::where(function ($query) use ($userLeagueId, $userTeam) {
                            $query->where(['league_id' => $userLeagueId, 'away_team_id' => $userTeam->id])
                                ->orwhere(['league_id' => $userLeagueId, 'home_team_id' => $userTeam->id]);
                        })
                            ->whereNull('home_team_score')
                            ->whereNotNull('away_team_id')
                            ->orderBy('start_at', 'desc')
                            ->first();

                        if ($userNextMatchs != null) {

                            // $homeTeamNextMatch récupère le nom de l'équipe home qui joue dans prochain matchs
                            $homeTeamNextMatch = Team::where('id', $userNextMatchs->home_team_id)
                                ->get()
                                ->first();

                            //  $awayTeamNextMatch récupère le nom de l'équipe away qui joue dans prochain matchs
                            $awayTeamNextMatch = Team::where('id', $userNextMatchs->away_team_id)
                                ->get()
                                ->first();

                            // $userHomeNextMatch récupère l'utilisateur de l'équipe home qui à joue dans prochain matchs
                            $userHomeNextMatch = $homeTeamNextMatch->userTeam;

                            // $userHomeNextMatchLogo récupère le logo de l'utilisateur de l'équipe home qui à joue dans prochain matchs
                            $userHomeNextMatchHasLogo = $user->nbaTeams;

                            if (!$userHomeNextMatchHasLogo) {
                                $userHomeNextMatchLogo = '/storage/images/leagues_portal/picto_league_publique.png';
                            } else {
                                $userHomeNextMatchLogo = '/storage/images/logos/' . $userHomeNextMatch->nbaTeams->name . '.png';
                            }


                            // $userAwayNextMatch récupère l'utilisateur de l'équipe away qui à joue dans prochain matchs
                            $userAwayNextMatch = $awayTeamNextMatch->userTeam;

                            // $userAwayNextMatchLogo récupère le logo de l'utilisateur de l'équipe home qui à joue dans prochain matchs
                            $userAwayNextMatchHasLogo = $user->nbaTeams;
                            if (!$userAwayNextMatchHasLogo) {
                                $userAwayNextMatchLogo = '/storage/images/leagues_portal/picto_league_publique.png';
                            } else {
                                $userAwayNextMatchLogo = '/storage/images/logos/' . $userAwayNextMatch->nbaTeams->name . '.png';
                            }

                        } else {
                            $homeTeamNextMatch = 'Match fini';
                            $awayTeamNextMatch = 'Match fini';
                            $userHomeNextMatch = 'Pas d\'utilisateur';
                            $userAwayNextMatch = 'Pas d\'utilisateur';
                            $userHomeNextMatchLogo = 'Pas de logo';
                            $userAwayNextMatchLogo = 'Pas de logo';

                        }


                        //----------------------------------  RECUPERATION DONNES DU DERNIER  MATCH --------------------------------//

                        // $userLastMatch récupère le dernier match jouer par l'utilisateur dans match
                        $userLastMatch = Match::where(function ($query) use ($userLeagueId, $userTeam) {
                            $query->where(['league_id' => $userLeagueId, 'away_team_id' => $userTeam->id])
                                ->orwhere(['league_id' => $userLeagueId, 'home_team_id' => $userTeam->id]);
                        })
                            ->whereNotNull('home_team_score')
                            ->whereNotNull('away_team_id')
                            ->orderBy('start_at', 'desc')
                            ->first();

                        if ($userLastMatch != null) {
                            // $homeTeamLastMatch récupère le nom de l'équipe home qui à jouer dans le dernier matchs
                            $homeTeamLastMatch = Team::where('id', $userLastMatch->home_team_id)
                                ->get()
                                ->first();

                            // $awayTeamLastMatch récupère le nom de l'équipe away qui à jouer dans le dernier matchs
                            $awayTeamLastMatch = Team::where('id', $userLastMatch->away_team_id)
                                ->get()
                                ->first();

                            // $userHomeLastMatch récupère l'utilisateur de l'équipe home qui à jouer dans le dernier matchs
                            $userHomeLastMatch = $homeTeamLastMatch->userTeam;

                            // $userHomeLastMatchLogo récupère le logo de l'utilisateur de l'équipe home qui à joue dans prochain matchs
                            $userHomeLastMatchHasLogo = $user->nbaTeams;

                            if (!$userHomeLastMatchHasLogo) {
                                $userHomeLastMatchLogo = '/storage/images/leagues_portal/picto_league_publique.png';
                            } else {
                                $userHomeLastMatchLogo = '/storage/images/logos/' . $userHomeLastMatch->nbaTeams->name . '.png';
                            }

                            // $userAwayLastMatch récupère l'utilisateur de l'équipe away qui à jouer dans le dernier matchs
                            $userAwayLastMatch = $awayTeamLastMatch->userTeam;

                            // $userAwayLastMatchHasLogo récupère le logo de l'utilisateur de l'équipe home qui à joue dans prochain matchs
                            $userAwayLastMatchHasLogo = $user->nbaTeams;

                            if (!$userAwayLastMatchHasLogo) {
                                $userAwayLastMatchLogo = '/storage/images/leagues_portal/picto_league_publique.png';
                            } else {
                                $userAwayLastMatchLogo = '/storage/images/logos/' . $userAwayLastMatch->nbaTeams->name . '.png';
                            }


                        } else {
                            $homeTeamLastMatch = 'Match pas fini';
                            $awayTeamLastMatch = 'Match pas fini';
                            $userHomeLastMatch = 'Pas d\'utilisateur';
                            $userAwayLastMatch = 'Pas d\'utilisateur';
                            $userHomeLastMatchLogo = 'Pas de logo';
                            $userAwayLastMatchLogo = 'Pas de logo';
                        }


                        return view('dashboard.index')
                            ->with('user', $user)
                            ->with('userHomeNextMatchLogo', $userHomeNextMatchLogo)
                            ->with('userAwayNextMatchLogo', $userAwayNextMatchLogo)
                            ->with('userHomeLastMatchLogo', $userHomeLastMatchLogo)
                            ->with('userAwayLastMatchLogo', $userAwayLastMatchLogo)
                            ->with('userTwitterFeed', $userTwitterFeed)
                            ->with('userLogo', $userLogo)
                            ->with('league', $userLeague)
                            ->with('userLeagueTeams', $userLeagueTeams)
                            ->with('teamVictoryRatio', $teamVictoryRatio)
                            ->with('userLeague', $userLeague)
                            ->with('team', $user->team)
                            ->with('userTeamId',$userTeamId)
                            ->with('draftIsOver', $draftIsOver)
                            ->with('userBestPlayersTeam', $userBestPlayersTeam)
                            ->with('homeTeamNextMatch', $homeTeamNextMatch)
                            ->with('userHomeNextMatch', $userHomeNextMatch)
                            ->with('awayTeamNextMatch', $awayTeamNextMatch)
                            ->with('userAwayNextMatch', $userAwayNextMatch)
                            ->with('homeTeamLastMatch', $homeTeamLastMatch)
                            ->with('userHomeLastMatch', $userHomeLastMatch)
                            ->with('awayTeamLastMatch', $awayTeamLastMatch)
                            ->with('userAwayLastMatch', $userAwayLastMatch)
                            ->with('userLastMatch', $userLastMatch)
                            ->with('userNextMatchs', $userNextMatchs);

                    }else{
                        return view('dashboard.index')
                            ->with('user', $user)
                            ->with('userTwitterFeed', $userTwitterFeed)
                            ->with('league', $userLeague)
                            ->with('team', $user->team)
                            ->with('draftIsOver', $draftIsOver);
                    }
                }else{
                    return view('dashboard.index')
                        ->with('user', $user)
                        ->with('userTwitterFeed', $userTwitterFeed)
                        ->with('league', $userLeague)
                        ->with('team', $user->team);
                }
            }else{
                return view('dashboard.index')
                    ->with('user', $user)
                    ->with('userTwitterFeed', $userTwitterFeed)
                    ->with('league', $userLeague)
                    ->with('team', $user->team);
            }
        }else{
            return view('dashboard.index')
                ->with('user', $user)
                ->with('userTwitterFeed', $userTwitterFeed)
                ;
        }
    }
}
