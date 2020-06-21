<?php


namespace App\CustomClass;


use App\Model\Player;
use Illuminate\Console\Command;

class UpdateNbaPlayersPrices extends Command
{
    protected $signature = 'UpdateNbaPlayersPrices';

    public function handle() {

        $players = Player::all();

        foreach ($players as $player) {
            $hasPlayed = json_decode($player->data)->pl->ca->gp;
            if ($hasPlayed !== null) {

                //pour récupérer les stats moyennes de la dernière saison présente dans le tableau des stats du joueur
                $generalStats = json_decode($player->data)->pl->ca->sa;

                $generalStats = last($generalStats);
                $assists = ($generalStats->ast) * 0.75;
                $points = ($generalStats->pts) * 0.5;
                $rebounds = ($generalStats->reb) * 0.6;
                $steals = ($generalStats->stl) * 1.5;
                $blocks = ($generalStats->blk) * 1.5;
                $turnovers = ($generalStats->tov) * (-0.6);
                $minutes = ($generalStats->min) * 0.2;

                $generalGrade = ($assists + $points + $rebounds + $steals + $blocks + ($turnovers) + $minutes);
                $generalGrade = round($generalGrade);
                if($generalGrade < 5) {
                    $generalGrade = 0;
                }
                Player::where('id', $player->id)->update(['price' => $generalGrade]);

            }
            echo '.';
        }


    }
}
