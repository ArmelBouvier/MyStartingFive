<?php


namespace App\CustomClass;


use App\Model\Player;
use Illuminate\Console\Command;

class StoreNbaPlayerPhotoUrl extends Command
{
    protected $signature = 'StoreNbaPlayerPhotoUrl';

    public function handle()
    {

            $players = Player::all();

            foreach ($players as $player) {
                $playerStats = json_decode($player->data)->pl;
                $url = "https://nba-players.herokuapp.com/players/" . $playerStats->ln ."/" . $playerStats->fn;

                $request = get_headers($url, 1);
                if(isset($request) && isset($request['Content-Type'])) {
                    if(is_array($request['Content-Type'])){ //In some responses Content-type is an array
                        $imageCheck = strpos($request['Content-Type'][1],'image');
                    }else{
                        $imageCheck = strpos($request['Content-Type'],'image');
                    }
                    if($imageCheck !== false) {
                        Player::where('id', $player->id)->update(['photo_url' => $url]);
                        echo'.';
                    } else {
                        Player::where('id', $player->id)->update(['photo_url' => 'image']);
                        echo'.';
                    }
                } else {
                    Player::where('id', $player->id)->update(['photo_url' => 'image']);
                    echo'.';
                }

            }


    }
}
