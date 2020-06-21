<?php

namespace App\Http\Middleware;

use App\Model\Draft;
use Closure;

class Match
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // récupération des infos sur la liguqe de l'utilisateur pour savoir quand la draft prend fin
        if($request->user()) {

            if($request->user()->leagues[0]->id){

                if($request->user()->team){
                    $userTeam = $request->user()->team;
                    $leagueId = $request->user()->leagues[0]->id;

                    $userNextMatch = \App\Model\Match::where(function ($query) use ($leagueId, $userTeam) {
                        $query->where(['league_id' => $leagueId, 'away_team_id' => $userTeam->id])
                            ->orwhere(['league_id' => $leagueId, 'home_team_id' => $userTeam->id]);
                    })
                        ->whereNull('home_team_score')
                        ->whereNotNull('away_team_id')
                        ->orderBy('start_at', 'asc')
                        ->first();


                    if($userNextMatch) {
                            return $next($request);
                    }

                    return redirect()->back();
                }
                return redirect()->back();
            }
            return redirect()->back();
        }
        return redirect()->back();
    }


}
