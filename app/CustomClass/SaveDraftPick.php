<?php


namespace App\CustomClass;


use App\Model\Auction;
use App\Model\Player;
use App\Model\Team;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class SaveDraftPick extends Command
{
    protected $signature = 'SaveDraftPick';

    public function handle()
    {
        date_default_timezone_set ( 	'Europe/Paris' );
        $auctions = Auction::orderBy('auction', 'desc')->get();
        $now = new \DateTime();

        foreach ($auctions as $auction){
            //date limite à partir de laquelle le joueur est enregistré dans la table pivot player_team
            $limitTime = new \DateTime($auction->auction_time_limit);
            if($auction->bought === 0 && $limitTime <= $now) {
                // booléen bought pour enregistrer l(enchère comme validée
                $auction->update(['bought' => 1]);
                $player = Player::find($auction->player_id);

                //créer le lien entre le joueur et l'équpe dans la table pivot
                $player->teams()->attach($auction->team_id);

                //met à jour le nouveau salary cap
                $team = Team::where('id', $auction->team_id)->get()->first();
                $newSalaryCap = $team->salary_cap - $auction->auction;

                Team::where('id', $auction->team_id)->update(['salary_cap'=> $newSalaryCap]);
            }
        }

    }
}
