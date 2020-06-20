<?php


namespace App\CustomClass\League;


use App\Model\League;
use App\Model\Match;
use App\Model\Team;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class EndOfLeague extends Command
{
    protected $signature = 'EndOfLeague';

    public function handle()
    {
        $allLeagues = League::all();


        foreach ($allLeagues as $league) {
            date_default_timezone_set ( 	'Europe/Paris' );
           $lastMatch = Match::where('league_id', $league->id)->orderBy('start_at','desc')->first();
           //temps limite avant la suppresion de la ligue
            $limitTime = Carbon::parse($lastMatch->start_at)->addminutes(5);

           if($lastMatch->start_at <= $limitTime
               && $lastMatch->team_wining !== Null) {

               $allTeamsFromLeague = $league->teams;
               $allTeamsFromLeagueIds =$allTeamsFromLeague->pluck('id')->toArray();


               //suppression de l'ensemble des relations match joueurs de la ligue
              $DeletePlayerFromMatch = DB::table('match_player')
                   ->whereIn('match_id', $allTeamsFromLeagueIds)
                   ->delete();


               //tous les ids des matchs de la ligue
               $deleteMatchesFromLeague = Match::where('league_id', $league->id)->delete();


               //Suppression de l'ensemble des relations entres joueurs et équipe de la ligue
               $deletePlayerTeamRelationship = DB::table('player_team')
                   ->whereIn('team_id', $allTeamsFromLeagueIds)
                   ->delete();



               //suppresion de toutes les équipes de la ligne
               $deleteAllTeamsFromLeague = Team::whereIn('id', $allTeamsFromLeagueIds)->delete();




           }


        }
    }
}
