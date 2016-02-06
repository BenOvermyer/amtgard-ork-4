<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    protected $table = 'ork_park';
    protected $primaryKey = 'park_id';

    public function kingdom()
    {
        return $this->belongsTo( 'App\Kingdom' );
    }

    public function events()
    {
        return $this->hasMany( 'App\Event' );
    }

    public function tournaments()
    {
        return $this->hasMany( 'App\Tournament' );
    }

    public function parkDays()
    {
        return $this->hasMany( 'App\ParkDay' );
    }

    public function members()
    {
        return $this->hasMany( 'App\Mundane' )->orderBy( 'persona' );
    }
}
