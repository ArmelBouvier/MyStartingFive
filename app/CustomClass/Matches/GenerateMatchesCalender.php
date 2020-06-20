<?php


namespace App\CustomClass\Matches;


use App\Mail\Register;
use App\Model\Auction;
use App\Model\Draft;
use App\Model\League;
use App\Model\Match;
use App\Model\Player;
use App\Model\Team;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class GenerateMatchesCalender extends Command
{
    protected $signature = 'GenerateMatchesCalender';

    public function handle()
    {
        $drafts = Draft::all();
        date_default_timezone_set ( 	'Europe/Paris' );
        foreach ($drafts as $draft) {

            //date limite à partir de laquelle la draft prend fin et le calendrier des matches est généré
            $limitTime = $draft->ends_at;

            if($draft->is_over === 0 && $limitTime <= now()) {

            // booléen bought pour enregistrer la draft comme terminée
           $draft->update(['is_over' => 1]);

            //récupérer tous les ID des équipes présentes dans la ligue
            $matches = [];

            $allTeams = Team::where('league_id', $draft->league_id)->orderBy('id')->get();


//  --------- GENERE LE CALENDRIER DES MATCHS --------------------------//
                $teams = [];
                foreach ($allTeams as $teamid) {
                    $teams[] = $teamid->id;
                }

                $teamCount = count($teams);
                $byeTeam = -1;
                $rounds = ['guest' => [], 'visitor' => []];
                if ($teamCount % 2 === 1) {
                    $teams[] = $byeTeam;
                    ++$teamCount;
                }
                for ($round = 0; $round < $teamCount - 1; ++$round) {
                    $rounds['guest'][$round] = [];
                    $rounds['visitor'][$round] = [];
                    for ($i = 0; $i < $teamCount / 2; ++$i) {
                        if ($teams[$i] !== $byeTeam && $teams[$teamCount - 1 - $i] !== $byeTeam) {
                            $rounds['guest'][$round][] = [$teams[$i], $teams[$teamCount - 1 - $i]];
                            $rounds['visitor'][$round][] = [$teams[$teamCount - 1 - $i], $teams[$i]];
                        }
                    }
                    array_splice($teams, 1, 0, array_pop($teams));
                }
                $scheduleGames = $rounds['guest'];
                array_push($scheduleGames, ...$rounds['visitor']);
                shuffle($scheduleGames);


                $i = 0;
                foreach ($scheduleGames as $scheduleGame) {

                    if ($i === 0) {

                        $matchTime = now()->addMinutes(5);
                    } else {
                        $matchTime = now()->addMinutes(5 + $i);

                    }

                    foreach ($scheduleGame as $game) {
                        date_default_timezone_set ( 	'Europe/Paris' );
                        $matchDatas = [
                            [
                                'home_team_id' => $game[0],
                                'away_team_id' => $game[1],
                                'league_id' => $draft->league_id,
                                'start_at' => $matchTime,
                            ]
                        ];
                        //insert les données des matchs dans
                        Match::insert($matchDatas);
                    }
                    $i++;
                }

//--------------------- RAJOUTE DES JOUEURS ALEATOIRES DANS LES EQUIPES QUI NE SONT PAS COMPLETES A TEMPS --------------------- //

//                    RECUPERATION DE JOUEURS ALEATOIRES PEU CHER
                $randomPlayers = Player::where('price', '<', 4)->take(50)->get();

                $randomCenters = [];
                $randomGuards = [];
                $randomForwards = [];


                foreach ($randomPlayers as $randomPlayer) {
                    $playersInfos = json_decode($randomPlayer->data);
                    $randomPlayerPosition = substr($playersInfos->pl->pos, 0, 1);

                    if ($randomPlayerPosition === "F") {
                        $randomForwards[] = $randomPlayer;
                    } elseif ($randomPlayerPosition === "C") {
                        $randomCenters[] = $randomPlayer;
                    } else {
                        $randomGuards[] = $randomPlayer;
                    }
                }

//                POUR CHAQUE EQUIPE AJOUT DU NOMBRE DE JOUEURS NECESSAIRES POUR QUE LE ROSTER SOIT COMPLET
            foreach ($allTeams as $team) {
                //max des joueurs autorisés dans l'équipe
                $maxPlayers = 12;
                if (count($team->getPlayers) < $maxPlayers) {

                    $maxCenters = 2;
                    $maxForward = 5;
                    $maxGuard = 5;
                    $centers = [];
                    $guards = [];
                    $forwards = [];
                    $teamPlayers = $team->getPlayers;

                    // vérifie que l'utilisatgeu ait bien des joueurs dans son équipe
                    if(!empty($teamPlayers)) {
                        foreach ($teamPlayers as $teamPlayer) {

                            $playersInfos = json_decode($teamPlayer->data);

                            $position = substr($playersInfos->pl->pos, 0, 1);

                            if ($position === "F") {
                                $forwards[] = $teamPlayer;
                            } elseif ($position === "C") {
                                $centers[] = $teamPlayer;
                            } else {
                                $guards[] = $teamPlayer;
                            }
                        }
                    } else {
                        $centers = [];
                        $guards = [];
                        $forwards = [];
                    }

                    if (count($forwards) < 5) {
                        $numberToAdd = 6 - count($forwards);
                        for ($i = 1; $i < $numberToAdd; $i++) {
                            $team->getplayers()->attach($randomForwards[$i]->id);
                        }

                    }

                    if (count($guards) < 5) {
                        $numberToAdd = 6 - count($guards);

                        for ($i = 1; $i < $numberToAdd; $i++) {
                            $team->getplayers()->attach($randomGuards[$i]->id);
                        }

                    }
                    if (count($centers) < 2) {
                        $numberToAdd = 3 - count($centers);
                        for ($i = 1; $i < $numberToAdd; $i++) {
                            $team->getplayers()->attach($randomCenters[$i]->id);
                        }
                    }

                }

            }

// ---------------- ENVOI D'EMAIL AUX UTILISATEURS PRESENTS DANS LA LIGUE  --------------------------//
                $leagueId = $draft->league;

                //Récupération des emails des membres de la league
                $userEmails = [];
                $users = DB::table('league_user')
                    ->leftjoin('users', 'id', '=', 'league_user.user_id')
                    ->where('league_user.league_id', (int)$leagueId->id)
                    ->get();
                foreach ($users as $user) {
                    $userEmails[]= $user->email;
                }

                // Envoi d'un mail de lancement de la draft
                $title = 'Fin de la draft !';
                $content = 'Salut, ta league ' . $leagueId->name .
                    ' viens de terminer sa draft ! Tu peux dès à présent faire ta composition pour ton prochain match';

                Mail::to($userEmails)->send(new Register($title, $content));

            }
        }
    }
}
