<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{

    protected $table = 'matchs';
    protected $dates = ['started_at'];

    // Récupére le nom des équipes présent dans le match grâce à la clé étranger home_team_id dans la table match
    public function homeTeamName() {
        return $this->belongsTo(Team::class, 'home_team_id');
    }


    // Récupére le nom des équipes présent dans le match grâce à la clé étranger away_team_id dans la table match
    public function awayTeamName() {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    // Récupére tous les joueurs du matchs
    public function matchPlayers()
    {
        return $this->belongsToMany('App\Model\Player');
    }
}

