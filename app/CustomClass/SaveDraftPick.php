<?php


namespace App\CustomClass;


use App\Model\Auction;
use App\Model\Player;
use App\Model\Team;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class SaveDraftPick extends Command
{
    protected $signature = 'SaveDraftPick';

    public function handle()
    {
        date_default_timezone_set ( 	'Europe/Paris' );
        $auctions = Auction::orderBy('auction_time_limit', 'desc')->get();
        $now = new \DateTime();


        foreach ($auctions as $auction){
            //date limite à partir de laquelle le joueur est enregistré dans la table pivot player_team
            $limitTime = new \DateTime($auction->auction_time_limit);
            //je récupère la ligne de l'enchère que je controle
            $leagueIdOfCurrentAuction = $auction->team->getLeague->id;

            //récuperer tous les ids des équipes de la ligue
            $teamsInLeague = Team::where('league_id', $leagueIdOfCurrentAuction)->pluck('id')->toArray();

            //récuperer les ids de tous les joueurs draftés par des utilisateurs de la ligue
            $allPlayersDraftedInLeague = DB::table('player_team')->whereIn('team_id',$teamsInLeague)->pluck('player_id')->toArray();

            //check si le joueur n'a pas déjà été drafté par quelqu'un de la ligue
            if(!in_array($auction->player_id, $allPlayersDraftedInLeague)) {
                // si enchere n'est pas validée et que le temps est dépassé, on va vérifier si il faut la valider
                if($auction->bought === 0 && $limitTime <= $now) {

                    //récupération de tous les enchres sur le joueur dont nous controlons l'enchere
                    $auctionsOnPlayer = Auction::where('player_id', $auction->player_id)->orderBy('auction_time_limit', 'desc')->get();

                    $checkAuction = false;
                    foreach ($auctionsOnPlayer as $auctionOnPlayer) {
                        $leagueOfAuctionOnPlayer = $auctionOnPlayer->team->getLeague->id;
                        if ($leagueIdOfCurrentAuction === $leagueOfAuctionOnPlayer && $limitTime > $auctionOnPlayer->auction_time_limit) {
                            $checkAuction = true;
                        }
                    }

                    if($checkAuction) {
                        // booléen bought pour enregistrer l(enchère comme validée
                        $auction->update(['bought' => 1]);
                        $player = Player::find($auction->player_id);

                        //créer le lien entre le joueur et l'équpe dans la table pivot
                        $player->teams()->attach($auction->team_id);

                        //met à jour le nouveau salary cap
                        $team = Team::where('id', $auction->team_id)->get()->first();
                        $newSalaryCap = $team->salary_cap - $auction->auction;

                        Team::where('id', $auction->team_id)->update(['salary_cap' => $newSalaryCap]);
                    }
                }
            }

        }

    }
}
