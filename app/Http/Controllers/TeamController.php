<?php

namespace App\Http\Controllers;

use App\Model\League;
use App\Model\Match;
use App\Model\Nbateam;
use App\Model\Team;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $user = Auth::user();

        $userId = $user->id;
        $userLeague = DB::table('league_user')
            ->leftjoin('leagues', 'leagues.id', '=', 'league_user.league_id')
            ->where('league_user.user_id', $userId)
            ->exists();
        //Vérification si l'utilisateur a une league
        if($userLeague === true){
            $userGetLeague = DB::table('league_user')
                ->leftjoin('leagues', 'leagues.id', '=', 'league_user.league_id')
                ->where('league_user.user_id', $userId)
                ->first();
            $userLeagueId = $userGetLeague->id;
            //Vérification du nombre d'équipes associées à l'utilisateur
            if ($hasTeam = $user->team()->exists()) {
                return redirect()->route('leagues.show', $userLeagueId)->withErrors('Tu as déjà une team !');
            } else {
                //récupérer l'équipe favorite du joueur pour lui afficher le logo correspondant
                $userHasLogo = $user->nbaTeams;
                if(!$userHasLogo) {
                    $userLogo ='/storage/images/leagues_portal/picto_league_publique.png';
                    return view('teams.create')->with('userLogo', $userLogo);
                } else {
                    $userLogo ='/storage/images/logos/' . $user->nbaTeams->name . '.png';
                    return view('teams.create')->with('userLogo', $userLogo);
                }
            }
        }else{
            return redirect()->route('leagues.index')->withErrors('Tu dois d\'abord rejoindre ou créer une league !');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Vérification du nombre d'équipes associées à l'utilisateur
        $user = Auth::user();
        $userId = $user->id;
        $userLeague = DB::table('league_user')
            ->leftjoin('leagues', 'leagues.id', '=', 'league_user.league_id')
            ->where('league_user.user_id', $userId)
            ->first();
        $userLeagueId = $userLeague->id;

        if ($hasTeam = $user->team()->exists()) {
            return redirect()->route('leagues.show', $userLeagueId)->withErrors('Tu as déjà une team !');
        } else {
            // Récupération des données du formulaire et association de l'id de l'utilisateur
            $user = Auth::user();


            $values = $request->all();
            $rules = [
                'name' => 'string|required|max:30|unique:teams',
                'stadium_name' => 'string|required|max:30|unique:teams',
//            'public'           => 'integer|required',
            ];
            // Vérification de la validité des informations transmises par l'utilisateur
            $validator = Validator::make($values, $rules, [
                'name.string' => 'Le nom de la team ne doit pas contenir de caractères spéciaux.',
                'name.required' => 'Il faut choisir un nom de team !',
                'name.unique' => 'Il faut choisir un autre nom de team!',
                'stadium_name.string' => 'Le nom du stade ne doit pas contenir de caractères spéciaux.',
                'stadium_name.required' => 'Il faut choisir un nom de stade !',
                'stadium_name.unique' => 'Il faut choisir un autre nom de stade!',
//            'public.required' => 'Privée ou publique ???',

            ]);
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            // Création de la nouvelle league avec les informations transmises
            $newTeam = new Team();
            $newTeam->user_id = $user->id;
            $newTeam->league_id = $userLeagueId;
            $newTeam->name = $values['name'];
            $newTeam->stadium_name = $values['stadium_name'];
//        $newTeam->public          = $publicLeague;

            $newTeam->save();

            $id = $newTeam->id;
            if ($userLeague->isActive === 1) {
                return redirect()->route('draft.index')->with('success', 'La team a bien été créée.');
            } else {
                return redirect()->route('leagues.show', $userLeagueId)->with('success', 'L\'équipe a bien été créée.');
                return redirect()->route('leagues.show', $userLeagueId)->with('success', 'L\'équipe a bien été créée.');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        // récupération des données user
        $user = Auth::user();

        // $userTeam récupère l'équipe de l'utilisateur
        $userTeam = Team::where('user_id', $user->id)->first();

        // Calcul de la cote cumulée de l'équipe
        $userPlayers = $userTeam->getPlayers;
        $teamValue = 0;
        foreach($userPlayers as $player){
            $teamValue += $player->price;
        }
        // Calcul du pourcentage de victoire de l'équipe
        $teamWiningCount = Match::where('team_wining', '=', $team->id)->count();
        $teamHomeCount = Match::where('home_team_id', $team->id)->count();
        $teamAwayCount = Match::where('away_team_id', $team->id)->count();
        $teamCountSum = $teamHomeCount + $teamAwayCount;

        if ($teamCountSum !== 0) {
            $teamVictoryRatio =  (float) number_format((($teamWiningCount/  $teamCountSum) * 100), 2, '.', '');
        } else {
            $teamVictoryRatio = 0;
        }

        // $userBestPlayersTeam récupère les 5 meilleurs joueurs de l'utilisateur dans son équipe
        $userBestPlayersTeam = $userTeam->getPlayers->sortByDesc('score')->take(5);

        //récupération du logo d ela team de l'utilisateur
        $userHasLogo = $user->nbaTeams;
        if(!$userHasLogo) {
            $userLogo ='/storage/images/leagues_portal/picto_league_publique.png';
            return view('teams.show')
                ->with('team', $team)
                ->with('logo', $userLogo)
                ->with('userBestPlayersTeam', $userBestPlayersTeam)
                ->with('teamValue', $teamValue)
                ->with('teamVictoryRatio', $teamVictoryRatio);
        } else {
            $userLogo ='/storage/images/logos/' . $user->nbaTeams->name . '.png';
            return view('teams.show')
                ->with('team', $team)
                ->with('logo', $userLogo)
                ->with('userBestPlayersTeam', $userBestPlayersTeam)
                ->with('teamValue', $teamValue)
                ->with('teamVictoryRatio', $teamVictoryRatio);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
