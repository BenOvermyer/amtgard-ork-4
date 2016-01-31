<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kingdom extends Model
{
    protected $primaryKey = 'kingdom_id';
    protected $table = 'ork_kingdom';

    public $timestamps = false;

    public function parks()
    {
        return $this->hasMany( 'App\Park' )->orderBy( 'name' );
    }

    public function activeParks()
    {
        return $this->hasMany( 'App\Park' )->where( 'active', 'Active' )->orderBy( 'name' );
    }

    public function principalities()
    {
        return $this->hasMany( 'App\Kingdom', 'parent_kingdom_id' )->orderBy( 'name' );
    }

    public function events()
    {
        return $this->hasMany( 'App\Event' );
    }

    public function tournaments()
    {
        return $this->hasMany( 'App\Tournament' );
    }

    public function upcomingTournaments()
    {
        return $this->hasMany( 'App\Tournament' )->where( 'date_time', '>=', date( 'm/D/Y' ) )->orderBy( 'date_time', 'asc' );
    }
}
