<?php


namespace App\CustomClass\Matches;


use App\Model\Match;
use App\Model\Team;
use Illuminate\Console\Command;

class PlayWeeklyMatches extends Command
{
    protected $signature = 'PlayWeeklyMatches';

    public function handle(){
        $matches = Match::all();

        foreach ($matches as $match) {
            $limitTime = $match->start_at;
            // Calcule du score de la team de l'utilisateur

            if($limitTime <= now()) {
                $allPlayers = $match->matchPlayers;
                $scoreHomeTeam = 0;
                $scoreAwayTeam = 0;

                foreach ($allPlayers as $player) {
                    // récupérer la home team pour vérifier que leur joueur lui apparatiennne et lui rajouter les points sinon
                    //les points sont rajoutés dans la away team
                    $homeTeam = Team::where('id', $match->home_team_id)->get()->first();
                    $HomeTeamPlayers = [];
                    $playersFromHomeTeam = $homeTeam->getPlayers;
                    foreach ($playersFromHomeTeam as $playerFromHomeTeam){
                        $HomeTeamPlayers[] = $playerFromHomeTeam->id;
                    }
                    if(in_array($player->id, $HomeTeamPlayers,true)) {
                        $scoreHomeTeam += $player->score;
                    } else {
                        $scoreAwayTeam += $player->score;
                    }
                };

                $match->home_team_score = $scoreHomeTeam;
                $match->away_team_score = $scoreAwayTeam;
                if($scoreHomeTeam > $scoreAwayTeam){
                    $match->team_wining = $match->home_team_id;
                    $match->team_losing = $match->away_team_id;
                } else {
                    $match->team_wining = $match->away_team_id;
                    $match->team_losing = $match->home_team_id;
                }
                $match->save();
            }

        }
    }
}
