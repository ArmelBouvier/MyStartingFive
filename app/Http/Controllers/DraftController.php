<?php


namespace App\Http\Controllers;

use App\Model\Auction;
use App\Model\Draft;
use App\Model\League;
use App\Model\Player;
use App\Model\Team;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DraftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
//-------------  RECUPERATION DONNES GENERALES  ---------------//

        //récupération des données users
        $user = Auth::user();

        //league à laquelle appartient l'utilisateur qui fait sa draft
        $userLeagueId = $user->team->league_id;

        //récupérer l'équipe favorite du joueur pour lui afficher le logo correspondant

        $userHasLogo = $user->nbaTeams;
        if(!$userHasLogo) {
            $userLogo ='/storage/images/leagues_portal/picto_league_publique.png';
        } else {
            $userLogo ='/storage/images/logos/' . $user->nbaTeams->name . '.png';
        }

        // $team récupère l'équipe de l'utilisateur
        $team = Team::where('user_id', $user->id)->first();

        // tous les joueurs
        $players = Player::where('price', '>', 1)->orderBy('price', 'desc')->Paginate(20);

        // id de tous les joueurs draftés
        $draftedPlayers = Player::all();
        //si le joueur est déjà drafté, donc présentt dans la table pivot player_team
        $notDisplayedPlayers = [];
        foreach ($draftedPlayers as $draftedPlayer) {
            if (!empty($draftedPlayer->teams[0])) {
                if ($draftedPlayer->teams[0]->league_id === $userLeagueId) {
                    $notDisplayedPlayers[] = $draftedPlayer->id;
                }
            }
        }

        //equipes présentent dans la ligue de l'utilisateur
        $leagueTeams = Team::where('league_id', $userLeagueId)->get();

        // met toutes les équipes présente dans la ligue de l'utilisateur dans un tableau pour récupérer ensuite toutes leurs enchrèes
        $teamsInLeagueId = [];
        foreach ($leagueTeams as $teams) {
            $teamsInLeagueId[] = $teams->id;
        }


        // récupère les enchères en cours dans la ligue
        $auctionsOnPlayers = DB::table('auctions')
            ->leftjoin('players', 'players.id', '=', 'auctions.player_id')
            ->whereIn('team_id', $teamsInLeagueId)
            ->where('bought', '=',0)
            ->orderBy('auction', 'desc')
            ->get();


//------------- FILTRES AFFICHAGES JOUEURS NBA ---------------//
        // trier par prix //
        if (request()->has('order')) {
            $players = Player::where('price', '>', 1)->orderBy('price', request('order'))->Paginate(20);
        }

        // trier par position  //
        if (request()->has('position')) {
            $allPlayersFromPosition = [];
            $players = Player::all();
            foreach ($players as $player) {
                $playerPosition = json_decode($player->data)->pl->pos;
                $playerPosition = substr($playerPosition, 0, 1);
                $hasPlayed = json_decode($player->data)->pl->ca->gp;
                if ($hasPlayed !== null && $playerPosition === request('position')) {
                    $allPlayersFromPosition[] = $player->id;
                }
            }

            $players = Player::whereIn('id', $allPlayersFromPosition)
                ->where('price', '>', 1)
                ->orderBy('price', 'desc')
                ->Paginate(20);

        }

//----------- retourne toutes données relatives enchères en cours de l'utilisateur -------------------- //


        //récuperer tous les ids des équipes de la ligue
        $IdsTeamsInLeague = Team::where('league_id', $userLeagueId)->pluck('id')->toArray();

        //récuperer les ids de tous les joueurs draftés par des utilisateurs de la ligue
        $allPlayersDraftedInLeague = DB::table('player_team')->whereIn('team_id',$IdsTeamsInLeague)->pluck('player_id')->toArray();

        //retourne toutes les enchères en cours de l'utilisateur pour les joueurs non draftés
        $auctions = Auction::where([['team_id', $user->team->id],['bought', 0]])->whereNotIn('player_id',$allPlayersDraftedInLeague)->get();

        // stocker les id des joueurs sur lesquels l'utilisateur a mis une enchère pour ne plus afficher le bouton enchérir dans la view
        $auctionPlayersId = [];
        foreach ($auctions as $auction) {
            $auctionPlayersId[] = $auction->player_id;
        }

