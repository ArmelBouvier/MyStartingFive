<?php

namespace App\Http\Controllers;

use App\Mail\Register;
use App\Model\Draft;
use App\Model\League;
use App\Model\Match;
use App\Model\Team;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LeagueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $usersInleagues = DB::table('league_user')
            ->where('league_user.user_id', $userId)
            ->exists();
//dd($usersInleagues);
        if($usersInleagues === true){
            $userLeagueId = DB::table('league_user')
                ->where('league_user.user_id', $userId)
                ->first()->league_id;
            return redirect()->route('leagues.show', $userLeagueId);
        }else{
            $leagues = League::all();
            return view('leagues.index');
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
        // Récupération des données du formulaire et association de l'id de l'utilisateur

        //Vérification du nombre de leagues associées à l'utilisateur
        $user_id = Auth::user()->id;

        $usersInleagues = DB::table('league_user')
            ->where('league_user.user_id', $user_id)
            ->exists();
        if ($usersInleagues === true) {
            $userLeagueId = DB::table('league_user')
                ->where('league_user.user_id', $user_id)
                ->first()->league_id;
            return redirect()->route('leagues.show', $userLeagueId)->withErrors('Tu as déjà une league !');
        } else {
            $token = md5(uniqid($user_id, true));
            $email = Auth::user()->email;
            $values = $request->all();
            $publicLeague = (int)$values['public'];
            $rules = [
                'name' => 'string|required|unique:leagues',
                'number_teams' => 'integer|required',
                'public' => 'integer|required',
            ];
            // Vérification de la validité des informations transmises par l'utilisateur
            $validator = Validator::make($values, $rules, [
                'name.string' => 'Le nom de la league ne doit pas contenir de caractères spéciaux.',
                'name.required' => 'Il faut choisir un nom de league !',
                'name.unique' => 'Il faut choisir un autre nom de league!',
                'number_teams.required' => 'Il faut choisir un nombre de teams !',
                'public.required' => 'Privée ou publique ???',

            ]);
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            // Création de la nouvelle league avec les informations transmises
            $newLeague = new League();
            $newLeague->user_id = $user_id;
            $newLeague->name = $values['name'];
            $newLeague->number_teams = $values['number_teams'];
            $newLeague->public = $publicLeague;
            if ($publicLeague === 1) {
                $newLeague->token = $token;
            }
            $newLeague->save();
            // On associe le joueur à la league dans la table pivot
            $newLeague->users()->sync(Auth::user()->id);

            // On ajoute le role créateur de league
            Auth::user()->roles()->attach([3]);

            // Envoi d'un mail de confirmation
            $title = 'Confirmation de création league !';
            $content = 'Salut, ta league ' . $newLeague['name'] .
                ' a bien été créée et comporte ' . $newLeague['number_teams'] . ' équipes.<br>';

            if ($publicLeague === 0) {
                $content .= "Il s'agit d'une league publique, que tout le monde peut rejoindre";
            } else {
                $content .= "Pour inviter tes potes, donne leur le mot de passe :<br><br> $token";
            }
            $content .= "<br><br>Bonne route vers la gloire !";

            Mail::to($email)->send(new Register($title, $content));

            $id = $newLeague->id;

            return redirect()->route('leagues.show', $id)->with('success', 'La league a bien été créée.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(League $league)
    {
        // traite les infos d'une league en cours et renvoie les infos à l'utilisateur sur une vue
        $leagueId = $league->id;
        //Récupération des matchs associés à chaque team de la league
        $allLeagueMatches = Match::where('league_id', $leagueId)->get();

        //recupération des équipes de la league
        $allLeagueTeams = Team::where('league_id', $leagueId)->get();
        $teamsID = [];
        //stocakge des ID de chaque équipe depuis Team dans un tableau
        foreach ($allLeagueTeams as $leagueTeam) {
            $teamsID[]= $leagueTeam->id;
        }

//        // Calcul de la valeur de chaque équipe de la league
//        $allLeagueTeamsPlayers = [];
//        $allLeagueTeamsValues = [];
//        foreach ($allLeagueTeams as $leagueTeam){
//            $allLeagueTeamsPlayers[] = $allLeagueTeams->getPlayers;
//            $teamValue = 0;
//            foreach($allLeagueTeamsPlayers as $player){
//                $teamValue += $player->price;
//
//            }
//            $allLeagueTeamsValues[] = $teamValue;
//        }
//        dd($allLeagueTeamsValues);

        // Calcul du pourcentage de victoire de chaque équipe de la league
        $teamVictoryRatio = [];
        foreach ($teamsID as $team){
            $teamWiningCount2 = Match::where('team_wining', '=', $team)->count();
            $teamHomeCount = Match::where('home_team_id', $team)->count();
            $teamAwayCount = Match::where('away_team_id', $team)->count();
            $teamCountSum = $teamHomeCount + $teamAwayCount;
            if ($teamCountSum !== 0) {
                $teamVictoryRatio[$team] =  (float) number_format((($teamWiningCount2 /  $teamCountSum) * 100), 2, '.', '');
            } else {
                $teamVictoryRatio[$team] = 0;
            }
        }

        // Check du statut de la draft
        if($league->isActive === 1){
            $draftStatus = $league->draft->is_over;
            return view('leagues.show')
                ->with('league', $league)
                ->with('teamVictoryRatio', $teamVictoryRatio)
                ->with('draftStatus', $draftStatus);
        }else{
            $draftStatus = 0;
            return view('leagues.show')
                ->with('league', $league)
                ->with('teamVictoryRatio', $teamVictoryRatio)
                ->with('draftStatus', $draftStatus);
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
        $data = League::find($id);
        if($data->teams->count() === $data->users->count()){
            if ($data->users->count()% 2 == 0){
                date_default_timezone_set ( 	'Europe/Paris' );
                $data->isActive = $request->isActive;
                $data->save();

                date_default_timezone_set ( 	'Europe/Paris' );
                //enregistrement du début de la draft avec heure de fin
                $draftEnd = now()->addMinutes(2);
                $draft = new Draft();
                $draft->league_id = $data->id;
                $draft->is_over = 0;
                $draft->ends_at = $draftEnd;
                $draft->save();

                //Récupération des emails des membres de la league
                $userEmails = [];
                $users = DB::table('league_user')
                    ->leftjoin('users', 'id', '=', 'league_user.user_id')
                    ->where('league_user.league_id', (int)$id)
                    ->get();
                foreach ($users as $user) {
                    $userEmails[]= $user->email;
                }

                // Envoi d'un mail de lancement de la draft
                $title = 'Lancement de la draft !';
                $content = 'Salut, ta league ' . $data->name .
                    ' viens d\'entamer sa draft ! Connecte toi vite pour y participer';



                Mail::to($userEmails)->send(new Register($title, $content));

                return redirect(route('draft.index'))->with('success', 'La draft commence !');
            } else{
                return redirect(route('leagues.show', $id))->withErrors('Le nombre de joueurs n\' est pas pair !');
            }
        } else{
            return redirect(route('leagues.show', $id))->withErrors('Tous les joueurs n\'ont pas créé leur équipe !');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $league = League::findOrFail($id);
        $teamsToDestroy = $league->teams;
        if (isset($teamsToDestroy)){
            // d'abord détruire les équipes avant de supprimer la league
            foreach ($teamsToDestroy as $team){
                $team->delete();
            }

             DB::table('role_user')
                ->where('user_id', $league->user_id)
                ->where('role_id', 3)
                ->delete();

            // suppression de la league
            $league->delete();

            return redirect(route('leagues.index'))->with('success', 'La league a bien été supprimée.');
        }else{
            // suppression de la league
            $league->delete();

            return redirect(route('leagues.index'))->with('success', 'La league a bien été supprimée.');
        }
    }

    public function publicLeagues()
    {
        //permet d'afficher les leagues publiques
        $leagues = League::where('public', 0)->paginate(15);

        return view('leagues.public')->with('leagues', $leagues);
    }

    public function joinPublicLeague($id)
    {
        //Vérification du nombre de leagues associées à l'utilisateur
        $user_id = Auth::user()->id;
        $usersInleagues = DB::table('league_user')
            ->where('league_user.user_id', $user_id)
            ->exists();
        if ($usersInleagues === true) {
            return redirect()->route('leagues.show', (int)$id)->withErrors('Tu as déjà une league !');
        } else {
            //permet de rejoindre une league publique
            $league_id = (int)$id;

            // insère les id dans la table pivot
            $user = Auth::user();
            $league = League::where('id', '=', $league_id)->first();
            if ($league->users->count() >= $league->number_teams) { // Vérifie les places dispo avant de sauvegarder
                return redirect()->route('leagues.index')->withErrors('Désolé cette league est complète !');
            } else {
                $user->leagues()->sync([$league->id]);
                $id = $league->id;
                return redirect()->route('leagues.show', $id)->with('success', 'Rattachement à la league pris en compte.');
            }
        }

    }

    public function joinPrivateLeague(Request $request)
    {
        //permet de rejoindre une league privée

        //Vérification du nombre de leagues associées à l'utilisateur
        $user_id = Auth::user()->id;$usersInleagues = DB::table('league_user')
        ->where('league_user.user_id', $user_id)
        ->exists();
        if ($usersInleagues === true) {
            return redirect()->route('dashboard.index', Auth::user()->id)->withErrors('Tu as déjà une league !');
        } else {
            if (League::where('token', '=', $request->token)->exists()) {
                // insère les id dans la table pivot
                $user = Auth::user();
                $league = League::where('token', '=', $request->token)->first();
                if ($league->users->count() >= $league->number_teams) { // Vérifie les places dispo avant de sauvegarder
                    return redirect()->route('leagues.index')->withErrors('Désolé cette league est complète !');
                } else {
                    $user->leagues()->sync([$league->id]);
                    $id = $league->id;
                    return redirect()->route('leagues.show', $id)->with('success', 'Rattachement à la league pris en compte.');

                }
            } else {
                return redirect('leagues')->withErrors('Cette league n\'existe pas');
            }
        }
    }
}
