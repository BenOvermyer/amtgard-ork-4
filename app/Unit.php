<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'ork_unit';
    protected $primaryKey = 'unit_id';

    public function members()
    {
        return $this->belongsToMany( 'App\Mundane', 'ork_unit_mundane' )->orderBy( 'persona' );
    }

    public function unitMemberships()
    {
        return $this->hasMany( 'App\UnitMembership', 'unit_id' );
    }
}