//--------------   JOUEURS DRAFTES ---------------------------------- //
        //retourne les joueurs draftés par l'utilisateur
        $drafted = $team->getPlayers;
        //tableaux pour stocker les données des joueurs selon leurs positions pour les afficher dans la view en fonction
        $forwards = [];
        $guards = [];
        $centers = [];
        foreach($drafted as $draftedPlayer) {
            $position = '';
            $draftedInfos = json_decode($draftedPlayer->data);
            $position  = substr($draftedInfos->pl->pos, 0,1);

            if($position === "F") {
                $forwards[] =  $draftedPlayer;
            } elseif($position === "C") {
                $centers[] = $draftedPlayer;
            } else {
                $guards[] = $draftedPlayer;
            }
        }
        //fin de la draft
        $draftEnd = Draft::where('league_id', $user->leagues[0]->id)->first()->ends_at;


//-------------------------- INFORMATIONS RETOURNEE DANS LA VUE ---------------------------------- //
        return view('draft.index')
            ->with('players', $players)
            ->with('team', $team)
            ->with('auctions', $auctions)
            ->with('drafted', $drafted)
            ->with('forwards', $forwards)
            ->with('guards', $guards)
            ->with('centers', $centers)
            ->with('auctionPlayersId', $auctionPlayersId)
            ->with('auctionsOnPlayers', $auctionsOnPlayers)
            ->with('notDisplayedPlayers', $notDisplayedPlayers)
            ->with('userLogo', $userLogo)
            ->with('draftEnd',$draftEnd);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Add the auction to the table.
     *
     * @param int $id
     * @return Response
     * @param Request $request
     */

    public function auction(Request $request, $id)
    {
        date_default_timezone_set ('Europe/Paris' );

        $user = Auth::user();
        $userLeagueId = $user->team->league_id;
        $leagueTeams = Team::where('league_id', $userLeagueId)->get();
        $teamsInLeagueId = [];
        foreach ($leagueTeams as $teams) {
            $teamsInLeagueId[] = $teams->id;
        }

        //recuperer l'équipe de l'utilisateur qui enregistre l'enchère
        $team = Team::where('user_id', $user->id)->get()->first();;
        $player = Player::where('id', $id)->get()->first();

        //valeur minimum enchère
        $minimumAuctionValue = 5;

        //si le joueur (id) a déjà été drafté par une autre équipe de la ligue l'ajout n'est pas possible
        $isAlreadyDrafted = $player->teams()->whereIn('team_id', $teamsInLeagueId)->get()->first();

        // ---- ENCHERES EN COURS ET SALARY CAP ACTUEL DE LUTILISATEUR -----------//

        //récuperer tous les ids des équipes de la ligue
        $IdsTeamsInLeague = Team::where('league_id', $userLeagueId)->pluck('id')->toArray();

        //récuperer les ids de tous les joueurs draftés par des utilisateurs de la ligue
        $allPlayersDraftedInLeague = DB::table('player_team')->whereIn('team_id',$IdsTeamsInLeague)->pluck('player_id')->toArray();
        $auctions = Auction::where([['team_id', $user->team->id],['bought', 0]])->whereNotIn('player_id',$allPlayersDraftedInLeague)->get();

        if($auctions) {
            $auctions = $auctions->sum("auction");
        } else {
            $auctions = 0;
        }

        //salary cap actuel de l'utilisateur
        $currentSalaryCap = $team->salary_cap;
        //salary_cap - la somme des enchères qu'il a en cours
        $moneyAvailable = $currentSalaryCap - $auctions;

//        RECUPERATION DES DONNES ENCHERE SUR LE JOUEUR SELECTIONNE PAR UTILISATEUR
        //prix actuel du joueur selon la dernière enchère faite dans la ligue
        $lastAuctionOnSelectedPlayer = Auction::whereIn('team_id', $teamsInLeagueId)
            ->where('bought', '=',0)
            ->where('player_id', $id)
            ->orderBy('auction', 'desc')
            ->first();
        //s'il n'y a pas déjà d'enchère en cours sur ce joueur, l'enchère est à 0
        if(!$lastAuctionOnSelectedPlayer) {
            $lastAuctionOnSelectedPlayer = 0;
        } else {
            $lastAuctionOnSelectedPlayer = $lastAuctionOnSelectedPlayer->auction;
        };


// ------   VERIFIER QUE LE NOMBRE MAX DE JOUEUR A DRAFTER NE SOIT PAS DEPASSER ---------//
        //retourne les joueurs draftés par l'utilisateur
        $drafted = $team->getPlayers;
        $nbDraftedPlayers = count($drafted);

        //tableaux pour stocker les données des joueurs selon leurs positions pour
        // verifier que le nombre limite pour chaque poste ne soit pas atteint
        //enregistrer chaque joueur drafté dans le tableau qui correspond à son poste
        $forwards = [];
        $guards = [];
        $centers = [];
        foreach($drafted as $draftedPlayer) {
            $position = '';
            $draftedInfos = json_decode($draftedPlayer->data);
            $position  = substr($draftedInfos->pl->pos, 0,1);
            if($position === "F") {
                $forwards[] =  $draftedPlayer;
            } elseif($position === "C") {
                $centers[] = $draftedPlayer;
            } else {
                $guards[] = $draftedPlayer;
            }
        }
        //récupération du poste du joueur sur lequel l'utilisateur veut faire une enchère
        $playerPosition = json_decode($player->data);
        $playerPosition = $playerPosition->pl->pos;
        if($playerPosition === "F") {
            $nbPosition = count($forwards);
            $limit = 5;
        } elseif($playerPosition === "C") {
            $nbPosition = count($centers);
            $limit = 2;
        } else {
            $nbPosition = count($guards);
            $limit = 5;
        }

        if(empty($isAlreadyDrafted) && $moneyAvailable >= ($player->price + $minimumAuctionValue)
            && $moneyAvailable >= ($lastAuctionOnSelectedPlayer + $minimumAuctionValue)
            && $nbDraftedPlayers < 12 && $nbPosition < $limit) {

            $auctionTimeLimit = Carbon::parse(now())->addSeconds(30)->format('Y-m-d H:i:s');

            //si la valeur de la dernière enchère existe et n'est donc pas égal à 0 selon ce qu'on a défini, la valeur de l'enchère est
            //la valeur initiale du joueur
            if($lastAuctionOnSelectedPlayer !== 0) {
                $auctionPrice = $lastAuctionOnSelectedPlayer + $minimumAuctionValue;
            } else {
                $auctionPrice = $player->price;
            }

            $data = [[
                'team_id' => $team->id,
                'player_id' => $id,
                'auction' => $auctionPrice,
                'created_at' => now(),
                'updated_at' => now(),
                'auction_time_limit' => $auctionTimeLimit,
            ]];

            Auction::insert($data);
            return back()->with('success', 'Enchère Enregistrée !');

        } else {
            if(!empty($isAlreadyDrafted)){
                $message = 'Tu as déjà  drafté ce joueur !';
            } elseif($moneyAvailable < $player->price) {
                $message = 'Ce joueur est trop cher pour toi !';
            }  elseif ($moneyAvailable < $lastAuctionOnSelectedPlayer) {
                $message = 'Tu n\'as plus assez d\'argent !';
            } elseif($nbDraftedPlayers >= 12) {
                $message = 'Tu as déjà drafté 12 joueurs !';
            } elseif ($nbPosition < $limit) {
                $message = 'Tu as atteint ton nombre limite de joueurs pour ce poste !';
            }

            return back()->with('errors','Tu n\'as pas assez d\'argent !');
        }
    }

    /**
     * Supprime l'enchère de l'utilisateur sur le joueur sélectionné
     *
     * @param int $id
     * @return Response
     */

    public function deleteAuction($id){
        $auctions = Auction::all();
        $user = Auth::user();
        //recuperer l'équipe de l'utilisateur qui enregistre l'enchère
        $team = Team::where('user_id', $user->id)->first();
        $team = $team->id;
        Auction::where([['player_id',$id], ['team_id', $team]])->delete();
        return back()->with('success', 'Enchère Supprimée !');
    }
    /**
     * Met à jour l'enchère de l'utilisateur sur le joueur sélectionné
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */

    public function updateAuction(Request $request, $id){
        date_default_timezone_set ( 	'Europe/Paris' );

        $user = Auth::user();
        $userLeagueId = $user->team->league_id;
        $leagueTeams = Team::where('league_id', $userLeagueId)->get();
        $teamsInLeagueId = [];
        foreach ($leagueTeams as $teams) {
            $teamsInLeagueId[] = $teams->id;
        }


        //recuperer l'équipe de l'utilisateur qui enregistre l'enchère
        $team = Team::where('user_id', $user->id)->get()->first();;
        $player = Player::where('id', $id)->get()->first();

        //si le joueur (id) a déjà été drafté par une autre équipe de la ligue l'ajout n'est pas possible
        $isAlreadyDrafted = $player->teams()->whereIn('team_id', $teamsInLeagueId)->get()->first();


        // ---- ENCHERES EN COURS ET SALARY CAP ACTUEL DE LUTILISATEUR -----------//

        //récuperer tous les ids des équipes de la ligue
        $IdsTeamsInLeague = Team::where('league_id', $userLeagueId)->pluck('id')->toArray();

        //récuperer les ids de tous les joueurs draftés par des utilisateurs de la ligue
        $allPlayersDraftedInLeague = DB::table('player_team')->whereIn('team_id',$IdsTeamsInLeague)->pluck('player_id')->toArray();
        $auctions = Auction::where([['team_id', $user->team->id],['bought', 0]])->whereNotIn('player_id',$allPlayersDraftedInLeague)->get();
        if($auctions) {
            $auctions = $auctions->sum("auction");
        } else {
            $auctions = 0;
        }

        //salary cap actuel de l'utilisateur
        $currentSalaryCap = $team->salary_cap;
        //salary_cap - la somme des enchères qu'il a en cours
        $moneyAvailable = $currentSalaryCap - $auctions;

        //prix actuel du joueur selon la dernière enchère faite dans la ligue
        // récupère les enchères en cours dans la ligue
        $auctionsOnPlayers = DB::table('auctions')
            ->leftjoin('players', 'players.id', '=', 'auctions.player_id')
            ->whereIn('team_id', $teamsInLeagueId)
            ->where('bought', '=',0)
            ->orderBy('auction', 'desc')
            ->get();

        //tableau pour stocker les ids de tous les joueurs qui ont des enchères en cours sur celle ligue
        $playerLatestAuction = 0;
        foreach ($auctionsOnPlayers as $auctionsOnPlayer){
            if($auctionsOnPlayer->player_id === $id ){
                $playerLatestAuction = $auctionsOnPlayer->auction;
            }
        }

        //prix actuel du joueur selon la dernière enchère faite dans la ligue
        $lastAuctionOnSelectedPlayer = Auction::whereIn('team_id', $teamsInLeagueId)
            ->where('bought', '=',0)
            ->where('player_id', $id)
            ->orderBy('auction', 'desc')
            ->first();
        //s'il n'y a pas déjà d'enchère en cours sur ce joueur, l'enchère est à 0
        if(!$lastAuctionOnSelectedPlayer) {
            $lastAuctionOnSelectedPlayer = 0;
        } else {
            $lastAuctionOnSelectedPlayer = $lastAuctionOnSelectedPlayer->auction;
        };

// ------   VERIFIER QUE LE NOMBRE MAX DE JOUEUR A DRAFTER NE SOIT PAS DEPASSER ---------//
        //retourne les joueurs draftés par l'utilisateur
        $drafted = $team->getPlayers;
        $nbDraftedPlayers = count($drafted);

        //tableaux pour stocker les données des joueurs selon leurs positions pour
        // verifier que le nombre limite pour chaque poste ne soit pas atteint
        //enregistrer chaque joueur drafté dans le tableau qui correspond à son poste
        $forwards = [];
        $guards = [];
        $centers = [];
        foreach($drafted as $draftedPlayer) {
            $position = '';
            $draftedInfos = json_decode($draftedPlayer->data);
            $position  = substr($draftedInfos->pl->pos, 0,1);
            if($position === "F") {
                $forwards[] =  $draftedPlayer;
            } elseif($position === "C") {
                $centers[] = $draftedPlayer;
            } else {
                $guards[] = $draftedPlayer;
            }
        }
        //récupération du poste du joueur sur lequel l'utilisateur veut faire une enchère
        $playerPosition = json_decode($player->data);
        $playerPosition = $playerPosition->pl->pos;
        $playerPosition  = substr($playerPosition, 0,1);
        if($playerPosition === "F") {
            $nbPosition = count($forwards);
            $limit = 5;
        } elseif($playerPosition === "C") {
            $nbPosition = count($centers);
            $limit = 2;
        } else {
            $nbPosition = count($guards);
            $limit = 5;
        }

        $updateValue = $request->all();
        //verification du champs entré
        $auctionValue = $updateValue["auctionValue"];


        $rules = ['auctionValue'     => 'integer|required'];
        // Vérification de la validité des informations transmises par l'utilisateur
        $validator = Validator::make($updateValue, $rules, [
            'auctionValue.integer' => 'Le nom de la league ne doit pas contenir de caractères spéciaux.',
            'auctionValue.required' => 'Veuillez entrer une valeur.',
            'auctionValue.required' => 'Veuillez entrer une valeur.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        //typage de auctionValue en integer pour pouvoir faire les vérifications avant l'enregistrement
        $auctionValue = (int) $auctionValue;

        //equipes présentent dans la ligue de l'utilisateur
        $leagueTeams = Team::where('league_id', $userLeagueId)->get();
        // met toutes les équipes présente dans la ligue de l'utilisateur dans un tableau pour récupérer ensuite toutes leurs enchrèes
        $teamsInLeagueId = [];
        foreach ($leagueTeams as $teamInLeague) {
            $teamsInLeagueId[] = $teamInLeague->id;
        }
        $PlayerCurrentPrice = Auction::whereIn('team_id', $teamsInLeagueId)->where('player_id', $id)->orderby('auction', 'desc')->get()->first();
        $PlayerCurrentPrice = $PlayerCurrentPrice->auction;

        //calcul difference entre valeur de l'update de l'enchère et argent restant
        $auctionUpdateDelta = $auctionValue - $PlayerCurrentPrice;

        //l'enchere doit être supérieur à la dernière valeur proposée par un joueur de la ligue
        if(empty($isAlreadyDrafted) && $auctionUpdateDelta <= $moneyAvailable && $nbDraftedPlayers < 15 && $nbPosition < $limit && $auctionValue > $lastAuctionOnSelectedPlayer) {
            $auctionTimeLimit = Carbon::parse(now())->addSeconds(30)->format('Y-m-d H:i:s');
            Auction::where([['player_id',$id], ['team_id', $team->id]])->update(['auction' => $auctionValue,'auction_time_limit' => $auctionTimeLimit ]);
            return back()->with('succes', 'Enchère Enregistrée !');
        }
        return back()->with('errors','Tu n\'as pas assez d\'argent !');
    }
}
