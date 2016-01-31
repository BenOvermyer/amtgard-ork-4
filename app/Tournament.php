<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $table = 'ork_tournament';
    protected $primaryKey = 'tournament_id';

    public function kingdom()
    {
        return $this->belongsTo( 'App\Kingdom' );
    }

    public function park()
    {
        return $this->belongsTo( 'App\Park' );
    }
}
