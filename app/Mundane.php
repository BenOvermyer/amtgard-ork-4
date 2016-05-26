<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mundane extends Model
{
    protected $table = 'ork_mundane';
    protected $primaryKey = 'mundane_id';

    public function kingdom()
    {
        return $this->belongsTo('App\Kingdom');
    }

    public function park()
    {
        return $this->belongsTo('App\Park');
    }

    public function awards()
    {
        return $this->hasMany('App\Award')->orderBy('date', 'desc');
    }

    public function units()
    {
        return $this->belongsToMany('App\Unit', 'ork_unit_mundane')->orderBy('name');
    }

    public function unitMemberships()
    {
        return $this->hasMany('App\UnitMembership', 'mundane_id');
    }
}
