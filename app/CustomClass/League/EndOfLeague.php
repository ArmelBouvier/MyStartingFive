<?php


namespace App\CustomClass\League;


use App\Mail\Register;
use App\Model\League;
use App\Model\Match;
use App\Model\Team;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EndOfLeague extends Command
{
    protected $signature = 'EndOfLeague';

    public function handle()
    {
        $allLeagues = League::all();


        foreach ($allLeagues as $league) {
            date_default_timezone_set('Europe/Paris');
            $lastMatch = Match::where('league_id', $league->id)->orderBy('start_at', 'desc')->first();
            //temps limite avant la suppresion de la ligue
            $limitTime = Carbon::parse($lastMatch->start_at)->addminutes(5);

            if($lastMatch->start_at === now()) {
                //Envoi d'un mail pour la fin de la ligue
                //Récupération des emails des membres de la league

                $userInLeague = $league->users;
                $userEmails = $userInLeague->pluck('email')->toArray();

                // Envoi d'un mail de lancement de la draft
                $title = 'Fin de la ligue !';
                $content = 'Salut, ta league ' . $league->name .
                    ' viens de se terminer ! Connecte toi vite pour voir les résultats !';

                Mail::to($userEmails)->send(new Register($title, $content));
            }

            if ($lastMatch->start_at <= $limitTime
                && $lastMatch->team_wining !== Null) {

                $allTeamsFromLeague = $league->teams;
                $allTeamsFromLeagueIds = $allTeamsFromLeague->pluck('id')->toArray();

                //tous les matchs de la ligue
                $allMatchesFromLeague = Match::where('league_id', $league->id)->pluck('id')->toArray();

                //suppression de l'ensemble des relations match joueurs de la ligue
                $DeletePlayerFromMatch = DB::table('match_player')
                    ->whereIn('match_id', $allMatchesFromLeague)
                    ->delete();


                //tous les ids des matchs de la ligue
                $deleteMatchesFromLeague = Match::where('league_id', $league->id)->delete();


                //Suppression de l'ensemble des relations entres joueurs et équipe de la ligue
                $deletePlayerTeamRelationship = DB::table('player_team')
                    ->whereIn('team_id', $allTeamsFromLeagueIds)
                    ->delete();

                //suppression de toutes les enchères faites par les utilisateurs de la ligue
                $deleteAuctionsTeamRelationship = DB::table('auctions')
                    ->whereIn('team_id', $allTeamsFromLeagueIds)
                    ->delete();

                //suppression de la draft
                $deleteLeagueDraft = $league->draft->delete();

                //suppresion de toutes les équipes de la ligne
                $deleteAllTeamsFromLeague = Team::whereIn('id', $allTeamsFromLeagueIds)->delete();

                //suppression du lien entre les utilisateurs et la ligue
                $deleteUserLeagueRelationship = DB::table('league_user')
                    ->where('league_id', $league->id)
                    ->delete();


                //suppression du role créateur du créateur de la ligue
                $deleteLeagueCreatorRelationship = DB::table('role_user')
                    ->where('user_id', $league->user_id)
                    ->where('role_id', 3)
                    ->delete();

                //suppression de la ligue
                $league->delete();

            }


        }
    }
}
