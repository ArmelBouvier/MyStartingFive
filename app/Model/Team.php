<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    protected $table = 'teams';

    protected $fillable = ['user_id', 'league_id', 'name', 'stadium_name'];

    protected $sortable = ['user_id', 'league_id', 'name', 'stadium_name'];

    // Recupérer les joueurs qui appartiennent une team
    public function getPlayers() {
        return $this->belongsToMany(Player::class);
    }

    // Recupérer la league qui appartiennent à seul team
    public function getLeague() {
        return $this->belongsTo(League::class, 'league_id');
    }

    // Associe un utilisateur à sa team
    public function userTeam()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function getMatches()
    {
        return $this->hasMany('App\Model\Match');
    }

}
